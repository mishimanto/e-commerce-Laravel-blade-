@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="">
    {{-- Header with buttons --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Product: {{ $product->name }}</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                <span class="hidden sm:inline">Back to List</span>
            </a>
            <button type="submit" form="product-form" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="hidden sm:inline">Update Product</span>
            </button>
        </div>
    </div>

    <form id="product-form" action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            {{-- Left Column --}}
            <div class="xl:col-span-2 space-y-6">
                {{-- Basic Information --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <h5 class="font-medium text-gray-800">Basic Information</h5>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 required">Product Name</label>
                                <input type="text" name="name" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror" 
                                       value="{{ old('name', $product->name) }}" required>
                                @error('name')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 required">SKU</label>
                                <input type="text" name="sku" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('sku') border-red-500 @enderror" 
                                       value="{{ old('sku', $product->sku) }}" required>
                                @error('sku')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 required">Category</label>
                                <select name="category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('category_id') border-red-500 @enderror" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 required">Brand</label>
                                <select name="brand_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('brand_id') border-red-500 @enderror" required>
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" 
                                            {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('brand_id')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Short Description</label>
                                <textarea name="short_description" rows="3" 
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('short_description') border-red-500 @enderror">{{ old('short_description', $product->short_description) }}</textarea>
                                @error('short_description')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Full Description</label>
                                <textarea name="description" id="description" rows="10" 
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pricing & Stock --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
        <div class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
            </svg>
            <h5 class="font-medium text-gray-800">Pricing & Warranty</h5>
        </div>
    </div>
    <div class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
            {{-- BUYING PRICE (New Field) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 required">Buying Price (৳)</label>
                <input type="number" step="0.01" name="buying_price" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('buying_price') border-red-500 @enderror" 
                       value="{{ old('buying_price', $product->buying_price) }}"
                       placeholder="Cost price" required>
                @error('buying_price')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Your purchase cost per unit</p>
            </div>

            {{-- Base Price --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 required">Selling Price (৳)</label>
                <input type="number" step="0.01" name="base_price" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('base_price') border-red-500 @enderror" 
                       value="{{ old('base_price', $product->base_price) }}" required>
                @error('base_price')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>            

            {{-- Sale Price --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Discount Price (৳)</label>
                <input type="number" step="0.01" name="sale_price" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('sale_price') border-red-500 @enderror" 
                       value="{{ old('sale_price', $product->sale_price) }}">
                @error('sale_price')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Stock Quantity --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity</label>
                <input type="number" name="stock" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('stock') border-red-500 @enderror" 
                       value="{{ old('stock', $product->stock) }}">
                @error('stock')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Warranty --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Warranty</label>
                <input type="text" name="warranty" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('warranty') border-red-500 @enderror" 
                       value="{{ old('warranty', $product->warranty) }}">
                @error('warranty')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 required">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror" required>
                    <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
                @error('status')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>

                {{-- Specifications --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                            </svg>
                            <h5 class="font-medium text-gray-800">Specifications</h5>
                        </div>
                    </div>
                    <div class="p-4">
                        <div id="specifications" class="space-y-2">
                            @php 
                                // FIX: Check if specifications is array or string
                                $specs = old('specifications');
                                
                                if (is_null($specs)) {
                                    $productSpecs = $product->specifications;
                                    
                                    // If it's a string, decode it
                                    if (is_string($productSpecs)) {
                                        $specs = json_decode($productSpecs, true) ?? [];
                                    } 
                                    // If it's already an array (from model casting)
                                    elseif (is_array($productSpecs)) {
                                        $specs = $productSpecs;
                                    } 
                                    else {
                                        $specs = [];
                                    }
                                }
                            @endphp
                            
                            @if(!empty($specs) && is_array($specs))
                                @foreach($specs as $index => $spec)
                                    <div class="grid grid-cols-12 gap-2 specification-row" id="spec-row-{{ $index }}">
                                        <div class="col-span-5">
                                            <input type="text" name="specifications[{{ $index }}][key]" 
                                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                                   placeholder="Specification name"
                                                   value="{{ is_array($spec) ? ($spec['key'] ?? '') : $index }}">
                                        </div>
                                        <div class="col-span-5">
                                            <input type="text" name="specifications[{{ $index }}][value]" 
                                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                                   placeholder="Specification value"
                                                   value="{{ is_array($spec) ? ($spec['value'] ?? '') : $spec }}">
                                        </div>
                                        <div class="col-span-2">
                                            <button type="button" class="remove-spec bg-red-500 hover:bg-red-600 text-white px-3 py-3 rounded-lg transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-auto" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" class="mt-3 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors" id="addSpec">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Add Specification
                        </button>
                    </div>
                </div>

                {{-- SEO Information --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                            <h5 class="font-medium text-gray-800">SEO Information</h5>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                                <input type="text" name="meta_title" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('meta_title') border-red-500 @enderror" 
                                       value="{{ old('meta_title', $product->meta_title) }}">
                                @error('meta_title')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                                <textarea name="meta_description" rows="3" 
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('meta_description') border-red-500 @enderror">{{ old('meta_description', $product->meta_description) }}</textarea>
                                @error('meta_description')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
                                <input type="text" name="meta_keywords" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('meta_keywords') border-red-500 @enderror" 
                                       value="{{ old('meta_keywords', $product->meta_keywords) }}" 
                                       placeholder="Comma separated">
                                @error('meta_keywords')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="space-y-6">
                {{-- Product Images --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                            </svg>
                            <h5 class="font-medium text-gray-800">Product Images</h5>
                        </div>
                    </div>
                    <div class="p-4">
                        @if($product->images->count() > 0)
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Images</label>
                            <div class="grid grid-cols-2 gap-2 mb-4" id="current-images">
                                @foreach($product->images as $image)
                                    <div class="relative group" data-image-id="{{ $image->id }}" id="image-{{ $image->id }}">
                                        <img src="{{ $image->full_url }}" 
                                            class="w-full h-32 object-cover rounded-lg border border-gray-200"
                                            alt="Product Image"
                                            onerror="this.src='https://via.placeholder.com/150?text=No+Image'">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                                            <div class="flex gap-2">
                                                <div class="flex items-center">
                                                    <input type="radio" name="primary_image" value="{{ $image->id }}" 
                                                           class="w-4 h-4 text-blue-600 focus:ring-blue-500"
                                                           id="primary_{{ $image->id }}"
                                                           {{ $image->is_primary ? 'checked' : '' }}>
                                                </div>
                                                <button type="button" class="bg-red-500 hover:bg-red-600 text-white p-1 rounded delete-image" 
                                                        data-image-id="{{ $image->id }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        @if($image->is_primary)
                                            <span class="absolute top-1 left-1 bg-blue-500 text-white text-xs px-1 rounded">Primary</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Add New Images</label>
                            <div class="flex items-center justify-center w-full">
                                <label for="image-upload" class="w-full flex flex-col items-center px-4 py-6 bg-white text-blue-500 rounded-lg border-2 border-dashed border-gray-300 cursor-pointer hover:border-blue-500 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="mt-2 text-sm text-gray-500">Click to upload images</span>
                                    <span class="text-xs text-gray-400">PNG, JPG, GIF up to 2MB</span>
                                    <input id="image-upload" type="file" name="new_images[]" multiple accept="image/*" class="hidden">
                                </label>
                            </div>
                            @error('new_images.*')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="image-preview" class="grid grid-cols-2 gap-2"></div>
                    </div>
                </div>

                {{-- Additional Options --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                            </svg>
                            <h5 class="font-medium text-gray-800">Additional Options</h5>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_featured" value="1" 
                                       class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                       id="isFeatured"
                                       {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                <label for="isFeatured" class="ml-2 block text-sm text-gray-700">
                                    Featured Product
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="is_trending" value="1" 
                                       class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                       id="isTrending"
                                       {{ old('is_trending', $product->is_trending) ? 'checked' : '' }}>
                                <label for="isTrending" class="ml-2 block text-sm text-gray-700">
                                    Trending Product
                                </label>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                                <input type="text" name="tags" id="tags" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                       value="{{ old('tags', is_array($product->tags) ? implode(',', $product->tags) : $product->tags) }}" 
                                       placeholder="Enter tags separated by comma">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">
<style>
    .bootstrap-tagsinput {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
        border-color: #e5e7eb;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    .bootstrap-tagsinput .tag {
        background: #3b82f6;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }
    .bootstrap-tagsinput input {
        padding: 0.25rem;
    }
    .select2-container--default .select2-selection--multiple {
        border-color: #e5e7eb;
        border-radius: 0.5rem;
        padding: 0.25rem;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #3b82f6;
        ring: 2px solid #3b82f6;
    }
    .required:after {
        content: " *";
        color: #ef4444;
    }
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize CKEditor
    ClassicEditor
        .create(document.querySelector('#description'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', '|', 'undo', 'redo']
        })
        .catch(error => {
            console.error(error);
        });

    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Select options',
        allowClear: true
    });

    // Initialize Tags Input
    $('#tags').tagsinput({
        trimValue: true,
        confirmKeys: [13, 44, 32]
    });

    // =========================
    // IMAGE PREVIEW WITH DELETE ON HOVER
    // =========================
    const fileInput = document.getElementById('image-upload');
    const previewContainer = document.getElementById('image-preview');
    
    // Store new image data
    let newImages = [];
    
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            // Clear existing preview
            previewContainer.innerHTML = '';
            newImages = [];
            
            if (e.target.files.length > 0) {
                // Add preview header
                const header = document.createElement('div');
                header.className = 'col-span-2 text-sm font-medium text-gray-700 mb-2';
                header.textContent = 'New Images Preview:';
                previewContainer.appendChild(header);
            }
            
            // Process each file
            Array.from(e.target.files).forEach((file, index) => {
                const fileId = `img_${Date.now()}_${index}`;
                
                // Store file
                newImages.push({
                    id: fileId,
                    file: file
                });
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    addImagePreview(fileId, file.name, e.target.result);
                };
                reader.readAsDataURL(file);
            });
            
            // Update file input
            updateFileInput();
        });
    }
    
    function addImagePreview(id, fileName, src) {
        const col = document.createElement('div');
        col.className = 'relative group';
        col.id = `preview-${id}`;
        col.dataset.imageId = id;
        
        col.innerHTML = `
            <img src="${src}" class="w-full h-32 object-cover rounded-lg border border-gray-200">
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                <button type="button" class="bg-red-500 hover:bg-red-600 text-white p-1.5 rounded-full delete-new-image shadow-lg" 
                        data-image-id="${id}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <span class="absolute top-1 right-1 bg-blue-500 text-white text-xs px-1.5 py-0.5 rounded truncate max-w-[100px]">New</span>
        `;
        
        previewContainer.appendChild(col);
    }
    
    // Handle delete new image on hover
    document.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.delete-new-image');
        if (!deleteBtn) return;
        
        e.preventDefault();
        
        const imageId = deleteBtn.dataset.imageId;
        const previewEl = document.getElementById(`preview-${imageId}`);
        
        if (!imageId || !previewEl) return;
        
        if (!confirm('Remove this image?')) return;
        
        // Remove from DOM
        previewEl.remove();
        
        // Remove from array
        newImages = newImages.filter(img => img.id !== imageId);
        
        // Update header if no images left
        const remainingPreviews = previewContainer.querySelectorAll('[id^="preview-"]');
        const header = previewContainer.querySelector('.col-span-2.text-sm.font-medium.text-gray-700.mb-2');
        
        if (remainingPreviews.length === 0 && header) {
            header.remove();
        }
        
        // Update file input
        updateFileInput();
    });
    
    function updateFileInput() {
        const dataTransfer = new DataTransfer();
        newImages.forEach(img => {
            dataTransfer.items.add(img.file);
        });
        fileInput.files = dataTransfer.files;
    }

    // =========================
    // ADD SPECIFICATION
    // =========================
    let specCount = {{ isset($specs) && is_array($specs) ? count($specs) : 0 }};
    
    document.getElementById('addSpec')?.addEventListener('click', function() {
        const container = document.getElementById('specifications');
        const row = document.createElement('div');
        row.className = 'grid grid-cols-12 gap-2 specification-row';
        row.id = `spec-row-${specCount}`;
        row.innerHTML = `
            <div class="col-span-5">
                <input type="text" name="specifications[${specCount}][key]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Specification name">
            </div>
            <div class="col-span-5">
                <input type="text" name="specifications[${specCount}][value]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Specification value">
            </div>
            <div class="col-span-2">
                <button type="button" class="remove-spec bg-red-500 hover:bg-red-600 text-white px-3 py-3 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-auto" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        `;
        container.appendChild(row);
        specCount++;
    });
});

// =========================
// DELETE EXISTING IMAGE (AJAX)
// =========================
document.addEventListener('click', async function(e) {
    const button = e.target.closest('.delete-image');
    if (!button) return;

    e.preventDefault();

    const imageId = button.dataset.imageId;
    const imageContainer = document.getElementById(`image-${imageId}`);

    if (!imageId || !imageContainer) {
        console.error('Image container not found');
        return;
    }

    if (!confirm('Are you sure you want to delete this image?')) {
        return;
    }

    // Store original content
    const originalHTML = button.innerHTML;
    
    // Show loading
    button.disabled = true;
    button.innerHTML = `
        <svg class="animate-spin h-4 w-4 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
    `;

    try {
        const response = await fetch(`/admin/products/images/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (data.success) {
            imageContainer.remove();
            
            // Check if any images left
            const remainingImages = document.querySelectorAll('[data-image-id]');
            const currentImagesContainer = document.getElementById('current-images');
            
            if (remainingImages.length === 0) {
                if (currentImagesContainer) {
                    currentImagesContainer.innerHTML = `
                        <div class="col-span-2 text-center py-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="text-gray-500">No images available</p>
                        </div>
                    `;
                }
            } else {
                // Check if we need to set a new primary
                const anyPrimary = Array.from(document.querySelectorAll('input[name="primary_image"]')).some(input => input.checked);
                
                if (!anyPrimary && remainingImages.length > 0) {
                    const firstRadio = document.querySelector('input[name="primary_image"]');
                    if (firstRadio) {
                        firstRadio.checked = true;
                        
                        const firstImageContainer = firstRadio.closest('[data-image-id]');
                        if (firstImageContainer) {
                            document.querySelectorAll('.absolute.top-1.left-1').forEach(badge => {
                                if (badge.textContent === 'Primary') badge.remove();
                            });
                            
                            const badge = document.createElement('span');
                            badge.className = 'absolute top-1 left-1 bg-blue-500 text-white text-xs px-1 rounded';
                            badge.textContent = 'Primary';
                            firstImageContainer.appendChild(badge);
                        }
                    }
                }
            }
        } else {
            throw new Error(data.message || 'Delete failed');
        }

    } catch (err) {
        console.error('Delete error:', err);
        alert('Error deleting image: ' + err.message);
        
        // Restore button
        button.disabled = false;
        button.innerHTML = originalHTML;
    }
});

// =========================
// REMOVE SPECIFICATION
// =========================
document.addEventListener('click', function(e) {
    const removeButton = e.target.closest('.remove-spec');
    if (!removeButton) return;
    
    e.preventDefault();
    
    const specificationRow = removeButton.closest('.specification-row');
    
    if (!specificationRow) {
        console.error('Specification row not found');
        return;
    }
    
    if (!confirm('Are you sure you want to remove this specification?')) {
        return;
    }
    
    specificationRow.remove();
});
</script>
@endpush