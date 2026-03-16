<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\RestaurantController;
use App\Http\Controllers\SuperAdmin\PlanController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\SettingController;

/*
|--------------------------------------------------------------------------
| Super Admin Routes
| Protected by: auth + super_admin role
|--------------------------------------------------------------------------
*/

// Stop impersonation — accessible by any authenticated user who has the session key
Route::get('/admin/impersonate/stop', [RestaurantController::class, 'stopImpersonating'])
     ->name('admin.impersonate.stop')
     ->middleware(['auth', 'installed']);

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:super_admin', 'installed'])
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Restaurant management
        Route::resource('restaurants', RestaurantController::class);
        Route::patch('restaurants/{restaurant}/toggle-status', [RestaurantController::class, 'toggleStatus'])
             ->name('restaurants.toggle-status');
        Route::get('restaurants/{restaurant}/login-as', [RestaurantController::class, 'loginAs'])
             ->name('restaurants.login-as');

        // Subscription plans
        Route::resource('plans', PlanController::class);

        // User management
        Route::resource('users', UserController::class)->only(['index','show','destroy']);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
             ->name('users.toggle-status');

        // System settings
        Route::get('/settings',        [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings',       [SettingController::class, 'update'])->name('settings.update');
    });
