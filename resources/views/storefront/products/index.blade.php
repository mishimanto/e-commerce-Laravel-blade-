@extends('layouts.app')

@section('title', isset($category) ? $category->name . ' - ' . config('app.name') : (isset($brand) ? $brand->name . ' - ' . config('app.name') : (isset($keyword) ? 'Search: ' . $keyword . ' - ' . config('app.name') : 'All Products - ' . config('app.name'))))

@section('meta_description', isset($category) ? $category->meta_description ?? $category->description : (isset($brand) ? $brand->meta_description ?? 'Browse ' . $brand->name . ' products' : 'Browse our wide range of phones, gadgets, and accessories'))

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Sidebar Filters --}}
            <div class="lg:w-1/4">
                @if(isset($categories) || isset($brands) || isset($attributes))
                    <x-filters 
                        :categories="$categories ?? collect([])" 
                        :brands="$brands ?? collect([])" 
                        :attributes="$attributes ?? collect([])" 
                        :selected="request()->all()" 
                    />
                @else
                    <div class="bg-white shadow-sm p-4">
                        <p class="text-gray-500">Filters not available</p>
                    </div>
                @endif
            </div>

            {{-- Products Grid --}}
            <div class="lg:w-3/4">
                {{-- Page Header --}}
                <!-- <div class="mb-6">
                    <h1 class="text-2xl font-bold">
                        @if(isset($category))
                            {{ $category->name }}
                        @elseif(isset($brand))
                            {{ $brand->name }}
                        @elseif(isset($keyword))
                            Search Results: "{{ $keyword }}"
                        @else
                            All Products
                        @endif
                    </h1>
                    @if(isset($category) && $category->description)
                        <p class="text-gray-600 mt-2">{{ $category->description }}</p>
                    @endif
                </div> -->

                {{-- Toolbar --}}
                <div class="bg-white shadow-lg p-4 mb-6">
                    <div class="flex flex-wrap items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <label class="text-sm text-gray-600 whitespace-nowrap">Sort by:</label>
                            <select onchange="window.location.href = this.value" 
                                    class="border border-gray-300 px-3 py-1 text-sm focus:outline-none focus:border-blue-500 w-48 md:w-56">
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" 
                                        {{ request('sort') == 'newest' ? 'selected' : '' }}>
                                    Newest
                                </option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}"
                                        {{ request('sort') == 'price_low' ? 'selected' : '' }}>
                                    Price: Low to High
                                </option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}"
                                        {{ request('sort') == 'price_high' ? 'selected' : '' }}>
                                    Price: High to Low
                                </option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}"
                                        {{ request('sort') == 'popular' ? 'selected' : '' }}>
                                    Most Popular
                                </option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'rating']) }}"
                                        {{ request('sort') == 'rating' ? 'selected' : '' }}>
                                    Top Rated
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Active Filters Summary --}}
                @if(request()->has('categories') || request()->has('brands') || request()->has('min_price') || request()->has('max_price') || request()->has('attr_'))
                    <div class="bg-blue-50 p-4 mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-blue-700">Active Filters:</span>
                            <a href="{{ route('product.index') }}" class="text-xs text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear All
                            </a>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @if(request()->has('categories'))
                                @foreach(request('categories') as $categoryId)
                                    @php
                                        $cat = \App\Models\Category::find($categoryId);
                                    @endphp
                                    @if($cat)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-white text-blue-700 text-sm rounded-full border border-blue-200">
                                            {{ $cat->name }}
                                            <a href="{{ request()->fullUrlWithQuery(['categories' => array_diff(request('categories', []), [$categoryId])]) }}" class="hover:text-blue-900">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </a>
                                        </span>
                                    @endif
                                @endforeach
                            @endif

                            @if(request()->has('brands'))
                                @foreach(request('brands') as $brandId)
                                    @php
                                        $b = \App\Models\Brand::find($brandId);
                                    @endphp
                                    @if($b)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-white text-blue-700 text-sm rounded-full border border-blue-200">
                                            {{ $b->name }}
                                            <a href="{{ request()->fullUrlWithQuery(['brands' => array_diff(request('brands', []), [$brandId])]) }}" class="hover:text-blue-900">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </a>
                                        </span>
                                    @endif
                                @endforeach
                            @endif

                            @if(request()->has('min_price') || request()->has('max_price'))
                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-white text-blue-700 text-sm rounded-full border border-blue-200">
                                    ৳{{ number_format(request('min_price', 0)) }} - ৳{{ number_format(request('max_price', 999999)) }}
                                    <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null]) }}" class="hover:text-blue-900">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </a>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Products --}}
                @if($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-8">
                        {{ $products->withQueryString()->links() }}
                    </div>
                @else
                    <div class="text-center py-12 bg-white shadow-sm">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4M12 20V4"></path>
                        </svg>
                        <h3 class="text-xl font-bold text-gray-700 mb-2">No Products Found</h3>
                        <!-- <p class="text-gray-500 mb-4">Try adjusting your filters or search criteria</p> -->
                        <a href="{{ route('product.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Clear Filters
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection