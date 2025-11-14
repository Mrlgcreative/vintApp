<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class CSSInjectionService
{
    protected $colorService;

    public function __construct(ColorPaletteService $colorService)
    {
        $this->colorService = $colorService;
    }

    /**
     * Générer le fichier CSS personnalisé avec les couleurs actives
     */
    public function generateCustomCSS(): string
    {
        return Cache::remember('vintapp_custom_css', 3600, function () {
            $activePalette = $this->colorService->getActivePalette();
            
            // Lire le fichier CSS de base
            $baseCSS = File::get(resource_path('css/app.css'));
            
            // Générer les variables CSS avec les couleurs actives
            $colorVariables = $this->generateColorVariables($activePalette);
            
            // Remplacer les variables dans le CSS
            $customCSS = $this->injectColorVariables($baseCSS, $colorVariables);
            
            return $customCSS;
        });
    }

    /**
     * Générer les variables CSS pour les couleurs
     */
    protected function generateColorVariables(array $palette): string
    {
        $css = ":root {\n";
        
        // Variables principales du système
        foreach ($palette as $colorName => $colorValue) {
            if ($colorName !== 'name') {
                $css .= "    --color-{$colorName}: {$colorValue};\n";
            }
        }
        
        // Variables dérivées pour compatibilité avec l'ancien système
        $css .= "    \n";
        $css .= "    /* Variables dérivées pour compatibilité */\n";
        $css .= "    --primary-color: var(--color-primary);\n";
        $css .= "    --primary-light: var(--color-accent);\n";
        $css .= "    --success-color: var(--color-success);\n";
        $css .= "    --info-color: var(--color-info);\n";
        $css .= "    --warning-color: var(--color-warning);\n";
        $css .= "    --danger-color: var(--color-danger);\n";
        $css .= "    --dark-color: var(--color-dark);\n";
        $css .= "    --light-color: var(--color-light);\n";
        $css .= "    --gray-color: var(--color-secondary);\n";
        $css .= "    --text-color: #222;\n";
        $css .= "    --white: #fff;\n";
        $css .= "    \n";
        
        // Générer les nuances automatiquement
        foreach ($palette as $colorName => $colorValue) {
            if ($colorName !== 'name') {
                $css .= $this->generateColorShades($colorName, $colorValue);
            }
        }
        
        $css .= "    --radius: 0.5rem;\n";
        $css .= "    --shadow: 0 4px 24px rgba(0,0,0,0.08), 0 1.5px 4px rgba(0,0,0,0.04);\n";
        $css .= "    --shadow-hover: 0 8px 32px rgba(0,0,0,0.16), 0 2px 8px rgba(0,0,0,0.08);\n";
        $css .= "    \n";
        $css .= "    --font-primary: 'Figtree', sans-serif;\n";
        $css .= "    --transition: all 0.3s ease;\n";
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
                $css .= "    --color-{$colorName}-{$shade}: {$adjustedColor};\n";
            }
        }

        return $css;
    }

    /**
     * Injecter les variables de couleur dans le CSS
     */
    protected function injectColorVariables(string $css, string $colorVariables): string
    {
        // Trouver et remplacer la section :root
        $pattern = '/\/\* --- Variables CSS Dynamiques.*?\*\/.*?:root\s*\{[^}]*\}/s';
        
        $replacement = "/* --- Variables CSS Dynamiques (injectées par le système de couleurs) --- */\n" . $colorVariables;
        
        $newCSS = preg_replace($pattern, $replacement, $css);
        
        // Si la regex n'a pas fonctionné, ajouter les variables au début
        if ($newCSS === $css) {
            $newCSS = $colorVariables . "\n\n" . $css;
        }
        
        return $newCSS;
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
     * Sauvegarder le CSS personnalisé dans public/css
     */
    public function saveCustomCSS(): string
    {
        $customCSS = $this->generateCustomCSS();
        $publicPath = public_path('css');
        
        if (!File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0755, true);
        }

        $filename = 'vintapp-dynamic.css';
        $filepath = $publicPath . '/' . $filename;
        
        File::put($filepath, $customCSS);
        
        return $filename;
    }

    /**
     * Obtenir l'URL du CSS personnalisé
     */
    public function getCustomCSSUrl(): string
    {
        $filename = $this->saveCustomCSS();
        return asset('css/' . $filename);
    }

    /**
     * Vider le cache CSS
     */
    public function clearCache(): void
    {
        Cache::forget('vintapp_custom_css');
    }
}