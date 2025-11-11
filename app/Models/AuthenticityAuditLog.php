<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthenticityAuditLog extends Model
{
    use HasFactory;

    const ACTION_SUBMITTED = 'submitted';
    const ACTION_PAYMENT_COMPLETED = 'payment_completed';
    const ACTION_AI_ANALYSIS_STARTED = 'ai_analysis_started';
    const ACTION_AI_ANALYSIS_COMPLETED = 'ai_analysis_completed';
    const ACTION_EXPERT_ASSIGNED = 'expert_assigned';
    const ACTION_EXPERT_REVIEW_STARTED = 'expert_review_started';
    const ACTION_EXPERT_REVIEW_COMPLETED = 'expert_review_completed';
    const ACTION_ADDITIONAL_EVIDENCE_REQUESTED = 'additional_evidence_requested';
    const ACTION_ADDITIONAL_EVIDENCE_PROVIDED = 'additional_evidence_provided';
    const ACTION_FINAL_DECISION_MADE = 'final_decision_made';

    protected $fillable = [
        'authenticity_check_id',
        'action',
        'performed_by',
        'details'
    ];

    protected $casts = [
        'details' => 'array'
    ];

    /**
     * Relations
     */
    public function authenticityCheck(): BelongsTo
    {
        return $this->belongsTo(ProductAuthenticityCheck::class, 'authenticity_check_id');
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Méthodes utilitaires
     */
    public function getActionLabel(): string
    {
        return match($this->action) {
            self::ACTION_SUBMITTED => 'Demande soumise',
            self::ACTION_PAYMENT_COMPLETED => 'Paiement effectué',
            self::ACTION_AI_ANALYSIS_STARTED => 'Analyse IA démarrée',
            self::ACTION_AI_ANALYSIS_COMPLETED => 'Analyse IA terminée',
            self::ACTION_EXPERT_ASSIGNED => 'Expert assigné',
            self::ACTION_EXPERT_REVIEW_STARTED => 'Examen expert démarré',
            self::ACTION_EXPERT_REVIEW_COMPLETED => 'Examen expert terminé',
            self::ACTION_ADDITIONAL_EVIDENCE_REQUESTED => 'Preuves supplémentaires demandées',
            self::ACTION_ADDITIONAL_EVIDENCE_PROVIDED => 'Preuves supplémentaires fournies',
            self::ACTION_FINAL_DECISION_MADE => 'Décision finale prise',
            default => 'Action inconnue'
        };
    }
}