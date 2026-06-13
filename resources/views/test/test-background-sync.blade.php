@extends('app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-pink-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">🔄 Test Background Sync</h1>
            <p class="text-gray-600">Testez la synchronisation des actions hors ligne</p>
        </div>

        <!-- État de connexion -->
        <div id="connectionStatus" class="mb-6 p-6 bg-white rounded-2xl shadow-lg">
            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                <span class="text-2xl">🌐</span>
                État de la connexion
            </h2>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="font-medium">État actuel:</span>
                    <span id="onlineStatus" class="px-3 py-1 rounded-full text-sm font-medium">
                        <!-- Rempli par JS -->
                    </span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="font-medium">Requêtes en attente:</span>
                    <span id="pendingCount" class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-sm font-medium">
                        0
                    </span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="font-medium">IndexedDB:</span>
                    <span id="dbStatus" class="text-sm text-gray-600">
                        <!-- Rempli par JS -->
                    </span>
                </div>
            </div>
        </div>

        <!-- Actions de test -->
        <div class="mb-6 p-6 bg-white rounded-2xl shadow-lg">
            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                <span class="text-2xl">🧪</span>
                Actions de test
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <!-- Créer un item -->
                <button onclick="testCreateItem()" class="p-4 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-xl hover:shadow-lg transition-all transform hover:scale-105">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">📦</span>
                        <div class="text-left">
                            <div class="font-semibold">Créer un article</div>
                            <div class="text-xs opacity-90">Test création item</div>
                        </div>
                    </div>
                </button>

                <!-- Créer une commande -->
                <button onclick="testCreateOrder()" class="p-4 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-xl hover:shadow-lg transition-all transform hover:scale-105">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🛒</span>
                        <div class="text-left">
                            <div class="font-semibold">Passer commande</div>
                            <div class="text-xs opacity-90">Test création order</div>
                        </div>
                    </div>
                </button>

                <!-- Envoyer un message -->
                <button onclick="testSendMessage()" class="p-4 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-xl hover:shadow-lg transition-all transform hover:scale-105">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">💬</span>
                        <div class="text-left">
                            <div class="font-semibold">Envoyer message</div>
                            <div class="text-xs opacity-90">Test envoi message</div>
                        </div>
                    </div>
                </button>

                <!-- Mettre à jour profil -->
                <button onclick="testUpdateProfile()" class="p-4 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl hover:shadow-lg transition-all transform hover:scale-105">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">👤</span>
                        <div class="text-left">
                            <div class="font-semibold">Mettre à jour profil</div>
                            <div class="text-xs opacity-90">Test update profile</div>
                        </div>
                    </div>
                </button>
            </div>

            <!-- Actions de gestion -->
            <div class="flex gap-3 pt-4 border-t">
                <button onclick="syncNow()" class="flex-1 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    🔄 Synchroniser maintenant
                </button>
                <button onclick="clearQueue()" class="flex-1 py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors">
                    🗑️ Vider la queue
                </button>
            </div>
        </div>

        <!-- Simulation -->
        <div class="mb-6 p-6 bg-white rounded-2xl shadow-lg">
            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                <span class="text-2xl">🎭</span>
                Simulation
            </h2>
            <div class="space-y-3">
                <button onclick="simulateOffline()" class="w-full py-3 bg-orange-600 text-white rounded-lg font-semibold hover:bg-orange-700 transition-colors">
                    📴 Simuler mode hors ligne
                </button>
                <button onclick="simulateOnline()" class="w-full py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors">
                    🌐 Simuler mode en ligne
                </button>
                <p class="text-sm text-gray-600 italic">
                    💡 Astuce : Ouvrez DevTools (F12) → Network → Cochez "Offline" pour un vrai test
                </p>
            </div>
        </div>

        <!-- Logs -->
        <div class="p-6 bg-white rounded-2xl shadow-lg">
            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                <span class="text-2xl">📋</span>
                Logs en temps réel
                <button onclick="clearLogs()" class="ml-auto text-sm px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">
                    Effacer
                </button>
            </h2>
            <div id="logs" class="space-y-2 max-h-96 overflow-y-auto bg-gray-50 rounded-lg p-4">
                <div class="text-sm text-gray-500 italic">Les logs apparaîtront ici...</div>
            </div>
        </div>

        <!-- Aide -->
        <div class="mt-6 p-6 bg-blue-50 rounded-2xl border-2 border-blue-200">
            <h3 class="font-semibold text-blue-900 mb-2">💡 Comment tester ?</h3>
            <ol class="text-sm text-blue-800 space-y-2 ml-4 list-decimal">
                <li>Vérifiez que vous êtes en ligne (état vert)</li>
                <li>Cliquez sur une action de test pour l'exécuter en ligne</li>
                <li>Passez en mode hors ligne (bouton orange ou DevTools)</li>
                <li>Cliquez sur plusieurs actions - elles seront mises en queue</li>
                <li>Repassez en ligne - les actions se synchroniseront automatiquement</li>
                <li>Observez les logs et le compteur de requêtes en attente</li>
            </ol>
        </div>
    </div>
</div>

<script>
    // Attendre que syncManager soit prêt
    function waitForSyncManager() {
        return new Promise((resolve) => {
            // Vérification complète
            const isReady = () => {
                return window.syncManager && 
                       window.syncManager.db && 
                       typeof window.syncManager.fetch === 'function';
            };
            
            if (isReady()) {
                console.log('✅ syncManager déjà prêt');
                resolve();
            } else {
                console.log('⏳ Attente de syncManager...', {
                    exists: !!window.syncManager,
                    hasDb: !!window.syncManager?.db,
                    hasFetch: typeof window.syncManager?.fetch === 'function'
                });
                
                let attempts = 0;
                const maxAttempts = 100; // 10 secondes max
                
                const interval = setInterval(() => {
                    attempts++;
                    
                    if (isReady()) {
                        console.log('✅ syncManager prêt après', attempts * 100, 'ms');
                        clearInterval(interval);
                        resolve();
                    } else if (attempts >= maxAttempts) {
                        console.error('❌ Timeout: syncManager non initialisé après 10 secondes');
                        console.log('État final:', {
                            syncManager: window.syncManager,
                            db: window.syncManager?.db,
                            fetch: typeof window.syncManager?.fetch
                        });
                        clearInterval(interval);
                        resolve(); // Resolve quand même pour éviter un blocage
                    } else if (attempts % 10 === 0) {
                        console.log(`⏳ Tentative ${attempts}/100...`, {
                            exists: !!window.syncManager,
                            hasDb: !!window.syncManager?.db,
                            hasFetch: typeof window.syncManager?.fetch === 'function'
                        });
                    }
                }, 100);
            }
        });
    }

    // Fonction pour logger
    function log(message, type = 'info') {
        const logsDiv = document.getElementById('logs');
        const time = new Date().toLocaleTimeString();
        const colors = {
            info: 'text-blue-600',
            success: 'text-green-600',
            error: 'text-red-600',
            warning: 'text-orange-600'
        };
        
        const logEntry = document.createElement('div');
        logEntry.className = `text-sm ${colors[type]} font-mono`;
        logEntry.textContent = `[${time}] ${message}`;
        
        // Supprimer le message par défaut
        if (logsDiv.querySelector('.italic')) {
            logsDiv.innerHTML = '';
        }
        
        logsDiv.appendChild(logEntry);
        logsDiv.scrollTop = logsDiv.scrollHeight;
    }

    // Mettre à jour l'état
    function updateStatus() {
        const isOnline = navigator.onLine;
        const statusEl = document.getElementById('onlineStatus');
        
        if (isOnline) {
            statusEl.className = 'px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium';
            statusEl.textContent = '🟢 En ligne';
        } else {
            statusEl.className = 'px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-sm font-medium';
            statusEl.textContent = '🔴 Hors ligne';
        }

        // Mettre à jour le compteur
        updatePendingCount();

        // Vérifier IndexedDB
        if (window.syncManager && window.syncManager.db) {
            document.getElementById('dbStatus').textContent = '✅ Initialisée';
        } else {
            document.getElementById('dbStatus').textContent = '⏳ En cours...';
        }
    }

    // Mettre à jour le compteur
    async function updatePendingCount() {
        if (window.syncManager) {
            const count = await window.syncManager.getPendingCount();
            document.getElementById('pendingCount').textContent = count;
        }
    }

    // Test créer un item
    async function testCreateItem() {
        log('📦 Test création d\'item...', 'info');
        
        // Attendre que syncManager soit prêt
        if (!window.syncManager) {
            log('⏳ Attente du Background Sync Manager...', 'warning');
            await waitForSyncManager();
        }
        
        const itemData = {
            title: 'Test Item - ' + Date.now(),
            description: 'Ceci est un test',
            price: 100,
            category_id: 1
        };

        try {
            const response = await window.syncManager.fetch('/api/items', {
                method: 'POST',
                syncType: 'create-item',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(itemData)
            });

            const data = await response.json();
            
            if (data.queued) {
                log('✅ Item mis en queue pour synchronisation', 'warning');
            } else {
                log('✅ Item créé avec succès', 'success');
            }
        } catch (error) {
            log('❌ Erreur: ' + error.message, 'error');
        }

        updatePendingCount();
    }

    // Test créer une commande
    async function testCreateOrder() {
        log('🛒 Test création de commande...', 'info');
        
        // Attendre que syncManager soit prêt
        if (!window.syncManager) {
            log('⏳ Attente du Background Sync Manager...', 'warning');
            await waitForSyncManager();
        }
        
        const orderData = {
            item_id: 1,
            quantity: 1,
            total: 100
        };

        try {
            const response = await window.syncManager.fetch('/api/orders', {
                method: 'POST',
                syncType: 'create-order',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(orderData)
            });

            const data = await response.json();
            
            if (data.queued) {
                log('✅ Commande mise en queue pour synchronisation', 'warning');
            } else {
                log('✅ Commande créée avec succès', 'success');
            }
        } catch (error) {
            log('❌ Erreur: ' + error.message, 'error');
        }

        updatePendingCount();
    }

    // Test envoyer un message
    async function testSendMessage() {
        log('💬 Test envoi de message...', 'info');
        
        // Attendre que syncManager soit prêt
        if (!window.syncManager) {
            log('⏳ Attente du Background Sync Manager...', 'warning');
            await waitForSyncManager();
        }
        
        const messageData = {
            conversation_id: 1,
            message: 'Test message - ' + Date.now()
        };

        try {
            const response = await window.syncManager.fetch('/api/messages', {
                method: 'POST',
                syncType: 'send-message',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(messageData)
            });

            const data = await response.json();
            
            if (data.queued) {
                log('✅ Message mis en queue pour synchronisation', 'warning');
            } else {
                log('✅ Message envoyé avec succès', 'success');
            }
        } catch (error) {
            log('❌ Erreur: ' + error.message, 'error');
        }

        updatePendingCount();
    }

    // Test mettre à jour profil
    async function testUpdateProfile() {
        log('👤 Test mise à jour du profil...', 'info');
        
        // Attendre que syncManager soit prêt
        if (!window.syncManager) {
            log('⏳ Attente du Background Sync Manager...', 'warning');
            await waitForSyncManager();
        }
        
        const profileData = {
            bio: 'Test bio - ' + Date.now()
        };

        try {
            const response = await window.syncManager.fetch('/api/profile', {
                method: 'PUT',
                syncType: 'update-profile',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(profileData)
            });

            const data = await response.json();
            
            if (data.queued) {
                log('✅ Profil mis en queue pour synchronisation', 'warning');
            } else {
                log('✅ Profil mis à jour avec succès', 'success');
            }
        } catch (error) {
            log('❌ Erreur: ' + error.message, 'error');
        }

        updatePendingCount();
    }

    // Synchroniser maintenant
    async function syncNow() {
        log('🔄 Lancement de la synchronisation manuelle...', 'info');
        
        // Attendre que syncManager soit prêt
        if (!window.syncManager) {
            log('⏳ Attente du Background Sync Manager...', 'warning');
            await waitForSyncManager();
        }
        
        if (!navigator.onLine) {
            log('❌ Impossible de synchroniser hors ligne', 'error');
            return;
        }

        try {
            await window.syncManager.syncPendingRequests();
            log('✅ Synchronisation terminée', 'success');
        } catch (error) {
            log('❌ Erreur synchronisation: ' + error.message, 'error');
        }

        updatePendingCount();
    }

    // Vider la queue
    async function clearQueue() {
        if (!confirm('Êtes-vous sûr de vouloir supprimer toutes les requêtes en attente ?')) {
            return;
        }

        log('🗑️ Suppression de la queue...', 'warning');
        
        // Attendre que syncManager soit prêt
        if (!window.syncManager) {
            log('⏳ Attente du Background Sync Manager...', 'warning');
            await waitForSyncManager();
        }
        
        try {
            const db = window.syncManager.db;
            const transaction = db.transaction(['pending-requests'], 'readwrite');
            const store = transaction.objectStore('pending-requests');
            await store.clear();
            
            log('✅ Queue vidée', 'success');
        } catch (error) {
            log('❌ Erreur: ' + error.message, 'error');
        }

        updatePendingCount();
    }

    // Simuler offline
    function simulateOffline() {
        log('📴 Simulation mode hors ligne (manuel uniquement)', 'warning');
        log('💡 Pour un vrai test, utilisez DevTools → Network → Offline', 'info');
    }

    // Simuler online
    function simulateOnline() {
        log('🌐 Simulation mode en ligne (manuel uniquement)', 'warning');
        log('💡 Pour un vrai test, décochez Offline dans DevTools', 'info');
    }

    // Effacer les logs
    function clearLogs() {
        document.getElementById('logs').innerHTML = '<div class="text-sm text-gray-500 italic">Les logs apparaîtront ici...</div>';
    }

    // Événements de connexion
    window.addEventListener('online', () => {
        log('🌐 Connexion rétablie', 'success');
        updateStatus();
    });

    window.addEventListener('offline', () => {
        log('📴 Connexion perdue', 'warning');
        updateStatus();
    });

    // Initialisation
    document.addEventListener('DOMContentLoaded', async () => {
        updateStatus();
        log('✅ Page de test chargée', 'success');
        
        // Attendre que syncManager soit prêt
        log('⏳ Initialisation du Background Sync Manager...', 'info');
        await waitForSyncManager();
        log('✅ Background Sync Manager prêt', 'success');
        updateStatus();
    });

    // Mise à jour périodique du compteur
    setInterval(updatePendingCount, 2000);
</script>
@endsection
