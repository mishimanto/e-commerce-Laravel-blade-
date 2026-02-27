<?php
// app/Models/PaymentMethod.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'icon',
        'instructions',
        'type',
        'config',
        'fixed_fee',
        'percentage_fee',
        'minimum_fee',
        'maximum_fee',
        'is_active',
        'sort_order',
        'minimum_order_amount',
        'maximum_order_amount'
    ];

    protected $casts = [
        'instructions' => 'array',
        'config' => 'array',
        'fixed_fee' => 'decimal:2',
        'percentage_fee' => 'decimal:2',
        'minimum_fee' => 'decimal:2',
        'maximum_fee' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'maximum_order_amount' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Scope a query to only include active payment methods.
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
     * Scope for online payments
     */
    public function scopeOnline($query)
    {
        return $query->where('type', 'online');
    }

    /**
     * Scope for offline payments
     */
    public function scopeOffline($query)
    {
        return $query->where('type', 'offline');
    }

    /**
     * Check if payment method is available for given subtotal
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
     * Calculate payment fee
     */
    public function calculateFee($subtotal)
    {
        $fee = $this->fixed_fee + ($subtotal * ($this->percentage_fee / 100));
        
        if ($this->minimum_fee && $fee < $this->minimum_fee) {
            $fee = $this->minimum_fee;
        }
        
        if ($this->maximum_fee && $fee > $this->maximum_fee) {
            $fee = $this->maximum_fee;
        }
        
        return round($fee, 2);
    }

    /**
     * Get icon URL
     */
    public function getIconUrlAttribute()
    {
        if (!$this->icon) {
            return null;
        }
        
        return asset('storage/' . $this->icon);
    }
}