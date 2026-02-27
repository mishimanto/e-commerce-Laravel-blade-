<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean'
    ];

    /**
     * Get the user that owns the address.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include default addresses.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Get formatted address string.
     */
    public function getFormattedAttribute(): string
    {
        return "{$this->address}, {$this->city}, {$this->state} {$this->zip}, {$this->country}";
    }

    /**
     * Get full name with address.
     */
    public function getFullDetailsAttribute(): string
    {
        return "{$this->name} - {$this->phone}, {$this->getFormattedAttribute()}";
    }
}