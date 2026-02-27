<?php
// database/seeders/PaymentMethodsSeeder.php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodsSeeder extends Seeder
{
    public function run()
    {
        $paymentMethods = [
            [
                'code' => 'cash_on_delivery',
                'name' => 'Cash on Delivery',
                'description' => 'Pay with cash when you receive your order',
                'type' => 'cash',
                'icon' => 'images/payments/cod.png',
                'instructions' => [
                    'Keep exact change ready',
                    'Check your items before payment'
                ],
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'code' => 'bkash',
                'name' => 'bKash',
                'description' => 'Pay with bKash mobile banking',
                'type' => 'online',
                'icon' => 'images/payments/bkash.png',
                'instructions' => [
                    'Go to your bKash mobile menu',
                    'Select "Payment"',
                    'Enter our merchant number: 01XXXXXXXXX',
                    'Enter the amount and your PIN'
                ],
                'config' => [
                    'merchant_number' => '01XXXXXXXXX',
                    'api_key' => 'your_bkash_api_key',
                    'api_secret' => 'your_bkash_secret'
                ],
                'fixed_fee' => 0,
                'percentage_fee' => 1.5,
                'minimum_fee' => 5,
                'maximum_fee' => 200,
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'code' => 'nagad',
                'name' => 'Nagad',
                'description' => 'Pay with Nagad mobile banking',
                'type' => 'online',
                'icon' => 'images/payments/nagad.png',
                'instructions' => [
                    'Dial *167#',
                    'Select "Payment"',
                    'Enter merchant number',
                    'Enter amount and PIN'
                ],
                'config' => [
                    'merchant_number' => '01XXXXXXXXX',
                    'api_key' => 'your_nagad_api_key'
                ],
                'fixed_fee' => 0,
                'percentage_fee' => 1.5,
                'minimum_fee' => 5,
                'maximum_fee' => 200,
                'sort_order' => 3,
                'is_active' => true
            ],
            [
                'code' => 'rocket',
                'name' => 'Rocket',
                'description' => 'Pay with Rocket mobile banking',
                'type' => 'online',
                'icon' => 'images/payments/rocket.png',
                'instructions' => [
                    'Dial *322#',
                    'Select "Payment"',
                    'Enter merchant wallet',
                    'Enter amount and PIN'
                ],
                'config' => [
                    'merchant_wallet' => 'XXXXXXXXX'
                ],
                'fixed_fee' => 0,
                'percentage_fee' => 1.5,
                'minimum_fee' => 5,
                'maximum_fee' => 200,
                'sort_order' => 4,
                'is_active' => true
            ],
            // [
            //     'code' => 'bank_transfer',
            //     'name' => 'Bank Transfer',
            //     'description' => 'Direct bank transfer',
            //     'type' => 'offline',
            //     'icon' => 'images/payments/bank.png',
            //     'instructions' => [
            //         'Bank: Dutch Bangla Bank',
            //         'Account Name: Phone & Gadgets',
            //         'Account Number: 12345678901',
            //         'Branch: Gulshan-1, Dhaka',
            //         'Send payment receipt to finance@example.com'
            //     ],
            //     'sort_order' => 5,
            //     'is_active' => false
            // ],
            // [
            //     'code' => 'card_payment',
            //     'name' => 'Card Payment',
            //     'description' => 'Pay with Visa/Mastercard/Amex',
            //     'type' => 'online',
            //     'icon' => 'images/payments/card.png',
            //     'config' => [
            //         'provider' => 'sslcommerz',
            //         'store_id' => 'your_store_id',
            //         'store_password' => 'your_password'
            //     ],
            //     'percentage_fee' => 2.0,
            //     'minimum_fee' => 10,
            //     'maximum_fee' => 500,
            //     'sort_order' => 6,
            //     'is_active' => false
            // ]
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::updateOrCreate(
                ['code' => $method['code']],
                $method
            );
        }
    }
}