<?php

namespace App\Jobs;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\Song;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use FFMpeg\Filters\Audio\SimpleFilter;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use FFMpeg\Coordinate\TimeCode;

class UploadSongToTwitter implements ShouldQueue
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
            exit;
        }

        $filename = storage_path(sha1(time()) . '.mp4');
        $filenameTrimmed = storage_path(sha1(time()) . '_trimmed.mp4');
        file_put_contents($filename, fopen($song->video_url, 'r'));

        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/bin/ffprobe',
            'timeout'          => 0,
            'ffmpeg.threads'   => 12,
        ]);

        $video = $ffmpeg->open($filename);
        $video->addFilter(new SimpleFilter(['-i', public_path('images/watermark.png')]));
        $filter = "[0:v]trim=start=0:end=30,setpts=PTS-STARTPTS[1:v][1:v]overlay='if(gte(t,1), -w+(t-1)*200, NAN)':(main_h-overlay_h)/2[v];[0:a]atrim=start=0:end=30,asetpts=PTS-STARTPTS[a]";

        $video->addFilter(new SimpleFilter(['-filter_complex', $filter]))
            ->addFilter(new SimpleFilter(['-map', '[v]']))
            ->addFilter(new SimpleFilter(['-map', '[a]']))
            ->addFilter(new SimpleFilter(['-ac', '2']))
            ->filters();

        $format = new X264();
        $format->setPasses(1)
            ->setAudioCodec('aac')
            ->setKiloBitrate(1200)
            ->setAudioChannels(2)
            ->setAudioKiloBitrate(192)
            ->setAdditionalParameters([
                '-vcodec', 'libx264',
                '-vprofile', 'baseline',
                '-level', 3.0,
                '-movflags', 'faststart',
                '-pix_fmt', 'yuv420p',
            ]);

        $video->save($format, $filenameTrimmed);

        $twitter = new TwitterOAuth(
            config('services.twitter.consumer_key'),
            config('services.twitter.consumer_secret'),
            config('services.twitter.access_token'),
            config('services.twitter.access_secret')
        );
        $twitter->setTimeouts(120, 60);

        $response = $twitter->upload('media/upload', [
            'media' => $filenameTrimmed,
            'media_type' => 'video/mp4'
        ], true);

        unlink($filename);
        unlink($filenameTrimmed);

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
