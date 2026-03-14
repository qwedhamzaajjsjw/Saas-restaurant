<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks access to the app if not yet installed.
 * Also blocks access to /install if already installed.
 */
class CheckInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        $installed = file_exists(storage_path('installed.lock'));

        // User hits /install/* but app is already installed → redirect to login
        if ($installed && $request->is('install') || $installed && $request->is('install/*')) {
            return redirect()->route('login');
        }

        // User hits any non-install route but app is NOT installed → force installer
        if (! $installed && ! $request->is('install') && ! $request->is('install/*')) {
            return redirect()->route('installer.index');
        }

        return $next($request);
    }
}
