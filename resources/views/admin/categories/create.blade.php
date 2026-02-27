@extends('layouts.admin')

@section('title', 'Create Category')

@section('content')
<div class="">
    {{-- Header --}}
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create New Category</h1>
            <!-- <p class="mt-2 text-sm text-gray-600">
                Add a new category to organize your products
            </p> -->
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.categories.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Categories
            </a>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Category Name --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1 required">
                            Category Name
                        </label>
                        <input type="text" 
                               name="name" 
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-500 @enderror" 
                               value="{{ old('name') }}" 
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Parent Category --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Parent Category
                        </label>
                        <select name="parent_id" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('parent_id') border-red-500 @enderror">
                            <option value="">None (Top Level)</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('parent_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Description
                        </label>
                        <textarea name="description" 
                                  rows="3" 
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Icon --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Icon (Font Awesome Class)
                        </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-5-5A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </span>
                            <input type="text" 
                                   name="icon" 
                                   class="block w-full rounded-none rounded-r-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('icon') border-red-500 @enderror" 
                                   value="{{ old('icon') }}" 
                                   placeholder="fas fa-mobile-alt">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Enter Font Awesome icon class (e.g., fas fa-mobile-alt)</p>
                        @error('icon')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Category Image --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Category Image
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-500 transition-colors duration-150">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="image-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                                        <span>Upload a image</span>
                                        <input id="image-upload" name="image" type="file" class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB (200x200px recommended)</p>
                            </div>
                        </div>
                        <div id="image-preview" class="mt-2 hidden">
                            <img src="" alt="Preview" class="h-20 w-20 object-cover rounded-lg">
                        </div>
                        @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Sort Order --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Sort Order
                        </label>
                        <input type="number" 
                               name="sort_order" 
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('sort_order') border-red-500 @enderror" 
                               value="{{ old('sort_order', 0) }}" 
                               min="0">
                        @error('sort_order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
<div class="col-span-2 md:col-span-1">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Status
    </label>
    <div class="mt-2">
        <label class="inline-flex items-center">
            <input type="checkbox" 
                   name="status" 
                   value="1" 
                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                   {{ old('status', true) ? 'checked' : '' }}>
            <span class="ml-2 text-sm text-gray-600">Active</span>
        </label>
    </div>
</div>

{{-- Featured Section --}}
<div class="col-span-2">
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Is Featured --}}
            <div class="col-span-2 md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Featured Category
                </label>
                <div class="mt-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" 
                               name="is_featured" 
                               value="1" 
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 featured-checkbox"
                               {{ old('is_featured') ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-600">Show in featured section</span>
                    </label>
                </div>
            </div>

            {{-- Featured Order --}}
            <div class="col-span-2 md:col-span-1 featured-order-field" style="{{ old('is_featured') ? '' : 'display: none;' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Featured Order
                </label>
                <input type="number" 
                       name="featured_order" 
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('featured_order') border-red-500 @enderror" 
                       value="{{ old('featured_order', 0) }}" 
                       min="0">
                <p class="mt-1 text-xs text-gray-500">Lower numbers appear first</p>
                @error('featured_order')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Show in Menu --}}
            <div class="col-span-2 md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Menu Display
                </label>
                <div class="mt-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" 
                               name="show_in_menu" 
                               value="1" 
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               {{ old('show_in_menu', true) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-600">Show in navigation menu</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

                    {{-- SEO Section Separator --}}
                    <div class="col-span-2">
                        <div class="relative py-4">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-start">
                                <span class="pr-3 bg-white text-lg font-medium text-gray-900">SEO Information</span>
                            </div>
                        </div>
                    </div>

                    {{-- Meta Title --}}
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Meta Title
                        </label>
                        <input type="text" 
                               name="meta_title" 
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('meta_title') border-red-500 @enderror" 
                               value="{{ old('meta_title') }}">
                        @error('meta_title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Meta Description --}}
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Meta Description
                        </label>
                        <textarea name="meta_description" 
                                  rows="2" 
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('meta_description') border-red-500 @enderror">{{ old('meta_description') }}</textarea>
                        @error('meta_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Meta Keywords --}}
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Meta Keywords
                        </label>
                        <input type="text" 
                               name="meta_keywords" 
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('meta_keywords') border-red-500 @enderror" 
                               value="{{ old('meta_keywords') }}" 
                               placeholder="Comma separated">
                        @error('meta_keywords')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="mt-6 flex items-center justify-end space-x-3 border-t pt-6">
                    <a href="{{ route('admin.categories.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview functionality
    const imageInput = document.querySelector('input[name="image"]');
    const previewContainer = document.getElementById('image-preview');
    const previewImage = previewContainer?.querySelector('img');
    const featuredCheckbox = document.querySelector('.featured-checkbox');
    const featuredOrderField = document.querySelector('.featured-order-field');

    if (featuredCheckbox && featuredOrderField) {
        featuredCheckbox.addEventListener('change', function() {
            if (this.checked) {
                featuredOrderField.style.display = 'block';
            } else {
                featuredOrderField.style.display = 'none';
                document.querySelector('input[name="featured_order"]').value = 0;
            }
        });
    }

    if (imageInput && previewContainer && previewImage) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const maxSize = 2 * 1024 * 1024; // 2MB
                if (file.size > maxSize) {
                    alert('File size exceeds 2MB. Please choose a smaller size.');
                    this.value = '';
                    previewContainer.classList.add('hidden');
                    previewImage.src = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            } else {
                previewContainer.classList.add('hidden');
                previewImage.src = '';
            }
        });
    }

    // Optional: Add live icon preview
    const iconInput = document.querySelector('input[name="icon"]');
    if (iconInput) {
        iconInput.addEventListener('input', function() {
            // You can add live icon preview here if needed
            // This would require Font Awesome to be loaded
        });
    }

    // Optional: Add character counter for meta fields
    const metaTitle = document.querySelector('input[name="meta_title"]');
    const metaDescription = document.querySelector('textarea[name="meta_description"]');

    if (metaTitle) {
        metaTitle.addEventListener('input', function() {
            // Meta title length warning (optional)
            if (this.value.length > 60) {
                this.classList.add('border-yellow-500');
            } else {
                this.classList.remove('border-yellow-500');
            }
        });
    }

    if (metaDescription) {
        metaDescription.addEventListener('input', function() {
            // Meta description length warning (optional)
            if (this.value.length > 160) {
                this.classList.add('border-yellow-500');
            } else {
                this.classList.remove('border-yellow-500');
            }
        });
    }
});
</script>
@endpush

@push('styles')
<style>
/* Custom styles for required field indicator */
.required:after {
    content: " *";
    color: #dc2626;
    font-weight: bold;
}

/* Optional: Add custom styles for drag and drop area */
.border-dashed:hover {
    border-color: #6366f1;
    background-color: #f9fafb;
}

/* Optional: Style for file input label */
label[for="image-upload"] {
    transition: color 0.15s ease-in-out;
}
</style>
@endpush
@endsection