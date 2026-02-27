@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 pb-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <!-- <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Account</h1>
            <p class="mt-2 text-xl text-center text-gray-600">Welcome back, <span class="font-semibold text-indigo-600">{{ Auth::user()->name }}</span></p>
        </div> -->

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Main Content -->
            <div class="flex-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <!-- <div class="p-6 border-b border-gray-100">
                        <h2 class="text-xl font-semibold text-gray-900">Dashboard Overview</h2>
                    </div> -->
                    
                    <div class="p-6">
                        <!-- Stats Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <!-- Total Orders -->
                            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium bg-white/20 px-3 py-1 rounded-full">Total</span>
                                </div>
                                <p class="text-3xl font-bold mb-1">{{ $totalOrders ?? 0 }}</p>
                                <p class="text-sm text-white/80">Orders Placed</p>
                            </div>

                            <!-- Wishlist -->
                            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-6 text-white">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium bg-white/20 px-3 py-1 rounded-full">Items</span>
                                </div>
                                <p class="text-3xl font-bold mb-1">{{ $wishlistCount ?? 0 }}</p>
                                <p class="text-sm text-white/80">In Wishlist</p>
                            </div>

                            <!-- Addresses -->
                            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-6 text-white">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium bg-white/20 px-3 py-1 rounded-full">Saved</span>
                                </div>
                                <p class="text-3xl font-bold mb-1">{{ $addressCount ?? 0 }}</p>
                                <p class="text-sm text-white/80">Addresses</p>
                            </div>
                        </div>

                        <!-- Recent Orders -->
                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                                <a href="{{ route('profile.orders') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 flex items-center">
                                    View All
                                    <svg class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($recentOrders ?? [] as $order)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $order->id }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $order->created_at->format('M d, Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">৳{{ number_format($order->total, 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                        @if($order->status == 'delivered') bg-green-100 text-green-800
                                                        @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                                                        @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                                        @else bg-yellow-100 text-yellow-800 @endif">
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <a href="{{ route('profile.order.show', $order->id) }}" class="text-indigo-600 hover:text-indigo-800">View</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-12 text-center">
                                                    <div class="flex flex-col items-center">
                                                        <svg class="h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                        </svg>
                                                        <p class="text-gray-500 text-sm">No orders found</p>
                                                        <a href="{{ route('product.index') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                                            Start Shopping
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <a href="{{ route('profile.addresses') }}" class="group relative bg-gray-50 rounded-xl p-6 hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="h-12 w-12 bg-indigo-100 rounded-lg flex items-center justify-center group-hover:bg-indigo-200 transition-colors">
                                        <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">Add New Address</h4>
                                        <!-- <p class="text-sm text-gray-600">Set up your delivery addresses</p> -->
                                    </div>
                                </div>
                            </a>

                            <a href="{{ route('profile.settings') }}" class="group relative bg-gray-50 rounded-xl p-6 hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                                        <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">Update Profile</h4>
                                        <!-- <p class="text-sm text-gray-600">Change your personal information</p> -->
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection