{{-- resources/views/components/home/hero.blade.php --}}
@props(['banners'])

@if(isset($banners) && $banners->count() > 0)
<section class="relative overflow-hidden bg-gradient-to-r from-gray-900 to-gray-800">
    <div x-data="heroSlider()" x-init="init()" class="relative h-[500px] lg:h-[600px]">
        @foreach($banners as $index => $banner)
            <div x-show="currentSlide === {{ $index }}" 
                 x-transition:enter="transition ease-out duration-1000"
                 x-transition:enter-start="opacity-0 transform scale-110"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-700"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="absolute inset-0 hero-slide">
                
                <img src="{{ asset('storage/' . $banner->image) }}" 
                     alt="{{ $banner->title }}"
                     class="hidden md:block w-full h-full object-cover">
                
                <img src="{{ asset('storage/' . ($banner->mobile_image ?? $banner->image)) }}" 
                     alt="{{ $banner->title }}"
                     class="md:hidden w-full h-full object-cover">
                
                <div class="absolute inset-0 hero-content">
                    <div class="container mx-auto h-full flex items-center px-4 md:px-8">
                        <div class="max-w-2xl text-white animate-fade-in">
                            @if($banner->subtitle)
                                <span class="inline-block bg-blue-600 text-white px-4 py-1 rounded-full text-sm font-semibold mb-4">
                                    {{ $banner->subtitle }}
                                </span>
                            @endif
                            
                            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 leading-tight">
                                {{ $banner->title }}
                            </h1>
                            
                            @if($banner->description)
                                <p class="text-lg md:text-xl mb-6 text-gray-200">{{ $banner->description }}</p>
                            @endif
                            
                            <div class="flex flex-wrap gap-4">
                                @if($banner->link)
                                    <a href="{{ $banner->link }}" 
                                       target="{{ $banner->target }}"
                                       class="group inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition-all transform hover:scale-105">
                                        <span>{{ $banner->button_text ?? 'Shop Now' }}</span>
                                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                        </svg>
                                    </a>
                                @endif
                                
                                <a href="{{ route('product.index') }}" 
                                   class="inline-flex items-center bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-8 py-3 rounded-lg font-semibold backdrop-blur-sm transition">
                                    Browse All
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Navigation Dots --}}
        <div class="absolute bottom-8 left-0 right-0 flex justify-center space-x-2 z-10">
            @foreach($banners as $index => $banner)
                <button @click="currentSlide = {{ $index }}" 
                        class="w-3 h-3 rounded-full transition-all duration-300"
                        :class="currentSlide === {{ $index }} ? 'w-8 bg-blue-600' : 'bg-white bg-opacity-50 hover:bg-opacity-75'">
                </button>
            @endforeach
        </div>

        {{-- Arrow Navigation --}}
        <button @click="prevSlide" 
                class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-30 hover:bg-opacity-50 text-white w-10 h-10 rounded-full flex items-center justify-center transition z-10 backdrop-blur-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        <button @click="nextSlide" 
                class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-30 hover:bg-opacity-50 text-white w-10 h-10 rounded-full flex items-center justify-center transition z-10 backdrop-blur-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
    </div>
</section>
@endif