<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'user_id', 'payment_method', 'payment_id',
        'transaction_id', 'amount', 'currency', 'status',
        'payment_data', 'error_message', 'paid_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_data' => 'array',
        'paid_at' => 'datetime'
    ];

    const METHODS = [
        'cash_on_delivery' => 'Cash on Delivery',
        'sslcommerz' => 'SSLCommerz',
        'stripe' => 'Stripe',
        'bkash' => 'bKash',
        'nagad' => 'Nagad',
        'rocket' => 'Rocket'
    ];

    const STATUSES = [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'completed' => 'Completed',
        'failed' => 'Failed',
        'refunded' => 'Refunded',
        'cancelled' => 'Cancelled'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'completed');
    }
}