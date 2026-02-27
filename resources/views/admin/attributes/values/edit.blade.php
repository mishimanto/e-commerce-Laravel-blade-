{{-- resources/views/admin/attributes/values/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit Value - ' . $value->value)

@section('content')
<div class="">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-500">Edit Value</h1>
            <p class="text-gray-600">Attribute: <span class="font-medium">{{ $attribute->name }}</span></p>
        </div>
        <a href="{{ route('admin.attributes.values.index', $attribute) }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Values
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
        <form action="{{ route('admin.attributes.values.update', [$attribute, $value]) }}" 
              method="POST" 
              class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Left Column --}}
                <div class="space-y-4">
                    <div>
                        <label for="value" class="block text-sm font-medium text-gray-700 mb-1 required">Value</label>
                        <input type="text" name="value" id="value" value="{{ old('value', $value->value) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('value')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $value->sort_order) }}"
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
                    @if($attribute->type === 'color')
                    <div>
                        <label for="color_code" class="block text-sm font-medium text-gray-700 mb-1">Color Code (Hex)</label>
                        <div class="flex items-center gap-2">
                            <input type="color" id="color_picker" 
                                   class="h-10 w-10 rounded-md border border-gray-300 cursor-pointer"
                                   value="{{ old('color_code', $value->color_code ?? '#000000') }}">
                            <input type="text" name="color_code" id="color_code" 
                                   value="{{ old('color_code', $value->color_code ?? '#000000') }}"
                                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="#000000">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Enter hex color code (e.g., #FF0000 for red)</p>
                        @error('color_code')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    @endif

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-gray-800">Value Information</h4>
                                <p class="text-xs text-gray-600 mt-1">
                                    <strong>Slug:</strong> {{ $value->slug }}<br>
                                    <strong>Created:</strong> {{ $value->created_at->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.attributes.values.index', $attribute) }}" 
                   class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Update Value
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
@if($attribute->type === 'color')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorPicker = document.getElementById('color_picker');
    const colorInput = document.getElementById('color_code');
    
    if (colorPicker && colorInput) {
        colorPicker.addEventListener('input', function(e) {
            colorInput.value = e.target.value;
        });
        
        colorInput.addEventListener('input', function(e) {
            const hex = e.target.value;
            if (/^#[0-9A-F]{6}$/i.test(hex)) {
                colorPicker.value = hex;
            }
        });
    }
});
</script>
@endif
@endpush