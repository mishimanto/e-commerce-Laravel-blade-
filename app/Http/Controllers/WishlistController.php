<?php

namespace App\Http\Controllers;

use App\Services\Wishlist\WishlistService;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    protected $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    /**
     * Display wishlist page
     */
    public function index()
    {
        $wishlistItems = $this->wishlistService->getWishlist();
        
        return view('storefront.wishlist', compact('wishlistItems'));
    }

    /**
     * Add item to wishlist
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id'
        ]);

        try {
            $added = $this->wishlistService->addItem(
                $request->product_id,
                $request->variant_id
            );

            if (!$added) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product already in wishlist.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product added to wishlist.',
                'wishlist_count' => $this->wishlistService->getCount()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add to wishlist: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove item from wishlist
     */
    public function remove(Request $request, $productId)
    {
        $request->validate([
            'variant_id' => 'nullable|exists:product_variants,id'
        ]);

        try {
            $removed = $this->wishlistService->removeItem(
                $productId,
                $request->variant_id
            );

            if (!$removed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found in wishlist.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Item removed from wishlist.',
                'wishlist_count' => $this->wishlistService->getCount()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove from wishlist: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle wishlist (add/remove)
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id'
        ]);

        try {
            $inWishlist = $this->wishlistService->isInWishlist(
                $request->product_id,
                $request->variant_id
            );

            if ($inWishlist) {
                $this->wishlistService->removeItem(
                    $request->product_id,
                    $request->variant_id
                );
                $message = 'Product removed from wishlist.';
            } else {
                $this->wishlistService->addItem(
                    $request->product_id,
                    $request->variant_id
                );
                $message = 'Product added to wishlist.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'in_wishlist' => !$inWishlist,
                'wishlist_count' => $this->wishlistService->getCount()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle wishlist: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear wishlist
     */
    public function clear()
    {
        try {
            $this->wishlistService->clearWishlist();

            return response()->json([
                'success' => true,
                'message' => 'Wishlist cleared successfully.',
                'wishlist_count' => 0
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear wishlist: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if product is in wishlist (AJAX)
     */
    public function check($productId)
    {
        try {
            $inWishlist = $this->wishlistService->isInWishlist($productId);

            return response()->json([
                'success' => true,
                'in_wishlist' => $inWishlist
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check wishlist: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Move wishlist item to cart
     */
    public function moveToCart($productId)
    {
        try {
            // Add to cart
            $cartService = app(\App\Services\Cart\CartService::class);
            $cartService->addItem($productId, null, 1);

            // Remove from wishlist
            $this->wishlistService->removeItem($productId);

            return response()->json([
                'success' => true,
                'message' => 'Product moved to cart successfully.',
                'wishlist_count' => $this->wishlistService->getCount()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to move to cart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get wishlist count
     */
    public function count()
    {
        try {
            return response()->json([
                'success' => true,
                'wishlist_count' => $this->wishlistService->getCount()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get wishlist count: ' . $e->getMessage()
            ], 500);
        }
    }
}