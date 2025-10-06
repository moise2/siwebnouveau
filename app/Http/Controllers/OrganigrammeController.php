<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrganigrammeController extends Controller
{
    public function index()
    {
        $title = 'Organigramme';
        return view('frontend.pages.page_organigramme'); // La vue que tu souhaites renvoyer
    }
}
