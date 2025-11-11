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
        Schema::create('expert_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('specialties'); // catégories d'expertise : ['mode_luxe', 'electronique', 'bijoux', etc.]
            $table->integer('verification_count')->default(0);
            $table->decimal('approval_rate', 5, 2)->default(0.00); // pourcentage
            $table->enum('certification_level', ['bronze', 'silver', 'gold'])->default('bronze');
            $table->boolean('is_active')->default(true);
            $table->decimal('commission_rate', 5, 2)->default(5.00); // pourcentage de la fee
            $table->text('bio')->nullable();
            $table->text('credentials')->nullable(); // certifications, expérience
            $table->timestamps();

            $table->unique('user_id');
            $table->index(['is_active']);
            $table->index(['certification_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expert_profiles');
    }
};
