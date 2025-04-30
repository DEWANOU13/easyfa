<?php

namespace App\Providers;

use App\Services\FactureService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FactureService::class, function ($app) {
            return new FactureService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();

        // Vérifie si l'utilisateur possède toutes les permissions.
        Blade::if('canall', function (array $permissions) {
            foreach ($permissions as $permission) {
                if (Gate::denies($permission)) {
                    return false;
                }
            }
            return true;
        });

        // Vérifie si l'utilisateur possède au moins une des permissions.
        Blade::if('canany', function (array $permissions) {
            foreach ($permissions as $permission) {
                if (Gate::allows($permission)) {
                    return true;
                }
            }
            return false;
        });
    }
}
