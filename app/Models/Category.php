<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model
{
    use HasSlug;

    protected $fillable = [
        'name', 
        'slug', 
        'description', 
        'parent_id', 
        'image', 
        'banner_image',
        'icon', 
        'sort_order', 
        'status',
        'is_featured',
        'featured_order',
        'show_in_menu',
        'meta_title', 
        'meta_description', 
        'meta_keywords'
    ];

    protected $hidden = [
        // Remove these from hidden as they should be visible in admin
        // 'parent_id', 
        // 'image', 'icon', 'sort_order', 'status',
        // 'meta_title', 'meta_description', 'meta_keywords'
    ];

    protected $visible = [
        'id', 'name', 'slug', 'description', 'parent_id', 'image', 'icon', 
        'sort_order', 'status', 'is_featured', 'featured_order', 'show_in_menu',
        'meta_title', 'meta_description', 'meta_keywords'
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'featured_order' => 'integer',
        'status' => 'boolean',
        'is_featured' => 'boolean',
        'show_in_menu' => 'boolean'
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
                     ->orderBy('featured_order');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        
        return Storage::disk('public')->url($this->image);
    }

    public function getBannerUrlAttribute(): ?string
    {
        if (!$this->banner_image) {
            return null;
        }
        
        return Storage::disk('public')->url($this->banner_image);
    }
}