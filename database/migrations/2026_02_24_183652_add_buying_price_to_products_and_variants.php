<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBuyingPriceToProductsAndVariants extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('buying_price', 10, 2)->nullable()->after('sale_price');
        });
        
        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('buying_price', 10, 2)->nullable()->after('price_adjustment');
        });
    }
    
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('buying_price');
        });
        
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('buying_price');
        });
    }
}