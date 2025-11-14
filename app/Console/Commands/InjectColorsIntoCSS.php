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
        
        // Lire le fichier app.css actuel
        $appCssPath = resource_path('css/app.css');
        $currentContent = File::get($appCssPath);
        
        // Remplacer la section :root avec les nouvelles variables
        $pattern = '/(:root\s*\{[^}]*\})/s';
        $newContent = preg_replace($pattern, $cssVariables, $currentContent);
        
        // Si aucun remplacement n'a été fait, ajouter les variables au début
        if ($newContent === $currentContent) {
            $newContent = $cssVariables . "\n\n" . $currentContent;
        }
        
        // Sauvegarder le fichier modifié
        File::put($appCssPath, $newContent);
        $this->line('✅ Variables CSS injectées dans app.css');
        
        // Optionnel : Recompiler Tailwind
        if ($this->option('build')) {
            $this->info('🔨 Recompilation de Tailwind CSS...');
            $result = $this->call('build:assets');
            
            if ($result === 0) {
                $this->line('✅ CSS recompilé avec succès');
            } else {
                $this->error('❌ Erreur lors de la recompilation CSS');
            }
        }
        
        $this->info('🎉 Injection terminée !');
        $this->line('');
        $this->line('Pour voir les changements :');
        $this->line('1. Exécutez : npm run build');
        $this->line('2. Rechargez votre navigateur');
        
        return 0;
    }
}