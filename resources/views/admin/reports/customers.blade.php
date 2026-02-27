@extends('layouts.admin')

@section('title', 'Customer Report')

@section('content')
<div class="container-fluid px-4">
    {{-- Header with buttons --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Customer Report</h1>
        <div class="flex items-center gap-2">
            {{-- Export Dropdown --}}
            <div class="relative inline-block text-left">
                <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <span>Export</span>
                </button>
                <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden group-hover:block">
                    <a href="{{ route('admin.reports.export', ['type' => 'customers', 'format' => 'excel']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Excel</a>
                    <a href="{{ route('admin.reports.export', ['type' => 'customers', 'format' => 'csv']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">CSV</a>
                    <a href="{{ route('admin.reports.export', ['type' => 'customers', 'format' => 'pdf']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">PDF</a>
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

    {{-- Date Range Filter --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="p-4">
            <form method="GET" action="{{ route('admin.reports.customers') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Period</label>
                    <select name="period" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                        <option value="week" {{ $period == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="quarter" {{ $period == 'quarter' ? 'selected' : '' }}>This Quarter</option>
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
                    <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors w-full">Apply</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-600 text-white rounded-lg shadow-sm p-4">
            <div class="text-sm opacity-90">Total Customers</div>
            <div class="text-2xl font-bold">{{ number_format($customerOverview['total_customers']) }}</div>
        </div>

        <div class="bg-green-600 text-white rounded-lg shadow-sm p-4">
            <div class="text-sm opacity-90">New Customers</div>
            <div class="text-2xl font-bold">{{ number_format($customerOverview['new_customers']) }}</div>
            <div class="mt-1 text-xs bg-white text-green-600 px-2 py-1 rounded-full inline-block">
                {{ number_format($customerOverview['customer_acquisition_rate'], 1) }}% growth
            </div>
        </div>

        <div class="bg-cyan-600 text-white rounded-lg shadow-sm p-4">
            <div class="text-sm opacity-90">Active Customers</div>
            <div class="text-2xl font-bold">{{ number_format($customerOverview['active_customers']) }}</div>
        </div>

        <div class="bg-yellow-600 text-white rounded-lg shadow-sm p-4">
            <div class="text-sm opacity-90">Repeat Customers</div>
            <div class="text-2xl font-bold">{{ number_format($customerOverview['repeat_customers']) }}</div>
            <div class="mt-1 text-xs bg-white text-yellow-600 px-2 py-1 rounded-full inline-block">
                {{ number_format($customerOverview['repeat_customer_rate'], 1) }}% repeat rate
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-6">
        {{-- Customer Growth Chart --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-4 py-3">
                <h5 class="font-medium text-gray-800">Customer Growth</h5>
            </div>
            <div class="p-4">
                <canvas id="customerGrowthChart" style="height: 300px;"></canvas>
            </div>
        </div>

        {{-- New vs Returning Chart --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-4 py-3">
                <h5 class="font-medium text-gray-800">New vs Returning</h5>
            </div>
            <div class="p-4">
                <canvas id="customerTypeChart" style="height: 250px;"></canvas>
                <div class="mt-4 text-center">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <div class="text-green-600 font-bold">{{ number_format($newVsReturning['first_time_percentage'], 1) }}%</div>
                            <span class="text-xs text-gray-500">New</span>
                        </div>
                        <div>
                            <div class="text-blue-600 font-bold">{{ number_format($newVsReturning['returning_percentage'], 1) }}%</div>
                            <span class="text-xs text-gray-500">Returning</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Customers Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200 px-4 py-3">
            <h5 class="font-medium text-gray-800">Top Customers</h5>
        </div>
        <div class="p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Orders</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Spent</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($topCustomers as $customer)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ $customer->name }}</div>
                                <div class="text-sm text-gray-500">{{ $customer->email }}</div>
                            </td>
                            <td class="px-6 py-4">{{ $customer->orders_count }}</td>
                            <td class="px-6 py-4 font-medium">৳{{ number_format($customer->orders_sum_total ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Customer Acquisition --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-4 py-3">
                <h5 class="font-medium text-gray-800">Customer Acquisition</h5>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-3 gap-2 text-center mb-3">
                    <div class="border rounded-lg p-3">
                        <div class="text-xs text-gray-500">7 Days</div>
                        <div class="text-xl font-bold text-green-600">{{ number_format($customerAcquisition['last_7_days']) }}</div>
                    </div>
                    <div class="border rounded-lg p-3">
                        <div class="text-xs text-gray-500">30 Days</div>
                        <div class="text-xl font-bold text-cyan-600">{{ number_format($customerAcquisition['last_30_days']) }}</div>
                    </div>
                    <div class="border rounded-lg p-3">
                        <div class="text-xs text-gray-500">90 Days</div>
                        <div class="text-xl font-bold text-blue-600">{{ number_format($customerAcquisition['last_90_days']) }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Locations --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-4 py-3">
                <h5 class="font-medium text-gray-800">Top Locations</h5>
            </div>
            <div class="p-4">
                @foreach($customerLocations as $location)
                    <div class="flex justify-between items-center mb-2">
                        <span>{{ $location->city ?? 'Unknown' }}</span>
                        <span class="font-medium">{{ number_format($location->count) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Period selector
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

    // Customer Growth Chart
    const growthCtx = document.getElementById('customerGrowthChart');
    if (growthCtx) {
        const growthLabels = {!! json_encode($customerGrowth->pluck('month')->map(function($m) { 
            return \Carbon\Carbon::parse($m)->format('M Y'); 
        })) !!};
        const growthData = {!! json_encode($customerGrowth->pluck('count')) !!};
        
        new Chart(growthCtx, {
            type: 'line',
            data: {
                labels: growthLabels,
                datasets: [{
                    label: 'New Customers',
                    data: growthData,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }

    // Customer Type Chart
    const typeCtx = document.getElementById('customerTypeChart');
    if (typeCtx) {
        new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: ['New', 'Returning'],
                datasets: [{
                    data: [
                        {{ $newVsReturning['first_time'] }},
                        {{ $newVsReturning['returning'] }}
                    ],
                    backgroundColor: ['rgb(16, 185, 129)', 'rgb(59, 130, 246)']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
});
</script>
@endpush