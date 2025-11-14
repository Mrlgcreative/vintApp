<?php

namespace App\Helpers;

class ColorSystemHelper
{
    /**
     * Configuration des palettes de couleurs VintApp
     */
    private static $palettes = [
        'luxury' => [
            'primary' => '#8B5CF6', // Violet royal
            'secondary' => '#6B7280', // Gris sophistiqué
            'accent' => '#F59E0B', // Or élégant
            'success' => '#10B981', // Vert émeraude
            'danger' => '#EF4444', // Rouge cardinal
            'warning' => '#F59E0B', // Ambre
            'info' => '#06B6D4', // Cyan
            'dark' => '#1F2937', // Noir profond
            'light' => '#F8FAFC', // Blanc nacré
        ],
        'modern' => [
            'primary' => '#3B82F6', // Bleu moderne
            'secondary' => '#6B7280', // Gris moderne
            'accent' => '#8B5CF6', // Violet moderne
            'success' => '#10B981', // Vert moderne
            'danger' => '#EF4444', // Rouge moderne
            'warning' => '#F59E0B', // Orange moderne
            'info' => '#06B6D4', // Cyan moderne
            'dark' => '#1F2937', // Gris très foncé
            'light' => '#F8FAFC', // Blanc moderne
        ],
        'elegant' => [
            'primary' => '#1F2937', // Noir élégant
            'secondary' => '#6B7280', // Gris élégant
            'accent' => '#D97706', // Bronze élégant
            'success' => '#059669', // Vert classique
            'danger' => '#DC2626', // Rouge classique
            'warning' => '#D97706', // Bronze
            'info' => '#0891B2', // Teal
            'dark' => '#111827', // Noir profond
            'light' => '#F9FAFB', // Blanc cassé
        ]
    ];

    /**
     * Génère les variables CSS pour une palette donnée
     */
    public static function generateCSSVariables($palette = 'luxury')
    {
        if (!isset(self::$palettes[$palette])) {
            $palette = 'luxury';
        }

        $colors = self::$palettes[$palette];
        $css = ":root {\n";

        foreach ($colors as $name => $color) {
            $shades = self::generateColorShades($color);
            
            // Couleur principale
            $css .= "  --color-{$name}: {$color};\n";
            
            // Nuances
            foreach ($shades as $shade => $value) {
                $css .= "  --color-{$name}-{$shade}: {$value};\n";
            }
        }

        // Couleurs spéciales pour les rôles
        $css .= "  --color-admin-primary: #DC2626;\n";
        $css .= "  --color-admin-accent: #FCD34D;\n";
        $css .= "  --color-expert-primary: #7C3AED;\n";
        $css .= "  --color-expert-accent: #A78BFA;\n";
        $css .= "  --color-vendor-primary: #059669;\n";
        $css .= "  --color-vendor-accent: #6EE7B7;\n";
        $css .= "  --color-buyer-primary: #2563EB;\n";
        $css .= "  --color-buyer-accent: #93C5FD;\n";

        // Couleurs de statut
        $css .= "  --color-status-pending: #F59E0B;\n";
        $css .= "  --color-status-approved: #10B981;\n";
        $css .= "  --color-status-rejected: #EF4444;\n";
        $css .= "  --color-status-processing: #3B82F6;\n";
        $css .= "  --color-status-completed: #059669;\n";
        $css .= "  --color-status-cancelled: #6B7280;\n";
        $css .= "  --color-status-active: #10B981;\n";
        $css .= "  --color-status-inactive: #6B7280;\n";
        $css .= "  --color-status-verified: #10B981;\n";
        $css .= "  --color-status-unverified: #F59E0B;\n";

        $css .= "}\n";

        return $css;
    }

    /**
     * Génère automatiquement les nuances d'une couleur
     */
    private static function generateColorShades($color)
    {
        // Conversion hex vers RGB
        $hex = ltrim($color, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $shades = [];

        // Génération des nuances claires (50-400)
        for ($i = 50; $i <= 400; $i += 50) {
            $factor = ($i / 500); // 0.1 à 0.8
            $newR = min(255, $r + (255 - $r) * $factor);
            $newG = min(255, $g + (255 - $g) * $factor);
            $newB = min(255, $b + (255 - $b) * $factor);
            $shades[$i] = sprintf('#%02x%02x%02x', $newR, $newG, $newB);
        }

        // Nuance 500 = couleur originale
        $shades[500] = $color;

        // Génération des nuances foncées (600-900)
        for ($i = 600; $i <= 900; $i += 100) {
            $factor = 1 - (($i - 500) / 500); // 0.8 à 0.2
            $newR = max(0, $r * $factor);
            $newG = max(0, $g * $factor);
            $newB = max(0, $b * $factor);
            $shades[$i] = sprintf('#%02x%02x%02x', $newR, $newG, $newB);
        }

        return $shades;
    }

    /**
     * Met à jour automatiquement le fichier CSS
     */
    public static function updateCSSFile($palette = 'luxury')
    {
        $cssPath = resource_path('css/app.css');
        
        if (!file_exists($cssPath)) {
            return false;
        }

        $currentContent = file_get_contents($cssPath);
        $newVariables = self::generateCSSVariables($palette);

        // Rechercher et remplacer les variables existantes
        $pattern = '/:root\s*{[^}]*--color-[^}]*}/s';
        
        if (preg_match($pattern, $currentContent)) {
            // Remplacer les variables existantes
            $updatedContent = preg_replace($pattern, $newVariables, $currentContent);
        } else {
            // Ajouter les variables au début du fichier après @tailwind
            $tailwindEnd = strpos($currentContent, '@tailwind utilities;');
            if ($tailwindEnd !== false) {
                $tailwindEnd = strpos($currentContent, "\n", $tailwindEnd) + 1;
                $updatedContent = substr($currentContent, 0, $tailwindEnd) . 
                                "\n/* Variables CSS dynamiques VintApp - injectées automatiquement */\n" .
                                $newVariables . "\n" .
                                substr($currentContent, $tailwindEnd);
            } else {
                $updatedContent = $newVariables . "\n\n" . $currentContent;
            }
        }

        return file_put_contents($cssPath, $updatedContent) !== false;
    }

    /**
     * Active une palette de couleurs
     */
    public static function activatePalette($palette = 'luxury')
    {
        // Mettre à jour le CSS
        $cssUpdated = self::updateCSSFile($palette);
        
        // Enregistrer la palette active
        $settingsPath = storage_path('framework/cache/color_palette.json');
        $settings = [
            'active_palette' => $palette,
            'updated_at' => now()->toISOString(),
            'css_updated' => $cssUpdated
        ];
        
        @mkdir(dirname($settingsPath), 0755, true);
        file_put_contents($settingsPath, json_encode($settings, JSON_PRETTY_PRINT));

        return $cssUpdated;
    }

    /**
     * Récupère la palette active
     */
    public static function getActivePalette()
    {
        $settingsPath = storage_path('framework/cache/color_palette.json');
        
        if (file_exists($settingsPath)) {
            $settings = json_decode(file_get_contents($settingsPath), true);
            return $settings['active_palette'] ?? 'luxury';
        }

        return 'luxury';
    }

    /**
     * Liste toutes les palettes disponibles
     */
    public static function getAvailablePalettes()
    {
        return array_keys(self::$palettes);
    }

    /**
     * Initialise le système de couleurs automatiquement
     */
    public static function autoInitialize()
    {
        $palette = self::getActivePalette();
        return self::activatePalette($palette);
    }
}