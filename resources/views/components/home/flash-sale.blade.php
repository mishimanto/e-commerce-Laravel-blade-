{{-- resources/views/components/home/flash-sale.blade.php --}}
@props(['products'])

@if(isset($products) && $products->count() > 0)
<section class="container mx-auto px-4 py-12">
    <div class="bg-gradient-to-r from-orange-500 to-red-500 p-8 md:p-12 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <pattern id="grid" patternUnits="userSpaceOnUse" width="10" height="10">
                    <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="1"/>
                </pattern>
                <rect width="100%" height="100%" fill="url(#grid)"/>
            </svg>
        </div>

        <div class="relative z-10">
            <div class="flex flex-col md:flex-row justify-between items-center mb-8">
                <div>
                    <span class="text-white/80 font-semibold text-sm uppercase tracking-wider">Limited Time Offer</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-white mt-2">Flash Sale</h2>
                </div>
                
                <div class="flex items-center gap-4 mt-4 md:mt-0">
                    <div class="text-white text-center">
                        <span class="block text-sm">Ends in</span>
                        <div x-data="countdownTimer('{{ now()->addHours(24) }}')" x-init="init()" class="flex gap-2">
                            <div class="bg-white/20 backdrop-blur-sm rounded-lg px-3 py-2 text-center">
                                <span x-text="hours" class="text-2xl font-bold">00</span>
                                <span class="block text-xs">Hrs</span>
                            </div>
                            <div class="bg-white/20 backdrop-blur-sm rounded-lg px-3 py-2 text-center">
                                <span x-text="minutes" class="text-2xl font-bold">00</span>
                                <span class="block text-xs">Min</span>
                            </div>
                            <div class="bg-white/20 backdrop-blur-sm rounded-lg px-3 py-2 text-center">
                                <span x-text="seconds" class="text-2xl font-bold">00</span>
                                <span class="block text-xs">Sec</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($products->take(6) as $product)
                    <div class="flash-sale-item bg-white p-3 text-center transition-all duration-300 hover:shadow-xl">
                        <a href="{{ route('product.show', $product->slug) }}" class="block">
                            <div class="relative mb-3">
                                @if($product->images && $product->images->first())
                                    <img src="{{ asset('storage/' . $product->images->first()->url) }}" 
                                         alt="{{ $product->name }}"
                                         class="w-full h-32 object-cover">
                                @else
                                    <div class="w-full h-32 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-mobile-alt text-4xl text-gray-300"></i>
                                    </div>
                                @endif
                                
                                @if($product->sale_price && $product->sale_price < $product->base_price)
                                    @php 
                                        $discount = $product->base_price > 0 
                                            ? round((($product->base_price - $product->sale_price) / $product->base_price) * 100) 
                                            : 0; 
                                    @endphp
                                    @if($discount > 0)
                                    <span class="absolute top-0 right-0 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                        -{{ $discount }}%
                                    </span>
                                    @endif
                                @endif
                            </div>
                            
                            <h3 class="font-semibold text-sm mb-2 line-clamp-2">{{ $product->name }}</h3>
                            
                            <div class="flex justify-center items-baseline gap-1">
                                @if($product->sale_price)
                                    <span class="text-lg font-bold text-red-600">৳{{ number_format($product->sale_price) }}</span>
                                    <span class="text-xs text-gray-400 line-through">৳{{ number_format($product->base_price) }}</span>
                                @else
                                    <span class="text-lg font-bold text-gray-800">৳{{ number_format($product->base_price) }}</span>
                                @endif
                            </div>
                            
                            @if($product->stock > 0)
                                @php 
                                    $totalItems = $product->stock + ($product->sold_count ?? 0);
                                    $soldPercentage = $totalItems > 0 
                                        ? min(100, (($product->sold_count ?? 0) / $totalItems) * 100) 
                                        : 0; 
                                @endphp
                                <div class="mt-2">
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="text-gray-500">Sold: {{ $product->sold_count ?? 0 }}</span>
                                        <span class="text-gray-500">Available: {{ $product->stock }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ $soldPercentage }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif