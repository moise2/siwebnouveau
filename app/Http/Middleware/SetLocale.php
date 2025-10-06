<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Si une locale est présente en session ET qu'elle est parmi les locales configurées
        if (Session::has('locale') && array_key_exists(Session::get('locale'), config('app.locales'))) {
            App::setLocale(Session::get('locale'));
        } else {
            // Sinon, utilisez la locale par défaut définie dans config/app.php
            App::setLocale(config('app.locale'));
        }

        return $next($request);
    }
}