<?php

namespace App\Jobs;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\Song;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use FFMpeg\Coordinate\TimeCode;

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

        $filename = storage_path(sha1(time()) . 'mp4');
        file_put_contents($filename, fopen($song->video_url, 'r'));

        $ffmpeg = FFMpeg\FFMpeg::create();
        $video = $ffmpeg->open($filename);
        $video->filters()->clip(
            FFMpeg\Coordinate\TimeCode::fromSeconds(0),
            FFMpeg\Coordinate\TimeCode::fromSeconds(30));
        $video->save(new FFMpeg\Format\Video\X264(), $filename . 'trimmed');

        $twitter = new TwitterOAuth(
            config('services.twitter.consumer_key'),
            config('services.twitter.consumer_secret'),
            config('services.twitter.access_token'),
            config('services.twitter.access_secret')
        );
        $twitter->setTimeouts(120, 60);

        $response = $twitter->upload('media/upload', [
            'media' => $filename . 'trimmed',
            'media_type' => 'video/mp4'
        ], true);

        unlink($filename);
        unlink($filename . 'trimmed');

        \Log::error('UPLOAD RESPONSE: ' . json_encode($response));

        if (property_exists($response, 'errors')) {
            $song->twitter_id = 'failed_to_upload:' . $response->errors[0]->message;
            $song->save();
            exit;
        }

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
            'media_ids' => $response->media_id_string
        ];

        $response = $twitter->post('statuses/update', $parameters);

        \Log::error('TWEET RESPONSE: ' . json_encode($response));

        if (property_exists($response, 'errors')) {
            $song->twitter_id = 'failed_to_upload:' . $response->errors[0]->message;
        } else {
            $song->twitter_id = $response->id;
        }

        $song->save();
    }
}
