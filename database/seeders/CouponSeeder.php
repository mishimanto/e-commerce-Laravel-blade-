<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CouponSeeder extends Seeder
{
    public function run()
    {
        $coupons = [
            // Welcome Coupon
            [
                'code' => 'WELCOME10',
                'name' => 'Welcome Discount',
                'description' => '10% off on your first order',
                'type' => 'percentage',
                'value' => 10,
                'min_order_amount' => 1000,
                'max_discount_amount' => 500,
                'usage_limit' => 1000,
                'usage_per_user' => 1,
                'total_used' => 0,
                'starts_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addYear(),
                'is_active' => true,
            ],
            
            // Festival Special
            [
                'code' => 'EID50',
                'name' => 'Eid Special',
                'description' => 'Flat ৳50 off on all orders',
                'type' => 'fixed',
                'value' => 50,
                'min_order_amount' => 500,
                'max_discount_amount' => 50,
                'usage_limit' => 500,
                'usage_per_user' => 2,
                'total_used' => 0,
                'starts_at' => Carbon::now()->subDays(5),
                'expires_at' => Carbon::now()->addDays(25),
                'is_active' => true,
            ],
            
            // Big Spender
            [
                'code' => 'BIG500',
                'name' => 'Big Spender',
                'description' => '৳500 off on orders above ৳10,000',
                'type' => 'fixed',
                'value' => 500,
                'min_order_amount' => 10000,
                'max_discount_amount' => 500,
                'usage_limit' => 100,
                'usage_per_user' => 1,
                'total_used' => 0,
                'starts_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addMonths(3),
                'is_active' => true,
            ],
            
            // Free Shipping
            [
                'code' => 'FREESHIP',
                'name' => 'Free Shipping',
                'description' => 'Free shipping on all orders',
                'type' => 'free_shipping',
                'value' => 0,
                'min_order_amount' => 1000,
                'max_discount_amount' => null,
                'usage_limit' => null,
                'usage_per_user' => null,
                'total_used' => 0,
                'starts_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addMonths(6),
                'is_active' => true,
            ],
            
            // Black Friday
            [
                'code' => 'BLACK20',
                'name' => 'Black Friday',
                'description' => '20% off on electronics',
                'type' => 'percentage',
                'value' => 20,
                'min_order_amount' => 5000,
                'max_discount_amount' => 2000,
                'usage_limit' => 200,
                'usage_per_user' => 3,
                'total_used' => 0,
                'starts_at' => Carbon::now()->subDays(10),
                'expires_at' => Carbon::now()->addDays(20),
                'is_active' => true,
                'applicable_categories' => json_encode([1, 2, 3]), // Electronics categories
            ],
            
            // Flash Sale
            [
                'code' => 'FLASH15',
                'name' => 'Flash Sale',
                'description' => '15% off - Today only!',
                'type' => 'percentage',
                'value' => 15,
                'min_order_amount' => 2000,
                'max_discount_amount' => 750,
                'usage_limit' => 50,
                'usage_per_user' => 1,
                'total_used' => 0,
                'starts_at' => Carbon::now(),
                'expires_at' => Carbon::now()->endOfDay(),
                'is_active' => true,
            ],
            
            // Member Exclusive
            [
                'code' => 'MEMBER25',
                'name' => 'Member Exclusive',
                'description' => '25% off for premium members',
                'type' => 'percentage',
                'value' => 25,
                'min_order_amount' => 3000,
                'max_discount_amount' => 1000,
                'usage_limit' => 500,
                'usage_per_user' => 5,
                'total_used' => 0,
                'starts_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addYear(),
                'is_active' => true,
                'applicable_users' => json_encode([]), // Will be assigned to specific users
            ],
            
            // Clearance Sale
            [
                'code' => 'CLEAR30',
                'name' => 'Clearance Sale',
                'description' => '30% off on clearance items',
                'type' => 'percentage',
                'value' => 30,
                'min_order_amount' => 1000,
                'max_discount_amount' => 1500,
                'usage_limit' => 100,
                'usage_per_user' => 2,
                'total_used' => 0,
                'starts_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addDays(15),
                'is_active' => true,
                'applicable_products' => json_encode([]), // Will be assigned to specific products
            ],
            
            // New User
            [
                'code' => 'NEWUSER50',
                'name' => 'New User Offer',
                'description' => 'Flat ৳50 off for new users',
                'type' => 'fixed',
                'value' => 50,
                'min_order_amount' => 500,
                'max_discount_amount' => 50,
                'usage_limit' => 1000,
                'usage_per_user' => 1,
                'total_used' => 0,
                'starts_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addMonths(6),
                'is_active' => true,
            ],
            
            // Anniversary Special
            [
                'code' => 'ANV22',
                'name' => '2nd Anniversary',
                'description' => '22% off - Anniversary special',
                'type' => 'percentage',
                'value' => 22,
                'min_order_amount' => 1500,
                'max_discount_amount' => 1000,
                'usage_limit' => 500,
                'usage_per_user' => 1,
                'total_used' => 0,
                'starts_at' => Carbon::now()->addDays(5),
                'expires_at' => Carbon::now()->addDays(12),
                'is_active' => true,
            ],
            
            // Referral Bonus
            [
                'code' => 'REFER100',
                'name' => 'Referral Bonus',
                'description' => 'Get ৳100 when your friend orders',
                'type' => 'fixed',
                'value' => 100,
                'min_order_amount' => 1000,
                'max_discount_amount' => 100,
                'usage_limit' => null,
                'usage_per_user' => null,
                'total_used' => 0,
                'starts_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addYear(),
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::create($coupon);
        }

        // Generate additional random coupons using Faker
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 20; $i++) {
            $type = $faker->randomElement(['fixed', 'percentage', 'free_shipping']);
            $value = $type == 'percentage' ? $faker->numberBetween(5, 30) : $faker->numberBetween(50, 500);
            
            Coupon::create([
                'code' => strtoupper($faker->unique()->bothify('??##??##')),
                'name' => $faker->words(3, true),
                'description' => $faker->sentence,
                'type' => $type,
                'value' => $value,
                'min_order_amount' => $faker->optional(0.7)->numberBetween(500, 5000),
                'max_discount_amount' => $type == 'percentage' ? $faker->numberBetween(500, 2000) : null,
                'usage_limit' => $faker->optional(0.6)->numberBetween(50, 500),
                'usage_per_user' => $faker->optional(0.5)->numberBetween(1, 5),
                'total_used' => 0,
                'starts_at' => Carbon::now()->subDays($faker->numberBetween(0, 30)),
                'expires_at' => Carbon::now()->addDays($faker->numberBetween(30, 365)),
                'is_active' => $faker->boolean(80),
            ]);
        }
    }
}