<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use App\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'is_active',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function notifications()
    {
        return $this->morphMany(\Illuminate\Notifications\DatabaseNotification::class, 'notifiable')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Get the entity's unread notifications.
     */
    public function unreadNotifications()
    {
        return $this->morphMany(\Illuminate\Notifications\DatabaseNotification::class, 'notifiable')
                    ->whereNull('read_at');
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function compares()
    {
        return $this->hasMany(Compare::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function recentlyViewed()
    {
        return $this->hasMany(RecentlyViewed::class);
    }

    /**
     * Roles relationship (already in HasRoles trait, but defined here for clarity)
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Role Helpers
    |--------------------------------------------------------------------------
    */

    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)
                ->orWhere('name', $role)
                ->first();
        }

        if (!$role) return $this;

        if (!$this->roles()->where('role_id', $role->id)->exists()) {
            $this->roles()->attach($role->id);
        }

        return $this;
    }

    public function removeRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)
                ->orWhere('name', $role)
                ->first();
        }

        if ($role) {
            $this->roles()->detach($role->id);
        }

        return $this;
    }

    public function syncRoles($roles)
    {
        $roleIds = [];

        foreach ($roles as $role) {
            if (is_string($role)) {
                $role = Role::where('slug', $role)
                    ->orWhere('name', $role)
                    ->first();
            }

            if ($role) {
                $roleIds[] = $role->id;
            }
        }

        $this->roles()->sync($roleIds);

        return $this;
    }

    public function hasRole($role)
    {
        // If roles relationship isn't loaded, load it
        if (!$this->relationLoaded('roles')) {
            $this->load('roles');
        }
        
        if (is_string($role)) {
            return $this->roles->contains(function ($value) use ($role) {
                return $value->slug === $role || 
                    $value->name === $role || 
                    $value->id == $role;
            });
        }
        
        if (is_array($role)) {
            foreach ($role as $r) {
                if ($this->hasRole($r)) {
                    return true;
                }
            }
            return false;
        }
        
        return $this->roles->contains('id', $role->id);
    }

    /**
     * Check if user is admin (super-admin or admin)
     */
    public function isAdmin()
    {
        return $this->hasRole('super-admin') || $this->hasRole('admin');
    }

    /**
     * Check if user is staff (super-admin, admin, or staff)
     */
    public function isStaff()
    {
        return $this->hasRole('super-admin') || $this->hasRole('admin') || $this->hasRole('staff');
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin()
    {
        return $this->hasRole('super-admin');
    }
}