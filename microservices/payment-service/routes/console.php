<?php

use App\Models\Payment;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

Artisan::command('demo:webhook {reference : Référence du paiement à notifier} {--code=0 : ResultCode M-Pesa}', function (string $reference, string $code = '0') {
    $body = json_encode([
        'TransID' => 'TRX-'.strtoupper(substr(bin2hex(random_bytes(3)), 0, 6)),
        'BillRefNumber' => $reference,
        'ResultCode' => $code,
        'TransAmount' => '4200',
        'Currency' => 'USD',
        'MSISDN' => '243000000',
        'ResultDesc' => 'Accepted',
    ]);

    // La signature porte sur le corps brut : c'est exactement ce que
    // l'opérateur enverrait.
    $secret = (string) env(config('payments.providers.mpesa.credential', 'MPESA_CALLBACK_SECRET'), '');

    if ($secret === '') {
        $this->error('MPESA_CALLBACK_SECRET non configuré.');

        return self::FAILURE;
    }

    $url = rtrim((string) config('payments.providers.mpesa.base_url', 'http://127.0.0.1:8102'), '/')
        .'/v1/webhooks/mpesa';

    // withBody() et non post() : post() ré-encode la chaîne en JSON, ce qui
    // produirait un corps différent de celui signé et un 403 légitime.
    $response = Http::withBody($body, 'application/json')
        ->withHeader('X-Signature', hash_hmac('sha256', $body, $secret))
        ->post($url);

    $this->line($response->body());

    return $response->successful() ? self::SUCCESS : self::FAILURE;
})->purpose('Poste un callback M-Pesa signé pour une référence donnée');

Artisan::command('demo:payment {reference? : Référence du paiement à créer}', function (?string $reference = null) {
    $reference ??= 'REF-DEMO-'.strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

    $payment = Payment::query()->firstOrCreate(
        ['reference' => $reference, 'provider_key' => 'mpesa'],
        [
            'user_id' => 1,
            'amount' => 4200,
            'currency' => 'USD',
            'method' => 'mobile_money',
            'status' => 'pending',
        ]
    );

    $this->line("payment public_id : {$payment->public_id}");
    $this->line("relayez le webhook M-Pesa avec BillRefNumber={$reference}");

    return self::SUCCESS;
})->purpose('Crée un paiement en attente pour tester le webhook M-Pesa');
