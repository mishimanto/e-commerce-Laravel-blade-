<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function dashboard()
    {
        $user = auth()->user();
        
        // Total orders count
        $totalOrders = $user->orders()->count();
        
        // Recent orders (last 5)
        $recentOrders = $user->orders()->with('items')->latest()->limit(5)->get();
        
        // Wishlist count
        $wishlistCount = $user->wishlist()->count();
        
        // Addresses count
        $addressesCount = $user->addresses()->count();
        
        return view('storefront.profile.dashboard', compact(
            'user', 
            'recentOrders', 
            'totalOrders',
            'wishlistCount', 
            'addressesCount'
        ));
    }

    public function orders()
    {
        $orders = auth()->user()
            ->orders()
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('storefront.profile.orders', compact('orders'));
    }

    public function showOrder(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('storefront.profile.order-show', compact('order'));
    }
    

    public function wishlist()
    {
        $wishlistItems = auth()->user()->wishlist()->with('product.images')->get();

        return view('storefront.profile.wishlist', compact('wishlistItems'));
    }


    public function addresses()
    {
        $addresses = auth()->user()->addresses()->orderBy('is_default', 'desc')->get();

        return view('storefront.profile.addresses', compact('addresses'));
    }

    public function editAddress(Address $address)
    {
        $this->authorize('update', $address);
        
        return response()->json($address);
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean'
        ]);

        $address = auth()->user()->addresses()->create($request->all());

        if ($request->boolean('is_default')) {
            auth()->user()->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        return redirect()->route('profile.addresses')->with('success', 'Address added successfully.');
    }

    public function updateAddress(Request $request, Address $address)
    {
        $this->authorize('update', $address);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean'
        ]);

        $address->update($request->all());

        if ($request->boolean('is_default')) {
            auth()->user()->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        return redirect()->route('profile.addresses')->with('success', 'Address updated successfully.');
    }

    public function deleteAddress(Address $address)
    {
        $this->authorize('delete', $address);

        $address->delete();

        return redirect()->route('profile.addresses')->with('success', 'Address deleted successfully.');
    }

    public function setDefaultAddress(Address $address)
    {
        $this->authorize('update', $address);

        auth()->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return redirect()->route('profile.addresses')->with('success', 'Default address updated.');
    }

    public function settings()
    {
        $user = auth()->user();
        return view('storefront.profile.settings', compact('user'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
        ]);

        auth()->user()->update($request->only(['name', 'email', 'phone']));

        return redirect()->route('profile.settings')->with('success', 'Profile updated successfully.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = auth()->user();

        // Delete old avatar
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Upload new avatar
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return redirect()->route('profile.settings')->with('success', 'Avatar updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8|confirmed'
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('profile.settings')->with('success', 'Password updated successfully.');
    }

    public function show()
    {
        $user = auth()->user();
        return view('profile.show', compact('user'));
    }

    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
        ]);

        auth()->user()->update($request->only(['name', 'email', 'phone']));

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }

    public function password(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('profile.show')->with('success', 'Password updated successfully.');
    }

    public function avatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = auth()->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return redirect()->route('profile.show')->with('success', 'Avatar updated successfully.');
    }

    public function deleteAvatar()
    {
        $user = auth()->user();
        
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }
        
        return redirect()->route('profile.settings')->with('success', 'Avatar removed successfully.');
    }


    public function destroy(Request $request)
    {
        $user = auth()->user();
        
        // Delete user's data
        $user->addresses()->delete();
        $user->wishlist()->delete();
        $user->orders()->delete();
        
        // Logout and delete user
        Auth::logout();
        $user->delete();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('success', 'Your account has been deleted successfully.');
    }
}