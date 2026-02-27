<?php

namespace App\Traits;

trait HasPermissions
{
    use HasRoles;

    /**
     * Check if user is authorized for a permission
     */
    public function can($permission, $arguments = [])
    {
        return $this->hasPermission($permission);
    }

    /**
     * Check if user is not authorized for a permission
     */
    public function cannot($permission, $arguments = [])
    {
        return !$this->can($permission, $arguments);
    }

    /**
     * Check if user has any of the given permissions
     */
    public function canAny($permissions)
    {
        return $this->hasAnyPermission($permissions);
    }

    /**
     * Check if user has all of the given permissions
     */
    public function canAll($permissions)
    {
        return $this->hasAllPermissions($permissions);
    }

    /**
     * Authorize user for a permission
     */
    public function authorize($permission)
    {
        if (!$this->hasPermission($permission)) {
            abort(403, 'Unauthorized action.');
        }
        
        return true;
    }

    /**
     * Authorize user for any of the given permissions
     */
    public function authorizeAny($permissions)
    {
        if (!$this->hasAnyPermission($permissions)) {
            abort(403, 'Unauthorized action.');
        }
        
        return true;
    }

    /**
     * Authorize user for all of the given permissions
     */
    public function authorizeAll($permissions)
    {
        if (!$this->hasAllPermissions($permissions)) {
            abort(403, 'Unauthorized action.');
        }
        
        return true;
    }

    /**
     * Get all permission slugs
     */
    public function getPermissionSlugs()
    {
        return $this->getAllPermissions()->pluck('slug')->toArray();
    }

    /**
     * Get permission groups
     */
    public function getPermissionGroups()
    {
        return $this->getAllPermissions()
            ->groupBy('group')
            ->map(function ($permissions) {
                return $permissions->pluck('name', 'slug')->toArray();
            })
            ->toArray();
    }

    /**
     * Check if user has higher level than given role
     */
    public function hasHigherLevelThan($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->orWhere('slug', $role)->first();
        }
        
        if (!$role) {
            return false;
        }
        
        return $this->getPermissionLevel() > $role->level;
    }

    /**
     * Check if user has lower level than given role
     */
    public function hasLowerLevelThan($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->orWhere('slug', $role)->first();
        }
        
        if (!$role) {
            return false;
        }
        
        return $this->getPermissionLevel() < $role->level;
    }

    /**
     * Get users with same permissions
     */
    public function getUsersWithSamePermissions()
    {
        $roleIds = $this->roles->pluck('id')->toArray();
        $permissionIds = $this->permissions->pluck('id')->toArray();
        
        return self::whereHas('roles', function ($q) use ($roleIds) {
            $q->whereIn('id', $roleIds);
        })->orWhereHas('permissions', function ($q) use ($permissionIds) {
            $q->whereIn('id', $permissionIds);
        })->where('id', '!=', $this->id)->get();
    }

    /**
     * Check if user is allowed to manage another user
     */
    public function canManageUser($user)
    {
        // Can't manage yourself
        if ($this->id === $user->id) {
            return false;
        }
        
        // Super admin can manage anyone
        if ($this->isSuperAdmin()) {
            return true;
        }
        
        // Admin can manage staff and customers
        if ($this->isAdmin() && !$user->isAdmin()) {
            return true;
        }
        
        // Staff can only manage customers
        if ($this->isStaff() && $user->hasRole('customer')) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if user can access admin panel
     */
    public function canAccessAdmin()
    {
        return $this->isStaff();
    }

    /**
     * Check if user can view reports
     */
    public function canViewReports()
    {
        return $this->hasAnyPermission(['view-reports', 'manage-reports']) || $this->isAdmin();
    }

    /**
     * Check if user can manage products
     */
    public function canManageProducts()
    {
        return $this->hasAnyPermission(['manage-products', 'edit-products', 'create-products', 'delete-products']) || $this->isAdmin();
    }

    /**
     * Check if user can manage orders
     */
    public function canManageOrders()
    {
        return $this->hasAnyPermission(['manage-orders', 'edit-orders', 'update-order-status']) || $this->isAdmin();
    }

    /**
     * Check if user can manage users
     */
    public function canManageUsers()
    {
        return $this->hasAnyPermission(['manage-users', 'edit-users', 'create-users', 'delete-users']) || $this->isAdmin();
    }

    /**
     * Check if user can manage settings
     */
    public function canManageSettings()
    {
        return $this->hasAnyPermission(['manage-settings', 'edit-settings']) || $this->isAdmin();
    }

    /**
     * Check if user can manage coupons
     */
    public function canManageCoupons()
    {
        return $this->hasAnyPermission(['manage-coupons', 'edit-coupons', 'create-coupons']) || $this->isAdmin();
    }

    /**
     * Check if user can manage banners
     */
    public function canManageBanners()
    {
        return $this->hasAnyPermission(['manage-banners', 'edit-banners', 'create-banners']) || $this->isAdmin();
    }

    /**
     * Check if user can manage couriers
     */
    public function canManageCouriers()
    {
        return $this->hasAnyPermission(['manage-couriers', 'edit-couriers']) || $this->isAdmin();
    }
}