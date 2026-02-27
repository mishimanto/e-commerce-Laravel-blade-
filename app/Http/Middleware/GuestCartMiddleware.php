<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Cart\CartService;
use Illuminate\Support\Facades\Cookie;

class GuestCartMiddleware
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Handle guest cart merging when user logs in
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && $request->hasCookie('guest_cart')) {
            $this->mergeGuestCart($request->user()->id, $request->cookie('guest_cart'));
            Cookie::queue(Cookie::forget('guest_cart'));
        }

        return $next($request);
    }

    /**
     * Merge guest cart items into user's cart
     */
    protected function mergeGuestCart($userId, $guestCartId)
    {
        // Get guest cart items
        $guestCart = Cart::where('session_id', $guestCartId)
            ->where('status', 'guest')
            ->with('items')
            ->first();

        if (!$guestCart || $guestCart->items->isEmpty()) {
            return;
        }

        // Get or create user cart
        $userCart = Cart::firstOrCreate(
            ['user_id' => $userId, 'status' => 'active'],
            ['status' => 'active']
        );

        // Merge items
        foreach ($guestCart->items as $item) {
            $existingItem = $userCart->items()
                ->where('product_id', $item->product_id)
                ->where('variant_id', $item->variant_id)
                ->first();

            if ($existingItem) {
                $existingItem->increment('quantity', $item->quantity);
            } else {
                $userCart->items()->create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                    'attributes' => $item->attributes,
                    'image' => $item->image
                ]);
            }
        }

        // Delete guest cart
        $guestCart->delete();

        // Recalculate cart totals
        $this->cartService->recalculateCart($userCart);
    }
}