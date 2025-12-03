<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CSSInjectionService;

class GenerateDynamicCSS extends Command
{
    protected $signature = 'css:generate';
    protected $description = 'Générer le fichier CSS dynamique avec la palette de couleurs active';

    public function handle()
    {
        $this->info('Génération du CSS dynamique...');
        
        $cssService = app(CSSInjectionService::class);
        $filename = $cssService->saveCustomCSS();
        
        $this->info("✅ Fichier CSS généré : public/css/{$filename}");
        
        return Command::SUCCESS;
    }
}
