<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    protected $fillable = [
        'title', 
        'subtitle', 
        'description', 
        'image', 
        'mobile_image',
        'link', 
        'button_text', 
        'position', 
        'type', 
        'target',
        'start_date', 
        'end_date', 
        'priority', 
        'is_active',
        'settings', 
        'clicks', 
        'views'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'settings' => 'array',
        'priority' => 'integer',
        'clicks' => 'integer',
        'views' => 'integer'
    ];

    protected $appends = [
        'image_url',
        'mobile_image_url',
        'image_full_url',
        'mobile_image_full_url',
        'status_text',
        'is_expired',
        'position_name',
        'type_name',
        'target_name'
    ];

    const POSITIONS = [
        'home_hero' => 'Homepage Hero',
        'home_sidebar' => 'Homepage Sidebar',
        'home_bottom' => 'Homepage Bottom',
        'category_top' => 'Category Top',
        'category_sidebar' => 'Category Sidebar',
        'product_details' => 'Product Details',
        'cart_page' => 'Cart Page',
        'checkout_page' => 'Checkout Page',
        'popup' => 'Popup Modal'
    ];

    const TYPES = [
        'image' => 'Image',
        'video' => 'Video',
        'carousel' => 'Carousel',
        'countdown' => 'Countdown',
        'custom_html' => 'Custom HTML'
    ];

    const TARGETS = [
        '_self' => 'Same Window',
        '_blank' => 'New Window'
    ];

    /**
     * Scope a query to only include active banners.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function($q) {
                        $q->whereNull('start_date')
                          ->orWhere('start_date', '<=', now());
                    })
                    ->where(function($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }

    /**
     * Scope a query to only include banners for a specific position.
     */
    public function scopePosition($query, $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Record a click.
     */
    public function recordClick()
    {
        $this->increment('clicks');
    }

    /**
     * Record a view.
     */
    public function recordView()
    {
        $this->increment('views');
    }

    /**
     * Get image URL attribute (for backward compatibility)
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    /**
     * Get mobile image URL attribute (for backward compatibility)
     */
    public function getMobileImageUrlAttribute()
    {
        return $this->mobile_image ? asset('storage/' . $this->mobile_image) : $this->image_url;
    }

    /**
     * Get full image URL for display
     */
    public function getImageFullUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }
        
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }
        
        if (Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }
        
        return null;
    }

    /**
     * Get full mobile image URL for display
     */
    public function getMobileImageFullUrlAttribute(): ?string
    {
        if (empty($this->mobile_image)) {
            return $this->image_full_url;
        }
        
        if (filter_var($this->mobile_image, FILTER_VALIDATE_URL)) {
            return $this->mobile_image;
        }
        
        if (Storage::disk('public')->exists($this->mobile_image)) {
            return asset('storage/' . $this->mobile_image);
        }
        
        return $this->image_full_url;
    }

    /**
     * Get the storage path for image (for delete operations)
     */
    public function getImageStoragePathAttribute(): ?string
    {
        return $this->getRawOriginal('image');
    }

    /**
     * Get the storage path for mobile image (for delete operations)
     */
    public function getMobileImageStoragePathAttribute(): ?string
    {
        return $this->getRawOriginal('mobile_image');
    }

    /**
     * Check if banner is expired
     */
    public function getIsExpiredAttribute(): bool
    {
        if ($this->end_date && $this->end_date->isPast()) {
            return true;
        }
        
        return false;
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute(): string
    {
        if (!$this->is_active) {
            return 'Inactive';
        }
        
        if ($this->is_expired) {
            return 'Expired';
        }
        
        if ($this->start_date && $this->start_date->isFuture()) {
            return 'Scheduled';
        }
        
        return 'Active';
    }

    /**
     * Get position display name
     */
    public function getPositionNameAttribute(): string
    {
        return self::POSITIONS[$this->position] ?? $this->position;
    }

    /**
     * Get type display name
     */
    public function getTypeNameAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Get target display name
     */
    public function getTargetNameAttribute(): string
    {
        return self::TARGETS[$this->target] ?? $this->target;
    }

    /**
     * Decode settings if needed
     */
    public function getSettingsArrayAttribute(): array
    {
        if (is_string($this->settings)) {
            return json_decode($this->settings, true) ?? [];
        }
        
        return $this->settings ?? [];
    }

    /**
     * Check if banner is currently active based on dates
     */
    public function getIsCurrentlyActiveAttribute(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        
        if ($this->start_date && $this->start_date->isFuture()) {
            return false;
        }
        
        if ($this->end_date && $this->end_date->isPast()) {
            return false;
        }
        
        return true;
    }

    /**
     * Get formatted date range
     */
    public function getDateRangeAttribute(): ?string
    {
        if (!$this->start_date && !$this->end_date) {
            return null;
        }
        
        if ($this->start_date && !$this->end_date) {
            return 'From ' . $this->start_date->format('M d, Y');
        }
        
        if (!$this->start_date && $this->end_date) {
            return 'Until ' . $this->end_date->format('M d, Y');
        }
        
        return $this->start_date->format('M d, Y') . ' - ' . $this->end_date->format('M d, Y');
    }

    /**
     * Get click through rate
     */
    public function getCtrAttribute(): float
    {
        if ($this->views == 0) {
            return 0;
        }
        
        return round(($this->clicks / $this->views) * 100, 2);
    }
}