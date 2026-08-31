/**
 * VintApp PWA Manager
 * Gestion de l'installation et des mises à jour du Service Worker
 */

class PWAManager {
    constructor() {
        this.swRegistration = null;
        this.deferredPrompt = null;
        this.init();
    }

    /**
     * Initialiser PWA
     */
    async init() {
        if ('serviceWorker' in navigator) {
            try {
                // Enregistrer le Service Worker
                this.swRegistration = await navigator.serviceWorker.register('/sw.js', {
                    scope: '/'
                });

                // Vérifier les mises à jour
                this.swRegistration.addEventListener('updatefound', () => {
                    this.onUpdateFound();
                });

                // Vérifier toutes les heures
                setInterval(() => {
                    this.swRegistration.update();
                }, 60 * 60 * 1000);

            } catch (error) {
                // Erreur silencieuse
            }
        }

        // Gérer le prompt d'installation
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;

            // Ne pas appeler prompt() directement ici : il nécessite un geste utilisateur.
            // À la place, afficher un bouton qui déclenchera le prompt lors d'un clic.
            if (document.getElementById('pwa-install-button')) {
                return;
            }
            this.showInstallButton();
        });

        // App installée
        window.addEventListener('appinstalled', () => {
            this.hideInstallButton();
            this.deferredPrompt = null;
            localStorage.removeItem('pwa-install-dismissed');
        });

        // Détection du mode standalone
        if (window.matchMedia('(display-mode: standalone)').matches) {
            // Mode standalone détecté
        }
    }

    /**
     * Nouvelle version disponible
     */
    onUpdateFound() {
        const installingWorker = this.swRegistration.installing;

        installingWorker.addEventListener('statechange', () => {
            if (installingWorker.state === 'installed') {
                if (navigator.serviceWorker.controller) {
                    // Mise à jour disponible
                    this.showUpdateNotification();
                }
            }
        });
    }

    /**
     * Afficher notification de mise à jour
     */
    showUpdateNotification() {
        // Vérifier si la notification a déjà été affichée dans cette session
        const updateDismissed = sessionStorage.getItem('pwa-update-dismissed');
        if (updateDismissed === 'true') {
            return;
        }
        
        // Vérifier si déjà affichée dans les dernières 24h (localStorage)
        const lastUpdateShown = localStorage.getItem('pwa-update-last-shown');
        if (lastUpdateShown) {
            const hoursSinceLastShown = (Date.now() - parseInt(lastUpdateShown)) / (1000 * 60 * 60);
            if (hoursSinceLastShown < 24) {
                return;
            }
        }
        
        // Vérifier si la notification existe déjà dans le DOM
        if (document.getElementById('pwa-update-notification')) {
            return;
        }
        
        // Enregistrer le timestamp de l'affichage
        localStorage.setItem('pwa-update-last-shown', Date.now().toString());
        
        const notification = document.createElement('div');
        notification.id = 'pwa-update-notification';
        notification.className = 'fixed bottom-4 right-4 bg-white dark:bg-gray-800 rounded-lg shadow-xl p-4 max-w-sm z-50 animate-slide-up';
        notification.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                        Mise à jour disponible
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Une nouvelle version de VintApp est disponible.
                    </p>
                    <div class="mt-3 flex space-x-2">
                        <button id="pwa-update-btn" 
                                class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                            Mettre à jour
                        </button>
                        <button id="pwa-dismiss-btn" 
                                class="px-3 py-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                            Plus tard
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(notification);
        
        // Ajouter les event listeners
        document.getElementById('pwa-update-btn').addEventListener('click', () => {
            this.updateApp();
        });
        
        document.getElementById('pwa-dismiss-btn').addEventListener('click', () => {
            this.dismissUpdate();
        });
    }

    /**
     * Appliquer la mise à jour
     */
    updateApp() {
        // Supprimer le flag de session et le timestamp
        sessionStorage.removeItem('pwa-update-dismissed');
        localStorage.removeItem('pwa-update-last-shown');
        
        if (!this.swRegistration) {
            window.location.reload(true);
            return;
        }
        
        if (!this.swRegistration.waiting) {
            window.location.reload(true);
            return;
        }
        
        // Envoyer le message au Service Worker
        this.swRegistration.waiting.postMessage({ type: 'SKIP_WAITING' });
        
        // Écouter le changement de contrôleur
        let refreshing = false;
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            if (refreshing) return;
            refreshing = true;
            window.location.reload();
        });
        
        // Afficher un message de chargement
        const notification = document.getElementById('pwa-update-notification');
        if (notification) {
            notification.innerHTML = `
                <div class="flex items-center">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                    <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">Mise à jour en cours...</span>
                </div>
            `;
        }
    }

    /**
     * Ignorer la mise à jour
     */
    dismissUpdate() {
        const notification = document.getElementById('pwa-update-notification');
        if (notification) {
            notification.remove();
        }
        // Marquer comme ignorée pour cette session ET enregistrer le timestamp
        sessionStorage.setItem('pwa-update-dismissed', 'true');
        localStorage.setItem('pwa-update-last-shown', Date.now().toString());
    }

    /**
     * Afficher le bouton d'installation
     */
    showInstallButton() {
        // Si l'utilisateur a déjà rejeté récemment, garder le bouton silencieux
        const lastDismissed = localStorage.getItem('pwa-install-dismissed');
        if (lastDismissed && (Date.now() - parseInt(lastDismissed)) < (24 * 60 * 60 * 1000)) {
            return;
        }
        // Éviter les doublons
        if (document.getElementById('pwa-install-button')) {
            return;
        }
        
        const installButton = document.createElement('button');
        installButton.id = 'pwa-install-button';
        installButton.className = 'fixed bottom-20 right-4 bg-primary-600 text-white px-6 py-3 rounded-full shadow-lg hover:bg-primary-700 transition-all z-50 flex items-center space-x-2';
        installButton.innerHTML = `
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            <span class="font-medium">Installer l'app</span>
        `;
        installButton.addEventListener('click', () => this.installApp());

        document.body.appendChild(installButton);
    }
    
    /**
     * Afficher le bouton immédiatement - DÉSACTIVÉ
     */
    showInstallButtonImmediately() {
        // Fonctionnalité désactivée
        return;
    }

    /**
     * Masquer le bouton d'installation
     */
    hideInstallButton() {
        const installButton = document.getElementById('pwa-install-button');
        if (installButton) {
            installButton.remove();
        }
    }
    
    /**
     * Dismiss le bouton temporairement
     */
    dismissInstallButton() {
        // Sauvegarder le timestamp du dismiss
        localStorage.setItem('pwa-install-dismissed', Date.now().toString());
        this.hideInstallButton();
    }

    /**
     * Installer l'application
     */
    async installApp() {
        if (!this.deferredPrompt) {
            return;
        }

        this.deferredPrompt.prompt();
        const { outcome } = await this.deferredPrompt.userChoice;

        this.deferredPrompt = null;
        this.hideInstallButton();
    }
    
    /**
     * Afficher les instructions d'installation manuelle - DÉSACTIVÉ
     */
    showManualInstallInstructions() {
        // Fonctionnalité désactivée
        return;
    }

    /**
     * Vérifier si installée
     */
    isInstalled() {
        return window.matchMedia('(display-mode: standalone)').matches ||
               window.navigator.standalone === true;
    }

    /**
     * Demander permission notifications
     */
    async requestNotificationPermission() {
        if (!('Notification' in window)) {
            return false;
        }

        if (Notification.permission === 'granted') {
            return true;
        }

        if (Notification.permission !== 'denied') {
            const permission = await Notification.requestPermission();
            return permission === 'granted';
        }

        return false;
    }

    /**
     * Envoyer notification locale
     */
    async showNotification(title, options = {}) {
        const granted = await this.requestNotificationPermission();

        if (!granted) {
            return;
        }

        if (this.swRegistration) {
            this.swRegistration.showNotification(title, {
                icon: '/favicon.ico',
                badge: '/favicon.ico',
                vibrate: [200, 100, 200],
                ...options
            });
        }
    }
}

// Initialiser au chargement
let pwaManager;
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        pwaManager = new PWAManager();
    });
} else {
    pwaManager = new PWAManager();
}

// Style pour les animations PWA (encapsulé pour éviter les conflits)
(function() {
    const pwaStyle = document.createElement('style');
    pwaStyle.textContent = `
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
        
        .animate-slide-up {
            animation: slide-up 0.3s ease-out;
        }
    `;
    document.head.appendChild(pwaStyle);
})();
