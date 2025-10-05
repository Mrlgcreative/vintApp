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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json, array
            $table->string('category')->default('general'); // general, app, payment, notification, etc.
            $table->string('label'); // Label pour l'interface admin
            $table->text('description')->nullable(); // Description pour l'admin
            $table->boolean('is_public')->default(false); // Accessible côté client
            $table->boolean('is_encrypted')->default(false); // Valeurs sensibles
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
