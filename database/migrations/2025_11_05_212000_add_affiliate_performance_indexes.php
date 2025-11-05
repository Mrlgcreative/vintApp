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
        Schema::table('referrals', function (Blueprint $table) {
            $table->index('referrer_id', 'idx_referrals_referrer_id');
            $table->index('created_at', 'idx_referrals_created_at');
            $table->index(['referrer_id', 'created_at'], 'idx_referrals_referrer_created');
        });

        Schema::table('point_transactions', function (Blueprint $table) {
            $table->index('user_id', 'idx_point_transactions_user_id');
            $table->index('type', 'idx_point_transactions_type');
            $table->index(['user_id', 'type'], 'idx_point_transactions_user_type');
        });

        Schema::table('affiliate_rewards', function (Blueprint $table) {
            $table->index('user_id', 'idx_affiliate_rewards_user_id');
            $table->index('created_at', 'idx_affiliate_rewards_created_at');
        });

        Schema::table('user_points', function (Blueprint $table) {
            $table->index('user_id', 'idx_user_points_user_id');
            $table->index('level', 'idx_user_points_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropIndex('idx_referrals_referrer_id');
            $table->dropIndex('idx_referrals_created_at');
            $table->dropIndex('idx_referrals_referrer_created');
        });

        Schema::table('point_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_point_transactions_user_id');
            $table->dropIndex('idx_point_transactions_type');
            $table->dropIndex('idx_point_transactions_user_type');
        });

        Schema::table('affiliate_rewards', function (Blueprint $table) {
            $table->dropIndex('idx_affiliate_rewards_user_id');
            $table->dropIndex('idx_affiliate_rewards_created_at');
        });

        Schema::table('user_points', function (Blueprint $table) {
            $table->dropIndex('idx_user_points_user_id');
            $table->dropIndex('idx_user_points_level');
        });
    }
};
