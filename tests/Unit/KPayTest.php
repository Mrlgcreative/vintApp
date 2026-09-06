<?php

namespace Tests\Unit;

use App\Services\KPay;
use PHPUnit\Framework\TestCase;

class KPayTest extends TestCase
{
    private function makeService(array $config = []): KPay
    {
        return new KPay(array_merge([
            'api_key' => 'kpay_live_testapikey',
            'secret_key' => str_repeat('a', 64),
            'environment' => 'production',
            'base_url' => 'https://admin.kpay.site',
            'webhook_secret' => 'webhook-secret',
            'gateway_secret' => 'gateway-secret',
            'default_provider' => 'VODACOM_MPESA_COD',
            'timeout' => 30,
        ], $config));
    }

    public function test_map_status(): void
    {
        $service = $this->makeService();

        $this->assertSame('completed', $service->mapStatus('COMPLETED'));
        $this->assertSame('completed', $service->mapStatus('SUCCESS'));
        $this->assertSame('failed', $service->mapStatus('FAILED'));
        $this->assertSame('cancelled', $service->mapStatus('CANCELLED'));
        $this->assertSame('processing', $service->mapStatus('PROCESSING'));
        $this->assertSame('pending', $service->mapStatus('PENDING'));
        $this->assertSame('pending', $service->mapStatus('UNKNOWN_STATUS'));
    }

    public function test_is_final_status(): void
    {
        $service = $this->makeService();

        $this->assertTrue($service->isFinalStatus('COMPLETED'));
        $this->assertTrue($service->isFinalStatus('FAILED'));
        $this->assertTrue($service->isFinalStatus('CANCELLED'));
        $this->assertFalse($service->isFinalStatus('PENDING'));
        $this->assertFalse($service->isFinalStatus('PROCESSING'));
    }

    public function test_normalize_phone_number(): void
    {
        $service = $this->makeService();

        $this->assertSame('243812345678', $service->normalizePhoneNumber('0812345678'));
        $this->assertSame('243812345678', $service->normalizePhoneNumber('+243812345678'));
        $this->assertSame('243812345678', $service->normalizePhoneNumber('812345678'));
        $this->assertSame('243812345678', $service->normalizePhoneNumber('+243 81 234 56 78'));
    }

    public function test_is_configured_requires_both_keys(): void
    {
        $this->assertTrue($this->makeService()->isConfigured());
        $this->assertFalse($this->makeService(['api_key' => ''])->isConfigured());
        $this->assertFalse($this->makeService(['secret_key' => ''])->isConfigured());
    }

    public function test_verify_return_signature_accepts_valid_signature(): void
    {
        $service = $this->makeService();

        $ts = (string) (time() * 1000);
        $stringToSign = 'COMPLETED|KPAY-20260514-ABC123|ORDER-12345|' . $ts;
        $sig = hash_hmac('sha256', $stringToSign, 'gateway-secret');

        $query = [
            'status' => 'COMPLETED',
            'reference' => 'KPAY-20260514-ABC123',
            'externalId' => 'ORDER-12345',
            'ts' => $ts,
            'sig' => $sig,
        ];

        $this->assertTrue($service->verifyReturnSignature($query));
    }

    public function test_verify_return_signature_rejects_invalid_or_expired(): void
    {
        $service = $this->makeService();
        $ts = (string) (time() * 1000);

        $stringToSign = 'COMPLETED|KPAY-20260514-ABC123|ORDER-12345|' . $ts;
        $sig = hash_hmac('sha256', $stringToSign, 'gateway-secret');

        // Signature invalide
        $bad = ['status' => 'COMPLETED', 'reference' => 'KPAY-20260514-ABC123', 'externalId' => 'ORDER-12345', 'ts' => $ts, 'sig' => str_repeat('0', 64)];
        $this->assertFalse($service->verifyReturnSignature($bad));

        // Timestamp expiré (> 10 minutes)
        $oldTs = (string) ((time() - 11 * 60) * 1000);
        $oldStringToSign = 'COMPLETED|KPAY-20260514-ABC123|ORDER-12345|' . $oldTs;
        $oldSig = hash_hmac('sha256', $oldStringToSign, 'gateway-secret');
        $expired = ['status' => 'COMPLETED', 'reference' => 'KPAY-20260514-ABC123', 'externalId' => 'ORDER-12345', 'ts' => $oldTs, 'sig' => $oldSig];
        $this->assertFalse($service->verifyReturnSignature($expired));

        // Signature manquante
        $this->assertFalse($service->verifyReturnSignature(['status' => 'COMPLETED', 'reference' => 'KPAY', 'ts' => $ts]));
    }

    public function test_get_default_provider(): void
    {
        $this->assertSame('VODACOM_MPESA_COD', $this->makeService()->getDefaultProvider());
        $this->assertSame('ORANGE_COD', $this->makeService(['default_provider' => 'ORANGE_COD'])->getDefaultProvider());
    }
}