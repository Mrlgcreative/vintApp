/**
 * Firebase Push Notification Manager
 * Gestion des permissions et abonnements aux notifications push
 */

class PushNotificationManager {
    constructor() {
        this.messaging = null;
        this.currentToken = null;
        this.isSupported = 'Notification' in window && 'serviceWorker' in navigator;
        this.vapidKey = 'BAE5dM7Fc4f3s7H5Isru52xxcR60apO46k9IuFcMni04qZW3iqbIjjhwP7gle-mWQ1vGyPxZ0i3SWvn1Q3UKnoE';
    }

    /**
     * Initialiser Firebase Messaging
     */
    async init() {
        if (!this.isSupported) {
            return false;
        }

        try {
            // Importer Firebase dynamiquement
            const { initializeApp } = await import('https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js');
            const { getMessaging, getToken, onMessage } = await import('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js');

            // Initialiser Firebase
            const firebaseConfig = {
                apiKey: "AIzaSyBe0WQbkZ0A3Cz9vKyQWsE-edxLfWrV1_E",
                authDomain: "vintapp-e6fa7.firebaseapp.com",
                projectId: "vintapp-e6fa7",
                storageBucket: "vintapp-e6fa7.appspot.com",
                messagingSenderId: "880178183981",
                appId: "1:880178183981:web:395604645bd7d758a35da4"
            };

            const app = initializeApp(firebaseConfig);
            this.messaging = getMessaging(app);

            console.log('✅ Firebase Messaging initialisé');

            // Écouter les messages en foreground
            onMessage(this.messaging, (payload) => {
                console.log('📬 Message reçu (foreground):', payload);
                this.showForegroundNotification(payload);
            });

            return true;
        } catch (error) {
            console.error('❌ Erreur init Firebase Messaging:', error);
            return false;
        }
    }

    /**
     * Vérifier le statut de la permission
     */
    getPermissionStatus() {
        if (!this.isSupported) return 'unsupported';
        return Notification.permission;
    }

    /**
     * Demander la permission pour les notifications
     */
    async requestPermission() {
        if (!this.isSupported) {
            throw new Error('Notifications non supportées sur ce navigateur');
        }

        const permission = await Notification.requestPermission();

        if (permission === 'granted') {
            await this.subscribeToNotifications();
        }

        return permission;
    }

    /**
     * S'abonner aux notifications push
     */
    async subscribeToNotifications() {
        try {
            const { getToken } = await import('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js');
            
            const token = await getToken(this.messaging, {
                vapidKey: this.vapidKey,
                serviceWorkerRegistration: await navigator.serviceWorker.ready
            });

            if (token) {
                console.log('✅ FCM Token obtenu:', token);
                this.currentToken = token;
                await this.saveTokenToServer(token);
                return token;
            } else {
                console.warn('⚠️ Aucun token FCM disponible');
                return null;
            }
        } catch (error) {
            console.error('❌ Erreur abonnement notifications:', error);
            throw error;
        }
    }

    /**
     * Se désabonner des notifications
     */
    async unsubscribe() {
        try {
            const { deleteToken } = await import('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js');
            
            await deleteToken(this.messaging);
            await this.removeTokenFromServer();
            
            this.currentToken = null;
            console.log('✅ Désabonnement réussi');
            return true;
        } catch (error) {
            console.error('❌ Erreur désabonnement:', error);
            return false;
        }
    }

    /**
     * Sauvegarder le token sur le serveur
     */
    async saveTokenToServer(token) {
        try {
            const response = await fetch('/api/notifications/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ 
                    token,
                    device_type: this.getDeviceType(),
                    browser: this.getBrowserInfo()
                })
            });

            if (!response.ok) {
                throw new Error('Erreur sauvegarde token');
            }

            const data = await response.json();
            console.log('✅ Token sauvegardé sur serveur:', data);
            localStorage.setItem('fcm_token', token);
            return data;
        } catch (error) {
            console.error('❌ Erreur sauvegarde token serveur:', error);
            throw error;
        }
    }

    /**
     * Supprimer le token du serveur
     */
    async removeTokenFromServer() {
        try {
            const response = await fetch('/api/notifications/unsubscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ 
                    token: this.currentToken || localStorage.getItem('fcm_token')
                })
            });

            localStorage.removeItem('fcm_token');
            console.log('✅ Token supprimé du serveur');
        } catch (error) {
            console.error('❌ Erreur suppression token serveur:', error);
        }
    }

    /**
     * Afficher une notification en foreground
     */
    showForegroundNotification(payload) {
        const notificationTitle = payload.notification?.title || 'VintApp';
        const notificationOptions = {
            body: payload.notification?.body || '',
            icon: payload.notification?.icon || '/images/icons/icon-192x192.png',
            badge: '/images/icons/icon-72x72.png',
            image: payload.notification?.image,
            data: payload.data,
            tag: 'vintapp-foreground',
            requireInteraction: true,
            vibrate: [200, 100, 200]
        };

        // Afficher notification système
        if (Notification.permission === 'granted') {
            new Notification(notificationTitle, notificationOptions);
        }

        // Afficher notification in-app
        this.showInAppNotification(notificationTitle, notificationOptions.body, payload.data?.url);
    }

    /**
     * Afficher notification in-app
     */
    showInAppNotification(title, message, url = null) {
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 z-50 max-w-sm bg-white rounded-lg shadow-2xl p-4 transform transition-all duration-300 translate-x-full';
        notification.innerHTML = `
            <div class="flex items-start gap-3">
                <img src="/images/icons/icon-72x72.png" alt="VintApp" class="w-12 h-12 rounded-lg flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 text-sm mb-1">${this.escapeHtml(title)}</h3>
                    <p class="text-gray-600 text-xs">${this.escapeHtml(message)}</p>
                    ${url ? `<a href="${this.escapeHtml(url)}" class="inline-block mt-2 text-xs text-purple-600 hover:text-purple-700 font-medium">Voir →</a>` : ''}
                </div>
                <button class="text-gray-400 hover:text-gray-600 flex-shrink-0" onclick="this.closest('div').remove()">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        `;

        document.body.appendChild(notification);

        // Animer l'entrée
        setTimeout(() => notification.classList.remove('translate-x-full'), 100);

        // Auto-supprimer après 5 secondes
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    /**
     * Afficher UI de demande de permission
     */
    showPermissionPrompt() {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all">
                <div class="text-center">
                    <div class="mx-auto w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Activer les notifications</h3>
                    <p class="text-gray-600 mb-6">Restez informé de vos commandes, nouveaux messages et mises à jour importantes.</p>
                    
                    <div class="space-y-2 mb-6 text-left text-sm">
                        <div class="flex items-start gap-2">
                            <span class="text-green-500 mt-0.5">✓</span>
                            <span class="text-gray-700">Nouvelle commande confirmée</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-green-500 mt-0.5">✓</span>
                            <span class="text-gray-700">Message d'un acheteur</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-green-500 mt-0.5">✓</span>
                            <span class="text-gray-700">Article vendu</span>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button id="notif-deny" class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition">
                            Plus tard
                        </button>
                        <button id="notif-allow" class="flex-1 px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium transition">
                            Activer
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Gestion des boutons
        modal.querySelector('#notif-allow').addEventListener('click', async () => {
            modal.remove();
            await this.requestPermission();
        });

        modal.querySelector('#notif-deny').addEventListener('click', () => {
            modal.remove();
            localStorage.setItem('notification-prompt-dismissed', Date.now());
        });
    }

    /**
     * Vérifier si on doit afficher le prompt
     */
    shouldShowPrompt() {
        const dismissed = localStorage.getItem('notification-prompt-dismissed');
        if (dismissed) {
            const daysSinceDismiss = (Date.now() - parseInt(dismissed)) / (1000 * 60 * 60 * 24);
            if (daysSinceDismiss < 7) return false; // Redemander après 7 jours
        }

        return this.getPermissionStatus() === 'default';
    }

    /**
     * Helper: Détecter le type d'appareil
     */
    getDeviceType() {
        const ua = navigator.userAgent;
        if (/mobile/i.test(ua)) return 'mobile';
        if (/tablet/i.test(ua)) return 'tablet';
        return 'desktop';
    }

    /**
     * Helper: Info navigateur
     */
    getBrowserInfo() {
        const ua = navigator.userAgent;
        if (ua.includes('Chrome')) return 'Chrome';
        if (ua.includes('Firefox')) return 'Firefox';
        if (ua.includes('Safari')) return 'Safari';
        if (ua.includes('Edge')) return 'Edge';
        return 'Other';
    }

    /**
     * Helper: Échapper HTML
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Instance globale
const pushManager = new PushNotificationManager();

// Auto-init après chargement
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => pushManager.init());
} else {
    pushManager.init();
}

// Afficher prompt après 30 secondes si pas encore de permission
setTimeout(() => {
    if (pushManager.shouldShowPrompt()) {
        pushManager.showPermissionPrompt();
    }
}, 30000);

// Export pour utilisation globale
window.pushManager = pushManager;
