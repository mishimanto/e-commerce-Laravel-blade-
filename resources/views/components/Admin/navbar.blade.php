{{-- resources/views/admin/layouts/navbar.blade.php --}}
@props(['unreadNotifications' => 0, 'notifications' => [], 'pageTitle' => 'Dashboard'])

<nav class="bg-white shadow-sm h-16 flex items-center justify-between px-6">
    <div class="flex items-center space-x-4">
        {{-- Page Title --}}
        <div class="text-lg font-semibold text-gray-800 hidden md:block">
            <!-- {{ $pageTitle }} -->
        </div>
    </div>

    <div class="flex items-center space-x-4">
        {{-- Notifications --}}
        <div class="relative" x-data="notificationComponent()" x-init="init()">
            <button @click="toggleDropdown()" class="text-gray-500 hover:text-gray-700 relative focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span x-show="unreadCount > 0" 
                      x-text="unreadCount"
                      class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center">
                </span>
            </button>

            <div x-show="open" @click.away="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border z-50">
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="font-bold">Notifications</h3>
                    <template x-if="unreadCount > 0">
                        <button @click="markAllAsRead()" class="text-xs text-blue-600 hover:text-blue-800">
                            Read all
                        </button>
                    </template>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    <template x-for="notification in notifications" :key="notification.id">
                        <a :href="notification.data.link" 
                           class="block px-4 py-3 hover:bg-gray-50 border-b"
                           :class="{ 'bg-blue-50': !notification.read_at }"
                           @click="markAsRead(notification.id)">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <template x-if="notification.data.icon === 'message'">
                                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </template>
                                    <template x-if="notification.data.icon === 'order'">
                                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                    </template>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm" x-text="notification.data.message" 
                                       :class="{ 'font-semibold': !notification.read_at }"></p>
                                    <p class="text-xs text-gray-500 mt-1" x-text="notification.created_at"></p>
                                </div>
                                <span x-show="!notification.read_at" class="inline-block w-2 h-2 bg-blue-600 rounded-full"></span>
                            </div>
                        </a>
                    </template>
                    
                    <div x-show="notifications.length === 0" class="text-center py-8">
                        <svg class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-gray-500">No notification found</p>
                    </div>
                </div>
                <a href="{{ route('admin.notifications.index') }}" class="block text-center text-sm text-blue-600 py-2 hover:bg-gray-50 border-t">
                    View all 
                </a>
            </div>
        </div>

        {{-- User Menu --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                {{-- Profile Image with Fallback --}}
                <div class="w-8 h-8 rounded-full overflow-hidden bg-blue-600 flex items-center justify-center text-white font-bold">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" 
                            alt="{{ auth()->user()->name }}" 
                            class="w-full h-full object-cover">
                    @else
                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                    @endif
                </div>
                
                {{-- User Name --}}
                <span class="text-sm font-medium text-gray-700 hidden sm:block">{{ auth()->user()->name ?? 'User' }}</span>
                
                {{-- Dropdown Arrow --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border z-50">
                <a href="{{ route('admin.profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Edit Profile
                    </div>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </div>
                </a>
                <hr class="my-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </div>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

@push('scripts')
<script>
function notificationComponent() {
    return {
        open: false,
        unreadCount: {{ $unreadNotifications }},
        notifications: @json($notifications),
        
        init() {
            this.loadNotifications();
            
            // Listen for custom events
            window.addEventListener('new-notification', (e) => {
                this.notifications.unshift(e.detail);
                this.unreadCount++;
            });
            
            window.addEventListener('update-unread-count', (e) => {
                this.unreadCount = e.detail.count;
            });
            
            // Initialize Pusher if user is logged in
            if (window.NotificationHandler) {
                window.NotificationHandler.init({{ auth()->id() }});
            }
        },
        
        toggleDropdown() {
            this.open = !this.open;
            if (this.open) {
                this.loadNotifications();
            }
        },
        
        loadNotifications() {
            fetch('{{ route("admin.notifications.list") }}')
                .then(response => response.json())
                .then(data => {
                    this.notifications = data;
                });
        },
        
        markAsRead(id) {
            fetch(`/admin/notifications/${id}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => {
                const notification = this.notifications.find(n => n.id === id);
                if (notification) {
                    notification.read_at = true;
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
            });
        },
        
        markAllAsRead() {
            fetch('{{ route("admin.notifications.mark-all-read") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => {
                this.notifications.forEach(n => n.read_at = true);
                this.unreadCount = 0;
            });
        }
    }
}
</script>
@endpush