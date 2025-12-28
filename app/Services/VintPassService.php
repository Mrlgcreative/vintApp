<?php

namespace App\Services;

use App\Models\Item;
use App\Models\User;
use App\Models\VintPass;
use App\Models\ProductAuthenticityCheck;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class VintPassService
{
    /**
     * Créer un VintPass pour un article vérifié
     */
    public function createVintPass(
        Item $item, 
        ?ProductAuthenticityCheck $authenticityCheck = null,
        ?User $expert = null
    ): VintPass {
        DB::beginTransaction();

        try {
            // Calculer les scores
            $aiScore = $authenticityCheck?->ai_confidence_score ?? $item->verification_score ?? 0;
            $expertScore = $authenticityCheck ? $this->calculateExpertScore($authenticityCheck) : null;
            $finalScore = $this->calculateFinalScore($aiScore, $expertScore);

            // Créer le snapshot de l'article
            $itemSnapshot = $this->createItemSnapshot($item);

            // Créer le VintPass
            $vintPass = VintPass::create([
                'item_id' => $item->id,
                'current_owner_id' => $item->user_id,
                'verified_by_expert_id' => $expert?->id ?? $authenticityCheck?->expert_id,
                'authenticity_check_id' => $authenticityCheck?->id,
                'ai_score' => $aiScore,
                'expert_score' => $expertScore,
                'final_score' => $finalScore,
                'item_snapshot' => $itemSnapshot,
                'verification_evidence' => $authenticityCheck?->verification_evidence,
                'ownership_history' => [
                    [
                        'user_id' => $item->user_id,
                        'user_name' => $item->user->name,
                        'date' => now()->toDateTimeString(),
                        'transaction_type' => 'original_owner',
                    ]
                ],
                'estimated_value' => $item->price,
                'currency' => $item->currency ?? 'USD',
                'status' => VintPass::STATUS_PENDING,
            ]);

            // Générer le QR Code
            $qrCodePath = $this->generateQRCode($vintPass);
            $vintPass->update(['qr_code_path' => $qrCodePath]);

            // Activer le VintPass
            $vintPass->activate();

            // Mettre à jour l'article
            $item->update([
                'authenticity_verified' => true,
                'authenticity_verified_at' => now(),
                'authenticity_badge_type' => $this->getBadgeType($finalScore),
            ]);

            DB::commit();

            Log::info('VintPass created successfully', [
                'vint_pass_id' => $vintPass->id,
                'pass_id' => $vintPass->pass_id,
                'item_id' => $item->id,
                'final_score' => $finalScore,
            ]);

            return $vintPass;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create VintPass', [
                'item_id' => $item->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Générer le QR Code pour un VintPass
     */
    public function generateQRCode(VintPass $vintPass): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrContent = $vintPass->public_url;

        // Générer le SVG
        $svg = $writer->writeString($qrContent);

        // Sauvegarder le fichier
        $filename = 'vintpass/qr/' . $vintPass->short_code . '.svg';
        Storage::disk('public')->put($filename, $svg);

        return $filename;
    }

    /**
     * Créer un snapshot de l'article au moment de la vérification
     */
    protected function createItemSnapshot(Item $item): array
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'description' => $item->description,
            'price' => $item->price,
            'currency' => $item->currency,
            'condition' => $item->condition,
            'brand' => $item->brand?->name,
            'brand_id' => $item->brand_id,
            'category' => $item->category?->name,
            'category_id' => $item->category_id,
            'image' => $item->first_image_url,
            'images' => $item->images,
            'color' => $item->color,
            'size' => $item->size,
            'specifications' => $item->specifications,
            'snapshot_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Calculer le score expert
     */
    protected function calculateExpertScore(?ProductAuthenticityCheck $check): ?float
    {
        if (!$check || !$check->expert_id) {
            return null;
        }

        // Si l'expert a approuvé
        if ($check->status === ProductAuthenticityCheck::STATUS_EXPERT_APPROVED) {
            return 100.0;
        }

        // Si l'expert a rejeté
        if ($check->status === ProductAuthenticityCheck::STATUS_EXPERT_REJECTED) {
            return 0.0;
        }

        return null;
    }

    /**
     * Calculer le score final combiné
     */
    protected function calculateFinalScore(?float $aiScore, ?float $expertScore): float
    {
        // Si on a les deux scores, pondérer
        if ($aiScore !== null && $expertScore !== null) {
            // Expert a plus de poids (60%) que l'IA (40%)
            return ($aiScore * 0.4) + ($expertScore * 0.6);
        }

        // Si on a seulement le score IA
        if ($aiScore !== null) {
            return $aiScore;
        }

        // Si on a seulement le score expert
        if ($expertScore !== null) {
            return $expertScore;
        }

        return 0.0;
    }

    /**
     * Déterminer le type de badge basé sur le score
     */
    protected function getBadgeType(float $score): string
    {
        if ($score >= 95) return 'platinum';
        if ($score >= 85) return 'gold';
        if ($score >= 75) return 'silver';
        if ($score >= 60) return 'bronze';
        return 'basic';
    }

    /**
     * Vérifier un VintPass par son code court
     */
    public function verifyByShortCode(string $shortCode): ?array
    {
        $vintPass = VintPass::where('short_code', $shortCode)
            ->with(['item', 'currentOwner', 'verifiedByExpert'])
            ->first();

        if (!$vintPass) {
            return null;
        }

        return [
            'valid' => $vintPass->isValid(),
            'vint_pass' => $vintPass->getPublicData(),
        ];
    }

    /**
     * Transférer un VintPass lors d'une vente
     */
    public function transferOnSale(VintPass $vintPass, User $newOwner, $order = null): bool
    {
        try {
            $vintPass->transferTo($newOwner, $order, $order?->total_amount);
            
            Log::info('VintPass transferred', [
                'vint_pass_id' => $vintPass->id,
                'from_user_id' => $vintPass->getOriginal('current_owner_id'),
                'to_user_id' => $newOwner->id,
                'order_id' => $order?->id,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to transfer VintPass', [
                'vint_pass_id' => $vintPass->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Obtenir les statistiques des VintPass
     */
    public function getStatistics(): array
    {
        return [
            'total' => VintPass::count(),
            'active' => VintPass::where('status', VintPass::STATUS_ACTIVE)->count(),
            'pending' => VintPass::where('status', VintPass::STATUS_PENDING)->count(),
            'suspended' => VintPass::where('status', VintPass::STATUS_SUSPENDED)->count(),
            'revoked' => VintPass::where('status', VintPass::STATUS_REVOKED)->count(),
            'total_scans' => VintPass::sum('scan_count'),
            'total_transfers' => VintPass::sum('transfer_count'),
            'avg_score' => VintPass::avg('final_score'),
            'by_level' => [
                'platinum' => VintPass::where('final_score', '>=', 95)->count(),
                'gold' => VintPass::whereBetween('final_score', [85, 94.99])->count(),
                'silver' => VintPass::whereBetween('final_score', [75, 84.99])->count(),
                'bronze' => VintPass::whereBetween('final_score', [60, 74.99])->count(),
                'basic' => VintPass::where('final_score', '<', 60)->count(),
            ],
        ];
    }
}
