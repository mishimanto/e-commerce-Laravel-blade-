<div 
    x-data="quickViewModal()" 
    x-show="open" 
    @quick-view.window="openModal($event.detail)"
    @close-quick-view.window="closeModal()"
    x-cloak
    class="fixed inset-0 z-[9999] overflow-y-auto"
    aria-labelledby="modal-title" 
    role="dialog" 
    aria-modal="true"
>
    {{-- Backdrop --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"
         @click="closeModal()">
    </div>

    {{-- Modal Content --}}
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-2 sm:p-3">
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden bg-white text-left shadow-2xl transition-all w-full max-w-3xl mx-2 sm:mx-4"
                 @click.away="closeModal()">
                
                {{-- Loading State --}}
                <div x-show="loading" class="p-8 text-center">
                    <svg class="animate-spin h-10 w-10 text-blue-600 mx-auto" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <!-- <p class="mt-3 text-sm text-gray-600">Loading product details...</p> -->
                </div>

                {{-- Product Content --}}
                <div x-show="!loading && productHtml" 
                     x-html="productHtml" 
                     class="max-h-[85vh] overflow-y-auto"
                     x-init="$el.querySelectorAll('script').forEach(script => {
                        if (!script.src) {
                            eval(script.innerHTML);
                        }
                     })">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function quickViewModal() {
    return {
        open: false,
        loading: false,
        productHtml: '',
        
        openModal(detail) {
            this.open = true;
            this.loading = true;
            this.productHtml = '';
            
            // Fetch product details
            fetch(`/products/quick-view/${detail.productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.productHtml = data.html;
                    } else {
                        if (window.showNotification) {
                            window.showNotification('error', data.message || 'Failed to load product', 'Error');
                        }
                        this.closeModal();
                    }
                })
                .catch(error => {
                    console.error('Error loading quick view:', error);
                    if (window.showNotification) {
                        window.showNotification('error', 'Failed to load product details', 'Error');
                    }
                    this.closeModal();
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        
        closeModal() {
            this.open = false;
            this.productHtml = '';
        }
    };
}

// Global quickView function
window.quickView = function(productId) {
    console.log('Quick view opened for product:', productId);
    window.dispatchEvent(new CustomEvent('quick-view', { 
        detail: { productId: productId } 
    }));
};

// Make sure Alpine picks up the component
if (typeof Alpine !== 'undefined') {
    Alpine.data('quickViewModal', quickViewModal);
}
</script>