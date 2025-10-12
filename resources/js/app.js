// VintApp JavaScript - Optimisé pour la performance

// Import des styles CSS
import '../css/app.css';

// Import de Laravel Echo et Pusher (pour les notifications temps réel)
import './bootstrap';

// Utilitaires de performance
const Utils = {
    // Debounce pour limiter les appels fréquents
    debounce(func, wait = 300) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    // Throttle pour limiter la fréquence d'exécution
    throttle(func, limit = 100) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    },

    // Cache pour les sélecteurs DOM
    cache: new Map(),
    
    getCached(selector) {
        if (!this.cache.has(selector)) {
            this.cache.set(selector, document.querySelector(selector));
        }
        return this.cache.get(selector);
    },

    // RequestAnimationFrame pour les animations fluides
    raf(callback) {
        return requestAnimationFrame(callback);
    }
};

// Gestion optimisée du thème
class ThemeManager {
    constructor() {
        this.currentTheme = this.getStoredTheme();
        this.mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        this.csrfToken = null;
        this.themeToggleBtn = null;
        this.init();
    }

    init() {
        // Application immédiate du thème (avant DOMContentLoaded)
        this.applyTheme();
        
        // Différer les autres initialisations
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupListeners(), { once: true });
        } else {
            this.setupListeners();
        }
    }

    setupListeners() {
        this.csrfToken = Utils.getCached('meta[name="csrf-token"]')?.getAttribute('content');
        this.themeToggleBtn = Utils.getCached('#theme-toggle');
        
        if (this.themeToggleBtn) {
            this.themeToggleBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleTheme();
            }, { passive: false });
        }

        // Écouter les changements de préférence système
        this.mediaQuery.addEventListener('change', () => {
            if (this.getStoredTheme() === 'auto') {
                Utils.raf(() => this.applyTheme());
            }
        });
    }

    getStoredTheme() {
        return localStorage.getItem('theme') || 'auto';
    }

    setStoredTheme(theme) {
        localStorage.setItem('theme', theme);
    }

    getEffectiveTheme() {
        const theme = this.getStoredTheme();
        return theme === 'auto' 
            ? (this.mediaQuery.matches ? 'dark' : 'light')
            : theme;
    }

    applyTheme() {
        const effectiveTheme = this.getEffectiveTheme();
        const docElement = document.documentElement;
        
        // Batch DOM updates
        Utils.raf(() => {
            docElement.classList.remove('light', 'dark');
            docElement.classList.add(effectiveTheme);
            docElement.setAttribute('data-theme', effectiveTheme);
        });
    }

    async toggleTheme() {
        if (!this.csrfToken) return;

        try {
            const response = await fetch('/theme/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                },
                credentials: 'same-origin'
            });

            if (response.ok) {
                const data = await response.json();
                this.setStoredTheme(data.theme);
                this.applyTheme();
                this.updateThemeIcon();
                this.showNotification(data.message);
            }
        } catch (error) {
            console.error('Erreur lors du changement de thème:', error);
        }
    }

    updateThemeIcon() {
        if (!this.themeToggleBtn) return;
        
        const icon = this.themeToggleBtn.querySelector('i');
        const theme = this.getStoredTheme();
        
        if (icon) {
            Utils.raf(() => {
                icon.className = this.getThemeIcon(theme);
            });
        }
    }

    getThemeIcon(theme) {
        const icons = {
            'light': 'fas fa-sun',
            'dark': 'fas fa-moon',
            'auto': 'fas fa-adjust'
        };
        return icons[theme] || icons.auto;
    }

    showNotification(message) {
        // Vérifier si une notification existe déjà
        const existingToast = document.querySelector('.theme-toast');
        if (existingToast) {
            existingToast.remove();
        }

        const toast = document.createElement('div');
        toast.className = 'theme-toast';
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas fa-check-circle me-2"></i>
                ${message}
            </div>
        `;
        
        Utils.raf(() => {
            document.body.appendChild(toast);
            Utils.raf(() => toast.classList.add('show'));
        });
        
        setTimeout(() => {
            Utils.raf(() => toast.classList.remove('show'));
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}


// Initialisation optimisée au chargement du DOM
class DashboardApp {
    constructor() {
        this.initialized = false;
        this.init();
    }

    init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.onReady(), { once: true });
        } else {
            this.onReady();
        }
    }

    onReady() {
        if (this.initialized) return;
        this.initialized = true;

        // Initialiser le gestionnaire de thème
        new ThemeManager();
        
        // Initialiser les fonctionnalités par priorité
        this.initCriticalFeatures();
        
        // Différer les fonctionnalités non-critiques
        requestIdleCallback(() => this.initNonCriticalFeatures(), { timeout: 2000 });
    }

    initCriticalFeatures() {
        // Animations des cartes (visible immédiatement)
        this.animateDashboardCards();
        
        // Event delegation pour les clics (meilleure performance)
        this.setupEventDelegation();
    }

    initNonCriticalFeatures() {
        // Bootstrap components (différés)
        this.initBootstrapComponents();
        
        // Autres fonctionnalités
        this.initSearch();
        this.initFilters();
    }

    animateDashboardCards() {
        const cards = document.querySelectorAll('.dashboard-card');
        if (cards.length === 0) return;

        // Utiliser IntersectionObserver pour lazy loading des animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    Utils.raf(() => {
                        entry.target.style.animationDelay = `${index * 0.1}s`;
                        entry.target.classList.add('fade-in');
                    });
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        cards.forEach(card => observer.observe(card));
    }

    setupEventDelegation() {
        // Event delegation pour les notifications
        document.addEventListener('click', (e) => {
            const notificationItem = e.target.closest('.notification-item');
            if (notificationItem) {
                Utils.raf(() => notificationItem.classList.add('read'));
                return;
            }

            const messageItem = e.target.closest('.message-item');
            if (messageItem) {
                Utils.raf(() => messageItem.classList.add('read'));
                return;
            }

            const favoriteBtn = e.target.closest('.favorite-btn');
            if (favoriteBtn) {
                e.preventDefault();
                this.handleFavoriteClick(favoriteBtn);
                return;
            }

            const filterToggle = e.target.closest('.filter-toggle');
            if (filterToggle) {
                this.handleFilterToggle(filterToggle);
                return;
            }

            const confirmBtn = e.target.closest('[data-confirm]');
            if (confirmBtn) {
                const message = confirmBtn.getAttribute('data-confirm');
                if (!confirm(message)) {
                    e.preventDefault();
                }
            }
        }, { passive: false });
    }

    handleFavoriteClick(btn) {
        Utils.raf(() => {
            btn.classList.toggle('active');
            
            const icon = btn.querySelector('i');
            if (icon) {
                icon.style.transform = 'scale(1.2)';
                setTimeout(() => {
                    Utils.raf(() => {
                        icon.style.transform = 'scale(1)';
                    });
                }, 200);
            }
        });
    }

    handleFilterToggle(toggle) {
        const filterSection = toggle.closest('.filter-section');
        const filterContent = filterSection?.querySelector('.filter-content');
        
        if (filterContent) {
            Utils.raf(() => {
                filterContent.style.display = 
                    filterContent.style.display === 'none' ? 'block' : 'none';
            });
        }
    }

    initBootstrapComponents() {
        // Tooltips
        const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        if (tooltipElements.length > 0 && typeof bootstrap !== 'undefined') {
            tooltipElements.forEach(el => new bootstrap.Tooltip(el));
        }

        // Popovers
        const popoverElements = document.querySelectorAll('[data-bs-toggle="popover"]');
        if (popoverElements.length > 0 && typeof bootstrap !== 'undefined') {
            popoverElements.forEach(el => new bootstrap.Popover(el));
        }
    }

    initSearch() {
        const searchForm = document.querySelector('.search-form');
        if (!searchForm) return;

        const searchInput = searchForm.querySelector('.search-input');
        if (!searchInput) return;

        // Debounce pour la recherche en temps réel
        const debouncedSearch = Utils.debounce((value) => {
            if (value.trim() === '') return;
            // Logique de recherche ici
            console.log('Recherche:', value);
        }, 300);

        searchInput.addEventListener('input', (e) => {
            debouncedSearch(e.target.value);
        }, { passive: true });

        searchForm.addEventListener('submit', (e) => {
            if (searchInput.value.trim() === '') {
                e.preventDefault();
                searchInput.focus();
            }
        });
    }

    initFilters() {
        // Déjà géré par event delegation
        // Cette fonction peut contenir une logique supplémentaire si nécessaire
    }

    initCharts() {
        // Optimisation des graphiques
        const chartBars = document.querySelectorAll('.chart-bar');
        if (chartBars.length === 0) return;

        // Utiliser requestAnimationFrame pour des animations fluides
        chartBars.forEach(bar => {
            const height = bar.getAttribute('data-height');
            if (height) {
                Utils.raf(() => {
                    bar.style.height = height + '%';
                });
            }
        });
    }
}

// Initialisation de l'application
new DashboardApp();

