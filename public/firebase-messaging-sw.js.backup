/**
 * Firebase Cloud Messaging Service Worker
 * Gestion des notifications push en arrière-plan
 */

// Configuration Firebase
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "AIzaSyC0x3pmQewGWynoAbFsG9SiFbYjKxDYOrE",
    authDomain: "vintapp-e6fa7.firebaseapp.com",
    projectId: "vintapp-e6fa7",
    storageBucket: "vintapp-e6fa7.appspot.com",
    messagingSenderId: "880178183981",
    appId: "1:880178183981:web:deed0feb693e8c82a35da4"
});

const messaging = firebase.messaging();

// Gestion des messages en arrière-plan (app fermée)
messaging.onBackgroundMessage((payload) => {
    console.log('📬 Message reçu en arrière-plan:', payload);

    const notificationTitle = payload.notification?.title || payload.data?.title || 'VintApp';
    const notificationOptions = {
        body: payload.notification?.body || payload.data?.body || 'Vous avez une nouvelle notification',
        icon: payload.notification?.icon || payload.data?.icon || '/images/icons/icon-192x192.png',
        badge: '/images/icons/icon-72x72.png',
        image: payload.notification?.image || payload.data?.image,
        data: payload.data || {},
        tag: payload.data?.tag || 'vintapp-notification',
        requireInteraction: payload.data?.requireInteraction === 'true',
        actions: [
            {
                action: 'view',
                title: 'Voir',
                icon: '/images/icons/icon-72x72.png'
            },
            {
                action: 'close',
                title: 'Fermer'
            }
        ],
        vibrate: [200, 100, 200],
        timestamp: Date.now()
    };

    return self.registration.showNotification(notificationTitle, notificationOptions);
});

// Gestion des clics sur les notifications
self.addEventListener('notificationclick', (event) => {
    console.log('🖱️ Click sur notification:', event.notification.tag);
    
    event.notification.close();

    if (event.action === 'close') {
        return;
    }

    // Récupérer l'URL de destination depuis les données
    const urlToOpen = event.notification.data?.url || event.notification.data?.click_action || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // Si une fenêtre VintApp est déjà ouverte, la focus
                for (const client of clientList) {
                    if (client.url.includes(self.location.origin) && 'focus' in client) {
                        client.focus();
                        client.navigate(urlToOpen);
                        return;
                    }
                }
                
                // Sinon, ouvrir une nouvelle fenêtre
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

// Gestion des erreurs de push
self.addEventListener('push', (event) => {
    if (!event.data) {
        console.log('❌ Push reçu sans données');
        return;
    }

    console.log('📨 Push reçu:', event.data.text());
});

// Logs de démarrage
console.log('🔥 Firebase Messaging Service Worker chargé');
