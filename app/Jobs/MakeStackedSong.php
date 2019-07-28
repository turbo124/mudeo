<?php

namespace App\Jobs;

use App\Models\Song;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Filters\Audio\SimpleFilter;
use FFMpeg\Format\Video\X264;
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

    protected $ffmpeg;

    protected $working_dir;
    /**
    * Create a new job instance.
    *
    * @return void
    */

    public function __construct(Song $song)
    {
        $this->song = $song;
        $this->ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/bin/ffprobe',
            'timeout'          => 0, // The timeout for the underlying process
            'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
        ]);
        $this->working_dir = sha1(time()) . '/';
    }

    /**
    * Execute the job.
    *
    * @return void
    */
    public function handle()
    {
        $video_count = count($this->song->videos);
        $song_videos = $this->song->song_videos;

        File::makeDirectory(storage_path($this->working_dir), 0755, true, true);

        $client = new Client();

        foreach($song_videos as $song_video)
        {
            $song = $song_video->song;
            $video = $song_video->video;
            $client->request('GET', $video->url, ['sink' => storage_path($this->working_dir) . basename($video->url)]);
        }

        /* Loop and make sure all videos are equal height*/
        $height_collection = collect();

        foreach($song_videos as $song_video)
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
                ->streams(storage_path($this->working_dir) . basename($video->url)) // extracts streams informations
                ->videos()                      // filters video streams
                ->first()                       // returns the first video stream
                ->getDimensions();

            $height = $dimension->getWidth();
            //             $height = $dimension->getHeight();

            $height_collection->push($height);
            Log::error('storing the height of = '.$height);
            //     Log::error('storing the width of = '.$width);
        }
        Log::error('number of heights collected = '.$height_collection->count());

        /* Compare all video heights, if there is a discrepency, resize all videos to ->min() */

        /* There is no obvious way to resize*/
        if($height_collection->min() != $height_collection->max())
        {
            foreach($song_videos as $song_video)
            {
                $song = $song_video->song;
                $video = $song_video->video;

                $this->ffmpeg = FFMpeg::create([
                    'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
                    'ffprobe.binaries' => '/usr/bin/ffprobe',
                    'timeout'          => 0, // The timeout for the underlying process
                    'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
                ]);

                $vid = $this->ffmpeg->open(storage_path($this->working_dir) . basename($video->url));
                $vid->addFilter(new SimpleFilter(['-vf', 'scale=-1:'.$height_collection->min()]))
                    ->filters()->synchronize();

                $format = new X264();
                $format->setPasses(1)
                    ->setAudioCodec('aac')
                    ->setKiloBitrate(1200)
                    ->setAudioChannels(2)
                    ->setAudioKiloBitrate(126);

                $vid->save($format, storage_path($this->working_dir) . 'temp_' .basename($video->url));

                Log::error('scaling video '. $video->url);

                File::move(storage_path($this->working_dir) . 'temp_' .basename($video->url), storage_path($this->working_dir) . basename($video->url));
            }
        }


        foreach($song_videos as $song_video)
        {
            $song = $song_video->song;
            $video = $song_video->video;

            if($song_video->volume != 100)
            {
                $volume = $song_video->volume / 100;
                $this->ffmpeg = FFMpeg::create([
                    'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
                    'ffprobe.binaries' => '/usr/bin/ffprobe',
                    'timeout'          => 0, // The timeout for the underlying process
                    'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
                ]);

                $vid = $this->ffmpeg->open(storage_path($this->working_dir) . basename($video->url));

                $format = new X264();
                $format->setPasses(1)
                    ->setAudioCodec('aac')
                    ->setKiloBitrate(1200)
                    ->setAudioChannels(2)
                    ->setAudioKiloBitrate(126)
                    ->setAdditionalParameters(['-filter:a', 'volume='.$volume]);

                $vid->save($format, storage_path($this->working_dir) . 'temp_' .basename($video->url));

                File::move(storage_path($this->working_dir) . 'temp_' .basename($video->url), storage_path($this->working_dir) . basename($video->url));
            }
        }

        $fileSongVideoPath = $this->buildStackedVideo($song_videos);
        $hashids = new Hashids('', 10);
        $disk = Storage::disk('gcs');
        $remote_storage_file_name = 'videos/' . $hashids->encode( $this->song->user_id ) . '/' . $hashids->encode( $song->id ) . '.mp4';
        $file = file_get_contents($fileSongVideoPath);
        $disk->put($remote_storage_file_name, $file);

        File::deleteDirectory(storage_path($this->working_dir));
    }


    public function buildStackedVideo($song_videos)
    {
        $x = count($song_videos);

        $mp4_file = $song_videos->toArray();

        if($x >= 2)
        {
            $filepath = $this->inAndOut(storage_path($this->working_dir) . basename($mp4_file[0]['video']['url']), storage_path($this->working_dir) . basename($mp4_file[1]['video']['url']), $mp4_file[1]['delay']);

            unset($mp4_file[0]);
            unset($mp4_file[1]);

            if(array_key_exists(2, $mp4_file)) {
                $filepath = $this->inAndOut($filepath, storage_path($this->working_dir) . basename($mp4_file[2]['video']['url']), $mp4_file[2]['delay']);
                unset($mp4_file[2]);
            }

            if(array_key_exists(3, $mp4_file)) {
                $filepath = $this->inAndOut($filepath, storage_path($this->working_dir) . basename($mp4_file[3]['video']['url']), $mp4_file[3]['delay']);
                unset($mp4_file[3]);
            }

            if(array_key_exists(4, $mp4_file)) {
                $filepath = $this->inAndOut($filepath, storage_path($this->working_dir) . basename($mp4_file[4]['video']['url']), $mp4_file[4]['delay']);
                unset($mp4_file[4]);
            }

            return $filepath;
        }
        else
        {
            return $song_videos->first()->video->url;
        }

    }

    public function inAndOut($parentVideo, $childVideo, $delay)
    {
        $this->ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/bin/ffprobe',
            'timeout'          => 0, // The timeout for the underlying process
            'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
        ]);

        $video = $this->ffmpeg->open($parentVideo);

        if ($delay > 0) {
            $video->addFilter(new SimpleFilter(['-itsoffset', $delay / 1000]));
        } elseif ($delay < 0) {
            $video->addFilter(new SimpleFilter(['-ss', $delay / 1000 * -1]));
        }

        $video->addFilter(new SimpleFilter(['-i', $childVideo]))
            ->addFilter(new SimpleFilter(['-filter_complex', 'hstack=inputs=2; amerge=inputs=2']))
            ->filters()->synchronize();

        $format = new X264();

        $format->setPasses(1)
            ->setAudioCodec('aac')
            ->setKiloBitrate(1200)
            ->setAudioChannels(2)
            ->setAudioKiloBitrate(126)
            ->setAdditionalParameters(['-vprofile', 'baseline', '-level', 3.0, '-movflags', '+faststart']);

        $filepath = sha1(time()) . '.mp4';

        $video->save($format, storage_path($this->working_dir) . $filepath);

        return storage_path($this->working_dir) . $filepath;
    }
}
