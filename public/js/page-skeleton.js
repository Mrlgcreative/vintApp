/**
 * Page Skeleton Loader pour VintApp
 * Affiche un skeleton complet pendant le chargement des données
 */

class PageSkeletonLoader {
    constructor(options = {}) {
        this.options = {
            containerSelector: options.containerSelector || 'body',
            skeletonClass: options.skeletonClass || 'page-skeleton',
            fadeOutDuration: options.fadeOutDuration || 300,
            minDisplayTime: options.minDisplayTime || 500, // Minimum 500ms pour éviter le flash
            ...options
        };

        this.startTime = Date.now();
        this.isLoading = true;
    }

    /**
     * Affiche le skeleton pour une grille de produits
     */
    showProductGrid(count = 12) {
        const skeleton = `
            <div class="${this.options.skeletonClass} fixed inset-0 bg-gray-50 dark:bg-gray-900 z-50 overflow-y-auto" id="productGridSkeleton">
                <div class="max-w-7xl mx-auto px-4 py-8">
                    <!-- Header Skeleton -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center">
                            <div class="skeleton-loader skeleton-title w-64 mb-4"></div>
                            <div class="skeleton-loader skeleton-button"></div>
                        </div>
                    </div>

                    <!-- Search Bar Skeleton -->
                    <div class="mb-8 max-w-3xl mx-auto">
                        <div class="skeleton-loader h-16 rounded-2xl"></div>
                    </div>

                    <!-- Products Grid Skeleton -->
                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        ${this.generateProductCards(count)}
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', skeleton);
    }

    /**
     * Génère des cards skeleton pour produits
     */
    generateProductCards(count) {
        let cards = '';
        for (let i = 0; i < count; i++) {
            cards += `
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                    <div class="skeleton-loader h-32"></div>
                    <div class="p-3">
                        <div class="skeleton-loader skeleton-text mb-2"></div>
                        <div class="skeleton-loader skeleton-text w-3/4 mb-2"></div>
                        <div class="flex justify-between items-center mt-3">
                            <div class="skeleton-loader skeleton-text w-20"></div>
                            <div class="skeleton-loader skeleton-avatar w-8 h-8"></div>
                        </div>
                    </div>
                </div>
            `;
        }
        return cards;
    }

    /**
     * Affiche le skeleton pour une page de détails produit
     */
    showProductDetail() {
        const skeleton = `
            <div class="${this.options.skeletonClass} fixed inset-0 bg-gray-50 dark:bg-gray-900 z-50 overflow-y-auto" id="productDetailSkeleton">
                <div class="max-w-7xl mx-auto px-4 py-8">
                    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
                        <!-- Images Column -->
                        <div class="xl:col-span-7 space-y-4">
                            <!-- Main Image -->
                            <div class="skeleton-loader h-96 rounded-2xl"></div>
                            <!-- Thumbnails -->
                            <div class="grid grid-cols-4 gap-2">
                                ${Array(4).fill('<div class="skeleton-loader h-20 rounded-lg"></div>').join('')}
                            </div>
                        </div>

                        <!-- Product Info Column -->
                        <div class="xl:col-span-5">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl">
                                <!-- Title -->
                                <div class="skeleton-loader skeleton-title mb-4"></div>
                                <div class="skeleton-loader skeleton-text w-3/4 mb-6"></div>
                                
                                <!-- Price -->
                                <div class="skeleton-loader h-12 w-32 mb-6"></div>
                                
                                <!-- Tags -->
                                <div class="flex gap-2 mb-6">
                                    <div class="skeleton-loader w-24 h-8 rounded-full"></div>
                                    <div class="skeleton-loader w-20 h-8 rounded-full"></div>
                                </div>
                                
                                <!-- Description -->
                                <div class="space-y-2 mb-6">
                                    <div class="skeleton-loader skeleton-text"></div>
                                    <div class="skeleton-loader skeleton-text"></div>
                                    <div class="skeleton-loader skeleton-text w-2/3"></div>
                                </div>
                                
                                <!-- Buttons -->
                                <div class="space-y-3">
                                    <div class="skeleton-loader h-12 rounded-xl"></div>
                                    <div class="skeleton-loader h-12 rounded-xl"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', skeleton);
    }

    /**
     * Affiche le skeleton pour une liste générique
     */
    showList(count = 10) {
        const skeleton = `
            <div class="${this.options.skeletonClass} fixed inset-0 bg-gray-50 dark:bg-gray-900 z-50 overflow-y-auto" id="listSkeleton">
                <div class="max-w-4xl mx-auto px-4 py-8">
                    <div class="skeleton-loader skeleton-title w-64 mb-6"></div>
                    <div class="space-y-4">
                        ${this.generateListItems(count)}
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', skeleton);
    }

    /**
     * Génère des items de liste skeleton
     */
    generateListItems(count) {
        let items = '';
        for (let i = 0; i < count; i++) {
            items += `
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-md">
                    <div class="flex items-center gap-4">
                        <div class="skeleton-loader skeleton-avatar"></div>
                        <div class="flex-1 space-y-2">
                            <div class="skeleton-loader skeleton-text"></div>
                            <div class="skeleton-loader skeleton-text w-3/4"></div>
                        </div>
                    </div>
                </div>
            `;
        }
        return items;
    }

    /**
     * Affiche le skeleton pour un dashboard
     */
    showDashboard() {
        const skeleton = `
            <div class="${this.options.skeletonClass} fixed inset-0 bg-gray-50 dark:bg-gray-900 z-50 overflow-y-auto" id="dashboardSkeleton">
                <div class="max-w-7xl mx-auto px-4 py-8">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        ${Array(4).fill(`
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
                                <div class="skeleton-loader skeleton-text w-20 mb-4"></div>
                                <div class="skeleton-loader h-10 w-24 mb-2"></div>
                                <div class="skeleton-loader skeleton-text w-16"></div>
                            </div>
                        `).join('')}
                    </div>

                    <!-- Chart -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md mb-8">
                        <div class="skeleton-loader skeleton-title w-48 mb-4"></div>
                        <div class="skeleton-loader h-64 rounded-lg"></div>
                    </div>

                    <!-- Table -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
                        <div class="skeleton-loader skeleton-title w-64 mb-4"></div>
                        <div class="space-y-3">
                            ${Array(5).fill('<div class="skeleton-loader h-12 rounded"></div>').join('')}
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', skeleton);
    }

    /**
     * Affiche un skeleton personnalisé
     */
    showCustom(template) {
        const skeleton = `
            <div class="${this.options.skeletonClass} fixed inset-0 bg-gray-50 dark:bg-gray-900 z-50 overflow-y-auto" id="customSkeleton">
                ${template}
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', skeleton);
    }

    /**
     * Cache le skeleton avec animation
     */
    hide() {
        const elapsed = Date.now() - this.startTime;
        const remainingTime = Math.max(0, this.options.minDisplayTime - elapsed);

        setTimeout(() => {
            const skeleton = document.querySelector(`.${this.options.skeletonClass}`);
            if (skeleton) {
                skeleton.style.transition = `opacity ${this.options.fadeOutDuration}ms ease-out`;
                skeleton.style.opacity = '0';

                setTimeout(() => {
                    skeleton.remove();
                    this.isLoading = false;
                    
                    // Événement personnalisé
                    document.dispatchEvent(new CustomEvent('skeletonHidden'));
                }, this.options.fadeOutDuration);
            }
        }, remainingTime);
    }

    /**
     * Vérifie si le skeleton est visible
     */
    isVisible() {
        return this.isLoading && document.querySelector(`.${this.options.skeletonClass}`) !== null;
    }

    /**
     * Force la suppression immédiate du skeleton
     */
    forceHide() {
        const skeleton = document.querySelector(`.${this.options.skeletonClass}`);
        if (skeleton) {
            skeleton.remove();
            this.isLoading = false;
        }
    }

    /**
     * Affiche un skeleton personnalisé avec du HTML
     */
    showCustom(htmlTemplate) {
        const skeleton = `
            <div class="${this.options.skeletonClass} fixed inset-0 bg-gray-50 dark:bg-gray-900 z-50 overflow-y-auto" id="customSkeleton">
                ${htmlTemplate}
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', skeleton);
    }
}

// Export global
window.PageSkeletonLoader = PageSkeletonLoader;

// Utilitaires pour détecter le type de page
const PageDetector = {
    isProductGrid() {
        // Vérifier d'abord l'attribut data
        const pageType = document.querySelector('[data-page-type="product-grid"]');
        if (pageType) return true;
        
        // Fallback sur l'URL
        return window.location.pathname.includes('/items') && 
               !window.location.pathname.match(/\/items\/\d+/);
    },

    isProductDetail() {
        const pageType = document.querySelector('[data-page-type="product-detail"]');
        if (pageType) return true;
        
        return window.location.pathname.match(/\/items\/\d+/);
    },

    isDashboard() {
        const pageType = document.querySelector('[data-page-type="dashboard"]');
        if (pageType) return true;
        
        return window.location.pathname.includes('/dashboard');
    },

    isList() {
        const pageType = document.querySelector('[data-page-type="list"]');
        if (pageType) return true;
        
        return window.location.pathname.includes('/orders') || 
               window.location.pathname.includes('/messages');
    },

    getPageType() {
        const element = document.querySelector('[data-page-type]');
        return element ? element.getAttribute('data-page-type') : null;
    }
};

// Auto-initialisation basée sur le type de page
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPageSkeleton);
} else {
    initPageSkeleton();
}

function initPageSkeleton() {
    // Ne pas afficher le skeleton si la page est déjà chargée depuis le cache
    if (performance.navigation.type === performance.navigation.TYPE_BACK_FORWARD) {
        return;
    }

    // Ne pas afficher si le DOM est déjà complètement chargé
    if (document.readyState === 'complete') {
        return;
    }

    const skeleton = new PageSkeletonLoader({
        minDisplayTime: 400 // Minimum 400ms pour éviter le flash
    });

    // Détecter le type de page via l'attribut data ou l'URL
    const pageType = PageDetector.getPageType();
    
    if (pageType === 'product-grid' || PageDetector.isProductGrid()) {
        skeleton.showProductGrid(12);
    } else if (pageType === 'product-detail' || PageDetector.isProductDetail()) {
        skeleton.showProductDetail();
    } else if (pageType === 'dashboard' || PageDetector.isDashboard()) {
        skeleton.showDashboard();
    } else if (pageType === 'list' || PageDetector.isList()) {
        skeleton.showList(10);
    } else {
        // Ne rien afficher pour les pages non reconnues
        return;
    }

    // Cacher le skeleton quand la page est complètement chargée
    window.addEventListener('load', () => {
        skeleton.hide();
    });

    // Fallback: cacher après 3 secondes max
    setTimeout(() => {
        if (skeleton.isVisible()) {
            skeleton.hide();
        }
    }, 3000);

    // Exposer globalement pour utilisation manuelle
    window.pageSkeleton = skeleton;
}

// Export pour utilisation en module
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { PageSkeletonLoader, PageDetector };
}
