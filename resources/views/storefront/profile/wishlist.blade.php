@extends('layouts.user')

@section('title', 'My Wishlist - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-gray-50 py-6 sm:py-8 lg:py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                <a href="{{ route('profile.dashboard') }}" class="hover:text-blue-600 transition-colors">
                    Dashboard
                </a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-gray-900 font-medium">Wishlist</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">My Wishlist</h1>
            <!-- <p class="mt-2 text-sm text-gray-600">Products you've saved for later</p> -->
        </div>

        <!-- @if(session('success'))
            <div class="mb-4 sm:mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif -->

        <!-- Wishlist Items -->
        @if($wishlistItems->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                @foreach($wishlistItems as $item)
                    @php
                        $product = $item->product;
                        $imageUrl = null;
                        if ($product && $product->images && $product->images->isNotEmpty()) {
                            $image = $product->images->first();
                            $imageUrl = filter_var($image->url, FILTER_VALIDATE_URL) 
                                ? $image->url 
                                : asset('storage/' . ltrim($image->url, '/'));
                        }
                        $discount = null;
                        if ($product->sale_price && $product->sale_price < $product->base_price) {
                            $discount = round((($product->base_price - $product->sale_price) / $product->base_price) * 100);
                        }
                    @endphp

                    <div class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 relative">
                        <!-- Remove Button -->
                        <button onclick="removeFromWishlist({{ $item->id }})" 
                                class="absolute top-3 right-3 z-10 w-8 h-8 bg-white rounded-full shadow-md flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <!-- Discount Badge -->
                        @if($discount)
                            <div class="absolute top-3 left-3 z-10">
                                <span class="bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs font-bold px-2 py-1 rounded-full shadow-lg">
                                    -{{ $discount }}%
                                </span>
                            </div>
                        @endif

                        <!-- Product Image -->
                        <a href="{{ route('product.show', $product->slug) }}" class="block aspect-w-1 aspect-h-1 bg-gray-100 overflow-hidden">
                            @if($imageUrl)
                                <img src="{{ $imageUrl }}" 
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                     onerror="this.onerror=null; this.src='{{ asset('images/no-image.jpg') }}';">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </a>

                        <!-- Product Info -->
                        <div class="p-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2 line-clamp-2 min-h-[40px]">
                                <a href="{{ route('product.show', $product->slug) }}" class="hover:text-blue-600 transition-colors">
                                    {{ $product->name }}
                                </a>
                            </h3>

                            <!-- Price -->
                            <div class="flex items-baseline gap-2 mb-3">
                                @if($product->sale_price && $product->sale_price < $product->base_price)
                                    <span class="text-lg font-bold text-blue-600">৳ {{ number_format($product->sale_price) }}</span>
                                    <span class="text-sm text-gray-400 line-through">৳ {{ number_format($product->base_price) }}</span>
                                @else
                                    <span class="text-lg font-bold text-blue-600">৳ {{ number_format($product->base_price) }}</span>
                                @endif
                            </div>

                            <!-- Rating -->
                            @if($product->reviews_avg_rating)
                                <div class="flex items-center mb-3">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($product->reviews_avg_rating))
                                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-xs text-gray-500 ml-2">({{ $product->reviews_count ?? 0 }})</span>
                                </div>
                            @endif

                            <!-- Stock Status -->
                            <div class="mb-3">
                                @if($product->effective_stock > 0)
                                    <span class="inline-flex items-center text-xs text-green-600">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                        In Stock ({{ $product->effective_stock }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-xs text-red-600">
                                        <span class="w-2 h-2 bg-red-500 rounded-full mr-1"></span>
                                        Out of Stock
                                    </span>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                @if($product->effective_stock > 0)
                                    <button onclick="addToCart({{ $product->id }})" 
                                            class="flex-1 bg-blue-600 text-white text-sm py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        Add to Cart
                                    </button>
                                @else
                                    <button disabled 
                                            class="flex-1 bg-gray-200 text-gray-500 text-sm py-2 rounded-lg cursor-not-allowed">
                                        Out of Stock
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if(method_exists($wishlistItems, 'links'))
                <div class="mt-8">
                    {{ $wishlistItems->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 sm:p-12 lg:p-16 text-center">
                <div class="max-w-md mx-auto">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto bg-pink-50 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-5">Your wishlist is empty</h3>
                    <!-- <p class="text-sm sm:text-base text-gray-600 mb-6 sm:mb-8">Save your favorite items to wishlist and come back to them later!</p> -->
                    <a href="{{ route('product.index') }}" 
                       class="inline-flex items-center justify-center px-5 sm:px-6 py-2.5 sm:py-3 bg-blue-600 text-white text-sm sm:text-base font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Browse Products
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Remove from Wishlist Confirmation Modal -->
<div id="removeWishlistModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Remove from Wishlist
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Are you sure you want to remove this item from your wishlist?
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <form id="removeWishlistForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Remove
                    </button>
                </form>
                <button type="button" onclick="closeRemoveModal()" 
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentWishlistId = null;

function removeFromWishlist(wishlistId) {
    currentWishlistId = wishlistId;
    const form = document.getElementById('removeWishlistForm');
    form.action = `{{ url('wishlist/remove') }}/${wishlistId}`;
    document.getElementById('removeWishlistModal').classList.remove('hidden');
}

function closeRemoveModal() {
    document.getElementById('removeWishlistModal').classList.add('hidden');
    currentWishlistId = null;
}

function addToCart(productId) {
    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in-up';
            toast.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Product added to cart!
                </div>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
            
            // Update cart count
            window.dispatchEvent(new CustomEvent('cart-updated', { 
                detail: { count: data.cart_count } 
            }));
        } else {
            alert(data.message || 'Failed to add to cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to add to cart');
    });
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('removeWishlistModal');
    if (event.target == modal) {
        closeRemoveModal();
    }
}
</script>
@endpush

<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.3s ease-out;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection