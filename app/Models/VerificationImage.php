<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationImage extends Model
{
    use HasFactory;

    const TYPE_PRODUCT_FRONT = 'product_front';
    const TYPE_PRODUCT_BACK = 'product_back';
    const TYPE_PRODUCT_SIDE = 'product_side';
    const TYPE_PRODUCT_DETAIL = 'product_detail';
    const TYPE_CERTIFICATE = 'certificate';
    const TYPE_RECEIPT = 'receipt';
    const TYPE_SERIAL_NUMBER = 'serial_number';
    const TYPE_PACKAGING = 'packaging';

    protected $fillable = [
        'authenticity_check_id',
        'image_path',
        'image_type',
        'ai_features_detected',
        'image_quality_score'
    ];

    protected $casts = [
        'ai_features_detected' => 'array'
    ];

    /**
     * Relations
     */
    public function authenticityCheck(): BelongsTo
    {
        return $this->belongsTo(ProductAuthenticityCheck::class, 'authenticity_check_id');
    }

    /**
     * Méthodes utilitaires
     */
    public function getImageUrl(): string
    {
        return asset('storage/' . $this->image_path);
    }

    public function getTypeLabel(): string
    {
        return match($this->image_type) {
            self::TYPE_PRODUCT_FRONT => 'Vue de face',
            self::TYPE_PRODUCT_BACK => 'Vue de dos',
            self::TYPE_PRODUCT_SIDE => 'Vue de profil',
            self::TYPE_PRODUCT_DETAIL => 'Détail',
            self::TYPE_CERTIFICATE => 'Certificat',
            self::TYPE_RECEIPT => 'Reçu/Facture',
            self::TYPE_SERIAL_NUMBER => 'Numéro de série',
            self::TYPE_PACKAGING => 'Emballage',
            default => 'Image'
        };
    }

    public function isHighQuality(): bool
    {
        return $this->image_quality_score >= 80;
    }
}