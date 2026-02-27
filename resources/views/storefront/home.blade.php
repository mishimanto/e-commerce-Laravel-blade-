{{-- resources/views/storefront/home.blade.php --}}
@extends('layouts.app')

@section('title', setting('meta_title', 'GadgetBD - Your Ultimate Tech Destination'))
@section('meta_description', setting('meta_description', 'Discover the latest smartphones, laptops, audio gear, and smart gadgets at unbeatable prices. Fast shipping across Bangladesh.'))
@section('meta_keywords', setting('meta_keywords', 'smartphones, laptops, gadgets, tech accessories, audio, wearables, Bangladesh'))

@push('styles')
<style>
    .animate-fade-in {
        animation: fadeIn 0.8s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .category-card:hover .category-icon {
        transform: scale(1.1) rotate(5deg);
    }
    
    .product-card:hover .product-actions {
        opacity: 1;
        transform: translateY(0);
    }
    
    .product-actions {
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.3s ease;
    }
    
    .badge-new {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .badge-trending {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .badge-featured {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .flash-sale-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    
    .hero-slide::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0) 100%);
        z-index: 1;
    }
    
    .hero-content {
        position: relative;
        z-index: 2;
    }
    
    .newsletter-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
    }
    
    .newsletter-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: rotate 30s linear infinite;
    }
    
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .category-showcase {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    
    @media (max-width: 768px) {
        .hero-slide h1 {
            font-size: 2rem;
        }
    }
</style>
@endpush

@section('content')

    {{-- Hero Banner Section Component --}}
    <x-home.hero :banners="$heroBanners" />

    {{-- Featured Categories Component --}}
    <x-home.categories :featuredCategories="$featuredCategories" />

    {{-- Promotional Banners Component (Sidebar) --}}
    <x-home.promo-banners :banners="$sidebarBanners" position="sidebar" />

    {{-- Featured Products Component --}}
    <x-home.featured-products :products="$featuredProducts" />

    {{-- Flash Sale Component --}}
    <x-home.flash-sale :products="$trendingProducts" />

    {{-- Trending Products Component --}}
    <x-home.trending-products :products="$trendingProducts" />

    {{-- New Arrivals Component --}}
    <x-home.new-arrivals :products="$newProducts" />

    {{-- Bottom Promotional Banners Component --}}
    <x-home.promo-banners :banners="$promoBanners" position="bottom" />

    {{-- Top Brands Component --}}
    <x-home.brands :brands="$brands" />

    {{-- Blog Section Component --}}
    <x-home.blog-posts />

    {{-- Newsletter Component --}}
    <x-home.newsletter />

    {{-- Service Features Component --}}
    <x-home.service-features />
@endsection

@push('scripts')
<script>
    function heroSlider() {
        return {
            currentSlide: 0,
            slides: {{ isset($heroBanners) ? $heroBanners->count() : 0 }},
            autoplay: true,
            init() {
                if (this.autoplay && this.slides > 1) {
                    setInterval(() => {
                        this.nextSlide();
                    }, 5000);
                }
            },
            nextSlide() {
                this.currentSlide = (this.currentSlide + 1) % this.slides;
            },
            prevSlide() {
                this.currentSlide = (this.currentSlide - 1 + this.slides) % this.slides;
            }
        }
    }

    function countdownTimer(endDate) {
        return {
            days: '00',
            hours: '00',
            minutes: '00',
            seconds: '00',
            init() {
                const target = new Date(endDate).getTime();
                
                setInterval(() => {
                    const now = new Date().getTime();
                    const distance = target - now;
                    
                    if (distance < 0) {
                        this.days = '00';
                        this.hours = '00';
                        this.minutes = '00';
                        this.seconds = '00';
                        return;
                    }
                    
                    this.days = Math.floor(distance / (1000 * 60 * 60 * 24)).toString().padStart(2, '0');
                    this.hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)).toString().padStart(2, '0');
                    this.minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)).toString().padStart(2, '0');
                    this.seconds = Math.floor((distance % (1000 * 60)) / 1000).toString().padStart(2, '0');
                }, 1000);
            }
        }
    }

    function addToCart(productId) {
        // Implement add to cart functionality
        console.log('Add to cart:', productId);
    }

    function addToWishlist(productId) {
        // Implement add to wishlist functionality
        console.log('Add to wishlist:', productId);
    }
</script>
@endpush