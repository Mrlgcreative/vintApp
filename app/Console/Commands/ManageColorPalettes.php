<?php

namespace App\Console\Commands;

use App\Helpers\ColorSystemHelper;
use Illuminate\Console\Command;

class ManageColorPalettes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'colors:manage {action?} {palette?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gérer les palettes de couleurs VintApp';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        if (!$action) {
            return $this->showMenu();
        }

        switch ($action) {
            case 'list':
                return $this->listPalettes();
            
            case 'current':
                return $this->showCurrentPalette();
            
            case 'set':
                $palette = $this->argument('palette');
                return $this->setPalette($palette);
            
            case 'refresh':
                return $this->refreshSystem();
            
            default:
                $this->error("Action '{$action}' non reconnue.");
                return $this->showMenu();
        }
    }

    /**
     * Affiche le menu principal
     */
    private function showMenu()
    {
        $this->info('=== Gestionnaire de Palettes VintApp ===');
        $this->line('');
        $this->line('Actions disponibles :');
        $this->line('  <comment>php artisan colors:manage list</comment>      - Lister les palettes disponibles');
        $this->line('  <comment>php artisan colors:manage current</comment>   - Voir la palette actuelle');
        $this->line('  <comment>php artisan colors:manage set [palette]</comment> - Changer de palette');
        $this->line('  <comment>php artisan colors:manage refresh</comment>   - Actualiser le système');
        $this->line('');
        $this->line('Exemples :');
        $this->line('  <comment>php artisan colors:manage set luxury</comment>');
        $this->line('  <comment>php artisan colors:manage set modern</comment>');
        $this->line('  <comment>php artisan colors:manage set elegant</comment>');
    }

    /**
     * Liste toutes les palettes disponibles
     */
    private function listPalettes()
    {
        $palettes = ColorSystemHelper::getAvailablePalettes();
        $current = ColorSystemHelper::getActivePalette();

        $this->info('=== Palettes Disponibles ===');
        $this->line('');

        foreach ($palettes as $palette) {
            $marker = $palette === $current ? ' <info>(ACTIVE)</info>' : '';
            $this->line("  • <comment>{$palette}</comment>{$marker}");
        }

        $this->line('');
    }

    /**
     * Affiche la palette actuelle
     */
    private function showCurrentPalette()
    {
        $current = ColorSystemHelper::getActivePalette();
        $settingsPath = storage_path('framework/cache/color_palette.json');

        $this->info("=== Palette Actuelle ===");
        $this->line('');
        $this->line("  Palette active : <comment>{$current}</comment>");

        if (file_exists($settingsPath)) {
            $settings = json_decode(file_get_contents($settingsPath), true);
            $this->line("  Dernière mise à jour : <comment>{$settings['updated_at']}</comment>");
            $status = $settings['css_updated'] ? '<info>OK</info>' : '<error>ERREUR</error>';
            $this->line("  Statut CSS : {$status}");
        }

        $this->line('');
    }

    /**
     * Change la palette de couleurs
     */
    private function setPalette($palette)
    {
        if (!$palette) {
            $palettes = ColorSystemHelper::getAvailablePalettes();
            $palette = $this->choice('Choisissez une palette :', $palettes);
        }

        $this->info("Activation de la palette '{$palette}'...");
        $this->line('');

        $result = ColorSystemHelper::activatePalette($palette);

        if ($result) {
            $this->info("✅ Palette '{$palette}' activée avec succès !");
            $this->line('');
            $this->line('Le système de couleurs a été mis à jour automatiquement.');
            $this->line('Les changements sont effectifs immédiatement.');
        } else {
            $this->error("❌ Erreur lors de l'activation de la palette '{$palette}'.");
            $this->line('');
            $this->line('Vérifiez les permissions sur le fichier resources/css/app.css');
        }
    }

    /**
     * Actualise le système de couleurs
     */
    private function refreshSystem()
    {
        $this->info('Actualisation du système de couleurs...');
        $this->line('');

        $result = ColorSystemHelper::autoInitialize();

        if ($result) {
            $this->info('✅ Système de couleurs actualisé avec succès !');
        } else {
            $this->error('❌ Erreur lors de l\'actualisation du système.');
        }
    }
}