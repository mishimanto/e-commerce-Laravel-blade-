<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Http\Requests\Admin\UserRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display list of users
     */
    public function index(Request $request)
    {
        $users = $this->userRepository->getFilteredUsers($request->all());
        $roles = $this->userRepository->getAllRoles(); 
        $statistics = $this->userRepository->getUserStatistics();

        return view('admin.users.index', compact('users', 'roles', 'statistics'));
    }

    /**
     * Display customers only
     */
    public function customers(Request $request)
    {
        $users = $this->userRepository->getCustomers($request->get('per_page', 15));
        $roles = $this->userRepository->getAllRoles();
        $statistics = $this->userRepository->getUserStatistics();

        return view('admin.users.index', compact('users', 'roles', 'statistics'));
    }

    /**
     * Display staff members
     */
    public function staff(Request $request)
    {
        $users = $this->userRepository->getStaff($request->get('per_page', 15));
        $roles = $this->userRepository->getAllRoles();
        $statistics = $this->userRepository->getUserStatistics();

        return view('admin.users.index', compact('users', 'roles', 'statistics'));
    }

    /**
     * Display admins only
     */
    public function admins(Request $request)
    {
        $users = $this->userRepository->getUsersByRole('admin', $request->get('per_page', 15));
        $roles = $this->userRepository->getAllRoles();
        $statistics = $this->userRepository->getUserStatistics();

        return view('admin.users.index', compact('users', 'roles', 'statistics'));
    }
    /**
     * Show create user form
     */
    public function create()
    {
        $roles = Role::where('name', '!=', 'super-admin')->get();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store new user
     */
    public function store(UserRequest $request)
    {
        $user = $this->userRepository->createUser($request->validated());

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show user details
     */
    public function show(User $user)
    {
        $user = $this->userRepository->getUserWithDetails($user->id);
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show edit user form
     */
    public function edit(User $user)
    {
        $roles = Role::where('name', '!=', 'super-admin')->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update user
     */
    public function update(UserRequest $request, User $user)
    {
        $this->userRepository->updateUser($user->id, $request->validated());

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        // Prevent deleting self
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account.');
        }

        // Check if user has orders
        if ($user->orders()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete user with order history.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(User $user)
    {
        // Prevent toggling own status
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change your own status.'
            ], 403);
        }

        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'success' => true,
            'status' => $user->is_active
        ]);
    }

    /**
     * Display customers only
     */
    // public function customers(Request $request)
    // {
    //     $users = $this->userRepository->getCustomers($request->get('per_page', 15));

    //     return view('admin.users.customers', compact('users'));
    // }

    /**
     * Display staff members
     */
    // public function staff()
    // {
    //     $staff = $this->userRepository->getStaff();

    //     return view('admin.users.staff', compact('staff'));
    // }

    /**
     * Impersonate user
     */
    public function impersonate(User $user)
    {
        // Prevent impersonating self
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot impersonate yourself.');
        }

        // Store original user ID in session
        session()->put('impersonate', auth()->id());
        
        // Login as the target user
        auth()->login($user);

        return redirect()->route('home')
            ->with('success', "You are now impersonating {$user->name}");
    }

    /**
     * Stop impersonating
     */
    public function stopImpersonate()
    {
        $originalId = session()->get('impersonate');
        
        if ($originalId) {
            auth()->loginUsingId($originalId);
            session()->forget('impersonate');
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'You have returned to your account.');
    }

    /**
     * Export users
     */
    public function export(Request $request)
    {
        $users = $this->userRepository->getFilteredUsers($request->all(), 1000);

        // Generate CSV
        $filename = "users-" . date('Y-m-d') . ".csv";
        $handle = fopen('php://temp', 'w+');

        // Add headers
        fputcsv($handle, [
            'Name',
            'Email',
            'Phone',
            'Role',
            'Orders Count',
            'Total Spent',
            'Joined Date',
            'Status'
        ]);

        // Add data
        foreach ($users as $user) {
            fputcsv($handle, [
                $user->name,
                $user->email,
                $user->phone ?? 'N/A',
                $user->roles->pluck('name')->implode(', '),
                $user->orders_count ?? 0,
                number_format($user->orders_sum_total ?? 0, 2),
                $user->created_at->format('Y-m-d'),
                $user->is_active ? 'Active' : 'Inactive'
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}