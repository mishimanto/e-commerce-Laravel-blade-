<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'name', 
        'slug', 
        'sku', 
        'category_id', 
        'brand_id', 
        'description', 
        'short_description', 
        'specifications', 
        'base_price', 
        'sale_price', 
        'buying_price',
        'stock', 
        'status', 
        'is_featured', 
        'is_trending', 
        'is_new', 
        'meta_title', 
        'meta_description', 
        'meta_keywords', 
        'warranty', 
        'tags',
        'weight',
        'dimensions',
        'views',
        'sold_count'
    ];

    protected $appends = [
        // 'price',
        // 'formatted_price',
        // 'on_sale',
    ];


    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'specifications' => 'array',
        'tags' => 'array',
        'dimensions' => 'array',
        'is_featured' => 'boolean',
        'is_trending' => 'boolean',
        'is_new' => 'boolean',
        'base_price' => 'float',
        'sale_price' => 'float',
        'buying_price' => 'float',
        'stock' => 'integer',
        'views' => 'integer',
        'sold_count' => 'integer',
        'weight' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)
                    ->orderBy('id', 'asc'); 
    }
    
    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)->where('status', 'active');
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attribute_product')
                    ->withPivot('attribute_value_id')
                    ->withTimestamps();
    }

    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'attribute_product')
                    ->withPivot('attribute_id')
                    ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function activeReviews()
    {
        return $this->hasMany(Review::class)->where('status', 1);
    }

    // Accessors
    public function getPriceAttribute()
    {
        return $this->sale_price ?? $this->base_price;
    }

    public function getFormattedPriceAttribute()
    {
        return '৳' . number_format($this->price, 2);
    }

    public function getOnSaleAttribute()
    {
        return !is_null($this->sale_price) && $this->sale_price < $this->base_price;
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->on_sale) {
            return 0;
        }
        
        return round((($this->base_price - $this->sale_price) / $this->base_price) * 100);
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews->avg('rating') ?? 0;
    }

    public function getReviewsCountAttribute()
    {
        return $this->activeReviews()->count();
    }

    public function getPrimaryImageUrlAttribute()
    {
        $primary = $this->images->where('is_primary', true)->first();
        
        if ($primary && $primary->url) {
            // Check if URL is already full URL or just path
            if (filter_var($primary->url, FILTER_VALIDATE_URL)) {
                return $primary->url;
            }
            return asset('storage/' . ltrim($primary->url, '/'));
        }
        
        if ($this->images->first()) {
            $firstImage = $this->images->first();
            if (filter_var($firstImage->url, FILTER_VALIDATE_URL)) {
                return $firstImage->url;
            }
            return asset('storage/' . ltrim($firstImage->url, '/'));
        }
        
        return asset('images/no-image.jpg');
    }

    public function getImageUrlsAttribute()
    {
        return $this->images->map(function ($image) {
            $url = $image->url;
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                $url = asset('storage/' . ltrim($url, '/'));
            }
            
            return [
                'id' => $image->id,
                'url' => $url,
                'is_primary' => $image->is_primary,
                'sort_order' => $image->sort_order
            ];
        });
    }

    public function getMinPriceAttribute()
    {
        if ($this->variants()->exists()) {
            $minVariant = $this->variants()->min('price_adjustment');
            return $this->base_price + ($minVariant ?? 0);
        }
        return $this->sale_price ?? $this->base_price;
    }

    public function getMaxPriceAttribute()
    {
        if ($this->variants()->exists()) {
            $maxVariant = $this->variants()->max('price_adjustment');
            return $this->base_price + ($maxVariant ?? 0);
        }
        return $this->sale_price ?? $this->base_price;
    }

    public function getPriceRangeAttribute()
    {
        $min = $this->min_price;
        $max = $this->max_price;
        
        if ($min == $max) {
            return '৳' . number_format($min, 2);
        }
        return '৳' . number_format($min, 2) . ' - ৳' . number_format($max, 2);
    }

    public function getEffectiveStockAttribute()
    {
        if ($this->variants()->exists()) {
            return $this->variants()->sum('stock');
        }
        return $this->stock ?? 0;
    }

    public function getStockStatusAttribute()
    {
        $effectiveStock = $this->effective_stock;
        $hasVariants = $this->variants()->exists();
        $variantCount = $hasVariants ? $this->variants()->count() : 0;
        
        return [
            'quantity' => $effectiveStock,
            'has_variants' => $hasVariants,
            'variant_count' => $variantCount,
            'status' => $this->determineStockStatus($effectiveStock),
            'display_text' => $this->getStockDisplayText($effectiveStock, $hasVariants, $variantCount),
            'tooltip' => $this->getStockTooltip($effectiveStock, $hasVariants, $variantCount)
        ];
    }

    public function getVariantAttributesAttribute()
    {
        $variants = $this->variants;
        
        $attributes = [];
        
        foreach ($variants as $variant) {
            $attrs = is_string($variant->attributes) 
                ? json_decode($variant->attributes, true) 
                : ($variant->attributes ?? []);
            
            if (is_array($attrs)) {
                foreach ($attrs as $key => $value) {
                    if (!isset($attributes[$key])) {
                        $attributes[$key] = [];
                    }
                    if (!in_array($value, $attributes[$key])) {
                        $attributes[$key][] = $value;
                    }
                }
            }
        }
        
        return $attributes;
    }

    /**
     * Get specifications in a normalized format
     * This handles both the new array format and the old numeric key format
     */
    public function getSpecificationsAttribute($value)
    {
        // If it's already an array (from casting), process it
        if (is_array($value)) {
            return $this->normalizeSpecifications($value);
        }
        
        // If it's a string, try to decode it
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            
            if (is_array($decoded)) {
                return $this->normalizeSpecifications($decoded);
            }
        }
        
        return [];
    }

    /**
     * Normalize specifications to a consistent format
     */
    protected function normalizeSpecifications($specs)
    {
        if (!is_array($specs)) {
            return [];
        }

        $normalized = [];
        
        // Check if it has numeric keys (old format with indices)
        $hasNumericKeys = false;
        foreach(array_keys($specs) as $key) {
            if (is_numeric($key)) {
                $hasNumericKeys = true;
                break;
            }
        }
        
        if ($hasNumericKeys) {
            // Format like: {"4":{"key":"Display","value":"6.9 Inch"}, ...}
            foreach($specs as $item) {
                if (is_array($item) && isset($item['key']) && isset($item['value']) && !empty($item['key'])) {
                    $normalized[] = [
                        'key' => $item['key'],
                        'value' => $item['value']
                    ];
                }
            }
        } else {
            // Format like: [{"key":"Display","value":"6.9 Inch"}, ...] 
            // or {"Display":"6.9 Inch", ...}
            foreach($specs as $key => $value) {
                if (is_array($value) && isset($value['key']) && isset($value['value'])) {
                    // Already in key-value object format
                    $normalized[] = [
                        'key' => $value['key'],
                        'value' => $value['value']
                    ];
                } elseif (!is_numeric($key) && !is_array($value)) {
                    // Simple key-value pair
                    $normalized[] = [
                        'key' => $key,
                        'value' => $value
                    ];
                } elseif (is_numeric($key) && is_array($value) && isset($value[0]) && isset($value[1])) {
                    // Array format like ["Display", "6.9 Inch"]
                    $normalized[] = [
                        'key' => $value[0],
                        'value' => $value[1]
                    ];
                }
            }
        }
        
        return $normalized;
    }

    // Mutators
    public function setSpecificationsAttribute($value)
    {
        // If it's already an array, encode it
        if (is_array($value)) {
            $this->attributes['specifications'] = json_encode($value);
        } elseif (is_string($value)) {
            // Check if it's already JSON
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $this->attributes['specifications'] = $value;
            } else {
                // It's a plain string, wrap it
                $this->attributes['specifications'] = json_encode([['key' => 'Specification', 'value' => $value]]);
            }
        } else {
            $this->attributes['specifications'] = null;
        }
    }

    public function setTagsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['tags'] = json_encode($value);
        } elseif (is_string($value)) {
            // If it's comma-separated string
            $tags = array_map('trim', explode(',', $value));
            $this->attributes['tags'] = json_encode($tags);
        } else {
            $this->attributes['tags'] = $value;
        }
    }

    // Helper methods
    public function findVariant(array $attributeValues)
    {
        return $this->variants()->get()->first(function ($variant) use ($attributeValues) {
            $attrs = is_string($variant->attributes) 
                ? json_decode($variant->attributes, true) 
                : ($variant->attributes ?? []);
            
            foreach ($attributeValues as $key => $value) {
                if (!isset($attrs[$key]) || $attrs[$key] != $value) {
                    return false;
                }
            }
            return true;
        });
    }

    private function determineStockStatus($stock)
    {
        if ($stock <= 0) {
            return 'out_of_stock';
        } elseif ($stock < 5) {
            return 'low_stock';
        } elseif ($stock < 20) {
            return 'limited_stock';
        } else {
            return 'in_stock';
        }
    }

    private function getStockDisplayText($stock, $hasVariants, $variantCount)
    {
        if ($stock <= 0) {
            return 'Out of Stock';
        }
        
        if ($hasVariants) {
            return number_format($stock) . ' units (' . $variantCount . ' ' . Str::plural('variant', $variantCount) . ')';
        }
        
        return number_format($stock) . ' in stock';
    }

    private function getStockTooltip($stock, $hasVariants, $variantCount)
    {
        if ($stock <= 0) {
            return 'This product is currently out of stock';
        }
        
        if ($hasVariants) {
            $variantDetails = $this->variants()
                ->where('stock', '>', 0)
                ->get()
                ->map(function($variant) {
                    $attrs = is_string($variant->attributes) 
                        ? json_decode($variant->attributes, true) 
                        : ($variant->attributes ?? []);
                    $attrString = is_array($attrs) ? implode(', ', $attrs) : '';
                    return $attrString . ': ' . $variant->stock;
                })
                ->implode("\n");
                
            return "Total: {$stock} units across {$variantCount} variants\n\n" . $variantDetails;
        }
        
        return "Stock quantity: {$stock}";
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeTrending($query)
    {
        return $query->where('is_trending', true);
    }

    public function scopeNew($query)
    {
        return $query->where('is_new', true);
    }

    public function scopeInStock($query)
    {
        return $query->where(function($q) {
            $q->where('stock', '>', 0)
              ->orWhereHas('variants', function($variant) {
                  $variant->where('stock', '>', 0);
              });
        });
    }

    public function scopeOnSale($query)
    {
        return $query->whereNotNull('sale_price')
                     ->whereColumn('sale_price', '<', 'base_price');
    }

    public function scopePriceRange($query, $min, $max)
    {
        return $query->where(function($q) use ($min, $max) {
            $q->whereBetween('base_price', [$min, $max])
              ->orWhereBetween('sale_price', [$min, $max]);
        });
    }

    public function inWishlist()
    {
        if (!auth()->check()) {
            return false;
        }
        
        return $this->wishlistUsers()->where('user_id', auth()->id())->exists();
    }

    public function wishlistUsers()
    {
        return $this->belongsToMany(User::class, 'wishlists')
                    ->withTimestamps();
    }
}