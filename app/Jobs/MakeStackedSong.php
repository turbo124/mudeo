<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Song;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Filters\Audio\SimpleFilter;
use FFMpeg\Format\Video\X264;
use FFMpeg\Coordinate\TimeCode;
use GuzzleHttp\Client;
use Hashids\Hashids;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\OAuth1\Client\Server\Twitter;
use App\Jobs\UploadSongToYouTube;
use Illuminate\Support\Str;
use App\Notifications\SongSubmitted;

class MakeStackedSong implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $song;
    protected $working_dir;

    public $timeout = 1200;
    public $tries = 1;

    public function __construct(Song $song)
    {
        $this->song = $song;
        $this->working_dir = sha1(time()) . '/';
    }

    /**
    * Execute the job.
    *
    * @return void
    */
    public function handle()
    {
        // Don't include YouTube videos in the stacked video
        $song = $this->song;
        $tracks = $song->local_song_videos;
        $isNew = ! $song->is_rendered;

        if (count($tracks) == 0) {
            return;
        }

        File::makeDirectory(storage_path($this->working_dir), 0755, true, true);

        $client = new Client();
        foreach ($tracks as $track) {
            $client->request('GET', $track->video->url, ['sink' => $this->getUrl($track->video)]);
        }

        $disk = Storage::disk('do_spaces');
        $hashids = new Hashids('', 10);
        $filepath = storage_path($this->working_dir) . sha1(time()) . '.mp4';

        $video = $this->createVideo($tracks, $filepath);
        $remote_storage_file_name = str_replace(config('mudeo.asset_url'), '', $song->video_url);

        $disk->put($remote_storage_file_name, file_get_contents($filepath));
        $this->saveThumbnail($song, $filepath);

        if (! config('mudeo.is_dance') && count($tracks) > 1) {
            $videoUrl = $song->track_video_url;

            if (!$videoUrl) {
                $videoUrl = config('mudeo.asset_url') . 'videos/' . $hashids->encode( $song->user_id ) . '/' . Str::random(40) . '.mp4';
                $song->track_video_url = $videoUrl;
                $song->save();
            }

            $video = $this->createVideo($tracks, $filepath, true);
            $remote_storage_file_name = str_replace(config('mudeo.asset_url'), '', $song->track_video_url);
            $disk->put($remote_storage_file_name, file_get_contents($filepath));
        }

        File::deleteDirectory(storage_path($this->working_dir));

        //UploadSongToYouTube::dispatch($song);

        if ($isNew && $song->is_public) {
            User::admin()->notify(new SongSubmitted($song));
        }
    }

    private function createVideo($tracks, $filepath, $onlyFirstTrack = false)
    {
        $ffmpeg = FFMpeg::create([
            //'ffmpeg.binaries'  => '/usr/local/bin/ffmpeg',
            'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/bin/ffprobe',
            'timeout'          => 0, // The timeout for the underlying process
            'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
        ]);

        $video = $ffmpeg->open($this->getUrl($tracks[0]->video));

        if (count($tracks) > 1) {
            $layout = $this->song->layout;
            $count = 0;
            $sizes = $this->getSizes($tracks);
            $filterVideo = '';
            $filterAudio = '';

            if ($sizes->duration > (60 * 3)) {
                \Log::error("Duration ({$sizes->duration}) for {$this->song->id}: {$this->song->title} is too long");
                exit;
            }

            foreach ($tracks as $track) {
                $delay = $track->delay;

                if ($count > 0) {
                    if ($delay < 0) {
                        $video->addFilter(new SimpleFilter(['-ss', $delay / 1000 * -1]));
                    }
                    $video->addFilter(new SimpleFilter(['-i', $this->getUrl($track->video)]));
                }

                if ($onlyFirstTrack) {
                    if ($count == 0) {
                        $filterVideo = "[{$count}:v]scale={$sizes->min_width}:-2[{$count}-scale:v];$filterVideo";
                    } else {
                        $filterVideo = "[{$count}:v]scale={$sizes->min_width}:1[{$count}-scale:v];$filterVideo";
                    }
                } else if ($layout == 'grid') {
                    $width = $sizes->min_width;
                    $height = $sizes->min_height;
                    $filterVideo = "[{$count}:v]scale={$width}:{$height}:force_original_aspect_ratio=increase,crop={$width}:{$height}[{$count}-scale:v];$filterVideo";
                } else if ($layout == 'column') {
                    $filterVideo = "[{$count}:v]scale={$sizes->min_width}:-2[{$count}-scale:v];$filterVideo";
                } else if ($layout == 'row') {
                    $filterVideo = "[{$count}:v]scale=-2:{$sizes->min_height}[{$count}-scale:v];$filterVideo";
                }

                $volume = $track->volume;

                if (config('mudeo.is_dance') && $count > 0) {
                    $volume = 0;
                }

                if ($delay > 0) {
                    $filterVideo = "[{$count}-scale:v]tpad=start_duration=" . ($delay / 1000) . "[{$count}-delay:v];"
                        . "[{$count}:a]adelay={$delay}|{$delay}[{$count}-delay:a];"
                        . "[{$count}-delay:a]volume=" . ($volume / 100) . "[{$count}-volume:a];"
                        . "{$filterVideo}[{$count}-delay:v]";
                } else {
                    $filterVideo = "[{$count}:a]volume=" . ($volume / 100) . "[{$count}-volume:a];"
                        . "{$filterVideo}[{$count}-scale:v]";
                }

                $filterAudio .= "[{$count}-volume:a]";

                $count++;
            }

            $width = 1920;
            $height = 1080;

            if ($layout == 'column' || $onlyFirstTrack) {
                $filter = "{$filterVideo}vstack=inputs={$count}[v-pre];[v-pre]scale=-2:{$height}[v];";
            } else if ($layout == 'grid') {
                $filter = "{$filterVideo}xstack=inputs={$count}:layout=0_0|w0_0|0_h0|w0_h0[v-pre];[v-pre]scale=-2:{$height}[v];";
            } else {
                $filter = "{$filterVideo}hstack=inputs={$count}[v-pre];[v-pre]scale={$width}:-2[v];";
            }

            $filter .= "{$filterAudio}amix=inputs={$count}[a]";

            $video->addFilter(new SimpleFilter(['-filter_complex', $filter]))
                ->addFilter(new SimpleFilter(['-map', '[v]']))
                ->addFilter(new SimpleFilter(['-map', '[a]']))
                ->addFilter(new SimpleFilter(['-ac', '2']))
                ->filters();
        }

        $format = new X264();

        $format->setPasses(1)
            ->setAudioCodec('aac')
            ->setKiloBitrate(1200)
            ->setAudioChannels(2)
            ->setAudioKiloBitrate(192)
            ->setAdditionalParameters([
                '-vcodec', 'libx264',
                '-vprofile', 'baseline',
                '-level', 3.0,
                '-movflags', 'faststart',
                '-pix_fmt', 'yuv420p',
            ]);

        $video->save($format, $filepath);

        return $video;
    }

    private function saveThumbnail($song, $filepath)
    {
        if ($song->thumbnail_url) {
            Storage::delete($song->thumbnail_url);
        }

        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/bin/ffprobe'
        ]);

        $video = $ffmpeg->open($filepath);

        $hashids = new Hashids('', 10);
        $tmp_file_name = Str::random(40) . '.jpg';
        $vid_object = $video->frame(TimeCode::fromSeconds(2))->save('', false, true);
        $tmp_file = Storage::disk('local')->put($tmp_file_name , $vid_object);

        $disk = Storage::disk(config('filesystems.default'));
        $remote_storage_file_name = 'videos/' . $hashids->encode( $song->user_id ) . '/' . $tmp_file_name;

        $disk->put($remote_storage_file_name, Storage::disk('local')->get($tmp_file_name));
        Storage::disk('local')->delete($tmp_file_name);

        $song->thumbnail_url = $disk->url($remote_storage_file_name);
        $song->is_rendered = true;
        $song->needs_render = false;
        $song->save();

        try {
            $image = @imagecreatefromjpeg($song->thumbnail_url);
            if ($image) {
                $image = imagecropauto($image, IMG_CROP_SIDES);
                $song->width = imagesx($image);
                $song->height = imagesy($image);
                $song->save();
            }
        } catch (Exception $e) {
            // do nothing
        }
    }

    private function getSizes($tracks)
    {
        $height_collection = collect();
        $width_collection = collect();
        $duration_collection = collect();

        $data = new \stdClass;
        $is_first = true;

        foreach($tracks as $song_video)
        {
            $song = $song_video->song;
            $video = $song_video->video;

            $ffprobe = FFProbe::create([
                'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
                'ffprobe.binaries' => '/usr/bin/ffprobe',
                'timeout'          => 0, // The timeout for the underlying process
                'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
            ]);

            $dimension = $ffprobe
                ->streams($this->getUrl($video)) // extracts streams informations
                ->videos()                      // filters video streams
                ->first()                       // returns the first video stream
                ->getDimensions();

            $height_collection->push($dimension->getWidth());
            $width_collection->push($dimension->getHeight());

            if ($is_first) {
                $data->first_height = $dimension->getWidth();
            }

            $ffprobe = FFProbe::create();
            $duration = $ffprobe
                ->format($this->getUrl($video)) // extracts file informations
                ->get('duration');             // returns the duration property

            $duration_collection->push($duration);
            $is_first = false;
        }

        $data->min_height = $height_collection->min();
        $data->max_height = $height_collection->max();
        $data->min_width = $width_collection->min();
        $data->max_width = $width_collection->max();
        $data->duration = $duration_collection->max();

        return $data;
    }

    private function getUrl($video)
    {
        return storage_path($this->working_dir) . basename($video->url);
    }
}
