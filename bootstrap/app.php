<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Custom domain storefront – loaded for every request;
            // CustomDomainMiddleware decides whether the host matches.
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(base_path('routes/custom_domain.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // ── Global web middleware ─────────────────────────────────────────
        // Runs on every web request: redirect to installer if not installed
        $middleware->web(append: [
            \App\Http\Middleware\CheckInstalled::class,
        ]);

        // ── Named (route-level) middleware aliases ────────────────────────
        $middleware->alias([
            'role'         => \App\Http\Middleware\RoleMiddleware::class,
            'tenant'       => \App\Http\Middleware\TenantMiddleware::class,
            'installed'    => \App\Http\Middleware\CheckInstalled::class,
            'subscription' => \App\Http\Middleware\CheckSubscription::class,
            'custom.domain' => \App\Http\Middleware\CustomDomainMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
