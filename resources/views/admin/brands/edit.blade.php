@extends('layouts.admin')

@section('title', 'Edit Brand')

@section('content')
<div class="">
    {{-- Header --}}
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Brand: {{ $brand->name }}</h1>
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
            <form action="{{ route('admin.brands.update', $brand) }}" method="POST" enctype="multipart/form-data" id="brand-form">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Brand Name --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1 required">
                            Brand Name
                        </label>
                        <input type="text" 
                               name="name" 
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-500 @enderror" 
                               value="{{ old('name', $brand->name) }}" 
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
                                   value="{{ old('website', $brand->website) }}" 
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
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('description') border-red-500 @enderror">{{ old('description', $brand->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Brand Logo --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Brand Logo
                        </label>
                        
                        {{-- Current Logo Preview with Remove Option --}}
                        @if($brand->logo)
                            <div class="mb-3 p-3 bg-gray-50 rounded-lg" id="current-logo-container">
                                <p class="text-xs font-medium text-gray-500 mb-2">Current Logo</p>
                                <div class="flex items-center space-x-3">
                                    <img src="{{ Storage::url($brand->logo) }}" 
                                         alt="{{ $brand->name }}" 
                                         class="h-16 w-16 object-contain rounded-lg border border-gray-200 bg-white p-1"
                                         id="current-logo-img"
                                         onerror="this.onerror=null; this.src='{{ asset('images/placeholder.png') }}';">
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-500">{{ basename($brand->logo) }}</p>
                                        <p class="text-xs text-gray-400 mt-1">Upload new logo to replace</p>
                                    </div>
                                    {{-- Remove Image Button --}}
                                    <button type="button" 
                                            onclick="removeImage()"
                                            class="p-1.5 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-150"
                                            title="Remove Logo">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            {{-- Hidden input to track image removal --}}
                            <input type="hidden" name="remove_logo" id="remove_logo" value="0">
                        @endif

                        {{-- New Logo Upload --}}
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-500 transition-colors duration-150" id="upload-container">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="logo-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                                        <span>Upload new logo</span>
                                        <input id="logo-upload" name="logo" type="file" class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB (200x200px recommended)</p>
                                <p class="text-xs text-gray-400 mt-1">Leave empty to keep current logo</p>
                            </div>
                        </div>
                        
                        {{-- New Logo Preview --}}
                        <div id="logo-preview" class="mt-3 hidden">
                            <p class="text-xs font-medium text-gray-500 mb-2">New Logo Preview</p>
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
                                       value="{{ old('sort_order', $brand->sort_order) }}" 
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
                                               {{ old('status', $brand->status) ? 'checked' : '' }}>
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
                               value="{{ old('meta_title', $brand->meta_title) }}">
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
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('meta_description') border-red-500 @enderror">{{ old('meta_description', $brand->meta_description) }}</textarea>
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
                               value="{{ old('meta_keywords', $brand->meta_keywords) }}" 
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Update Brand
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
    const uploadContainer = document.getElementById('upload-container');
    const removeLogoInput = document.getElementById('remove_logo');

    if (logoInput && previewContainer && previewImage) {
        logoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Check file size (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    this.value = '';
                    return;
                }
                
                // Check file type
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    alert('Please upload a valid image file (JPEG, PNG, GIF)');
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                    // Enable upload container
                    if (uploadContainer) {
                        uploadContainer.classList.remove('opacity-50');
                    }
                    
                    // Automatically mark for removal if there's a current logo
                    if (removeLogoInput && document.getElementById('current-logo-container')) {
                        removeLogoInput.value = '1';
                        console.log('Remove logo set to 1 (new upload)'); // Debug log
                        
                        // Optionally hide the current logo container
                        const currentContainer = document.getElementById('current-logo-container');
                        if (currentContainer) {
                            currentContainer.style.transition = 'opacity 0.3s ease';
                            currentContainer.style.opacity = '0.5';
                        }
                    }
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

    // Warn before leaving if form is dirty
    const form = document.getElementById('brand-form');
    let formDirty = false;
    
    form.addEventListener('input', function() {
        formDirty = true;
    });
    
    form.addEventListener('submit', function() {
        formDirty = false;
    });
    
    window.addEventListener('beforeunload', function(e) {
        if (formDirty) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        }
    });
});

// Function to remove current image
function removeImage() {
    if (confirm('Are you sure you want to remove this logo?')) {
        // Set the hidden input to 1 indicating we want to remove the image
        const removeLogoInput = document.getElementById('remove_logo');
        if (removeLogoInput) {
            removeLogoInput.value = '1';
            console.log('Remove logo set to:', removeLogoInput.value); // Debug log
        } else {
            console.error('remove_logo input not found');
        }
        
        // Find and remove the current logo container
        const currentLogoContainer = document.getElementById('current-logo-container');
        if (currentLogoContainer) {
            // Add a fade-out animation
            currentLogoContainer.style.transition = 'opacity 0.3s ease';
            currentLogoContainer.style.opacity = '0';
            
            // Remove after animation
            setTimeout(() => {
                currentLogoContainer.remove();
                console.log('Logo container removed'); // Debug log
                
                // Show success message after removal
                showNotification('success', 'Logo removed successfully. Don\'t forget to save your changes.');
            }, 300);
        } else {
            console.error('current-logo-container not found');
            showNotification('error', 'Could not find logo container');
        }
        
        // Enable the upload container
        const uploadContainer = document.getElementById('upload-container');
        if (uploadContainer) {
            uploadContainer.classList.remove('opacity-50');
        }
        
        // Clear any file input value
        const logoInput = document.querySelector('input[name="logo"]');
        if (logoInput) {
            logoInput.value = '';
        }
        
        // Hide preview container if visible
        const previewContainer = document.getElementById('logo-preview');
        if (previewContainer) {
            previewContainer.classList.add('hidden');
            const previewImage = previewContainer.querySelector('img');
            if (previewImage) {
                previewImage.src = '';
            }
        }
        
        // Mark form as dirty
        const form = document.getElementById('brand-form');
        if (form) {
            form.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }
}

// Helper function to show notifications
function showNotification(type, message) {
    // Remove any existing notifications
    const existingNotifications = document.querySelectorAll('.custom-notification');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create notification
    const notification = document.createElement('div');
    notification.className = 'custom-notification fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 flex items-center space-x-2 min-w-[300px]';
    
    if (type === 'success') {
        notification.classList.add('bg-green-500', 'text-white');
        notification.innerHTML = `
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="flex-1">${message}</span>
            <button onclick="this.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
    } else {
        notification.classList.add('bg-red-500', 'text-white');
        notification.innerHTML = `
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="flex-1">${message}</span>
            <button onclick="this.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
    }
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.transition = 'opacity 0.3s ease';
            notification.style.opacity = '0';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }
    }, 5000);
}
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

/* Animation for notifications */
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.custom-notification {
    animation: slideIn 0.3s ease-out;
}

/* Smooth transitions */
#current-logo-container {
    transition: opacity 0.3s ease;
}

/* Focus styles for better accessibility */
button:focus-visible {
    outline: 2px solid #6366f1;
    outline-offset: 2px;
}

/* Loading state for buttons */
button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
@endpush
@endsection