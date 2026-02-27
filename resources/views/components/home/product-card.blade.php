{{-- resources/views/components/product-card.blade.php --}}
@props(['product'])

<div class="product-card group bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
    <a href="{{ route('product.show', $product->slug) }}" class="block relative">
        {{-- Product Image --}}
        <div class="relative h-48 overflow-hidden bg-gray-100">
            @if($product->images->first())
                <img src="{{ asset('storage/' . $product->images->first()->url) }}" 
                     alt="{{ $product->name }}"
                     class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <i class="fas fa-mobile-alt text-6xl text-gray-300"></i>
                </div>
            @endif
            
            {{-- Badges --}}
            <div class="absolute top-2 left-2 flex flex-col gap-1">
                @if($product->is_featured)
                    <span class="badge-featured text-white text-xs px-2 py-1 rounded-full">Featured</span>
                @endif
                @if($product->is_trending)
                    <span class="badge-trending text-white text-xs px-2 py-1 rounded-full">Trending</span>
                @endif
                @if($product->created_at >= now()->subDays(7))
                    <span class="badge-new text-white text-xs px-2 py-1 rounded-full">New</span>
                @endif
            </div>
            
            {{-- Sale Badge --}}
            @if($product->sale_price && $product->sale_price < $product->base_price)
                @php $discount = round((($product->base_price - $product->sale_price) / $product->base_price) * 100); @endphp
                <span class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                    -{{ $discount }}%
                </span>
            @endif
        </div>
        
        {{-- Product Info --}}
        <div class="p-4">
            @if($product->brand)
                <p class="text-xs text-gray-500 mb-1">{{ $product->brand->name }}</p>
            @endif
            
            <h3 class="font-semibold text-sm mb-2 line-clamp-2 min-h-[40px]">{{ $product->name }}</h3>
            
            {{-- Price --}}
            <div class="flex items-baseline gap-2 mb-2">
                @if($product->sale_price)
                    <span class="text-lg font-bold text-blue-600">৳{{ number_format($product->sale_price) }}</span>
                    <span class="text-sm text-gray-400 line-through">৳{{ number_format($product->base_price) }}</span>
                @else
                    <span class="text-lg font-bold text-gray-800">৳{{ number_format($product->base_price) }}</span>
                @endif
            </div>
            
            {{-- Rating --}}
            @if($product->reviews_count > 0)
                <div class="flex items-center gap-1 mb-2">
                    <div class="flex text-yellow-400">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($product->reviews_avg_rating ?? 0))
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                            @else
                                <svg class="w-4 h-4 fill-current text-gray-300" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                            @endif
                        @endfor
                    </div>
                    <span class="text-xs text-gray-500">({{ $product->reviews_count }})</span>
                </div>
            @endif
            
            {{-- Stock Status --}}
            <p class="text-xs {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
            </p>
        </div>
    </a>
    
    {{-- Quick Actions --}}
    <div class="product-actions p-4 pt-0 flex gap-2">
        <button onclick="addToCart({{ $product->id }})" 
                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 rounded-lg transition">
            Add to Cart
        </button>
        <button onclick="addToWishlist({{ $product->id }})" 
                class="w-10 h-10 border border-gray-300 hover:border-blue-600 rounded-lg flex items-center justify-center transition">
            <svg class="w-5 h-5 text-gray-600 hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
        </button>
    </div>
</div>