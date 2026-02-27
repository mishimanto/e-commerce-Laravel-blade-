@extends('layouts.admin')

@section('title', 'Add New Courier')

@section('content')
<div class="">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Add New Courier</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.couriers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Courier Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Code --}}
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Courier Code *</label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('code') border-red-500 @enderror"
                        placeholder="e.g., DHL, FEDEX, SUNDARBAN">
                    @error('code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Logo --}}
                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                    <input type="file" name="logo" id="logo" accept="image/*"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('logo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- API URL --}}
                <div>
                    <label for="api_url" class="block text-sm font-medium text-gray-700 mb-2">API URL</label>
                    <input type="url" name="api_url" id="api_url" value="{{ old('api_url') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- API Key --}}
                <div>
                    <label for="api_key" class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                    <input type="text" name="api_key" id="api_key" value="{{ old('api_key') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- API Secret --}}
                <div>
                    <label for="api_secret" class="block text-sm font-medium text-gray-700 mb-2">API Secret</label>
                    <input type="text" name="api_secret" id="api_secret" value="{{ old('api_secret') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Username --}}
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" id="password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>

                {{-- Settings --}}
                <div class="md:col-span-2">
                    <label for="settings" class="block text-sm font-medium text-gray-700 mb-2">Settings (JSON)</label>
                    <textarea name="settings" id="settings" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm">{{ old('settings', '{}') }}</textarea>
                </div>

                {{-- Sandbox Mode --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sandbox Mode</label>
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="sandbox_mode" value="1" {{ old('sandbox_mode', '1') == '1' ? 'checked' : '' }} class="form-radio text-blue-600">
                            <span class="ml-2">Yes</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="sandbox_mode" value="0" {{ old('sandbox_mode') == '0' ? 'checked' : '' }} class="form-radio text-blue-600">
                            <span class="ml-2">No</span>
                        </label>
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="status" value="1" {{ old('status', '1') == '1' ? 'checked' : '' }} class="form-radio text-blue-600">
                            <span class="ml-2">Active</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="status" value="0" {{ old('status') == '0' ? 'checked' : '' }} class="form-radio text-blue-600">
                            <span class="ml-2">Inactive</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('admin.couriers.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Create Courier
                </button>
            </div>
        </form>
    </div>
</div>
@endsection