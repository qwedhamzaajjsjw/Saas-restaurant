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

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:super_admin', 'installed'])
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Restaurant management
        Route::resource('restaurants', RestaurantController::class);

        // Subscription plans
        Route::resource('plans', PlanController::class);

        // User management
        Route::resource('users', UserController::class);

        // System settings
        Route::get('/settings',        [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings',       [SettingController::class, 'update'])->name('settings.update');
    });
