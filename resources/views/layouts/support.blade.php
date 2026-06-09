<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Support Client') - {{ $appName ?? 'VintApp' }}</title>
    <link rel="icon" type="image/png" href="{{ asset($appFavicon ?? '/favicon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/dynamic-colors.css') }}?v={{ filemtime(public_path('css/dynamic-colors.css')) }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="h-full bg-gray-100 dark:bg-gray-900 font-sans antialiased">
    <div class="min-h-full flex flex-col">

        {{-- Topbar Support --}}
        <header class="sticky top-0 z-40 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-14 sm:h-16">
                    {{-- Logo + titre --}}
                    <a href="{{ route('admin.support.index') }}" class="flex items-center gap-2 sm:gap-3 group">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center shadow-md group-hover:scale-105 transition-transform">
                            <i class="fas fa-headset text-white text-sm sm:text-base"></i>
                        </div>
                        <div>
                            <h1 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white leading-tight">Support Client</h1>
                            <p class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 leading-tight hidden sm:block">{{ $appName ?? 'VintApp' }}</p>
                        </div>
                    </a>

                    {{-- Navigation --}}
                    <nav class="hidden sm:flex items-center gap-1">
                        <a href="{{ route('admin.support.index') }}"
                           class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.support.index') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <i class="fas fa-inbox text-xs"></i>
                            <span>Tickets</span>
                            @php
                                $openCount = \App\Models\SupportChat::whereIn('status', ['open', 'in_progress'])->count();
                            @endphp
                            @if($openCount > 0)
                                <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-bold rounded-full bg-red-500 text-white">{{ $openCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.support.stats') }}"
                           class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.support.stats') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <i class="fas fa-chart-bar text-xs"></i>
                            <span>Statistiques</span>
                        </a>
                        <a href="{{ route('admin.support.agents') }}"
                           class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.support.agents') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <i class="fas fa-users-cog text-xs"></i>
                            <span>Agents</span>
                        </a>
                    </nav>

                    {{-- Actions droite --}}
                    <div class="flex items-center gap-2 sm:gap-3">
                        {{-- Nav mobile --}}
                        <div class="flex sm:hidden items-center gap-1">
                            <a href="{{ route('admin.support.index') }}"
                               class="p-2 rounded-lg transition-colors {{ request()->routeIs('admin.support.index') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600' : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                <i class="fas fa-inbox"></i>
                            </a>
                            <a href="{{ route('admin.support.stats') }}"
                               class="p-2 rounded-lg transition-colors {{ request()->routeIs('admin.support.stats') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600' : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                <i class="fas fa-chart-bar"></i>
                            </a>
                            <a href="{{ route('admin.support.agents') }}"
                               class="p-2 rounded-lg transition-colors {{ request()->routeIs('admin.support.agents') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600' : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                <i class="fas fa-users-cog"></i>
                            </a>
                        </div>

                        <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 hidden sm:block"></div>

                        {{-- Retour admin --}}
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors" title="Retour au tableau de bord admin">
                            <i class="fas fa-arrow-left text-[10px] sm:text-xs"></i>
                            <span class="hidden sm:inline">Admin</span>
                        </a>

                        {{-- Profil --}}
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            @if(session('success'))
                <div class="mt-4 flex items-center gap-3 p-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 rounded-lg" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                    <i class="fas fa-check-circle flex-shrink-0"></i>
                    <span class="flex-1 text-sm">{{ session('success') }}</span>
                    <button type="button" class="text-green-400 hover:text-green-600" @click="show = false"><i class="fas fa-times"></i></button>
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

        {{-- Footer léger --}}
        <footer class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <span>&copy; {{ date('Y') }} {{ $appName ?? 'VintApp' }} — Support Client</span>
                <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i>Tableau de bord
                </a>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
