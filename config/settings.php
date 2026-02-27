<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Permission Groups
    |--------------------------------------------------------------------------
    |
    | Define permission groups for organizing permissions in the UI.
    |
    */
    'groups' => [
        'dashboard' => 'Dashboard',
        'products' => 'Product Management',
        'orders' => 'Order Management',
        'users' => 'User Management',
        'categories' => 'Category Management',
        'brands' => 'Brand Management',
        'coupons' => 'Coupon Management',
        'banners' => 'Banner Management',
        'reports' => 'Report Management',
        'settings' => 'Settings',
        'system' => 'System',
        'marketing' => 'Marketing',
        'couriers' => 'Courier Management',
        'reviews' => 'Review Management',
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions List
    |--------------------------------------------------------------------------
    |
    | Define all permissions available in the system.
    | Format: 'slug' => ['name' => 'Display Name', 'group' => 'group_name']
    |
    */
    'permissions' => [
        // Dashboard
        'view-dashboard' => [
            'name' => 'View Dashboard',
            'group' => 'dashboard',
            'description' => 'Can view the admin dashboard',
        ],

        // Product Management
        'view-products' => [
            'name' => 'View Products',
            'group' => 'products',
            'description' => 'Can view product list and details',
        ],
        'create-products' => [
            'name' => 'Create Products',
            'group' => 'products',
            'description' => 'Can create new products',
        ],
        'edit-products' => [
            'name' => 'Edit Products',
            'group' => 'products',
            'description' => 'Can edit existing products',
        ],
        'delete-products' => [
            'name' => 'Delete Products',
            'group' => 'products',
            'description' => 'Can delete products',
        ],
        'manage-products' => [
            'name' => 'Manage Products',
            'group' => 'products',
            'description' => 'Full product management access',
        ],

        // Order Management
        'view-orders' => [
            'name' => 'View Orders',
            'group' => 'orders',
            'description' => 'Can view order list and details',
        ],
        'create-orders' => [
            'name' => 'Create Orders',
            'group' => 'orders',
            'description' => 'Can create orders manually',
        ],
        'edit-orders' => [
            'name' => 'Edit Orders',
            'group' => 'orders',
            'description' => 'Can edit order details',
        ],
        'delete-orders' => [
            'name' => 'Delete Orders',
            'group' => 'orders',
            'description' => 'Can delete orders',
        ],
        'update-order-status' => [
            'name' => 'Update Order Status',
            'group' => 'orders',
            'description' => 'Can update order status',
        ],
        'manage-orders' => [
            'name' => 'Manage Orders',
            'group' => 'orders',
            'description' => 'Full order management access',
        ],

        // User Management
        'view-users' => [
            'name' => 'View Users',
            'group' => 'users',
            'description' => 'Can view user list and details',
        ],
        'create-users' => [
            'name' => 'Create Users',
            'group' => 'users',
            'description' => 'Can create new users',
        ],
        'edit-users' => [
            'name' => 'Edit Users',
            'group' => 'users',
            'description' => 'Can edit user details',
        ],
        'delete-users' => [
            'name' => 'Delete Users',
            'group' => 'users',
            'description' => 'Can delete users',
        ],
        'manage-users' => [
            'name' => 'Manage Users',
            'group' => 'users',
            'description' => 'Full user management access',
        ],

        // Category Management
        'view-categories' => [
            'name' => 'View Categories',
            'group' => 'categories',
            'description' => 'Can view category list',
        ],
        'create-categories' => [
            'name' => 'Create Categories',
            'group' => 'categories',
            'description' => 'Can create new categories',
        ],
        'edit-categories' => [
            'name' => 'Edit Categories',
            'group' => 'categories',
            'description' => 'Can edit existing categories',
        ],
        'delete-categories' => [
            'name' => 'Delete Categories',
            'group' => 'categories',
            'description' => 'Can delete categories',
        ],
        'manage-categories' => [
            'name' => 'Manage Categories',
            'group' => 'categories',
            'description' => 'Full category management access',
        ],

        // Brand Management
        'view-brands' => [
            'name' => 'View Brands',
            'group' => 'brands',
            'description' => 'Can view brand list',
        ],
        'create-brands' => [
            'name' => 'Create Brands',
            'group' => 'brands',
            'description' => 'Can create new brands',
        ],
        'edit-brands' => [
            'name' => 'Edit Brands',
            'group' => 'brands',
            'description' => 'Can edit existing brands',
        ],
        'delete-brands' => [
            'name' => 'Delete Brands',
            'group' => 'brands',
            'description' => 'Can delete brands',
        ],
        'manage-brands' => [
            'name' => 'Manage Brands',
            'group' => 'brands',
            'description' => 'Full brand management access',
        ],

        // Coupon Management
        'view-coupons' => [
            'name' => 'View Coupons',
            'group' => 'coupons',
            'description' => 'Can view coupon list',
        ],
        'create-coupons' => [
            'name' => 'Create Coupons',
            'group' => 'coupons',
            'description' => 'Can create new coupons',
        ],
        'edit-coupons' => [
            'name' => 'Edit Coupons',
            'group' => 'coupons',
            'description' => 'Can edit existing coupons',
        ],
        'delete-coupons' => [
            'name' => 'Delete Coupons',
            'group' => 'coupons',
            'description' => 'Can delete coupons',
        ],
        'manage-coupons' => [
            'name' => 'Manage Coupons',
            'group' => 'coupons',
            'description' => 'Full coupon management access',
        ],

        // Banner Management
        'view-banners' => [
            'name' => 'View Banners',
            'group' => 'banners',
            'description' => 'Can view banner list',
        ],
        'create-banners' => [
            'name' => 'Create Banners',
            'group' => 'banners',
            'description' => 'Can create new banners',
        ],
        'edit-banners' => [
            'name' => 'Edit Banners',
            'group' => 'banners',
            'description' => 'Can edit existing banners',
        ],
        'delete-banners' => [
            'name' => 'Delete Banners',
            'group' => 'banners',
            'description' => 'Can delete banners',
        ],
        'manage-banners' => [
            'name' => 'Manage Banners',
            'group' => 'banners',
            'description' => 'Full banner management access',
        ],

        // Report Management
        'view-reports' => [
            'name' => 'View Reports',
            'group' => 'reports',
            'description' => 'Can view reports',
        ],
        'export-reports' => [
            'name' => 'Export Reports',
            'group' => 'reports',
            'description' => 'Can export reports',
        ],
        'manage-reports' => [
            'name' => 'Manage Reports',
            'group' => 'reports',
            'description' => 'Full report management access',
        ],

        // Settings
        'view-settings' => [
            'name' => 'View Settings',
            'group' => 'settings',
            'description' => 'Can view settings',
        ],
        'edit-settings' => [
            'name' => 'Edit Settings',
            'group' => 'settings',
            'description' => 'Can edit settings',
        ],
        'manage-settings' => [
            'name' => 'Manage Settings',
            'group' => 'settings',
            'description' => 'Full settings management access',
        ],

        // System
        'view-logs' => [
            'name' => 'View Logs',
            'group' => 'system',
            'description' => 'Can view system logs',
        ],
        'clear-cache' => [
            'name' => 'Clear Cache',
            'group' => 'system',
            'description' => 'Can clear system cache',
        ],
        'run-backup' => [
            'name' => 'Run Backup',
            'group' => 'system',
            'description' => 'Can run system backup',
        ],
        'manage-system' => [
            'name' => 'Manage System',
            'group' => 'system',
            'description' => 'Full system management access',
        ],

        // Marketing
        'send-newsletter' => [
            'name' => 'Send Newsletter',
            'group' => 'marketing',
            'description' => 'Can send newsletters',
        ],
        'manage-marketing' => [
            'name' => 'Manage Marketing',
            'group' => 'marketing',
            'description' => 'Full marketing management access',
        ],

        // Courier Management
        'view-couriers' => [
            'name' => 'View Couriers',
            'group' => 'couriers',
            'description' => 'Can view courier list',
        ],
        'manage-couriers' => [
            'name' => 'Manage Couriers',
            'group' => 'couriers',
            'description' => 'Full courier management access',
        ],

        // Review Management
        'view-reviews' => [
            'name' => 'View Reviews',
            'group' => 'reviews',
            'description' => 'Can view product reviews',
        ],
        'manage-reviews' => [
            'name' => 'Manage Reviews',
            'group' => 'reviews',
            'description' => 'Can approve, edit, delete reviews',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Role Permissions
    |--------------------------------------------------------------------------
    |
    | Define default permissions for each role.
    |
    */
    'roles' => [
        'super-admin' => [
            'name' => 'Super Admin',
            'level' => 100,
            'permissions' => '*', // All permissions
        ],

        'admin' => [
            'name' => 'Admin',
            'level' => 80,
            'permissions' => [
                'view-dashboard',
                'manage-products',
                'manage-orders',
                'manage-users',
                'manage-categories',
                'manage-brands',
                'manage-coupons',
                'manage-banners',
                'view-reports',
                'export-reports',
                'view-settings',
                'edit-settings',
                'manage-couriers',
                'view-reviews',
                'manage-reviews',
            ],
        ],

        'staff' => [
            'name' => 'Staff',
            'level' => 50,
            'permissions' => [
                'view-dashboard',
                'view-products',
                'edit-products',
                'view-orders',
                'edit-orders',
                'update-order-status',
                'view-users',
                'view-categories',
                'view-brands',
                'view-coupons',
                'view-banners',
                'view-reports',
                'view-couriers',
                'view-reviews',
            ],
        ],

        'customer' => [
            'name' => 'Customer',
            'level' => 10,
            'permissions' => [], // Customers have no admin permissions
        ],
    ],
];