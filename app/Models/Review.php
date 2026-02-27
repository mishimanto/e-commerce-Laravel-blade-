<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'order_id',
        'rating', 'title', 'comment', 'pros', 'cons',
        'images', 'verified_purchase', 'status', 'guest_info'
    ];

    protected $casts = [
        'rating' => 'integer',
        'pros' => 'array',
        'cons' => 'array',
        'images' => 'array',
        'verified_purchase' => 'boolean',
        'status' => 'boolean',
        'guest_info' => 'array'
    ];

    protected $appends = ['is_guest'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('verified_purchase', true);
    }

    public function scopeRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }
    
    public function getIsGuestAttribute()
    {
        return $this->user_id === null || !empty($this->guest_info);
    }
    
    public function getGuestNameAttribute()
    {
        if ($this->guest_info && isset($this->guest_info['name'])) {
            return $this->guest_info['name'];
        }
        return 'Guest';
    }
}