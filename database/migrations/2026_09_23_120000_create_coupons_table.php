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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('title')->nullable();
            // 'percent' | 'fixed'
            $table->enum('type', ['percent', 'fixed'])->default('percent');
            // Pourcentage (1-100) ou montant fixe dans la devise choisie
            $table->decimal('value', 10, 2);
            // Devise pour les montants fixes
            $table->string('currency', 3)->default('USD');
            // Montant de commande minimum (vide = aucun)
            $table->decimal('min_amount', 10, 2)->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            // 'active' | 'inactive'
            $table->enum('status', ['active', 'inactive'])->default('active');
            // Limite maximum d'utilisations (vide = illimité)
            $table->unsignedInteger('max_redemptions')->nullable();
            $table->unsignedInteger('redemption_count')->default(0);
            // Créateur (admin)
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};