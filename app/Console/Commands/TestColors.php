<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ColorPaletteService;

class TestColors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'colors:test {--palette=default : Nom de la palette à tester}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tester le système de couleurs VintApp';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $colorService = app(ColorPaletteService::class);
        $paletteName = $this->option('palette');

        $this->info("=== Test du Système de Couleurs VintApp ===");
        $this->newLine();

        // Test 1: Configuration chargée
        $this->info("1. Vérification de la configuration...");
        $config = config('colors');
        if ($config && isset($config['palettes'])) {
            $this->line("✅ Configuration chargée avec " . count($config['palettes']) . " palettes");
            foreach ($config['palettes'] as $name => $palette) {
                $this->line("   - {$name}: {$palette['name']}");
            }
        } else {
            $this->error("❌ Configuration des couleurs non trouvée");
            return 1;
        }

        $this->newLine();

        // Test 2: Palette spécifique
        $this->info("2. Test de la palette '{$paletteName}'...");
        if ($colorService->paletteExists($paletteName)) {
            $this->line("✅ Palette '{$paletteName}' trouvée");
            $palette = $colorService->getPalette($paletteName);
            foreach ($palette as $colorName => $colorValue) {
                if ($colorName !== 'name') {
                    $this->line("   {$colorName}: {$colorValue}");
                }
            }
        } else {
            $this->error("❌ Palette '{$paletteName}' introuvable");
        }

        $this->newLine();

        // Test 3: Palette active
        $this->info("3. Palette actuellement active...");
        $activePalette = $colorService->getActivePaletteName();
        $this->line("🎨 Palette active: {$activePalette}");

        $this->newLine();

        // Test 4: Génération CSS
        $this->info("4. Génération des variables CSS...");
        try {
            $css = $colorService->generateActivePaletteCSS();
            $this->line("✅ CSS généré avec succès (" . strlen($css) . " caractères)");
            
            if ($this->option('verbose')) {
                $this->newLine();
                $this->line("Aperçu du CSS généré:");
                $this->line(substr($css, 0, 500) . "...");
            }
        } catch (\Exception $e) {
            $this->error("❌ Erreur lors de la génération CSS: " . $e->getMessage());
        }

        $this->newLine();

        // Test 5: Changement de palette
        if ($paletteName !== $activePalette) {
            $this->info("5. Test de changement de palette...");
            if ($this->confirm("Voulez-vous changer la palette active vers '{$paletteName}'?")) {
                if ($colorService->setActivePalette($paletteName)) {
                    $this->line("✅ Palette changée avec succès vers '{$paletteName}'");
                } else {
                    $this->error("❌ Échec du changement de palette");
                }
            }
        }

        $this->newLine();
        $this->info("=== Test terminé ===");
        
        return 0;
    }
}