<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFive();
        Gate::define('client', fn ($user) => $user->default_role === 'CLI' || empty($user->default_role));
        Gate::define('except_client', fn ($user) => $user->default_role !== 'CLI' && !empty($user->default_role));
        Gate::define('admin', fn ($user) => $user->default_role === 'DBA');
        Gate::define('readwrite', fn ($user) => in_array($user->default_role, ['DBA', 'DBRW']));
        Gate::define('readonly', fn ($user) => in_array($user->default_role, ['DBA', 'DBRW', 'DBRO']));

        // Add query macro for case-insensitive JSON column queries
        \Illuminate\Database\Query\Builder::macro('whereJsonLike', function ($column, $value, $locale = null) {
            if (!$locale) {
                $locale = app()->getLocale();
                // Normalize to base locale (e.g., 'en' from 'en_US')
                $locale = substr($locale, 0, 2);
            }
            
            return $this->whereRaw(
                "JSON_UNQUOTE(JSON_EXTRACT($column, '$.$locale')) COLLATE utf8mb4_0900_ai_ci LIKE ?",
                ["$value%"]
            );
        });
    }
}
