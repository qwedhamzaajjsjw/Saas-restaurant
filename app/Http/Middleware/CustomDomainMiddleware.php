<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Restaurant;

/**
 * Resolves a restaurant from the incoming Host header.
 *
 * Supports two domain modes:
 *
 *  1. Custom domain  → pizza-palace.com          (stored in custom_domain)
 *  2. Subdomain      → pizza.yourdomain.com       (stored in subdomain)
 *
 * Once resolved the restaurant model and its slug are injected into the
 * request attributes and route parameters so existing controllers work
 * without modification.
 */
class CustomDomainMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = strtolower($request->getHost());
        $host = preg_replace('/^www\./', '', $host);

        $restaurant = $this->resolveRestaurant($host);

        if (! $restaurant) {
            abort(404, 'Restaurant not found for this domain.');
        }

        $request->attributes->set('restaurant', $restaurant);
        $request->attributes->set('slug', $restaurant->slug);

        if ($route = $request->route()) {
            $route->setParameter('slug', $restaurant->slug);
        }

        return $next($request);
    }

    private function resolveRestaurant(string $host): ?Restaurant
    {
        // 1. Exact custom-domain match (e.g. pizza-palace.com)
        $restaurant = Restaurant::where('custom_domain', $host)
            ->where('status', 'active')
            ->first();

        if ($restaurant) {
            return $restaurant;
        }

        // 2. Subdomain match: strip the main app host and check the prefix
        $appHost = strtolower(parse_url(config('app.url'), PHP_URL_HOST) ?? '');
        $appHost = preg_replace('/^www\./', '', $appHost);

        if ($appHost && str_ends_with($host, '.' . $appHost)) {
            $prefix = substr($host, 0, strlen($host) - strlen('.' . $appHost));

            if ($prefix !== '' && ! str_contains($prefix, '.')) {
                $restaurant = Restaurant::where('subdomain', $prefix)
                    ->where('status', 'active')
                    ->first();
            }
        }

        return $restaurant;
    }
}
