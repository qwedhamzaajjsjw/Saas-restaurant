<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ── Installer ────────────────────────────────────────────────────────────
require __DIR__.'/installer.php';

// ── Auth (login / logout / password reset) ───────────────────────────────
require __DIR__.'/auth.php';

// ── Super Admin ──────────────────────────────────────────────────────────
require __DIR__.'/admin.php';

// ── Restaurant Owner Dashboard ───────────────────────────────────────────
require __DIR__.'/restaurant.php';

// ── Customer Storefront ──────────────────────────────────────────────────
require __DIR__.'/customer.php';

// ── Smart redirects ──────────────────────────────────────────────────────
// /dashboard → redirect to the correct dashboard based on role
Route::get('/dashboard', function () {
    $user = auth()->user();
    if (! $user) {
        return redirect()->route('login');
    }
    return match ($user->role) {
        'super_admin'      => redirect()->route('admin.dashboard'),
        'restaurant_owner' => redirect()->route('restaurant.dashboard'),
        default            => redirect()->route('login'),
    };
})->middleware('auth');

// Root → login page
Route::get('/', function () {
    return redirect()->route('login');
});
