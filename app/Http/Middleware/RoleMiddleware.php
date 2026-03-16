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
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->role !== $role) {
            // Redirect to the correct dashboard instead of showing a 403
            return match ($user->role) {
                'super_admin'      => redirect()->route('admin.dashboard'),
                'restaurant_owner' => redirect()->route('restaurant.dashboard'),
                default            => abort(403, 'Unauthorized. You do not have the required role.'),
            };
        }

        return $next($request);
    }
}
