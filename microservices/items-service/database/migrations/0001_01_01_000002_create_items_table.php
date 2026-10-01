<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Articles du catalogue (périmètre v1 : catalogue cœur).
 *
 * `user_id` est le vendeur, identité possédée par auth-service : aucune clé
 * étrangère, seulement un index. Les catégories et marques, elles, sont
 * possédées par ce service et donc contraintes.
 *
 * Volontairement absents de v1 (périmètre ultérieur) : boost, authenticité,
 * vérification, modération, avis, favoris.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->ulid('public_id')->unique();
            $table->unsignedBigInteger('user_id')->index();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->enum('currency', ['USD', 'CDF'])->default('USD');
            $table->unsignedInteger('quantity')->default(1);
            $table->enum('condition', ['new', 'like_new', 'good', 'fair', 'poor'])->default('good');
            $table->enum('status', ['active', 'inactive', 'sold', 'pending', 'pending_verification'])->default('active');
            $table->unsignedInteger('views')->default(0);
            $table->json('images')->nullable();
            $table->json('specifications')->nullable();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->string('item_number')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['category_id', 'status']);
            $table->index(['brand_id', 'status']);
            $table->index(['condition', 'price']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
