<!-- Notifications Toast Container -->
<div id="notifications-container" 
     class="fixed top-20 right-4 z-50 space-y-3 max-w-md w-full pointer-events-none"
     style="max-height: calc(100vh - 100px); overflow-y: auto;">
</div>

@auth
<script>
// Configuration Pusher/Echo pour les notifications temps réel
@if(config('broadcasting.default') === 'pusher')
window.Echo.private('user.{{ Auth::id() }}')
    .notification((notification) => {
        console.log('Notification reçue:', notification);
        showNotification(notification);
        
        // Jouer un son si disponible
        playNotificationSound();
        
        // Mettre à jour le badge de notifications
        updateNotificationBadge();
    });
@endif

/**
 * Affiche une notification toast
 */
function showNotification(notification) {
    const container = document.getElementById('notifications-container');
    if (!container) return;
    
    const notifId = 'notif-' + Date.now();
    const isApproved = notification.type === 'App\\Notifications\\ItemApproved';
    
    const bgColor = isApproved 
        ? 'bg-gradient-to-r from-green-500 to-green-600' 
        : 'bg-gradient-to-r from-red-500 to-red-600';
    
    const icon = isApproved ? '✅' : '❌';
    const title = isApproved ? 'Article Approuvé !' : 'Article Rejeté';
    
    const toast = document.createElement('div');
    toast.id = notifId;
    toast.className = `${bgColor} text-white rounded-xl shadow-2xl p-4 transform transition-all duration-300 pointer-events-auto animate-slide-in-right`;
    
    toast.innerHTML = `
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0 text-3xl">${icon}</div>
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between mb-1">
                    <h4 class="font-bold text-lg">${title}</h4>
                    <button onclick="closeNotification('${notifId}')" 
                            class="text-white hover:text-gray-200 transition ml-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-white/90 mb-2">${notification.message || notification.data?.message || ''}</p>
                ${notification.data?.item_image ? `
                    <div class="mb-2">
                        <img src="/storage/${notification.data.item_image}" 
                             class="w-16 h-16 object-cover rounded-lg border-2 border-white/30"
                             alt="Article">
                    </div>
                ` : ''}
                ${isApproved && notification.data?.verification_score ? `
                    <div class="flex items-center space-x-2 text-xs text-white/80 mb-2">
                        <span>Score IA: <strong>${notification.data.verification_score}/100</strong></span>
                    </div>
                ` : ''}
                ${!isApproved && notification.data?.reason ? `
                    <div class="bg-white/20 rounded p-2 text-xs mb-2">
                        <strong>Raison:</strong> ${notification.data.reason}
                    </div>
                ` : ''}
                <div class="flex items-center space-x-2 mt-2">
                    ${notification.data?.item_id ? `
                        <a href="/items/${notification.data.item_id}" 
                           class="px-3 py-1 bg-white text-${isApproved ? 'green' : 'red'}-600 rounded-lg text-sm font-medium hover:bg-gray-100 transition">
                            Voir l'article
                        </a>
                    ` : ''}
                    ${!isApproved && notification.data?.item_id ? `
                        <a href="/items/${notification.data.item_id}/edit" 
                           class="px-3 py-1 bg-white/20 text-white rounded-lg text-sm hover:bg-white/30 transition">
                            Modifier
                        </a>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
    
    container.insertBefore(toast, container.firstChild);
    
    // Auto-fermeture après 10 secondes
    setTimeout(() => {
        closeNotification(notifId);
    }, 10000);
}

/**
 * Ferme une notification
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
 * Joue un son de notification
 */
function playNotificationSound() {
    try {
        const audio = new Audio('/sounds/notification.mp3');
        audio.volume = 0.3;
        audio.play().catch(e => console.log('Son désactivé:', e));
    } catch (e) {
        // Silencieux si le son n'est pas disponible
    }
}

/**
 * Met à jour le badge de compteur de notifications
 */
function updateNotificationBadge() {
    // Si vous avez un badge dans le header
    const badge = document.getElementById('notification-badge');
    if (badge) {
        const currentCount = parseInt(badge.textContent || '0');
        badge.textContent = currentCount + 1;
        badge.classList.remove('hidden');
    }
}

// Animation CSS pour le slide-in
const style = document.createElement('style');
style.textContent = `
    @keyframes slide-in-right {
        0% {
            opacity: 0;
            transform: translateX(100%);
        }
        100% {
            opacity: 1;
            transform: translateX(0);
        }
    }
    .animate-slide-in-right {
        animation: slide-in-right 0.4s ease-out;
    }
`;
document.head.appendChild(style);
</script>
@endauth
