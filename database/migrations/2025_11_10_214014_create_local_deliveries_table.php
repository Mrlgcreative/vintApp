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
        Schema::create('local_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            
            // Informations de livraison
            $table->enum('delivery_type', ['hand_delivery', 'pickup', 'meetup'])->default('hand_delivery');
            $table->enum('status', ['pending', 'accepted', 'in_transit', 'delivered', 'cancelled'])->default('pending');
            
            // Localisation du vendeur
            $table->decimal('seller_latitude', 10, 8)->nullable();
            $table->decimal('seller_longitude', 11, 8)->nullable();
            $table->string('seller_address', 500)->nullable();
            $table->string('seller_city', 100)->nullable();
            $table->string('seller_commune', 100)->nullable();
            
            // Localisation de l'acheteur  
            $table->decimal('buyer_latitude', 10, 8)->nullable();
            $table->decimal('buyer_longitude', 11, 8)->nullable();
            $table->string('buyer_address', 500)->nullable();
            $table->string('buyer_city', 100)->nullable();
            $table->string('buyer_commune', 100)->nullable();
            
            // Point de rendez-vous (pour meetup)
            $table->decimal('meetup_latitude', 10, 8)->nullable();
            $table->decimal('meetup_longitude', 11, 8)->nullable();
            $table->string('meetup_address', 500)->nullable();
            $table->string('meetup_landmark', 200)->nullable(); // Point de repère
            
            // Distance et frais
            $table->decimal('distance_km', 8, 2)->nullable(); // Distance en kilomètres
            $table->decimal('delivery_fee', 10, 2)->default(0); // Frais de livraison
            $table->string('currency', 3)->default('USD');
            
            // Planning
            $table->timestamp('estimated_pickup_time')->nullable();
            $table->timestamp('estimated_delivery_time')->nullable();
            $table->timestamp('actual_pickup_time')->nullable();
            $table->timestamp('actual_delivery_time')->nullable();
            
            // Communication
            $table->string('seller_phone', 20)->nullable();
            $table->string('buyer_phone', 20)->nullable();
            $table->text('delivery_instructions')->nullable();
            $table->text('special_notes')->nullable();
            
            // Vérification
            $table->string('delivery_code', 10)->nullable(); // Code de vérification
            $table->boolean('buyer_confirmed')->default(false);
            $table->boolean('seller_confirmed')->default(false);
            $table->text('cancellation_reason')->nullable();
            
            $table->timestamps();
            
            // Index pour optimiser les recherches géographiques
            $table->index(['seller_latitude', 'seller_longitude']);
            $table->index(['buyer_latitude', 'buyer_longitude']);
            $table->index(['seller_city', 'buyer_city']);
            $table->index(['status', 'delivery_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('local_deliveries');
    }
};
