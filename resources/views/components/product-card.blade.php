@props(['product', 'layout' => 'grid'])

@php
    // Helper function to get correct image URL
    $getProductImageUrl = function($product) {
        $image = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
        
        if (!$image) {
            return asset('images/no-image.jpg');
        }
        
        if (filter_var($image->url, FILTER_VALIDATE_URL)) {
            return $image->url;
        }
        
        if ($image->url === '/images/default-product.jpg' || str_starts_with($image->url, '/images/')) {
            return asset($image->url);
        }
        
        return asset('storage/' . $image->url);
    };
    
    $imageUrl = $getProductImageUrl($product);
    
    // Calculate discount percentage
    $discount = null;
    if ($product->sale_price && $product->sale_price < $product->base_price) {
        $discount = round((($product->base_price - $product->sale_price) / $product->base_price) * 100);
    }
    
    $rating = $product->average_rating ?? 0;
    $reviewCount = $product->reviews_count ?? 0;
    $stockStatus = $product->stock_status ?? null;
    $effectiveStock = $stockStatus['quantity'] ?? $product->stock ?? 0;
    $hasVariants = $stockStatus['has_variants'] ?? false;
    
    // Check if current page is wishlist page
    $isWishlistPage = request()->routeIs('wishlist.index');
@endphp

@if($layout === 'grid')
    <div class="group relative bg-white shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-transparent"
         x-data="{ 
            inWishlist: false,
            loading: false,
            
            async toggleWishlist() {
                if (this.loading) return;
                this.loading = true;
                
                try {
                    const response = await fetch('{{ route("wishlist.toggle") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: {{ $product->id }},
                            variant_id: null
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        const wasInWishlist = this.inWishlist;
                        this.inWishlist = data.in_wishlist;
                        
                        // Update wishlist count in navbar
                        window.dispatchEvent(new CustomEvent('wishlist-updated', { 
                            detail: { count: data.wishlist_count } 
                        }));
                        
                        // Show notification
                        if (window.showNotification) {
                            window.showNotification(
                                data.in_wishlist ? 'success' : 'info',
                                data.message,
                                data.in_wishlist ? 'Added to Wishlist' : 'Removed from Wishlist'
                            );
                        }
                        
                        // If we're on the wishlist page and item was removed, reload the page
                        @if($isWishlistPage)
                        if (wasInWishlist && !data.in_wishlist) {
                            setTimeout(() => {
                                window.location.reload();
                            }, 500);
                        }
                        @endif
                    } else {
                        if (window.showNotification) {
                            window.showNotification('error', data.message, 'Error');
                        }
                    }
                } catch (error) {
                    console.error('Error toggling wishlist:', error);
                    if (window.showNotification) {
                        window.showNotification('error', 'Failed to update wishlist', 'Error');
                    }
                } finally {
                    this.loading = false;
                }
            },
            
            async checkWishlist() {
                try {
                    const response = await fetch('{{ route("wishlist.check", $product->id) }}', {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.inWishlist = data.in_wishlist;
                    }
                } catch (error) {
                    console.error('Error checking wishlist:', error);
                }
            }
        }"
        x-init="checkWishlist()">
        
        {{-- Product Image --}}
        <div class="relative aspect-[4/3] overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100">

            <img 
                src="{{ $imageUrl }}" 
                alt="{{ $product->name }}"
                loading="lazy"
                class="w-full h-full object-center object-cover group-hover:scale-110 transition-transform duration-700"
                onerror="this.src='{{ asset('storage/images/default-product.jpg') }}'"
            >
            
            {{-- Discount Badge --}}
            @if($discount)
                <div class="absolute top-3 left-3 z-10">
                    <span class="bg-gradient-to-r from-rose-500 to-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg shadow-red-500/20">
                        Save {{ $discount }}%
                    </span>
                </div>
            @endif

            {{-- Quick Actions - Right side --}}
            <div class="absolute top-3 right-3 flex flex-col space-y-2 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0 z-10">
                {{-- Wishlist Button --}}
                <button 
                    @click="toggleWishlist()"
                    :class="{ 'opacity-50 cursor-not-allowed': loading }"
                    :disabled="loading"
                    class="bg-white/90 backdrop-blur-sm hover:bg-white w-10 h-10 rounded-xl shadow-lg hover:shadow-xl flex items-center justify-center transition-all duration-200 group/btn border border-white/50"
                    :aria-label="inWishlist ? 'Remove from wishlist' : 'Add to wishlist'"
                    :title="inWishlist ? 'Remove from wishlist' : 'Add to wishlist'"
                >
                    {{-- Heart Icon (filled when in wishlist) --}}
                    <template x-if="inWishlist">
                        <svg class="w-4 h-4 text-rose-500 transition-colors" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </template>
                    <template x-if="!inWishlist">
                        <svg class="w-4 h-4 text-gray-700 group-hover/btn:text-rose-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </template>
                </button>

                {{-- Compare Button --}}
                <!-- <button 
                    onclick="addToCompare({{ $product->id }})"
                    class="bg-white/90 backdrop-blur-sm hover:bg-white w-10 h-10 rounded-xl shadow-lg hover:shadow-xl flex items-center justify-center transition-all duration-200 group/btn border border-white/50"
                    aria-label="Compare"
                    title="Compare"
                >
                    {{-- Bar Chart Icon --}}
                    <svg class="w-4 h-4 text-gray-700 group-hover/btn:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </button> -->

                {{-- Quick View Button --}}
                <button 
                    onclick="quickView({{ $product->id }})"
                    class="bg-white/90 backdrop-blur-sm hover:bg-white w-10 h-10 rounded-xl shadow-lg hover:shadow-xl flex items-center justify-center transition-all duration-200 group/btn border border-white/50"
                    aria-label="Quick view"
                    title="Quick view"
                >
                    {{-- Eye Icon --}}
                    <svg class="w-4 h-4 text-gray-700 group-hover/btn:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </button>
            </div>

            {{-- Out of Stock Overlay --}}
            @if($effectiveStock <= 0)
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-20">
                    <span class="bg-white/90 backdrop-blur-sm text-red-700 text-md font-semibold px-4 py-2 rounded-xl shadow-xl">
                        Out of Stock
                    </span>
                </div>
            @endif
        </div>

        {{-- Product Info --}}
        <div class="p-5">
            {{-- Brand and Stock Status --}}
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ $product->brand->name ?? 'Generic' }}</p>
                @if($effectiveStock > 0 && $effectiveStock < 10)
                    <span class="text-xs bg-amber-50 text-amber-600 px-2 py-1 rounded-full font-medium">
                        Only {{ $effectiveStock }} left
                    </span>
                @endif
            </div>
            
            {{-- Name --}}
            <h3 class="text-sm font-semibold text-gray-900 mb-3 line-clamp-2 h-10 leading-snug">
                <a href="{{ route('product.show', $product->slug) }}" class="hover:text-blue-600 transition-colors">
                    {{ $product->name }}
                </a>
            </h3>

            {{-- Rating --}}
            <div class="flex items-center mb-4">
                <div class="flex items-center gap-0.5">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($rating))
                            <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @elseif($i - $rating < 1 && $i - $rating > 0)
                            <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <defs>
                                    <linearGradient id="half-{{ $product->id }}-{{ $i }}">
                                        <stop offset="50%" stop-color="currentColor" />
                                        <stop offset="50%" stop-color="#d1d5db" />
                                    </linearGradient>
                                </defs>
                                <path fill="url(#half-{{ $product->id }}-{{ $i }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @else
                            <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        @endif
                    @endfor
                </div>
                <span class="text-xs text-gray-500 ml-2 font-medium">{{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}</span>
            </div>

            {{-- Price --}}
            <div class="flex items-end justify-between">
                <div class="flex flex-col">
                    @if($product->sale_price)
                        <span class="text-xl font-bold text-gray-900 leading-tight">৳{{ number_format($product->sale_price) }}</span>
                        <span class="text-xs text-gray-400 line-through">৳{{ number_format($product->base_price) }}</span>
                    @else
                        <span class="text-xl font-bold text-gray-900">৳{{ number_format($product->base_price) }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@else
    {{-- List Layout --}}
    <div class="group flex bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-transparent"
         x-data="{ 
            inWishlist: false,
            loading: false,
            
            async toggleWishlist() {
                if (this.loading) return;
                this.loading = true;
                
                try {
                    const response = await fetch('{{ route("wishlist.toggle") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: {{ $product->id }},
                            variant_id: null
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        const wasInWishlist = this.inWishlist;
                        this.inWishlist = data.in_wishlist;
                        
                        window.dispatchEvent(new CustomEvent('wishlist-updated', { 
                            detail: { count: data.wishlist_count } 
                        }));
                        
                        if (window.showNotification) {
                            window.showNotification(
                                data.in_wishlist ? 'success' : 'info',
                                data.message,
                                data.in_wishlist ? 'Added to Wishlist' : 'Removed from Wishlist'
                            );
                        }
                        
                        // If we're on the wishlist page and item was removed, reload the page
                        @if($isWishlistPage)
                        if (wasInWishlist && !data.in_wishlist) {
                            setTimeout(() => {
                                window.location.reload();
                            }, 500);
                        }
                        @endif
                    } else {
                        if (window.showNotification) {
                            window.showNotification('error', data.message, 'Error');
                        }
                    }
                } catch (error) {
                    console.error('Error toggling wishlist:', error);
                    if (window.showNotification) {
                        window.showNotification('error', 'Failed to update wishlist', 'Error');
                    }
                } finally {
                    this.loading = false;
                }
            },
            
            async checkWishlist() {
                try {
                    const response = await fetch('{{ route("wishlist.check", $product->id) }}', {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.inWishlist = data.in_wishlist;
                    }
                } catch (error) {
                    console.error('Error checking wishlist:', error);
                }
            }
        }"
        x-init="checkWishlist()">
        
        <div class="w-56 h-56 flex-shrink-0 bg-gradient-to-br from-gray-50 to-gray-100 relative overflow-hidden">
            <img src="{{ $imageUrl }}" 
                 alt="{{ $product->name }}"
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                 onerror="this.src='{{ asset('images/no-image.jpg') }}'">
            
            @if($discount)
                <span class="absolute top-3 left-3 bg-gradient-to-r from-rose-500 to-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg shadow-red-500/20 z-10">
                    -{{ $discount }}%
                </span>
            @endif

            {{-- Quick Actions for List View --}}
            <div class="absolute top-3 right-3 flex flex-col space-y-2 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0 z-10">
                {{-- Wishlist Button --}}
                <button 
                    @click="toggleWishlist()"
                    :class="{ 'opacity-50 cursor-not-allowed': loading }"
                    :disabled="loading"
                    class="bg-white/90 backdrop-blur-sm hover:bg-white w-10 h-10 rounded-xl shadow-lg hover:shadow-xl flex items-center justify-center transition-all duration-200 group/btn border border-white/50"
                    :title="inWishlist ? 'Remove from wishlist' : 'Add to wishlist'"
                >
                    <template x-if="inWishlist">
                        <svg class="w-4 h-4 text-rose-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </template>
                    <template x-if="!inWishlist">
                        <svg class="w-4 h-4 text-gray-700 group-hover/btn:text-rose-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </template>
                </button>

                {{-- Compare Button --}}
                <button onclick="addToCompare({{ $product->id }})" 
                        class="bg-white/90 backdrop-blur-sm hover:bg-white w-10 h-10 rounded-xl shadow-lg hover:shadow-xl flex items-center justify-center transition-all duration-200 group/btn border border-white/50"
                        title="Compare">
                    <svg class="w-4 h-4 text-gray-700 group-hover/btn:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </button>
            </div>

            @if($effectiveStock <= 0)
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center">
                    <span class="bg-white/90 backdrop-blur-sm text-gray-900 text-sm font-semibold px-4 py-2 rounded-xl shadow-xl">
                        Out of Stock
                    </span>
                </div>
            @endif
        </div>
        
        <div class="flex-1 p-6">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">{{ $product->brand->name ?? 'Generic' }}</p>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">
                        <a href="{{ route('product.show', $product->slug) }}" class="hover:text-blue-600 transition-colors">
                            {{ $product->name }}
                        </a>
                    </h3>
                </div>
            </div>
            
            <div class="flex items-center mb-4">
                <div class="flex items-center gap-0.5">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($rating))
                            <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @elseif($i - $rating < 1 && $i - $rating > 0)
                            <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <defs>
                                    <linearGradient id="half-list-{{ $product->id }}-{{ $i }}">
                                        <stop offset="50%" stop-color="currentColor" />
                                        <stop offset="50%" stop-color="#d1d5db" />
                                    </linearGradient>
                                </defs>
                                <path fill="url(#half-list-{{ $product->id }}-{{ $i }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @else
                            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        @endif
                    @endfor
                </div>
                <span class="text-sm text-gray-500 ml-2 font-medium">{{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}</span>
                @if($effectiveStock > 0 && $effectiveStock < 10)
                    <span class="ml-4 text-xs bg-amber-50 text-amber-600 px-2 py-1 rounded-full font-medium">
                        Only {{ $effectiveStock }} left
                    </span>
                @endif
            </div>
            
            <p class="text-gray-600 mb-5 line-clamp-2 text-sm leading-relaxed">{{ $product->short_description ?? $product->description ?? '' }}</p>
            
            <div class="flex items-center justify-between">
                <div class="flex flex-col">
                    @if($product->sale_price)
                        <span class="text-3xl font-bold text-gray-900">৳{{ number_format($product->sale_price) }}</span>
                        <span class="text-sm text-gray-400 line-through">৳{{ number_format($product->base_price) }}</span>
                    @else
                        <span class="text-3xl font-bold text-gray-900">৳{{ number_format($product->base_price) }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif