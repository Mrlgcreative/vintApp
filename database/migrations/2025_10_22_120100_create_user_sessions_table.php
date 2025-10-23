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
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('session_id')->unique(); // Laravel session ID
            $table->string('ip_address', 45); // IPv4 ou IPv6
            $table->text('user_agent')->nullable(); // Navigateur et OS
            $table->string('device_type')->nullable(); // mobile, tablet, desktop
            $table->string('browser')->nullable(); // Chrome, Firefox, etc.
            $table->string('os')->nullable(); // Windows, iOS, Android, etc.
            $table->decimal('latitude', 10, 8)->nullable(); // Localisation de connexion
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->timestamp('last_activity')->useCurrent(); // Dernière activité
            $table->timestamp('login_at')->useCurrent(); // Date de connexion
            $table->timestamp('logout_at')->nullable(); // Date de déconnexion
            $table->boolean('is_active')->default(true); // Encore connecté ?
            $table->timestamps();
            
            // Index pour performances
            $table->index('user_id');
            $table->index('session_id');
            $table->index('is_active');
            $table->index('last_activity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
