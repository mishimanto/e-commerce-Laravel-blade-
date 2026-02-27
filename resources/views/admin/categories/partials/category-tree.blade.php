{{-- resources/views/admin/categories/partials/category-tree.blade.php --}}

<div class="mb-4 last:mb-0 category-item" data-category-id="{{ $category->id }}" style="margin-left: {{ $level * 20 }}px;">
    <div class="bg-white border rounded-lg border-gray-200 hover:shadow-md transition-shadow duration-200">
        {{-- Category Header --}}
        <div class="px-6 py-4 flex items-center justify-between">
            <div class="flex items-center flex-1">
                {{-- Expand/Collapse Button for categories with children --}}
                @if($category->children->where('status', 1)->count() > 0)
                    <button type="button" 
                            onclick="toggleCategory({{ $category->id }})" 
                            class="mr-3 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors duration-150"
                            id="toggle-{{ $category->id }}">
                        <svg class="w-5 h-5 transform transition-transform duration-200 {{ session('expanded_' . $category->id, true) ? 'rotate-90' : '' }}" 
                            id="icon-{{ $category->id }}"
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                @else
                    <div class="w-8 mr-3"></div>
                @endif

                {{-- Category Icon/Image --}}
                <div class="flex-shrink-0 mr-4">
                    @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" 
                             alt="{{ $category->name }}"
                             class="w-10 h-10 object-cover rounded-lg">
                    @elseif($category->icon)
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="{{ $category->icon }} text-indigo-600 text-xl"></i>
                        </div>
                    @else
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Category Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center flex-wrap gap-2">
                        <h3 class="text-base font-medium text-gray-900 truncate">
                            {{ $category->name }}
                        </h3>

                        @if($category->is_featured)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                Featured ({{ $category->featured_order }})
                            </span>
                        @endif

                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $category->products_count }} Products
                        </span>

                        @if(!$category->show_in_menu)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                Hidden from Menu
                            </span>
                        @endif
                    </div>
                    @if($category->description)
                        <p class="mt-1 text-sm text-gray-500 truncate max-w-2xl">
                            {{ $category->description }}
                        </p>
                    @endif
                </div>

                {{-- Sort Order Badge --}}
                <div class="mx-4 flex items-center space-x-1">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <span class="text-sm text-gray-600">#{{ $category->sort_order }}</span>
                </div>

                {{-- Actions --}}
                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.categories.edit', $category) }}" 
                       class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors duration-150"
                       title="Edit Category">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </a>

                    <button type="button" 
                            onclick="moveUp({{ $category->id }})"
                            class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors duration-150"
                            title="Move Up">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                        </svg>
                    </button>

                    <button type="button" 
                            onclick="moveDown({{ $category->id }})"
                            class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors duration-150"
                            title="Move Down">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <form action="{{ route('admin.categories.destroy', $category) }}" 
                          method="POST" class="inline-block"
                          onsubmit="return confirm('Are you sure you want to delete this category? All subcategories will also be deleted.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-150"
                                title="Delete Category">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Children Categories Container --}}
        @if($category->children->where('status', 1)->count() > 0)
            <div id="children-{{ $category->id }}" class="border-t border-gray-100 {{ session('expanded_' . $category->id, true) ? '' : 'hidden' }}">
                <div class="py-2">
                    @foreach($category->children->where('status', 1)->sortBy('sort_order') as $child)
                        @include('admin.categories.partials.category-tree', ['category' => $child, 'level' => $level + 1])
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>