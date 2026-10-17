<?php

namespace App\Services;

use App\Contracts\EventPublisher;
use App\Models\OutboxMessage;

/**
 * N'écrit nulle part. Utile pour un environnement qui veut consommer les
 * événements sans les exposer (tests d'intégration isolés du transport).
 */
class NullEventPublisher implements EventPublisher
{
    public function publish(OutboxMessage $message): void
    {
        //
    }

    public function name(): string
    {
        return 'null';
    }
}
