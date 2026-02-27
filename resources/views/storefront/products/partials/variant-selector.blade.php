@props(['product'])

@php
    $variants = $product->activeVariants;
    $attributeGroups = [];
    $productAttributes = $product->variant_attributes;
    
    // Group by attribute name
    foreach ($productAttributes as $attrName => $values) {
        $attributeGroups[$attrName] = $values;
    }
@endphp

@if($variants->count() > 0)
    <div class="product-variants space-y-6" 
         x-data="variants({{ $product->id }}, {{ json_encode($variants->map(function($variant) {
             return [
                 'id' => $variant->id,
                 'sku' => $variant->sku,
                 'attributes' => $variant->attributes,
                 'price' => $variant->price,
                 'stock' => $variant->stock,
                 'in_stock' => $variant->in_stock
             ];
         })) }})">
         
        @foreach($attributeGroups as $attrName => $values)
            <div class="variant-group">
                <h4 class="text-sm font-medium text-gray-900 mb-3">
                    {{ ucfirst(str_replace('_', ' ', $attrName)) }}
                </h4>
                
                <div class="flex flex-wrap gap-2">
                    @foreach($values as $value)
                        @php
                            $displayType = $attrName === 'color' ? 'color' : 
                                          (in_array(strtolower($attrName), ['size', 'storage', 'ram']) ? 'button' : 'default');
                        @endphp
                        
                        @if($displayType === 'color')
                            <button type="button"
                                    class="variant-option color-option w-10 h-10 rounded-full border-2 border-gray-200 hover:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition-all"
                                    style="background-color: {{ $value }}"
                                    :class="{ 'border-blue-500 ring-2 ring-blue-200': selectedAttributes['{{ $attrName }}'] === '{{ $value }}' }"
                                    @click="selectAttribute('{{ $attrName }}', '{{ $value }}')"
                                    :disabled="!isCombinationAvailable('{{ $attrName }}', '{{ $value }}')"
                                    title="{{ $value }}">
                            </button>
                            
                        @elseif($displayType === 'button')
                            <button type="button"
                                    class="variant-option px-4 py-2 border border-gray-300 rounded-lg text-sm hover:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition-all"
                                    :class="{ 'bg-blue-500 text-white border-blue-500': selectedAttributes['{{ $attrName }}'] === '{{ $value }}' }"
                                    @click="selectAttribute('{{ $attrName }}', '{{ $value }}')"
                                    :disabled="!isCombinationAvailable('{{ $attrName }}', '{{ $value }}')">
                                {{ $value }}
                            </button>
                            
                        @else
                            <label class="inline-flex items-center">
                                <input type="radio" 
                                       name="{{ $attrName }}" 
                                       value="{{ $value }}"
                                       class="form-radio text-blue-600"
                                       @change="selectAttribute('{{ $attrName }}', '{{ $value }}')"
                                       :disabled="!isCombinationAvailable('{{ $attrName }}', '{{ $value }}')">
                                <span class="ml-2 text-sm text-gray-700">{{ $value }}</span>
                            </label>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
        
        {{-- Selected Variant Info --}}
        <div class="selected-variant mt-6 p-4 bg-gray-50 rounded-lg" x-show="selectedVariant" x-cloak>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Selected Configuration:</p>
                    <p class="font-medium" x-text="getSelectedVariantDisplay()"></p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-blue-600" x-text="'৳' + selectedVariant.price.toFixed(2)"></p>
                    <p class="text-sm" :class="selectedVariant.stock > 0 ? 'text-green-600' : 'text-red-600'"
                       x-text="selectedVariant.stock > 0 ? selectedVariant.stock + ' in stock' : 'Out of stock'">
                    </p>
                </div>
            </div>
        </div>
        
        <style>
        [x-cloak] { display: none !important; }
        </style>
    </div>
@endif