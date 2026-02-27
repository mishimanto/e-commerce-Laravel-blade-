@extends('layouts.app')

@section('title', 'Checkout - ' . config('app.name'))

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Checkout</h1>

        {{-- Show Error Messages --}}
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <strong class="font-bold">Please fix the following errors:</strong>
                <ul class="list-disc list-inside mt-2">
                    @foreach($errors->all() as $error)
                        <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Show Success Message --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form" x-data="checkout()">
            @csrf
            
            <div class="flex flex-col lg:flex-row gap-8">
                {{-- Checkout Form --}}
                <div class="lg:w-2/3">
                    {{-- Contact Information --}}
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h2 class="text-xl font-bold mb-4">Contact Information</h2>
                        
                        @guest
                            <div class="mb-4">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="create_account" 
                                           x-model="createAccount"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-600">Create an account for faster checkout</span>
                                </label>
                            </div>
                        @endguest

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                                <input type="email" 
                                       name="email" 
                                       value="{{ old('email', auth()->user()->email ?? '') }}"
                                       required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 @error('email') border-red-500 @enderror">
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number <span class="text-red-500">*</span></label>
                                <input type="tel" 
                                       name="phone" 
                                       value="{{ old('phone', auth()->user()->phone ?? '') }}"
                                       required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 @error('phone') border-red-500 @enderror">
                                @error('phone')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Shipping Address --}}
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h2 class="text-xl font-bold mb-4">Shipping Address</h2>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       name="shipping_name" 
                                       value="{{ old('shipping_name', auth()->user()->name ?? '') }}"
                                       required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 @error('shipping_name') border-red-500 @enderror">
                                @error('shipping_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Address <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       name="shipping_address" 
                                       value="{{ old('shipping_address', auth()->user()->address ?? '') }}"
                                       required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 @error('shipping_address') border-red-500 @enderror">
                                @error('shipping_address')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">City <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       name="shipping_city" 
                                       value="{{ old('shipping_city', auth()->user()->city ?? '') }}"
                                       required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 @error('shipping_city') border-red-500 @enderror">
                                @error('shipping_city')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">State/Division <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       name="shipping_state" 
                                       value="{{ old('shipping_state', auth()->user()->state ?? '') }}"
                                       required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 @error('shipping_state') border-red-500 @enderror">
                                @error('shipping_state')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       name="shipping_zip" 
                                       value="{{ old('shipping_zip', auth()->user()->zip ?? '') }}"
                                       required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 @error('shipping_zip') border-red-500 @enderror">
                                @error('shipping_zip')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Country <span class="text-red-500">*</span></label>
                                <select name="shipping_country" 
                                        required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500 @error('shipping_country') border-red-500 @enderror">
                                    <option value="Bangladesh" {{ old('shipping_country', 'Bangladesh') == 'Bangladesh' ? 'selected' : '' }}>Bangladesh</option>
                                </select>
                                @error('shipping_country')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Different Billing Address Checkbox --}}
                        <div class="mt-4 pt-4 border-t">
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" 
                                       name="different_billing" 
                                       x-model="differentBilling"
                                       value="1"
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700 group-hover:text-gray-900">
                                    Billing address is different from shipping address
                                </span>
                            </label>
                        </div>
                    </div>

                    {{-- Billing Address - Only shown when checkbox is checked --}}
                    <div x-show="differentBilling" 
                         x-cloak 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h2 class="text-xl font-bold mb-4">Billing Address</h2>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       name="billing_name" 
                                       x-bind:required="differentBilling"
                                       value="{{ old('billing_name') }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500">
                                @error('billing_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Address <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       name="billing_address" 
                                       x-bind:required="differentBilling"
                                       value="{{ old('billing_address') }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500">
                                @error('billing_address')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">City <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       name="billing_city" 
                                       x-bind:required="differentBilling"
                                       value="{{ old('billing_city') }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500">
                                @error('billing_city')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">State/Division <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       name="billing_state" 
                                       x-bind:required="differentBilling"
                                       value="{{ old('billing_state') }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500">
                                @error('billing_state')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       name="billing_zip" 
                                       x-bind:required="differentBilling"
                                       value="{{ old('billing_zip') }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500">
                                @error('billing_zip')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Country <span class="text-red-500">*</span></label>
                                <select name="billing_country" 
                                        x-bind:required="differentBilling"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500">
                                    <option value="Bangladesh" {{ old('billing_country', 'Bangladesh') == 'Bangladesh' ? 'selected' : '' }}>Bangladesh</option>
                                </select>
                                @error('billing_country')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Shipping Method --}}
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h2 class="text-xl font-bold mb-4">Shipping Method</h2>

                        <div class="space-y-3">
                            @foreach($shippingMethods as $method)
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:border-blue-500 transition-all duration-200"
                                       :class="{'border-blue-500 bg-blue-50': shippingMethod === '{{ $method['code'] }}'}">
                                    <input type="radio" 
                                           name="shipping_method" 
                                           value="{{ $method['code'] }}"
                                           x-model="shippingMethod"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <div class="ml-3 flex-1">
                                        <div class="flex justify-between">
                                            <span class="font-medium">{{ $method['name'] }}</span>
                                            <span class="font-medium text-blue-600">৳ {{ number_format($method['cost'], 2) }}</span>
                                        </div>
                                        <!-- <p class="text-sm text-gray-500">{{ $method['description'] }}</p> -->
                                        <p class="text-xs text-gray-400 mt-1">
                                            <i class="far fa-clock mr-1"></i> Delivery: {{ $method['delivery_time'] }}
                                        </p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('shipping_method')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Payment Method --}}
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h2 class="text-xl font-bold mb-4">Payment Method</h2>

                        <div class="space-y-3">
                            @foreach($paymentMethods as $method)
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:border-blue-500 transition-all duration-200"
                                       :class="{'border-blue-500 bg-blue-50': paymentMethod === '{{ $method['code'] }}'}">
                                    <input type="radio" 
                                           name="payment_method" 
                                           value="{{ $method['code'] }}"
                                           x-model="paymentMethod"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center">
                                            <span class="font-medium">{{ $method['name'] }}</span>
                                            @if($method['icon'])
                                                <img src="{{ asset($method['icon']) }}" alt="{{ $method['name'] }}" class="h-6 ml-2">
                                            @endif
                                        </div>
                                        <!-- <p class="text-sm text-gray-500">{{ $method['description'] }}</p> -->
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('payment_method')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Order Notes --}}
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h2 class="text-xl font-bold mb-4">Order Notes (Optional)</h2>
                        <textarea name="notes" 
                                  rows="3" 
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500"
                                  placeholder="Special instructions for delivery">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- Order Summary --}}
                <div class="lg:w-1/3">
                    <div class="bg-white rounded-lg shadow-sm p-6 sticky top-6">
                        <h2 class="text-xl font-bold mb-4">Order Summary</h2>

                        {{-- Cart Items --}}
                        <div class="space-y-3 mb-4 max-h-60 overflow-y-auto pr-2">
                            @foreach($formattedItems as $item)
                                <div class="flex justify-between text-sm border-b border-gray-100 pb-2">
                                    <div class="flex-1">
                                        <span class="font-medium text-blue-700">{{ $item['name'] }}</span>
                                        <span class="text-gray-500 ml-1">x{{ $item['quantity'] }}</span>
                                        
                                        {{-- Show variant attributes if exists --}}
                                        @if(isset($item['attributes']) && is_array($item['attributes']) && count($item['attributes']) > 0)
                                            <p class="text-xs text-gray-500 mt-1">
                                                @foreach($item['attributes'] as $key => $value)
                                                    <span class="mr-1">{{ ucfirst($key) }}: {{ $value }}</span>
                                                @endforeach
                                            </p>
                                        @endif
                                        
                                        {{-- Show variant SKU if exists --}}
                                        <!-- @if(isset($item['variant_sku']) && $item['variant_sku'])
                                            <p class="text-xs text-gray-400 mt-1">
                                                SKU: {{ $item['variant_sku'] }}
                                            </p>
                                        @endif -->
                                    </div>
                                    <span class="font-medium text-gray-700">
                                        ৳{{ number_format($item['subtotal'], 2) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Summary --}}
                        <div class="space-y-2 text-sm border-t border-gray-200 pt-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-medium" id="summary-subtotal">৳{{ number_format($subtotal, 2) }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping:</span>
                                <span class="font-medium" id="summary-shipping" x-text="'৳' + shippingCost.toFixed(2)"></span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax:</span>
                                <span class="font-medium" id="summary-tax" x-text="'৳' + taxAmount.toFixed(2)"></span>
                            </div>
                            
                            @if($discount > 0)
                                <div class="flex justify-between text-green-600">
                                    <span>Discount:</span>
                                    <span class="font-medium" id="summary-discount">-৳{{ number_format($discount, 2) }}</span>
                                </div>
                            @endif
                            
                            <div class="flex justify-between text-base font-bold pt-2 border-t border-gray-200 mt-2">
                                <span>Total:</span>
                                <span class="text-xl text-blue-600" id="summary-total" x-text="'৳' + totalAmount.toFixed(2)"></span>
                            </div>
                        </div>

                        {{-- Place Order Button --}}
                        <button type="submit" 
                                class="w-full bg-blue-600 text-white py-3 rounded-lg mt-6 hover:bg-blue-700 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed font-semibold"
                                :disabled="!canPlaceOrder || processing">
                            <span x-show="!processing" class="flex items-center justify-center">
                                <!-- <i class="fas fa-lock mr-2"></i> -->
                                 Place Order
                            </span>
                            <span x-show="processing" class="flex items-center justify-center">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Processing...
                            </span>
                        </button>

                        {{-- Secure Checkout Notice --}}
                        <!-- <div class="mt-4 text-center">
                            <i class="fas fa-shield-alt text-green-600 mr-1"></i>
                            <span class="text-xs text-gray-500">Secure Checkout - SSL Encrypted</span>
                        </div> -->

                        {{-- Terms --}}
                        <p class="text-xs text-gray-500 text-center mt-4">
                            By placing your order, you agree to our 
                            <a href="{{ route('terms') }}" class="text-blue-600 hover:text-blue-700">Terms of Service</a> 
                            and 
                            <a href="{{ route('privacy') }}" class="text-blue-600 hover:text-blue-700">Privacy Policy</a>.
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <style>
    [x-cloak] { display: none !important; }
    .sticky {
        position: sticky;
        top: 1rem;
    }
    </style>
@endsection

@push('scripts')
<script>
function checkout() {
    return {
        createAccount: false,
        differentBilling: false,
        shippingMethod: '{{ old('shipping_method', 'standard') }}',
        paymentMethod: '{{ old('payment_method') }}',
        processing: false,
        
        get shippingCost() {
            const methods = @json($shippingMethods);
            const method = methods.find(m => m.code === this.shippingMethod);
            return method ? parseFloat(method.cost) : 0;
        },
        
        get subtotal() {
            return parseFloat({{ $subtotal }});
        },
        
        get discount() {
            return parseFloat({{ $discount }});
        },
        
        get taxRate() {
            return {{ $taxRate }};
        },
        
        get taxAmount() {
            // Tax is calculated on subtotal + shipping
            // const taxableAmount = this.subtotal + this.shippingCost;
             return (this.subtotal * this.taxRate) / 100;
        },
        
        get totalAmount() {
            return this.subtotal + this.shippingCost + this.taxAmount - this.discount;
        },
        
        get canPlaceOrder() {
            return this.shippingMethod && this.paymentMethod;
        },
        
        init() {
            // Set processing to false when page loads
            this.processing = false;
            
            // Update summary when shipping method changes
            this.$watch('shippingMethod', () => {
                // This will trigger the getters to recalculate
            });
            
            // Listen for form submit
            document.getElementById('checkout-form').addEventListener('submit', (e) => {
                this.processing = true;
                
                // Log form data for debugging
                const formData = new FormData(e.target);
                console.log('Submitting checkout form:', Object.fromEntries(formData));
            });
        }
    }
}
</script>
@endpush