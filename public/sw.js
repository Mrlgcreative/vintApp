// Service Worker pour notifications
self.addEventListener('install', function(event) {
    console.log('🔧 Service Worker installé');
    self.skipWaiting();
});

self.addEventListener('activate', function(event) {
    console.log('✅ Service Worker activé');
    event.waitUntil(self.clients.claim());
});

// Écouter les messages push
self.addEventListener('push', function(event) {
    console.log('📨 Push reçu:', event);
    
    if (!event.data) {
        return;
    }

    const data = event.data.json();
    
    const options = {
        body: data.message || 'Nouveau message',
        icon: '/favicon.ico',
        badge: '/favicon.ico',
        vibrate: [200, 100, 200],
        data: {
            url: data.url || '/',
            notificationId: data.id
        },
        actions: [
            {
                action: 'view',
                title: 'Voir',
                icon: '/favicon.ico'
            },
            {
                action: 'close',
                title: 'Fermer'
            }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(data.title || 'VintApp', options)
    );
});

// Gérer les clics sur les notifications
self.addEventListener('notificationclick', function(event) {
    console.log('🖱️ Notification cliquée:', event);
    
    event.notification.close();
    
    if (event.action === 'close') {
        return;
    }
    
    const url = event.notification.data.url || '/';
    
    event.waitUntil(
        clients.matchAll({ type: 'window' }).then(function(clientList) {
            for (let client of clientList) {
                if (client.url === url && 'focus' in client) {
                    return client.focus();
                }
            }
            
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});