<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Services\StorageSyncService;
use Illuminate\Support\Facades\Storage;

class UpdateCategoryImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'categories:update-images {--force : Update even if image already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing categories with placeholder images';

    /**
     * Category images mapping
     *
     * @var array
     */
    protected $categoryImages = [
        'electronique' => 'categories/electronique.svg',
        'vetements' => 'categories/vetements.svg',
        'livres' => 'categories/livres.svg',
        'sport' => 'categories/sport.svg',
        'maison' => 'categories/maison.svg',
        'automobile' => 'categories/automobile.svg',
        'jouets' => 'categories/jouets.svg',
        'informatique' => 'categories/informatique.svg',
        'beaute' => 'categories/beaute.svg',
        'musique' => 'categories/musique.svg',
        'jardinage' => 'categories/jardinage.svg',
        'collection' => 'categories/collection.svg',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $categories = Category::all();
        $updated = 0;
        $skipped = 0;

        $this->info("Found " . $categories->count() . " categories");
        $this->newLine();

        foreach ($categories as $category) {
            if (isset($this->categoryImages[$category->slug])) {
                $imagePath = $this->categoryImages[$category->slug];
                
                // Skip if image already exists and not forced
                if ($category->image && !$this->option('force')) {
                    $this->line("  <comment>⏭</comment> {$category->name} - already has image: {$category->image}");
                    $skipped++;
                    continue;
                }

                // Check if image file exists in storage
                $fullPath = storage_path('app/public/' . $imagePath);
                if (!file_exists($fullPath)) {
                    $this->error("  <error>✗</error> {$category->name} - image file not found: {$imagePath}");
                    continue;
                }

                // Update category
                $category->update(['image' => $imagePath]);
                
                // Sync to public storage
                StorageSyncService::syncFile($imagePath);
                
                $this->info("  <info>✓</info> {$category->name} - image set to: {$imagePath}");
                $updated++;
            } else {
                $this->line("  <comment>⏭</comment> {$category->name} - no image mapping for slug: {$category->slug}");
                $skipped++;
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->info("  Updated: {$updated}");
        $this->info("  Skipped: {$skipped}");

        return 0;
    }
}
