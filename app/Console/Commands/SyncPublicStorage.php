<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncPublicStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:sync {--force : Force sync even if destination exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync storage/app/public to public/storage (alternative to symlink)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $source = storage_path('app/public');
        $destination = public_path('storage');

        if (!File::exists($source)) {
            $this->error("Source directory does not exist: {$source}");
            return 1;
        }

        // Create destination if it doesn't exist
        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
            $this->info("Created destination directory: {$destination}");
        }

        // Remove destination if it's a symlink
        if (is_link($destination)) {
            @unlink($destination);
            File::makeDirectory($destination, 0755, true);
            $this->info("Removed symlink and created directory");
        }

        $this->info("Syncing files from {$source} to {$destination}...");

        try {
            $this->syncDirectory($source, $destination);
            $this->info("✓ Storage sync completed successfully!");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error syncing storage: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Recursively sync directories
     */
    private function syncDirectory($source, $destination)
    {
        $files = File::allFiles($source);
        $directories = File::directories($source);

        // Create subdirectories
        foreach ($directories as $directory) {
            $relativePath = str_replace($source, '', $directory);
            $destDir = $destination . $relativePath;
            
            if (!File::exists($destDir)) {
                File::makeDirectory($destDir, 0755, true);
                $this->line("  Created directory: {$relativePath}");
            }
        }

        // Copy files
        $copied = 0;
        foreach ($files as $file) {
            $relativePath = str_replace($source, '', $file->getPathname());
            $destFile = $destination . $relativePath;

            // Copy if destination doesn't exist or source is newer
            if (!File::exists($destFile) || 
                $this->option('force') || 
                File::lastModified($file->getPathname()) > File::lastModified($destFile)) {
                
                File::copy($file->getPathname(), $destFile);
                $copied++;
                
                if ($copied <= 10 || $copied % 50 === 0) {
                    $this->line("  Copied: {$relativePath}");
                }
            }
        }

        $this->info("  Total files copied: {$copied}");
    }
}
