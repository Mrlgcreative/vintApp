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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            // 'global' | 'categories' | 'items'
            $table->enum('scope', ['global', 'categories', 'items'])->default('global');
            // 'percent' | 'fixed'
            $table->enum('type', ['percent', 'fixed'])->default('percent');
            // Valeur : pourcentage (1-100) ou montant fixe dans la devise choisie
            $table->decimal('value', 10, 2);
            // Devise pour les montants fixes
            $table->string('currency', 3)->default('USD');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            // 'active' | 'paused' | 'expired'
            $table->enum('status', ['active', 'paused', 'expired'])->default('active');
            // Limite maximum d'utilisations (vide = illimité)
            $table->unsignedInteger('max_redemptions')->nullable();
            $table->unsignedInteger('redemption_count')->default(0);
            // Vraie vente flash : force un compte à rebours visible
            $table->boolean('is_flash_sale')->default(false);
            // Créateur : admin ou vendeur
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        // Offre -> catégories cibles
        Schema::create('offer_category', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id');
            $table->unsignedBigInteger('category_id');
            $table->foreign('offer_id')->references('id')->on('offers')->cascadeOnDelete();
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
            $table->unique(['offer_id', 'category_id']);
        });

        // Offre -> produits cibles
        Schema::create('offer_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id');
            $table->unsignedBigInteger('item_id');
            $table->foreign('offer_id')->references('id')->on('offers')->cascadeOnDelete();
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
            $table->unique(['offer_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_item');
        Schema::dropIfExists('offer_category');
        Schema::dropIfExists('offers');
    }
};