<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Programme;
use App\Models\Projet;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Méthode pour afficher la page de tableau de bord
    public function index()
    {
        // Vous pouvez laisser cette méthode vide ou y ajouter une logique par défaut si nécessaire
        return view('frontend.pages.espaces.dashboard');
    }

    // Méthode pour déconnexion
    public function logout()
    {
        Auth::logout();
        // Redirige vers la route nommée 'login' avec un message de succès
        return redirect()->route('login')->with('message', 'Vous avez été déconnecté avec succès.');
    }

    // Méthode pour afficher les détails d'un projet
    public function showProject($id)
    {
        $project = Projet::findOrFail($id);

        // Vous pouvez retourner une vue ou une réponse JSON si besoin
        return view('project-details', ['project' => $project]);
    }

    // Méthode pour afficher la page d'accueil après connexion
    public function home()
    {
        return view('frontend.pages.home');
    }
}
