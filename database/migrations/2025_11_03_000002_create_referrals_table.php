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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade'); // Parrain
            $table->foreignId('referred_id')->constrained('users')->onDelete('cascade'); // Filleul
            $table->foreignId('referral_code_id')->constrained('referral_codes')->onDelete('cascade'); // Code utilisé
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');
            $table->decimal('points_earned', 10, 2)->default(0); // Points gagnés par le parrain
            $table->decimal('bonus_points', 10, 2)->default(0); // Points bonus du filleul
            $table->timestamp('activated_at')->nullable(); // Quand le filleul devient actif
            $table->timestamp('completed_at')->nullable(); // Quand les conditions sont remplies
            $table->json('conditions_met')->nullable(); // Conditions remplies (JSON)
            $table->text('notes')->nullable(); // Notes administratives
            $table->timestamps();

            // Contraintes d'unicité et index
            $table->unique(['referrer_id', 'referred_id'], 'unique_referral');
            $table->index(['referrer_id', 'status']);
            $table->index(['referred_id', 'status']);
            $table->index(['referral_code_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};