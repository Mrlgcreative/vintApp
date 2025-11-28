/**
 * VintApp Background Sync Manager
 * Gestion des actions hors ligne et synchronisation automatique
 */

class BackgroundSyncManager {
    constructor() {
        this.dbName = 'vintapp-sync';
        this.dbVersion = 1;
        this.storeName = 'pending-requests';
        this.db = null;
        this.isOnline = navigator.onLine;
        this.syncInProgress = false;
    }

    /**
     * Initialiser le manager
     */
    async init() {
        console.log('🔄 Background Sync: Début initialisation...');
        
        // Initialiser IndexedDB
        try {
            await this.initDB();
            console.log('✅ Background Sync: IndexedDB initialisée');
        } catch (error) {
            console.error('❌ Background Sync: Erreur IndexedDB', error);
            throw error;
        }

        // Écouter les changements de connexion
        window.addEventListener('online', () => this.handleOnline());
        window.addEventListener('offline', () => this.handleOffline());

        // Afficher l'état initial
        this.updateConnectionStatus();

        // Synchroniser les requêtes en attente si en ligne
        if (this.isOnline) {
            await this.syncPendingRequests();
        }

        console.log('✅ Background Sync Manager initialisé');
    }

    /**
     * Initialiser IndexedDB
     */
    async initDB() {
        console.log('📦 IndexedDB: Ouverture de la base de données...');
        
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.dbVersion);

            request.onerror = () => {
                console.error('❌ IndexedDB: Erreur ouverture', request.error);
                reject(request.error);
            };
            
            request.onsuccess = () => {
                this.db = request.result;
                console.log('✅ IndexedDB: Base de données ouverte');
                resolve();
            };

            request.onupgradeneeded = (event) => {
                console.log('🔧 IndexedDB: Mise à jour du schéma...');
                const db = event.target.result;

                // Créer le store si nécessaire
                if (!db.objectStoreNames.contains(this.storeName)) {
                    const objectStore = db.createObjectStore(this.storeName, { 
                        keyPath: 'id', 
                        autoIncrement: true 
                    });

                    // Index pour rechercher par type
                    objectStore.createIndex('type', 'type', { unique: false });
                    objectStore.createIndex('timestamp', 'timestamp', { unique: false });
                    objectStore.createIndex('retryCount', 'retryCount', { unique: false });
                    
                    console.log('✅ IndexedDB: Store créé avec succès');
                }
            };
        });
    }

    /**
     * Gérer le passage en ligne
     */
    async handleOnline() {
        console.log('🌐 Connexion rétablie');
        this.isOnline = true;
        this.updateConnectionStatus();
        
        // Lancer la synchronisation
        await this.syncPendingRequests();
    }

    /**
     * Gérer le passage hors ligne
     */
    handleOffline() {
        console.log('📴 Connexion perdue');
        this.isOnline = false;
        this.updateConnectionStatus();
    }

    /**
     * Mettre à jour l'indicateur de connexion
     */
    updateConnectionStatus() {
        // Supprimer l'ancien badge s'il existe
        const existingBadge = document.getElementById('connection-status-badge');
        if (existingBadge) {
            existingBadge.remove();
        }

        if (!this.isOnline) {
            // Afficher le badge offline
            const badge = document.createElement('div');
            badge.id = 'connection-status-badge';
            badge.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-orange-500 text-white px-4 py-2 rounded-full shadow-lg z-50 flex items-center gap-2 animate-slide-down';
            badge.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414" />
                </svg>
                <span class="font-medium">Mode hors ligne</span>
            `;
            document.body.appendChild(badge);
        }
    }

    /**
     * Ajouter une requête à la queue
     */
    async addToQueue(type, url, method, data = null) {
        const requestData = {
            type,
            url,
            method,
            data,
            timestamp: Date.now(),
            retryCount: 0,
            maxRetries: 3
        };

        const transaction = this.db.transaction([this.storeName], 'readwrite');
        const objectStore = transaction.objectStore(this.storeName);
        
        return new Promise((resolve, reject) => {
            const addRequest = objectStore.add(requestData);
            addRequest.onsuccess = () => {
                console.log(`✅ Requête "${type}" ajoutée à la queue`, addRequest.result);
                this.showQueuedNotification(type);
                resolve(addRequest.result);
            };
            addRequest.onerror = () => reject(addRequest.error);
        });
    }

    /**
     * Récupérer toutes les requêtes en attente
     */
    async getPendingRequests() {
        const transaction = this.db.transaction([this.storeName], 'readonly');
        const objectStore = transaction.objectStore(this.storeName);

        return new Promise((resolve, reject) => {
            const request = objectStore.getAll();
            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Synchroniser toutes les requêtes en attente
     */
    async syncPendingRequests() {
        if (this.syncInProgress || !this.isOnline) {
            return;
        }

        this.syncInProgress = true;
        this.showSyncNotification();

        try {
            const pendingRequests = await this.getPendingRequests();

            if (pendingRequests.length === 0) {
                console.log('ℹ️ Aucune requête en attente');
                this.syncInProgress = false;
                return;
            }

            console.log(`🔄 Synchronisation de ${pendingRequests.length} requête(s)...`);

            let successCount = 0;
            let failCount = 0;

            for (const req of pendingRequests) {
                try {
                    await this.executeRequest(req);
                    await this.removeFromQueue(req.id);
                    successCount++;
                    console.log(`✅ Requête ${req.id} synchronisée`);
                } catch (error) {
                    console.error(`❌ Erreur requête ${req.id}:`, error);
                    
                    // Incrémenter le compteur de retry
                    req.retryCount++;
                    
                    if (req.retryCount >= req.maxRetries) {
                        // Trop de tentatives, supprimer
                        await this.removeFromQueue(req.id);
                        failCount++;
                        console.log(`❌ Requête ${req.id} abandonnée après ${req.maxRetries} tentatives`);
                    } else {
                        // Mettre à jour le compteur
                        await this.updateRequest(req);
                        failCount++;
                    }
                }
            }

            this.showSyncCompleteNotification(successCount, failCount);

        } catch (error) {
            console.error('❌ Erreur synchronisation:', error);
        } finally {
            this.syncInProgress = false;
            this.hideSyncNotification();
        }
    }

    /**
     * Exécuter une requête
     */
    async executeRequest(req) {
        const options = {
            method: req.method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        };

        if (req.data && (req.method === 'POST' || req.method === 'PUT' || req.method === 'PATCH')) {
            options.body = JSON.stringify(req.data);
        }

        const response = await fetch(req.url, options);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        return await response.json();
    }

    /**
     * Supprimer une requête de la queue
     */
    async removeFromQueue(id) {
        const transaction = this.db.transaction([this.storeName], 'readwrite');
        const objectStore = transaction.objectStore(this.storeName);

        return new Promise((resolve, reject) => {
            const request = objectStore.delete(id);
            request.onsuccess = () => resolve();
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Mettre à jour une requête
     */
    async updateRequest(req) {
        const transaction = this.db.transaction([this.storeName], 'readwrite');
        const objectStore = transaction.objectStore(this.storeName);

        return new Promise((resolve, reject) => {
            const request = objectStore.put(req);
            request.onsuccess = () => resolve();
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Afficher une notification de mise en queue
     */
    showQueuedNotification(type) {
        const messages = {
            'create-item': 'Article enregistré, sera publié en ligne',
            'create-order': 'Commande enregistrée, sera envoyée en ligne',
            'send-message': 'Message enregistré, sera envoyé en ligne',
            'update-profile': 'Profil enregistré, sera synchronisé en ligne'
        };

        const message = messages[type] || 'Action enregistrée';
        this.showToast(message, 'info');
    }

    /**
     * Afficher la notification de synchronisation
     */
    showSyncNotification() {
        const syncBadge = document.createElement('div');
        syncBadge.id = 'sync-notification';
        syncBadge.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-blue-500 text-white px-4 py-2 rounded-full shadow-lg z-50 flex items-center gap-2 animate-slide-down';
        syncBadge.innerHTML = `
            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="font-medium">Synchronisation...</span>
        `;
        document.body.appendChild(syncBadge);
    }

    /**
     * Masquer la notification de synchronisation
     */
    hideSyncNotification() {
        const syncBadge = document.getElementById('sync-notification');
        if (syncBadge) {
            syncBadge.remove();
        }
    }

    /**
     * Afficher la notification de synchronisation terminée
     */
    showSyncCompleteNotification(successCount, failCount) {
        if (successCount > 0) {
            this.showToast(`✅ ${successCount} action(s) synchronisée(s)`, 'success');
        }
        if (failCount > 0) {
            this.showToast(`⚠️ ${failCount} action(s) en échec`, 'warning');
        }
    }

    /**
     * Afficher un toast
     */
    showToast(message, type = 'info') {
        const colors = {
            info: 'bg-blue-500',
            success: 'bg-green-500',
            warning: 'bg-orange-500',
            error: 'bg-red-500'
        };

        const toast = document.createElement('div');
        toast.className = `fixed bottom-20 right-4 ${colors[type]} text-white px-4 py-3 rounded-lg shadow-lg z-50 animate-slide-up`;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slide-down 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    /**
     * Wrapper pour les requêtes avec gestion offline
     */
    async fetch(url, options = {}) {
        if (!this.isOnline) {
            // Hors ligne, mettre en queue
            const type = options.syncType || 'generic';
            await this.addToQueue(type, url, options.method || 'GET', options.body ? JSON.parse(options.body) : null);
            
            // Retourner une fausse réponse
            return {
                ok: true,
                queued: true,
                json: async () => ({ success: true, message: 'Action mise en queue', queued: true })
            };
        }

        // En ligne, exécuter normalement
        try {
            const response = await fetch(url, options);
            return response;
        } catch (error) {
            // Erreur réseau, mettre en queue
            const type = options.syncType || 'generic';
            await this.addToQueue(type, url, options.method || 'GET', options.body ? JSON.parse(options.body) : null);
            
            throw error;
        }
    }

    /**
     * Obtenir le nombre de requêtes en attente
     */
    async getPendingCount() {
        const requests = await this.getPendingRequests();
        return requests.length;
    }
}

// Initialiser au chargement et exporter globalement
window.syncManager = null;

async function initializeSyncManager() {
    try {
        console.log('🚀 Démarrage de l\'initialisation du Background Sync Manager...');
        window.syncManager = new BackgroundSyncManager();
        await window.syncManager.init();
        console.log('🎉 Background Sync Manager complètement initialisé et prêt !');
    } catch (error) {
        console.error('❌ Erreur lors de l\'initialisation du Background Sync Manager:', error);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeSyncManager);
} else {
    initializeSyncManager();
}

// Style pour les animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slide-down {
        from {
            transform: translate(-50%, -100%);
            opacity: 0;
        }
        to {
            transform: translate(-50%, 0);
            opacity: 1;
        }
    }
    
    @keyframes slide-up {
        from {
            transform: translateY(100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .animate-slide-down {
        animation: slide-down 0.3s ease-out;
    }
    
    .animate-slide-up {
        animation: slide-up 0.3s ease-out;
    }
`;
document.head.appendChild(style);

console.log('🔄 Background Sync Manager chargé');
