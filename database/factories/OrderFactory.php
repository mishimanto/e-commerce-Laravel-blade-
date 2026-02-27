<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        $subtotal = $this->faker->numberBetween(1000, 50000);
        $shippingCost = $this->faker->numberBetween(50, 500);
        $taxRate = 15; // 15% tax
        $taxAmount = $subtotal * $taxRate / 100;
        $discountAmount = $this->faker->boolean(30) ? $this->faker->numberBetween(100, 1000) : 0;
        $total = $subtotal + $shippingCost + $taxAmount - $discountAmount;

        $status = $this->faker->randomElement([
            'pending', 'processing', 'confirmed', 'shipped', 'delivered', 'cancelled'
        ]);
        
        $paymentStatus = match($status) {
            'delivered', 'completed' => 'paid',
            'cancelled' => $this->faker->randomElement(['refunded', 'cancelled']),
            default => $this->faker->randomElement(['pending', 'paid', 'failed']),
        };

        return [
            'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            
            // Billing Address
            'billing_name' => $this->faker->name,
            'billing_email' => $this->faker->email,
            'billing_phone' => $this->faker->phoneNumber,
            'billing_address' => $this->faker->streetAddress,
            'billing_city' => $this->faker->city,
            'billing_state' => $this->faker->state,
            'billing_zip' => $this->faker->postcode,
            'billing_country' => 'Bangladesh',
            
            // Shipping Address
            'shipping_name' => $this->faker->name,
            'shipping_email' => $this->faker->email,
            'shipping_phone' => $this->faker->phoneNumber,
            'shipping_address' => $this->faker->streetAddress,
            'shipping_city' => $this->faker->city,
            'shipping_state' => $this->faker->state,
            'shipping_zip' => $this->faker->postcode,
            'shipping_country' => 'Bangladesh',
            
            // Order Summary
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'coupon_code' => $discountAmount > 0 ? Coupon::inRandomOrder()->first()->code ?? null : null,
            'coupon_discount' => $discountAmount,
            'shipping_cost' => $shippingCost,
            'tax_amount' => $taxAmount,
            'total' => $total,
            
            // Payment
            'payment_method' => $this->faker->randomElement([
                'cash_on_delivery', 'sslcommerz', 'stripe', 'bkash', 'nagad'
            ]),
            'payment_status' => $paymentStatus,
            'payment_id' => $this->faker->uuid,
            
            // Shipping
            'shipping_method' => $this->faker->randomElement(['standard', 'express']),
            'shipping_courier' => $this->faker->randomElement(['pathao', 'steadfast', 'redx', 'sundarban']),
            'tracking_number' => $this->faker->bothify('TRK-########'),
            
            // Status
            'status' => $status,
            'notes' => $this->faker->boolean(30) ? $this->faker->sentence : null,
            'admin_notes' => $this->faker->boolean(20) ? $this->faker->paragraph : null,
            
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            // Create order items
            $itemCount = $this->faker->numberBetween(1, 5);
            
            for ($i = 0; $i < $itemCount; $i++) {
                $product = \App\Models\Product::inRandomOrder()->first() ?? \App\Models\Product::factory();
                $quantity = $this->faker->numberBetween(1, 3);
                $price = $product->sale_price ?? $product->base_price;
                
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $price * $quantity,
                    'attributes' => $this->faker->boolean(50) ? json_encode([
                        'color' => $this->faker->colorName,
                        'storage' => $this->faker->randomElement(['64GB', '128GB', '256GB']),
                    ]) : null,
                ]);
            }

            // Create payment record
            if ($order->payment_status === 'paid') {
                \App\Models\Payment::factory()->create([
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'payment_method' => $order->payment_method,
                    'amount' => $order->total,
                    'status' => 'completed',
                    'paid_at' => $order->updated_at,
                ]);
            }
        });
    }

    /**
     * Indicate that the order is pending.
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
                'payment_status' => 'pending',
            ];
        });
    }

    /**
     * Indicate that the order is processing.
     */
    public function processing()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'processing',
                'payment_status' => 'paid',
            ];
        });
    }

    /**
     * Indicate that the order is confirmed.
     */
    public function confirmed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'confirmed',
                'payment_status' => 'paid',
            ];
        });
    }

    /**
     * Indicate that the order is shipped.
     */
    public function shipped()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'shipped',
                'payment_status' => 'paid',
                'tracking_number' => $this->faker->bothify('TRK-########'),
            ];
        });
    }

    /**
     * Indicate that the order is delivered.
     */
    public function delivered()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'delivered',
                'payment_status' => 'paid',
                'delivery_date' => now(),
            ];
        });
    }

    /**
     * Indicate that the order is cancelled.
     */
    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled',
                'payment_status' => $this->faker->randomElement(['refunded', 'cancelled']),
            ];
        });
    }

    /**
     * Indicate that the order is paid.
     */
    public function paid()
    {
        return $this->state(function (array $attributes) {
            return [
                'payment_status' => 'paid',
            ];
        });
    }

    /**
     * Indicate that the order is for a guest.
     */
    public function guest()
    {
        return $this->state(function (array $attributes) {
            return [
                'user_id' => null,
                'guest_email' => $this->faker->email,
                'guest_phone' => $this->faker->phoneNumber,
            ];
        });
    }

    /**
     * Indicate that the order uses a coupon.
     */
    public function withCoupon()
    {
        return $this->state(function (array $attributes) {
            $coupon = Coupon::inRandomOrder()->first() ?? Coupon::factory();
            $subtotal = $attributes['subtotal'] ?? $this->faker->numberBetween(1000, 50000);
            
            $discount = match($coupon->type) {
                'fixed' => min($coupon->value, $coupon->max_discount_amount ?? $coupon->value),
                'percentage' => min($subtotal * $coupon->value / 100, $coupon->max_discount_amount ?? $subtotal * $coupon->value / 100),
                default => 0,
            };
            
            return [
                'coupon_code' => $coupon->code,
                'coupon_discount' => $discount,
                'discount_amount' => $discount,
            ];
        });
    }
}