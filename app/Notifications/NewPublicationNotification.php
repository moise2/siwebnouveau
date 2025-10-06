<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewPublicationNotification extends Notification
{
    use Queueable;

    protected $type;
    protected $publication;

    // Le constructeur attend deux arguments : $type et $publication
    public function __construct($type, $publication)
    {
        $this->type = $type;
        $this->publication = $publication;
    }

    // Définir les canaux de notification : mail et base de données
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    // Définir le contenu de l'email
    public function toMail($notifiable)
    {
        // Définir le titre et le message en fonction du type

        $title = ($this->type === 'post') ? "Nouvelle publication sur Togoreformes" : "Nouveau document sur Togoreformes";
        $message = ($this->type === 'post') ? "Une nouvelle publication est disponible" : "Un nouveau document est disponible";

        // Définir l'URL en fonction du type
        if ($this->type === 'post') {
        // Pour les 'post', on utilise le slug avec le préfixe '/articles/'
        $url = url('/articles/' . $this->publication->slug);
    } else {
        // Pour les 'document', on utilise le slug (ou l'ID si pas de slug) avec le préfixe '/documents/'
        // Assurez-vous que vos documents ont aussi un slug, sinon ajustez ici.
        // Si la route des documents utilise l'ID, gardez $this->publication->id
        $url = url('/documents/' . $this->publication->slug); 
    }


        return (new MailMessage)
                    ->subject($title)
                    ->greeting('Bonjour,')
                    ->line($message . " : " . $this->publication->title)
                    ->action('Voir la publication', $url)
                    ->line('Merci de rester informé avec Togoreformes !');
    }

    // Définir le contenu de la notification dans la base de données
    public function toArray($notifiable)
    {
 if ($this->type === 'post') {
        $url = url('/articles/' . $this->publication->slug);
    } else {
        $url = url('/documents/' . $this->publication->slug);
    }

    return [
        'title' => $this->publication->title,
        'url' => $url, // L'URL corrigée est utilisée ici
    ];
    }
}
