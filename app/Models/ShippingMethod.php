<?php
// app/Models/ShippingMethod.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'cost',
        'delivery_time',
        'minimum_order_amount',
        'maximum_order_amount',
        'is_active',
        'sort_order',
        'is_free_shipping',
        'free_shipping_threshold',
        'available_countries',
        'available_cities'
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'maximum_order_amount' => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2',
        'is_active' => 'boolean',
        'is_free_shipping' => 'boolean',
        'available_countries' => 'array',
        'available_cities' => 'array'
    ];

    /**
     * Scope a query to only include active shipping methods.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Check if shipping method is available for given subtotal
     */
    public function isAvailableForSubtotal($subtotal)
    {
        if ($this->minimum_order_amount && $subtotal < $this->minimum_order_amount) {
            return false;
        }
        
        if ($this->maximum_order_amount && $subtotal > $this->maximum_order_amount) {
            return false;
        }
        
        return true;
    }

    /**
     * Check if free shipping applies
     */
    public function freeShippingApplies($subtotal)
    {
        return $this->is_free_shipping && 
               $this->free_shipping_threshold && 
               $subtotal >= $this->free_shipping_threshold;
    }

    /**
     * Get final shipping cost based on subtotal
     */
    public function getFinalCost($subtotal)
    {
        if ($this->freeShippingApplies($subtotal)) {
            return 0;
        }
        
        return $this->cost;
    }

    /**
     * Check if available in given country
     */
    public function isAvailableInCountry($country)
    {
        if (empty($this->available_countries)) {
            return true;
        }
        
        return in_array($country, $this->available_countries);
    }
}