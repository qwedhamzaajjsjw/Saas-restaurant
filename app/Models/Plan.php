<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'max_products',
        'max_orders_per_month',
        'max_categories',
        'features',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'features'             => 'array',
            'price'                => 'decimal:2',
            'is_active'            => 'boolean',
            'max_products'         => 'integer',
            'max_orders_per_month' => 'integer',
            'max_categories'       => 'integer',
        ];
    }

    // ── Scopes ───────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    public function isFree(): bool
    {
        return $this->price == 0;
    }

    // ── Relationships ────────────────────────────────────────────────────

    public function restaurants(): HasMany
    {
        return $this->hasMany(Restaurant::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
