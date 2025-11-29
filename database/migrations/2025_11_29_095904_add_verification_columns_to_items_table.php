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
        Schema::table('items', function (Blueprint $table) {
            // Statut de vérification: approved, pending, rejected
            $table->string('verification_status')->default('pending')->after('status');
            
            // Score de vérification (0-100)
            $table->decimal('verification_score', 5, 2)->nullable()->after('verification_status');
            
            // Détails de la vérification (JSON)
            $table->json('verification_details')->nullable()->after('verification_score');
            
            // Date de vérification
            $table->timestamp('verified_at')->nullable()->after('verification_details');
            
            // ID de l'admin qui a vérifié (si vérification manuelle)
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete()->after('verified_at');
            
            // Index pour requêtes fréquentes
            $table->index('verification_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex(['verification_status']);
            $table->dropForeign(['verified_by']);
            $table->dropColumn([
                'verification_status',
                'verification_score',
                'verification_details',
                'verified_at',
                'verified_by'
            ]);
        });
    }
};
