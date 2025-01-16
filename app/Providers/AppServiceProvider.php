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
    }
}
