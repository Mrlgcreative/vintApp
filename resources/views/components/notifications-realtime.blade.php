<!-- Notifications Toast Container -->
<div id="notifications-container" 
     class="fixed top-20 right-4 z-50 space-y-3 max-w-md w-full pointer-events-none"
     style="max-height: calc(100vh - 100px); overflow-y: auto;">
</div>

@auth
<script>
// Configuration Pusher/Echo pour les notifications temps réel
// Seulement en environnement de production : en local, le temps réel est désactivé proprement.
@if(app()->environment('production') && config('broadcasting.default') === 'pusher' && config('broadcasting.connections.pusher.key'))
if (typeof window.Echo !== 'undefined' && window.Echo) {
    window.Echo.private('user.{{ Auth::id() }}')
        .notification((notification) => {
            handleNotification(notification);
        })
        .listen('.notification.created', (notification) => {
            handleNotification(notification);
        });
}
@endif

/**
 * Point d'entree unique pour toutes les notifications recues
 */
function handleNotification(notification) {
    showNotification(notification);
    playNotificationSound();
    updateNotificationBadge();
    refreshNotificationsPanel();
}

/**
 * Affiche une notification toast
 */
function showNotification(notification) {
    const container = document.getElementById('notifications-container');
    if (!container) return;

    const notifId = 'notif-' + Date.now();

    let iconSvg = '';
    let title = notification.title || 'Notification';

    if (notification.type) {
        if (notification.type.includes('new_message') || notification.type === 'App\\Notifications\\NewMessage') {
            iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>';
        } else if (notification.type.includes('new_order') || notification.type === 'App\\Notifications\\NewOrder') {
            iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>';
        } else if (notification.type.includes('discount') || notification.type === 'App\\Notifications\\DiscountApplied') {
            iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>';
        } else if (notification.type.includes('refund')) {
            iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>';
        } else if (notification.type.includes('item_favorited')) {
            iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>';
        } else if (notification.type.includes('Approved')) {
            iconSvg = '<svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        } else if (notification.type.includes('Rejected')) {
            iconSvg = '<svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        } else if (notification.type.includes('wallet')) {
            iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>';
        } else {
            iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>';
        }
    } else {
        iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>';
    }

    const actionUrl = notification.data?.url || '#';

    const toast = document.createElement('div');
    toast.id = notifId;
    toast.className = `bg-gray-900/95 backdrop-blur-sm border border-gray-700/50 text-white rounded-xl shadow-2xl p-4 transform transition-all duration-300 pointer-events-auto animate-slide-in-right cursor-pointer hover:bg-gray-800`;
    toast.onclick = function() {
        if (actionUrl !== '#') {
            window.location.href = actionUrl;
        }
        closeNotification(notifId);
    };

    toast.innerHTML = `
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0 w-9 h-9 rounded-full bg-white/10 flex items-center justify-center">${iconSvg}</div>
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between mb-1">
                    <h4 class="font-bold text-sm">${title}</h4>
                    <button onclick="event.stopPropagation(); closeNotification('${notifId}')" 
                            class="text-white hover:text-gray-200 transition ml-2 flex-shrink-0">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="text-sm text-white/90">${notification.message || ''}</p>
                <p class="text-xs text-white/60 mt-1">${formatTime(notification.created_at)}</p>
            </div>
        </div>
    `;

    container.insertBefore(toast, container.firstChild);

    // Auto-fermeture apres 8 secondes
    setTimeout(() => {
        closeNotification(notifId);
    }, 8000);
}

/**
 * Ferme une notification toast
 */
function closeNotification(notifId) {
    const notif = document.getElementById(notifId);
    if (notif) {
        notif.style.opacity = '0';
        notif.style.transform = 'translateX(100%)';
        setTimeout(() => {
            notif.remove();
        }, 300);
    }
}

/**
 * Joue un son de notification (Web Audio API + MP3 fallback)
 */
function playNotificationSound() {
    try {
        const audio = new Audio();
        audio.src = '/sounds/notification-double.wav';
        audio.volume = 0.4;
        audio.play().catch(() => {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const now = ctx.currentTime;

                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.type = 'sine';
                osc1.frequency.value = 880;
                gain1.gain.setValueAtTime(0, now);
                gain1.gain.linearRampToValueAtTime(0.3, now + 0.02);
                gain1.gain.linearRampToValueAtTime(0, now + 0.12);
                osc1.start(now);
                osc1.stop(now + 0.12);

                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.type = 'sine';
                osc2.frequency.value = 1100;
                gain2.gain.setValueAtTime(0, now + 0.2);
                gain2.gain.linearRampToValueAtTime(0.25, now + 0.22);
                gain2.gain.linearRampToValueAtTime(0, now + 0.32);
                osc2.start(now + 0.2);
                osc2.stop(now + 0.32);
            } catch (e) {
                // Silencieux
            }
        });
    } catch (e) {
        // Silencieux
    }
}

/**
 * Formate une date en temps relatif
 */
function formatTime(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;

    if (diff < 60000) return "A l'instant";
    if (diff < 3600000) return Math.floor(diff / 60000) + ' min';
    if (diff < 86400000) return Math.floor(diff / 3600000) + ' h';
    return Math.floor(diff / 86400000) + ' j';
}

/**
 * Met a jour le badge de compteur de notifications
 */
function updateNotificationBadge() {
    const badge = document.getElementById('notification-badge');
    if (badge) {
        let currentCount = parseInt(badge.textContent || '0');
        if (isNaN(currentCount)) currentCount = 0;
        currentCount++;
        badge.textContent = currentCount > 99 ? '99+' : currentCount;
        badge.classList.remove('hidden');
    }
}

/**
 * Rafraichit le panneau de notifications flottant s'il est ouvert
 */
function refreshNotificationsPanel() {
    const panel = document.getElementById('notifications-panel');
    if (panel) {
        panel.remove();
        if (typeof toggleNotifications === 'function') {
            toggleNotifications();
        }
    }
}

// Animation CSS pour le slide-in
(function() {
    if (!document.getElementById('notification-anim-style')) {
        const style = document.createElement('style');
        style.id = 'notification-anim-style';
        style.textContent = `
            @keyframes slide-in-right {
                0% { opacity: 0; transform: translateX(100%); }
                100% { opacity: 1; transform: translateX(0); }
            }
            .animate-slide-in-right {
                animation: slide-in-right 0.35s ease-out;
            }
        `;
        document.head.appendChild(style);
    }
})();
</script>
@endauth
