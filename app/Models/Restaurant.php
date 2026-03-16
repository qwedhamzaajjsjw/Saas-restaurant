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
        'subdomain',
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

    /**
     * Returns the public-facing URL for this restaurant's storefront.
     * Priority: custom_domain > subdomain > path-based slug URL.
     */
    public function getStorefrontUrlAttribute(): string
    {
        if ($this->custom_domain) {
            return 'https://' . $this->custom_domain;
        }

        if ($this->subdomain) {
            $appHost = parse_url(config('app.url'), PHP_URL_HOST);
            return 'https://' . $this->subdomain . '.' . $appHost;
        }

        return route('customer.index', ['slug' => $this->slug]);
    }

    public function getLogoUrlAttribute(): string
    {
        if (!$this->logo) {
            return asset('images/default-restaurant.png');
        }
        if (str_starts_with($this->logo, 'demo/')) {
            return asset($this->logo);
        }
        return asset('storage/'.$this->logo);
    }

    public function getCoverImageUrlAttribute(): string
    {
        if (!$this->cover_image) {
            return asset('images/default-cover.jpg');
        }
        if (str_starts_with($this->cover_image, 'demo/')) {
            return asset($this->cover_image);
        }
        return asset('storage/'.$this->cover_image);
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

    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class);
    }
}
