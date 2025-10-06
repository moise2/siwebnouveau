<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Document;
use App\Models\Category; // Nécessaire si Category est utilisé directement ici, sinon pas obligatoire dans ce cas précis
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DocumentSearchService
{
    /**
     * Effectue une recherche de documents avec des filtres dynamiques.
     * Cette méthode renvoie un Paginator d'objets Document Eloquent,
     * en se basant sur les relations Eloquent correctement définies dans les modèles.
     *
     * @param string|null $categoryName Optionnel: le nom d'une catégorie spécifique à filtrer.
     * @param string|null $query Le terme de recherche.
     * @param array $filters Un tableau associatif de filtres ('months', 'years', 'categories').
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function search(?string $categoryName = null, ?string $query = null, array $filters = []): LengthAwarePaginator
    {
        // On commence la requête avec un eager loading pour la performance.
        // Cela fonctionnera car la relation 'categories' est bien définie dans le modèle Document.
        $documentsQuery = Document::with('categories');

        // Filtre par nom de catégorie principal, en utilisant la relation.
        if ($categoryName) {
            $documentsQuery->whereHas('categories', function ($q) use ($categoryName) {
                $q->where('name', $categoryName);
            });
        }

        // Filtre par terme de recherche sur le titre ou la description.
        if (!is_null($query) && $query !== '') {
            $documentsQuery->where(function ($q) use ($query) {
                $q->where('documents.title', 'LIKE', '%' . $query . '%')
                  ->orWhere('documents.description', 'LIKE', '%' . $query . '%');
            });
        }

        // Filtre par mois.
        if (!empty($filters['months'])) {
            $months = array_map('intval', $filters['months']);
            $documentsQuery->whereIn(DB::raw('MONTH(documents.date_publication)'), $months);
        }

        // Filtre par année.
        if (!empty($filters['years'])) {
            $years = array_map('intval', $filters['years']);
            $documentsQuery->whereIn(DB::raw('YEAR(documents.date_publication)'), $years);
        }

        // Filtre par IDs de catégories supplémentaires.
        if (!empty($filters['categories'])) {
            $categoryIds = array_map('intval', $filters['categories']);
            $documentsQuery->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            });
        }

        // On retourne l'objet Paginator SANS le transformer en tableau.
        // La vue se chargera de formater les données en utilisant les objets Eloquent.
        return $documentsQuery->orderBy('documents.date_publication', 'desc')->paginate(44); // Ajustez la pagination si besoin.
    }
}
