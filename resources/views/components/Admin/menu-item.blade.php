@props(['icon', 'title', 'route' => null, 'badge' => null, 'active' => false])

@php
    $href = $route ? route($route) : '#';
    $classes = $active 
        ? 'flex items-center px-6 py-3 bg-blue-600 text-white' 
        : 'flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white';
@endphp

<a href="{{ $href }}" class="{{ $classes }} transition-colors duration-200">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
    </svg>
    <span class="flex-1">{{ $title }}</span>
    @if($badge)
        <span class="ml-2 bg-red-500 text-white text-xs rounded-full px-2 py-1">{{ $badge }}</span>
    @endif
</a>