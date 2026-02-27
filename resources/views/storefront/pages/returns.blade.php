{{-- resources/views/storefront/pages/returns.blade.php --}}

@extends('layouts.app')

@section('title', 'Return Policy - ' . config('app.name'))

@section('content')
<!-- Hero Section -->
<div class="relative bg-gradient-to-r from-gray-400 to-gray-700 text-white overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-20"></div>
    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32 relative z-10">
        <div class="max-w-3xl mx-auto text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 animate-fade-in">Return & Refund Policy</h1>
            <!-- <p class="text-xl md:text-2xl text-blue-100 leading-relaxed">Your Trusted Partner for the Latest and Greatest in Technology Since 2020</p> -->
        </div>
    </div>
</div>

<!-- Return Process -->
<div class="bg-white py-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Easy Return Process</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
            @foreach($returnSteps as $step)
                <div class="relative">
                    <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow border border-gray-100">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl font-bold text-white">{{ $step['step'] }}</span>
                        </div>
                        @php
                            $icons = [
                                'edit' => '<svg class="w-8 h-8 text-blue-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>',
                                'check' => '<svg class="w-8 h-8 text-green-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                                'package' => '<svg class="w-8 h-8 text-orange-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>',
                                'refresh-cw' => '<svg class="w-8 h-8 text-purple-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>',
                            ];
                        @endphp
                        {!! $icons[$step['icon']] ?? '' !!}
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $step['title'] }}</h3>
                        <p class="text-gray-600 text-sm">{{ $step['description'] }}</p>
                    </div>
                    
                    @if(!$loop->last)
                        <div class="hidden lg:block absolute top-1/2 -right-3 transform -translate-y-1/2">
                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Conditions Grid -->
<div class="bg-gray-50 py-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Return Conditions</h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 max-w-5xl mx-auto">
            <!-- Eligible -->
            <div class="bg-white rounded-xl shadow-lg p-8 border-2 border-green-100">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-green-600">Eligible for Return</h3>
                </div>
                <ul class="space-y-4">
                    @foreach($conditions['Eligible for Return'] as $condition)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-gray-700">{{ $condition }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            
            <!-- Not Eligible -->
            <div class="bg-white rounded-xl shadow-lg p-8 border-2 border-red-100">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-red-600">Not Eligible for Return</h3>
                </div>
                <ul class="space-y-4">
                    @foreach($conditions['Not Eligible for Return'] as $condition)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span class="text-gray-700">{{ $condition }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Refund Methods -->
<div class="bg-white py-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Refund Methods & Timeline</h2>
        <!-- <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto">How and when you'll get your money back</p> -->
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-5xl mx-auto">
            @foreach($refundMethods as $method)
                <div class="bg-gray-50 p-6 text-center hover:shadow-lg transition-shadow">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        @php
                            $icons = [
                                'Cash on Delivery' => '<svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>',
                                'bKash/Nagad/Rocket' => '<svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                            ];
                        @endphp
                        {!! $icons[$method['method']] ?? '' !!}
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">{{ $method['method'] }}</h3>
                    <p class="text-gray-600 text-sm mb-3">{{ $method['process'] }}</p>
                    <div class="inline-block bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-sm font-medium">
                        {{ $method['time'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Important Notes -->
<div class="bg-gray-50 py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-0 max-w-5xl">
        <div class="bg-yellow-50 p-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-yellow-600 mr-3 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h4 class="font-bold text-yellow-800 mb-2">Important Notes</h4>
                    <ul class="space-y-2 text-yellow-700">
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Shipping costs for returns are the customer's responsibility unless the return is due to our error.</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Items must be returned in their original condition with all tags and packaging.</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>We recommend using a trackable shipping service for returns.</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">•</span>
                            <span>Refunds are processed to the original payment method.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Help CTA -->
        <!-- <div class="mt-8 text-center">
            <p class="text-gray-600 mb-4">Need help with a return? Our support team is here for you.</p>
            <a href="{{ route('contact') }}" class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                Contact Support
            </a>
        </div> -->
    </div>
</div>
@endsection