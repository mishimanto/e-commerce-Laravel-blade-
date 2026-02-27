<?php
// database/migrations/2024_01_01_000002_create_payment_methods_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentMethodsTable extends Migration
{
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->json('instructions')->nullable(); // Payment instructions
            
            // Payment type
            $table->enum('type', ['online', 'offline', 'cash'])->default('offline');
            
            // Configuration (for online payments)
            $table->json('config')->nullable();
            
            // Fees
            $table->decimal('fixed_fee', 10, 2)->default(0);
            $table->decimal('percentage_fee', 5, 2)->default(0);
            $table->decimal('minimum_fee', 10, 2)->nullable();
            $table->decimal('maximum_fee', 10, 2)->nullable();
            
            // Status and sorting
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            
            // Restrictions
            $table->decimal('minimum_order_amount', 10, 2)->nullable();
            $table->decimal('maximum_order_amount', 10, 2)->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('is_active');
            $table->index('sort_order');
            $table->index('type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_methods');
    }
}