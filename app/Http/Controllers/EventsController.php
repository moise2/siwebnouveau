<?php

namespace App\Http\Controllers;

use App\Models\Event; // Modèle pour les événements
use Illuminate\Http\Request;

class EventsController extends Controller
{
    // Afficher la liste des événements avec pagination
    public function index()
    {
        $events = Event::paginate(21); // 21 événements par page
        return view('frontend.pages.page_agenda', compact('events')); // Adapter la vue pour les événements
    }

    // Afficher un événement spécifique par slug
    public function show($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        // Récupérer l'événement précédent
        $previousEvent = Event::where('id', '<', $event->id)
            ->orderBy('id', 'desc')
            ->first();

        // Récupérer l'événement suivant
        $nextEvent = Event::where('id', '>', $event->id)
            ->orderBy('id', 'asc')
            ->first();

        return view('frontend.pages.page_singleagenda', [
            'event' => $event,
            'previousEvent' => $previousEvent,
            'nextEvent' => $nextEvent,
        ]);
    }

    // Méthode pour la recherche des événements
    public function search(Request $request)
    {
        $query = $request->input('query');
        // Récupération de la requête de recherche

        // Recherche sur le titre des événements
        if (empty($query)) {
            // Si aucune recherche n'est faite, renvoyer tous les événements
            $events = Event::all();
        } else {
            // Recherche sur le titre des événements
            $events = Event::where('title', 'like', "%{$query}%")->get();
        }
        // Retour des résultats sous forme de JSON
        return response()->json($events);
    }
}
