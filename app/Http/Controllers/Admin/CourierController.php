<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourierController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    //     $this->middleware('verified');
    //     $this->middleware('role:super-admin,admin,staff');
    // }

    /**
     * Display a listing of the couriers.
     */
    public function index()
    {
        $couriers = Courier::orderBy('name', 'asc')->paginate(15);
        return view('admin.couriers.index', compact('couriers'));
    }

    /**
     * Show the form for creating a new courier.
     */
    public function create()
    {
        return view('admin.couriers.create');
    }

    /**
     * Store a newly created courier in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:couriers',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'description' => 'nullable|string',
            'api_url' => 'nullable|url|max:255',
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'sandbox_mode' => 'required|in:0,1',
            'status' => 'required|in:0,1',
            'settings' => 'nullable|json',
        ]);

        $data = $request->except('logo', 'password', 'settings');
        
        // Handle password (encrypt if provided)
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        // Handle settings JSON
        if ($request->filled('settings')) {
            $data['settings'] = json_decode($request->settings, true);
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('couriers', 'public');
            $data['logo'] = $path;
        }

        Courier::create($data);

        return redirect()->route('admin.couriers.index')
            ->with('success', 'Courier created successfully.');
    }

    /**
     * Display the specified courier.
     */
    public function show(Courier $courier)
    {
        return view('admin.couriers.show', compact('courier'));
    }

    /**
     * Show the form for editing the specified courier.
     */
    public function edit(Courier $courier)
    {
        return view('admin.couriers.edit', compact('courier'));
    }

    /**
     * Update the specified courier in storage.
     */
    public function update(Request $request, Courier $courier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:couriers,code,' . $courier->id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'description' => 'nullable|string',
            'api_url' => 'nullable|url|max:255',
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'sandbox_mode' => 'required|in:0,1',
            'status' => 'required|in:0,1',
            'settings' => 'nullable|json',
        ]);

        $data = $request->except('logo', 'password', 'settings');

        // Handle password (update only if provided)
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        // Handle settings JSON
        if ($request->filled('settings')) {
            $data['settings'] = json_decode($request->settings, true);
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($courier->logo) {
                Storage::disk('public')->delete($courier->logo);
            }
            
            $path = $request->file('logo')->store('couriers', 'public');
            $data['logo'] = $path;
        }

        $courier->update($data);

        return redirect()->route('admin.couriers.index')
            ->with('success', 'Courier updated successfully.');
    }

    /**
     * Remove the specified courier from storage.
     */
    public function destroy(Courier $courier)
    {
        // Delete logo if exists
        if ($courier->logo) {
            Storage::disk('public')->delete($courier->logo);
        }

        $courier->delete();

        return redirect()->route('admin.couriers.index')
            ->with('success', 'Courier deleted successfully.');
    }
}