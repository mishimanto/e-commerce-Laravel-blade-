@extends('layouts.admin')

@section('title', 'Inventory Report')

@section('content')
<div class="">
    {{-- Header with buttons --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Inventory Report</h1>
        <div class="flex items-center gap-2">
            {{-- Export Dropdown --}}
            <div class="relative inline-block text-left">
                <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors dropdown-toggle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <span>Export</span>
                </button>
                <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden dropdown-menu z-50">
                    <a href="{{ route('admin.reports.export', ['type' => 'inventory', 'format' => 'excel']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Excel</a>
                    <a href="{{ route('admin.reports.export', ['type' => 'inventory', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">CSV</a>
                    <a href="{{ route('admin.reports.export', ['type' => 'inventory', 'format' => 'pdf']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">PDF</a>
                </div>
            </div>
            
            {{-- Print Button --}}
            <button type="button" onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                <span>Print</span>
            </button>
        </div>
    </div>

    {{-- Stock Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        {{-- Total Products --}}
        <div class="bg-blue-600 text-white rounded-lg shadow-sm h-24">
            <div class="p-4 flex justify-between items-center h-full">
                <div>
                    <div class="text-sm opacity-90">Total Products</div>
                    <div class="text-2xl font-bold">{{ number_format($stockSummary['total_products']) }}</div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50">
                    <path d="M20 7h-4.5A2.5 2.5 0 0 1 13 4.5V3"></path>
                    <path d="M20 7v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5Z"></path>
                </svg>
            </div>
        </div>

        {{-- In Stock --}}
        <div class="bg-cyan-600 text-white rounded-lg shadow-sm h-24">
            <div class="p-4 flex justify-between items-center h-full">
                <div>
                    <div class="text-sm opacity-90">In Stock</div>
                    <div class="text-2xl font-bold">{{ number_format($stockSummary['in_stock']) }}</div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="m9 12 2 2 4-4"></path>
                </svg>
            </div>
        </div>

        {{-- Low Stock --}}
        <div class="bg-gray-400 text-white rounded-lg shadow-sm h-24">
            <div class="p-4 flex justify-between items-center h-full">
                <div>
                    <div class="text-sm opacity-90">Low Stock</div>
                    <div class="text-2xl font-bold">{{ number_format($stockSummary['low_stock']) }}</div>
                    <!-- <div class="mt-1">
                        <span class="bg-white text-yellow-600 text-xs px-2 py-1 rounded-full">
                            {{ number_format($stockSummary['out_of_stock']) }} out of stock
                        </span>
                    </div> -->
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
        </div>
        
        {{-- Stock out --}}
        <div class="bg-red-600 text-white rounded-lg shadow-sm h-24">
            <div class="p-4 flex justify-between items-center h-full">
                <div>
                    <div class="text-sm opacity-90">Out of Stock</div>
                    <div class="text-2xl font-bold">{{ number_format($stockSummary['out_of_stock']) }}</div>
                    <!-- <div class="mt-1">
                        <span class="bg-white text-yellow-600 text-xs px-2 py-1 rounded-full">
                            {{ number_format($stockSummary['out_of_stock']) }} out of stock
                        </span>
                    </div> -->
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </div>
        </div>
    </div>

    {{-- Inventory Value with Cost Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        {{-- Total Stock Value --}}
        <div class="bg-green-600 text-white rounded-lg shadow-sm h-24">
            <div class="p-4 flex justify-between items-center h-full">
                <div>
                    <div class="text-sm opacity-90">Total Stock Value</div>
                    <div class="text-2xl font-bold">৳{{ number_format($stockSummary['total_value'], 2) }}</div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50">
                    <line x1="12" y1="2" x2="12" y2="22"></line>
                    <path d="M17 5H9.5M17 12h-5M17 19h-5"></path>
                </svg>
            </div>
        </div>

        {{-- Inventory Value (Selling) --}}
        <div class="bg-blue-600 text-white rounded-lg shadow-sm h-24">
            <div class="p-4 flex justify-between items-center h-full">
                <div>
                    <div class="text-sm opacity-90">Inventory Value (Selling)</div>
                    <div class="text-2xl font-bold">৳{{ number_format($inventoryValueWithCost['total_value'], 2) }}</div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50">
                    <path d="M20 7h-4.5A2.5 2.5 0 0 1 13 4.5V3"></path>
                    <path d="M20 7v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5Z"></path>
                </svg>
            </div>
        </div>

        {{-- Inventory Cost (Buying) --}}
        <div class="bg-red-600 text-white rounded-lg shadow-sm h-24">
            <div class="p-4 flex justify-between items-center h-full">
                <div>
                    <div class="text-sm opacity-90">Inventory Cost (Buying)</div>
                    <div class="text-2xl font-bold">৳{{ number_format($inventoryValueWithCost['total_cost'], 2) }}</div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50">
                    <path d="M12 2v20M17 5H9.5M17 12h-5M17 19h-5"></path>
                </svg>
            </div>
        </div>

        {{-- Potential Profit --}}
        <div class="bg-green-600 text-white rounded-lg shadow-sm h-24">
            <div class="p-4 flex justify-between items-center h-full">
                <div>
                    <div class="text-sm opacity-90">Potential Profit</div>
                    <div class="text-2xl font-bold">৳{{ number_format($inventoryValueWithCost['potential_profit'], 2) }}</div>
                    <div class="mt-1">
                        <span class="bg-white text-green-600 text-xs px-2 py-1 rounded-full">
                            {{ number_format($inventoryValueWithCost['profit_margin'], 1) }}% margin
                        </span>
                    </div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50">
                    <path d="M21 12v-2a5 5 0 0 0-5-5H8a5 5 0 0 0-5 5v2"></path>
                    <circle cx="12" cy="16" r="5"></circle>
                    <path d="m9 16 3-3 3 3"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- Stock Status Charts --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-6">
        {{-- Stock Status Chart --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-4 py-3">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 2a15 15 0 0 0 0 30 15 15 0 0 0 0-30"></path>
                    </svg>
                    <h5 class="font-medium text-gray-800">Stock Status Distribution</h5>
                </div>
            </div>
            <div class="p-4">
                <canvas id="stockStatusChart" style="height: 300px;"></canvas>
            </div>
        </div>

        {{-- Category Value Chart --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-4 py-3">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"></line>
                        <line x1="12" y1="20" x2="12" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                    <h5 class="font-medium text-gray-800">Inventory Value by Category</h5>
                </div>
            </div>
            <div class="p-4">
                <canvas id="categoryValueChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>

    {{-- Low Stock Products --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200 px-4 py-3 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-yellow-500">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <h5 class="font-medium text-gray-800">Low Stock Products (Below {{ $threshold }} units)</h5>
            </div>
            <div>
                <form method="GET" action="{{ route('admin.reports.inventory') }}" class="inline-flex items-center gap-2">
                    <label class="text-sm text-gray-600">Threshold:</label>
                    <select name="threshold" class="border border-gray-300 rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                        <option value="5" {{ $threshold == 5 ? 'selected' : '' }}>5 units</option>
                        <option value="10" {{ $threshold == 10 ? 'selected' : '' }}>10 units</option>
                        <option value="20" {{ $threshold == 20 ? 'selected' : '' }}>20 units</option>
                        <option value="50" {{ $threshold == 50 ? 'selected' : '' }}>50 units</option>
                    </select>
                </form>
            </div>
        </div>
        <div class="p-4 overflow-x-auto">
            @if($lowStockProducts->isNotEmpty())
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Value</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($lowStockProducts as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        @if($product->images && $product->images->first())
                                            <img src="{{ $product->images->first()->full_url }}" 
                                                 alt="{{ $product->name }}"
                                                 class="w-12 h-12 object-cover rounded-lg">
                                        @else
                                            <img src="{{ asset('images/no-image.jpg') }}" 
                                                 alt="No Image"
                                                 class="w-12 h-12 object-cover rounded-lg">
                                        @endif
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $product->name }}</div>
                                            @if($product->sale_price)
                                                <span class="text-xs text-green-600">On Sale</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->sku }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->category->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->brand->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 text-xs rounded-full {{ $product->stock == 0 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $product->stock }} units
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($product->sale_price)
                                        <span class="line-through text-gray-400">৳{{ number_format($product->base_price) }}</span>
                                        <br>
                                        <span class="font-bold text-red-600">৳{{ number_format($product->sale_price) }}</span>
                                    @else
                                        ৳{{ number_format($product->base_price) }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">৳{{ number_format($product->stock * ($product->sale_price ?? $product->base_price), 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('admin.inventory.show', $product) }}" class="inline-flex items-center gap-1 bg-cyan-500 hover:bg-cyan-600 text-white px-3 py-1 rounded-lg text-sm transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path>
                                            <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon>
                                        </svg>
                                        Restock
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-center text-gray-500 py-8">No low stock products found.</p>
            @endif
        </div>
    </div>

    {{-- Out of Stock Products --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200 px-4 py-3">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-500">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
                <h5 class="font-medium text-gray-800">Out of Stock Products</h5>
            </div>
        </div>
        <div class="p-4 overflow-x-auto">
            @if($outOfStockProducts->isNotEmpty())
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($outOfStockProducts as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        @if($product->images && $product->images->first())
                                            <img src="{{ $product->images->first()->full_url }}" 
                                                 alt="{{ $product->name }}"
                                                 class="w-12 h-12 object-cover rounded-lg">
                                        @else
                                            <img src="{{ asset('images/no-image.jpg') }}" 
                                                 alt="No Image"
                                                 class="w-12 h-12 object-cover rounded-lg">
                                        @endif
                                        <div class="font-medium text-gray-900">{{ $product->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->sku }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->category->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->brand->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $product->updated_at instanceof Carbon ? $product->updated_at->format('M d, Y') : \Carbon\Carbon::parse($product->updated_at)->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('admin.inventory.show', $product) }}" class="inline-flex items-center gap-1 bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg text-sm transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12h14"></path>
                                            <path d="M12 5v14"></path>
                                        </svg>
                                        Restock
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-center text-gray-500 py-8">No out of stock products found.</p>
            @endif
        </div>
    </div>

    {{-- Best Selling Products --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200 px-4 py-3">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-500">
                    <circle cx="12" cy="8" r="6"></circle>
                    <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"></path>
                </svg>
                <h5 class="font-medium text-gray-800">Best Selling Products</h5>
            </div>
        </div>
        <div class="p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sold</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Turnover Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Until Stockout</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($bestSellingProducts as $product)
                        @php
                            $dailySales = $product->total_sold / 30; // Average daily sales (last 30 days)
                            $daysUntilStockout = $dailySales > 0 ? floor($product->stock / $dailySales) : 999;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $product->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->sku }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-full 
                                    {{ $product->stock > 10 ? 'bg-green-100 text-green-800' : ($product->stock > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $product->stock }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($product->total_sold) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($product->stock > 0)
                                    {{ number_format($product->total_sold / $product->stock, 2) }}x
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($daysUntilStockout < 0)
                                    <span class="text-red-600">Out of stock</span>
                                @elseif($daysUntilStockout < 7)
                                    <span class="text-yellow-600">{{ $daysUntilStockout }} days</span>
                                @elseif($daysUntilStockout < 30)
                                    <span class="text-cyan-600">{{ $daysUntilStockout }} days</span>
                                @else
                                    <span class="text-green-600">{{ $daysUntilStockout }}+ days</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.bg-cyan-600 {
    background-color: #0891b2;
}
.bg-cyan-500 {
    background-color: #06b6d4;
}
.hover\:bg-cyan-600:hover {
    background-color: #0891b2;
}
.dropdown-menu {
    z-index: 50;
}
.dropdown-toggle + .dropdown-menu {
    display: none;
}
.dropdown-toggle:hover + .dropdown-menu,
.dropdown-menu:hover {
    display: block;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Stock Status Chart
    const statusCtx = document.getElementById('stockStatusChart');
    if (statusCtx) {
        new Chart(statusCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['In Stock', 'Low Stock', 'Out of Stock'],
                datasets: [{
                    data: [
                        {{ $stockSummary['in_stock'] }},
                        {{ $stockSummary['low_stock'] }},
                        {{ $stockSummary['out_of_stock'] }}
                    ],
                    backgroundColor: [
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Category Value Chart
    const categoryCtx = document.getElementById('categoryValueChart');
    if (categoryCtx) {
        new Chart(categoryCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($inventoryValue->pluck('name')) !!},
                datasets: [{
                    label: 'Inventory Value (৳)',
                    data: {!! json_encode($inventoryValue->pluck('total_value')) !!},
                    backgroundColor: 'rgb(59, 130, 246)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '৳' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush