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
        Schema::table('brands', function (Blueprint $table) {
            if (!Schema::hasColumn('brands', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('status');
            }
            
            if (!Schema::hasColumn('brands', 'featured_order')) {
                $table->integer('featured_order')->default(0)->after('is_featured');
            }
            
            if (!Schema::hasColumn('brands', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('featured_order');
            }
            
            if (!Schema::hasColumn('brands', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            
            if (!Schema::hasColumn('brands', 'meta_keywords')) {
                $table->text('meta_keywords')->nullable()->after('meta_description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn([
                'is_featured',
                'featured_order',
                'meta_title',
                'meta_description',
                'meta_keywords'
            ]);
        });
    }
};