// Splash Screen Manager pour PWA
class SplashScreenManager {
    constructor() {
        this.minDisplayTime = 2000; // Minimum 2 secondes
        this.startTime = Date.now();
        this.init();
    }

    init() {
        // Vérifier si on est en mode PWA (standalone)
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches 
            || window.navigator.standalone 
            || document.referrer.includes('android-app://');

        if (isStandalone) {
            this.createSplashScreen();
            this.setupListeners();
        }
    }

    createSplashScreen() {
        // Créer le splash screen
        const splash = document.createElement('div');
        splash.id = 'vintapp-splash';
        splash.innerHTML = `
            <img id="vintapp-splash-logo" src="/images/icons/icon-512x512.png" alt="VintApp Logo">
            <div id="vintapp-splash-text">VintApp</div>
            <div id="vintapp-splash-tagline">Marketplace de Confiance</div>
            <div id="vintapp-splash-loader"></div>
        `;
        
        document.body.prepend(splash);
    }

    setupListeners() {
        // Cacher le splash screen quand tout est chargé
        if (document.readyState === 'complete') {
            this.hideSplash();
        } else {
            window.addEventListener('load', () => this.hideSplash());
        }
    }

    hideSplash() {
        const elapsedTime = Date.now() - this.startTime;
        const remainingTime = Math.max(0, this.minDisplayTime - elapsedTime);

        setTimeout(() => {
            const splash = document.getElementById('vintapp-splash');
            if (splash) {
                splash.classList.add('fade-out');
                setTimeout(() => {
                    splash.remove();
                }, 500); // Temps de la transition CSS
            }
        }, remainingTime);
    }
}

// Initialiser immédiatement (avant même le DOM)
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new SplashScreenManager();
    });
} else {
    new SplashScreenManager();
}
