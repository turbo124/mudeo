<?php

namespace App\Jobs;

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

class MakeStackedSong implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $song;
    protected $working_dir;

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

        File::makeDirectory(storage_path($this->working_dir), 0755, true, true);

        $client = new Client();
        foreach ($tracks as $track) {
            $client->request('GET', $track->video->url, ['sink' => $this->getUrl($track->video)]);
        }

        $filepath = storage_path($this->working_dir) . sha1(time()) . '.mp4';
        $video = $this->createVideo($tracks, $filepath);

        $hashids = new Hashids('', 10);
        $remote_storage_file_name = 'videos/' . $hashids->encode( $song->user_id ) .
            '/' . $hashids->encode( $song->id ) . '.mp4';
        $file = file_get_contents($filepath);

        $disk = Storage::disk('gcs');
        $disk->put($remote_storage_file_name, $file);

        $this->saveThumbnail($song, $filepath);

        File::deleteDirectory(storage_path($this->working_dir));
    }

    private function createVideo($tracks, $filepath)
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

            foreach ($tracks as $track) {
                $delay = $track->delay;

                if ($count > 0) {
                    if ($delay < 0) {
                        $video->addFilter(new SimpleFilter(['-ss', $delay / 1000 * -1]));
                    }
                    $video->addFilter(new SimpleFilter(['-i', $this->getUrl($track->video)]));
                }

                if ($layout == 'grid') {
                    $width = $sizes->min_width;
                    $height = $sizes->min_height;
                    $filterVideo = "[{$count}:v]scale={$width}:{$height}:force_original_aspect_ratio=increase,crop={$width}:{$height}[{$count}-scale:v];$filterVideo";
                } else if ($layout == 'column') {
                    $filterVideo = "[{$count}:v]scale={$sizes->min_width}:-2[{$count}-scale:v];$filterVideo";
                } else if ($layout == 'row') {
                    $filterVideo = "[{$count}:v]scale=-2:{$sizes->min_height}[{$count}-scale:v];$filterVideo";
                }

                if ($delay > 0) {
                    $filterVideo = "[{$count}-scale:v]tpad=start_duration=" . ($delay / 1000) . "[{$count}-delay:v];"
                        . "[{$count}:a]adelay={$delay}|{$delay}[{$count}-delay:a];"
                        . "[{$count}-delay:a]volume=" . ($track->volume / 100) . "[{$count}-volume:a];"
                        . "{$filterVideo}[{$count}-delay:v]";

                    /*
                    $filterVideo = "{$filterVideo}[{$count}-scale:v]split[{$count}-scale-a:v][{$count}-scale-b:v];"
                        . "[{$count}-scale-a:v]trim=duration=" . ($delay / 1000) . ",geq=0:128:128[{$count}-blank:v];"
                        . "[{$count}-blank:v][{$count}-scale-b:v]concat[{$count}-delay:v];"
                        . "[{$count}:a]adelay={$delay}|{$delay}[{$count}-delay:a];"
                        . "[{$count}-delay:a]volume=" . ($track->volume / 100) . "[{$count}-volume:a];"
                        . "[{$count}-delay:v]";
                        */
                } else {
                    $filterVideo = "[{$count}:a]volume=" . ($track->volume / 100) . "[{$count}-volume:a];"
                        . "{$filterVideo}[{$count}-scale:v]";
                }

                $filterAudio .= "[{$count}-volume:a]";

                $count++;
            }

            if ($layout == 'grid') {
                $filter = "{$filterVideo}xstack=inputs={$count}:layout=0_0|w0_0|0_h0|w0_h0[v];";
            } else if ($layout == 'column') {
                $filter = "{$filterVideo}vstack=inputs={$count}[v];";
            } else {
                $filter = "{$filterVideo}hstack=inputs={$count}[v];";
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
            ->setAudioKiloBitrate(126)
            ->setAdditionalParameters(['-vprofile', 'baseline', '-level', 3.0, '-movflags', '+faststart']);

        $video->save($format, $filepath);

        return $video;
    }

    private function saveThumbnail($song, $filepath)
    {
        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/bin/ffprobe'
        ]);

        $video = $ffmpeg->open($filepath);

        $hashids = new Hashids('', 10);
        $tmp_file_name = sha1(time()) . '.jpg';
        $vid_object = $video->frame(TimeCode::fromSeconds(1))->save('', false, true);
        $tmp_file = Storage::disk('local')->put($tmp_file_name , $vid_object);

        $disk = Storage::disk(config('filesystems.default'));
        $remote_storage_file_name = 'videos/' . $hashids->encode( $song->user_id ) . '/' . $hashids->encode( $song->user_id ) . '_' .$tmp_file_name;

        $disk->put($remote_storage_file_name, Storage::disk('local')->get($tmp_file_name));
        Storage::disk('local')->delete($tmp_file_name);

        $song->thumbnail_url = $disk->url($remote_storage_file_name);
        $song->is_rendered = true;
        $song->save();
    }

    private function getSizes($tracks)
    {
        $height_collection = collect();
        $width_collection = collect();

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
        }

        $data = new \stdClass;
        $data->min_height = $height_collection->min();
        $data->max_height = $height_collection->max();
        $data->min_width = $width_collection->min();
        $data->max_width = $width_collection->max();

        return $data;
    }

    private function getUrl($video)
    {
        return storage_path($this->working_dir) . basename($video->url);
    }
}
