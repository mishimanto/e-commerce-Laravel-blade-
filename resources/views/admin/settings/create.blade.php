@extends('layouts.admin')

@section('title', 'Create Setting')

@section('content')
<div class="">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Create New Setting</h1>
        <a href="{{ route('admin.settings.index') }}" 
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Settings
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 mb-6 text-sm text-green-800 bg-green-100 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 mb-6 text-sm text-red-800 bg-red-100 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 mb-6 text-sm text-red-800 bg-red-100 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Create Form -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Setting Details</h2>
        </div>

        <div class="p-6">
            <form action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="space-y-6">
                    <!-- Key -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Setting Key <span class="text-red-600">*</span>
                        </label>
                        <input type="text" 
                               name="key" 
                               value="{{ old('key') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="e.g., site_name, contact_email, social_facebook"
                               required>
                        <p class="mt-1 text-xs text-gray-500">
                            Use lowercase letters, numbers, and underscores only. Must be unique.
                        </p>
                    </div>

                    <!-- Label -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Display Label <span class="text-red-600">*</span>
                        </label>
                        <input type="text" 
                               name="label" 
                               value="{{ old('label') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="e.g., Site Name, Contact Email, Facebook URL"
                               required>
                    </div>

                    <!-- Group -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Group <span class="text-red-600">*</span>
                        </label>
                        <select name="group" 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                required>
                            <option value="">Select a group</option>
                            @foreach($groups ?? [] as $key => $label)
                                <option value="{{ $key }}" {{ old('group') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Field Type <span class="text-red-600">*</span>
                        </label>
                        <select name="type" 
                                id="type-select"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                required>
                            <option value="">Select field type</option>
                            @foreach($types ?? [] as $key => $label)
                                <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Default Value -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Default Value
                        </label>
                        <input type="text" 
                               name="value" 
                               value="{{ old('value') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="Enter default value">
                        <p class="mt-1 text-xs text-gray-500">
                            Optional: Set a default value for this setting
                        </p>
                    </div>

                    <!-- Options (for select, radio, etc) -->
                    <div id="options-field" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Options
                        </label>
                        <textarea name="options" 
                                  rows="4"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                  placeholder="e.g., {&quot;option1&quot;:&quot;Option 1&quot;,&quot;option2&quot;:&quot;Option 2&quot;}">{{ old('options') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">
                            JSON format required for select, radio, and checkbox options.
                        </p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Description
                        </label>
                        <textarea name="description" 
                                  rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                  placeholder="Enter a description for this setting">{{ old('description') }}</textarea>
                    </div>

                    <!-- Status -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="is_editable" 
                                   value="1"
                                   id="is_editable"
                                   {{ old('is_editable', true) ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <label for="is_editable" class="ml-2 text-sm text-gray-700">
                                Editable
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="is_visible" 
                                   value="1"
                                   id="is_visible"
                                   {{ old('is_visible', true) ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <label for="is_visible" class="ml-2 text-sm text-gray-700">
                                Visible
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-4">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            Create Setting
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type-select');
    const optionsField = document.getElementById('options-field');
    
    // Types that require options
    const typesWithOptions = ['select', 'radio', 'checkbox'];
    
    function toggleOptionsField() {
        const selectedType = typeSelect.value;
        if (typesWithOptions.includes(selectedType)) {
            optionsField.classList.remove('hidden');
        } else {
            optionsField.classList.add('hidden');
        }
    }
    
    // Initial check
    toggleOptionsField();
    
    // Listen for changes
    typeSelect.addEventListener('change', toggleOptionsField);
});
</script>
@endpush
@endsection