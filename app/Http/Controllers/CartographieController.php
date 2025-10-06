<?php

namespace App\Http\Controllers;

use App\Models\Localite;
use App\Models\Post;
use Illuminate\Http\Request;

class CartographieController extends Controller
{
    // Afficher la liste des articles de la catégorie "Sociale" avec pagination
    public function index()
    {
        $title = 'Cartographie des réformes';
    

        $localites = Localite::with('projets', 'programmes')->get();
        

        return view('frontend.pages.page_cartographie', compact('title','localites')); // Passer la variable aux vues
    }
}
