<?php

namespace App\Services\Wishlist;

use App\Models\Wishlist;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;

class WishlistService
{
    /**
     * Get wishlist items
     */
    public function getWishlist()
    {
        if (auth()->check()) {
            return Wishlist::with(['product', 'variant'])
                ->where('user_id', auth()->id())
                ->get();
        } else {
            $sessionId = Session::getId();
            return Wishlist::with(['product', 'variant'])
                ->where('session_id', $sessionId)
                ->get();
        }
    }

    /**
     * Add item to wishlist
     */
    public function addItem($productId, $variantId = null)
    {
        // Check if already exists
        $exists = $this->isInWishlist($productId, $variantId);
        
        if ($exists) {
            return false;
        }

        $data = [
            'product_id' => $productId,
            'variant_id' => $variantId
        ];

        if (auth()->check()) {
            $data['user_id'] = auth()->id();
        } else {
            $data['session_id'] = Session::getId();
        }

        return Wishlist::create($data);
    }

    /**
     * Remove item from wishlist
     */
    public function removeItem($productId, $variantId = null)
    {
        $query = Wishlist::query();

        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            $query->where('session_id', Session::getId());
        }

        $query->where('product_id', $productId);
        
        if ($variantId) {
            $query->where('variant_id', $variantId);
        }

        return $query->delete();
    }

    /**
     * Check if product is in wishlist
     */
    public function isInWishlist($productId, $variantId = null)
    {
        $query = Wishlist::query();

        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            $query->where('session_id', Session::getId());
        }

        $query->where('product_id', $productId);
        
        if ($variantId) {
            $query->where('variant_id', $variantId);
        } else {
            $query->whereNull('variant_id');
        }

        return $query->exists();
    }

    /**
     * Get wishlist count
     */
    public function getCount()
    {
        if (auth()->check()) {
            return Wishlist::where('user_id', auth()->id())->count();
        } else {
            return Wishlist::where('session_id', Session::getId())->count();
        }
    }

    /**
     * Clear wishlist
     */
    public function clearWishlist()
    {
        if (auth()->check()) {
            return Wishlist::where('user_id', auth()->id())->delete();
        } else {
            return Wishlist::where('session_id', Session::getId())->delete();
        }
    }
}