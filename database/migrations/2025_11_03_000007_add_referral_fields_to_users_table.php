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
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code', 20)->nullable()->unique()->after('fcm_token');
            $table->foreignId('referred_by')->nullable()->constrained('users')->after('referral_code');
            $table->timestamp('referral_activated_at')->nullable()->after('referred_by');
            $table->boolean('referral_program_active')->default(true)->after('referral_activated_at');
            
            // Index pour optimiser les recherches
            $table->index(['referral_code']);
            $table->index(['referred_by', 'referral_activated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['referral_code']);
            $table->dropIndex(['referred_by', 'referral_activated_at']);
            
            $table->dropForeign(['referred_by']);
            $table->dropColumn([
                'referral_code',
                'referred_by', 
                'referral_activated_at',
                'referral_program_active'
            ]);
        });
    }
};