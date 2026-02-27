<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            
            $table->integer('rating');
            $table->string('title')->nullable();
            $table->text('comment');
            $table->json('pros')->nullable();
            $table->json('cons')->nullable();
            $table->json('images')->nullable();
            $table->boolean('verified_purchase')->default(false);
            $table->boolean('status')->default(true);
            
            $table->timestamps();

            $table->unique(['user_id', 'product_id', 'order_id'], 'review_unique');
            $table->index(['product_id', 'rating', 'status']);
            $table->index('created_at');
        });

        Schema::create('recently_viewed', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->timestamp('viewed_at');

            $table->index(['user_id', 'viewed_at']);
            $table->index(['session_id', 'viewed_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('recently_viewed');
        Schema::dropIfExists('reviews');
    }
};