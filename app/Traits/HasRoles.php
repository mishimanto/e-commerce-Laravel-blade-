<?php

namespace App\Traits;

use App\Models\Role;
use App\Models\Permission;

trait HasRoles
{
    /**
     * Boot the trait
     */
    public static function bootHasRoles()
    {
        static::deleting(function ($model) {
            if (method_exists($model, 'isForceDeleting') && !$model->isForceDeleting()) {
                return;
            }
            $model->roles()->detach();
            $model->permissions()->detach();
        });
    }

    /**
     * Get all roles for the user
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * Get all permissions for the user (direct + through roles)
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user');
    }

    /**
     * Assign a role to the user
     */
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->orWhere('slug', $role)->firstOrFail();
        }
        
        $this->roles()->syncWithoutDetaching([$role->id]);
        
        return $this;
    }

    /**
     * Remove a role from the user
     */
    public function removeRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->orWhere('slug', $role)->firstOrFail();
        }
        
        $this->roles()->detach($role->id);
        
        return $this;
    }

    /**
     * Sync roles for the user
     */
    public function syncRoles($roles)
    {
        $roleIds = collect($roles)->map(function ($role) {
            if (is_numeric($role)) {
                return $role;
            }
            
            if (is_string($role)) {
                return Role::where('name', $role)->orWhere('slug', $role)->firstOrFail()->id;
            }
            
            return $role->id;
        })->toArray();
        
        $this->roles()->sync($roleIds);
        
        return $this;
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role) || 
                   $this->roles->contains('slug', $role);
        }
        
        if (is_numeric($role)) {
            return $this->roles->contains('id', $role);
        }
        
        return $this->roles->contains('id', $role->id);
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole($roles)
    {
        return collect($roles)->contains(function ($role) {
            return $this->hasRole($role);
        });
    }

    /**
     * Check if user has all of the given roles
     */
    public function hasAllRoles($roles)
    {
        return collect($roles)->every(function ($role) {
            return $this->hasRole($role);
        });
    }

    /**
     * Give a permission directly to the user
     */
    public function givePermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)
                ->orWhere('slug', $permission)
                ->firstOrFail();
        }
        
        $this->permissions()->syncWithoutDetaching([$permission->id]);
        
        return $this;
    }

    /**
     * Remove a direct permission from the user
     */
    public function revokePermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)
                ->orWhere('slug', $permission)
                ->firstOrFail();
        }
        
        $this->permissions()->detach($permission->id);
        
        return $this;
    }

    /**
     * Sync permissions for the user
     */
    public function syncPermissions($permissions)
    {
        $permissionIds = collect($permissions)->map(function ($permission) {
            if (is_numeric($permission)) {
                return $permission;
            }
            
            if (is_string($permission)) {
                return Permission::where('name', $permission)
                    ->orWhere('slug', $permission)
                    ->firstOrFail()
                    ->id;
            }
            
            return $permission->id;
        })->toArray();
        
        $this->permissions()->sync($permissionIds);
        
        return $this;
    }

    /**
     * Check if user has a specific permission (direct or through roles)
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            // Check direct permissions
            if ($this->permissions->contains('name', $permission) || 
                $this->permissions->contains('slug', $permission)) {
                return true;
            }
            
            // Check through roles
            foreach ($this->roles as $role) {
                if ($role->permissions->contains('name', $permission) || 
                    $role->permissions->contains('slug', $permission)) {
                    return true;
                }
            }
            
            return false;
        }
        
        if (is_numeric($permission)) {
            // Check direct permissions
            if ($this->permissions->contains('id', $permission)) {
                return true;
            }
            
            // Check through roles
            foreach ($this->roles as $role) {
                if ($role->permissions->contains('id', $permission)) {
                    return true;
                }
            }
            
            return false;
        }
        
        // Check direct permissions
        if ($this->permissions->contains('id', $permission->id)) {
            return true;
        }
        
        // Check through roles
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('id', $permission->id)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission($permissions)
    {
        return collect($permissions)->contains(function ($permission) {
            return $this->hasPermission($permission);
        });
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions($permissions)
    {
        return collect($permissions)->every(function ($permission) {
            return $this->hasPermission($permission);
        });
    }

    /**
     * Get all permissions (direct + through roles)
     */
    public function getAllPermissions()
    {
        $permissions = $this->permissions->keyBy('id');
        
        foreach ($this->roles as $role) {
            $permissions = $permissions->merge($role->permissions->keyBy('id'));
        }
        
        return $permissions->unique('id')->values();
    }

    /**
     * Get permission level (highest role level)
     */
    public function getPermissionLevel()
    {
        return $this->roles->max('level') ?? 0;
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin()
    {
        return $this->hasRole('super-admin');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->hasAnyRole(['super-admin', 'admin']);
    }

    /**
     * Check if user is staff
     */
    public function isStaff()
    {
        return $this->hasAnyRole(['super-admin', 'admin', 'staff']);
    }

    /**
     * Get role names as string
     */
    public function getRoleNamesAttribute()
    {
        return $this->roles->pluck('name')->implode(', ');
    }

    /**
     * Get permission names as string
     */
    public function getPermissionNamesAttribute()
    {
        return $this->getAllPermissions()->pluck('name')->implode(', ');
    }

    /**
     * Scope query to users with specific role
     */
    public function scopeRole($query, $role)
    {
        if (is_string($role)) {
            return $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role)->orWhere('slug', $role);
            });
        }
        
        if (is_array($role)) {
            return $query->whereHas('roles', function ($q) use ($role) {
                $q->whereIn('name', $role)->orWhereIn('slug', $role);
            });
        }
        
        return $query->whereHas('roles', function ($q) use ($role) {
            $q->where('id', $role->id);
        });
    }

    /**
     * Scope query to users with specific permission
     */
    public function scopePermission($query, $permission)
    {
        if (is_string($permission)) {
            return $query->where(function ($q) use ($permission) {
                $q->whereHas('permissions', function ($permQuery) use ($permission) {
                    $permQuery->where('name', $permission)->orWhere('slug', $permission);
                })->orWhereHas('roles.permissions', function ($permQuery) use ($permission) {
                    $permQuery->where('name', $permission)->orWhere('slug', $permission);
                });
            });
        }
        
        return $query->whereHas('permissions', function ($q) use ($permission) {
            $q->where('id', $permission->id);
        })->orWhereHas('roles.permissions', function ($q) use ($permission) {
            $q->where('id', $permission->id);
        });
    }
}