@props(['position' => 'top-right'])

<div x-data="toast()" x-init="init()" @toast.window="show($event.detail)" 
     class="fixed z-50 {{ $position === 'top-right' ? 'top-5 right-5' : ($position === 'top-left' ? 'top-5 left-5' : ($position === 'bottom-right' ? 'bottom-5 right-5' : 'bottom-5 left-5')) }} w-80 max-w-full">
    <template x-for="(toast, index) in toasts" :key="toast.id">
        <div x-show="visible[toast.id]" 
             x-transition:enter="transform ease-out duration-300 transition"
             x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
             x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="mb-3 w-full bg-white rounded-lg shadow-lg border-l-4 overflow-hidden"
             :class="{
                 'border-green-500': toast.type === 'success',
                 'border-red-500': toast.type === 'error',
                 'border-yellow-500': toast.type === 'warning',
                 'border-blue-500': toast.type === 'info'
             }">
            <div class="flex items-center p-4">
                {{-- Icons --}}
                <div class="flex-shrink-0 mr-3">
                    {{-- Success Icon --}}
                    <svg x-show="toast.type === 'success'" class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    
                    {{-- Error Icon --}}
                    <svg x-show="toast.type === 'error'" class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    
                    {{-- Warning Icon --}}
                    <svg x-show="toast.type === 'warning'" class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    
                    {{-- Info Icon --}}
                    <svg x-show="toast.type === 'info'" class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                
                {{-- Content --}}
                <div class="flex-1">
                    <p x-show="toast.title" class="text-sm font-semibold" :class="{
                        'text-green-800': toast.type === 'success',
                        'text-red-800': toast.type === 'error',
                        'text-yellow-800': toast.type === 'warning',
                        'text-blue-800': toast.type === 'info'
                    }" x-text="toast.title"></p>
                    <!-- <p class="text-sm text-gray-600" x-text="toast.message"></p> -->
                </div>
                
                {{-- Close Button --}}
                <button @click="close(toast.id)" class="ml-3 flex-shrink-0 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>

<script>
function toast() {
    return {
        toasts: [],
        visible: {},
        nextId: 1,
        
        init() {
            window.showToast = (type, message, title = '', duration = 4000) => {
                this.show({ type, message, title, duration });
            };
        },
        
        show(data) {
            const id = this.nextId++;
            const toast = {
                id,
                type: data.type || 'info',
                message: data.message,
                title: data.title || this.getDefaultTitle(data.type),
                duration: data.duration || 4000
            };
            
            this.toasts.push(toast);
            this.$nextTick(() => {
                this.visible[id] = true;
            });
            
            if (toast.duration > 0) {
                setTimeout(() => {
                    this.close(id);
                }, toast.duration);
            }
        },
        
        close(id) {
            this.visible[id] = false;
            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, 200);
        },
        
        getDefaultTitle(type) {
            const titles = {
                success: 'Success!',
                error: 'Error!',
                warning: 'Warning!',
                info: 'Info'
            };
            return titles[type] || 'Notification';
        }
    };
}
</script>