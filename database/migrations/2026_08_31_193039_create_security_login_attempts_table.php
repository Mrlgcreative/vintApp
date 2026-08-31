<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table de journalisation des tentatives de connexion.
     *
     * Elle sert de source pour :
     *  - l'affichage des tentatives (succès/échec/rate-limit) dans le monitoring,
     *  - la détection automatique de force brute (agrégats par email et par IP).
     */
    public function up(): void
    {
        Schema::create('security_login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255)->nullable()->index();       // email tenté
            $table->string('ip_address', 45)->index();               // IPv4 ou IPv6
            $table->string('user_agent', 500)->nullable();
            $table->string('route', 255)->nullable();                // nom de la route
            $table->string('guard', 64)->nullable();                 // web | sanctum
            $table->boolean('success')->default(false)->index();     // connexion aboutie ?
            $table->unsignedSmallInteger('status_code')->nullable(); // 200, 302, 401, 422, 429...
            $table->unsignedInteger('attempts')->default(1);         // nb de tentatives côté client
            $table->text('throttle_key')->nullable();                // clé email|IP du RateLimiter
            $table->timestamp('created_at')->useCurrent()->index();

            $table->index(['email', 'ip_address']);
            $table->index(['created_at', 'success']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_login_attempts');
    }
};
