<?php

namespace App\Jobs;

use App\Models\Song;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $this->song = $song
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $video_count = count($song->videos);
        $song_videos = $song->song_videos;

        $working_dir = sha1(time());

        foreach($song_videos as $song_video)
        {
            $song = $song_video->song;
            $video = $song_video->video;

            $client->request('GET', $video->url, ['sink' => '/path/to/file']);

        }
    }
}
