/**
 * Content Visibility Manager
 * Gère l'affichage du contenu réel après le chargement du skeleton
 */

(function () {
    "use strict";

    // Cacher le contenu principal pendant le chargement
    const style = document.createElement("style");
    style.textContent = `
        .content-loading {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease-in, visibility 0.3s;
        }

        .content-loaded {
            opacity: 1;
            visibility: visible;
        }

        /* Éviter le flash de contenu non stylé */
        body.skeleton-active > main,
        body.skeleton-active > .container,
        body.skeleton-active > #app {
            opacity: 0;
        }
    `;
    document.head.appendChild(style);

    // Détecter si c'est une navigation back/forward (API moderne)
    function isBackForwardNavigation() {
        // Navigation Timing API Level 2 (moderne)
        if (window.performance && performance.getEntriesByType) {
            const navEntries = performance.getEntriesByType("navigation");
            if (navEntries.length > 0) {
                return navEntries[0].type === "back_forward";
            }
        }
        return false;
    }

    // Marquer le body comme ayant un skeleton actif (sauf pour back/forward)
    if (!isBackForwardNavigation()) {
        document.body.classList.add("skeleton-active");
    }

    // Fonction pour afficher le contenu
    function showContent() {
        document.body.classList.remove("skeleton-active");

        // Animer l'apparition du contenu
        const mainContent =
            document.querySelector("main") ||
            document.querySelector(".container") ||
            document.querySelector("#app");

        if (mainContent) {
            mainContent.style.opacity = "0";
            requestAnimationFrame(() => {
                mainContent.style.transition = "opacity 0.3s ease-in";
                mainContent.style.opacity = "1";
            });
        }
    }

    // Retirer la classe quand le skeleton est caché
    document.addEventListener("skeletonHidden", showContent);

    // Fallback: afficher le contenu après le chargement complet ou 2 secondes max
    let contentShown = false;

    function ensureContentVisible() {
        if (contentShown) return;
        contentShown = true;

        // Dispatcher l'événement si pas déjà fait par le skeleton
        if (document.body.classList.contains("skeleton-active")) {
            showContent();
        }
    }

    // Afficher dès que le DOM est prêt
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", () => {
            setTimeout(ensureContentVisible, 500);
        });
    } else {
        setTimeout(ensureContentVisible, 500);
    }

    // Fallback ultime après 2 secondes
    setTimeout(ensureContentVisible, 2000);
})();
