<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'product_id', 'variant_id'], 'wishlist_user_product_unique');
            $table->unique(['session_id', 'product_id', 'variant_id'], 'wishlist_session_product_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('wishlists');
    }
};