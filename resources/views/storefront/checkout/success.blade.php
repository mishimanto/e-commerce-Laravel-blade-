@extends('layouts.app')

@section('title', 'Order Confirmation - ' . config('app.name'))

@section('content')
    <div class="container mx-auto px-4 py-20">
        <div class="max-w-2xl mx-auto text-center">
            <div class="bg-white rounded-lg shadow-sm p-8">
                {{-- Success Icon --}}
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-check-circle text-4xl text-green-600"></i>
                </div>

                <h1 class="text-3xl font-bold mb-4">Thank You for Your Order!</h1>
                
                <p class="text-gray-600 mb-2">
                    Your order has been placed successfully.
                </p>
                
                <p class="text-gray-600 mb-6">
                    Order Number: <span class="font-bold text-blue-600">{{ $order->order_number }}</span>
                </p>

                <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
                    <h2 class="font-bold mb-4">Order Details</h2>
                    
                    @foreach($order->items as $item)
                        <div class="flex justify-between text-sm mb-2">
                            <span>{{ $item->name }} <span class="text-gray-500">x{{ $item->quantity }}</span></span>
                            <span>৳{{ number_format($item->price * $item->quantity, 2) }}</span>
                        </div>
                    @endforeach

                    <div class="border-t mt-4 pt-4">
                        <div class="flex justify-between font-bold">
                            <span>Total:</span>
                            <span class="text-blue-600">৳{{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Invoice Download & Print Buttons --}}
                <div class="p-4">
                    <div class="flex flex-col sm:flex-row justify-center space-y-2 sm:space-y-0 sm:space-x-3">
                        <a href="{{ route('order.invoice.download', ['order' => $order->id, 'signature' => $signature ?? '']) }}" 
                           class="text-right text-blue-600 hover:underline transition">
                            Download Invoice
                        </a>
                        <!-- <a href="{{ route('order.invoice.print', ['order' => $order->id, 'signature' => $signature ?? '']) }}" 
                           target="_blank"
                           class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                            <i class="fas fa-print mr-2"></i>
                            Print Invoice
                        </a> -->
                    </div>
                </div>

                <div class="space-y-3">
                    <a href="{{ route('product.index') }}" 
                       class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                        Continue Shopping
                    </a>
                    
                    @if(auth()->check())
                        <a href="{{ route('profile.orders') }}" 
                           class="inline-block ml-4 text-blue-600 hover:text-blue-700">
                            View My Orders
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection