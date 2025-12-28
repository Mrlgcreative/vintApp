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
        Schema::create('vint_passes', function (Blueprint $table) {
            $table->id();
            
            // Identifiant unique du VintPass (ex: VNT-2025-LV-00847)
            $table->string('pass_id', 50)->unique();
            
            // Relations
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('current_owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('verified_by_expert_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('authenticity_check_id')->nullable()->constrained('product_authenticity_checks')->onDelete('set null');
            
            // Scores de vérification
            $table->decimal('ai_score', 5, 2)->nullable(); // Score IA (0-100)
            $table->decimal('expert_score', 5, 2)->nullable(); // Score Expert (0-100)
            $table->decimal('final_score', 5, 2); // Score final combiné
            
            // Blockchain
            $table->string('blockchain_hash', 66)->nullable(); // Hash de la transaction
            $table->string('blockchain_network', 20)->default('polygon'); // polygon, ethereum, etc.
            $table->string('contract_address', 42)->nullable(); // Adresse du smart contract
            $table->unsignedBigInteger('token_id')->nullable(); // ID du NFT si minté
            $table->timestamp('blockchain_confirmed_at')->nullable();
            
            // QR Code
            $table->string('qr_code_path')->nullable(); // Chemin vers l'image QR
            $table->string('verification_url')->nullable(); // URL publique de vérification
            $table->string('short_code', 10)->unique(); // Code court pour URL (ex: ABC123XYZ)
            
            // Détails de l'article (snapshot au moment de la vérification)
            $table->json('item_snapshot')->nullable(); // Nom, description, images, prix au moment de la vérification
            $table->json('verification_evidence')->nullable(); // Preuves de vérification
            
            // Historique des propriétaires
            $table->json('ownership_history')->nullable(); // [{user_id, date, price, transaction_type}]
            
            // Statut
            $table->enum('status', ['pending', 'active', 'suspended', 'revoked'])->default('pending');
            $table->text('suspension_reason')->nullable();
            
            // Métadonnées
            $table->decimal('estimated_value', 12, 2)->nullable(); // Valeur estimée
            $table->string('currency', 3)->default('USD');
            $table->unsignedInteger('transfer_count')->default(0); // Nombre de transferts
            $table->unsignedInteger('scan_count')->default(0); // Nombre de scans QR
            
            // Timestamps
            $table->timestamp('issued_at')->nullable(); // Date d'émission
            $table->timestamp('last_transferred_at')->nullable();
            $table->timestamp('last_scanned_at')->nullable();
            $table->timestamps();
            
            // Index
            $table->index(['status', 'created_at']);
            $table->index('blockchain_hash');
            $table->index('short_code');
        });

        // Table pour l'historique des scans
        Schema::create('vint_pass_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vint_pass_id')->constrained()->onDelete('cascade');
            $table->foreignId('scanned_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('city', 100)->nullable();
            $table->enum('scan_result', ['valid', 'suspicious', 'invalid'])->default('valid');
            $table->timestamps();
            
            $table->index(['vint_pass_id', 'created_at']);
        });

        // Table pour les transferts de propriété
        Schema::create('vint_pass_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vint_pass_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('transfer_price', 12, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('blockchain_tx_hash', 66)->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['vint_pass_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vint_pass_transfers');
        Schema::dropIfExists('vint_pass_scans');
        Schema::dropIfExists('vint_passes');
    }
};
