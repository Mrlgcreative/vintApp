<?php

namespace App\Console\Commands;

use App\Services\OutboxRelay;
use Illuminate\Console\Command;

class RelayOutbox extends Command
{
    protected $signature = 'events:relay
                            {--once : Traiter une seule passe puis sortir}
                            {--batch= : Nombre maximal de messages par passe}';

    protected $description = "Publier les événements de l'outbox sur le bus";

    public function handle(OutboxRelay $relay): int
    {
        $batch = $this->option('batch') !== null ? (int) $this->option('batch') : null;

        do {
            $result = $relay->flush($batch);

            $this->line("publiés: {$result['published']}  en échec: {$result['failed']}");

            if ($this->option('once') || $result['published'] === 0) {
                break;
            }
        } while (true);

        return $result['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
