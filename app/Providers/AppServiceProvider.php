<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\Compare;
use App\Models\Category;
use App\Repositories\ProductRepository;
use App\Services\Product\ProductService;
use App\Helpers\SettingsHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // First bind the repository with Product model
        $this->app->bind(ProductRepository::class, function ($app) {
            return new ProductRepository(new Product());
        });

        // Then bind the service with repository dependency
        $this->app->bind(ProductService::class, function ($app) {
            return new ProductService(
                $app->make(ProductRepository::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share data with all views
        View::composer('*', function ($view) {
            // Cart count
            $cartCount = $this->getCartCount();
            $view->with('cartCount', $cartCount);

            // Wishlist count
            $wishlistCount = $this->getWishlistCount();
            $view->with('wishlistCount', $wishlistCount);

            // Compare count
            $compareCount = $this->getCompareCount();
            $view->with('compareCount', $compareCount);

            // Categories for navbar
            $categories = $this->getCategories();
            $view->with('categories', $categories);

           
        });
    }

    /**
     * Get cart count for current user/session
     */
    protected function getCartCount(): int
    {
        try {
            if (auth()->check()) {
                $cart = Cart::where('user_id', auth()->id())
                    ->where('status', 'active')
                    ->first();
                
                if ($cart) {
                    return (int) $cart->items()->sum('quantity');
                }
            } else {
                $sessionId = session()->getId();
                $cart = Cart::where('session_id', $sessionId)
                    ->where('status', 'guest')
                    ->first();
                
                if ($cart) {
                    return (int) $cart->items()->sum('quantity');
                }
            }
        } catch (\Exception $e) {
            // Log error if needed
            \Log::error('Error getting cart count: ' . $e->getMessage());
        }
        
        return 0;
    }

    /**
     * Get wishlist count for current user/session
     */
    protected function getWishlistCount(): int
    {
        try {
            if (auth()->check()) {
                return Wishlist::where('user_id', auth()->id())->count();
            } else {
                $sessionId = session()->getId();
                return Wishlist::where('session_id', $sessionId)->count();
            }
        } catch (\Exception $e) {
            \Log::error('Error getting wishlist count: ' . $e->getMessage());
        }
        
        return 0;
    }

    /**
     * Get compare count for current user/session
     */
    protected function getCompareCount(): int
    {
        try {
            if (auth()->check()) {
                return Compare::where('user_id', auth()->id())->count();
            } else {
                $sessionId = session()->getId();
                return Compare::where('session_id', $sessionId)->count();
            }
        } catch (\Exception $e) {
            \Log::error('Error getting compare count: ' . $e->getMessage());
        }
        
        return 0;
    }

    /**
     * Get categories for navbar
     */
    protected function getCategories()
    {
        try {
            return Category::with('children')
                ->whereNull('parent_id')
                ->where('status', true)
                ->orderBy('sort_order')
                ->get();
        } catch (\Exception $e) {
            \Log::error('Error getting categories: ' . $e->getMessage());
            return collect([]);
        }
    }
}