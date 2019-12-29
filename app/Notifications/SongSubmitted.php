<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Twitter\TwitterChannel;
use NotificationChannels\Twitter\TwitterStatusUpdate;

class SongSubmitted extends Notification
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
                    ->subject('New song: ' . $this->song->title)
                    ->greeting('Hello!')
                    ->line('A new song has been submitted!')
                    ->action('View Song', $this->song->url);
    }
}
