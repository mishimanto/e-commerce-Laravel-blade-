<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\RecentlyViewed;
use App\Services\Cart\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Show cart page
     */
    public function index()
    {
        try {
            // Get cart items from service
            $cartData = $this->cartService->getFormattedCart();
            
            // Extract variables for the view
            $cartItems = $cartData['items'] ?? collect([]);
            $subtotal = $cartData['subtotal'] ?? 0;
            $discount = $cartData['discount'] ?? 0;
            $shipping = $cartData['shipping'] ?? 100;
            $total = $cartData['total'] ?? 0;
            
            // Get recently viewed products
            $recentlyViewed = $this->getRecentlyViewed();

            return view('storefront.cart.index', compact(
                'cartItems',
                'subtotal',
                'discount',
                'shipping',
                'total',
                'recentlyViewed'
            ));

        } catch (\Exception $e) {
            Log::error('Cart index error: ' . $e->getMessage());
            
            // Return empty cart if error occurs
            return view('storefront.cart.index', [
                'cartItems' => collect([]),
                'subtotal' => 0,
                'discount' => 0,
                'shipping' => 100,
                'total' => 100,
                'recentlyViewed' => collect([])
            ]);
        }
    }

    public function add(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'variant_id' => 'nullable|exists:product_variants,id',
                'quantity' => 'required|integer|min:1|max:100'
            ]);

            $item = $this->cartService->addItem(
                $request->product_id,
                $request->variant_id,
                $request->quantity
            );

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully!',
                'cart_count' => $this->cartService->getCount(),
                'cart_total' => $this->cartService->getTotal(),
                'item' => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'quantity' => $item->quantity,
                    'price' => (float) $item->price,
                    'subtotal' => (float) $item->subtotal
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Cart add error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $itemId)
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:1|max:100'
            ]);

            $item = $this->cartService->updateQuantity($itemId, $request->quantity);

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully',
                'cart_count' => $this->cartService->getCount(),
                'subtotal' => $this->cartService->getSubtotal(),
                'total' => $this->cartService->getTotal(),
                'item_total' => $item->subtotal
            ]);

        } catch (\Exception $e) {
            Log::error('Cart update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function remove($itemId)
    {
        try {
            $this->cartService->removeItem($itemId);

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cart_count' => $this->cartService->getCount(),
                'subtotal' => $this->cartService->getSubtotal(),
                'total' => $this->cartService->getTotal()
            ]);

        } catch (\Exception $e) {
            Log::error('Cart remove error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function clear()
    {
        try {
            $this->cartService->clearCart();

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully',
                'cart_count' => 0
            ]);

        } catch (\Exception $e) {
            Log::error('Cart clear error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getSummary()
    {
        try {
            $cartData = $this->cartService->getFormattedCart();

            return response()->json(array_merge(['success' => true], $cartData));

        } catch (\Exception $e) {
            Log::error('Cart summary error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function applyCoupon(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string|max:50'
            ]);

            $result = $this->cartService->applyCoupon($request->code);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'discount' => $result['discount'],
                'subtotal' => $this->cartService->getSubtotal(),
                'total' => $this->cartService->getTotal()
            ]);

        } catch (\Exception $e) {
            Log::error('Coupon apply error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function removeCoupon()
    {
        try {
            $result = $this->cartService->removeCoupon();

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'subtotal' => $this->cartService->getSubtotal(),
                'total' => $this->cartService->getTotal()
            ]);

        } catch (\Exception $e) {
            Log::error('Coupon remove error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    protected function getRecentlyViewed($limit = 4)
    {
        try {
            if (auth()->check()) {
                return RecentlyViewed::where('user_id', auth()->id())
                    ->with('product.images', 'product.brand')
                    ->latest('viewed_at')
                    ->limit($limit)
                    ->get()
                    ->pluck('product')
                    ->filter();
            }

            $sessionId = session()->getId();
            return RecentlyViewed::where('session_id', $sessionId)
                ->with('product.images', 'product.brand')
                ->latest('viewed_at')
                ->limit($limit)
                ->get()
                ->pluck('product')
                ->filter();

        } catch (\Exception $e) {
            Log::error('Recently viewed error: ' . $e->getMessage());
            return collect([]);
        }
    }
}