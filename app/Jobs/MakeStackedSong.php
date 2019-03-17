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
                'ffprobe.binaries' => '/usr/bin/ffprobe' 
            ]);
        $this->working_dir = sha1(time() . '/';
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

        File::makeDirectory($this->working_dir);

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

                    $video = $this->ffmpeg->open(storage_path($this->working_dir) . basename($video->url));
                    $video->addFilter(new SimpleFilter(['-filter:a', 'volume='.$volume]))
                    ->filters();

                    $format = new X264();
                    $format->setAudioCodec("aac");

                    $video->save($format, storage_path($this->working_dir) . basename($video->url)); 

                }

        }

        $fileSongVideoPath = $this->buildStackedVideo($song_videos);
        $hashids = new Hashids('', 10);

        $disk = Storage::disk('gcs');
        
        $remote_storage_file_name = 'videos/' . $hashids->encode( auth()->user()->id ) . '/' . $hashids->encode( $song->id ) . 'mp4';

        $disk->put($remote_storage_file_name, Storage::disk('local')->get($fileSongVideoPath));

      }


      public function buildStackedVideo($song_videos)
      {
        $x = count($song_videos);

        $mp4_file = $song_videos->toArray();

        Log::error(basename($mp4_file[0]['video']['url']));

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

          $video = $this->ffmpeg->open($parentVideo);

          $video->addFilter(new SimpleFilter(['-i', $childVideo]))
                ->addFilter(new SimpleFilter(['-filter_complex', 'hstack']))
                ->filters();

          $format = new X264();
          $format->setAudioCodec("aac");

          $filepath = sha1(time()) . '.mp4';

          $video->save($format, storage_path($this->working_dir) . $filepath);

          return $filepath;
              
      }


}
