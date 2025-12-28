<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class VintPass extends Model
{
    use HasFactory;

    // Statuts
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_REVOKED = 'revoked';

    // Réseaux blockchain supportés
    const NETWORK_POLYGON = 'polygon';
    const NETWORK_ETHEREUM = 'ethereum';

    protected $fillable = [
        'pass_id',
        'item_id',
        'current_owner_id',
        'verified_by_expert_id',
        'authenticity_check_id',
        'ai_score',
        'expert_score',
        'final_score',
        'blockchain_hash',
        'blockchain_network',
        'contract_address',
        'token_id',
        'blockchain_confirmed_at',
        'qr_code_path',
        'verification_url',
        'short_code',
        'item_snapshot',
        'verification_evidence',
        'ownership_history',
        'status',
        'suspension_reason',
        'estimated_value',
        'currency',
        'transfer_count',
        'scan_count',
        'issued_at',
        'last_transferred_at',
        'last_scanned_at',
    ];

    protected $casts = [
        'item_snapshot' => 'array',
        'verification_evidence' => 'array',
        'ownership_history' => 'array',
        'ai_score' => 'decimal:2',
        'expert_score' => 'decimal:2',
        'final_score' => 'decimal:2',
        'estimated_value' => 'decimal:2',
        'blockchain_confirmed_at' => 'datetime',
        'issued_at' => 'datetime',
        'last_transferred_at' => 'datetime',
        'last_scanned_at' => 'datetime',
    ];

    protected $appends = ['public_url', 'status_badge', 'authenticity_level'];

    /**
     * Boot du modèle
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vintPass) {
            // Générer le pass_id unique
            if (empty($vintPass->pass_id)) {
                $vintPass->pass_id = self::generatePassId($vintPass);
            }

            // Générer le short_code pour l'URL
            if (empty($vintPass->short_code)) {
                $vintPass->short_code = self::generateShortCode();
            }

            // Générer l'URL de vérification
            if (empty($vintPass->verification_url)) {
                $vintPass->verification_url = url('/verify/' . $vintPass->short_code);
            }
        });
    }

    /**
     * Générer un ID de pass unique
     */
    public static function generatePassId($vintPass): string
    {
        $year = date('Y');
        $prefix = 'VNT';
        
        // Récupérer le code marque si disponible
        $brandCode = 'XX';
        if ($vintPass->item_id) {
            $item = Item::with('brand')->find($vintPass->item_id);
            if ($item && $item->brand) {
                $brandCode = strtoupper(substr($item->brand->name, 0, 2));
            }
        }

        // Numéro séquentiel
        $lastPass = self::whereYear('created_at', $year)->orderByDesc('id')->first();
        $sequence = $lastPass ? ($lastPass->id + 1) : 1;

        return sprintf('%s-%s-%s-%05d', $prefix, $year, $brandCode, $sequence);
    }

    /**
     * Générer un code court unique
     */
    public static function generateShortCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('short_code', $code)->exists());

        return $code;
    }

    // ==================== RELATIONS ====================

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function currentOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_owner_id');
    }

    public function verifiedByExpert(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_expert_id');
    }

    public function authenticityCheck(): BelongsTo
    {
        return $this->belongsTo(ProductAuthenticityCheck::class, 'authenticity_check_id');
    }

    public function scans(): HasMany
    {
        return $this->hasMany(VintPassScan::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(VintPassTransfer::class);
    }

    // ==================== ACCESSEURS ====================

    /**
     * URL publique de vérification
     */
    public function getPublicUrlAttribute(): string
    {
        return url('/verify/' . $this->short_code);
    }

    /**
     * Badge de statut formaté
     */
    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            self::STATUS_ACTIVE => [
                'label' => 'Actif',
                'color' => 'green',
                'icon' => '✅',
            ],
            self::STATUS_PENDING => [
                'label' => 'En attente',
                'color' => 'yellow',
                'icon' => '⏳',
            ],
            self::STATUS_SUSPENDED => [
                'label' => 'Suspendu',
                'color' => 'orange',
                'icon' => '⚠️',
            ],
            self::STATUS_REVOKED => [
                'label' => 'Révoqué',
                'color' => 'red',
                'icon' => '❌',
            ],
            default => [
                'label' => 'Inconnu',
                'color' => 'gray',
                'icon' => '❓',
            ],
        };
    }

    /**
     * Niveau d'authenticité basé sur le score
     */
    public function getAuthenticityLevelAttribute(): array
    {
        $score = $this->final_score ?? 0;

        if ($score >= 95) {
            return ['level' => 'platinum', 'label' => 'Platine', 'icon' => '💎', 'color' => 'purple'];
        } elseif ($score >= 85) {
            return ['level' => 'gold', 'label' => 'Or', 'icon' => '🥇', 'color' => 'yellow'];
        } elseif ($score >= 75) {
            return ['level' => 'silver', 'label' => 'Argent', 'icon' => '🥈', 'color' => 'gray'];
        } elseif ($score >= 60) {
            return ['level' => 'bronze', 'label' => 'Bronze', 'icon' => '🥉', 'color' => 'orange'];
        } else {
            return ['level' => 'basic', 'label' => 'Basique', 'icon' => '📋', 'color' => 'blue'];
        }
    }

    /**
     * URL du QR code
     */
    public function getQrCodeUrlAttribute(): ?string
    {
        if ($this->qr_code_path) {
            return asset('storage/' . $this->qr_code_path);
        }
        return null;
    }

    // ==================== MÉTHODES ====================

    /**
     * Enregistrer un scan
     */
    public function recordScan(?User $user = null, ?string $ip = null, ?string $userAgent = null): VintPassScan
    {
        $this->increment('scan_count');
        $this->update(['last_scanned_at' => now()]);

        return $this->scans()->create([
            'scanned_by_user_id' => $user?->id,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'scan_result' => 'valid',
        ]);
    }

    /**
     * Transférer le VintPass à un nouveau propriétaire
     */
    public function transferTo(User $newOwner, ?Order $order = null, ?float $price = null): VintPassTransfer
    {
        $previousOwner = $this->currentOwner;

        // Créer l'enregistrement de transfert
        $transfer = $this->transfers()->create([
            'from_user_id' => $previousOwner->id,
            'to_user_id' => $newOwner->id,
            'order_id' => $order?->id,
            'transfer_price' => $price,
            'currency' => $this->currency,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Mettre à jour l'historique
        $history = $this->ownership_history ?? [];
        $history[] = [
            'user_id' => $newOwner->id,
            'user_name' => $newOwner->name,
            'date' => now()->toDateTimeString(),
            'price' => $price,
            'currency' => $this->currency,
            'transaction_type' => $order ? 'sale' : 'transfer',
            'order_id' => $order?->id,
        ];

        // Mettre à jour le VintPass
        $this->update([
            'current_owner_id' => $newOwner->id,
            'ownership_history' => $history,
            'transfer_count' => $this->transfer_count + 1,
            'last_transferred_at' => now(),
        ]);

        return $transfer;
    }

    /**
     * Activer le VintPass
     */
    public function activate(): bool
    {
        return $this->update([
            'status' => self::STATUS_ACTIVE,
            'issued_at' => now(),
        ]);
    }

    /**
     * Suspendre le VintPass
     */
    public function suspend(string $reason): bool
    {
        return $this->update([
            'status' => self::STATUS_SUSPENDED,
            'suspension_reason' => $reason,
        ]);
    }

    /**
     * Révoquer le VintPass
     */
    public function revoke(string $reason): bool
    {
        return $this->update([
            'status' => self::STATUS_REVOKED,
            'suspension_reason' => $reason,
        ]);
    }

    /**
     * Vérifier si le VintPass est valide
     */
    public function isValid(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Obtenir les données pour l'affichage public
     */
    public function getPublicData(): array
    {
        return [
            'pass_id' => $this->pass_id,
            'short_code' => $this->short_code,
            'status' => $this->status_badge,
            'authenticity_level' => $this->authenticity_level,
            'final_score' => $this->final_score,
            'issued_at' => $this->issued_at?->format('d/m/Y'),
            'item' => [
                'name' => $this->item_snapshot['name'] ?? $this->item?->name,
                'image' => $this->item_snapshot['image'] ?? $this->item?->first_image_url,
                'brand' => $this->item_snapshot['brand'] ?? $this->item?->brand?->name,
                'category' => $this->item_snapshot['category'] ?? $this->item?->category?->name,
            ],
            'verification' => [
                'ai_score' => $this->ai_score,
                'expert_score' => $this->expert_score,
                'expert_name' => $this->verifiedByExpert?->name,
            ],
            'blockchain' => [
                'network' => $this->blockchain_network,
                'hash' => $this->blockchain_hash,
                'confirmed' => $this->blockchain_confirmed_at !== null,
            ],
            'ownership' => [
                'current_owner' => $this->currentOwner?->name,
                'transfer_count' => $this->transfer_count,
                'history_count' => count($this->ownership_history ?? []),
            ],
            'stats' => [
                'scan_count' => $this->scan_count,
                'last_scanned' => $this->last_scanned_at?->diffForHumans(),
            ],
        ];
    }
}
