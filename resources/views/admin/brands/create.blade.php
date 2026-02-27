@extends('layouts.admin')

@section('title', 'Create Brand')

@section('content')
<div class="">
    {{-- Header --}}
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create New Brand</h1>
            <!-- <p class="mt-2 text-sm text-gray-600">
                Add a new brand to your product catalog
            </p> -->
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.brands.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Brands
            </a>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Brand Name --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1 required">
                            Brand Name
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

                    {{-- Website --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Website
                        </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <!-- <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"></path>
                                </svg>
                            </span> -->
                            <input type="url" 
                                   name="website" 
                                   class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('website') border-red-500 @enderror" 
                                   value="{{ old('website') }}" 
                                   placeholder="https://example.com">
                        </div>
                        @error('website')
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

                    {{-- Brand Logo --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Brand Logo
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-500 transition-colors duration-150">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="logo-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                                        <span>Upload a logo</span>
                                        <input id="logo-upload" name="logo" type="file" class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB (200x200px recommended)</p>
                            </div>
                        </div>
                        <div id="logo-preview" class="mt-2 hidden">
                            <img src="" alt="Logo Preview" class="h-20 w-20 object-contain rounded-lg border border-gray-200">
                        </div>
                        @error('logo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <div class="grid grid-cols-2 gap-4">
                            {{-- Sort Order --}}
                            <div>
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
                            <div>
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
                    <a href="{{ route('admin.brands.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create Brand
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Logo preview functionality
    const logoInput = document.querySelector('input[name="logo"]');
    const previewContainer = document.getElementById('logo-preview');
    const previewImage = previewContainer?.querySelector('img');

    if (logoInput && previewContainer && previewImage) {
        logoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
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

    // Meta title character counter
    const metaTitle = document.querySelector('input[name="meta_title"]');
    if (metaTitle) {
        const titleCounter = document.createElement('p');
        titleCounter.className = 'mt-1 text-xs text-gray-500';
        metaTitle.parentNode.appendChild(titleCounter);
        
        const updateTitleCounter = () => {
            const length = metaTitle.value.length;
            titleCounter.textContent = `${length} characters (recommended: 50-60)`;
            if (length > 60) {
                titleCounter.classList.add('text-yellow-600');
            } else {
                titleCounter.classList.remove('text-yellow-600');
            }
        };
        
        metaTitle.addEventListener('input', updateTitleCounter);
        updateTitleCounter();
    }
});
</script>
@endpush

@push('styles')
<style>
.required:after {
    content: " *";
    color: #dc2626;
    font-weight: bold;
}

.border-dashed:hover {
    border-color: #6366f1;
    background-color: #f9fafb;
}
</style>
@endpush
@endsection