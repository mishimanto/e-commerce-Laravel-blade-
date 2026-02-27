{{-- resources/views/components/home/brands.blade.php --}}
@props(['brands'])

@if(isset($brands) && $brands->count() > 0)
<section class="container mx-auto px-4 py-12">
    <div class="text-center mb-8">
        <span class="text-blue-600 font-semibold text-sm uppercase tracking-wider">Brands</span>
        <!-- <h2 class="text-3xl font-bold mt-2">Shop From Top Brands</h2> -->
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
        @foreach($brands as $brand)
            <a href="{{ route('product.brand', $brand->slug) }}" 
               class="group bg-white p-4 rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 text-center">
                @if($brand->logo)
                    <img src="{{ asset('storage/' . $brand->logo) }}" 
                         alt="{{ $brand->name }}"
                         class="h-16 mx-auto object-contain mb-2 group-hover:scale-110 transition-transform">
                @else
                    <div class="h-16 flex items-center justify-center">
                        <span class="text-lg font-bold text-gray-800">{{ $brand->name }}</span>
                    </div>
                @endif
                <!-- @if(isset($brand->is_featured) && $brand->is_featured)
                    <span class="text-xs text-blue-600">Featured</span>
                @endif -->
            </a>
        @endforeach
    </div>
</section>
@endif