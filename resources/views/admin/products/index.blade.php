@extends('layouts.admin')

@section('title', 'Products Management')

@section('content')
<div class="">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Products</h1>
        <a href="{{ route('admin.products.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Add New Product
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="p-4">
            <form method="GET" action="{{ route('admin.products.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Name, SKU..." value="{{ request('search') }}">
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                    <select name="brand" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                    <select name="stock" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Stock</option>
                        <option value="in_stock" {{ request('stock') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="low_stock" {{ request('stock') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ request('stock') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
                <div class="col-span-1 flex items-end">
                    <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 mb-1 rounded-lg transition-colors">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Products Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Brand</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 flex items-center justify-center">
                                <img src="{{ $product->primary_image_url ?? asset('storage/images/placeholder-photo.png') }}" 
                                     alt="{{ $product->name }}"
                                     class="w-16 h-16 object-cover rounded-lg border border-gray-200"
                                >
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500">Created: {{ $product->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $product->sku }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $product->category->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $product->brand->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($product->sale_price)
                                    <div class="text-sm">
                                        <span class="line-through text-gray-400">৳{{ number_format($product->base_price) }}</span>
                                        <span class="font-bold text-red-600 ml-1">৳{{ number_format($product->sale_price) }}</span>
                                    </div>
                                @else
                                    <span class="font-bold text-gray-900">৳{{ number_format($product->base_price) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $stockStatus = $product->stock_status;
                                @endphp
                                
                                <div class="relative group">
                                    {{-- Stock badge with tooltip trigger --}}
                                    @if($stockStatus['status'] == 'out_of_stock')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200 cursor-help"
                                            title="{{ $stockStatus['tooltip'] }}">
                                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                            Out of Stock
                                        </span>
                                    @elseif($stockStatus['status'] == 'low_stock')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200 cursor-help"
                                            title="{{ $stockStatus['tooltip'] }}">
                                            <span class="w-2 h-2 bg-orange-500 rounded-full mr-2"></span>
                                            <span class="font-bold">{{ number_format($stockStatus['quantity']) }}</span>
                                            <span class="ml-1">left</span>
                                            @if($stockStatus['has_variants'])
                                                <span class="ml-1 text-orange-600">({{ $stockStatus['variant_count'] }} variants)</span>
                                            @endif
                                        </span>
                                    @elseif($stockStatus['status'] == 'limited_stock')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200 cursor-help"
                                            title="{{ $stockStatus['tooltip'] }}">
                                            <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                                            <span class="font-bold">{{ number_format($stockStatus['quantity']) }}</span>
                                            <!-- <span class="ml-1">in stock</span> -->
                                            @if($stockStatus['has_variants'])
                                                <span class="ml-1 text-yellow-600">({{ $stockStatus['variant_count'] }} variants)</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200 cursor-help"
                                            title="{{ $stockStatus['tooltip'] }}">
                                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                            <span class="font-bold">{{ number_format($stockStatus['quantity']) }}</span>
                                            <!-- <span class="ml-1">in stock</span> -->
                                            @if($stockStatus['has_variants'])
                                                <span class="ml-1 text-green-600">({{ $stockStatus['variant_count'] }} variants)</span>
                                            @endif
                                        </span>
                                    @endif
                                    
                                    @if($stockStatus['has_variants'])
                                        <div class="absolute hidden group-hover:block z-50 bottom-full left-0 mb-2 w-64 bg-gray-900 text-white text-xs rounded-lg shadow-xl">
                                            <div class="px-3 py-2 border-b border-gray-700 font-medium">Stock Breakdown</div>
                                            <div class="p-3 space-y-1 max-h-48 overflow-y-auto">
                                                @foreach($product->variants as $variant)
                                                    <div class="flex justify-between items-center">
                                                        <span class="text-gray-300">{{ $variant->display_name }}</span>
                                                        <span class="font-mono {{ $variant->stock > 0 ? 'text-green-400' : 'text-red-400' }}">
                                                            {{ number_format($variant->stock) }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="px-3 py-2 bg-gray-800 text-gray-300 rounded-b-lg text-right font-mono">
                                                Total: {{ number_format($stockStatus['quantity']) }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($product->status == 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                        Active
                                    </span>
                                @elseif($product->status == 'inactive')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <span class="w-1.5 h-1.5 bg-gray-500 rounded-full mr-1.5"></span>
                                        Inactive
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5"></span>
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.products.edit', $product) }}" 
                                       class="text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-50 transition-colors"
                                       title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.products.variants', $product) }}" 
                                       class="text-purple-600 hover:text-purple-800 p-1 rounded hover:bg-purple-50 transition-colors"
                                       title="Variants">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                                        </svg>
                                    </a>
                                    <button type="button" 
                                            onclick="duplicateProduct({{ $product->id }})"
                                            class="text-gray-600 hover:text-gray-800 p-1 rounded hover:bg-gray-100 transition-colors"
                                            title="Duplicate">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M7 9a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2V9z" />
                                            <path d="M5 3a2 2 0 00-2 2v6a2 2 0 002 2V5h8a2 2 0 00-2-2H5z" />
                                        </svg>
                                    </button>
                                    <form action="{{ route('admin.products.destroy', $product) }}" 
                                          method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50 transition-colors"
                                                onclick="return confirm('Are you sure you want to delete this product?')"
                                                title="Delete">
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
                            <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <p class="text-gray-500 mb-2">No products found.</p>
                                    <a href="{{ route('admin.products.create') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                                        Add your first product
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($products->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $products->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function duplicateProduct(productId) {
    if (confirm('Duplicate this product?')) {
        fetch(`/admin/products/${productId}/duplicate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Failed to duplicate product: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while duplicating the product.');
        });
    }
}
</script>
@endpush
@endsection