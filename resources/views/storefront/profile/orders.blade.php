@extends('layouts.user')

@section('title', 'My Orders - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-gray-50 py-6 sm:py-8 lg:py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                <a href="{{ route('profile.dashboard') }}" class="hover:text-indigo-600 transition-colors">
                    Dashboard
                </a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-900 font-medium">My Orders</span>
            </div>

            {{-- Header with Title and Button --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">My Orders</h1>
                <a href="{{ route('product.index') }}" 
                class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Continue Shopping
                </a>
            </div>
        </div>

        <!-- Orders List -->
        @if($orders->isNotEmpty())
            <div class="space-y-4 sm:space-y-6">
                @foreach($orders as $order)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
                        {{-- Order Header --}}
                        <div class="bg-gradient-to-r from-gray-50 to-white px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                            <div class="flex flex-wrap items-center gap-2 sm:gap-4">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16 4 16M9 12h6" />
                                    </svg>
                                    <span class="text-sm sm:text-base font-semibold text-gray-900">#{{ $order->order_number }}</span>
                                </div>
                                <span class="text-xs sm:text-sm text-gray-500 flex items-center">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $order->created_at->format('M d, Y') }}
                                </span>
                            </div>
                            
                            {{-- Status Badge --}}
                            <span class="inline-flex items-center px-2.5 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium w-fit
                                @if($order->status == 'delivered') bg-green-100 text-green-700
                                @elseif($order->status == 'cancelled') bg-red-100 text-red-700
                                @elseif($order->status == 'shipped') bg-blue-100 text-blue-700
                                @elseif($order->status == 'processing') bg-purple-100 text-purple-700
                                @elseif($order->status == 'confirmed') bg-cyan-100 text-cyan-700
                                @elseif($order->status == 'pending') bg-yellow-100 text-yellow-700
                                @else bg-gray-100 text-gray-700
                                @endif">
                                <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full mr-1.5
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
                        </div>

                        {{-- Order Items --}}
                        <div class="p-4 sm:p-6">
                            <div class="space-y-3 sm:space-y-4">
                                @foreach($order->items as $item)
                                    <div class="flex items-center {{ !$loop->last ? 'border-b border-gray-100 pb-3 sm:pb-4' : '' }}">
                                        {{-- Product Image --}}
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
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Product Details --}}
                                        <div class="ml-3 sm:ml-4 flex-1 min-w-0">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1 sm:gap-2">
        <div>
            <h4 class="text-sm sm:text-base font-semibold text-gray-900 truncate max-w-[200px] sm:max-w-xs">{{ $item->name }}</h4>
            @if($item->attributes)
                <p class="text-xs text-gray-500 mt-1">
                    @php
                        $attributes = $item->attributes;
                        $formattedAttributes = [];
                        
                        if (is_string($attributes)) {
                            // Try to decode JSON string
                            $decoded = json_decode($attributes, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $attributes = $decoded;
                            }
                        }
                        
                        if (is_array($attributes) || is_object($attributes)) {
                            foreach($attributes as $key => $value) {
                                if (!empty($value) && $value !== 'null' && $value !== null) {
                                    $formattedAttributes[] = ucfirst($key) . ': ' . $value;
                                }
                            }
                        }
                    @endphp
                    
                    @if(!empty($formattedAttributes))
                        <span class="inline-flex flex-wrap gap-2">
                            @foreach($formattedAttributes as $attr)
                                <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">
                                    {{ $attr }}
                                </span>
                            @endforeach
                        </span>
                    @elseif(is_string($attributes) && !empty($attributes) && $attributes !== 'null')
                        <span class="text-gray-600">{{ $attributes }}</span>
                    @endif
                </p>
            @endif
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Qty: {{ $item->quantity }}</p>
        </div>
        <span class="text-sm sm:text-base font-bold text-indigo-600 whitespace-nowrap">
            ৳{{ number_format($item->price * $item->quantity, 2) }}
        </span>
    </div>
</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Order Footer --}}
                        <div class="bg-gradient-to-r from-gray-50 to-white px-4 sm:px-6 py-3 sm:py-4 border-t border-gray-100 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                            <div class="flex items-center">
                                <span class="text-xs sm:text-sm text-gray-600">Total Amount:</span>
                                <span class="text-lg sm:text-xl font-bold text-indigo-600 ml-2">৳{{ number_format($order->total, 2) }}</span>
                                
                                {{-- Payment Status --}}
                                @if(isset($order->payment_status))
                                    <span class="ml-3 sm:ml-4 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        @if($order->payment_status == 'paid') bg-green-100 text-green-700
                                        @elseif($order->payment_status == 'failed') bg-red-100 text-red-700
                                        @else bg-yellow-100 text-yellow-700
                                        @endif">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                        </svg>
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                @endif
                            </div>
                            
                            <div class="flex items-center gap-3">
                                {{-- Track Order Button (if shipped) --}}
                                @if(in_array($order->status, ['shipped', 'delivered']) && isset($order->tracking_number))
                                    <button onclick="trackOrder('{{ $order->tracking_number }}')" 
                                            class="inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                        </svg>
                                        Track
                                    </button>
                                @endif

                                {{-- View Details Button --}}
                                <a href="{{ route('profile.order.show', $order) }}" 
                                   class="inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                                    View Details
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6 sm:mt-8">
                {{ $orders->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 sm:p-12 lg:p-16 text-center">
                <div class="max-w-md mx-auto">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-4 sm:mb-6 bg-indigo-50 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">No orders yet</h3>
                    <p class="text-sm sm:text-base text-gray-600 mb-6 sm:mb-8">Looks like you haven't placed any orders. Start shopping to see your orders here!</p>
                    <a href="{{ route('product.index') }}" 
                       class="inline-flex items-center justify-center px-5 sm:px-6 py-2.5 sm:py-3 bg-indigo-600 text-white text-sm sm:text-base font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Start Shopping
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
function trackOrder(trackingNumber) {
    // You can implement tracking modal or redirect to courier website
    alert('Tracking feature coming soon. Tracking Number: ' + trackingNumber);
}
</script>
@endpush