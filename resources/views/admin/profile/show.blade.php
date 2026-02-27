{{-- resources/views/profile/show.blade.php --}}

@extends('layouts.admin')

@section('title', 'My Profile - ' . config('app.name'))

@section('content')
<div class="mx-auto">
    <div class="">
        {{-- Header --}}
        <div class="flex items-end justify-end mb-6">
            <!-- <h1 class="text-2xl font-bold text-gray-900">My Profile</h1> -->
            <a href="{{ route('admin.profile.edit') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                </svg>
                Edit Profile
            </a>
        </div>

        {{-- Profile Information --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6">
                <div class="flex items-center space-x-4 mb-6">
                    {{-- Avatar --}}
                    <div class="w-20 h-20 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white text-2xl font-bold">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold">{{ $user->name }}</h2>
                        <p class="text-gray-600">{{ $user->email }}</p>
                        <p class="text-sm text-gray-500 mt-1">Member since {{ $user->created_at->format('F Y') }}</p>
                    </div>
                </div>
                

                {{-- Profile Details --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-medium text-gray-900 mb-3">Personal Information</h3>
                        <dl class="space-y-2">
                            <div class="flex">
                                <dt class="w-24 text-sm text-gray-600">Full Name:</dt>
                                <dd class="text-sm font-medium">{{ $user->name }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="w-24 text-sm text-gray-600">Email:</dt>
                                <dd class="text-sm">{{ $user->email }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="w-24 text-sm text-gray-600">Phone:</dt>
                                <dd class="text-sm">{{ $user->phone ?? 'Not provided' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="font-medium text-gray-900 mb-3">Address Information</h3>
                        <dl class="space-y-2">
                            <div class="flex">
                                <dt class="w-24 text-sm text-gray-600">Address:</dt>
                                <dd class="text-sm">{{ $user->address ?? 'Not provided' }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="w-24 text-sm text-gray-600">City:</dt>
                                <dd class="text-sm">{{ $user->city ?? 'Not provided' }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="w-24 text-sm text-gray-600">Country:</dt>
                                <dd class="text-sm">{{ $user->country ?? 'Not provided' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- {{-- Quick Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <div class="text-3xl font-bold text-blue-600 mb-2">{{ $user->orders()->count() }}</div>
                <div class="text-sm text-gray-600">Total Orders</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <div class="text-3xl font-bold text-green-600 mb-2">{{ $user->wishlist()->count() }}</div>
                <div class="text-sm text-gray-600">Wishlist Items</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <div class="text-3xl font-bold text-purple-600 mb-2">{{ $user->reviews()->count() }}</div>
                <div class="text-sm text-gray-600">Reviews</div>
            </div>
        </div>

        {{-- Recent Orders --}}
        @if($user->orders()->count() > 0)
        <div class="bg-white rounded-lg shadow-sm mt-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900">Recent Orders</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($user->orders()->latest()->limit(5)->get() as $order)
                <div class="px-6 py-4 flex items-center justify-between">
                    <div>
                        <p class="font-medium">Order #{{ $order->order_number }}</p>
                        <p class="text-sm text-gray-600">{{ $order->created_at->format('d M Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-blue-600">৳{{ number_format($order->total, 2) }}</p>
                        <span class="inline-block px-2 py-1 text-xs rounded-full 
                            @if($order->status == 'delivered') bg-green-100 text-green-800
                            @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div> -->
        @endif
    </div>
</div>
@endsection