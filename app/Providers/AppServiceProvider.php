<?php

namespace App\Providers;

use App\Services\TenantService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // TenantService is a singleton: one instance per request lifecycle.
        // All middleware, models, and controllers share the same tenant context.
        $this->app->singleton(TenantService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
