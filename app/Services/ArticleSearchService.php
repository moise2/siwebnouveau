<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class ArticleSearchService
{
    /**
     * Recherche d'articles avec filtres de catégories, mois, années et texte.
     *
     * @param string|null $searchQuery
     * @param array|null $categories
     * @param array|null $months
     * @param array|null $years
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function searchArticles($query = null, $filters = [])
    {




        $articles = Post::select("posts.*")->join('category_post', 'category_post.post_id', 'posts.id')
            ->join('categories', 'categories.id', 'category_post.category_id')->with("categories");

        $articles = $articles->where([['posts.title', 'LIKE', '%' . $query . '%']]);
        if (count($filters) > 0 && count($filters['months']) > 0)
            $articles = $articles->whereRaw("MONTH(posts.published_at) IN (" . implode(',', $filters['months']) . ")");
        if (count($filters) > 0 && count($filters['years']) > 0)
            $articles = $articles->whereRaw("YEAR(posts.published_at) IN (" . implode(',', $filters['years']) . ")");
        if (count($filters) > 0 && count($filters['categories']) > 0)
            $articles = $articles->whereRaw("categories.id IN (" . implode(',', $filters['categories']) . ")");

        return $articles->paginate(44)->map(function ($article) {
            return [
                'title' => $article->title,
                'category' => $article->categories->pluck('name')->implode(', '),
                'slug' => $article->slug,
                'published_at' => $article->published_at,
                'file_url' => asset('storage/' . $article->image),
            ];
        });



        // Démarrer une requête de base sur les articles
        // $query = Post::query();

        // // Filtrer par texte de recherche si fourni
        // if (!empty($searchQuery)) {
        //     $query->where(function ($subQuery) use ($searchQuery) {
        //         $subQuery->where('title', 'like', '%' . $searchQuery . '%')
        //             ->orWhere('body', 'like', '%' . $searchQuery . '%');
        //     });
        // }

        // Filtrer par catégories si sélectionnées
        // if (!empty($categories)) {
        //     $query->whereHas('categories', function ($subQuery) use ($categories) {
        //         $subQuery->whereIn('categories.id', $categories);
        //     });
        // }

        // // Filtrer par mois si sélectionnés
        // if (!empty($months)) {
        //     $query->where(function ($subQuery) use ($months) {
        //         $subQuery->whereIn(DB::raw('MONTH(published_at)'), $months);
        //     });
        // }

        // // Filtrer par années si sélectionnées
        // if (!empty($years)) {
        //     $query->whereIn(DB::raw('YEAR(published_at)'), $years);
        // }

        // // Retourner les résultats paginés (20 par page)
        // return $query->paginate(20);
    }
}
