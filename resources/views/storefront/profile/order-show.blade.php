@extends('layouts.user')

@section('title', 'Order Details - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-gray-50 py-6 sm:py-8 lg:py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header with Back Button -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                        <a href="{{ route('profile.orders') }}" class="hover:text-indigo-600 transition-colors">
                            My Orders
                        </a>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <span class="text-gray-900 font-medium">#{{ $order->order_number }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Order Details</h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}
                    </p>
                </div>
                
                <!-- Order Status Badge -->
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium
                        @if($order->status == 'delivered') bg-green-100 text-green-700
                        @elseif($order->status == 'cancelled') bg-red-100 text-red-700
                        @elseif($order->status == 'shipped') bg-blue-100 text-blue-700
                        @elseif($order->status == 'processing') bg-purple-100 text-purple-700
                        @elseif($order->status == 'confirmed') bg-cyan-100 text-cyan-700
                        @elseif($order->status == 'pending') bg-yellow-100 text-yellow-700
                        @else bg-gray-100 text-gray-700
                        @endif">
                        <span class="w-2 h-2 rounded-full mr-2
                            @if($order->status == 'delivered') bg-green-500
                            @elseif($order->status == 'cancelled') bg-red-500
                            @elseif($order->status == 'shipped') bg-blue-500
                            @elseif($order->status == 'processing') bg-purple-500
                            @elseif($order->status == 'confirmed') bg-cyan-500
                            @elseif($order->status == 'pending') bg-yellow-500
                            @else bg-gray-500
                            @endif">
                        </span>
                        {{ ucfirst($order->status) }}
                    </span>
                    
                    @if($order->payment_status)
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium
                            @if($order->payment_status == 'paid') bg-green-100 text-green-700
                            @elseif($order->payment_status == 'failed') bg-red-100 text-red-700
                            @else bg-yellow-100 text-yellow-700
                            @endif">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                            </svg>
                            Payment: {{ ucfirst($order->payment_status) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6 lg:gap-8">
            <!-- Main Content - Order Items -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Items Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-4 sm:px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Order Items
                        </h2>
                    </div>
                    
                    <div class="p-4 sm:p-6">
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="flex items-start space-x-4 {{ !$loop->last ? 'border-b border-gray-100 pb-4' : '' }}">
                                    <!-- Product Image -->
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-200">
                                        @php
                                            $imageUrl = null;
                                            if ($item->product && $item->product->images && $item->product->images->isNotEmpty()) {
                                                $image = $item->product->images->first();
                                                $imageUrl = filter_var($image->url, FILTER_VALIDATE_URL) 
                                                    ? $image->url 
                                                    : asset('storage/' . ltrim($image->url, '/'));
                                            }
                                        @endphp
                                        
                                        @if($imageUrl)
                                            <img src="{{ $imageUrl }}" 
                                                 alt="{{ $item->name }}"
                                                 class="w-full h-full object-cover"
                                                 onerror="this.onerror=null; this.src='{{ asset('images/no-image.jpg') }}';">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Product Details -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2">
                                            <div>
                                                <h3 class="text-sm sm:text-base font-semibold text-blue-700">
                                                    @if($item->product)
                                                        <a href="{{ route('product.show', $item->product->slug) }}" 
                                                        class="hover:text-indigo-600 transition-colors duration-200 inline-flex items-center group">
                                                            {{ $item->name }}
                                                        </a>
                                                    @else
                                                        <span class="text-blue-700">{{ $item->name }}</span>
                                                    @endif
                                                </h3>
                                                
                                                @if($item->attributes)
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        @php
                                                            $attributes = $item->attributes;
                                                            $attrText = '';
                                                            
                                                            if (is_string($attributes)) {
                                                                $decoded = json_decode($attributes, true);
                                                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                                    $attrParts = [];
                                                                    foreach($decoded as $key => $value) {
                                                                        if (!empty($value) && $value !== 'null') {
                                                                            $attrParts[] = ucfirst($key) . ': ' . $value;
                                                                        }
                                                                    }
                                                                    $attrText = implode(' | ', $attrParts);
                                                                } else {
                                                                    $attrText = $attributes;
                                                                }
                                                            } elseif (is_array($attributes)) {
                                                                $attrParts = [];
                                                                foreach($attributes as $key => $value) {
                                                                    if (!empty($value) && $value !== 'null') {
                                                                        $attrParts[] = ucfirst($key) . ': ' . $value;
                                                                    }
                                                                }
                                                                $attrText = implode(' | ', $attrParts);
                                                            }
                                                        @endphp
                                                        {{ $attrText }}
                                                    </p>
                                                @endif
                                                
                                                <p class="text-xs sm:text-sm text-gray-500 mt-1">
                                                    Price: ৳ {{ number_format($item->price, 2) }} × {{ $item->quantity }}
                                                </p>
                                            </div>
                                            <span class="text-sm sm:text-base font-bold text-blue-700 whitespace-nowrap">
                                                ৳ {{ number_format($item->price * $item->quantity, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Tracking Information (if available) -->
                @if($order->tracking_number)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-4 sm:px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                                Tracking Information
                            </h2>
                        </div>
                        <div class="p-4 sm:p-6">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Tracking Number</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $order->tracking_number }}</p>
                                </div>
                                @if($order->shipping_courier)
                                    <div>
                                        <p class="text-sm text-gray-600">Courier</p>
                                        <p class="text-lg font-semibold text-gray-900">{{ $order->shipping_courier }}</p>
                                    </div>
                                @endif
                                <a href="#" onclick="trackOrder('{{ $order->tracking_number }}')" 
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                    </svg>
                                    Track Order
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar - Order Summary & Addresses -->
            <div class="space-y-6">
                <!-- Order Summary Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-4 sm:px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Order Summary
                        </h2>
                    </div>
                    
                    <div class="p-4 sm:p-6">
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-medium text-gray-900">৳ {{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            
                            @if($order->discount_amount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Discount</span>
                                    <span class="font-medium text-green-600">-৳ {{ number_format($order->discount_amount, 2) }}</span>
                                </div>
                            @endif
                            
                            @if($order->coupon_discount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Coupon ({{ $order->coupon_code }})</span>
                                    <span class="font-medium text-green-600">-৳ {{ number_format($order->coupon_discount, 2) }}</span>
                                </div>
                            @endif
                            
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Shipping</span>
                                <span class="font-medium text-gray-900">৳ {{ number_format($order->shipping_cost, 2) }}</span>
                            </div>
                            
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tax</span>
                                <span class="font-medium text-gray-900">৳ {{ number_format($order->tax_amount, 2) }}</span>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-3 mt-3">
                                <div class="flex justify-between">
                                    <span class="text-base font-semibold text-gray-900">Total</span>
                                    <span class="text-xl font-bold text-indigo-600">৳ {{ number_format($order->total, 2) }}</span>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Payment Method</span>
                                    <span class="font-medium text-gray-900">{{ ucwords(str_replace('_', ' ', $order->payment_method)) }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm mt-2">
                                    <span class="text-gray-600">Shipping Method</span>
                                    <span class="font-medium text-gray-900">{{ ucwords(str_replace('_', ' ', $order->shipping_method)) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Billing Address Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-4 sm:px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Billing Address
                        </h2>
                    </div>
                    
                    <div class="p-4 sm:p-6">
                        <div class="space-y-2 text-sm">
                            <p class="font-semibold text-gray-900">{{ $order->billing_name }}</p>
                            <p class="text-gray-600">{{ $order->billing_email }}</p>
                            <p class="text-gray-600">{{ $order->billing_phone }}</p>
                            <p class="text-gray-600">{{ $order->billing_address }}</p>
                            <p class="text-gray-600">
                                {{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_zip }}
                            </p>
                            <p class="text-gray-600">{{ $order->billing_country }}</p>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address Card -->
                @if($order->shipping_address && (
                    $order->shipping_address != $order->billing_address ||
                    $order->shipping_city != $order->billing_city ||
                    $order->shipping_name != $order->billing_name
                ))
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-4 sm:px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Shipping Address
                            </h2>
                        </div>
                        
                        <div class="p-4 sm:p-6">
                            <div class="space-y-2 text-sm">
                                <p class="font-semibold text-gray-900">{{ $order->shipping_name ?? $order->billing_name }}</p>
                                <p class="text-gray-600">{{ $order->shipping_email ?? $order->billing_email }}</p>
                                <p class="text-gray-600">{{ $order->shipping_phone ?? $order->billing_phone }}</p>
                                <p class="text-gray-600">{{ $order->shipping_address ?? $order->billing_address }}</p>
                                <p class="text-gray-600">
                                    {{ $order->shipping_city ?? $order->billing_city }}, 
                                    {{ $order->shipping_state ?? $order->billing_state }} 
                                    {{ $order->shipping_zip ?? $order->billing_zip }}
                                </p>
                                <p class="text-gray-600">{{ $order->shipping_country ?? $order->billing_country }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex flex-col gap-3">
                    <!--  -->
                    <a href="{{ route('profile.orders') }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-3 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Orders
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function trackOrder(trackingNumber) {
    // You can implement tracking modal or redirect to courier website
    // For now, show an alert
    alert('Tracking feature coming soon. Tracking Number: ' + trackingNumber);
    
    // If you want to redirect to a courier tracking page:
    // window.open('https://your-courier.com/track/' + trackingNumber, '_blank');
}
</script>
@endpush
@endsection