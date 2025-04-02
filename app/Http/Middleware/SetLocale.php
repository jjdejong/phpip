<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        // Check if the session has started
        if ($request->hasSession()) {
            // If user is authenticated and has a language preference, use it
            if (Auth::check() && Auth::user()->language) {
                $userLocale = Auth::user()->language;
                
                // Extract the language code ('en' from 'en_US' or 'fr' from 'fr')
                $languageCode = explode('_', $userLocale)[0];
                
                // Set the application locale
                app()->setLocale($languageCode);
                
                // Store the full locale for date formatting
                if ($request->session()->isStarted()) {
                    $request->session()->put('formatting_locale', $userLocale);
                }
            }
        }
        return $next($request);
    }
}