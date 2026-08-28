<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Seuils de détection d'anomalies & alertes
    |--------------------------------------------------------------------------
    |
    | Ces valeurs contrôlent quand le monitoring considère un événement comme
    | suspect et déclenche une alerte automatique pour les administrateurs.
    |
    */

    'severity_levels' => ['critical', 'warning', 'info'],

    'alert' => [
        // Durée (minutes) pendant laquelle une alerte du même type n'est pas
        // renvoyée par email pour éviter le spam.
        'email_cooldown_minutes' => (int) env('MONITORING_ALERT_COOLDOWN', 30),

        // Durée de rétention des alertes actives (minutes).
        'active_ttl_minutes' => (int) env('MONITORING_ALERT_TTL', 360),
    ],

    'wallet' => [
        // Montant (en devise de la transaction) au-dessus duquel un retrait
        // est considéré comme "élevé" et donc surveillé.
        'large_withdrawal_threshold' => (float) env('MONITORING_LARGE_WITHDRAWAL', 200000),

        // Nombre d'échecs de retrait consécutifs dans la fenêtre pour alerter.
        'max_withdrawal_failures' => (int) env('MONITORING_WITHDRAWAL_FAILURES', 3),
    ],

    'login' => [
        // Nombre de comptes distincts créés depuis une même IP dans la fenêtre.
        'max_accounts_per_ip' => (int) env('MONITORING_ACCOUNTS_PER_IP', 3),

        // Fenêtre (minutes) pour la détection multi-comptes par IP.
        'window_minutes' => (int) env('MONITORING_IP_WINDOW', 60),
    ],

    'orders' => [
        // Nombre de commandes échouées/annulées dans la fenêtre pour alerter.
        'max_cancelled' => (int) env('MONITORING_CANCELLED_ORDERS', 5),

        // Fenêtre (minutes) pour la détection de cascade d'annulations.
        'window_minutes' => (int) env('MONITORING_ORDER_WINDOW', 30),
    ],

    'errors' => [
        // Nombre d'erreurs applicatives dans la fenêtre courante avant alerte
        // (combine avec les métriques déjà en cache par MonitoringService).
        'max_recent_errors' => (int) env('MONITORING_MAX_ERRORS', 5),
    ],

    'health' => [
        // Alerter quand la santé passe dégradée ou indisponible.
        'degraded_threshold' => 'warning', // degraded | unhealthy
    ],

];
