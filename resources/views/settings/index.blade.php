@extends('app')

@section('title', 'Paramètres - VintApp')

@section('meta_description', 'Gérez vos préférences, votre profil et les paramètres de votre compte VintApp.')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-900 py-6 sm:py-10 animate-fade-in-up">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex items-center gap-4">
                <button onclick="history.back()" class="flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 shadow-sm transition-colors hover:bg-slate-50 hover:text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white">
                    <i class="fas fa-arrow-left text-base"></i>
                </button>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl">Paramètres</h1>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Gérez vos préférences et votre compte</p>
                </div>
            </div>
        </div>

        <!-- Carte profil -->
        <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center gap-4 sm:gap-5">
                <div class="relative flex-shrink-0">
                    @if(Auth::user()->avatar)
                        @php
                            $avatarUrl = filter_var(Auth::user()->avatar, FILTER_VALIDATE_URL)
                                ? Auth::user()->avatar
                                : asset('storage/' . Auth::user()->avatar);
                        @endphp
                        <img src="{{ $avatarUrl }}"
                             alt="{{ Auth::user()->name }}"
                             class="h-16 w-16 rounded-full object-cover ring-4 ring-primary-100 dark:ring-primary-900/40"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="hidden h-16 w-16 items-center justify-center rounded-full bg-gradient-to-r from-primary-600 to-cyan-400 text-xl font-bold text-white">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                    @else
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-r from-primary-600 to-cyan-400 text-xl font-bold text-white">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="min-w-0">
                    <h2 class="truncate text-xl font-semibold text-slate-900 dark:text-white">{{ Auth::user()->name }}</h2>
                    <p class="truncate text-sm text-slate-500 dark:text-slate-400">{{ Auth::user()->email }}</p>
                    <span class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        Compte actif
                    </span>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Mon compte -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6 dark:border-slate-700/60">
                    <h3 class="flex items-center gap-3 text-base font-semibold text-slate-900 dark:text-white">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-100 text-primary-600 dark:bg-primary-900/40 dark:text-primary-400">
                            <i class="fas fa-user-circle"></i>
                        </span>
                        Mon compte
                    </h3>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    <a href="{{ route('profile.edit') }}" class="group block px-5 py-4 transition-colors hover:bg-slate-50 sm:px-6 dark:hover:bg-slate-700/30">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600 transition-transform duration-200 group-hover:scale-110 dark:bg-blue-900/30 dark:text-blue-400">
                                    <i class="fas fa-user-cog text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-900 dark:text-white">Modifier mon profil</h4>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Informations personnelles, photo de profil</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-slate-300 transition-colors group-hover:text-slate-500 dark:text-slate-600 dark:group-hover:text-slate-300"></i>
                        </div>
                    </a>

                    <button onclick="openPersonalizationModal()" class="group block w-full px-5 py-4 text-left transition-colors hover:bg-slate-50 sm:px-6 dark:hover:bg-slate-700/30">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 transition-transform duration-200 group-hover:scale-110 dark:bg-emerald-900/30 dark:text-emerald-400">
                                    <i class="fas fa-cogs text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-900 dark:text-white">Personnalisation</h4>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Préférences d'affichage et notifications</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-slate-300 transition-colors group-hover:text-slate-500 dark:text-slate-600 dark:group-hover:text-slate-300"></i>
                        </div>
                    </button>

                    <button onclick="openThemeModal()" class="group block w-full px-5 py-4 text-left transition-colors hover:bg-slate-50 sm:px-6 dark:hover:bg-slate-700/30">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600 transition-transform duration-200 group-hover:scale-110 dark:bg-amber-900/30 dark:text-amber-400">
                                    <i class="fas fa-palette text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-900 dark:text-white">Thème d'affichage</h4>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Clair, Sombre ou Automatique</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span id="current-theme-badge" class="rounded-full bg-slate-100 px-3 py-1 text-sm font-medium text-slate-600 dark:bg-slate-700 dark:text-slate-300">Auto</span>
                                <i class="fas fa-chevron-right text-slate-300 transition-colors group-hover:text-slate-500 dark:text-slate-600 dark:group-hover:text-slate-300"></i>
                            </div>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Navigation rapide -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6 dark:border-slate-700/60">
                    <h3 class="flex items-center gap-3 text-base font-semibold text-slate-900 dark:text-white">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-100 text-primary-600 dark:bg-primary-900/40 dark:text-primary-400">
                            <i class="fas fa-compass"></i>
                        </span>
                        Navigation rapide
                    </h3>
                </div>
                <div class="grid grid-cols-1 divide-y divide-slate-100 md:grid-cols-2 md:divide-x md:divide-y-0 dark:divide-slate-700/50">
                    <div class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @if(Auth::user()->isSeller())
                        <a href="{{ route('dashboard') }}" class="group block px-5 py-4 transition-colors hover:bg-slate-50 sm:px-6 dark:hover:bg-slate-700/30">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 transition-transform duration-200 group-hover:scale-110 dark:bg-indigo-900/30 dark:text-indigo-400">
                                    <i class="fas fa-tachometer-alt"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-slate-900 dark:text-white">Dashboard</h4>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Vue d'ensemble</p>
                                </div>
                            </div>
                        </a>
                        @endif

                        <a href="{{ route('items.favorites') }}" class="group block px-5 py-4 transition-colors hover:bg-slate-50 sm:px-6 dark:hover:bg-slate-700/30">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100 text-rose-600 transition-transform duration-200 group-hover:scale-110 dark:bg-rose-900/30 dark:text-rose-400">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-slate-900 dark:text-white">Mes favoris</h4>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Articles sauvegardés</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('orders.index') }}" class="group block px-5 py-4 transition-colors hover:bg-slate-50 sm:px-6 dark:hover:bg-slate-700/30">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-orange-600 transition-transform duration-200 group-hover:scale-110 dark:bg-orange-900/30 dark:text-orange-400">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-slate-900 dark:text-white">Mes commandes</h4>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Historique d'achats</p>
                                </div>
                            </div>
                        </a>

                        @if(Auth::user()->isSeller())
                        <a href="{{ route('seller.sales') }}" class="group block px-5 py-4 transition-colors hover:bg-slate-50 sm:px-6 dark:hover:bg-slate-700/30">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 transition-transform duration-200 group-hover:scale-110 dark:bg-emerald-900/30 dark:text-emerald-400">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-slate-900 dark:text-white">Mes ventes</h4>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Articles vendus</p>
                                </div>
                            </div>
                        </a>
                        @endif
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @if(Auth::user()->isSeller())
                        <a href="{{ route('seller.items') }}" class="group block px-5 py-4 transition-colors hover:bg-slate-50 sm:px-6 dark:hover:bg-slate-700/30">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-100 text-primary-600 transition-transform duration-200 group-hover:scale-110 dark:bg-primary-900/30 dark:text-primary-400">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-slate-900 dark:text-white">Mes articles</h4>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Articles en vente</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('seller.wallet') }}" class="group block px-5 py-4 transition-colors hover:bg-slate-50 sm:px-6 dark:hover:bg-slate-700/30">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600 transition-transform duration-200 group-hover:scale-110 dark:bg-amber-900/30 dark:text-amber-400">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-slate-900 dark:text-white">Mon portefeuille</h4>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Solde et transactions</p>
                                </div>
                            </div>
                        </a>
                        @endif

                        <a href="{{ route('messages.index') }}" class="group block px-5 py-4 transition-colors hover:bg-slate-50 sm:px-6 dark:hover:bg-slate-700/30">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-600 transition-transform duration-200 group-hover:scale-110 dark:bg-blue-900/30 dark:text-blue-400">
                                    <i class="fas fa-comments"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-slate-900 dark:text-white">Messages</h4>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Conversations</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="border-t border-slate-100 bg-slate-50/50 dark:border-slate-700/50 dark:bg-slate-800">
                    <a href="{{ route('affiliate.dashboard') }}" class="group block px-5 py-4 transition-colors hover:bg-slate-100 sm:px-6 dark:hover:bg-slate-700/30">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-primary-600 to-cyan-400 text-white shadow-lg shadow-primary-600/20 transition-transform duration-200 group-hover:scale-110">
                                    <i class="fas fa-users text-lg"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-semibold text-slate-900 dark:text-white">Programme d'affiliation</h4>
                                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">NOUVEAU</span>
                                    </div>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Parrainez vos amis et gagnez des récompenses</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-slate-300 transition-colors group-hover:text-slate-500 dark:text-slate-600 dark:group-hover:text-slate-300"></i>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Catalogue -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6 dark:border-slate-700/60">
                    <h3 class="flex items-center gap-3 text-base font-semibold text-slate-900 dark:text-white">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-100 text-primary-600 dark:bg-primary-900/40 dark:text-primary-400">
                            <i class="fas fa-store"></i>
                        </span>
                        Catalogue
                    </h3>
                </div>
                <div class="grid grid-cols-1 divide-y divide-slate-100 md:grid-cols-3 md:divide-x md:divide-y-0 dark:divide-slate-700/50">
                    <a href="{{ route('brands.index') }}" class="group block px-5 py-4 transition-colors hover:bg-slate-50 sm:px-6 dark:hover:bg-slate-700/30">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100 text-rose-600 transition-transform duration-200 group-hover:scale-110 dark:bg-rose-900/30 dark:text-rose-400">
                                <i class="fas fa-tags"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-slate-900 dark:text-white">Marques</h4>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Explorer les marques</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('categories.index') }}" class="group block px-5 py-4 transition-colors hover:bg-slate-50 sm:px-6 dark:hover:bg-slate-700/30">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-600 transition-transform duration-200 group-hover:scale-110 dark:bg-blue-900/30 dark:text-blue-400">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-slate-900 dark:text-white">Catégories</h4>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Par catégorie</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('items.index') }}" class="group block px-5 py-4 transition-colors hover:bg-slate-50 sm:px-6 dark:hover:bg-slate-700/30">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 transition-transform duration-200 group-hover:scale-110 dark:bg-emerald-900/30 dark:text-emerald-400">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-slate-900 dark:text-white">Tous les articles</h4>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Catalogue complet</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Aide & Support -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6 dark:border-slate-700/60">
                    <h3 class="flex items-center gap-3 text-base font-semibold text-slate-900 dark:text-white">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-100 text-primary-600 dark:bg-primary-900/40 dark:text-primary-400">
                            <i class="fas fa-question-circle"></i>
                        </span>
                        Aide & Support
                    </h3>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    <a href="{{ route('help.index') }}" class="group block px-5 py-4 transition-colors hover:bg-slate-50 sm:px-6 dark:hover:bg-slate-700/30">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-600 transition-transform duration-200 group-hover:scale-110 dark:bg-blue-900/30 dark:text-blue-400">
                                    <i class="fas fa-life-ring"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-slate-900 dark:text-white">Centre d'aide</h4>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">FAQ et guides</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-slate-300 transition-colors group-hover:text-slate-500 dark:text-slate-600 dark:group-hover:text-slate-300"></i>
                        </div>
                    </a>

                    <a href="{{ route('support.create') }}" class="group block px-5 py-4 transition-colors hover:bg-slate-50 sm:px-6 dark:hover:bg-slate-700/30">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-100 text-primary-600 transition-transform duration-200 group-hover:scale-110 dark:bg-primary-900/30 dark:text-primary-400">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-slate-900 dark:text-white">Nous contacter</h4>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Créer un ticket de support</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-slate-300 transition-colors group-hover:text-slate-500 dark:text-slate-600 dark:group-hover:text-slate-300"></i>
                        </div>
                    </a>

                    <a href="{{ route('terms') }}" class="group block px-5 py-4 transition-colors hover:bg-slate-50 sm:px-6 dark:hover:bg-slate-700/30">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600 transition-transform duration-200 group-hover:scale-110 dark:bg-amber-900/30 dark:text-amber-400">
                                    <i class="fas fa-file-contract"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-slate-900 dark:text-white">Conditions d'utilisation</h4>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">CGU et politique de confidentialité</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-slate-300 transition-colors group-hover:text-slate-500 dark:text-slate-600 dark:group-hover:text-slate-300"></i>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="py-8 text-center text-slate-400 dark:text-slate-500">
                <div class="mb-2 flex items-center justify-center gap-2">
                    <i class="fas fa-mobile-alt"></i>
                    <span class="font-medium">{{ config('app.name', 'VintApp') }} v1.0.0</span>
                </div>
                <p class="text-sm">&copy; {{ date('Y') }} Tous droits réservés</p>
            </div>
        </div>

        <!-- Déconnexion -->
        <div class="mx-auto mt-4 flex max-w-4xl justify-center px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:bg-red-700 hover:shadow">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Se déconnecter</span>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Thème -->
<div id="themeModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4 backdrop-blur-sm dark:bg-black/70">
    <div class="max-h-[90vh] w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-700/60">
            <h3 class="flex items-center gap-2 text-lg font-semibold text-slate-900 dark:text-white">
                <i class="fas fa-palette text-primary-600"></i>
                Choisir un thème
            </h3>
            <button onclick="closeThemeModal()" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600 dark:text-slate-500 dark:hover:bg-slate-700 dark:hover:text-slate-200">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="p-5 sm:p-6">
            <div class="space-y-3">
                <button onclick="selectTheme('light')" class="theme-option w-full rounded-xl border-2 border-slate-200 p-4 text-left transition-all hover:border-primary-300 dark:border-slate-600 dark:hover:border-primary-500" data-theme="light">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                                <i class="fas fa-sun"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-900 dark:text-white">Clair</h4>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Thème lumineux</p>
                            </div>
                        </div>
                        <i class="fas fa-check hidden text-emerald-500 dark:text-emerald-400 theme-check"></i>
                    </div>
                </button>

                <button onclick="selectTheme('dark')" class="theme-option w-full rounded-xl border-2 border-slate-200 p-4 text-left transition-all hover:border-primary-300 dark:border-slate-600 dark:hover:border-primary-500" data-theme="dark">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-800 text-white dark:bg-slate-600">
                                <i class="fas fa-moon"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-900 dark:text-white">Sombre</h4>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Thème foncé</p>
                            </div>
                        </div>
                        <i class="fas fa-check hidden text-emerald-500 dark:text-emerald-400 theme-check"></i>
                    </div>
                </button>

                <button onclick="selectTheme('auto')" class="theme-option w-full rounded-xl border-2 border-slate-200 p-4 text-left transition-all hover:border-primary-300 dark:border-slate-600 dark:hover:border-primary-500" data-theme="auto">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-100 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400">
                                <i class="fas fa-magic"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-900 dark:text-white">Automatique</h4>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Suit les préférences système</p>
                            </div>
                        </div>
                        <i class="fas fa-check hidden text-emerald-500 dark:text-emerald-400 theme-check"></i>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Personnalisation -->
<div id="personalizationModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4 backdrop-blur-sm dark:bg-black/70">
    <div class="max-h-[90vh] w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-700/60">
            <h3 class="flex items-center gap-2 text-lg font-semibold text-slate-900 dark:text-white">
                <i class="fas fa-cogs text-emerald-600"></i>
                Personnalisation
            </h3>
            <button onclick="closePersonalizationModal()" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600 dark:text-slate-500 dark:hover:bg-slate-700 dark:hover:text-slate-200">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="max-h-[calc(90vh-4rem)] space-y-6 overflow-y-auto p-5 sm:p-6">
            <div>
                <h4 class="mb-3 font-semibold text-slate-900 dark:text-white">Notifications</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-4 rounded-xl bg-slate-50 p-3 dark:bg-slate-900/50">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-bell text-blue-600"></i>
                            <span class="text-slate-900 dark:text-white">Notifications push</span>
                        </div>
                        <label class="relative inline-flex shrink-0 cursor-pointer items-center">
                            <input type="checkbox" class="peer sr-only" checked>
                            <div class="h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-slate-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary-600 peer-checked:after:translate-x-5 peer-checked:after:border-white dark:bg-slate-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between gap-4 rounded-xl bg-slate-50 p-3 dark:bg-slate-900/50">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-envelope text-emerald-600"></i>
                            <span class="text-slate-900 dark:text-white">Notifications email</span>
                        </div>
                        <label class="relative inline-flex shrink-0 cursor-pointer items-center">
                            <input type="checkbox" class="peer sr-only" checked>
                            <div class="h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-slate-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary-600 peer-checked:after:translate-x-5 peer-checked:after:border-white dark:bg-slate-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="mb-3 font-semibold text-slate-900 dark:text-white">Affichage</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-4 rounded-xl bg-slate-50 p-3 dark:bg-slate-900/50">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-eye text-primary-600"></i>
                            <span class="text-slate-900 dark:text-white">Mode compact</span>
                        </div>
                        <label class="relative inline-flex shrink-0 cursor-pointer items-center">
                            <input type="checkbox" class="peer sr-only">
                            <div class="h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-slate-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary-600 peer-checked:after:translate-x-5 peer-checked:after:border-white dark:bg-slate-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between gap-4 rounded-xl bg-slate-50 p-3 dark:bg-slate-900/50">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-images text-orange-600"></i>
                            <span class="text-slate-900 dark:text-white">Chargement automatique des images</span>
                        </div>
                        <label class="relative inline-flex shrink-0 cursor-pointer items-center">
                            <input type="checkbox" class="peer sr-only" checked>
                            <div class="h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-slate-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary-600 peer-checked:after:translate-x-5 peer-checked:after:border-white dark:bg-slate-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-100 pt-4 dark:border-slate-700/50">
                <button onclick="closePersonalizationModal()" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white">
                    Annuler
                </button>
                <button class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-cyan-500 px-6 py-2 text-sm font-medium text-white shadow-sm transition-all duration-200 hover:from-primary-500 hover:to-cyan-400 hover:shadow">
                    <i class="fas fa-check"></i>
                    Sauvegarder
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    updateThemeBadge();
});

function openThemeModal() {
    document.getElementById('themeModal').classList.remove('hidden');
    updateThemeSelection();
}

function closeThemeModal() {
    document.getElementById('themeModal').classList.add('hidden');
}

function selectTheme(theme) {
    const root = document.documentElement;

    if (theme === 'light') {
        root.classList.remove('dark');
        root.setAttribute('data-theme', 'day');
        localStorage.setItem('vintapp_day_night_manual', 'day');
    } else if (theme === 'dark') {
        root.classList.add('dark');
        root.setAttribute('data-theme', 'night');
        localStorage.setItem('vintapp_day_night_manual', 'night');
    } else {
        root.classList.remove('dark');
        localStorage.removeItem('vintapp_day_night_manual');
        root.removeAttribute('data-theme');
        if (window.VintAppDayNight && window.VintAppDayNight.resetAuto) {
            window.VintAppDayNight.resetAuto();
        }
    }

    localStorage.setItem('theme', theme);
    updateThemeBadge();

    if (window.isAuthenticated) {
        fetch('/profile/theme', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ theme_preference: theme })
        });
    }

    closeThemeModal();
}

function updateThemeSelection() {
    const currentTheme = getPreferredTheme();
    document.querySelectorAll('.theme-option').forEach(option => {
        const check = option.querySelector('.theme-check');
        if (option.getAttribute('data-theme') === currentTheme) {
            option.classList.add('border-primary-500', 'bg-primary-50');
            check.classList.remove('hidden');
        } else {
            option.classList.remove('border-primary-500', 'bg-primary-50');
            check.classList.add('hidden');
        }
    });
}

function updateThemeBadge() {
    const currentTheme = getPreferredTheme();
    const badge = document.getElementById('current-theme-badge');
    const labels = { light: 'Clair', dark: 'Sombre', auto: 'Auto' };
    if (badge) badge.textContent = labels[currentTheme] || 'Auto';
}

function getPreferredTheme() {
    return localStorage.getItem('theme') || window.userTheme || 'auto';
}

function openPersonalizationModal() {
    document.getElementById('personalizationModal').classList.remove('hidden');
}

function closePersonalizationModal() {
    document.getElementById('personalizationModal').classList.add('hidden');
}

document.addEventListener('click', function(event) {
    ['themeModal', 'personalizationModal'].forEach(id => {
        const modal = document.getElementById(id);
        if (event.target === modal) modal.classList.add('hidden');
    });
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        ['themeModal', 'personalizationModal'].forEach(id => {
            document.getElementById(id).classList.add('hidden');
        });
    }
});
</script>
@endpush
