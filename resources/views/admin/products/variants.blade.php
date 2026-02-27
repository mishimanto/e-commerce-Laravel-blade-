{{-- resources/views/admin/products/variants.blade.php --}}

@extends('layouts.admin')

@section('title', 'Manage Variants - ' . $product->name)

@section('content')
<div class="">
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-2">
            <h1 class="text-2xl font-bold text-gray-800">
                Product Variants: 
            </h1>

            <span class="text-2xl text-gray-600">
                {{ $product->name }}
            </span>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('admin.products.variants.create', $product) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Add New Variant
            </a>
            <a href="{{ route('admin.products.edit', $product) }}" 
               class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                Edit Product
            </a>
            <a href="{{ route('admin.products.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Products
            </a>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Variants Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attributes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price Adj.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Final Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($variants as $variant)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $variant->sku }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="space-y-1">
                                    @php
                                        $attrs = is_string($variant->attributes) 
                                            ? json_decode($variant->attributes, true) 
                                            : $variant->attributes;
                                    @endphp
                                    
                                    @if(is_array($attrs) && count($attrs) > 0)
                                        @foreach($attrs as $key => $value)
                                            <span class="inline-block bg-gray-100 px-2 py-1 rounded text-xs">
                                                {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-gray-400 text-xs">No attributes</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($variant->image)
                                    <img src="{{ asset('storage/' . $variant->image) }}" 
                                         alt="Variant" 
                                         class="w-12 h-12 object-cover rounded">
                                @else
                                    <span class="text-gray-400 text-sm">No image</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm {{ $variant->price_adjustment >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $variant->price_adjustment >= 0 ? '+' : '' }}{{ number_format($variant->price_adjustment, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium">
                                ৳{{ number_format($variant->price, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full {{ $variant->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $variant->stock > 0 ? $variant->stock . ' in stock' : 'Out of stock' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full {{ $variant->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($variant->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    {{-- সঠিক রুট নাম: admin.products.variants.edit --}}
                                    <a href="{{ route('admin.products.variants.edit', [$product, $variant]) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </a>
                                    {{-- সঠিক রুট নাম: admin.products.variants.destroy --}}
                                    <form action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this variant?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4a1 1 0 00-1 1v1h12V5a1 1 0 00-1-1H5zM3 8v7a2 2 0 002 2h10a2 2 0 002-2V8H3z" />
                                </svg>
                                <p class="text-gray-500">No variants found for this product.</p>
                                <a href="{{ route('admin.products.variants.create', $product) }}" 
                                   class="inline-block mt-3 text-blue-600 hover:text-blue-800">
                                    Create your first variant
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($variants->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $variants->links() }}
            </div>
        @endif
    </div>
</div>
@endsection