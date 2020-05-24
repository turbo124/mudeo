<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Twitter\TwitterChannel;
use NotificationChannels\Twitter\TwitterStatusUpdate;

class SongJoined extends Notification
{
    public function __construct($song, $user)
    {
        $this->song = $song;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $user = $this->user;
        $song = $this->song;

        return (new MailMessage)
                    ->subject($user->name . ' has joined ' . $song->title . ' 🎉')
                    ->greeting('Hello!')
                    ->line('Your song ' . $song->title . ' has a new collaborator')
                    ->line($user->name . ' - @' . $user->handle);
    }
}
