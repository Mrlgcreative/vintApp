<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" data-user-id="{{ Auth::id() }}">
    <meta name="is-expert" data-is-expert="{{ auth()->check() && auth()->user()->isExpert() ? 'true' : 'false' }}">
    @php
        $isExpert = auth()->check() && auth()->user()->isExpert();
        $contextTitle = $isExpert ? 'Expert' : 'Administration';
    @endphp
    <title>@yield('title') - {{ $contextTitle }} {{ $appName ?? 'VintApp' }}</title>
    <link rel="icon" type="image/png" href="{{ asset($appFavicon ?? '/favicon.png') }}">
    
    <!-- Lazy Loading CSS -->
    <link href="{{ asset('css/lazy-loading.css') }}" rel="stylesheet">
    
    <!-- Custom Admin Styles -->
    <link href="{{ asset('css/admin-components.css') }}" rel="stylesheet">
    
    <!-- Tailwind CSS compilé avec Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Color Palette Variables (loaded AFTER Vite to override default colors) -->
    <link rel="stylesheet" href="{{ asset('css/dynamic-colors.css') }}?v={{ filemtime(public_path('css/dynamic-colors.css')) }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous">
    
    <!-- Custom Page Styles -->
    @stack('styles')
    

    
    <!-- Styles complémentaires pour les composants -->
    <style>
        .select2-container--default .select2-selection--single {
            @apply border border-gray-300 dark:border-gray-600 rounded-lg focus:border-primary-500 focus:ring-2 focus:ring-primary-200;
        }
        .flatpickr-input {
            @apply border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:border-primary-500 focus:ring-2 focus:ring-primary-200;
        }
        .notification-dot {
            @apply absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center;
        }
        .custom-scrollbar::-webkit-scrollbar { width: 6px }
        .custom-scrollbar::-webkit-scrollbar-track { @apply bg-transparent; margin: 8px 0 }
        .custom-scrollbar::-webkit-scrollbar-thumb { @apply bg-white/20 rounded-full; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { @apply bg-white/40; }
        .custom-scrollbar { scrollbar-width: thin; scrollbar-color: rgba(255,255,255,.2) transparent; }
        @media (max-width: 1023px) {
            #sidebar { transform: translateX(-100%) }
            #sidebar.active { transform: translateX(0) }
        }
        @keyframes fade-in { from { opacity:0; transform:translateY(-4px) } to { opacity:1; transform:translateY(0) } }
        .animate-fade-in { animation: fade-in .3s ease-out }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-gray-900 dark:to-gray-800 font-sans text-sm leading-relaxed text-gray-900 dark:text-white">
    <div class="flex min-h-screen">
        @php
            // Détecter si l'utilisateur est un expert
            $isExpert = auth()->check() && auth()->user()->isExpert();
        @endphp

        <!-- Sidebar -->
        <nav class="fixed left-0 top-0 z-50 h-screen w-72 bg-gradient-primary-sidebar shadow-2xl transition-transform duration-300 ease-in-out" id="sidebar">
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
                    @if($isExpert)
                        <!-- Menu Expert -->
                        <a href="{{ route('expert.dashboard') }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('expert.dashboard*')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Dashboard Expert</span>
                        </a>

                        <div class="sidebar-section-title">
                            <span>Vérifications</span>
                        </div>
                        <a href="{{ route('expert.verifications.index') }}" 
                            class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('expert.verifications*')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <span class="flex-1">Mes Vérifications</span>
                            @php
                                $pendingVerifications = \App\Models\ProductAuthenticityCheck::where('expert_id', auth()->id())
                                    ->where('status', 'expert_review')
                                    ->count();
                            @endphp
                            @if($pendingVerifications > 0)
                                <span class="inline-flex items-center justify-center px-2 py-0.5 text-[11px] font-bold text-white bg-orange-500 rounded-full">{{ $pendingVerifications }}</span>
                            @endif
                        </a>

                        <a href="{{ route('expert.verifications.index', ['status' => 'expert_review']) }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('expert.verifications.index') && request('status') === 'expert_review') active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="flex-1">En attente d'examen</span>
                            @if($pendingVerifications > 0)
                                <span class="inline-flex items-center justify-center px-2 py-0.5 text-[11px] font-bold text-white bg-red-500 rounded-full animate-pulse">{{ $pendingVerifications }}</span>
                            @endif
                        </a>

                        <a href="{{ route('expert.verifications.index', ['status' => 'expert_approved']) }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('expert.verifications.index') && request('status') === 'expert_approved') active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Approuvées</span>
                        </a>

                        <a href="{{ route('expert.verifications.index', ['status' => 'expert_rejected']) }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('expert.verifications.index') && request('status') === 'expert_rejected') active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Rejetées</span>
                        </a>

                        <a href="{{ route('expert.items.pending') }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('expert.items.*')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            <span class="flex-1">Articles à vérifier</span>
                            @php
                                $pendingItemsCount = \App\Models\Item::where('verification_status', 'pending')
                                    ->whereNull('verified_at')
                                    ->count();
                            @endphp
                            @if($pendingItemsCount > 0)
                                <span class="inline-flex items-center justify-center px-2 py-0.5 text-[11px] font-bold text-white bg-purple-500 rounded-full animate-pulse">{{ $pendingItemsCount }}</span>
                            @endif
                        </a>

                        <div class="sidebar-section-title">
                            <span>Profil</span>
                        </div>
                        <a href="{{ route('expert.profile') }}" 
                            class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('expert.profile*')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Mon Profil Expert</span>
                        </a>

                        <!-- Statistiques rapides -->
                        <div class="mt-5 rounded-xl bg-white/[0.04] border border-white/[0.06] p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-3.5 h-3.5 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <h4 class="text-[11px] font-semibold uppercase tracking-wider text-white/40">Statistiques</h4>
                            </div>
                            @php
                                $expertStats = [
                                    'total' => \App\Models\ProductAuthenticityCheck::where('expert_id', auth()->id())->count(),
                                    'completed_today' => \App\Models\ProductAuthenticityCheck::where('expert_id', auth()->id())
                                        ->whereDate('expert_completed_at', today())->count(),
                                    'approval_rate' => auth()->user()->expertProfile->approval_rate ?? 0
                                ];
                            @endphp
                            <div class="space-y-2 text-xs">
                                <div class="flex items-center justify-between rounded-lg bg-white/[0.03] px-3 py-2">
                                    <span class="text-white/50">Total traité</span>
                                    <span class="text-white font-semibold">{{ $expertStats['total'] }}</span>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-white/[0.03] px-3 py-2">
                                    <span class="text-white/50">Aujourd'hui</span>
                                    <span class="text-emerald-400 font-semibold">{{ $expertStats['completed_today'] }}</span>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-white/[0.03] px-3 py-2">
                                    <span class="text-white/50">Taux succès</span>
                                    <span class="text-blue-400 font-semibold">{{ number_format($expertStats['approval_rate'], 1) }}%</span>
                                </div>
                            </div>
                        </div>

                    @else
                        <!-- Menu Admin -->
                        <a href="{{ route('admin.dashboard') }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('admin.dashboard')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                            <span>Tableau de bord</span>
                        </a>

                        <a href="{{ route('admin.users.index') }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('admin.users.index') || request()->routeIs('admin.users.show') || request()->routeIs('admin.users.edit')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="flex-1">Utilisateurs</span>
                            @if(isset($pendingUsersCount) && $pendingUsersCount > 0)
                                <span class="inline-flex items-center justify-center px-2 py-0.5 text-[11px] font-bold text-white bg-red-500 rounded-full">{{ $pendingUsersCount }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.users.online') }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('admin.users.online')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="flex-1">Utilisateurs Connectés</span>
                            <span class="w-2.5 h-2.5 bg-emerald-400 rounded-full animate-pulse shadow-lg shadow-emerald-400/50"></span>
                        </a>

                        <a href="{{ route('admin.experts.index') }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('admin.experts.*')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                            <span class="flex-1">Experts</span>
                            @php
                                $totalExperts = \App\Models\ExpertProfile::count();
                                $activeExperts = \App\Models\ExpertProfile::where('is_active', true)->count();
                            @endphp
                            @if($totalExperts > 0)
                                <span class="inline-flex items-center justify-center px-2 py-0.5 text-[11px] font-bold text-white bg-primary-500 rounded-full">{{ $activeExperts }}/{{ $totalExperts }}</span>
                            @endif
                        </a>

                        <div class="sidebar-section-title">
                            <span>Gestion</span>
                        </div>
                        <a href="{{ route('admin.transactions.index') }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('admin.transactions.*')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            <span>Transactions</span>
                        </a>

                        <a href="{{ route('admin.wallets.pending') }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('admin.wallets.pending')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="flex-1">Wallets en attente</span>
                            @if(isset($pendingWalletsCount) && $pendingWalletsCount > 0)
                                <span class="inline-flex items-center justify-center px-2 py-0.5 text-[11px] font-bold text-gray-900 bg-yellow-400 rounded-full">{{ $pendingWalletsCount }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.orders.index') }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('admin.orders.*') && !request()->routeIs('admin.orders.tracking')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                            <span>Commandes</span>
                        </a>

                        <a href="{{ route('admin.items.pending_verification') }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('admin.items.pending_verification')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                            <span class="flex-1">Vérification IA</span>
                            @php
                                $pendingItemsCount = \App\Models\Item::where('verification_status', 'pending')->count();
                            @endphp
                            @if($pendingItemsCount > 0)
                                <span class="inline-flex items-center justify-center px-2 py-0.5 text-[11px] font-bold text-white bg-purple-500 rounded-full animate-pulse">{{ $pendingItemsCount }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.refunds.index') }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('admin.refunds.*')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            <span class="flex-1">Remboursements</span>
                            @php
                                $pendingRefundsCount = \App\Models\Refund::where('status', 'pending')->count();
                            @endphp
                            @if($pendingRefundsCount > 0)
                                <span class="inline-flex items-center justify-center px-2 py-0.5 text-[11px] font-bold text-white bg-orange-500 rounded-full animate-pulse">{{ $pendingRefundsCount }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.orders.tracking.list') }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('admin.orders.tracking*')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="flex-1">Traçage GPS</span>
                            <span class="w-2.5 h-2.5 bg-primary-400 rounded-full animate-pulse shadow-lg shadow-primary-400/50"></span>
                        </a>

                        <div class="sidebar-section-title">
                            <span>Catalogue & Services</span>
                        </div>
                        <a href="{{ route('admin.brands.index') }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('admin.brands.*')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            <span>Marques</span>
                        </a>

                        <a href="{{ route('admin.categories.index') }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('admin.categories.*')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            <span>Catégories</span>
                        </a>

                        <a href="{{ route('admin.support.index') }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('admin.support.*')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span class="flex-1">Support Client</span>
                            @php
                                $unassignedSupport = \App\Models\SupportChat::whereNull('admin_id')
                                    ->whereIn('status', ['open', 'in_progress'])->count();
                            @endphp
                            @if($unassignedSupport > 0)
                                <span class="inline-flex items-center justify-center px-2 py-0.5 text-[11px] font-bold text-white bg-orange-500 rounded-full">{{ $unassignedSupport }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.affiliate.index') }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('admin.affiliate.*')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            <span class="flex-1">Affiliation</span>
                            @php
                                $topPerformersCount = \App\Models\User::whereHas('referrals', function($q) {
                                    $q->whereDate('created_at', '>=', now()->subDays(30));
                                })->count();
                            @endphp
                            @if($topPerformersCount > 0)
                                <span class="inline-flex items-center justify-center px-2 py-0.5 text-[11px] font-bold text-gray-900 bg-yellow-400 rounded-full">{{ $topPerformersCount }}</span>
                            @endif
                        </a>

                        <div class="sidebar-section-title">
                            <span>Système</span>
                        </div>
                        <a href="{{ route('admin.reports') }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('admin.reports')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <span>Rapports</span>
                        </a>

                        <a href="{{ route('admin.monitoring.index') }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('admin.monitoring.*')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            <span class="flex-1">Monitoring</span>
                            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse shadow-lg shadow-emerald-400/50"></span>
                        </a>

                        <a href="{{ route('admin.logs') }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('admin.logs')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Logs système</span>
                        </a>

                        <a href="{{ route('admin.settings.index') }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('admin.settings.*') && !request()->routeIs('admin.locations.*')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Paramètres</span>
                        </a>

                        <a href="{{ route('admin.locations.index') }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('admin.locations.*')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                            <span>Zones autorisées</span>
                        </a>

                        <a href="{{ route('admin.broadcast.fcm') }}" 
                           class="sidebar-link group flex items-center rounded-xl px-4 py-3 text-white/70 transition-all duration-200 hover:translate-x-0.5 hover:bg-white/10 hover:text-white @if(request()->routeIs('admin.broadcast.fcm')) active bg-gradient-primary-link text-white font-semibold shadow-lg translate-x-0.5 @endif">
                            <svg class="w-5 h-5 mr-3 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                            <span class="flex-1">Broadcast Push</span>
                            <span class="w-2 h-2 bg-orange-400 rounded-full animate-pulse shadow-lg shadow-orange-400/50"></span>
                        </a>
                    @endif
                </nav>

                <!-- Footer -->
                <div class="mt-auto p-4 space-y-2">
                    <a href="{{ route('home') }}" 
                       class="flex w-full items-center justify-center gap-2 rounded-xl border border-white/20 bg-transparent px-4 py-2.5 text-sm text-white/80 transition-all duration-200 hover:bg-white/10 hover:text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Retour au site
                    </a>
                    @if($isExpert)
                        <div class="text-center text-[11px] text-white/40 py-1.5">Interface Expert VintApp</div>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" 
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-red-500 to-red-600 px-4 py-2.5 text-sm text-white transition-all duration-200 hover:from-red-600 hover:to-red-700 hover:shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
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
            <header class="sticky top-0 z-30 border-b border-primary-700 bg-primary dark:bg-gray-800/95 p-4 shadow-sm backdrop-blur-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Toggle Button -->
                        <button class="rounded-lg p-2 text-white transition-colors duration-200 hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/30" 
                                id="sidebar-toggle"
                                aria-label="Toggle sidebar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <h1 class="text-lg font-semibold text-white lg:text-xl">@yield('page-title')</h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative">
                            <button class="relative rounded-lg p-2 text-white transition-colors duration-200 hover:bg-white/10" 
                                    type="button" id="notificationsDropdown">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                <span class="notification-dot hidden" id="notification-badge">0</span>
                            </button>
                            
                            <!-- Dropdown notifications -->
                            <div class="absolute right-0 top-full mt-2 hidden w-80 origin-top-right rounded-xl bg-white dark:bg-gray-800 shadow-xl ring-1 ring-black/5" 
                                 id="notifications-dropdown">
                                <div class="p-4">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</h3>
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

                        <!-- Theme Toggle -->
                        <button id="theme-toggle" class="relative rounded-lg p-2 text-white transition-colors duration-200 hover:bg-white/10" type="button" aria-label="Changer le thème">
                            <svg id="theme-icon-sun" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <svg id="theme-icon-moon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        </button>

                        <!-- Profil -->
                        <div class="relative">
                            <button class="flex items-center rounded-lg p-2 text-white dark:text-gray-300 transition-colors hover:bg-primary-700 dark:hover:bg-gray-700" 
                                    type="button" id="userDropdown">
                                @if(auth()->user()->avatar)
                                    @php
                                        $avatarUrl = filter_var(auth()->user()->avatar, FILTER_VALIDATE_URL) 
                                            ? auth()->user()->avatar 
                                            : asset('storage/' . auth()->user()->avatar);
                                    @endphp
                                    <img src="{{ $avatarUrl }}" 
                                         alt="{{ auth()->user()->name }}" 
                                         class="h-8 w-8 rounded-full object-cover border-2 border-white mr-2"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="mr-2 hidden h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-primary-600 to-cyan-400 text-white text-sm font-semibold">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                @else
                                    <div class="mr-2 flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-primary-600 to-cyan-400 text-white text-sm font-semibold">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="hidden text-sm font-medium text-white lg:block">{{ auth()->user()->name }}</span>
                                <svg class="ml-1.5 w-3.5 h-3.5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            
                            <!-- Dropdown profil -->
                            <div class="absolute right-0 top-full mt-2 hidden w-48 origin-top-right rounded-xl bg-white dark:bg-gray-800 shadow-xl ring-1 ring-black/5" 
                                 id="user-dropdown">
                                <div class="p-1">
                                    <a href="{{ route('profile.edit') }}" 
                                       class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Mon profil
                                    </a>
                                    <div class="my-1 h-px bg-gray-100 dark:bg-gray-700"></div>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
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
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    @yield('page-actions')
                </div>
            @endif

            <!-- Alertes -->
            <div class="p-4 space-y-3">
                @if(session('success'))
                    <div class="flex items-center gap-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 px-4 py-3 text-emerald-800 dark:text-emerald-300 animate-fade-in" role="alert">
                        <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="flex-1 text-sm">{{ session('success') }}</span>
                        <button type="button" class="text-emerald-400 hover:text-emerald-600 transition-colors" onclick="this.parentElement.remove()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="flex items-center gap-3 rounded-xl bg-red-50 dark:bg-red-900/20 px-4 py-3 text-red-800 dark:text-red-300 animate-fade-in" role="alert">
                        <svg class="w-5 h-5 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="flex-1 text-sm">{{ session('error') }}</span>
                        <button type="button" class="text-red-400 hover:text-red-600 transition-colors" onclick="this.parentElement.remove()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="flex items-center gap-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 px-4 py-3 text-amber-800 dark:text-amber-300 animate-fade-in" role="alert">
                        <svg class="w-5 h-5 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="flex-1 text-sm">{{ session('warning') }}</span>
                        <button type="button" class="text-amber-400 hover:text-amber-600 transition-colors" onclick="this.parentElement.remove()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Contenu principal -->
            <div class="flex-1 p-4 lg:p-8" data-page-type="dashboard">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Auto-dismiss des alertes -->
    <script>
        // Theme Toggle
        (function() {
            const html = document.documentElement;
            const sun = document.getElementById('theme-icon-sun');
            const moon = document.getElementById('theme-icon-moon');
            const stored = localStorage.getItem('theme');

            if (stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                html.classList.add('dark');
                if (sun) sun.classList.remove('hidden');
                if (moon) moon.classList.add('hidden');
            } else {
                html.classList.remove('dark');
                if (sun) sun.classList.add('hidden');
                if (moon) moon.classList.remove('hidden');
            }

            document.getElementById('theme-toggle')?.addEventListener('click', function() {
                html.classList.toggle('dark');
                const isDark = html.classList.contains('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                if (sun) sun.classList.toggle('hidden', !isDark);
                if (moon) moon.classList.toggle('hidden', isDark);
            });
        })();
    </script>

    <script>
        document.querySelectorAll('[role="alert"]').forEach(function(a) {
            setTimeout(function() { a.style.transition = 'opacity .3s, transform .3s'; a.style.opacity = '0'; a.style.transform = 'translateY(-8px)'; setTimeout(function() { a.remove(); }, 300); }, 5000);
        });
    </script>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>

    <script>
        // Attendre que le DOM et jQuery soient complètement chargés
        document.addEventListener('DOMContentLoaded', function() {
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
            }

            // Fermer le sidebar sur clic overlay (mobile uniquement)
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
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

            // Toggle notifications dropdown
            if (notificationsDropdown && notificationsDropdownMenu) {
                notificationsDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notificationsDropdownMenu.classList.toggle('hidden');
                    userDropdownMenu.classList.add('hidden');
                });
            }

            // Toggle user dropdown
            if (userDropdown && userDropdownMenu) {
                userDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userDropdownMenu.classList.toggle('hidden');
                    notificationsDropdownMenu.classList.add('hidden');
                });
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
                return;
            }

            // Vérifier si on a un token CSRF (indicateur d'authentification)
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            if (!csrfToken) {
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
                            <a href="${notification.link}" class="block px-4 py-3 hover:bg-gray-50 dark:bg-gray-900 ${!notification.read_at ? 'bg-blue-50' : ''}">
                                <div class="flex items-center">
                                    <i class="fas ${notification.icon} mr-3 text-gray-400"></i>
                                    <div class="flex-1">
                                        <div class="text-xs text-gray-500 dark:text-gray-400">${notification.created_at}</div>
                                        <div class="text-sm text-gray-900 dark:text-white">${notification.message}</div>
                                    </div>
                                </div>
                            </a>
                        `;
                    });
                } else {
                    notificationsHtml = '<div class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Aucune notification</div>';
                }
                
                const container = document.getElementById('notifications-container');
                if (container) {
                    container.innerHTML = notificationsHtml;
                }
            }).fail(function(xhr, status, error) {
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

    <!-- Network Speed Adapter (doit être chargé en premier) -->
    <script src="{{ asset('js/network-adapter.js') }}?v={{ filemtime(public_path('js/network-adapter.js')) }}"></script>

    <!-- Admin Utils JavaScript -->
    <script src="{{ asset('js/admin-utils.js') }}"></script>

    <!-- Lazy Loading & PWA Scripts (dans le bon ordre) -->
    <script src="{{ asset('js/content-visibility.js') }}"></script>
    <script src="{{ asset('js/page-skeleton.js') }}"></script>
    <script>
        // S'assurer que PageSkeletonLoader est disponible globalement
        if (typeof PageSkeletonLoader !== 'undefined') {
            window.PageSkeletonLoader = PageSkeletonLoader;
        }
    </script>
    <script src="{{ asset('js/admin-skeleton-config.js') }}"></script>
    <script src="{{ asset('js/navigation-skeleton.js') }}"></script>
    <script src="{{ asset('js/lazy-loading.js') }}" defer></script>

    <!-- Expert Notifications System -->
    @if(auth()->check() && auth()->user()->isExpert())
    <script src="{{ asset('js/expert-notifications.js') }}"></script>
    @endif

    @stack('scripts')
</body>
</html>

