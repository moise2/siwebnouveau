<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class ArticleactualiteController extends Controller
{
    // Afficher la liste des articles de la catégorie "actualité" avec pagination
    public function index()
    {
        // Récupérer les articles de la catégorie "actualité" uniquement
        $articleactualites = Post::whereHas('categories', function ($query) {
            $query->where('name', 'actualité'); // Filtrer par catégorie "actualité"
        })->paginate(21); // 21 articles par page

        return view('frontend.pages.page_articlenews', compact('articleactualites')); // Passer la variable aux vues
    }

    // Afficher un article spécifique par slug
    public function show($slug)
    {
        // Charger l'article avec la relation 'categories' et filtrer par la catégorie "actualité"
        $articleactualite = Post::with(['categories' => function ($query) {
            $query->where('name', 'actualité');
        }])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Vérifier si l'article appartient à la catégorie "actualité"
        if ($articleactualite->categories->isEmpty()) {
            abort(404); // Si l'article n'appartient pas à "actualité", retourner une 404
        }

        // Récupérer l'ID de la première catégorie (en supposant qu'il n'y a qu'une seule catégorie "actualité")
        $categoryId = $articleactualite->categories->first()->id;

        // Récupérer les articles similaires (de la même catégorie "actualité")
        $relatedArticles = Post::with('categories')
            ->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('categories.id', $categoryId);
            })
            ->where('id', '!=', $articleactualite->id)
            ->published()
            ->take(3)
            ->get();

        // Récupérer l'article précédent dans la catégorie "actualité"
        $previousArticle = Post::whereHas('categories', function ($query) use ($categoryId) {
            $query->where('categories.id', $categoryId);
        })
            ->where('id', '<', $articleactualite->id)
            ->published()
            ->orderBy('id', 'desc')
            ->first();

        // Récupérer l'article suivant dans la catégorie "actualité"
        $nextArticle = Post::whereHas('categories', function ($query) use ($categoryId) {
            $query->where('categories.id', $categoryId);
        })
            ->where('id', '>', $articleactualite->id)
            ->published()
            ->orderBy('id', 'asc')
            ->first();

        return view('frontend.pages.page_articlenews', [
            'articleactualite' => $articleactualite,
            'relatedArticleactualites' => $relatedArticles,
            'previousArticleactualite' => $previousArticle,
            'nextArticleactualite' => $nextArticle,
        ]);
    }


    // Méthode pour la recherche des articles (déjà implémentée)
    public function search(Request $request)
    {
        $query = $request->input('query');
        $filters = $request->input('filters', []);

        $articles = Post::where('title', 'LIKE', '%' . $query . '%');

        if (!empty($filters)) {
            if (isset($filters['categories']) && !empty($filters['categories'])) {
                $articles->whereIn('category', $filters['categories']);
            }

            if (isset($filters['year']) && !empty($filters['year'])) {
                $articles->whereYear('created_at', $filters['year']);
            }

            if (isset($filters['month']) && !empty($filters['month'])) {
                $articles->whereMonth('created_at', date('m', strtotime($filters['month'])));
            }
        }
        return response()->json($articles->get());
    }
}
