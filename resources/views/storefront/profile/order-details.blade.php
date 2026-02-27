@extends('layouts.user')

@section('title', 'Order Details')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Order #{{ $order->id }}</h1>
                    <p class="mt-2 text-sm text-gray-600">Placed on {{ $order->created_at->format('M d, Y') }}</p>
                </div>
                <a href="{{ route('profile.orders') }}" class="text-sm text-indigo-600 hover:text-indigo-800 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Orders
                </a>
            </div>
        </div>

        <!-- Order Details -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-xl font-semibold text-gray-900">Order Details</h2>
            </div>
            
            <div class="p-6">
                <!-- Order Status -->
                <div class="mb-6">
                    <span class="px-3 py-1 text-sm font-medium rounded-full 
                        @if($order->status == 'delivered') bg-green-100 text-green-800
                        @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                        @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        Status: {{ ucfirst($order->status) }}
                    </span>
                </div>

                <!-- Order Items -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h3>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex items-center justify-between border-b pb-4">
                                <div class="flex items-center space-x-4">
                                    <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}" class="w-16 h-16 object-cover rounded">
                                    <div>
                                        <h4 class="font-medium">{{ $item->product->name }}</h4>
                                        <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                                    </div>
                                </div>
                                <p class="font-medium">৳{{ number_format($item->price * $item->quantity, 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="border-t pt-4">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium">৳{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Shipping:</span>
                        <span class="font-medium">৳{{ number_format($order->shipping_cost, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Tax:</span>
                        <span class="font-medium">৳{{ number_format($order->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold mt-4 pt-4 border-t">
                        <span>Total:</span>
                        <span class="text-indigo-600">৳{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>

                <!-- Shipping Address -->
                @if($order->shipping_address)
                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Shipping Address</h3>
                        <p class="text-gray-600">{{ $order->shipping_address }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection