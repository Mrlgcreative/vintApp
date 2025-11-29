/**
 * Lazy Loading Manager pour VintApp PWA
 * Optimise le chargement des images et du contenu pour améliorer les performances
 */

class LazyLoadingManager {
    constructor(options = {}) {
        this.options = {
            rootMargin: options.rootMargin || '50px',
            threshold: options.threshold || 0.01,
            loadingClass: options.loadingClass || 'lazy-loading',
            loadedClass: options.loadedClass || 'lazy-loaded',
            errorClass: options.errorClass || 'lazy-error',
            placeholderClass: options.placeholderClass || 'lazy-placeholder',
            ...options
        };

        this.observer = null;
        this.images = [];
        this.iframes = [];
        this.backgroundImages = [];
        
        this.init();
    }

    /**
     * Initialise le système de lazy loading
     */
    init() {
        if ('IntersectionObserver' in window) {
            this.setupIntersectionObserver();
        } else {
            // Fallback pour les navigateurs non supportés
            this.loadAllImagesImmediately();
        }

        this.setupImageObservers();
        this.setupIframeObservers();
        this.setupBackgroundImageObservers();
        
        // Écouter les changements DOM pour de nouvelles images
        this.observeDOMChanges();
    }

    /**
     * Configure l'Intersection Observer
     */
    setupIntersectionObserver() {
        this.observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.loadElement(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, {
            rootMargin: this.options.rootMargin,
            threshold: this.options.threshold
        });
    }

    /**
     * Configure l'observation des images
     */
    setupImageObservers() {
        // Images avec data-src
        const lazyImages = document.querySelectorAll('img[data-src]:not(.lazy-loaded)');
        lazyImages.forEach(img => {
            this.observeImage(img);
        });

        // Images avec loading="lazy" natif
        const nativeLazyImages = document.querySelectorAll('img[loading="lazy"]:not([data-src])');
        nativeLazyImages.forEach(img => {
            img.classList.add(this.options.loadedClass);
        });
    }

    /**
     * Configure l'observation des iframes
     */
    setupIframeObservers() {
        const lazyIframes = document.querySelectorAll('iframe[data-src]:not(.lazy-loaded)');
        lazyIframes.forEach(iframe => {
            this.observeIframe(iframe);
        });
    }

    /**
     * Configure l'observation des images de fond
     */
    setupBackgroundImageObservers() {
        const lazyBackgrounds = document.querySelectorAll('[data-bg]:not(.lazy-loaded)');
        lazyBackgrounds.forEach(element => {
            this.observeBackgroundImage(element);
        });
    }

    /**
     * Observer une image
     */
    observeImage(img) {
        // Ajouter un placeholder si nécessaire
        if (!img.src && !img.classList.contains(this.options.placeholderClass)) {
            img.src = this.createPlaceholder(img.dataset.width || 300, img.dataset.height || 200);
            img.classList.add(this.options.placeholderClass);
        }

        img.classList.add(this.options.loadingClass);
        this.images.push(img);
        
        if (this.observer) {
            this.observer.observe(img);
        }
    }

    /**
     * Observer un iframe
     */
    observeIframe(iframe) {
        iframe.classList.add(this.options.loadingClass);
        this.iframes.push(iframe);
        
        if (this.observer) {
            this.observer.observe(iframe);
        }
    }

    /**
     * Observer une image de fond
     */
    observeBackgroundImage(element) {
        element.classList.add(this.options.loadingClass);
        this.backgroundImages.push(element);
        
        if (this.observer) {
            this.observer.observe(element);
        }
    }

    /**
     * Charge un élément
     */
    loadElement(element) {
        if (element.tagName === 'IMG') {
            this.loadImage(element);
        } else if (element.tagName === 'IFRAME') {
            this.loadIframe(element);
        } else if (element.dataset.bg) {
            this.loadBackgroundImage(element);
        }
    }

    /**
     * Charge une image
     */
    loadImage(img) {
        const src = img.dataset.src;
        const srcset = img.dataset.srcset;

        if (!src) return;

        // Créer une nouvelle image pour précharger
        const tempImg = new Image();
        
        tempImg.onload = () => {
            img.src = src;
            if (srcset) {
                img.srcset = srcset;
            }
            
            img.classList.remove(this.options.loadingClass, this.options.placeholderClass);
            img.classList.add(this.options.loadedClass);
            
            // Animation de fondu
            img.style.opacity = '0';
            setTimeout(() => {
                img.style.transition = 'opacity 0.3s ease-in-out';
                img.style.opacity = '1';
            }, 10);

            // Événement personnalisé
            img.dispatchEvent(new CustomEvent('lazyloaded', { detail: { src } }));
        };

        tempImg.onerror = () => {
            img.classList.remove(this.options.loadingClass);
            img.classList.add(this.options.errorClass);
            
            // Image par défaut en cas d'erreur
            img.src = this.createErrorPlaceholder();
            img.alt = 'Image non disponible';

            console.error('Erreur de chargement lazy:', src);
        };

        tempImg.src = src;
        if (srcset) {
            tempImg.srcset = srcset;
        }
    }

    /**
     * Charge un iframe
     */
    loadIframe(iframe) {
        const src = iframe.dataset.src;
        if (!src) return;

        iframe.src = src;
        iframe.classList.remove(this.options.loadingClass);
        iframe.classList.add(this.options.loadedClass);

        iframe.dispatchEvent(new CustomEvent('lazyloaded', { detail: { src } }));
    }

    /**
     * Charge une image de fond
     */
    loadBackgroundImage(element) {
        const bg = element.dataset.bg;
        if (!bg) return;

        const img = new Image();
        img.onload = () => {
            element.style.backgroundImage = `url(${bg})`;
            element.classList.remove(this.options.loadingClass);
            element.classList.add(this.options.loadedClass);

            element.dispatchEvent(new CustomEvent('lazyloaded', { detail: { bg } }));
        };

        img.onerror = () => {
            element.classList.remove(this.options.loadingClass);
            element.classList.add(this.options.errorClass);
            console.error('Erreur de chargement background:', bg);
        };

        img.src = bg;
    }

    /**
     * Crée un placeholder SVG
     */
    createPlaceholder(width = 300, height = 200) {
        const svg = `
            <svg xmlns="http://www.w3.org/2000/svg" width="${width}" height="${height}" viewBox="0 0 ${width} ${height}">
                <rect width="100%" height="100%" fill="#e5e7eb"/>
                <text x="50%" y="50%" font-family="sans-serif" font-size="14" fill="#9ca3af" text-anchor="middle" dy=".3em">
                    Chargement...
                </text>
            </svg>
        `;
        return 'data:image/svg+xml;base64,' + btoa(svg);
    }

    /**
     * Crée un placeholder d'erreur
     */
    createErrorPlaceholder() {
        const svg = `
            <svg xmlns="http://www.w3.org/2000/svg" width="300" height="200" viewBox="0 0 300 200">
                <rect width="100%" height="100%" fill="#fee2e2"/>
                <text x="50%" y="50%" font-family="sans-serif" font-size="14" fill="#dc2626" text-anchor="middle" dy=".3em">
                    Image non disponible
                </text>
            </svg>
        `;
        return 'data:image/svg+xml;base64,' + btoa(svg);
    }

    /**
     * Charge toutes les images immédiatement (fallback)
     */
    loadAllImagesImmediately() {
        const allLazyImages = document.querySelectorAll('img[data-src], iframe[data-src], [data-bg]');
        allLazyImages.forEach(element => {
            this.loadElement(element);
        });
    }

    /**
     * Observer les changements DOM pour de nouvelles images
     */
    observeDOMChanges() {
        if ('MutationObserver' in window) {
            const mutationObserver = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.addedNodes.length) {
                        mutation.addedNodes.forEach((node) => {
                            if (node.nodeType === 1) { // Element node
                                // Vérifier si c'est une image lazy
                                if (node.tagName === 'IMG' && node.dataset.src) {
                                    this.observeImage(node);
                                }
                                // Vérifier si c'est un iframe lazy
                                if (node.tagName === 'IFRAME' && node.dataset.src) {
                                    this.observeIframe(node);
                                }
                                // Vérifier si c'est un élément avec background lazy
                                if (node.dataset.bg) {
                                    this.observeBackgroundImage(node);
                                }
                                // Vérifier les enfants
                                const lazyImages = node.querySelectorAll('img[data-src]:not(.lazy-loaded)');
                                lazyImages.forEach(img => this.observeImage(img));
                                
                                const lazyIframes = node.querySelectorAll('iframe[data-src]:not(.lazy-loaded)');
                                lazyIframes.forEach(iframe => this.observeIframe(iframe));
                                
                                const lazyBackgrounds = node.querySelectorAll('[data-bg]:not(.lazy-loaded)');
                                lazyBackgrounds.forEach(el => this.observeBackgroundImage(el));
                            }
                        });
                    }
                });
            });

            mutationObserver.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    }

    /**
     * Précharge une liste d'images
     */
    preloadImages(urls) {
        urls.forEach(url => {
            const img = new Image();
            img.src = url;
        });
    }

    /**
     * Force le chargement de toutes les images
     */
    loadAll() {
        [...this.images, ...this.iframes, ...this.backgroundImages].forEach(element => {
            this.loadElement(element);
        });
    }

    /**
     * Détruit l'instance
     */
    destroy() {
        if (this.observer) {
            this.observer.disconnect();
        }
        this.images = [];
        this.iframes = [];
        this.backgroundImages = [];
    }
}

// Initialisation automatique
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.lazyLoader = new LazyLoadingManager({
            rootMargin: '100px',
            threshold: 0.01
        });
    });
} else {
    window.lazyLoader = new LazyLoadingManager({
        rootMargin: '100px',
        threshold: 0.01
    });
}

// Export pour utilisation en module
if (typeof module !== 'undefined' && module.exports) {
    module.exports = LazyLoadingManager;
}
