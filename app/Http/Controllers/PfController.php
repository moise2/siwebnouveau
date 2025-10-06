<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PfController extends Controller
{
    public function index()
    {
        // Récupérer les données des chiffres clés depuis la session
        $chiffresCles = session('chiffresCles');

        // Logique pour afficher le tableau de bord PF
        return view('pf.dashboard', compact('chiffresCles'));
    }
}
