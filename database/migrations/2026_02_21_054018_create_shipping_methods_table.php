<?php
// database/migrations/2024_01_01_000001_create_shipping_methods_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingMethodsTable extends Migration
{
    public function up()
    {
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->string('delivery_time')->nullable();
            $table->decimal('minimum_order_amount', 10, 2)->nullable();
            $table->decimal('maximum_order_amount', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            
            // For free shipping threshold
            $table->boolean('is_free_shipping')->default(false);
            $table->decimal('free_shipping_threshold', 10, 2)->nullable();
            
            // For location-based restrictions
            $table->json('available_countries')->nullable();
            $table->json('available_cities')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipping_methods');
    }
}