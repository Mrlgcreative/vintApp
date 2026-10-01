<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bus d'événements
    |--------------------------------------------------------------------------
    |
    | Les événements catalogue partent sur des Redis Streams plutôt qu'en
    | Pub/Sub : un `item.updated` émis pendant que le consommateur est arrêté
    | doit rester lisible jusqu'à son XACK, sinon un prix change sans que le
    | cache des autres services ne le voie.
    |
    | Le publisher est configurable pour qu'un environnement sans Redis
    | n'écrase pas les événements : `log` les trace, `null` les ignore.
    | L'outbox reste la source de vérité dans les deux cas.
    |
    */

    'publisher' => env('EVENT_PUBLISHER', 'redis-stream'),

    // Connexion Redis sans préfixe, pour que `vintapp.catalog` soit le nom
    // de clé littéral attendu par les consommateurs.
    'redis_connection' => env('EVENT_REDIS_CONNECTION', 'events'),

    'stream_max_length' => (int) env('EVENT_STREAM_MAX_LENGTH', 10000),

    /*
    | Streams par type d'événement. Regroupés : un consommateur qui suit tout
    | l'historique du catalogue lit un seul stream.
    */
    'streams' => [
        'item.created' => 'vintapp.catalog',
        'item.updated' => 'vintapp.catalog',
        'item.deleted' => 'vintapp.catalog',
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
