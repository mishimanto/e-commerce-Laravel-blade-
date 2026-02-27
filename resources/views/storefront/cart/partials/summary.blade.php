<div class="bg-white rounded-lg shadow-sm p-6" id="cart-summary">
    <h2 class="text-lg font-bold mb-4">Cart Summary</h2>
    
    <div class="space-y-3 mb-4">
        <div class="flex justify-between">
            <span class="text-gray-600">Subtotal:</span>
            <span class="font-medium" id="summary-subtotal">৳{{ number_format($subtotal ?? 0, 2) }}</span>
        </div>
        
        <div class="flex justify-between text-green-600" id="summary-discount-row" style="{{ isset($discount) && $discount > 0 ? '' : 'display: none;' }}">
            <span>Discount 
                <span id="coupon-code-display" class="text-xs text-gray-500">{{ session('coupon_code') ? '(' . session('coupon_code') . ')' : '' }}</span>
            :</span>
            <span id="summary-discount">{{ isset($discount) && $discount > 0 ? '-৳' . number_format($discount, 2) : '-৳0.00' }}</span>
        </div>
        
        <!-- <div class="flex justify-between" id="summary-shipping-row" style="{{ isset($shipping) && $shipping > 0 ? '' : 'display: none;' }}">
            <span>Shipping:</span>
            <span id="summary-shipping">৳{{ number_format($shipping ?? 0, 2) }}</span>
        </div> -->
        
        <div class="border-t pt-3">
            <div class="flex justify-between font-bold">
                <span>Total:</span>
                <span class="text-xl text-blue-600" id="summary-total">৳{{ number_format($total ?? 0, 2) }}</span>
            </div>
        </div>
    </div>
    
    <a href="{{ route('checkout.index') }}" 
       class="block w-full bg-blue-600 text-white text-center py-3 rounded-lg hover:bg-blue-700 transition">
        Proceed to Checkout
    </a>
    
    {{-- Coupon Code --}}
    <div class="mt-4" x-data="couponManager()">
        <div x-show="loading" class="text-center text-sm text-gray-500 mb-2">
            <i class="fas fa-spinner fa-spin mr-1"></i> Processing...
        </div>
        
        <div x-show="!loading && !appliedCoupon" class="mb-2">
            <div x-show="error" class="text-red-500 text-sm mb-2" x-text="error"></div>
            <form @submit.prevent="applyCoupon" class="flex">
                <input type="text" 
                       x-model="code"
                       placeholder="Coupon code"
                       class="flex-1 border border-gray-300 rounded-l-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                       :disabled="loading">
                <button type="submit" 
                        class="bg-gray-800 text-white px-4 py-2 rounded-r-lg text-sm hover:bg-gray-900 transition disabled:opacity-50"
                        :disabled="loading || !code">
                    Apply
                </button>
            </form>
        </div>
        
        <div x-show="!loading && appliedCoupon" class="bg-green-50 p-3 rounded-lg">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm font-medium text-green-700">
                        <i class="fas fa-check-circle mr-1"></i> Coupon Applied!
                    </p>
                    <p class="text-xs text-green-600 mt-1">
                        <span x-text="appliedCoupon.code"></span> - <span x-text="appliedCoupon.discountText"></span>
                    </p>
                </div>
                <button @click="removeCoupon" class="text-red-500 hover:text-red-700 text-sm" :disabled="loading">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Function to update cart summary
window.updateCartSummary = function(data) {
    // Update subtotal
    const subtotalEl = document.getElementById('summary-subtotal');
    if (subtotalEl) {
        subtotalEl.textContent = '৳' + (data.subtotal || 0).toFixed(2);
    }
    
    // Update discount
    const discountRow = document.getElementById('summary-discount-row');
    const discountEl = document.getElementById('summary-discount');
    const couponDisplay = document.getElementById('coupon-code-display');
    
    if (discountRow && discountEl && couponDisplay) {
        if (data.discount && data.discount > 0) {
            discountRow.style.display = 'flex';
            discountEl.textContent = '-৳' + (data.discount || 0).toFixed(2);
            if (data.coupon_code) {
                couponDisplay.textContent = '(' + data.coupon_code + ')';
            }
        } else {
            discountRow.style.display = 'none';
            couponDisplay.textContent = '';
        }
    }
    
    // Update total
    const totalEl = document.getElementById('summary-total');
    if (totalEl) {
        totalEl.textContent = '৳' + (data.total || 0).toFixed(2);
    }
};

// Alpine.js Component for Coupon Management
// Coupon Manager Component
function couponManager() {
    return {
        code: '',
        loading: false,
        error: '',
        appliedCoupon: null,
        
        init() {
            @if(isset($coupon_code) && isset($discount) && $discount > 0)
                this.appliedCoupon = {
                    code: '{{ $coupon_code }}',
                    discount: {{ $discount }},
                    discountText: '{{ $discount > 0 ? "৳" . number_format($discount, 2) . " off" : "" }}'
                };
            @endif
        },
        
        async applyCoupon() {
            if (!this.code) {
                this.error = 'Please enter a coupon code';
                return;
            }
            
            this.loading = true;
            this.error = '';
            
            try {
                const response = await fetch('{{ route("cart.apply-coupon") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ code: this.code })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Parse discount to float
                    const discount = parseFloat(data.discount) || 0;
                    
                    this.appliedCoupon = {
                        code: this.code,
                        discount: discount,
                        discountText: this.formatDiscountText(discount)
                    };
                    
                    // Update checkout component's discount (if needed)
                    // For now, we'll update the summary directly
                    window.updateCartSummary({
                        subtotal: data.subtotal,
                        discount: discount,
                        total: data.total,
                        coupon_code: this.code
                    });
                    
                    this.code = '';
                    this.error = '';
                    
                    // Dispatch event for other components
                    window.dispatchEvent(new CustomEvent('cart-updated', { 
                        detail: { count: data.cart_count } 
                    }));
                    
                } else {
                    this.error = data.message || 'Invalid coupon code';
                }
            } catch (error) {
                console.error('Error applying coupon:', error);
                this.error = 'Failed to apply coupon';
            } finally {
                this.loading = false;
            }
        },
        
        async removeCoupon() {
            this.loading = true;
            
            try {
                const response = await fetch('{{ route("cart.remove-coupon") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.appliedCoupon = null;
                    
                    window.updateCartSummary({
                        subtotal: data.subtotal,
                        discount: 0,
                        total: data.total,
                        coupon_code: null
                    });
                    
                    window.dispatchEvent(new CustomEvent('cart-updated', { 
                        detail: { count: data.cart_count } 
                    }));
                    
                } else {
                    alert(data.message || 'Failed to remove coupon');
                }
            } catch (error) {
                console.error('Error removing coupon:', error);
                alert('Failed to remove coupon');
            } finally {
                this.loading = false;
            }
        },
        
        formatDiscountText(discount) {
            // Parse to float and ensure it's a number
            const discountValue = parseFloat(discount) || 0;
            return '৳' + discountValue.toFixed(2) + ' off';
        }
    }
}

// Global function to update cart summary
window.updateCartSummary = function(data) {
    const subtotalEl = document.getElementById('summary-subtotal');
    if (subtotalEl) {
        const subtotal = parseFloat(data.subtotal) || 0;
        subtotalEl.textContent = '৳' + subtotal.toFixed(2);
    }
    
    const discountRow = document.getElementById('summary-discount-row');
    const discountEl = document.getElementById('summary-discount');
    const couponDisplay = document.getElementById('coupon-code-display');
    
    if (discountRow && discountEl && couponDisplay) {
        const discount = parseFloat(data.discount) || 0;
        if (discount > 0) {
            discountRow.style.display = 'flex';
            discountEl.textContent = '-৳' + discount.toFixed(2);
            if (data.coupon_code) {
                couponDisplay.textContent = '(' + data.coupon_code + ')';
            }
        } else {
            discountRow.style.display = 'none';
            couponDisplay.textContent = '';
        }
    }
    
    const totalEl = document.getElementById('summary-total');
    if (totalEl) {
        const total = parseFloat(data.total) || 0;
        totalEl.textContent = '৳' + total.toFixed(2);
    }
};

// Listen for cart updates from parent page
document.addEventListener('cart-updated', function(e) {
    console.log('Cart updated event received:', e.detail);
});
</script>