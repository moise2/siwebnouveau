<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PostController extends Controller
{
    public function show($slug)
    {
        // Charger l'article avec les relations 'categories'
        $article = Post::with('categories')
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Récupérer l'ID de la première catégorie ou 1 si aucune catégorie
        $categoryId = optional($article->categories->first())->id ?? 1;

        // Récupérer les articles similaires (avec la relation 'categories')
        $relatedArticles = Post::with('categories')
            ->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('categories.id', $categoryId);
            })
            ->where('id', '!=', $article->id)
            ->published()
            ->take(3)
            ->get();

        // Récupérer l'article précédent
        $previousArticle = Post::where('id', '<', $article->id)
            ->published()
            ->orderBy('id', 'desc')
            ->first();

        // Récupérer l'article suivant
        $nextArticle = Post::where('id', '>', $article->id)
            ->published()
            ->orderBy('id', 'asc')
            ->first();

        return view('frontend.pages.page_article', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'previousArticle' => $previousArticle,
            'nextArticle' => $nextArticle,
        ]);
    }
}
