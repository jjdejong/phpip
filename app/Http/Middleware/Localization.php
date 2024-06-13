<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class Localization
{
    /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
    public function handle(Request $request, Closure $next)
    {
        if (is_null(Auth::user())) {
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            $acceptLang = ['fr', 'de', 'en']; 
            App::setLocale(in_array($lang, $acceptLang) ? $lang : 'en');
        }
        else {
            App::setLocale(Auth::user()->language);
        }
        return $next($request);
    }
}