<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Brand extends Model
{
    use HasSlug;

    protected $fillable = [
        'name', 
        'slug', 
        'description', 
        'logo', 
        'website', 
        'sort_order', 
        'status',
        'meta_title', 
        'meta_description', 
        'meta_keywords'
    ];

    protected $hidden = [
        'website', 'sort_order', 'status',
        'meta_title', 'meta_description', 'meta_keywords'
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'status' => 'boolean'
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

   
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('starts_at', '<=', now())
                    ->where('expires_at', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeInactive($query)
    {
        return $query->where(function($q) {
            $q->where('is_active', false)
            ->where('expires_at', '>=', now());
        })->orWhere(function($q) {
            $q->where('is_active', true)
            ->where('starts_at', '>', now())
            ->where('expires_at', '>=', now());
        });
    }
    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }
        
        return Storage::disk('public')->url($this->logo);
    }
}