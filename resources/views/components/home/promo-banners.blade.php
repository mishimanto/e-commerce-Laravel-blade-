{{-- resources/views/components/home/promo-banners.blade.php --}}
@props(['banners', 'position' => 'sidebar', 'autoplay' => true, 'interval' => 4000])

@if(isset($banners) && $banners->count() > 0)
    <section class="container mx-auto px-4 py-8">
        <div class="swiper promo-swiper relative overflow-hidden"
             data-autoplay="{{ $autoplay }}"
             data-interval="{{ $interval }}">
            <div class="swiper-wrapper">
                @foreach($banners as $banner)
                    <div class="swiper-slide">
                        <a href="{{ $banner->link ?? '#' }}" target="{{ $banner->target ?? '_self' }}" 
                           class="relative group block overflow-hidden">
                            <img src="{{ asset('storage/' . $banner->image) }}" 
                                 alt="{{ $banner->title }}"
                                 class="w-full h-64 md:h-80 lg:h-96 object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-6">
                                <div>
                                    <h4 class="text-white font-bold text-xl">{{ $banner->title }}</h4>
                                    @if($banner->subtitle)
                                        <p class="text-white text-sm mt-1">{{ $banner->subtitle }}</p>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <!-- <div class="swiper-pagination"></div> -->
            
            <!-- Navigation -->
            <!-- <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div> -->
        </div>
    </section>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const swiperEl = document.querySelector('.promo-swiper');
            if (swiperEl) {
                new Swiper(swiperEl, {
                    slidesPerView: 1,
                    spaceBetween: 0,
                    loop: true,
                    autoplay: {
                        delay: swiperEl.dataset.interval || 3000,
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    breakpoints: {
                        // যখন একাধিক স্লাইড দেখাতে চান
                        640: {
                            slidesPerView: {{ $position === 'sidebar' ? 2 : 1 }},
                        },
                        1024: {
                            slidesPerView: {{ $position === 'sidebar' ? 4 : 3 }},
                        },
                    }
                });
            }
        });
    </script>
    @endpush
@endif