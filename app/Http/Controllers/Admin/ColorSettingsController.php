<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ColorPaletteService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class ColorSettingsController extends Controller
{
    protected $colorService;
    protected $cssService;

    public function __construct(ColorPaletteService $colorService)
    {
        $this->colorService = $colorService;
        $this->cssService = app(\App\Services\CSSInjectionService::class);
    }

    /**
     * Afficher les paramètres de couleurs
     */
    public function index()
    {
        $palettes = config('colors.palettes');
        
        // Fusionner avec les palettes personnalisées
        $customPalettes = Cache::get('vintapp_custom_palettes', []);
        $palettes = array_merge($palettes, $customPalettes);
        
        $activePaletteName = $this->colorService->getActivePaletteName();
        $currentColors = $this->colorService->getAllColors();

        return view('admin.settings.colors', compact('palettes', 'activePaletteName', 'currentColors'));
    }

    /**
     * Mettre à jour la palette de couleurs
     */
    public function update(Request $request)
    {
        $request->validate([
            'palette' => 'required|string'
        ]);

        try {
            $paletteName = $request->palette;
            
            // Vérifier si la palette existe
            if (!$this->colorService->paletteExists($paletteName)) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => "La palette '{$paletteName}' n'existe pas."
                    ], 404);
                }
                return redirect()->back()->with('error', "La palette '{$paletteName}' n'existe pas.");
            }
            
            // Appliquer la palette
            if ($this->colorService->setActivePalette($paletteName)) {
                // Vider le cache CSS pour forcer la régénération
                Cache::forget('vintapp_active_palette_css');
                
                // Injecter automatiquement les couleurs dans le CSS
                \Artisan::call('colors:inject');
                
                // Compiler automatiquement avec npm run build
                try {
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                        exec('cd ' . base_path() . ' && npm run build > nul 2>&1 &');
                    } else {
                        exec('cd ' . base_path() . ' && npm run build > /dev/null 2>&1 &');
                    }
                } catch (\Exception $e) {
                    // Ignorer les erreurs de compilation
                }
                
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => "Palette '{$paletteName}' activée avec succès ! Les couleurs vont être compilées automatiquement."
                    ]);
                }
                
                return redirect()->back()->with('success', 'Palette de couleurs mise à jour et CSS injecté avec succès !');
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de mettre à jour la palette.'
                ], 500);
            }
            return redirect()->back()->with('error', 'Impossible de mettre à jour la palette.');

        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour : ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    /**
     * Aperçu d'une palette
     */
    public function preview(Request $request, string $palette)
    {
        try {
            $palettes = config('colors.palettes');
            
            if (!isset($palettes[$palette])) {
                return response()->json(['success' => false, 'message' => 'Palette introuvable'], 404);
            }

            // Créer une instance temporaire du service avec la nouvelle palette
            $tempConfig = config('colors');
            $tempConfig['active_palette'] = $palette;
            
            // Générer le CSS pour l'aperçu
            $css = $this->generatePreviewCSS($palettes[$palette]);

            return response()->json([
                'success' => true,
                'css' => $css,
                'palette' => $palettes[$palette]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Récupérer les palettes personnalisées
     */
    public function getCustomPalettes()
    {
        try {
            $customPalettes = Cache::get('vintapp_custom_palettes', []);
            
            return response()->json([
                'success' => true,
                'palettes' => $customPalettes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer une palette personnalisée
     */
    public function createCustom(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'primary' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'success' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'danger' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'warning' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'info' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'light' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'dark' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        try {
            $customPalettes = Cache::get('vintapp_custom_palettes', []);
            $paletteKey = 'custom_' . uniqid();

            $paletteData = [
                'name' => $request->name,
                'primary' => $request->primary,
                'secondary' => $request->secondary,
                'success' => $request->success,
                'danger' => $request->danger,
                'warning' => $request->warning,
                'info' => $request->info,
                'light' => $request->light,
                'dark' => $request->dark,
                'accent' => $request->accent,
                'created_at' => now(),
                'custom' => true
            ];
            
            $customPalettes[$paletteKey] = $paletteData;

            // Sauvegarder dans le cache
            Cache::put('vintapp_custom_palettes', $customPalettes, now()->addDays(365));
            
            // Sauvegarder aussi dans la base de données pour persistance
            \App\Models\Setting::updateOrCreate(
                ['key' => "custom_palette_{$paletteKey}"],
                [
                    'value' => json_encode($paletteData),
                    'label' => "Palette personnalisée: {$request->name}",
                    'description' => 'Palette de couleurs personnalisée créée par l\'utilisateur',
                    'category' => 'appearance',
                    'type' => 'json',
                    'is_public' => false,
                    'is_encrypted' => false
                ]
            );

            return redirect()->back()->with('success', 'Palette personnalisée créée avec succès !');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une palette personnalisée
     */
    public function deleteCustom(Request $request, string $paletteKey)
    {
        try {
            $customPalettes = Cache::get('vintapp_custom_palettes', []);
            
            if (isset($customPalettes[$paletteKey])) {
                unset($customPalettes[$paletteKey]);
                Cache::put('vintapp_custom_palettes', $customPalettes, now()->addDays(365));
                
                return response()->json(['success' => true, 'message' => 'Palette supprimée']);
            }

            return response()->json(['success' => false, 'message' => 'Palette introuvable'], 404);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Exporter la configuration des couleurs
     */
    public function export()
    {
        try {
            $config = [
                'active_palette' => config('colors.active_palette'),
                'palettes' => config('colors.palettes'),
                'custom_palettes' => Cache::get('vintapp_custom_palettes', []),
                'dark_mode' => Cache::get('vintapp_dark_mode', false),
                'auto_dark_mode' => Cache::get('vintapp_auto_dark_mode', true),
                'exported_at' => now()->toISOString()
            ];

            $filename = 'vintapp_colors_' . now()->format('Y-m-d_H-i-s') . '.json';

            return response()->json($config)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Content-Type', 'application/json');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'export : ' . $e->getMessage());
        }
    }

    /**
     * Importer la configuration des couleurs
     */
    public function import(Request $request)
    {
        $request->validate([
            'config_file' => 'required|file|mimes:json|max:1024'
        ]);

        try {
            $content = File::get($request->file('config_file')->getPathname());
            $config = json_decode($content, true);

            if (!$config || !isset($config['palettes'])) {
                return redirect()->back()->with('error', 'Fichier de configuration invalide.');
            }

            // Valider et importer les palettes personnalisées
            if (isset($config['custom_palettes']) && is_array($config['custom_palettes'])) {
                $existingCustom = Cache::get('vintapp_custom_palettes', []);
                $importedCustom = array_merge($existingCustom, $config['custom_palettes']);
                Cache::put('vintapp_custom_palettes', $importedCustom, now()->addDays(365));
            }

            // Appliquer les paramètres
            if (isset($config['active_palette'])) {
                $this->colorService->setPalette($config['active_palette']);
            }

            if (isset($config['dark_mode'])) {
                Cache::put('vintapp_dark_mode', $config['dark_mode'], now()->addDays(30));
            }

            if (isset($config['auto_dark_mode'])) {
                Cache::put('vintapp_auto_dark_mode', $config['auto_dark_mode'], now()->addDays(30));
            }

            $this->generateCSSFile();

            return redirect()->back()->with('success', 'Configuration importée avec succès !');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'import : ' . $e->getMessage());
        }
    }

    /**
     * Générer le fichier CSS pour les couleurs
     */
    protected function generateCSSFile()
    {
        $css = $this->colorService->generateFullCSS();
        $publicPath = public_path('css');
        
        if (!File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0755, true);
        }

        File::put($publicPath . '/vintapp-colors.css', $css);
    }

    /**
     * Générer le CSS pour l'aperçu
     */
    protected function generatePreviewCSS(array $palette): string
    {
        $css = ":root {\n";
        
        foreach ($palette as $colorName => $colorValue) {
            if ($colorName !== 'name') {
                $css .= "  --color-{$colorName}: {$colorValue} !important;\n";
            }
        }
        
        $css .= "}\n";
        
        return $css;
    }
}