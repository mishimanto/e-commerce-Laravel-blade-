@extends('layouts.admin')

@section('title', 'Edit Setting')

@section('content')
<div class="w-full px-4 py-8 mx-auto max-w-7xl">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Setting: {{ $setting->label }}</h1>
        <a href="{{ route('admin.settings.index', ['group' => $setting->group]) }}" 
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Settings
        </a>
    </div>

    @if($errors->any())
        <div class="p-4 mb-6 text-sm text-red-800 bg-red-100 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <form action="{{ route('admin.settings.update', $setting) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Setting Key -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Setting Key <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="key" 
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('key') border-red-500 @enderror" 
                               value="{{ old('key', $setting->key) }}" 
                               required 
                               placeholder="e.g., store_name, tax_rate">
                        <p class="mt-1 text-xs text-gray-500">Use lowercase and underscores only</p>
                        @error('key')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Display Label -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Display Label <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="label" 
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('label') border-red-500 @enderror" 
                               value="{{ old('label', $setting->label) }}" 
                               required 
                               placeholder="e.g., Store Name">
                        @error('label')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Group -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Group <span class="text-red-500">*</span>
                        </label>
                        <select name="group" 
                                class="block w-full mt-1 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md @error('group') border-red-500 @enderror" 
                                required>
                            <option value="">Select Group</option>
                            @foreach($groups as $key => $label)
                                <option value="{{ $key }}" {{ old('group', $setting->group) == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('group')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Input Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Input Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type" id="typeSelect"
                                class="block w-full mt-1 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md @error('type') border-red-500 @enderror" 
                                required>
                            <option value="">Select Type</option>
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}" {{ old('type', $setting->type) == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Options Field (for select/radio) -->
                    <div class="md:col-span-2" id="options-field" style="display: {{ in_array($setting->type, ['select', 'radio']) ? 'block' : 'none' }};">
                        <label class="block text-sm font-medium text-gray-700">Options (JSON)</label>
                        <textarea name="options" rows="3" 
                                  class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('options') border-red-500 @enderror" 
                                  placeholder='{"value1":"Label 1","value2":"Label 2"}'>{{ old('options', json_encode($setting->options, JSON_PRETTY_PRINT)) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Required for select, radio types. Format as JSON key-value pairs.</p>
                        @error('options')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Value (Read Only) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Current Value</label>
                        <input type="text" 
                               class="block w-full mt-1 bg-gray-100 border-gray-300 rounded-md shadow-sm sm:text-sm" 
                               value="{{ $setting->value }}" 
                               readonly 
                               disabled>
                    </div>

                    <!-- Default Value -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Default Value</label>
                        <input type="text" name="value" 
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('value') border-red-500 @enderror" 
                               value="{{ old('value', $setting->value) }}">
                        @error('value')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" rows="2" 
                                  class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('description') border-red-500 @enderror">{{ old('description', $setting->description) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Help text to explain what this setting does</p>
                        @error('description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Editable Checkbox -->
                    <div class="flex items-center">
                        <input type="checkbox" name="is_editable" value="1" 
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                               id="isEditable" {{ old('is_editable', $setting->is_editable) ? 'checked' : '' }}>
                        <label for="isEditable" class="ml-2 text-sm text-gray-700">Editable</label>
                        <p class="ml-2 text-xs text-gray-500">Allow editing in settings page</p>
                    </div>

                    <!-- Visible Checkbox -->
                    <div class="flex items-center">
                        <input type="checkbox" name="is_visible" value="1" 
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                               id="isVisible" {{ old('is_visible', $setting->is_visible) ? 'checked' : '' }}>
                        <label for="isVisible" class="ml-2 text-sm text-gray-700">Visible</label>
                        <p class="ml-2 text-xs text-gray-500">Show in settings page</p>
                    </div>
                </div>

                <div class="flex justify-end mt-6 space-x-3">
                    <a href="{{ route('admin.settings.index', ['group' => $setting->group]) }}" 
                       class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Update Setting
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('typeSelect');
    const optionsField = document.getElementById('options-field');
    
    function toggleOptions() {
        const type = typeSelect.value;
        if (type === 'select' || type === 'radio') {
            optionsField.style.display = 'block';
        } else {
            optionsField.style.display = 'none';
        }
    }
    
    typeSelect.addEventListener('change', toggleOptions);
    toggleOptions(); // Initial call
});
</script>
@endpush
@endsection