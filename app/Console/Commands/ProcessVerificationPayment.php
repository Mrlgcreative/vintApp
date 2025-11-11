<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductAuthenticityCheck;

class ProcessVerificationPayment extends Command
{
    protected $signature = 'expert:process-payment {verification_id}';
    protected $description = 'Processer le paiement de vérification via le wallet utilisateur';

    public function handle()
    {
        $id = $this->argument('verification_id');
    $check = ProductAuthenticityCheck::with(['vendor', 'item'])->find($id);
        if (!$check) {
            $this->error("Vérification {$id} non trouvée");
            return 1;
        }

        $service = app(\App\Services\VerificationPaymentService::class);
        $result = $service->processVerificationPayment($check);

        if ($result['success']) {
            $this->info('Paiement traité avec succès: ' . json_encode($result['data']));
            return 0;
        }

        $this->error('Échec du paiement: ' . $result['message']);
        return 1;
    }
}
