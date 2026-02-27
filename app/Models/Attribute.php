<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    protected $fillable = [
        'name', 'slug', 'type', 'is_required', 
        'is_filterable', 'sort_order'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_filterable' => 'boolean',
        'sort_order' => 'integer'
    ];

    const TYPES = [
        'text' => 'Text',
        'select' => 'Select Dropdown',
        'radio' => 'Radio Buttons',
        'checkbox' => 'Checkboxes',
        'color' => 'Color Picker',
        'size' => 'Size Buttons',
        'number' => 'Number',
        'range' => 'Range Slider'
    ];

     // Category-specific attribute groups
    const CATEGORY_ATTRIBUTES = [
        'smartphones' => ['color', 'storage', 'ram', 'processor', 'screen_size', 'camera_resolution', 'battery_capacity'],
        'laptops' => ['color', 'processor', 'ram', 'storage', 'screen_size', 'operating_system', 'graphics'],
        'headphones' => ['color', 'connectivity', 'material', 'battery_life', 'frequency_response', 'impedance'],
        'speakers' => ['color', 'connectivity', 'battery_life', 'power_output', 'water_resistant'],
        'wearables' => ['color', 'size', 'material', 'battery_life', 'water_resistant', 'connectivity'],
        'accessories' => ['color', 'size', 'material', 'compatibility'],
    ];

    /**
     * Get the values for the attribute.
     */
    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }

    /**
     * Get the products for the attribute.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('value')
            ->withTimestamps();
    }

    public static function getForCategory($categorySlug)
    {
        $attributeSlugs = self::CATEGORY_ATTRIBUTES[$categorySlug] ?? [];
        
        return self::with('values')
            ->whereIn('slug', $attributeSlugs)
            ->orWhere('is_filterable', true)
            ->orderBy('sort_order')
            ->get();
    }
}