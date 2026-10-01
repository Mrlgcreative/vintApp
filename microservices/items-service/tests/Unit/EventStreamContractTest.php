<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Le nom du stream est un contrat inter-services, pas une clé privée. Ce test
 * verrouille le piège rencontré : le préfixe Redis Laravel par défaut
 * (`{app}_database_`) transformait `vintapp.catalog` en
 * `vintapp_items_database_vintapp.catalog`, invisible pour un consommateur
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

        $this->assertSame('vintapp.catalog', $streams['item.created']);
        $this->assertSame('vintapp.catalog', $streams['item.updated']);
        $this->assertSame('vintapp.catalog', $streams['item.deleted']);
    }

    public function test_default_connection_still_prefixes_its_keys(): void
    {
        // Le préfixe reste souhaitable pour le cache du service : on ne l'a
        // retiré que pour le bus, pas globalement.
        $this->assertNotSame('', config('database.redis.options.prefix'));
    }
}
