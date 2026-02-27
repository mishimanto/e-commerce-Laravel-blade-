<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeValue extends Model
{
    protected $fillable = [
        'attribute_id', 'value', 'slug', 
        'color_code', 'sort_order'
    ];

    protected $casts = [
        'sort_order' => 'integer'
    ];

    /**
     * Get the attribute that owns the value.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Get the products for the attribute value.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('price_adjustment', 'stock')
            ->withTimestamps();
    }
}