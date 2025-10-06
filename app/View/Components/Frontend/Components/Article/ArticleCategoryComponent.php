<?php

namespace App\View\Components;

use App\Models\Category;
use Illuminate\View\Component;

class ArticleCategoryComponent extends Component
{
    public $category;
    public $articles;

    /**
     * Créer une nouvelle instance de composant.
     *
     * @param  Category $category
     * @return void
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
        // Limiter à 5 articles par catégorie
        $this->articles = $category->posts()->published()->limit(5)->get();
    }

    /**
     * Obtenir la vue / les contenus qui représentent le composant.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.article-category');
    }
}
