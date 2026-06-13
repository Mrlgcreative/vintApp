<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Push Notifications</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="manifest" href="/manifest.json">
</head>
<body class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-2xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-purple-600 mb-6">🔔 Test Push Notifications</h1>
        
        @auth
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            ✅ Connecté en tant que <strong>{{ auth()->user()->name }}</strong>
        </div>
        @else
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
            ⚠️ Non connecté - <a href="{{ route('login') }}" class="underline font-medium">Se connecter</a> pour tester les notifications
        </div>
        @endauth
        
        <!-- Status -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Statut</h2>
            <div id="status-permission" class="mb-2">
                <span class="font-medium">Permission:</span> 
                <span id="perm-status" class="text-gray-600">Vérification...</span>
            </div>
            <div id="status-sw" class="mb-2">
                <span class="font-medium">Service Worker:</span> 
                <span id="sw-status" class="text-gray-600">Vérification...</span>
            </div>
            <div id="status-token">
                <span class="font-medium">FCM Token:</span> 
                <span id="token-status" class="text-gray-600">Non défini</span>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Actions</h2>
            <button id="btn-permission" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition mb-3 w-full">
                🔔 Demander la permission
            </button>
            <button id="btn-subscribe" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition mb-3 w-full" disabled>
                ✅ S'abonner aux notifications
            </button>
            <button id="btn-test-local" class="bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 transition mb-3 w-full" disabled>
                🧪 Test notification (Frontend)
            </button>
            <button id="btn-test" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition w-full" disabled>
                🚀 Test notification (Backend)
            </button>
        </div>

        <!-- Logs -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h2 class="text-xl font-semibold mb-4">Logs</h2>
            <div id="logs" class="text-sm font-mono text-gray-700 max-h-48 overflow-y-auto space-y-1"></div>
        </div>
    </div>

    <!-- Firebase SDK -->
    <script type="module">
        import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js';
        import { getMessaging, getToken, onMessage } from 'https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js';

        const firebaseConfig = {
            apiKey: "AIzaSyC0x3pmQewGWynoAbFsG9SiFbYjKxDYOrE",
            authDomain: "vintapp-e6fa7.firebaseapp.com",
            projectId: "vintapp-e6fa7",
            storageBucket: "vintapp-e6fa7.firebasestorage.app",
            messagingSenderId: "880178183981",
            appId: "1:880178183981:web:395604645bd7d758a35da4"
        };

        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);
        
        const VAPID_KEY = 'BAE5dM7Fc4f3s7H5Isru52xxcR60apO46k9IuFcMni04qZW3iqbIjjhwP7gle-mWQ1vGyPxZ0i3SWvn1Q3UKnoE';
        
        let currentToken = null;

        function log(message, type = 'info') {
            const logs = document.getElementById('logs');
            const time = new Date().toLocaleTimeString();
            const color = type === 'error' ? 'text-red-600' : type === 'success' ? 'text-green-600' : 'text-blue-600';
            logs.innerHTML += `<div class="${color}">[${time}] ${message}</div>`;
            logs.scrollTop = logs.scrollHeight;
        }

        // Vérifier le support
        async function checkStatus() {
            // Permission
            const perm = Notification.permission;
            document.getElementById('perm-status').textContent = perm;
            document.getElementById('perm-status').className = 
                perm === 'granted' ? 'text-green-600 font-medium' : 
                perm === 'denied' ? 'text-red-600 font-medium' : 'text-yellow-600 font-medium';
            
            // Service Worker
            if ('serviceWorker' in navigator) {
                const reg = await navigator.serviceWorker.getRegistration();
                const status = reg ? 'Actif ✓' : 'Non actif';
                document.getElementById('sw-status').textContent = status;
                document.getElementById('sw-status').className = reg ? 'text-green-600 font-medium' : 'text-red-600 font-medium';
                log(reg ? 'Service Worker actif' : 'Service Worker non trouvé', reg ? 'success' : 'error');
            }

            // Activer les boutons
            if (perm === 'granted') {
                document.getElementById('btn-subscribe').disabled = false;
                document.getElementById('btn-test-local').disabled = false;
                document.getElementById('btn-test').disabled = false;
            }
        }

        // Demander permission
        document.getElementById('btn-permission').addEventListener('click', async () => {
            log('Demande de permission...');
            const perm = await Notification.requestPermission();
            log(`Permission: ${perm}`, perm === 'granted' ? 'success' : 'error');
            await checkStatus();
        });

        // S'abonner
        document.getElementById('btn-subscribe').addEventListener('click', async () => {
            try {
                log('Enregistrement du Service Worker...');
                const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
                log('Service Worker enregistré', 'success');
                
                log('Obtention du token FCM...');
                const token = await getToken(messaging, { 
                    vapidKey: VAPID_KEY,
                    serviceWorkerRegistration: registration
                });
                
                if (token) {
                    currentToken = token;
                    log(`Token obtenu: ${token.substring(0, 20)}...`, 'success');
                    document.getElementById('token-status').textContent = token.substring(0, 30) + '...';
                    document.getElementById('token-status').className = 'text-green-600 font-medium';
                    
                    // Envoyer au serveur
                    log('Envoi du token au serveur...');
                    const response = await fetch('/api/notifications/subscribe', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            token: token,
                            device_type: 'desktop',
                            browser: navigator.userAgent.includes('Chrome') ? 'chrome' : 'other'
                        })
                    });
                    
                    const responseText = await response.text();
                    log(`Réponse serveur (${response.status}): ${responseText.substring(0, 100)}`, response.ok ? 'success' : 'error');
                    
                    if (response.ok) {
                        const data = JSON.parse(responseText);
                        log('✅ Abonnement réussi!', 'success');
                    } else {
                        log('❌ Erreur serveur: ' + response.status, 'error');
                    }
                } else {
                    log('Aucun token obtenu', 'error');
                }
            } catch (error) {
                log('Erreur: ' + error.message, 'error');
                console.error(error);
            }
        });

        // Test notification frontend (sans backend)
        document.getElementById('btn-test-local').addEventListener('click', async () => {
            try {
                log('Envoi notification test locale...');
                
                if (Notification.permission !== 'granted') {
                    log('Permission non accordée', 'error');
                    return;
                }

                // Utiliser le Service Worker pour afficher la notification
                const registration = await navigator.serviceWorker.getRegistration();
                
                if (!registration) {
                    log('Service Worker non trouvé', 'error');
                    return;
                }

                await registration.showNotification('🧪 Test Notification Frontend', {
                    body: 'Vos notifications fonctionnent parfaitement ! 🎉',
                    icon: '/images/logo.png',
                    badge: '/images/icons/icon-72x72.png',
                    tag: 'test-notification',
                    requireInteraction: false,
                    vibrate: [200, 100, 200],
                    data: {
                        url: '/test-push',
                        type: 'test'
                    },
                    actions: [
                        { action: 'open', title: '👀 Voir' },
                        { action: 'close', title: '✖ Fermer' }
                    ]
                });

                log('✅ Notification test envoyée (frontend)', 'success');
            } catch (error) {
                log('Erreur: ' + error.message, 'error');
            }
        });

        // Test notification backend
        document.getElementById('btn-test').addEventListener('click', async () => {
            try {
                log('Envoi notification test...');
                const response = await fetch('/api/notifications/test', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const responseText = await response.text();
                log(`Réponse test (${response.status}): ${responseText.substring(0, 100)}`, response.ok ? 'success' : 'error');
                
                if (response.ok) {
                    const data = JSON.parse(responseText);
                    log(data.message, 'success');
                }
            } catch (error) {
                log('Erreur: ' + error.message, 'error');
            }
        });

        // Écouter les messages foreground
        onMessage(messaging, (payload) => {
            log('📨 Notification reçue: ' + payload.notification.title, 'success');
            new Notification(payload.notification.title, {
                body: payload.notification.body,
                icon: payload.notification.icon || '/images/logo.png'
            });
        });

        // Init
        checkStatus();
        log('Page chargée - Prêt pour les tests', 'success');
    </script>
</body>
</html>
