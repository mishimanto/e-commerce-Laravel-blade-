<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('brand_id')->constrained();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->json('specifications')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->integer('stock')->nullable();
            $table->string('status')->default('active');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_trending')->default(false);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('warranty')->nullable();
            $table->json('tags')->nullable();
            $table->integer('views')->default(0);
            $table->integer('sold_count')->default(0);
            $table->decimal('weight', 8, 2)->nullable();
            $table->json('dimensions')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'is_featured', 'is_trending']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};