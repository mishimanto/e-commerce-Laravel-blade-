<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    protected $fillable = [
        'name', 'code', 'logo', 'description',
        'api_url', 'api_key', 'api_secret', 'username', 'password',
        'sandbox_mode', 'status', 'settings'
    ];

    protected $casts = [
        'sandbox_mode' => 'boolean',
        'status' => 'boolean',
        'settings' => 'array'
    ];

    const COURIERS = [
        'pathao' => 'Pathao',
        'steadfast' => 'Steadfast',
        'redx' => 'RedX',
        'sundarban' => 'Sundarban',
        'sa_poribohon' => 'SA Poribohon'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'shipping_courier', 'code');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }
}