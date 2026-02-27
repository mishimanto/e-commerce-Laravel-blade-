@props([
    'size' => 'md',
    'fullScreen' => false,
    'image' => null, 
])

@php
$sizes = [
    'sm' => 'w-10 h-10',
    'md' => 'w-16 h-16',
    'lg' => 'w-24 h-24',
    'xl' => 'w-32 h-32',
];


$imageSizes = [
    'sm' => 'w-5 h-5',
    'md' => 'w-10 h-10',
    'lg' => 'w-16 h-16',
    'xl' => 'w-20 h-20',
];


$brandGreen = 'from-emerald-500 via-green-500 to-teal-600';
@endphp

@if($fullScreen)
<div class="fixed inset-0 z-[9999] bg-white/80 backdrop-blur-md flex flex-col items-center justify-center">
@endif

    <div class="flex flex-col items-center justify-center">
        {{-- Spinner Container --}}
        <div class="relative {{ $sizes[$size] }}">
            
            {{-- Outer Thicker Spinner (Professional Look) --}}
            <div class="absolute inset-0 rounded-full bg-gradient-to-tr {{ $brandGreen }} animate-spin shadow-lg"
                 style="padding: 4px; animation-duration: 1s;"> 
                <div class="w-full h-full bg-white rounded-full"></div>
            </div>

            {{-- Soft Pulse Glow (Greenish) --}}
            <div class="absolute inset-0 rounded-full bg-emerald-400 blur-xl opacity-20 animate-pulse"></div>

            {{-- Center Image/Logo Container --}}
            @if($image)
                <div class="absolute inset-0 flex items-center justify-center">
                    
                    <div class="p-2 sm:p-3 bg-white rounded-full">
                        <img src="{{ $image }}" 
                             alt="Loading..." 
                             class="{{ $imageSizes[$size] }} object-contain">
                    </div>
                </div>
            @endif
        </div>

        <!-- {{-- Optional: Loading Text --}}
        <p class="mt-4 text-sm font-semibold text-emerald-700 tracking-widest uppercase animate-pulse">
            Loading...
        </p> -->
    </div>

@if($fullScreen)
</div>
@endif

@push('styles')
<style>
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
</style>
@endpush