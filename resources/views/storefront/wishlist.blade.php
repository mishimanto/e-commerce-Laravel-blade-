@extends('layouts.app')

@section('title', 'My Wishlist - ' . config('app.name'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8 text-gray-900">My Wishlist</h1>

    @if($wishlistItems->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($wishlistItems as $item)
                <div>
                    <x-product-card :product="$item->product" />
                </div>
            @endforeach
        </div>

        {{-- Clear Wishlist Button --}}
        <div class="text-center mt-8" x-data="{ clearing: false }">
            <button 
                @click="clearWishlist()" 
                class="inline-flex items-center gap-2 px-6 py-2 border border-red-500 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                :disabled="clearing"
            >
                <span x-show="!clearing" class="inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span>Clear Wishlist</span>
                </span>
                <span x-show="clearing" x-cloak class="inline-flex items-center gap-2">
                    <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>Clearing...</span>
                </span>
            </button>
        </div>
    @else
        <div class="text-center py-16 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-center mb-4">
                <div class="bg-pink-50 p-4 rounded-full">
                    <svg class="w-16 h-16 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Your wishlist is empty</h2>
            <p class="text-gray-500 mb-8 max-w-md mx-auto">Save items you love to your wishlist and they'll appear here</p>
            <a href="{{ route('product.index') }}" 
               class="inline-flex items-center gap-2 bg-blue-600 text-white px-8 py-2 hover:bg-blue-700 transition-all duration-200 shadow-lg shadow-blue-600/20 hover:shadow-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                Browse Products
            </a>
        </div>
    @endif
</div>
@endsection


@push('scripts')
<script>
function clearWishlist() {
    if (!confirm('Are you sure you want to clear your entire wishlist?')) {
        return;
    }
    
    const button = event.currentTarget;
    const originalHtml = button.innerHTML;
    button.disabled = true;
    button.innerHTML = `
        <span class="inline-flex items-center gap-2">
            <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Clearing...</span>
        </span>
    `;
    
    fetch('{{ route("wishlist.clear") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.dispatchEvent(new CustomEvent('wishlist-updated', { 
                detail: { count: 0 } 
            }));
            
            if (window.showNotification) {
                window.showNotification('success', data.message, 'Wishlist Cleared');
            }
            
            setTimeout(() => {
                window.location.reload();
            }, 500);
        } else {
            button.disabled = false;
            button.innerHTML = originalHtml;
            
            if (window.showNotification) {
                window.showNotification('error', data.message || 'Failed to clear wishlist', 'Error');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
        button.innerHTML = originalHtml;
        
        if (window.showNotification) {
            window.showNotification('error', 'Failed to clear wishlist', 'Error');
        }
    });
}

// Listen for wishlist updates to reload page
document.addEventListener('wishlist-updated', function(event) {
    // Only reload if we're on the wishlist page
    if (window.location.pathname.includes('/wishlist')) {
        setTimeout(() => {
            window.location.reload();
        }, 500);
    }
});
</script>
@endpush