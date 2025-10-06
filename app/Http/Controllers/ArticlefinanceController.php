<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class ArticlefinanceController extends Controller
{
    // Afficher la liste des articles de la catégorie "réforme" avec pagination
    public function index()
    {
        $title = 'Actualités sur l\'économie';
        // Récupérer les articles de la catégorie "réforme" uniquement
        $articles = Post::whereHas('categories', function ($query) {
            $query->where('name', 'Economie'); // Remplace 'name' par le champ approprié dans ta table catégories
        })->paginate(44); // 21 articles par page
        $hasCategory = $articles->isNotEmpty();

        $autoSelectedCategories = Category::where('name', 'Economie')->pluck('id')->toArray();

        $autoSelectedCount = count($autoSelectedCategories);
        return view('frontend.pages.page_articles', compact('articles', 'title', 'hasCategory', 'autoSelectedCategories', 'autoSelectedCount'));
    }

    // Afficher un article spécifique par slug
    public function show($slug)
    {
        // Charger l'article avec les relations 'categories'
        $article = Post::with('categories')
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Récupérer les articles similaires de la catégorie 'Economie'
        $relatedArticles = Post::with('categories')
            ->whereHas('categories', function ($query) {
                // Filtrer par la catégorie "Economie" (par nom ou ID)
                $query->where('categories.name', 'Economie');
                // Si tu connais l'ID de la catégorie "Economie", utilise ceci à la place :
                // $query->where('categories.id', 2); // Remplacer 2 par l'ID de "Economie"
            })
            ->where('id', '!=', $article->id)  // Exclure l'article actuel
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

        return view('frontend.pages.page_articlereformes', [
            'articlereformes' => $article,
            'relatedArticlereformes' => $relatedArticles,
            'previousArticlereforme' => $previousArticle,
            'nextArticlereforme' => $nextArticle,
        ]);
    }

    // Méthode pour la recherche des articles (déjà implémentée)
    public function search(Request $request)
    {
        $query = $request->input('query');
        $filters = $request->input('filters', []);

        // Début de la requête de recherche d'articles
        $articles = Post::where('title', 'LIKE', '%' . $query . '%');

        // Filtrer uniquement pour la catégorie "Économie"
        $articles->whereHas('categories', function ($q) {
            // Filtrer par le nom de la catégorie "Economie"
            $q->where('name', 'Economie');
            // Ou si tu connais l'ID de la catégorie "Économie", utilise l'ID :
            // $q->where('id', 2);  // Remplace 2 par l'ID de la catégorie
        });

        // Application des autres filtres si présents
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

        // Renvoyer les résultats sous forme de JSON
        return response()->json($articles->get());
    }
}
