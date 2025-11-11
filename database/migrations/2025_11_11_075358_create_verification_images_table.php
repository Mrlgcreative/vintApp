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
        Schema::create('verification_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('authenticity_check_id')->constrained('product_authenticity_checks')->onDelete('cascade');
            $table->string('image_path');
            $table->enum('image_type', [
                'product_front',
                'product_back', 
                'product_side',
                'product_detail',
                'certificate',
                'receipt',
                'serial_number',
                'packaging'
            ]);
            $table->json('ai_features_detected')->nullable(); // résultats analyse IA
            $table->integer('image_quality_score')->nullable(); // 0-100
            $table->timestamps();

            $table->index(['authenticity_check_id']);
            $table->index(['image_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_images');
    }
};
