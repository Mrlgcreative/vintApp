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
    ],

    /*
    |--------------------------------------------------------------------------
    | Mode Jour / Nuit (couleurs dynamiques)
    |--------------------------------------------------------------------------
    |
    | Système automatique qui change les couleurs de l'interface selon
    | l'heure de la journée. Fonctionne côté client (JavaScript) pour
    | une transition fluide sans rechargement de page.
    |
    | Périodes :
    |   - Jour   : 07:00 - 18:59 (couleurs vives et claires)
    |   - Nuit   : 19:00 - 06:59 (couleurs douces et sombres)
    |
    */
    'day_night' => [
        'enabled' => env('VINTAPP_DAY_NIGHT_MODE', true),

        // Heures de transition (format 24h)
        'day_start' => 7,    // 07:00
        'night_start' => 19, // 19:00

        // Durée de la transition en millisecondes (côté JS)
        'transition_duration' => 800,

        // Palettes sélectionnées (clés des tableaux ci-dessous)
        'active_day' => env('VINTAPP_DAY_PALETTE', 'ciel'),
        'active_night' => env('VINTAPP_NIGHT_PALETTE', 'indigo'),

        // =============================================
        // PALETTES DE JOUR (couleurs vives & claires)
        // =============================================
        'day_palettes' => [
            'ciel' => [
                'name' => '☀️ Ciel Bleu',
                'primary' => '#3B82F6',
                'secondary' => '#6B7280',
                'success' => '#10B981',
                'danger' => '#EF4444',
                'warning' => '#F59E0B',
                'info' => '#06B6D4',
                'light' => '#F8FAFC',
                'dark' => '#1F2937',
                'accent' => '#8B5CF6',
                'background' => '#FFFFFF',
                'surface' => '#F9FAFB',
                'text' => '#111827',
                'text_muted' => '#6B7280',
                'border' => '#E5E7EB',
            ],
            'soleil' => [
                'name' => '🌅 Soleil Doré',
                'primary' => '#D97706',
                'secondary' => '#78716C',
                'success' => '#059669',
                'danger' => '#DC2626',
                'warning' => '#F59E0B',
                'info' => '#0891B2',
                'light' => '#FFFBEB',
                'dark' => '#292524',
                'accent' => '#EA580C',
                'background' => '#FFFDF7',
                'surface' => '#FEF3C7',
                'text' => '#1C1917',
                'text_muted' => '#78716C',
                'border' => '#FDE68A',
            ],
            'emeraude' => [
                'name' => '🌿 Émeraude Frais',
                'primary' => '#059669',
                'secondary' => '#6B7280',
                'success' => '#10B981',
                'danger' => '#EF4444',
                'warning' => '#F59E0B',
                'info' => '#06B6D4',
                'light' => '#ECFDF5',
                'dark' => '#064E3B',
                'accent' => '#14B8A6',
                'background' => '#F0FDF9',
                'surface' => '#D1FAE5',
                'text' => '#064E3B',
                'text_muted' => '#6B7280',
                'border' => '#A7F3D0',
            ],
            'corail' => [
                'name' => '🌺 Corail Tropical',
                'primary' => '#F43F5E',
                'secondary' => '#6B7280',
                'success' => '#10B981',
                'danger' => '#E11D48',
                'warning' => '#F59E0B',
                'info' => '#06B6D4',
                'light' => '#FFF1F2',
                'dark' => '#4C0519',
                'accent' => '#FB923C',
                'background' => '#FFFFFF',
                'surface' => '#FFE4E6',
                'text' => '#1F2937',
                'text_muted' => '#6B7280',
                'border' => '#FECDD3',
            ],
            'lavande' => [
                'name' => '💜 Lavande Douce',
                'primary' => '#7C3AED',
                'secondary' => '#6B7280',
                'success' => '#10B981',
                'danger' => '#EF4444',
                'warning' => '#F59E0B',
                'info' => '#06B6D4',
                'light' => '#F5F3FF',
                'dark' => '#2E1065',
                'accent' => '#A78BFA',
                'background' => '#FEFCFF',
                'surface' => '#EDE9FE',
                'text' => '#1F2937',
                'text_muted' => '#6B7280',
                'border' => '#DDD6FE',
            ],
            'ocean' => [
                'name' => '🌊 Océan Lumineux',
                'primary' => '#0EA5E9',
                'secondary' => '#64748B',
                'success' => '#22C55E',
                'danger' => '#EF4444',
                'warning' => '#EAB308',
                'info' => '#06B6D4',
                'light' => '#F0F9FF',
                'dark' => '#0C4A6E',
                'accent' => '#38BDF8',
                'background' => '#FFFFFF',
                'surface' => '#E0F2FE',
                'text' => '#0F172A',
                'text_muted' => '#64748B',
                'border' => '#BAE6FD',
            ],
        ],

        // =============================================
        // PALETTES DE NUIT (couleurs douces & sombres)
        // =============================================
        'night_palettes' => [
            'indigo' => [
                'name' => '🌙 Indigo Nuit',
                'primary' => '#818CF8',
                'secondary' => '#9CA3AF',
                'success' => '#34D399',
                'danger' => '#F87171',
                'warning' => '#FBBF24',
                'info' => '#22D3EE',
                'light' => '#1F2937',
                'dark' => '#F9FAFB',
                'accent' => '#A78BFA',
                'background' => '#0F172A',
                'surface' => '#1E293B',
                'text' => '#F1F5F9',
                'text_muted' => '#94A3B8',
                'border' => '#334155',
            ],
            'midnight' => [
                'name' => '🌌 Midnight Blue',
                'primary' => '#60A5FA',
                'secondary' => '#94A3B8',
                'success' => '#4ADE80',
                'danger' => '#FB7185',
                'warning' => '#FCD34D',
                'info' => '#67E8F9',
                'light' => '#1E293B',
                'dark' => '#F8FAFC',
                'accent' => '#38BDF8',
                'background' => '#020617',
                'surface' => '#0F172A',
                'text' => '#E2E8F0',
                'text_muted' => '#64748B',
                'border' => '#1E293B',
            ],
            'aurora' => [
                'name' => '🌌 Aurora Boréale',
                'primary' => '#34D399',
                'secondary' => '#94A3B8',
                'success' => '#4ADE80',
                'danger' => '#F87171',
                'warning' => '#FBBF24',
                'info' => '#22D3EE',
                'light' => '#1A2332',
                'dark' => '#F0FDF4',
                'accent' => '#2DD4BF',
                'background' => '#0A1628',
                'surface' => '#132237',
                'text' => '#D1FAE5',
                'text_muted' => '#6EE7B7',
                'border' => '#1E3A4F',
            ],
            'violet' => [
                'name' => '💫 Violet Stellaire',
                'primary' => '#C084FC',
                'secondary' => '#A1A1AA',
                'success' => '#4ADE80',
                'danger' => '#FB7185',
                'warning' => '#FDE68A',
                'info' => '#67E8F9',
                'light' => '#27233A',
                'dark' => '#FAF5FF',
                'accent' => '#E879F9',
                'background' => '#0D0A1A',
                'surface' => '#1C1730',
                'text' => '#F3E8FF',
                'text_muted' => '#A78BFA',
                'border' => '#2E2545',
            ],
            'charbon' => [
                'name' => '🖤 Charbon Élégant',
                'primary' => '#E5E5E5',
                'secondary' => '#A3A3A3',
                'success' => '#86EFAC',
                'danger' => '#FCA5A5',
                'warning' => '#FDE68A',
                'info' => '#A5F3FC',
                'light' => '#262626',
                'dark' => '#FAFAFA',
                'accent' => '#D4D4D8',
                'background' => '#0A0A0A',
                'surface' => '#171717',
                'text' => '#FAFAFA',
                'text_muted' => '#737373',
                'border' => '#262626',
            ],
            'rose' => [
                'name' => '🌹 Rose de Minuit',
                'primary' => '#FB7185',
                'secondary' => '#A1A1AA',
                'success' => '#4ADE80',
                'danger' => '#F87171',
                'warning' => '#FBBF24',
                'info' => '#67E8F9',
                'light' => '#2A1520',
                'dark' => '#FFF1F2',
                'accent' => '#F472B6',
                'background' => '#120810',
                'surface' => '#1F0F18',
                'text' => '#FECDD3',
                'text_muted' => '#FDA4AF',
                'border' => '#3B1A2B',
            ],
        ],
    ],
];