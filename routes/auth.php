<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

// ── Guest-only routes ────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {

    // Login (Super Admin / General)
    Route::get('/login',  [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    // Restaurant Owner Login
    Route::get('/restaurant/login', [LoginController::class, 'showRestaurantForm'])->name('restaurant.login');

    // Forgot password
    Route::get('/forgot-password',  [ForgotPasswordController::class, 'showForm'])
         ->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])
         ->name('password.email');

    // Reset password
    Route::get('/reset-password/{token}',  [ResetPasswordController::class, 'showForm'])
         ->name('password.reset');
    Route::post('/reset-password',         [ResetPasswordController::class, 'reset'])
         ->name('password.update');
});

// ── Authenticated routes ─────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
});
