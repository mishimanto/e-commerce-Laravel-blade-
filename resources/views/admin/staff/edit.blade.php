@extends('layouts.admin')

@section('title', 'Edit Staff')

@section('content')
<div class="">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Edit Staff Member: {{ $user->name }}</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.staff.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required readonly
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required readonly
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" readonly
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- New Password (Optional) --}}
                <!-- <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password (leave blank to keep current)</label>
                    <input type="password" name="password" id="password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm New Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div> -->

                {{-- Role --}}
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                    <select name="role" id="role" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($roles as $role)
                            @if($role->id != 1) {{-- Prevent assigning Super Admin --}}
                                <option value="{{ $role->id }}" {{ $user->roles->first()->id == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="is_active" value="1" {{ old('is_active', $user->is_active) == '1' ? 'checked' : '' }} class="form-radio text-blue-600">
                            <span class="ml-2">Active</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="is_active" value="0" {{ old('is_active', $user->is_active) == '0' ? 'checked' : '' }} class="form-radio text-blue-600">
                            <span class="ml-2">Inactive</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Permissions Section --}}
            <!-- <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Direct Permissions</h3>
                <p class="text-sm text-gray-600 mb-4">Select additional permissions for this user beyond their role.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($permissions->groupBy('group') as $group => $groupPermissions)
                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2 capitalize">{{ $group }}</h4>
                            <div class="space-y-2">
                                @foreach($groupPermissions as $permission)
                                    @php
                                        $hasPermission = $user->permissions->contains($permission->id);
                                    @endphp
                                    <label class="flex items-center">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            {{ $hasPermission ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">{{ $permission->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div> -->

            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('admin.staff.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Update Staff Member
                </button>
            </div>
        </form>
    </div>
</div>
@endsection