<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code', 'name', 'description', 'type', 'value',
        'min_order_amount', 'max_discount_amount', 'usage_limit',
        'usage_per_user', 'total_used', 'starts_at', 'expires_at',
        'is_active', 'applicable_products', 'applicable_categories',
        'applicable_users', 'excluded_products', 'excluded_categories'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'applicable_products' => 'array',
        'applicable_categories' => 'array',
        'applicable_users' => 'array',
        'excluded_products' => 'array',
        'excluded_categories' => 'array'
    ];

    const TYPES = [
        'fixed' => 'Fixed Amount',
        'percentage' => 'Percentage',
        'free_shipping' => 'Free Shipping'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isValid()
    {
        return $this->is_active &&
               now()->between($this->starts_at, $this->expires_at) &&
               ($this->usage_limit === null || $this->total_used < $this->usage_limit);
    }

    public function calculateDiscount($subtotal)
    {
        if ($subtotal < $this->min_order_amount) {
            return 0;
        }

        switch ($this->type) {
            case 'fixed':
                return min($this->value, $this->max_discount_amount ?? $this->value);
            case 'percentage':
                $discount = ($subtotal * $this->value / 100);
                return $this->max_discount_amount ? min($discount, $this->max_discount_amount) : $discount;
            case 'free_shipping':
                return 0; // Handled separately
            default:
                return 0;
        }
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

    public function scopeValidForUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->whereNull('applicable_users')
              ->orWhereJsonContains('applicable_users', $userId);
        });
    }

    public function getStatusAttribute()
    {
        $now = now();
        
        if ($this->expires_at < $now) {
            return 'expired';
        }
        
        if ($this->is_active && $this->starts_at <= $now) {
            return 'active';
        }
        
        return 'inactive';
    }
}