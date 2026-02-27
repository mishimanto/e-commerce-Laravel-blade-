@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">User: <span class="text-indigo-600">{{ $user->name }}</span></h1>
        <div class="flex space-x-2">
            <!-- <a href="{{ route('admin.users.edit', $user) }}" 
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit User
            </a> -->
            <a href="{{ route('admin.users.index') }}" 
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    <!-- User Details Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- User Profile Header -->
        <div class="px-6 py-8 bg-gradient-to-r from-blue-500 to-purple-600">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-20 h-20 rounded-full bg-white flex items-center justify-center text-3xl font-bold text-indigo-600">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="ml-6 flex items-center justify-between w-full">

                    <!-- Left Side -->
                    <div class="flex items-center">
                        <h2 class="text-2xl font-bold text-white">{{ $user->name }}</h2>

                        @if($user->is_active)
                            <span class="ml-3 flex h-3 w-3 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </span>
                        @else
                            <span class="ml-3 inline-flex rounded-full h-3 w-3 bg-gray-400"></span>
                        @endif
                    </div>

                    <!-- Right Side -->
                    <div>
                        <p class="text-gray-200 text-sm">
                            Member since <span class="font-bold">{{ $user->created_at->format('F d, Y') }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="p-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Personal Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Personal Information
                    </h3>
                    <dl class="space-y-3">
                        <div class="flex py-2 border-b border-gray-100">
                            <dt class="w-32 text-sm font-medium text-gray-500">Full Name:</dt>
                            <dd class="text-sm text-gray-900">{{ $user->name }}</dd>
                        </div>
                        <div class="flex py-2 border-b border-gray-100">
                            <dt class="w-32 text-sm font-medium text-gray-500">Email:</dt>
                            <dd class="text-sm text-gray-900">{{ $user->email }}</dd>
                        </div>
                        <div class="flex py-2 border-b border-gray-100">
                            <dt class="w-32 text-sm font-medium text-gray-500">Phone:</dt>
                            <dd class="text-sm text-gray-900">{{ $user->phone ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex py-2 border-b border-gray-100">
                            <dt class="w-32 text-sm font-medium text-gray-500">Role:</dt>
                            <dd class="text-sm">
                                 @foreach($user->roles as $role)
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full 
                                        @if(trim(strtolower($role->name)) == 'super-admin' || trim(strtolower($role->name)) == 'super admin') 
                                            bg-red-100 text-red-800
                                        @elseif(trim(strtolower($role->name)) == 'admin') 
                                            bg-red-100 text-red-600
                                        @elseif(trim(strtolower($role->name)) == 'staff') 
                                            bg-blue-100 text-blue-800
                                        @else 
                                            bg-green-100 text-green-800
                                        @endif">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                @endforeach
                            </dd>
                        </div>
                        <div class="flex py-2 border-b border-gray-100">
                            <dt class="w-32 text-sm font-medium text-gray-500">Status:</dt>
                            <dd class="text-sm">
                                @if($user->is_active)
                                    <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Inactive</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Address Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Address Information
                    </h3>
                    <dl class="space-y-3">
                        <div class="flex py-2 border-b border-gray-100">
                            <dt class="w-32 text-sm font-medium text-gray-500">Address:</dt>
                            <dd class="text-sm text-gray-900">{{ $user->address ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex py-2 border-b border-gray-100">
                            <dt class="w-32 text-sm font-medium text-gray-500">City:</dt>
                            <dd class="text-sm text-gray-900">{{ $user->city ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex py-2 border-b border-gray-100">
                            <dt class="w-32 text-sm font-medium text-gray-500">State:</dt>
                            <dd class="text-sm text-gray-900">{{ $user->state ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex py-2 border-b border-gray-100">
                            <dt class="w-32 text-sm font-medium text-gray-500">Postal Code:</dt>
                            <dd class="text-sm text-gray-900">{{ $user->zip ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex py-2 border-b border-gray-100">
                            <dt class="w-32 text-sm font-medium text-gray-500">Country:</dt>
                            <dd class="text-sm text-gray-900">{{ $user->country ?? 'Bangladesh' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Order Statistics -->
            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    Order Statistics
                </h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <p class="text-sm font-medium text-blue-800">Total Orders</p>
                        <p class="mt-2 text-3xl font-semibold text-blue-900">{{ $user->orders_count ?? 0 }}</p>
                    </div>
                    <div class="p-4 bg-green-50 rounded-lg">
                        <p class="text-sm font-medium text-green-800">Total Spent</p>
                        <p class="mt-2 text-3xl font-semibold text-green-900">৳ {{ number_format($user->orders_sum_total ?? 0, 2) }}</p>
                    </div>
                    <div class="p-4 bg-purple-50 rounded-lg">
                        <p class="text-sm font-medium text-purple-800">Average Order Value</p>
                        <p class="mt-2 text-3xl font-semibold text-purple-900">
                            ৳ {{ $user->orders_count > 0 ? number_format(($user->orders_sum_total ?? 0) / $user->orders_count, 2) : 0 }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            @if(isset($user->orders) && $user->orders->count() > 0)
            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Recent Orders
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($user->orders as $order)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900">#{{ $order->id }}</td>
                                <td class="px-4 py-2 text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900">৳{{ number_format($order->total, 2) }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                        @if($order->status == 'completed') bg-green-100 text-green-800
                                        @elseif($order->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                                        @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Addresses -->
            @if(isset($user->addresses) && $user->addresses->count() > 0)
            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                    </svg>
                    Saved Addresses
                </h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach($user->addresses as $address)
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <p class="text-sm text-gray-900">{{ $address->address }}</p>
                        <p class="text-sm text-gray-600">{{ $address->city }}, {{ $address->state }} {{ $address->zip }}</p>
                        <p class="text-sm text-gray-600">{{ $address->country }}</p>
                        @if($address->is_default)
                            <span class="inline-flex px-2 py-1 mt-2 text-xs font-medium bg-green-100 text-green-800 rounded-full">Default</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection