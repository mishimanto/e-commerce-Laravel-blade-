{{-- resources/views/admin/inventory/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Inventory Details - ' . $product->name)

@section('content')
<div class="">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-500">Inventory Details</h1>
            <p class="text-blue-600 mt-1">{{ $product->name }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.inventory.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Inventory
            </a>
            <a href="{{ route('admin.products.edit', $product) }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                Edit Product
            </a>
        </div>
    </div>

    {{-- Product Summary with Stock Status --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        @php
            // Calculate stock status
            $hasVariants = $product->variants->count() > 0;
            $totalStock = $product->stock;
            
            if ($hasVariants) {
                $stockStatus = [
                    'status' => 'in_stock',
                    'quantity' => 0,
                    'has_variants' => true,
                    'variant_count' => $product->variants->count(),
                    'tooltip' => 'Stock breakdown available'
                ];
                
                $inStockCount = 0;
                $lowStockCount = 0;
                $outOfStockCount = 0;
                
                foreach ($product->variants as $variant) {
                    $stockStatus['quantity'] += $variant->stock;
                    
                    if ($variant->stock <= 0) {
                        $outOfStockCount++;
                    } elseif ($variant->stock <= 5) {
                        $lowStockCount++;
                    } else {
                        $inStockCount++;
                    }
                }
                
                if ($stockStatus['quantity'] <= 0) {
                    $stockStatus['status'] = 'out_of_stock';
                    $stockStatus['tooltip'] = 'All variants are out of stock';
                } elseif ($stockStatus['quantity'] <= 5) {
                    $stockStatus['status'] = 'low_stock';
                    $stockStatus['tooltip'] = 'Total stock is very low';
                } elseif ($outOfStockCount > 0 || $lowStockCount > 0) {
                    $stockStatus['status'] = 'limited_stock';
                    $stockStatus['tooltip'] = "Some variants have limited stock (In Stock: {$inStockCount}, Low Stock: {$lowStockCount}, Out of Stock: {$outOfStockCount})";
                } else {
                    $stockStatus['status'] = 'in_stock';
                    $stockStatus['tooltip'] = 'All variants are in stock';
                }
            } else {
                $stockStatus = [
                    'status' => $totalStock <= 0 ? 'out_of_stock' : ($totalStock <= 5 ? 'low_stock' : 'in_stock'),
                    'quantity' => $totalStock,
                    'has_variants' => false,
                    'variant_count' => 0,
                    'tooltip' => $totalStock <= 0 ? 'Product is out of stock' : ($totalStock <= 5 ? 'Stock is running low' : 'Product is in stock')
                ];
            }
        @endphp

        {{-- Stock Status Card --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-sm text-gray-500 mb-1">Stock Status</div>
            <div class="relative group">
                @if($stockStatus['status'] == 'out_of_stock')
                    <span class="inline-flex items-center font-medium text-red-800 cursor-help"
                        title="{{ $stockStatus['tooltip'] }}">
                        Out of Stock
                    </span>
                @elseif($stockStatus['status'] == 'low_stock')
                    <span class="inline-flex items-center font-medium text-orange-800 cursor-help"
                        title="{{ $stockStatus['tooltip'] }}">
                        <span class="text-2xl font-bold">{{ number_format($stockStatus['quantity']) }}</span>
                    </span>
                @elseif($stockStatus['status'] == 'limited_stock')
                    <span class="inline-flex items-center font-medium text-yellow-800 cursor-help"
                        title="{{ $stockStatus['tooltip'] }}">
                        <span class="text-2xl font-bold">{{ number_format($stockStatus['quantity']) }}</span>
                    </span>
                @else
                    <span class="inline-flex items-center font-medium text-green-800 cursor-help"
                        title="{{ $stockStatus['tooltip'] }}">
                        <span class="text-2xl font-bold">{{ number_format($stockStatus['quantity']) }}</span>
                    </span>
                @endif
            </div>
        </div>

        {{-- Variants Count Card --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-sm text-gray-500 mb-1">Total Variants</div>
            <div class="text-2xl font-bold">{{ $product->variants->count() }}</div>
        </div>
        
        {{-- Base Price Card --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-sm text-gray-500 mb-1">Base Price</div>
            <div class="text-2xl font-bold">৳{{ number_format($product->base_price, 2) }}</div>
        </div>
        
        {{-- Category Card --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-sm text-gray-500 mb-1">Category</div>
            <div class="text-lg font-medium">{{ $product->category->name ?? 'N/A' }}</div>
        </div>
    </div>

    {{-- Product Information --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Product Details</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-gray-500">SKU</div>
                            <div class="text-sm font-medium font-mono">{{ $product->sku }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Brand</div>
                            <div class="text-sm font-medium">{{ $product->brand->name ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Status</div>
                            <div>
                                @if($product->status == 'active')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactive</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Warranty</div>
                            <div class="text-sm font-medium">{{ $product->warranty ?? 'N/A' }}</div>
                        </div>
                    </div>
                    
                    @if($product->description)
                    <div class="mt-4">
                        <div class="text-sm text-gray-500 mb-2">Description</div>
                        <div class="text-sm text-gray-700">{!! nl2br(e($product->description)) !!}</div>
                    </div>
                    @endif

                    @if($product->specifications)
                    <div class="mt-4">
                        <div class="text-sm text-gray-500 mb-2">Specifications</div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            @php
                                $specs = is_string($product->specifications) ? json_decode($product->specifications, true) : $product->specifications;
                            @endphp
                            @if(is_array($specs))
                                @foreach($specs as $key => $value)
                                    <div class="grid grid-cols-2 gap-2 text-sm py-1 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                                        <div class="text-gray-600">{{ $key }}</div>
                                        <div class="font-medium">{{ is_array($value) ? ($value['value'] ?? 'N/A') : $value }}</div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Product Images</h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-2">
                        @forelse($product->images as $image)
                            <div class="relative group">
                                @php
                                    $imageUrl = $image->url ?? null;
                                @endphp
                                @if($imageUrl && !empty($imageUrl))
                                    <img src="{{ asset($imageUrl) }}" alt="{{ $image->alt_text ?? $product->name }}" 
                                         class="w-full h-24 object-cover rounded-lg border border-gray-200"
                                         onerror="this.onerror=null; this.src='https://via.placeholder.com/100?text=No+Image';">
                                @else
                                    <div class="w-full h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                @if($image->is_primary)
                                    <span class="absolute top-1 right-1 bg-blue-500 text-white text-xs px-1 rounded">Primary</span>
                                @endif
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-8 text-gray-500">
                                No images available
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Variants Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Product Variants</h3>
            <a href="{{ route('admin.products.variants.create', $product) }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-3 py-1 rounded-lg flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Add Variant
            </a>
        </div>
        
        @if($product->variants->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attributes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Price Adjustment</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Final Price</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($product->variants as $variant)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @php
                                    $attributes = is_string($variant->attributes) ? json_decode($variant->attributes, true) : $variant->attributes;
                                @endphp
                                @if(is_array($attributes))
                                    @foreach($attributes as $key => $value)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst($key) }}: {{ $value }}
                                        </span>
                                    @endforeach
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">{{ $variant->sku }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            @if($variant->price_adjustment > 0)
                                <span class="text-green-600">+৳{{ number_format($variant->price_adjustment, 2) }}</span>
                            @elseif($variant->price_adjustment < 0)
                                <span class="text-red-600">-৳{{ number_format(abs($variant->price_adjustment), 2) }}</span>
                            @else
                                <span class="text-gray-500">৳0.00</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-right">
                            ৳{{ number_format($product->base_price + $variant->price_adjustment, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @php
                                $variantStock = $variant->stock ?? 0;
                            @endphp
                            @if($variantStock <= 0)
                                <span class="text-red-600 font-medium">{{ $variantStock }}</span>
                            @elseif($variantStock <= 5)
                                <span class="text-yellow-600 font-medium">{{ $variantStock }}</span>
                            @else
                                <span class="text-green-600 font-medium">{{ $variantStock }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($variant->status == 'active')
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center justify-center">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.products.variants.edit', [$product, $variant]) }}" 
                                   class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </a>
                                <button type="button" 
                                        onclick="openStockModal({{ $variant->id }})" 
                                        class="text-green-600 hover:text-green-900" 
                                        title="Update Stock"
                                        data-variant-id="{{ $variant->id }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                                        <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="p-12 text-center">
                <div class="text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <p class="text-lg">No variants found for this product.</p>
                    <a href="{{ route('admin.products.variants.create', $product) }}" class="text-blue-600 hover:underline mt-2 inline-block">
                        Create your first variant
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Stock Update Modal --}}
<div id="stockModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Update Stock</h3>
            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="stockForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">New Stock Quantity</label>
                <input type="number" name="stock" id="stockInput" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       min="0" required>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal()" 
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Update Stock
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let currentVariantId = null;

function openStockModal(variantId) {
    currentVariantId = variantId;
    const modal = document.getElementById('stockModal');
    const form = document.getElementById('stockForm');
    
    // Create the correct URL with the variant ID
    const url = '{{ route("admin.inventory.update-stock", ":id") }}'.replace(':id', variantId);
    form.action = url;
    
    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('stockModal').classList.add('hidden');
    document.getElementById('stockInput').value = '';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('stockModal');
    if (event.target == modal) {
        closeModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});
</script>
@endpush
@endsection