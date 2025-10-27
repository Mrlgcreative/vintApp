<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Administration {{ $appName ?? 'VintApp' }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset($appFavicon ?? '/favicon.ico') }}">
    
    <!-- CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    
    <!-- Custom Admin Styles -->
    <link href="{{ asset('css/admin-components.css') }}" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom Page Styles -->
    @stack('styles')
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0f4ff',
                            100: '#e5edff',
                            200: '#d0ddff',
                            300: '#adc0ff',
                            400: '#8499ff',
                            500: '#6366f1',
                            600: '#5855eb',
                            700: '#4c44d8',
                            800: '#3e37af',
                            900: '#36318a',
                        },
                        dark: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-in-out',
                        'slide-in-right': 'slideInRight 0.3s ease-out',
                        'pulse-slow': 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        slideInRight: {
                            '0%': { transform: 'translateX(30px)', opacity: '0' },
                            '100%': { transform: 'translateX(0)', opacity: '1' }
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Styles complémentaires pour les composants -->
    <style>
        /* Personnalisation des composants externes avec Tailwind */
        .select2-container--default .select2-selection--single {
            @apply border border-gray-300 rounded-lg focus:border-primary-500 focus:ring-2 focus:ring-primary-200;
        }
        
        .flatpickr-input {
            @apply border border-gray-300 rounded-lg px-3 py-2 focus:border-primary-500 focus:ring-2 focus:ring-primary-200;
        }

        /* Badge personnalisé pour les notifications */
        .notification-dot {
            @apply absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center;
        }

        /* Transitions fluides */
        .transition-all {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Scrollbar personnalisé */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            @apply bg-transparent;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            @apply bg-white bg-opacity-20 rounded-full;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            @apply bg-white bg-opacity-30;
        }

        /* Sidebar responsive */
        @media (max-width: 1023px) {
            #sidebar {
                transform: translateX(-100%);
            }
            #sidebar.active {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 font-sans text-sm leading-relaxed text-gray-900">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <nav class="fixed left-0 top-0 z-50 h-screen w-72 bg-gradient-to-b from-dark-800 to-dark-900 shadow-2xl transition-transform duration-300 ease-in-out" id="sidebar">
            <div class="flex h-full flex-col">
                <!-- Brand -->
                <div class="relative border-b border-white/10 bg-white/5 p-6">
                    <x-app-brand 
                        :show-logo="true"
                        :show-name="true"
                        logo-height="30px"
                        logo-width="100px"
                        name-size="1.25rem"
                        name-class="text-white font-bold"
                    />
                    <div class="absolute bottom-0 left-6 right-6 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 space-y-1 p-4 custom-scrollbar overflow-y-auto">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-white/10 hover:text-white/90 @if(request()->routeIs('admin.dashboard')) bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 @endif">
                        <i class="fas fa-tachometer-alt w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                        <span>Tableau de bord</span>
                    </a>

                    <a href="{{ route('admin.users.index') }}" 
                       class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-white/10 hover:text-white/90 @if(request()->routeIs('admin.users.index') || request()->routeIs('admin.users.show') || request()->routeIs('admin.users.edit')) bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 @endif">
                        <i class="fas fa-users w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                        <span class="flex-1">Utilisateurs</span>
                        @if(isset($pendingUsersCount) && $pendingUsersCount > 0)
                            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                                {{ $pendingUsersCount }}
                            </span>
                        @endif
                    </a>

                    <!-- 🆕 Menu Utilisateurs Connectés -->
                    <a href="{{ route('admin.users.online') }}" 
                       class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-white/10 hover:text-white/90 @if(request()->routeIs('admin.users.online')) bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 @endif">
                        <i class="fas fa-user-check w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                        <span class="flex-1">Utilisateurs Connectés</span>
                        <span class="inline-flex items-center justify-center w-3 h-3 bg-green-400 rounded-full animate-pulse shadow-lg shadow-green-400/50"></span>
                    </a>

                    <a href="{{ route('admin.transactions.index') }}" 
                       class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-white/10 hover:text-white/90 @if(request()->routeIs('admin.transactions.*')) bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 @endif">
                        <i class="fas fa-exchange-alt w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                        <span>Transactions</span>
                    </a>

                    <a href="{{ route('admin.wallets.pending') }}" 
                       class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-white/10 hover:text-white/90 @if(request()->routeIs('admin.wallets.pending')) bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 @endif">
                        <i class="fas fa-clock w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                        <span class="flex-1">Wallets en attente</span>
                        @if(isset($pendingWalletsCount) && $pendingWalletsCount > 0)
                            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-gray-800 bg-yellow-400 rounded-full">
                                {{ $pendingWalletsCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('admin.orders.index') }}" 
                       class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-white/10 hover:text-white/90 @if(request()->routeIs('admin.orders.*') && !request()->routeIs('admin.orders.tracking')) bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 @endif">
                        <i class="fas fa-shopping-cart w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                        <span>Commandes</span>
                    </a>

                    <!-- 🆕 Menu Traçage GPS -->
                    <a href="{{ route('admin.orders.tracking.list') }}" 
                       class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-white/10 hover:text-white/90 @if(request()->routeIs('admin.orders.tracking*')) bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 @endif">
                        <i class="fas fa-map-marker-alt w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                        <span class="flex-1">Traçage GPS</span>
                        <span class="inline-flex items-center justify-center w-3 h-3 bg-purple-400 rounded-full animate-pulse shadow-lg shadow-purple-400/50"></span>
                    </a>

                    <a href="{{ route('admin.brands.index') }}" 
                       class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-white/10 hover:text-white/90 @if(request()->routeIs('admin.brands.*')) bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 @endif">
                        <i class="fas fa-tags w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                        <span>Marques</span>
                    </a>

                    <a href="{{ route('admin.categories.index') }}" 
                       class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-white/10 hover:text-white/90 @if(request()->routeIs('admin.categories.*')) bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 @endif">
                        <i class="fas fa-list w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                        <span>Catégories</span>
                    </a>

                    <a href="{{ route('admin.support.index') }}" 
                       class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-white/10 hover:text-white/90 @if(request()->routeIs('admin.support.*')) bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 @endif">
                        <i class="fas fa-headset w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                        <span class="flex-1">Support Client</span>
                        @php
                            $unassignedSupport = \App\Models\SupportChat::whereNull('admin_id')
                                ->whereIn('status', ['open', 'in_progress'])->count();
                        @endphp
                        @if($unassignedSupport > 0)
                            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-orange-500 rounded-full">
                                {{ $unassignedSupport }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('admin.reports') }}" 
                       class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-white/10 hover:text-white/90 @if(request()->routeIs('admin.reports')) bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 @endif">
                        <i class="fas fa-chart-bar w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                        <span>Rapports</span>
                    </a>

                    <a href="{{ route('admin.logs') }}" 
                       class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-white/10 hover:text-white/90 @if(request()->routeIs('admin.logs')) bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 @endif">
                        <i class="fas fa-list-alt w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                        <span>Logs système</span>
                    </a>

                    <a href="{{ route('admin.settings.index') }}" 
                       class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-white/10 hover:text-white/90 @if(request()->routeIs('admin.settings.*') && !request()->routeIs('admin.locations.*')) bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 @endif">
                        <i class="fas fa-cog w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                        <span>Paramètres</span>
                    </a>

                    <a href="{{ route('admin.locations.index') }}" 
                       class="group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-300 hover:translate-x-1 hover:bg-white/10 hover:text-white/90 @if(request()->routeIs('admin.locations.*')) bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold shadow-lg translate-x-1 @endif">
                        <i class="fas fa-map-marked-alt w-5 text-center mr-3 transition-transform group-hover:scale-110"></i>
                        <span>Zones autorisées</span>
                    </a>
                </nav>

                <!-- Footer -->
                <div class="mt-auto p-4 space-y-2">
                    <a href="{{ route('home') }}" 
                       class="flex w-full items-center justify-center rounded-xl border border-white/20 bg-transparent px-4 py-3 text-white/80 transition-all duration-300 hover:bg-white/10 hover:text-white">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Retour au site
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" 
                                class="flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-red-500 to-red-600 px-4 py-3 text-white transition-all duration-300 hover:from-red-600 hover:to-red-700 hover:shadow-lg">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Overlay pour mobile -->
        <div class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm transition-opacity duration-300 lg:hidden" id="sidebar-overlay" style="display: none; opacity: 0;"></div>

        <!-- Contenu principal -->
        <main class="flex-1 transition-all duration-300" id="main-content">
            <!-- Header -->
            <header class="sticky top-0 z-30 border-b border-gray-200 bg-white/95 p-4 shadow-sm backdrop-blur-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Toggle Button -->
                        <button class="rounded-lg p-2 text-gray-600 transition-all duration-300 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500" 
                                id="sidebar-toggle"
                                aria-label="Toggle sidebar">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="text-xl font-bold text-gray-900 lg:text-2xl">@yield('page-title')</h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative">
                            <button class="relative rounded-lg p-2 text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900" 
                                    type="button" id="notificationsDropdown">
                                <i class="fas fa-bell text-lg"></i>
                                <span class="notification-dot hidden" id="notification-badge">0</span>
                            </button>
                            
                            <!-- Dropdown notifications -->
                            <div class="absolute right-0 top-full mt-2 hidden w-80 origin-top-right rounded-xl bg-white shadow-xl ring-1 ring-black/5" 
                                 id="notifications-dropdown">
                                <div class="p-4">
                                    <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                                </div>
                                <div class="max-h-96 overflow-y-auto" id="notifications-container">
                                    <!-- Les notifications seront injectées ici -->
                                </div>
                                <div class="border-t border-gray-100 p-4">
                                    <a href="/admin/notifications" 
                                       class="block text-center text-sm font-medium text-primary-600 hover:text-primary-700">
                                        Voir toutes les notifications
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Profil -->
                        <div class="relative">
                            <button class="flex items-center rounded-lg p-2 text-gray-600 transition-colors hover:bg-gray-100" 
                                    type="button" id="userDropdown">
                                @if(auth()->user()->avatar)
                                    <img src="{{ auth()->user()->avatar_url ?? '/images/default-avatar.png' }}" 
                                         alt="Avatar" class="h-8 w-8 rounded-full mr-2">
                                @else
                                    <div class="mr-2 flex h-8 w-8 items-center justify-center rounded-full bg-primary-500 text-white text-sm font-semibold">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                @endif
                                <span class="hidden text-sm font-medium text-gray-900 lg:block">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down ml-2 text-xs text-gray-400"></i>
                            </button>
                            
                            <!-- Dropdown profil -->
                            <div class="absolute right-0 top-full mt-2 hidden w-48 origin-top-right rounded-xl bg-white shadow-xl ring-1 ring-black/5" 
                                 id="user-dropdown">
                                <div class="p-1">
                                    <a href="{{ route('profile.edit') }}" 
                                       class="flex items-center rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-user mr-3 w-4 text-center text-gray-400"></i>
                                        Mon profil
                                    </a>
                                    <div class="my-1 h-px bg-gray-100"></div>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="flex w-full items-center rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <i class="fas fa-sign-out-alt mr-3 w-4 text-center"></i>
                                            Déconnexion
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Actions de page -->
            @hasSection('page-actions')
                <div class="p-4 border-b border-gray-200">
                    @yield('page-actions')
                </div>
            @endif

            <!-- Alertes -->
            <div class="p-4 space-y-4">
                @if(session('success'))
                    <div class="flex items-center rounded-xl bg-green-50 p-4 text-green-800 animate-fade-in" role="alert">
                        <i class="fas fa-check-circle mr-3 text-green-500"></i>
                        <span class="flex-1">{{ session('success') }}</span>
                        <button type="button" class="ml-4 text-green-500 hover:text-green-700" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="flex items-center rounded-xl bg-red-50 p-4 text-red-800 animate-fade-in" role="alert">
                        <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
                        <span class="flex-1">{{ session('error') }}</span>
                        <button type="button" class="ml-4 text-red-500 hover:text-red-700" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="flex items-center rounded-xl bg-yellow-50 p-4 text-yellow-800 animate-fade-in" role="alert">
                        <i class="fas fa-exclamation-triangle mr-3 text-yellow-500"></i>
                        <span class="flex-1">{{ session('warning') }}</span>
                        <button type="button" class="ml-4 text-yellow-500 hover:text-yellow-700" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Contenu principal -->
            <div class="flex-1 p-4 lg:p-8">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>

    <script>
        // Attendre que le DOM et jQuery soient complètement chargés
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Admin layout JavaScript chargé');

            // Initialisation des composants jQuery
            if (typeof $ !== 'undefined') {
                // Select2
                $('.select2').select2();

                // Flatpickr (datepicker)
                flatpickr(".datepicker", {
                    locale: "fr",
                    dateFormat: "Y-m-d",
                    allowInput: true
                });

                // Flatpickr (datetimepicker)
                flatpickr(".datetimepicker", {
                    locale: "fr",
                    dateFormat: "Y-m-d H:i",
                    enableTime: true,
                    time_24hr: true,
                    allowInput: true
                });
            }

            // Sidebar Toggle - Gestion responsive améliorée
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            const mainContent = document.getElementById('main-content');
            
            console.log('🔍 Éléments sidebar:', {
                toggle: !!sidebarToggle,
                sidebar: !!sidebar,
                overlay: !!sidebarOverlay,
                mainContent: !!mainContent
            });
            
            // État initial basé sur la taille de l'écran
            function initSidebar() {
                if (window.innerWidth >= 1024) {
                    // Desktop: sidebar visible, margin sur le contenu
                    sidebar.classList.remove('active');
                    sidebar.style.transform = 'translateX(0)';
                    mainContent.style.marginLeft = '288px'; // 18rem = 288px
                    if (sidebarOverlay) {
                        sidebarOverlay.style.display = 'none';
                        sidebarOverlay.style.opacity = '0';
                    }
                } else {
                    // Mobile: sidebar cachée
                    sidebar.classList.remove('active');
                    sidebar.style.transform = 'translateX(-100%)';
                    mainContent.style.marginLeft = '0';
                    if (sidebarOverlay) {
                        sidebarOverlay.style.display = 'none';
                        sidebarOverlay.style.opacity = '0';
                    }
                }
            }

            // Initialiser au chargement
            initSidebar();

            // Réinitialiser lors du redimensionnement
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (!sidebar.classList.contains('active')) {
                        initSidebar();
                    }
                }, 250);
            });

            // Toggle du sidebar
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    console.log('🔘 Sidebar toggle cliqué');
                    const isActive = sidebar.classList.toggle('active');
                    
                    if (window.innerWidth >= 1024) {
                        // Desktop: toggle avec animation
                        if (isActive) {
                            sidebar.style.transform = 'translateX(-100%)';
                            mainContent.style.marginLeft = '0';
                        } else {
                            sidebar.style.transform = 'translateX(0)';
                            mainContent.style.marginLeft = '288px';
                        }
                    } else {
                        // Mobile: toggle avec overlay
                        if (isActive) {
                            sidebar.style.transform = 'translateX(0)';
                            if (sidebarOverlay) {
                                sidebarOverlay.style.display = 'block';
                                setTimeout(() => {
                                    sidebarOverlay.style.opacity = '1';
                                }, 10);
                            }
                        } else {
                            sidebar.style.transform = 'translateX(-100%)';
                            if (sidebarOverlay) {
                                sidebarOverlay.style.opacity = '0';
                                setTimeout(() => {
                                    sidebarOverlay.style.display = 'none';
                                }, 300);
                            }
                        }
                    }
                });
            } else {
                console.error('❌ Bouton sidebar-toggle non trouvé');
            }

            // Fermer le sidebar sur clic overlay (mobile uniquement)
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    console.log('🔘 Overlay cliqué');
                    sidebar.classList.remove('active');
                    sidebar.style.transform = 'translateX(-100%)';
                    sidebarOverlay.style.opacity = '0';
                    setTimeout(() => {
                        sidebarOverlay.style.display = 'none';
                    }, 300);
                });
            }

            // Fermer le sidebar en cliquant sur un lien (mobile uniquement)
            const sidebarLinks = sidebar.querySelectorAll('a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 1024) {
                        sidebar.classList.remove('active');
                        sidebar.style.transform = 'translateX(-100%)';
                        if (sidebarOverlay) {
                            sidebarOverlay.style.opacity = '0';
                            setTimeout(() => {
                                sidebarOverlay.style.display = 'none';
                            }, 300);
                        }
                    }
                });
            });

            // Dropdowns
            const notificationsDropdown = document.getElementById('notificationsDropdown');
            const notificationsDropdownMenu = document.getElementById('notifications-dropdown');
            const userDropdown = document.getElementById('userDropdown');
            const userDropdownMenu = document.getElementById('user-dropdown');

            console.log('🔍 Éléments dropdowns:', {
                notificationsDropdown: !!notificationsDropdown,
                notificationsDropdownMenu: !!notificationsDropdownMenu,
                userDropdown: !!userDropdown,
                userDropdownMenu: !!userDropdownMenu
            });

            // Toggle notifications dropdown
            if (notificationsDropdown && notificationsDropdownMenu) {
                notificationsDropdown.addEventListener('click', function(e) {
                    console.log('🔔 Dropdown notifications cliqué');
                    e.stopPropagation();
                    notificationsDropdownMenu.classList.toggle('hidden');
                    userDropdownMenu.classList.add('hidden');
                });
            } else {
                console.error('❌ Dropdown notifications non trouvé');
            }

            // Toggle user dropdown
            if (userDropdown && userDropdownMenu) {
                userDropdown.addEventListener('click', function(e) {
                    console.log('👤 Dropdown profil cliqué');
                    e.stopPropagation();
                    userDropdownMenu.classList.toggle('hidden');
                    notificationsDropdownMenu.classList.add('hidden');
                });
            } else {
                console.error('❌ Dropdown profil non trouvé');
            }

            // Fermer les dropdowns en cliquant ailleurs
            document.addEventListener('click', function() {
                if (notificationsDropdownMenu) notificationsDropdownMenu.classList.add('hidden');
                if (userDropdownMenu) userDropdownMenu.classList.add('hidden');
            });
        });

        // Gestion des notifications
        function fetchNotifications() {
            if (typeof $ === 'undefined') {
                console.error('❌ jQuery non chargé');
                return;
            }

            $.get('/admin/notifications', function(data) {
                const badge = document.getElementById('notification-badge');
                
                // Mise à jour du badge
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }

                // Mise à jour du conteneur de notifications
                let notificationsHtml = '';
                if (data.notifications.length > 0) {
                    data.notifications.forEach(notification => {
                        notificationsHtml += `
                            <a href="${notification.link}" class="block px-4 py-3 hover:bg-gray-50 ${!notification.read_at ? 'bg-blue-50' : ''}">
                                <div class="flex items-center">
                                    <i class="fas ${notification.icon} mr-3 text-gray-400"></i>
                                    <div class="flex-1">
                                        <div class="text-xs text-gray-500">${notification.created_at}</div>
                                        <div class="text-sm text-gray-900">${notification.message}</div>
                                    </div>
                                </div>
                            </a>
                        `;
                    });
                } else {
                    notificationsHtml = '<div class="px-4 py-8 text-center text-sm text-gray-500">Aucune notification</div>';
                }
                
                const container = document.getElementById('notifications-container');
                if (container) {
                    container.innerHTML = notificationsHtml;
                }
            }).fail(function() {
                // En cas d'erreur, masquer le badge
                const badge = document.getElementById('notification-badge');
                if (badge) badge.classList.add('hidden');
            });
        }

        // Rafraîchir les notifications toutes les 30 secondes
        if (typeof $ !== 'undefined') {
            fetchNotifications();
            setInterval(fetchNotifications, 30000);

            // Protection CSRF pour les requêtes AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        }
    </script>

    <!-- Admin Utils JavaScript -->
    <script src="{{ asset('js/admin-utils.js') }}"></script>

    <!-- Performance Monitor (Development only) -->
    @if(config('app.env') === 'local' || config('app.debug'))
    <script src="{{ asset('js/performance-monitor.js') }}"></script>
    @endif

    @stack('scripts')
</body>
</html>