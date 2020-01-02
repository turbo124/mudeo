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
        $tweet = "Title: " . $song->title . " 🎵 🎶\nArtist: ";

        if ($handle = $song->user->twitterHandle()) {
            $tweet .= $handle;
        } else {
            $tweet .= $song->user->handle;
        }

        $tweet .= "\n" . $song->url . ' #mudeo';

        if ($song->genre_id) {
            $map = [
                1 => 'African',
                2 => 'Arabic',
                3 => 'Asian',
                4 => 'AvantGarde',
                5 => 'Blue',
                6 => 'Caribbean',
                7 => 'ClassicalMusic',
                8 => 'Comedy',
                9 => 'Country',
                10 => 'EasyListening',
                11 => 'Electronic',
                12 => 'Folk',
                13 => 'HipHop',
                14 => 'Jazz',
                15 => 'Latin',
                16 => 'Pop',
                17 => 'Soul',
                18 => 'Rock',
                19 => 'Other',
            ];

            $tweet .= ' #' . $map[$song->genre_id];
        }

        return new TwitterStatusUpdate($tweet);
    }
}
