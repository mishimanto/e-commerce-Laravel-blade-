@extends('layouts.app')

@section('title', 'Shopping Cart - ' . config('app.name'))

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Shopping Cart</h1>

        @if($cartItems->isNotEmpty())
            <div class="flex flex-col lg:flex-row gap-8">
                {{-- Cart Items --}}
                <div class="lg:w-2/3">
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($cartItems as $item)
                                    <tr class="hover:bg-gray-50" x-data="cartItem({{ $item['id'] ?? $item->id }}, {{ $item['quantity'] ?? $item->quantity }})">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                @php
                                                    // Get image URL - handle both object and array
                                                    $imageUrl = is_array($item) ? ($item['image'] ?? null) : ($item->image ?? null);
                                                    
                                                    if (!$imageUrl) {
                                                        $product = is_array($item) ? ($item['product'] ?? null) : ($item->product ?? null);
                                                        if ($product && is_array($product) && isset($product['images']) && count($product['images']) > 0) {
                                                            $image = $product['images'][0]['url'] ?? null;
                                                            $imageUrl = $image ? (filter_var($image, FILTER_VALIDATE_URL) ? $image : asset('storage/' . ltrim($image, '/'))) : null;
                                                        } elseif ($product && !is_array($product) && method_exists($product, 'images') && $product->images->isNotEmpty()) {
                                                            $image = $product->images->first()->url;
                                                            $imageUrl = filter_var($image, FILTER_VALIDATE_URL) ? $image : asset('storage/' . ltrim($image, '/'));
                                                        }
                                                    }
                                                    
                                                    $imageUrl = $imageUrl ?? asset('images/no-image.jpg');
                                                    
                                                    // Get product slug for link
                                                    $productSlug = null;
                                                    $productName = is_array($item) ? ($item['name'] ?? 'Product') : ($item->name ?? 'Product');
                                                    
                                                    if (is_array($item) && isset($item['product']) && is_array($item['product']) && isset($item['product']['slug'])) {
                                                        $productSlug = $item['product']['slug'];
                                                    } elseif (!is_array($item) && isset($item->product) && $item->product) {
                                                        $productSlug = $item->product->slug;
                                                    }
                                                @endphp
                                                
                                                <img src="{{ $imageUrl }}" 
                                                     alt="{{ $productName }}"
                                                     class="w-16 h-16 object-cover rounded">
                                                <div class="ml-4">
                                                    @if($productSlug)
                                                        <h3 class="text-sm font-medium text-gray-900">
                                                            <a href="{{ route('product.show', $productSlug) }}" class="hover:text-blue-600">
                                                                {{ $productName }}
                                                            </a>
                                                        </h3>
                                                    @else
                                                        <h3 class="text-sm font-medium text-gray-900">
                                                            {{ $productName }}
                                                        </h3>
                                                    @endif
                                                    
                                                    @php
                                                        $attributes = is_array($item) ? ($item['attributes_raw'] ?? $item['attributes'] ?? null) : ($item->attributes_raw ?? $item->attributes ?? null);
                                                        if (is_string($attributes)) {
                                                            $attributes = json_decode($attributes, true);
                                                        }
                                                    @endphp
                                                    
                                                    @if($attributes && is_array($attributes) && count($attributes) > 0)
                                                        <p class="text-xs text-gray-500 mt-1">
                                                            @foreach($attributes as $key => $value)
                                                                <span class="mr-2">{{ ucfirst($key) }}: {{ $value }}</span>
                                                            @endforeach
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm font-medium">৳{{ number_format(is_array($item) ? ($item['price'] ?? 0) : ($item->price ?? 0), 2) }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-2">
                                                <button @click="decrement" 
                                                        class="w-8 h-8 border border-gray-300 rounded-lg hover:bg-gray-100 disabled:opacity-50"
                                                        :disabled="quantity <= 1">
                                                    <i class="fas fa-minus text-xs"></i>
                                                </button>
                                                <span x-text="quantity" class="w-8 text-center text-sm font-medium"></span>
                                                <button @click="increment" 
                                                        class="w-8 h-8 border border-gray-300 rounded-lg hover:bg-gray-100 disabled:opacity-50"
                                                        :disabled="quantity >= {{ is_array($item) ? ($item['stock'] ?? 999) : ($item->stock ?? 999) }}">
                                                    <i class="fas fa-plus text-xs"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm font-medium text-blue-600" x-text="'৳' + ({{ is_array($item) ? ($item['price'] ?? 0) : ($item->price ?? 0) }} * quantity).toFixed(2)"></span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <button @click="remove" class="text-red-500 hover:text-red-700">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-6 flex justify-between">
                        <a href="{{ route('product.index') }}" class="text-blue-600 hover:text-blue-700">
                            <i class="fas fa-arrow-left mr-1"></i> Continue Shopping
                        </a>
                        <button onclick="clearCart()" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-trash mr-1"></i> Clear Cart
                        </button>
                    </div>
                </div>

                {{-- Cart Summary --}}
                <div class="lg:w-1/3">
                    @include('storefront.cart.partials.summary')
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-700 mb-2">Your cart is empty</h2>
                <!-- <p class="text-gray-500 mb-6">Looks like you haven't added anything to your cart yet</p> -->
                <a href="{{ route('product.index') }}" 
                   class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                    Start Shopping
                </a>
            </div>
        @endif
    </div>

    {{-- Recently Viewed --}}
    @if(isset($recentlyViewed) && $recentlyViewed->isNotEmpty())
        <section class="container mx-auto px-4 py-12">
            <h2 class="text-2xl font-bold mb-6">Recently Viewed</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($recentlyViewed as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif
@endsection

@push('scripts')
<script>
function cartItem(itemId, initialQuantity) {
    return {
        quantity: initialQuantity,
        async decrement() {
            if (this.quantity > 1) {
                this.quantity--;
                await this.updateCart();
            }
        },
        async increment() {
            this.quantity++;
            await this.updateCart();
        },
        async updateCart() {
            try {
                const response = await fetch(`{{ url('cart/update') }}/${itemId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ quantity: this.quantity })
                });
                
                const data = await response.json();
                if (data.success) {
                    // Update header cart count
                    window.dispatchEvent(new CustomEvent('cart-updated', { 
                        detail: { count: data.cart_count } 
                    }));
                    
                    // Update cart summary if function exists
                    if (window.updateCartSummary) {
                        window.updateCartSummary({
                            subtotal: data.subtotal,
                            discount: data.discount || 0,
                            shipping: data.shipping || 100,
                            total: data.total
                        });
                    }
                } else {
                    alert(data.message || 'Failed to update cart');
                    // Revert quantity on error
                    this.quantity = initialQuantity;
                }
            } catch (error) {
                console.error('Failed to update cart:', error);
                alert('Failed to update cart');
                // Revert quantity on error
                this.quantity = initialQuantity;
            }
        },
        async remove() {
            if (confirm('Remove this item from cart?')) {
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
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to remove item');
                    }
                } catch (error) {
                    console.error('Failed to remove item:', error);
                    alert('Failed to remove item');
                }
            }
        }
    }
}

async function clearCart() {
    if (confirm('Clear your entire cart?')) {
        try {
            const response = await fetch('{{ route("cart.clear") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Failed to clear cart');
            }
        } catch (error) {
            console.error('Failed to clear cart:', error);
            alert('Failed to clear cart');
        }
    }
}
</script>
@endpush