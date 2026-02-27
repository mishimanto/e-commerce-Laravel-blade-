<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // General Settings
            [
                'key' => 'store_name',
                'value' => 'Phone & Gadgets Store',
                'group' => 'general',
                'type' => 'text',
                'label' => 'Store Name',
                'description' => 'Your store name',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'store_tagline',
                'value' => 'Your Trusted Tech Partner',
                'group' => 'general',
                'type' => 'text',
                'label' => 'Store Tagline',
                'description' => 'Short description of your store',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'store_email',
                'value' => 'support@phonegadgets.com',
                'group' => 'general',
                'type' => 'email',
                'label' => 'Store Email',
                'description' => 'Primary contact email',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'store_phone',
                'value' => '+880 1234 567890',
                'group' => 'general',
                'type' => 'text',
                'label' => 'Store Phone',
                'description' => 'Primary contact number',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'store_address',
                'value' => '123 Tech Street, Dhaka, Bangladesh',
                'group' => 'general',
                'type' => 'textarea',
                'label' => 'Store Address',
                'description' => 'Physical store address',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'store_logo',
                'value' => null,
                'group' => 'general',
                'type' => 'image',
                'label' => 'Store Logo',
                'description' => 'Upload your store logo',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'store_favicon',
                'value' => null,
                'group' => 'general',
                'type' => 'image',
                'label' => 'Favicon',
                'description' => 'Browser tab icon',
                'is_editable' => true,
                'is_visible' => true
            ],

            // Currency Settings
            [
                'key' => 'currency_code',
                'value' => 'BDT',
                'group' => 'currency',
                'type' => 'select',
                'label' => 'Currency',
                'description' => 'Store currency',
                'options' => json_encode(['BDT' => 'BDT (৳)', 'USD' => 'USD ($)', 'EUR' => 'EUR (€)']),
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'currency_symbol',
                'value' => '৳',
                'group' => 'currency',
                'type' => 'text',
                'label' => 'Currency Symbol',
                'description' => 'Display symbol',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'currency_position',
                'value' => 'left',
                'group' => 'currency',
                'type' => 'radio',
                'label' => 'Currency Position',
                'options' => json_encode(['left' => 'Left (৳100)', 'right' => 'Right (100৳)']),
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'thousand_separator',
                'value' => ',',
                'group' => 'currency',
                'type' => 'text',
                'label' => 'Thousand Separator',
                'description' => 'Separator for thousands',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'decimal_separator',
                'value' => '.',
                'group' => 'currency',
                'type' => 'text',
                'label' => 'Decimal Separator',
                'description' => 'Separator for decimals',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'number_of_decimals',
                'value' => '2',
                'group' => 'currency',
                'type' => 'number',
                'label' => 'Number of Decimals',
                'description' => 'Decimal places',
                'is_editable' => true,
                'is_visible' => true
            ],

            // Tax Settings
            [
                'key' => 'tax_enabled',
                'value' => '1',
                'group' => 'tax',
                'type' => 'checkbox',
                'label' => 'Enable Tax',
                'description' => 'Apply tax to orders',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'tax_rate',
                'value' => '15',
                'group' => 'tax',
                'type' => 'number',
                'label' => 'Tax Rate (%)',
                'description' => 'Default tax percentage',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'tax_included',
                'value' => '0',
                'group' => 'tax',
                'type' => 'checkbox',
                'label' => 'Tax Included',
                'description' => 'Tax included in prices',
                'is_editable' => true,
                'is_visible' => true
            ],

            // Shipping Settings
            [
                'key' => 'free_shipping_threshold',
                'value' => '5000',
                'group' => 'shipping',
                'type' => 'number',
                'label' => 'Free Shipping Threshold',
                'description' => 'Order amount for free shipping',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'shipping_flat_rate',
                'value' => '100',
                'group' => 'shipping',
                'type' => 'number',
                'label' => 'Flat Shipping Rate',
                'description' => 'Default shipping cost',
                'is_editable' => true,
                'is_visible' => true
            ],

            // SEO Settings
            [
                'key' => 'meta_title',
                'value' => 'Phone & Gadgets Store - Best Tech Products',
                'group' => 'seo',
                'type' => 'text',
                'label' => 'Default Meta Title',
                'description' => 'SEO title for homepage',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'meta_description',
                'value' => 'Shop the latest smartphones, gadgets, and accessories at best prices. Fast shipping, genuine products, and excellent customer service.',
                'group' => 'seo',
                'type' => 'textarea',
                'label' => 'Default Meta Description',
                'description' => 'SEO description for homepage',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'meta_keywords',
                'value' => 'phones, gadgets, smartphones, accessories, tech store',
                'group' => 'seo',
                'type' => 'text',
                'label' => 'Default Meta Keywords',
                'description' => 'SEO keywords',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'google_analytics_id',
                'value' => null,
                'group' => 'seo',
                'type' => 'text',
                'label' => 'Google Analytics ID',
                'description' => 'UA-XXXXX-X or G-XXXXXXX',
                'is_editable' => true,
                'is_visible' => true
            ],

            // Payment Settings
            [
                'key' => 'payment_methods',
                'value' => json_encode(['cash_on_delivery', 'sslcommerz', 'stripe']),
                'group' => 'payment',
                'type' => 'select',
                'label' => 'Payment Methods',
                'description' => 'Enabled payment methods',
                'options' => json_encode([
                    'cash_on_delivery' => 'Cash on Delivery',
                    'sslcommerz' => 'SSLCommerz',
                    'stripe' => 'Stripe',
                    'bkash' => 'bKash',
                    'nagad' => 'Nagad',
                    'rocket' => 'Rocket'
                ]),
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'sslcommerz_sandbox',
                'value' => '1',
                'group' => 'payment',
                'type' => 'checkbox',
                'label' => 'SSLCommerz Sandbox',
                'description' => 'Enable test mode',
                'is_editable' => true,
                'is_visible' => true
            ],

            // Social Media
            [
                'key' => 'facebook_url',
                'value' => null,
                'group' => 'social',
                'type' => 'url',
                'label' => 'Facebook Page URL',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'twitter_url',
                'value' => null,
                'group' => 'social',
                'type' => 'url',
                'label' => 'Twitter/X URL',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'instagram_url',
                'value' => null,
                'group' => 'social',
                'type' => 'url',
                'label' => 'Instagram URL',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'youtube_url',
                'value' => null,
                'group' => 'social',
                'type' => 'url',
                'label' => 'YouTube Channel URL',
                'is_editable' => true,
                'is_visible' => true
            ],

            // Order Settings
            [
                'key' => 'order_prefix',
                'value' => 'ORD',
                'group' => 'order',
                'type' => 'text',
                'label' => 'Order Prefix',
                'description' => 'Prefix for order numbers',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'invoice_prefix',
                'value' => 'INV',
                'group' => 'order',
                'type' => 'text',
                'label' => 'Invoice Prefix',
                'description' => 'Prefix for invoice numbers',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'auto_complete_order_days',
                'value' => '7',
                'group' => 'order',
                'type' => 'number',
                'label' => 'Auto Complete Order',
                'description' => 'Days after delivery to auto complete',
                'is_editable' => true,
                'is_visible' => true
            ],

            // Notification Settings
            [
                'key' => 'order_confirmation_sms',
                'value' => '1',
                'group' => 'notification',
                'type' => 'checkbox',
                'label' => 'Order Confirmation SMS',
                'description' => 'Send SMS on order confirmation',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'order_confirmation_email',
                'value' => '1',
                'group' => 'notification',
                'type' => 'checkbox',
                'label' => 'Order Confirmation Email',
                'description' => 'Send email on order confirmation',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'stock_alert_threshold',
                'value' => '5',
                'group' => 'notification',
                'type' => 'number',
                'label' => 'Stock Alert Threshold',
                'description' => 'Alert when stock is below',
                'is_editable' => true,
                'is_visible' => true
            ],

            // Maintenance Mode
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'group' => 'maintenance',
                'type' => 'checkbox',
                'label' => 'Enable Maintenance Mode',
                'description' => 'Put store in maintenance mode',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'Store is under maintenance. We will be back soon!',
                'group' => 'maintenance',
                'type' => 'textarea',
                'label' => 'Maintenance Message',
                'description' => 'Message to show during maintenance',
                'is_editable' => true,
                'is_visible' => true
            ],

            // Invoice Settings
            [
                'key' => 'invoice_footer',
                'value' => 'Thank you for shopping with us!',
                'group' => 'invoice',
                'type' => 'textarea',
                'label' => 'Invoice Footer',
                'description' => 'Footer text for invoices',
                'is_editable' => true,
                'is_visible' => true
            ],
            [
                'key' => 'invoice_terms',
                'value' => '1. Goods once sold cannot be returned or exchanged.\n2. Warranty applies as per manufacturer terms.',
                'group' => 'invoice',
                'type' => 'textarea',
                'label' => 'Invoice Terms',
                'description' => 'Terms and conditions for invoices',
                'is_editable' => true,
                'is_visible' => true
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}