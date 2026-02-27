<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('can:manage-users'); // Super Admin only
    // }

    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $roles = Role::withCount('users')->orderBy('id')->paginate(15);
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::orderBy('group')->orderBy('name')->get();
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            // Create role
            $role = Role::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
            ]);

            // Attach permissions
            if ($request->has('permissions')) {
                $role->permissions()->attach($request->permissions);
            }

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role created successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error creating role: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        // Prevent editing Super Admin role (ID 1)
        if ($role->id == 1) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Super Admin role cannot be edited.');
        }

        $permissions = Permission::orderBy('group')->orderBy('name')->get();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        // Prevent editing Super Admin role (ID 1)
        if ($role->id == 1) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Super Admin role cannot be edited.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            // Update role
            $role->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
            ]);

            // Sync permissions
            $role->permissions()->sync($request->permissions ?? []);

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating role: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        // Prevent deleting Super Admin role (ID 1)
        if ($role->id == 1) {
            return back()->with('error', 'Super Admin role cannot be deleted.');
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role with assigned users.');
        }

        try {
            $role->permissions()->detach();
            $role->delete();

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting role: ' . $e->getMessage());
        }
    }
}