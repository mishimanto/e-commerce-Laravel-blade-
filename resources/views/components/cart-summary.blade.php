<div class="bg-white rounded-lg shadow-sm p-6" x-data="cartSummary()">
    <h2 class="text-lg font-bold mb-4">Order Summary</h2>
    
    {{-- Cart Items --}}
    <div class="space-y-3 mb-4 max-h-60 overflow-y-auto">
        @foreach($items as $item)
            <div class="flex justify-between text-sm">
                <div class="flex-1">
                    <span class="font-medium">{{ $item->name }}</span>
                    <span class="text-gray-500"> x{{ $item->quantity }}</span>
                </div>
                <span class="font-medium">৳{{ number_format($item->price * $item->quantity) }}</span>
            </div>
        @endforeach
    </div>

    {{-- Coupon Code --}}
    <div class="mb-4">
        <form @submit.prevent="applyCoupon" class="flex">
            <input type="text" 
                   x-model="couponCode"
                   placeholder="Coupon code"
                   class="flex-1 border border-gray-300 rounded-l-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            <button type="submit" 
                    class="bg-gray-800 text-white px-4 py-2 rounded-r-lg text-sm hover:bg-gray-900">
                Apply
            </button>
        </form>
        <p x-show="couponMessage" x-text="couponMessage" 
           :class="couponSuccess ? 'text-green-600' : 'text-red-600'"
           class="text-xs mt-1"></p>
    </div>

    {{-- Summary --}}
    <div class="space-y-2 text-sm border-t pt-4">
        <div class="flex justify-between">
            <span class="text-gray-600">Subtotal:</span>
            <span class="font-medium">৳{{ number_format($subtotal) }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Shipping:</span>
            <span class="font-medium" x-text="shippingText"></span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Tax ({{ setting('tax_rate', 15) }}%):</span>
            <span class="font-medium" x-text="taxText"></span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Discount:</span>
            <span class="font-medium text-green-600" x-text="discountText"></span>
        </div>
        <div class="flex justify-between text-base font-bold pt-2 border-t">
            <span>Total:</span>
            <span class="text-xl text-blue-600" x-text="totalText"></span>
        </div>
    </div>

    {{-- Checkout Button --}}
    <a href="{{ route('checkout.index') }}" 
       class="block w-full bg-blue-600 text-white text-center py-3 rounded-lg mt-4 hover:bg-blue-700">
        Proceed to Checkout
    </a>
</div>

@push('scripts')
<script>
function cartSummary() {
    return {
        couponCode: '',
        couponMessage: '',
        couponSuccess: false,
        shipping: {{ setting('shipping_flat_rate', 100) }},
        tax: {{ $subtotal * (setting('tax_rate', 15) / 100) }},
        discount: 0,
        
        get shippingText() {
            return '৳' + this.shipping.toFixed(2);
        },
        
        get taxText() {
            return '৳' + this.tax.toFixed(2);
        },
        
        get discountText() {
            return this.discount > 0 ? '-৳' + this.discount.toFixed(2) : '৳0.00';
        },
        
        get totalText() {
            const total = {{ $subtotal }} + this.shipping + this.tax - this.discount;
            return '৳' + total.toFixed(2);
        },
        
        async applyCoupon() {
            if (!this.couponCode) return;
            
            try {
                const response = await fetch('{{ route("cart.apply-coupon") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ code: this.couponCode })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.couponSuccess = true;
                    this.couponMessage = data.message;
                    this.discount = data.discount;
                } else {
                    this.couponSuccess = false;
                    this.couponMessage = data.message;
                }
            } catch (error) {
                this.couponSuccess = false;
                this.couponMessage = 'Failed to apply coupon';
            }
        }
    }
}
</script>
@endpush