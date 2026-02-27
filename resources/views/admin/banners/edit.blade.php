@extends('layouts.admin')

@section('title', 'Edit Banner')

@section('content')
<div class="">
    {{-- Header with buttons --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Banner: {{ $banner->title }}</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.banners.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                <span class="hidden sm:inline">Back to List</span>
            </a>
            <button type="submit" form="banner-form" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="hidden sm:inline">Update Banner</span>
            </button>
        </div>
    </div>

    <form id="banner-form" action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            {{-- Left Column (Main Content) --}}
            <div class="xl:col-span-2 space-y-6">
                {{-- Basic Information --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <h5 class="font-medium text-gray-800">Basic Information</h5>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1 required">Banner Title</label>
                                <input type="text" name="title" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror" 
                                       value="{{ old('title', $banner->title) }}" required>
                                @error('title')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
                                <input type="text" name="subtitle" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('subtitle') border-red-500 @enderror" 
                                       value="{{ old('subtitle', $banner->subtitle) }}">
                                @error('subtitle')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Button Text</label>
                                <input type="text" name="button_text" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('button_text') border-red-500 @enderror" 
                                       value="{{ old('button_text', $banner->button_text) }}" placeholder="Shop Now">
                                @error('button_text')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="description" rows="3" 
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $banner->description) }}</textarea>
                                @error('description')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Link URL</label>
                                <input type="url" name="link" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('link') border-red-500 @enderror" 
                                       value="{{ old('link', $banner->link) }}" placeholder="https://example.com/products">
                                @error('link')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Settings --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                            </svg>
                            <h5 class="font-medium text-gray-800">Banner Settings</h5>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 required">Position</label>
                                <select name="position" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('position') border-red-500 @enderror" required>
                                    <option value="">Select Position</option>
                                    @foreach($positions as $key => $label)
                                        <option value="{{ $key }}" {{ old('position', $banner->position) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('position')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 required">Banner Type</label>
                                <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('type') border-red-500 @enderror" required>
                                    <option value="">Select Type</option>
                                    @foreach($types as $key => $label)
                                        <option value="{{ $key }}" {{ old('type', $banner->type) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 required">Link Target</label>
                                <select name="target" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('target') border-red-500 @enderror" required>
                                    <option value="">Select Target</option>
                                    @foreach($targets as $key => $label)
                                        <option value="{{ $key }}" {{ old('target', $banner->target) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('target')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                                <input type="number" name="priority" min="0" max="999" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('priority') border-red-500 @enderror" 
                                       value="{{ old('priority', $banner->priority) }}">
                                <p class="text-xs text-gray-500 mt-1">Higher priority = higher display order</p>
                                @error('priority')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Scheduling --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                            <h5 class="font-medium text-gray-800">Schedule (Optional)</h5>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                <input type="datetime-local" name="start_date" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('start_date') border-red-500 @enderror" 
                                       value="{{ old('start_date', $banner->start_date ? $banner->start_date->format('Y-m-d\TH:i') : '') }}">
                                @error('start_date')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                <input type="datetime-local" name="end_date" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('end_date') border-red-500 @enderror" 
                                       value="{{ old('end_date', $banner->end_date ? $banner->end_date->format('Y-m-d\TH:i') : '') }}">
                                @error('end_date')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Leave both empty for no time restriction</p>
                    </div>
                </div>
            </div>

            {{-- Right Column (Images & Status) --}}
            <div class="space-y-6">
                {{-- Banner Images --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                            </svg>
                            <h5 class="font-medium text-gray-800">Banner Images</h5>
                        </div>
                    </div>
                    <div class="p-4">
                        {{-- Desktop Image --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Desktop Image</label>
                            
                            {{-- Current Image --}}
                            @if($banner->image_full_url)
                                <div class="mb-3 p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs font-medium text-gray-500 mb-2">Current Image</p>
                                    <img src="{{ $banner->image_full_url }}" alt="Current banner" 
                                         class="w-full h-32 object-cover rounded-lg border border-gray-200">
                                </div>
                            @endif

                            {{-- Upload New --}}
                            <div class="flex items-center justify-center w-full">
                                <label for="image-upload" class="w-full flex flex-col items-center px-4 py-6 bg-white text-blue-500 rounded-lg border-2 border-dashed border-gray-300 cursor-pointer hover:border-blue-500 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="mt-2 text-sm text-gray-500">Click to upload new desktop banner</span>
                                    <span class="text-xs text-gray-400">PNG, JPG, WebP up to 5MB (1920x1080 recommended)</span>
                                    <input id="image-upload" type="file" name="image" accept="image/*" class="hidden">
                                </label>
                            </div>
                            @error('image')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                            
                            {{-- Preview --}}
                            <div id="image-preview" class="mt-3 hidden">
                                <p class="text-xs font-medium text-gray-500 mb-2">New Image Preview</p>
                                <img src="" alt="Preview" class="w-full h-40 object-cover rounded-lg border border-gray-200">
                            </div>
                        </div>

                        {{-- Mobile Image --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Image</label>
                            
                            {{-- Current Mobile Image --}}
                            @if($banner->mobile_image_full_url && $banner->mobile_image_full_url != $banner->image_full_url)
                                <div class="mb-3 p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs font-medium text-gray-500 mb-2">Current Mobile Image</p>
                                    <img src="{{ $banner->mobile_image_full_url }}" alt="Current mobile banner" 
                                         class="w-full h-32 object-cover rounded-lg border border-gray-200">
                                </div>
                            @endif

                            {{-- Upload New --}}
                            <div class="flex items-center justify-center w-full">
                                <label for="mobile-image-upload" class="w-full flex flex-col items-center px-4 py-6 bg-white text-blue-500 rounded-lg border-2 border-dashed border-gray-300 cursor-pointer hover:border-blue-500 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="mt-2 text-sm text-gray-500">Click to upload new mobile banner</span>
                                    <span class="text-xs text-gray-400">PNG, JPG, WebP up to 5MB (750x1334 recommended)</span>
                                    <input id="mobile-image-upload" type="file" name="mobile_image" accept="image/*" class="hidden">
                                </label>
                            </div>
                            @error('mobile_image')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                            
                            {{-- Mobile Preview --}}
                            <div id="mobile-image-preview" class="mt-3 hidden">
                                <p class="text-xs font-medium text-gray-500 mb-2">New Mobile Preview</p>
                                <img src="" alt="Preview" class="w-full h-40 object-cover rounded-lg border border-gray-200">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                            </svg>
                            <h5 class="font-medium text-gray-800">Status</h5>
                        </div>
                    </div>
                    <div class="p-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-700">Active</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-2">Inactive banners won't be displayed on the site</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Desktop image preview
    const imageInput = document.getElementById('image-upload');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = imagePreview?.querySelector('img');

    if (imageInput && imagePreview && previewImg) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            } else {
                imagePreview.classList.add('hidden');
            }
        });
    }

    // Mobile image preview
    const mobileImageInput = document.getElementById('mobile-image-upload');
    const mobileImagePreview = document.getElementById('mobile-image-preview');
    const mobilePreviewImg = mobileImagePreview?.querySelector('img');

    if (mobileImageInput && mobileImagePreview && mobilePreviewImg) {
        mobileImageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    mobilePreviewImg.src = e.target.result;
                    mobileImagePreview.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            } else {
                mobileImagePreview.classList.add('hidden');
            }
        });
    }
});
</script>
@endpush

@push('styles')
<style>
.required:after {
    content: " *";
    color: #ef4444;
}
</style>
@endpush
@endsection