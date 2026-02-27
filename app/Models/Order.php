<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number', 
        'user_id', 
        'guest_email', 
        'guest_phone',
        'billing_name', 
        'billing_email', 
        'billing_phone', 
        'billing_address',
        'billing_city', 
        'billing_state', 
        'billing_zip', 
        'billing_country',
        'shipping_name', 
        'shipping_email', 
        'shipping_phone', 
        'shipping_address',
        'shipping_city', 
        'shipping_state', 
        'shipping_zip', 
        'shipping_country',
        'subtotal', 
        'discount_amount', 
        'coupon_code', 
        'coupon_discount',
        'shipping_cost', 
        'tax_amount', 
        'total', 
        'payment_method',
        'payment_status', 
        'payment_id', 
        'shipping_method', 
        'shipping_courier',
        'tracking_number', 
        'status', 
        'notes', 
        'admin_notes',
        'invoice_number', 
        'invoice_date', 
        'delivery_date'
    ];

    protected $hidden = [
        'user_id', 
        'guest_email',
        'tracking_number', 'status', 'notes', 'admin_notes',
        'invoice_number', 'invoice_date', 'delivery_date'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'coupon_discount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'invoice_date' => 'datetime',
        'delivery_date' => 'datetime'
    ];

    const STATUSES = [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'confirmed' => 'Confirmed',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'refunded' => 'Refunded',
        'failed' => 'Failed'
    ];

    const PAYMENT_STATUSES = [
        'pending' => 'Pending',
        'paid' => 'Paid',
        'failed' => 'Failed',
        'refunded' => 'Refunded',
        'cancelled' => 'Cancelled'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_code', 'code');
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}