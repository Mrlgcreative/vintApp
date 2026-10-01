<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->string('address')->nullable();
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('city')->nullable();
            $table->string('commune')->nullable();
            $table->timestamp('location_updated_at')->nullable();

            $table->string('theme_preference')->default('system');
            $table->string('locale')->default('fr');
            $table->boolean('newsletter_subscribed')->default(false);

            $table->string('google2fa_secret')->nullable();
            $table->boolean('google2fa_enabled')->default(false);
            $table->text('two_factor_recovery_codes')->nullable();

            $table->string('verification_code')->nullable();
            $table->timestamp('verification_code_expires_at')->nullable();

            $table->string('fcm_token')->nullable();
            $table->string('device_type')->nullable();
            $table->timestamp('fcm_token_updated_at')->nullable();
            $table->timestamp('last_seen')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
