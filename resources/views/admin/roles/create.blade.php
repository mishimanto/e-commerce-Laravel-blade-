@extends('layouts.admin')

@section('title', 'Add New Role')

@section('content')
<div class="">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Add New Role</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Role Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Role Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                        placeholder="e.g., Editor, Manager, Support">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <input type="text" name="description" id="description" value="{{ old('description') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Brief description of this role">
                </div>
            </div>

            {{-- Permissions Section --}}
            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Role Permissions</h3>
                <p class="text-sm text-gray-600 mb-4">Select the permissions for this role.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($permissions->groupBy('group') as $group => $groupPermissions)
                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2 capitalize">{{ $group ?: 'General' }}</h4>
                            <div class="space-y-2 max-h-60 overflow-y-auto">
                                @foreach($groupPermissions as $permission)
                                    <label class="flex items-start">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                            class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">
                                            {{ $permission->name }}
                                            @if($permission->description)
                                                <br><span class="text-xs text-gray-500">{{ $permission->description }}</span>
                                            @endif
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Create Role
                </button>
            </div>
        </form>
    </div>
</div>
@endsection