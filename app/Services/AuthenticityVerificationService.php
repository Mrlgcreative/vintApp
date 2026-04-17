<?php

namespace App\Services;

use App\Models\Item;
use App\Models\User;
use App\Models\ProductAuthenticityCheck;
use App\Models\VerificationImage;
use App\Models\AuthenticityAuditLog;
use App\Models\ExpertProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthenticityVerificationService
{
    /**
     * Soumettre un item pour vérification d'authenticité
     */
    public function submitForVerification(Item $item, array $evidence, array $images): ProductAuthenticityCheck
    {
        DB::beginTransaction();
        
        try {
            // Créer la demande de vérification
            $check = ProductAuthenticityCheck::create([
                'item_id' => $item->id,
                'user_id' => $item->user_id,
                'status' => ProductAuthenticityCheck::STATUS_PENDING,
                'verification_evidence' => $evidence,
                'verification_fee' => $this->calculateVerificationFee($item),
                'submitted_at' => now(),
            ]);

            // Sauvegarder les images de vérification
            $this->saveVerificationImages($check, $images);

            // Marquer l'item comme ayant demandé la vérification
            $item->update(['authenticity_requested' => true]);

            // Log de l'audit
            $this->logAction($check, AuthenticityAuditLog::ACTION_SUBMITTED, $item->user_id);

            DB::commit();

            Log::info('Authenticity verification submitted', [
                'item_id' => $item->id,
                'check_id' => $check->id,
                'user_id' => $item->user_id
            ]);

            return $check;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to submit authenticity verification', [
                'item_id' => $item->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Calculer les frais de vérification
     */
    protected function calculateVerificationFee(Item $item): float
    {
        // Frais de base en USD
        $baseFee = 5.00; // 5 USD au lieu de 10 FC

        // Frais selon la catégorie
        $categoryMultipliers = [
            'mode_luxe' => 1.5,       // 7.50 USD
            'bijoux' => 2.0,          // 10.00 USD
            'montres' => 2.0,         // 10.00 USD
            'electronique' => 1.2,    // 6.00 USD
            'sacs_maroquinerie' => 1.3, // 6.50 USD
        ];

        $categorySlug = $item->category->slug ?? 'general';
        $multiplier = $categoryMultipliers[$categorySlug] ?? 1.0;

        return $baseFee * $multiplier;
    }

    /**
     * Sauvegarder les images de vérification
     */
    protected function saveVerificationImages(ProductAuthenticityCheck $check, array $images): void
    {
        foreach ($images as $imageData) {
            /** @var UploadedFile $file */
            $file = $imageData['file'];
            $type = $imageData['type'];

            // Générer un nom de fichier unique avec extension validée côté serveur
            $allowedMimes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            $mime = $file->getMimeType();
            $ext = $allowedMimes[$mime] ?? null;
            if (!$ext) {
                throw new \InvalidArgumentException("Type de fichier non autorisé: {$mime}");
            }
            $filename = 'verification_' . $check->id . '_' . $type . '_' . time() . '.' . $ext;
            
            // Sauvegarder le fichier
            $path = $file->storeAs('verification_images', $filename, 'public');

            // Créer l'enregistrement en base
            VerificationImage::create([
                'authenticity_check_id' => $check->id,
                'image_path' => $path,
                'image_type' => $type,
                'image_quality_score' => $this->assessImageQuality($file),
            ]);
        }
    }

    /**
     * Évaluer la qualité d'une image
     */
    protected function assessImageQuality(UploadedFile $file): int
    {
        // Vérifications basiques de qualité
        $score = 50; // Score de base

        // Taille du fichier (plus c'est gros, mieux c'est)
        $sizeMB = $file->getSize() / (1024 * 1024);
        if ($sizeMB > 2) $score += 20;
        elseif ($sizeMB > 1) $score += 10;

        // Type de fichier
        $mimeType = $file->getMimeType();
        if (in_array($mimeType, ['image/jpeg', 'image/png'])) {
            $score += 15;
        }

        // Extensions acceptées
        $extension = strtolower($file->getClientOriginalExtension());
        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            $score += 15;
        }

        return min(100, $score);
    }

    /**
     * Analyser avec IA (placeholder pour intégration future)
     */
    public function analyzeWithAI(ProductAuthenticityCheck $check): void
    {
        try {
            $this->logAction($check, AuthenticityAuditLog::ACTION_AI_ANALYSIS_STARTED);

            // TODO: Intégration avec API IA
            // Pour l'instant, simulation basique
            $score = rand(60, 95); // Score aléatoire pour test
            
            $analysis = [
                'confidence_score' => $score,
                'detected_features' => [
                    'brand_logo_detected' => true,
                    'serial_number_readable' => rand(0, 1) ? true : false,
                    'material_quality' => rand(70, 95),
                    'craftsmanship_score' => rand(75, 95),
                ],
                'risk_indicators' => [],
                'recommendation' => $score > 85 ? 'approve' : 'expert_review'
            ];

            // Mettre à jour la vérification
            $check->update([
                'ai_confidence_score' => $score,
                'ai_analysis_result' => $analysis,
                'ai_completed_at' => now(),
                'status' => $score > 85 ? ProductAuthenticityCheck::STATUS_AI_APPROVED : ProductAuthenticityCheck::STATUS_EXPERT_REVIEW
            ]);

            $this->logAction($check, AuthenticityAuditLog::ACTION_AI_ANALYSIS_COMPLETED);

            // Si approuvé par IA, finaliser
            if ($score > 85) {
                $this->finalizeVerification($check, true, 'vintapp_verified');
            } else {
                // Assigner à un expert
                $this->assignToExpert($check);
            }

        } catch (\Exception $e) {
            Log::error('AI analysis failed', [
                'check_id' => $check->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Assigner à un expert
     */
    public function assignToExpert(ProductAuthenticityCheck $check): void
    {
        // Trouver un expert spécialisé disponible
        $categorySlug = $check->item->category->slug ?? 'general';
        
        $expert = ExpertProfile::where('is_active', true)
            ->whereJsonContains('specialties', $categorySlug)
            ->whereHas('user')
            ->with('user')
            ->inRandomOrder()
            ->first();

        if (!$expert) {
            // Fallback: expert généraliste
            $expert = ExpertProfile::where('is_active', true)
                ->whereHas('user')
                ->with('user')
                ->inRandomOrder()
                ->first();
        }

        if ($expert) {
            $check->update([
                'expert_id' => $expert->user_id,
                'expert_assigned_at' => now(),
                'status' => ProductAuthenticityCheck::STATUS_EXPERT_REVIEW
            ]);

            $this->logAction($check, AuthenticityAuditLog::ACTION_EXPERT_ASSIGNED, null, [
                'expert_id' => $expert->user_id,
                'expert_name' => $expert->user->name,
                'specialties' => $expert->specialties
            ]);

            // TODO: Notifier l'expert
            Log::info('Expert assigned to authenticity check', [
                'check_id' => $check->id,
                'expert_id' => $expert->user_id
            ]);
        }
    }

    /**
     * Finaliser la vérification
     */
    public function finalizeVerification(ProductAuthenticityCheck $check, bool $approved, string $badgeType = null): void
    {
        $status = $approved ? ProductAuthenticityCheck::STATUS_EXPERT_APPROVED : ProductAuthenticityCheck::STATUS_EXPERT_REJECTED;
        
        $check->update([
            'status' => $status,
            'final_decision_at' => now()
        ]);

        if ($approved) {
            $check->item->update([
                'authenticity_verified' => true,
                'authenticity_verified_at' => now(),
                'authenticity_badge_type' => $badgeType ?? 'expert_certified'
            ]);
        }

        $this->logAction($check, AuthenticityAuditLog::ACTION_FINAL_DECISION_MADE, null, [
            'approved' => $approved,
            'badge_type' => $badgeType
        ]);

        Log::info('Authenticity verification finalized', [
            'check_id' => $check->id,
            'approved' => $approved,
            'badge_type' => $badgeType
        ]);
    }

    /**
     * Logger une action dans l'audit trail
     */
    protected function logAction(ProductAuthenticityCheck $check, string $action, int $performedBy = null, array $details = []): void
    {
        AuthenticityAuditLog::create([
            'authenticity_check_id' => $check->id,
            'action' => $action,
            'performed_by' => $performedBy,
            'details' => $details
        ]);
    }

    /**
     * Obtenir les statistiques de vérification
     */
    public function getVerificationStats(): array
    {
        return [
            'total_submitted' => ProductAuthenticityCheck::count(),
            'pending' => ProductAuthenticityCheck::where('status', ProductAuthenticityCheck::STATUS_PENDING)->count(),
            'ai_approved' => ProductAuthenticityCheck::where('status', ProductAuthenticityCheck::STATUS_AI_APPROVED)->count(),
            'expert_approved' => ProductAuthenticityCheck::where('status', ProductAuthenticityCheck::STATUS_EXPERT_APPROVED)->count(),
            'rejected' => ProductAuthenticityCheck::whereIn('status', [
                ProductAuthenticityCheck::STATUS_AI_REJECTED,
                ProductAuthenticityCheck::STATUS_EXPERT_REJECTED
            ])->count(),
            'success_rate' => $this->calculateSuccessRate(),
        ];
    }

    /**
     * Calculer le taux de succès
     */
    protected function calculateSuccessRate(): float
    {
        $total = ProductAuthenticityCheck::whereNotIn('status', [ProductAuthenticityCheck::STATUS_PENDING])->count();
        
        if ($total === 0) return 0;

        $approved = ProductAuthenticityCheck::whereIn('status', [
            ProductAuthenticityCheck::STATUS_AI_APPROVED,
            ProductAuthenticityCheck::STATUS_EXPERT_APPROVED
        ])->count();

        return round(($approved / $total) * 100, 2);
    }
}