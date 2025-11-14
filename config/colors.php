<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Palette de Couleurs VintApp
    |--------------------------------------------------------------------------
    |
    | Définit les couleurs principales de l'application pour maintenir
    | une cohérence visuelle à travers toute l'interface.
    |
    */

    'palettes' => [
        'default' => [
            'name' => 'Palette par défaut',
            'primary' => '#3B82F6',     // Bleu
            'secondary' => '#6B7280',    // Gris
            'success' => '#10B981',      // Vert
            'danger' => '#EF4444',       // Rouge
            'warning' => '#F59E0B',      // Orange
            'info' => '#06B6D4',         // Cyan
            'light' => '#F8FAFC',        // Gris très clair
            'dark' => '#1F2937',         // Gris foncé
            'accent' => '#8B5CF6',       // Violet
        ],
        
        'luxury' => [
            'name' => 'Luxe & Élégance',
            'primary' => '#C9A961',      // Or
            'secondary' => '#2C2C2C',    // Noir charbon
            'success' => '#28A745',      // Vert émeraude
            'danger' => '#DC3545',       // Rouge bordeaux
            'warning' => '#FFC107',      // Jaune or
            'info' => '#17A2B8',         // Bleu turquoise
            'light' => '#FAF9F6',        // Blanc cassé
            'dark' => '#1A1A1A',         // Noir profond
            'accent' => '#8E4EC6',       // Violet royal
        ],
        
        'vintage' => [
            'name' => 'Vintage Rétro',
            'primary' => '#D4A574',      // Beige doré
            'secondary' => '#8B5A3C',    // Marron vintage
            'success' => '#6B8E23',      // Vert olive
            'danger' => '#CD853F',       // Rouille
            'warning' => '#DAA520',      // Or antique
            'info' => '#4682B4',         // Bleu acier
            'light' => '#FDF5E6',        // Blanc antique
            'dark' => '#2F4F4F',         // Gris ardoise foncé
            'accent' => '#DDA0DD',       // Prune
        ],
        
        'modern' => [
            'name' => 'Moderne & Minimaliste',
            'primary' => '#667EEA',      // Bleu moderne
            'secondary' => '#A0AEC0',    // Gris moderne
            'success' => '#48BB78',      // Vert moderne
            'danger' => '#F56565',       // Rouge corail
            'warning' => '#ED8936',      // Orange moderne
            'info' => '#4299E1',         // Bleu ciel
            'light' => '#FAFAFA',        // Blanc pur
            'dark' => '#2D3748',         // Gris moderne foncé
            'accent' => '#9F7AEA',       // Violet moderne
        ],
        
        'earth' => [
            'name' => 'Tons Naturels',
            'primary' => '#8B4513',      // Brun terre
            'secondary' => '#696969',    // Gris pierre
            'success' => '#228B22',      // Vert forêt
            'danger' => '#B22222',       // Rouge brique
            'warning' => '#FF8C00',      // Orange terre cuite
            'info' => '#20B2AA',         // Turquoise naturel
            'light' => '#F5F5DC',        // Beige naturel
            'dark' => '#2F4F2F',         // Vert foncé
            'accent' => '#CD853F',       // Cuivre
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Palette Active
    |--------------------------------------------------------------------------
    |
    | Définit quelle palette est actuellement active dans l'application
    |
    */
    'active_palette' => env('VINTAPP_COLOR_PALETTE', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Variations de Couleurs
    |--------------------------------------------------------------------------
    |
    | Définit les variations (nuances) pour chaque couleur
    |
    */
    'shades' => [
        '50', '100', '200', '300', '400', '500', '600', '700', '800', '900'
    ],

    /*
    |--------------------------------------------------------------------------
    | Classes CSS Tailwind correspondantes
    |--------------------------------------------------------------------------
    |
    | Mapping des couleurs vers les classes Tailwind CSS
    |
    */
    'tailwind_classes' => [
        'primary' => [
            'bg' => 'bg-primary',
            'text' => 'text-primary',
            'border' => 'border-primary',
            'ring' => 'ring-primary',
            'hover-bg' => 'hover:bg-primary-600',
            'focus-ring' => 'focus:ring-primary-500',
        ],
        'secondary' => [
            'bg' => 'bg-secondary',
            'text' => 'text-secondary',
            'border' => 'border-secondary',
            'ring' => 'ring-secondary',
            'hover-bg' => 'hover:bg-secondary-600',
            'focus-ring' => 'focus:ring-secondary-500',
        ],
        'success' => [
            'bg' => 'bg-success',
            'text' => 'text-success',
            'border' => 'border-success',
            'ring' => 'ring-success',
            'hover-bg' => 'hover:bg-success-600',
            'focus-ring' => 'focus:ring-success-500',
        ],
        'danger' => [
            'bg' => 'bg-danger',
            'text' => 'text-danger',
            'border' => 'border-danger',
            'ring' => 'ring-danger',
            'hover-bg' => 'hover:bg-danger-600',
            'focus-ring' => 'focus:ring-danger-500',
        ],
        'warning' => [
            'bg' => 'bg-warning',
            'text' => 'text-warning',
            'border' => 'border-warning',
            'ring' => 'ring-warning',
            'hover-bg' => 'hover:bg-warning-600',
            'focus-ring' => 'focus:ring-warning-500',
        ],
        'info' => [
            'bg' => 'bg-info',
            'text' => 'text-info',
            'border' => 'border-info',
            'ring' => 'ring-info',
            'hover-bg' => 'hover:bg-info-600',
            'focus-ring' => 'focus:ring-info-500',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Couleurs Spéciales
    |--------------------------------------------------------------------------
    |
    | Couleurs spécifiques à certains éléments de l'application
    |
    */
    'special' => [
        'navbar' => env('VINTAPP_NAVBAR_COLOR', 'primary'),
        'sidebar' => env('VINTAPP_SIDEBAR_COLOR', 'dark'),
        'footer' => env('VINTAPP_FOOTER_COLOR', 'dark'),
        'buttons' => env('VINTAPP_BUTTON_COLOR', 'primary'),
        'links' => env('VINTAPP_LINK_COLOR', 'primary'),
        'borders' => env('VINTAPP_BORDER_COLOR', 'secondary'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Couleurs par Rôle Utilisateur
    |--------------------------------------------------------------------------
    |
    | Couleurs spécifiques selon le rôle de l'utilisateur
    |
    */
    'roles' => [
        'admin' => [
            'primary' => '#DC2626',     // Rouge admin
            'accent' => '#FCD34D',      // Jaune attention
        ],
        'expert' => [
            'primary' => '#7C3AED',     // Violet expert
            'accent' => '#A78BFA',      // Violet clair
        ],
        'vendor' => [
            'primary' => '#059669',     // Vert vendeur
            'accent' => '#6EE7B7',      // Vert clair
        ],
        'buyer' => [
            'primary' => '#2563EB',     // Bleu acheteur
            'accent' => '#93C5FD',      // Bleu clair
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Couleurs de Statut
    |--------------------------------------------------------------------------
    |
    | Couleurs pour les différents statuts dans l'application
    |
    */
    'status' => [
        'pending' => '#F59E0B',        // Orange - En attente
        'approved' => '#10B981',       // Vert - Approuvé
        'rejected' => '#EF4444',       // Rouge - Rejeté
        'processing' => '#3B82F6',     // Bleu - En traitement
        'completed' => '#059669',      // Vert foncé - Terminé
        'cancelled' => '#6B7280',      // Gris - Annulé
        'active' => '#10B981',         // Vert - Actif
        'inactive' => '#6B7280',       // Gris - Inactif
        'verified' => '#10B981',       // Vert - Vérifié
        'unverified' => '#F59E0B',     // Orange - Non vérifié
    ],

    /*
    |--------------------------------------------------------------------------
    | Mode Sombre
    |--------------------------------------------------------------------------
    |
    | Variations pour le mode sombre
    |
    */
    'dark_mode' => [
        'enabled' => env('VINTAPP_DARK_MODE', false),
        'auto_switch' => env('VINTAPP_AUTO_DARK_MODE', true),
        'switch_time' => [
            'start' => '19:00',
            'end' => '07:00'
        ]
    ]
];