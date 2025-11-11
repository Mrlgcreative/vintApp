<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAuthenticityCheck extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_AI_APPROVED = 'ai_approved';
    const STATUS_AI_REJECTED = 'ai_rejected';
    const STATUS_EXPERT_REVIEW = 'expert_review';
    const STATUS_EXPERT_APPROVED = 'expert_approved';
    const STATUS_EXPERT_REJECTED = 'expert_rejected';

    protected $fillable = [
        'item_id',
        'user_id',
        'status',
        'ai_confidence_score',
        'ai_analysis_result',
        'expert_id',
        'expert_notes',
        'verification_evidence',
        'verification_fee',
        'payment_completed',
        'payment_completed_at',
        'payment_method',
        'submitted_at',
        'ai_completed_at',
        'expert_assigned_at',
        'expert_completed_at',
        'final_decision_at',
    ];

    protected $casts = [
        'ai_analysis_result' => 'array',
        'verification_evidence' => 'array',
        'payment_completed' => 'boolean',
        'submitted_at' => 'datetime',
        'ai_completed_at' => 'datetime',
        'expert_assigned_at' => 'datetime',
        'expert_completed_at' => 'datetime',
        'final_decision_at' => 'datetime',
        'verification_fee' => 'decimal:2',
    ];

    /**
     * Relations
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function expert(): BelongsTo
    {
        return $this->belongsTo(User::class, 'expert_id');
    }

    public function verificationImages(): HasMany
    {
        return $this->hasMany(VerificationImage::class, 'authenticity_check_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuthenticityAuditLog::class, 'authenticity_check_id');
    }

    /**
     * Méthodes utilitaires
     */
    public function isApproved(): bool
    {
        return in_array($this->status, [self::STATUS_AI_APPROVED, self::STATUS_EXPERT_APPROVED]);
    }

    public function isRejected(): bool
    {
        return in_array($this->status, [self::STATUS_AI_REJECTED, self::STATUS_EXPERT_REJECTED]);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function needsExpertReview(): bool
    {
        return $this->status === self::STATUS_EXPERT_REVIEW;
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_AI_APPROVED, self::STATUS_EXPERT_APPROVED => 'bg-green-100 text-green-800',
            self::STATUS_AI_REJECTED, self::STATUS_EXPERT_REJECTED => 'bg-red-100 text-red-800',
            self::STATUS_EXPERT_REVIEW => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'En attente',
            self::STATUS_AI_APPROVED => 'Approuvé par IA',
            self::STATUS_AI_REJECTED => 'Rejeté par IA',
            self::STATUS_EXPERT_REVIEW => 'Examen expert',
            self::STATUS_EXPERT_APPROVED => 'Certifié par expert',
            self::STATUS_EXPERT_REJECTED => 'Rejeté par expert',
            default => 'Statut inconnu'
        };
    }
}