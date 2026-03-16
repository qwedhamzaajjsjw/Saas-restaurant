<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Restaurant;

/**
 * Detects a restaurant from its custom domain and injects the slug
 * into the route so existing controllers work without modification.
 *
 * Flow:
 *  1. Read the incoming HTTP Host header.
 *  2. Strip www. prefix if present.
 *  3. Look up the restaurants table by custom_domain.
 *  4. Bind the found restaurant's slug into the request attributes
 *     and into the route parameters so downstream controllers
 *     receive it as the {slug} parameter.
 */
class CustomDomainMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = strtolower($request->getHost());
        // Strip www.
        $host = preg_replace('/^www\./', '', $host);

        $restaurant = Restaurant::where('custom_domain', $host)
            ->where('status', 'active')
            ->first();

        if (! $restaurant) {
            abort(404, 'Restaurant not found for this domain.');
        }

        // Make the slug available to controllers via the request
        $request->attributes->set('restaurant', $restaurant);
        $request->attributes->set('slug', $restaurant->slug);

        // Inject into route parameters so {slug} is resolved automatically
        if ($route = $request->route()) {
            $route->setParameter('slug', $restaurant->slug);
        }

        return $next($request);
    }
}
