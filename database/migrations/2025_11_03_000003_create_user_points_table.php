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
        Schema::create('user_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('total_points', 15, 2)->default(0); // Points totaux accumulés
            $table->decimal('available_points', 15, 2)->default(0); // Points disponibles à utiliser
            $table->decimal('pending_points', 15, 2)->default(0); // Points en attente de validation
            $table->decimal('redeemed_points', 15, 2)->default(0); // Points déjà convertis
            $table->integer('level')->default(1); // Niveau d'affiliation (1-10)
            $table->decimal('level_multiplier', 4, 2)->default(1.00); // Multiplicateur de niveau
            $table->timestamp('last_activity_at')->nullable(); // Dernière activité de points
            $table->timestamps();

            // Index pour les requêtes fréquentes
            $table->unique('user_id');
            $table->index(['level', 'total_points']);
            $table->index('last_activity_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_points');
    }
};