<?php

use Illuminate\Support\Str;

return [
    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DB_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            'busy_timeout' => null,
            'journal_mode' => null,
            'synchronous' => null,
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'vintapp_items'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => 'InnoDB',
        ],
    ],

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),
        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug((string) env('APP_NAME', 'vintapp'), '_').'_database_'),
        ],
        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        /*
         * Connexion dédiée au bus d'événements, volontairement SANS préfixe.
         *
         * Le préfixe Laravel par défaut (app_database_) est utile pour les
         * clés de cache propres au service, mais pas pour un stream inter-
         * services : `vintapp.catalog` est un nom de contrat, pas une clé
         * privée. Sans cette connexion, la clé réelle devient
         * `vintapp_items_database_vintapp.catalog` et un consommateur
         * (autre langage, autre framework, redis-cli) ne la trouve pas.
         */
        'events' => [
            'url' => env('REDIS_EVENTS_URL', env('REDIS_URL')),
            'host' => env('REDIS_EVENTS_HOST', env('REDIS_HOST', '127.0.0.1')),
            'username' => env('REDIS_EVENTS_USERNAME', env('REDIS_USERNAME')),
            'password' => env('REDIS_EVENTS_PASSWORD', env('REDIS_PASSWORD')),
            'port' => env('REDIS_EVENTS_PORT', env('REDIS_PORT', '6379')),
            'database' => env('REDIS_EVENTS_DB', env('REDIS_DB', '0')),
            'prefix' => '',
        ],
    ],
];
