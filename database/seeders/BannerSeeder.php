<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run()
    {
        $banners = [
            // Homepage Hero Banners
            [
                'title' => 'iPhone 15 Pro Max',
                'subtitle' => 'The ultimate iPhone',
                'description' => 'Titanium design. A17 Pro chip. Action button. 48MP camera.',
                'image' => 'banners/iphone-17-pro-max-hero.jpg',
                'mobile_image' => 'banners/mobile/iphone-17-pro-max-hero.jpg',
                'link' => '/products/iphone-17-pro-max',
                'button_text' => 'Pre-order Now',
                'position' => 'home_hero',
                'type' => 'image',
                'target' => '_self',
                'priority' => 100,
                'is_active' => true,
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(20),
            ],
            [
                'title' => 'Samsung Galaxy S24 Ultra',
                'subtitle' => 'Galaxy AI is here',
                'description' => 'The first Galaxy AI phone. Live translate, circle to search, and more.',
                'image' => 'banners/samsung-s24-ultra.jpg',
                'mobile_image' => 'banners/mobile/samsung-s24-ultra.jpg',
                'link' => '/products/samsung-galaxy-s24-ultra',
                'button_text' => 'Learn More',
                'position' => 'home_hero',
                'type' => 'image',
                'target' => '_self',
                'priority' => 90,
                'is_active' => true,
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(25),
            ],
            [
                'title' => 'Summer Sale',
                'subtitle' => 'Up to 40% off',
                'description' => 'Limited time offer on selected gadgets and accessories.',
                'image' => 'banners/summer-sale.jpg',
                'mobile_image' => 'banners/mobile/summer-sale.jpg',
                'link' => '/sale',
                'button_text' => 'Shop Now',
                'position' => 'home_hero',
                'type' => 'countdown',
                'target' => '_self',
                'priority' => 80,
                'is_active' => true,
                'start_date' => now(),
                'end_date' => now()->addDays(15),
                'settings' => json_encode([
                    'countdown_end' => now()->addDays(15)->format('Y-m-d H:i:s'),
                    'theme' => 'dark'
                ]),
            ],
            
            // Homepage Sidebar Banners
            [
                'title' => 'Wireless Earbuds',
                'subtitle' => 'Starting at ৳2,499',
                'image' => 'banners/wireless-earbuds.jpg',
                'link' => '/category/audio',
                'button_text' => 'Shop Audio',
                'position' => 'home_sidebar',
                'type' => 'image',
                'target' => '_self',
                'priority' => 50,
                'is_active' => true,
            ],
            [
                'title' => 'Smart Watches',
                'subtitle' => 'Track your fitness',
                'image' => 'banners/smart-watches.jpg',
                'link' => '/category/wearables',
                'button_text' => 'Explore',
                'position' => 'home_sidebar',
                'type' => 'image',
                'target' => '_self',
                'priority' => 40,
                'is_active' => true,
            ],
            
            // Homepage Bottom Banners
            [
                'title' => 'Free Shipping',
                'subtitle' => 'On orders over ৳5,000',
                'image' => 'banners/free-shipping.jpg',
                'position' => 'home_bottom',
                'type' => 'image',
                'priority' => 30,
                'is_active' => true,
            ],
            [
                'title' => '1 Year Warranty',
                'subtitle' => 'On all products',
                'image' => 'banners/warranty.jpg',
                'position' => 'home_bottom',
                'type' => 'image',
                'priority' => 20,
                'is_active' => true,
            ],
            [
                'title' => 'Easy Returns',
                'subtitle' => '7 days return policy',
                'image' => 'banners/returns.jpg',
                'position' => 'home_bottom',
                'type' => 'image',
                'priority' => 10,
                'is_active' => true,
            ],
            
            // Category Top Banners
            [
                'title' => 'Smartphones',
                'subtitle' => 'Latest flagships and budget phones',
                'image' => 'banners/category-smartphones.jpg',
                'link' => '/category/smartphones',
                'position' => 'category_top',
                'type' => 'image',
                'target' => '_self',
                'priority' => 50,
                'is_active' => true,
            ],
            [
                'title' => 'Gaming Laptops',
                'subtitle' => 'RTX 40 series available',
                'image' => 'banners/gaming-laptops.jpg',
                'link' => '/category/laptops',
                'position' => 'category_top',
                'type' => 'image',
                'target' => '_self',
                'priority' => 40,
                'is_active' => true,
            ],
            
            // Product Details Banners
            [
                'title' => 'Genuine Products',
                'subtitle' => '100% authentic',
                'image' => 'banners/genuine-products.jpg',
                'position' => 'product_details',
                'type' => 'image',
                'priority' => 30,
                'is_active' => true,
            ],
            [
                'title' => 'EMI Available',
                'subtitle' => '0% interest on selected cards',
                'image' => 'banners/emi.jpg',
                'position' => 'product_details',
                'type' => 'image',
                'priority' => 20,
                'is_active' => true,
            ],
            
            // Cart Page Banner
            [
                'title' => 'Add ৳500 more for free shipping!',
                'image' => 'banners/free-shipping-cart.jpg',
                'position' => 'cart_page',
                'type' => 'custom_html',
                'priority' => 10,
                'is_active' => true,
                'settings' => json_encode([
                    'html' => '<div class="bg-blue-50 p-4 rounded-lg"><p class="text-blue-800">🎉 Add ৳500 more to your cart to get <strong>FREE SHIPPING</strong>!</p></div>'
                ]),
            ],
            
            // Popup Modal
            [
                'title' => 'Get 10% Off',
                'subtitle' => 'Sign up for newsletter',
                'description' => 'Subscribe to get exclusive offers and 10% off your first order!',
                'image' => 'banners/newsletter-popup.jpg',
                'button_text' => 'Subscribe Now',
                'position' => 'popup',
                'type' => 'image',
                'target' => '_self',
                'priority' => 100,
                'is_active' => true,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'settings' => json_encode([
                    'delay' => 5000,
                    'frequency' => 'once_per_session',
                    'show_on_exit' => true
                ]),
            ],
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }

        // Generate additional random banners
        $faker = \Faker\Factory::create();
        $positions = array_keys(Banner::POSITIONS);
        
        for ($i = 0; $i < 15; $i++) {
            Banner::create([
                'title' => $faker->words(3, true),
                'subtitle' => $faker->optional(0.7)->words(3, true),
                'description' => $faker->optional(0.5)->sentence,
                'image' => 'banners/placeholder-' . $i . '.jpg',
                'link' => $faker->optional(0.6)->url,
                'button_text' => $faker->optional(0.6)->words(2, true),
                'position' => $faker->randomElement($positions),
                'type' => $faker->randomElement(['image', 'video', 'carousel', 'countdown']),
                'target' => $faker->randomElement(['_self', '_blank']),
                'priority' => $faker->numberBetween(0, 100),
                'is_active' => $faker->boolean(70),
                'start_date' => $faker->optional(0.6)->dateTimeBetween('-30 days', 'now'),
                'end_date' => $faker->optional(0.6)->dateTimeBetween('now', '+60 days'),
            ]);
        }
    }
}