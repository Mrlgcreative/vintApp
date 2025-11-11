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
        Schema::table('items', function (Blueprint $table) {
            $table->boolean('authenticity_requested')->default(false);
            $table->boolean('authenticity_verified')->default(false);
            $table->timestamp('authenticity_verified_at')->nullable();
            $table->string('authenticity_badge_type')->nullable(); // 'vintapp_verified', 'expert_certified'
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn([
                'authenticity_requested',
                'authenticity_verified', 
                'authenticity_verified_at',
                'authenticity_badge_type'
            ]);
        });
    }
};
