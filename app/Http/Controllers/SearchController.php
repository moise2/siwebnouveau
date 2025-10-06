<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Event;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SearchController extends Controller
{
    /**
     * Affiche la page de recherche.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Le titre de la page pour la vue frontend
        $title = "Recherche Globale";
        return view('frontend.pages.page_recherche', compact('title'));
    }

    /**
     * Gère la requête de recherche AJAX.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $tables = $request->input('tables', ['posts', 'events', 'documents']);
        $months = $request->input('months', []);
        $years = $request->input('years', []);
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);

        $results = new Collection();

        // Recherche dans les documents
        if (in_array('documents', $tables)) {
            $documents = Document::query()
                ->when($query, function ($q) use ($query) {
                    $q->where('title', 'like', '%' . $query . '%')
                      ->orWhere('description', 'like', '%' . $query . '%');
                })
                ->when(!empty($months), function ($q) use ($months) {
                    $q->whereIn(\DB::raw('MONTH(date_publication)'), $months);
                })
                ->when(!empty($years), function ($q) use ($years) {
                    $q->whereIn('year', $years);
                })
                ->with('categories') // Charger la relation pour l'accesseur category_name
                ->get()
                ->map(function ($document) {
                    $document->type = 'documents';
                    $document->category_name = $document->getCategoryAttribute(); // Utiliser l'accesseur
                    return $document;
                });
            $results = $results->merge($documents);
        }

        // Recherche dans les articles (Posts)
        if (in_array('posts', $tables)) {
            $posts = Post::query()
                ->when($query, function ($q) use ($query) {
                    $q->where('title', 'like', '%' . $query . '%')
                      ->orWhere('excerpt', 'like', '%' . $query . '%')
                      ->orWhere('body', 'like', '%' . $query . '%');
                })
                 ->when(!empty($months), function ($q) use ($months) {
                    $q->whereIn(\DB::raw('MONTH(published_at)'), $months);
                })
                ->when(!empty($years), function ($q) use ($years) {
                    $q->whereIn(\DB::raw('YEAR(published_at)'), $years);
                })
                ->get()
                ->map(function ($post) {
                    $post->type = 'posts';
                    return $post;
                });
            $results = $results->merge($posts);
        }

        // Recherche dans les événements
        if (in_array('events', $tables)) {
            $events = Event::query()
                ->when($query, function ($q) use ($query) {
                    $q->where('title', 'like', '%' . $query . '%')
                      ->orWhere('description', 'like', '%' . $query . '%')
                      ->orWhere('location', 'like', '%' . $query . '%');
                })
                ->when(!empty($months), function ($q) use ($months) {
                    // Pour les événements, on peut chercher dans start_date ou end_date
                    $q->where(function ($subQ) use ($months) {
                        $subQ->whereIn(\DB::raw('MONTH(start_date)'), $months)
                             ->orWhereIn(\DB::raw('MONTH(end_date)'), $months);
                    });
                })
                ->when(!empty($years), function ($q) use ($years) {
                     $q->where(function ($subQ) use ($years) {
                        $subQ->whereIn(\DB::raw('YEAR(start_date)'), $years)
                             ->orWhereIn(\DB::raw('YEAR(end_date)'), $years);
                    });
                })
                ->get()
                ->map(function ($event) {
                    $event->type = 'events';
                    return $event;
                });
            $results = $results->merge($events);
        }

        // Triez les résultats par date la plus récente
        $sortedResults = $results->sortByDesc(function ($item) {
            if ($item->type === 'documents') {
                return $item->date_publication;
            } elseif ($item->type === 'posts') {
                return $item->published_at;
            } elseif ($item->type === 'events') {
                return $item->start_date; // Ou end_date, selon la pertinence
            }
            return null;
        })->values(); // Réinitialiser les clés numériques après le tri

        // Manually paginate the collection
        $total = $sortedResults->count();
        $items = $sortedResults->forPage($page, $perPage);
        $paginator = new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        $documentsData = [];
        $postsData = [];
        $eventsData = [];

        // Répartir les résultats paginés par type pour le rendu côté client
        foreach ($paginator->items() as $item) {
            if ($item->type === 'documents') {
                $documentsData[] = $item->toArray();
            } elseif ($item->type === 'posts') {
                $postsData[] = $item->toArray();
            } elseif ($item->type === 'events') {
                $eventsData[] = $item->toArray();
            }
        }

        return response()->json([
            'documents' => [
                'data' => $documentsData,
            ],
            'posts' => [
                'data' => $postsData,
            ],
            'events' => [
                'data' => $eventsData,
            ],
            'pagination' => [
                'currentPage' => $paginator->currentPage(),
                'totalPages' => $paginator->lastPage(),
                'totalItems' => $paginator->total(),
                'perPage' => $paginator->perPage(),
            ]
        ]);
    }
}