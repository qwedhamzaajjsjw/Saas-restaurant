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

// ── Root redirect ────────────────────────────────────────────────────────
Route::get('/', function () {
    return redirect()->route('login');
});
