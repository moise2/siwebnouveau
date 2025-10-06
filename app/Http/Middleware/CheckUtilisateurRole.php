<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUtilisateur
{

    public function __construct()
{
    // $this->middleware('guest:utilisateur')->except('logout');
    // $this->middleware('check.utilisateur')->only('profil');
}


    public function handle(Request $request, Closure $next)
    {
        // Vérifie si l'utilisateur est connecté dans le guard 'utilisateur'
        if (!Auth::guard('utilisateur')->check()) {
            return redirect()->route('connexion')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        // Vérifie si l'utilisateur a le rôle 'utilisateur'
        $utilisateur = Auth::guard('utilisateur')->user();
        if ($utilisateur->role !== 'utilisateur') {
            return redirect()->route('home')->with('error', 'Accès refusé, vous devez être un utilisateur.');
        }

        // Si l'utilisateur a le bon rôle, on continue
        return $next($request);
    }
}
