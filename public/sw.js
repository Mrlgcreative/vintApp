/**
 * VintApp Service Worker
 * Version: 1.0.3
 * 
 * Gestion du cache offline, stratégies de mise en cache et Firebase Cloud Messaging
 */

// Importer Firebase pour les notifications push (optionnel - ne bloque pas l'installation)
try {
    importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
    importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');
    
    firebase.initializeApp({
        apiKey: "AIzaSyC0x3pmQewGWynoAbFsG9SiFbYjKxDYOrE",
        authDomain: "vintapp-e6fa7.firebaseapp.com",
        projectId: "vintapp-e6fa7",
        storageBucket: "vintapp-e6fa7.appspot.com",
        messagingSenderId: "880178183981",
        appId: "1:880178183981:web:395604645bd7d758a35da4"
    });
    
    const messaging = firebase.messaging();
    
    // Gestion des messages en arrière-plan
    messaging.onBackgroundMessage((payload) => {
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
                { action: 'view', title: 'Voir', icon: '/images/icons/icon-72x72.png' },
                { action: 'close', title: 'Fermer' }
            ],
            vibrate: [200, 100, 200],
            timestamp: Date.now()
        };

        return self.registration.showNotification(notificationTitle, notificationOptions);
    });
} catch (error) {
    // Firebase Messaging non disponible (mode offline/dégradé)
}

const CACHE_VERSION = 'vintapp-v1.0.3';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const DYNAMIC_CACHE = `${CACHE_VERSION}-dynamic`;
const IMAGE_CACHE = `${CACHE_VERSION}-images`;

// Ressources à mettre en cache immédiatement
const STATIC_ASSETS = [
    '/',
];

// Durée de vie du cache (7 jours)
const CACHE_LIFETIME = 7 * 24 * 60 * 60 * 1000;

/**
 * Installation du Service Worker
 */
self.addEventListener('install', (event) => {
    // Forcer l'installation immédiate sans mettre en cache
    event.waitUntil(
        Promise.resolve()
            .then(() => {
                return self.skipWaiting();
            })
            .catch((error) => {
                // Continuer quand même
                return self.skipWaiting();
            })
    );
});

/**
 * Activation du Service Worker
 */
self.addEventListener('activate', (event) => {
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
                            return caches.delete(cacheName);
                        })
                );
            })
            .then(() => {
                return self.clients.claim();
            })
    );
});

/**
 * Message handler pour SKIP_WAITING
 */
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
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
            // Essayer de retourner la page d'accueil au lieu de /offline
            const homeResponse = await caches.match('/');
            if (homeResponse) return homeResponse;
        }

        return new Response('Ressource non disponible hors ligne', { 
            status: 503,
            statusText: 'Service Unavailable',
            headers: { 'Content-Type': 'text/plain' }
        });
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
            // Essayer de retourner la page d'accueil au lieu de /offline
            const homeResponse = await caches.match('/');
            if (homeResponse) return homeResponse;
        }

        return new Response('Ressource non disponible hors ligne', { 
            status: 503,
            statusText: 'Service Unavailable',
            headers: { 'Content-Type': 'text/plain' }
        });
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
    if (!event.data) {
        return;
    }

    let data;
    try {
        data = event.data.json();
    } catch (e) {
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
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    if (event.action === 'close') {
        return;
    }

    const urlToOpen = event.notification.data?.url || '/';
    const orderId = event.notification.data?.orderId;
    const finalUrl = orderId ? `/orders/${orderId}` : urlToOpen;

    event.waitUntil(
        clients.matchAll({ 
            type: 'window', 
            includeUncontrolled: true 
        }).then((clientList) => {
            for (const client of clientList) {
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    return client.focus()
                        .then(() => client.navigate(finalUrl))
                        .catch(() => clients.openWindow(finalUrl));
                }
            }
            
            if (clients.openWindow) {
                return clients.openWindow(finalUrl);
            }
        }).catch(() => {})
    );
});

self.addEventListener('notificationclose', (event) => {
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
    if (event.tag === 'sync-pending-requests') {
        event.waitUntil(syncPendingRequests());
    }
});

/**
 * Synchroniser les requêtes en attente depuis IndexedDB
 */
async function syncPendingRequests() {
    try {
        // Ouvrir IndexedDB
        const db = await openDatabase();
        const requests = await getAllPendingRequests(db);

        if (requests.length === 0) {
            return;
        }

        let successCount = 0;
        let failCount = 0;

        for (const req of requests) {
            try {
                await executeRequest(req);
                await deleteRequest(db, req.id);
                successCount++;
            } catch (error) {
                // Incrémenter le retry count
                req.retryCount++;
                
                if (req.retryCount >= req.maxRetries) {
                    await deleteRequest(db, req.id);
                    failCount++;
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

    } catch (error) {
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
