<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Services\ArticleSearchService;

class ArticlesController extends Controller
{
    protected $searchService;

    // Injecter le service dans le contrôleur
    public function __construct(ArticleSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Affiche tous les articles avec pagination.
     */
    public function index()
    {
        $title = 'Tous les Articles';
        // Récupérer tous les articles avec leurs catégories, sans filtrer par catégorie
        //$articles = Post::with('categories')->paginate(44);
        $articles = Post::orderBy('published_at', 'desc')->paginate(45);

        $hasCategory = $articles->isNotEmpty();

        $autoSelectedCategories = Category::pluck('id')->toArray();

        $autoSelectedCount = count($autoSelectedCategories);
        return view('frontend.pages.page_articles', compact('articles', 'title', 'hasCategory', 'autoSelectedCategories', 'autoSelectedCount'));
    }

    /**
     * Affiche un article spécifique avec les articles précédents et suivants.
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        
        $article = Post::where('slug', $slug)->firstOrFail();
        
        // Obtenir d'autres articles en fonction des catégories
        $relatedArticles = Post::where('id', '!=', $article->id)
            ->whereHas('categories', function ($query) {
                $query->whereIn('categories.id', [30]); // Assurez-vous de spécifier le nom de la table 'categories'
            })
            ->limit(4)
            ->get();

        // Récupérer l'article précédent et suivant
        $previousArticle = Post::where('id', '<', $article->id)->orderBy('id', 'desc')->first();
        $nextArticle = Post::where('id', '>', $article->id)->orderBy('id')->first();
        
        // Récupérer des articles similaires (basés sur les catégories de l'article)
        // $relatedArticles = Post::where('id', '!=', $article->id)
        //     ->whereHas('categories', function ($query) use ($article) {
        //         $query->whereIn('id', $article->categories->pluck('id'));
        //     })
        //     ->take(4)
        //     ->get();
        
        return view('frontend.pages.page_article', compact('article', 'previousArticle', 'nextArticle', 'relatedArticles'));
    }

    /**
     * Recherche d'articles avec filtres (texte, catégories, mois, années).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        //try {
        // Récupérer les filtres depuis la requête
        $query = $request->input('query', '');
        $filters = $request->input('filters', []);

        // Passer les filtres au service de recherche
        $articles = $this->searchService->searchArticles(
            $query,
            $filters
        );

        $isSingleCategory = count($articles) === 1 && isset($articles[0]->categories) && count($articles[0]->categories) === 1;

        return response()->json([
            'status' => 'success',
            'articles' => $articles, // Assurez-vous de renvoyer les éléments uniquement
            'isSingleCategory' => $isSingleCategory,
            'message' => count($articles) > 0 ? 'Articles trouvés' : 'Aucun article trouvé'
        ]);
        // } catch (\Exception $e) {
        //\Log::error('Erreur lors de la recherche : ' . $e->getMessage());
        //return response()->json(['error' => 'Erreur lors de la recherche.'], 500);
        //}
    }
}
