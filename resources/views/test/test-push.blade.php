<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>🔔 Test Push Notifications - VintApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
</head>
<body class="bg-gradient-to-br from-purple-50 to-blue-50 min-h-screen p-8">
    
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 bg-purple-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Test Push Notifications</h1>
                    <p class="text-gray-600">Testez et déboguez les notifications Firebase</p>
                </div>
            </div>
            
            <!-- Status -->
            <div id="status" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="text-sm text-gray-500 mb-1">Permission</div>
                    <div id="permission-status" class="text-lg font-semibold">Chargement...</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="text-sm text-gray-500 mb-1">Service Worker</div>
                    <div id="sw-status" class="text-lg font-semibold">Chargement...</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="text-sm text-gray-500 mb-1">FCM Token</div>
                    <div id="token-status" class="text-lg font-semibold">Chargement...</div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            
            <!-- Permissions -->
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span>🔐</span> Permissions
                </h2>
                <div class="space-y-3">
                    <button onclick="requestPermission()" class="w-full bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition font-medium">
                        Demander Permission
                    </button>
                    <button onclick="showPrompt()" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-medium">
                        Afficher Modal
                    </button>
                    <button onclick="checkStatus()" class="w-full bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition font-medium">
                        Vérifier Status
                    </button>
                </div>
            </div>

            <!-- Subscription -->
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span>📱</span> Abonnement
                </h2>
                <div class="space-y-3">
                    <button onclick="subscribe()" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-medium">
                        S'abonner (Subscribe)
                    </button>
                    <button onclick="unsubscribe()" class="w-full bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition font-medium">
                        Se désabonner
                    </button>
                    <button onclick="getToken()" class="w-full bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-medium">
                        Voir Token FCM
                    </button>
                </div>
            </div>

            <!-- Test Notifications -->
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span>🧪</span> Tests
                </h2>
                <div class="space-y-3">
                    <button onclick="testNotification()" class="w-full bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 transition font-medium">
                        Test Backend API
                    </button>
                    <button onclick="testLocal()" class="w-full bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 transition font-medium">
                        Test Notification Locale
                    </button>
                    <button onclick="testInApp()" class="w-full bg-pink-600 text-white px-6 py-3 rounded-lg hover:bg-pink-700 transition font-medium">
                        Test In-App Notification
                    </button>
                </div>
            </div>

            <!-- Debug -->
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span>🔧</span> Debug
                </h2>
                <div class="space-y-3">
                    <button onclick="clearToken()" class="w-full bg-yellow-600 text-white px-6 py-3 rounded-lg hover:bg-yellow-700 transition font-medium">
                        Clear Token
                    </button>
                    <button onclick="clearLocalStorage()" class="w-full bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition font-medium">
                        Clear LocalStorage
                    </button>
                    <button onclick="reloadPage()" class="w-full bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition font-medium">
                        Recharger Page
                    </button>
                </div>
            </div>
        </div>

        <!-- Console Logs -->
        <div class="bg-gray-900 rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <span>📋</span> Console Logs
                </h2>
                <button onclick="clearLogs()" class="text-sm bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                    Clear
                </button>
            </div>
            <div id="console" class="bg-black rounded-lg p-4 font-mono text-sm text-green-400 h-96 overflow-y-auto">
                <div class="text-gray-500">Logs apparaîtront ici...</div>
            </div>
        </div>
    </div>

    <script type="module">
        // Logger personnalisé
        const consoleDiv = document.getElementById('console');
        
        function log(message, type = 'info') {
            const colors = {
                info: 'text-green-400',
                error: 'text-red-400',
                warn: 'text-yellow-400',
                success: 'text-blue-400'
            };
            
            const timestamp = new Date().toLocaleTimeString();
            const logLine = document.createElement('div');
            logLine.className = colors[type];
            logLine.textContent = `[${timestamp}] ${message}`;
            consoleDiv.appendChild(logLine);
            consoleDiv.scrollTop = consoleDiv.scrollHeight;
            console.log(message);
        }

        window.log = log;

        // Intercepter console
        const originalLog = console.log;
        const originalError = console.error;
        const originalWarn = console.warn;

        console.log = (...args) => {
            originalLog(...args);
            log(args.join(' '), 'info');
        };

        console.error = (...args) => {
            originalError(...args);
            log('❌ ' + args.join(' '), 'error');
        };

        console.warn = (...args) => {
            originalWarn(...args);
            log('⚠️ ' + args.join(' '), 'warn');
        };

        // Fonctions globales
        window.requestPermission = async () => {
            log('Demande de permission...');
            const result = await window.pushManager.requestPermission();
            log(`✅ Permission: ${result}`, 'success');
            updateStatus();
        };

        window.showPrompt = () => {
            log('Affichage du modal...');
            window.pushManager.showPermissionPrompt();
        };

        window.checkStatus = () => {
            log('Vérification du statut...');
            const status = window.pushManager.getPermissionStatus();
            const token = window.pushManager.currentToken;
            log(`Permission: ${status}`);
            log(`Token: ${token ? token.substring(0, 30) + '...' : 'null'}`);
            updateStatus();
        };

        window.subscribe = async () => {
            log('Abonnement en cours...');
            try {
                const token = await window.pushManager.subscribeToNotifications();
                log(`✅ Abonné! Token: ${token.substring(0, 30)}...`, 'success');
                updateStatus();
            } catch (err) {
                log(`❌ Erreur: ${err.message}`, 'error');
            }
        };

        window.unsubscribe = async () => {
            log('Désabonnement en cours...');
            const success = await window.pushManager.unsubscribe();
            log(success ? '✅ Désabonné!' : '❌ Échec désabonnement', success ? 'success' : 'error');
            updateStatus();
        };

        window.getToken = () => {
            const token = window.pushManager.currentToken;
            if (token) {
                log(`Token FCM: ${token}`, 'info');
                navigator.clipboard.writeText(token);
                log('✅ Token copié dans presse-papiers', 'success');
            } else {
                log('❌ Aucun token disponible', 'error');
            }
        };

        window.testNotification = async () => {
            log('Test notification backend...');
            try {
                const response = await fetch('/api/notifications/test', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                log(data.success ? '✅ Notification envoyée!' : '❌ Échec: ' + data.message, data.success ? 'success' : 'error');
            } catch (err) {
                log(`❌ Erreur API: ${err.message}`, 'error');
            }
        };

        window.testLocal = () => {
            log('Test notification locale...');
            if (Notification.permission === 'granted') {
                new Notification('🧪 Test Notification', {
                    body: 'Ceci est une notification de test locale',
                    icon: '/images/icons/icon-192x192.png',
                    badge: '/images/icons/icon-72x72.png',
                    vibrate: [200, 100, 200]
                });
                log('✅ Notification locale affichée', 'success');
            } else {
                log('❌ Permission refusée', 'error');
            }
        };

        window.testInApp = () => {
            log('Test notification in-app...');
            window.pushManager.showInAppNotification(
                '🎉 Test In-App',
                'Ceci est une notification in-app de test',
                '/'
            );
            log('✅ Notification in-app affichée', 'success');
        };

        window.clearToken = () => {
            localStorage.removeItem('fcm_token');
            log('✅ Token FCM supprimé du localStorage', 'success');
            updateStatus();
        };

        window.clearLocalStorage = () => {
            const keys = Object.keys(localStorage);
            keys.forEach(key => {
                if (key.includes('notification') || key.includes('pwa') || key.includes('fcm')) {
                    localStorage.removeItem(key);
                    log(`🗑️ Supprimé: ${key}`, 'warn');
                }
            });
            log('✅ LocalStorage nettoyé', 'success');
        };

        window.clearLogs = () => {
            consoleDiv.innerHTML = '<div class="text-gray-500">Logs cleared...</div>';
        };

        window.reloadPage = () => {
            location.reload();
        };

        async function updateStatus() {
            // Permission
            const permission = Notification.permission;
            const permissionEl = document.getElementById('permission-status');
            permissionEl.textContent = permission;
            permissionEl.className = 'text-lg font-semibold ' + (
                permission === 'granted' ? 'text-green-600' :
                permission === 'denied' ? 'text-red-600' : 'text-yellow-600'
            );

            // Service Worker
            const swEl = document.getElementById('sw-status');
            const registration = await navigator.serviceWorker.getRegistration();
            swEl.textContent = registration ? '✅ Actif' : '❌ Inactif';
            swEl.className = 'text-lg font-semibold ' + (registration ? 'text-green-600' : 'text-red-600');

            // Token
            const tokenEl = document.getElementById('token-status');
            const token = window.pushManager?.currentToken || localStorage.getItem('fcm_token');
            tokenEl.textContent = token ? '✅ Présent' : '❌ Absent';
            tokenEl.className = 'text-lg font-semibold ' + (token ? 'text-green-600' : 'text-red-600');
        }

        // Auto-update status
        setInterval(updateStatus, 2000);
        
        // Initial check
        setTimeout(() => {
            log('🚀 Page de test chargée');
            updateStatus();
            
            if (window.pushManager) {
                log('✅ Push Manager disponible');
            } else {
                log('❌ Push Manager non disponible - vérifier que push-manager.js est chargé', 'error');
            }
        }, 1000);
    </script>

    <!-- Charger Push Manager -->
    <script type="module" src="{{ asset('js/push-manager.js') }}?v={{ time() }}"></script>

</body>
</html>
