{{-- resources/views/admin/attributes/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Create Attribute')

@section('content')
<div class="">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-500">Create New Attribute</h1>
            <!-- <p class="text-gray-600 mt-1">Add a new product attribute for variations and filtering</p> -->
        </div>
        <a href="{{ route('admin.attributes.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Attributes
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('admin.attributes.store') }}" 
              method="POST" 
              class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Left Column --}}
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1 required">Attribute Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1 required">Input Type</label>
                        <select name="type" id="type" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                            <option value="">Select Type</option>
                            <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>Text</option>
                            <option value="select" {{ old('type') == 'select' ? 'selected' : '' }}>Select Dropdown</option>
                            <option value="radio" {{ old('type') == 'radio' ? 'selected' : '' }}>Radio Buttons</option>
                            <option value="checkbox" {{ old('type') == 'checkbox' ? 'selected' : '' }}>Checkboxes</option>
                            <option value="color" {{ old('type') == 'color' ? 'selected' : '' }}>Color Picker</option>
                            <option value="size" {{ old('type') == 'size' ? 'selected' : '' }}>Size Buttons</option>
                            <option value="number" {{ old('type') == 'number' ? 'selected' : '' }}>Number</option>
                        </select>
                        @error('type')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               min="0">
                        <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
                        @error('sort_order')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Right Column --}}
                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Settings</h3>
                        
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_required" value="1" 
                                       {{ old('is_required') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">Required Field</span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" name="is_filterable" value="1" 
                                       {{ old('is_filterable', true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">Show in product filters</span>
                            </label>
                        </div>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-blue-800 mb-2">About Attribute Types</h4>
                        <ul class="text-xs text-blue-700 space-y-1">
                            <li><strong>Text:</strong> Single line text input</li>
                            <li><strong>Select Dropdown:</strong> Dropdown menu for selecting one value</li>
                            <li><strong>Radio Buttons:</strong> Radio buttons for selecting one value</li>
                            <li><strong>Checkboxes:</strong> Multiple checkboxes for selecting multiple values</li>
                            <li><strong>Color Picker:</strong> Color swatches for visual selection</li>
                            <li><strong>Size Buttons:</strong> Button-style size selection</li>
                            <li><strong>Number:</strong> Numeric input field</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.attributes.index') }}" 
                   class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Create Attribute
                </button>
            </div>
        </form>
    </div>
</div>
@endsection