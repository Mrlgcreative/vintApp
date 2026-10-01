<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bus d'événements
    |--------------------------------------------------------------------------
    |
    | Les événements partent sur des Redis Streams plutôt qu'en Pub/Sub : un
    | `payment.completed` émis pendant que le consommateur est arrêté doit
    | rester lisible jusqu'à son XACK, sinon une commande payée devient
    | invisible pour order-service.
    |
    | Le publisher est configurable pour qu'un environnement sans Redis
    | n'écrase pas les événements : `log` les trace, `null` les ignore.
    | L'outbox reste la source de vérité dans les deux cas.
    |
    */

    'publisher' => env('EVENT_PUBLISHER', 'redis-stream'),

    'redis_connection' => env('EVENT_REDIS_CONNECTION', 'default'),

    'stream_max_length' => (int) env('EVENT_STREAM_MAX_LENGTH', 10000),

    /*
    | Streams par type d'événement. Regroupés : un consommateur qui suit tout
    | l'historique des paiements lit un seul stream.
    */
    'streams' => [
        'payment.completed' => 'vintapp.payment',
        'payment.failed' => 'vintapp.payment',
    ],

    /*
    | Rejeu de l'outbox. `batch` borne le travail d'une passe, `backoff`
    | évite de marteler un transport tombé.
    */
    'outbox' => [
        'batch' => (int) env('EVENT_OUTBOX_BATCH', 100),
        'backoff_seconds' => (int) env('EVENT_OUTBOX_BACKOFF', 30),
    ],
];
