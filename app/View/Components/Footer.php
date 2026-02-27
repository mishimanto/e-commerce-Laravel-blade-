<?php
// app/View/Components/Footer.php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Setting;

class Footer extends Component
{
    public $categories;
    public $brands;
    public $settings;

    public function __construct()
    {
        // Get footer categories - parent categories only
        $this->categories = Category::whereNull('parent_id')
            ->where('status', 1)
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        // Get brands for footer - using correct column names
        $this->brands = Brand::where('status', 1)  // 'status' column, not 'is_active'
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(8)
            ->get();

        // Get store settings
        $this->settings = Setting::all()->pluck('value', 'key')->toArray();
    }

    public function render()
    {
        return view('components.footer');
    }
}