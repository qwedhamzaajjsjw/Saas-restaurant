<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\TenantService;

/**
 * Resolves and sets the active restaurant (tenant) context
 * for restaurant-owner dashboard routes.
 *
 * The tenant is determined from the authenticated user's restaurant_id.
 */
class TenantMiddleware
{
    public function __construct(protected TenantService $tenantService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->restaurant_id) {
            abort(403, 'No restaurant associated with this account.');
        }

        // Resolve and cache the tenant in the service
        $this->tenantService->setTenant($user->restaurant_id);

        return $next($request);
    }
}
