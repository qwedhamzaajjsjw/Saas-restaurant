<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\StorefrontController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\OrderController;

/*
|--------------------------------------------------------------------------
| Custom Domain Storefront Routes
|--------------------------------------------------------------------------
| These routes are loaded only when the incoming Host header matches a
| restaurant's custom_domain value.  The CustomDomainMiddleware resolves
| the restaurant and injects the slug so existing controllers work as-is.
|
| Example: pizza-palace.com/ → StorefrontController@index (slug injected)
*/

Route::middleware(['installed', 'custom.domain'])
    ->group(function () {

        Route::get('/',
            [StorefrontController::class, 'index'])->name('custom.index');

        Route::get('/menu',
            [StorefrontController::class, 'menu'])->name('custom.menu');

        Route::get('/cart',
            [CartController::class, 'index'])->name('custom.cart.index');

        Route::post('/cart/add',
            [CartController::class, 'add'])->name('custom.cart.add');

        Route::patch('/cart/{item}',
            [CartController::class, 'update'])->name('custom.cart.update');

        Route::delete('/cart/{item}',
            [CartController::class, 'remove'])->name('custom.cart.remove');

        Route::post('/checkout',
            [OrderController::class, 'checkout'])->name('custom.checkout');

        Route::get('/order/{order}/confirmation',
            [OrderController::class, 'confirmation'])->name('custom.order.confirmation');
    });
