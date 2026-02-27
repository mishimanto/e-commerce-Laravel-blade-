@extends('layouts.admin')

@section('title', 'Create Variant - ' . $product->name)

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Create Variant for: {{ $product->name }}</h1>
        <a href="{{ route('admin.products.variants', $product) }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            <span>Back to Variants</span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-2xl">
        <form action="{{ route('admin.products.variants.store', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="space-y-4">
                {{-- SKU --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 required">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku') }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('sku') border-red-500 @enderror" 
                           required>
                    @error('sku')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Color --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                    <select name="color" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">Select Color</option>
                        <option value="Red" {{ old('color') == 'Red' ? 'selected' : '' }}>Red</option>
                        <option value="Blue" {{ old('color') == 'Blue' ? 'selected' : '' }}>Blue</option>
                        <option value="Green" {{ old('color') == 'Green' ? 'selected' : '' }}>Green</option>
                        <option value="Black" {{ old('color') == 'Black' ? 'selected' : '' }}>Black</option>
                        <option value="White" {{ old('color') == 'White' ? 'selected' : '' }}>White</option>
                        <option value="Silver" {{ old('color') == 'Silver' ? 'selected' : '' }}>Silver</option>
                        <option value="Gold" {{ old('color') == 'Gold' ? 'selected' : '' }}>Gold</option>
                        <option value="Purple" {{ old('color') == 'Purple' ? 'selected' : '' }}>Purple</option>
                        <option value="Graphite" {{ old('color') == 'Graphite' ? 'selected' : '' }}>Graphite</option>
                    </select>
                </div>

                {{-- Storage --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Storage</label>
                    <select name="storage" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">Select Storage</option>
                        <option value="64GB" {{ old('storage') == '64GB' ? 'selected' : '' }}>64GB</option>
                        <option value="128GB" {{ old('storage') == '128GB' ? 'selected' : '' }}>128GB</option>
                        <option value="256GB" {{ old('storage') == '256GB' ? 'selected' : '' }}>256GB</option>
                        <option value="512GB" {{ old('storage') == '512GB' ? 'selected' : '' }}>512GB</option>
                        <option value="1TB" {{ old('storage') == '1TB' ? 'selected' : '' }}>1TB</option>
                    </select>
                </div>

                {{-- RAM --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RAM</label>
                    <select name="ram" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">Select RAM</option>
                        <option value="4GB" {{ old('ram') == '4GB' ? 'selected' : '' }}>4GB</option>
                        <option value="6GB" {{ old('ram') == '6GB' ? 'selected' : '' }}>6GB</option>
                        <option value="8GB" {{ old('ram') == '8GB' ? 'selected' : '' }}>8GB</option>
                        <option value="12GB" {{ old('ram') == '12GB' ? 'selected' : '' }}>12GB</option>
                        <option value="16GB" {{ old('ram') == '16GB' ? 'selected' : '' }}>16GB</option>
                    </select>
                </div>

                {{-- Price Adjustment --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 required">Price Adjustment (৳)</label>
                    <div class="text-sm text-gray-500 mb-1">Base Price: ৳{{ number_format($product->base_price, 2) }}</div>
                    <input type="number" step="0.01" name="price_adjustment" value="{{ old('price_adjustment', 0) }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('price_adjustment') border-red-500 @enderror" 
                           required>
                    <p class="text-xs text-gray-500 mt-1">Use positive value for extra cost, negative for discount</p>
                    @error('price_adjustment')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Stock --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 required">Stock</label>
                    <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('stock') border-red-500 @enderror" 
                           required>
                    @error('stock')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Image --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Variant Image</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <p class="text-xs text-gray-500 mt-1">Optional - specific image for this variant</p>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 required">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                {{-- Submit Button --}}
                <div class="flex justify-end pt-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                        Create Variant
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection