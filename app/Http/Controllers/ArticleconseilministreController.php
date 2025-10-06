<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class ArticleconseilministreController extends Controller
{
    // Afficher la liste des articles de la catégorie "réforme" avec pagination
    public function index()
    {
        $title = 'Actualités sur le Conseil des Ministres';
        // Récupérer les articles de la catégorie "réforme" uniquement
        $articles = Post::whereHas('categories', function ($query) {
            $query->where('name', 'Conseil des Ministres'); // Remplace 'name' par le champ approprié dans ta table catégories
        })->paginate(44); // 21 articles par page
        $hasCategory = $articles->isNotEmpty();

        $autoSelectedCategories = Category::where('name', 'Conseil des Ministres')->pluck('id')->toArray();

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

        // Requête pour cibler uniquement les articles de la catégorie "conseil des ministres"
        $articles = Post::whereHas('categories', function ($query) {
            $query->where('name', 'conseil des ministres'); // Assurez-vous que 'name' est le bon champ pour la catégorie
        });

        // Vérifier le titre en plus de la catégorie
        if ($query) {
            $articles->where('title', 'LIKE', '%' . $query . '%');
        }

        // Application des autres filtres si présents
        if (!empty($filters)) {
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
