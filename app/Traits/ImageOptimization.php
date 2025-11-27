<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

trait ImageOptimization
{
    /**
     * Tailles de thumbnails
     */
    protected $thumbnailSizes = [
        'thumb' => ['width' => 150, 'height' => 150],
        'small' => ['width' => 300, 'height' => 300],
        'medium' => ['width' => 600, 'height' => 600],
        'large' => ['width' => 1200, 'height' => 1200],
    ];

    /**
     * Générer les thumbnails pour une image
     */
    public function generateThumbnails(string $imagePath): array
    {
        $thumbnails = [];
        $fullPath = Storage::disk('public')->path($imagePath);

        if (!file_exists($fullPath)) {
            return [];
        }

        $pathInfo = pathinfo($imagePath);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        $extension = $pathInfo['extension'];

        foreach ($this->thumbnailSizes as $size => $dimensions) {
            $thumbnailName = "{$filename}_{$size}.{$extension}";
            $thumbnailPath = "{$directory}/{$thumbnailName}";
            $thumbnailFullPath = Storage::disk('public')->path($thumbnailPath);

            try {
                // Créer le thumbnail avec Intervention Image si disponible
                if (class_exists('Intervention\Image\Facades\Image')) {
                    $img = Image::make($fullPath);
                    $img->fit($dimensions['width'], $dimensions['height'], function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $img->save($thumbnailFullPath, 80); // Qualité 80%
                } else {
                    // Fallback avec GD si Intervention Image n'est pas installé
                    $this->createThumbnailWithGD(
                        $fullPath,
                        $thumbnailFullPath,
                        $dimensions['width'],
                        $dimensions['height']
                    );
                }

                $thumbnails[$size] = $thumbnailPath;
            } catch (\Exception $e) {
                \Log::error("Erreur génération thumbnail {$size}: " . $e->getMessage());
            }
        }

        return $thumbnails;
    }

    /**
     * Créer un thumbnail avec GD (fallback)
     */
    protected function createThumbnailWithGD(string $source, string $destination, int $width, int $height): bool
    {
        $imageInfo = getimagesize($source);
        if (!$imageInfo) {
            return false;
        }

        $mime = $imageInfo['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($source);
                break;
            case 'image/png':
                $image = imagecreatefrompng($source);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($source);
                break;
            default:
                return false;
        }

        $srcWidth = imagesx($image);
        $srcHeight = imagesy($image);

        // Calculer les nouvelles dimensions en conservant le ratio
        $ratio = min($width / $srcWidth, $height / $srcHeight);
        $newWidth = (int)($srcWidth * $ratio);
        $newHeight = (int)($srcHeight * $ratio);

        $thumbnail = imagecreatetruecolor($newWidth, $newHeight);

        // Préserver la transparence pour PNG et GIF
        if ($mime === 'image/png' || $mime === 'image/gif') {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
        }

        imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);

        switch ($mime) {
            case 'image/jpeg':
                imagejpeg($thumbnail, $destination, 80);
                break;
            case 'image/png':
                imagepng($thumbnail, $destination, 8);
                break;
            case 'image/gif':
                imagegif($thumbnail, $destination);
                break;
        }

        imagedestroy($image);
        imagedestroy($thumbnail);

        return true;
    }

    /**
     * Obtenir l'URL du thumbnail
     */
    public function getThumbnailUrl(string $imagePath, string $size = 'medium'): string
    {
        if (empty($imagePath)) {
            return asset('images/placeholder.png');
        }

        $pathInfo = pathinfo($imagePath);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        $extension = $pathInfo['extension'];

        $thumbnailPath = "{$directory}/{$filename}_{$size}.{$extension}";

        // Vérifier si le thumbnail existe
        if (Storage::disk('public')->exists($thumbnailPath)) {
            return asset('storage/' . $thumbnailPath);
        }

        // Retourner l'image originale si le thumbnail n'existe pas
        return asset('storage/' . $imagePath);
    }

    /**
     * Supprimer tous les thumbnails d'une image
     */
    public function deleteThumbnails(string $imagePath): void
    {
        $pathInfo = pathinfo($imagePath);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        $extension = $pathInfo['extension'];

        foreach (array_keys($this->thumbnailSizes) as $size) {
            $thumbnailPath = "{$directory}/{$filename}_{$size}.{$extension}";
            
            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }
        }
    }

    /**
     * Optimiser une image (réduire la taille du fichier)
     */
    public function optimizeImage(string $imagePath, int $quality = 80): bool
    {
        $fullPath = Storage::disk('public')->path($imagePath);

        if (!file_exists($fullPath)) {
            return false;
        }

        try {
            if (class_exists('Intervention\Image\Facades\Image')) {
                $img = Image::make($fullPath);
                $img->save($fullPath, $quality);
                return true;
            }

            // Fallback GD
            $imageInfo = getimagesize($fullPath);
            $mime = $imageInfo['mime'];

            if ($mime === 'image/jpeg') {
                $image = imagecreatefromjpeg($fullPath);
                imagejpeg($image, $fullPath, $quality);
                imagedestroy($image);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            \Log::error("Erreur optimisation image: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir les attributs lazy loading pour une image
     */
    public function getLazyLoadingAttributes(string $imagePath, string $size = 'medium'): array
    {
        return [
            'data-src' => $this->getThumbnailUrl($imagePath, $size),
            'data-srcset' => $this->generateSrcSet($imagePath),
            'src' => asset('images/placeholder.png'),
            'loading' => 'lazy',
            'class' => 'lazy-load',
        ];
    }

    /**
     * Générer srcset pour responsive images
     */
    protected function generateSrcSet(string $imagePath): string
    {
        $srcset = [];

        foreach ($this->thumbnailSizes as $size => $dimensions) {
            $url = $this->getThumbnailUrl($imagePath, $size);
            $srcset[] = "{$url} {$dimensions['width']}w";
        }

        return implode(', ', $srcset);
    }
}
