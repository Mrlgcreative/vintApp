<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CSSInjectionService;
use App\Services\ColorPaletteService;

class GenerateCustomCSS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'css:generate {--force : Forcer la régénération même si le cache existe}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Générer le fichier CSS personnalisé avec les couleurs actives';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cssService = app(CSSInjectionService::class);
        $colorService = app(ColorPaletteService::class);

        $this->info("=== Génération CSS Personnalisé VintApp ===");
        $this->newLine();

        // Vider le cache si forcé
        if ($this->option('force')) {
            $this->info("🧹 Vidage du cache CSS...");
            $cssService->clearCache();
        }

        // Afficher la palette active
        $activePalette = $colorService->getActivePaletteName();
        $this->info("🎨 Palette active: {$activePalette}");

        // Générer le CSS
        $this->info("⚙️  Génération du CSS personnalisé...");
        
        try {
            $filename = $cssService->saveCustomCSS();
            $filepath = public_path('css/' . $filename);
            $filesize = round(filesize($filepath) / 1024, 2);
            
            $this->line("✅ CSS généré avec succès!");
            $this->line("   📁 Fichier: public/css/{$filename}");
            $this->line("   📊 Taille: {$filesize} KB");
            $this->line("   🔗 URL: " . asset('css/' . $filename));
            
            // Afficher un aperçu des couleurs
            $this->newLine();
            $this->info("🎨 Couleurs dans la palette active:");
            $activeColors = $colorService->getAllColors();
            foreach ($activeColors as $colorName => $colorValue) {
                if ($colorName !== 'name') {
                    $this->line("   {$colorName}: {$colorValue}");
                }
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Erreur lors de la génération: " . $e->getMessage());
            return 1;
        }

        $this->newLine();
        $this->info("✨ Génération terminée!");
        
        return 0;
    }
}