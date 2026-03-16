<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Banner extends Model
{
    protected $fillable = [
        'restaurant_id',
        'title',
        'subtitle',
        'image',
        'link_url',
        'button_text',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            return asset('images/default-banner.jpg');
        }
        if (str_starts_with($this->image, 'demo/') || str_starts_with($this->image, 'http')) {
            return str_starts_with($this->image, 'http') ? $this->image : asset($this->image);
        }
        return asset('storage/' . $this->image);
    }
}
