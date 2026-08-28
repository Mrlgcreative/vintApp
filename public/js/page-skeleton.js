/**
 * Page Skeleton Loader
 * Affiche des skeletons de chargement pour améliorer l'UX
 * VintApp PWA - 2025
 */

class PageSkeletonLoader {
    constructor(options = {}) {
        this.options = {
            containerSelector: options.containerSelector || "body",
            skeletonClass: options.skeletonClass || "page-skeleton",
            fadeOutDuration: options.fadeOutDuration || 300,
            minDisplayTime: options.minDisplayTime || 400,
            ...options,
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
                <div class="mb-6 flex items-center justify-between">
                    <div class="skeleton-loader skeleton-title w-48"></div>
                    <div class="hidden sm:flex space-x-2">
                        ${[...Array(4)].map(() => `<div class="skeleton-loader skeleton-badge"></div>`).join("")}
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                    ${[...Array(count)]
                        .map(
                            () => `
                        <div class="skeleton-card">
                            <div class="skeleton-loader skeleton-image" style="height:0;padding-bottom:100%"></div>
                            <div class="p-4">
                                <div class="skeleton-loader skeleton-title w-3/4 mb-2"></div>
                                <div class="skeleton-loader skeleton-text w-1/2 mb-4"></div>
                                <div class="flex items-center justify-between pt-3">
                                    <div class="skeleton-loader skeleton-text w-16 mb-0"></div>
                                    <div class="skeleton-loader skeleton-button" style="width:2.5rem;height:2.25rem"></div>
                                </div>
                            </div>
                        </div>
                    `,
                        )
                        .join("")}
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
                <!-- Fil d'ariane -->
                <div class="flex items-center space-x-2 mb-6">
                    <div class="skeleton-loader skeleton-text w-16 mb-0"></div>
                    <div class="skeleton-loader skeleton-text w-3 mb-0"></div>
                    <div class="skeleton-loader skeleton-text w-24 mb-0"></div>
                    <div class="skeleton-loader skeleton-text w-3 mb-0"></div>
                    <div class="skeleton-loader skeleton-text w-20 mb-0"></div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Images -->
                    <div>
                        <div class="skeleton-card p-2">
                            <div class="skeleton-loader skeleton-image" style="height:0;padding-bottom:100%"></div>
                        </div>
                        <div class="grid grid-cols-4 gap-2 mt-2">
                            ${[...Array(4)].map(() => `
                                <div class="skeleton-loader skeleton-image" style="height:5rem;padding-bottom:0"></div>
                            `).join("")}
                        </div>
                    </div>
                    <!-- Info -->
                    <div class="space-y-4">
                        <div class="skeleton-loader skeleton-title w-3/4"></div>
                        <div class="skeleton-loader skeleton-title w-1/3" style="margin-top:0.25rem"></div>
                        <div class="skeleton-card p-5 space-y-3">
                            <div class="skeleton-loader skeleton-text w-full"></div>
                            <div class="skeleton-loader skeleton-text w-full"></div>
                            <div class="skeleton-loader skeleton-text w-2/3"></div>
                        </div>
                        <div class="skeleton-loader skeleton-button w-full" style="height:3rem"></div>
                        <div class="skeleton-loader skeleton-button w-full" style="height:3rem"></div>
                        <div class="skeleton-card p-4 flex items-center space-x-3">
                            <div class="skeleton-loader skeleton-avatar"></div>
                            <div class="flex-1">
                                <div class="skeleton-loader skeleton-text w-32 mb-0"></div>
                                <div class="skeleton-loader skeleton-text w-24 mb-0"></div>
                            </div>
                        </div>
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
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    ${[...Array(4)].map(() => `
                        <div class="skeleton-card p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="skeleton-loader skeleton-circle" style="width:2.5rem;height:2.5rem"></div>
                                <div class="skeleton-loader skeleton-text w-20 mb-0"></div>
                            </div>
                            <div class="skeleton-loader skeleton-title w-24 mb-2"></div>
                            <div class="skeleton-loader skeleton-text w-16 mb-0"></div>
                        </div>
                    `).join("")}
                </div>
                <!-- Chart + side -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="skeleton-card p-6 lg:col-span-2">
                        <div class="skeleton-loader skeleton-title w-48 mb-6"></div>
                        <div class="skeleton-loader skeleton-chart"></div>
                    </div>
                    <div class="skeleton-card p-6">
                        <div class="skeleton-loader skeleton-title w-32 mb-6"></div>
                        ${[...Array(4)].map(() => `
                            <div class="flex items-center gap-3 mb-4">
                                <div class="skeleton-loader skeleton-avatar" style="width:2.5rem;height:2.5rem"></div>
                                <div class="flex-1">
                                    <div class="skeleton-loader skeleton-text w-24 mb-0"></div>
                                    <div class="skeleton-loader skeleton-text w-16 mb-0"></div>
                                </div>
                            </div>
                        `).join("")}
                    </div>
                </div>
                <!-- Table -->
                <div class="skeleton-card p-6">
                    <div class="skeleton-loader skeleton-title w-64 mb-6"></div>
                    <div class="space-y-3">
                        ${[...Array(5)].map(() => `<div class="skeleton-loader skeleton-text w-full mb-3"></div>`).join("")}
                    </div>
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
                    ${[...Array(count)]
                        .map(
                            () => `
                        <div class="skeleton-card p-4 flex items-center gap-4">
                            <div class="skeleton-loader skeleton-avatar"></div>
                            <div class="flex-1">
                                <div class="skeleton-loader skeleton-text w-48 mb-2"></div>
                                <div class="skeleton-loader skeleton-text w-64"></div>
                            </div>
                            <div class="skeleton-loader skeleton-button" style="width:5rem"></div>
                        </div>
                    `,
                        )
                        .join("")}
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
     * Injecte les styles d'animation pour les skeletons
     */
    injectSkeletonStyles() {
        // Éviter double injection
        if (document.getElementById("skeleton-animation-styles")) return;

        const style = document.createElement("style");
        style.id = "skeleton-animation-styles";
        style.textContent = `
            :root {
                --sk-base: #e4e7ec;
                --sk-highlight: rgba(255, 255, 255, 0.75);
                --sk-radius: 0.75rem;
            }
            .dark {
                --sk-base: #2a2f3a;
                --sk-highlight: rgba(255, 255, 255, 0.10);
            }

            @keyframes skeleton-shimmer {
                0% { transform: translateX(-100%); }
                100% { transform: translateX(100%); }
            }

            .skeleton-loader {
                position: relative;
                display: block;
                overflow: hidden;
                border-radius: var(--sk-radius);
                background: var(--sk-base);
            }
            .skeleton-loader::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                height: 100%;
                width: 60%;
                background: linear-gradient(
                    90deg,
                    transparent,
                    var(--sk-highlight),
                    transparent
                );
                animation: skeleton-shimmer 1.6s ease-in-out infinite;
                will-change: transform;
            }

            /* Variantes de forme */
            .skeleton-text { height: 0.875rem; border-radius: 0.375rem; margin-bottom: 0.5rem; }
            .skeleton-title { height: 1.375rem; width: 60%; border-radius: 0.5rem; margin-bottom: 1rem; }
            .skeleton-image { height: 12rem; width: 100%; }
            .skeleton-avatar { width: 3rem; height: 3rem; border-radius: 9999px; }
            .skeleton-button { height: 2.5rem; width: 7rem; border-radius: 0.75rem; }
            .skeleton-badge { height: 1.5rem; width: 4rem; border-radius: 9999px; }
            .skeleton-circle { border-radius: 9999px; }
            .skeleton-chart { height: 16rem; width: 100%; }

            /* Carte squelette unifiée */
            .skeleton-card {
                background: #ffffff;
                border: 1px solid #eceef1;
                border-radius: 1rem;
                box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
                overflow: hidden;
            }
            .dark .skeleton-card {
                background: #1f242d;
                border-color: #2e3440;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.4);
            }

            /* Accessibilité : réduire les animations si demandé */
            @media (prefers-reduced-motion: reduce) {
                .skeleton-loader::after {
                    animation: none;
                    display: none;
                }
                .skeleton-loader {
                    animation: none;
                }
                * {
                    animation-duration: 0.001ms !important;
                }
            }
        `;
        document.head.appendChild(style);
    }

    /**
     * Affiche le skeleton
     */
    show(template) {
        this.displayStartTime = Date.now();
        this.visible = true;

        // Injecter les styles d'animation
        this.injectSkeletonStyles();

        // Créer l'élément skeleton
        this.skeletonElement = document.createElement("div");
        this.skeletonElement.className = this.options.skeletonClass;
        this.skeletonElement.innerHTML = template;
        this.skeletonElement.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #f7f8fa;
            z-index: 9999;
            overflow-y: auto;
            opacity: 1;
            transition: opacity ${this.options.fadeOutDuration}ms ease-out;
        `;

        // Mode sombre
        if (document.documentElement.classList.contains("dark")) {
            this.skeletonElement.style.background = "#0f1419";
        }

        // Ajouter au DOM
        const container = document.querySelector(
            this.options.containerSelector,
        );
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

        // Animation de sortie (fondu + léger flou)
        this.skeletonElement.style.opacity = "0";
        this.skeletonElement.style.filter = "blur(4px)";
        this.skeletonElement.style.transition = `opacity ${this.options.fadeOutDuration}ms ease-out, filter ${this.options.fadeOutDuration}ms ease-out`;

        setTimeout(() => {
            if (this.skeletonElement && this.skeletonElement.parentNode) {
                this.skeletonElement.parentNode.removeChild(
                    this.skeletonElement,
                );
            }
            this.skeletonElement = null;
            this.visible = false;

            // Dispatcher l'événement pour informer que le skeleton est caché
            document.dispatchEvent(new CustomEvent("skeletonHidden"));
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
            await new Promise((resolve) => setTimeout(resolve, remaining));
        }
    }
}

// Auto-hide sur chargement complet de la page
function hideSkeletonWhenReady() {
    if (window.pageSkeleton && window.pageSkeleton.isVisible()) {
        window.pageSkeleton.hide();
    } else {
        // Dispatcher l'événement même si pas de skeleton actif
        document.dispatchEvent(new CustomEvent("skeletonHidden"));
    }
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
        setTimeout(hideSkeletonWhenReady, 100);
    });
} else {
    setTimeout(hideSkeletonWhenReady, 100);
}

// Export global
if (typeof window !== "undefined") {
    window.PageSkeletonLoader = PageSkeletonLoader;
}
