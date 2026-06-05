<!-- Notifications Toast Container -->
<div id="notifications-container" 
     class="fixed top-20 right-4 z-50 space-y-3 max-w-md w-full pointer-events-none"
     style="max-height: calc(100vh - 100px); overflow-y: auto;">
</div>

<?php if(auth()->guard()->check()): ?>
<script>
// Configuration Pusher/Echo pour les notifications temps réel
<?php if(config('broadcasting.default') === 'pusher'): ?>
if (typeof window.Echo !== 'undefined' && window.Echo) {
    window.Echo.private('user.<?php echo e(Auth::id()); ?>')
        .notification((notification) => {
            handleNotification(notification);
        })
        .listen('.notification.created', (notification) => {
            handleNotification(notification);
        });
} else {
    console.warn('Echo non disponible - notifications temps reel desactivees');
}
<?php endif; ?>

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

    // Couleur selon le type
    let bgColor = 'bg-gradient-to-r from-blue-500 to-indigo-600';
    let icon = '🔔';
    let title = notification.title || 'Notification';

    if (notification.type) {
        if (notification.type.includes('new_message') || notification.type === 'App\\Notifications\\NewMessage') {
            bgColor = 'bg-gradient-to-r from-blue-500 to-blue-600';
            icon = '💬';
        } else if (notification.type.includes('new_order') || notification.type === 'App\\Notifications\\NewOrder') {
            bgColor = 'bg-gradient-to-r from-green-500 to-emerald-600';
            icon = '🛒';
        } else if (notification.type.includes('discount') || notification.type === 'App\\Notifications\\DiscountApplied') {
            bgColor = 'bg-gradient-to-r from-purple-500 to-purple-600';
            icon = '🏷️';
        } else if (notification.type.includes('refund')) {
            bgColor = 'bg-gradient-to-r from-amber-500 to-orange-600';
            icon = '💰';
        } else if (notification.type.includes('item_favorited')) {
            bgColor = 'bg-gradient-to-r from-pink-500 to-rose-600';
            icon = '❤️';
        } else if (notification.type.includes('Approved')) {
            bgColor = 'bg-gradient-to-r from-green-500 to-green-600';
            icon = '✅';
        } else if (notification.type.includes('Rejected')) {
            bgColor = 'bg-gradient-to-r from-red-500 to-red-600';
            icon = '❌';
        } else if (notification.type.includes('wallet')) {
            bgColor = 'bg-gradient-to-r from-emerald-500 to-teal-600';
            icon = '💳';
        }
    }

    const actionUrl = notification.data?.url || '#';

    const toast = document.createElement('div');
    toast.id = notifId;
    toast.className = `${bgColor} text-white rounded-xl shadow-2xl p-4 transform transition-all duration-300 pointer-events-auto animate-slide-in-right cursor-pointer hover:shadow-3xl`;
    toast.onclick = function() {
        if (actionUrl !== '#') {
            window.location.href = actionUrl;
        }
        closeNotification(notifId);
    };

    toast.innerHTML = `
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0 text-2xl">${icon}</div>
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
<?php endif; ?>
<?php /**PATH /home/aizen/Bureau/sky/vintApp/resources/views/components/notifications-realtime.blade.php ENDPATH**/ ?>