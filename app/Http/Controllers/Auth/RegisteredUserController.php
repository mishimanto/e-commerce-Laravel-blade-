<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ]);

            $roleExists = DB::table('roles')->where('id', 4)->exists();
            
            if ($roleExists) {
                DB::table('role_user')->insert([
                    'role_id' => 4,
                    'user_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $smallestRoleId = DB::table('roles')->min('id');
                
                DB::table('role_user')->insert([
                    'role_id' => $smallestRoleId,
                    'user_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            event(new Registered($user));

            Auth::login($user);

            return redirect(route('profile.dashboard', absolute: false));

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }
}