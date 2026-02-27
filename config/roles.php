<?php

return [
    'super_admin' => [
        'name' => 'Super Admin',
        'permissions' => [
            'view-dashboard',
            'manage-products',
            'manage-orders',
            'manage-users',
            'manage-categories',
            'manage-brands',
            'manage-coupons',
            'manage-banners',
            'manage-settings',
            'manage-reports',
            'manage-couriers',
            'manage-roles',
            'manage-permissions',
            'view-logs',
            'backup-database',
        ],
    ],
    
    'admin' => [
        'name' => 'Admin',
        'permissions' => [
            'view-dashboard',
            'manage-products',
            'manage-orders',
            'manage-users',
            'manage-categories',
            'manage-brands',
            'manage-coupons',
            'manage-banners',
            'view-settings',
            'view-reports',
            'manage-couriers',
        ],
    ],
    
    'staff' => [
        'name' => 'Staff',
        'permissions' => [
            'view-dashboard',
            'view-products',
            'manage-orders',
            'view-categories',
            'view-brands',
            'view-coupons',
            'view-banners',
            'view-couriers',
        ],
    ],
];