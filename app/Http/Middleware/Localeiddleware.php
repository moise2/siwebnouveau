<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class Localeiddleware
{
    public function handle($request, Closure $next)
    {
       if(session()->has('locale')){
        App::setLocale(session()->get('locale'));
       }
        return $next($request);
    }
}