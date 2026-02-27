@props([
    'categories' => null,
    'brands' => null,
    'attributes' => null,
    'selected' => []
])

@php
    use App\Models\Category;
    use Illuminate\Support\Facades\DB;
    
    $categories = $categories ?? Category::with('children')
        ->whereNull('parent_id')
        ->where('status', 1)
        ->orderBy('sort_order')
        ->get();
    
    $brands = $brands ?? collect([]);
    $attributes = $attributes ?? collect([]);
    
    // Get min and max price from database for accuracy
    $dbPriceRange = DB::table('products')
        ->select(DB::raw('MIN(base_price) as min_price, MAX(base_price) as max_price'))
        ->where('status', 'active')
        ->first();
    
    $minPossible = $dbPriceRange ? floor($dbPriceRange->min_price / 1000) * 1000 : 0;
    $maxPossible = $dbPriceRange ? ceil($dbPriceRange->max_price / 1000) * 1000 : 200000;
    
    $selectedMinPrice = $selected['min_price'] ?? $minPossible;
    $selectedMaxPrice = $selected['max_price'] ?? $maxPossible;
@endphp

<div class="bg-white shadow-sm border border-gray-100 p-5" 
     x-data="filters()" 
     x-init="initFilters()">
    
    {{-- Header with close button for mobile --}}
    <div class="flex items-center justify-between mb-5">
        <h3 class="font-bold text-lg flex items-center gap-2 text-gray-800">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
            </svg>
            Filters
        </h3>
        <button @click="clearFilters" class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1 lg:hidden">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Reset
        </button>
    </div>

    {{-- Categories with improved collapse --}}
    @if($categories->isNotEmpty())
    <div class="mb-6 border-b border-gray-100 pb-5">
        <button @click="toggleSection('categories')" 
                class="w-full flex items-center justify-between text-left font-semibold text-gray-700 hover:text-gray-900 mb-3 group">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-400 group-hover:text-blue-500 transition-colors" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
                <span>Categories</span>
            </div>
            <svg class="w-4 h-4 transition-transform duration-300 text-gray-400"
                 :class="{'rotate-180': openSections.categories}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        
        <div x-show="openSections.categories" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="space-y-2">
            @foreach($categories as $category)
                <div class="ml-0" 
                     x-data="{ childOpen: {{ in_array($category->id, $selected['categories'] ?? []) ? 'true' : 'false' }} }">
                    
                    {{-- Parent Category with improved toggle --}}
                    <div class="flex items-center py-1.5 group hover:bg-gray-50 rounded-lg px-2 -mx-2">
                        <label class="flex items-center cursor-pointer flex-1">
                            <input type="checkbox" 
                                   name="categories[]" 
                                   value="{{ $category->id }}"
                                   @change="updateFilters"
                                   {{ in_array($category->id, $selected['categories'] ?? []) ? 'checked' : '' }}
                                   class="hidden peer">
                            <span class="w-4 h-4 border-2 border-gray-300 rounded peer-checked:bg-blue-600 peer-checked:border-blue-600 flex items-center justify-center mr-3 group-hover:border-blue-400 transition-all flex-shrink-0">
                                <svg class="w-3 h-3 text-white hidden peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </span>
                            <span class="text-sm font-medium text-gray-700">{{ $category->name }}</span>
                        </label>
                        
                        @if($category->children->isNotEmpty())
                            <button @click="childOpen = !childOpen" 
                                    class="ml-2 p-1.5 hover:bg-gray-200 rounded-full transition-all duration-200"
                                    :class="{'bg-gray-200 rotate-90': childOpen}">
                                <svg class="w-3.5 h-3.5 text-gray-500 transition-transform duration-200" 
                                     :class="{'rotate-90': childOpen}"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        @endif
                    </div>
                    
                    {{-- Child Categories with smooth animation --}}
                    @if($category->children->isNotEmpty())
                        <div x-show="childOpen" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="ml-8 mt-1.5 space-y-1.5 border-l-2 border-blue-100 pl-3">
                            @foreach($category->children as $child)
                                <label class="flex items-center cursor-pointer group py-1 hover:bg-blue-50 rounded-lg px-2 -ml-2">
                                    <input type="checkbox" 
                                           name="categories[]" 
                                           value="{{ $child->id }}"
                                           @change="updateFilters"
                                           {{ in_array($child->id, $selected['categories'] ?? []) ? 'checked' : '' }}
                                           class="hidden peer">
                                    <span class="w-3.5 h-3.5 border-2 border-gray-300 rounded peer-checked:bg-blue-600 peer-checked:border-blue-600 flex items-center justify-center mr-2.5 group-hover:border-blue-400 transition-all flex-shrink-0">
                                        <svg class="w-2.5 h-2.5 text-white hidden peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </span>
                                    <span class="text-xs text-gray-600 group-hover:text-gray-900">{{ $child->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Improved Price Range Design --}}
    <div class="mb-6 border-b border-gray-100 pb-5">
        <button @click="toggleSection('price')" 
                class="w-full flex items-center justify-between text-left font-semibold text-gray-700 hover:text-gray-900 mb-4 group">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-400 group-hover:text-blue-500 transition-colors" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Price Range</span>
            </div>
            <svg class="w-4 h-4 transition-transform duration-300 text-gray-400"
                 :class="{'rotate-180': openSections.price}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        
        <div x-show="openSections.price" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="px-1">
            
            {{-- Price Display Cards --}}
            <div class="flex items-center gap-3 mb-6">
                <div class="flex-1 bg-gradient-to-br from-gray-50 to-gray-100 p-3 border border-gray-200">
                    <span class="text-xs text-gray-500 block mb-1">Min Price</span>
                    <div class="flex items-center text-lg font-bold text-gray-800">
                        <span class="text-sm mr-1">৳</span>
                        <span x-text="minPrice.toLocaleString()"></span>
                    </div>
                </div>
                <div class="text-gray-400 font-bold">—</div>
                <div class="flex-1 bg-gradient-to-br from-gray-50 to-gray-100 p-3 border border-gray-200">
                    <span class="text-xs text-gray-500 block mb-1">Max Price</span>
                    <div class="flex items-center text-lg font-bold text-gray-800">
                        <span class="text-sm mr-1">৳</span>
                        <span x-text="maxPrice.toLocaleString()"></span>
                    </div>
                </div>
            </div>
            
            {{-- Modern Range Slider --}}
            <div class="relative pt-6 pb-4">
                <!-- Slider Background -->
                <div class="relative h-2 bg-gray-200 rounded-full">
                    <!-- Filled Range -->
                    <div class="absolute h-2 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full" 
                         :style="'left: ' + ((minPrice - minPossible) / (maxPossible - minPossible) * 100) + '%; right: ' + (100 - ((maxPrice - minPossible) / (maxPossible - minPossible) * 100)) + '%'"></div>
                </div>
                
                <!-- Min Slider -->
                <input type="range" 
                       x-model="minPrice"
                       :min="minPossible"
                       :max="maxPossible"
                       step="1000"
                       @input="updateRange"
                       class="absolute top-0 w-full appearance-none bg-transparent pointer-events-none"
                       style="z-index: 2; height: 8px;">
                
                <!-- Max Slider -->
                <input type="range" 
                       x-model="maxPrice"
                       :min="minPossible"
                       :max="maxPossible"
                       step="1000"
                       @input="updateRange"
                       class="absolute top-0 w-full appearance-none bg-transparent pointer-events-none"
                       style="z-index: 3; height: 8px;">
                
                <!-- Range Labels -->
                <div class="flex justify-between mt-4 text-xs text-gray-500">
                    <span class="bg-gray-100 px-2 py-1">৳ {{ number_format($minPossible) }}</span>
                    <span class="bg-gray-100 px-2 py-1">৳ {{ number_format($maxPossible) }}</span>
                </div>
            </div>
            
            {{-- Quick Price Filters --}}
            <div class="flex flex-wrap gap-2 mt-4">
                <button @click="setPriceRange(0, 10000)" 
                        class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 transition-colors text-gray-700">
                    Under ৳10k
                </button>
                <button @click="setPriceRange(10000, 25000)" 
                        class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200  transition-colors text-gray-700">
                    ৳10k - 25k
                </button>
                <button @click="setPriceRange(25000, 50000)" 
                        class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 transition-colors text-gray-700">
                    ৳25k - 50k
                </button>
                <button @click="setPriceRange(50000, 100000)" 
                        class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 transition-colors text-gray-700">
                    ৳50k+
                </button>
            </div>
        </div>
    </div>

    {{-- Brands with improved design --}}
    @if($brands->isNotEmpty())
    <div class="mb-6 border-b border-gray-100 pb-5">
        <button @click="toggleSection('brands')" 
                class="w-full flex items-center justify-between text-left font-semibold text-gray-700 hover:text-gray-900 mb-3 group">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-400 group-hover:text-blue-500 transition-colors" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                </svg>
                <span>Brands</span>
            </div>
            <svg class="w-4 h-4 transition-transform duration-300 text-gray-400"
                 :class="{'rotate-180': openSections.brands}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        
        <div x-show="openSections.brands" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="space-y-2 max-h-60 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
            @foreach($brands as $brand)
                <label class="flex items-center cursor-pointer group py-1.5 hover:bg-gray-50 rounded-lg px-2 -mx-2 transition-colors">
                    <input type="checkbox" 
                           name="brands[]" 
                           value="{{ $brand->id }}"
                           @change="updateFilters"
                           {{ in_array($brand->id, $selected['brands'] ?? []) ? 'checked' : '' }}
                           class="hidden peer">
                    <span class="w-4 h-4 border-2 border-gray-300 rounded peer-checked:bg-blue-600 peer-checked:border-blue-600 flex items-center justify-center mr-3 group-hover:border-blue-400 transition-all flex-shrink-0">
                        <svg class="w-3 h-3 text-white hidden peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </span>
                    <span class="text-sm text-gray-700 group-hover:text-gray-900">{{ $brand->name }}</span>
                </label>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Attributes with improved design --}}
    @if($attributes->isNotEmpty())
        @foreach($attributes as $attribute)
            <div class="mb-6 border-b border-gray-100 pb-5 last:border-b-0">
                <button @click="toggleSection('attr_{{ $attribute->id }}')" 
                        class="w-full flex items-center justify-between text-left font-semibold text-gray-700 hover:text-gray-900 mb-3 group">
                    <div class="flex items-center">
                        @if($attribute->type === 'color')
                            <svg class="w-5 h-5 mr-2 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                            </svg>
                        @else
                            <svg class="w-5 h-5 mr-2 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        @endif
                        <span>{{ $attribute->name }}</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-300 text-gray-400"
                         :class="{'rotate-180': openSections['attr_{{ $attribute->id }}']}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <div x-show="openSections['attr_{{ $attribute->id }}']" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="space-y-2 max-h-60 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                    @foreach($attribute->values as $value)
                        <label class="flex items-center cursor-pointer group py-1.5 hover:bg-gray-50 rounded-lg px-2 -mx-2 transition-colors">
                            @if($attribute->type === 'color')
                                <input type="checkbox" 
                                       name="attr_{{ $attribute->id }}[]" 
                                       value="{{ $value->id }}"
                                       @change="updateFilters"
                                       {{ in_array($value->id, $selected['attr_'.$attribute->id] ?? []) ? 'checked' : '' }}
                                       class="hidden peer">
                                <span class="w-6 h-6 rounded-full border-2 flex items-center justify-center mr-3 transition-all peer-checked:border-blue-600 peer-checked:scale-110 shadow-sm"
                                      style="background-color: {{ $value->color_code ?? '#000000' }}; border-color: {{ $value->color_code ?? '#000000' }};">
                                    <svg class="w-3 h-3 text-white hidden peer-checked:block drop-shadow-md" 
                                         :class="{ 'block': {{ in_array($value->id, $selected['attr_'.$attribute->id] ?? []) ? 'true' : 'false' }} }"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </span>
                                <span class="text-sm text-gray-700 group-hover:text-gray-900">{{ $value->value }}</span>
                            @else
                                <input type="checkbox" 
                                       name="attr_{{ $attribute->id }}[]" 
                                       value="{{ $value->id }}"
                                       @change="updateFilters"
                                       {{ in_array($value->id, $selected['attr_'.$attribute->id] ?? []) ? 'checked' : '' }}
                                       class="hidden peer">
                                <span class="w-4 h-4 border-2 border-gray-300 rounded peer-checked:bg-blue-600 peer-checked:border-blue-600 flex items-center justify-center mr-3 group-hover:border-blue-400 transition-all flex-shrink-0">
                                    <svg class="w-3 h-3 text-white hidden peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </span>
                                <span class="text-sm text-gray-700 group-hover:text-gray-900">{{ $value->value }}</span>
                            @endif
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif

    {{-- Improved Action Buttons --}}
    <div class="flex flex-col gap-2 pt-4 border-t border-gray-100">
        <button @click="applyFilters" 
                class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 hover:from-blue-700 hover:to-blue-800 transition-all duration-200 flex items-center justify-center gap-2 text-sm font-medium shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Apply Filters
        </button>
        <button @click="clearFilters" 
                class="w-full border-2 border-gray-200 text-gray-600 py-2.5 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 flex items-center justify-center gap-2 text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Clear All
        </button>
    </div>
</div>

<style>
    /* Custom scrollbar for filter sections */
    .scrollbar-thin::-webkit-scrollbar {
        width: 4px;
    }
    
    .scrollbar-thin::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 10px;
    }
    
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Custom range slider styling */
    input[type=range] {
        -webkit-appearance: none;
        height: 8px;
        background: transparent;
    }
    
    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 22px;
        height: 22px;
        background: white;
        border: 2.5px solid #2563eb;
        border-radius: 50%;
        cursor: pointer;
        pointer-events: auto;
        margin-top: -7px;
        box-shadow: 0 4px 8px rgba(37, 99, 235, 0.3);
        transition: all 0.2s;
    }
    
    input[type=range]::-webkit-slider-thumb:hover {
        transform: scale(1.2);
        background: #2563eb;
        box-shadow: 0 6px 12px rgba(37, 99, 235, 0.4);
    }
    
    input[type=range]::-moz-range-thumb {
        width: 22px;
        height: 22px;
        background: white;
        border: 2.5px solid #2563eb;
        border-radius: 50%;
        cursor: pointer;
        pointer-events: auto;
        box-shadow: 0 4px 8px rgba(37, 99, 235, 0.3);
        transition: all 0.2s;
    }
    
    input[type=range]::-moz-range-thumb:hover {
        transform: scale(1.2);
        background: #2563eb;
        box-shadow: 0 6px 12px rgba(37, 99, 235, 0.4);
    }
    
    /* Hide default range track */
    input[type=range]::-webkit-slider-runnable-track {
        width: 100%;
        height: 8px;
        background: transparent;
    }
    
    input[type=range]::-moz-range-track {
        width: 100%;
        height: 8px;
        background: transparent;
    }
    
    [x-cloak] { display: none !important; }
</style>

@push('scripts')
<script>
function filters() {
    return {
        openSections: {
            categories: true,
            price: true,
            brands: true,
        },
        minPrice: {{ $selectedMinPrice }},
        maxPrice: {{ $selectedMaxPrice }},
        minPossible: {{ $minPossible }},
        maxPossible: {{ $maxPossible }},
        
        initFilters() {
            // Initialize attribute sections
            @if($attributes->isNotEmpty())
                @foreach($attributes as $attribute)
                    this.openSections['attr_{{ $attribute->id }}'] = true;
                @endforeach
            @endif
            
            // Watch for price changes
            this.$watch('minPrice', value => {
                if (parseFloat(value) > parseFloat(this.maxPrice)) {
                    this.maxPrice = value;
                }
            });
            
            this.$watch('maxPrice', value => {
                if (parseFloat(value) < parseFloat(this.minPrice)) {
                    this.minPrice = value;
                }
            });
        },
        
        toggleSection(section) {
            if (this.openSections.hasOwnProperty(section)) {
                this.openSections[section] = !this.openSections[section];
            }
        },
        
        setPriceRange(min, max) {
            this.minPrice = min;
            this.maxPrice = max;
            this.applyFilters();
        },
        
        updateRange() {
            // Update in real-time but don't apply yet
        },
        
        updateFilters() {
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => {
                this.applyFilters();
            }, 500);
        },
        
        applyFilters() {
            const params = new URLSearchParams(window.location.search);
            
            // Clear existing filter parameters
            const filterParams = ['categories[]', 'brands[]', 'min_price', 'max_price'];
            filterParams.forEach(param => params.delete(param));
            
            // Clear any existing attribute params
            document.querySelectorAll('[name^="attr_"]').forEach(input => {
                if (input.name) {
                    params.delete(input.name);
                }
            });
            
            // Add price filters
            if (this.minPrice > this.minPossible || this.maxPrice < this.maxPossible) {
                params.set('min_price', this.minPrice);
                params.set('max_price', this.maxPrice);
            }
            
            // Add category filters
            document.querySelectorAll('input[name="categories[]"]:checked').forEach(cb => {
                params.append('categories[]', cb.value);
            });
            
            // Add brand filters
            document.querySelectorAll('input[name="brands[]"]:checked').forEach(cb => {
                params.append('brands[]', cb.value);
            });
            
            // Add attribute filters
            @if($attributes->isNotEmpty())
                @foreach($attributes as $attribute)
                    document.querySelectorAll('input[name="attr_{{ $attribute->id }}[]"]:checked').forEach(cb => {
                        params.append('attr_{{ $attribute->id }}[]', cb.value);
                    });
                @endforeach
            @endif
            
            // Preserve search query if exists
            const currentUrl = new URL(window.location.href);
            if (currentUrl.searchParams.has('q')) {
                params.set('q', currentUrl.searchParams.get('q'));
            }
            
            // Build URL and redirect
            const queryString = params.toString();
            const baseUrl = window.location.pathname;
            window.location.href = queryString ? baseUrl + '?' + queryString : baseUrl;
        },
        
        clearFilters() {
            window.location.href = window.location.pathname;
        }
    }
}
</script>
@endpush