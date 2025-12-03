<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class StorageSyncService
{
    /**
     * Copy a file from storage/app/public to public/storage
     */
    public static function syncFile($path)
    {
        try {
            $sourcePath = storage_path('app/public/' . $path);
            $destPath = public_path('storage/' . $path);

            // Skip if source doesn't exist
            if (!File::exists($sourcePath)) {
                return false;
            }

            // Create destination directory if needed
            $destDir = dirname($destPath);
            if (!File::exists($destDir)) {
                File::makeDirectory($destDir, 0755, true);
            }

            // Remove if destination is a symlink
            if (is_link(public_path('storage'))) {
                @unlink(public_path('storage'));
                File::makeDirectory(public_path('storage'), 0755, true);
            }

            // Copy the file
            File::copy($sourcePath, $destPath);

            return true;
        } catch (\Exception $e) {
            Log::error('Storage sync error: ' . $e->getMessage(), [
                'path' => $path,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Sync entire directory
     */
    public static function syncDirectory($directory = '')
    {
        try {
            $source = storage_path('app/public/' . $directory);
            $destination = public_path('storage/' . $directory);

            if (!File::exists($source)) {
                return false;
            }

            // Create destination
            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            // Copy all files
            $files = File::allFiles($source);
            foreach ($files as $file) {
                $relativePath = str_replace($source . '/', '', $file->getPathname());
                self::syncFile($directory . '/' . $relativePath);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Storage directory sync error: ' . $e->getMessage());
            return false;
        }
    }
}
