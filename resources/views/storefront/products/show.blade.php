@extends('layouts.app')

@section('title', $product->name . ' - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-gray-50 py-4 sm:py-6 lg:py-8">
    <div class="container mx-auto ">
        

        {{-- Main Product Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 p-4 sm:p-6 lg:p-8">
                {{-- Product Images with Slider --}}
                <div x-data="productGallery()" class="relative w-full">
                    {{-- Discount Badge --}}
                    @php
                        $discount = null;
                        if ($product->sale_price && $product->sale_price < $product->base_price) {
                            $discount = round((($product->base_price - $product->sale_price) / $product->base_price) * 100);
                        }
                    @endphp

                    @if($discount)
                        <div class="absolute top-2 sm:top-3 right-2 sm:right-3 z-20">
                            <span class="bg-gradient-to-r from-rose-500 to-red-500 text-white text-xs font-bold px-2 sm:px-3 py-1 sm:py-1.5 rounded-full shadow-lg shadow-red-500/20">
                                -{{ $discount }}% Off
                            </span>
                        </div>
                    @endif

                    {{-- Main Image --}}
                    <div class="relative w-full bg-gray-100 rounded-lg overflow-hidden aspect-square sm:aspect-h-1">
                        <img :src="currentImage" 
                             alt="{{ $product->name }}"
                             class="w-full h-full object-cover object-center transition-opacity duration-300"
                             x-on:error="handleImageError">
                        
                        @if($product->is_new)
                            <div class="absolute top-2 sm:top-3 left-2 sm:left-3 z-20">
                                <span class="bg-gradient-to-r from-green-400 to-emerald-500 text-white text-xs font-bold px-2 sm:px-3 py-1 sm:py-1.5 rounded-full shadow-lg shadow-green-500/20">
                                    New
                                </span>
                            </div>
                        @endif

                        {{-- Image Counter --}}
                        <div class="absolute bottom-2 sm:bottom-3 right-2 sm:right-3 bg-black/60 text-white text-xs px-2 py-1 rounded-full backdrop-blur-sm">
                            <span x-text="currentIndex + 1"></span>/<span x-text="totalImages"></span>
                        </div>

                        {{-- Navigation Arrows --}}
                        <template x-if="totalImages > 1">
                            <div class="absolute inset-y-0 left-0 right-0 flex items-center justify-between px-2 sm:px-4">
                                <button @click="prevImage" 
                                        class="w-8 h-8 sm:w-10 sm:h-10 bg-black/50 rounded-full flex items-center justify-center text-white hover:bg-black/70 transition-all backdrop-blur-sm"
                                        :class="{'opacity-50 cursor-not-allowed': currentIndex === 0}"
                                        :disabled="currentIndex === 0">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </button>
                                <button @click="nextImage" 
                                        class="w-8 h-8 sm:w-10 sm:h-10 bg-black/50 rounded-full flex items-center justify-center text-white hover:bg-black/70 transition-all backdrop-blur-sm"
                                        :class="{'opacity-50 cursor-not-allowed': currentIndex === totalImages - 1}"
                                        :disabled="currentIndex === totalImages - 1">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    {{-- Collect All Unique Images --}}
                    @php
                        $allUniqueImages = [];
                        $seenImages = [];
                        
                        // Add product images
                        foreach($product->images as $image) {
                            $imageUrl = filter_var($image->url, FILTER_VALIDATE_URL) 
                                ? $image->url 
                                : asset('storage/' . ltrim($image->url, '/'));
                            
                            if (!in_array($imageUrl, $seenImages)) {
                                $seenImages[] = $imageUrl;
                                $allUniqueImages[] = [
                                    'url' => $imageUrl,
                                    'type' => 'product',
                                    'alt' => $product->name
                                ];
                            }
                        }
                        
                        // Add unique variant images
                        foreach($product->variants as $variant) {
                            if($variant->image && !str_contains($variant->image, 'no-image') && $variant->image !== null) {
                                // Format variant image URL
                                if (filter_var($variant->image, FILTER_VALIDATE_URL)) {
                                    $variantImageUrl = $variant->image;
                                } elseif (str_starts_with($variant->image, 'storage/')) {
                                    $variantImageUrl = asset($variant->image);
                                } elseif (str_starts_with($variant->image, 'variants/')) {
                                    $variantImageUrl = asset('storage/' . $variant->image);
                                } else {
                                    $variantImageUrl = asset('storage/variants/' . ltrim($variant->image, '/'));
                                }
                                
                                if (!in_array($variantImageUrl, $seenImages)) {
                                    $seenImages[] = $variantImageUrl;
                                    $allUniqueImages[] = [
                                        'url' => $variantImageUrl,
                                        'type' => 'variant',
                                        'sku' => $variant->sku,
                                        'alt' => $product->name . ' - ' . $variant->sku
                                    ];
                                }
                            }
                        }
                        
                        $totalUniqueImages = count($allUniqueImages);
                    @endphp

                    {{-- Thumbnails with Horizontal Scroll --}}
                    @if($totalUniqueImages > 1)
                    <div class="relative mt-4" x-data="thumbnailSlider()">
                        {{-- Scroll Left Button --}}
                        <button @click="scrollLeft" 
                                class="absolute -left-2 top-1/2 transform -translate-y-1/2 z-10 bg-white rounded-full shadow-md p-1.5 sm:p-2 hover:bg-gray-100 transition-all disabled:opacity-30 disabled:cursor-not-allowed"
                                x-show="showLeftScroll"
                                :disabled="!showLeftScroll"
                                x-cloak>
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>

                        {{-- Thumbnails Container --}}
                        <div class="overflow-hidden px-2" x-ref="thumbnailContainer">
                            <div class="flex gap-2 transition-transform duration-300 ease-in-out" 
                                 x-ref="thumbnailSlider"
                                 :style="'transform: translateX(-' + scrollPosition + 'px)'">
                                @foreach($allUniqueImages as $index => $image)
                                    <button @click="setImage('{{ $image['url'] }}', {{ $index }})" 
                                            class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 border-2 rounded-lg overflow-hidden bg-gray-100 hover:border-blue-400 transition-all focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            :class="{'border-blue-600 ring-2 ring-blue-200': currentImage === '{{ $image['url'] }}'}"
                                            title="{{ $image['alt'] }}">
                                        <img src="{{ $image['url'] }}" 
                                             alt="{{ $image['alt'] }}"
                                             class="w-full h-full object-cover"
                                             x-on:error="this.src='{{ asset('storage/images/no-image.jpg') }}'">
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Scroll Right Button --}}
                        <button @click="scrollRight" 
                                class="absolute -right-2 top-1/2 transform -translate-y-1/2 z-10 bg-white rounded-full shadow-md p-1.5 sm:p-2 hover:bg-gray-100 transition-all disabled:opacity-30 disabled:cursor-not-allowed"
                                x-show="showRightScroll"
                                :disabled="!showRightScroll"
                                x-cloak>
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                    @endif
                </div>

                {{-- Product Info --}}
                <div class="flex flex-col space-y-4 sm:space-y-6">
                    {{-- Header with Brand and Title --}}
                    <div class="space-y-2">
                        @if($product->brand)
                            <a href="{{ route('product.brand', $product->brand->slug) }}" 
                            class="inline-block text-xs sm:text-sm text-gray-500 hover:text-blue-600 transition">
                                {{ $product->brand->name }}
                            </a>
                        @endif
                        
                        {{-- Product Name and Price Flex Container --}}
                        <div class="flex justify-between items-start gap-4">
                            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 leading-tight flex-1">{{ $product->name }}</h1>
                            
                            {{-- Price with Variant Support --}}
                            <div x-data="priceDisplay()" class="flex-shrink-0">
                                <template x-if="!hasVariants">
                                    <div class="flex flex-col items-end">
                                        <template x-if="salePrice">
                                            <div class="flex flex-col items-end">
                                                <span class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-600 whitespace-nowrap" x-text="'৳ ' + (salePrice || basePrice).toFixed(2)"></span>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-sm sm:text-base text-gray-500 line-through" x-text="'৳ ' + basePrice.toFixed(2)"></span>
                                                    <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded" x-text="'-' + discountPercentage + '%'"></span>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!salePrice">
                                            <span class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-600 whitespace-nowrap" x-text="'৳ ' + basePrice.toFixed(2)"></span>
                                        </template>
                                    </div>
                                </template>
                                
                                <template x-if="hasVariants">
                                    <div>
                                        <span class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-600 whitespace-nowrap" x-text="'৳ ' + displayPrice.toFixed(2)"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Variants --}}
                    @if($product->variants->count() > 0)
                        <div class="border-t border-gray-200 pt-4 sm:pt-6" x-data="variants({{ $product->id }}, {{ json_encode($product->variants) }})" x-init="initWithFirstVariant()">
                            <template x-for="(variantGroup, attrName) in variantGroups" :key="attrName">
                                <div class="mb-4 sm:mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2" x-text="formatAttributeName(attrName)"></label>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="value in variantGroup" :key="value">
                                            <button @click="selectAttribute(attrName, value)"
                                                    class="px-3 sm:px-4 py-2 border rounded-lg text-xs sm:text-sm transition-all focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                    :class="selectedAttributes[attrName] === value ? 'border-blue-600 bg-blue-50 text-blue-600 ring-1 ring-blue-200' : 'border-gray-300 hover:border-gray-400 hover:bg-gray-50'"
                                                    :disabled="!isCombinationAvailable(attrName, value)"
                                                    x-text="value">
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                            
                            {{-- Selected Variant Info --}}
                            <div class="mt-4 p-3 sm:p-4 bg-blue-50 rounded-lg" x-show="selectedVariant" x-cloak>
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                                    <div>
                                        <p class="text-xs sm:text-sm text-gray-600">Selected Configuration:</p>
                                        <p class="text-sm sm:text-base font-medium text-gray-900" x-text="getSelectedVariantDisplay()"></p>
                                    </div>
                                    <div class="text-left sm:text-right">
                                        <p class="text-xs sm:text-sm font-medium" :class="selectedVariant.stock > 0 ? 'text-green-600' : 'text-red-600'"
                                        x-text="selectedVariant.stock > 0 ? selectedVariant.stock + ' in stock' : 'Out of stock'">
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Quantity and Actions --}}
                    <div class="border-t border-gray-200 pt-4 sm:pt-6 space-y-4 sm:space-y-6">
                        {{-- Quantity --}}
                        <div x-data="quantityManager({{ $product->effective_stock }}, {{ $product->variants->count() > 0 ? 'true' : 'false' }})" x-ref="qtyBox">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                            <div class="flex flex-wrap items-center gap-3">
                                <div class="flex items-center">
                                    <button @click="decrementQuantity" 
                                            class="w-8 h-8 sm:w-10 sm:h-10 border border-gray-300 rounded-l-lg hover:bg-gray-100 disabled:opacity-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            :disabled="quantity <= 1">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    <input type="number" 
                                        x-model="quantity"
                                        min="1" 
                                        :max="maxStock"
                                        class="w-16 sm:w-20 text-center border-t border-b border-gray-300 py-1.5 sm:py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <button @click="incrementQuantity" 
                                            class="w-8 h-8 sm:w-10 sm:h-10 border border-gray-300 rounded-r-lg hover:bg-gray-100 disabled:opacity-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            :disabled="quantity >= maxStock">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row gap-3" 
                            x-data="cartActions({{ $product->id }}, {{ $product->variants->count() > 0 ? 'true' : 'false' }}, {{ auth()->check() ? ($product->inWishlist() ? 'true' : 'false') : 'false' }})"
                            x-init="initWishlistState()">
                            
                            @if($product->effective_stock > 0)
                                <button @click="addToCart()" 
                                        class="flex-1 inline-flex items-center justify-center bg-blue-600 text-white px-4 sm:px-6 py-3 sm:py-3.5 rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm sm:text-base font-medium"
                                        :disabled="isAdding || (hasVariants && !getSelectedVariant())">
                                    <span x-show="!isAdding" class="flex items-center">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <span x-text="(hasVariants && !getSelectedVariant()) ? 'Select Variant First' : 'Add to Cart'"></span>
                                    </span>
                                    <span x-show="isAdding" class="flex items-center" x-cloak>
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Adding...
                                    </span>
                                </button>
                            @else
                                <button disabled class="flex-1 bg-gray-300 text-gray-500 px-4 sm:px-6 py-3 sm:py-3.5 rounded-lg cursor-not-allowed text-sm sm:text-base font-medium">
                                    Out of Stock
                                </button>
                            @endif
                            
                            {{-- Updated Wishlist Button with Active State --}}
                            <button @click="toggleWishlist()" 
                                    class="inline-flex items-center justify-center px-4 sm:px-6 py-3 sm:py-3.5 border border-gray-300 rounded-lg hover:bg-gray-100 transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm sm:text-base font-medium relative group"
                                    :class="{ 'bg-red-50 border-red-300 hover:bg-red-100': inWishlist, 'bg-white hover:bg-gray-50': !inWishlist }"
                                    :disabled="isWishlistAdding"
                                    x-cloak>
                                
                                <span x-show="!isWishlistAdding" class="flex items-center">
                                    {{-- Heart icon - filled when in wishlist, outlined when not --}}
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 transition-all duration-300" 
                                        :class="{ 
                                            'text-red-500 fill-current': inWishlist,
                                            'text-gray-500 group-hover:text-red-400': !inWishlist
                                        }"
                                        fill="none" 
                                        stroke="currentColor" 
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" 
                                            stroke-linejoin="round" 
                                            stroke-width="2" 
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                                            :class="{ 'fill-current': inWishlist }">
                                        </path>
                                    </svg>
                                </span>
                                
                                {{-- Loading Spinner --}}
                                <span x-show="isWishlistAdding" class="flex items-center" x-cloak>
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>

                    {{-- Additional Info --}}
                    <div class="border-t border-gray-200 pt-4 sm:pt-6 space-y-3">
                        {{-- Warranty --}}
                        @if($product->warranty)
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                <div>
                                    <span class="text-xs sm:text-sm font-medium text-gray-700">Warranty:</span>
                                    <span class="text-xs sm:text-sm text-gray-600 ml-1">{{ $product->warranty }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Product Details Tabs --}}
            <div class="border-t border-gray-200" x-data="{ tab: 'description' }">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-wrap gap-4 sm:gap-6 lg:gap-8 border-b border-gray-200 overflow-x-auto pb-px">
                        <button @click="tab = 'description'" 
                                class="py-3 sm:py-4 text-xs sm:text-sm font-medium border-b-2 transition whitespace-nowrap focus:outline-none"
                                :class="tab === 'description' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                            Description
                        </button>
                        @if($product->specifications)
                        <button @click="tab = 'specifications'" 
                                class="py-3 sm:py-4 text-xs sm:text-sm font-medium border-b-2 transition whitespace-nowrap focus:outline-none"
                                :class="tab === 'specifications' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                            Specifications
                        </button>
                        @endif
                        <button @click="tab = 'reviews'" 
                                class="py-3 sm:py-4 text-xs sm:text-sm font-medium border-b-2 transition whitespace-nowrap focus:outline-none"
                                :class="tab === 'reviews' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                            Reviews ({{ $product->reviews->count() }})
                        </button>
                    </div>
                </div>

                <div class="p-4 sm:p-6 lg:p-8">
                    {{-- Description Tab --}}
                    <div x-show="tab === 'description'" class="prose prose-sm sm:prose-base max-w-none">
                        @if($product->description)
                            {!! $product->description !!}
                        @else
                            <p class="text-gray-500 text-sm sm:text-base">No description available.</p>
                        @endif
                    </div>

                    {{-- Specifications Tab --}}
                    @if($product->specifications)
                    <div x-show="tab === 'specifications'" class="space-y-3">
                        @php 
                            $specs = $product->specifications;
                            $displaySpecs = [];
                            
                            if (!empty($specs) && is_array($specs)) {
                                foreach($specs as $key => $value) {
                                    if (is_numeric($key) && is_array($value) && isset($value['key']) && isset($value['value'])) {
                                        $displaySpecs[$value['key']] = $value['value'];
                                    }
                                    elseif (is_numeric($key) && is_array($value) && isset($value['key'])) {
                                        $displaySpecs[$value['key']] = $value['value'] ?? '';
                                    }
                                    elseif (!is_numeric($key) && !is_array($value)) {
                                        $displaySpecs[$key] = $value;
                                    }
                                    elseif (!is_numeric($key) && is_array($value) && isset($value['value'])) {
                                        $displaySpecs[$key] = $value['value'];
                                    }
                                    elseif (is_numeric($key) && is_array($value) && count($value) == 2 && isset($value[0]) && isset($value[1])) {
                                        $displaySpecs[$value[0]] = $value[1];
                                    }
                                    elseif (!is_numeric($key) && is_string($key)) {
                                        $displaySpecs[$key] = is_array($value) ? json_encode($value) : $value;
                                    }
                                }
                            }
                        @endphp
                        
                        @if(!empty($displaySpecs))
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($displaySpecs as $specKey => $specValue)
                                    <div class="flex flex-col sm:flex-row sm:items-baseline border-b border-gray-100 pb-2">
                                        <span class="text-xs sm:text-sm font-medium text-gray-700 sm:w-2/5">{{ $specKey }}:</span>
                                        <span class="text-xs sm:text-sm text-gray-600 sm:w-3/5 sm:pl-2 break-words">{{ $specValue }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm sm:text-base text-center py-4">No specifications available.</p>
                        @endif
                    </div>
                    @endif

                    {{-- Reviews Tab --}}
                    <div x-show="tab === 'reviews'" id="reviews" class="space-y-6">
                        
                        {{-- Reviews Header with Write Button --}}
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Customer Reviews</h3>
                                <!-- <p class="text-sm text-gray-500 mt-1">
                                    Based on <span>{{ $product->reviews->count() }}</span> reviews
                                </p> -->
                            </div>
                            
                            {{-- Write Review Button --}}
                            <button onclick="openReviewModal()" 
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Write a Review
                            </button>
                        </div>
                        
                        {{-- Reviews List --}}
                        @forelse($product->reviews as $review)
                            <div class="border-b border-gray-100 pb-6">
                                <div class="flex items-start gap-4">
                                    {{-- User Avatar --}}
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-sm">
                                            {{ substr($review->user->name ?? ($review->guest_info['name'] ?? 'G'), 0, 1) }}
                                        </div>
                                    </div>
                                    
                                    <div class="flex-1">
                                        {{-- Rating and Title --}}
                                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2">
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->rating)
                                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                    @endif
                                                @endfor
                                                @if($review->verified_purchase)
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Verified Purchase
                                                    </span>
                                                @endif
                                            </div>
                                            @if($review->title)
                                                <span class="text-sm font-semibold text-gray-900">{{ $review->title }}</span>
                                            @endif
                                        </div>
                                        
                                        {{-- Review Comment --}}
                                        <p class="text-sm text-gray-600 mb-3">{{ $review->comment }}</p>
                                        
                                        {{-- Pros & Cons --}}
                                        @if($review->pros || $review->cons)
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                                @if($review->pros)
                                                    <div class="flex items-start gap-2">
                                                        <svg class="w-4 h-4 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        <div>
                                                            <span class="text-xs font-medium text-gray-700">Pros:</span>
                                                            <span class="text-xs text-gray-600">{{ is_array($review->pros) ? implode(', ', $review->pros) : $review->pros }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if($review->cons)
                                                    <div class="flex items-start gap-2">
                                                        <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                        <div>
                                                            <span class="text-xs font-medium text-gray-700">Cons:</span>
                                                            <span class="text-xs text-gray-600">{{ is_array($review->cons) ? implode(', ', $review->cons) : $review->cons }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        {{-- Review Images --}}
                                        @if($review->images)
                                            <div class="flex gap-2 mb-3">
                                                @foreach($review->images as $image)
                                                    <img src="{{ Storage::url($image) }}" 
                                                         class="w-16 h-16 object-cover rounded-lg border border-gray-200 cursor-pointer hover:opacity-75 transition"
                                                         onclick="window.open(this.src, '_blank')">
                                                @endforeach
                                            </div>
                                        @endif
                                        
                                        {{-- Review Meta --}}
                                        <div class="flex items-center gap-3 text-xs text-gray-500">
                                            <span>By {{ $review->user->name ?? ($review->guest_info['name'] ?? 'Guest') }}</span>
                                            <span>•</span>
                                            <span>{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <p class="text-gray-500 text-sm sm:text-base">No reviews yet. Be the first to review this product!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Related Products --}}
            @if($relatedProducts->count() > 0)
                <div class="border-t border-gray-200 px-4 sm:px-6 lg:px-8 py-6 sm:py-8 lg:py-12">
                    <h2 class="text-lg sm:text-xl lg:text-2xl font-bold mb-4 sm:mb-6">Related Products</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                        @foreach($relatedProducts as $related)
                            <div class="group bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300">
                                <a href="{{ route('product.show', $related->slug) }}" class="block aspect-w-1 aspect-h-1 bg-gray-100 overflow-hidden">
                                    <img src="{{ $related->primary_image_url }}" 
                                         alt="{{ $related->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                         onerror="this.src='{{ asset('storage/images/no-image.jpg') }}'">
                                </a>
                                <div class="p-3 sm:p-4">
                                    <h3 class="text-sm sm:text-base font-semibold text-gray-900 mb-1 line-clamp-2">
                                        <a href="{{ route('product.show', $related->slug) }}" class="hover:text-blue-600 transition">
                                            {{ $related->name }}
                                        </a>
                                    </h3>
                                    <div class="flex flex-wrap items-baseline gap-2">
                                        @if($related->on_sale)
                                            <span class="text-base sm:text-lg font-bold text-blue-600">৳{{ number_format($related->sale_price) }}</span>
                                            <span class="text-xs sm:text-sm text-gray-500 line-through">৳{{ number_format($related->base_price) }}</span>
                                        @else
                                            <span class="text-base sm:text-lg font-bold text-blue-600">৳{{ number_format($related->base_price) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <x-notification-toast />

    {{-- Include Review Modal --}}
    <x-review-modal :product-id="$product->id" :product-name="$product->name" />

    {{-- Global Modal Trigger Script --}}
    <script>
    // Global function to open review modal
    window.openReviewModal = function(productId) {
        console.log('Opening review modal for product:', productId);
        
        // Method 1: Find by Alpine instance
        const elements = document.querySelectorAll('[x-data]');
        for (let el of elements) {
            if (el.__x && el.__x.$data && typeof el.__x.$data.openModal === 'function') {
                el.__x.$data.openModal();
                return;
            }
        }
        
        // Method 2: Use global array
        if (window.reviewModals && window.reviewModals.length > 0) {
            window.reviewModals[0].openModal();
            return;
        }
        
        // Method 3: Direct DOM query
        const modalElement = document.querySelector('[x-data^="reviewModal"]');
        if (modalElement && modalElement.__x) {
            modalElement.__x.openModal();
            return;
        }
        
        console.log('Modal not found - retrying in 500ms');
        setTimeout(() => {
            const retryElement = document.querySelector('[x-data^="reviewModal"]');
            if (retryElement && retryElement.__x) {
                retryElement.__x.openModal();
            }
        }, 500);
    };

    // Debug: Check if Alpine is loaded
    document.addEventListener('alpine:init', () => {
        console.log('Alpine initialized');
    });

    // Debug: Check when DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
        console.log('DOM ready - checking for modal elements');
        const modalElements = document.querySelectorAll('[x-data^="reviewModal"]');
        console.log('Found modal elements:', modalElements.length);
    });
    </script>

    <style>
    [x-cloak] { display: none !important; }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    </style>
</div>
@endsection

@push('scripts')
<script>
// Product Gallery Component with Slider Support
function productGallery() {
    return {
        currentImage: '{{ $product->primary_image_url }}',
        allImages: [],
        currentIndex: 0,
        
        init() {
            // Initialize all images array from PHP
            this.allImages = [
                @foreach($allUniqueImages as $image)
                    '{{ $image['url'] }}',
                @endforeach
            ];
            
            // Set current index
            this.updateCurrentIndex();
        },
        
        setImage(url, index) {
            this.currentImage = url;
            this.currentIndex = index;
        },
        
        prevImage() {
            if (this.currentIndex > 0) {
                this.currentIndex--;
                this.currentImage = this.allImages[this.currentIndex];
            }
        },
        
        nextImage() {
            if (this.currentIndex < this.totalImages - 1) {
                this.currentIndex++;
                this.currentImage = this.allImages[this.currentIndex];
            }
        },
        
        updateCurrentIndex() {
            this.currentIndex = this.allImages.indexOf(this.currentImage);
            if (this.currentIndex === -1) this.currentIndex = 0;
        },
        
        handleImageError(event) {
            event.target.src = '{{ asset('storage/images/no-image.jpg') }}';
        },
        
        get totalImages() {
            return this.allImages.length;
        }
    }
}

// Thumbnail Slider Component
function thumbnailSlider() {
    return {
        scrollPosition: 0,
        thumbnailWidth: 72,
        showLeftScroll: false,
        showRightScroll: false,
        
        init() {
            this.checkScrollButtons();
            window.addEventListener('resize', () => this.checkScrollButtons());
        },
        
        scrollLeft() {
            const container = this.$refs.thumbnailContainer;
            const slider = this.$refs.thumbnailSlider;
            
            const scrollAmount = this.thumbnailWidth * 3;
            this.scrollPosition = Math.max(0, this.scrollPosition - scrollAmount);
            
            this.checkScrollButtons();
        },
        
        scrollRight() {
            const container = this.$refs.thumbnailContainer;
            const slider = this.$refs.thumbnailSlider;
            const containerWidth = container.offsetWidth;
            const sliderWidth = slider.scrollWidth;
            const maxScroll = Math.max(0, sliderWidth - containerWidth);
            
            const scrollAmount = this.thumbnailWidth * 3;
            this.scrollPosition = Math.min(maxScroll, this.scrollPosition + scrollAmount);
            
            this.checkScrollButtons();
        },
        
        checkScrollButtons() {
            const container = this.$refs.thumbnailContainer;
            const slider = this.$refs.thumbnailSlider;
            
            if (container && slider) {
                const containerWidth = container.offsetWidth;
                const sliderWidth = slider.scrollWidth;
                const maxScroll = Math.max(0, sliderWidth - containerWidth);
                
                this.showLeftScroll = this.scrollPosition > 0;
                this.showRightScroll = this.scrollPosition < maxScroll;
            }
        }
    }
}

// Price Display Component
function priceDisplay() {
    return {
        basePrice: {{ $product->base_price }},
        salePrice: {{ $product->sale_price ?? 'null' }},
        hasVariants: {{ $product->variants->count() > 0 ? 'true' : 'false' }},
        selectedVariant: null,
        
        get discountPercentage() {
            if (!this.salePrice) return 0;
            return Math.round(((this.basePrice - this.salePrice) / this.basePrice) * 100);
        },
        
        get displayPrice() {
            if (this.selectedVariant) {
                return this.selectedVariant.price;
            }
            return this.salePrice || this.basePrice;
        },
        
        init() {
            if (this.hasVariants) {
                this.$watch('selectedVariant', (value) => {});
            }
        }
    }
}

// Variants Component
function variants(productId, variantsData) {
    return {
        productId: productId,
        variants: variantsData,
        variantGroups: {},
        selectedAttributes: {},
        selectedVariant: null,
        
        init() {
            this.variantGroups = {};
            this.variants.forEach(variant => {
                const attrs = typeof variant.attributes === 'string' 
                    ? JSON.parse(variant.attributes) 
                    : (variant.attributes || {});
                
                Object.keys(attrs).forEach(key => {
                    if (!this.variantGroups[key]) {
                        this.variantGroups[key] = [];
                    }
                    const value = attrs[key];
                    if (!this.variantGroups[key].includes(value)) {
                        this.variantGroups[key].push(value);
                    }
                });
            });
        },
        
        initWithFirstVariant() {
            if (this.variants && this.variants.length > 0) {
                const firstVariant = this.variants[0];
                const attrs = typeof firstVariant.attributes === 'string' 
                    ? JSON.parse(firstVariant.attributes) 
                    : (firstVariant.attributes || {});
                
                Object.keys(attrs).forEach(key => {
                    this.selectedAttributes[key] = attrs[key];
                });
                
                this.selectedVariant = firstVariant;
                
                const priceElement = document.querySelector('[x-data="priceDisplay()"]');
                if (priceElement) {
                    const pd = Alpine.$data(priceElement);
                    pd.selectedVariant = firstVariant;
                }
                
                const quantityElement = document.querySelector('[x-data^="quantityManager"]');
                if (quantityElement) {
                    const qm = Alpine.$data(quantityElement);
                    qm.updateMaxStock(firstVariant.stock);
                }
            }
        },
        
        formatAttributeName(name) {
            return name.charAt(0).toUpperCase() + name.slice(1).replace(/_/g, ' ');
        },
        
        selectAttribute(attr, value) {
            this.selectedAttributes[attr] = value;
            this.findMatchingVariant();
        },
        
        findMatchingVariant() {
            const requiredAttrs = Object.keys(this.variantGroups);
            if (Object.keys(this.selectedAttributes).length !== requiredAttrs.length) {
                this.selectedVariant = null;
                this.updatePriceAndStock();
                return;
            }
            
            this.selectedVariant = this.variants.find(variant => {
                const attrs = typeof variant.attributes === 'string' 
                    ? JSON.parse(variant.attributes) 
                    : (variant.attributes || {});
                
                return requiredAttrs.every(key => attrs[key] === this.selectedAttributes[key]);
            }) || null;
            
            this.updatePriceAndStock();
        },
        
        updatePriceAndStock() {
            const priceElement = document.querySelector('[x-data="priceDisplay()"]');
            if (priceElement) {
                const pd = Alpine.$data(priceElement);
                pd.selectedVariant = this.selectedVariant;
            }
            
            if (this.selectedVariant) {
                const quantityElement = document.querySelector('[x-data^="quantityManager"]');
                if (quantityElement) {
                    const qm = Alpine.$data(quantityElement);
                    qm.updateMaxStock(this.selectedVariant.stock);
                }
            }
        },
        
        getSelectedVariantDisplay() {
            if (!this.selectedVariant) return '';
            
            const attrs = typeof this.selectedVariant.attributes === 'string' 
                ? JSON.parse(this.selectedVariant.attributes) 
                : (this.selectedVariant.attributes || {});
            
            return Object.values(attrs).join(' / ');
        },
        
        isCombinationAvailable(attr, value) {
            return this.variants.some(variant => {
                const attrs = typeof variant.attributes === 'string' 
                    ? JSON.parse(variant.attributes) 
                    : (variant.attributes || {});
                return attrs[attr] === value;
            });
        }
    }
}

// Quantity Manager Component
function quantityManager(maxStock, hasVariants) {
    return {
        quantity: 1,
        maxStock: maxStock,
        hasVariants: hasVariants,
        
        decrementQuantity() {
            if (this.quantity > 1) this.quantity--;
        },
        
        incrementQuantity() {
            if (this.quantity < this.maxStock) this.quantity++;
        },
        
        updateMaxStock(newStock) {
            this.maxStock = newStock;
            if (this.quantity > newStock) {
                this.quantity = newStock;
            }
        }
    }
}

// Cart Actions Component with Wishlist Active State
function cartActions(productId, hasVariants, initialWishlistState = false) {
    return {
        productId: productId,
        hasVariants: hasVariants,
        inWishlist: initialWishlistState,
        isAdding: false,
        isWishlistAdding: false,
        
        initWishlistState() {
            if (!{{ auth()->check() ? 'true' : 'false' }}) {
                this.checkWishlistStatus();
            }
        },
        
        checkWishlistStatus() {
            fetch('/wishlist/check/' + this.productId, {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                this.inWishlist = data.in_wishlist;
            })
            .catch(error => {
                console.error('Error checking wishlist:', error);
            });
        },
        
        getSelectedVariant() {
            const variantsElement = document.querySelector('[x-data^="variants"]');
            if (variantsElement) {
                return Alpine.$data(variantsElement).selectedVariant;
            }
            return null;
        },
        
        showToast(type, message, title = '') {
            if (window.showNotification) {
                window.showNotification(type, message, title);
            } else {
                alert(message);
            }
        },
        
        addToCart() {
            const selectedVariant = this.getSelectedVariant();
            
            if (this.hasVariants && !selectedVariant) {
                this.showToast('warning', 'Please select a variant first', 'Selection Required');
                return;
            }
            
            const quantityElement = document.querySelector('[x-data^="quantityManager"]');
            const quantity = quantityElement ? Alpine.$data(quantityElement).quantity : 1;
            
            const variantId = selectedVariant ? selectedVariant.id : null;
            
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
                    variant_id: variantId,
                    quantity: quantity
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Server error');
                    });
                }
                return response.json();
            })
            .then(data => {
                this.isAdding = false;
                
                if (data.success) {
                    window.dispatchEvent(new CustomEvent('cart-updated', { 
                        detail: { count: data.cart_count } 
                    }));
                    
                    this.showToast('success', data.message || 'Product added to cart!', 'Added to Cart');
                } else {
                    this.showToast('error', data.message || 'Failed to add product to cart', 'Error');
                }
            })
            .catch(error => {
                this.isAdding = false;
                console.error('Error:', error);
                this.showToast('error', error.message || 'An error occurred. Please try again.', 'Error');
            });
        },
        
        toggleWishlist() {
            this.isWishlistAdding = true;
            
            fetch('{{ route("wishlist.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    product_id: this.productId 
                })
            })
            .then(response => response.json())
            .then(data => {
                this.isWishlistAdding = false;
                
                if (data.success) {
                    this.inWishlist = data.in_wishlist;
                    
                    window.dispatchEvent(new CustomEvent('wishlist-updated', { 
                        detail: { count: data.wishlist_count } 
                    }));
                    
                    this.showToast(
                        data.in_wishlist ? 'success' : 'info', 
                        data.message, 
                        'Add to Wishlist'
                    );
                } else {
                    this.showToast('error', data.message || 'Failed to update wishlist', 'Error');
                }
            })
            .catch(error => {
                this.isWishlistAdding = false;
                console.error('Error:', error);
                this.showToast('error', 'Failed to update wishlist', 'Error');
            });
        }
    };
}
</script>
@endpush