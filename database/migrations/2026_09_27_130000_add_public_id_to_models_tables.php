<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Tables des modèles utilisant le trait HasPublicId.
     *
     * La colonne reste nullable : la rendre NOT NULL imposerait un ALTER TABLE
     * sur des tables InnoDB déjà chargées, et le trait garantit de toute façon
     * une valeur à l'insertion.
     */
    private const TABLES = [
        'affiliate_rewards', 'allowed_cities', 'allowed_regions', 'app_downloads',
        'authenticity_audit_logs', 'boost_types', 'brands', 'carts', 'categories',
        'conversations', 'coupons', 'dashboards', 'delivery_addresses', 'discounts',
        'distributions', 'expert_notifications', 'expert_profiles', 'expositions',
        'favorites', 'hero_slides', 'items', 'local_deliveries', 'messages',
        'newsletter_subscribers', 'notifications', 'offers', 'orders', 'order_tracking',
        'payments', 'payment_callbacks', 'point_conversion_rates', 'point_redemptions',
        'point_transactions', 'product_authenticity_checks', 'product_boosts',
        'referrals', 'referral_codes', 'refunds', 'reviews', 'roles',
        'security_login_attempts', 'settings', 'support_agents', 'support_chats',
        'support_messages', 'transactions', 'users', 'user_points', 'user_sessions',
        'users_waiting', 'verification_images', 'vint_passes', 'vint_pass_scans',
        'vint_pass_transfers', 'wallets', 'wallet_transactions', 'withdrawal_requests',
    ];

    public function up(): void
    {
        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table) || Schema::hasColumn($table, 'public_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->char('public_id', 26)->nullable();
            });

            $this->backfill($table);

            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->unique('public_id');
            });
        }
    }

    private function backfill(string $table): void
    {
        DB::table($table)
            ->whereNull('public_id')
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($table) {
                foreach ($rows as $row) {
                    DB::table($table)->where('id', $row->id)->update([
                        'public_id' => (string) Str::ulid(),
                    ]);
                }
            }, 'id');
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'public_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropUnique(['public_id']);
                $blueprint->dropColumn('public_id');
            });
        }
    }
};
