@extends('layouts.admin')

@section('title', 'Create Coupon')

@section('content')
<div class="container-fluid px-4 py-8 mx-auto">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Create New Coupon</h1>
        <a href="{{ route('admin.coupons.index') }}" 
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to List
        </a>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <form action="{{ route('admin.coupons.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Coupon Code -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Coupon Code</label>
                        <div class="flex mt-1 rounded-md shadow-sm">
                            <input type="text" name="code" 
                                   class="flex-1 block w-full rounded-l-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('code') border-red-500 @enderror" 
                                   value="{{ old('code') }}" 
                                   placeholder="AUTO-GENERATE">
                            <button type="button" 
                                    onclick="generateCode()"
                                    class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate</p>
                        @error('code')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Coupon Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Coupon Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" 
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('name') border-red-500 @enderror" 
                               value="{{ old('name') }}" 
                               required>
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" rows="2" 
                                  class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Discount Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Discount Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type" 
                                class="block w-full mt-1 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md @error('type') border-red-500 @enderror" 
                                required>
                            <option value="">Select Type</option>
                            <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                            <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                            <option value="free_shipping" {{ old('type') == 'free_shipping' ? 'selected' : '' }}>Free Shipping</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Discount Value -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Discount Value <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.01" name="value" 
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('value') border-red-500 @enderror" 
                               value="{{ old('value') }}" 
                               required>
                        @error('value')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Max Discount Amount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Max Discount Amount (৳)</label>
                        <input type="number" step="0.01" name="max_discount_amount" 
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('max_discount_amount') border-red-500 @enderror" 
                               value="{{ old('max_discount_amount') }}">
                        <p class="mt-1 text-xs text-gray-500">For percentage discounts</p>
                        @error('max_discount_amount')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Minimum Order Amount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Minimum Order Amount (৳)</label>
                        <input type="number" step="0.01" name="min_order_amount" 
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('min_order_amount') border-red-500 @enderror" 
                               value="{{ old('min_order_amount') }}">
                        @error('min_order_amount')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Usage Limit -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Usage Limit</label>
                        <input type="number" name="usage_limit" 
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('usage_limit') border-red-500 @enderror" 
                               value="{{ old('usage_limit') }}" 
                               placeholder="Unlimited">
                        <p class="mt-1 text-xs text-gray-500">Maximum number of times this coupon can be used</p>
                        @error('usage_limit')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Usage Per User -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Usage Per User</label>
                        <input type="number" name="usage_per_user" 
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('usage_per_user') border-red-500 @enderror" 
                               value="{{ old('usage_per_user') }}" 
                               placeholder="Unlimited">
                        <p class="mt-1 text-xs text-gray-500">How many times a single user can use this coupon</p>
                        @error('usage_per_user')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="starts_at" 
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('starts_at') border-red-500 @enderror" 
                               value="{{ old('starts_at', now()->format('Y-m-d\TH:i')) }}" 
                               required>
                        @error('starts_at')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Expiry Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Expiry Date <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="expires_at" 
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('expires_at') border-red-500 @enderror" 
                               value="{{ old('expires_at', now()->addDays(30)->format('Y-m-d\TH:i')) }}" 
                               required>
                        @error('expires_at')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Active Status -->
                    <div class="md:col-span-2">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="is_active" value="1" 
                                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                       id="isActive" {{ old('is_active', true) ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="isActive" class="font-medium text-gray-700">Active</label>
                            </div>
                        </div>
                    </div>

                    <!-- Restrictions Section -->
                    <div class="md:col-span-2">
                        <hr class="my-6">
                        <h3 class="text-lg font-medium text-gray-900">Restrictions (Optional)</h3>
                    </div>

                    <!-- Applicable Products -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Applicable Products</label>
                        <select name="applicable_products[]" multiple 
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm select2">
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                    {{ in_array($product->id, old('applicable_products', [])) ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ $product->sku }})
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Leave empty to apply to all products</p>
                    </div>

                    <!-- Excluded Products -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Excluded Products</label>
                        <select name="excluded_products[]" multiple 
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm select2">
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                    {{ in_array($product->id, old('excluded_products', [])) ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ $product->sku }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Applicable Categories -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Applicable Categories</label>
                        <select name="applicable_categories[]" multiple 
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm select2">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ in_array($category->id, old('applicable_categories', [])) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Excluded Categories -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Excluded Categories</label>
                        <select name="excluded_categories[]" multiple 
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm select2">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ in_array($category->id, old('excluded_categories', [])) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end mt-6 space-x-3">
                    <a href="{{ route('admin.coupons.index') }}" 
                       class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Create Coupon
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple {
        border-color: #d1d5db;
        border-radius: 0.375rem;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #6366f1;
        outline: 0;
        box-shadow: 0 0 0 1px #6366f1;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: 'Select options',
        allowClear: true,
        width: '100%'
    });
});

function generateCode() {
    fetch('{{ route("admin.coupons.generate") }}')
        .then(response => response.json())
        .then(data => {
            document.querySelector('input[name="code"]').value = data.code;
        });
}
</script>
@endpush
@endsection