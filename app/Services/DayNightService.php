<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DayNightService
{
    protected array $config;

    public function __construct()
    {
        $this->config = config('colors.day_night', []);
    }

    /**
     * Vérifier si le mode jour/nuit est activé
     */
    public function isEnabled(): bool
    {
        // Vérifier d'abord en BDD, sinon config
        $dbEnabled = \App\Models\Setting::where('key', 'day_night_enabled')->first();
        if ($dbEnabled) {
            return $dbEnabled->value === 'true';
        }
        return $this->config['enabled'] ?? false;
    }

    /**
     * Déterminer si c'est actuellement le jour ou la nuit
     */
    public function getCurrentMode(): string
    {
        $hour = (int) now()->format('H');
        $dayStart = $this->config['day_start'] ?? 7;
        $nightStart = $this->config['night_start'] ?? 19;

        return ($hour >= $dayStart && $hour < $nightStart) ? 'day' : 'night';
    }

    // =============================================
    // GESTION DES PALETTES MULTIPLES
    // =============================================

    /**
     * Obtenir toutes les palettes de jour disponibles
     */
    public function getDayPalettes(): array
    {
        return $this->config['day_palettes'] ?? [];
    }

    /**
     * Obtenir toutes les palettes de nuit disponibles
     */
    public function getNightPalettes(): array
    {
        return $this->config['night_palettes'] ?? [];
    }

    /**
     * Obtenir la clé de la palette de jour active
     */
    public function getActiveDayKey(): string
    {
        $dbKey = \App\Models\Setting::where('key', 'day_night_active_day')->first();
        if ($dbKey) {
            return $dbKey->value;
        }
        return $this->config['active_day'] ?? 'ciel';
    }

    /**
     * Obtenir la clé de la palette de nuit active
     */
    public function getActiveNightKey(): string
    {
        $dbKey = \App\Models\Setting::where('key', 'day_night_active_night')->first();
        if ($dbKey) {
            return $dbKey->value;
        }
        return $this->config['active_night'] ?? 'indigo';
    }

    /**
     * Obtenir la palette de jour active (couleurs)
     */
    public function getDayPalette(): array
    {
        $key = $this->getActiveDayKey();
        $palettes = $this->getDayPalettes();
        return $palettes[$key] ?? $palettes['ciel'] ?? [];
    }

    /**
     * Obtenir la palette de nuit active (couleurs)
     */
    public function getNightPalette(): array
    {
        $key = $this->getActiveNightKey();
        $palettes = $this->getNightPalettes();
        return $palettes[$key] ?? $palettes['indigo'] ?? [];
    }

    /**
     * Obtenir la palette active selon l'heure
     */
    public function getActivePalette(): array
    {
        $mode = $this->getCurrentMode();
        return $mode === 'day' ? $this->getDayPalette() : $this->getNightPalette();
    }

    /**
     * Obtenir les heures de transition
     */
    public function getSchedule(): array
    {
        return [
            'day_start' => $this->config['day_start'] ?? 7,
            'night_start' => $this->config['night_start'] ?? 19,
            'transition_duration' => $this->config['transition_duration'] ?? 800,
        ];
    }

    /**
     * Générer les variables CSS pour les deux modes actifs (jour ET nuit)
     * Le CSS ne contient que les palettes sélectionnées.
     * Le JS appliquera dynamiquement les variables quand l'utilisateur change de palette.
     */
    public function generateCSSVariables(): string
    {
        $dayPalette = $this->getDayPalette();
        $nightPalette = $this->getNightPalette();
        $dayKey = $this->getActiveDayKey();
        $nightKey = $this->getActiveNightKey();

        $css = "/* VintApp Day/Night Mode - Généré automatiquement */\n";
        $css .= "/* Palette jour: {$dayKey} | Palette nuit: {$nightKey} */\n\n";

        // Variables de jour (défaut)
        $dayName = $dayPalette['name'] ?? $dayKey;
        $css .= "/* Mode Jour — {$dayName} */\n";
        $css .= ":root, [data-theme=\"day\"] {\n";
        $css .= $this->paletteToCSS($dayPalette);
        $css .= "}\n\n";

        // Variables de nuit
        $nightName = $nightPalette['name'] ?? $nightKey;
        $css .= "/* Mode Nuit — {$nightName} */\n";
        $css .= "[data-theme=\"night\"] {\n";
        $css .= $this->paletteToCSS($nightPalette);
        $css .= "}\n\n";

        // Transition fluide
        $transitionDuration = $this->config['transition_duration'] ?? 800;
        $css .= $this->generateTransitionCSS($transitionDuration);

        // Overrides mode nuit
        $css .= $this->generateNightOverridesCSS();

        // Indicateur visuel
        $css .= $this->generateIndicatorCSS();

        return $css;
    }

    /**
     * Générer le CSS de transitions
     */
    protected function generateTransitionCSS(int $duration): string
    {
        return <<<CSS
/* Transitions fluides */
*,
*::before,
*::after {
    transition:
        background-color {$duration}ms ease,
        color {$duration}ms ease,
        border-color {$duration}ms ease,
        box-shadow {$duration}ms ease,
        fill 400ms ease,
        stroke 400ms ease;
}

.no-transition,
.no-transition *,
.no-transition *::before,
.no-transition *::after {
    transition: none !important;
}

input,
textarea,
select,
button,
a,
[class*="animate-"],
[class*="transition-"] {
    transition:
        background-color {$duration}ms ease,
        color {$duration}ms ease,
        border-color {$duration}ms ease;
}

CSS;
    }

    /**
     * Générer les overrides CSS pour le mode nuit
     */
    protected function generateNightOverridesCSS(): string
    {
        return <<<'CSS'
/* ============================== */
/* OVERRIDES MODE NUIT            */
/* ============================== */

[data-theme="night"] body {
    background-color: var(--color-background);
    color: var(--color-text);
}

[data-theme="night"] .bg-white {
    background-color: var(--color-surface) !important;
}

[data-theme="night"] .bg-gray-50 {
    background-color: var(--color-background) !important;
}

[data-theme="night"] .bg-gray-100 {
    background-color: color-mix(in srgb, var(--color-surface), var(--color-background) 30%) !important;
}

[data-theme="night"] .bg-gray-200 {
    background-color: var(--color-surface) !important;
}

[data-theme="night"] .text-gray-800,
[data-theme="night"] .text-gray-900,
[data-theme="night"] .text-black {
    color: var(--color-text) !important;
}

[data-theme="night"] .text-gray-700 {
    color: color-mix(in srgb, var(--color-text), var(--color-text-muted) 30%) !important;
}

[data-theme="night"] .text-gray-600 {
    color: color-mix(in srgb, var(--color-text), var(--color-text-muted) 50%) !important;
}

[data-theme="night"] .text-gray-500 {
    color: var(--color-text-muted) !important;
}

[data-theme="night"] .text-gray-400 {
    color: color-mix(in srgb, var(--color-text-muted), var(--color-border) 30%) !important;
}

[data-theme="night"] .border-gray-100,
[data-theme="night"] .border-gray-200,
[data-theme="night"] .border-gray-300 {
    border-color: var(--color-border) !important;
}

[data-theme="night"] .divide-gray-200 > :not(:last-child) {
    border-color: var(--color-border) !important;
}

[data-theme="night"] .shadow-sm {
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.3) !important;
}

[data-theme="night"] .shadow {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4) !important;
}

[data-theme="night"] .shadow-md {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.4) !important;
}

[data-theme="night"] .shadow-lg {
    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.5) !important;
}

[data-theme="night"] input,
[data-theme="night"] textarea,
[data-theme="night"] select {
    background-color: var(--color-surface) !important;
    color: var(--color-text) !important;
    border-color: var(--color-border) !important;
}

[data-theme="night"] input::placeholder,
[data-theme="night"] textarea::placeholder {
    color: var(--color-text-muted) !important;
}

[data-theme="night"] input:focus,
[data-theme="night"] textarea:focus,
[data-theme="night"] select:focus {
    border-color: var(--color-primary) !important;
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-primary), transparent 80%) !important;
}

[data-theme="night"] table {
    color: var(--color-text);
}

[data-theme="night"] th {
    background-color: color-mix(in srgb, var(--color-surface), var(--color-background) 30%) !important;
    color: var(--color-text) !important;
    border-color: var(--color-border) !important;
}

[data-theme="night"] td {
    border-color: var(--color-border) !important;
}

[data-theme="night"] tr:hover {
    background-color: color-mix(in srgb, var(--color-surface), transparent 50%) !important;
}

[data-theme="night"] .bg-white.rounded-lg,
[data-theme="night"] .bg-white.rounded-xl {
    background-color: var(--color-surface) !important;
    border-color: var(--color-border) !important;
}

[data-theme="night"] [class*="modal"] .bg-white {
    background-color: var(--color-surface) !important;
}

[data-theme="night"] [class*="dropdown"] {
    background-color: var(--color-surface) !important;
    border-color: var(--color-border) !important;
}

[data-theme="night"] ::-webkit-scrollbar {
    width: 8px;
}

[data-theme="night"] ::-webkit-scrollbar-track {
    background: var(--color-background);
}

[data-theme="night"] ::-webkit-scrollbar-thumb {
    background: var(--color-border);
    border-radius: 4px;
}

[data-theme="night"] ::-webkit-scrollbar-thumb:hover {
    background: var(--color-text-muted);
}

[data-theme="night"] header {
    background-color: var(--color-surface) !important;
    border-color: var(--color-border) !important;
}

[data-theme="night"] nav {
    background-color: var(--color-surface) !important;
}

CSS;
    }

    /**
     * Générer le CSS de l'indicateur flottant
     */
    protected function generateIndicatorCSS(): string
    {
        return <<<'CSS'
/* ============================== */
/* BOUTON INDICATEUR JOUR/NUIT   */
/* ============================== */
.day-night-indicator {
    position: fixed;
    bottom: 80px;
    right: 20px;
    z-index: 9999;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 20px;
    user-select: none;
    -webkit-tap-highlight-color: transparent;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

[data-theme="day"] .day-night-indicator {
    background: linear-gradient(135deg, var(--color-warning, #fcd34d), var(--color-primary, #f59e0b));
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
}

[data-theme="night"] .day-night-indicator {
    background: linear-gradient(135deg, var(--color-primary, #818cf8), var(--color-accent, #4f46e5));
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
}

.day-night-indicator:hover {
    transform: scale(1.1);
}

.day-night-indicator:active {
    transform: scale(0.95);
}

CSS;
    }

    /**
     * Convertir une palette en propriétés CSS
     */
    protected function paletteToCSS(array $palette): string
    {
        $css = '';

        foreach ($palette as $colorName => $colorValue) {
            if ($colorName === 'name') continue;

            // Transformer les underscores en tirets pour CSS
            $cssProp = str_replace('_', '-', $colorName);
            $css .= "  --color-{$cssProp}: {$colorValue};\n";

            // Générer les nuances 50-900 seulement pour les couleurs principales
            $mainColors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'accent'];
            if (in_array($colorName, $mainColors)) {
                $shades = $this->generateShades($colorValue);
                foreach ($shades as $shade => $value) {
                    $css .= "  --color-{$cssProp}-{$shade}: {$value};\n";
                }
            }
        }

        return $css;
    }

    /**
     * Générer les nuances d'une couleur (50 à 900)
     */
    protected function generateShades(string $hex): array
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $shades = [];

        $factors = [50 => 0.95, 100 => 0.9, 200 => 0.8, 300 => 0.6, 400 => 0.4];
        foreach ($factors as $shade => $factor) {
            $shades[$shade] = sprintf('#%02x%02x%02x',
                min(255, $r + (255 - $r) * $factor),
                min(255, $g + (255 - $g) * $factor),
                min(255, $b + (255 - $b) * $factor)
            );
        }

        $shades[500] = "#{$hex}";

        $darkFactors = [600 => 0.9, 700 => 0.8, 800 => 0.7, 900 => 0.6];
        foreach ($darkFactors as $shade => $factor) {
            $shades[$shade] = sprintf('#%02x%02x%02x',
                max(0, round($r * $factor)),
                max(0, round($g * $factor)),
                max(0, round($b * $factor))
            );
        }

        return $shades;
    }

    /**
     * Générer le fichier CSS et le sauvegarder dans public/css/
     */
    public function publishCSS(): bool
    {
        try {
            $css = $this->generateCSSVariables();
            $path = public_path('css/day-night-theme.css');

            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            file_put_contents($path, $css);

            Cache::forget('vintapp_day_night_css');

            return true;
        } catch (\Exception $e) {
            Log::error('DayNightService: Erreur publication CSS - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Données complètes pour le JavaScript côté client
     * Inclut TOUTES les palettes pour que le JS puisse switcher dynamiquement
     */
    public function getClientConfig(): array
    {
        return [
            'enabled' => $this->isEnabled(),
            'currentMode' => $this->getCurrentMode(),
            'schedule' => $this->getSchedule(),
            'activeDayKey' => $this->getActiveDayKey(),
            'activeNightKey' => $this->getActiveNightKey(),
            'dayPalettes' => $this->preparePalettesForJS($this->getDayPalettes()),
            'nightPalettes' => $this->preparePalettesForJS($this->getNightPalettes()),
        ];
    }

    /**
     * Préparer les palettes avec les nuances pour le JS
     */
    protected function preparePalettesForJS(array $palettes): array
    {
        $result = [];
        foreach ($palettes as $key => $palette) {
            $result[$key] = $palette;
            // Ajouter les nuances générées pour les couleurs principales
            $mainColors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'accent'];
            foreach ($mainColors as $colorName) {
                if (isset($palette[$colorName])) {
                    $shades = $this->generateShades($palette[$colorName]);
                    foreach ($shades as $shade => $value) {
                        $result[$key]["{$colorName}_{$shade}"] = $value;
                    }
                }
            }
        }
        return $result;
    }
}
