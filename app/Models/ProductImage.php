<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'url',
        'alt_text',
        'is_primary',
        'sort_order'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer'
    ];

    protected $appends = ['full_url']; // Add this

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function getFullUrlAttribute(): string
    {
        if (empty($this->url)) {
            return 'https://via.placeholder.com/300x300?text=No+Image';
        }
        
        if (filter_var($this->url, FILTER_VALIDATE_URL)) {
            return $this->url;
        }
        
        if (Storage::disk('public')->exists($this->url)) {
            return asset('storage/' . $this->url);
        }
        
        return 'https://via.placeholder.com/300x300?text=No+Image';
    }

    public function getUrlAttribute($value)
    {
        return $value; // Return original path for delete
    }

    public function getStoragePathAttribute(): string
    {
        return $this->getRawOriginal('url');
    }
}