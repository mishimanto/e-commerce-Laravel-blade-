<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = [
        'name', 
        'slug', 
        'description', 
        'level'
    ];

    protected $casts = [
        'level' => 'integer'
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function hasPermission($permission): bool
    {
        return $this->permissions()->where('slug', $permission)->exists();
    }
}