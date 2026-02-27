<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(!empty(config('settings.store_favicon')))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . config('settings.store_favicon')) }}">
        <link rel="shortcut icon" href="{{ asset('storage/' . config('settings.store_favicon')) }}">
        <link rel="apple-touch-icon" href="{{ asset('storage/' . config('settings.store_favicon')) }}">
    @else
        {{-- Default favicon --}}
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    @endif
    
    {{-- SEO Meta Tags --}}
    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="@yield('meta_description', setting('meta_description', 'Your trusted store for phones and gadgets'))">
    <meta name="keywords" content="@yield('meta_keywords', setting('meta_keywords', 'phones, gadgets, electronics, accessories'))">
    
    {{-- Open Graph Tags --}}
    <meta property="og:title" content="@yield('og_title', config('app.name'))">
    <meta property="og:description" content="@yield('og_description', setting('meta_description'))">
    <meta property="og:image" content="@yield('og_image', asset(setting('og_image', 'images/og-image.jpg')))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    
    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', config('app.name'))">
    <meta name="twitter:description" content="@yield('meta_description', setting('meta_description'))">
    <meta name="twitter:image" content="@yield('og_image', asset(setting('og_image')))">
    
    {{-- Schema Markup --}}
    @stack('schema')

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
    {{-- Styles --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    {{-- Page Loader --}}
    <div 
        x-data="{ loading: true }"
        x-init="
            window.addEventListener('load', () => {
                setTimeout(() => loading = false, 800);
            });
            
            if (document.readyState === 'complete') {
                setTimeout(() => loading = false, 800);
            }
        "
        x-show="loading"
        x-transition:leave="transition ease-in-out duration-700"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-110"
        class="fixed inset-0 z-[9999]"
    >
        <x-loader.spinner 
            size="xl"
            fullScreen
            image="{{ asset('storage/images/logo.webp') }}"  
        />
    </div>
     <x-notification-toast />
     <x-quick-view-modal />
    <div class="min-h-screen flex flex-col">
       
        
        {{-- Header/Navigation --}}
        <x-navbar />

        
        {{-- Page Content --}}
        <main class="flex-1">
            @yield('content')
        </main>

        {{-- Toast Component --}}
        <x-toast position="top-right" />
        
        {{-- Footer --}}
        <x-footer />
        
        <!-- {{-- Cart Summary Modal --}}
        <x-cart-summary /> -->
        
        {{-- Loading Overlay --}}
        <div id="loading-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
            <div class="flex items-center justify-center h-full">
                <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-white"></div>
            </div>
        </div>
    </div>
    
    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @vite(['resources/js/app.js'])
    @stack('scripts')
    
    {{-- Alpine.js Store --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('cart', {
                items: [],
                subtotal: 0,
                total: 0,
                
                async loadCart() {
                    const response = await fetch('/cart');
                    const data = await response.json();
                    this.items = data.items;
                    this.subtotal = data.subtotal;
                    this.total = data.total;
                },
                
                async addToCart(productId, variantId = null, quantity = 1) {
                    const response = await fetch('/cart/add', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            variant_id: variantId,
                            quantity: quantity
                        })
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        await this.loadCart();
                        this.$dispatch('cart-updated', data.cart);
                    }
                }
            });
        });
    </script>
</body>
</html>