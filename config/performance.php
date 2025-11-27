<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */
    'cache' => [
        // Durées de cache (en secondes)
        'durations' => [
            'items_list' => env('CACHE_ITEMS_LIST', 300),           // 5 minutes
            'item_detail' => env('CACHE_ITEM_DETAIL', 600),         // 10 minutes
            'categories' => env('CACHE_CATEGORIES', 3600),          // 1 heure
            'brands' => env('CACHE_BRANDS', 3600),                  // 1 heure
            'settings' => env('CACHE_SETTINGS', 7200),              // 2 heures
            'user_stats' => env('CACHE_USER_STATS', 300),           // 5 minutes
            'popular_items' => env('CACHE_POPULAR_ITEMS', 900),     // 15 minutes
        ],

        // Activer/désactiver le cache
        'enabled' => env('PERFORMANCE_CACHE_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Optimization
    |--------------------------------------------------------------------------
    */
    'database' => [
        // Activer les index de performance
        'use_indexes' => env('DB_USE_PERFORMANCE_INDEXES', true),

        // Utiliser les requêtes eager loading
        'eager_loading' => env('DB_EAGER_LOADING', true),

        // Limite de résultats par page
        'pagination' => [
            'items' => env('PAGINATION_ITEMS', 12),
            'orders' => env('PAGINATION_ORDERS', 20),
            'messages' => env('PAGINATION_MESSAGES', 50),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Optimization
    |--------------------------------------------------------------------------
    */
    'images' => [
        // Activer la génération de thumbnails
        'thumbnails_enabled' => env('IMAGES_THUMBNAILS_ENABLED', true),

        // Tailles de thumbnails
        'thumbnail_sizes' => [
            'thumb' => ['width' => 150, 'height' => 150],
            'small' => ['width' => 300, 'height' => 300],
            'medium' => ['width' => 600, 'height' => 600],
            'large' => ['width' => 1200, 'height' => 1200],
        ],

        // Qualité de compression (1-100)
        'compression_quality' => env('IMAGES_COMPRESSION_QUALITY', 80),

        // Lazy loading activé
        'lazy_loading' => env('IMAGES_LAZY_LOADING', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Response Optimization
    |--------------------------------------------------------------------------
    */
    'http' => [
        // Compression GZIP activée
        'compression_enabled' => env('HTTP_COMPRESSION_ENABLED', true),

        // Taille minimale pour compression (bytes)
        'compression_min_size' => env('HTTP_COMPRESSION_MIN_SIZE', 1024),

        // Cache HTTP activé
        'cache_enabled' => env('HTTP_CACHE_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limiting' => [
        'api' => [
            'default' => env('RATE_LIMIT_API_DEFAULT', 60),      // requêtes/minute
            'read' => env('RATE_LIMIT_API_READ', 100),           // GET requests
            'write' => env('RATE_LIMIT_API_WRITE', 20),          // POST/PUT/DELETE
            'public' => env('RATE_LIMIT_API_PUBLIC', 30),        // Routes publiques
        ],
        
        'web' => [
            'default' => env('RATE_LIMIT_WEB_DEFAULT', 60),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Optimization
    |--------------------------------------------------------------------------
    */
    'queue' => [
        // Utiliser les queues pour les tâches lourdes
        'enabled' => env('QUEUE_ENABLED', true),

        // Jobs à mettre en queue
        'jobs' => [
            'image_optimization' => env('QUEUE_IMAGE_OPTIMIZATION', true),
            'email_sending' => env('QUEUE_EMAIL_SENDING', true),
            'notifications' => env('QUEUE_NOTIFICATIONS', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring
    |--------------------------------------------------------------------------
    */
    'monitoring' => [
        // Logger les performances
        'log_slow_queries' => env('MONITOR_SLOW_QUERIES', true),
        
        // Seuil pour les requêtes lentes (ms)
        'slow_query_threshold' => env('MONITOR_SLOW_QUERY_THRESHOLD', 1000),

        // Logger les erreurs de cache
        'log_cache_errors' => env('MONITOR_CACHE_ERRORS', true),
    ],

];
