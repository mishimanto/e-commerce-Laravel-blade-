<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        // Product specifications templates for gadgets
        $specsTemplates = [
            'iPhone 17 Pro Max' => [
                'Display' => '6.9-inch Super Retina XDR',
                'Processor' => 'A16 Bionic',
                'RAM' => '6GB',
                'Storage' => ['128GB', '256GB', '512GB'],
                'Camera' => '48MP + 12MP + 12MP',
                'Battery' => '3200mAh',
                'OS' => 'iOS 16'
            ],
            'iPhone 17 Pro' => [
                'Display' => '6.3-inch Super Retina XDR',
                'Processor' => 'A16 Bionic',
                'RAM' => '6GB',
                'Storage' => ['128GB', '256GB', '512GB'],
                'Camera' => '48MP + 12MP + 12MP',
                'Battery' => '2800mAh',
                'OS' => 'iOS 16'
            ],
            'Samsung S24 Ultra' => [
                'Display' => '6.8-inch Dynamic AMOLED 2X',
                'Processor' => 'Snapdragon 8 Gen 2',
                'RAM' => ['8GB', '12GB'],
                'Storage' => ['256GB', '512GB', '1TB'],
                'Camera' => '200MP + 12MP + 10MP + 10MP',
                'Battery' => '5000mAh',
                'OS' => 'Android 13'
            ],
            'Samsung S24' => [
                'Display' => '6.2-inch Dynamic AMOLED 2X',
                'Processor' => 'Snapdragon 8 Gen 2',
                'RAM' => ['8GB', '12GB'],
                'Storage' => ['256GB', '512GB', '1TB'],
                'Camera' => '200MP + 12MP + 10MP + 10MP',
                'Battery' => '5000mAh',
                'OS' => 'Android 13'
            ],

            'Xiaomi 13 Pro' => [
                'Display' => '6.73-inch AMOLED',
                'Processor' => 'Snapdragon 8 Gen 2',
                'RAM' => ['8GB', '12GB'],
                'Storage' => ['256GB', '512GB'],
                'Camera' => '50MP + 50MP + 50MP',
                'Battery' => '4820mAh',
                'OS' => 'Android 13'
            ],   

            'Xiaomi 13' => [
                    'Display' => '6.36-inch AMOLED',
                    'Processor' => 'Snapdragon 8 Gen 2',
                    'RAM' => ['8GB', '12GB'],
                    'Storage' => ['128GB', '256GB'],
                    'Camera' => '50MP + 12MP + 10MP',
                    'Battery' => '4500mAh',
                    'OS' => 'Android 13'
            ],       

            'OnePlus 11 Pro' => [
                'Display' => '6.7-inch AMOLED',
                'Processor' => 'Snapdragon 8 Gen 2',
                'RAM' => ['8GB', '12GB'],
                'Storage' => ['256GB', '512GB'],
                'Camera' => '50MP + 48MP + 32MP',   
                'Battery' => '5000mAh',
                'OS' => 'Android 13'
            ],
            'OnePlus 11' => [
                'Display' => '6.1-inch AMOLED',
                'Processor' => 'Snapdragon 8 Gen 2',
                'RAM' => ['8GB', '12GB'],
                'Storage' => ['128GB', '256GB'],            
                'Camera' => '50MP + 48MP + 32MP',
                'Battery' => '5000mAh',
                'OS' => 'Android 13'
            ],
            'Google Pixel 7 Pro' => [
                'Display' => '6.7-inch AMOLED',
                'Processor' => 'Snapdragon 8 Gen 2',
                'RAM' => ['12GB', '16GB'],
                'Storage' =>    ['128GB', '256GB', '512GB'],
                'Camera' => '50MP + 48MP + 12MP',   
                'Battery' => '5000mAh',
                'OS' => 'Android 13'
            ],
            'Google Pixel 7' => [
                'Display' => '6.1-inch AMOLED',
                'Processor' => 'Snapdragon 8 Gen 2',
                'RAM' => ['8GB', '12GB'],
                'Storage' => ['128GB', '256GB'],
                'Camera' => '50MP + 12MP',
                'Battery' => '5000mAh',
                'OS' => 'Android 13'
            ],
            
        ];

        // Colors for variants
        $colors = ['Black', 'White', 'Blue', 'Red', 'Purple', 'Gold', 'Silver', 'Graphite'];
        $storages = ['64GB', '128GB', '256GB', '512GB', '1TB'];
        $rams = ['4GB', '6GB', '8GB', '12GB', '16GB'];

        // Generate 500+ products
        for ($i = 1; $i <= 500; $i++) {
            $category = Category::inRandomOrder()->first();
            $brand = Brand::inRandomOrder()->first();
            
            // Generate product name
            $productName = $brand->name . ' ' . $faker->words(3, true) . ' ' . $faker->year;
            
            // Random price between 10000 and 150000 BDT
            $basePrice = $faker->numberBetween(10000, 150000);
            $hasSale = $faker->boolean(30); // 30% chance of having sale price
            $salePrice = $hasSale ? $basePrice * $faker->numberBetween(70, 95) / 100 : null;

            $product = Product::create([
                'name' => $productName,
                'slug' => \Str::slug($productName),
                'sku' => 'PRD-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'description' => $faker->paragraphs(5, true),
                'short_description' => $faker->paragraph,
                'specifications' => json_encode([
                    'Display' => $faker->randomElement(['6.1"', '6.7"', '6.8"', '6.4"']),
                    'Processor' => $faker->randomElement(['A16', 'Snapdragon 8 Gen 2', 'Dimensity 9000']),
                    'RAM' => $faker->randomElement($rams),
                    'Storage' => $faker->randomElement($storages),
                    'Camera' => $faker->randomElement(['48MP', '50MP', '108MP', '200MP']),
                    'Battery' => $faker->randomElement(['4000mAh', '4500mAh', '5000mAh', '6000mAh']),
                ]),
                'base_price' => $basePrice,
                'sale_price' => $salePrice,
                'stock' => $faker->numberBetween(0, 100),
                'status' => $faker->randomElement(['active', 'inactive', 'draft']),
                'is_featured' => $faker->boolean(20),
                'is_trending' => $faker->boolean(15),
                'meta_title' => $productName,
                'meta_description' => $faker->sentence,
                'meta_keywords' => implode(',', $faker->words(5)),
                'warranty' => $faker->randomElement(['6 months', '1 year', '2 years']),
                'tags' => json_encode($faker->words(8)),
                'views' => $faker->numberBetween(100, 10000),
            ]);

            // Create 2-5 variants for each product
            $variantCount = $faker->numberBetween(2, 5);
            for ($j = 1; $j <= $variantCount; $j++) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $product->sku . '-VAR-' . $j,
                    'attributes' => json_encode([
                        'color' => $faker->randomElement($colors),
                        'storage' => $faker->randomElement($storages),
                        'ram' => $faker->randomElement($rams),
                    ]),
                    'price_adjustment' => $faker->numberBetween(-2000, 5000),
                    'stock' => $faker->numberBetween(0, 50),
                    'image' => null,
                    'status' => $faker->randomElement(['active', 'inactive']),
                ]);
            }

            // Create 3-7 images for each product
            $imageCount = $faker->numberBetween(3, 7);
            for ($j = 1; $j <= $imageCount; $j++) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'url' => "https://via.placeholder.com/600x600?text=Product+{$i}+Image+{$j}",
                    'alt_text' => $product->name . ' Image ' . $j,
                    'is_primary' => $j === 1,
                    'sort_order' => $j,
                ]);
            }
        }
    }
}