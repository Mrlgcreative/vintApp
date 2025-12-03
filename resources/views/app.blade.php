<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'Vintapp - La marketplace de confiance pour acheter et vendre des articles d\'occasion de qualité')">
    <meta name="keywords" content="@yield('meta_keywords', 'vintapp, marketplace, occasion, vente, achat, articles, vêtements, électronique')">

    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#7c3aed">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="VintApp">
    
    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-512x512.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('images/icons/icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="96x96" href="{{ asset('images/icons/icon-96x96.png') }}">
    <link rel="apple-touch-icon" sizes="128x128" href="{{ asset('images/icons/icon-128x128.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('images/icons/icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('images/icons/icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('images/icons/icon-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="384x384" href="{{ asset('images/icons/icon-384x384.png') }}">
    <link rel="apple-touch-icon" sizes="512x512" href="{{ asset('images/icons/icon-512x512.png') }}">

    <title>@yield('title', '{{ $appName ?? "Vintapp" }}')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset($appFavicon ?? '/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-512x512.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous">

    <!-- Lazy Loading CSS -->
    <link rel="stylesheet" href="{{ asset('css/lazy-loading.css') }}">

    <!-- Variables CSS Dynamiques (DOIT être chargé AVANT Tailwind) -->
    <link href="{{ asset('css/vintapp-dynamic.css') }}?v={{ time() }}" rel="stylesheet">
    
    <!-- Variables CSS Dynamiques Inline (priorité maximale) -->
    <style>
        {!! $activePaletteCSS ?? '' !!}
    </style>

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom Styles -->
    @stack('styles')
    
    <script>
        window.userTheme = "{{ addslashes(Auth::user()?->theme_preference ?? '') }}";
        window.isAuthenticated = {{ Auth::check() ? 'true' : 'false' }};
        
        // Fonction pour appliquer le thème
        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            
            // Gérer la classe dark pour Tailwind
            if (theme === 'dark' || (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            
            console.log('Thème appliqué:', theme);
        }
    </script>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-200">
    
    <!-- Header avec barre de profil -->
    <header class="bg-primary lg:bg-white dark:bg-gray-800 shadow-sm border-b border-primary-700 lg:border-gray-200 dark:border-gray-700 sticky top-0 z-50">
        <div class="flex items-center justify-between px-4 py-3 max-w-7xl lg:mx-auto">
            @auth
                <!-- Profil utilisateur connecté -->
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('profile.index') }}" class="flex items-center space-x-2 hover:opacity-80 transition-opacity">
                            @if(Auth::user()->avatar)
                                @php
                                    $avatarUrl = filter_var(Auth::user()->avatar, FILTER_VALIDATE_URL) 
                                        ? Auth::user()->avatar 
                                        : asset('storage/' . Auth::user()->avatar);
                                @endphp
                                <img src="{{ $avatarUrl }}" 
                                     alt="{{ Auth::user()->name }}" 
                                     class="w-10 h-10 rounded-full object-cover border-2 border-white lg:border-primary-200"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-primary-600 to-accent-400 items-center justify-center text-white font-bold text-sm hidden">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                            @else
                                <div class="w-10 h-10 rounded-full bg-white lg:bg-gradient-to-r lg:from-primary-600 lg:to-accent-400 flex items-center justify-center text-primary-600 lg:text-white font-bold text-sm border-2 border-white lg:border-0">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                            @endif
                            <span class="font-semibold text-white lg:text-gray-800 dark:text-gray-100 text-sm sm:text-base">{{ Auth::user()->name }}</span>
                        </a>
                    </div>
                    
                    <!-- Actions utilisateur connecté -->
                    <div class="flex items-center space-x-2">
                        <!-- Notifications -->
                        <button class="relative p-2.5 hover:bg-primary-700 lg:hover:bg-gray-100 dark:bg-gray-800 rounded-full transition-colors" onclick="toggleNotifications()">
                            <i class="fas fa-bell text-white lg:text-gray-700 dark:text-gray-200 text-lg"></i>
                            @php
                                $unreadNotifications = App\Models\Notification::where('user_id', Auth::id())->whereNull('read_at')->count();
                            @endphp
                            @if($unreadNotifications > 0)
                                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                            @endif
                        </button>
                        
                        <!-- Panier -->
                        <a href="{{ route('cart.index') }}" class="relative p-2.5 hover:bg-primary-700 lg:hover:bg-gray-100 dark:bg-gray-800 rounded-full transition-colors">
                            <i class="fas fa-shopping-cart text-white lg:text-gray-700 dark:text-gray-200 text-lg"></i>
                            @if(session('cart') && count(session('cart')) > 0)
                                <span class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-white lg:bg-primary-600 text-primary-600 lg:text-white text-xs rounded-full flex items-center justify-center font-bold border-2 border-primary-600 lg:border-0">
                                    {{ count(session('cart')) }}
                                </span>
                            @endif
                        </a>
                    </div>
                @else
                    <!-- Logo pour utilisateur non connecté -->
                    <div class="flex items-center space-x-3">
                        <a href="{{ url('/') }}" class="flex items-center space-x-2 hover:opacity-80 transition-opacity">
                            <div class="w-10 h-10 rounded-full bg-white lg:bg-gradient-to-r lg:from-primary-600 lg:to-accent-400 flex items-center justify-center text-primary-600 lg:text-white font-bold text-sm border-2 border-white lg:border-0">
                                <i class="fas fa-home"></i>
                            </div>
                            <span class="font-semibold text-white lg:text-gray-800 dark:text-gray-100 text-sm sm:text-base">{{ config('app.name', 'VintApp') }}</span>
                        </a>
                    </div>
                    
                    <!-- Boutons de connexion -->
                    <!-- <div class="flex items-center space-x-2">
                        <a href="{{ route('login') }}" class="px-4 py-2 text-primary-600 hover:bg-primary-50 rounded-full font-semibold text-sm transition-colors border border-primary-200 hover:border-primary-300">
                            <i class="fas fa-sign-in-alt mr-1"></i>
                            Se connecter
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-primary-600 text-white hover:bg-primary-700 rounded-full font-semibold text-sm transition-colors">
                            <i class="fas fa-user-plus mr-1"></i>
                            S'inscrire
                        </a>
                    </div> -->
                @endauth
        </div>

        <!-- Navigation principale (desktop seulement) -->
        <nav class="bg-primary hidden lg:block">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo et navigation gauche -->
                    <div class="flex items-center space-x-8">
                        <!-- Logo -->
                        <a href="{{ url('/') }}" class="flex items-center space-x-2 text-white hover:opacity-80 transition-opacity">
                            <x-app-brand 
                                :show-logo="true"
                                :show-name="true"
                                logo-height="32px"
                                logo-width="100px"
                                name-size="1.5rem"
                                name-class="text-white"
                                class="flex items-center"
                            />
                        </a>
                        
                        <!-- Navigation links -->
                        <div class="flex items-center space-x-6">
                            <a href="{{ route('dashboard') }}" class="text-white hover:text-primary-200 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary-700' : '' }}">
                                <i class="fas fa-tachometer-alt mr-1"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('items.index') }}" class="text-white hover:text-primary-200 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('items.index') ? 'bg-primary-700' : '' }}">
                                <i class="fas fa-box mr-1"></i>
                                Articles
                            </a>
                            <a href="{{ route('categories.index') }}" class="text-white hover:text-primary-200 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('categories.*') ? 'bg-primary-700' : '' }}">
                                <i class="fas fa-layer-group mr-1"></i>
                                Catégories
                            </a>
                            <a href="{{ route('brands.index') }}" class="text-white hover:text-primary-200 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('brands.*') ? 'bg-primary-700' : '' }}">
                                <i class="fas fa-tags mr-1"></i>
                                Marques
                            </a>
                            
                            @auth
                                <a href="{{ route('items.my-items') }}" class="text-white hover:text-primary-200 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('items.my-items') ? 'bg-primary-700' : '' }}">
                                    <i class="fas fa-list mr-1"></i>
                                    Mes Articles
                                </a>
                                <a href="{{ route('orders.index') }}" class="text-white hover:text-primary-200 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('orders.index') ? 'bg-primary-700' : '' }}">
                                    <i class="fas fa-shopping-cart mr-1"></i>
                                    Commandes
                                </a>
                                <a href="{{ route('wallet.index') }}" class="text-white hover:text-primary-200 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('wallet.*') ? 'bg-primary-700' : '' }}">
                                    <i class="fas fa-wallet mr-1"></i>
                                    Wallet
                                </a>
                            @endauth
                            
                            <a href="{{ route('help.index') }}" class="text-white hover:text-primary-200 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('help.*') ? 'bg-primary-700' : '' }}">
                                <i class="fas fa-question-circle mr-1"></i>
                                Aide
                            </a>
                        </div>
                    </div>
                    
                    <!-- Barre de recherche et menu utilisateur -->
                    <div class="flex items-center space-x-4">
                        <!-- Barre de recherche -->
                        <form class="flex items-center" method="GET" action="{{ route('items.search') }}">
                            <div class="relative">
                                <input type="search" 
                                       name="q" 
                                       placeholder="Rechercher un article..." 
                                       value="{{ request('q') }}"
                                       class="w-80 px-4 py-2 pl-10 pr-4 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-full focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent">
                                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                                <button type="submit" class="absolute right-1 top-1 bottom-1 px-3 bg-primary-500 text-white rounded-full hover:bg-primary-600 transition-colors">
                                    <i class="fas fa-search text-xs"></i>
                                </button>
                            </div>
                        </form>
                        
                        @auth
                            <!-- Menu utilisateur -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center space-x-2 text-white hover:text-primary-200 transition-colors">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ $avatarUrl }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-primary-700 flex items-center justify-center text-white font-bold text-xs">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>
                                
                                <!-- Dropdown menu -->
                                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-50">
                                    <div class="px-4 py-2 text-xs text-gray-500 dark:text-gray-400 border-b">Profil & Paramètres</div>
                                    <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800">
                                        <i class="fas fa-user mr-2"></i> Mon Profil
                                    </a>
                                    <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800">
                                        <i class="fas fa-cog mr-2"></i> Paramètres
                                    </a>
                                    <a href="{{ route('messages.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800">
                                        <i class="fas fa-comments mr-2"></i> Messages
                                    </a>
                                    <a href="{{ route('admin.refunds.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800">
                                        <i class="fas fa-undo mr-2"></i> Remboursements
                                    </a>
                                    <div class="border-t my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:bg-gray-800">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('login') }}" class="text-white hover:text-primary-200 px-3 py-2 text-sm font-medium">
                                    <i class="fas fa-sign-in-alt mr-1"></i> Connexion
                                </a>
                                <a href="{{ route('register') }}" class="bg-white dark:bg-gray-800 text-primary-600 hover:bg-gray-100 dark:bg-gray-800 px-4 py-2 rounded-full text-sm font-medium transition-colors">
                                    <i class="fas fa-user-plus mr-1"></i> S'inscrire
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Fil d'Ariane -->
    @if(!request()->routeIs('welcome'))
        <nav class="bg-gray-100 dark:bg-gray-800 py-2 hidden lg:block">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex items-center space-x-2 text-sm">
                    <a href="{{ url('/') }}" class="text-primary-600 hover:text-primary-800">
                        <i class="fas fa-home mr-1"></i> Accueil
                    </a>
                    @if(request()->routeIs('dashboard'))
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-600 dark:text-gray-300">Dashboard</span>
                    @elseif(request()->routeIs('categories.*'))
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="{{ route('categories.index') }}" class="text-primary-600 hover:text-primary-800">Catégories</a>
                        @if(request()->routeIs('categories.show'))
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-gray-600 dark:text-gray-300">{{ $category->name ?? 'Détails' }}</span>
                        @endif
                    @elseif(request()->routeIs('brands.*'))
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="{{ route('brands.index') }}" class="text-primary-600 hover:text-primary-800">Marques</a>
                        @if(request()->routeIs('brands.show'))
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-gray-600 dark:text-gray-300">{{ $brand->name ?? 'Détails' }}</span>
                        @endif
                    @elseif(request()->routeIs('items.*'))
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="{{ route('items.index') }}" class="text-primary-600 hover:text-primary-800">Articles</a>
                        @if(request()->routeIs('items.show'))
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-gray-600 dark:text-gray-300">{{ $item->name ?? 'Détails' }}</span>
                        @elseif(request()->routeIs('items.my-items'))
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-gray-600 dark:text-gray-300">Mes articles</span>
                        @endif
                    @elseif(request()->routeIs('wallet.*'))
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-600 dark:text-gray-300">Wallet</span>
                    @endif
                </div>
            </div>
        </nav>
    @endif

    <!-- Contenu principal -->
    <main class="flex-1 pb-20 lg:pb-0">
        @yield('content')
    </main>

    <!-- Notifications en temps réel -->
    <x-notifications-realtime />

    <!-- Footer -->
    @if(!request()->routeIs('messages.*'))
        <footer class="bg-gray-800 text-gray-300 py-12 mt-8">
            <div class="max-w-7xl mx-auto px-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <!-- À propos -->
                    <div class="col-span-2 md:col-span-1">
                        <h5 class="font-semibold text-white mb-4">
                            <x-app-brand 
                                :show-logo="true"
                                :show-name="true"
                                logo-height="24px"
                                logo-width="80px"
                                name-size="1.25rem"
                                name-class="text-white"
                            />
                        </h5>
                        <p class="text-sm text-gray-400">
                            {{ $appDescription ?? 'La marketplace de confiance pour acheter et vendre des articles d\'occasion.' }}
                        </p>
                    </div>
                    
                    <!-- Navigation -->
                    <div>
                        <h6 class="font-semibold text-white mb-4">Navigation</h6>
                        <ul class="space-y-2 text-sm">
                            <li><a href="{{ route('items.index') }}" class="hover:text-white transition-colors">Articles</a></li>
                            <li><a href="{{ route('categories.index') }}" class="hover:text-white transition-colors">Catégories</a></li>
                            <li><a href="{{ route('brands.index') }}" class="hover:text-white transition-colors">Marques</a></li>
                            @auth
                                <li><a href="{{ route('items.my-items') }}" class="hover:text-white transition-colors">Mes articles</a></li>
                            @endauth
                        </ul>
                    </div>
                    
                    <!-- Support -->
                    <div>
                        <h6 class="font-semibold text-white mb-4">Support</h6>
                        <ul class="space-y-2 text-sm">
                            <li><a href="{{ route('help.index') }}" class="hover:text-white transition-colors">Centre d'aide</a></li>
                            <li><a href="{{ route('help.index') }}#contact" class="hover:text-white transition-colors">Contact</a></li>
                            <li><a href="{{ route('terms') }}" class="hover:text-white transition-colors">CGU</a></li>
                            <li><a href="{{ route('privacy') }}" class="hover:text-white transition-colors">Confidentialité</a></li>
                        </ul>
                    </div>
                    
                    <!-- Réseaux sociaux -->
                    <div>
                        <h6 class="font-semibold text-white mb-4">Suivez-nous</h6>
                        <div class="flex space-x-3">
                            <a href="https://facebook.com/vintapp" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                                <i class="fab fa-facebook-f text-lg"></i>
                            </a>
                            <a href="https://twitter.com/vintapp" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                                <i class="fab fa-twitter text-lg"></i>
                            </a>
                            <a href="https://instagram.com/vintapp" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                                <i class="fab fa-instagram text-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Newsletter -->
                <div class="border-t border-gray-700 mt-8 pt-8">
                    <div class="text-center max-w-md mx-auto">
                        <h5 class="font-semibold text-white mb-3">📧 Newsletter</h5>
                        <p class="text-sm text-gray-400 mb-4">Recevez nos dernières offres et nouveautés.</p>
                        <form id="newsletterForm" class="flex gap-2">
                            @csrf
                            <input type="email" id="newsletterEmail" 
                                   class="flex-1 px-3 py-2 bg-gray-700 text-white rounded-md border border-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                                   placeholder="Votre email" required>
                            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition-colors">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                        <div id="newsletterMessage" class="mt-2 text-sm"></div>
                    </div>
                </div>
                
                <!-- Copyright -->
                <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                    <p class="text-sm text-gray-400">
                        © {{ date('Y') }} {{ $appName ?? config('app.name', 'VintApp') }}. Tous droits réservés.
                    </p>
                </div>
            </div>
        </footer>
    @endif

    <!-- Navigation mobile (bottom) -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-primary dark:bg-gray-800 border-t border-primary-700 dark:border-gray-700 z-50">
        <div class="grid grid-cols-5 h-16">
            <a href="{{ url('/') }}" class="flex flex-col items-center justify-center text-primary-100 dark:text-gray-400 hover:text-white {{ request()->is('/') ? 'text-white font-bold' : '' }}">
                <i class="fas fa-home text-lg"></i>
                <span class="text-xs mt-1">Accueil</span>
            </a>
            <a href="{{ route('items.create') }}" class="flex flex-col items-center justify-center text-primary-100 dark:text-gray-400 hover:text-white {{ request()->routeIs('items.create') ? 'text-white font-bold' : '' }}">
                <i class="fas fa-plus-circle text-lg"></i>
                <span class="text-xs mt-1">Vendre</span>
            </a>
            <a href="{{ route('items.index') }}" class="flex flex-col items-center justify-center text-primary-100 dark:text-gray-400 hover:text-white {{ request()->routeIs('items.index') ? 'text-white font-bold' : '' }}">
                <i class="fas fa-box text-lg"></i>
                <span class="text-xs mt-1">Articles</span>
            </a>
            @auth
                <a href="{{ route('wallet.index') }}" class="flex flex-col items-center justify-center text-primary-100 dark:text-gray-400 hover:text-white {{ request()->routeIs('wallet.*') ? 'text-white font-bold' : '' }}">
                    <i class="fas fa-wallet text-lg"></i>
                    <span class="text-xs mt-1">Wallet</span>
                </a>
                <a href="{{ route('settings.index') }}" class="flex flex-col items-center justify-center text-primary-100 dark:text-gray-400 hover:text-white {{ request()->routeIs('settings.*') ? 'text-white font-bold' : '' }}">
                    <i class="fas fa-cog text-lg"></i>
                    <span class="text-xs mt-1">Profil</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="flex flex-col items-center justify-center text-primary-100 dark:text-gray-400 hover:text-white">
                    <i class="fas fa-sign-in-alt text-lg"></i>
                    <span class="text-xs mt-1">Connexion</span>
                </a>
                <a href="{{ route('register') }}" class="flex flex-col items-center justify-center text-primary-100 dark:text-gray-400 hover:text-white">
                    <i class="fas fa-user-plus text-lg"></i>
                    <span class="text-xs mt-1">S'inscrire</span>
                </a>
            @endauth
        </div>
    </nav>

    <!-- Scripts -->
    @stack('scripts')

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Network Speed Adapter (doit être chargé en premier) -->
    <script src="{{ asset('js/network-adapter.js') }}?v={{ filemtime(public_path('js/network-adapter.js')) }}"></script>

    <!-- Content Visibility Manager (charger en premier) -->
    <script src="{{ asset('js/content-visibility.js') }}"></script>

    <!-- Page Skeleton Loader -->
    <script src="{{ asset('js/page-skeleton.js') }}"></script>

    <!-- Navigation Skeleton Manager (pour les transitions entre pages) -->
    <script src="{{ asset('js/navigation-skeleton.js') }}"></script>

    <!-- Lazy Loading Manager -->
    <script src="{{ asset('js/lazy-loading.js') }}" defer></script>

    <!-- PWA Manager -->
    <script src="{{ asset('js/pwa.js') }}?v={{ time() }}" defer></script>
    
    <!-- Push Notification Manager -->
    <script type="module" src="{{ asset('js/push-manager.js') }}?v={{ time() }}"></script>
    
    <!-- Background Sync Manager -->
    <script src="{{ asset('js/background-sync.js') }}?v={{ time() }}"></script>

    <!-- Scripts personnalisés -->
    <script>
        // Fonction pour afficher les notifications
        function toggleNotifications() {
            if (!window.isAuthenticated) {
                window.location.href = '{{ route("login") }}';
                return;
            }
            
            const existingPanel = document.getElementById('notifications-panel');
            
            if (existingPanel) {
                existingPanel.remove();
                return;
            }
            
            const panel = document.createElement('div');
            panel.id = 'notifications-panel';
            panel.className = 'fixed top-20 right-4 w-80 max-w-[calc(100vw-2rem)] bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50 max-h-96 overflow-y-auto';
            panel.innerHTML = `
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100">Notifications</h3>
                    <button onclick="this.closest('#notifications-panel').remove()" class="text-gray-400 hover:text-gray-600 dark:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-4" id="notifications-content">
                    <p class="text-gray-500 dark:text-gray-400 text-sm text-center">Chargement...</p>
                </div>
            `;
            
            document.body.appendChild(panel);
            loadNotifications();
            
            // Fermer en cliquant à l'extérieur
            setTimeout(() => {
                document.addEventListener('click', function closePanel(e) {
                    if (!panel.contains(e.target) && !e.target.closest('[onclick*="toggleNotifications"]')) {
                        panel.remove();
                        document.removeEventListener('click', closePanel);
                    }
                });
            }, 100);
        }

        @auth
        function loadNotifications() {
            const content = document.getElementById('notifications-content');
            if (!content) return;
            
            fetch('/notifications')
                .then(response => response.json())
                .then(data => {
                    if (data.notifications.length === 0) {
                        content.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-sm text-center">Aucune notification</p>';
                        return;
                    }
                    
                    content.innerHTML = data.notifications.map(notification => `
                        <div class="p-3 border-b border-gray-100 hover:bg-gray-50 dark:bg-gray-900 cursor-pointer" 
                             onclick="markNotificationAsRead(${notification.id}, '${notification.data?.url || '#'}')">
                            <div class="flex items-start space-x-3">
                                <i class="fas ${getNotificationIcon(notification.type)} text-primary-600 mt-1"></i>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-800 dark:text-gray-100 text-sm">${notification.title}</div>
                                    <div class="text-gray-600 dark:text-gray-300 text-xs mt-1">${notification.message}</div>
                                    <div class="text-gray-400 text-xs mt-1">${formatDate(notification.created_at)}</div>
                                </div>
                                ${!notification.read_at ? '<div class="w-2 h-2 bg-red-500 rounded-full"></div>' : ''}
                            </div>
                        </div>
                    `).join('');
                })
                .catch(error => {
                    content.innerHTML = '<p class="text-red-500 text-sm text-center">Erreur de chargement</p>';
                });
        }

        function getNotificationIcon(type) {
            const icons = {
                'new_message': 'fa-comment',
                'new_order': 'fa-shopping-cart',
                'discount_applied': 'fa-percentage',
                'item_favorited': 'fa-heart'
            };
            return icons[type] || 'fa-bell';
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diff = now - date;
            
            if (diff < 60000) return 'À l\'instant';
            if (diff < 3600000) return Math.floor(diff / 60000) + ' min';
            if (diff < 86400000) return Math.floor(diff / 3600000) + ' h';
            return Math.floor(diff / 86400000) + ' j';
        }

        function markNotificationAsRead(notificationId, url) {
            fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(() => {
                if (url && url !== '#') {
                    window.location.href = url;
                }
            });
        }
        @else
        function loadNotifications() {
            const content = document.getElementById('notifications-content');
            if (content) {
                content.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-sm text-center">Connectez-vous pour voir vos notifications</p>';
            }
        }
        @endauth

        // Newsletter
        document.getElementById('newsletterForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('newsletterEmail').value;
            const messageDiv = document.getElementById('newsletterMessage');
            
            fetch('/newsletter/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageDiv.innerHTML = '<p class="text-green-500">✅ Inscription réussie !</p>';
                    document.getElementById('newsletterEmail').value = '';
                } else {
                    messageDiv.innerHTML = '<p class="text-red-500">❌ Erreur lors de l\'inscription</p>';
                }
            })
            .catch(error => {
                messageDiv.innerHTML = '<p class="text-red-500">❌ Erreur lors de l\'inscription</p>';
            });
        });

        // Thème
        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            
            // Gérer la classe dark pour Tailwind
            if (theme === 'dark' || (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }

        function getPreferredTheme() {
            return localStorage.getItem('theme') || window.userTheme || 'auto';
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 VintApp chargé avec Tailwind CSS');
            const theme = getPreferredTheme();
            applyTheme(theme);
            
            // Écouter les changements de préférences système pour le mode auto
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                if (getPreferredTheme() === 'auto') {
                    applyTheme('auto');
                }
            });
            
            @auth
            // Initialiser les notifications push Firebase
            initFirebasePushNotifications();
            @endauth
        });
    </script>

    @auth
    <!-- Firebase SDK pour notifications push -->
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js"></script>
    
    <script>
        // Configuration Firebase
        const firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key') }}",
            authDomain: "{{ config('services.firebase.auth_domain') }}",
            projectId: "{{ config('services.firebase.project_id') }}",
            storageBucket: "{{ config('services.firebase.storage_bucket') }}",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
            appId: "{{ config('services.firebase.app_id') }}"
        };

        // Initialiser Firebase
        const firebaseApp = firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        // VAPID Key pour les notifications push web
        const vapidKey = "{{ config('services.firebase.vapid_key') }}";

        async function initFirebasePushNotifications() {
            try {
                console.log('📱 Initialisation des notifications push...');

                // Vérifier si le navigateur supporte les notifications
                if (!('Notification' in window)) {
                    console.log('⚠️ Ce navigateur ne supporte pas les notifications');
                    return;
                }

                // Vérifier si le Service Worker est supporté
                if (!('serviceWorker' in navigator)) {
                    console.log('⚠️ Service Worker non supporté');
                    return;
                }

                // Enregistrer le Service Worker et attendre qu'il soit actif
                let registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
                console.log('✅ Service Worker enregistré:', registration);

                // Attendre que le Service Worker soit actif
                if (registration.installing) {
                    console.log('⏳ Service Worker en cours d\'installation...');
                    await new Promise((resolve) => {
                        registration.installing.addEventListener('statechange', (e) => {
                            if (e.target.state === 'activated') {
                                resolve();
                            }
                        });
                    });
                } else if (registration.waiting) {
                    console.log('⏳ Service Worker en attente...');
                    await navigator.serviceWorker.ready;
                } else if (!registration.active) {
                    console.log('⏳ Attente activation Service Worker...');
                    await navigator.serviceWorker.ready;
                }

                // S'assurer qu'on a bien un Service Worker actif
                registration = await navigator.serviceWorker.ready;
                console.log('✅ Service Worker actif et prêt');

                // Demander la permission de notification
                const permission = await requestNotificationPermission();
                
                if (permission === 'granted') {
                    // Récupérer le token FCM
                    const currentToken = await messaging.getToken({
                        vapidKey: vapidKey,
                        serviceWorkerRegistration: registration
                    });

                    if (currentToken) {
                        console.log('✅ Token FCM obtenu:', currentToken);
                        await saveFCMToken(currentToken);
                    } else {
                        console.log('⚠️ Aucun token FCM disponible');
                    }

                    // Écouter les messages en premier plan (app ouverte)
                    messaging.onMessage((payload) => {
                        console.log('📬 Message reçu en premier plan:', payload);
                        displayForegroundNotification(payload);
                    });
                } else {
                    console.log('❌ Permission de notification refusée');
                }

            } catch (error) {
                console.error('❌ Erreur initialisation FCM:', error);
            }
        }

        async function requestNotificationPermission() {
            try {
                const permission = await Notification.requestPermission();
                console.log('🔔 Permission notifications:', permission);
                return permission;
            } catch (error) {
                console.error('Erreur demande permission:', error);
                return 'denied';
            }
        }

        async function saveFCMToken(token) {
            try {
                const response = await fetch('/api/fcm-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ 
                        token: token,
                        device_type: /iPhone|iPad|iPod|Android/i.test(navigator.userAgent) ? 'mobile' : 'desktop'
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    console.log('✅ Token FCM enregistré sur le serveur');
                    localStorage.setItem('fcm_token', token);
                } else {
                    console.error('❌ Erreur enregistrement token:', data.message);
                }
            } catch (error) {
                console.error('❌ Erreur sauvegarde token:', error);
            }
        }

        function displayForegroundNotification(payload) {
            const title = payload.notification?.title || payload.data?.title || 'VintApp';
            const options = {
                body: payload.notification?.body || payload.data?.body || 'Nouvelle notification',
                icon: payload.notification?.icon || payload.data?.icon || '/images/icons/icon-192x192.png',
                badge: '/images/icons/icon-96x96.png',
                vibrate: [200, 100, 200],
                data: payload.data || {},
                requireInteraction: false
            };

            // Afficher notification système (si permission accordée)
            if (Notification.permission === 'granted') {
                const notification = new Notification(title, options);
                
                notification.onclick = function(event) {
                    event.preventDefault();
                    const url = payload.data?.url || payload.fcmOptions?.link || '/';
                    window.open(url, '_blank');
                    notification.close();
                };
            }

            // Aussi afficher le toast dans l'app
            if (typeof showNotification === 'function' && payload.data) {
                showNotification(payload.data);
            }
        }

        // Rafraîchir le token périodiquement (toutes les 24h)
        setInterval(async () => {
            try {
                const currentToken = await messaging.getToken({ vapidKey: vapidKey });
                const savedToken = localStorage.getItem('fcm_token');
                
                if (currentToken && currentToken !== savedToken) {
                    console.log('🔄 Token FCM mis à jour');
                    await saveFCMToken(currentToken);
                }
            } catch (error) {
                console.error('Erreur rafraîchissement token:', error);
            }
        }, 24 * 60 * 60 * 1000); // 24 heures
    </script>
    @endauth
</body>
</html>
