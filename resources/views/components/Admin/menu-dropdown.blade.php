@props(['icon', 'title', 'items' => [], 'badge' => null, 'active' => false])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="relative">
    <button @click="open = !open" 
            class="flex items-center justify-between w-full px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200">
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
            </svg>
            <span>{{ $title }}</span>
        </div>
        <div class="flex items-center">
            @if($badge)
                <span class="mr-2 bg-red-500 text-white text-xs rounded-full px-2 py-1">{{ $badge }}</span>
            @endif
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-200" 
                 :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </button>
    
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="bg-gray-700">
        @foreach($items as $item)
            @php
                $route = $item['route'] ?? '#';
                $params = $item['params'] ?? [];
                $href = $route !== '#' ? route($route, $params) : '#';
                $isActive = request()->routeIs($route) || (isset($item['active']) && $item['active']);
            @endphp
            <a href="{{ $href }}" 
               class="block px-12 py-2 text-sm text-gray-300 hover:bg-gray-600 hover:text-white {{ $isActive ? 'bg-gray-600 text-white' : '' }} transition-colors duration-200">
                {{ $item['title'] }}
            </a>
        @endforeach
    </div>
</div>