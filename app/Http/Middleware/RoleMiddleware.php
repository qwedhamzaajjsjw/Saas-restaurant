<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verifies the authenticated user has the required role.
 *
 * Usage in routes: middleware('role:super_admin')
 *                  middleware('role:restaurant_owner')
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user() || $request->user()->role !== $role) {
            abort(403, 'Unauthorized. You do not have the required role.');
        }

        return $next($request);
    }
}
