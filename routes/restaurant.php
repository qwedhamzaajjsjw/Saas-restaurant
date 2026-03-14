<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Restaurant\DashboardController;
use App\Http\Controllers\Restaurant\MenuController;
use App\Http\Controllers\Restaurant\CategoryController;
use App\Http\Controllers\Restaurant\ProductController;
use App\Http\Controllers\Restaurant\OrderController;
use App\Http\Controllers\Restaurant\SettingController;

/*
|--------------------------------------------------------------------------
| Restaurant Owner Routes
| Protected by: auth + role:restaurant_owner + tenant resolved
|--------------------------------------------------------------------------
*/

Route::prefix('dashboard')
    ->name('restaurant.')
    ->middleware(['auth', 'role:restaurant_owner', 'installed', 'tenant'])
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Menu management
        Route::resource('menus', MenuController::class);

        // Categories
        Route::resource('categories', CategoryController::class);

        // Products
        Route::resource('products', ProductController::class);

        // Orders
        Route::get('/orders',          [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}',  [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])
             ->name('orders.status');

        // Settings
        Route::get('/settings',        [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings',       [SettingController::class, 'update'])->name('settings.update');
    });
