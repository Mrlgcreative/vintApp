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
    <meta name="theme-color" content="#1a1a1a">
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
    <link rel="icon" type="image/png" href="{{ asset($appFavicon ?? '/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-512x512.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous">

    <!-- Lazy Loading CSS -->
    <link rel="stylesheet" href="{{ asset('css/lazy-loading.css') }}">

    <!-- Splash Screen CSS -->
    <link rel="stylesheet" href="{{ asset('css/splash-screen.css') }}">

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Color Palette Variables (loaded AFTER Vite to override default colors) -->
    @production
        <link rel="stylesheet" href="{{ asset('css/dynamic-colors.css') }}?v={{ filemtime(public_path('css/dynamic-colors.css')) }}">
    @else
        {{-- In dev mode, Vite injects CSS via JS (after synchronous link tags), so we inject dynamic-colors after DOMContentLoaded to ensure correct cascade order --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = '{{ asset('css/dynamic-colors.css') }}?v={{ filemtime(public_path('css/dynamic-colors.css')) }}';
                document.head.appendChild(link);
            });
        </script>
    @endproduction

    <!-- Day/Night Theme (système automatique jour/nuit) -->
    @if(config('colors.day_night.enabled', false))
        @production
            <link rel="stylesheet" href="{{ asset('css/day-night-theme.css') }}?v={{ filemtime(public_path('css/day-night-theme.css')) }}">
        @else
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = '{{ asset('css/day-night-theme.css') }}?v={{ filemtime(public_path('css/day-night-theme.css')) }}';
                    document.head.appendChild(link);
                });
            </script>
        @endproduction
    @endif

    <!-- Custom Styles -->
    @stack('styles')
    
    <script>
        window.userTheme = "{{ addslashes(Auth::user()?->theme_preference ?? '') }}";
        window.isAuthenticated = {{ Auth::check() ? 'true' : 'false' }};

        // Configuration jour/nuit multi-palettes (injectée côté serveur)
        @php
            $dayNightService = app(\App\Services\DayNightService::class);
            $dayNightClientConfig = $dayNightService->getClientConfig();
        @endphp
        window.VintAppDayNightConfig = @json($dayNightClientConfig);
        
        // Fonction pour appliquer le thème
        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            
            // Gérer la classe dark pour Tailwind
            if (theme === 'dark' || theme === 'night' || (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }

        // Appliquer immédiatement la préférence utilisateur (avant day-night.js)
        if (window.userTheme) {
            applyTheme(window.userTheme);
        }
    </script>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-200">
    
    <!-- Header avec barre de profil -->
    <header class="bg-gray-900 lg:bg-white dark:bg-gray-800/95 dark:backdrop-blur-md shadow-sm border-b border-gray-800 lg:border-gray-200 dark:border-gray-700/50 sticky top-0 z-50 transition-colors duration-300">
        <div class="flex items-center justify-between px-4 py-2.5 max-w-7xl lg:mx-auto">
            @auth
                <!-- Profil utilisateur connecté -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('profile.index') }}" class="group flex items-center gap-2.5" aria-label="Mon profil">
                        @if(Auth::user()->avatar)
                            @php
                                $avatarUrl = filter_var(Auth::user()->avatar, FILTER_VALIDATE_URL) 
                                    ? Auth::user()->avatar 
                                    : asset('storage/' . Auth::user()->avatar);
                            @endphp
                            <img src="{{ $avatarUrl }}" 
                                 alt="{{ Auth::user()->name }}" 
                                 class="w-9 h-9 rounded-full object-cover ring-2 ring-white/80 lg:ring-gray-300 group-hover:ring-white lg:group-hover:ring-gray-400 transition-all duration-200"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-9 h-9 rounded-full bg-gray-700 items-center justify-center text-white font-bold text-xs hidden shadow-inner">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                        @else
                            <div class="w-9 h-9 rounded-full bg-white/90 lg:bg-gray-800 flex items-center justify-center text-gray-800 lg:text-white font-bold text-xs ring-2 ring-white/60 lg:ring-0 shadow-inner">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                        @endif
                        <span class="font-semibold text-white lg:text-gray-800 dark:text-gray-100 text-sm truncate max-w-[140px] group-hover:opacity-80 transition-opacity">{{ Auth::user()->name }}</span>
                    </a>
                </div>
                
                <!-- Actions utilisateur connecté -->
                <div class="flex items-center gap-1">
                    <!-- Theme Toggle -->
                    <button id="headerThemeToggle" onclick="toggleHeaderTheme()" class="p-2 hover:bg-white/10 lg:hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-all duration-200 active:scale-95" aria-label="Changer le theme">
                        <svg id="headerThemeIcon" class="w-5 h-5 text-white lg:text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>

                    <!-- Notifications -->
                    <button class="relative p-2 hover:bg-white/10 lg:hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-all duration-200 active:scale-95" onclick="toggleNotifications()" aria-label="Notifications">
                        <svg class="w-5 h-5 text-white lg:text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                        </svg>
                        @php
                            $unreadNotifications = App\Models\Notification::where('user_id', Auth::id())->whereNull('read_at')->count();
                        @endphp
                        @if($unreadNotifications > 0)
                            <span id="notification-badge" class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] rounded-full flex items-center justify-center font-bold ring-2 ring-primary lg:ring-white dark:ring-gray-800 shadow-lg shadow-red-500/30">
                                {{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}
                            </span>
                        @else
                            <span id="notification-badge" class="hidden absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] rounded-full flex items-center justify-center font-bold ring-2 ring-primary lg:ring-white dark:ring-gray-800 shadow-lg shadow-red-500/30">0</span>
                        @endif
                    </button>
                    
                    <!-- Panier -->
                    <a href="{{ route('cart.index') }}" class="relative p-2 hover:bg-white/10 lg:hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-all duration-200 active:scale-95" aria-label="Panier">
                        <svg class="w-5 h-5 text-white lg:text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                        </svg>
                        @php $cCount = cart_count(); @endphp
                        @if($cCount > 0)
                            <span id="cart-badge" class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] rounded-full flex items-center justify-center font-bold ring-2 ring-primary lg:ring-white dark:ring-gray-800">
                                {{ $cCount }}
                            </span>
                        @else
                            <span id="cart-badge" class="hidden absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] rounded-full flex items-center justify-center font-bold ring-2 ring-primary lg:ring-white dark:ring-gray-800">0</span>
                        @endif
                    </a>
                </div>
            @else
                <!-- Logo pour utilisateur non connecté -->
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="group flex items-center gap-2.5" aria-label="Accueil">
                        <div class="w-9 h-9 rounded-xl bg-white/90 lg:bg-gray-800 flex items-center justify-center text-gray-800 lg:text-white shadow-sm group-hover:shadow-md transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-white lg:text-gray-800 dark:text-gray-100 text-sm group-hover:opacity-80 transition-opacity">{{ config('app.name', 'VintApp') }}</span>
                    </a>
                </div>
                <div class="flex items-center gap-1">
                    <button id="headerThemeToggle" onclick="toggleHeaderTheme()" class="p-2 hover:bg-white/10 lg:hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-all duration-200 active:scale-95" aria-label="Changer le theme">
                        <svg id="headerThemeIcon" class="w-5 h-5 text-white lg:text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>
                </div>
            @endauth
        </div>

        <!-- Navigation principale (desktop seulement) -->
        <nav class="bg-gray-900 hidden lg:block" role="navigation" aria-label="Navigation principale">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex items-center justify-between h-14">
                    <!-- Logo et navigation gauche -->
                    <div class="flex items-center gap-8">
                        <!-- Logo -->
                        <a href="{{ url('/') }}" class="flex items-center gap-2 text-white hover:opacity-80 transition-opacity">
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
                        <div class="flex items-center gap-1">
                            @php
                                $isSeller = auth()->check() && auth()->user()->isSeller();

                                $desktopNavLinks = [
                                    ['route' => 'items.index', 'label' => 'Articles', 'active' => request()->routeIs('items.index'), 'icon' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z'],
                                    ['route' => 'promotions', 'label' => 'Promotions', 'active' => request()->routeIs('promotions'), 'icon' => 'M5.25 4.5h13.5a.75.75 0 01.75.75v13.5a.75.75 0 01-1.5 0v-6.914l-4.72 4.72a.75.75 0 01-1.06 0l-5.47-5.47-4.72 4.72a.75.75 0 01-1.06-1.06l5.25-5.25a.75.75 0 011.06 0l5.47 5.47 4.18-4.18V7a.75.75 0 01.75-.75'],
                                    ['route' => 'expositions.index', 'label' => 'Expos', 'active' => request()->routeIs('expositions.*'), 'icon' => 'M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z'],
                                ];

                                $authNavLinks = [
                                    ['route' => 'orders.index', 'label' => 'Commandes', 'active' => request()->routeIs('orders.index'), 'icon' => 'M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121 0 2.002-.895 1.924-2.013l-.553-7.72a.75.75 0 00-.746-.687H6.154a.75.75 0 00-.746.687l-.553 7.72a1.924 1.924 0 001.924 2.013zm12.75 3a3 3 0 00-3-3m3 3v.008h-.008V17.25h.008zm-3 0v.008h-.008V17.25h.008z'],
                                ];
                            @endphp
                            
                            @foreach($desktopNavLinks as $link)
                                <a href="{{ route($link['route']) }}" class="relative flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $link['active'] ? 'text-white bg-white/15' : 'text-white/75 hover:text-white hover:bg-white/10' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"/></svg>
                                    {{ $link['label'] }}
                                    @if($link['active'])<span class="absolute bottom-0 left-3 right-3 h-0.5 bg-white rounded-full"></span>@endif
                                </a>
                            @endforeach
                            
                            @auth
                                @foreach($authNavLinks as $link)
                                    <a href="{{ route($link['route']) }}" class="relative flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $link['active'] ? 'text-white bg-white/15' : 'text-white/75 hover:text-white hover:bg-white/10' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"/></svg>
                                        {{ $link['label'] }}
                                        @if($link['active'])<span class="absolute bottom-0 left-3 right-3 h-0.5 bg-white rounded-full"></span>@endif
                                    </a>
                                @endforeach
                            @endauth
                            
                            @auth
                                @if($isSeller)
                                    <a href="{{ route('seller.dashboard') }}" class="relative flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('seller.*') ? 'text-white bg-white/15' : 'text-white/75 hover:text-white hover:bg-white/10' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-13.5 0V9.75M3 21h18M3 9.75l7.5-6 7.5 6"/></svg>
                                        Espace vendeur
                                        @if(request()->routeIs('seller.*'))<span class="absolute bottom-0 left-3 right-3 h-0.5 bg-white rounded-full"></span>@endif
                                    </a>
                                @endif
                            @endauth
                            
                            <a href="{{ route('help.index') }}" class="relative flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('help.*') ? 'text-white bg-white/15' : 'text-white/75 hover:text-white hover:bg-white/10' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>
                                Aide
                                @if(request()->routeIs('help.*'))<span class="absolute bottom-0 left-3 right-3 h-0.5 bg-white rounded-full"></span>@endif
                            </a>
                        </div>
                    </div>
                    
                    <!-- Barre de recherche et menu utilisateur -->
                    <div class="flex items-center gap-3">
                        <!-- Barre de recherche (autocomplétion dynamique) -->
                        <div class="relative" x-data="searchAutoComplete()" x-on:keydown.escape.window="close(false)">
                            <form class="flex items-center" method="GET" action="{{ route('items.search') }}" x-on:submit="submitSearch($event)">
                                <div class="relative group">
                                    <input type="search" 
                                           name="q" 
                                           x-ref="input"
                                           x-model="query"
                                           x-on:input="onInput()"
                                           x-on:focus="open=true"
                                           x-on:keydown.down.prevent="move(1)"
                                           x-on:keydown.up.prevent="move(-1)"
                                           x-on:keydown.enter.prevent="submitSearch($event)"
                                           x-on:click.away="close(false)"
                                           placeholder="Rechercher un article..." 
                                           value="{{ request('q') }}"
                                           autocomplete="off"
                                           class="w-72 px-4 py-2 pl-10 pr-9 text-sm bg-white/10 text-white placeholder-white/50 border border-white/20 rounded-xl focus:outline-none focus:bg-white/20 focus:border-white/40 focus:ring-0 transition-all duration-200">
                                    <svg class="w-4 h-4 absolute left-3 top-2.5 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2">
                                        <i x-show="loading" x-cloak class="fas fa-spinner fa-spin text-sm text-white/50"></i>
                                        <button type="button" x-show="query" x-on:click="clear()" class="text-white/50 hover:text-white transition-colors" aria-label="Effacer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </span>
                                </div>
                            </form>

                            <!-- Suggestions -->
                            <div x-show="open && (loading || hasResults || error || query.length > 0)" x-cloak
                                 x-on:click.away="close(false)"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="absolute right-0 mt-2 w-96 max-w-[85vw] bg-white rounded-xl shadow-xl ring-1 ring-black/5 z-50 overflow-hidden">
                                <!-- État chargement -->
                                <div x-show="loading" class="p-4 flex items-center gap-3 text-sm text-gray-500">
                                    <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                    Recherche en cours...
                                </div>

                                <!-- État erreur -->
                                <div x-show="!loading && error" class="p-4 text-sm text-red-500 flex items-center gap-2">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span x-text="error"></span>
                                </div>

                                <!-- État vide -->
                                <div x-show="!loading && !error && query && hasResults === false" class="p-4 text-sm text-gray-500 flex items-center gap-2">
                                    <i class="fas fa-search text-gray-400"></i>
                                    Aucun résultat pour <strong class="truncate" x-text="query"></strong>
                                </div>

                                <!-- Résultats -->
                                <template x-if="hasResults">
                                    <div>
                                        <div class="px-4 py-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400 border-b border-gray-100">
                                            Résultats
                                        </div>
                                        <ul class="max-h-80 overflow-y-auto py-1">
                                            <template x-for="(item, i) in results" :key="item.id">
                                                <li>
                                                    <a :href="item.url || '/items/search?q=' + encodeURIComponent(query)" x-on:click="close(true)"
                                                       class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors"
                                                       :class="i === activeIndex ? 'bg-gray-100 hover:bg-gray-100' : ''">
                                                        <img :src="item.first_image_url || '/images/placeholder.png'" 
                                                             :alt="item.name"
                                                             class="w-10 h-10 rounded-lg object-cover flex-shrink-0 bg-gray-100"
                                                             x-on:error="$el.src='/images/placeholder.png'">
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-gray-900 truncate" x-text="item.name"></p>
                                                            <p class="text-xs text-gray-500 flex items-center gap-1">
                                                                <i class="fas fa-store text-gray-400"></i>
                                                                <span class="truncate" x-text="item.user?.name || 'Vendeur'"></span>
                                                            </p>
                                                        </div>
                                                        <span class="text-sm font-semibold text-gray-900 flex-shrink-0" x-text="item.formatted_price"></span>
                                                    </a>
                                                </li>
                                            </template>
                                        </ul>
                                        <div class="px-4 py-2.5 border-t border-gray-100">
                                            <a :href="'/items/search?q=' + encodeURIComponent(query)" x-on:click="close(true)"
                                               class="flex items-center justify-center gap-2 text-sm text-vinted-primary-600 hover:text-vinted-primary-700 font-medium">
                                                Voir tous les résultats pour <span class="truncate block max-w-[50%]" x-text="query"></span>
                                                <i class="fas fa-arrow-right text-xs"></i>
                                            </a>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        
                        @auth
                            <!-- Menu utilisateur -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center gap-2 text-white hover:text-gray-300 transition-colors p-1.5 rounded-lg hover:bg-white/10" aria-label="Menu utilisateur">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ $avatarUrl }}" alt="{{ Auth::user()->name }}" class="w-7 h-7 rounded-lg object-cover ring-2 ring-white/30">
                                    @else
                                        <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center text-white font-bold text-xs">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                </button>
                                
                                <!-- Dropdown menu -->
                                <div x-show="open" @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl ring-1 ring-black/5 dark:ring-white/10 py-1 z-50 overflow-hidden">
                                    <div class="px-4 py-2.5 text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">Profil & Paramètres</div>
                                    @auth
                                        @if(auth()->user()->isSeller())
                                            <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-13.5 0V9.75M3 21h18M3 9.75l7.5-6 7.5 6"/></svg>
                                                Espace vendeur
                                            </a>
                                        @endif
                                    @endauth
                                    <a href="{{ route('profile.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                        Mon Profil
                                    </a>
                                    <a href="{{ route('settings.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Paramètres
                                    </a>
                                    <a href="{{ route('messages.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/></svg>
                                        Messages
                                    </a>
                                    @php $__u = auth()->user(); @endphp
                                    @if($__u && ($__u->hasRole('admin') || $__u->hasRole('support') || $__u->isExpert()))
                                        <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                                        <div class="px-4 py-1.5 text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">Interface rôles</div>
                                        @if($__u->hasRole('admin'))
                                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-vinted-primary-700 dark:text-vinted-primary-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15L15 9.75m-3-7.036A11.959 11.959 0 013.598 6C3.214 8.848 3.823 11.876 4.5 14.5c.594 2.316 1.594 4.012 2.564 5.163C7.698 21.151 10.848 21 12 21s4.31.151 5.936-1.337c.97-1.151 1.97-2.847 2.564-5.163.677-2.624 1.286-5.652.902-8.5A11.959 11.959 0 0112.75 2.714z"/></svg>
                                                Administration
                                            </a>
                                        @elseif($__u->hasRole('support'))
                                            <a href="{{ route('agent.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-vinted-primary-700 dark:text-vinted-primary-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                                Espace support
                                            </a>
                                        @elseif($__u->isExpert())
                                            <a href="{{ route('expert.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-vinted-primary-700 dark:text-vinted-primary-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15L15 9.75M12 3l7.5 2.625v6.034c0 4.525-2.93 8.146-7.5 9.591-4.57-1.445-7.5-5.066-7.5-9.591V5.625L12 3z"/></svg>
                                                Espace expert
                                            </a>
                                        @endif
                                        <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                                    @endif
                                    <a href="{{ route('admin.refunds.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                                        Remboursements
                                    </a>
                                    <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-2.5 w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                                            Déconnexion
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-2">
                                <a href="{{ route('login') }}" class="text-white/80 hover:text-white px-3 py-1.5 text-sm font-medium rounded-lg hover:bg-white/10 transition-all duration-200">
                                    Connexion
                                </a>
                                <a href="{{ route('register') }}" class="bg-white text-gray-900 hover:bg-white/90 px-4 py-1.5 rounded-lg text-sm font-semibold transition-all duration-200 shadow-sm">
                                    S'inscrire
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
        <nav class="bg-gray-50 dark:bg-gray-800/50 py-2 hidden lg:block border-b border-gray-100 dark:border-gray-700/30" aria-label="Fil d'Ariane">
            <div class="max-w-7xl mx-auto px-4">
                <ol class="flex items-center gap-1.5 text-sm">
                    <li>
                        <a href="{{ url('/') }}" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                            Accueil
                        </a>
                    </li>
                    @php
                        $chevronSvg = '<svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>';
                    @endphp
                    @if(request()->routeIs('categories.*'))
                        <li class="flex items-center gap-1.5">{!! $chevronSvg !!}<a href="{{ route('categories.index') }}" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">Catégories</a></li>
                        @if(request()->routeIs('categories.show'))
                            <li class="flex items-center gap-1.5">{!! $chevronSvg !!}<span class="text-gray-700 dark:text-gray-200 font-medium">{{ $category->name ?? 'Détails' }}</span></li>
                        @endif
                    @elseif(request()->routeIs('brands.*'))
                        <li class="flex items-center gap-1.5">{!! $chevronSvg !!}<a href="{{ route('brands.index') }}" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">Marques</a></li>
                        @if(request()->routeIs('brands.show'))
                            <li class="flex items-center gap-1.5">{!! $chevronSvg !!}<span class="text-gray-700 dark:text-gray-200 font-medium">{{ $brand->name ?? 'Détails' }}</span></li>
                        @endif
                    @elseif(request()->routeIs('items.*'))
                        <li class="flex items-center gap-1.5">{!! $chevronSvg !!}<a href="{{ route('items.index') }}" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">Articles</a></li>
                        @if(request()->routeIs('items.show'))
                            <li class="flex items-center gap-1.5">{!! $chevronSvg !!}<span class="text-gray-700 dark:text-gray-200 font-medium">{{ $item->name ?? 'Détails' }}</span></li>
                        @elseif(request()->routeIs('items.my-items'))
                            <li class="flex items-center gap-1.5">{!! $chevronSvg !!}<span class="text-gray-700 dark:text-gray-200 font-medium">Mes articles</span></li>
                        @endif
                    @elseif(request()->routeIs('wallet.*'))
                        <li class="flex items-center gap-1.5">{!! $chevronSvg !!}<span class="text-gray-700 dark:text-gray-200 font-medium">Wallet</span></li>
                    @endif
                </ol>
            </div>
        </nav>
    @endif

    <!-- Firebase SDK : app + auth chargés pour toutes les pages (guest et connecté) -->
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-auth-compat.js"></script>
    <script>
        try {
            if (!firebase.apps.length) {
                firebase.initializeApp({
                    apiKey: "{{ config('services.firebase.api_key') }}",
                    authDomain: "{{ config('services.firebase.auth_domain') }}",
                    projectId: "{{ config('services.firebase.project_id') }}",
                    storageBucket: "{{ config('services.firebase.storage_bucket') }}",
                    messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
                    appId: "{{ config('services.firebase.app_id') }}"
                });
            }
        } catch (error) {
            console.error('Firebase init error:', error);
        }
    </script>

    <!-- Contenu principal -->
    <main class="flex-1 pb-20 lg:pb-0">
        @yield('content')
    </main>

    <!-- Notifications en temps réel -->
    <x-notifications-realtime />

    <!-- Toast global shadcn-style -->
    <x-toast />

    <!-- Footer -->
    @if(!request()->routeIs('messages.*'))
        <x-footer />
    @endif

    <!-- Navigation mobile (bottom) -->
    @if(!request()->routeIs('messages.show'))
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-50" role="navigation" aria-label="Navigation mobile" x-data="{ moreOpen: false }">
        <!-- Fond avec blur -->
        <div class="absolute inset-0 bg-white/80 dark:bg-gray-900/90 backdrop-blur-xl border-t border-gray-200/60 dark:border-gray-700/50"></div>
        
        <div class="relative flex items-center justify-around h-16 max-w-lg mx-auto px-2 safe-area-bottom">
            @php
                $isSellerMobile = auth()->check() && auth()->user()->isSeller();

                $iconSell = 'M12 4.5v15m7.5-7.5h-15';
                $iconArticles = 'M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z';
                $iconArticlesFilled = 'M3.375 3C2.339 3 1.5 3.84 1.5 4.875v.75c0 1.036.84 1.875 1.875 1.875h17.25c1.035 0 1.875-.84 1.875-1.875v-.75C22.5 3.839 21.66 3 20.625 3H3.375zm0 0 M3.087 9l.54 9.176A3 3 0 006.62 21h10.757a3 3 0 002.995-2.824L20.913 9H3.087zm6.163 3.75A.75.75 0 0110 12h4a.75.75 0 010 1.5h-4a.75.75 0 01-.75-.75z';
                $iconProfile = 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z';
                $iconOrders = 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z';
                $iconVendor = 'M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z';
                $iconLogin = 'M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75';
                $iconRegister = 'M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z';
                $mobileNav = [
                    ['url' => url('/'), 'label' => 'Accueil', 'active' => request()->is('/'),
                     'icon' => 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
                     'iconFilled' => 'M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.432z'],
                ];

                $iconMore = 'M6.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0z';

                // Liens regroupés dans l'onglet « Plus » (Expos, Promotions + onglet contextuel).
                $moreNavLinks = [
                    ['url' => route('expositions.index'), 'label' => 'Expos', 'active' => request()->routeIs('expositions.*'),
                     'icon' => 'M13.5 21v-7.5a.75.75 0 017.5 0V21m-19.5 0V9.75m19.5 5V21M3 21h18M3 9.75L10.5 3l3 2.625M8.25 3.75V3A.75.75 0 019 2.25h1.5a.75.75 0 01.75.75v1.5'],
                    ['url' => route('promotions'), 'label' => 'Promotions', 'active' => request()->routeIs('promotions'),
                     'icon' => 'M5.25 4.5h13.5a.75.75 0 01.75.75v13.5a.75.75 0 01-1.5 0v-6.914l-4.72 4.72a.75.75 0 01-1.06 0l-5.47-5.47-4.72 4.72a.75.75 0 01-1.06-1.06l5.25-5.25a.75.75 0 011.06 0l5.47 5.47 4.18-4.18V7a.75.75 0 01.75-.75'],
                ];

                if (auth()->check()) {
                    $mobileNav = array_merge($mobileNav, [
                        ['url' => route('items.index'), 'label' => 'Articles', 'active' => request()->routeIs('items.index'),
                         'icon' => $iconArticles, 'iconFilled' => $iconArticlesFilled],
                        ['url' => route('items.create'), 'label' => 'Vendre', 'active' => request()->routeIs('items.create'),
                         'icon' => $iconSell, 'iconFilled' => $iconSell, 'special' => true],
                        ['url' => null, 'label' => 'Plus', 'active' => false, 'menu' => true,
                         'icon' => $iconMore, 'iconFilled' => $iconMore],
                        ['url' => route('settings.index'), 'label' => 'Profil', 'active' => request()->routeIs('settings.*'),
                         'icon' => $iconProfile, 'iconFilled' => $iconProfile],
                    ]);

                    if ($isSellerMobile) {
                        $moreNavLinks[] = ['url' => route('seller.dashboard'), 'label' => 'Espace vendeur', 'active' => request()->routeIs('seller.*'),
                            'icon' => $iconVendor];
                    } else {
                        $moreNavLinks[] = ['url' => route('orders.index'), 'label' => 'Commandes', 'active' => request()->routeIs('orders.*'),
                            'icon' => $iconOrders];
                    }
                } else {
                    $mobileNav = array_merge($mobileNav, [
                        ['url' => route('items.index'), 'label' => 'Articles', 'active' => request()->routeIs('items.index'),
                         'icon' => $iconArticles, 'iconFilled' => $iconArticlesFilled],
                        ['url' => route('items.create'), 'label' => 'Vendre', 'active' => request()->routeIs('items.create'),
                         'icon' => $iconSell, 'iconFilled' => $iconSell, 'special' => true],
                        ['url' => null, 'label' => 'Plus', 'active' => false, 'menu' => true,
                         'icon' => $iconMore, 'iconFilled' => $iconMore],
                        ['url' => route('login'), 'label' => 'Connexion', 'active' => request()->routeIs('login'),
                         'icon' => $iconLogin, 'iconFilled' => $iconLogin],
                    ]);
                    $moreNavLinks[] = ['url' => route('register'), 'label' => "S'inscrire", 'active' => request()->routeIs('register'),
                        'icon' => $iconRegister];
                }
            @endphp
            
            @php
                // L'onglet « Plus » est actif si une de ses entrées est la page courante.
                foreach ($mobileNav as $i => $item) {
                    if (!empty($item['menu'])) {
                        $mobileNav[$i]['active'] = collect($moreNavLinks)->contains(fn ($l) => !empty($l['active']));
                    }
                }
            @endphp
            @foreach($mobileNav as $item)
                @if(!empty($item['menu']))
                    <button type="button" x-on:click="moreOpen = !moreOpen" :aria-expanded="moreOpen"
                            class="group flex flex-col items-center justify-center gap-0.5 relative cursor-pointer" aria-label="{{ $item['label'] }}">
                        <div class="relative p-1">
                            <svg :class="moreOpen ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors'"
                                 class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">@foreach(explode(' M', $item['iconFilled']) as $i => $path)<path d="{{ $i > 0 ? 'M' : '' }}{{ $path }}"/>@endforeach</svg>
                        </div>
                        <span class="text-[10px] font-medium {{ $item['active'] ? 'text-gray-900 dark:text-gray-100 font-semibold' : 'text-gray-500 dark:text-gray-400' }}">{{ $item['label'] }}</span>
                        @if($item['active'])
                            <span class="absolute top-0 left-1/2 -translate-x-1/2 w-4 h-0.5 bg-gray-900 rounded-full"></span>
                        @endif
                    </button>
                @else
                <a href="{{ $item['url'] }}" class="group flex flex-col items-center justify-center gap-0.5 relative" aria-label="{{ $item['label'] }}">
                    @if(!empty($item['special']))
                        {{-- Bouton Vendre spécial --}}
                        <div class="w-10 h-10 -mt-3 rounded-xl flex items-center justify-center transition-all duration-200 {{ $item['active'] ? 'bg-gray-900 shadow-lg shadow-black/30' : 'bg-gray-800 shadow-md shadow-black/20 group-active:scale-90' }}">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/></svg>
                        </div>
                        <span class="text-[10px] font-semibold {{ $item['active'] ? 'text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400' }}">{{ $item['label'] }}</span>
                    @else
                        <div class="relative p-1">
                            @if($item['active'])
                                <svg class="w-5 h-5 text-gray-900 dark:text-gray-100" viewBox="0 0 24 24" fill="currentColor">@foreach(explode(' M', $item['iconFilled']) as $i => $path)<path d="{{ $i > 0 ? 'M' : '' }}{{ $path }}"/>@endforeach</svg>
                            @else
                                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/></svg>
                            @endif
                        </div>
                        <span class="text-[10px] font-medium {{ $item['active'] ? 'text-gray-900 dark:text-gray-100 font-semibold' : 'text-gray-500 dark:text-gray-400' }}">{{ $item['label'] }}</span>
                        @if($item['active'])
                            <span class="absolute top-0 left-1/2 -translate-x-1/2 w-4 h-0.5 bg-gray-900 rounded-full"></span>
                        @endif
                    @endif
                </a>
                @endif
            @endforeach
        </div>

        {{-- Panneau « Plus » --}}
        @if(!empty($moreNavLinks))
        <div x-show="moreOpen" @click.away="moreOpen = false" x-cloak
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             class="absolute inset-x-0 bottom-full mb-2 z-50">
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl ring-1 ring-black/5 dark:ring-white/10 mx-3 overflow-hidden">
                <div class="px-4 py-3 text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider border-b border-gray-100 dark:border-gray-800">Explorer</div>
                @foreach($moreNavLinks as $menuLink)
                    <a href="{{ $menuLink['url'] }}" @click="moreOpen = false"
                       class="flex items-center gap-3 px-4 py-3.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors {{ !empty($menuLink['active']) ? 'bg-gray-50 dark:bg-gray-800 font-semibold' : '' }}">
                        <svg class="w-5 h-5 {{ !empty($menuLink['active']) ? 'text-gray-900 dark:text-white' : 'text-gray-400' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $menuLink['icon'] }}"/></svg>
                        {{ $menuLink['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </nav>
    @endif

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

    <!-- Day/Night Theme -->
    @if(config('colors.day_night.enabled', false))
        <script src="{{ asset('js/day-night.js') }}?v={{ filemtime(public_path('js/day-night.js')) }}"></script>
    @endif

    <!-- Scripts personnalisés -->
    <script>
        // Barre de recherche dynamique avec autocomplétion (Alpine)
        function searchAutoComplete() {
            return {
                query: @json(request('q', '')),
                results: [],
                loading: false,
                error: '',
                open: false,
                activeIndex: -1,
                debounceTimer: null,

                get hasResults() {
                    return this.results.length > 0;
                },

                onInput() {
                    clearTimeout(this.debounceTimer);
                    this.activeIndex = -1;
                    if (!this.query || this.query.trim().length < 2) {
                        this.results = [];
                        this.error = '';
                        this.open = this.query.length > 0 ? true : false;
                        return;
                    }
                    this.open = true;
                    this.debounceTimer = setTimeout(() => this.fetchResults(), 300);
                },

                async fetchResults() {
                    const q = this.query.trim();
                    if (q.length < 2) return;
                    this.loading = true;
                    this.error = '';
                    try {
                        const res = await fetch(`/items/suggestions?q=${encodeURIComponent(q)}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (!res.ok) throw new Error('Erreur ' + res.status);
                        const data = await res.json();
                        const items = data?.data || [];
                        this.results = items.slice(0, 6);
                    } catch (e) {
                        console.error('Search autocomplete error:', e);
                        this.error = 'Impossible de charger les résultats';
                        this.results = [];
                    } finally {
                        this.loading = false;
                    }
                },

                move(dir) {
                    if (!this.results.length) return;
                    this.activeIndex = (this.activeIndex + dir + this.results.length) % this.results.length;
                },

                submitSearch(e) {
                    this.close(false);
                    if (this.activeIndex >= 0 && this.results[this.activeIndex]?.url) {
                        e.preventDefault();
                        window.location.href = this.results[this.activeIndex].url;
                        return;
                    }
                    return true;
                },

                clear() {
                    this.query = '';
                    this.results = [];
                    this.error = '';
                    this.open = false;
                    this.activeIndex = -1;
                    clearTimeout(this.debounceTimer);
                },

                close() {
                    this.open = false;
                    this.activeIndex = -1;
                }
            };
        }

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
                <div class="p-3 border-t border-gray-200 dark:border-gray-700 text-center">
                    <a href="{{ route('notifications.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline font-medium">
                        Voir toutes les notifications
                    </a>
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
            
            fetch('/api/v1/notifications')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    const notifications = data.data || [];
                    if (notifications.length === 0) {
                        content.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-sm text-center">Aucune notification</p>';
                        return;
                    }
                    
                    content.innerHTML = notifications.map(notification => `
                        <div class="p-3 border-b border-gray-100 hover:bg-gray-50 dark:bg-gray-900 cursor-pointer" 
                             onclick="markNotificationAsRead(${notification.id}, '${notification.data?.url || '#'}')">
                            <div class="flex items-start space-x-3">
                                <i class="fas ${getNotificationIcon(notification.type)} text-gray-600 mt-1"></i>
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
                    console.error('Notifications error:', error);
                    if (content.querySelector('.text-center')) return;
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
            fetch(`/api/v1/notifications/${notificationId}/mark-read`, {
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
                    messageDiv.innerHTML = '<p class="text-green-600 font-medium">Inscription réussie !</p>';
                    document.getElementById('newsletterEmail').value = '';
                } else {
                    messageDiv.innerHTML = '<p class="text-red-500 font-medium">Erreur lors de l\'inscription</p>';
                }
            })
            .catch(error => {
                messageDiv.innerHTML = '<p class="text-red-500 font-medium">Erreur lors de l\'inscription</p>';
            });
        });

        // Header theme toggle
        function toggleHeaderTheme() {
            const current = localStorage.getItem('theme') || window.userTheme || 'auto';
            const html = document.documentElement;
            const icon = document.getElementById('headerThemeIcon');

            let newTheme;
            if (current === 'dark') {
                newTheme = 'light';
                if (icon) {
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>';
                }
            } else {
                newTheme = 'dark';
                if (icon) {
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>';
                }
            }

            localStorage.setItem('theme', newTheme);
            html.setAttribute('data-theme', newTheme);

            if (newTheme === 'dark') {
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
            }
        }

        // Sync header icon on load
        document.addEventListener('DOMContentLoaded', function() {
            const icon = document.getElementById('headerThemeIcon');
            if (icon) {
                const theme = localStorage.getItem('theme') || window.userTheme || 'light';
                if (theme === 'dark') {
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>';
                }
            }
        });
    </script>

    @auth
    <!-- Firebase SDK pour notifications push (app + auth déjà chargés ci-dessus) -->
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js"></script>
    
    <script>
        // Récupérer l'application Firebase déjà initialisée pour toutes les pages
        const firebaseApp = firebase.app();
        const messaging = firebase.messaging();

        // VAPID Key pour les notifications push web
        const vapidKey = "{{ config('services.firebase.vapid_key') }}";

        async function initFirebasePushNotifications() {
            try {
                // Vérifier si le navigateur supporte les notifications
                if (!('Notification' in window)) {
                    return;
                }

                // Vérifier si le Service Worker est supporté
                if (!('serviceWorker' in navigator)) {
                    return;
                }

                // Nettoyer les anciens Service Workers orphelins/cassés (ex:
                // /firebase-messaging-sw.js d'un déploiement précédent) qui
                // peuvent bloquer les mises à jour du SW courant et produire
                // l'erreur "The object is in an invalid state".
                try {
                    const existingRegs = await navigator.serviceWorker.getRegistrations();
                    for (const reg of existingRegs) {
                        const script = reg.active?.scriptURL || reg.installing?.scriptURL || reg.waiting?.scriptURL || '';
                        if (script && !script.endsWith('/sw.js')) {
                            await reg.unregister();
                            console.info('SW orphelin désenregistré:', script);
                        }
                    }
                } catch (e) {
                    // Non bloquant
                }

                // Enregistrer le Service Worker principal (qui inclut Firebase)
                let registration = await navigator.serviceWorker.register('/sw.js');

                // Attendre que le Service Worker soit actif
                if (registration.installing) {
                    await new Promise((resolve) => {
                        registration.installing.addEventListener('statechange', (e) => {
                            if (e.target.state === 'activated') {
                                resolve();
                            }
                        });
                    });
                } else if (registration.waiting) {
                    await navigator.serviceWorker.ready;
                } else if (!registration.active) {
                    await navigator.serviceWorker.ready;
                }

                // S'assurer qu'on a bien un Service Worker actif
                registration = await navigator.serviceWorker.ready;

                // Demander la permission de notification
                const permission = await requestNotificationPermission();
                
                if (permission === 'granted') {
                    // Récupérer le token FCM
                    const currentToken = await messaging.getToken({
                        vapidKey: vapidKey,
                        serviceWorkerRegistration: registration
                    });

                    if (currentToken) {
                        await saveFCMToken(currentToken);
                    }

                    // Écouter les messages en premier plan (app ouverte)
                    messaging.onMessage((payload) => {
                        displayForegroundNotification(payload);
                    });
                }

            } catch (error) {
                // Erreur silencieuse
            }
        }

        async function requestNotificationPermission() {
            try {
                const permission = await Notification.requestPermission();
                return permission;
            } catch (error) {
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
                    localStorage.setItem('fcm_token', token);
                }
            } catch (error) {
                // Erreur silencieuse
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
                    await saveFCMToken(currentToken);
                }
            } catch (error) {
                // Erreur silencieuse
            }
        }, 24 * 60 * 60 * 1000); // 24 heures

        // Initialiser les notifications push au chargement
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initFirebasePushNotifications);
        } else {
            initFirebasePushNotifications();
        }
    </script>
    @endauth

    
</body>
</html>
