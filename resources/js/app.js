// VintApp JavaScript

// Import des styles CSS
import '../css/app.css';

// Gestion du thème
class ThemeManager {
    constructor() {
        this.currentTheme = this.getStoredTheme();
        this.init();
    }

    init() {
        this.applyTheme();
        this.setupThemeToggle();
        this.detectSystemTheme();
    }

    getStoredTheme() {
        return localStorage.getItem('theme') || 'auto';
    }

    setStoredTheme(theme) {
        localStorage.setItem('theme', theme);
    }

    getEffectiveTheme() {
        const theme = this.getStoredTheme();
        if (theme === 'auto') {
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        return theme;
    }

    applyTheme() {
        const effectiveTheme = this.getEffectiveTheme();
        document.documentElement.classList.remove('light', 'dark');
        document.documentElement.classList.add(effectiveTheme);
        document.documentElement.setAttribute('data-theme', effectiveTheme);
    }

    setupThemeToggle() {
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleTheme();
            });
        }
    }

    async toggleTheme() {
        try {
            const response = await fetch('/theme/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
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
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            const icon = themeToggle.querySelector('i');
            const theme = this.getStoredTheme();
            
            if (icon) {
                icon.className = this.getThemeIcon(theme);
            }
        }
    }

    getThemeIcon(theme) {
        switch (theme) {
            case 'light':
                return 'fas fa-sun';
            case 'dark':
                return 'fas fa-moon';
            case 'auto':
            default:
                return 'fas fa-adjust';
        }
    }

    detectSystemTheme() {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        mediaQuery.addEventListener('change', () => {
            if (this.getStoredTheme() === 'auto') {
                this.applyTheme();
            }
        });
    }

    showNotification(message) {
        // Créer une notification toast
        const toast = document.createElement('div');
        toast.className = 'theme-toast';
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas fa-check-circle me-2"></i>
                ${message}
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Animation d'entrée
        setTimeout(() => toast.classList.add('show'), 100);
        
        // Supprimer après 3 secondes
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => document.body.removeChild(toast), 300);
        }, 3000);
    }
}

// Fonctionnalités JavaScript pour le dashboard
document.addEventListener('DOMContentLoaded', function() {
    
    // Initialiser le gestionnaire de thème
    const themeManager = new ThemeManager();
    
    // Initialisation des tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialisation des popovers Bootstrap
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Animation des cartes du dashboard
    const dashboardCards = document.querySelectorAll('.dashboard-card');
    dashboardCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('fade-in');
    });

    // Gestion des notifications
    const notificationItems = document.querySelectorAll('.notification-item');
    notificationItems.forEach(item => {
        item.addEventListener('click', function() {
            // Marquer comme lu
            this.classList.add('read');
        });
    });

    // Gestion des messages
    const messageItems = document.querySelectorAll('.message-item');
    messageItems.forEach(item => {
        item.addEventListener('click', function() {
            // Marquer comme lu
            this.classList.add('read');
        });
    });

    // Gestion des favoris
    const favoriteBtns = document.querySelectorAll('.favorite-btn');
    favoriteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            this.classList.toggle('active');
            
            // Animation de l'icône
            const icon = this.querySelector('i');
            if (icon) {
                icon.style.transform = 'scale(1.2)';
                setTimeout(() => {
                    icon.style.transform = 'scale(1)';
                }, 200);
            }
        });
    });

    // Gestion de la recherche
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const searchInput = this.querySelector('.search-input');
            if (searchInput && searchInput.value.trim() === '') {
                e.preventDefault();
                searchInput.focus();
            }
        });
    }

    // Gestion des filtres
    const filterToggles = document.querySelectorAll('.filter-toggle');
    filterToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const filterSection = this.closest('.filter-section');
            const filterContent = filterSection.querySelector('.filter-content');
            
            if (filterContent) {
                filterContent.style.display = 
                    filterContent.style.display === 'none' ? 'block' : 'none';
            }
        });
    });

    // Gestion des modales de confirmation
    const confirmButtons = document.querySelectorAll('[data-confirm]');
    confirmButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm');
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // Gestion des graphiques (placeholder)
    const chartBars = document.querySelectorAll('.chart-bar');
    chartBars.forEach(bar => {
        const height = bar.getAttribute('data-height');
        if (height) {
            bar.style.height = height + '%';
        }
    });
});
