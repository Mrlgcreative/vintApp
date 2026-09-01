<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace Agent') - {{ $appName ?? 'VintApp' }}</title>
    <link rel="icon" type="image/png" href="{{ asset($appFavicon ?? '/favicon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @production
        <link rel="stylesheet" href="{{ asset('css/dynamic-colors.css') }}?v={{ filemtime(public_path('css/dynamic-colors.css')) }}">
    @else
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = '{{ asset('css/dynamic-colors.css') }}?v={{ filemtime(public_path('css/dynamic-colors.css')) }}';
                document.head.appendChild(link);
            });
        </script>
    @endproduction
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 font-sans antialiased">
    <div class="min-h-full lg:flex">

        @php
            $myActiveCount = \App\Models\SupportChat::where('admin_id', auth()->id())->whereNotIn('status', ['closed'])->count();
            $unassignedCount = \App\Models\SupportChat::whereNull('admin_id')->whereIn('status', ['open'])->count();
            $currentAgent = \App\Models\SupportAgent::where('user_id', auth()->id())->first();
        @endphp

        {{-- ===== SIDEBAR ===== --}}
        <aside x-data="{ open: false }"
               :class="open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 shadow-lg lg:shadow-none transform transition-transform duration-300 flex flex-col">

            {{-- Logo --}}
            <div class="flex items-center justify-between h-16 px-5 border-b border-gray-200 dark:border-gray-700">
                <a href="{{ route('agent.dashboard') }}" class="flex items-center gap-3 group">
<div class="w-9 h-9 bg-gray-900 dark:bg-gray-600 rounded-lg flex items-center justify-center shadow-md group-hover:scale-105 transition-transform">
                            <i class="fas fa-headset text-white text-base"></i>
                        </div>
                    <div>
                        <h1 class="text-base font-bold text-gray-900 dark:text-white leading-tight">Espace Agent</h1>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 leading-tight">Support {{ $appName ?? 'VintApp' }}</p>
                    </div>
                </a>
                <button @click="open = false" class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <p class="px-3 text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-2">Menu</p>

                <a href="{{ route('agent.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('agent.dashboard') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <i class="fas fa-tachometer-alt w-5 text-center"></i>
                    <span>Tableau de bord</span>
                </a>

                <a href="{{ route('agent.tickets') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('agent.tickets') || request()->routeIs('agent.show') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <i class="fas fa-inbox w-5 text-center"></i>
                    <span class="flex-1">Mes Tickets</span>
                    @if($myActiveCount > 0)
                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-bold rounded-full bg-gray-900 text-white">{{ $myActiveCount }}</span>
                    @endif
                </a>

                <a href="{{ route('agent.unassigned') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('agent.unassigned') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <i class="fas fa-exclamation-circle w-5 text-center"></i>
                    <span class="flex-1">Non assignés</span>
                    @if($unassignedCount > 0)
                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-bold rounded-full bg-red-500 text-white">{{ $unassignedCount }}</span>
                    @endif
                </a>
            </nav>

            {{-- Pied sidebar : statut + profil --}}
            <div class="px-3 py-3 border-t border-gray-200 dark:border-gray-700 space-y-1">
                @if($currentAgent)
                    <div class="flex items-center gap-2 px-3 py-1.5 mb-1">
                        <span class="w-2 h-2 rounded-full {{ $currentAgent->is_active ? 'bg-gray-900' : 'bg-gray-400' }}"></span>
                        <span class="text-xs font-medium {{ $currentAgent->is_active ? 'text-gray-700 dark:text-gray-300' : 'text-gray-500 dark:text-gray-400' }}">
                            {{ $currentAgent->is_active ? 'En ligne' : 'Hors ligne' }}
                        </span>
                        <span class="ml-auto text-[10px] text-gray-400">{{ $currentAgent->activeChatsCount() }}/{{ $currentAgent->max_chats }}</span>
                    </div>
                @endif

                {{-- Toggle thème --}}
                <button id="theme-toggle" type="button" title="Changer de thème"
                        class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-adjust w-5 text-center"></i>
                    <span>Thème</span>
                    <span class="ml-auto text-[10px] text-gray-400" id="theme-label">Auto</span>
                </button>

                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-2 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-gray-900 dark:bg-gray-600 flex items-center justify-center text-white text-xs font-bold shadow-sm flex-shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fas fa-ellipsis-v text-xs"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition
                             class="absolute bottom-full right-0 mb-1 w-44 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 py-1 z-50">
                            <a href="{{ route('profile.index') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <i class="fas fa-user text-gray-400"></i> Mon profil
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Overlay mobile --}}
        <div x-show="open" @click="open = false" x-transition:opacity class="fixed inset-0 z-40 bg-black/50 lg:hidden"></div>

        {{-- ===== CONTENU ===== --}}
        <div class="flex-1 min-w-0 lg:pl-64 flex flex-col min-h-screen">
            {{-- Topbar compacte (mobile + bouton menu) --}}
            <header class="sticky top-0 z-30 bg-white/90 dark:bg-gray-800/90 backdrop-blur border-b border-gray-200 dark:border-gray-700 lg:hidden">
                <div class="flex items-center justify-between h-14 px-4">
                    <button @click="open = true" class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="flex items-center gap-2 sm:gap-3">
                        <button type="button" @click="document.getElementById('theme-toggle').click()" title="Changer de thème"
                                class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-adjust"></i>
                        </button>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full {{ $currentAgent && $currentAgent->is_active ? 'bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $currentAgent && $currentAgent->is_active ? 'bg-gray-700 dark:bg-gray-300' : 'bg-gray-400' }}"></span>
                            {{ $currentAgent && $currentAgent->is_active ? 'En ligne' : 'Hors ligne' }}
                        </span>
                    </div>
                </div>
            </header>

            {{-- Flash messages --}}
            <div class="w-full px-4 sm:px-6 lg:px-8 mx-auto max-w-7xl">
                @if(session('success'))
                    <div class="mt-4 flex items-center gap-3 p-3 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200 rounded-lg" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                        <i class="fas fa-check-circle flex-shrink-0"></i>
                        <span class="flex-1 text-sm">{{ session('success') }}</span>
                        <button type="button" class="text-gray-400 hover:text-gray-600" @click="show = false"><i class="fas fa-times"></i></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mt-4 flex items-center gap-3 p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-lg" x-data="{ show: true }" x-show="show">
                        <i class="fas fa-exclamation-circle flex-shrink-0"></i>
                        <span class="flex-1 text-sm">{{ session('error') }}</span>
                        <button type="button" class="text-red-400 hover:text-red-600" @click="show = false"><i class="fas fa-times"></i></button>
                    </div>
                @endif
            </div>

            {{-- Contenu --}}
            <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                @yield('content')
            </main>

            {{-- Footer --}}
            <footer class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 mt-auto">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span>&copy; {{ date('Y') }} {{ $appName ?? 'VintApp' }} — Espace Agent</span>
                    <span>{{ $myActiveCount }} ticket(s) actif(s)</span>
                </div>
            </footer>
        </div>
    </div>

    @stack('scripts')

    {{-- Synchro icône + libellé du thème (initial + après toggle) --}}
    <script>
    (function () {
        const labels = { 'light': 'Clair', 'dark': 'Sombre', 'auto': 'Auto' };
        const icons  = { 'light': 'fas fa-sun', 'dark': 'fas fa-moon', 'auto': 'fas fa-adjust' };
        function sync() {
            const theme = localStorage.getItem('theme') || 'auto';
            const btn = document.getElementById('theme-toggle');
            if (!btn) return;
            const icon = btn.querySelector('i');
            if (icon) icon.className = (icons[theme] || icons.auto) + ' w-5 text-center';
            const label = document.getElementById('theme-label');
            if (label) label.textContent = labels[theme] || labels.auto;
        }
        const btn = document.getElementById('theme-toggle');
        if (btn) {
            btn.addEventListener('click', function () {
                setTimeout(sync, 350);
            });
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', sync);
        } else {
            sync();
        }
    })();
    </script>
</body>
</html>