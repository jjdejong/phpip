<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = config('app.fallback_locale');
        
        // If user is authenticated, set locale based on user's language preference
        if (Auth::check() && Auth::user()->language) {
            $locale = Auth::user()->language;
        }
            
        // For UI translations, use just the language part (before underscore if present)
        // e.g., 'en' from 'en_US'
        $uiLanguage = explode('_', $locale)[0];
        App::setLocale($uiLanguage);
            
        // Store the full locale for date/number formatting (e.g., 'en_US')
        // This is used by FormatHelper for locale-specific formatting
        session(['formatting_locale' => $locale]);

        return $next($request);
    }
}