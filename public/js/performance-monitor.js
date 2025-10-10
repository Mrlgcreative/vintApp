/**
 * Moniteur de Performance VintApp
 * 
 * Mesure et affiche les métriques de performance en temps réel
 * À utiliser en développement uniquement
 */

class PerformanceMonitor {
    constructor() {
        this.metrics = {
            fcp: null,
            lcp: null,
            fid: null,
            cls: null,
            ttfb: null,
            tti: null
        };
        
        this.init();
    }

    init() {
        if (typeof PerformanceObserver === 'undefined') {
            console.warn('PerformanceObserver non supporté');
            return;
        }

        this.observePaint();
        this.observeLayoutShift();
        this.observeFirstInput();
        this.measureTiming();
        
        // Afficher les résultats après 5 secondes
        setTimeout(() => this.displayResults(), 5000);
    }

    observePaint() {
        try {
            const observer = new PerformanceObserver((list) => {
                for (const entry of list.getEntries()) {
                    if (entry.name === 'first-contentful-paint') {
                        this.metrics.fcp = entry.startTime;
                    }
                }
            });
            observer.observe({ type: 'paint', buffered: true });
        } catch (e) {
            console.warn('Paint observer error:', e);
        }
    }

    observeLayoutShift() {
        try {
            let clsValue = 0;
            const observer = new PerformanceObserver((list) => {
                for (const entry of list.getEntries()) {
                    if (!entry.hadRecentInput) {
                        clsValue += entry.value;
                        this.metrics.cls = clsValue;
                    }
                }
            });
            observer.observe({ type: 'layout-shift', buffered: true });
        } catch (e) {
            console.warn('Layout shift observer error:', e);
        }
    }

    observeFirstInput() {
        try {
            const observer = new PerformanceObserver((list) => {
                const firstInput = list.getEntries()[0];
                if (firstInput) {
                    this.metrics.fid = firstInput.processingStart - firstInput.startTime;
                }
            });
            observer.observe({ type: 'first-input', buffered: true });
        } catch (e) {
            console.warn('First input observer error:', e);
        }
    }

    measureTiming() {
        window.addEventListener('load', () => {
            const timing = performance.timing;
            
            // Time to First Byte
            this.metrics.ttfb = timing.responseStart - timing.requestStart;
            
            // DOM Content Loaded
            const dcl = timing.domContentLoadedEventEnd - timing.navigationStart;
            
            // Load Complete
            const loadComplete = timing.loadEventEnd - timing.navigationStart;
            
            // Time to Interactive (approximation)
            this.metrics.tti = timing.domInteractive - timing.navigationStart;

            console.log('📊 Timing Metrics:', {
                ttfb: `${this.metrics.ttfb}ms`,
                dcl: `${dcl}ms`,
                loadComplete: `${loadComplete}ms`,
                tti: `${this.metrics.tti}ms`
            });
        });
    }

    displayResults() {
        console.log('🚀 Performance Metrics:');
        console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        // First Contentful Paint
        if (this.metrics.fcp) {
            const fcpStatus = this.metrics.fcp < 1000 ? '✅' : 
                            this.metrics.fcp < 2500 ? '⚠️' : '❌';
            console.log(`${fcpStatus} FCP (First Contentful Paint): ${this.metrics.fcp.toFixed(2)}ms`);
        }

        // Time to Interactive
        if (this.metrics.tti) {
            const ttiStatus = this.metrics.tti < 2000 ? '✅' : 
                            this.metrics.tti < 5000 ? '⚠️' : '❌';
            console.log(`${ttiStatus} TTI (Time to Interactive): ${this.metrics.tti.toFixed(2)}ms`);
        }

        // Time to First Byte
        if (this.metrics.ttfb) {
            const ttfbStatus = this.metrics.ttfb < 200 ? '✅' : 
                             this.metrics.ttfb < 600 ? '⚠️' : '❌';
            console.log(`${ttfbStatus} TTFB (Time to First Byte): ${this.metrics.ttfb.toFixed(2)}ms`);
        }

        // Cumulative Layout Shift
        if (this.metrics.cls !== null) {
            const clsStatus = this.metrics.cls < 0.1 ? '✅' : 
                            this.metrics.cls < 0.25 ? '⚠️' : '❌';
            console.log(`${clsStatus} CLS (Cumulative Layout Shift): ${this.metrics.cls.toFixed(4)}`);
        }

        // First Input Delay
        if (this.metrics.fid !== null) {
            const fidStatus = this.metrics.fid < 100 ? '✅' : 
                            this.metrics.fid < 300 ? '⚠️' : '❌';
            console.log(`${fidStatus} FID (First Input Delay): ${this.metrics.fid.toFixed(2)}ms`);
        }

        console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        console.log('Légende: ✅ Bon | ⚠️ À améliorer | ❌ Mauvais');
        
        // Recommandations
        this.giveRecommendations();
    }

    giveRecommendations() {
        const recommendations = [];

        if (this.metrics.fcp > 2500) {
            recommendations.push('🔧 FCP élevé : Optimiser le CSS critique et réduire le JavaScript bloquant');
        }

        if (this.metrics.tti > 5000) {
            recommendations.push('🔧 TTI élevé : Différer les scripts non essentiels et utiliser code splitting');
        }

        if (this.metrics.ttfb > 600) {
            recommendations.push('🔧 TTFB élevé : Optimiser le backend ou utiliser un CDN');
        }

        if (this.metrics.cls > 0.25) {
            recommendations.push('🔧 CLS élevé : Spécifier les dimensions des images et éviter l\'injection de contenu');
        }

        if (this.metrics.fid > 300) {
            recommendations.push('🔧 FID élevé : Réduire l\'exécution JavaScript et utiliser web workers');
        }

        if (recommendations.length > 0) {
            console.log('\n💡 Recommandations:');
            recommendations.forEach(rec => console.log(rec));
        } else {
            console.log('\n🎉 Excellent ! Toutes les métriques sont dans les limites recommandées !');
        }
    }

    // Méthode publique pour obtenir les métriques
    getMetrics() {
        return { ...this.metrics };
    }
}

// Initialiser uniquement en développement
if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    window.perfMonitor = new PerformanceMonitor();
    
    // Ajouter un bouton dans la console
    console.log('%c🚀 VintApp Performance Monitor Activé', 
        'background: #4CAF50; color: white; padding: 5px 10px; border-radius: 3px; font-weight: bold;');
    console.log('Tapez perfMonitor.getMetrics() pour voir les métriques en temps réel');
}
