<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;

class TestImageDisplay extends Command
{
    protected $signature = 'test:images';
    protected $description = 'Test image display for boosted items';

    public function handle()
    {
        $this->info('=== TEST D\'AFFICHAGE DES IMAGES ===');
        $this->newLine();

        // Récupérer les articles boostés
        $boostedItems = Item::whereHas('activeBoosts')
            ->with(['activeBoosts.boostType', 'category'])
            ->where('status', 'active')
            ->take(2)
            ->get();

        $this->info('Articles boostés trouvés : ' . $boostedItems->count());
        $this->newLine();

        foreach($boostedItems as $item) {
            $this->line("--- Article: {$item->name} ---");
            $this->line("ID: {$item->id}");
            
            $images = $item->images ?? [];
            $firstImage = count($images) > 0 ? $images[0] : null;
            
            $this->line("Images JSON: " . json_encode($images));
            $this->line("Première image: " . ($firstImage ?? 'AUCUNE'));
            
            if($firstImage) {
                $exists = Storage::disk('public')->exists($firstImage);
                $url = Storage::url($firstImage);
                $this->line("Fichier existe: " . ($exists ? 'OUI' : 'NON'));
                $this->line("URL: {$url}");
                
                if($exists) {
                    $size = Storage::disk('public')->size($firstImage);
                    $this->line("Taille: " . number_format($size / 1024, 2) . " KB");
                }
            }
            
            $activeBoost = $item->activeBoosts->first();
            $this->line("Boost actif: " . ($activeBoost ? 'OUI' : 'NON'));
            if($activeBoost) {
                $this->line("Type: " . ($activeBoost->boostType->name ?? 'N/A'));
            }
            
            $this->newLine();
        }

        $this->info('=== TEST TERMINÉ ===');
        return 0;
    }
}