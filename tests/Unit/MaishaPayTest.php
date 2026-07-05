<?php

namespace Tests\Unit;

use App\Services\MaishaPay;
use PHPUnit\Framework\TestCase;

class MaishaPayTest extends TestCase
{
    public function test_status_reference_prefers_originating_transaction_id(): void
    {
        $service = new MaishaPay();

        $response = [
            'data' => [
                'transactionId' => 256487,
                'originatingTransactionId' => 'MP-REF-123',
                'transactionReference' => 'MP-REF-123',
            ],
        ];

        $this->assertSame('MP-REF-123', $service->resolveStatusReference($response));
    }
}
