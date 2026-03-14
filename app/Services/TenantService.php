<?php

namespace App\Services;

use App\Models\Restaurant;
use Illuminate\Support\Facades\Cache;

/**
 * Holds the currently active tenant (restaurant) for the request lifecycle.
 * Bound as a singleton in the service container so all classes share the same instance.
 */
class TenantService
{
    protected ?Restaurant $tenant = null;

    /**
     * Set the active tenant by restaurant ID.
     * Caches the model for 60 minutes to avoid repeated DB hits.
     */
    public function setTenant(int $restaurantId): void
    {
        $this->tenant = Cache::remember(
            "restaurant.{$restaurantId}",
            3600,
            fn () => Restaurant::findOrFail($restaurantId)
        );
    }

    /**
     * Get the active tenant model.
     */
    public function getTenant(): ?Restaurant
    {
        return $this->tenant;
    }

    /**
     * Get the active tenant ID (shorthand for Global Scopes).
     */
    public function getTenantId(): ?int
    {
        return $this->tenant?->id;
    }
}
