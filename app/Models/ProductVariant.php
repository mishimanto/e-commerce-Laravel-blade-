<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'sku',
        'attributes',
        'price_adjustment',
        'buying_price',
        'stock',
        'image',
        'status',
    ];

    protected $casts = [
        'price_adjustment' => 'decimal:2',
        'buying_price' => 'decimal:2',
        'attributes' => 'array',
        'stock' => 'integer',
    ];

    protected $appends = [
        'price',
        'sale_price',
        'display_name',
        'in_stock',
        'formatted_attributes'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getPriceAttribute()
    {
        $product = $this->product;
        
        if (!$product) {
            return 0;
        }

        // Get base price (sale price if available, otherwise base price)
        $basePrice = $product->sale_price ?? $product->base_price;
        
        // Add price adjustment
        $price = (float) $basePrice + (float) ($this->price_adjustment ?? 0);
        
        return max(0, $price); // Ensure price is not negative
    }

    public function getSalePriceAttribute()
    {
        if ($this->product && $this->product->sale_price) {
            return $this->product->sale_price + ($this->price_adjustment ?? 0);
        }
        return null;
    }

    public function getDisplayNameAttribute()
    {
        $parts = [];
        $attrs = is_string($this->attributes) 
            ? json_decode($this->attributes, true) 
            : $this->attributes;
        
        if (is_array($attrs)) {
            foreach ($attrs as $value) {
                $parts[] = $value;
            }
        }
        
        return implode(' - ', $parts);
    }

    public function getInStockAttribute()
    {
        return $this->stock > 0;
    }

    public function getFormattedAttributesAttribute()
    {
        $attrs = is_string($this->attributes) 
            ? json_decode($this->attributes, true) 
            : $this->attributes;
        
        $formatted = [];
        if (is_array($attrs)) {
            foreach ($attrs as $key => $value) {
                $formatted[] = ucfirst($key) . ': ' . $value;
            }
        }
        
        return implode(' | ', $formatted);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }
}