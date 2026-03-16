<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ImpersonationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ── Impersonation (no role middleware — user is being switched) ───────────
Route::middleware(['auth', 'installed'])->group(function () {
    // Super admin → restaurant owner (requires auth as super_admin, validated inside controller)
    Route::get('/auth/restaurant-switch/{token}', [ImpersonationController::class, 'switchToRestaurant'])
         ->name('auth.restaurant-switch');

    // Restaurant owner → back to super admin
    Route::get('/auth/admin-return', [ImpersonationController::class, 'returnToAdmin'])
         ->name('auth.admin-return');
});

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
