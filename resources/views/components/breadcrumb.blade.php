<nav class="bg-gray-100 py-3 mb-6">
    <div class="container mx-auto px-4">
        <ol class="flex items-center text-sm">
            @if($home)
                <li>
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-blue-600">
                        <i class="fas fa-home mr-1"></i> Home
                    </a>
                    <span class="mx-2 text-gray-400">/</span>
                </li>
            @endif

            @foreach($items as $item)
                @if($loop->last)
                    <li class="text-gray-800 font-medium">
                        @if(isset($item['icon']))
                            <i class="{{ $item['icon'] }} mr-1"></i>
                        @endif
                        {{ $item['name'] }}
                    </li>
                @else
                    <li>
                        <a href="{{ $item['url'] }}" class="text-gray-600 hover:text-blue-600">
                            @if(isset($item['icon']))
                                <i class="{{ $item['icon'] }} mr-1"></i>
                            @endif
                            {{ $item['name'] }}
                        </a>
                        <span class="mx-2 text-gray-400">/</span>
                    </li>
                @endif
            @endforeach
        </ol>
    </div>
</nav>