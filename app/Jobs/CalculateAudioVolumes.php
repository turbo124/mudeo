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
        $filePath = $this->working_dir . '/log.txt';

        shell_exec("ffmpeg -i {$video->url} -af astats=metadata=1:reset=1,ametadata=print:key=lavfi.astats.Overall.RMS_level:file={$filePath} -f null -");

        $video->volume_data = file_get_contents($filePath);
        $video->save();

        File::deleteDirectory(storage_path($this->working_dir));
    }
}
