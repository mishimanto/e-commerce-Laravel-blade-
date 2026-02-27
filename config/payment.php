<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    |
    | This option controls the default payment gateway for the application.
    |
    */
    'default' => env('PAYMENT_GATEWAY', 'sslcommerz'),

    /*
    |--------------------------------------------------------------------------
    | Payment Gateways
    |--------------------------------------------------------------------------
    |
    | Here you may configure the payment gateways for your application.
    |
    */
    'gateways' => [
        'cash_on_delivery' => [
            'enabled' => true,
            'title' => 'Cash on Delivery',
            'description' => 'Pay with cash upon delivery',
            'icon' => '/images/payments/cod.png',
            'order' => 1,
        ],

        'sslcommerz' => [
            'enabled' => env('SSLCOMMERZ_ENABLED', true),
            'title' => 'SSLCommerz',
            'description' => 'Pay via credit/debit cards, mobile banking, or internet banking',
            'icon' => '/images/payments/sslcommerz.png',
            'order' => 2,
            'sandbox' => env('SSLCOMMERZ_SANDBOX', true),
            'store_id' => env('SSLCOMMERZ_STORE_ID'),
            'store_password' => env('SSLCOMMERZ_STORE_PASSWORD'),
            'currency' => 'BDT',
            'success_url' => '/payment/sslcommerz/success',
            'fail_url' => '/payment/sslcommerz/fail',
            'cancel_url' => '/payment/sslcommerz/cancel',
            'ipn_url' => '/payment/sslcommerz/ipn',
        ],

        'stripe' => [
            'enabled' => env('STRIPE_ENABLED', false),
            'title' => 'Stripe',
            'description' => 'Pay securely with credit/debit card',
            'icon' => '/images/payments/stripe.png',
            'order' => 3,
            'public_key' => env('STRIPE_KEY'),
            'secret_key' => env('STRIPE_SECRET'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
            'currency' => 'usd',
        ],

        'bkash' => [
            'enabled' => env('BKASH_ENABLED', false),
            'title' => 'bKash',
            'description' => 'Pay with bKash mobile banking',
            'icon' => '/images/payments/bkash.png',
            'order' => 4,
            'sandbox' => env('BKASH_SANDBOX', true),
            'app_key' => env('BKASH_APP_KEY'),
            'app_secret' => env('BKASH_APP_SECRET'),
            'username' => env('BKASH_USERNAME'),
            'password' => env('BKASH_PASSWORD'),
            'currency' => 'BDT',
        ],

        'nagad' => [
            'enabled' => env('NAGAD_ENABLED', false),
            'title' => 'Nagad',
            'description' => 'Pay with Nagad mobile banking',
            'icon' => '/images/payments/nagad.png',
            'order' => 5,
            'sandbox' => env('NAGAD_SANDBOX', true),
            'merchant_id' => env('NAGAD_MERCHANT_ID'),
            'merchant_number' => env('NAGAD_MERCHANT_NUMBER'),
            'public_key' => env('NAGAD_PUBLIC_KEY'),
            'private_key' => env('NAGAD_PRIVATE_KEY'),
            'currency' => 'BDT',
        ],

        'rocket' => [
            'enabled' => env('ROCKET_ENABLED', false),
            'title' => 'Rocket',
            'description' => 'Pay with Rocket (DBBL) mobile banking',
            'icon' => '/images/payments/rocket.png',
            'order' => 6,
            'wallet_number' => env('ROCKET_WALLET_NUMBER'),
            'currency' => 'BDT',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency Settings
    |--------------------------------------------------------------------------
    |
    | Here you may configure the currency settings for your application.
    |
    */
    'currency' => [
        'default' => env('DEFAULT_CURRENCY', 'BDT'),
        'symbol' => env('CURRENCY_SYMBOL', '৳'),
        'position' => env('CURRENCY_POSITION', 'left'), // left or right
        'thousand_separator' => env('THOUSAND_SEPARATOR', ','),
        'decimal_separator' => env('DECIMAL_SEPARATOR', '.'),
        'decimal_places' => env('DECIMAL_PLACES', 2),

        'available' => [
            'BDT' => [
                'name' => 'Bangladeshi Taka',
                'symbol' => '৳',
                'code' => 'BDT',
                'rate' => 1,
            ],
            'USD' => [
                'name' => 'US Dollar',
                'symbol' => '$',
                'code' => 'USD',
                'rate' => env('USD_RATE', 110),
            ],
            'EUR' => [
                'name' => 'Euro',
                'symbol' => '€',
                'code' => 'EUR',
                'rate' => env('EUR_RATE', 120),
            ],
            'GBP' => [
                'name' => 'British Pound',
                'symbol' => '£',
                'code' => 'GBP',
                'rate' => env('GBP_RATE', 140),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tax Settings
    |--------------------------------------------------------------------------
    |
    | Here you may configure the tax settings for your application.
    |
    */
    'tax' => [
        'enabled' => env('TAX_ENABLED', true),
        'rate' => env('TAX_RATE', 15), // percentage
        'included_in_price' => env('TAX_INCLUDED', false),
        'name' => env('TAX_NAME', 'VAT'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Invoice Settings
    |--------------------------------------------------------------------------
    |
    | Here you may configure the invoice settings for your application.
    |
    */
    'invoice' => [
        'prefix' => env('INVOICE_PREFIX', 'INV'),
        'footer' => env('INVOICE_FOOTER', 'Thank you for your business!'),
        'terms' => env('INVOICE_TERMS', 'Payment is due within 30 days.'),
        'logo' => env('INVOICE_LOGO'),
        'show_tax' => env('INVOICE_SHOW_TAX', true),
        'show_discount' => env('INVOICE_SHOW_DISCOUNT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Refund Settings
    |--------------------------------------------------------------------------
    |
    | Here you may configure the refund settings for your application.
    |
    */
    'refund' => [
        'period_days' => env('REFUND_PERIOD_DAYS', 7),
        'auto_approve' => env('REFUND_AUTO_APPROVE', false),
        'restock_on_refund' => env('RESTOCK_ON_REFUND', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Settings
    |--------------------------------------------------------------------------
    |
    | Here you may configure the webhook settings for payment gateways.
    |
    */
    'webhooks' => [
        'sslcommerz' => [
            'url' => '/payment/sslcommerz/webhook',
            'secret' => env('SSLCOMMERZ_WEBHOOK_SECRET'),
        ],
        'stripe' => [
            'url' => '/payment/stripe/webhook',
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
        ],
        'bkash' => [
            'url' => '/payment/bkash/webhook',
            'secret' => env('BKASH_WEBHOOK_SECRET'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Messages
    |--------------------------------------------------------------------------
    |
    | Here you may configure error messages for payment failures.
    |
    */
    'messages' => [
        'payment_failed' => 'Payment processing failed. Please try again.',
        'payment_cancelled' => 'Payment was cancelled.',
        'invalid_gateway' => 'Invalid payment gateway selected.',
        'gateway_disabled' => 'Selected payment gateway is currently disabled.',
        'verification_failed' => 'Payment verification failed.',
        'refund_failed' => 'Refund processing failed.',
    ],
];