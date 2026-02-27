<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Cart;
use App\Models\User;
use App\Services\Cart\CartService;
use App\Services\Product\ProductService;
use App\Services\Product\VariantService;
use App\Notifications\OrderConfirmation;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderStatusUpdated;
use App\Notifications\OrderCancelled;
use App\Notifications\OrderShipped;
use App\Notifications\PaymentConfirmation;
use App\Mail\GuestOrderConfirmation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\URL;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderService
{
    protected $cartService;
    protected $productService;
    protected $variantService;
    protected $orderRepository;

    public function __construct(
        CartService $cartService, 
        ProductService $productService,
        VariantService $variantService,
        OrderRepository $orderRepository 
    ) {
        $this->cartService = $cartService;
        $this->productService = $productService;
        $this->variantService = $variantService;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Create order from cart
     */
    public function createOrderFromCart(array $data)
    {
        return DB::transaction(function () use ($data) {
            $cart = $this->cartService->getCart();

            if ($cart->items->isEmpty()) {
                throw new \Exception('Cart is empty');
            }

            // Generate order number
            $orderNumber = $this->generateOrderNumber();

            // Calculate totals
            $subtotal = $cart->items->sum(function($item) {
                return $item->price * $item->quantity;
            });

            $shippingCost = $data['shipping_cost'] ?? 0;
            $taxRate = config('settings.tax_rate', 0);
            $taxAmount = $subtotal * ($taxRate / 100);
            $total = $subtotal + $shippingCost + $taxAmount - ($cart->discount_amount ?? 0);

            // Prepare order data
            $orderData = [
                'order_number' => $orderNumber,
                'user_id' => auth()->id() ?? null,
                'guest_email' => $data['email'] ?? null,
                'guest_phone' => $data['phone'] ?? null,
                
                // Shipping Address (always required)
                'shipping_name' => $data['shipping_name'],
                'shipping_email' => $data['email'],
                'shipping_phone' => $data['phone'],
                'shipping_address' => $data['shipping_address'],
                'shipping_city' => $data['shipping_city'],
                'shipping_state' => $data['shipping_state'],
                'shipping_zip' => $data['shipping_zip'],
                'shipping_country' => $data['shipping_country'],
                
                // Billing Address (use shipping if not provided)
                'billing_name' => $data['billing_name'] ?? $data['shipping_name'],
                'billing_email' => $data['email'],
                'billing_phone' => $data['phone'],
                'billing_address' => $data['billing_address'] ?? $data['shipping_address'],
                'billing_city' => $data['billing_city'] ?? $data['shipping_city'],
                'billing_state' => $data['billing_state'] ?? $data['shipping_state'],
                'billing_zip' => $data['billing_zip'] ?? $data['shipping_zip'],
                'billing_country' => $data['billing_country'] ?? $data['shipping_country'],
                
                // Order Summary
                'subtotal' => $subtotal,
                'discount_amount' => $cart->discount_amount ?? 0,
                'coupon_code' => $cart->coupon_code,
                'shipping_cost' => $shippingCost,
                'tax_amount' => $taxAmount,
                'total' => $total,
                
                // Shipping & Payment
                'shipping_method' => $data['shipping_method'],
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
                'status' => 'pending',
                
                // Notes
                'notes' => $data['notes'] ?? null,
            ];

            // Create order
            $order = Order::create($orderData);

            // Create order items and update stock
            foreach ($cart->items as $item) {
                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                    'attributes' => $item->attributes,
                ]);

                // Update stock based on variant
                if ($item->variant_id) {
                    // Find the variant and update its stock
                    $variant = ProductVariant::with('product')->find($item->variant_id);
                    if ($variant) {
                        // Check if enough stock available
                        if ($variant->stock < $item->quantity) {
                            throw new \Exception("Insufficient stock for variant: {$item->name}");
                        }
                        
                        // Update variant stock (this will also update product total stock)
                        $this->variantService->updateStock(
                            $variant,
                            $item->quantity,
                            'subtract'
                        );
                        
                        Log::info('Variant stock updated during order creation', [
                            'order_number' => $orderNumber,
                            'variant_id' => $item->variant_id,
                            'product_id' => $item->product_id,
                            'quantity_removed' => $item->quantity,
                            'new_stock' => $variant->fresh()->stock
                        ]);
                    }
                } else {
                    // Update product stock directly if no variant
                    $this->productService->updateStock(
                        $item->product_id, 
                        $item->quantity, 
                        'subtract'
                    );
                }
            }

            // Clear cart
            $this->cartService->clearCart();

            // Create order timeline
            $this->createTimelineEntry($order, 'pending', 'Order placed');

            // Send notifications (একবারই কল হবে)
            Log::info('Calling sendOrderNotifications for order: ' . $order->id);
            $this->sendOrderNotifications($order);
            Log::info('sendOrderNotifications completed for order: ' . $order->id);

            return $order;
        });
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Order $order, $status, $notes = null)
    {
        return DB::transaction(function () use ($order, $status, $notes) {
            $oldStatus = $order->status;
            
            $order->update([
                'status' => $status,
                'admin_notes' => $notes ?? $order->admin_notes
            ]);

            // Create timeline entry
            $this->createTimelineEntry($order, $status, "Status changed from {$oldStatus} to {$status}");

            // Send status update notification
            if ($order->user) {
                $order->user->notify(new OrderStatusUpdated($order, $oldStatus));
            } else {
                $this->sendGuestEmail($order, 'status_updated', [
                    'old_status' => $oldStatus,
                    'new_status' => $status
                ]);
            }

            return $order;
        });
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Order $order, $status, $paymentData = null)
    {
        return DB::transaction(function () use ($order, $status, $paymentData) {
            $oldStatus = $order->payment_status;
            
            $order->update([
                'payment_status' => $status,
                'payment_data' => $paymentData ? array_merge($order->payment_data ?? [], $paymentData) : $order->payment_data
            ]);

            // Create timeline entry
            $this->createTimelineEntry(
                $order, 
                $status, 
                "Payment status changed from {$oldStatus} to {$status}"
            );

            // If payment is completed, send confirmation
            if ($status === 'paid' && $oldStatus !== 'paid') {
                $this->sendPaymentConfirmation($order);
            }

            return $order;
        });
    }

    /**
     * Cancel order
     */
    public function cancelOrder(Order $order, $reason = null)
    {
        return DB::transaction(function () use ($order, $reason) {
            // Restore product and variant stock
            foreach ($order->items as $item) {
                if ($item->variant_id) {
                    // Restore variant stock
                    $variant = ProductVariant::find($item->variant_id);
                    if ($variant) {
                        $this->variantService->updateStock(
                            $variant,
                            $item->quantity,
                            'add'
                        );
                        
                        Log::info('Variant stock restored during order cancellation', [
                            'order_number' => $order->order_number,
                            'variant_id' => $item->variant_id,
                            'quantity_added' => $item->quantity
                        ]);
                    }
                } else {
                    // Restore product stock
                    $this->productService->updateStock(
                        $item->product_id,
                        $item->quantity,
                        'add'
                    );
                }
            }

            $order->update([
                'status' => 'cancelled',
                'admin_notes' => $reason ? ($order->admin_notes . "\nCancelled: " . $reason) : $order->admin_notes
            ]);

            // Create timeline entry
            $this->createTimelineEntry($order, 'cancelled', 'Order cancelled: ' . ($reason ?? 'No reason provided'));

            // Send cancellation notification
            if ($order->user) {
                $order->user->notify(new OrderCancelled($order, $reason));
            } else {
                $this->sendGuestEmail($order, 'cancelled', ['reason' => $reason]);
            }

            return $order;
        });
    }

    /**
     * Process refund
     */
    public function processRefund(Order $order, $amount = null, $reason = null)
    {
        return DB::transaction(function () use ($order, $amount, $reason) {
            $refundAmount = $amount ?? $order->total;

            $order->update([
                'status' => 'refunded',
                'admin_notes' => $reason ? ($order->admin_notes . "\nRefunded: " . $reason) : $order->admin_notes
            ]);

            $this->updatePaymentStatus($order, 'refunded', [
                'refund_amount' => $refundAmount,
                'refund_reason' => $reason,
                'refunded_at' => now()
            ]);

            return $order;
        });
    }

    /**
     * Add tracking information
     */
    public function addTracking(Order $order, $courier, $trackingNumber)
    {
        return DB::transaction(function () use ($order, $courier, $trackingNumber) {
            $order->update([
                'shipping_courier' => $courier,
                'tracking_number' => $trackingNumber,
                'status' => 'shipped'
            ]);

            // Create timeline entry
            $this->createTimelineEntry(
                $order, 
                'shipped', 
                "Order shipped via {$courier}. Tracking: {$trackingNumber}"
            );

            // Send tracking notification
            if ($order->user) {
                $order->user->notify(new OrderShipped($order));
            } else {
                $this->sendGuestEmail($order, 'shipped', [
                    'courier' => $courier,
                    'tracking' => $trackingNumber
                ]);
            }

            return $order;
        });
    }

    /**
     * Generate unique order number
     */
    protected function generateOrderNumber()
    {
        $prefix = config('settings.order_prefix', 'ORD');
        
        do {
            $number = $prefix . '-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }

    /**
     * Create timeline entry
     */
    protected function createTimelineEntry(Order $order, $status, $description)
    {
        Log::info("Order {$order->order_number}: {$description}");
    }

    /**
     * Send order notifications
     */
    // app/Services/Order/OrderService.php

// app/Services/Order/OrderService.php

protected function sendOrderNotifications(Order $order)
{
    try {
        if ($order->user_id) {
            Log::info('User ID found: ' . $order->user_id);
            
            $user = User::find($order->user_id);
            
            if ($user) {
                Log::info('User found in DB', [
                    'user_id' => $user->id,
                    'user_email' => $user->email
                ]);
                
                try {
                    // Send notification
                    $user->notify(new OrderConfirmation($order));
                    Log::info('✅ Notification sent successfully to user: ' . $user->email);
                    
                } catch (\Exception $e) {
                    Log::error('❌ Failed to send notification to user: ' . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    // If notification fails, try sending direct email
                    $this->sendDirectFallbackEmail($order, $user->email);
                }
            } else {
                Log::error('User not found for ID: ' . $order->user_id);
                $this->sendGuestOrderConfirmation($order);
            }
        } else {
            Log::info('No user_id found, sending guest email to: ' . ($order->guest_email ?? $order->billing_email));
            $this->sendGuestOrderConfirmation($order);
        }

        Log::info('✅✅✅ sendOrderNotifications COMPLETED for order: ' . $order->id);

    } catch (\Exception $e) {
        Log::error('💥 CRITICAL ERROR in sendOrderNotifications: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        
        // Final fallback - ultra simple email
        $this->sendUltraSimpleFallbackEmail($order);
    }
}

/**
 * Send direct fallback email without notification system
 */
protected function sendDirectFallbackEmail(Order $order, $email)
{
    try {
        Mail::raw(
            "Your order #{$order->order_number} has been placed successfully.\n\n" .
            "Order Total: ৳" . number_format($order->total, 2) . "\n\n" .
            "Thank you for shopping with " . config('app.name') . "!",
            function ($message) use ($email, $order) {
                $message->to($email, $order->billing_name)
                        ->subject('Order Confirmation - ' . $order->order_number);
            }
        );
        Log::info('Direct fallback email sent to: ' . $email);
    } catch (\Exception $e) {
        Log::error('Direct fallback email failed: ' . $e->getMessage());
    }
}

/**
 * Ultra simple fallback email
 */
protected function sendUltraSimpleFallbackEmail(Order $order)
{
    try {
        $email = $order->guest_email ?? $order->billing_email;
        if ($email) {
            Mail::raw(
                "Thank you for your order! Order #{$order->order_number}",
                function ($message) use ($email, $order) {
                    $message->to($email)
                            ->subject('Order Confirmation');
                }
            );
        }
    } catch (\Exception $e) {
        Log::error('Ultra simple fallback failed: ' . $e->getMessage());
    }
}

    /**
     * Send guest order confirmation email with invoice
     */
    // app/Services/Order/OrderService.php

protected function sendGuestOrderConfirmation(Order $order)
{
    try {
        // Generate signed URLs
        $signedDownloadUrl = URL::temporarySignedRoute(
            'order.invoice.download', 
            now()->addDays(7),
            ['order' => $order->id]
        );
        
        $signedPrintUrl = URL::temporarySignedRoute(
            'order.invoice.print', 
            now()->addDays(7),
            ['order' => $order->id]
        );

        // Load order with details for PDF
        $orderWithDetails = $this->orderRepository->getOrderWithDetails($order->id);
        
        // Generate PDF
        $pdf = Pdf::loadView('admin.orders.invoice', ['order' => $orderWithDetails]);

        $data = [
            'order' => $order,
            'items' => $order->items,
            'subtotal' => $order->subtotal,
            'shipping' => $order->shipping_cost,
            'tax' => $order->tax_amount,
            'total' => $order->total,
            'order_date' => $order->created_at->format('F d, Y'),
            'signedDownloadUrl' => $signedDownloadUrl,
            'signedPrintUrl' => $signedPrintUrl
        ];

        Mail::send('emails.guest-order-confirmation', $data, function ($message) use ($order, $pdf) {
            $message->to($order->guest_email ?? $order->billing_email, $order->billing_name)
                    ->subject('Order Confirmation - ' . $order->order_number)
                    ->attachData($pdf->output(), "invoice-{$order->order_number}.pdf", [
                        'mime' => 'application/pdf',
                    ]);
        });

        Log::info('Guest order confirmation email sent with invoice', [
            'order_id' => $order->id,
            'email' => $order->guest_email ?? $order->billing_email
        ]);

    } catch (\Exception $e) {
        Log::error('Failed to send guest order confirmation: ' . $e->getMessage(), [
            'order_id' => $order->id,
            'trace' => $e->getTraceAsString()
        ]);
        
        // Fallback email without PDF
        try {
            Mail::send('emails.guest-order-confirmation-fallback', ['order' => $order], function ($message) use ($order) {
                $message->to($order->guest_email ?? $order->billing_email, $order->billing_name)
                        ->subject('Order Confirmation - ' . $order->order_number);
            });
        } catch (\Exception $ex) {
            Log::error('Failed to send fallback guest email: ' . $ex->getMessage());
        }
    }
}

    /**
     * Send payment confirmation
     */
    protected function sendPaymentConfirmation(Order $order)
    {
        try {
            if ($order->user) {
                $order->user->notify(new PaymentConfirmation($order));
            } else {
                $data = [
                    'order' => $order,
                    'payment_status' => 'paid',
                    'payment_method' => $order->payment_method
                ];

                Mail::send('emails.guest-payment-confirmation', $data, function ($message) use ($order) {
                    $message->to($order->guest_email ?? $order->billing_email, $order->billing_name)
                            ->subject('Payment Confirmation - ' . $order->order_number);
                });
            }
        } catch (\Exception $e) {
            Log::error('Failed to send payment confirmation: ' . $e->getMessage());
        }
    }

    /**
     * Send guest email for various events
     */
    protected function sendGuestEmail(Order $order, $type, $data = [])
    {
        try {
            $template = 'emails.guest-' . $type;
            $subject = match($type) {
                'status_updated' => 'Order Status Updated - ' . $order->order_number,
                'cancelled' => 'Order Cancelled - ' . $order->order_number,
                'shipped' => 'Order Shipped - ' . $order->order_number,
                default => 'Order Update - ' . $order->order_number
            };

            Mail::send($template, array_merge(['order' => $order], $data), function ($message) use ($order, $subject) {
                $message->to($order->guest_email ?? $order->billing_email, $order->billing_name)
                        ->subject($subject);
            });

        } catch (\Exception $e) {
            Log::error('Failed to send guest email: ' . $e->getMessage());
        }
    }

    /**
     * Get order statistics
     */
    public function getOrderStatistics($startDate = null, $endDate = null)
    {
        $query = Order::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return [
            'total_orders' => $query->count(),
            'total_revenue' => $query->sum('total'),
            'average_order_value' => $query->avg('total'),
            'orders_by_status' => $query->groupBy('status')
                ->select('status', DB::raw('count(*) as count'))
                ->pluck('count', 'status')
                ->toArray(),
            'payment_methods' => $query->groupBy('payment_method')
                ->select('payment_method', DB::raw('count(*) as count'))
                ->pluck('count', 'payment_method')
                ->toArray(),
        ];
    }

    /**
     * Get customer orders
     */
    public function getCustomerOrders($userId, $limit = 10)
    {
        return Order::where('user_id', $userId)
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get order by ID with relations
     */
    public function getOrderWithDetails($orderId)
    {
        return Order::with(['items.product', 'items.variant', 'user'])
            ->findOrFail($orderId);
    }

    /**
     * Get order by order number
     */
    public function getOrderByNumber($orderNumber)
    {
        return Order::with(['items.product', 'items.variant', 'user'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();
    }

    /**
     * Check if order can be cancelled
     */
    public function canCancelOrder(Order $order)
    {
        return in_array($order->status, ['pending', 'processing']) 
            && $order->payment_status !== 'paid';
    }

    /**
     * Check if order can be modified
     */
    public function canModifyOrder(Order $order)
    {
        return in_array($order->status, ['pending', 'draft']);
    }

    /**
     * Update order notes
     */
    public function updateNotes(Order $order, $notes, $isAdmin = false)
    {
        if ($isAdmin) {
            $order->update(['admin_notes' => $notes]);
        } else {
            $order->update(['notes' => $notes]);
        }
        
        return $order;
    }

    /**
     * Get orders by status
     */
    public function getOrdersByStatus($status, $perPage = 15)
    {
        return Order::with(['user', 'items'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Search orders
     */
    public function searchOrders($keyword, $perPage = 15)
    {
        return Order::with(['user', 'items'])
            ->where('order_number', 'LIKE', "%{$keyword}%")
            ->orWhere('shipping_name', 'LIKE', "%{$keyword}%")
            ->orWhere('shipping_email', 'LIKE', "%{$keyword}%")
            ->orWhere('shipping_phone', 'LIKE', "%{$keyword}%")
            ->orWhereHas('user', function($query) use ($keyword) {
                $query->where('name', 'LIKE', "%{$keyword}%")
                      ->orWhere('email', 'LIKE', "%{$keyword}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get recent orders
     */
    public function getRecentOrders($limit = 10)
    {
        return Order::with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get monthly sales report
     */
    public function getMonthlySalesReport($year = null)
    {
        $year = $year ?? date('Y');
        
        $sales = Order::whereYear('created_at', $year)
            ->where('status', '!=', 'cancelled')
            ->where('payment_status', 'paid')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total) as total_revenue'),
                DB::raw('AVG(total) as average_order_value')
            )
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->get();

        return $sales;
    }

    /**
     * Export orders
     */
    public function exportOrders($filters = [])
    {
        $query = Order::with(['user', 'items'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        return $query->get();
    }

    /**
     * Bulk update order status
     */
    public function bulkUpdateStatus(array $orderIds, $status)
    {
        return DB::transaction(function () use ($orderIds, $status) {
            $orders = Order::whereIn('id', $orderIds)->get();
            
            foreach ($orders as $order) {
                $this->updateOrderStatus($order, $status);
            }
            
            return true;
        });
    }

    /**
     * Get order timeline
     */
    public function getOrderTimeline(Order $order)
    {
        return [
            [
                'status' => $order->status,
                'description' => 'Current status',
                'created_at' => $order->updated_at
            ],
            [
                'status' => 'created',
                'description' => 'Order placed',
                'created_at' => $order->created_at
            ]
        ];
    }
}