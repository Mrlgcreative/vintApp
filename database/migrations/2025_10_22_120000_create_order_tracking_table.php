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
        Schema::create('order_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, picked_up, in_transit, out_for_delivery, delivered
            $table->decimal('latitude', 10, 8)->nullable(); // Position actuelle de la livraison
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('address')->nullable(); // Adresse actuelle de la position
            $table->string('city')->nullable();
            $table->string('country')->nullable()->default('CD');
            $table->text('description')->nullable(); // Description de l'étape
            $table->string('tracking_code')->nullable(); // Code de suivi externe (ex: DHL, FedEx)
            $table->string('carrier')->nullable(); // Transporteur (DHL, FedEx, Local, etc.)
            $table->decimal('customer_latitude', 10, 8)->nullable(); // Destination finale du client
            $table->decimal('customer_longitude', 11, 8)->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_city')->nullable();
            $table->string('customer_phone')->nullable();
            $table->timestamp('tracked_at')->useCurrent(); // Date de cette position
            $table->timestamp('estimated_delivery')->nullable(); // Date estimée de livraison
            $table->timestamps();
            
            // Index pour performances
            $table->index('order_id');
            $table->index('status');
            $table->index('tracked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_tracking');
    }
};
