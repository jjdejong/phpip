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
                
                // Set the application locale to the full locale (e.g., 'en_US', 'fr')
                // Laravel will automatically extract the primary language for translations
                app()->setLocale($userLocale);
                
                // No need to store separate formatting_locale anymore, we use the same locale for everything
                if ($request->session()->isStarted()) {
                    $request->session()->put('locale', $userLocale);
                }
            }
        }
        return $next($request);
    }
}