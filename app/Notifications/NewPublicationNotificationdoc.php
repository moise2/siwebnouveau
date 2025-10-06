<?php
namespace App\Notifications;

use App\Models\Document;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewPublicationNotification extends Notification 
{
    use Queueable;

    protected $publication;
    public $document;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function via($notifiable)
    {
        return ['mail','database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Nouvelle publication sur Togoreformes')
                    ->greeting('Bonjour,')
                    ->line('Une nouvelle publication est disponible : ' . $this->document->title)
                    ->action('Voir 21054la publication', url('/documents/' . $this->document->id))
                    ->line('Merci de rester informé avec Togoreformes !');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->document->title,
            'url' => url('/document/' . $this->document->id),
        ];
    }
}
