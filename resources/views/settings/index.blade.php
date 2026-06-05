@extends('app')

@section('title', 'Paramètres - VintApp')

@section('meta_description', 'Gérez vos préférences, votre profil et les paramètres de votre compte VintApp.')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tete -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <button onclick="history.back()" class="mr-4 p-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors">
                    <i class="fas fa-arrow-left text-lg"></i>
                </button>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Parametres</h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">Gerer vos preferences et votre compte</p>
                </div>
            </div>
        </div>

        <!-- Profil utilisateur -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-6 mb-8">
            <div class="flex items-center space-x-4">
                @if(Auth::user()->avatar)
                    @php
                        $avatarUrl = filter_var(Auth::user()->avatar, FILTER_VALIDATE_URL)
                            ? Auth::user()->avatar
                            : asset('storage/' . Auth::user()->avatar);
                    @endphp
                    <img src="{{ $avatarUrl }}"
                         alt="{{ Auth::user()->name }}"
                         class="w-16 h-16 rounded-full object-cover border-4 border-primary-200"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-r from-primary-600 to-cyan-400 items-center justify-center text-white font-bold text-xl hidden">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                @else
                    <div class="w-16 h-16 rounded-full bg-gradient-to-r from-primary-600 to-cyan-400 flex items-center justify-center text-white font-bold text-xl">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ Auth::user()->name }}</h2>
                    <p class="text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                    <div class="flex items-center mt-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                        <span class="text-sm text-green-600 dark:text-green-400 font-medium">Compte actif</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grille des sections -->
        <div class="space-y-6">
            <!-- Section : Mon compte -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700/50">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-user-circle mr-3 text-primary-600"></i>
                        Mon compte
                    </h3>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    <a href="{{ route('profile.edit') }}" class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-user-cog text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Modifier mon profil</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Informations personnelles, photo de profil</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300 group-hover:text-gray-500 dark:text-gray-600 dark:group-hover:text-gray-400 transition-colors"></i>
                        </div>
                    </a>

                    <button onclick="openPersonalizationModal()" class="w-full text-left px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-cogs text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Personnalisation</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Preferences d'affichage et notifications</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300 group-hover:text-gray-500 dark:text-gray-600 dark:group-hover:text-gray-400 transition-colors"></i>
                        </div>
                    </button>

                    <button onclick="openThemeModal()" class="w-full text-left px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-palette text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Theme d'affichage</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Clair, Sombre ou Automatique</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span id="current-theme-badge" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm rounded-full font-medium">Auto</span>
                                <i class="fas fa-chevron-right text-gray-300 group-hover:text-gray-500 dark:text-gray-600 dark:group-hover:text-gray-400 transition-colors"></i>
                            </div>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Section : Navigation rapide -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700/50">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-compass mr-3 text-primary-600"></i>
                        Navigation rapide
                    </h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-gray-700/50">
                    <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        <a href="{{ route('dashboard') }}" class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-tachometer-alt"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">Dashboard</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Vue d'ensemble</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('orders.index') }}" class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">Mes commandes</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Historique d'achats</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('orders.my-sales') }}" class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">Mes ventes</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Articles vendus</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        <a href="{{ route('items.my-items') }}" class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">Mes articles</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Articles en vente</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('wallet.index') }}" class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">Mon portefeuille</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Solde et transactions</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('messages.index') }}" class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-comments"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">Messages</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Conversations</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Affiliation -->
                <div class="border-t border-gray-100 dark:border-gray-700/50 bg-gradient-to-r from-primary-50 to-cyan-50 dark:from-primary-900/20 dark:to-cyan-900/20">
                    <a href="{{ route('affiliate.dashboard') }}" class="block px-6 py-4 hover:from-primary-100 hover:to-cyan-100 dark:hover:from-primary-900/30 dark:hover:to-cyan-900/30 transition-all group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-primary-600 to-cyan-400 text-white rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shadow-lg">
                                    <i class="fas fa-users text-lg"></i>
                                </div>
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">Programme d'affiliation</h4>
                                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-medium rounded-full">NOUVEAU</span>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Parrainez vos amis et gagnez des recompenses</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300 group-hover:text-gray-500 dark:text-gray-600 dark:group-hover:text-gray-400 transition-colors"></i>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Section : Catalogue -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700/50">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-store mr-3 text-primary-600"></i>
                        Catalogue
                    </h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-gray-700/50">
                    <a href="{{ route('brands.index') }}" class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="fas fa-tags"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">Marques</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Explorer les marques</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('categories.index') }}" class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">Categories</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Par categorie</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('items.index') }}" class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">Tous les articles</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Catalogue complet</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Section : Aide & Support -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700/50">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-question-circle mr-3 text-primary-600"></i>
                        Aide & Support
                    </h3>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    <a href="{{ route('help.index') }}" class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-life-ring"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">Centre d'aide</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">FAQ et guides</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300 group-hover:text-gray-500 dark:text-gray-600 dark:group-hover:text-gray-400 transition-colors"></i>
                        </div>
                    </a>

                    <a href="{{ route('support.create') }}" class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">Nous contacter</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Creer un ticket de support</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300 group-hover:text-gray-500 dark:text-gray-600 dark:group-hover:text-gray-400 transition-colors"></i>
                        </div>
                    </a>

                    <a href="{{ route('terms') }}" class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-file-contract"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">Conditions d'utilisation</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">CGU et politique de confidentialite</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300 group-hover:text-gray-500 dark:text-gray-600 dark:group-hover:text-gray-400 transition-colors"></i>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Section : Actions du compte -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Deconnexion -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-orange-200 dark:border-orange-900/30 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 border-b border-orange-200 dark:border-orange-800/30">
                        <h3 class="text-lg font-semibold text-orange-800 dark:text-orange-300 flex items-center">
                            <i class="fas fa-sign-out-alt mr-3 text-orange-600 dark:text-orange-400"></i>
                            Deconnexion
                        </h3>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold py-3 px-4 rounded-xl shadow-lg shadow-orange-500/25 transition-all duration-200 hover:-translate-y-0.5 flex items-center justify-center space-x-2">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Se deconnecter</span>
                            </button>
                        </form>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-3 text-center">Deconnexion securisee de votre compte</p>
                    </div>
                </div>

                <!-- Zone dangereuse -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-red-200 dark:border-red-900/30 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border-b border-red-200 dark:border-red-800/30">
                        <h3 class="text-lg font-semibold text-red-800 dark:text-red-300 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-3 text-red-600 dark:text-red-400"></i>
                            Zone dangereuse
                        </h3>
                    </div>
                    <div class="p-6">
                        <button onclick="openDeleteAccountModal()" class="w-full bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold py-3 px-4 rounded-xl shadow-lg shadow-red-500/25 transition-all duration-200 hover:-translate-y-0.5 flex items-center justify-center space-x-2">
                            <i class="fas fa-user-slash"></i>
                            <span>Supprimer mon compte</span>
                        </button>
                        <p class="text-sm text-red-500 dark:text-red-400 mt-3 text-center font-medium">Action irreversible</p>
                    </div>
                </div>
            </div>

            <!-- Version et copyright -->
            <div class="text-center py-8 text-gray-400 dark:text-gray-500">
                <div class="flex items-center justify-center space-x-2 mb-2">
                    <i class="fas fa-mobile-alt"></i>
                    <span class="font-medium">{{ config('app.name', 'VintApp') }} v1.0.0</span>
                </div>
                <p class="text-sm">&copy; {{ date('Y') }} Tous droits reserves</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal de selection du theme -->
<div id="themeModal" class="fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700/50">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-palette mr-2 text-primary-600"></i>
                    Choisir un theme
                </h3>
                <button onclick="closeThemeModal()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <button onclick="selectTheme('light')" class="theme-option w-full p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl hover:border-primary-300 dark:hover:border-primary-500 transition-all text-left" data-theme="light">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 rounded-xl flex items-center justify-center">
                                <i class="fas fa-sun"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white">Clair</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Theme lumineux</p>
                            </div>
                        </div>
                        <i class="fas fa-check text-green-500 dark:text-green-400 hidden theme-check"></i>
                    </div>
                </button>

                <button onclick="selectTheme('dark')" class="theme-option w-full p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl hover:border-primary-300 dark:hover:border-primary-500 transition-all text-left" data-theme="dark">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gray-800 dark:bg-gray-600 text-white rounded-xl flex items-center justify-center">
                                <i class="fas fa-moon"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white">Sombre</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Theme fonce</p>
                            </div>
                        </div>
                        <i class="fas fa-check text-green-500 dark:text-green-400 hidden theme-check"></i>
                    </div>
                </button>

                <button onclick="selectTheme('auto')" class="theme-option w-full p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl hover:border-primary-300 dark:hover:border-primary-500 transition-all text-left" data-theme="auto">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-xl flex items-center justify-center">
                                <i class="fas fa-magic"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white">Automatique</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Suit les preferences systeme</p>
                            </div>
                        </div>
                        <i class="fas fa-check text-green-500 dark:text-green-400 hidden theme-check"></i>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de personnalisation -->
<div id="personalizationModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700/50">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-cogs mr-2 text-green-600"></i>
                    Personnalisation
                </h3>
                <button onclick="closePersonalizationModal()" class="text-gray-400 hover:text-gray-600 dark:text-gray-300 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6 space-y-6">
            <!-- Notifications -->
            <div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Notifications</h4>
                <div class="space-y-3">
                    <label class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl cursor-pointer">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-bell text-blue-600"></i>
                            <span class="text-gray-900 dark:text-white">Notifications push</span>
                        </div>
                        <input type="checkbox" class="toggle-switch" checked>
                    </label>
                    <label class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl cursor-pointer">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-envelope text-green-600"></i>
                            <span class="text-gray-900 dark:text-white">Notifications email</span>
                        </div>
                        <input type="checkbox" class="toggle-switch" checked>
                    </label>
                </div>
            </div>

            <!-- Preferences d'affichage -->
            <div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Affichage</h4>
                <div class="space-y-3">
                    <label class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl cursor-pointer">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-eye text-primary-600"></i>
                            <span class="text-gray-900 dark:text-white">Mode compact</span>
                        </div>
                        <input type="checkbox" class="toggle-switch">
                    </label>
                    <label class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl cursor-pointer">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-images text-orange-600"></i>
                            <span class="text-gray-900 dark:text-white">Chargement automatique des images</span>
                        </div>
                        <input type="checkbox" class="toggle-switch" checked>
                    </label>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700/50">
                <button onclick="closePersonalizationModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 transition-colors">
                    Annuler
                </button>
                <button class="px-6 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-xl shadow-lg shadow-green-500/25 transition-all duration-200">
                    Sauvegarder
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression du compte -->
<div id="deleteAccountModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border-b border-red-200 dark:border-red-800/30">
            <h3 class="text-xl font-semibold text-red-800 dark:text-red-300 flex items-center">
                <i class="fas fa-exclamation-triangle mr-2 text-red-600 dark:text-red-400"></i>
                Supprimer definitivement mon compte
            </h3>
        </div>
        <div class="p-6">
            <div class="bg-gradient-to-r from-red-100 to-red-50 dark:from-red-900/30 dark:to-red-800/20 border border-red-200 dark:border-red-800/50 text-red-800 dark:text-red-300 px-4 py-3 rounded-xl mb-6">
                <div class="flex items-center">
                    <i class="fas fa-skull-crossbones mr-2"></i>
                    <strong>ATTENTION !</strong>&nbsp;Cette action est <strong>IRREVERSIBLE</strong>
                </div>
            </div>

            <div class="mb-6">
                <p class="text-gray-700 dark:text-gray-200 mb-3">En supprimant votre compte, vous perdrez :</p>
                <ul class="space-y-2.5">
                    <li class="flex items-center text-red-600 dark:text-red-400">
                        <i class="fas fa-times-circle mr-2"></i>
                        <strong>Tous vos articles</strong>&nbsp;en vente
                    </li>
                    <li class="flex items-center text-red-600 dark:text-red-400">
                        <i class="fas fa-times-circle mr-2"></i>
                        <strong>Votre historique</strong>&nbsp;de commandes
                    </li>
                    <li class="flex items-center text-red-600 dark:text-red-400">
                        <i class="fas fa-times-circle mr-2"></i>
                        <strong>Vos messages</strong>&nbsp;et conversations
                    </li>
                    <li class="flex items-center text-red-600 dark:text-red-400">
                        <i class="fas fa-times-circle mr-2"></i>
                        <strong>Votre portefeuille</strong>&nbsp;et son solde
                    </li>
                    <li class="flex items-center text-red-600 dark:text-red-400">
                        <i class="fas fa-times-circle mr-2"></i>
                        <strong>Toutes vos donnees</strong>&nbsp;personnelles
                    </li>
                </ul>
            </div>

            <div class="bg-gradient-to-r from-yellow-100 to-yellow-50 dark:from-yellow-900/30 dark:to-yellow-800/20 border border-yellow-200 dark:border-yellow-800/50 text-yellow-800 dark:text-yellow-300 px-4 py-3 rounded-xl mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle mr-2 mt-0.5"></i>
                    <div>
                        Si vous avez des <strong>commandes en cours</strong> ou un <strong>solde dans votre portefeuille</strong>, veuillez les finaliser avant de supprimer votre compte.
                    </div>
                </div>
            </div>

            <form id="deleteAccountForm" method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')

                <div class="space-y-4">
                    <div>
                        <label for="delete_password" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Pour confirmer, entrez votre mot de passe :
                        </label>
                        <input type="password"
                               id="delete_password"
                               name="password"
                               class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all"
                               placeholder="Votre mot de passe"
                               required
                               autocomplete="current-password">
                    </div>

                    <label class="flex items-start space-x-3 cursor-pointer">
                        <input type="checkbox" id="confirmDelete" class="mt-1" required>
                        <span class="text-sm text-gray-700 dark:text-gray-200">
                            Je comprends que cette action est definitive et irreversible
                        </span>
                    </label>
                </div>
            </form>

            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-100 dark:border-gray-700/50">
                <button onclick="closeDeleteAccountModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 transition-colors">
                    Annuler
                </button>
                <button onclick="confirmDeleteAccount()" class="px-6 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl shadow-lg shadow-red-500/25 transition-all duration-200 flex items-center space-x-2">
                    <i class="fas fa-trash-alt"></i>
                    <span>Supprimer definitivement</span>
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
    applyTheme(theme);
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

function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
}

function openPersonalizationModal() {
    document.getElementById('personalizationModal').classList.remove('hidden');
}

function closePersonalizationModal() {
    document.getElementById('personalizationModal').classList.add('hidden');
}

function openDeleteAccountModal() {
    document.getElementById('deleteAccountModal').classList.remove('hidden');
}

function closeDeleteAccountModal() {
    document.getElementById('deleteAccountModal').classList.add('hidden');
    document.getElementById('deleteAccountForm').reset();
}

function confirmDeleteAccount() {
    const password = document.getElementById('delete_password').value;
    const confirmCheckbox = document.getElementById('confirmDelete');

    if (!password) {
        alert('Veuillez entrer votre mot de passe pour confirmer.');
        document.getElementById('delete_password').focus();
        return;
    }

    if (!confirmCheckbox.checked) {
        alert('Veuillez cocher la case de confirmation.');
        confirmCheckbox.focus();
        return;
    }

    const finalConfirm = confirm(
        'DERNIERE CONFIRMATION\n\n' +
        'Etes-vous ABSOLUMENT SUR de vouloir supprimer votre compte ?\n\n' +
        '- Cette action est IRREVERSIBLE\n' +
        '- Toutes vos donnees seront DEFINITIVEMENT supprimees\n' +
        '- Vous ne pourrez PAS recuperer votre compte\n\n' +
        'Cliquez sur OK pour confirmer la suppression definitive.'
    );

    if (finalConfirm) {
        document.getElementById('deleteAccountForm').submit();
    }
}

// Fermer les modaux en cliquant a l'exterieur
document.addEventListener('click', function(event) {
    ['themeModal', 'personalizationModal', 'deleteAccountModal'].forEach(id => {
        const modal = document.getElementById(id);
        if (event.target === modal) modal.classList.add('hidden');
    });
});

// Touche Echap pour fermer les modaux
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        ['themeModal', 'personalizationModal', 'deleteAccountModal'].forEach(id => {
            document.getElementById(id).classList.add('hidden');
        });
    }
});
</script>
@endpush

@push('styles')
<style>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.max-w-4xl {
    animation: fadeInUp 0.6s ease-out;
}

.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

.toggle-switch {
    appearance: none;
    width: 3rem;
    height: 1.5rem;
    border-radius: 9999px;
    background-color: #d1d5db;
    position: relative;
    cursor: pointer;
    transition: background-color 0.2s;
}

.toggle-switch:checked {
    background-color: #9333ea;
}

.toggle-switch::after {
    content: '';
    position: absolute;
    top: 2px;
    left: 2px;
    width: 1.25rem;
    height: 1.25rem;
    border-radius: 50%;
    background-color: white;
    transition: transform 0.2s;
}

.toggle-switch:checked::after {
    transform: translateX(1.5rem);
}

@media (max-width: 768px) {
    .grid-cols-1.md\:grid-cols-2 > *,
    .grid-cols-1.md\:grid-cols-3 > * {
        border-right: none !important;
    }
}

::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #94a3b8;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}
</style>
@endpush
