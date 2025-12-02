/**
 * Network Speed Adapter
 * Adapte les performances du lazy loading et skeleton selon la vitesse de connexion
 * VintApp PWA - 2025
 */

class NetworkSpeedAdapter {
    constructor() {
        this.connectionType = 'unknown';
        this.effectiveType = 'unknown';
        this.downlink = null;
        this.rtt = null;
        this.saveData = false;
        this.speedCategory = 'medium'; // slow, medium, fast, very-fast
        
        this.init();
    }

    init() {
        // Détecter la connexion initiale
        this.detectConnection();
        
        // Écouter les changements de connexion
        this.listenToConnectionChanges();
        
        // Mesurer la vitesse réelle
        this.measureSpeed();
        
        // Appliquer les optimisations
        this.applyOptimizations();
        
        console.log('📡 Network Speed Adapter initialisé:', {
            speedCategory: this.speedCategory,
            effectiveType: this.effectiveType,
            downlink: this.downlink,
            rtt: this.rtt
        });
    }

    detectConnection() {
        // API Network Information (supportée par Chrome, Edge, Opera)
        const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
        
        if (connection) {
            this.effectiveType = connection.effectiveType || 'unknown';
            this.downlink = connection.downlink; // Mbps
            this.rtt = connection.rtt; // ms
            this.saveData = connection.saveData || false;
            
            // Catégoriser la vitesse
            this.categorizeSpeed();
        } else {
            // Fallback: détecter via mesure de vitesse
            this.speedCategory = 'medium';
        }
    }

    categorizeSpeed() {
        // Utiliser effectiveType si disponible
        if (this.effectiveType) {
            switch (this.effectiveType) {
                case 'slow-2g':
                case '2g':
                    this.speedCategory = 'slow';
                    break;
                case '3g':
                    this.speedCategory = 'medium';
                    break;
                case '4g':
                    this.speedCategory = 'fast';
                    break;
                default:
                    this.speedCategory = 'medium';
            }
        }
        
        // Affiner avec downlink si disponible
        if (this.downlink !== null) {
            if (this.downlink < 1) {
                this.speedCategory = 'slow';
            } else if (this.downlink < 5) {
                this.speedCategory = 'medium';
            } else if (this.downlink < 20) {
                this.speedCategory = 'fast';
            } else {
                this.speedCategory = 'very-fast';
            }
        }
        
        // Affiner avec RTT si disponible
        if (this.rtt !== null) {
            if (this.rtt > 1000) {
                this.speedCategory = 'slow';
            } else if (this.rtt > 400 && this.speedCategory !== 'slow') {
                this.speedCategory = 'medium';
            } else if (this.rtt < 100 && this.downlink > 10) {
                this.speedCategory = 'very-fast';
            }
        }
        
        // Mode économie de données
        if (this.saveData) {
            this.speedCategory = 'slow';
        }
    }

    listenToConnectionChanges() {
        const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
        
        if (connection) {
            connection.addEventListener('change', () => {
                console.log('📡 Connexion changée, re-détection...');
                this.detectConnection();
                this.applyOptimizations();
            });
        }
    }

    async measureSpeed() {
        // Mesure simple de la vitesse en téléchargeant le logo
        const startTime = performance.now();
        const testImageUrl = '/images/logo.svg?cache=' + Date.now();
        
        try {
            const response = await fetch(testImageUrl, { 
                cache: 'no-cache'
            });
            
            if (!response.ok) {
                throw new Error('Failed to load test image');
            }
            
            const endTime = performance.now();
            const duration = endTime - startTime;
            
            // Estimer la catégorie basée sur le temps de réponse
            if (duration < 100) {
                this.speedCategory = 'very-fast';
            } else if (duration < 300) {
                this.speedCategory = 'fast';
            } else if (duration < 800) {
                this.speedCategory = 'medium';
            } else {
                this.speedCategory = 'slow';
            }
            
            console.log('⏱️ Vitesse mesurée:', duration.toFixed(0) + 'ms', '→', this.speedCategory);
        } catch (error) {
            console.warn('⚠️ Impossible de mesurer la vitesse, utilisation de la détection API');
            // Fallback sur la détection via Network Information API uniquement
        }
    }

    applyOptimizations() {
        const config = this.getOptimizedConfig();
        
        // Appliquer au PageSkeletonLoader
        if (window.PageSkeletonLoader) {
            window.defaultSkeletonConfig = config.skeleton;
        }
        
        // Appliquer au NavigationSkeletonManager
        if (window.navigationSkeletonManager) {
            window.navigationSkeletonManager.options.minDisplayTime = config.navigation.minDisplayTime;
        }
        
        // Appliquer au LazyLoadingManager
        if (window.lazyLoader) {
            window.lazyLoader.options.rootMargin = config.lazyLoading.rootMargin;
            window.lazyLoader.options.threshold = config.lazyLoading.threshold;
        }
        
        // Mettre à jour la config admin si elle existe
        if (window.adminSkeletonConfig) {
            window.adminSkeletonConfig.minDisplayTime = config.skeleton.minDisplayTime;
            window.adminSkeletonConfig.fadeOutDuration = config.skeleton.fadeOutDuration;
        }
        
        // Émettre un événement personnalisé
        window.dispatchEvent(new CustomEvent('networkSpeedChanged', {
            detail: {
                speedCategory: this.speedCategory,
                config: config
            }
        }));
        
        console.log('⚙️ Optimisations appliquées pour:', this.speedCategory);
    }

    getOptimizedConfig() {
        const configs = {
            'slow': {
                skeleton: {
                    minDisplayTime: 200,  // Très court pour ne pas bloquer
                    fadeOutDuration: 150,
                    maxWaitTime: 2000     // Timeout rapide
                },
                navigation: {
                    minDisplayTime: 200,
                    maxWaitTime: 2000
                },
                lazyLoading: {
                    rootMargin: '10px',   // Charger très proche du viewport
                    threshold: 0.1,
                    eager: false          // Pas de préchargement
                },
                preload: false,           // Désactiver préchargement
                animations: 'minimal'     // Animations minimales
            },
            'medium': {
                skeleton: {
                    minDisplayTime: 300,
                    fadeOutDuration: 200,
                    maxWaitTime: 3000
                },
                navigation: {
                    minDisplayTime: 300,
                    maxWaitTime: 3000
                },
                lazyLoading: {
                    rootMargin: '50px',
                    threshold: 0.05,
                    eager: false
                },
                preload: false,
                animations: 'normal'
            },
            'fast': {
                skeleton: {
                    minDisplayTime: 400,
                    fadeOutDuration: 300,
                    maxWaitTime: 5000
                },
                navigation: {
                    minDisplayTime: 400,
                    maxWaitTime: 5000
                },
                lazyLoading: {
                    rootMargin: '100px',  // Précharger plus loin
                    threshold: 0.01,
                    eager: true           // Préchargement activé
                },
                preload: true,
                animations: 'smooth'
            },
            'very-fast': {
                skeleton: {
                    minDisplayTime: 200,  // Court car chargement rapide
                    fadeOutDuration: 200,
                    maxWaitTime: 8000
                },
                navigation: {
                    minDisplayTime: 200,
                    maxWaitTime: 8000
                },
                lazyLoading: {
                    rootMargin: '200px',  // Précharger beaucoup plus loin
                    threshold: 0.01,
                    eager: true
                },
                preload: true,
                animations: 'smooth'
            }
        };
        
        return configs[this.speedCategory] || configs['medium'];
    }

    getSpeedCategory() {
        return this.speedCategory;
    }

    isSlowConnection() {
        return this.speedCategory === 'slow' || this.saveData;
    }

    isFastConnection() {
        return this.speedCategory === 'fast' || this.speedCategory === 'very-fast';
    }

    getConnectionInfo() {
        return {
            speedCategory: this.speedCategory,
            effectiveType: this.effectiveType,
            downlink: this.downlink,
            rtt: this.rtt,
            saveData: this.saveData
        };
    }
}

// Auto-initialisation
let networkAdapter;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        networkAdapter = new NetworkSpeedAdapter();
        window.networkAdapter = networkAdapter;
    });
} else {
    networkAdapter = new NetworkSpeedAdapter();
    window.networkAdapter = networkAdapter;
}

// Export
window.NetworkSpeedAdapter = NetworkSpeedAdapter;
