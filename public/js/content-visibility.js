/**
 * Content Visibility Manager
 * Gère l'affichage du contenu réel après le chargement du skeleton
 */

(function() {
    'use strict';

    // Cacher le contenu principal pendant le chargement
    const style = document.createElement('style');
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

    // Marquer le body comme ayant un skeleton actif
    if (performance.navigation.type !== performance.navigation.TYPE_BACK_FORWARD) {
        document.body.classList.add('skeleton-active');
    }

    // Retirer la classe quand le skeleton est caché
    document.addEventListener('skeletonHidden', function() {
        document.body.classList.remove('skeleton-active');
        
        // Animer l'apparition du contenu
        const mainContent = document.querySelector('main') || 
                          document.querySelector('.container') || 
                          document.querySelector('#app');
        
        if (mainContent) {
            mainContent.style.opacity = '0';
            setTimeout(() => {
                mainContent.style.transition = 'opacity 0.3s ease-in';
                mainContent.style.opacity = '1';
            }, 50);
        }
    });

    // Fallback: afficher le contenu après 3 secondes max
    window.addEventListener('load', function() {
        setTimeout(() => {
            document.body.classList.remove('skeleton-active');
        }, 3000);
    });
})();
