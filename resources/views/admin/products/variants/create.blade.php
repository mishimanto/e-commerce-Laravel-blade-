{{-- resources/views/admin/products/variants/create.blade.php --}}

@extends('layouts.admin')

@section('title', 'Create Variant - ' . $product->name)

@section('content')
<div class="">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-500">Create New Variant</h1>
            <p class="text-gray-600">Product: <span class="font-medium">{{ $product->name }}</span></p>
        </div>
        <a href="{{ route('admin.products.variants', $product) }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Variants
        </a>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('admin.products.variants.store', $product) }}" 
              method="POST" 
              enctype="multipart/form-data"
              class="p-6"
              id="variantForm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Left Column - Attributes --}}
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Variant Attributes</h3>
                        
                        @if(isset($attributes) && $attributes->count() > 0)
                            @foreach($attributes as $attribute)
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ $attribute->name }}
                                    </label>
                                    
                                    @if($attribute->type == 'color')
                                        <div class="flex flex-wrap gap-4">
                                            @foreach($attribute->values as $value)
                                                <label class="inline-flex items-center cursor-pointer group">
                                                    <input type="radio" 
                                                           name="attributes[{{ $attribute->slug }}]" 
                                                           value="{{ $value->value }}" 
                                                           class="hidden peer attribute-input"
                                                           data-attribute="{{ $attribute->slug }}"
                                                           {{ old('attributes.' . $attribute->slug) == $value->value ? 'checked' : '' }}
                                                           >
                                                    <span class="w-10 h-10 rounded-full border-2 border-gray-200 peer-checked:border-blue-500 peer-checked:ring-2 peer-checked:ring-blue-200 transition-all group-hover:scale-110" 
                                                          style="background-color: {{ $value->color_code ?? '#cccccc' }}"
                                                          title="{{ $value->value }}"></span>
                                                </label>
                                            @endforeach
                                        </div>

                                    @elseif($attribute->type == 'size')
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($attribute->values as $value)
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="radio" 
                                                           name="attributes[{{ $attribute->slug }}]" 
                                                           value="{{ $value->value }}" 
                                                           class="hidden peer attribute-input"
                                                           data-attribute="{{ $attribute->slug }}"
                                                           {{ old('attributes.' . $attribute->slug) == $value->value ? 'checked' : '' }}
                                                           >
                                                    <span class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium peer-checked:bg-blue-500 peer-checked:text-white peer-checked:border-blue-500 hover:bg-gray-50 transition-all">
                                                        {{ $value->value }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>

                                    @else
                                        <select name="attributes[{{ $attribute->slug }}]" 
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 attribute-select"
                                                data-attribute="{{ $attribute->slug }}"
                                                >
                                            <option value="">Select {{ $attribute->name }}</option>
                                            @foreach($attribute->values as $value)
                                                <option value="{{ $value->value }}" 
                                                    {{ old('attributes.' . $attribute->slug) == $value->value ? 'selected' : '' }}>
                                                    {{ $value->value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif

                                    @error('attributes.' . $attribute->slug)
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8 bg-gray-50 rounded-lg">
                                <p class="text-gray-500">No attributes available.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right Column - Variant Details --}}
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Variant Details</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">SKU (Optional)</label>
                                <input type="text" name="sku" value="{{ old('sku') }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Leave empty to auto-generate">
                                @error('sku')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- BUYING PRICE (New Field) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 required">Buying Price (৳)</label>
                                <input type="number" step="0.01" name="buying_price" 
                                    value="{{ old('buying_price') }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Cost price for this variant" required>
                                <p class="text-xs text-gray-500 mt-1">
                                    Your purchase cost for this specific variant. Leave empty to use product's buying price.
                                </p>
                                @error('buying_price')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 required">Price Adjustment (৳)</label>
                                <input type="number" step="0.01" name="price_adjustment" 
                                       value="{{ old('price_adjustment', 0) }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       required>
                                <p class="text-xs text-gray-500 mt-1">
                                    Base price: ৳{{ number_format($product->base_price, 2) }}
                                </p>
                                @error('price_adjustment')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 required">Stock Quantity</label>
                                <input type="number" name="stock" value="{{ old('stock') }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       required min="0">
                                @error('stock')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 required">Status</label>
                                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Image Upload Field with Preview and Remove Option --}}
                            <div x-data="imageManager()">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Variant Image</label>
                                
                                <div class="flex items-center justify-center w-full">
                                    <label for="image" class="w-full flex flex-col items-center px-4 py-6 bg-white text-blue-500 rounded-lg border-2 border-dashed border-gray-300 cursor-pointer hover:border-blue-500 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="mt-2 text-sm text-gray-500">Click to upload image</span>
                                        <span class="text-xs text-gray-400">PNG, JPG, GIF up to 2MB</span>
                                        <input id="image" type="file" name="image" accept="image/*" class="hidden" @change="fileChanged">
                                    </label>
                                </div>
                                @error('image')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror

                                {{-- Image Preview with Remove Option --}}
                                <template x-if="imagePreview">
                                    <div class="mt-3">
                                        <p class="text-xs text-gray-500 mb-2">Image Preview:</p>
                                        <div class="relative inline-block">
                                            <img :src="imagePreview" 
                                                 alt="Preview" 
                                                 class="w-24 h-24 object-cover rounded-lg border border-gray-200">
                                            <button type="button" 
                                                    @click="removePreview()"
                                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors shadow-md"
                                                    title="Remove preview">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                        <p class="text-xs text-gray-400 mt-1">Click the X button to remove preview</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.products.variants', $product) }}" 
                   class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Create Variant
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

<style>
    .bootstrap-tagsinput {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
        border-color: #e5e7eb;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    .bootstrap-tagsinput .tag {
        background: #3b82f6;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }
    .bootstrap-tagsinput input {
        padding: 0.25rem;
    }
    .select2-container--default .select2-selection--multiple {
        border-color: #e5e7eb;
        border-radius: 0.5rem;
        padding: 0.25rem;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #3b82f6;
        ring: 2px solid #3b82f6;
    }
    .required:after {
        content: " *";
        color: #ef4444;
    }
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    /* Image preview styles */
    .image-preview-item {
        transition: all 0.3s ease;
    }
    .image-preview-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .image-preview-item .actions {
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .image-preview-item:hover .actions {
        opacity: 1;
    }
</style>

@push('scripts')
<script>
// Image Manager Alpine Component
function imageManager() {
    return {
        imagePreview: null,
        
        removePreview() {
            this.imagePreview = null;
            
            // Clear file input
            const fileInput = document.getElementById('image');
            if (fileInput) {
                fileInput.value = '';
            }
        },
        
        fileChanged(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                this.imagePreview = null;
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // =========================
    // FILTER EMPTY ATTRIBUTES BEFORE SUBMIT
    // =========================
    const form = document.getElementById('variantForm');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            // Get all attribute inputs (radio buttons and selects)
            const radioGroups = {};
            const selects = document.querySelectorAll('select.attribute-select');
            
            // Handle radio buttons - find which ones are checked
            document.querySelectorAll('input[type="radio"].attribute-input').forEach(radio => {
                const attrName = radio.getAttribute('data-attribute');
                
                if (!radioGroups[attrName]) {
                    radioGroups[attrName] = {
                        anyChecked: false,
                        elements: []
                    };
                }
                
                radioGroups[attrName].elements.push(radio);
                
                if (radio.checked) {
                    radioGroups[attrName].anyChecked = true;
                }
            });
            
            // Disable all radio buttons in groups where nothing is selected
            Object.keys(radioGroups).forEach(attrName => {
                if (!radioGroups[attrName].anyChecked) {
                    radioGroups[attrName].elements.forEach(radio => {
                        radio.disabled = true;
                    });
                }
            });
            
            // Handle select dropdowns - disable if no value selected
            selects.forEach(select => {
                if (!select.value || select.value === '') {
                    select.disabled = true;
                }
            });
            
            // Optional: Show a message about what's being submitted
            console.log('Form submitted with filtered attributes');
            
            // The form will submit with disabled fields excluded
            // This is fine because the page will reload on success/failure
        });
    }

    // =========================
    // PRICE ADJUSTMENT PREVIEW
    // =========================
    const priceInput = document.querySelector('input[name="price_adjustment"]');
    const basePrice = {{ $product->base_price }};
    
    if (priceInput) {
        // Create price preview element if it doesn't exist
        const priceField = priceInput.closest('div');
        let pricePreview = priceField.querySelector('.price-preview');
        
        if (!pricePreview) {
            pricePreview = document.createElement('p');
            pricePreview.className = 'text-xs text-green-600 mt-1 price-preview';
            priceField.appendChild(pricePreview);
        }
        
        function updatePricePreview() {
            const adjustment = parseFloat(priceInput.value) || 0;
            const finalPrice = basePrice + adjustment;
            pricePreview.textContent = `Final price will be: ৳${finalPrice.toFixed(2)}`;
        }
        
        priceInput.addEventListener('input', updatePricePreview);
        updatePricePreview(); // Initial update
    }

    // =========================
    // SKU GENERATION HINT
    // =========================
    const skuInput = document.querySelector('input[name="sku"]');
    
    if (skuInput) {
        skuInput.addEventListener('focus', function() {
            if (!this.value) {
                // Show hint about auto-generation
                const hint = document.createElement('p');
                hint.className = 'text-xs text-blue-600 mt-1 sku-hint';
                hint.textContent = 'Leave empty to auto-generate based on product SKU and selected attributes';
                
                // Remove any existing hint
                const existingHint = this.closest('div').querySelector('.sku-hint');
                if (existingHint) existingHint.remove();
                
                this.closest('div').appendChild(hint);
            }
        });
    }
});
</script>
@endpush