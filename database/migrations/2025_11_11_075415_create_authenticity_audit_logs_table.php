<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('authenticity_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('authenticity_check_id')->constrained('product_authenticity_checks')->onDelete('cascade');
            $table->enum('action', [
                'submitted',
                'payment_completed',
                'ai_analysis_started',
                'ai_analysis_completed',
                'expert_assigned',
                'expert_review_started',
                'expert_review_completed',
                'additional_evidence_requested',
                'additional_evidence_provided',
                'final_decision_made'
            ]);
            $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('details')->nullable(); // données contextuelles de l'action
            $table->timestamps();

            $table->index(['authenticity_check_id']);
            $table->index(['action']);
            $table->index(['performed_by']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authenticity_audit_logs');
    }
};
