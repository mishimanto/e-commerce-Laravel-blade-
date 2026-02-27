<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Coupon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartService
{
    protected $cart;
    protected $sessionKey = 'cart_id';

    public function __construct()
    {
        $this->initializeCart();
    }

    protected function initializeCart()
    {
        if (auth()->check()) {
            $this->cart = Cart::firstOrCreate(
                ['user_id' => auth()->id(), 'status' => 'active'],
                [
                    'status' => 'active',
                    'shipping_cost' => 100,
                    'discount_amount' => 0,
                    'total' => 0
                ]
            );
        } else {
            $this->cart = $this->getGuestCart();
        }
    }

    protected function getGuestCart()
    {
        $cartId = Session::get($this->sessionKey);
        
        if ($cartId) {
            $cart = Cart::find($cartId);
            if ($cart) {
                return $cart;
            }
        }
        
        $cart = Cart::create([
            'session_id' => Session::getId(),
            'status' => 'guest',
            'shipping_cost' => 100,
            'discount_amount' => 0,
            'total' => 0
        ]);
        
        Session::put($this->sessionKey, $cart->id);
        return $cart;
    }

    /**
     * Add item to cart
     */
    public function addItem($productId, $variantId = null, $quantity = 1)
    {
        try {
            DB::beginTransaction();

            $product = Product::with(['images'])->findOrFail($productId);
            
            // Calculate price and get attributes
            $price = 0;
            $sku = $product->sku;
            $attributes = null;
            $stock = 0;
            $image = $product->primary_image_url;

            if ($variantId) {
                $variant = ProductVariant::with('product')->find($variantId);
                if (!$variant) {
                    throw new \Exception('Variant not found');
                }
                
                $price = $variant->price; // Uses accessor that adds adjustment
                $sku = $variant->sku;
                $attributes = $variant->attributes;
                $stock = $variant->stock ?? 0;
                
                // Use variant image if available
                if ($variant->image) {
                    $image = filter_var($variant->image, FILTER_VALIDATE_URL) 
                        ? $variant->image 
                        : asset('storage/' . ltrim($variant->image, '/'));
                }
            } else {
                $price = $product->sale_price ?? $product->base_price;
                $stock = $product->stock ?? 0;
            }

            // Ensure price is not null or zero
            if (empty($price) || $price <= 0) {
                Log::error('Price is zero or null', [
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'price' => $price
                ]);
                throw new \Exception('Product price could not be determined');
            }

            // Check stock
            if ($stock < $quantity) {
                throw new \Exception('Requested quantity not available in stock. Available: ' . $stock);
            }

            // Check if same product with same variant exists
            $existingItem = CartItem::where('cart_id', $this->cart->id)
                ->where('product_id', $productId)
                ->where(function($query) use ($variantId) {
                    if ($variantId) {
                        $query->where('variant_id', $variantId);
                    } else {
                        $query->whereNull('variant_id');
                    }
                })
                ->first();

            if ($existingItem) {
                // Update quantity if same variant
                $newQuantity = $existingItem->quantity + $quantity;
                if ($newQuantity > $stock) {
                    throw new \Exception('Cannot add more than available stock. Maximum: ' . $stock);
                }
                
                $existingItem->quantity = $newQuantity;
                $existingItem->subtotal = $price * $newQuantity;
                $existingItem->save();
                $item = $existingItem;
            } else {
                // Create new item (different variant = new item)
                $itemData = [
                    'cart_id' => $this->cart->id,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'name' => $product->name,
                    'sku' => $sku,
                    'price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $price * $quantity,
                    'image' => $image
                ];

                // Add attributes if exists
                if ($attributes) {
                    $itemData['attributes'] = is_array($attributes) ? json_encode($attributes) : $attributes;
                }

                Log::info('Creating cart item:', $itemData);

                $item = CartItem::create($itemData);
            }

            $this->updateCartTotals();

            DB::commit();

            return $item;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CartService addItem error: ' . $e->getMessage(), [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity($itemId, $quantity)
    {
        try {
            DB::beginTransaction();

            $item = CartItem::where('cart_id', $this->cart->id)
                ->where('id', $itemId)
                ->with('product', 'variant')
                ->first();

            if (!$item) {
                throw new \Exception('Cart item not found');
            }

            // Check stock
            $stock = $item->variant_id ? ($item->variant->stock ?? 0) : ($item->product->stock ?? 0);
            if ($stock < $quantity) {
                throw new \Exception('Requested quantity not available in stock. Available: ' . $stock);
            }

            $item->quantity = $quantity;
            $item->subtotal = $item->price * $quantity;
            $item->save();

            $this->updateCartTotals();

            DB::commit();

            return $item;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CartService updateQuantity error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Remove item from cart
     */
    public function removeItem($itemId)
    {
        try {
            DB::beginTransaction();

            $deleted = CartItem::where('cart_id', $this->cart->id)
                ->where('id', $itemId)
                ->delete();

            $this->updateCartTotals();

            DB::commit();

            return $deleted;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CartService removeItem error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get cart with items
     */
    public function getCart()
    {
        return $this->cart->load(['items.product', 'items.variant']);
    }

    /**
     * Get formatted cart items for frontend
     */
    public function getFormattedCart()
    {
        $items = $this->cart->items()
            ->with(['product', 'variant'])
            ->get()
            ->map(function ($item) {
                $imageUrl = $item->image;
                
                if (!$imageUrl && $item->product && $item->product->images->isNotEmpty()) {
                    $image = $item->product->images->first()->url;
                    $imageUrl = filter_var($image, FILTER_VALIDATE_URL) 
                        ? $image 
                        : asset('storage/' . ltrim($image, '/'));
                }
                
                // Format attributes for display
                $attributes = null;
                if ($item->attributes) {
                    $attrs = is_string($item->attributes) 
                        ? json_decode($item->attributes, true) 
                        : $item->attributes;
                    
                    if (is_array($attrs) && count($attrs) > 0) {
                        // Format as "Color: Red | Size: XL" for display
                        $attrStrings = [];
                        foreach ($attrs as $key => $value) {
                            $attrStrings[] = ucfirst($key) . ': ' . $value;
                        }
                        $attributes = implode(' | ', $attrStrings);
                    }
                }
                
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'price' => (float) $item->price,
                    'quantity' => (int) $item->quantity,
                    'subtotal' => (float) $item->subtotal,
                    'attributes' => $attributes,
                    'attributes_raw' => $item->attributes ? json_decode($item->attributes, true) : null,
                    'image' => $imageUrl ?? asset('images/no-image.jpg'),
                    'stock' => $item->variant_id ? ($item->variant->stock ?? 0) : ($item->product->stock ?? 0)
                ];
            });

        return [
            'items' => $items,
            'subtotal' => $this->getSubtotal(),
            'discount' => $this->cart->discount_amount ?? 0,
            'shipping' => $this->cart->shipping_cost ?? 100,
            'total' => $this->getTotal(),
            'count' => $this->getCount(),
            'coupon_code' => $this->cart->coupon_code
        ];
    }

    /**
     * Get subtotal
     */
    public function getSubtotal()
    {
        return (float) $this->cart->items()->sum('subtotal');
    }

    /**
     * Get total items count
     */
    public function getCount()
    {
        return (int) $this->cart->items()->sum('quantity');
    }

    /**
     * Get total with shipping and discount
     */
    public function getTotal()
    {
        return $this->getSubtotal() - ($this->cart->discount_amount ?? 0);
    }

    /**
     * Update cart totals
     */
    protected function updateCartTotals()
    {
        $subtotal = $this->getSubtotal();
        $total = $subtotal + ($this->cart->shipping_cost ?? 100) - ($this->cart->discount_amount ?? 0);
        
        $this->cart->update([
            'total' => $total
        ]);
    }

    /**
     * Apply coupon to cart
     */
    public function applyCoupon($code)
    {
        try {
            DB::beginTransaction();

            // Find the coupon
            $coupon = Coupon::where('code', $code)
                ->where('is_active', true)
                ->where('starts_at', '<=', now())
                ->where('expires_at', '>=', now())
                ->first();

            if (!$coupon) {
                throw new \Exception('Invalid or expired coupon code');
            }

            // Check usage limit
            if ($coupon->usage_limit && $coupon->total_used >= $coupon->usage_limit) {
                throw new \Exception('This coupon has reached its usage limit');
            }

            // Calculate subtotal
            $subtotal = $this->getSubtotal();

            // Check minimum order amount
            if ($coupon->min_order_amount && $subtotal < $coupon->min_order_amount) {
                throw new \Exception('Minimum order amount for this coupon is ৳' . number_format($coupon->min_order_amount, 2));
            }

            // Calculate discount
            $discount = $coupon->calculateDiscount($subtotal);

            // Handle free shipping separately
            if ($coupon->type === 'free_shipping') {
                $discount = 0;
                $this->cart->update([
                    'shipping_cost' => 0
                ]);
            }

            // Apply to cart
            $this->cart->update([
                'coupon_code' => $code,
                'discount_amount' => $discount
            ]);

            // Increment coupon usage
            $coupon->increment('total_used');

            $this->updateCartTotals();

            DB::commit();

            return [
                'success' => true,
                'discount' => $discount,
                'message' => 'Coupon applied successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Coupon apply error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Remove coupon from cart
     */
    public function removeCoupon()
    {
        try {
            DB::beginTransaction();

            $oldCouponCode = $this->cart->coupon_code;
            
            if ($oldCouponCode) {
                // Decrement coupon usage
                $coupon = Coupon::where('code', $oldCouponCode)->first();
                if ($coupon) {
                    $coupon->decrement('total_used');
                }
            }

            // Reset shipping cost to default if it was free shipping
            if ($this->cart->shipping_cost == 0) {
                $this->cart->shipping_cost = 100;
            }

            $this->cart->update([
                'coupon_code' => null,
                'discount_amount' => 0
            ]);

            $this->updateCartTotals();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Coupon removed successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Coupon remove error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get applied coupon info
     */
    public function getAppliedCoupon()
    {
        if ($this->cart->coupon_code) {
            $coupon = Coupon::where('code', $this->cart->coupon_code)->first();
            return [
                'code' => $this->cart->coupon_code,
                'discount' => $this->cart->discount_amount,
                'coupon' => $coupon
            ];
        }
        return null;
    }

    /**
     * Clear cart
     */
    public function clearCart()
    {
        try {
            DB::beginTransaction();

            // Remove coupon if applied
            if ($this->cart->coupon_code) {
                $coupon = Coupon::where('code', $this->cart->coupon_code)->first();
                if ($coupon) {
                    $coupon->decrement('total_used');
                }
            }

            $this->cart->items()->delete();
            $this->cart->update([
                'total' => 0,
                'discount_amount' => 0,
                'coupon_code' => null,
                'shipping_cost' => 100
            ]);

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CartService clearCart error: ' . $e->getMessage());
            throw $e;
        }
    }
}