<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $user->load('roles');
        
        
        if (count($roles) === 1 && str_contains($roles[0], '|')) {
            
            $roles = explode('|', $roles[0]);
        }
        
        // Log::info('Processed roles: ', $roles);

        foreach ($roles as $role) {
            if ($user->hasRole(trim($role))) {
                return $next($request);
            }
        }

        // if (app()->environment('local')) {
        //     dd([
        //         'user_id' => $user->id,
        //         'user_email' => $user->email,
        //         'user_roles' => $user->roles->toArray(),
        //         'required_roles' => $roles,
        //         'has_super_admin' => $user->hasRole('super-admin'),
        //         'has_admin' => $user->hasRole('admin'),
        //         'has_staff' => $user->hasRole('staff'),
        //     ]);
        // }
        
        abort(403, 'Unauthorized access. You do not have the required role.');
    }
}