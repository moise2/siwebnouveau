<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PtfController extends Controller
{
    public function index()
    {
        $chiffresCles = session('chiffresCles');

        // Logique pour afficher le tableau de bord PTF
        return view('ptf.profils', compact('chiffresCles'));
    }
}
