<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Cart;
use Illuminate\Support\Facades\View;

class ShareSettingsMiddleware
{
    /**
     * Share global data with all views
     */
    public function handle(Request $request, Closure $next)
    {
        // Share settings
        $settings = $this->getSettings();
        View::share('settings', $settings);

        // Share categories for navigation
        $categories = $this->getCategories();
        View::share('categories', $categories);

        // Share cart data
        $cartData = $this->getCartData();
        View::share('cartCount', $cartData['count']);
        View::share('cartItems', $cartData['items']);
        View::share('subtotal', $cartData['subtotal']);
        View::share('total', $cartData['total']);

        // Share wishlist count
        $wishlistCount = $this->getWishlistCount();
        View::share('wishlistCount', $wishlistCount);

        // Share compare count
        $compareCount = $this->getCompareCount();
        View::share('compareCount', $compareCount);

        // Share current currency
        $currency = session('currency', setting('currency_code', 'BDT'));
        View::share('currency', $currency);

        // Share current locale
        $locale = session('locale', app()->getLocale());
        View::share('locale', $locale);

        return $next($request);
    }

    /**
     * Get all settings as key-value pairs
     */
    protected function getSettings()
    {
        try {
            return cache()->remember('app_settings', 3600, function () {
                return Setting::pluck('value', 'key')->toArray();
            });
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get categories for navigation
     */
    protected function getCategories()
    {
        try {
            return cache()->remember('nav_categories', 3600, function () {
                return Category::with('children')
                    ->whereNull('parent_id')
                    ->where('status', true)
                    ->orderBy('sort_order')
                    ->get();
            });
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Get cart data
     */
    protected function getCartData()
    {
        try {
            if (auth()->check()) {
                $cart = Cart::with('items.product')
                    ->where('user_id', auth()->id())
                    ->where('status', 'active')
                    ->first();
            } else {
                $sessionId = session()->getId();
                $cart = Cart::with('items.product')
                    ->where('session_id', $sessionId)
                    ->where('status', 'guest')
                    ->first();
            }

            if ($cart && $cart->items->isNotEmpty()) {
                $subtotal = $cart->items->sum(function($item) {
                    return $item->price * $item->quantity;
                });
                
                return [
                    'count' => $cart->items->count(),
                    'items' => $cart->items,
                    'subtotal' => $subtotal,
                    'total' => $subtotal + ($cart->shipping_cost ?? 0) + ($cart->tax_amount ?? 0)
                ];
            }
        } catch (\Exception $e) {
            // Log error if needed
        }

        return [
            'count' => 0,
            'items' => collect([]),
            'subtotal' => 0,
            'total' => 0
        ];
    }

    /**
     * Get wishlist count
     */
    protected function getWishlistCount()
    {
        try {
            if (auth()->check()) {
                return auth()->user()->wishlist()->count();
            }

            $sessionId = session()->getId();
            return \App\Models\Wishlist::where('session_id', $sessionId)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get compare list count
     */
    protected function getCompareCount()
    {
        try {
            if (auth()->check()) {
                return auth()->user()->compares()->count();
            }

            $sessionId = session()->getId();
            return \App\Models\Compare::where('session_id', $sessionId)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
}