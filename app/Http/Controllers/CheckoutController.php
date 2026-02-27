<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingMethod;
use App\Models\PaymentMethod;
use App\Models\Setting; // Add this
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    protected $cart;
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
        $this->initializeCart();
    }

    protected function initializeCart()
    {
        if (auth()->check()) {
            $this->cart = Cart::firstOrCreate(
                ['user_id' => auth()->id()],
                ['status' => 'active', 'total' => 0]
            );
        } else {
            $sessionId = session()->getId();
            $this->cart = Cart::firstOrCreate(
                ['session_id' => $sessionId],
                ['status' => 'guest', 'total' => 0]
            );
        }
    }

    public function index()
    {
        // Load cart items
        $cartItems = $this->cart->items()
            ->with(['product', 'variant'])
            ->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        // Calculate subtotal
        $subtotal = $cartItems->sum('subtotal');
        $discount = $this->cart->discount_amount ?? 0;
        
        // Get tax settings from database
        $taxEnabled = Setting::where('key', 'tax_enabled')->value('value');
        $taxRate = Setting::where('key', 'tax_rate')->value('value');
        
        // Convert to proper format
        $taxEnabled = $taxEnabled ? json_decode($taxEnabled) : false;
        $taxRate = $taxRate ? (float) json_decode($taxRate) : 0;
        
        // If tax is disabled, set rate to 0
        if (!$taxEnabled) {
            $taxRate = 0;
        }

        // Get shipping methods from database
        $shippingMethods = ShippingMethod::active()
            ->ordered()
            ->get()
            ->map(function ($method) use ($subtotal) {
                return [
                    'code' => $method->code,
                    'name' => $method->name,
                    'cost' => $method->getFinalCost($subtotal),
                    'description' => $method->description,
                    'delivery_time' => $method->delivery_time,
                    'is_available' => $method->isAvailableForSubtotal($subtotal),
                    'original_cost' => $method->cost,
                    'free_shipping_applies' => $method->freeShippingApplies($subtotal)
                ];
            })
            ->filter(function ($method) {
                return $method['is_available'];
            })
            ->values()
            ->toArray();

        // Get payment methods from database
        $paymentMethods = PaymentMethod::active()
            ->ordered()
            ->get()
            ->map(function ($method) use ($subtotal) {
                return [
                    'code' => $method->code,
                    'name' => $method->name,
                    'icon' => $method->icon_url,
                    'description' => $method->description,
                    'instructions' => $method->instructions,
                    'type' => $method->type,
                    'fee' => $method->calculateFee($subtotal),
                    'is_available' => $method->isAvailableForSubtotal($subtotal)
                ];
            })
            ->filter(function ($method) {
                return $method['is_available'];
            })
            ->values()
            ->toArray();

        // Format cart items properly
        $formattedItems = $cartItems->map(function ($item) {
            // Decode attributes if it's a string
            $attributes = null;
            if ($item->attributes) {
                $attributes = is_string($item->attributes) 
                    ? json_decode($item->attributes, true) 
                    : $item->attributes;
            }
            
            // Get image URL
            $imageUrl = $item->image;
            if (!$imageUrl && $item->product && $item->product->images->isNotEmpty()) {
                $primaryImage = $item->product->images->firstWhere('is_primary', true);
                $image = $primaryImage ?? $item->product->images->first();
                if ($image) {
                    if (filter_var($image->url, FILTER_VALIDATE_URL)) {
                        $imageUrl = $image->url;
                    } elseif (str_starts_with($image->url, '/')) {
                        $imageUrl = asset($image->url);
                    } else {
                        $imageUrl = asset('storage/' . $image->url);
                    }
                }
            }
            
            return [
                'id' => $item->id,
                'cart_id' => $item->cart_id,
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id,
                'name' => $item->name,
                'sku' => $item->sku,
                'price' => (float) $item->price,
                'quantity' => (int) $item->quantity,
                'subtotal' => (float) $item->subtotal,
                'attributes' => $attributes,
                'attributes_raw' => $item->attributes,
                'image' => $imageUrl ?? asset('images/no-image.jpg'),
                'variant_sku' => $item->variant ? $item->variant->sku : null,
                'product' => [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'slug' => $item->product->slug,
                    'sku' => $item->product->sku,
                ],
                'stock' => $item->variant ? ($item->variant->stock ?? 0) : ($item->product->stock ?? 0)
            ];
        })->toArray();

        // Store checkout data in session
        session(['checkout_data' => [
            'cart_id' => $this->cart->id,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax_rate' => $taxRate,
            'tax_enabled' => $taxEnabled
        ]]);

        return view('storefront.checkout.index', compact(
            'formattedItems', 
            'subtotal', 
            'discount',
            'taxRate',
            'taxEnabled',
            'shippingMethods',
            'paymentMethods'
        ));
    }

    public function process(Request $request)
    {
        try {
            Log::info('Checkout process started', ['data' => $request->all()]);

            // Validate shipping method exists and is active
            $shippingMethod = ShippingMethod::active()
                ->where('code', $request->shipping_method)
                ->first();

            if (!$shippingMethod) {
                throw new \Exception('Invalid shipping method selected');
            }

            // Validate payment method exists and is active
            $paymentMethod = PaymentMethod::active()
                ->where('code', $request->payment_method)
                ->first();

            if (!$paymentMethod) {
                throw new \Exception('Invalid payment method selected');
            }

            // Base validation rules
            $rules = [
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'shipping_name' => 'required|string|max:255',
                'shipping_address' => 'required|string',
                'shipping_city' => 'required|string|max:100',
                'shipping_state' => 'required|string|max:100',
                'shipping_zip' => 'required|string|max:20',
                'shipping_country' => 'required|string|max:100',
                'shipping_method' => 'required|string',
                'payment_method' => 'required|string',
                'different_billing' => 'nullable|boolean',
                'notes' => 'nullable|string'
            ];

            // Add billing validation
            if ($request->has('different_billing') && $request->different_billing == '1') {
                $rules = array_merge($rules, [
                    'billing_name' => 'required|string|max:255',
                    'billing_address' => 'required|string',
                    'billing_city' => 'required|string|max:100',
                    'billing_state' => 'required|string|max:100',
                    'billing_zip' => 'required|string|max:20',
                    'billing_country' => 'required|string|max:100',
                ]);
            }

            // Validate the request
            $validated = $request->validate($rules);

            Log::info('Checkout validation passed');

            // Get checkout data from session
            $checkoutData = session('checkout_data', []);
            $subtotal = $checkoutData['subtotal'] ?? 0;
            $taxRate = $checkoutData['tax_rate'] ?? 0;

            // Get shipping cost
            $shippingCost = $shippingMethod->getFinalCost($subtotal);

            // Calculate tax ONLY on product subtotal (not on shipping or other charges)
            $taxAmount = ($subtotal * $taxRate) / 100;

            // Prepare order data
            $orderData = [
                'email' => $request->email,
                'phone' => $request->phone,
                'shipping_name' => $request->shipping_name,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_state' => $request->shipping_state,
                'shipping_zip' => $request->shipping_zip,
                'shipping_country' => $request->shipping_country,
                'shipping_method' => $request->shipping_method,
                'shipping_cost' => $shippingCost,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'tax_amount' => $taxAmount, // Add tax amount
                'tax_rate' => $taxRate // Add tax rate for reference
            ];

            // Add billing address
            if ($request->has('different_billing') && $request->different_billing == '1') {
                $orderData['billing_name'] = $request->billing_name;
                $orderData['billing_address'] = $request->billing_address;
                $orderData['billing_city'] = $request->billing_city;
                $orderData['billing_state'] = $request->billing_state;
                $orderData['billing_zip'] = $request->billing_zip;
                $orderData['billing_country'] = $request->billing_country;
            } else {
                // If billing same as shipping, copy shipping data
                $orderData['billing_name'] = $request->shipping_name;
                $orderData['billing_address'] = $request->shipping_address;
                $orderData['billing_city'] = $request->shipping_city;
                $orderData['billing_state'] = $request->shipping_state;
                $orderData['billing_zip'] = $request->shipping_zip;
                $orderData['billing_country'] = $request->shipping_country;
            }

            Log::info('Creating order with data', $orderData);

            // Create order
            $order = $this->orderService->createOrderFromCart($orderData);

            // Save payment method fee if needed
            if ($paymentMethod->fixed_fee > 0 || $paymentMethod->percentage_fee > 0) {
                $order->update([
                    'payment_fee' => $paymentMethod->calculateFee($subtotal)
                ]);
            }

            Log::info('Order created successfully', ['order_id' => $order->id]);

            // Clear session
            session()->forget('checkout_data');
            
            if (!auth()->check()) {
                session(['last_order_id' => $order->id]);
                session(['guest_email' => $request->email]);
            }

            return redirect()->route('checkout.success', $order->id)
                ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            Log::error('Checkout error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->with('error', 'Failed to process order: ' . $e->getMessage())
                ->withInput();
        }
    }

    // app/Http/Controllers/CheckoutController.php

public function success($orderId)
{
    try {
        $order = Order::with('items')->findOrFail($orderId);
        
        // Check if order belongs to current user/session
        if (auth()->check()) {
            if ($order->user_id !== auth()->id()) {
                Log::warning('Unauthorized access to order success page', [
                    'order_id' => $orderId,
                    'user_id' => auth()->id(),
                    'order_user_id' => $order->user_id
                ]);
                abort(403);
            }
        } else {
            $lastOrderId = session('last_order_id');
            if (!$lastOrderId || $lastOrderId != $orderId) {
                Log::warning('Unauthorized guest access to order success page', [
                    'order_id' => $orderId,
                    'session_order_id' => $lastOrderId
                ]);
                abort(403);
            }
        }

        // সেশনে অর্ডার আইডি রাখুন পরবর্তী ইউজের জন্য
        session(['current_order_id' => $orderId]);

        return view('storefront.checkout.success', compact('order'));

    } catch (\Exception $e) {
        Log::error('Error loading order success page: ' . $e->getMessage());
        abort(404);
    }
}

    public function cancel($orderId = null)
    {
        return view('storefront.checkout.cancel');
    }
}