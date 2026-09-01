<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
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

    <link href="{{ asset('css/lazy-loading.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin-components.css') }}" rel="stylesheet">

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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous">

    @stack('styles')

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent }
        .custom-scrollbar::-webkit-scrollbar-thumb { @apply bg-slate-300 dark:bg-slate-600 rounded-full }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb { @apply bg-slate-400 dark:bg-slate-500 }
        @keyframes admin-fade-in { from { opacity: 0; transform: translateY(-6px) } to { opacity: 1; transform: translateY(0) } }
        .animate-fade-in { animation: admin-fade-in .35s ease-out }
        @keyframes admin-pop { from { opacity: 0; transform: scale(.97) translateY(4px) } to { opacity: 1; transform: scale(1) translateY(0) } }
        .animate-pop { animation: admin-pop .2s ease-out }
        /* ===== Sidebar shadcn (sidebar-07) ===== */
        #sidebar { width: 16rem; transition: width .2s ease-in-out, transform .3s ease-in-out }
        #sidebar.sidebar-collapsed { width: 4.75rem }
        #sidebar.sidebar-collapsed .sidebar-nav { padding-inline: .5rem }
        #sidebar.sidebar-collapsed .sidebar-link { justify-content: center; padding-inline: .5rem }
        #sidebar.sidebar-collapsed .sidebar-link > span { display: none }
        #sidebar.sidebar-collapsed .sidebar-section-title,
        #sidebar.sidebar-collapsed .sidebar-brand-block,
        #sidebar.sidebar-collapsed .sidebar-footer-text,
        #sidebar.sidebar-collapsed .sidebar-extra { display: none }
        #sidebar.sidebar-collapsed .sidebar-footer { padding-inline: .75rem }
        #sidebar.sidebar-collapsed .sidebar-link-btn { padding-inline: 0; border-color: transparent }
        .sidebar-brand-glyph { display: none }
        #sidebar.sidebar-collapsed .sidebar-brand-glyph { display: flex }
        @media (max-width: 1023px) {
            #sidebar { transform: translateX(-100%) }
            #sidebar.active { transform: translateX(0) }
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 font-sans text-sm leading-relaxed text-slate-800 antialiased dark:bg-slate-950 dark:text-slate-100">
    @php
        $isExpert = auth()->check() && auth()->user()->isExpert();

        $linkIdle = 'sidebar-link group flex items-center gap-3 rounded-lg px-3 py-2 text-[13px] font-medium text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white';
        $linkActive = 'sidebar-link group flex items-center gap-3 rounded-lg px-3 py-2 text-[13px] font-semibold bg-slate-200/80 text-slate-900 dark:bg-slate-800 dark:text-white';
        $sectionTitle = 'sidebar-section-title flex items-center gap-2 px-2 pt-5 pb-2';
        $sectionLabel = 'text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400 dark:text-slate-500';
        $icon = 'sidebar-icon w-4 h-4 shrink-0 opacity-80';
    @endphp

    <div class="flex min-h-screen">

        <!-- Fond décoratif neutre (permet au glassmorphisme de la sidebar de se voir) -->
        <div class="pointer-events-none fixed inset-0 -z-10 bg-slate-50 dark:bg-slate-950"></div>

        <!-- ===== Sidebar (shadcn) ===== -->
        <nav id="sidebar"
             class="fixed left-0 top-0 z-50 flex h-screen flex-col border-r border-slate-200 bg-white text-slate-600 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">

            <!-- Brand -->
            <div class="relative flex h-16 shrink-0 items-center gap-3 border-b border-slate-200 px-4 dark:border-slate-800">
                <i class="sidebar-brand-glyph fas fa-cube flex items-center text-lg text-slate-900 dark:text-white" aria-hidden="true"></i>
                <div class="sidebar-brand-block flex min-w-0 flex-1 items-center gap-2">
                    <x-app-brand
                        :show-logo="true"
                        :show-name="true"
                        logo-height="26px"
                        logo-width="84px"
                        name-size="1.05rem"
                        name-class="text-slate-900 font-bold dark:text-white"
                    />
                </div>
                <button type="button"
                        class="absolute right-2 top-1/2 -translate-y-1/2 flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600 dark:text-slate-500 dark:hover:bg-slate-800 dark:hover:text-white lg:hidden"
                        id="sidebar-close">
                    <i class="fas fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Navigation -->
            <div class="sidebar-nav flex-1 space-y-0.5 overflow-y-auto px-3 py-4 custom-scrollbar">
                @if($isExpert)
                    <!-- ===== Menu Expert ===== -->
                    <a href="{{ route('expert.dashboard') }}"
                       class="{{ request()->routeIs('expert.dashboard') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-chart-pie {{ $icon }}"></i>
                        <span>Dashboard Expert</span>
                    </a>

                    <div class="{{ $sectionTitle }}">
                        <span class="h-px flex-1 bg-slate-200 dark:bg-white/10"></span>
                        <span class="{{ $sectionLabel }}">Vérifications</span>
                        <span class="h-px flex-1 bg-slate-200 dark:bg-white/10"></span>
                    </div>

                    @php $pendingVerifications = \App\Models\ProductAuthenticityCheck::where('expert_id', auth()->id())->where('status', 'expert_review')->count(); @endphp
                    <a href="{{ route('expert.verifications.index') }}"
                       class="{{ request()->routeIs('expert.verifications*') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-search {{ $icon }}"></i>
                        <span class="flex-1">Mes Vérifications</span>
                        @if($pendingVerifications > 0)
                            <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-orange-500 px-1.5 text-[11px] font-bold text-white">{{ $pendingVerifications }}</span>
                        @endif
                    </a>

                    <a href="{{ route('expert.verifications.index', ['status' => 'expert_review']) }}"
                       class="{{ request()->routeIs('expert.verifications.index') && request('status') === 'expert_review' ? $linkActive : $linkIdle }}">
                        <i class="fas fa-clock {{ $icon }}"></i>
                        <span class="flex-1">En attente d'examen</span>
                        @if($pendingVerifications > 0)
                            <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 text-[11px] font-bold text-white animate-pulse">{{ $pendingVerifications }}</span>
                        @endif
                    </a>

                    <a href="{{ route('expert.verifications.index', ['status' => 'expert_approved']) }}"
                       class="{{ request()->routeIs('expert.verifications.index') && request('status') === 'expert_approved' ? $linkActive : $linkIdle }}">
                        <i class="fas fa-circle-check {{ $icon }}"></i>
                        <span>Approuvées</span>
                    </a>

                    <a href="{{ route('expert.verifications.index', ['status' => 'expert_rejected']) }}"
                       class="{{ request()->routeIs('expert.verifications.index') && request('status') === 'expert_rejected' ? $linkActive : $linkIdle }}">
                        <i class="fas fa-circle-xmark {{ $icon }}"></i>
                        <span>Rejetées</span>
                    </a>

                    @php $pendingItemsCount = \App\Models\Item::where('verification_status', 'pending')->whereNull('verified_at')->count(); @endphp
                    <a href="{{ route('expert.items.pending') }}"
                       class="{{ request()->routeIs('expert.items.*') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-boxes-stacked {{ $icon }}"></i>
                        <span class="flex-1">Articles à vérifier</span>
                        @if($pendingItemsCount > 0)
                            <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-purple-500 px-1.5 text-[11px] font-bold text-white animate-pulse">{{ $pendingItemsCount }}</span>
                        @endif
                    </a>

                    <div class="{{ $sectionTitle }}">
                        <span class="h-px flex-1 bg-slate-200 dark:bg-white/10"></span>
                        <span class="{{ $sectionLabel }}">Profil</span>
                        <span class="h-px flex-1 bg-slate-200 dark:bg-white/10"></span>
                    </div>

                    <a href="{{ route('expert.profile') }}"
                       class="{{ request()->routeIs('expert.profile*') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-id-badge {{ $icon }}"></i>
                        <span>Mon Profil Expert</span>
                    </a>

                    @php
                        $expertStats = [
                            'total' => \App\Models\ProductAuthenticityCheck::where('expert_id', auth()->id())->count(),
                            'completed_today' => \App\Models\ProductAuthenticityCheck::where('expert_id', auth()->id())->whereDate('expert_completed_at', today())->count(),
                            'approval_rate' => auth()->user()->expertProfile->approval_rate ?? 0,
                        ];
                    @endphp
                    <div class="sidebar-extra mt-5 rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-white/10 dark:bg-white/[0.04]">
                        <p class="mb-2.5 text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400">Statistiques</p>
                        <div class="space-y-1.5 text-xs">
                            <div class="flex items-center justify-between rounded-lg border border-slate-200/70 px-3 py-2 dark:border-white/5">
                                <span class="text-slate-400">Total traité</span>
                                <span class="font-semibold text-slate-900 dark:text-white">{{ $expertStats['total'] }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-lg border border-slate-200/70 px-3 py-2 dark:border-white/5">
                                <span class="text-slate-400">Aujourd'hui</span>
                                <span class="font-semibold text-emerald-400">{{ $expertStats['completed_today'] }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-lg border border-slate-200/70 px-3 py-2 dark:border-white/5">
                                <span class="text-slate-400">Taux succès</span>
                                <span class="font-semibold text-blue-400">{{ number_format($expertStats['approval_rate'], 1) }}%</span>
                            </div>
                        </div>
                    </div>

                @else
                    <!-- ===== Menu Admin ===== -->
                    <a href="{{ route('admin.dashboard') }}"
                       class="{{ request()->routeIs('admin.dashboard') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-gauge-high {{ $icon }}"></i>
                        <span>Tableau de bord</span>
                    </a>

                    <a href="{{ route('admin.users.index') }}"
                       class="{{ request()->routeIs('admin.users.index') || request()->routeIs('admin.users.show') || request()->routeIs('admin.users.edit') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-users {{ $icon }}"></i>
                        <span class="flex-1">Utilisateurs</span>
                        @if(isset($pendingUsersCount) && $pendingUsersCount > 0)
                            <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 text-[11px] font-bold text-white">{{ $pendingUsersCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.users.online') }}"
                       class="{{ request()->routeIs('admin.users.online') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-wifi {{ $icon }}"></i>
                        <span class="flex-1">Utilisateurs Connectés</span>
                        <span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-60"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-400"></span></span>
                    </a>

                    @php
                        $totalExperts = \App\Models\ExpertProfile::count();
                        $activeExperts = \App\Models\ExpertProfile::where('is_active', true)->count();
                    @endphp
                    <a href="{{ route('admin.experts.index') }}"
                       class="{{ request()->routeIs('admin.experts.*') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-user-shield {{ $icon }}"></i>
                        <span class="flex-1">Experts</span>
                        @if($totalExperts > 0)
                            <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-primary-500 px-1.5 text-[11px] font-bold text-white">{{ $activeExperts }}/{{ $totalExperts }}</span>
                        @endif
                    </a>

                    <div class="{{ $sectionTitle }}">
                        <span class="h-px flex-1 bg-slate-200 dark:bg-white/10"></span>
                        <span class="{{ $sectionLabel }}">Gestion</span>
                        <span class="h-px flex-1 bg-slate-200 dark:bg-white/10"></span>
                    </div>

                    <a href="{{ route('admin.transactions.index') }}"
                       class="{{ request()->routeIs('admin.transactions.*') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-arrow-right-arrow-left {{ $icon }}"></i>
                        <span>Transactions</span>
                    </a>

                    <a href="{{ route('admin.wallets.pending') }}"
                       class="{{ request()->routeIs('admin.wallets.pending') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-wallet {{ $icon }}"></i>
                        <span class="flex-1">Wallets en attente</span>
                        @if(isset($pendingWalletsCount) && $pendingWalletsCount > 0)
                            <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-yellow-400 px-1.5 text-[11px] font-bold text-slate-900">{{ $pendingWalletsCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.orders.index') }}"
                       class="{{ request()->routeIs('admin.orders.*') && !request()->routeIs('admin.orders.tracking') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-cart-shopping {{ $icon }}"></i>
                        <span>Commandes</span>
                    </a>

                    @php
                        $totalItems = \App\Models\Item::count();
                        $blockedItems = \App\Models\Item::where('is_blocked', true)->count();
                    @endphp
                    <a href="{{ route('admin.items.index') }}"
                       class="{{ request()->routeIs('admin.items.index') || request()->routeIs('admin.items.show') || request()->routeIs('admin.items.edit') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-box-open {{ $icon }}"></i>
                        <span class="flex-1">Articles</span>
                        @if($blockedItems > 0)
                            <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 text-[11px] font-bold text-white">{{ $blockedItems }}</span>
                        @else
                            <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-slate-500 px-1.5 text-[11px] font-bold text-white">{{ $totalItems }}</span>
                        @endif
                    </a>

                    @php
                        $pendingItemsCount = \App\Models\Item::where(function($q) { $q->where('verification_status', 'pending')->orWhereNull('verification_status'); })->where('status', '!=', 'sold')->count();
                    @endphp
                    <a href="{{ route('admin.items.pending_verification') }}"
                       class="{{ request()->routeIs('admin.items.pending_verification') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-lightbulb {{ $icon }}"></i>
                        <span class="flex-1">Vérification</span>
                        @if($pendingItemsCount > 0)
                            <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-purple-500 px-1.5 text-[11px] font-bold text-white animate-pulse">{{ $pendingItemsCount }}</span>
                        @endif
                    </a>

                    @php $pendingRefundsCount = \App\Models\Refund::where('status', 'pending')->count(); @endphp
                    <a href="{{ route('admin.refunds.index') }}"
                       class="{{ request()->routeIs('admin.refunds.*') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-rotate-left {{ $icon }}"></i>
                        <span class="flex-1">Remboursements</span>
                        @if($pendingRefundsCount > 0)
                            <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-orange-500 px-1.5 text-[11px] font-bold text-white animate-pulse">{{ $pendingRefundsCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.orders.tracking.list') }}"
                       class="{{ request()->routeIs('admin.orders.tracking*') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-location-dot {{ $icon }}"></i>
                        <span class="flex-1">Traçage GPS</span>
                        <span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-primary-400 opacity-60"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-primary-400"></span></span>
                    </a>

                    @php $activeBoostsCount = \App\Models\ProductBoost::where('status', 'active')->count(); @endphp
                    <a href="{{ route('admin.product-boosts.index') }}"
                       class="{{ request()->routeIs('admin.product-boosts.*') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-bolt {{ $icon }}"></i>
                        <span class="flex-1">Boosts appliqués</span>
                        @if($activeBoostsCount > 0)
                            <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-green-500 px-1.5 text-[11px] font-bold text-white">{{ $activeBoostsCount }}</span>
                        @endif
                    </a>

                    <div class="{{ $sectionTitle }}">
                        <span class="h-px flex-1 bg-slate-200 dark:bg-white/10"></span>
                        <span class="{{ $sectionLabel }}">Catalogue & Services</span>
                        <span class="h-px flex-1 bg-slate-200 dark:bg-white/10"></span>
                    </div>

                    <a href="{{ route('admin.brands.index') }}"
                       class="{{ request()->routeIs('admin.brands.*') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-tag {{ $icon }}"></i>
                        <span>Marques</span>
                    </a>

                    <a href="{{ route('admin.categories.index') }}"
                       class="{{ request()->routeIs('admin.categories.*') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-folder-tree {{ $icon }}"></i>
                        <span>Catégories</span>
                    </a>

                    <a href="{{ route('admin.offers.index') }}"
                       class="{{ request()->routeIs('admin.offers.*') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-tags {{ $icon }}"></i>
                        <span>Offres / Promos</span>
                    </a>

                    <a href="{{ route('admin.boost-types.index') }}"
                       class="{{ request()->routeIs('admin.boost-types.*') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-bolt-lightning {{ $icon }}"></i>
                        <span>Types de boost</span>
                    </a>

                    @php $unassignedSupport = \App\Models\SupportChat::whereNull('admin_id')->whereIn('status', ['open', 'in_progress'])->count(); @endphp
                    <a href="{{ route('admin.support.index') }}"
                       class="{{ request()->routeIs('admin.support.*') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-headset {{ $icon }}"></i>
                        <span class="flex-1">Support Client</span>
                        @if($unassignedSupport > 0)
                            <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-orange-500 px-1.5 text-[11px] font-bold text-white">{{ $unassignedSupport }}</span>
                        @endif
                    </a>

                    @php $topPerformersCount = \App\Models\User::whereHas('referrals', function($q) { $q->whereDate('created_at', '>=', now()->subDays(30)); })->count(); @endphp
                    <a href="{{ route('admin.affiliate.index') }}"
                       class="{{ request()->routeIs('admin.affiliate.*') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-link {{ $icon }}"></i>
                        <span class="flex-1">Affiliation</span>
                        @if($topPerformersCount > 0)
                            <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-yellow-400 px-1.5 text-[11px] font-bold text-slate-900">{{ $topPerformersCount }}</span>
                        @endif
                    </a>

                    <div class="{{ $sectionTitle }}">
                        <span class="h-px flex-1 bg-slate-200 dark:bg-white/10"></span>
                        <span class="{{ $sectionLabel }}">Système</span>
                        <span class="h-px flex-1 bg-slate-200 dark:bg-white/10"></span>
                    </div>

                    <a href="{{ route('admin.reports') }}"
                       class="{{ request()->routeIs('admin.reports') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-chart-column {{ $icon }}"></i>
                        <span>Rapports</span>
                    </a>

                    <a href="{{ route('admin.monitoring.index') }}"
                       class="{{ request()->routeIs('admin.monitoring.*') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-heart-pulse {{ $icon }}"></i>
                        <span class="flex-1">Monitoring</span>
                        <span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-60"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-400"></span></span>
                    </a>

                    <a href="{{ route('admin.logs') }}"
                       class="{{ request()->routeIs('admin.logs') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-file-lines {{ $icon }}"></i>
                        <span>Logs système</span>
                    </a>

                    <a href="{{ route('admin.settings.index') }}"
                       class="{{ request()->routeIs('admin.settings.*') && !request()->routeIs('admin.locations.*') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-gear {{ $icon }}"></i>
                        <span>Paramètres</span>
                    </a>

                    <a href="{{ route('admin.locations.index') }}"
                       class="{{ request()->routeIs('admin.locations.*') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-map-location-dot {{ $icon }}"></i>
                        <span>Zones autorisées</span>
                    </a>

                    <a href="{{ route('admin.broadcast.fcm') }}"
                       class="{{ request()->routeIs('admin.broadcast.fcm') ? $linkActive : $linkIdle }}">
                        <i class="fas fa-bullhorn {{ $icon }}"></i>
                        <span class="flex-1">Broadcast Push</span>
                        <span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-orange-400 opacity-60"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-orange-400"></span></span>
                    </a>
                @endif
            </div>

            <!-- Footer -->
            <div class="sidebar-footer mt-auto space-y-2 border-t border-slate-200 p-4 dark:border-slate-800">
                <a href="{{ route('home') }}"
                   class="sidebar-link-btn flex w-full items-center justify-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-[13px] font-medium text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900 dark:border-white/15 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span class="sidebar-footer-text">Retour au site</span>
                </a>
                @if($isExpert)
                    <div class="sidebar-extra py-1 text-center text-[11px] text-slate-400 dark:text-slate-500">Interface Expert VintApp</div>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit"
                            class="sidebar-link-btn flex w-full items-center justify-center gap-2 rounded-lg bg-slate-900 px-3 py-2 text-[13px] font-medium text-white transition-colors hover:bg-slate-700 dark:bg-white/10 dark:text-white dark:hover:bg-white/20">
                        <i class="fas fa-right-from-bracket text-xs"></i>
                        <span class="sidebar-footer-text">Déconnexion</span>
                    </button>
                </form>
            </div>
        </nav>

        <!-- Overlay mobile -->
        <div class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300 lg:hidden" id="sidebar-overlay" style="display: none; opacity: 0;"></div>

        <!-- ===== Contenu principal ===== -->
        <div class="flex min-h-screen w-full flex-col transition-all duration-300" id="main-content">

            <!-- Topbar -->
            <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/85 backdrop-blur-md dark:border-slate-800 dark:bg-slate-900/85">
                <div class="flex h-16 items-center gap-2 px-4 sm:gap-3 sm:px-6">
                    <!-- Toggle sidebar -->
                    <button class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500/40 dark:text-slate-400 dark:hover:bg-slate-800"
                            id="sidebar-toggle" aria-label="Replier/ouvrir la sidebar">
                        <i class="fas fa-bars text-sm"></i>
                    </button>

                    <span class="hidden h-6 w-px bg-slate-200 sm:block dark:bg-slate-700" aria-hidden="true"></span>

                    <!-- Titre de page -->
                    <div class="min-w-0 flex-1">
                        <h1 class="truncate text-[15px] font-semibold tracking-tight text-slate-900 dark:text-white">@yield('page-title')</h1>
                        <p class="hidden truncate text-xs text-slate-500 sm:block dark:text-slate-400">@yield('page-subtitle', 'Gestion de votre plateforme')</p>
                    </div>

                    <div class="ml-auto flex items-center gap-1.5 sm:gap-2">
                        <!-- Notifications -->
                        <div class="relative">
                            <button class="relative flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500/40 dark:text-slate-400 dark:hover:bg-slate-800"
                                    type="button" id="notificationsDropdown">
                                <i class="fas fa-bell text-sm"></i>
                                <span class="notification-dot absolute right-1.5 top-1.5 hidden h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white ring-2 ring-white dark:ring-slate-900" id="notification-badge">0</span>
                            </button>

                            <div class="absolute right-0 top-full mt-2 hidden w-80 origin-top-right animate-pop rounded-2xl bg-white p-0 shadow-xl ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700 sm:w-96" id="notifications-dropdown">
                                <div class="flex items-center justify-between rounded-t-2xl border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Notifications</h3>
                                    <span class="rounded-full bg-primary-50 px-2 py-0.5 text-[11px] font-semibold text-primary-600 dark:bg-primary-900/30 dark:text-primary-300" id="notification-count-label">0</span>
                                </div>
                                <div class="max-h-96 overflow-y-auto" id="notifications-container"></div>
                                <div class="rounded-b-2xl border-t border-slate-100 p-2 dark:border-slate-700">
                                    <a href="{{ route('admin.notifications') }}"
                                       class="block rounded-xl px-4 py-2.5 text-center text-sm font-medium text-primary-600 transition-colors hover:bg-primary-50 dark:text-primary-300 dark:hover:bg-primary-900/20">
                                        Voir toutes les notifications
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Theme Toggle (shadcn switch : light/dark) -->
                        <label class="flex h-9 shrink-0 cursor-pointer items-center gap-2 rounded-lg px-1.5 transition-colors hover:bg-slate-100 dark:hover:bg-slate-800"
                               for="theme-switch" title="Changer le thème">
                            <i id="theme-icon-sun" class="fas fa-sun text-xs text-slate-400 dark:hidden" aria-hidden="true"></i>
                            <i id="theme-icon-moon" class="fas fa-moon hidden text-xs text-slate-400 dark:block" aria-hidden="true"></i>
                            <input type="checkbox" id="theme-switch" class="sr-only peer" aria-label="Changer le thème">
                            <span class="relative inline-flex h-5 w-9 shrink-0 items-center rounded-full border-2 border-transparent bg-slate-300 transition-colors duration-200 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500/40 dark:bg-primary-600">
                                <span class="pointer-events-none block h-4 w-4 rounded-full bg-white shadow transition-transform duration-200 dark:translate-x-4"></span>
                            </span>
                        </label>

                        <span class="hidden h-6 w-px bg-slate-200 sm:block dark:bg-slate-700" aria-hidden="true"></span>

                        <!-- Profil -->
                        <div class="relative">
                            <button class="flex items-center gap-2 rounded-lg py-1 pl-1 pr-2 transition-colors hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500/40 dark:hover:bg-slate-800"
                                    type="button" id="userDropdown">
                                @if(auth()->user()->avatar)
                                    @php
                                        $avatarUrl = filter_var(auth()->user()->avatar, FILTER_VALIDATE_URL)
                                            ? auth()->user()->avatar
                                            : asset('storage/' . auth()->user()->avatar);
                                    @endphp
                                    <img src="{{ $avatarUrl }}"
                                         alt="{{ auth()->user()->name }}"
                                         class="h-8 w-8 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-600"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="hidden h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-primary-600 to-cyan-400 text-sm font-semibold text-white">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                @else
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-primary-600 to-cyan-400 text-sm font-semibold text-white">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="hidden text-sm font-medium text-slate-700 dark:text-slate-200 lg:block">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                            </button>

                            <div class="absolute right-0 top-full mt-2 hidden w-52 origin-top-right animate-pop rounded-2xl bg-white p-1.5 shadow-xl ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700" id="user-dropdown">
                                <div class="border-b border-slate-100 px-3 py-2.5 dark:border-slate-700">
                                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ auth()->user()->name }}</p>
                                    <p class="truncate text-xs text-slate-400">{{ auth()->user()->email }}</p>
                                </div>
                                <div class="p-1">
                                    <a href="{{ route('profile.edit') }}"
                                       class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700">
                                        <i class="fas fa-user w-4 text-slate-400"></i>
                                        Mon profil
                                    </a>
                                    <div class="my-1 h-px bg-slate-100 dark:bg-slate-700"></div>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20">
                                            <i class="fas fa-right-from-bracket w-4"></i>
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
                <div class="border-b border-slate-200 bg-white/60 px-4 py-3 sm:px-6 dark:border-slate-700 dark:bg-slate-800/40">
                    @yield('page-actions')
                </div>
            @endif

            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                <!-- Alertes -->
                <div class="space-y-3">
                    @if(session('success'))
                        <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 animate-fade-in dark:border-emerald-900/30 dark:bg-emerald-900/20 dark:text-emerald-300" role="alert">
                            <i class="fas fa-circle-check text-emerald-500"></i>
                            <span class="flex-1">{{ session('success') }}</span>
                            <button type="button" class="text-emerald-400 transition-colors hover:text-emerald-600" onclick="this.parentElement.remove()">
                                <i class="fas fa-xmark"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 animate-fade-in dark:border-red-900/30 dark:bg-red-900/20 dark:text-red-300" role="alert">
                            <i class="fas fa-circle-exclamation text-red-500"></i>
                            <span class="flex-1">{{ session('error') }}</span>
                            <button type="button" class="text-red-400 transition-colors hover:text-red-600" onclick="this.parentElement.remove()">
                                <i class="fas fa-xmark"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="flex items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 animate-fade-in dark:border-amber-900/30 dark:bg-amber-900/20 dark:text-amber-300" role="alert">
                            <i class="fas fa-triangle-exclamation text-amber-500"></i>
                            <span class="flex-1">{{ session('warning') }}</span>
                            <button type="button" class="text-amber-400 transition-colors hover:text-amber-600" onclick="this.parentElement.remove()">
                                <i class="fas fa-xmark"></i>
                            </button>
                        </div>
                    @endif
                </div>

                <div class="mt-6" data-page-type="dashboard">
                    @yield('content')
                </div>
            </main>

            <footer class="border-t border-slate-200 px-6 py-4 text-center text-xs text-slate-400 dark:border-slate-700 dark:text-slate-500">
                © {{ date('Y') }} {{ $appName ?? 'VintApp' }} — Interface {{ $contextTitle }}
            </footer>
        </div>
    </div>

    <!-- Scripts globaux -->
    <script>
        (function() {
            const html = document.documentElement;
            const stored = localStorage.getItem('theme');
            const isDark = stored === 'dark'
                || ((stored === 'auto' || !stored) && window.matchMedia('(prefers-color-scheme: dark)').matches);
            html.classList.toggle('dark', isDark);

            const themeSwitch = document.getElementById('theme-switch');
            if (themeSwitch) {
                themeSwitch.checked = isDark;
                themeSwitch.addEventListener('change', function() {
                    const dark = html.classList.toggle('dark');
                    localStorage.setItem('theme', dark ? 'dark' : 'light');
                });
            }
        })();
    </script>

    <script>
        document.querySelectorAll('[role="alert"]').forEach(function(a) {
            setTimeout(function() { a.style.transition = 'opacity .3s, transform .3s'; a.style.opacity = '0'; a.style.transform = 'translateY(-8px)'; setTimeout(function() { a.remove(); }, 300); }, 5000);
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof $ !== 'undefined') {
                $('.select2').select2();
                flatpickr('.datepicker', { locale: 'fr', dateFormat: 'Y-m-d', allowInput: true });
                flatpickr('.datetimepicker', { locale: 'fr', dateFormat: 'Y-m-d H:i', enableTime: true, time_24hr: true, allowInput: true });
            }

            // ===== Sidebar responsive (sidebar-07) =====
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarClose = document.getElementById('sidebar-close');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            const mainContent = document.getElementById('main-content');
            const SIDEBAR_EXPANDED = 256;
            const SIDEBAR_COLLAPSED = 76;

            function setDesktop(state) {
                sidebar.classList.toggle('sidebar-collapsed', !state);
                sidebar.classList.remove('active');
                sidebar.style.transform = '';
                mainContent.style.marginLeft = (state ? SIDEBAR_EXPANDED : SIDEBAR_COLLAPSED) + 'px';
                if (sidebarOverlay) { sidebarOverlay.style.display = 'none'; sidebarOverlay.style.opacity = '0'; }
                try { localStorage.setItem('sidebarCollapsed', state ? '0' : '1'); } catch (e) {}
            }
            function setMobile(state) {
                sidebar.classList.toggle('active', state);
                sidebar.style.transform = state ? 'translateX(0)' : 'translateX(-100%)';
                if (sidebarOverlay) {
                    if (state) { sidebarOverlay.style.display = 'block'; setTimeout(() => sidebarOverlay.style.opacity = '1', 10); }
                    else { sidebarOverlay.style.opacity = '0'; setTimeout(() => sidebarOverlay.style.display = 'none', 300); }
                }
            }

            function initSidebar() {
                if (window.innerWidth >= 1024) {
                    let saved = '0';
                    try { saved = localStorage.getItem('sidebarCollapsed') || '0'; } catch (e) {}
                    setDesktop(saved !== '1');
                } else {
                    setMobile(false);
                }
            }
            initSidebar();

            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() { if (!sidebar.classList.contains('active')) initSidebar(); }, 250);
            });

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    if (window.innerWidth >= 1024) {
                        setDesktop(sidebar.classList.contains('sidebar-collapsed'));
                    } else {
                        setMobile(!sidebar.classList.contains('active'));
                    }
                });
            }
            if (sidebarClose) {
                sidebarClose.addEventListener('click', function() { setMobile(false); });
            }
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() { setMobile(false); });
            }
            const sidebarLinks = sidebar.querySelectorAll('a');
            sidebarLinks.forEach(link => link.addEventListener('click', function() { if (window.innerWidth < 1024) setMobile(false); }));

            // ===== Dropdowns =====
            const notificationsDropdown = document.getElementById('notificationsDropdown');
            const notificationsDropdownMenu = document.getElementById('notifications-dropdown');
            const userDropdown = document.getElementById('userDropdown');
            const userDropdownMenu = document.getElementById('user-dropdown');

            function closeDropdowns() {
                if (notificationsDropdownMenu) notificationsDropdownMenu.classList.add('hidden');
                if (userDropdownMenu) userDropdownMenu.classList.add('hidden');
            }

            if (notificationsDropdown && notificationsDropdownMenu) {
                notificationsDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isHidden = notificationsDropdownMenu.classList.contains('hidden');
                    closeDropdowns();
                    if (isHidden) notificationsDropdownMenu.classList.remove('hidden');
                });
            }
            if (userDropdown && userDropdownMenu) {
                userDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isHidden = userDropdownMenu.classList.contains('hidden');
                    closeDropdowns();
                    if (isHidden) userDropdownMenu.classList.remove('hidden');
                });
            }
            document.addEventListener('click', closeDropdowns);

            // ===== Modales vanilla (compat data-bs) =====
            document.querySelectorAll('[data-bs-toggle="modal"]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const target = this.getAttribute('data-bs-target');
                    const modal = document.querySelector(target);
                    if (modal) {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        document.body.style.overflow = 'hidden';
                    }
                });
            });
            document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const modal = this.closest('[data-bs-toggle-target]') || this.closest('.modal-wrapper');
                    if (modal) {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        document.body.style.overflow = '';
                    }
                });
            });
            document.querySelectorAll('.modal-wrapper').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        document.body.style.overflow = '';
                    }
                });
            });
        });

        // ===== Notifications =====
        function fetchNotifications() {
            if (typeof $ === 'undefined') return;
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            if (!csrfToken) return;

            $.get('/admin/notifications', function(data) {
                const badge = document.getElementById('notification-badge');
                const countLabel = document.getElementById('notification-count-label');
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count;
                    badge.classList.remove('hidden');
                    if (countLabel) countLabel.textContent = data.unread_count;
                } else {
                    badge.classList.add('hidden');
                    if (countLabel) countLabel.textContent = '0';
                }

                let html = '';
                if (data.notifications.length > 0) {
                    data.notifications.forEach(notification => {
                        html += `
                            <a href="${notification.link}" class="block px-5 py-3.5 transition-colors hover:bg-slate-50 dark:hover:bg-slate-700/40 ${!notification.read_at ? 'bg-primary-50/60 dark:bg-primary-900/10' : ''}">
                                <div class="flex items-start gap-3">
                                    <i class="fas ${notification.icon} mt-0.5 text-slate-400"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-slate-800 dark:text-white leading-snug">${notification.message}</p>
                                        <p class="text-xs text-slate-400 mt-0.5">${notification.created_at}</p>
                                    </div>
                                    ${!notification.read_at ? '<span class="mt-1.5 h-2 w-2 rounded-full bg-primary-500 flex-shrink-0"></span>' : ''}
                                </div>
                            </a>`;
                    });
                } else {
                    html = '<div class="px-4 py-10 text-center text-sm text-slate-400"><i class="fas fa-bell-slash text-2xl text-slate-200 dark:text-slate-600 mb-2 block"></i>Aucune notification</div>';
                }
                const container = document.getElementById('notifications-container');
                if (container) container.innerHTML = html;
            }).fail(function() {
                const badge = document.getElementById('notification-badge');
                if (badge) badge.classList.add('hidden');
            });
        }

        if (typeof $ !== 'undefined') {
            fetchNotifications();
            setInterval(fetchNotifications, 30000);
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        }
    </script>

    <script src="{{ asset('js/network-adapter.js') }}?v={{ filemtime(public_path('js/network-adapter.js')) }}"></script>
    <script src="{{ asset('js/admin-utils.js') }}"></script>
    <script src="{{ asset('js/admin-confirm.js') }}"></script>
    <script src="{{ asset('js/content-visibility.js') }}"></script>
    <script src="{{ asset('js/page-skeleton.js') }}"></script>
    <script>
        if (typeof PageSkeletonLoader !== 'undefined') { window.PageSkeletonLoader = PageSkeletonLoader; }
    </script>
    <script src="{{ asset('js/admin-skeleton-config.js') }}"></script>
    <script src="{{ asset('js/navigation-skeleton.js') }}"></script>
    <script src="{{ asset('js/lazy-loading.js') }}" defer></script>

    @if(auth()->check() && auth()->user()->isExpert())
    <script src="{{ asset('js/expert-notifications.js') }}"></script>
    @endif

    {{-- Toast Notification --}}
    <div id="toast-container" class="fixed top-5 right-5 z-[9999] flex flex-col gap-3 max-w-sm w-full pointer-events-none"></div>

    <style>
        @keyframes toast-slide-in { from { opacity: 0; transform: translateX(100%); } to { opacity: 1; transform: translateX(0); } }
        @keyframes toast-slide-out { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(100%); } }
        .toast-enter { animation: toast-slide-in 0.3s ease-out forwards; }
        .toast-exit { animation: toast-slide-out 0.3s ease-in forwards; }
    </style>

    <script>
    function showToast(message, type = 'success', duration = 4000) {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };
        const colors = {
            success: 'bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-900/30 dark:border-emerald-700 dark:text-emerald-200',
            error: 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/30 dark:border-red-700 dark:text-red-200',
            warning: 'bg-amber-50 border-amber-200 text-amber-800 dark:bg-amber-900/30 dark:border-amber-700 dark:text-amber-200',
            info: 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/30 dark:border-blue-700 dark:text-blue-200'
        };
        const iconColors = {
            success: 'text-emerald-500',
            error: 'text-red-500',
            warning: 'text-amber-500',
            info: 'text-blue-500'
        };

        const toast = document.createElement('div');
        toast.className = `pointer-events-auto flex items-start gap-3 p-4 rounded-xl border shadow-lg backdrop-blur-sm ${colors[type]} toast-enter`;
        toast.innerHTML = `
            <i class="fas ${icons[type]} mt-0.5 text-lg ${iconColors[type]} flex-shrink-0"></i>
            <p class="text-sm font-medium leading-snug flex-1">${message}</p>
            <button onclick="dismissToast(this)" class="flex-shrink-0 ml-2 opacity-60 hover:opacity-100 transition-opacity">
                <i class="fas fa-times text-xs"></i>
            </button>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.remove('toast-enter');
            toast.classList.add('toast-exit');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    function dismissToast(btn) {
        const toast = btn.closest('div');
        toast.classList.remove('toast-enter');
        toast.classList.add('toast-exit');
        setTimeout(() => toast.remove(), 300);
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif
        @if(session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif
        @if(session('warning'))
            showToast('{{ session('warning') }}', 'warning');
        @endif
        @if(session('info'))
            showToast('{{ session('info') }}', 'info');
        @endif
    });
    </script>

    @stack('scripts')
</body>
</html>
