<?php

namespace App\Contracts;

use App\Models\OutboxMessage;

/**
 * Transport de publication des événements inter-services.
 *
 * Implémentation attendue : Redis Streams (`XADD`). L'implémentation doit être
 * idempotente sur `event_id` côté consommateur, mais doit aussi lever une
 * exception si la publication échoue : le message reste alors en base et sera
 * rejoué, plutôt que d'être perdu silencieusement.
 */
interface EventPublisher
{
    public function publish(OutboxMessage $message): void;

    public function name(): string;
}
