<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Main Categories
            [
                'name' => 'Smartphones',
                'slug' => 'smartphones',
                'description' => 'Latest smartphones from top brands',
                'icon' => 'fas fa-mobile-alt',
                'is_featured' => true,
                'status' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Tablets',
                'slug' => 'tablets',
                'description' => 'Powerful tablets for work and entertainment',
                'icon' => 'fas fa-tablet-alt',
                'is_featured' => true,
                'status' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Laptops',
                'slug' => 'laptops',
                'description' => 'High-performance laptops for every need',
                'icon' => 'fas fa-laptop',
                'is_featured' => true,
                'status' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Audio',
                'slug' => 'audio',
                'description' => 'Headphones, earphones, and speakers',
                'icon' => 'fas fa-headphones',
                'is_featured' => true,
                'status' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Wearables',
                'slug' => 'wearables',
                'description' => 'Smart watches and fitness trackers',
                'icon' => 'fas fa-clock',
                'is_featured' => true,
                'status' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'description' => 'Cases, chargers, and more',
                'icon' => 'fas fa-plug',
                'is_featured' => true,
                'status' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Cameras',
                'slug' => 'cameras',
                'description' => 'Digital cameras and accessories',
                'icon' => 'fas fa-camera',
                'is_featured' => false,
                'status' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Gaming',
                'slug' => 'gaming',
                'description' => 'Gaming consoles and accessories',
                'icon' => 'fas fa-gamepad',
                'is_featured' => false,
                'status' => true,
                'sort_order' => 8,
            ],
        ];

        // Subcategories for Smartphones
        $smartphoneSubs = [
            [
                'name' => 'Android Phones',
                'slug' => 'android-phones',
                'description' => 'Samsung, Google, OnePlus and more',
                'parent_id' => 1,
                'status' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'iPhones',
                'slug' => 'iphones',
                'description' => 'Apple iPhone series',
                'parent_id' => 1,
                'status' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Budget Phones',
                'slug' => 'budget-phones',
                'description' => 'Affordable smartphones under ৳20,000',
                'parent_id' => 1,
                'status' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Flagship Phones',
                'slug' => 'flagship-phones',
                'description' => 'Premium high-end smartphones',
                'parent_id' => 1,
                'status' => true,
                'sort_order' => 4,
            ],
        ];

        // Subcategories for Laptops
        $laptopSubs = [
            [
                'name' => 'Gaming Laptops',
                'slug' => 'gaming-laptops',
                'description' => 'High-performance gaming laptops',
                'parent_id' => 3,
                'status' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Ultrabooks',
                'slug' => 'ultrabooks',
                'description' => 'Thin and light laptops',
                'parent_id' => 3,
                'status' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'MacBooks',
                'slug' => 'macbooks',
                'description' => 'Apple MacBook series',
                'parent_id' => 3,
                'status' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Business Laptops',
                'slug' => 'business-laptops',
                'description' => 'Laptops for professionals',
                'parent_id' => 3,
                'status' => true,
                'sort_order' => 4,
            ],
        ];

        // Subcategories for Audio
        $audioSubs = [
            [
                'name' => 'Wireless Earbuds',
                'slug' => 'wireless-earbuds',
                'description' => 'True wireless earbuds',
                'parent_id' => 4,
                'status' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Headphones',
                'slug' => 'headphones',
                'description' => 'Over-ear and on-ear headphones',
                'parent_id' => 4,
                'status' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Bluetooth Speakers',
                'slug' => 'bluetooth-speakers',
                'description' => 'Portable wireless speakers',
                'parent_id' => 4,
                'status' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Soundbars',
                'slug' => 'soundbars',
                'description' => 'Home theater sound systems',
                'parent_id' => 4,
                'status' => true,
                'sort_order' => 4,
            ],
        ];

        // Insert main categories
        foreach ($categories as $category) {
            Category::create($category);
        }

        // Insert subcategories
        foreach ($smartphoneSubs as $sub) {
            Category::create($sub);
        }

        foreach ($laptopSubs as $sub) {
            Category::create($sub);
        }

        foreach ($audioSubs as $sub) {
            Category::create($sub);
        }

        $this->command->info('Categories seeded successfully!');
    }
}