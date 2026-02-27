@extends('layouts.admin')

@section('title', 'Sales Report')

@section('content')
<div class="">
    {{-- Header with buttons --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Sales Report</h1>
        <div class="flex items-center gap-2">
            {{-- Export Dropdown --}}
            <div class="relative inline-block text-left">
                <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-6 py-1.5 rounded-sm flex items-center gap-2 transition-colors dropdown-toggle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <span>Export</span>
                </button>
                <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden dropdown-menu">
                    <a href="{{ route('admin.reports.export', ['type' => 'sales', 'format' => 'excel']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Excel</a>
                    <a href="{{ route('admin.reports.export', ['type' => 'sales', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">CSV</a>
                    <a href="{{ route('admin.reports.export', ['type' => 'sales', 'format' => 'pdf']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">PDF</a>
                </div>
            </div>
            
            {{-- Print Button --}}
            <button type="button" onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-1.5 rounded-sm flex items-center gap-2 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                <span>Print</span>
            </button>
        </div>
    </div>

    {{-- Date Range Filter --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="p-4">
            <form method="GET" action="{{ route('admin.reports.sales') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Period</label>
                    <select name="period" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                        <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ $period == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="week" {{ $period == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="year" {{ $period == 'year' ? 'selected' : '' }}>This Year</option>
                        <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>
                
                <div id="startDateField" style="{{ $period != 'custom' ? 'display: none;' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           value="{{ $startDate instanceof Carbon ? $startDate->format('Y-m-d') : \Carbon\Carbon::parse($startDate)->format('Y-m-d') }}">
                </div>
                
                <div id="endDateField" style="{{ $period != 'custom' ? 'display: none;' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           value="{{ $endDate instanceof Carbon ? $endDate->format('Y-m-d') : \Carbon\Carbon::parse($endDate)->format('Y-m-d') }}">
                </div>
                
                <div id="filterButton" class="flex items-end" style="{{ $period != 'custom' ? 'display: none;' : '' }}">
                    <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors w-full">Apply Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

        {{-- Total Orders --}}
        <div class="bg-green-600 text-white rounded-lg shadow-sm h-24">
            <div class="p-4 flex justify-between items-center h-full">
                <div>
                    <div class="text-sm opacity-90">Total Orders</div>
                    <div class="text-2xl font-bold">{{ number_format($salesOverview['total_orders']) }}</div>
                    <div class="mt-1">
                        <span class="bg-white text-green-600 text-xs px-2 py-1 rounded-full">
                            {{ number_format($salesOverview['orders_growth'], 1) }}% vs previous
                        </span>
                    </div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
            </div>
        </div>

        {{-- Total Items Sold --}}
        <div class="bg-yellow-600 text-white rounded-lg shadow-sm h-24">
            <div class="p-4 flex justify-between items-center h-full">
                <div>
                    <div class="text-sm opacity-90">Total Items Sold</div>
                    <div class="text-2xl font-bold">{{ number_format($salesOverview['total_items']) }}</div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50">
                    <path d="M20 7h-4.5A2.5 2.5 0 0 1 13 4.5V3"></path>
                    <path d="M20 7v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5Z"></path>
                </svg>
            </div>
        </div>
    
        {{-- Total Sales --}}
        <div class="bg-primary-600 text-white rounded-lg shadow-sm h-24">
            <div class="p-4 flex justify-between items-center h-full">
                <div>
                    <div class="text-sm opacity-90">Total Sales</div>
                    <div class="text-2xl font-bold">৳ {{ number_format($salesOverview['total_sales'], 2) }}<span class="text-gray-400 text-xs px-2">+ Discount: ৳ {{ number_format($profitOverview['total_discount'], 2) }}</span></div>
                    <div class="mt-1">
                        <span class="bg-white text-blue-600 text-xs px-2 py-1 rounded-full">
                            {{ number_format($salesOverview['sales_growth']) }}% vs previous
                        </span>
                       
                    </div>                   
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50">
                    <line x1="12" y1="2" x2="12" y2="22"></line>
                    <path d="M17 5H9.5M17 12h-5M17 19h-5"></path>
                </svg>
            </div>
        </div>

        {{-- Expenses --}}
        <div class="bg-red-600 text-white rounded-lg shadow-sm h-24">
            <div class="p-4 flex justify-between items-center h-full">
                <div>
                    <div class="text-sm opacity-90">Expenses</div>
                    <div class="text-2xl font-bold">৳ {{ number_format($profitOverview['total_shipping'] + $profitOverview['total_tax'], 2) }}</div>                    
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50">
                    <path d="M4 7h16M4 12h16M4 17h16"></path>
                </svg>
            </div>
        </div>

         {{-- Total Revenue --}}
        <div class="bg-blue-600 text-white rounded-lg shadow-sm h-24">
            <div class="p-4 flex justify-between items-center h-full">
                <div>
                    <div class="text-sm opacity-90">Total Revenue</div>
                    <div class="text-2xl font-bold">৳ {{ number_format($profitOverview['total_revenue'], 2) }}</div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50">
                    <path d="M12 2v20M17 5H9.5M17 12h-5M17 19h-5"></path>
                </svg>
            </div>
        </div>   

        {{-- Buying Cost --}}
        <div class="bg-red-600 text-white rounded-lg shadow-sm h-24">
            <div class="p-4 flex justify-between items-center h-full">
                <div>
                    <div class="text-sm opacity-90">Buying Cost</div>
                    <div class="text-2xl font-bold">৳ {{ number_format($profitOverview['total_cost'], 2) }}</div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50">
                    <path d="M12 2v20M17 5H9.5M17 12h-5M17 19h-5"></path>
                </svg>
            </div>
        </div>  

        {{-- Gross Profit --}}
        <div class="bg-green-600 text-white rounded-lg shadow-sm h-24">
            <div class="p-4 flex justify-between items-center h-full">
                <div>
                    <div class="text-sm opacity-90">Gross Profit</div>
                    <div class="text-2xl font-bold">৳ {{ number_format($profitOverview['gross_profit'], 2) }}</div>
                    <div class="mt-1">
                        <span class="bg-white text-purple-600 text-xs px-2 py-1 rounded-full">
                            {{ number_format($profitOverview['profit_margin'], 1) }}% margin
                        </span>
                    </div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50">
                    <line x1="12" y1="20" x2="12" y2="10"></line>
                    <line x1="18" y1="20" x2="18" y2="4"></line>
                    <line x1="6" y1="20" x2="6" y2="16"></line>
                </svg>
            </div>
        </div>        
        {{-- Total Discount --}}
        <div class="bg-red-600 text-white rounded-lg shadow-sm h-24">
            <div class="p-4 flex justify-between items-center h-full">
                <div>
                    <div class="text-sm opacity-90">Total Discount</div>
                    <div class="text-2xl font-bold">৳ {{ number_format($profitOverview['total_discount'], 2) }}</div>                    
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50">
                    <path d="M4 7h16M4 12h16M4 17h16"></path>
                </svg>
            </div>
        </div>       
    </div>

    {{-- Profit Overview Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        {{-- Net Profit --}}
        <div class="bg-green-600 text-white rounded-lg shadow-sm h-24">
            <div class="p-4 flex justify-between items-center h-full">
                <div>
                    <div class="text-sm opacity-90">Net Profit</div>
                    <div class="text-2xl font-bold">৳ {{ number_format($profitOverview['net_profit'], 2) }}</div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50">
                    <path d="M21 12v-2a5 5 0 0 0-5-5H8a5 5 0 0 0-5 5v2"></path>
                    <circle cx="12" cy="16" r="5"></circle>
                    <path d="m9 16 3-3 3 3"></path>
                </svg>
            </div>
        </div>
        {{-- Average Order Value --}}
        <div class="bg-blue-600 text-white rounded-lg shadow-sm h-24">
            <div class="p-4 flex justify-between items-center h-full">
                <div>
                    <div class="text-sm opacity-90">Average Order Value</div>
                    <div class="text-2xl font-bold">৳ {{ number_format($salesOverview['average_order_value'], 2) }}</div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50">
                    <path d="M21 12v-2a5 5 0 0 0-5-5H8a5 5 0 0 0-5 5v2"></path>
                    <circle cx="12" cy="16" r="5"></circle>
                    <path d="m9 16 3-3 3 3"></path>
                </svg>
            </div>
        </div>        
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6">
        {{-- Sales Chart (2/3 width) --}}
        <div class="xl:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-4 py-3">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"></line>
                        <line x1="12" y1="20" x2="12" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                    <h5 class="font-medium text-gray-800">Sales Overview</h5>
                </div>
            </div>
            <div class="p-4">
                <canvas id="salesChart" style="height: 300px;"></canvas>
            </div>
        </div>

        {{-- Payment Chart (1/3 width) --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-4 py-3">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 2a15 15 0 0 0 0 30 15 15 0 0 0 0-30"></path>
                    </svg>
                    <h5 class="font-medium text-gray-800">Payment Methods</h5>
                </div>
            </div>
            <div class="p-4">
                <canvas id="paymentChart" style="height: 250px;"></canvas>
            </div>
        </div>
    </div>

    {{-- Product-wise Profit Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200 px-4 py-3">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 7h-4.5A2.5 2.5 0 0 1 13 4.5V3"></path>
                    <path d="M20 7v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5Z"></path>
                    <path d="M9 13h6"></path>
                    <path d="M12 10v6"></path>
                </svg>
                <h5 class="font-medium text-gray-800">Product-wise Profit Analysis</h5>
            </div>
        </div>
        <div class="p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                        <!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th> -->
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Margin</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($productProfit as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $product->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->sku }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($product->total_quantity) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">৳ {{ number_format($product->total_revenue, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">৳ {{ number_format($product->total_cost, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $product->total_profit > 0 ? 'text-green-600' : 'text-red-600' }}">
                                ৳ {{ number_format($product->total_profit, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $product->profit_margin > 20 ? 'bg-green-500' : ($product->profit_margin > 10 ? 'bg-blue-500' : 'bg-yellow-500') }}" 
                                         style="width: {{ min($product->profit_margin, 100) }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 mt-1">{{ number_format($product->profit_margin, 1) }}%</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Variant-wise Profit Table (if exists) --}}
    @if($variantProfit->isNotEmpty())
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200 px-4 py-3">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                    <line x1="8" y1="10" x2="16" y2="10"></line>
                    <line x1="8" y1="14" x2="12" y2="14"></line>
                </svg>
                <h5 class="font-medium text-gray-800">Variant-wise Profit Analysis</h5>
            </div>
        </div>
        <div class="p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Margin</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($variantProfit as $variant)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $variant->product_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $attrs = is_string($variant->variant_attributes) 
                                        ? json_decode($variant->variant_attributes, true) 
                                        : $variant->variant_attributes;
                                @endphp
                                @if(is_array($attrs))
                                    @foreach($attrs as $key => $value)
                                        <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-xs rounded mr-1 mb-1">{{ $key }}: {{ $value }}</span>
                                    @endforeach
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $variant->variant_sku }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($variant->total_quantity) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">৳ {{ number_format($variant->total_revenue, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">৳ {{ number_format($variant->total_cost, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $variant->total_profit > 0 ? 'text-green-600' : 'text-red-600' }}">
                                ৳ {{ number_format($variant->total_profit, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-full {{ $variant->profit_margin > 20 ? 'bg-green-100 text-green-800' : ($variant->profit_margin > 10 ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ number_format($variant->profit_margin, 1) }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Top Selling Products Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200 px-4 py-3">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="8" r="6"></circle>
                    <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"></path>
                </svg>
                <h5 class="font-medium text-gray-800">Top Selling Products</h5>
            </div>
        </div>
        <div class="p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% of Sales</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($topProducts as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $product->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->sku }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->category ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($product->total_quantity) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">৳ {{ number_format($product->total_revenue, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($product->order_count) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php $percentage = $salesOverview['total_sales'] > 0 ? ($product->total_revenue / $salesOverview['total_sales']) * 100 : 0; @endphp
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 mt-1">{{ number_format($percentage, 1) }}%</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Daily Sales Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200 px-4 py-3">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <h5 class="font-medium text-gray-800">Daily Sales Breakdown</h5>
            </div>
        </div>
        <div class="p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items Sold</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Average Order</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($dailySales as $day)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($day['date'])->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $day['day'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($day['orders']) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($day['items']) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">৳ {{ number_format($day['sales'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">৳ {{ $day['orders'] > 0 ? number_format($day['sales'] / $day['orders'], 2) : 0 }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-bold">
                    <tr>
                        <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Total</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format(collect($dailySales)->sum('orders')) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format(collect($dailySales)->sum('items')) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">৳ {{ number_format(collect($dailySales)->sum('sales'), 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">৳ {{ number_format(collect($dailySales)->avg('sales') ?? 0, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Category Performance Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200 px-4 py-3">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 7h-4.5A2.5 2.5 0 0 1 13 4.5V3"></path>
                    <path d="M20 7v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5Z"></path>
                </svg>
                <h5 class="font-medium text-gray-800">Category Performance</h5>
            </div>
        </div>
        <div class="p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products Sold</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Share</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($categoryPerformance as $category)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $category->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($category->total_items) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">৳ {{ number_format($category->total_revenue, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($category->total_orders) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php $share = $salesOverview['total_sales'] > 0 ? ($category->total_revenue / $salesOverview['total_sales']) * 100 : 0; @endphp
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $share }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 mt-1">{{ number_format($share, 1) }}%</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Category-wise Profit Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200 px-4 py-3">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 7h-4.5A2.5 2.5 0 0 1 13 4.5V3"></path>
                    <path d="M20 7v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5Z"></path>
                    <line x1="9" y1="13" x2="15" y2="13"></line>
                    <line x1="12" y1="10" x2="12" y2="16"></line>
                </svg>
                <h5 class="font-medium text-gray-800">Category-wise Profit</h5>
            </div>
        </div>
        <div class="p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Margin</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($categoryProfit as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $category->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($category->total_quantity) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">৳ {{ number_format($category->total_revenue, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">৳ {{ number_format($category->total_cost, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $category->total_profit > 0 ? 'text-green-600' : 'text-red-600' }}">
                                ৳ {{ number_format($category->total_profit, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-full {{ $category->profit_margin > 20 ? 'bg-green-100 text-green-800' : ($category->profit_margin > 10 ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ number_format($category->profit_margin, 1) }}%
                                </span>
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
.bg-primary-600 {
    background-color: #2563eb;
}
.bg-purple-600 {
    background-color: #9333ea;
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
    // Period selector toggle
    const periodSelect = document.querySelector('select[name="period"]');
    const startDateField = document.getElementById('startDateField');
    const endDateField = document.getElementById('endDateField');
    const filterButton = document.getElementById('filterButton');
    
    if (periodSelect) {
        periodSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                startDateField.style.display = 'block';
                endDateField.style.display = 'block';
                filterButton.style.display = 'flex';
            } else {
                startDateField.style.display = 'none';
                endDateField.style.display = 'none';
                filterButton.style.display = 'none';
                this.form.submit();
            }
        });
    }

    // Sales Chart
    const salesCtx = document.getElementById('salesChart');
    if (salesCtx) {
        new Chart(salesCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($salesChartData['labels']) !!},
                datasets: [{
                    label: 'Sales (৳ )',
                    data: {!! json_encode($salesChartData['sales']) !!},
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1,
                    yAxisID: 'y'
                }, {
                    label: 'Orders',
                    data: {!! json_encode($salesChartData['orders']) !!},
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.1,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Sales (৳ )'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                        title: {
                            display: true,
                            text: 'Orders'
                        }
                    }
                }
            }
        });
    }

    // Payment Methods Chart
    const paymentCtx = document.getElementById('paymentChart');
    if (paymentCtx) {
        new Chart(paymentCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($paymentMethods->pluck('payment_method')->map(function($method) {
                    return ucfirst(str_replace('_', ' ', $method));
                })) !!},
                datasets: [{
                    data: {!! json_encode($paymentMethods->pluck('total_amount')) !!},
                    backgroundColor: [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)',
                        'rgb(139, 92, 246)'
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
});
</script>
@endpush