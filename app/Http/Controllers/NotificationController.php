<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\WebNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function sendNotification()
    {
        $user = User::first(); // Vous pouvez sélectionner un utilisateur spécifique

        $user->notify(new WebNotification(
            'Bienvenue sur notre site',
            'Nous avons un nouveau message pour vous',
            'Explorez nos fonctionnalités dès maintenant.',
            'https://via.placeholder.com/150'
        ));

        return redirect()->back()->with('success', 'Notification envoyée avec succès!');
    }
}
