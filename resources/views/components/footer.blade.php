{{-- resources/views/components/footer.blade.php --}}

<footer class="bg-gradient-to-b from-gray-900 to-gray-950 text-white relative overflow-hidden">
    {{-- Decorative Background Pattern --}}
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    {{-- Animated Gradient Orbs --}}
    <div class="absolute top-0 -left-40 w-80 h-80 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
    <div class="absolute top-0 -right-40 w-80 h-80 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>
    <div class="absolute bottom-0 left-20 w-80 h-80 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-4000"></div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        {{-- Main Footer Content --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 xl:gap-12 py-16">
            {{-- Company Info - 4 cols --}}
            <div class="lg:col-span-4 space-y-6">
                <div class="group">
                    <a href="{{ route('home') }}" class="inline-flex items-center space-x-3 hover:opacity-90 transition">
                        {{-- Logo with animation --}}
                        <div class="relative">
                            @if(!empty(config('settings.store_logo')))
                                <img src="{{ asset('storage/' . config('settings.store_logo')) }}" 
                                    alt="{{ config('settings.store_name') ?? 'GadgetBD' }}" 
                                    class="h-12 w-auto relative z-10 group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="h-12 w-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all">
                                    <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Store Name with Gradient --}}
                        <span class="text-2xl font-bold bg-gradient-to-r from-blue-400 via-indigo-400 to-purple-400 bg-clip-text text-transparent group-hover:from-blue-300 group-hover:via-indigo-300 group-hover:to-purple-300 transition-all duration-300">
                            {{ config('settings.store_name') ?? 'GadgetBD' }}
                        </span>
                    </a>
                </div>
                
                {{-- Store Tagline with enhanced styling --}}
                <p class="text-gray-300 leading-relaxed text-sm border-l-4 border-blue-500 pl-4 italic">
                    "{{ config('settings.store_tagline') ?? 'Your trusted source for the latest smartphones, gadgets, and accessories in Bangladesh.' }}"
                </p>
                
                {{-- Enhanced Social Media Links --}}
                <div class="flex space-x-3 pt-2">
                    @php
                        $socialLinks = [
                            'facebook' => ['url' => config('settings.facebook_url'), 'color' => 'hover:bg-blue-600', 'icon' => 'M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z'],
                            'twitter' => ['url' => config('settings.twitter_url'), 'color' => 'hover:bg-sky-500', 'icon' => 'M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84'],
                            'instagram' => ['url' => config('settings.instagram_url'), 'color' => 'hover:bg-pink-600', 'icon' => 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zM5.838 12a6.162 6.162 0 1112.324 0 6.162 6.162 0 01-12.324 0zM12 16a4 4 0 110-8 4 4 0 010 8zm4.965-10.405a1.44 1.44 0 112.881.001 1.44 1.44 0 01-2.881-.001z'],
                            'youtube' => ['url' => config('settings.youtube_url'), 'color' => 'hover:bg-red-600', 'icon' => 'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z'],
                            'linkedin' => ['url' => config('settings.linkedin_url'), 'color' => 'hover:bg-blue-700', 'icon' => 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451c.979 0 1.778-.773 1.778-1.729V1.73C24 .774 23.204 0 22.225 0z'],
                        ];
                    @endphp

                    @foreach($socialLinks as $platform => $data)
                        @if(!empty($data['url']))
                            <a href="{{ $data['url'] }}" target="_blank" 
                            class="group relative w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:scale-110 transition-all duration-300 {{ $data['color'] }} hover:shadow-lg">
                                <svg class="w-5 h-5 text-gray-300 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="{{ $data['icon'] }}"/>
                                </svg>
                                {{-- Tooltip --}}
                                <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                    {{ ucfirst($platform) }}
                                </span>
                            </a>
                        @endif
                    @endforeach
                </div>

                {{-- Trust Badges --}}
                <!-- <div class="flex items-center space-x-4 pt-4">
                    <div class="flex -space-x-2">
                        <img class="w-8 h-8 rounded-full border-2 border-gray-700" src="https://randomuser.me/api/portraits/men/32.jpg" alt="Customer">
                        <img class="w-8 h-8 rounded-full border-2 border-gray-700" src="https://randomuser.me/api/portraits/women/44.jpg" alt="Customer">
                        <img class="w-8 h-8 rounded-full border-2 border-gray-700" src="https://randomuser.me/api/portraits/men/46.jpg" alt="Customer">
                    </div>
                    <div class="text-sm">
                        <span class="font-bold text-white">10k+</span>
                        <span class="text-gray-400"> happy customers</span>
                    </div>
                </div> -->
            </div>

            {{-- Quick Links - 3 cols (Vertically aligned) --}}
            <div class="lg:col-span-2">
                <h3 class="text-lg font-bold mb-6 flex items-center">
                    <!-- <span class="w-8 h-1 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full mr-3"></span> -->
                    Quick Links
                </h3>
                <ul class="space-y-3">
                    @php
                        $quickLinks = [
                            ['route' => 'about', 'label' => 'About Us', 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ['route' => 'contact', 'label' => 'Contact Us', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                            ['route' => 'faq', 'label' => 'FAQ', 'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
                            ['route' => 'terms', 'label' => 'Terms & Conditions', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            ['route' => 'privacy', 'label' => 'Privacy Policy', 'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                            ['route' => 'returns', 'label' => 'Return Policy', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
                        ];
                    @endphp
                    
                    @foreach($quickLinks as $link)
                        <li>
                            <a href="{{ route($link['route']) }}" class="group flex items-center text-gray-400 hover:text-white transition-all duration-300">
                                <svg class="w-4 h-4 mr-3 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/>
                                </svg>
                                <span class="group-hover:translate-x-2 transition-transform duration-300">{{ $link['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Top Categories - 3 cols (Vertically aligned - main change here) --}}
            <div class="lg:col-span-3">
                <h3 class="text-lg font-bold mb-6 flex items-center">
                    <!-- <span class="w-8 h-1 bg-gradient-to-r from-green-500 to-teal-500 rounded-full mr-3"></span> -->
                    Top Categories
                </h3>
                <ul class="space-y-3">
                    @foreach($categories as $category)
                        <li>
                            <a href="{{ route('product.category', $category->slug) }}" 
                            class="group flex items-center text-gray-400 hover:text-white transition-all duration-300">
                                <svg class="w-4 h-4 mr-3 group-hover:text-green-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                <span class="group-hover:translate-x-2 transition-transform duration-300">{{ $category->name }}</span>
                                <!-- {{-- Optional: Show category product count --}}
                                <span class="ml-2 text-xs text-gray-500 group-hover:text-gray-400 transition-colors">
                                    ({{ $category->products_count ?? 0 }})
                                </span> -->
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Contact Info - 2 cols (Vertically aligned) --}}
            <div class="lg:col-span-3">
                <h3 class="text-lg font-bold mb-6 flex items-center">
                    <!-- <span class="w-8 h-1 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-full mr-3"></span> -->
                    Contact Info
                </h3>
                <ul class="space-y-4">
                    @php
                        $contactInfo = [
                            ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z', 
                            'label' => 'Address', 
                            'value' => config('settings.store_address') ?? '123 Tech Street, Dhaka, Bangladesh'],
                            ['icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
                            'label' => 'Phone', 
                            'value' => config('settings.store_phone') ?? '+880 1234 567890'],
                            ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                            'label' => 'Email', 
                            'value' => config('settings.store_email') ?? 'support@gadgetbd.com'],
                            ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                            'label' => 'Business Hours', 
                            'value' => 'Sat - Thu: 10:00 AM - 8:00 PM, Fri: Closed'],
                        ];
                    @endphp

                    @foreach($contactInfo as $info)
                        <li class="group flex items-start">
                            <div class="flex-shrink-0 mr-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-yellow-500/20 to-orange-500/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $info['icon'] }}"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">{{ $info['label'] }}</p>
                                <p class="text-gray-300 text-sm group-hover:text-white transition-colors">{{ $info['value'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Brands Section (Commented out as per original) --}}
        <!-- {{--
        @if(isset($brands) && $brands->count() > 0)
        <div class="border-t border-gray-800 mt-8 pt-12">
            <h3 class="text-xl font-bold mb-8 text-center relative">
                <span class="absolute inset-x-0 top-1/2 transform -translate-y-1/2 border-t border-gray-800"></span>
                <span class="relative px-6 bg-gradient-to-b from-gray-900 to-gray-950 text-gray-300">Our Trusted Brands</span>
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-6">
                @foreach($brands as $brand)
                    <a href="{{ route('product.brand', $brand->slug) }}" 
                       class="group relative bg-gray-800/30 rounded-xl p-4 hover:bg-gray-800 transition-all duration-300 hover:scale-105">
                        @if($brand->logo)
                            <img src="{{ asset('storage/' . $brand->logo) }}" 
                                 alt="{{ $brand->name }}"
                                 class="h-12 mx-auto object-contain mb-2 filter grayscale group-hover:grayscale-0 transition-all duration-300">
                        @else
                            <span class="text-gray-400 group-hover:text-white transition text-sm block text-center">{{ $brand->name }}</span>
                        @endif
                        <div class="absolute inset-0 rounded-xl ring-1 ring-inset ring-white/10 group-hover:ring-white/20"></div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
        --}} -->

        {{-- Map Section --}}
        <div class="border-t border-gray-800">
            {{-- Google Map --}}
                <div class="bg-gray-800/50 overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 h-80">
                    @php
                        $address = urlencode(config('settings.store_address') ?? 'Dhaka, Bangladesh');
                        $mapUrl = "https://www.google.com/maps/embed/v1/place?key=YOUR_API_KEY&q={$address}";
                    @endphp
                        
                {{-- Fallback map using iframe with static address --}}
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d233667.82239298325!2d90.27923772234494!3d23.78088745621141!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755b8b087026b81%3A0x8fa563bbdd5904c2!2sDhaka%2C%20Bangladesh!5e0!3m2!1sen!2sbd!4v1700000000000!5m2!1sen!2sbd" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        class="hover:scale-105 transition-transform duration-700">
                </iframe>
            </div>
        </div>

        {{-- Copyright with enhanced styling --}}
        <div class="border-t border-gray-800 mt-8 pt-8 pb-6">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <p class="text-gray-400 text-sm">
                    &copy; {{ date('Y') }} 
                    <span class="text-white font-semibold">{{ config('settings.store_name') ?? 'GadgetBD' }}</span>
                    . All rights reserved.
                </p>
                
                {{-- Payment Methods --}}
                <div class="flex items-center space-x-4">
                    <span class="text-gray-500 text-sm">We Accept:</span>
                    <div class="flex space-x-2">
                        <!-- <div class="w-10 h-6 bg-gray-800 rounded flex items-center justify-center text-xs text-gray-400 hover:bg-gray-700 transition">VISA</div> -->
                        <div class="w-10 h-6 bg-gray-800 rounded flex items-center justify-center text-xs text-gray-400 hover:bg-gray-700 transition">Rocket</div>
                        <div class="w-10 h-6 bg-gray-800 rounded flex items-center justify-center text-xs text-gray-400 hover:bg-gray-700 transition">bkash</div>
                        <div class="w-10 h-6 bg-gray-800 rounded flex items-center justify-center text-xs text-gray-400 hover:bg-gray-700 transition">Nagad</div>
                    </div>
                </div>

                <p class="text-gray-500 text-xs">
                    {{ config('settings.invoice_footer') ?? 'Thank you for staying with us!' }}
                </p>
            </div>
        </div>
    </div>
</footer>

{{-- CSS Animations --}}
<style>
@keyframes blob {
    0% { transform: translate(0px, 0px) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
    100% { transform: translate(0px, 0px) scale(1); }
}
.animate-blob {
    animation: blob 7s infinite;
}
.animation-delay-2000 {
    animation-delay: 2s;
}
.animation-delay-4000 {
    animation-delay: 4s;
}
</style>