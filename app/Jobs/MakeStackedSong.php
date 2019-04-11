<?php

namespace App\Jobs;

use App\Models\Song;
use FFMpeg\FFMpeg;
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

                    $vid->addFilter(new SimpleFilter(['-filter:a', 'volume='.$volume]))
                    ->filters();

                    $format = new X264();

                    $format->setKiloBitrate(1000);

                    $format->setAudioCodec("aac");

                    $vid->save($format, storage_path($this->working_dir) . 'temp_' .basename($video->url)); 

                    File::move(storage_path($this->working_dir) . 'temp_' .basename($video->url), storage_path($this->working_dir) . basename($video->url));
                    
                }

        }


        $fileSongVideoPath = $this->buildStackedVideo($song_videos);
        
        /*
        Scale song to 1080p
         */
        

        $fileSongVideoPath = $this->scaleDownVideo($fileSongVideoPath);

        /*
        End Scale
         */
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

            $filepath = $this->inAndOut(storage_path($this->working_dir) . basename($mp4_file[0]['video']['url']), storage_path($this->working_dir) . basename($mp4_file[1]['video']['url']), 1);

            unset($mp4_file[0]);
            unset($mp4_file[1]);

              if(array_key_exists(2, $mp4_file)) {

              $filepath = $this->inAndOut($filepath, storage_path($this->working_dir) . basename($mp4_file[2]['video']['url']), 1);

              unset($mp4_file[2]);

              }

              if(array_key_exists(3, $mp4_file)) {

              $filepath = $this->inAndOut($filepath, storage_path($this->working_dir) . basename($mp4_file[3]['video']['url']), 1);

              unset($mp4_file[3]);

              }    

              if(array_key_exists(4, $mp4_file)) {

              $filepath = $this->inAndOut($filepath, storage_path($this->working_dir) . basename($mp4_file[4]['video']['url']), 1);

              unset($mp4_file[4]);

              }            

            return $filepath;

          }
          else
          {
              return $song_videos->first()->video->url;
          }

      }

      public function inAndOut($parentVideo, $childVideo, $userHash)
      {

          $this->ffmpeg = FFMpeg::create([
                  'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
                  'ffprobe.binaries' => '/usr/bin/ffprobe',
                  'timeout'          => 0, // The timeout for the underlying process
                  'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
              ]);

          $video = $this->ffmpeg->open($parentVideo);

          $video->addFilter(new SimpleFilter(['-i', $childVideo]))
                ->addFilter(new SimpleFilter(['-filter_complex', 'hstack=inputs=2; amerge=inputs=2']))
                ->addFilter(new SimpleFilter(['-vprofile', 'baseline']))
                ->addFilter(new SimpleFilter(['-level', 3.1]))
                ->addFilter(new SimpleFilter(['-movflags', '+faststart']))
                ->filters();

          $format = new X264();
          $format->setKiloBitrate(1000);
          $format->setAudioCodec("aac");

          $filepath = sha1(time()) . '.mp4';

          $video->save($format, storage_path($this->working_dir) . $filepath);
          
          return storage_path($this->working_dir) . $filepath;
              
      }

      public function scaleDownVideo($filePath)
      {

          $this->ffmpeg = FFMpeg::create([
                  'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
                  'ffprobe.binaries' => '/usr/bin/ffprobe',
                  'timeout'          => 0, // The timeout for the underlying process
                  'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
              ]);

          $video = $this->ffmpeg->open($filePath);

          $video->addFilter(new SimpleFilter(['-vf', 'scale=1920:-2']))
                ->addFilter(new SimpleFilter(['-profile:v', 'baseline']))
                ->addFilter(new SimpleFilter(['-level', 3.1]))
                ->addFilter(new SimpleFilter(['-movflags', '+faststart']))
                ->filters();

          $format = new X264();
          $format->setAudioCodec("aac");

          $filepath = sha1(time()) . '.mp4';

          $video->save($format, storage_path($this->working_dir) . $filepath);

          return storage_path($this->working_dir) . $filepath;


      }


}


/*
-vf scale=320:-1

->addFilter(new SimpleFilter(['-s', 'hd1080']))

 */