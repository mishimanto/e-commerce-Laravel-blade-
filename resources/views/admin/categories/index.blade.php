@extends('layouts.admin')

@section('title', 'Category Management')

@section('content')
<div class="">
    {{-- Header with Stats --}}
    <div class="mb-8">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.categories.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New Category
                </a>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-indigo-100 rounded-lg p-3">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Categories</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $totalCategories }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-green-100 rounded-lg p-3">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Categories</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $activeCategories }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-red-100 rounded-lg p-3">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Inactive Categories</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $inactiveCategories }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-yellow-100 rounded-lg p-3">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Featured Categories</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $featuredCategoriesCount }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-purple-100 rounded-lg p-3">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Products</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $totalProducts }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Categories Tree View --}}
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">All Categories</h2>
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-gray-600">{{ $activeCategories }} active, {{ $inactiveCategories }} inactive</span>
                    <span class="text-gray-300">|</span>
                    <button type="button" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900" onclick="expandAll()">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                        Expand All
                    </button>
                    <span class="text-gray-300">|</span>
                    <button type="button" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900" onclick="collapseAll()">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                        Collapse All
                    </button>
                </div>
            </div>
        </div>

        <div class="p-6">
            @php
                // সব parent categories নিন, status যাই হোক
                $parentCategories = $categoryList->where('parent_id', null)->sortBy('sort_order');
            @endphp

            @forelse($parentCategories as $category)
                {{-- Main Category Card --}}
                <div class="mb-4 last:mb-0">
                    <div class="bg-white border rounded-lg hover:shadow-md transition-shadow duration-200 {{ !$category->status ? 'border-red-200 bg-red-50' : 'border-gray-200' }}">
                        {{-- Category Header --}}
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center flex-1">
                                {{-- Category Icon/Image --}}
                                <div class="flex-shrink-0 mr-4">
                                    @if($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}" 
                                             class="w-10 h-10 object-cover rounded-lg">
                                    @elseif($category->icon)
                                        <div class="w-10 h-10 {{ !$category->status ? 'bg-red-200' : 'bg-indigo-100' }} rounded-lg flex items-center justify-center">
                                            <i class="{{ $category->icon }} {{ !$category->status ? 'text-red-600' : 'text-indigo-600' }} text-xl"></i>
                                        </div>
                                    @else
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img 
                                                class="h-10 w-10 object-contain rounded-lg"
                                                src="{{ asset('storage/images/placeholder-photo.png') }}"
                                                alt="{{ $category->name }}" />
                                        </div>
                                    @endif
                                </div>

                                {{-- Category Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center flex-wrap gap-2">
                                        <h3 class="text-base font-medium {{ !$category->status ? 'text-gray-500' : 'text-gray-900' }} truncate">
                                            {{ $category->name }}
                                        </h3>
                                        
                                        @if(!$category->status)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                Inactive
                                            </span>
                                        @endif

                                        @if($category->is_featured)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Featured
                                            </span>
                                        @endif

                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $category->products_count }} Products
                                        </span>

                                        @if(!$category->show_in_menu)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                Hidden
                                            </span>
                                        @endif

                                        {{-- Expand/Collapse Button for categories with children --}}
                                        @if($category->children->count() > 0)
                                            <button type="button" 
                                                    onclick="toggleCategory({{ $category->id }})" 
                                                    class="ml-2 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors duration-150"
                                                    id="toggle-{{ $category->id }}">
                                                <svg class="w-5 h-5 transform transition-transform duration-200" 
                                                    id="icon-{{ $category->id }}"
                                                    fill="none" 
                                                    stroke="currentColor" 
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
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
                                          method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-150"
                                                title="Delete Category"
                                                onclick="return confirm('Are you sure? This will affect all subcategories and products.')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Subcategories Container --}}
                        @if($category->children->count() > 0)
                            <div id="children-{{ $category->id }}" class="border-t border-gray-100">
                                <div class="py-2">
                                    @foreach($category->children->sortBy('sort_order') as $child)
                                        <div class="px-6 py-3 hover:bg-gray-50 transition-colors duration-150 {{ !$child->status ? 'bg-red-50' : '' }}">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center flex-1">
                                                    {{-- Indentation Line with Branch --}}
                                                    <div class="relative flex items-center ml-6 mr-4">
                                                        <div class="w-6 h-px bg-gray-300"></div>
                                                        <div class="absolute -left-1 w-2 h-2 rounded-full bg-gray-300"></div>
                                                    </div>

                                                    {{-- Subcategory Icon/Image --}}
                                                    <div class="flex-shrink-0 mr-4">
                                                        @if($child->image)
                                                            <img src="{{ asset('storage/' . $child->image) }}" 
                                                                 alt="{{ $child->name }}"
                                                                 class="w-8 h-8 object-cover rounded-lg">
                                                        @elseif($child->icon)
                                                            <div class="w-8 h-8 {{ !$child->status ? 'bg-red-100' : 'bg-indigo-50' }} rounded-lg flex items-center justify-center">
                                                                <i class="{{ $child->icon }} {{ !$child->status ? 'text-red-500' : 'text-indigo-500' }} text-sm"></i>
                                                            </div>
                                                        @else
                                                            <div class="flex-shrink-0 h-8 w-8">
                                                                <img 
                                                                    class="h-8 w-8 object-contain rounded-lg"
                                                                    src="{{ asset('storage/images/placeholder-photo.png') }}"
                                                                    alt="{{ $child->name }}" />
                                                            </div>
                                                        @endif
                                                    </div>

                                                    {{-- Subcategory Info --}}
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center flex-wrap gap-2">
                                                            <h4 class="text-sm font-medium {{ !$child->status ? 'text-gray-500' : 'text-gray-900' }}">
                                                                {{ $child->name }}
                                                            </h4>
                                                            
                                                            @if(!$child->status)
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                                    Inactive
                                                                </span>
                                                            @endif

                                                            @if($child->is_featured)
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                    Featured
                                                                </span>
                                                            @endif

                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                {{ $child->products_count }}
                                                            </span>

                                                            @if(!$child->show_in_menu)
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                                    Hidden
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    {{-- Sort Order Badge --}}
                                                    <div class="mx-4 flex items-center space-x-1">
                                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                        </svg>
                                                        <span class="text-xs text-gray-500">#{{ $child->sort_order }}</span>
                                                    </div>

                                                    {{-- Subcategory Actions --}}
                                                    <div class="flex items-center space-x-1">
                                                        <a href="{{ route('admin.categories.edit', $child) }}" 
                                                           class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors duration-150"
                                                           title="Edit Subcategory">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                        </a>

                                                        <form action="{{ route('admin.categories.destroy', $child) }}" 
                                                              method="POST" class="inline-block">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-150"
                                                                    title="Delete Subcategory"
                                                                    onclick="return confirm('Are you sure? This will affect all products in this category.')">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No categories</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new category.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.categories.create') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add New Category
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
// Toggle subcategories visibility
function toggleCategory(categoryId) {
    const childrenContainer = document.getElementById(`children-${categoryId}`);
    const icon = document.getElementById(`icon-${categoryId}`);
    
    if (childrenContainer) {
        if (childrenContainer.style.display === 'none' || !childrenContainer.style.display) {
            childrenContainer.style.display = 'block';
            icon.classList.remove('-rotate-90');
        } else {
            childrenContainer.style.display = 'none';
            icon.classList.add('-rotate-90');
        }
    }
}

// Expand all categories
function expandAll() {
    document.querySelectorAll('[id^="children-"]').forEach(container => {
        container.style.display = 'block';
    });
    document.querySelectorAll('[id^="icon-"]').forEach(icon => {
        icon.classList.remove('-rotate-90');
    });
}

// Collapse all categories
function collapseAll() {
    document.querySelectorAll('[id^="children-"]').forEach(container => {
        container.style.display = 'none';
    });
    document.querySelectorAll('[id^="icon-"]').forEach(icon => {
        icon.classList.add('-rotate-90');
    });
}

// Move category up
function moveUp(categoryId) {
    const button = event.currentTarget;
    const originalHtml = button.innerHTML;
    
    button.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>';
    button.disabled = true;
    
    fetch(`/admin/categories/${categoryId}/move-up`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message || 'Category moved up successfully');
            setTimeout(() => window.location.reload(), 500);
        } else {
            throw new Error(data.message || 'Failed to move category up');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', error.message || 'Failed to move category. Please try again.');
        button.innerHTML = originalHtml;
        button.disabled = false;
    });
}

// Move category down
function moveDown(categoryId) {
    const button = event.currentTarget;
    const originalHtml = button.innerHTML;
    
    button.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>';
    button.disabled = true;
    
    fetch(`/admin/categories/${categoryId}/move-down`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message || 'Category moved down successfully');
            setTimeout(() => window.location.reload(), 500);
        } else {
            throw new Error(data.message || 'Failed to move category down');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', error.message || 'Failed to move category. Please try again.');
        button.innerHTML = originalHtml;
        button.disabled = false;
    });
}

// Show notification
function showNotification(type, message) {
    if (typeof toastr !== 'undefined') {
        if (type === 'success') {
            toastr.success(message);
        } else {
            toastr.error(message);
        }
    } else {
        alert(message);
    }
}

// Initialize all subcategories as expanded by default
document.addEventListener('DOMContentLoaded', function() {
    expandAll();
});
</script>
@endpush

@push('styles')
<style>
/* Rotate animation */
.-rotate-90 {
    transform: rotate(-90deg);
    transition: transform 0.3s ease-in-out;
}

.rotate-90 {
    transform: rotate(90deg);
    transition: transform 0.3s ease-in-out;
}

/* Transition for expand/collapse */
[id^="children-"] {
    transition: all 0.3s ease-in-out;
}

/* Inactive category styling */
.bg-red-50 {
    background-color: #fef2f2;
}

.border-red-200 {
    border-color: #fecaca;
}

.text-red-800 {
    color: #991b1b;
}

.bg-red-100 {
    background-color: #fee2e2;
}

.text-red-600 {
    color: #dc2626;
}

.text-red-500 {
    color: #ef4444;
}

/* Hover effects */
.hover\:bg-gray-50:hover {
    background-color: #f9fafb;
}

.hover\:bg-indigo-50:hover {
    background-color: #eef2ff;
}

.hover\:bg-red-50:hover {
    background-color: #fef2f2;
}

/* Button focus states */
button:focus {
    outline: none;
    ring: 2px solid #6366f1;
    ring-offset: 2px;
}

/* Animation for loading spinner */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
@endpush
@endsection