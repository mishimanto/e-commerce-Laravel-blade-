{{-- resources/views/storefront/pages/about.blade.php --}}

@extends('layouts.app')

@section('title', 'About Us - ' . config('app.name'))

@section('content')
<!-- Hero Section -->
<div class="relative bg-gradient-to-r from-gray-400 to-gray-700 text-white overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-20"></div>
    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32 relative z-10">
        <div class="max-w-3xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6 animate-fade-in">About Us</h1>
            <!-- <p class="text-xl md:text-2xl text-blue-100 leading-relaxed">Your Trusted Partner for the Latest and Greatest in Technology Since 2020</p> -->
        </div>
    </div>
</div>

<!-- Stats Section -->
<div class="bg-white py-16 border-b border-gray-200">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-5xl mx-auto">
            @foreach($stats as $stat)
                <div class="text-center group">
                    <div class="text-4xl md:text-5xl font-bold text-blue-600 mb-2 group-hover:scale-110 transition-transform">{{ $stat['value'] }}</div>
                    <div class="text-gray-600 text-sm uppercase tracking-wide">{{ $stat['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Our Story Section -->
<div class="bg-gray-50 py-20">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mx-auto">
            <div class="relative">
                <div class="relative z-10 overflow-hidden shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1531297484001-80022131f5a1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                         alt="Our Store" 
                         class="w-full h-[500px] object-cover">
                </div>
                <div class="absolute -bottom-6 -right-6 w-48 h-48 bg-blue-100 rounded-2xl -z-10"></div>
                <div class="absolute -top-6 -left-6 w-48 h-48 bg-indigo-100 rounded-2xl -z-10"></div>
            </div>
            
            <div class="space-y-6">
                <div>
                    <span class="text-blue-600 font-semibold text-sm uppercase tracking-wider">Our Story</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-2 mb-4">A Journey of Innovation</h2>
                </div>
                
                <p class="text-gray-600 leading-relaxed">
                    Founded in 2020, <span class="font-semibold text-blue-600">{{ config('settings.store_name', 'GadgetBD') }}</span> started with a simple mission: to make the latest technology accessible and affordable for everyone in Bangladesh. What began as a small online venture has quickly grown into a trusted destination for tech enthusiasts and everyday users alike.
                </p>
                
                <p class="text-gray-600 leading-relaxed">
                    We are passionate about gadgets. From the flagship smartphones to the smallest accessories, we curate our collection to ensure you get the best quality and value. Our team works tirelessly to bring you the most innovative products from around the world.
                </p>
                
                <p class="text-gray-600 leading-relaxed">
                    We believe in building lasting relationships with our customers based on trust, transparency, and exceptional service. When you shop with us, you're not just a customer; you're part of the <span class="font-semibold text-blue-600">{{ config('settings.store_name', 'GadgetBD') }}</span> family.
                </p>
                
                <div class="flex items-center space-x-4 pt-4">
                    <div class="flex -space-x-2">
                        <img class="w-10 h-10 rounded-full border-2 border-white" src="https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" alt="Customer">
                        <img class="w-10 h-10 rounded-full border-2 border-white" src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" alt="Customer">
                        <img class="w-10 h-10 rounded-full border-2 border-white" src="https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" alt="Customer">
                    </div>
                    <div class="text-sm text-gray-500">
                        <span class="font-bold text-gray-800">{{ $stats[0]['value'] }}</span> Happy Users
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Values Section -->
<div class="bg-white py-20">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-blue-600 font-semibold text-sm uppercase tracking-wider">Our Values</span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-2 mb-4">What Drives Us Forward</h2>
            <!-- <p class="text-gray-600">These core principles guide everything we do, from product selection to customer service.</p> -->
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8  mx-auto">
            @foreach($values as $value)
                <div class="bg-gray-50 p-8 text-center group hover:shadow-xl transition-all hover:-translate-y-2">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                        @php
                            $icons = [
                                'shield' => '<svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>',
                                'zap' => '<svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
                                'heart' => '<svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>',
                                'trending-up' => '<svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>',
                            ];
                        @endphp
                        {!! $icons[$value['icon']] ?? '' !!}
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">{{ $value['title'] }}</h3>
                    <p class="text-gray-600 text-sm">{{ $value['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Team Section -->
<div class="bg-gray-50 py-20">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-blue-600 font-semibold text-sm uppercase tracking-wider">Our Team</span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-2 mb-4">Meet The People Behind <span class="font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">{{ config('settings.store_name', 'GadgetBD') }}</span></h2>
            <!-- <p class="text-gray-600">Dedicated professionals working to provide you the best experience.</p> -->
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-5xl mx-auto">
            @foreach($team as $member)
                <div class="bg-white shadow-lg overflow-hidden group hover:shadow-2xl transition-all hover:-translate-y-2">
                    <div class="relative h-80 overflow-hidden">
                        <img src="{{ $member['image'] }}" 
                             alt="{{ $member['name'] }}" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="absolute bottom-4 left-4 right-4 text-white opacity-0 group-hover:opacity-100 transition-opacity">
                            <p class="text-sm">{{ $member['bio'] }}</p>
                        </div>
                    </div>
                    <div class="p-6 text-center">
                        <h3 class="text-xl font-bold text-gray-800 mb-1">{{ $member['name'] }}</h3>
                        <p class="text-blue-600 font-medium">{{ $member['position'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Why Choose Us -->
<div class="bg-white py-20">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-blue-600 font-semibold text-sm uppercase tracking-wider">Why Choose Us</span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-2 mb-4">The <span class="font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">{{ config('settings.store_name', 'GadgetBD') }}</span> Advantage</h2>
            <!-- <p class="text-gray-600">Experience the difference with our premium services</p> -->
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <div class="flex flex-col items-center text-center p-6">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">100% Genuine Products</h3>
                <p class="text-gray-600">All products are sourced directly from authorized distributors with full warranty.</p>
            </div>
            
            <div class="flex flex-col items-center text-center p-6">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Fast Delivery</h3>
                <p class="text-gray-600">Free shipping over BDT 5,000 with delivery across all 64 districts in Bangladesh.</p>
            </div>
            
            <div class="flex flex-col items-center text-center p-6">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Secure Payments</h3>
                <p class="text-gray-600">Multiple payment options with 100% secure transaction protection.</p>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-20">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-8">Ready to Experience the Best in Tech?</h2>
        <!-- <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">Join thousands of satisfied customers who trust us for their gadget needs.</p> -->
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ route('home') }}" class="inline-flex items-center bg-white text-blue-600 px-8 py-4 font-semibold hover:bg-gray-100 transition shadow-xl hover:shadow-2xl">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Shop Now
            </a>
            <a href="{{ route('contact') }}" class="inline-flex items-center border-2 border-white text-white px-8 py-4 font-semibold hover:bg-white hover:text-blue-600 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                Contact Us
            </a>
        </div>
    </div>
</div>

<!-- Map Section -->
<div class="bg-gray-50 py-20">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Visit Our Store</h2>
            <!-- <p class="text-gray-600">Come visit us for a hands-on experience with our products</p> -->
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8  mx-auto">
            <div class="bg-white shadow-lg p-8">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Store Information</h3>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <div>
                            <p class="font-medium text-gray-800">Address</p>
                            <p class="text-gray-600">{{ config('settings.store_address', '123 Tech Street, Dhaka, Bangladesh') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <div>
                            <p class="font-medium text-gray-800">Phone</p>
                            <p class="text-gray-600">{{ config('settings.store_phone', '+880 1234 567890') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <div>
                            <p class="font-medium text-gray-800">Email</p>
                            <p class="text-gray-600">{{ config('settings.store_email', 'info@example.com') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="font-medium text-gray-800">Business Hours</p>
                            <p class="text-gray-600">Saturday - Thursday: 10:00 AM - 8:00 PM</p>
                            <p class="text-gray-600">Friday: Closed</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white shadow-lg overflow-hidden h-96">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d233667.82239298325!2d90.27923772234494!3d23.78088745621141!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755b8b087026b81%3A0x8fa563bbdd5904c2!2sDhaka%2C%20Bangladesh!5e0!3m2!1sen!2sbd!4v1700000000000!5m2!1sen!2sbd" 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </div>
</div>

<!-- CSS Animations -->
<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 1s ease-out;
    }
</style>
@endsection