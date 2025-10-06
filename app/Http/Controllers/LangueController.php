<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App; // Importez la façade App
use Illuminate\Support\Facades\Session; // Importez la façade Session
use Illuminate\Support\Facades\Redirect; // Importez la façade Redirect si vous l'utilisez

class LangueController extends Controller
{
    public function changeLanguage($lang)
    {
        // 1. Vérifiez si la langue est supportée (optionnel mais recommandé)
        $supportedLocales = array_keys(config('app.locales')); // Assurez-vous d'avoir 'locales' dans config/app.php
        if (!in_array($lang, $supportedLocales)) {
            // Loggez ou gérez l'erreur si une langue non supportée est demandée
            abort(400, 'Langue non supportée.');
        }

        // 2. Définissez la locale dans la session
        Session::put('locale', $lang);

        // 3. Définissez la locale pour la requête actuelle de Laravel
        App::setLocale($lang);

        // 4. Redirigez l'utilisateur vers la page précédente
        return Redirect::back();
    }
}