<div class="bg-white relative" 
     x-data="{
        productId: {{ $product->id }},
        isAdding: false,
        
        addToCart() {
            this.isAdding = true;
            
            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: this.productId,
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.dispatchEvent(new CustomEvent('cart-updated', { 
                        detail: { count: data.cart_count } 
                    }));
                    
                    if (window.showNotification) {
                        window.showNotification('success', 'Product added to cart!', 'Added to Cart');
                    }
                } else {
                    if (window.showNotification) {
                        window.showNotification('error', data.message || 'Failed to add to cart', 'Error');
                    }
                }
            })
            .catch(error => {
                console.error('Error adding to cart:', error);
                if (window.showNotification) {
                    window.showNotification('error', 'Failed to add to cart', 'Error');
                }
            })
            .finally(() => {
                this.isAdding = false;
            });
        }
     }">
    
    {{-- Close Button --}}
    <button @click="$dispatch('close-quick-view')" 
            class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 z-50 bg-white rounded-full p-1 shadow-md hover:shadow-lg transition-all">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
        {{-- Product Image Section --}}
        <div class="relative">
            {{-- Discount Badge --}}
            @if($discount)
                <div class="absolute top-2 left-2 z-20">
                    <span class="bg-gradient-to-r from-rose-500 to-red-500 text-white text-xs font-bold px-2 py-1 rounded-full shadow-lg">
                        -{{ $discount }}% Off
                    </span>
                </div>
            @endif

            {{-- Main Image --}}
            <div class="relative w-full h-full bg-gray-100 rounded-lg overflow-hidden aspect-square">
                <img src="{{ $imageUrl }}" 
                     alt="{{ $product->name }}"
                     class="w-full h-full object-center object-cover group-hover:scale-110 transition-transform duration-700"
                     onerror="this.src='{{ asset('images/no-image.jpg') }}'">
            </div>
        </div>

        {{-- Product Info Section --}}
        <div class="flex flex-col space-y-4">
            {{-- Brand --}}
            @if($product->brand)
                <a href="{{ route('product.brand', $product->brand->slug) }}" 
                   class="text-sm text-gray-500 hover:text-blue-600 transition">
                    {{ $product->brand->name }}
                </a>
            @endif

            {{-- Title --}}
            <h2 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h2>

            {{-- Price --}}
            <div class="flex items-center gap-3">
                @if($product->sale_price)
                    <span class="text-3xl font-bold text-blue-600">৳{{ number_format($product->sale_price) }}</span>
                    <span class="text-lg text-gray-400 line-through">৳{{ number_format($product->base_price) }}</span>
                @else
                    <span class="text-3xl font-bold text-blue-600">৳{{ number_format($product->base_price) }}</span>
                @endif
            </div>

            {{-- Stock Status --}}
            <div class="flex items-center gap-2">
                @if($product->stock > 0)
                    <span class="text-green-600 text-sm font-medium">✓ In Stock</span>
                    @if($product->stock < 10)
                        <span class="text-amber-600 text-xs">(Only {{ $product->stock }} left)</span>
                    @endif
                @else
                    <span class="text-red-600 text-sm font-medium">✗ Out of Stock</span>
                @endif
            </div>

            {{-- Short Description --}}
            @if($product->short_description)
                <div class="border-t border-gray-200 pt-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Description</h3>
                    <p class="text-gray-600">{{ $product->short_description }}</p>
                </div>
            @endif

            {{-- Specifications --}}
            @if(!empty($specifications))
                <div class="border-t border-gray-200 pt-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Specifications</h3>
                    <div class="grid grid-cols-1 gap-2 max-h-60 overflow-y-auto">
                        @foreach($specifications as $key => $value)
                            <div class="flex flex-col sm:flex-row sm:items-baseline border-b border-gray-100 pb-2">
                                <span class="text-xs font-medium text-gray-700 sm:w-2/5">{{ $key }}:</span>
                                <span class="text-xs text-gray-600 sm:w-3/5 sm:pl-2 break-words">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-3 mt-4 pt-4 border-t border-gray-200">
                @if($product->stock > 0)
                    <button @click="addToCart()" 
                            :disabled="isAdding"
                            class="flex-1 inline-flex items-center justify-center bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!isAdding" class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Add to Cart
                        </span>
                        <span x-show="isAdding" class="flex items-center gap-2" x-cloak>
                            <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Adding...
                        </span>
                    </button>
                @endif

                <a href="{{ route('product.show', $product->slug) }}" 
                   class="inline-flex items-center justify-center border border-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-50 transition">
                    View Full Details
                </a>
            </div>
        </div>
    </div>
</div>