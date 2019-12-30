<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\Twitter\TwitterChannel;
use NotificationChannels\Twitter\TwitterStatusUpdate;

class SongApproved extends Notification
{
    public function via($song)
    {
        return [
            TwitterChannel::class,
            //FacebookPosterChannel::class,
        ];
    }

    public function toTwitter($song)
    {
        $tweet = 'New Song!! 🙌 ' . $song->title . ' 🎵 🎶';

        if ($handle = $song->twitterHandle()) {
            $tweet .= ' ' . $handle;
        }

        $tweet .= "\n" . $song->url;

        return new TwitterStatusUpdate($tweet);
    }
}
