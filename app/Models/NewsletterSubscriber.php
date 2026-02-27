<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'is_active',
        'verified_at',
        'verification_token'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'verified_at' => 'datetime'
    ];

    /**
     * Scope active subscribers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope verified subscribers
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    /**
     * Get subscriber by email
     */
    public static function findByEmail($email)
    {
        return self::where('email', $email)->first();
    }
}