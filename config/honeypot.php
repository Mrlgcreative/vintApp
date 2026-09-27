<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Durée minimale de remplissage du formulaire
    |--------------------------------------------------------------------------
    | Un humain passe quelques secondes sur un formulaire d'inscription ou de
    | mot de passe oublié. Un robot poste instantanément. En dessous de ce
    | seuil, la soumission est considérée comme automatisée.
    |
    | Ce n'est qu'un filtre secondaire : un bot déterminé peut réutiliser un
    | timestamp. Les vrais verrous restent les champs honeypot et les rate
    | limiters (auth.register / auth.password).
    */

    'min_seconds' => (int) env('HONEYPOT_MIN_SECONDS', 3),

    /*
    |--------------------------------------------------------------------------
    | Nom du champ horodaté
    |--------------------------------------------------------------------------
    | Champ caché contenant l UNIX timestamp du rendu du formulaire. Ne doit
    | JAMAIS être repopulé avec old() : une revalidation qui réutiliserait
    | l'ancienne valeur piégerait l'utilisateur dans une boucle d'erreurs.
    */

    'timestamp_field' => 'form_ts',

    /*
    |--------------------------------------------------------------------------
    | Champs pièges
    |--------------------------------------------------------------------------
    | Noms volontairement crédibles (ceux que remplissent les robots
    | génériques) plutôt que "honeypot" ou "website_please", qu'un bot
    | légèrement intelligent saucerait. Doivent rester absents des formulaires
    | concernés, sinon les vrais champs se font remplir.
    */

    'fields' => [
        'website',
        'company',
        'fax_number',
    ],

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    | Un seul message pour tous les motifs de rejet : donner un retour
    | distinct par motif offrirait au bot une oracle pour affiner sa
    | soumission.
    */

    'message' => 'Requête invalide. Veuillez réessayer.',

    /*
    |--------------------------------------------------------------------------
    | Clé de journalisation
    |--------------------------------------------------------------------------
    | Les pièges déclenchés ne sont pas journalisés dans security_login_attempts
    | (réservé aux tentatives d'authentification) mais dans le canal log, afin de
    | ne pas polluer les agrégats de force brute du dashboard de monitoring.
    */

    'log_channel' => null,

];
