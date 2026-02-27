<div x-data="notification()" 
     x-init="init()"
     @notify.window="show($event.detail)"
     class="fixed top-4 right-4 z-[9999] flex flex-col gap-3 min-w-[320px] max-w-[420px] pointer-events-none">
    
    <template x-for="(notification, index) in notifications" :key="index">
        <div x-show="notification.show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-full translate-y-0"
             x-transition:enter-end="opacity-100 transform translate-x-0 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-x-0 translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-x-full translate-y-0"
             class="pointer-events-auto w-full bg-white shadow-2xl overflow-hidden border-l-4 transform-gpu"
             :class="{
                 'border-green-500': notification.type === 'success',
                 'border-red-500': notification.type === 'error',
                 'border-yellow-500': notification.type === 'warning',
                 'border-blue-500': notification.type === 'info'
             }">
            
            <div class="p-4">
                <div class="flex items-center justify-center gap-3">
                    {{-- Icon with background --}}
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center"
                             :class="{
                                 'bg-green-100': notification.type === 'success',
                                 'bg-red-100': notification.type === 'error',
                                 'bg-yellow-100': notification.type === 'warning',
                                 'bg-blue-100': notification.type === 'info'
                             }">
                            <template x-if="notification.type === 'success'">
                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </template>
                            <template x-if="notification.type === 'error'">
                                <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </template>
                            <template x-if="notification.type === 'warning'">
                                <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </template>
                            <template x-if="notification.type === 'info'">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </template>
                        </div>
                    </div>
                    
                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p x-show="notification.title" class="text-sm font-semibold text-gray-900" x-text="notification.title"></p>
                        <!-- <p class="text-sm text-gray-600 break-words" x-text="notification.message"></p> -->
                    </div>
                    
                    {{-- Close button --}}
                    <div class="flex-shrink-0 ml-2">
                        <button @click="remove(index)" class="text-gray-400 hover:text-gray-600 focus:outline-none transition-colors p-1 hover:bg-gray-100 rounded-full">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                {{-- Progress bar (optional) --}}
                <div x-show="notification.progress" class="absolute bottom-0 left-0 h-1 bg-gray-200 rounded-b-xl overflow-hidden w-full">
                    <div class="h-full transition-all duration-[5000ms] linear"
                         :style="'width: ' + notification.progress + '%'"
                         :class="{
                             'bg-green-500': notification.type === 'success',
                             'bg-red-500': notification.type === 'error',
                             'bg-yellow-500': notification.type === 'warning',
                             'bg-blue-500': notification.type === 'info'
                         }"></div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function notification() {
    return {
        notifications: [],
        
        init() {
            window.showNotification = (type, message, title = '', options = {}) => {
                this.show({ type, message, title, ...options });
            };
        },
        
        show({ type = 'info', message, title = '', duration = 5000, showProgress = false }) {
            const id = Date.now() + Math.random();
            const notification = {
                id,
                type,
                message,
                title,
                show: true,
                progress: showProgress ? 100 : null
            };
            
            this.notifications.push(notification);
            
            // Animate progress bar if enabled
            if (showProgress) {
                const startTime = Date.now();
                const updateProgress = () => {
                    const index = this.notifications.findIndex(n => n.id === id);
                    if (index === -1) return;
                    
                    const elapsed = Date.now() - startTime;
                    const remaining = Math.max(0, duration - elapsed);
                    const progress = (remaining / duration) * 100;
                    
                    this.notifications[index].progress = progress;
                    
                    if (remaining > 0) {
                        requestAnimationFrame(updateProgress);
                    }
                };
                requestAnimationFrame(updateProgress);
            }
            
            setTimeout(() => {
                this.remove(this.notifications.findIndex(n => n.id === id));
            }, duration);
        },
        
        remove(index) {
            if (index !== -1) {
                this.notifications[index].show = false;
                setTimeout(() => {
                    this.notifications.splice(index, 1);
                }, 300);
            }
        }
    };
}
</script>