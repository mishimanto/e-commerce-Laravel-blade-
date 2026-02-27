// resources/js/echo.js

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Initialize Echo
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    encrypted: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Accept': 'application/json'
        }
    }
});

// Notification sound (optional)
const notificationSound = new Audio('/sounds/notification.mp3');

// Global notification handler
window.NotificationHandler = {
    init(userId) {
        if (!window.Echo) return;
        
        console.log('Initializing notifications for user:', userId);
        
        // Listen to private channel
        window.Echo.private(`App.Models.User.${userId}`)
            .notification((notification) => {
                console.log('New notification received:', notification);
                this.handleNewNotification(notification);
            });
    },

    handleNewNotification(notification) {
        // Play sound
        this.playSound();
        
        // Update unread count
        this.updateUnreadCount();
        
        // Show browser notification (optional)
        this.showBrowserNotification(notification);
        
        // Dispatch custom event for Alpine.js
        window.dispatchEvent(new CustomEvent('new-notification', { 
            detail: notification 
        }));
    },

    // playSound() {
    //     notificationSound.play().catch(e => console.log('Audio play failed:', e));
    // },

    updateUnreadCount() {
        fetch('/admin/notifications/unread-count')
            .then(response => response.json())
            .then(data => {
                window.dispatchEvent(new CustomEvent('update-unread-count', { 
                    detail: { count: data.count } 
                }));
            });
    },

    showBrowserNotification(notification) {
        if (Notification.permission === 'granted') {
            new Notification('GadgetsBD', {
                body: notification.data?.message || 'New Notification',
                icon: '/favicon.ico',
                badge: '/favicon.ico',
                vibrate: [200, 100, 200],
                silent: false
            });
        }
    }
};

// Request notification permission
document.addEventListener('DOMContentLoaded', function() {
    if (Notification.permission !== 'granted' && Notification.permission !== 'denied') {
        Notification.requestPermission();
    }
});

export default window.Echo;