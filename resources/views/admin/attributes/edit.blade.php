{{-- resources/views/admin/attributes/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit Attribute - ' . $attribute->name)

@section('content')
<div class="">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-500">Edit Attribute</h1>
            <p class="text-gray-600 mt-1">{{ $attribute->name }}</p>
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
        <form action="{{ route('admin.attributes.update', $attribute) }}" 
              method="POST" 
              class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Left Column --}}
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1 required">Attribute Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $attribute->name) }}"
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
                            <option value="text" {{ old('type', $attribute->type) == 'text' ? 'selected' : '' }}>Text</option>
                            <option value="select" {{ old('type', $attribute->type) == 'select' ? 'selected' : '' }}>Select Dropdown</option>
                            <option value="radio" {{ old('type', $attribute->type) == 'radio' ? 'selected' : '' }}>Radio Buttons</option>
                            <option value="checkbox" {{ old('type', $attribute->type) == 'checkbox' ? 'selected' : '' }}>Checkboxes</option>
                            <option value="color" {{ old('type', $attribute->type) == 'color' ? 'selected' : '' }}>Color Picker</option>
                            <option value="size" {{ old('type', $attribute->type) == 'size' ? 'selected' : '' }}>Size Buttons</option>
                            <option value="number" {{ old('type', $attribute->type) == 'number' ? 'selected' : '' }}>Number</option>
                        </select>
                        @error('type')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $attribute->sort_order) }}"
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
                                       {{ old('is_required', $attribute->is_required) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">Required Field</span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" name="is_filterable" value="1" 
                                       {{ old('is_filterable', $attribute->is_filterable) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">Show in product filters</span>
                            </label>
                        </div>
                    </div>

                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <div class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-yellow-800">Manage Attribute Values</h4>
                                <p class="text-xs text-yellow-700 mt-1">
                                    After saving the attribute, you can add values from the 
                                    <a href="{{ route('admin.attributes.values.index', $attribute) }}" class="underline">Attribute Values</a> page.
                                </p>
                            </div>
                        </div>
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
                    Update Attribute
                </button>
            </div>
        </form>
    </div>
</div>
@endsection