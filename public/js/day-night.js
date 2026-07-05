/**
 * VintApp - Système Jour/Nuit Dynamique Multi-Palettes
 *
 * Change automatiquement les couleurs selon l'heure.
 * Supporte PLUSIEURS palettes pour le jour et la nuit.
 * Le JS injecte les variables CSS directement depuis les données JSON serveur.
 *
 * API publique : window.VintAppDayNight
 *   .toggle()           — Basculer jour/nuit manuellement
 *   .resetAuto()       — Revenir au mode automatique
 *   .getMode()         — 'day' | 'night'
 *   .setDayPalette(k)  — Changer la palette de jour (ex: 'soleil')
 *   .setNightPalette(k)— Changer la palette de nuit (ex: 'aurora')
 *   .getDayPalettes()  — Liste des palettes jour
 *   .getNightPalettes()— Liste des palettes nuit
 */

(function () {
    "use strict";

    // Configuration (injectée depuis le serveur ou valeurs par défaut)
    const serverConfig = window.VintAppDayNightConfig || {};

    const config = {
        enabled: serverConfig.enabled ?? true,
        dayStart: serverConfig.schedule?.day_start ?? 7,
        nightStart: serverConfig.schedule?.night_start ?? 19,
        transitionDuration: serverConfig.schedule?.transition_duration ?? 800,
        checkInterval: 60000,
        activeDayKey: serverConfig.activeDayKey ?? "ciel",
        activeNightKey: serverConfig.activeNightKey ?? "indigo",
        dayPalettes: serverConfig.dayPalettes ?? {},
        nightPalettes: serverConfig.nightPalettes ?? {},
    };

    if (!config.enabled) return;

    // État
    let currentMode = null;
    let manualOverride = null;
    let checkTimer = null;
    let activeDayKey = config.activeDayKey;
    let activeNightKey = config.activeNightKey;

    // =============================================
    // INJECTION CSS DYNAMIQUE
    // =============================================

    /**
     * Applique une palette de couleurs en injectant les variables CSS dans :root
     */
    function applyPaletteCSS(palette, targetSelector) {
        if (!palette || typeof palette !== "object") return;

        const root = document.documentElement;

        Object.keys(palette).forEach(function (key) {
            if (key === "name") return;
            const cssProp = key.replace(/_/g, "-");
            root.style.setProperty("--color-" + cssProp, palette[key]);
        });
    }

    /**
     * Injecte dynamiquement un bloc <style> pour [data-theme="night"]
     */
    function injectNightStyles(palette) {
        if (!palette || typeof palette !== "object") return;

        let existingBlock = document.getElementById("vintapp-night-dynamic");
        if (existingBlock) existingBlock.remove();

        let css = '[data-theme="night"] {\n';
        Object.keys(palette).forEach(function (key) {
            if (key === "name") return;
            const cssProp = key.replace(/_/g, "-");
            css += "  --color-" + cssProp + ": " + palette[key] + ";\n";
        });
        css += "}\n";

        const style = document.createElement("style");
        style.id = "vintapp-night-dynamic";
        style.textContent = css;
        document.head.appendChild(style);
    }

    /**
     * Injecte les couleurs de la palette de jour dans :root et [data-theme="day"]
     */
    function injectDayStyles(palette) {
        if (!palette || typeof palette !== "object") return;

        let existingBlock = document.getElementById("vintapp-day-dynamic");
        if (existingBlock) existingBlock.remove();

        let css = ':root, [data-theme="day"] {\n';
        Object.keys(palette).forEach(function (key) {
            if (key === "name") return;
            const cssProp = key.replace(/_/g, "-");
            css += "  --color-" + cssProp + ": " + palette[key] + ";\n";
        });
        css += "}\n";

        const style = document.createElement("style");
        style.id = "vintapp-day-dynamic";
        style.textContent = css;
        document.head.appendChild(style);
    }

    // =============================================
    // MODE JOUR / NUIT
    // =============================================

    /**
     * Détermine le mode actuel selon l'heure
     */
    function getTimeBasedMode() {
        const hour = new Date().getHours();
        return hour >= config.dayStart && hour < config.nightStart
            ? "day"
            : "night";
    }

    /**
     * Applique le mode (jour ou nuit) + la palette correspondante
     */
    function applyMode(mode, animate) {
        if (animate === undefined) animate = true;

        const root = document.documentElement;
        const oldMode = currentMode;
        currentMode = mode;

        if (!animate) {
            root.classList.add("no-transition");
        }

        root.setAttribute("data-theme", mode);

        if (mode === "night") {
            root.classList.add("dark");
        } else {
            root.classList.remove("dark");
        }

        // Appliquer les variables CSS de la palette active
        if (mode === "day") {
            const palette = config.dayPalettes[activeDayKey];
            if (palette) applyPaletteCSS(palette);
        } else {
            const palette = config.nightPalettes[activeNightKey];
            if (palette) applyPaletteCSS(palette);
        }

        if (!animate) {
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    root.classList.remove("no-transition");
                });
            });
        }

        localStorage.setItem("vintapp_day_night_mode", mode);
        localStorage.setItem(
            "vintapp_day_night_timestamp",
            Date.now().toString(),
        );

        updateIndicator(mode);
        updateThemeColor(mode);

        window.dispatchEvent(
            new CustomEvent("vintapp:theme-changed", {
                detail: {
                    mode: mode,
                    oldMode: oldMode,
                    isManual: manualOverride !== null,
                    dayPalette: activeDayKey,
                    nightPalette: activeNightKey,
                },
            }),
        );

    }

    function updateThemeColor(mode) {
        var metaTheme = document.querySelector('meta[name="theme-color"]');
        if (!metaTheme) return;

        var palette =
            mode === "night"
                ? config.nightPalettes[activeNightKey]
                : config.dayPalettes[activeDayKey];

        if (palette) {
            metaTheme.setAttribute(
                "content",
                palette.background ||
                    (mode === "night" ? "#0F172A" : "#FFFFFF"),
            );
        }
    }

    function updateIndicator(mode) {
        // Indicator removed — theme toggle is now in the header
    }

    function toggleMode() {
        var newMode = currentMode === "day" ? "night" : "day";
        manualOverride = newMode;
        localStorage.setItem("vintapp_day_night_manual", newMode);
        applyMode(newMode, true);

        if (typeof showToast === "function") {
            showToast(
                "info",
                newMode === "day"
                    ? "Mode jour active"
                    : "Mode nuit active",
            );
        }
    }

    function resetToAutoMode() {
        manualOverride = null;
        localStorage.removeItem("vintapp_day_night_manual");
        var autoMode = getTimeBasedMode();
        applyMode(autoMode, true);

        if (typeof showToast === "function") {
            showToast("info", "🔄 Mode automatique réactivé");
        }
    }

    // =============================================
    // GESTION DES PALETTES MULTIPLES
    // =============================================

    function setDayPalette(key) {
        if (!config.dayPalettes[key]) {
            console.warn("[VintApp] Palette jour '" + key + "' introuvable");
            return false;
        }

        activeDayKey = key;
        localStorage.setItem("vintapp_active_day_palette", key);
        injectDayStyles(config.dayPalettes[key]);

        if (currentMode === "day") {
            applyPaletteCSS(config.dayPalettes[key]);
        }

        window.dispatchEvent(
            new CustomEvent("vintapp:palette-changed", {
                detail: {
                    type: "day",
                    key: key,
                    palette: config.dayPalettes[key],
                },
            }),
        );

        console.log(
            "[VintApp] Palette jour changée : " +
                key +
                " (" +
                (config.dayPalettes[key].name || key) +
                ")",
        );
        return true;
    }

    function setNightPalette(key) {
        if (!config.nightPalettes[key]) {
            console.warn("[VintApp] Palette nuit '" + key + "' introuvable");
            return false;
        }

        activeNightKey = key;
        localStorage.setItem("vintapp_active_night_palette", key);
        injectNightStyles(config.nightPalettes[key]);

        if (currentMode === "night") {
            applyPaletteCSS(config.nightPalettes[key]);
        }

        window.dispatchEvent(
            new CustomEvent("vintapp:palette-changed", {
                detail: {
                    type: "night",
                    key: key,
                    palette: config.nightPalettes[key],
                },
            }),
        );

        console.log(
            "[VintApp] Palette nuit changée : " +
                key +
                " (" +
                (config.nightPalettes[key].name || key) +
                ")",
        );
        return true;
    }

    // =============================================
    // VÉRIFICATION PÉRIODIQUE & INDICATEUR
    // =============================================

    function startAutoCheck() {
        if (checkTimer) clearInterval(checkTimer);

        checkTimer = setInterval(function () {
            if (manualOverride) return;
            var shouldBe = getTimeBasedMode();
            if (shouldBe !== currentMode) {
                applyMode(shouldBe, true);
            }
        }, config.checkInterval);
    }

    function createIndicator() {
        // Indicator removed — theme toggle is now in the header
    }

    // =============================================
    // INITIALISATION
    // =============================================

    function init() {
        // Restaurer les palettes sauvegardées en localStorage
        var savedDay = localStorage.getItem("vintapp_active_day_palette");
        var savedNight = localStorage.getItem("vintapp_active_night_palette");

        if (savedDay && config.dayPalettes[savedDay]) {
            activeDayKey = savedDay;
        }
        if (savedNight && config.nightPalettes[savedNight]) {
            activeNightKey = savedNight;
        }

        // Injecter les styles CSS dynamiques pour les palettes sélectionnées
        if (config.dayPalettes[activeDayKey]) {
            injectDayStyles(config.dayPalettes[activeDayKey]);
        }
        if (config.nightPalettes[activeNightKey]) {
            injectNightStyles(config.nightPalettes[activeNightKey]);
        }

        // Override manuel récent (< 4h)
        var savedManual = localStorage.getItem("vintapp_day_night_manual");
        var savedTimestamp = localStorage.getItem(
            "vintapp_day_night_timestamp",
        );

        if (savedManual && savedTimestamp) {
            var elapsed = Date.now() - parseInt(savedTimestamp, 10);
            var fourHours = 4 * 60 * 60 * 1000;
            if (elapsed < fourHours) {
                manualOverride = savedManual;
            } else {
                localStorage.removeItem("vintapp_day_night_manual");
            }
        }

        // Utiliser la préférence utilisateur du serveur (profile.edit) comme override permanent
        if (!manualOverride && window.userTheme && window.userTheme !== 'auto') {
            manualOverride = window.userTheme === 'dark' ? 'night' : 'day';
        }

        var initialMode = manualOverride || getTimeBasedMode();
        applyMode(initialMode, false);

        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", createIndicator);
        } else {
            createIndicator();
        }

        startAutoCheck();

        document.addEventListener("visibilitychange", function () {
            if (!document.hidden && !manualOverride) {
                var shouldBe = getTimeBasedMode();
                if (shouldBe !== currentMode) {
                    applyMode(shouldBe, true);
                }
            }
        });
    }

    // API publique
    window.VintAppDayNight = {
        enabled: config.enabled,
        dayStart: config.dayStart,
        nightStart: config.nightStart,

        toggle: toggleMode,
        resetAuto: resetToAutoMode,
        getMode: function () {
            return currentMode;
        },
        isManual: function () {
            return manualOverride !== null;
        },
        setDayStart: function (h) {
            config.dayStart = h;
        },
        setNightStart: function (h) {
            config.nightStart = h;
        },

        // Palettes dynamiques
        setDayPalette: setDayPalette,
        setNightPalette: setNightPalette,
        getActiveDayKey: function () {
            return activeDayKey;
        },
        getActiveNightKey: function () {
            return activeNightKey;
        },
        getDayPalettes: function () {
            return config.dayPalettes;
        },
        getNightPalettes: function () {
            return config.nightPalettes;
        },

        getCurrentPalette: function () {
            return currentMode === "day"
                ? config.dayPalettes[activeDayKey]
                : config.nightPalettes[activeNightKey];
        },

        saveToServer: function () {
            var csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (!csrfMeta) return Promise.reject("No CSRF token");

            return fetch("/admin/settings/colors/day-night", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfMeta.getAttribute("content"),
                    Accept: "application/json",
                },
                body: JSON.stringify({
                    enabled: config.enabled,
                    day_start: config.dayStart,
                    night_start: config.nightStart,
                    active_day_palette: activeDayKey,
                    active_night_palette: activeNightKey,
                }),
            }).then(function (r) {
                return r.json();
            });
        },
    };

    init();
})();
