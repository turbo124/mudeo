<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UploadSongToYouTube implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


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
        $video = Youtube::upload($song->video_url, [
            'title' => $song->title,
            'description' => $song->description,
            'tags' => ['mudeo'],
            'category_id' => 10,
        ], 'unlisted');

        $song->youtube_id = $video->getVideoId();
        $song->save();
    }
}
