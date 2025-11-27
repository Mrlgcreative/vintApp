<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

/**
 * Service d'optimisation d'images pour mobile
 * 
 * Conversion WebP, compression, responsive images
 */
class ImageOptimizationService
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Convertir une image en WebP
     */
    public function convertToWebP(string $imagePath, int $quality = 80): ?string
    {
        try {
            $image = $this->manager->read($imagePath);
            
            // Générer le nouveau chemin WebP
            $pathInfo = pathinfo($imagePath);
            $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
            
            // Encoder en WebP
            $encoded = $image->toWebp($quality);
            
            // Sauvegarder
            Storage::put($webpPath, $encoded);
            
            return $webpPath;
        } catch (\Exception $e) {
            \Log::error('WebP conversion failed', [
                'image' => $imagePath,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Créer des versions responsive d'une image
     */
    public function createResponsiveVersions(string $imagePath): array
    {
        $sizes = [
            'small' => 320,   // Mobile portrait
            'medium' => 768,  // Tablet
            'large' => 1024,  // Desktop
            'xlarge' => 1920, // Full HD
        ];

        $versions = [];

        foreach ($sizes as $name => $width) {
            try {
                $image = $this->manager->read($imagePath);
                
                // Redimensionner
                $image->scale(width: $width);
                
                // Générer le chemin
                $pathInfo = pathinfo($imagePath);
                $resizedPath = $pathInfo['dirname'] . '/' . 
                               $pathInfo['filename'] . "_{$name}." . 
                               $pathInfo['extension'];
                
                // Sauvegarder
                Storage::put($resizedPath, $image->encode());
                
                $versions[$name] = $resizedPath;
            } catch (\Exception $e) {
                \Log::error("Responsive image creation failed for {$name}", [
                    'image' => $imagePath,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $versions;
    }

    /**
     * Optimiser une image (compression sans perte de qualité visible)
     */
    public function optimize(string $imagePath, int $quality = 85): bool
    {
        try {
            $image = $this->manager->read($imagePath);
            
            // Encoder avec qualité optimisée
            $encoded = $image->encode(quality: $quality);
            
            // Remplacer l'original
            Storage::put($imagePath, $encoded);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Image optimization failed', [
                'image' => $imagePath,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Générer un placeholder blur (LQIP - Low Quality Image Placeholder)
     */
    public function generateBlurPlaceholder(string $imagePath): ?string
    {
        try {
            $image = $this->manager->read($imagePath);
            
            // Redimensionner à une très petite taille (20px de largeur)
            $image->scale(width: 20);
            
            // Appliquer un flou
            $image->blur(5);
            
            // Encoder en base64
            $encoded = $image->encode();
            $base64 = 'data:image/jpeg;base64,' . base64_encode($encoded);
            
            return $base64;
        } catch (\Exception $e) {
            \Log::error('Blur placeholder generation failed', [
                'image' => $imagePath,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Traiter une image complètement (WebP + responsive + blur)
     */
    public function processImage(string $imagePath): array
    {
        $result = [
            'original' => $imagePath,
            'webp' => null,
            'responsive' => [],
            'placeholder' => null,
        ];

        // Optimiser l'original
        $this->optimize($imagePath);

        // Créer version WebP
        $result['webp'] = $this->convertToWebP($imagePath);

        // Créer versions responsive
        $result['responsive'] = $this->createResponsiveVersions($imagePath);

        // Générer placeholder
        $result['placeholder'] = $this->generateBlurPlaceholder($imagePath);

        return $result;
    }

    /**
     * Obtenir la taille d'une image
     */
    public function getImageSize(string $imagePath): array
    {
        try {
            $image = $this->manager->read($imagePath);
            
            return [
                'width' => $image->width(),
                'height' => $image->height(),
                'mime' => $image->mime(),
            ];
        } catch (\Exception $e) {
            return [
                'width' => 0,
                'height' => 0,
                'mime' => null,
            ];
        }
    }
}
