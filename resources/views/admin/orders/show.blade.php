@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')
<div class="">
    {{-- Header with Actions --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Order #{{ $order->order_number }}</h1>
            <p class="text-sm text-gray-600 mt-1">Placed on {{ $order->created_at->format('F d, Y h:i A') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.orders.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-lg transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </a>
            <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank"
               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Invoice
            </a>
            <button onclick="window.print()" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column - Order Items & Timeline --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Order Items --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <h2 class="text-lg font-semibold text-gray-800">Order Items</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($order->items as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            <div class="flex items-center">
                                                @if($item->product && $item->product->images->first())
                                                    @php
                                                        $imageUrl = $item->product->images->first()->url;
                                                        if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                                                            $imageUrl = asset('storage/' . ltrim($imageUrl, '/'));
                                                        }
                                                    @endphp
                                                    <img src="{{ $imageUrl }}" 
                                                         alt="{{ $item->name }}"
                                                         class="w-12 h-12 object-cover rounded-lg mr-3">
                                                @endif
                                                <div>
                                                    <div class="font-medium text-gray-800">{{ $item->name }}</div>
                                                    @if($item->attributes)
                                                        @php
                                                            $attributes = is_string($item->attributes) 
                                                                ? json_decode($item->attributes, true) 
                                                                : ($item->attributes ?? []);
                                                        @endphp
                                                        @if(is_array($attributes) && count($attributes) > 0)
                                                            <div class="flex flex-wrap gap-2 mt-1">
                                                                @foreach($attributes as $key => $value)
                                                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">
                                                                        {{ ucfirst($key) }}: 
                                                                        @if(is_array($value))
                                                                            {{ implode(', ', $value) }}
                                                                        @else
                                                                            {{ $value }}
                                                                        @endif
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-600">{{ $item->sku }}</td>
                                        <td class="px-4 py-4 text-right text-sm text-gray-800">৳{{ number_format($item->price, 2) }}</td>
                                        <td class="px-4 py-4 text-center text-sm text-gray-600">{{ $item->quantity }}</td>
                                        <td class="px-4 py-4 text-right font-medium text-gray-800">৳{{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-right text-sm text-gray-600">Subtotal:</td>
                                    <td class="px-4 py-3 text-right font-medium">৳{{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-right text-sm text-gray-600">Shipping:</td>
                                    <td class="px-4 py-3 text-right font-medium">৳{{ number_format($order->shipping_cost, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-right text-sm text-gray-600">Tax:</td>
                                    <td class="px-4 py-3 text-right font-medium">৳{{ number_format($order->tax_amount, 2) }}</td>
                                </tr>
                                @if($order->discount_amount > 0)
                                    <tr>
                                        <td colspan="4" class="px-4 py-3 text-right text-sm text-green-600">Discount:</td>
                                        <td class="px-4 py-3 text-right font-medium text-green-600">-৳{{ number_format($order->discount_amount, 2) }}</td>
                                    </tr>
                                @endif
                                <tr class="border-t border-gray-300">
                                    <td colspan="4" class="px-4 py-3 text-right text-base font-bold">Total:</td>
                                    <td class="px-4 py-3 text-right text-lg font-bold text-blue-600">৳{{ number_format($order->total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Order Timeline --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h2 class="text-lg font-semibold text-gray-800">Order Timeline</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @php
                            $timeline = [
                                [
                                    'time' => $order->created_at,
                                    'title' => 'Order Placed',
                                    'description' => 'Order has been placed successfully.',
                                    'icon' => 'check',
                                    'color' => 'green'
                                ]
                            ];
                            
                            if ($order->status == 'processing' || $order->status == 'confirmed') {
                                $timeline[] = [
                                    'time' => $order->updated_at,
                                    'title' => ucfirst($order->status),
                                    'description' => 'Order is being ' . ($order->status == 'confirmed' ? 'confirmed' : 'processed') . '.',
                                    'icon' => $order->status == 'confirmed' ? 'check-circle' : 'refresh',
                                    'color' => $order->status == 'confirmed' ? 'green' : 'blue'
                                ];
                            } elseif ($order->status == 'shipped') {
                                $timeline[] = [
                                    'time' => $order->updated_at,
                                    'title' => 'Shipped',
                                    'description' => 'Order has been shipped.',
                                    'icon' => 'truck',
                                    'color' => 'purple'
                                ];
                            } elseif (in_array($order->status, ['delivered', 'completed'])) {
                                $timeline[] = [
                                    'time' => $order->updated_at,
                                    'title' => ucfirst($order->status),
                                    'description' => 'Order has been ' . ($order->status == 'delivered' ? 'delivered' : 'completed') . '.',
                                    'icon' => 'check-circle',
                                    'color' => 'green'
                                ];
                            } elseif (in_array($order->status, ['cancelled', 'refunded', 'failed'])) {
                                $timeline[] = [
                                    'time' => $order->updated_at,
                                    'title' => ucfirst($order->status),
                                    'description' => 'Order has been ' . $order->status . '.',
                                    'icon' => 'x-circle',
                                    'color' => 'red'
                                ];
                            }
                        @endphp

                        @foreach($timeline as $event)
                            <div class="flex gap-4">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 rounded-full bg-{{ $event['color'] }}-100 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-{{ $event['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($event['icon'] == 'check')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            @elseif($event['icon'] == 'refresh')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            @elseif($event['icon'] == 'truck')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                            @elseif($event['icon'] == 'check-circle')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            @elseif($event['icon'] == 'x-circle')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            @endif
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 pb-4 border-b border-gray-200">
                                    <h4 class="font-medium text-gray-800">{{ $event['title'] }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">{{ $event['description'] }}</p>
                                    <span class="text-xs text-gray-500 mt-1 block">{{ $event['time']->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column - Order Details --}}
        <div class="space-y-6">
            {{-- Order Status --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                        </svg>
                        <h2 class="text-lg font-semibold text-gray-800">Order Status</h2>
                    </div>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Order Status</label>
                            <select name="status" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @foreach(App\Models\Order::STATUSES as $value => $label)
                                    <option value="{{ $value }}" {{ $order->status == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" name="field" value="status"
                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Update Order Status
                        </button>
                    </form>
                </div>
            </div>

            {{-- Payment Status --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <h2 class="text-lg font-semibold text-gray-800">Payment Status</h2>
                    </div>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                            <select name="status" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @foreach(App\Models\Order::PAYMENT_STATUSES as $value => $label)
                                    <option value="{{ $value }}" {{ $order->payment_status == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" name="field" value="payment_status"
                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Update Payment Status
                        </button>
                    </form>
                </div>
            </div>

            {{-- Customer Information --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <h2 class="text-lg font-semibold text-gray-800">Customer Information</h2>
                    </div>
                </div>
                <div class="p-6">
                    <p class="font-medium text-gray-800">{{ $order->user->name ?? $order->billing_name }}</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $order->user->email ?? $order->billing_email }}</p>
                    <p class="text-sm text-gray-600">{{ $order->user->phone ?? $order->billing_phone }}</p>
                    
                    @if($order->user)
                        <hr class="my-4 border-gray-200">
                        <div class="space-y-2">
                            <p class="text-sm">
                                <span class="text-gray-600">Total Orders:</span>
                                <span class="font-medium ml-2">{{ $order->user->orders->count() }}</span>
                            </p>
                            <p class="text-sm">
                                <span class="text-gray-600">Customer Since:</span>
                                <span class="font-medium ml-2">{{ $order->user->created_at->format('M d, Y') }}</span>
                            </p>
                            <a href="{{ route('admin.users.show', $order->user) }}" 
                               class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition duration-200 mt-2">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View Profile
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Shipping Address --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h2 class="text-lg font-semibold text-gray-800">Shipping Address</h2>
                    </div>
                </div>
                <div class="p-6">
                    <p class="font-medium text-gray-800">{{ $order->shipping_name }}</p>
                    <p class="text-sm text-gray-600 mt-2">{{ $order->shipping_address }}</p>
                    <p class="text-sm text-gray-600">{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}</p>
                    <p class="text-sm text-gray-600">{{ $order->shipping_country }}</p>
                    <p class="text-sm text-gray-600 mt-2"><span class="font-medium">Phone:</span> {{ $order->shipping_phone }}</p>
                </div>
            </div>

            {{-- Billing Address --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <h2 class="text-lg font-semibold text-gray-800">Billing Address</h2>
                    </div>
                </div>
                <div class="p-6">
                    <p class="font-medium text-gray-800">{{ $order->billing_name }}</p>
                    <p class="text-sm text-gray-600 mt-2">{{ $order->billing_address }}</p>
                    <p class="text-sm text-gray-600">{{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_zip }}</p>
                    <p class="text-sm text-gray-600">{{ $order->billing_country }}</p>
                    <p class="text-sm text-gray-600 mt-2"><span class="font-medium">Phone:</span> {{ $order->billing_phone }}</p>
                </div>
            </div>

            {{-- Shipping Information --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <h2 class="text-lg font-semibold text-gray-800">Shipping Information</h2>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-sm"><span class="font-medium text-gray-600">Method:</span> {{ ucfirst(str_replace('_', ' ', $order->shipping_method ?? 'Standard')) }}</p>
                    @if($order->shipping_courier)
                        <p class="text-sm mt-2"><span class="font-medium text-gray-600">Courier:</span> {{ ucfirst(str_replace('_', ' ', $order->shipping_courier)) }}</p>
                    @endif
                    @if($order->tracking_number)
                        <p class="text-sm mt-2"><span class="font-medium text-gray-600">Tracking #:</span> {{ $order->tracking_number }}</p>
                        <button onclick="trackOrder()" 
                                class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition duration-200 mt-3">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Track Order
                        </button>
                    @else
                        <button onclick="addTracking()" 
                                class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition duration-200 mt-3">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Tracking
                        </button>
                    @endif
                </div>
            </div>

            {{-- Payment Information --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <h2 class="text-lg font-semibold text-gray-800">Payment Information</h2>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-sm"><span class="font-medium text-gray-600">Method:</span> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                    @if($order->payment_id)
                        <p class="text-sm mt-2"><span class="font-medium text-gray-600">Transaction ID:</span> {{ $order->payment_id }}</p>
                    @endif
                    @if($order->payment && $order->payment->paid_at)
                        <p class="text-sm mt-2"><span class="font-medium text-gray-600">Paid at:</span> {{ $order->payment->paid_at->format('M d, Y h:i A') }}</p>
                    @endif
                </div>
            </div>

            {{-- Admin Notes --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <h2 class="text-lg font-semibold text-gray-800">Admin Notes</h2>
                    </div>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.orders.notes', $order) }}" method="POST">
                        @csrf
                        <textarea name="admin_notes" rows="4" 
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Add admin notes...">{{ $order->admin_notes }}</textarea>
                        <button type="submit" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition duration-200 mt-3">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            Save Notes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tracking Modal --}}
<div id="trackingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Update Tracking Information</h3>
            <button onclick="closeTrackingModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form action="{{ route('admin.orders.tracking', $order) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Courier Service</label>
                <select name="shipping_courier" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Select Courier</option>
                    <option value="sundarban">Sundarban Courier</option>
                    <option value="sa_paribahan">SA Paribahan</option>
                    <option value="pathao">Pathao</option>
                    <option value="redx">RedX</option>
                    <option value="paperfly">Paperfly</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tracking Number</label>
                <input type="text" name="tracking_number" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" 
                        onclick="closeTrackingModal()"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-lg transition duration-200">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition duration-200">
                    Update Tracking
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function trackOrder() {
    const trackingNumber = '{{ $order->tracking_number }}';
    const courier = '{{ $order->shipping_courier }}';
    window.open(`https://www.google.com/search?q=${encodeURIComponent(courier)}+tracking+${encodeURIComponent(trackingNumber)}`, '_blank');
}

function addTracking() {
    document.getElementById('trackingModal').classList.remove('hidden');
}

function closeTrackingModal() {
    document.getElementById('trackingModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('trackingModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeTrackingModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeTrackingModal();
    }
});
</script>
@endpush
@endsection