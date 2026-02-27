@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="">
    <!-- <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
            <p class="mt-2 text-sm text-gray-700">Welcome back, {{ Auth::user()->name }}! Here's what's happening with your store today.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <span class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Download Report
            </span>
        </div>
    </div> -->

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Today's Sales -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Today's Sales</dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900">৳{{ number_format($todaySales ?? 0, 2) }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <span class="font-medium text-{{ ($salesGrowth ?? 0) >= 0 ? 'green' : 'red' }}-600">
                        {{ ($salesGrowth ?? 0) >= 0 ? '+' : '' }}{{ $salesGrowth ?? 0 }}%
                    </span>
                    <span class="text-gray-500"> vs yesterday</span>
                </div>
            </div>
        </div>

        <!-- Today's Orders -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Today's Orders</dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900">{{ $todayOrders ?? 0 }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <span class="font-medium text-{{ ($orderGrowth ?? 0) >= 0 ? 'green' : 'red' }}-600">
                        {{ ($orderGrowth ?? 0) >= 0 ? '+' : '' }}{{ $orderGrowth ?? 0 }}%
                    </span>
                    <span class="text-gray-500"> vs yesterday</span>
                </div>
            </div>
        </div>

        <!-- Total Customers -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Customers</dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900">{{ $totalCustomers ?? 0 }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <span class="font-medium text-green-600">+{{ $newCustomers ?? 0 }}</span>
                    <span class="text-gray-500"> new this month</span>
                </div>
            </div>
        </div>

        <!-- Low Stock Products -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Low Stock Products</dt>
                            <dd>
                                <div class="text-lg font-medium text-gray-900">{{ $lowStockProducts ?? 0 }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <span class="font-medium text-red-600">{{ $outOfStock ?? 0 }}</span>
                    <span class="text-gray-500"> out of stock</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="mt-8 grid grid-cols-1 gap-5 lg:grid-cols-3">
        <!-- Sales Chart -->
        <div class="lg:col-span-2 bg-white shadow rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Sales Overview</h3>
                <p class="mt-1 text-sm text-gray-500">Last 7 days performance</p>
            </div>
            <div class="px-6 py-5">
                <div class="h-80" id="sales-chart"></div>
            </div>
        </div>

        <!-- Order Status Chart -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Order Status</h3>
                <p class="mt-1 text-sm text-gray-500">Current orders by status</p>
            </div>
            <div class="px-6 py-5">
                <div class="h-64" id="status-chart"></div>
            </div>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="mt-8">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Recent Orders</h3>
                    <p class="mt-1 text-sm text-gray-500">Latest orders from your store</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    View All
                    <svg class="ml-2 -mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentOrders ?? [] as $order)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ $order->order_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $order->user->name ?? $order->guest_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $order->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ৳{{ number_format($order->total, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'processing' => 'bg-blue-100 text-blue-800',
                                            'shipped' => 'bg-purple-100 text-purple-800',
                                            'delivered' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $colorClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $paymentColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'paid' => 'bg-green-100 text-green-800',
                                            'failed' => 'bg-red-100 text-red-800',
                                            'refunded' => 'bg-gray-100 text-gray-800',
                                        ];
                                        $paymentColor = $paymentColors[$order->payment_status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $paymentColor }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="mt-2">No recent orders found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Bottom Grid --}}
    <div class="mt-8 grid grid-cols-1 gap-5 lg:grid-cols-2">
        <!-- Top Products -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Top Selling Products</h3>
                <p class="mt-1 text-sm text-gray-500">Best performing products this month</p>
            </div>
            <div class="px-6 py-5">
                <div class="flow-root">
                    <ul role="list" class="-my-5 divide-y divide-gray-200">
                        @forelse($topProducts ?? [] as $product)
                            <li class="py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                                        <p class="text-sm text-gray-500 truncate">{{ $product->sku }}</p>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <p class="text-sm font-medium text-gray-900">{{ $product->total_sold }} sold</p>
                                        <p class="text-sm text-gray-500">৳{{ number_format($product->total_revenue, 2) }}</p>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="py-12 text-center text-sm text-gray-500">
                                No sales data available
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Recent Activities</h3>
                <p class="mt-1 text-sm text-gray-500">Latest actions in your store</p>
            </div>
            <div class="px-6 py-5">
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @forelse($activities ?? [] as $index => $activity)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex items-start space-x-3">
                                        <div class="relative">
                                            <div class="h-10 w-10 rounded-full bg-{{ $activity->causer ? 'green' : 'gray' }}-100 flex items-center justify-center ring-8 ring-white">
                                                @if($activity->causer)
                                                    <svg class="h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                @else
                                                    <svg class="h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                    </svg>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <div class="text-sm">
                                                    <span class="font-medium text-gray-900">{{ $activity->causer?->name ?? 'System' }}</span>
                                                    <span class="text-gray-500"> {{ $activity->description }}</span>
                                                </div>
                                                <p class="mt-0.5 text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="py-12 text-center text-sm text-gray-500">
                                No recent activities
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales Chart
    const salesOptions = {
        chart: {
            type: 'area',
            height: 320,
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        series: [{
            name: 'Sales',
            data: {!! json_encode($chartData ?? [0,0,0,0,0,0,0]) !!}
        }],
        xaxis: {
            categories: {!! json_encode($chartLabels ?? ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']) !!},
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                formatter: function(value) {
                    return '৳' + value.toFixed(0);
                }
            }
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        colors: ['#4f46e5'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3
            }
        },
        tooltip: {
            y: {
                formatter: function(value) {
                    return '৳' + value.toFixed(2);
                }
            }
        }
    };

    const salesChart = new ApexCharts(document.querySelector("#sales-chart"), salesOptions);
    salesChart.render();

    // Status Chart
    const statusOptions = {
        chart: {
            type: 'donut',
            height: 250
        },
        series: {!! json_encode($statusData ?? [30, 25, 20, 15, 10]) !!},
        labels: {!! json_encode($statusLabels ?? ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled']) !!},
        colors: ['#fbbf24', '#3b82f6', '#8b5cf6', '#10b981', '#ef4444'],
        legend: {
            position: 'bottom',
            fontSize: '12px'
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: function() {
                                return {!! json_encode(array_sum($statusData ?? [0])) !!};
                            }
                        }
                    }
                }
            }
        },
        dataLabels: {
            enabled: false
        },
        tooltip: {
            y: {
                formatter: function(value) {
                    return value + ' orders';
                }
            }
        }
    };

    const statusChart = new ApexCharts(document.querySelector("#status-chart"), statusOptions);
    statusChart.render();
});
</script>
@endpush
@endsection