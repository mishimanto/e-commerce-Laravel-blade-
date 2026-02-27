<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('categories', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('status');
            }
            
            if (!Schema::hasColumn('categories', 'featured_order')) {
                $table->integer('featured_order')->default(0)->after('is_featured');
            }
            
            if (!Schema::hasColumn('categories', 'icon')) {
                $table->string('icon')->nullable()->after('image');
            }
            
            if (!Schema::hasColumn('categories', 'banner_image')) {
                $table->string('banner_image')->nullable()->after('icon');
            }
            
            if (!Schema::hasColumn('categories', 'show_in_menu')) {
                $table->boolean('show_in_menu')->default(true)->after('featured_order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'is_featured',
                'featured_order',
                'icon',
                'banner_image',
                'show_in_menu'
            ]);
        });
    }
};