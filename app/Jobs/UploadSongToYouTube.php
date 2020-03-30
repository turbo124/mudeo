<?php

namespace App\Jobs;

use Youtube;
use App\Models\Song;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UploadSongToYouTube implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $song;

    public function __construct(Song $song)
    {
        $this->song = $song;
    }

    /**
    * Execute the job.
    *
    * @return void
    */
    public function handle()
    {
        $song = $this->song;
        $filename = storage_path(sha1(time()) . 'mp4');

        file_put_contents($filename, fopen($song->video_url, 'r'));

        $video = Youtube::upload($filename, [
            'title' => $song->title,
            'description' => $song->description,
            'tags' => ['mudeo'],
            'category_id' => 10,
        ], 'unlisted');

        $song->youtube_id = $video->getVideoId();
        $song->save();

        unlink($filename);
    }
}
