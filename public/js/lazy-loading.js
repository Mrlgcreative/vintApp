/**
 * Lazy Loading Manager pour VintApp PWA
 * Optimise le chargement des images et du contenu pour améliorer les performances
 * - decode() pour rendu sans saccade
 * - Network Information API (save-data, connexion lente)
 * - Retry automatique sur erreur réseau
 * - Animations via classes CSS (GPU-accelerated)
 * - Protection CSS injection sur backgroundImage
 */

class LazyLoadingManager {
    constructor(options = {}) {
        this.options = {
            rootMargin: options.rootMargin || "50px",
            threshold: options.threshold || 0.01,
            loadingClass: options.loadingClass || "lazy-loading",
            loadedClass: options.loadedClass || "lazy-loaded",
            errorClass: options.errorClass || "lazy-error",
            placeholderClass: options.placeholderClass || "lazy-placeholder",
            retryAttempts: options.retryAttempts || 2,
            retryDelay: options.retryDelay || 1500,
            ...options,
        };

        this.observer = null;
        this.observed = new WeakSet();
        this.loadedCount = 0;
        this.errorCount = 0;
        this._mutationDebounce = null;
        this._placeholderCache = new Map();

        this.init();
    }

    /**
     * Initialise le système de lazy loading
     */
    init() {
        if ("IntersectionObserver" in window) {
            this.setupIntersectionObserver();
        } else {
            this.loadAllImagesImmediately();
        }

        this.setupImageObservers();
        this.setupIframeObservers();
        this.setupBackgroundImageObservers();
        this.observeDOMChanges();
    }

    /**
     * Détecte si l'utilisateur est en mode économie de données ou connexion lente
     */
    shouldReduceData() {
        const conn = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
        if (!conn) return false;
        if (conn.saveData) return true;
        if (conn.effectiveType && ["slow-2g", "2g"].includes(conn.effectiveType)) return true;
        return false;
    }

    /**
     * Configure l'Intersection Observer avec marge adaptée à la connexion
     */
    setupIntersectionObserver() {
        const margin = this.shouldReduceData()
            ? "0px"
            : this.options.rootMargin;

        this.observer = new IntersectionObserver(
            (entries, observer) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        this.loadElement(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            },
            {
                rootMargin: margin,
                threshold: this.options.threshold,
            },
        );
    }

    /**
     * Configure l'observation des images
     */
    setupImageObservers() {
        const lazyImages = document.querySelectorAll(
            "img[data-src]:not(.lazy-loaded)",
        );
        lazyImages.forEach((img) => this.observeImage(img));

        const nativeLazyImages = document.querySelectorAll(
            'img[loading="lazy"]:not([data-src])',
        );
        nativeLazyImages.forEach((img) => {
            img.classList.add(this.options.loadedClass);
        });
    }

    /**
     * Configure l'observation des iframes
     */
    setupIframeObservers() {
        const lazyIframes = document.querySelectorAll(
            "iframe[data-src]:not(.lazy-loaded)",
        );
        lazyIframes.forEach((iframe) => this.observeIframe(iframe));
    }

    /**
     * Configure l'observation des images de fond
     */
    setupBackgroundImageObservers() {
        const lazyBackgrounds = document.querySelectorAll(
            "[data-bg]:not(.lazy-loaded)",
        );
        lazyBackgrounds.forEach((element) => this.observeBackgroundImage(element));
    }

    /**
     * Observer une image
     */
    observeImage(img) {
        if (this.observed.has(img) || img.classList.contains(this.options.loadedClass)) {
            return;
        }

        this.observed.add(img);

        if (!img.src && !img.classList.contains(this.options.placeholderClass)) {
            img.src = this.createPlaceholder(
                img.dataset.width || 300,
                img.dataset.height || 200,
            );
            img.classList.add(this.options.placeholderClass);
        }

        img.classList.add(this.options.loadingClass);

        if (this.observer) {
            this.observer.observe(img);
        }
    }

    /**
     * Observer un iframe
     */
    observeIframe(iframe) {
        if (this.observed.has(iframe)) return;
        this.observed.add(iframe);

        iframe.classList.add(this.options.loadingClass);

        if (this.observer) {
            this.observer.observe(iframe);
        }
    }

    /**
     * Observer une image de fond
     */
    observeBackgroundImage(element) {
        if (this.observed.has(element)) return;
        this.observed.add(element);

        element.classList.add(this.options.loadingClass);

        if (this.observer) {
            this.observer.observe(element);
        }
    }

    /**
     * Charge un élément selon son type
     */
    loadElement(element) {
        if (element.tagName === "IMG") {
            this.loadImage(element);
        } else if (element.tagName === "IFRAME") {
            this.loadIframe(element);
        } else if (element.dataset.bg) {
            this.loadBackgroundImage(element);
        }
    }

    /**
     * Charge une image avec decode() et retry
     */
    loadImage(img, attempt = 0) {
        const src = img.dataset.src;
        const srcset = img.dataset.srcset;
        const sizes = img.dataset.sizes;

        if (!src) return;

        // En mode save-data, ignorer srcset pour charger la version la plus légère
        const useSrcset = srcset && !this.shouldReduceData();

        const tempImg = new Image();

        tempImg.onload = () => {
            img.src = src;
            if (useSrcset) img.srcset = srcset;
            if (sizes) img.sizes = sizes;

            // Utiliser decode() pour éviter le flash blanc / saccade
            const applyLoaded = () => {
                img.classList.remove(
                    this.options.loadingClass,
                    this.options.placeholderClass,
                );
                img.classList.add(this.options.loadedClass);
                this.loadedCount++;

                img.dispatchEvent(
                    new CustomEvent("lazyloaded", { detail: { src } }),
                );
            };

            if (typeof img.decode === "function") {
                img.decode().then(applyLoaded).catch(applyLoaded);
            } else {
                applyLoaded();
            }
        };

        tempImg.onerror = () => {
            // Retry automatique
            if (attempt < this.options.retryAttempts) {
                setTimeout(() => {
                    this.loadImage(img, attempt + 1);
                }, this.options.retryDelay * (attempt + 1));
                return;
            }

            img.classList.remove(this.options.loadingClass);
            img.classList.add(this.options.errorClass);
            img.src = this.createErrorPlaceholder();
            img.alt = "Image non disponible";
            this.errorCount++;

            img.dispatchEvent(
                new CustomEvent("lazyerror", { detail: { src, attempts: attempt + 1 } }),
            );
        };

        tempImg.src = src;
        if (useSrcset) tempImg.srcset = srcset;
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
        this.loadedCount++;

        iframe.dispatchEvent(
            new CustomEvent("lazyloaded", { detail: { src } }),
        );
    }

    /**
     * Charge une image de fond (avec protection CSS injection)
     */
    loadBackgroundImage(element) {
        const bg = element.dataset.bg;
        if (!bg) return;

        // Sanitize : supprimer les caractères dangereux pour CSS
        const safeBg = bg.replace(/["'()\\]/g, "");

        const img = new Image();
        img.onload = () => {
            element.style.backgroundImage = `url("${safeBg}")`;
            element.classList.remove(this.options.loadingClass);
            element.classList.add(this.options.loadedClass);
            this.loadedCount++;

            element.dispatchEvent(
                new CustomEvent("lazyloaded", { detail: { bg: safeBg } }),
            );
        };

        img.onerror = () => {
            element.classList.remove(this.options.loadingClass);
            element.classList.add(this.options.errorClass);
            this.errorCount++;
        };

        img.src = safeBg;
    }

    /**
     * Encode une chaîne en base64 (compatible unicode)
     */
    safeBase64Encode(str) {
        try {
            return btoa(unescape(encodeURIComponent(str)));
        } catch (e) {
            return btoa(str.replace(/[^\x00-\x7F]/g, ""));
        }
    }

    /**
     * Crée un placeholder SVG (avec cache)
     */
    createPlaceholder(width = 300, height = 200) {
        const key = `${width}x${height}`;
        if (this._placeholderCache.has(key)) {
            return this._placeholderCache.get(key);
        }

        const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="${width}" height="${height}" viewBox="0 0 ${width} ${height}"><rect width="100%" height="100%" fill="#e5e7eb"/><rect x="45%" y="45%" width="10%" height="10%" rx="2" fill="#d1d5db"><animate attributeName="opacity" values="0.4;1;0.4" dur="1.2s" repeatCount="indefinite"/></rect></svg>`;
        const dataUri = "data:image/svg+xml;base64," + this.safeBase64Encode(svg);
        this._placeholderCache.set(key, dataUri);
        return dataUri;
    }

    /**
     * Crée un placeholder d'erreur
     */
    createErrorPlaceholder() {
        if (this._errorPlaceholder) return this._errorPlaceholder;

        const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="300" height="200" viewBox="0 0 300 200"><rect width="100%" height="100%" fill="#fee2e2"/><path d="M140 85 l20 35 l20-35" stroke="#dc2626" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"/><circle cx="160" cy="130" r="2" fill="#dc2626"/><text x="50%" y="160" font-family="system-ui,sans-serif" font-size="12" fill="#dc2626" text-anchor="middle">Image non disponible</text></svg>`;
        this._errorPlaceholder = "data:image/svg+xml;base64," + this.safeBase64Encode(svg);
        return this._errorPlaceholder;
    }

    /**
     * Charge toutes les images immédiatement (fallback)
     */
    loadAllImagesImmediately() {
        const allLazy = document.querySelectorAll(
            "img[data-src], iframe[data-src], [data-bg]",
        );
        allLazy.forEach((element) => this.loadElement(element));
    }

    /**
     * Observer les changements DOM (debounced) pour les nouveaux éléments lazy
     */
    observeDOMChanges() {
        if (!("MutationObserver" in window)) return;

        this._mutationObserver = new MutationObserver((mutations) => {
            // Debounce : regrouper les mutations pour éviter le travail excessif
            if (this._mutationDebounce) cancelAnimationFrame(this._mutationDebounce);

            this._mutationDebounce = requestAnimationFrame(() => {
                const newNodes = [];
                mutations.forEach((mutation) => {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === 1) newNodes.push(node);
                    });
                });

                if (!newNodes.length) return;

                newNodes.forEach((node) => {
                    // Vérifier le nœud lui-même
                    if (node.tagName === "IMG" && node.dataset && node.dataset.src) {
                        this.observeImage(node);
                    }
                    if (node.tagName === "IFRAME" && node.dataset && node.dataset.src) {
                        this.observeIframe(node);
                    }
                    if (node.dataset && node.dataset.bg) {
                        this.observeBackgroundImage(node);
                    }

                    if (typeof node.querySelectorAll !== "function") return;

                    // Vérifier les enfants
                    node.querySelectorAll("img[data-src]:not(.lazy-loaded)")
                        .forEach((img) => this.observeImage(img));
                    node.querySelectorAll("iframe[data-src]:not(.lazy-loaded)")
                        .forEach((iframe) => this.observeIframe(iframe));
                    node.querySelectorAll("[data-bg]:not(.lazy-loaded)")
                        .forEach((el) => this.observeBackgroundImage(el));
                });
            });
        });

        this._mutationObserver.observe(document.body, {
            childList: true,
            subtree: true,
        });
    }

    /**
     * Précharge une liste d'images (avec priorité optionnelle)
     */
    preloadImages(urls, { priority = "low" } = {}) {
        urls.forEach((url) => {
            if ("requestIdleCallback" in window && priority === "low") {
                requestIdleCallback(() => {
                    const img = new Image();
                    img.src = url;
                });
            } else {
                const img = new Image();
                img.fetchPriority = priority;
                img.src = url;
            }
        });
    }

    /**
     * Force le chargement de tous les éléments en attente
     */
    loadAll() {
        if (this.observer) {
            this.observer.disconnect();
        }
        document.querySelectorAll(
            `img.${this.options.loadingClass}, iframe.${this.options.loadingClass}, .${this.options.loadingClass}[data-bg]`
        ).forEach((element) => this.loadElement(element));
    }

    /**
     * Retourne des statistiques de chargement
     */
    getStats() {
        return {
            loaded: this.loadedCount,
            errors: this.errorCount,
        };
    }

    /**
     * Détruit l'instance et libère la mémoire
     */
    destroy() {
        if (this.observer) {
            this.observer.disconnect();
            this.observer = null;
        }
        if (this._mutationObserver) {
            this._mutationObserver.disconnect();
            this._mutationObserver = null;
        }
        if (this._mutationDebounce) {
            cancelAnimationFrame(this._mutationDebounce);
        }
        this.observed = new WeakSet();
        this._placeholderCache.clear();
    }
}

// Initialisation automatique
function initLazyLoader() {
    if (window.lazyLoader) return;

    window.lazyLoader = new LazyLoadingManager({
        rootMargin: "100px",
        threshold: 0.01,
    });

    // Charger immédiatement les images above-the-fold (LCP)
    const viewportHeight = window.innerHeight;
    document.querySelectorAll("img[data-src]").forEach((img) => {
        const rect = img.getBoundingClientRect();
        if (rect.top < viewportHeight && rect.bottom > 0) {
            window.lazyLoader.loadElement(img);
        }
    });
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initLazyLoader);
} else {
    initLazyLoader();
}

if (typeof module !== "undefined" && module.exports) {
    module.exports = LazyLoadingManager;
}
