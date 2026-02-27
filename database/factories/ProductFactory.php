<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        $name = $this->faker->unique()->words(3, true);
        $basePrice = $this->faker->numberBetween(1000, 150000);
        $hasSale = $this->faker->boolean(30);
        
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'sku' => 'PRD-' . strtoupper($this->faker->unique()->bothify('??####')),
            'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
            'brand_id' => Brand::inRandomOrder()->first()->id ?? Brand::factory(),
            'description' => $this->faker->paragraphs(5, true),
            'short_description' => $this->faker->paragraph,
            'specifications' => json_encode([
                'Display' => $this->faker->randomElement(['6.1"', '6.7"', '6.8"', '6.4"']),
                'Processor' => $this->faker->randomElement(['A16 Bionic', 'Snapdragon 8 Gen 2', 'Dimensity 9000']),
                'RAM' => $this->faker->randomElement(['4GB', '6GB', '8GB', '12GB', '16GB']),
                'Storage' => $this->faker->randomElement(['64GB', '128GB', '256GB', '512GB', '1TB']),
                'Camera' => $this->faker->randomElement(['48MP', '50MP', '108MP', '200MP']),
                'Battery' => $this->faker->randomElement(['4000mAh', '4500mAh', '5000mAh', '6000mAh']),
            ]),
            'base_price' => $basePrice,
            'sale_price' => $hasSale ? $basePrice * $this->faker->numberBetween(70, 95) / 100 : null,
            'stock' => $this->faker->numberBetween(0, 100),
            'status' => $this->faker->randomElement(['active', 'inactive', 'draft']),
            'is_featured' => $this->faker->boolean(20),
            'is_trending' => $this->faker->boolean(15),
            'meta_title' => $name,
            'meta_description' => $this->faker->sentence,
            'meta_keywords' => implode(',', $this->faker->words(5)),
            'warranty' => $this->faker->randomElement(['6 months', '1 year', '2 years']),
            'tags' => json_encode($this->faker->words(8)),
            'views' => $this->faker->numberBetween(0, 10000),
            'sold_count' => $this->faker->numberBetween(0, 500),
            'weight' => $this->faker->randomFloat(2, 0.1, 5),
            'dimensions' => json_encode([
                'length' => $this->faker->numberBetween(10, 30),
                'width' => $this->faker->numberBetween(5, 20),
                'height' => $this->faker->numberBetween(0.5, 5),
            ]),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure()
    {
        return $this->afterCreating(function (Product $product) {
            // Create product images
            \App\Models\ProductImage::factory()
                ->count($this->faker->numberBetween(3, 7))
                ->create([
                    'product_id' => $product->id,
                ]);

            // Create product variants
            if ($this->faker->boolean(70)) {
                \App\Models\ProductVariant::factory()
                    ->count($this->faker->numberBetween(2, 5))
                    ->create([
                        'product_id' => $product->id,
                    ]);
            }

            // Create reviews
            if ($this->faker->boolean(60)) {
                \App\Models\Review::factory()
                    ->count($this->faker->numberBetween(1, 20))
                    ->create([
                        'product_id' => $product->id,
                    ]);
            }
        });
    }

    /**
     * Indicate that the product is active.
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
            ];
        });
    }

    /**
     * Indicate that the product is inactive.
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'inactive',
            ];
        });
    }

    /**
     * Indicate that the product is featured.
     */
    public function featured()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_featured' => true,
            ];
        });
    }

    /**
     * Indicate that the product is trending.
     */
    public function trending()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_trending' => true,
            ];
        });
    }

    /**
     * Indicate that the product is on sale.
     */
    public function onSale()
    {
        return $this->state(function (array $attributes) {
            $basePrice = $attributes['base_price'] ?? $this->faker->numberBetween(1000, 150000);
            
            return [
                'sale_price' => $basePrice * $this->faker->numberBetween(70, 90) / 100,
            ];
        });
    }

    /**
     * Indicate that the product is in stock.
     */
    public function inStock()
    {
        return $this->state(function (array $attributes) {
            return [
                'stock' => $this->faker->numberBetween(10, 100),
            ];
        });
    }

    /**
     * Indicate that the product is low stock.
     */
    public function lowStock()
    {
        return $this->state(function (array $attributes) {
            return [
                'stock' => $this->faker->numberBetween(1, 5),
            ];
        });
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock()
    {
        return $this->state(function (array $attributes) {
            return [
                'stock' => 0,
            ];
        });
    }
}