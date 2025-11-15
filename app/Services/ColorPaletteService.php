<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ColorPaletteService
{
    protected $config;
    protected $activePalette;

    public function __construct()
    {
        $this->config = config('colors');
    }

    /**
     * Obtenir la palette active depuis la base de données
     */
    public function getActivePalette(): array
    {
        $paletteName = $this->getActivePaletteName();
        return $this->getPalette($paletteName);
    }

    /**
     * Obtenir le nom de la palette active
     */
    public function getActivePaletteName(): string
    {
        try {
            $setting = \App\Models\Setting::where('key', 'active_color_palette')->first();
            return $setting ? $setting->value : 'default';
        } catch (\Exception $e) {
            return 'default';
        }
    }

    /**
     * Obtenir une palette par son nom
     */
    public function getPalette(string $paletteName): array
    {
        // D'abord chercher dans les palettes prédéfinies
        if (isset($this->config['palettes'][$paletteName])) {
            return $this->config['palettes'][$paletteName];
        }

        // Ensuite chercher dans les palettes personnalisées (base de données)
        try {
            $customPalette = \App\Models\Setting::where('key', "custom_palette_{$paletteName}")->first();
            if ($customPalette && $customPalette->value) {
                return json_decode($customPalette->value, true);
            }
        } catch (\Exception $e) {
            // Fallback
        }

        // Retourner la palette par défaut si rien n'est trouvé
        return $this->config['palettes']['default'];
    }

    /**
     * Vérifier si une palette existe
     */
    public function paletteExists(string $paletteName): bool
    {
        // Vérifier dans les palettes prédéfinies
        if (isset($this->config['palettes'][$paletteName])) {
            return true;
        }

        // Vérifier dans les palettes personnalisées du cache
        $customPalettes = Cache::get('vintapp_custom_palettes', []);
        if (isset($customPalettes[$paletteName])) {
            return true;
        }

        // Vérifier dans les palettes personnalisées de la base de données
        try {
            $customPalette = \App\Models\Setting::where('key', "custom_palette_{$paletteName}")->first();
            return $customPalette && $customPalette->value;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Définir la palette active
     */
    public function setActivePalette(string $paletteName): bool
    {
        try {
            // Vérifier que la palette existe d'abord
            if (!$this->paletteExists($paletteName)) {
                return false;
            }

            \App\Models\Setting::updateOrCreate(
                ['key' => 'active_color_palette'],
                [
                    'value' => $paletteName,
                    'label' => 'Palette de couleurs active',
                    'description' => 'Définit quelle palette de couleurs est actuellement active dans l\'application',
                    'category' => 'appearance',
                    'type' => 'text',
                    'is_public' => false,
                    'is_encrypted' => false
                ]
            );
            
            // Vider le cache
            Cache::forget('vintapp_active_palette_css');
            return true;
        } catch (\Exception $e) {
            // En cas d'erreur, log et retourner false
            Log::error('Erreur lors du changement de palette: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir une couleur spécifique
     */
    public function getColor(string $colorName): string
    {
        $activePalette = $this->getActivePalette();
        return $activePalette[$colorName] ?? $this->config['palettes']['default'][$colorName] ?? '#000000';
    }

    /**
     * Obtenir toutes les couleurs de la palette active
     */
    public function getAllColors(): array
    {
        return $this->getActivePalette();
    }

    /**
     * Changer la palette active (méthode mise à jour)
     */
    public function setPalette(string $paletteName): bool
    {
        return $this->setActivePalette($paletteName);
    }

    /**
     * Obtenir le CSS pour la palette active (avec cache)
     */
    public function generateActivePaletteCSS(): string
    {
        return Cache::remember('vintapp_active_palette_css', 3600, function () {
            return $this->generateCSSVariables();
        });
    }

    /**
     * Obtenir la liste des palettes disponibles
     */
    public function getAvailablePalettes(): array
    {
        return collect($this->config['palettes'])->map(function ($palette, $key) {
            return [
                'key' => $key,
                'name' => $palette['name'],
                'colors' => $palette
            ];
        })->toArray();
    }

    /**
     * Générer les variables CSS pour la palette active
     */
    public function generateCSSVariables(): string
    {
        $activePalette = $this->getActivePalette();
        $css = ":root {\n";
        
        foreach ($activePalette as $colorName => $colorValue) {
            if ($colorName !== 'name') {
                $css .= "  --color-{$colorName}: {$colorValue};\n";
                
                // Générer les variations de teinte
                $css .= $this->generateColorShades($colorName, $colorValue);
            }
        }

        // Ajouter les couleurs de rôles
        foreach ($this->config['roles'] as $role => $colors) {
            foreach ($colors as $colorType => $colorValue) {
                $css .= "  --color-{$role}-{$colorType}: {$colorValue};\n";
            }
        }

        // Ajouter les couleurs de statut
        foreach ($this->config['status'] as $status => $colorValue) {
            $css .= "  --color-status-{$status}: {$colorValue};\n";
        }

        $css .= "}\n";
        return $css;
    }

    /**
     * Générer les nuances d'une couleur
     */
    protected function generateColorShades(string $colorName, string $hexColor): string
    {
        $css = "";
        $rgb = $this->hexToRgb($hexColor);
        
        if ($rgb) {
            // Générer les nuances de 50 à 900
            $shades = [
                50 => 0.95,   // Très clair
                100 => 0.9,   // Clair
                200 => 0.8,   // Plus clair
                300 => 0.6,   // Clair moyen
                400 => 0.4,   // Moyen clair
                500 => 0.0,   // Couleur de base
                600 => -0.1,  // Moyen foncé
                700 => -0.2,  // Foncé
                800 => -0.3,  // Plus foncé
                900 => -0.4   // Très foncé
            ];

            foreach ($shades as $shade => $adjustment) {
                $adjustedColor = $this->adjustBrightness($hexColor, $adjustment);
                $css .= "  --color-{$colorName}-{$shade}: {$adjustedColor};\n";
            }
        }

        return $css;
    }

    /**
     * Convertir hex en RGB
     */
    protected function hexToRgb(string $hex): ?array
    {
        $hex = ltrim($hex, '#');
        
        if (strlen($hex) === 6) {
            return [
                'r' => hexdec(substr($hex, 0, 2)),
                'g' => hexdec(substr($hex, 2, 2)),
                'b' => hexdec(substr($hex, 4, 2))
            ];
        }
        
        return null;
    }

    /**
     * Ajuster la luminosité d'une couleur
     */
    protected function adjustBrightness(string $hex, float $adjustment): string
    {
        $rgb = $this->hexToRgb($hex);
        
        if (!$rgb) {
            return $hex;
        }

        if ($adjustment > 0) {
            // Éclaircir en mélangeant avec du blanc
            $rgb['r'] = round($rgb['r'] + (255 - $rgb['r']) * $adjustment);
            $rgb['g'] = round($rgb['g'] + (255 - $rgb['g']) * $adjustment);
            $rgb['b'] = round($rgb['b'] + (255 - $rgb['b']) * $adjustment);
        } else {
            // Assombrir en multipliant par un facteur
            $factor = 1 + $adjustment;
            $rgb['r'] = round($rgb['r'] * $factor);
            $rgb['g'] = round($rgb['g'] * $factor);
            $rgb['b'] = round($rgb['b'] * $factor);
        }

        // S'assurer que les valeurs restent dans la plage 0-255
        $rgb['r'] = max(0, min(255, $rgb['r']));
        $rgb['g'] = max(0, min(255, $rgb['g']));
        $rgb['b'] = max(0, min(255, $rgb['b']));

        return sprintf('#%02x%02x%02x', $rgb['r'], $rgb['g'], $rgb['b']);
    }

    /**
     * Obtenir la couleur selon le rôle de l'utilisateur
     */
    public function getUserRoleColor(string $role, string $type = 'primary'): string
    {
        return $this->config['roles'][$role][$type] ?? $this->getColor('primary');
    }

    /**
     * Obtenir la couleur selon le statut
     */
    public function getStatusColor(string $status): string
    {
        return $this->config['status'][$status] ?? $this->getColor('secondary');
    }

    /**
     * Générer une classe CSS Tailwind
     */
    public function getTailwindClass(string $color, string $type = 'bg', string $shade = '500'): string
    {
        $colorMap = [
            'primary' => 'blue',
            'secondary' => 'gray',
            'success' => 'green',
            'danger' => 'red',
            'warning' => 'yellow',
            'info' => 'cyan',
            'light' => 'gray',
            'dark' => 'gray',
            'accent' => 'purple'
        ];

        $tailwindColor = $colorMap[$color] ?? $color;
        
        switch ($type) {
            case 'bg':
                return "bg-{$tailwindColor}-{$shade}";
            case 'text':
                return "text-{$tailwindColor}-{$shade}";
            case 'border':
                return "border-{$tailwindColor}-{$shade}";
            case 'ring':
                return "ring-{$tailwindColor}-{$shade}";
            default:
                return "bg-{$tailwindColor}-{$shade}";
        }
    }

    /**
     * Vérifier si le mode sombre est activé
     */
    public function isDarkModeEnabled(): bool
    {
        return $this->config['dark_mode']['enabled'] ?? false;
    }

    /**
     * Activer/désactiver le mode sombre
     */
    public function toggleDarkMode(): bool
    {
        $currentState = $this->isDarkModeEnabled();
        Cache::put('vintapp_dark_mode', !$currentState, now()->addDays(30));
        return !$currentState;
    }

    /**
     * Générer le fichier CSS complet
     */
    public function generateFullCSS(): string
    {
        $css = $this->generateCSSVariables();
        
        // Ajouter les classes utilitaires
        $css .= "\n/* Classes utilitaires VintApp */\n";
        $css .= ".bg-primary { background-color: var(--color-primary); }\n";
        $css .= ".bg-secondary { background-color: var(--color-secondary); }\n";
        $css .= ".bg-success { background-color: var(--color-success); }\n";
        $css .= ".bg-danger { background-color: var(--color-danger); }\n";
        $css .= ".bg-warning { background-color: var(--color-warning); }\n";
        $css .= ".bg-info { background-color: var(--color-info); }\n";
        
        $css .= ".text-primary { color: var(--color-primary); }\n";
        $css .= ".text-secondary { color: var(--color-secondary); }\n";
        $css .= ".text-success { color: var(--color-success); }\n";
        $css .= ".text-danger { color: var(--color-danger); }\n";
        $css .= ".text-warning { color: var(--color-warning); }\n";
        $css .= ".text-info { color: var(--color-info); }\n";
        
        $css .= ".border-primary { border-color: var(--color-primary); }\n";
        $css .= ".border-secondary { border-color: var(--color-secondary); }\n";
        $css .= ".border-success { border-color: var(--color-success); }\n";
        $css .= ".border-danger { border-color: var(--color-danger); }\n";
        $css .= ".border-warning { border-color: var(--color-warning); }\n";
        $css .= ".border-info { border-color: var(--color-info); }\n";

        // Mode sombre
        if ($this->isDarkModeEnabled()) {
            $css .= "\n/* Mode sombre */\n";
            $css .= "@media (prefers-color-scheme: dark) {\n";
            $css .= "  :root {\n";
            $css .= "    --color-light: #1F2937;\n";
            $css .= "    --color-dark: #F8FAFC;\n";
            $css .= "  }\n";
            $css .= "}\n";
        }

        return $css;
    }
}