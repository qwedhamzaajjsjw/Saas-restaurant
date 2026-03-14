<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'restaurant_id',
        'key',
        'value',
        'type',
    ];

    // ── Static helpers ────────────────────────────────────────────────────

    /**
     * Get a setting value by key.
     * @param  string|null  $restaurantId  NULL = global setting
     */
    public static function get(string $key, mixed $default = null, ?int $restaurantId = null): mixed
    {
        $cacheKey = "setting.{$restaurantId}.{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($key, $restaurantId, $default) {
            $setting = static::where('key', $key)
                             ->where('restaurant_id', $restaurantId)
                             ->first();

            return $setting ? $setting->getCastedValue() : $default;
        });
    }

    /**
     * Set / upsert a setting value.
     */
    public static function set(string $key, mixed $value, ?int $restaurantId = null, string $type = 'string'): void
    {
        static::updateOrCreate(
            ['key' => $key, 'restaurant_id' => $restaurantId],
            ['value' => $value, 'type' => $type]
        );

        Cache::forget("setting.{$restaurantId}.{$key}");
    }

    // ── Type casting ─────────────────────────────────────────────────────

    public function getCastedValue(): mixed
    {
        return match ($this->type) {
            'boolean' => (bool) $this->value,
            'integer' => (int)  $this->value,
            'json'    => json_decode($this->value, true),
            default   => $this->value,
        };
    }

    // ── Relationships ────────────────────────────────────────────────────

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
