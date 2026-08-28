/**
 * Navigation Skeleton Manager
 * Affiche un skeleton lors de la navigation entre les pages
 * VintApp PWA - 2025
 */

class NavigationSkeletonManager {
    constructor(options = {}) {
        this.options = {
            enabledSelectors: 'a[href]:not([target="_blank"]):not([data-no-skeleton])',
            excludePatterns: ['/logout', '/login', '/register', '#'],
            minDisplayTime: 300,
            maxWaitTime: 5000,
            detectPageType: true,
            ...options
        };

        this.isNavigating = false;
        this.currentSkeleton = null;
        this.navigationStartTime = null;
        
        this.init();
    }

    init() {
        // Intercepter les clics sur les liens
        document.addEventListener('click', (e) => this.handleLinkClick(e), true);
        
        // Intercepter l'événement popstate (boutons back/forward)
        window.addEventListener('popstate', () => this.handlePopState());
        
        // Écouter les changements de hash
        window.addEventListener('hashchange', () => this.handleHashChange());
    }

    handleLinkClick(e) {
        // Trouver le lien cliqué
        const link = e.target.closest('a');
        if (!link) return;

        // Vérifier si c'est un lien éligible
        if (!this.shouldInterceptLink(link)) return;

        const href = link.getAttribute('href');
        
        // Ignorer les liens externes et ceux qui matchent les patterns exclus
        if (this.shouldExcludeHref(href)) return;

        // Empêcher la navigation par défaut
        e.preventDefault();
        e.stopPropagation();

        // Démarrer la navigation avec skeleton
        this.navigateWithSkeleton(href, link);
    }

    shouldInterceptLink(link) {
        // Ne pas intercepter si déjà en cours de navigation
        if (this.isNavigating) return false;

        // Vérifier le sélecteur
        if (!link.matches(this.options.enabledSelectors)) return false;

        // Vérifier si le lien a l'attribut data-no-skeleton
        if (link.hasAttribute('data-no-skeleton')) return false;

        // Ne pas intercepter les liens de téléchargement
        if (link.hasAttribute('download')) return false;

        return true;
    }

    shouldExcludeHref(href) {
        if (!href || href === '#') return true;

        // Vérifier les patterns exclus
        return this.options.excludePatterns.some(pattern => {
            if (typeof pattern === 'string') {
                return href.includes(pattern);
            } else if (pattern instanceof RegExp) {
                return pattern.test(href);
            }
            return false;
        });
    }

    handlePopState() {
        // Lors de la navigation back/forward, recharger la page
        // Le skeleton initial se chargera naturellement
        window.location.reload();
    }

    handleHashChange() {
        // Ne rien faire pour les changements de hash
        // Ils ne nécessitent pas de skeleton
    }

    async navigateWithSkeleton(href, linkElement) {
        this.isNavigating = true;
        this.navigationStartTime = Date.now();

        try {
            // Déterminer le type de skeleton à afficher
            const skeletonType = this.detectSkeletonType(href, linkElement);
            
            // Afficher le skeleton
            await this.showSkeleton(skeletonType);

            // Attendre le temps minimum
            await this.ensureMinimumDisplayTime();

            // Naviguer vers la nouvelle page
            window.location.href = href;

        } catch (error) {
            // En cas d'erreur, naviguer quand même
            window.location.href = href;
        }
    }

    detectSkeletonType(href, linkElement) {
        // 1. Vérifier l'attribut data-skeleton-type sur le lien
        const dataType = linkElement?.getAttribute('data-skeleton-type');
        if (dataType) return dataType;

        // 2. Détecter basé sur l'URL
        if (href.includes('/items/') && !href.endsWith('/items')) {
            return 'product-detail';
        } else if (href.includes('/items')) {
            return 'product-grid';
        } else if (href.includes('/dashboard')) {
            return 'dashboard';
        } else if (href.includes('/orders') || href.includes('/messages') || href.includes('/notifications')) {
            return 'list';
        } else if (href.includes('/profile') || href.includes('/settings')) {
            return 'profile';
        }

        // 3. Détecter basé sur les classes du lien
        if (linkElement?.classList.contains('product-link')) {
            return 'product-detail';
        } else if (linkElement?.classList.contains('category-link')) {
            return 'product-grid';
        }

        // 4. Par défaut, utiliser une page générique
        return 'generic';
    }

    async showSkeleton(type) {
        // Créer une instance de PageSkeletonLoader
        if (!window.PageSkeletonLoader) {
            return;
        }

        this.currentSkeleton = new PageSkeletonLoader({
            minDisplayTime: this.options.minDisplayTime,
            containerSelector: 'body'
        });

        // Afficher le skeleton approprié
        switch (type) {
            case 'product-grid':
                this.currentSkeleton.showProductGrid(12);
                break;
            
            case 'product-detail':
                this.currentSkeleton.showProductDetail();
                break;
            
            case 'dashboard':
                this.currentSkeleton.showDashboard();
                break;
            
            case 'list':
                this.currentSkeleton.showList(10);
                break;
            
            case 'profile':
                this.showProfileSkeleton();
                break;
            
            case 'generic':
            default:
                this.showGenericSkeleton();
                break;
        }

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    showProfileSkeleton() {
        const template = `
            <div class="max-w-4xl mx-auto px-4 py-8">
                <!-- Header avec avatar -->
                <div class="skeleton-card p-6 mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="skeleton-loader skeleton-avatar" style="width:5rem;height:5rem"></div>
                        <div class="flex-1">
                            <div class="skeleton-loader skeleton-title w-48 mb-2"></div>
                            <div class="skeleton-loader skeleton-text w-64"></div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="flex space-x-3 mb-6">
                    ${[...Array(4)].map(() => `
                        <div class="skeleton-loader skeleton-badge" style="width:6rem;height:2.5rem"></div>
                    `).join('')}
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    ${[...Array(6)].map(() => `
                        <div class="skeleton-card p-4">
                            <div class="skeleton-loader skeleton-text w-32 mb-3"></div>
                            <div class="skeleton-loader skeleton-text w-full mb-2"></div>
                            <div class="skeleton-loader skeleton-text w-3/4"></div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;

        this.currentSkeleton.showCustom(template);
    }

    showGenericSkeleton() {
        const template = `
            <div class="max-w-7xl mx-auto px-4 py-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="skeleton-loader skeleton-title w-64 mb-4"></div>
                    <div class="skeleton-loader skeleton-text w-96 mb-2"></div>
                    <div class="skeleton-loader skeleton-text w-80"></div>
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-4">
                        ${[...Array(4)].map(() => `
                            <div class="skeleton-card p-6">
                                <div class="skeleton-loader skeleton-text w-48 mb-4"></div>
                                <div class="skeleton-loader skeleton-text w-full mb-2"></div>
                                <div class="skeleton-loader skeleton-text w-full mb-2"></div>
                                <div class="skeleton-loader skeleton-text w-3/4"></div>
                            </div>
                        `).join('')}
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-4">
                        ${[...Array(3)].map(() => `
                            <div class="skeleton-card p-4">
                                <div class="skeleton-loader skeleton-text w-32 mb-3"></div>
                                <div class="skeleton-loader" style="height:8rem;margin-bottom:0.5rem"></div>
                                <div class="skeleton-loader skeleton-text w-full"></div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
        `;

        this.currentSkeleton.showCustom(template);
    }

    async ensureMinimumDisplayTime() {
        const elapsed = Date.now() - this.navigationStartTime;
        const remaining = this.options.minDisplayTime - elapsed;

        if (remaining > 0) {
            await new Promise(resolve => setTimeout(resolve, remaining));
        }
    }

    // Méthode publique pour désactiver temporairement
    disable() {
        this.isNavigating = true;
    }

    // Méthode publique pour réactiver
    enable() {
        this.isNavigating = false;
    }

    // Méthode pour exclure dynamiquement des patterns
    addExcludePattern(pattern) {
        this.options.excludePatterns.push(pattern);
    }
}

// Auto-initialisation
let navigationSkeletonManager;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        navigationSkeletonManager = new NavigationSkeletonManager();
        window.navigationSkeletonManager = navigationSkeletonManager;
    });
} else {
    navigationSkeletonManager = new NavigationSkeletonManager();
    window.navigationSkeletonManager = navigationSkeletonManager;
}

// Export pour usage global
window.NavigationSkeletonManager = NavigationSkeletonManager;
