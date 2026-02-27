<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if users table exists
        if (!Schema::hasTable('users')) {
            $this->command->error('Users table does not exist. Please run migrations first.');
            return;
        }

        // Create admin user if not exists
        $this->createUser([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin'
        ]);

        // Create staff user
        $this->createUser([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => 'password',
            'role' => 'staff'
        ]);

        // Create manager user
        $this->createUser([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => 'password',
            'role' => 'admin'
        ]);

        // Create 10 customer users
        $customers = [
            ['name' => 'John Doe', 'email' => 'john@example.com'],
            ['name' => 'Jane Smith', 'email' => 'jane@example.com'],
            ['name' => 'Bob Johnson', 'email' => 'bob@example.com'],
            ['name' => 'Alice Brown', 'email' => 'alice@example.com'],
            ['name' => 'Charlie Wilson', 'email' => 'charlie@example.com'],
            ['name' => 'Diana Prince', 'email' => 'diana@example.com'],
            ['name' => 'Edward Norton', 'email' => 'edward@example.com'],
            ['name' => 'Fiona Apple', 'email' => 'fiona@example.com'],
            ['name' => 'George Clooney', 'email' => 'george@example.com'],
            ['name' => 'Helen Hunt', 'email' => 'helen@example.com'],
        ];

        foreach ($customers as $customerData) {
            $this->createUser([
                'name' => $customerData['name'],
                'email' => $customerData['email'],
                'password' => 'password',
                'role' => 'customer'
            ]);
        }

        $this->command->info('Users seeded successfully!');
    }

    /**
     * Create a user if not exists
     */
    protected function createUser($data)
    {
        try {
            // Check if user already exists
            $existingUser = User::where('email', $data['email'])->first();
            
            if ($existingUser) {
                $this->command->warn("User {$data['email']} already exists. Skipping...");
                return $existingUser;
            }

            // Prepare user data
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'email_verified_at' => now(),
            ];

            // Add is_active if column exists
            if (Schema::hasColumn('users', 'is_active')) {
                $userData['is_active'] = true;
            }

            // Add phone if column exists
            if (Schema::hasColumn('users', 'phone')) {
                $userData['phone'] = '01' . rand(100000000, 999999999);
            }

            // Add address fields if they exist
            if (Schema::hasColumn('users', 'address')) {
                $userData['address'] = rand(1, 999) . ' Main Street';
            }
            
            if (Schema::hasColumn('users', 'city')) {
                $userData['city'] = 'Dhaka';
            }
            
            if (Schema::hasColumn('users', 'state')) {
                $userData['state'] = 'Dhaka';
            }
            
            if (Schema::hasColumn('users', 'zip')) {
                $userData['zip'] = rand(1000, 9999);
            }
            
            if (Schema::hasColumn('users', 'country')) {
                $userData['country'] = 'Bangladesh';
            }

            // Create user
            $user = User::create($userData);

            // Assign role
            if (isset($data['role'])) {
                $role = Role::where('slug', $data['role'])
                    ->orWhere('name', $data['role'])
                    ->first();
                
                if ($role && method_exists($user, 'assignRole')) {
                    $user->assignRole($role->slug);
                } elseif ($role) {
                    // Fallback: direct pivot insert if assignRole doesn't work
                    try {
                        \DB::table('role_user')->insert([
                            'role_id' => $role->id,
                            'user_id' => $user->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    } catch (\Exception $e) {
                        $this->command->warn("Could not assign role via pivot: " . $e->getMessage());
                    }
                }
            }

            $this->command->info("Created user: {$data['email']}");

            return $user;

        } catch (\Exception $e) {
            $this->command->error("Failed to create user {$data['email']}: " . $e->getMessage());
            return null;
        }
    }
}