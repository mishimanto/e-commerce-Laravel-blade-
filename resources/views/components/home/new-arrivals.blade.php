{{-- resources/views/components/home/new-arrivals.blade.php --}}
@props(['products'])

@if(isset($products) && $products->count() > 0)
<section class="container mx-auto px-4 py-12">
    <div class="flex justify-between items-end mb-8">
        <div>
            <span class="text-blue-600 font-semibold text-sm uppercase tracking-wider">Just In</span>
            <h2 class="text-3xl font-bold mt-2">New Arrivals</h2>
        </div>
        <a href="{{ route('product.index', ['sort' => 'newest']) }}" 
           class="text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-2 group">
            <span>View All</span>
            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($products as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>
</section>
@endif