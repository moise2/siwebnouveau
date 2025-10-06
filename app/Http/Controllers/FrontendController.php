<?php

// app/Http/Controllers/FrontendController.php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class FrontendController extends Controller
{

    public function articlesParCategorie(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $articles = $category->posts()->published()->paginate(6);

        return view('frontend.pages.page_categorie_article', [
            'category' => $category,
            'articles' => $articles,
        ]);
    }
}
