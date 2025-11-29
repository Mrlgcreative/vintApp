<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;

class ItemVerificationService
{
    /**
     * Score minimum requis pour auto-validation (sur 100)
     */
    const AUTO_APPROVE_THRESHOLD = 75;

    /**
     * Score minimum pour mise en attente (en dessous = rejet automatique)
     */
    const PENDING_THRESHOLD = 50;

    /**
     * Vérifie un article complet (images + description)
     * 
     * @param array $images Chemins des images
     * @param string $name Nom de l'article
     * @param string $description Description de l'article
     * @param string|null $brandName Nom de la marque
     * @param string|null $categoryName Nom de la catégorie
     * @return array ['status' => 'approved|pending|rejected', 'score' => int, 'details' => array]
     */
    public function verifyItem(array $images, string $name, string $description, ?string $brandName = null, ?string $categoryName = null): array
    {
        $verificationDetails = [
            'images' => [],
            'text' => [],
            'coherence' => [],
            'timestamp' => now()->toDateTimeString(),
        ];

        $totalScore = 0;
        $weights = [
            'images' => 0.4,      // 40% du score total
            'text' => 0.3,        // 30% du score total
            'coherence' => 0.3,   // 30% du score total
        ];

        // 1. Vérification des images
        $imageScore = $this->verifyImages($images);
        $verificationDetails['images'] = $imageScore;
        $totalScore += $imageScore['score'] * $weights['images'];

        // 2. Vérification du texte (description + nom)
        $textScore = $this->verifyText($name, $description, $brandName);
        $verificationDetails['text'] = $textScore;
        $totalScore += $textScore['score'] * $weights['text'];

        // 3. Vérification de la cohérence image-texte
        $coherenceScore = $this->verifyCoherence($images, $name, $description, $categoryName);
        $verificationDetails['coherence'] = $coherenceScore;
        $totalScore += $coherenceScore['score'] * $weights['coherence'];

        // Déterminer le statut final
        $status = 'rejected';
        if ($totalScore >= self::AUTO_APPROVE_THRESHOLD) {
            $status = 'approved';
        } elseif ($totalScore >= self::PENDING_THRESHOLD) {
            $status = 'pending';
        }

        return [
            'status' => $status,
            'score' => round($totalScore, 2),
            'details' => $verificationDetails,
        ];
    }

    /**
     * Vérifie la qualité et l'authenticité des images
     */
    private function verifyImages(array $imagePaths): array
    {
        $issues = [];
        $scores = [];
        $totalScore = 0;

        foreach ($imagePaths as $index => $imagePath) {
            $imageIssues = [];
            $imageScore = 100;

            try {
                $fullPath = Storage::disk('public')->path($imagePath);
                
                if (!file_exists($fullPath)) {
                    $imageIssues[] = 'Fichier introuvable';
                    $imageScore = 0;
                    continue;
                }

                // Charger l'image avec Intervention
                $img = Image::make($fullPath);
                $width = $img->width();
                $height = $img->height();

                // 1. Vérifier les dimensions minimales
                if ($width < 400 || $height < 400) {
                    $imageIssues[] = "Résolution trop faible ({$width}x{$height})";
                    $imageScore -= 30;
                }

                // 2. Vérifier la taille du fichier
                $filesize = filesize($fullPath);
                if ($filesize < 20480) { // < 20KB
                    $imageIssues[] = 'Fichier trop petit (possiblement compressé artificiellement)';
                    $imageScore -= 20;
                }

                // 3. Analyser la qualité de l'image
                $qualityAnalysis = $this->analyzeImageQuality($fullPath);
                if ($qualityAnalysis['variance'] < 50) {
                    $imageIssues[] = 'Variance de couleur très faible (image suspecte)';
                    $imageScore -= 25;
                }

                if ($qualityAnalysis['blur_score'] > 70) {
                    $imageIssues[] = 'Image très floue';
                    $imageScore -= 15;
                }

                // 4. Détecter les watermarks suspects
                if ($this->detectWatermark($fullPath)) {
                    $imageIssues[] = 'Watermark ou logo suspect détecté';
                    $imageScore -= 20;
                }

                // 5. Vérifier les métadonnées EXIF
                $exifAnalysis = $this->analyzeExif($fullPath);
                if ($exifAnalysis['modified']) {
                    $imageIssues[] = 'Image probablement retouchée (métadonnées modifiées)';
                    $imageScore -= 15;
                }

                // 6. Détecter les duplications sur Internet (placeholder pour future implémentation)
                // Cette fonctionnalité pourrait utiliser des APIs comme Google Vision, TinEye, etc.
                
                $imageScore = max(0, $imageScore);

            } catch (\Exception $e) {
                Log::error('Erreur vérification image', [
                    'path' => $imagePath,
                    'error' => $e->getMessage()
                ]);
                $imageIssues[] = 'Erreur lors de l\'analyse';
                $imageScore = 0;
            }

            $scores[] = $imageScore;
            if (!empty($imageIssues)) {
                $issues["image_$index"] = [
                    'path' => $imagePath,
                    'score' => $imageScore,
                    'issues' => $imageIssues,
                ];
            }
        }

        $avgScore = empty($scores) ? 0 : array_sum($scores) / count($scores);

        return [
            'score' => round($avgScore, 2),
            'issues' => $issues,
            'images_count' => count($imagePaths),
            'passed' => $avgScore >= 70,
        ];
    }

    /**
     * Analyse la qualité technique d'une image
     */
    private function analyzeImageQuality(string $fullPath): array
    {
        $result = [
            'variance' => 0,
            'blur_score' => 0,
            'brightness' => 0,
        ];

        try {
            $img = Image::make($fullPath);
            $width = $img->width();
            $height = $img->height();

            // Échantillonnage pour performances
            $sampleSize = min(100, min($width, $height));
            $stepX = max(1, floor($width / $sampleSize));
            $stepY = max(1, floor($height / $sampleSize));

            $pixels = [];
            $brightness = [];

            for ($x = 0; $x < $width; $x += $stepX) {
                for ($y = 0; $y < $height; $y += $stepY) {
                    $color = $img->pickColor($x, $y);
                    $gray = 0.299 * $color[0] + 0.587 * $color[1] + 0.114 * $color[2];
                    $pixels[] = $gray;
                    $brightness[] = ($color[0] + $color[1] + $color[2]) / 3;
                }
            }

            if (!empty($pixels)) {
                // Calculer la variance
                $mean = array_sum($pixels) / count($pixels);
                $variance = 0;
                foreach ($pixels as $p) {
                    $variance += pow($p - $mean, 2);
                }
                $variance = sqrt($variance / count($pixels));
                $result['variance'] = $variance;

                // Estimation du flou via gradient
                $gradients = [];
                for ($i = 1; $i < count($pixels); $i++) {
                    $gradients[] = abs($pixels[$i] - $pixels[$i - 1]);
                }
                $avgGradient = array_sum($gradients) / count($gradients);
                $result['blur_score'] = max(0, 100 - ($avgGradient * 2));

                // Luminosité moyenne
                $result['brightness'] = array_sum($brightness) / count($brightness);
            }

        } catch (\Exception $e) {
            Log::warning('Erreur analyse qualité image', ['error' => $e->getMessage()]);
        }

        return $result;
    }

    /**
     * Détecte les watermarks suspects (logos d'autres sites)
     */
    private function detectWatermark(string $fullPath): bool
    {
        // Implémentation basique - peut être améliorée avec OCR
        try {
            $img = Image::make($fullPath);
            
            // Analyser les coins de l'image (où se trouvent généralement les watermarks)
            $corners = [
                ['x' => 10, 'y' => 10], // Haut gauche
                ['x' => $img->width() - 100, 'y' => 10], // Haut droit
                ['x' => 10, 'y' => $img->height() - 50], // Bas gauche
                ['x' => $img->width() - 100, 'y' => $img->height() - 50], // Bas droit
            ];

            foreach ($corners as $corner) {
                // Vérifier si cette zone a des caractéristiques de texte/logo
                // (Simplifié - une vraie implémentation utiliserait OCR)
                $regionVariance = $this->analyzeRegionVariance($img, $corner['x'], $corner['y'], 80, 40);
                
                // Variance élevée dans un coin = possiblement un watermark
                if ($regionVariance > 30 && $regionVariance < 80) {
                    return true;
                }
            }

            return false;

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Analyse la variance d'une région de l'image
     */
    private function analyzeRegionVariance($img, $startX, $startY, $width, $height): float
    {
        $pixels = [];
        $endX = min($startX + $width, $img->width());
        $endY = min($startY + $height, $img->height());

        for ($x = $startX; $x < $endX; $x += 5) {
            for ($y = $startY; $y < $endY; $y += 5) {
                $color = $img->pickColor($x, $y);
                $gray = 0.299 * $color[0] + 0.587 * $color[1] + 0.114 * $color[2];
                $pixels[] = $gray;
            }
        }

        if (count($pixels) < 2) return 0;

        $mean = array_sum($pixels) / count($pixels);
        $variance = 0;
        foreach ($pixels as $p) {
            $variance += pow($p - $mean, 2);
        }
        
        return sqrt($variance / count($pixels));
    }

    /**
     * Analyse les métadonnées EXIF pour détecter les modifications
     */
    private function analyzeExif(string $fullPath): array
    {
        $result = [
            'modified' => false,
            'software' => null,
            'camera' => null,
        ];

        try {
            $exif = @exif_read_data($fullPath);
            
            if ($exif) {
                // Vérifier si modifié avec un logiciel d'édition
                $editingSoftware = ['photoshop', 'gimp', 'paint.net', 'affinity', 'pixlr'];
                
                if (isset($exif['Software'])) {
                    $software = strtolower($exif['Software']);
                    $result['software'] = $exif['Software'];
                    
                    foreach ($editingSoftware as $editor) {
                        if (strpos($software, $editor) !== false) {
                            $result['modified'] = true;
                            break;
                        }
                    }
                }

                // Vérifier incohérences dans les dates
                if (isset($exif['DateTimeOriginal']) && isset($exif['DateTime'])) {
                    $original = strtotime($exif['DateTimeOriginal']);
                    $modified = strtotime($exif['DateTime']);
                    
                    if (abs($original - $modified) > 86400) { // Plus d'1 jour de différence
                        $result['modified'] = true;
                    }
                }

                if (isset($exif['Model'])) {
                    $result['camera'] = $exif['Model'];
                }
            }

        } catch (\Exception $e) {
            // EXIF non disponible - pas forcément un problème
        }

        return $result;
    }

    /**
     * Vérifie le texte (description) pour détecter fraudes et spam
     */
    private function verifyText(string $name, string $description, ?string $brandName): array
    {
        $issues = [];
        $score = 100;

        // 1. Vérifier la longueur minimale
        if (strlen($description) < 50) {
            $issues[] = 'Description trop courte (minimum 50 caractères recommandé)';
            $score -= 15;
        }

        // 2. Détecter spam et répétitions excessives
        $words = str_word_count(strtolower($description), 1);
        $wordCount = array_count_values($words);
        $maxRepetition = max($wordCount);
        $totalWords = count($words);
        
        if ($totalWords > 0 && ($maxRepetition / $totalWords) > 0.3) {
            $issues[] = 'Répétitions excessives détectées (possiblement spam)';
            $score -= 25;
        }

        // 3. Détecter mots suspects (arnaque, contact externe, etc.)
        $suspiciousKeywords = [
            'whatsapp', 'telegram', 'email', '@', 'contactez', 'appeler',
            'garantie 100%', 'argent facile', 'promotion limitée',
            'cliquez ici', 'offre exceptionnelle', 'livraison gratuite',
        ];

        $lowerDescription = strtolower($description . ' ' . $name);
        $foundSuspicious = [];
        
        foreach ($suspiciousKeywords as $keyword) {
            if (strpos($lowerDescription, $keyword) !== false) {
                $foundSuspicious[] = $keyword;
            }
        }

        if (!empty($foundSuspicious)) {
            $issues[] = 'Mots suspects détectés: ' . implode(', ', $foundSuspicious);
            $score -= 20;
        }

        // 4. Vérifier la présence d'URLs externes
        if (preg_match('/https?:\/\/|www\./i', $description)) {
            $issues[] = 'Liens externes détectés dans la description';
            $score -= 30;
        }

        // 5. Vérifier cohérence nom/marque
        if ($brandName && strlen($brandName) > 2) {
            $nameAndDesc = strtolower($name . ' ' . $description);
            if (strpos($nameAndDesc, strtolower($brandName)) === false) {
                $issues[] = 'La marque spécifiée n\'apparaît pas dans le nom ou la description';
                $score -= 10;
            }
        }

        // 6. Détecter CAPS LOCK excessif
        $uppercaseCount = preg_match_all('/[A-Z]/', $description);
        $totalChars = strlen(preg_replace('/[^a-zA-Z]/', '', $description));
        
        if ($totalChars > 0 && ($uppercaseCount / $totalChars) > 0.5) {
            $issues[] = 'Utilisation excessive de majuscules';
            $score -= 15;
        }

        $score = max(0, $score);

        return [
            'score' => $score,
            'issues' => $issues,
            'word_count' => $totalWords,
            'passed' => $score >= 60,
        ];
    }

    /**
     * Vérifie la cohérence entre images et texte
     */
    private function verifyCoherence(array $imagePaths, string $name, string $description, ?string $categoryName): array
    {
        $issues = [];
        $score = 100;

        // 1. Vérifier nombre d'images
        $imageCount = count($imagePaths);
        if ($imageCount < 2) {
            $issues[] = 'Nombre d\'images insuffisant (minimum 2 recommandé)';
            $score -= 20;
        }

        // 2. Vérifier cohérence catégorie/description
        if ($categoryName) {
            $lowerText = strtolower($name . ' ' . $description);
            $lowerCategory = strtolower($categoryName);
            
            // Si la catégorie n'apparaît nulle part dans le texte, c'est suspect
            if (strpos($lowerText, $lowerCategory) === false) {
                // Vérifier des synonymes basiques
                $categoryKeywords = $this->getCategoryKeywords($lowerCategory);
                $foundKeyword = false;
                
                foreach ($categoryKeywords as $keyword) {
                    if (strpos($lowerText, $keyword) !== false) {
                        $foundKeyword = true;
                        break;
                    }
                }
                
                if (!$foundKeyword) {
                    $issues[] = 'Incohérence entre la catégorie et le contenu';
                    $score -= 15;
                }
            }
        }

        // 3. Analyser la diversité des images
        if ($imageCount >= 2) {
            $similarity = $this->compareImageSimilarity($imagePaths);
            if ($similarity > 0.95) {
                $issues[] = 'Images trop similaires (possiblement duplicatas)';
                $score -= 25;
            }
        }

        // 4. Vérifier que les images ne sont pas toutes de la même source
        $sameDimensions = $this->checkSameDimensions($imagePaths);
        if ($sameDimensions && $imageCount > 2) {
            $issues[] = 'Toutes les images ont les mêmes dimensions (possiblement copiées)';
            $score -= 15;
        }

        $score = max(0, $score);

        return [
            'score' => $score,
            'issues' => $issues,
            'images_analyzed' => $imageCount,
            'passed' => $score >= 70,
        ];
    }

    /**
     * Obtient des mots-clés associés à une catégorie
     */
    private function getCategoryKeywords(string $category): array
    {
        $keywords = [
            'vêtements' => ['vêtement', 'habit', 'tenue', 'mode', 'fashion'],
            'chaussures' => ['chaussure', 'basket', 'sneaker', 'sandale', 'botte'],
            'électronique' => ['électronique', 'tech', 'appareil', 'device'],
            'accessoires' => ['accessoire', 'bijou', 'montre', 'sac', 'ceinture'],
        ];

        return $keywords[$category] ?? [$category];
    }

    /**
     * Compare la similarité entre plusieurs images
     */
    private function compareImageSimilarity(array $imagePaths): float
    {
        if (count($imagePaths) < 2) return 0;

        try {
            // Comparer les 2 premières images
            $img1 = Image::make(Storage::disk('public')->path($imagePaths[0]));
            $img2 = Image::make(Storage::disk('public')->path($imagePaths[1]));

            // Redimensionner pour comparaison
            $img1->resize(100, 100);
            $img2->resize(100, 100);

            // Comparer les histogrammes de couleur (simplifié)
            $hash1 = $this->getImageHash($img1);
            $hash2 = $this->getImageHash($img2);

            // Calculer la distance de Hamming
            $similarity = 1 - (count(array_diff_assoc($hash1, $hash2)) / count($hash1));

            return $similarity;

        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Génère un hash perceptuel simple pour une image
     */
    private function getImageHash($img): array
    {
        $hash = [];
        for ($x = 0; $x < 10; $x++) {
            for ($y = 0; $y < 10; $y++) {
                $color = $img->pickColor($x * 10, $y * 10);
                $hash[] = round(($color[0] + $color[1] + $color[2]) / 3 / 25);
            }
        }
        return $hash;
    }

    /**
     * Vérifie si toutes les images ont les mêmes dimensions
     */
    private function checkSameDimensions(array $imagePaths): bool
    {
        if (count($imagePaths) < 2) return false;

        $dimensions = [];
        foreach ($imagePaths as $path) {
            try {
                $img = Image::make(Storage::disk('public')->path($path));
                $dimensions[] = $img->width() . 'x' . $img->height();
            } catch (\Exception $e) {
                continue;
            }
        }

        return count(array_unique($dimensions)) === 1;
    }
}
