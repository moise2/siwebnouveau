<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    //
    public function index()
    {
        $articles = Post::published()->paginate(21); // Ou votre logique de récupération
        return view('frontend.pages.page_articles', compact('articles'));
    }
}
