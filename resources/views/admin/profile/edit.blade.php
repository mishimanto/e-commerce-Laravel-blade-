{{-- resources/views/admin/profile/edit.blade.php --}}

@extends('layouts.guest-admin')

@section('title', 'Admin Profile Settings')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mx-auto">
        <div class="bg-white rounded-lg overflow-hidden">
            {{-- Header with Avatar --}}
            <!-- <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" 
                                 alt="Avatar" 
                                 class="w-16 h-16 rounded-full border-2 border-white object-cover">
                        @else
                            <div class="w-16 h-16 rounded-full bg-white text-blue-600 flex items-center justify-center">
                                <span class="text-2xl font-bold">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
                        <p class="text-blue-100">{{ $user->email }}</p>
                    </div>
                </div>
            </div> -->

            {{-- Tab Navigation --}}
            <div x-data="{ activeTab: 'profile' }" class="p-6">
                <div class="border-b border-gray-200 mb-6">
                    <nav class="flex -mb-px space-x-8">
                        <button @click="activeTab = 'profile'" 
                                class="py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
                                :class="activeTab === 'profile' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Profile Information
                        </button>
                        
                        <button @click="activeTab = 'password'" 
                                class="py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
                                :class="activeTab === 'password' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Change Password
                        </button>
                        
                        <button @click="activeTab = 'avatar'" 
                                class="py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
                                :class="activeTab === 'avatar' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Profile Avatar
                        </button>
                    </nav>
                </div>

                {{-- Tab Contents --}}
                <div>
                    {{-- Profile Information Tab --}}
                    <div x-show="activeTab === 'profile'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                        <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                                    <input type="text" value="{{ ucfirst($user->roles->first()->name ?? 'Admin') }}" 
                                           class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg cursor-not-allowed" 
                                           disabled>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4">
                                <button type="submit" 
                                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Update Profile
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Change Password Tab --}}
                    <div x-show="activeTab === 'password'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                        <form action="{{ route('admin.profile.password') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                    <input type="password" name="current_password" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                           placeholder="Enter your current password">
                                    @error('current_password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                    <input type="password" name="new_password" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            placeholder="Enter new password">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                    <input type="password" name="new_password_confirmation" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            placeholder="Confirm new password">
                                </div>
                               
                                @error('new_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                {{-- Password Strength Meter --}}
                                <div class="mt-2">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-16 h-2 bg-gray-200 rounded-full overflow-hidden">
                                            <div id="password-strength" class="h-full transition-all duration-300" style="width: 0%"></div>
                                        </div>
                                        <span id="password-strength-text" class="text-xs text-gray-500">Enter password</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4">
                                <button type="submit" 
                                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Avatar Tab --}}
                    <div x-show="activeTab === 'avatar'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                        <div class="space-y-6">
                            {{-- Current Avatar Display --}}
                            <div class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg">
                                <div class="relative group">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" 
                                             alt="Avatar" 
                                             class="w-32 h-32 rounded-full border-4 border-white shadow-lg object-cover">
                                    @else
                                        <div class="w-32 h-32 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 text-white flex items-center justify-center shadow-lg">
                                            <span class="text-4xl font-bold">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    
                                    {{-- Upload Overlay --}}
                                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 cursor-pointer"
                                         onclick="document.getElementById('avatar-input').click()">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="mt-4 text-sm text-gray-600">Click on the image to upload new avatar</p>
                            </div>

                            {{-- Upload Form --}}
                            <form id="avatar-form" action="{{ route('admin.profile.avatar') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <input type="file" id="avatar-input" name="avatar" accept="image/*" class="hidden" onchange="document.getElementById('avatar-form').submit();">
                            </form>

                            {{-- Remove Avatar Button --}}
                            @if($user->avatar)
                                <div class="flex justify-center">
                                    <button type="button" 
                                            onclick="if(confirm('Are you sure you want to remove your avatar?')) document.getElementById('delete-avatar-form').submit();"
                                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Remove Avatar
                                    </button>
                                </div>
                            @endif

                            {{-- Delete Avatar Form --}}
                            @if($user->avatar)
                                <form id="delete-avatar-form" action="{{ route('admin.profile.avatar.destroy') }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Password Strength Meter
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.querySelector('input[name="new_password"]');
        const strengthBar = document.getElementById('password-strength');
        const strengthText = document.getElementById('password-strength-text');

        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                let strengthLabel = '';

                if (password.length === 0) {
                    strength = 0;
                    strengthLabel = 'Enter password';
                } else if (password.length < 6) {
                    strength = 20;
                    strengthLabel = 'Too short';
                } else {
                    // Check for uppercase letters
                    if (/[A-Z]/.test(password)) strength += 20;
                    // Check for lowercase letters
                    if (/[a-z]/.test(password)) strength += 20;
                    // Check for numbers
                    if (/[0-9]/.test(password)) strength += 20;
                    // Check for special characters
                    if (/[^A-Za-z0-9]/.test(password)) strength += 20;
                    // Check length
                    if (password.length >= 8) strength += 20;

                    if (strength <= 40) {
                        strengthLabel = 'Weak';
                        strengthBar.className = 'h-full bg-red-500 transition-all duration-300';
                    } else if (strength <= 60) {
                        strengthLabel = 'Fair';
                        strengthBar.className = 'h-full bg-yellow-500 transition-all duration-300';
                    } else if (strength <= 80) {
                        strengthLabel = 'Good';
                        strengthBar.className = 'h-full bg-blue-500 transition-all duration-300';
                    } else {
                        strengthLabel = 'Strong';
                        strengthBar.className = 'h-full bg-green-500 transition-all duration-300';
                    }
                }

                strengthBar.style.width = strength + '%';
                strengthText.textContent = strengthLabel;
            });
        }
    });
</script>
@endpush