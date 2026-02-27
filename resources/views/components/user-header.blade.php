@props(['user'])

{{-- Top Navigation Bar --}}
<nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            {{-- Logo and Main Nav --}}
            <div class="flex">
                <!-- Logo -->
                <!-- <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="text-xl font-bold text-indigo-600">
                        {{ setting('store_name', 'Phone & Gadgets') }}
                    </a>
                </div> -->

                {{-- Navigation Links --}}
                <div class="hidden sm:ml-8 sm:flex sm:space-x-6">
                    <a href="{{ route('profile.dashboard') }}" 
                       class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('profile.dashboard') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-700 hover:text-indigo-600' }}">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('profile.orders') }}" 
                       class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('profile.orders') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-700 hover:text-indigo-600' }}">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Orders
                    </a>

                    <a href="{{ route('profile.wishlist') }}" 
                       class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('profile.wishlist') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-700 hover:text-indigo-600' }}">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        Wishlist
                    </a>

                    <a href="{{ route('profile.addresses') }}" 
                       class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('profile.addresses') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-700 hover:text-indigo-600' }}">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Addresses
                    </a>

                    <a href="{{ route('profile.settings') }}" 
                       class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('profile.settings') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-700 hover:text-indigo-600' }}">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        </svg>
                        Profile
                    </a>
                </div>
            </div>

            {{-- Right Side Navigation --}}
            <div class="flex items-center space-x-4">
                {{-- Back to Store --}}
                <a href="{{ route('home') }}" class="text-sm text-gray-700 hover:text-indigo-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Back to Store
                </a>

                {{-- User Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2 text-sm focus:outline-none group">
                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center group-hover:bg-indigo-200 transition-colors overflow-hidden">
                            @if($user->avatar)
                                @php
                                    $avatarUrl = filter_var($user->avatar, FILTER_VALIDATE_URL) 
                                        ? $user->avatar 
                                        : asset('storage/' . ltrim($user->avatar, '/'));
                                @endphp
                                <img src="{{ $avatarUrl }}" 
                                     alt="{{ $user->name }}" 
                                     class="h-full w-full object-cover"
                                     onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.png') }}'; this.parentElement.innerHTML='<svg class=\'h-4 w-4 text-indigo-600\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z\' /></svg>';">
                            @else
                                <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            @endif
                        </div>
                        <span class="text-gray-700 group-hover:text-indigo-600">{{ $user->name }}</span>
                        <svg class="w-4 h-4 text-gray-500 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 border border-gray-200 z-50">
                        <a href="{{ route('profile.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                            Profile Settings
                        </a>
                        <a href="{{ route('profile.orders') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                            My Orders
                        </a>
                        <a href="{{ route('profile.wishlist') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                            Wishlist
                        </a>
                        <a href="{{ route('profile.addresses') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                            Addresses
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mobile Navigation (visible on small screens) --}}
        <div class="sm:hidden border-t border-gray-200 py-2">
            <div class="flex flex-wrap gap-2 justify-center">
                <a href="{{ route('profile.dashboard') }}" 
                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium {{ request()->routeIs('profile.dashboard') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg">
                    Dashboard
                </a>
                <a href="{{ route('profile.orders') }}" 
                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium {{ request()->routeIs('profile.orders') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg">
                    Orders
                </a>
                <a href="{{ route('profile.wishlist') }}" 
                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium {{ request()->routeIs('profile.wishlist') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg">
                    Wishlist
                </a>
                <a href="{{ route('profile.addresses') }}" 
                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium {{ request()->routeIs('profile.addresses') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg">
                    Addresses
                </a>
                <a href="{{ route('profile.settings') }}" 
                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium {{ request()->routeIs('profile.settings') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-50' }} rounded-lg">
                    Settings
                </a>
            </div>
        </div>
    </div>
</nav>