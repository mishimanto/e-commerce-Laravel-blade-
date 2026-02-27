<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Get users with filters - roles and role_users table based
     */
    public function getFilteredUsers(array $filters, $perPage = 15)
    {
        $query = $this->model
            ->select('users.*')
            ->withCount('orders')
            ->withSum('orders', 'total')
            ->with(['roles']);

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'LIKE', "%{$search}%")
                  ->orWhere('users.email', 'LIKE', "%{$search}%")
                  ->orWhere('users.phone', 'LIKE', "%{$search}%");
            });
        }

        // Role filter
        if (!empty($filters['role'])) {
            $query->whereExists(function ($q) use ($filters) {
                $q->select(DB::raw(1))
                  ->from('role_user')
                  ->join('roles', 'role_user.role_id', '=', 'roles.id')
                  ->whereColumn('role_user.user_id', 'users.id')
                  ->where('roles.name', $filters['role']);
            });
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query->where('users.is_active', $filters['status'] === 'active');
        }

        // Date filters
        if (!empty($filters['date_from'])) {
            $query->whereDate('users.created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('users.created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('users.created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get user with details including roles
     */
    public function getUserWithDetails($id)
    {
        return $this->model
            ->with(['roles', 'orders' => function ($query) {
                $query->latest()->limit(5);
            }, 'addresses'])
            ->findOrFail($id);
    }

    /**
     * Create a new user with role
     */
    public function createUser(array $data)
    {
        DB::beginTransaction();
        
        try {
            $data['password'] = Hash::make($data['password']);
            
            $user = $this->create($data);
            
            // Assign role if provided
            if (!empty($data['role'])) {
                $role = DB::table('roles')->where('name', $data['role'])->first();
                if ($role) {
                    DB::table('role_user')->insert([
                        'user_id' => $user->id,
                        'role_id' => $role->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
            
            DB::commit();
            return $user;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update user with role
     */
    public function updateUser($id, array $data)
    {
        DB::beginTransaction();
        
        try {
            $user = $this->find($id);

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $user->update($data);

            // Update role if provided
            if (!empty($data['role'])) {
                // Delete existing roles
                DB::table('role_user')->where('user_id', $user->id)->delete();
                
                // Assign new role
                $role = DB::table('roles')->where('name', $data['role'])->first();
                if ($role) {
                    DB::table('role_user')->insert([
                        'user_id' => $user->id,
                        'role_id' => $role->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
            
            DB::commit();
            return $user;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get all roles from roles table
     */
    public function getAllRoles()
    {
        return DB::table('roles')->orderBy('name')->get();
    }

    /**
     * Get customers only (users with 'customer' role)
     */
    public function getCustomers($perPage = 15)
    {
        return $this->model
            ->select('users.*')
            ->withCount('orders')
            ->withSum('orders', 'total')
            ->with(['roles'])
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('role_user')
                    ->join('roles', 'role_user.role_id', '=', 'roles.id')
                    ->whereColumn('role_user.user_id', 'users.id')
                    ->where('roles.name', 'customer');
            })
            ->orderBy('users.created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get staff members (users with admin, staff, super-admin roles)
     */
    public function getStaff($perPage = 15)
    {
        return $this->model
            ->select('users.*')
            ->withCount('orders')
            ->withSum('orders', 'total')
            ->with(['roles'])
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('role_user')
                    ->join('roles', 'role_user.role_id', '=', 'roles.id')
                    ->whereColumn('role_user.user_id', 'users.id')
                    ->whereIn('roles.name', ['admin', 'staff', 'super-admin']);
            })
            ->orderBy('users.created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get users by specific role
     */
    public function getUsersByRole($roleName, $perPage = 15)
    {
        return $this->model
            ->select('users.*')
            ->withCount('orders')
            ->withSum('orders', 'total')
            ->with(['roles'])
            ->whereExists(function ($query) use ($roleName) {
                $query->select(DB::raw(1))
                    ->from('role_user')
                    ->join('roles', 'role_user.role_id', '=', 'roles.id')
                    ->whereColumn('role_user.user_id', 'users.id')
                    ->where('roles.name', $roleName);
            })
            ->orderBy('users.created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get user statistics with role counts
     */
    public function getUserStatistics()
    {
        $totalUsers = $this->model->count();
        $activeUsers = $this->model->where('is_active', true)->count();
        $newThisMonth = $this->model->whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->count();
        
        // Customer count
        $customerCount = $this->model
            ->whereHas('roles', function ($query) {
                $query->where('name', 'customer');
            })
            ->count();
        
        // Staff count (admin, staff, super-admin)
        $staffCount = $this->model
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['admin', 'staff', 'super-admin']);
            })
            ->count();
        
        // Admin count (admin, super-admin)
        $adminCount = $this->model
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['admin', 'super-admin']);
            })
            ->count();

        return [
            'total' => $totalUsers,
            'active' => $activeUsers,
            'new_this_month' => $newThisMonth,
            'customers' => $customerCount,
            'staff' => $staffCount,
            'admins' => $adminCount,
        ];
    }    
}