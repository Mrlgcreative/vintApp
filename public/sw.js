/**
 * VintApp Service Worker
 * Version: 1.0.0
 * 
 * Gestion du cache offline et stratégies de mise en cache
 */

const CACHE_VERSION = 'vintapp-v1.0.0';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const DYNAMIC_CACHE = `${CACHE_VERSION}-dynamic`;
const IMAGE_CACHE = `${CACHE_VERSION}-images`;

// Ressources à mettre en cache immédiatement
const STATIC_ASSETS = [
    '/',
    '/offline',
    '/manifest.json',
    '/favicon.ico',
];

// Durée de vie du cache (7 jours)
const CACHE_LIFETIME = 7 * 24 * 60 * 60 * 1000;

/**
 * Installation du Service Worker
 */
self.addEventListener('install', (event) => {
    console.log('🔧 Service Worker: Installation...');
    
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => {
                console.log('📦 Service Worker: Mise en cache des assets statiques');
                // Mettre en cache les assets individuellement pour éviter les échecs
                return Promise.allSettled(
                    STATIC_ASSETS.map(url => 
                        cache.add(url).catch(err => {
                            console.warn(`⚠️ Impossible de mettre en cache: ${url}`, err);
                            return null;
                        })
                    )
                );
            })
            .then(() => {
                console.log('✅ Service Worker: Installation terminée');
                return self.skipWaiting();
            })
            .catch((error) => {
                console.error('❌ Service Worker: Erreur installation', error);
                // Continuer quand même l'installation
                return self.skipWaiting();
            })
    );
});

/**
 * Activation du Service Worker
 */
self.addEventListener('activate', (event) => {
    console.log('🚀 Service Worker: Activation...');
    
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames
                        .filter((cacheName) => {
                            return cacheName.startsWith('vintapp-') && 
                                   cacheName !== STATIC_CACHE &&
                                   cacheName !== DYNAMIC_CACHE &&
                                   cacheName !== IMAGE_CACHE;
                        })
                        .map((cacheName) => {
                            console.log('🗑️ Service Worker: Suppression cache obsolète', cacheName);
                            return caches.delete(cacheName);
                        })
                );
            })
            .then(() => {
                console.log('✅ Service Worker: Activation terminée');
                return self.clients.claim();
            })
    );
});

/**
 * Message handler pour SKIP_WAITING
 */
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        console.log('⏭️ Service Worker: SKIP_WAITING reçu');
        self.skipWaiting();
    }
});

/**
 * Interception des requêtes réseau
 */
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    if (request.method !== 'GET' || !url.origin.includes(self.location.origin)) {
        return;
    }

    if (isImageRequest(url)) {
        event.respondWith(cacheFirstStrategy(request, IMAGE_CACHE));
    } else if (isStaticAsset(url)) {
        event.respondWith(cacheFirstStrategy(request, STATIC_CACHE));
    } else if (isAPIRequest(url)) {
        event.respondWith(networkFirstStrategy(request, DYNAMIC_CACHE));
    } else {
        event.respondWith(networkFirstStrategy(request, DYNAMIC_CACHE));
    }
});

/**
 * Stratégie Cache First
 */
async function cacheFirstStrategy(request, cacheName) {
    try {
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            const cacheDate = new Date(cachedResponse.headers.get('date'));
            const now = new Date();
            
            if (now - cacheDate < CACHE_LIFETIME) {
                return cachedResponse;
            }
        }

        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(cacheName);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (error) {
        const cachedResponse = await caches.match(request);
        if (cachedResponse) return cachedResponse;

        if (request.destination === 'document') {
            return caches.match('/offline');
        }

        return new Response('Ressource non disponible', { status: 503 });
    }
}

/**
 * Stratégie Network First
 */
async function networkFirstStrategy(request, cacheName) {
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(cacheName);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (error) {
        const cachedResponse = await caches.match(request);
        if (cachedResponse) return cachedResponse;

        if (request.destination === 'document') {
            return caches.match('/offline');
        }

        return new Response('Ressource non disponible', { status: 503 });
    }
}

function isImageRequest(url) {
    return /\.(jpg|jpeg|png|gif|webp|svg|ico)$/i.test(url.pathname) ||
           url.pathname.includes('/storage/');
}

function isStaticAsset(url) {
    return /\.(css|js|woff|woff2)$/i.test(url.pathname) ||
           url.pathname.startsWith('/build/');
}

function isAPIRequest(url) {
    return url.pathname.startsWith('/api/');
}

/**
 * Notifications Push - Configuration avancée
 */
self.addEventListener('push', (event) => {
    console.log('📬 Push reçu:', event);
    
    if (!event.data) {
        console.log('❌ Push sans données');
        return;
    }

    let data;
    try {
        data = event.data.json();
    } catch (e) {
        console.warn('⚠️ Données push non-JSON:', e);
        data = {
            title: 'VintApp',
            body: event.data.text(),
            icon: '/images/icons/icon-192x192.png'
        };
    }

    const title = data.title || 'VintApp';
    const options = {
        body: data.body || 'Vous avez une nouvelle notification',
        icon: data.icon || '/images/icons/icon-192x192.png',
        badge: '/images/icons/icon-72x72.png',
        image: data.image,
        data: {
            url: data.url || '/',
            orderId: data.orderId,
            type: data.type,
            ...data.data
        },
        tag: data.tag || 'vintapp-notification',
        requireInteraction: data.requireInteraction || false,
        actions: [
            { action: 'view', title: '👁️ Voir', icon: '/images/icons/icon-72x72.png' },
            { action: 'close', title: '❌ Fermer' }
        ],
        vibrate: [200, 100, 200, 100, 200],
        timestamp: Date.now(),
        silent: false,
        renotify: true
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
            .then(() => console.log('✅ Notification affichée:', title))
            .catch(err => console.error('❌ Erreur notification:', err))
    );
});

self.addEventListener('notificationclick', (event) => {
    console.log('🖱️ Click notification:', event.action, event.notification.tag);
    
    event.notification.close();

    if (event.action === 'close') {
        console.log('🚪 Notification fermée par l\'utilisateur');
        return;
    }

    const urlToOpen = event.notification.data?.url || '/';
    const orderId = event.notification.data?.orderId;
    const finalUrl = orderId ? `/orders/${orderId}` : urlToOpen;

    console.log('🔗 Navigation vers:', finalUrl);

    event.waitUntil(
        clients.matchAll({ 
            type: 'window', 
            includeUncontrolled: true 
        }).then((clientList) => {
            for (const client of clientList) {
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    console.log('🎯 Focus sur fenêtre existante');
                    return client.focus()
                        .then(() => client.navigate(finalUrl))
                        .catch(() => clients.openWindow(finalUrl));
                }
            }
            
            if (clients.openWindow) {
                console.log('🆕 Ouverture nouvelle fenêtre');
                return clients.openWindow(finalUrl);
            }
        }).catch(err => console.error('❌ Erreur click notification:', err))
    );
});

self.addEventListener('notificationclose', (event) => {
    console.log('🔕 Notification fermée:', event.notification.tag);
    
    event.waitUntil(
        fetch('/api/notifications/closed', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                tag: event.notification.tag,
                timestamp: Date.now()
            })
        }).catch(() => {})
    );
});

/**
 * Background Sync
 * Synchroniser les requêtes en attente quand la connexion est rétablie
 */
self.addEventListener('sync', (event) => {
    console.log('🔄 Service Worker: Événement sync -', event.tag);

    if (event.tag === 'sync-pending-requests') {
        event.waitUntil(syncPendingRequests());
    }
});

/**
 * Synchroniser les requêtes en attente depuis IndexedDB
 */
async function syncPendingRequests() {
    try {
        console.log('🔄 Synchronisation des requêtes en attente...');

        // Ouvrir IndexedDB
        const db = await openDatabase();
        const requests = await getAllPendingRequests(db);

        if (requests.length === 0) {
            console.log('ℹ️ Aucune requête en attente');
            return;
        }

        console.log(`📋 ${requests.length} requête(s) à synchroniser`);

        let successCount = 0;
        let failCount = 0;

        for (const req of requests) {
            try {
                await executeRequest(req);
                await deleteRequest(db, req.id);
                successCount++;
                console.log(`✅ Requête ${req.id} synchronisée`);
            } catch (error) {
                console.error(`❌ Erreur requête ${req.id}:`, error);
                
                // Incrémenter le retry count
                req.retryCount++;
                
                if (req.retryCount >= req.maxRetries) {
                    await deleteRequest(db, req.id);
                    failCount++;
                    console.log(`❌ Requête ${req.id} abandonnée`);
                } else {
                    await updateRequest(db, req);
                    failCount++;
                }
            }
        }

        // Notifier l'utilisateur
        if (successCount > 0) {
            self.registration.showNotification('Synchronisation terminée', {
                body: `${successCount} action(s) synchronisée(s) avec succès`,
                icon: '/images/icons/icon-192x192.png',
                badge: '/images/icons/badge-72x72.png',
                tag: 'sync-complete'
            });
        }

        console.log(`✅ Sync terminée: ${successCount} succès, ${failCount} échecs`);

    } catch (error) {
        console.error('❌ Erreur synchronisation:', error);
        throw error;
    }
}

/**
 * Ouvrir la base de données IndexedDB
 */
function openDatabase() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('vintapp-sync', 1);
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

/**
 * Récupérer toutes les requêtes en attente
 */
function getAllPendingRequests(db) {
    return new Promise((resolve, reject) => {
        const transaction = db.transaction(['pending-requests'], 'readonly');
        const store = transaction.objectStore('pending-requests');
        const request = store.getAll();
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

/**
 * Exécuter une requête
 */
async function executeRequest(req) {
    const options = {
        method: req.method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    };

    if (req.data && (req.method === 'POST' || req.method === 'PUT' || req.method === 'PATCH')) {
        options.body = JSON.stringify(req.data);
    }

    const response = await fetch(req.url, options);

    if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
    }

    return response.json();
}

/**
 * Supprimer une requête
 */
function deleteRequest(db, id) {
    return new Promise((resolve, reject) => {
        const transaction = db.transaction(['pending-requests'], 'readwrite');
        const store = transaction.objectStore('pending-requests');
        const request = store.delete(id);
        request.onsuccess = () => resolve();
        request.onerror = () => reject(request.error);
    });
}

/**
 * Mettre à jour une requête
 */
function updateRequest(db, req) {
    return new Promise((resolve, reject) => {
        const transaction = db.transaction(['pending-requests'], 'readwrite');
        const store = transaction.objectStore('pending-requests');
        const request = store.put(req);
        request.onsuccess = () => resolve();
        request.onerror = () => reject(request.error);
    });
}

console.log('🎯 Service Worker chargé - Version:', CACHE_VERSION);
