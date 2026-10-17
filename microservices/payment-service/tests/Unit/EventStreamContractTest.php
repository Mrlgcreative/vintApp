<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Le nom du stream est un contrat inter-services, pas une clé privée. Ce test
 * verrouille le piège rencontré : le préfixe Redis Laravel par défaut
 * (`{app}_database_`) transformait `vintapp.payment` en
 * `vintapp_payment_database_vintapp.payment`, invisible pour un consommateur
 * qui lit le nom documenté.
 */
class EventStreamContractTest extends TestCase
{
    public function test_events_use_a_dedicated_prefix_free_connection(): void
    {
        $this->assertSame('events', config('events.redis_connection'));
        $this->assertSame('', config('database.redis.events.prefix'));
    }

    public function test_stream_names_are_the_literal_contract_names(): void
    {
        $streams = config('events.streams');

        $this->assertSame('vintapp.payment', $streams['payment.completed']);
        $this->assertSame('vintapp.payment', $streams['payment.failed']);
    }

    public function test_default_connection_still_prefixes_its_keys(): void
    {
        // Le préfixe reste souhaitable pour le cache du service : on ne l'a
        // retiré que pour le bus, pas globalement.
        $default = config('database.redis.options.prefix');

        $this->assertNotSame('', $default);
    }
}
