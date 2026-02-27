<nav class="bg-white shadow-lg" 
     x-data="{ 
        open: false, 
        searchOpen: false, 
        cartOpen: false, 
        categoriesOpen: false,
        cartCount: {{ $cartCount ?? 0 }},
        wishlistCount: {{ $wishlistCount ?? 0 }},
        compareCount: {{ $compareCount ?? 0 }}
     }" 
     @cart-updated.window="cartCount = $event.detail.count">
    
    {{-- Top Bar - Hide on mobile --}}
    <div class="bg-gray-800 text-white py-2 hidden md:block">
        <div class="container mx-auto px-4 relative z-10">
            <div class="flex justify-between items-center text-sm">
                {{-- Left Side - Contact Info --}}
                <div class="flex items-center space-x-6">
                    <div class="flex items-center group">
                        <div class="p-1 transition-all mr-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <span class="text-gray-300 group-hover:text-white transition">{{ $settings['store_phone'] ?? '+880 1234 567890' }}</span>
                    </div>
                    
                    <div class="flex items-center group">
                        <div class="p-1 transition-all mr-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="text-gray-300 group-hover:text-white transition">{{ $settings['store_email'] ?? 'support@example.com' }}</span>
                    </div>
                </div>
                
                {{-- Right Side - User Menu --}}
                <div class="flex items-center space-x-4">
                    @auth
                        <div class="relative group">
                            <a href="{{ route('profile.dashboard') }}" class="flex items-center space-x-2 text-gray-300 hover:text-white transition px-3 py-1 rounded-lg hover:bg-white/10">
                                <div class="p-1 bg-green-500/20 rounded-full">
                                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <span class="font-medium">{{ auth()->user()->name }}</span>
                            </a>
                        </div>
                        
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="flex items-center space-x-2 text-red-400 hover:text-red-300 transition px-3 py-1 hover:bg-red-500/10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="flex items-center space-x-2 text-gray-300 hover:text-white transition px-4 py-1.5 hover:bg-white/10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Login</span>
                        </a>
                        <a href="{{ route('register') }}" class="flex items-center space-x-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-1.5 hover:from-blue-700 hover:to-blue-800 transition shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            <span>Register</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    {{-- Main Navigation --}}
    <div class="container mx-auto px-4 py-2 md:py-4">
        <div class="flex items-center justify-between">
            {{-- Mobile Menu Button --}}
            <button @click="open = !open" class="md:hidden p-2 text-gray-600 hover:text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            {{-- Logo --}}
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    @if(!empty(config('settings.store_logo')))
                        <img src="{{ asset('storage/' . config('settings.store_logo')) }}" 
                            alt="{{ config('settings.store_name') ?? 'GadgetsBD' }}" 
                            class="h-8 md:h-10 w-auto">
                    @else
                        <svg class="h-8 md:h-10 w-8 md:w-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    @endif
                    
                    <span class="text-lg md:text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        {{ config('settings.store_name') ?? 'GadgetsBD' }}
                    </span>
                </a>
            </div>

            {{-- Search Bar - Hidden on mobile, shown in mobile menu --}}
            <div class="hidden md:block flex-1 max-w-2xl mx-8">
                <form action="{{ route('product.search') }}" method="GET" class="relative">
                    <input type="text" 
                           name="q" 
                           placeholder="Search for phones, gadgets, accessories..."
                           class="w-full border border-gray-300 pl-4 pr-12 py-2 focus:outline-none focus:border-blue-500">
                    <button type="submit" class="absolute right-0 top-0 h-full px-4 text-gray-500 hover:text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>
            </div>

            {{-- Action Icons --}}
            <div class="flex items-center space-x-4 md:space-x-6">
                {{-- Mobile Search Icon --}}
                <button @click="searchOpen = !searchOpen" class="md:hidden p-2 text-gray-600 hover:text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>

                {{-- Wishlist --}}
                <a href="{{ route('wishlist.index') }}" class="relative hover:text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <span x-show="wishlistCount > 0" 
                        x-cloak
                        class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                        <span x-text="wishlistCount"></span>
                    </span>
                </a>

                {{-- Cart --}}
                <button @click="cartOpen = !cartOpen; if(cartOpen) loadCart()" class="relative hover:text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span x-show="cartCount > 0" 
                          class="absolute -top-2 -right-2 bg-blue-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                        <span x-text="cartCount"></span>
                    </span>
                    <span x-show="!cartCount" 
                          class="absolute -top-2 -right-2 bg-blue-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                        0
                    </span>
                </button>
            </div>
        </div>

        {{-- Mobile Search Bar --}}
        <div x-show="searchOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2"
             class="md:hidden mt-3">
            <form action="{{ route('product.search') }}" method="GET" class="relative">
                <input type="text" 
                       name="q" 
                       placeholder="Search products..."
                       class="w-full border border-gray-300 pl-4 pr-12 py-2 rounded-lg focus:outline-none focus:border-blue-500">
                <button type="submit" class="absolute right-0 top-0 h-full px-4 text-gray-500 hover:text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    {{-- Category Menu - Desktop --}}
    <div class="border-t border-b border-gray-200 hidden md:block">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between">
                {{-- Categories Dropdown --}}
                <div class="relative" x-data="{ show: false }">
                    <button @mouseenter="show = true" @mouseleave="show = false" 
                            class="py-3 px-4 bg-blue-600 text-white font-semibold flex items-center">
                        <svg class="mr-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        All Categories
                    </button>
                    
                    <div x-show="show" @mouseenter="show = true" @mouseleave="show = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="absolute z-50 w-64 bg-white shadow-xl border border-gray-200">
                        @forelse($categories ?? [] as $category)
                            <div class="relative" x-data="{ showSub: false }">
                                <a href="{{ route('product.category', $category->slug) }}" 
                                   @mouseenter="showSub = true" @mouseleave="showSub = false"
                                   class="block px-4 py-2 hover:bg-gray-100">
                                    {{ $category->name }}
                                    @if($category->children->count() > 0)
                                        <svg class="float-right mt-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    @endif
                                </a>
                                
                                @if($category->children->count() > 0)
                                    <div x-show="showSub" @mouseenter="showSub = true" @mouseleave="showSub = false"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 transform -translate-x-2"
                                         x-transition:enter-end="opacity-100 transform translate-x-0"
                                         class="absolute left-full top-0 w-64 bg-white shadow-xl border border-gray-200">
                                        @foreach($category->children as $child)
                                            <a href="{{ route('product.category', $child->slug) }}" 
                                               class="block px-4 py-2 hover:bg-gray-100">
                                                {{ $child->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="px-4 py-2 text-gray-500">No categories found</div>
                        @endforelse
                    </div>
                </div>

                {{-- Main Menu --}}
                <div class="flex space-x-6">
                    <a href="{{ route('home') }}" class="py-3 text-gray-700 hover:text-blue-600">Home</a>
                    <a href="{{ route('product.index') }}" class="py-3 text-gray-700 hover:text-blue-600">Products</a>
                    <a href="{{ route('contact') }}" class="py-3 text-gray-700 hover:text-blue-600">Contact Us</a>
                    <a href="{{ route('about') }}" class="py-3 text-gray-700 hover:text-blue-600">About Us</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile Menu with Collapsible Categories --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="md:hidden border-t border-gray-200 bg-white absolute w-full z-50 shadow-lg"
         @click.away="open = false">
        <div class="container mx-auto px-4 py-3">
            <div class="space-y-2">
                {{-- Main Menu Items --}}
                <a href="{{ route('home') }}" class="block py-2 text-gray-700 hover:text-blue-600 border-b border-gray-100">Home</a>
                <a href="{{ route('product.index') }}" class="block py-2 text-gray-700 hover:text-blue-600 border-b border-gray-100">Products</a>
                <a href="{{ route('contact') }}" class="block py-2 text-gray-700 hover:text-blue-600 border-b border-gray-100">Contact Us</a>
                <a href="{{ route('about') }}" class="block py-2 text-gray-700 hover:text-blue-600 border-b border-gray-100">About Us</a>
                
                {{-- Mobile Categories with Collapse --}}
                <div class="border-b border-gray-100">
                    <button @click="categoriesOpen = !categoriesOpen" 
                            class="w-full flex items-center justify-between py-2 text-gray-700 hover:text-blue-600">
                        <span class="font-semibold">Categories</span>
                        <svg class="w-5 h-5 transition-transform duration-200" 
                             :class="{ 'rotate-180': categoriesOpen }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <div x-show="categoriesOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="pl-4 pb-2 space-y-2">
                        @forelse($categories ?? [] as $category)
                            @if($category->children->count() > 0)
                                {{-- Category with subcategories --}}
                                <div x-data="{ open: false }" class="">
                                    <button @click="open = !open" 
                                            class="w-full flex items-center justify-between py-1 text-gray-600 hover:text-blue-600">
                                        <span>{{ $category->name }}</span>
                                        <svg class="w-4 h-4 transition-transform duration-200" 
                                             :class="{ 'rotate-180': open }"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    
                                    <div x-show="open" 
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                                         x-transition:enter-end="opacity-100 transform translate-y-0"
                                         class="pl-4 py-1 space-y-1">
                                        @foreach($category->children as $child)
                                            <a href="{{ route('product.category', $child->slug) }}" 
                                               class="block py-1 text-sm text-gray-500 hover:text-blue-600">
                                                {{ $child->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                {{-- Category without subcategories --}}
                                <a href="{{ route('product.category', $category->slug) }}" 
                                   class="block py-1 text-gray-600 hover:text-blue-600">
                                    {{ $category->name }}
                                </a>
                            @endif
                        @empty
                            <p class="py-2 text-gray-500">No categories found</p>
                        @endforelse
                    </div>
                </div>

                {{-- Mobile User Menu --}}
                <div class="pt-2 mt-2">
                    @auth
                        <div class="space-y-2">
                            <a href="{{ route('profile.dashboard') }}" class="block py-2 text-gray-700 hover:text-blue-600">
                                {{ auth()->user()->name }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full font-bold text-left py-2 text-red-600 hover:text-red-700">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="space-y-2">
                            <a href="{{ route('login') }}" class="block font-bold py-2 text-gray-700 hover:text-blue-600">Login</a>
                            <a href="{{ route('register') }}" class="block font-bold py-2 text-blue-600 hover:text-blue-700">Register</a>
                        </div>
                    @endauth
                </div>

                {{-- Mobile Contact Info --}}
                <div class="pt-2 mt-2 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-1">{{ $settings['store_phone'] ?? '+8801234567890' }}</p>
                    <p class="text-sm text-gray-600">{{ $settings['store_email'] ?? 'support@example.com' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Cart Sidebar --}}
    <div x-show="cartOpen" 
         @click.away="cartOpen = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-x-full"
         x-transition:enter-end="opacity-100 transform translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-x-0"
         x-transition:leave-end="opacity-0 transform translate-x-full"
         class="fixed top-0 right-0 w-full md:w-96 h-full bg-white shadow-2xl z-50 overflow-y-auto"
         x-data="cartSidebar()">
        
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Shopping Cart</h2>
                <button @click="cartOpen = false" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Empty Cart --}}
            <div x-show="cartItems.length === 0" class="text-center py-12">
                <svg class="mx-auto w-24 h-24 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="text-gray-500">Your cart is empty</p>
                <a href="{{ route('product.index') }}"
                   class="mt-4 inline-block text-blue-600 hover:text-blue-700">
                    Continue Shopping
                </a>
            </div>

            {{-- Cart Items --}}
            <div x-show="cartItems.length > 0" class="space-y-4">
                <div class="space-y-4 mb-6 max-h-96 overflow-y-auto">
                    <template x-for="item in cartItems" :key="item.id">
                        <div class="flex space-x-4 border-b pb-4">
                            <img :src="item.image || '{{ asset('images/no-image.jpg') }}'" 
                                 :alt="item.name" 
                                 class="w-20 h-20 object-cover rounded">
                            
                            <div class="flex-1">
                                <div class="flex justify-between">
                                    <h3 class="font-semibold text-sm" x-text="item.name"></h3>
                                    <button @click="removeItem(item.id)" class="text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                                
                                <p class="text-xs text-gray-600 mt-1" x-show="item.attributes">
                                    <span x-text="formatAttributes(item.attributes)"></span>
                                </p>
                                
                                <div class="flex justify-between items-center mt-2">
                                    <span class="font-bold text-blue-600" x-text="'৳' + item.price.toFixed(2)"></span>
                                    
                                    <div class="flex items-center border rounded-lg">
                                        <button @click="decreaseQuantity(item)" 
                                                class="px-2 py-1 text-gray-600 hover:bg-gray-100 rounded-l disabled:opacity-50"
                                                :disabled="item.quantity <= 1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                        <span class="px-3 py-1 text-sm font-medium border-x min-w-[40px] text-center" 
                                              x-text="item.quantity"></span>
                                        <button @click="increaseQuantity(item)" 
                                                class="px-2 py-1 text-gray-600 hover:bg-gray-100 rounded-r">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <p class="text-xs text-gray-500 mt-1 text-right" 
                                   x-text="'Subtotal: ৳' + (item.price * item.quantity).toFixed(2)"></p>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Cart Summary --}}
                <div class="border-t pt-4">
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm"> 
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium" x-text="'৳' + subtotal.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total:</span>
                            <span class="text-blue-600" x-text="'৳' + (subtotal).toFixed(2)"></span>
                        </div>
                    </div>
                    
                    <a href="{{ route('cart.index') }}" 
                       class="block w-full bg-gray-800 text-white text-center py-3 rounded-lg mb-2 hover:bg-gray-900 transition">
                        View Cart
                    </a>
                    <a href="{{ route('checkout.index') }}" 
                       class="block w-full bg-blue-600 text-white text-center py-3 rounded-lg hover:bg-blue-700 transition">
                        Checkout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
    [x-cloak] { display: none !important; }
    </style>
</nav>

<script>
// All JavaScript functions remain exactly the same
function cartSidebar() {
    return {
        cartItems: [],
        subtotal: 0,
        
        init() {
            this.$watch('cartOpen', value => {
                if (value) {
                    this.loadCart();
                }
            });
            
            window.addEventListener('cart-updated', () => {
                if (this.cartOpen) {
                    this.loadCart();
                }
            });
        },
        
        formatAttributes(attributes) {
            if (!attributes) return '';
            
            try {
                const attrs = typeof attributes === 'string' ? JSON.parse(attributes) : attributes;
                if (typeof attrs === 'object' && attrs !== null) {
                    return Object.values(attrs).join(' / ');
                }
                return String(attributes);
            } catch (e) {
                return String(attributes);
            }
        },
        
        loadCart() {
            fetch('{{ route("cart.summary") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.cartItems = data.items || [];
                    this.subtotal = data.subtotal || 0;
                }
            })
            .catch(error => {
                console.error('Error loading cart:', error);
            });
        },
        
        async decreaseQuantity(item) {
            if (item.quantity <= 1) return;
            
            const newQuantity = item.quantity - 1;
            
            try {
                const response = await fetch(`{{ url('cart/update') }}/${item.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ quantity: newQuantity })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    item.quantity = newQuantity;
                    this.subtotal = data.subtotal;
                    window.dispatchEvent(new CustomEvent('cart-updated', { 
                        detail: { count: data.cart_count } 
                    }));
                }
            } catch (error) {
                console.error('Error updating quantity:', error);
            }
        },
        
        async increaseQuantity(item) {
            const newQuantity = item.quantity + 1;
            
            try {
                const response = await fetch(`{{ url('cart/update') }}/${item.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ quantity: newQuantity })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    item.quantity = newQuantity;
                    this.subtotal = data.subtotal;
                    window.dispatchEvent(new CustomEvent('cart-updated', { 
                        detail: { count: data.cart_count } 
                    }));
                }
            } catch (error) {
                console.error('Error updating quantity:', error);
            }
        },
        
        async removeItem(itemId) {
            if (!confirm('Remove this item from cart?')) return;
            
            try {
                const response = await fetch(`{{ url('cart/remove') }}/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.cartItems = this.cartItems.filter(item => item.id !== itemId);
                    this.subtotal = data.subtotal;
                    window.dispatchEvent(new CustomEvent('cart-updated', { 
                        detail: { count: data.cart_count } 
                    }));
                }
            } catch (error) {
                console.error('Error removing item:', error);
            }
        }
    };
}

function showNotification(message, type = 'success') {
    alert(message);
}

function loadCart() {
    const cartSidebar = document.querySelector('[x-data="cartSidebar()"]');
    if (cartSidebar) {
        Alpine.$data(cartSidebar).loadCart();
    }
}

function updateWishlistCount(count) {
    const navbar = document.querySelector('nav[x-data]');
    if (navbar) {
        Alpine.$data(navbar).wishlistCount = count;
    }
}

window.addEventListener('wishlist-updated', (event) => {
    updateWishlistCount(event.detail.count);
});

document.addEventListener('alpine:init', () => {
    fetch('{{ route("wishlist.count") }}', {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateWishlistCount(data.wishlist_count);
        }
    })
    .catch(error => console.error('Error loading wishlist count:', error));
});
</script>