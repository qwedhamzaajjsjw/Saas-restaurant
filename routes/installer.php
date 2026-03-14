<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Installer\InstallerController;

/*
|--------------------------------------------------------------------------
| Installer Routes
| Accessible only when the application is NOT yet installed.
| Once installed, CheckInstalled middleware blocks these routes.
|--------------------------------------------------------------------------
*/

Route::prefix('install')
    ->name('installer.')
    ->group(function () {

        // Step 1 – requirements check
        Route::get('/',                [InstallerController::class, 'index'])
             ->name('index');

        // Step 2 – database configuration form
        Route::get('/database',        [InstallerController::class, 'database'])
             ->name('database');
        Route::post('/database',       [InstallerController::class, 'saveDatabase'])
             ->name('database.save');

        // Step 3+4 – run migrations
        Route::post('/migrate',        [InstallerController::class, 'migrate'])
             ->name('migrate');

        // Step 5 – create super admin
        Route::get('/admin',           [InstallerController::class, 'admin'])
             ->name('admin');
        Route::post('/admin',          [InstallerController::class, 'saveAdmin'])
             ->name('admin.save');

        // Step 6 – finish & lock
        Route::get('/complete',        [InstallerController::class, 'complete'])
             ->name('complete');
    });
