@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Settings</h1>
        <div class="flex space-x-3">
            <button type="button" 
                    onclick="clearCache()"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Clear Cache
            </button>
            <a href="{{ route('admin.settings.create') }}" 
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Setting
            </a>
        </div>
    </div>

    <!-- Settings Navigation -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
        <!-- Sidebar with Tab-style Groups -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <!-- <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Settings Groups</span>
                    </div>
                </div> -->
                <div class="p-2">
                    @php
                        $activeGroup = request()->get('group', 'general');
                    @endphp
                    
                    @foreach($groups as $groupKey => $groupLabel)
                        <a href="{{ route('admin.settings.index', ['group' => $groupKey]) }}" 
                           class="flex items-center px-3 py-2.5 mb-1 text-md font-medium rounded-lg transition-all duration-200 {{ $activeGroup == $groupKey ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900 border-l-4 border-transparent' }}">
                            <svg class="w-4 h-4 mr-3 {{ $activeGroup == $groupKey ? 'text-blue-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            {{ $groupLabel }}
                            @php
                                $count = \App\Models\Setting::where('group', $groupKey)->count();
                            @endphp
                            <!-- <span class="ml-auto {{ $activeGroup == $groupKey ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2 rounded-full text-xs">
                                {{ $count }}
                            </span> -->
                        </a>
                    @endforeach
                </div>
                
                <!-- Quick Stats -->
                <!-- <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                    <div class="text-xs text-gray-500">
                        <div class="flex justify-between mb-1">
                            <span>Total Settings:</span>
                            <span class="font-medium">{{ \App\Models\Setting::count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Active Groups:</span>
                            <span class="font-medium">{{ \App\Models\Setting::distinct('group')->count('group') }}</span>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                        <h2 class="text-lg font-medium text-gray-900">{{ $groups[$group] ?? 'Settings' }}</h2>
                    </div>
                    <button type="button" 
                            onclick="saveSettings()"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Save Changes
                    </button>
                </div>

                <div class="p-6">
                    <form id="settings-form" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="space-y-6">
                            @forelse($settings as $setting)
                                <div class="p-4 bg-gray-50 rounded-lg hover:shadow-sm transition-shadow duration-200">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $setting->label }}</label>
                                    <!-- @if($setting->description)
                                        <p class="text-xs text-gray-500 mb-2">{{ $setting->description }}</p>
                                    @endif -->
                                    
                                    {{-- Different input types --}}
                                    @switch($setting->type)
                                        @case('text')
                                        @case('email')
                                        @case('url')
                                        @case('number')
                                        @case('color')
                                            <input type="{{ $setting->type }}" 
                                                   name="settings[{{ $setting->key }}]" 
                                                   id="setting_{{ $setting->id }}"
                                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                   value="{{ $setting->value }}"
                                                   {{ !$setting->is_editable ? 'disabled' : '' }}>
                                            @break
                                        
                                        @case('textarea')
                                            <textarea name="settings[{{ $setting->key }}]" 
                                                      id="setting_{{ $setting->id }}"
                                                      rows="3" 
                                                      class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                      {{ !$setting->is_editable ? 'disabled' : '' }}>{{ $setting->value }}</textarea>
                                            @break
                                        
                                        @case('select')
                                            <select name="settings[{{ $setting->key }}]" 
                                                    id="setting_{{ $setting->id }}"
                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                    {{ !$setting->is_editable ? 'disabled' : '' }}>
                                                @if($setting->options && is_array($setting->options))
                                                    @foreach($setting->options as $value => $label)
                                                        <option value="{{ $value }}" 
                                                            {{ $setting->value == $value ? 'selected' : '' }}>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @break
                                        
                                        @case('checkbox')
                                            <div class="flex items-center">
                                                <input type="checkbox" 
                                                       name="settings[{{ $setting->key }}]" 
                                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                                       value="1"
                                                       id="setting_{{ $setting->id }}"
                                                       {{ $setting->value ? 'checked' : '' }}
                                                       {{ !$setting->is_editable ? 'disabled' : '' }}>
                                                <label class="ml-2 text-sm text-gray-700" for="setting_{{ $setting->id }}">
                                                    Enable
                                                </label>
                                            </div>
                                            @break
                                        
                                        @case('radio')
                                            <div class="space-y-2">
                                                @if($setting->options && is_array($setting->options))
                                                    @foreach($setting->options as $value => $label)
                                                        <div class="flex items-center">
                                                            <input type="radio" 
                                                                   name="settings[{{ $setting->key }}]" 
                                                                   class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                                                   value="{{ $value }}"
                                                                   id="setting_{{ $setting->id }}_{{ $value }}"
                                                                   {{ $setting->value == $value ? 'checked' : '' }}
                                                                   {{ !$setting->is_editable ? 'disabled' : '' }}>
                                                            <label class="ml-2 text-sm text-gray-700" for="setting_{{ $setting->id }}_{{ $value }}">
                                                                {{ $label }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                            @break
                                        
                                        @case('image')
                                            @if($setting->value)
                                                <div class="mb-2">
                                                    <img src="{{ asset('storage/' . $setting->value) }}" 
                                                         alt="{{ $setting->label }}"
                                                         class="object-cover w-32 h-32 border border-gray-200 rounded-lg">
                                                </div>
                                            @endif
                                            <input type="file" 
                                                   name="settings[{{ $setting->key }}]" 
                                                   id="setting_{{ $setting->id }}"
                                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                                   accept="image/*"
                                                   {{ !$setting->is_editable ? 'disabled' : '' }}>
                                            @break
                                        
                                        @case('file')
                                            @if($setting->value)
                                                <div class="mb-2">
                                                    <a href="{{ asset('storage/' . $setting->value) }}" target="_blank"
                                                       class="inline-flex items-center px-3 py-1 text-sm text-blue-700 bg-blue-100 rounded hover:bg-blue-200">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                        View File
                                                    </a>
                                                </div>
                                            @endif
                                            <input type="file" 
                                                   name="settings[{{ $setting->key }}]" 
                                                   id="setting_{{ $setting->id }}"
                                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                                   {{ !$setting->is_editable ? 'disabled' : '' }}>
                                            @break
                                        
                                        @case('editor')
                                            <textarea name="settings[{{ $setting->key }}]" 
                                                      id="setting_{{ $setting->id }}"
                                                      class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm editor"
                                                      {{ !$setting->is_editable ? 'disabled' : '' }}>{{ $setting->value }}</textarea>
                                            @break
                                        
                                        @case('code')
                                            <textarea name="settings[{{ $setting->key }}]" 
                                                      id="setting_{{ $setting->id }}"
                                                      class="block w-full px-3 py-2 font-mono text-sm border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 code-editor"
                                                      rows="10"
                                                      {{ !$setting->is_editable ? 'disabled' : '' }}>{{ $setting->value }}</textarea>
                                            @break
                                        
                                        @default
                                            <input type="text" 
                                                   name="settings[{{ $setting->key }}]" 
                                                   id="setting_{{ $setting->id }}"
                                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                   value="{{ $setting->value }}"
                                                   {{ !$setting->is_editable ? 'disabled' : '' }}>
                                    @endswitch
                                    
                                    <!-- <p class="mt-1 text-xs text-gray-500">
                                        Key: <code class="px-1 py-0.5 bg-gray-100 rounded">{{ $setting->key }}</code>
                                    </p> -->
                                </div>
                            @empty
                                <div class="py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="mt-2 text-gray-500">No settings found in this group.</p>
                                    <!-- <p class="text-sm text-gray-400">Select a different group from the sidebar or create a new setting.</p> -->
                                </div>
                            @endforelse
                        </div>
                    </form>
                </div>
            </div>

            <!-- System Tools Section -->
            <div class="grid grid-cols-1 gap-6 mt-6 md:grid-cols-2">
                <!-- Cache Management -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-sm font-medium text-gray-700">Cache Management</h3>
                    </div>
                    <div class="p-4">
                        <button type="button" 
                                onclick="clearCache()"
                                class="w-full inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Clear All Cache
                        </button>
                    </div>
                </div>

                <!-- Maintenance Mode -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-sm font-medium text-gray-700">Maintenance Mode</h3>
                    </div>
                    <div class="p-4">
                        <button type="button" 
                                onclick="showMaintenanceModal()"
                                class="w-full inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-white bg-yellow-600 border border-transparent rounded-md shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Toggle Maintenance Mode
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Maintenance Mode Modal --}}
<div id="maintenanceModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="hideMaintenanceModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-yellow-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                            Maintenance Mode
                        </h3>
                        <div class="mt-2">
                            <form id="maintenance-form" action="{{ route('admin.settings.maintenance') }}" method="POST">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Secret Token (Optional)</label>
                                        <input type="text" name="secret" 
                                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                               placeholder="Enter secret token to bypass maintenance">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Retry After (minutes)</label>
                                        <input type="number" name="retry" 
                                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                               value="60">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" 
                        onclick="enableMaintenance()"
                        class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-yellow-600 border border-transparent rounded-md shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Enable Maintenance Mode
                </button>
                <button type="button" 
                        onclick="hideMaintenanceModal()"
                        class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
<style>
    .CodeMirror { border: 1px solid #d1d5db; border-radius: 0.375rem; }
    /* Smooth transitions */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 200ms;
    }
    /* Sidebar group items */
    .sidebar-group-item {
        border-left-width: 4px;
        transition: all 0.2s ease;
    }
    .sidebar-group-item:hover {
        transform: translateX(2px);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/htmlmixed/htmlmixed.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Summernote editors
    if ($('.editor').length > 0) {
        $('.editor').summernote({
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    }
    
    // Initialize CodeMirror editors
    document.querySelectorAll('.code-editor').forEach(textarea => {
        CodeMirror.fromTextArea(textarea, {
            lineNumbers: true,
            mode: 'htmlmixed',
            theme: 'default',
            lineWrapping: true
        });
    });
});

function saveSettings() {
    document.getElementById('settings-form').submit();
}

function clearCache() {
    if (confirm('Clear all system cache?')) {
        fetch('{{ route("admin.settings.cache") }}', {
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
                alert('Cache cleared successfully');
                window.location.reload();
            } else {
                alert('Error clearing cache');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error clearing cache');
        });
    }
}

function showMaintenanceModal() {
    document.getElementById('maintenanceModal').classList.remove('hidden');
}

function hideMaintenanceModal() {
    document.getElementById('maintenanceModal').classList.add('hidden');
}

function enableMaintenance() {
    document.getElementById('maintenance-form').submit();
}
</script>
@endpush