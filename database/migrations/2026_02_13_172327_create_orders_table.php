<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_email')->nullable();
            $table->string('guest_phone')->nullable();
            
            // Billing Address
            $table->string('billing_name');
            $table->string('billing_email');
            $table->string('billing_phone');
            $table->text('billing_address');
            $table->string('billing_city');
            $table->string('billing_state')->nullable();
            $table->string('billing_zip')->nullable();
            $table->string('billing_country')->default('Bangladesh');
            
            // Shipping Address
            $table->string('shipping_name');
            $table->string('shipping_email');
            $table->string('shipping_phone');
            $table->text('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_state')->nullable();
            $table->string('shipping_zip')->nullable();
            $table->string('shipping_country')->default('Bangladesh');
            
            // Order Summary
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->string('coupon_code')->nullable();
            $table->decimal('coupon_discount', 10, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            
            // Payment
            $table->string('payment_method');
            $table->string('payment_status')->default('pending');
            $table->string('payment_id')->nullable();
            
            // Shipping
            $table->string('shipping_method')->nullable();
            $table->string('shipping_courier')->nullable();
            $table->string('tracking_number')->nullable();
            
            // Status
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            
            // Invoice
            $table->string('invoice_number')->nullable();
            $table->timestamp('invoice_date')->nullable();
            $table->timestamp('delivery_date')->nullable();
            
            $table->softDeletes();
            $table->timestamps();

            $table->index(['order_number', 'user_id', 'status']);
            $table->index(['payment_status', 'payment_method']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};