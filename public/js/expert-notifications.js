/**
 * Expert Notifications System
 * Gère les notifications en temps réel et les notifications push FCM
 */

class ExpertNotificationManager {
    constructor() {
        this.fcmToken = null;
        this.unreadCount = 0;
        this.isExpert = document.querySelector('meta[data-is-expert]')?.content === 'true';
        
        if (this.isExpert) {
            this.init();
        }
    }

    init() {
        console.log('🔔 Initializing Expert Notification Manager');
        
        // Initialiser FCM
        this.initFCM();
        
        // Initialiser les listeners WebSocket
        this.initWebSocketListeners();
        
        // Charger les notifications initiales
        this.fetchNotifications();
        
        // Rafraîchir toutes les 30 secondes
        setInterval(() => this.fetchNotifications(), 30000);
    }

    /**
     * Initialiser Firebase Cloud Messaging
     */
    initFCM() {
        if (!('serviceWorker' in navigator) || !window.firebase) {
            console.warn('⚠️ Service Worker ou Firebase non disponible');
            return;
        }

        try {
            // Demander la permission pour les notifications
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    console.log('✅ Notification permission granted');
                    this.registerFCMToken();
                } else {
                    console.log('❌ Notification permission denied');
                }
            });

            // Écouter les messages FCM
            const messaging = firebase.messaging();
            
            messaging.onMessage((payload) => {
                console.log('📬 Message reçu:', payload);
                this.handleFCMMessage(payload);
            });

        } catch (error) {
            console.error('Error initializing FCM:', error);
        }
    }

    /**
     * Enregistrer le token FCM
     */
    registerFCMToken() {
        if (!window.firebase) return;

        const messaging = firebase.messaging();
        
        messaging.getToken({
            vapidKey: window.FIREBASE_CONFIG?.vapidKey
        }).then(token => {
            if (token) {
                console.log('🔑 FCM Token obtained:', token.substring(0, 20) + '...');
                this.fcmToken = token;
                this.sendFCMTokenToServer(token);
            }
        }).catch(error => {
            console.error('Error getting FCM token:', error);
        });
    }

    /**
     * Envoyer le token FCM au serveur
     */
    sendFCMTokenToServer(token) {
        fetch('/expert/fcm-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ fcm_token: token })
        }).then(response => {
            if (response.ok) {
                console.log('✅ FCM token sent to server');
            }
        }).catch(error => {
            console.error('Error sending FCM token:', error);
        });
    }

    /**
     * Traiter les messages FCM
     */
    handleFCMMessage(payload) {
        const { notification, data } = payload;
        
        console.log('🔔 Handling FCM message:', notification);
        
        // Afficher la notification du navigateur
        if (notification) {
            new Notification(notification.title || 'VintApp', {
                body: notification.body,
                icon: notification.icon || '/images/icons/icon-192x192.png',
                badge: '/images/icons/icon-72x72.png',
                tag: 'expert-notification',
                data: data || {},
                requireInteraction: true
            });
        }

        // Rafraîchir les notifications
        this.fetchNotifications();
    }

    /**
     * Initialiser les listeners WebSocket
     */
    initWebSocketListeners() {
        // Écouter l'événement broadcast de nouvelles notifications
        if (window.Echo) {
            const userId = document.querySelector('meta[data-user-id]')?.content;
            
            if (userId) {
                window.Echo.private(`expert.${userId}`)
                    .listen('ItemPendingForVerification', (e) => {
                        console.log('🎉 Nouvel article en attente:', e);
                        this.handleNewItemNotification(e);
                    })
                    .listen('item.pending', (e) => {
                        console.log('📢 Notification broadcast reçue:', e);
                        this.handleNewItemNotification(e);
                    });

                window.Echo.private('expert.notifications')
                    .listen('.', (e) => {
                        console.log('📣 Notification générale:', e);
                        this.fetchNotifications();
                    });
            }
        }
    }

    /**
     * Traiter une notification d'article en attente
     */
    handleNewItemNotification(data) {
        console.log('🔔 Nouvelle notification d\'article:', data);
        
        // Mettre à jour l'interface
        this.fetchNotifications();

        // Jouer un son si autorisé
        this.playNotificationSound();

        // Afficher une notification navigateur
        if (Notification.permission === 'granted') {
            new Notification('Nouvel article à vérifier', {
                body: data.message || `Article: ${data.item_name}`,
                icon: data.item_image || '/images/icons/icon-192x192.png',
                badge: '/images/icons/icon-72x72.png',
                tag: `item-${data.item_id}`,
                requireInteraction: true
            });
        }
    }

    /**
     * Charger les notifications depuis le serveur
     */
    fetchNotifications() {
        fetch('/expert/notifications', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            this.unreadCount = data.unread_count;
            this.updateNotificationUI(data);
        })
        .catch(error => console.error('Error fetching notifications:', error));
    }

    /**
     * Mettre à jour l'interface des notifications
     */
    updateNotificationUI(data) {
        const badge = document.getElementById('notification-badge');
        const container = document.getElementById('notifications-container');

        if (!badge || !container) return;

        // Mettre à jour le badge
        if (data.unread_count > 0) {
            badge.textContent = data.unread_count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }

        // Mettre à jour le conteneur
        if (data.notifications.length === 0) {
            container.innerHTML = '<div class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Aucune notification</div>';
            return;
        }

        container.innerHTML = data.notifications.map(notif => `
            <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-b-0 ${!notif.read ? 'bg-blue-50 dark:bg-blue-900/20' : ''}" data-notification-id="${notif.id}">
                <a href="${notif.action_url || '#'}" class="block cursor-pointer" onclick="expertNotificationManager.handleNotificationClick(event, ${notif.id})">
                    <div class="flex items-start gap-3">
                        <i class="fas ${notif.icon} text-primary-500 mt-1 flex-shrink-0"></i>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-sm text-gray-900 dark:text-white">${notif.title}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">${notif.message}</div>
                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">${notif.created_at}</div>
                        </div>
                        ${!notif.read ? '<div class="flex-shrink-0 w-2 h-2 bg-primary-500 rounded-full"></div>' : ''}
                    </div>
                </a>
            </div>
        `).join('');
    }

    /**
     * Gérer le clic sur une notification
     */
    handleNotificationClick(event, notificationId) {
        event.preventDefault();
        
        // Marquer comme lue
        this.markAsRead(notificationId);

        // Naviguer vers l'URL
        const notification = document.querySelector(`[data-notification-id="${notificationId}"]`);
        const link = notification?.querySelector('a');
        if (link?.href && link.href !== '#') {
            window.location.href = link.href;
        }
    }

    /**
     * Marquer une notification comme lue
     */
    markAsRead(notificationId) {
        fetch(`/expert/notifications/${notificationId}/read`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        }).then(() => {
            this.fetchNotifications();
        });
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    markAllAsRead() {
        fetch('/expert/notifications/read-all', {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        }).then(() => {
            this.fetchNotifications();
        });
    }

    /**
     * Jouer un son de notification
     */
    playNotificationSound() {
        try {
            const audio = new Audio('/sounds/notification.mp3');
            audio.volume = 0.5;
            audio.play().catch(error => {
                console.log('Audio not allowed:', error);
            });
        } catch (error) {
            console.log('Error playing sound:', error);
        }
    }
}

// Initialiser le gestionnaire de notifications
const expertNotificationManager = new ExpertNotificationManager();

// Exposer globalement pour les event handlers
window.expertNotificationManager = expertNotificationManager;
