<?php

return [
    'pathao' => [
        'base_url' => env('PATHAO_BASE_URL', 'https://api.pathao.com/v1'),
        'api_key' => env('PATHAO_API_KEY'),
        'secret_key' => env('PATHAO_SECRET_KEY'),
        'merchant_id' => env('PATHAO_MERCHANT_ID'),
    ],
    
    'steadfast' => [
        'base_url' => env('STEADFAST_BASE_URL', 'https://portal.packeep.com/api/v1'),
        'api_key' => env('STEADFAST_API_KEY'),
        'secret_key' => env('STEADFAST_SECRET_KEY'),
    ],
    
    'redx' => [
        'base_url' => env('REDX_BASE_URL', 'https://api.redx.com.bd/v1'),
        'access_token' => env('REDX_ACCESS_TOKEN'),
    ],
    
    'sundarban' => [
        'base_url' => env('SUNDARBAN_BASE_URL'),
        'username' => env('SUNDARBAN_USERNAME'),
        'password' => env('SUNDARBAN_PASSWORD'),
    ],
    
    'sa_poribohon' => [
        'base_url' => env('SA_PORIBOHON_BASE_URL'),
        'api_key' => env('SA_PORIBOHON_API_KEY'),
    ],
    
    'default' => env('DEFAULT_COURIER', 'pathao'),
    
    'webhook_secret' => env('COURIER_WEBHOOK_SECRET'),
];