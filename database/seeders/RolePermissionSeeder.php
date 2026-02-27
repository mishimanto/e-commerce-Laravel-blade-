<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('permission_role')->truncate();
        DB::table('role_user')->truncate();
        DB::table('permission_user')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Create permissions
        $permissions = [
            // Dashboard
            ['name' => 'View Dashboard', 'slug' => 'view-dashboard', 'group' => 'dashboard'],
            
            // Product Management
            ['name' => 'View Products', 'slug' => 'view-products', 'group' => 'products'],
            ['name' => 'Create Products', 'slug' => 'create-products', 'group' => 'products'],
            ['name' => 'Edit Products', 'slug' => 'edit-products', 'group' => 'products'],
            ['name' => 'Delete Products', 'slug' => 'delete-products', 'group' => 'products'],
            ['name' => 'Manage Products', 'slug' => 'manage-products', 'group' => 'products'],
            
            // Order Management
            ['name' => 'View Orders', 'slug' => 'view-orders', 'group' => 'orders'],
            ['name' => 'Create Orders', 'slug' => 'create-orders', 'group' => 'orders'],
            ['name' => 'Edit Orders', 'slug' => 'edit-orders', 'group' => 'orders'],
            ['name' => 'Delete Orders', 'slug' => 'delete-orders', 'group' => 'orders'],
            ['name' => 'Update Order Status', 'slug' => 'update-order-status', 'group' => 'orders'],
            ['name' => 'Manage Orders', 'slug' => 'manage-orders', 'group' => 'orders'],
            
            // User Management
            ['name' => 'View Users', 'slug' => 'view-users', 'group' => 'users'],
            ['name' => 'Create Users', 'slug' => 'create-users', 'group' => 'users'],
            ['name' => 'Edit Users', 'slug' => 'edit-users', 'group' => 'users'],
            ['name' => 'Delete Users', 'slug' => 'delete-users', 'group' => 'users'],
            ['name' => 'Manage Users', 'slug' => 'manage-users', 'group' => 'users'],
            
            // Category Management
            ['name' => 'View Categories', 'slug' => 'view-categories', 'group' => 'categories'],
            ['name' => 'Create Categories', 'slug' => 'create-categories', 'group' => 'categories'],
            ['name' => 'Edit Categories', 'slug' => 'edit-categories', 'group' => 'categories'],
            ['name' => 'Delete Categories', 'slug' => 'delete-categories', 'group' => 'categories'],
            ['name' => 'Manage Categories', 'slug' => 'manage-categories', 'group' => 'categories'],
            
            // Brand Management
            ['name' => 'View Brands', 'slug' => 'view-brands', 'group' => 'brands'],
            ['name' => 'Create Brands', 'slug' => 'create-brands', 'group' => 'brands'],
            ['name' => 'Edit Brands', 'slug' => 'edit-brands', 'group' => 'brands'],
            ['name' => 'Delete Brands', 'slug' => 'delete-brands', 'group' => 'brands'],
            ['name' => 'Manage Brands', 'slug' => 'manage-brands', 'group' => 'brands'],
            
            // Coupon Management
            ['name' => 'View Coupons', 'slug' => 'view-coupons', 'group' => 'coupons'],
            ['name' => 'Create Coupons', 'slug' => 'create-coupons', 'group' => 'coupons'],
            ['name' => 'Edit Coupons', 'slug' => 'edit-coupons', 'group' => 'coupons'],
            ['name' => 'Delete Coupons', 'slug' => 'delete-coupons', 'group' => 'coupons'],
            ['name' => 'Manage Coupons', 'slug' => 'manage-coupons', 'group' => 'coupons'],
            
            // Banner Management
            ['name' => 'View Banners', 'slug' => 'view-banners', 'group' => 'banners'],
            ['name' => 'Create Banners', 'slug' => 'create-banners', 'group' => 'banners'],
            ['name' => 'Edit Banners', 'slug' => 'edit-banners', 'group' => 'banners'],
            ['name' => 'Delete Banners', 'slug' => 'delete-banners', 'group' => 'banners'],
            ['name' => 'Manage Banners', 'slug' => 'manage-banners', 'group' => 'banners'],
            
            // Report Management
            ['name' => 'View Reports', 'slug' => 'view-reports', 'group' => 'reports'],
            ['name' => 'Export Reports', 'slug' => 'export-reports', 'group' => 'reports'],
            ['name' => 'Manage Reports', 'slug' => 'manage-reports', 'group' => 'reports'],
            
            // Settings
            ['name' => 'View Settings', 'slug' => 'view-settings', 'group' => 'settings'],
            ['name' => 'Edit Settings', 'slug' => 'edit-settings', 'group' => 'settings'],
            ['name' => 'Manage Settings', 'slug' => 'manage-settings', 'group' => 'settings'],
            
            // System
            ['name' => 'View Logs', 'slug' => 'view-logs', 'group' => 'system'],
            ['name' => 'Clear Cache', 'slug' => 'clear-cache', 'group' => 'system'],
            ['name' => 'Run Backup', 'slug' => 'run-backup', 'group' => 'system'],
            ['name' => 'Manage System', 'slug' => 'manage-system', 'group' => 'system'],
            
            // Courier Management
            ['name' => 'View Couriers', 'slug' => 'view-couriers', 'group' => 'couriers'],
            ['name' => 'Manage Couriers', 'slug' => 'manage-couriers', 'group' => 'couriers'],
            
            // Review Management
            ['name' => 'View Reviews', 'slug' => 'view-reviews', 'group' => 'reviews'],
            ['name' => 'Manage Reviews', 'slug' => 'manage-reviews', 'group' => 'reviews'],
            
            // Marketing
            ['name' => 'Send Newsletter', 'slug' => 'send-newsletter', 'group' => 'marketing'],
            ['name' => 'Manage Marketing', 'slug' => 'manage-marketing', 'group' => 'marketing'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create roles
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'level' => 100,
                'description' => 'Full access to all system features'
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'level' => 80,
                'description' => 'Administrative access with limited system controls'
            ],
            [
                'name' => 'Staff',
                'slug' => 'staff',
                'level' => 50,
                'description' => 'Staff member with operational access'
            ],
            [
                'name' => 'Customer',
                'slug' => 'customer',
                'level' => 10,
                'description' => 'Regular customer account'
            ],
        ];

        foreach ($roles as $roleData) {
            Role::create($roleData);
        }

        // Assign permissions to roles
        $superAdmin = Role::where('slug', 'super-admin')->first();
        $admin = Role::where('slug', 'admin')->first();
        $staff = Role::where('slug', 'staff')->first();

        // Super Admin gets all permissions
        $superAdmin->permissions()->attach(Permission::all());

        // Admin gets most permissions
        $adminPermissions = Permission::whereNotIn('slug', [
            'manage-system',
            'view-logs',
            'clear-cache',
            'run-backup',
            'delete-users',
            'delete-products',
            'delete-categories',
            'delete-brands',
            'delete-coupons',
            'delete-banners'
        ])->get();
        $admin->permissions()->attach($adminPermissions);

        // Staff gets limited permissions
        $staffPermissions = Permission::whereIn('slug', [
            'view-dashboard',
            'view-products',
            'view-orders',
            'update-order-status',
            'view-users',
            'view-categories',
            'view-brands',
            'view-coupons',
            'view-banners',
            'view-reports',
            'view-couriers',
            'view-reviews'
        ])->get();
        $staff->permissions()->attach($staffPermissions);

        // Create default admin user
        $adminUser = \App\Models\User::where('email', 'admin@example.com')->first();
        if (!$adminUser) {
            $adminUser = \App\Models\User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'is_active' => true
            ]);
        }
        $adminUser->assignRole('admin');

        // Create default staff user
        $staffUser = \App\Models\User::where('email', 'staff@example.com')->first();
        if (!$staffUser) {
            $staffUser = \App\Models\User::create([
                'name' => 'Staff User',
                'email' => 'staff@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'is_active' => true
            ]);
        }
        $staffUser->assignRole('staff');

        // Create sample customers
        for ($i = 1; $i <= 5; $i++) {
            $customer = \App\Models\User::where('email', "customer{$i}@example.com")->first();
            if (!$customer) {
                $customer = \App\Models\User::create([
                    'name' => "Customer {$i}",
                    'email' => "customer{$i}@example.com",
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                    'is_active' => true
                ]);
            }
            $customer->assignRole('customer');
        }

        $this->command->info('Roles and permissions seeded successfully!');
    }
}