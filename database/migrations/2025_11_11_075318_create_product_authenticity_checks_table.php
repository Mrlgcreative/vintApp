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
        Schema::create('product_authenticity_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // vendeur
            $table->enum('status', [
                'pending',
                'ai_approved', 
                'ai_rejected', 
                'expert_review', 
                'expert_approved', 
                'expert_rejected'
            ])->default('pending');
            $table->integer('ai_confidence_score')->nullable(); // 0-100
            $table->json('ai_analysis_result')->nullable();
            $table->foreignId('expert_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('expert_notes')->nullable();
            $table->json('verification_evidence')->nullable(); // métadonnées soumises
            $table->decimal('verification_fee', 8, 2)->default(5.00); // Frais en USD
            $table->boolean('payment_completed')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('ai_completed_at')->nullable();
            $table->timestamp('expert_assigned_at')->nullable();
            $table->timestamp('expert_completed_at')->nullable();
            $table->timestamp('final_decision_at')->nullable();
            $table->timestamps();

            $table->index(['status']);
            $table->index(['item_id']);
            $table->index(['user_id']);
            $table->index(['expert_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_authenticity_checks');
    }
};
