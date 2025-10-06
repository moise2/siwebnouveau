<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WebNotification extends Notification
{
    use Queueable;

    public $title;
    public $message;
    public $image;

    public function __construct($title, $message, $image)
    {
        $this->title = $title;
        $this->message = $message;
        $this->image = $image;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'image' => $this->image,
        ];
    }
}
