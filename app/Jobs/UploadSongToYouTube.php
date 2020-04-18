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

    public $tries = 1;

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

        if (! $song->is_public || $song->deleted_at) {
            return;
        }

        /*
        if ($song->youtube_id && $song->youtube_id != $song->youtube_published_id) {
            Youtube::delete($song->youtube_id);
        }
        */

        $filename = storage_path(sha1(time()) . 'mp4');
        file_put_contents($filename, fopen($song->video_url, 'r'));

        $tags = ['mudeo'];
        if ($song->genre_id) {
            $tags[] = $song->genre();
        }

        $video = Youtube::upload($filename, [
            'title' => $song->title,
            'description' => $song->url . " • https://mudeo.app\n\n" . $song->description,
            'tags' => $tags,
            'category_id' => 10,
        ], 'unlisted');

        $song->youtube_id = $video->getVideoId();
        $song->save();

        unlink($filename);
    }
}
