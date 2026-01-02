<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ColorPaletteService;
use Illuminate\Support\Facades\File;

class InjectColorsIntoCSS extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'colors:inject {--build : Rebuild Tailwind CSS after injection}';

    /**
     * The console command description.
     */
    protected $description = 'Injecter les variables de couleur dans le fichier app.css et recompiler Tailwind';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎨 Injection des couleurs VintApp...');
        
        $colorService = app(ColorPaletteService::class);
        $activePaletteName = $colorService->getActivePaletteName();
        
        $this->line("Palette active : {$activePaletteName}");
        
        // Générer les variables CSS
        $this->info('📝 Génération des variables CSS...');
        $cssVariables = $colorService->generateCSSVariables();
        
        // Écrire directement dans public/css/dynamic-colors.css
        // Ce fichier est chargé séparément et n'est pas affecté par la minification Vite
        $dynamicCssPath = public_path('css/dynamic-colors.css');
        
        // S'assurer que le répertoire existe
        if (!File::isDirectory(dirname($dynamicCssPath))) {
            File::makeDirectory(dirname($dynamicCssPath), 0755, true);
        }
        
        // Créer le contenu du fichier avec un header informatif
        $fileContent = "/* Variables CSS dynamiques VintApp - Générées automatiquement */\n";
        $fileContent .= "/* Palette active: {$activePaletteName} */\n";
        $fileContent .= "/* Dernière mise à jour: " . now()->format('Y-m-d H:i:s') . " */\n\n";
        $fileContent .= $cssVariables;
        
        // Sauvegarder le fichier
        File::put($dynamicCssPath, $fileContent);
        $this->line('✅ Variables CSS écrites dans public/css/dynamic-colors.css');
        
        // Vider le cache des vues pour forcer le rechargement
        $this->call('view:clear');
        $this->line('✅ Cache des vues vidé');
        
        $this->info('🎉 Injection terminée !');
        $this->line('');
        $this->line('Les couleurs sont maintenant actives.');
        $this->line('Le fichier dynamic-colors.css est chargé directement par le navigateur.');
        
        return 0;
    }
}