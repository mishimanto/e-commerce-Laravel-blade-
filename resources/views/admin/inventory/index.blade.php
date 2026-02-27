{{-- resources/views/admin/inventory/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Inventory Management')

@section('content')
<div class="">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-500">Inventory Management</h1>
        <div class="flex gap-2">
            <button onclick="exportInventory()" 
                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                Export
            </button>
            <a href="{{ route('admin.products.create') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Add New Product
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class=" bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
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
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Product name, SKU..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            {{-- Stock Status Filter --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stock Status</label>
                <select name="stock_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All</option>
                    <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="limited_stock" {{ request('stock_status') == 'limited_stock' ? 'selected' : '' }}>Limited Stock</option>
                    <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    Filter
                </button>
                <a href="{{ route('admin.inventory.index') }}" class="ml-2 border border-gray-300 text-gray-600 hover:text-gray-800 px-3 py-2 rounded-lg flex items-center">Clear</a>
            </div>
        </form>
    </div>

    {{-- Inventory Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Base Price</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Stock</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Variants</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($products as $product)
                <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.inventory.show', $product) }}'">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0">
                                @php
                                    $imageUrl = null;
                                    
                                    // Try to get primary image first
                                    if ($product->images && $product->images->isNotEmpty()) {
                                        $primaryImage = $product->images->firstWhere('is_primary', true);
                                        if ($primaryImage && !empty($primaryImage->url)) {
                                            $imageUrl = asset('storage/' . ltrim($primaryImage->url, '/'));
                                        } else {
                                            // If no primary image, get the first image
                                            $firstImage = $product->images->first();
                                            if ($firstImage && !empty($firstImage->url)) {
                                                $imageUrl = $firstImage->url;
                                            }
                                        }
                                    }
                                @endphp
                                
                                @if($imageUrl && !empty($imageUrl))
                                    <img class="h-10 w-10 rounded-lg object-cover" 
                                         src="{{ asset($imageUrl) }}" 
                                         alt="{{ $product->name }}"
                                         onerror="this.onerror=null; this.src='https://via.placeholder.com/40?text=No+Image';">
                                @else
                                    <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500">slug: {{ $product->slug }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="text-sm text-gray-900">{{ $product->category->name ?? 'N/A' }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="text-sm font-mono text-gray-600">{{ $product->sku }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <span class="text-sm font-medium text-gray-900">৳{{ number_format($product->base_price, 2) }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                                @php
                                    $stockStatus = $product->stock_status;
                                @endphp
                                
                                <div class="relative group">
                                    {{-- Stock badge with tooltip trigger --}}
                                    @if($stockStatus['status'] == 'out_of_stock')
                                        <span class="inline-flex items-center font-medium text-red-800 cursor-help"
                                            title="{{ $stockStatus['tooltip'] }}">                                        
                                            Out of Stock
                                        </span>
                                    @elseif($stockStatus['status'] == 'low_stock')
                                        <span class="inline-flex items-center font-medium text-orange-800 cursor-help"
                                            title="{{ $stockStatus['tooltip'] }}">                                            
                                            <span class="font-bold">{{ number_format($stockStatus['quantity']) }}</span>                    
                                        </span>
                                    @elseif($stockStatus['status'] == 'limited_stock')
                                        <span class="inline-flex items-center font-medium text-yellow-800 cursor-help"
                                            title="{{ $stockStatus['tooltip'] }}">                                
                                            <span class="font-bold">{{ number_format($stockStatus['quantity']) }}</span>                                            
                                        </span>
                                    @else
                                        <span class="inline-flex items-center font-medium text-green-800 cursor-help"
                                            title="{{ $stockStatus['tooltip'] }}">                                            
                                            <span class="font-bold">{{ number_format($stockStatus['quantity']) }}</span>
                                        </span>
                                    @endif
                                </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="font-bold text-gray-600">{{ $product->variants_count ?? 0 }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($product->status == 'active')
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center justify-center">
                        <div class="flex items-center gap-2" onclick="event.stopPropagation()">
                            <a href="{{ route('admin.inventory.show', $product) }}" 
                               class="text-blue-600 hover:text-blue-900" title="View Details">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="{{ route('admin.products.edit', $product) }}" 
                               class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="text-lg">No products found.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
</div>

@push('scripts')
<script>
function exportInventory() {
    // Get current filters
    const params = new URLSearchParams(window.location.search);
    window.location.href = '{{ route("admin.inventory.export") }}?' + params.toString();
}
</script>
@endpush
@endsection