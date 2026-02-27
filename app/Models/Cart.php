<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id', 'session_id', 'coupon_code', 'discount_amount',
        'shipping_cost', 'tax_amount', 'total', 'status'
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_code', 'code');
    }

    public function getSubtotalAttribute()
    {
        return $this->items->sum(function($item) {
            return $item->price * $item->quantity;
        });
    }

    public function getTotalWeightAttribute()
    {
        return $this->items->sum(function($item) {
            return ($item->product->weight ?? 0) * $item->quantity;
        });
    }
}