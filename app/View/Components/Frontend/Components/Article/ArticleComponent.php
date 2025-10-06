<?php

namespace App\View\Components\Frontend\Components\Article;

use App\Models\Post;
use Illuminate\View\Component;

class ArticleComponent extends Component
{
    public $article;

    public function __construct(Post $article)
    {
        $this->article = $article;
    }

    public function render()
    {
        // Corrigez le chemin de la vue ici
        return view('frontend.components.article.article');
    }
}
