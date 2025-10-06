<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUtilisateur
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifie si l'utilisateur est connecté dans le guard 'utilisateur'
        if (!Auth::guard('utilisateur')->check()) {
            return redirect()->route('connexion')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        // Vérifie si l'utilisateur est actif
        $utilisateur = Auth::guard('utilisateur')->user();

        if (!$utilisateur->is_active) {
            Auth::guard('utilisateur')->logout();
            return redirect()->route('connexion')->with('error', 'Votre compte est inactif ou non approuvé.');
        }

        // Si l'utilisateur est actif, on continue
        return $next($request);
    }
}
