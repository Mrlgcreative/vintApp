<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductAuthenticityCheck;
use App\Models\Wallet;

class DebugCheckWallets extends Command
{
    protected $signature = 'expert:debug-check {verification_id}';
    protected $description = 'Afficher les détails du check et des wallets liés pour debug';

    public function handle()
    {
        $id = $this->argument('verification_id');
        $check = ProductAuthenticityCheck::with(['vendor.wallets', 'item'])->find($id);
        if (!$check) {
            $this->error("Vérification {$id} non trouvée");
            return 1;
        }

        $vendor = $check->vendor;
        $this->info("Check #{$check->id} - Item: {$check->item->name} - Fee: {$check->verification_fee}");
        if ($vendor) {
            $this->info("Vendor: {$vendor->id} - {$vendor->name} - {$vendor->email}");
            foreach ($vendor->wallets as $w) {
                $this->line("  Wallet #{$w->id} | type: {$w->type} | currency: {$w->currency} | subtype: {$w->subtype} | balance: {$w->balance}");
            }
        } else {
            $this->warn('Aucun vendor lié');
        }

        $this->info('Enterprise sub-wallets:');
        $subs = Wallet::where('type', 'enterprise')->get();
        foreach ($subs as $s) {
            $this->line("  #{$s->id} | currency: {$s->currency} | subtype: {$s->subtype} | balance: {$s->balance}");
        }

        return 0;
    }
}
