<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Event;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class SearchService
{
    /**
     * Effectue une recherche sur les modèles Documents, Events et Posts selon les filtres.
     *
     * @param string|null $query
     * @param array $tables
     * @param array $months
     * @param array $years
     * @param int $perPage
     * @param int $page
     * @return array
     */
    public function search($query, $tables, $months, $years, $perPage, $page)
    {
        $results = [];

        // Recherche dans la table Documents si sélectionné
        if (in_array('documents', $tables)) {
            $documents = Document::join('category_document', 'category_document.document_id', '=', 'documents.id')
                ->join('categories', 'categories.id', '=', 'category_document.document_category_id')
                ->when($query, function ($q) use ($query) {
                    return $q->where('documents.title', 'like', "%{$query}%")
                        ->orWhere('documents.description', 'like', "%{$query}%");
                })
                ->when($months, function ($q) use ($months) {
                    return $q->whereIn(DB::raw('MONTH(documents.date_publication)'), $months);
                })
                ->when($years, function ($q) use ($years) {
                    return $q->whereIn(DB::raw('YEAR(documents.date_publication)'), $years);
                })
                ->orderBy('documents.date_publication', 'desc') 
                ->select(
                    'documents.*',
                    'categories.name as category_name'
                )
                ->paginate($perPage, ['*'], 'page', $page);
        
            $results['documents'] = $documents;
        }
        

        // Recherche dans la table Events si sélectionné
        if (in_array('events', $tables)) {
            $events = Event::query()
                ->when($query, function ($q) use ($query) {
                    return $q->where('title', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                })
                ->when($months, function ($q) use ($months) {
                    return $q->whereIn(DB::raw('MONTH(start_date)'), $months);
                })
                ->when($years, function ($q) use ($years) {
                    return $q->whereIn(DB::raw('YEAR(start_date)'), $years);
                })
                ->orderBy('events.start_date', 'desc') 
                ->paginate($perPage, ['*'], 'page', $page); // Pagination
            $results['events'] = $events;
        }

        // Recherche dans la table Posts si sélectionné
        if (in_array('posts', $tables)) {
            $posts = Post::query()
                ->when($query, function ($q) use ($query) {
                    return $q->where('title', 'like', "%{$query}%")
                        ->orWhere('body', 'like', "%{$query}%");
                })
                ->when($months, function ($q) use ($months) {
                    return $q->whereIn(DB::raw('MONTH(published_at)'), $months);
                })
                ->when($years, function ($q) use ($years) {
                    return $q->whereIn(DB::raw('YEAR(published_at)'), $years);
                })
                ->orderBy('posts.published_at', 'desc') 
                ->paginate($perPage, ['*'], 'page', $page); // Pagination
            $results['posts'] = $posts;
        }

        // Retourne les résultats paginés pour chaque entité
        return $results;
    }

    /**
     * Récupère toutes les catégories des trois modèles pour les dropdowns.
     *
     * @return \Illuminate\Support\Collection
     */
public function getAllCategories()
{
    // Récupère les catégories uniques associées aux Documents (y compris ceux sans catégorie)
    $documentCategories = Document::with('categories')->get()
        ->flatMap(function ($document) {
            return $document->categories->isEmpty() 
                ? [null] // Inclure les documents sans catégorie
                : $document->categories;
        })
        ->filter() // Retire les valeurs null si nécessaire
        ->unique('id');

    // Récupère les catégories uniques associées aux Posts
    $postCategories = Post::with('categories')->get()
        ->pluck('categories')
        ->flatten()
        ->unique('id');

    // Combine et retourne les catégories uniques (uniquement documents et posts)
    return $documentCategories
        ->merge($postCategories)
        ->unique('id');
}
}
