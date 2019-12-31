<?php

namespace App\Jobs;

use App\Models\Video;
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

class CalculateAudioVolumes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $video;
    protected $working_dir;

    public function __construct(Video $video)
    {
        $this->video = $video;
        $this->working_dir = sha1(time()) . '_video/';
    }

    /**
    * Execute the job.
    *
    * @return void
    */
    public function handle()
    {
        // Don't include YouTube videos in the stacked video
        $video = $this->video;
        $filePath = storage_path($this->working_dir) . 'log.txt';

        \Log::error("Handle - URL: " . $video->url);

        if (!$video->url) {
            return;
        }

        File::makeDirectory(storage_path($this->working_dir), 0755, true, true);

        $command = "ffmpeg -i {$video->url} -af astats=metadata=1:reset=1,ametadata=print:key=lavfi.astats.Overall.RMS_level:file={$filePath} -f null -";
        $response = shell_exec($command);

        if (file_exists($filePath)) {
            $data = file_get_contents($filePath);
            $data = explode("\n", $data);

            $obj = new \stdClass;
            $time = 0;
            $times = [];
            $min = 99999;
            $max = 0;

            foreach ($data as $item) {
                if (substr($item, 0, 5) == 'frame') {
                    preg_match_all('/:([\d\.]*)/', $item, $matches);
                    $time = ltrim($matches[0][2], ':');
                    $time = (floor($time) * 1000) + (($time - floor($time)) * 1000);
                    if (intval($time) > 10000) {
                        break;
                    }
                } else if (strpos($item, '=-') !== false) {
                    $parts = explode('=-', $item);
                    $volume = floatval($parts[1]);

                    if ($volume < 20) {
                        $min = 20;
                    } else if ($volume > 100) {
                        $max = 100;
                    } else {
                        $times[$time] = $volume;
                        if ($volume > $max) {
                            $max = $volume;
                        } else if ($volume < $min) {
                            $min = $volume;
                        }
                    }
                }
            }

            foreach ($times as $time => $volume) {
                $obj->$time = round($max - $volume, 4);
            }

            $video = $video->fresh();
            $video->volume_data = json_encode($obj);
            $video->max_volume = round($max, 4);
            $video->save();
        }

        //File::deleteDirectory(storage_path($this->working_dir));
    }
}
