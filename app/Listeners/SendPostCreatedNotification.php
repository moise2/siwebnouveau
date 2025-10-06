<?php

namespace App\Listeners;

use App\Events\PostCreated;
use App\Models\Subscriber;
use App\Notifications\NewPublicationNotification; // Assurez-vous que la notification est correctement définie

class SendPostCreatedNotification
{
    public function handle(PostCreated $event)
    {
        // Récupérer tous les abonnés
        $subscribers = Subscriber::where('is_active', 1)
        ->where('verified', '>', 0)
        ->get();

        // Envoyer la notification à chaque abonné
        foreach ($subscribers as $subscriber) {
            // Envoi de la notification
            $subscriber->notify(new NewPublicationNotification($event->post));
        }
    }
}
