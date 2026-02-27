@props(['title' => null, 'closeable' => true, 'maxWidth' => '2xl'])

@php
$maxWidthClass = match($maxWidth) {
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    '3xl' => 'max-w-3xl',
    '4xl' => 'max-w-4xl',
    '5xl' => 'max-w-5xl',
    '6xl' => 'max-w-6xl',
    '7xl' => 'max-w-7xl',
    'full' => 'max-w-full mx-4',
    default => 'max-w-2xl',
};
@endphp

<div x-data="{ show: false }" 
     x-init="$watch('show', value => { 
        if (value) { 
            document.body.style.overflow = 'hidden'; 
        } else { 
            document.body.style.overflow = 'auto'; 
        }
     })"
     x-cloak>
    
    {{-- Trigger --}}
    <div @click="show = true">
        {{ $trigger }}
    </div>

    {{-- Modal --}}
    <div x-show="show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="show = false"
         style="display: none;">
        
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

        {{-- Modal Container --}}
        <div class="flex items-center justify-center min-h-screen px-4">
            <div x-show="show" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 @click.away="show = false"
                 class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all w-full {{ $maxWidthClass }}"
                 style="display: none;">
                
                {{-- Header --}}
                @if($title || $closeable)
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        @if($title)
                            <h3 class="text-lg font-bold text-gray-900">{{ $title }}</h3>
                        @endif
                        @if($closeable)
                            <button @click="show = false" class="text-gray-400 hover:text-gray-600 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        @endif
                    </div>
                @endif

                {{-- Content --}}
                <div class="px-6 py-4">
                    {{ $slot }}
                </div>

                {{-- Footer --}}
                @isset($footer)
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        {{ $footer }}
                    </div>
                @endisset
            </div>
        </div>
    </div>
</div>