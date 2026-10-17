<?php

namespace App\Services;

use App\Models\OutboxMessage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Écrit un événement dans l'outbox.
 *
 * À appeler DANS la transaction métier : soit l'état de l'article et
 * l'événement passent ensemble, soit rien. La publication sur le bus est
 * ensuite faite par `events:relay`.
 */
class OutboxWriter
{
    public const ITEM_CREATED = 'item.created';

    public const ITEM_UPDATED = 'item.updated';

    public const ITEM_DELETED = 'item.deleted';

    /**
     * @param  array<string, mixed>  $payload
     */
    public function write(string $type, array $payload, ?string $stream = null): OutboxMessage
    {
        // Les clés de `events.streams` contiennent un point (`item.created`) :
        // un accès `config("events.streams.{$type}")` serait interprété comme
        // une notation imbriquée. On lit le tableau puis on indexe littéralement.
        $stream ??= config('events.streams')[$type] ?? null;

        if (! is_string($stream) || $stream === '') {
            throw new RuntimeException("Aucun stream configuré pour l'événement {$type}.");
        }

        return OutboxMessage::create([
            'event_id' => (string) Str::uuid(),
            'type' => $type,
            'stream' => $stream,
            'payload' => $payload,
            'available_at' => now(),
        ]);
    }
}
