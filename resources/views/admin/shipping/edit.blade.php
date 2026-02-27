@extends('layouts.admin')

@section('title', 'Edit Shipping Method')

@section('content')
<div class="">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Shipping Method: {{ $shippingMethod->name }}</h1>
            <!-- <p class="mt-2 text-sm text-gray-700">Update shipping method details and configuration.</p> -->
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.shipping.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    <div class="mt-8">
        <form action="{{ route('admin.shipping.update', $shippingMethod) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Basic Information</h3>
                        <!-- <p class="mt-1 text-sm text-gray-500">Basic details about the shipping method.</p> -->
                    </div>
                    <div class="mt-5 md:col-span-2 md:mt-0">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Code <span class="text-red-500">*</span></label>
                                <input type="text" name="code" value="{{ old('code', $shippingMethod->code) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('code') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                <p class="mt-2 text-sm text-gray-500">Unique identifier (e.g., standard, express)</p>
                                @error('code') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $shippingMethod->name) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-span-6">
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('description') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">{{ old('description', $shippingMethod->description) }}</textarea>
                                @error('description') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Cost <span class="text-red-500">*</span></label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">৳</span>
                                    </div>
                                    <input type="number" name="cost" value="{{ old('cost', $shippingMethod->cost) }}" step="0.01" required
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('cost') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror">
                                </div>
                                @error('cost') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Delivery Time</label>
                                <input type="text" name="delivery_time" value="{{ old('delivery_time', $shippingMethod->delivery_time) }}" placeholder="e.g., 3-5 business days"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('delivery_time') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('delivery_time') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Order Restrictions</h3>
                        <!-- <p class="mt-1 text-sm text-gray-500">Set minimum and maximum order amounts for this shipping method.</p> -->
                    </div>
                    <div class="mt-5 md:col-span-2 md:mt-0">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Minimum Order Amount</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">৳</span>
                                    </div>
                                    <input type="number" name="minimum_order_amount" value="{{ old('minimum_order_amount', $shippingMethod->minimum_order_amount) }}" step="0.01"
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('minimum_order_amount') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror">
                                </div>
                                @error('minimum_order_amount') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Maximum Order Amount</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">৳</span>
                                    </div>
                                    <input type="number" name="maximum_order_amount" value="{{ old('maximum_order_amount', $shippingMethod->maximum_order_amount) }}" step="0.01"
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('maximum_order_amount') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror">
                                </div>
                                @error('maximum_order_amount') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Free Shipping Configuration</h3>
                        <!-- <p class="mt-1 text-sm text-gray-500">Set up free shipping options.</p> -->
                    </div>
                    <div class="mt-5 md:col-span-2 md:mt-0">
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex h-5 items-center">
                                    <input type="checkbox" name="is_free_shipping" id="is_free_shipping" value="1" 
                                           {{ old('is_free_shipping', $shippingMethod->is_free_shipping) ? 'checked' : '' }} 
                                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="is_free_shipping" class="font-medium text-gray-700">Free Shipping</label>
                                    <p class="text-gray-500">Enable free shipping for this method</p>
                                </div>
                            </div>

                            <div id="free_shipping_threshold_group" class="grid grid-cols-6 gap-6" style="{{ old('is_free_shipping', $shippingMethod->is_free_shipping) ? '' : 'display: none;' }}">
                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Free Shipping Threshold</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">৳</span>
                                        </div>
                                        <input type="number" name="free_shipping_threshold" value="{{ old('free_shipping_threshold', $shippingMethod->free_shipping_threshold) }}" step="0.01"
                                            class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('free_shipping_threshold') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror">
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">Minimum order amount for free shipping</p>
                                    @error('free_shipping_threshold') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Settings</h3>
                        <!-- <p class="mt-1 text-sm text-gray-500">Additional settings for this shipping method.</p> -->
                    </div>
                    <div class="mt-5 md:col-span-2 md:mt-0">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Sort Order</label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', $shippingMethod->sort_order) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('sort_order') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('sort_order') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <div class="flex items-start">
                                    <div class="flex h-5 items-center">
                                        <input type="checkbox" name="is_active" id="is_active" value="1" 
                                               {{ old('is_active', $shippingMethod->is_active) ? 'checked' : '' }} 
                                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="is_active" class="font-medium text-gray-700">Active</label>
                                        <p class="text-gray-500">Enable this shipping method for customers</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Location Restrictions</h3>
                        <!-- <p class="mt-1 text-sm text-gray-500">Restrict shipping method to specific countries/cities.</p> -->
                    </div>
                    <div class="mt-5 md:col-span-2 md:mt-0">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Available Countries</label>
                                <select name="available_countries[]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm select2" multiple>
                                    @php
                                        $countries = [
                                            'Bangladesh', 'India', 'Pakistan', 'Nepal', 'Bhutan', 'Sri Lanka',
                                            'USA', 'UK', 'Canada', 'Australia', 'UAE', 'Saudi Arabia',
                                            'Malaysia', 'Singapore', 'Thailand', 'Indonesia', 'Vietnam'
                                        ];
                                        $selectedCountries = old('available_countries', $shippingMethod->available_countries ?? []);
                                    @endphp
                                    @foreach($countries as $country)
                                        <option value="{{ $country }}" {{ in_array($country, $selectedCountries) ? 'selected' : '' }}>
                                            {{ $country }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-2 text-sm text-gray-500">Leave empty to make available in all countries</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Available Cities (JSON format)</label>
                                <textarea name="available_cities" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-mono @error('available_cities') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror" placeholder='["Dhaka", "Chittagong", "Sylhet"]'>{{ old('available_cities', is_array($shippingMethod->available_cities) ? json_encode($shippingMethod->available_cities, JSON_PRETTY_PRINT) : $shippingMethod->available_cities) }}</textarea>
                                <p class="mt-2 text-sm text-gray-500">Enter cities as JSON array or leave empty for all cities</p>
                                @error('available_cities') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.shipping.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--multiple {
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    min-height: 38px;
}
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: #6366f1;
    outline: 0;
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #e0e7ff;
    border-color: #c7d2fe;
    color: #1e1b4b;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #4f46e5;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Select countries',
        allowClear: true,
        width: '100%'
    });

    // Toggle free shipping threshold
    document.getElementById('is_free_shipping').addEventListener('change', function() {
        const group = document.getElementById('free_shipping_threshold_group');
        group.style.display = this.checked ? 'block' : 'none';
    });
});
</script>
@endpush
@endsection