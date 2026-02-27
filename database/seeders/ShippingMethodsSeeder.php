<?php
// database/seeders/ShippingMethodsSeeder.php

namespace Database\Seeders;

use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class ShippingMethodsSeeder extends Seeder
{
    public function run()
    {
        $shippingMethods = [
            [
                'code' => 'standard',
                'name' => 'Standard Shipping',
                'description' => 'Delivery within 3-5 business days',
                'cost' => 100,
                'delivery_time' => '3-5 business days',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'code' => 'express',
                'name' => 'Express Shipping',
                'description' => 'Delivery within 1-2 business days',
                'cost' => 200,
                'delivery_time' => '1-2 business days',
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'code' => 'free',
                'name' => 'Free Shipping',
                'description' => 'Free shipping on orders over ৳5000',
                'cost' => 0,
                'delivery_time' => '5-7 business days',
                'is_free_shipping' => true,
                'free_shipping_threshold' => 5000,
                'sort_order' => 3,
                'is_active' => true
            ],
            [
                'code' => 'same_day',
                'name' => 'Same Day Delivery',
                'description' => 'Delivery within 24 hours (Dhaka only)',
                'cost' => 300,
                'delivery_time' => '24 hours',
                'available_cities' => ['Dhaka'],
                'sort_order' => 4,
                'is_active' => true
            ],
            [
                'code' => 'outside_dhaka',
                'name' => 'Outside Dhaka',
                'description' => 'Delivery outside Dhaka city',
                'cost' => 150,
                'delivery_time' => '4-6 business days',
                'sort_order' => 5,
                'is_active' => true
            ]
        ];

        foreach ($shippingMethods as $method) {
            ShippingMethod::updateOrCreate(
                ['code' => $method['code']],
                $method
            );
        }
    }
}