<?php

namespace App\Services\Product;

use App\Models\Category;
use App\Models\Brand;
use App\Models\Attribute;
use Illuminate\Support\Facades\Cache;

class SidebarService
{
    public function get()
    {
        return Cache::remember('product_sidebar', 3600, function () {
            return [
                'categories' => Category::with('children')
                    ->whereNull('parent_id')
                    ->where('status', true)
                    ->orderBy('sort_order')
                    ->get(),

                'brands' => Brand::where('status', true)
                    ->orderBy('name')
                    ->get(),

                'attributes' => Attribute::with('values')
                    ->where('is_filterable', true)
                    ->orderBy('sort_order')
                    ->get()
            ];
        });
    }

    public function clear()
    {
        Cache::forget('product_sidebar');
    }
}