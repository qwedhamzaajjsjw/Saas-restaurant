<?php

namespace App\Traits;

use App\Services\TenantService;
use Illuminate\Database\Eloquent\Builder;

/**
 * Automatically scopes queries to the active restaurant (tenant).
 *
 * Add this trait to any model that has a restaurant_id column:
 *   use BelongsToRestaurant;
 *
 * The Global Scope is applied on every query automatically.
 * The creating observer auto-fills restaurant_id on new records.
 */
trait BelongsToRestaurant
{
    public static function bootBelongsToRestaurant(): void
    {
        // ── Global Scope: automatically filter by active tenant ───────────
        static::addGlobalScope('tenant', function (Builder $query): void {
            /** @var TenantService $tenantService */
            $tenantService = app(TenantService::class);
            $tenantId      = $tenantService->getTenantId();

            if ($tenantId !== null) {
                $query->where((new static)->getTable().'.restaurant_id', $tenantId);
            }
        });

        // ── Creating: auto-fill restaurant_id ────────────────────────────
        static::creating(function ($model): void {
            if (empty($model->restaurant_id)) {
                /** @var TenantService $tenantService */
                $tenantService        = app(TenantService::class);
                $model->restaurant_id = $tenantService->getTenantId();
            }
        });
    }
}
