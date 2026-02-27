<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('can:manage-users'); // Adjust based on your permission system
    // }

    /**
     * Display a listing of staff members.
     */
    public function index()
    {
        $staff = User::with(['roles', 'permissions'])
            ->whereHas('roles', function($query) {
                $query->whereIn('role_id', [2, 3]); // Admin and Staff roles
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.staff.index', compact('staff'));
    }

    /**
     * Show the form for creating a new staff member.
     */
    public function create()
    {
        $roles = Role::whereIn('id', [2, 3])->get(); // Admin and Staff roles
        $permissions = Permission::orderBy('group')->orderBy('name')->get();

        return view('admin.staff.create', compact('roles', 'permissions'));
    }

    /**
     * Store a newly created staff member.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|exists:roles,id',
            'is_active' => 'required|in:0,1',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'is_active' => $request->is_active,
            ]);

            // Assign role
            $user->roles()->attach($request->role);

            // Assign direct permissions if any
            if ($request->has('permissions')) {
                $user->permissions()->attach($request->permissions);
            }

            DB::commit();

            return redirect()->route('admin.staff.index')
                ->with('success', 'Staff member created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating staff member: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified staff member.
     */
    public function edit(User $staff)
    {
        // Ensure we're only editing staff/admin
        if (!$staff->roles()->whereIn('role_id', [2, 3])->exists()) {
            abort(403, 'You can only edit staff or admin users.');
        }

        $roles = Role::whereIn('id', [2, 3])->get();
        $permissions = Permission::orderBy('group')->orderBy('name')->get();
        
        $user = $staff->load(['roles', 'permissions']);

        return view('admin.staff.edit', compact('user', 'roles', 'permissions'));
    }

    /**
     * Update the specified staff member.
     */
    public function update(Request $request, User $staff)
    {
        // Ensure we're only updating staff/admin
        if (!$staff->roles()->whereIn('role_id', [2, 3])->exists()) {
            abort(403, 'You can only edit staff or admin users.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $staff->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|exists:roles,id',
            'is_active' => 'required|in:0,1',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            DB::beginTransaction();

            // Update user
            $staff->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'is_active' => $request->is_active,
            ]);

            // Update password if provided
            if ($request->filled('password')) {
                $staff->update([
                    'password' => Hash::make($request->password)
                ]);
            }

            // Sync role (detach all and attach new)
            $staff->roles()->sync([$request->role]);

            // Sync permissions
            $staff->permissions()->sync($request->permissions ?? []);

            DB::commit();

            return redirect()->route('admin.staff.index')
                ->with('success', 'Staff member updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating staff member: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified staff member.
     */
    public function destroy(User $staff)
    {
        // Prevent deleting yourself
        if ($staff->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Ensure we're only deleting staff/admin
        if (!$staff->roles()->whereIn('role_id', [2, 3])->exists()) {
            abort(403, 'You can only delete staff or admin users.');
        }

        try {
            DB::beginTransaction();
            
            // Detach roles and permissions
            $staff->roles()->detach();
            $staff->permissions()->detach();
            
            // Delete the user
            $staff->delete();

            DB::commit();

            return redirect()->route('admin.staff.index')
                ->with('success', 'Staff member deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting staff member: ' . $e->getMessage());
        }
    }
}