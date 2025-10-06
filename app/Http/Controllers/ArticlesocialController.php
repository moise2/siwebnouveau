<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class ArticlesocialController extends Controller
{
    // Afficher la liste des articles de la catégorie "Sociale" avec pagination
    public function index()
    {
        // Récupérer les articles de la catégorie "Sociale" uniquement
        $articlesociales = Post::whereHas('categories', function ($query) {
            $query->where('name', 'Sociale'); // Filtrer par la catégorie "Sociale"
        })->paginate(21); // 21 articles par page

        return view('frontend.pages.page_articlesocial', compact('articlesociales')); // Passer la variable aux vues
    }

    // Afficher un article spécifique par slug
    public function show($slug)
    {
        // Charger l'article avec la relation 'categories' et filtrer par la catégorie "Sociale"
        $articleSociale = Post::with(['categories' => function ($query) {
            $query->where('name', 'Sociale');
        }])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Vérifier si l'article appartient à la catégorie "Sociale"
        if ($articleSociale->categories->isEmpty()) {
            abort(404); // Si l'article n'appartient pas à "Sociale", retourner une 404
        }

        // Récupérer l'ID de la première catégorie (en supposant qu'il n'y a qu'une seule catégorie "Sociale")
        $categoryId = $articleSociale->categories->first()->id;

        // Récupérer les articles similaires (de la même catégorie "Sociale")
        $relatedArticles = Post::with('categories')
            ->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('categories.id', $categoryId);
            })
            ->where('id', '!=', $articleSociale->id)
            ->published()
            ->take(3)
            ->get();

        // Récupérer l'article précédent dans la catégorie "Sociale"
        $previousArticle = Post::whereHas('categories', function ($query) use ($categoryId) {
            $query->where('categories.id', $categoryId);
        })
            ->where('id', '<', $articleSociale->id)
            ->published()
            ->orderBy('id', 'desc')
            ->first();

        // Récupérer l'article suivant dans la catégorie "Sociale"
        $nextArticle = Post::whereHas('categories', function ($query) use ($categoryId) {
            $query->where('categories.id', $categoryId);
        })
            ->where('id', '>', $articleSociale->id)
            ->published()
            ->orderBy('id', 'asc')
            ->first();

        return view('frontend.pages.page_articlesocial', [
            'articleSociale' => $articleSociale,
            'relatedArticleSociales' => $relatedArticles,
            'previousArticleSociale' => $previousArticle,
            'nextArticleSociale' => $nextArticle,
        ]);
    }

    // Méthode pour la recherche des articles avec filtres
    public function search(Request $request)
    {
        $query = $request->input('query');
        $filters = $request->input('filters', []);

        $articles = Post::where('title', 'LIKE', '%' . $query . '%')
            ->whereHas('categories', function ($query) {
                $query->where('name', 'Sociale');
            });

        if (!empty($filters)) {
            if (isset($filters['categories']) && !empty($filters['categories'])) {
                $articles->whereHas('categories', function ($query) use ($filters) {
                    $query->whereIn('name', $filters['categories']);
                });
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
