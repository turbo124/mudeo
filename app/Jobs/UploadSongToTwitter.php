<?php

namespace App\Jobs;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\Song;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UploadSongToTwitter implements ShouldQueue
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

        if (! $song->is_public || $song->deleted_at) {
            exit;
        }

        $filename = storage_path(sha1(time()));
        file_put_contents($filename, fopen($song->video_url, 'r'));

        $twitter = new TwitterOAuth(
            config('services.twitter.consumer_key'),
            config('services.twitter.consumer_secret'),
            config('services.twitter.access_token'),
            config('services.twitter.access_secret')
        );
        $twitter->setTimeouts(120, 60);

        $result = $twitter->upload('media/upload', [
            'media' => $filename,
            'media_type' => 'video/mp4'
        ], true);

        $tweet = "New Song by ";

        if ($handle = $song->user->twitterHandle()) {
            $tweet .= $handle;
        } else {
            $tweet .= $song->user->handle;
        }

        $tweet .= " 🙌 " . $song->title . " 🎵 🎶 " . $song->url . " #mudeo";

        if ($song->genre_id) {
            $tweet .= ' #' . strtolower(str_replace(' ', '', $song->genre()));
        }

        $parameters = [
            'status' => $tweet,
            'media_ids' => $result->media_id_string
        ];

        $response = $twitter->post('statuses/update', $parameters);

        if ($response instanceof ErrorException) {
            //$song->twitter_id = 'failed_to_upload';
            $song->twitter_id = $response->getMessage();
        } else {
            $song->twitter_id = $response->id;
        }

        $song->save();

        unlink($filename);
    }
}
