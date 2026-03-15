<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Global middleware that enforces the installation state.
 *
 * - Not installed + hitting non-installer route  → redirect to /install
 * - Already installed + hitting /install route   → redirect to /login
 * - All other combinations                       → pass through
 */
class CheckInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        $installed    = file_exists(storage_path('installed.lock'));
        $isInstaller  = $request->is('install') || $request->is('install/*');

        // Already installed → block installer access
        if ($installed && $isInstaller) {
            return redirect()->route('login');
        }

        // Not installed → force installer for all non-installer routes
        if (! $installed && ! $isInstaller) {
            return redirect()->route('installer.index');
        }

        return $next($request);
    }
}
