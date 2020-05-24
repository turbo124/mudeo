<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SongBuilt extends Notification
{
    public function __construct($song)
    {
        $this->song = $song;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Your song is ready')
                    ->greeting('Hello!')
                    ->line('Your song ' . $this->song->title . ' has finished processing')
                    ->action('View Song', $this->song->url);
    }
}
