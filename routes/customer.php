<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\StorefrontController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\OrderController;

/*
|--------------------------------------------------------------------------
| Customer Storefront Routes
| Public access – tenant resolved by restaurant slug
|--------------------------------------------------------------------------
*/

Route::prefix('restaurant/{slug}')
    ->name('customer.')
    ->middleware(['installed'])
    ->group(function () {

        // Menu / storefront
        Route::get('/',                [StorefrontController::class, 'index'])->name('index');
        Route::get('/menu',            [StorefrontController::class, 'menu'])->name('menu');

        // Cart
        Route::get('/cart',            [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add',       [CartController::class, 'add'])->name('cart.add');
        Route::patch('/cart/{item}',   [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{item}',  [CartController::class, 'remove'])->name('cart.remove');

        // Orders
        Route::post('/checkout',       [OrderController::class, 'checkout'])->name('checkout');
        Route::get('/order/{order}/confirmation', [OrderController::class, 'confirmation'])
             ->name('order.confirmation');
    });
