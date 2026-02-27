<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Category;
use App\Models\Cart;

class Navbar extends Component
{
    public $categories;
    public $cartCount;

    public function __construct()
    {
        $this->categories = Category::parents()
            ->with('children')
            ->active()
            ->orderBy('sort_order')
            ->get();
            
        $this->cartCount = $this->getCartCount();
    }

    protected function getCartCount()
    {
        if (auth()->check()) {
            return Cart::where('user_id', auth()->id())
                ->where('status', 'active')
                ->withCount('items')
                ->first()?->items_count ?? 0;
        }
        
        return Cart::where('session_id', session()->getId())
            ->where('status', 'active')
            ->withCount('items')
            ->first()?->items_count ?? 0;
    }

    public function render()
    {
        return view('components.navbar');
    }
}