<?php

return [
    // Répartition par défaut des frais de vérification entre les sous-wallets
    // Les valeurs doivent totaliser 1.0 (100%)
    'verification_fee_split' => [
        'commission' => 0.60,
        'transport' => 0.25,
        'boost' => 0.15,
    ],
];
