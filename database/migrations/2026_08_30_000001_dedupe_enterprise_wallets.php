<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dédoublonne les wallets entreprise.
     *
     * Contexte : la migration 2025_10_09 créait 1 wallet "generique" par devise
     * (subtype NULL), puis 2025_11_11 en a ajouté 8 par sous-type (commission,
     * transport, boost, verification). Résultat : plusieurs wallets pour la même
     * caisse, et des doublons par devise.
     *
     * On conserve les sous-wallets (subtype) et on fusionne le solde et
     * l'historique des wallets generiques dans le sous-wallet "commission".
     */
    public function up(): void
    {
        $legacyWallets = DB::table('wallets')
            ->where('type', 'enterprise')
            ->whereNull('user_id')
            ->whereNull('subtype')
            ->get();

        foreach ($legacyWallets as $legacy) {
            $target = DB::table('wallets')
                ->where('type', 'enterprise')
                ->whereNull('user_id')
                ->where('currency', $legacy->currency)
                ->where('subtype', 'commission')
                ->first();

            if (!$target) {
                $target = DB::table('wallets')
                    ->where('type', 'enterprise')
                    ->whereNull('user_id')
                    ->where('currency', $legacy->currency)
                    ->whereNotNull('subtype')
                    ->first();
            }

            if (!$target) {
                // Aucun sous-wallet pour cette devise : conserver ce wallet en le typant commission
                DB::table('wallets')->where('id', $legacy->id)->update(['subtype' => 'commission']);
                continue;
            }

            DB::transaction(function () use ($legacy, $target) {
                DB::table('wallets')->where('id', $target->id)->increment('balance', $legacy->balance);
                DB::table('wallet_transactions')->where('wallet_id', $legacy->id)->update(['wallet_id' => $target->id]);
                DB::table('transactions')->where('wallet_id', $legacy->id)->update(['wallet_id' => $target->id]);
                DB::table('wallets')->where('id', $legacy->id)->delete();
            });
        }

        // Empêcher les doublons futurs : un seul wallet entreprise par type+devise+sous-type.
        // MySQL/MariaDB autorise plusieurs NULL dans un index unique, donc les wallets
        // utilisateurs (subtype NULL) ne sont pas impactés.
        if (!Schema::hasIndex('wallets', 'wallets_type_currency_subtype_unique')) {
            try {
                DB::statement('ALTER TABLE `wallets` ADD UNIQUE INDEX `wallets_type_currency_subtype_unique` (`type`, `currency`, `subtype`)');
            } catch (\Exception $e) {
                // Index déjà présent ou doublons résiduels : on ignore ici
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasIndex('wallets', 'wallets_type_currency_subtype_unique')) {
            DB::statement('ALTER TABLE `wallets` DROP INDEX `wallets_type_currency_subtype_unique`');
        }

        foreach (['USD', 'CDF'] as $currency) {
            $commission = DB::table('wallets')
                ->where('type', 'enterprise')
                ->whereNull('user_id')
                ->where('currency', $currency)
                ->where('subtype', 'commission')
                ->first();

            if (!$commission) {
                continue;
            }

            DB::table('wallets')->insert([
                'user_id' => null,
                'type' => 'enterprise',
                'currency' => $currency,
                'subtype' => null,
                'balance' => $commission->balance,
                'commission_rate' => $commission->commission_rate,
                'is_active' => $commission->is_active,
                'status' => $commission->status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};