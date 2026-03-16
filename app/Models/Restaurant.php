<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Restaurant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'custom_domain',
        'description',
        'logo',
        'cover_image',
        'primary_color',
        'phone',
        'email',
        'address',
        'city',
        'country',
        'plan_id',
        'status',
        'timezone',
        'currency',
        'accepts_orders',
    ];

    protected function casts(): array
    {
        return [
            'accepts_orders' => 'boolean',
        ];
    }

    // ── Scopes ───────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo
            ? asset('storage/'.$this->logo)
            : asset('images/default-restaurant.png');
    }

    // ── Relationships ────────────────────────────────────────────────────

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function owner(): HasOne
    {
        return $this->hasOne(User::class)->where('role', 'restaurant_owner');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
                    ->where('status', 'active')
                    ->latestOfMany();
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }
}
