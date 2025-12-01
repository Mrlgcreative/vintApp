/**
 * Page Skeleton Loader
 * Affiche des skeletons de chargement pour améliorer l'UX
 * VintApp PWA - 2025
 */

class PageSkeletonLoader {
    constructor(options = {}) {
        this.options = {
            containerSelector: options.containerSelector || 'body',
            skeletonClass: options.skeletonClass || 'page-skeleton',
            fadeOutDuration: options.fadeOutDuration || 300,
            minDisplayTime: options.minDisplayTime || 400,
            ...options
        };
        
        this.skeletonElement = null;
        this.displayStartTime = null;
        this.visible = false;
    }

    /**
     * Affiche un skeleton de grille de produits
     */
    showProductGrid(count = 12) {
        const template = `
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                    ${[...Array(count)].map(() => `
                        <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-sm">
                            <div class="skeleton-loader skeleton-image h-48"></div>
                            <div class="p-4">
                                <div class="skeleton-loader skeleton-title w-3/4 mb-2"></div>
                                <div class="skeleton-loader skeleton-text w-1/2 mb-4"></div>
                                <div class="skeleton-loader skeleton-button w-full"></div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
        this.show(template);
    }

    /**
     * Affiche un skeleton de détail produit
     */
    showProductDetail() {
        const template = `
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Images -->
                    <div>
                        <div class="skeleton-loader skeleton-image h-96 mb-4"></div>
                        <div class="grid grid-cols-4 gap-2">
                            ${[...Array(4)].map(() => `
                                <div class="skeleton-loader skeleton-image h-20"></div>
                            `).join('')}
                        </div>
                    </div>
                    <!-- Info -->
                    <div>
                        <div class="skeleton-loader skeleton-title w-3/4 mb-4"></div>
                        <div class="skeleton-loader skeleton-text w-1/3 mb-6"></div>
                        <div class="skeleton-loader skeleton-text w-full mb-2"></div>
                        <div class="skeleton-loader skeleton-text w-full mb-2"></div>
                        <div class="skeleton-loader skeleton-text w-2/3 mb-6"></div>
                        <div class="skeleton-loader skeleton-button w-full mb-4"></div>
                        <div class="skeleton-loader skeleton-button w-full"></div>
                    </div>
                </div>
            </div>
        `;
        this.show(template);
    }

    /**
     * Affiche un skeleton de dashboard
     */
    showDashboard() {
        const template = `
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    ${[...Array(4)].map(() => `
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                            <div class="skeleton-loader skeleton-text w-32 mb-4"></div>
                            <div class="skeleton-loader skeleton-title w-20 mb-2"></div>
                            <div class="skeleton-loader skeleton-text w-24"></div>
                        </div>
                    `).join('')}
                </div>
                <!-- Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm mb-8">
                    <div class="skeleton-loader skeleton-title w-48 mb-6"></div>
                    <div class="skeleton-loader h-64"></div>
                </div>
                <!-- Table -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm">
                    <div class="skeleton-loader skeleton-title w-64 mb-6"></div>
                    ${[...Array(5)].map(() => `
                        <div class="skeleton-loader skeleton-text w-full mb-4"></div>
                    `).join('')}
                </div>
            </div>
        `;
        this.show(template);
    }

    /**
     * Affiche un skeleton de liste
     */
    showList(count = 10) {
        const template = `
            <div class="max-w-4xl mx-auto px-4 py-8">
                <div class="skeleton-loader skeleton-title w-64 mb-8"></div>
                <div class="space-y-4">
                    ${[...Array(count)].map(() => `
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm flex items-center gap-4">
                            <div class="skeleton-loader skeleton-avatar"></div>
                            <div class="flex-1">
                                <div class="skeleton-loader skeleton-text w-48 mb-2"></div>
                                <div class="skeleton-loader skeleton-text w-64"></div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
        this.show(template);
    }

    /**
     * Affiche un template personnalisé
     */
    showCustom(htmlTemplate) {
        this.show(htmlTemplate);
    }

    /**
     * Affiche le skeleton
     */
    show(template) {
        this.displayStartTime = Date.now();
        this.visible = true;

        // Créer l'élément skeleton
        this.skeletonElement = document.createElement('div');
        this.skeletonElement.className = this.options.skeletonClass;
        this.skeletonElement.innerHTML = template;
        this.skeletonElement.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #f9fafb;
            z-index: 9999;
            overflow-y: auto;
            opacity: 1;
            transition: opacity ${this.options.fadeOutDuration}ms ease-out;
        `;

        // Mode sombre
        if (document.documentElement.classList.contains('dark')) {
            this.skeletonElement.style.background = '#111827';
        }

        // Ajouter au DOM
        const container = document.querySelector(this.options.containerSelector);
        if (container) {
            container.appendChild(this.skeletonElement);
        }
    }

    /**
     * Cache le skeleton avec animation
     */
    async hide() {
        if (!this.visible || !this.skeletonElement) return;

        // Attendre le temps minimum d'affichage
        await this.ensureMinimumDisplayTime();

        // Animation de sortie
        this.skeletonElement.style.opacity = '0';

        setTimeout(() => {
            if (this.skeletonElement && this.skeletonElement.parentNode) {
                this.skeletonElement.parentNode.removeChild(this.skeletonElement);
            }
            this.skeletonElement = null;
            this.visible = false;
        }, this.options.fadeOutDuration);
    }

    /**
     * Cache immédiatement sans animation
     */
    forceHide() {
        if (this.skeletonElement && this.skeletonElement.parentNode) {
            this.skeletonElement.parentNode.removeChild(this.skeletonElement);
        }
        this.skeletonElement = null;
        this.visible = false;
    }

    /**
     * Vérifie si le skeleton est visible
     */
    isVisible() {
        return this.visible;
    }

    /**
     * Assure un temps minimum d'affichage
     */
    async ensureMinimumDisplayTime() {
        if (!this.displayStartTime) return;

        const elapsed = Date.now() - this.displayStartTime;
        const remaining = this.options.minDisplayTime - elapsed;

        if (remaining > 0) {
            await new Promise(resolve => setTimeout(resolve, remaining));
        }
    }
}

// Auto-hide sur chargement complet de la page
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        if (window.pageSkeleton) {
            setTimeout(() => window.pageSkeleton.hide(), 100);
        }
    });
} else {
    if (window.pageSkeleton) {
        setTimeout(() => window.pageSkeleton.hide(), 100);
    }
}

// Export global
if (typeof window !== 'undefined') {
    window.PageSkeletonLoader = PageSkeletonLoader;
}
