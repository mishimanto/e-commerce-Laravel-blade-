{{-- resources/views/components/home/categories.blade.php --}}
@props(['featuredCategories' => []])

@if(isset($featuredCategories) && $featuredCategories->count() > 0)
<section class="container mx-auto px-4 py-16">

    {{-- Section Header --}}
    <div class="flex items-center justify-between mb-10">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                Featured Categories
            </h2>
        </div>
        <!-- <a href="{{ route('product.index') }}" class="text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1 transition-colors">
            View All
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a> -->
    </div>

    {{-- Category Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-6 gap-6">

        @foreach($featuredCategories as $category)
            <a href="{{ route('product.category', $category->slug) }}"
               class="group block text-center">

                {{-- Image/Icon Container --}}
                <div class="relative mb-4 mx-auto w-24 h-24 sm:w-32 sm:h-32 rounded-lg overflow-hidden bg-gray-50 border-2 border-transparent group-hover:shadow-lg transition-all duration-300 p-2">
                    
                    <div class="w-full h-full bg-white flex items-center justify-center">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}"
                                 alt="{{ $category->name }}"
                                 class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center text-blue-600">
                                <i class="{{ $category->icon ?? 'fas fa-layer-group' }} text-3xl opacity-80"></i>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Text Content --}}
                <h3 class="text-sm sm:text-base font-bold text-gray-800 group-hover:text-blue-600 transition-colors truncate px-2">
                    {{ $category->name }}
                </h3>

                @if(isset($category->products_count) && $category->products_count > 0)
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $category->products_count }} Products
                    </p>
                @endif
            </a>
        @endforeach

    </div>

</section>
@endif