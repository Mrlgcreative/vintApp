@extends('app')

@section('title', 'Mon Profil - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-gray-900">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">

        <!-- Profile header -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sm:p-8 mb-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-center gap-6">
                <!-- Avatar -->
                <div class="flex-shrink-0">
                    @php
                        $avatarUrl = $user->avatar_url;
                    @endphp
                    @if($avatarUrl)
                        <img src="{{ $avatarUrl }}"
                             alt="{{ $user->name }}"
                             class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover ring-2 ring-primary-100 dark:ring-primary-800"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-primary text-white flex items-center justify-center text-2xl sm:text-3xl font-bold ring-2 ring-primary-100 dark:ring-primary-800 hidden">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    @else
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-primary text-white flex items-center justify-center text-2xl sm:text-3xl font-bold ring-2 ring-primary-100 dark:ring-primary-800">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    @endif
                </div>

                <!-- Identity -->
                <div class="flex-1 text-center sm:text-left">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">{{ $user->email }}</p>
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-3 text-sm text-gray-600 dark:text-gray-400">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Membre depuis {{ $user->created_at->translatedFormat('F Y') }}
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex-shrink-0">
                    <a href="{{ route('profile.edit') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-600 text-white rounded-xl font-semibold text-sm shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-primary-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier le profil
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            @php
                $statCards = [
                    ['label' => 'Articles publiés', 'value' => $stats['total_items'], 'sub' => $stats['active_items'] . ' actifs',
                     'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                    ['label' => 'Commandes', 'value' => $stats['total_orders'], 'sub' => $stats['completed_orders'] . ' complétées',
                     'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
                    ['label' => 'Revenus (USD)', 'value' => number_format($stats['total_revenue'], 2), 'sub' => number_format($stats['total_revenue'] * 2500, 0) . ' FC',
                     'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['label' => 'Note moyenne', 'value' => number_format($stats['average_rating'], 1), 'sub' => 'sur 5',
                     'icon' => 'M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z'],
                ];
            @endphp
            @foreach ($statCards as $card)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-lg bg-primary-400 flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $card['value'] }}</p>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 mt-0.5">{{ $card['label'] }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">{{ $card['sub'] }}</p>
                </div>
            @endforeach
        </div>

        <!-- Quick actions -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Tableau de bord</h2>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $actions = [
                        ['route' => route('items.my-items'), 'label' => 'Mes articles', 'desc' => $stats['active_items'] . ' actifs · ' . $stats['sold_items'] . ' vendus',
                         'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                        ['route' => route('orders.index'), 'label' => 'Commandes', 'desc' => $stats['total_orders'] . ' commande(s)',
                         'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
                        ['route' => route('vintpass.index'), 'label' => 'VintPass', 'desc' => 'Certificats d\'authenticité blockchain',
                         'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                        ['route' => route('messages.index'), 'label' => 'Messages', 'desc' => $stats['unread_messages'] > 0 ? $stats['unread_messages'] . ' non lu(s)' : 'Aucun nouveau',
                         'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
                        ['route' => route('wallet.index'), 'label' => 'Wallet', 'desc' => 'Paiements et transactions',
                         'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                        ['route' => route('profile.stats'), 'label' => 'Statistiques', 'desc' => 'Analyse de vos performances',
                         'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    ];
                @endphp
                @foreach ($actions as $action)
                    <a href="{{ $action['route'] }}"
                       class="group flex items-start gap-4 p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-primary-200 dark:hover:border-primary-700 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-all duration-200">
                        <div class="w-11 h-11 rounded-lg bg-primary-400 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $action['label'] }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ $action['desc'] }}</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-primary-500 group-hover:translate-x-0.5 transition-all duration-200 flex-shrink-0 self-center" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Activity -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Activité récente</h2>
            </div>
            <div class="p-5 space-y-3">
                @if($stats['unread_messages'] > 0)
                    <div class="flex items-start gap-4 p-4 rounded-xl border border-l-4 border-l-primary-500 border-gray-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-900">
                        <div class="w-10 h-10 rounded-lg bg-primary-400 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Vous avez <span class="text-primary-600 dark:text-primary-400">{{ $stats['unread_messages'] }}</span> message(s) non lu(s)</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Consultez vos messages pour rester en contact.</p>
                            <a href="{{ route('messages.index') }}" class="inline-flex items-center gap-1 mt-2 text-sm font-medium text-primary dark:text-primary-400 hover:text-primary-600">Les consulter maintenant
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                        </div>
                    </div>
                @endif

                @if($stats['active_items'] > 0)
                    <div class="flex items-start gap-4 p-4 rounded-xl border border-l-4 border-l-success-500 border-gray-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-900">
                        <div class="w-10 h-10 rounded-lg bg-success-400 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-success-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Vous avez <span class="text-success-600 dark:text-success-400">{{ $stats['active_items'] }}</span> article(s) en ligne</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Vos articles sont visibles par des milliers d'acheteurs potentiels.</p>
                            <a href="{{ route('items.my-items') }}" class="inline-flex items-center gap-1 mt-2 text-sm font-medium text-success-600 dark:text-success-400 hover:text-success-700">Les gérer
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                        </div>
                    </div>
                @endif

                @if($stats['total_items'] === 0)
                    <div class="flex items-start gap-4 p-4 rounded-xl border border-l-4 border-l-warning-500 border-gray-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-900">
                        <div class="w-10 h-10 rounded-lg bg-warning-400 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-warning-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Commencez à vendre dès maintenant</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Publiez votre premier article et commencez à gagner de l'argent.</p>
                            <a href="{{ route('items.create') }}" class="inline-flex items-center gap-1 mt-2 text-sm font-medium text-warning-600 dark:text-warning-400 hover:text-warning-700">Publier votre premier article
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                        </div>
                    </div>
                @endif

                @if($stats['active_items'] === 0 && $stats['total_items'] > 0)
                    <div class="flex items-start gap-4 p-4 rounded-xl border border-l-4 border-l-primary-500 border-gray-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-900">
                        <div class="w-10 h-10 rounded-lg bg-primary-400 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Tous vos articles ont été vendus !</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Félicitations ! Publiez de nouveaux articles pour continuer.</p>
                            <a href="{{ route('items.create') }}" class="inline-flex items-center gap-1 mt-2 text-sm font-medium text-primary dark:text-primary-400 hover:text-primary-600">Publier un nouvel article
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
