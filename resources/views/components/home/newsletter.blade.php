{{-- resources/views/components/home/newsletter.blade.php --}}
<section class="newsletter-section py-16 text-white">
    <div class="container mx-auto px-4 text-center relative z-10">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Stay Updated!</h2>
        <p class="text-lg mb-8 max-w-2xl mx-auto opacity-90">Subscribe to get updates on new arrivals, exclusive offers, and tech insights.</p>
        
        <div x-data="newsletter()" class="max-w-md mx-auto">
            <form @submit.prevent="subscribe" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <input type="email" 
                       x-model="email"
                       placeholder="Enter your email" 
                       class="flex-1 px-6 py-3 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-300"
                       :disabled="loading"
                       required>
                
                <button type="submit" 
                        :disabled="loading"
                        class="px-8 py-3 bg-white text-blue-600 rounded-lg font-semibold hover:bg-gray-100 transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!loading">Subscribe</span>
                    <span x-show="loading" class="flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </form>
            
            {{-- Success Message with Fade Effect --}}
            <div x-show="success" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                 class="mt-4 p-4 bg-green-500/20 border border-green-500 rounded-lg backdrop-blur-sm">
                <div class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-white-500 font-medium" x-text="success"></p>
                </div>
            </div>
            
            {{-- Error Message with Fade Effect --}}
            <div x-show="error" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                 class="mt-4 p-4 bg-red-500/20 border border-red-500 rounded-lg backdrop-blur-sm">
                <div class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-white font-medium" x-text="error"></p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    [x-cloak] { display: none !important; }
    
    .backdrop-blur-sm {
        backdrop-filter: blur(4px);
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-slide-down {
        animation: slideDown 0.3s ease-out;
    }
</style>

<script>
function newsletter() {
    return {
        email: '',
        loading: false,
        success: '',
        error: '',
        
        subscribe() {
            this.loading = true;
            this.success = '';
            this.error = '';
            
            fetch('{{ route("newsletter.subscribe") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ email: this.email })
            })
            .then(response => response.json())
            .then(data => {
                this.loading = false;
                
                if (data.success) {
                    this.success = data.message;
                    this.email = '';
                    
                    // Auto hide success message after 5 seconds with fade out
                    setTimeout(() => {
                        this.success = '';
                    }, 5000);
                } else {
                    this.error = data.message || 'Failed to subscribe';
                    
                    // Auto hide error message after 5 seconds with fade out
                    setTimeout(() => {
                        this.error = '';
                    }, 5000);
                }
            })
            .catch(error => {
                this.loading = false;
                this.error = 'An error occurred. Please try again.';
                console.error('Newsletter error:', error);
                
                // Auto hide error message after 5 seconds with fade out
                setTimeout(() => {
                    this.error = '';
                }, 5000);
            });
        }
    }
}
</script>