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
        Schema::create('referral_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('code', 20)->unique()->index(); // Code de parrainage unique
            $table->string('title')->nullable(); // Titre personnalisé du code
            $table->text('description')->nullable(); // Description du code
            $table->boolean('is_active')->default(true); // Code actif ou non
            $table->integer('max_uses')->nullable(); // Limite d'utilisation (null = illimité)
            $table->integer('current_uses')->default(0); // Utilisations actuelles
            $table->decimal('bonus_points', 10, 2)->default(0); // Points bonus pour ce code
            $table->date('expires_at')->nullable(); // Date d'expiration
            $table->timestamps();

            // Index pour les recherches fréquentes
            $table->index(['user_id', 'is_active']);
            $table->index(['code', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_codes');
    }
};