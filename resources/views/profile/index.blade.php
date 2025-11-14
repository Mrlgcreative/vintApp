@extends('app')

@section('title', 'Mon Profil - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- En-tête du profil -->
    <div class="mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
            <div class="p-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                    <div class="flex-shrink-0">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" 
                                 alt="{{ $user->name }}" 
                                 class="w-20 h-20 rounded-full object-cover ring-4 ring-primary-100">
                        @else
                            <div class="w-20 h-20 rounded-full bg-gradient-to-r from-primary-600 to-cyan-400 flex items-center justify-center text-white text-2xl font-bold ring-4 ring-primary-100">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ $user->name }}</h1>
                        <div class="space-y-2 text-gray-600">
                            <p class="flex items-center gap-2">
                                <i class="fas fa-envelope text-primary-500"></i>
                                {{ $user->email }}
                            </p>
                            <p class="flex items-center gap-2">
                                <i class="fas fa-calendar-alt text-primary-500"></i>
                                Membre depuis {{ $user->created_at->format('F Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ route('profile.edit') }}" 
                           class="inline-flex items-center gap-2 px-6 py-3 bg-white border-2 border-primary-600 text-primary-600 rounded-lg hover:bg-primary-50 transition-colors duration-200 font-medium">
                            <i class="fas fa-edit"></i>
                            Modifier le profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center hover:shadow-md hover:-translate-y-1 transition-all duration-200">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-primary-100 rounded-lg mb-4">
                <i class="fas fa-box text-primary-600 text-xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['total_items'] }}</h3>
            <p class="text-gray-600">Articles publiés</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center hover:shadow-md hover:-translate-y-1 transition-all duration-200">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg mb-4">
                <i class="fas fa-shopping-cart text-green-600 text-xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['total_orders'] }}</h3>
            <p class="text-gray-600">Commandes</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center hover:shadow-md hover:-translate-y-1 transition-all duration-200">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg mb-4">
                <i class="fas fa-euro-sign text-yellow-600 text-xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ number_format($stats['total_revenue'], 2) }}€</h3>
            <p class="text-gray-600">Revenus totaux</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center hover:shadow-md hover:-translate-y-1 transition-all duration-200">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg mb-4">
                <i class="fas fa-star text-blue-600 text-xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ number_format($stats['average_rating'], 1) }}/5</h3>
            <p class="text-gray-600">Note moyenne</p>
        </div>
    </div>

    <!-- Navigation des sections -->
    <div class="mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                    <i class="fas fa-tachometer-alt text-primary-600"></i>
                    Tableau de bord
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Articles -->
                    <div class="group bg-gradient-to-br from-primary-50 to-primary-100 border-2 border-primary-200 rounded-xl p-6 text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-200 hover:border-primary-300">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-600 text-white rounded-xl mb-4 group-hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-box text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Mes Articles</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            {{ $stats['active_items'] }} actifs · {{ $stats['sold_items'] }} vendus
                        </p>
                        <a href="{{ route('items.my-items') }}" 
                           class="inline-flex items-center justify-center w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors duration-200 font-medium">
                            Gérer mes articles
                        </a>
                    </div>

                    <!-- Commandes -->
                    <div class="group bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-200 rounded-xl p-6 text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-200 hover:border-green-300">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-600 text-white rounded-xl mb-4 group-hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-shopping-cart text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Mes Commandes</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            {{ $stats['total_orders'] }} commandes passées
                        </p>
                        <a href="{{ route('orders.index') }}" 
                           class="inline-flex items-center justify-center w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 font-medium">
                            Voir mes commandes
                        </a>
                    </div>

                    <!-- Messages -->
                    <div class="group bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 rounded-xl p-6 text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-200 hover:border-blue-300">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 text-white rounded-xl mb-4 group-hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-comments text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Messages</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            {{ $stats['unread_messages'] }} non lus
                        </p>
                        <a href="{{ route('messages.index') }}" 
                           class="inline-flex items-center justify-center w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 font-medium">
                            Voir mes messages
                        </a>
                    </div>

                    <!-- Wallet -->
                    <div class="group bg-gradient-to-br from-yellow-50 to-yellow-100 border-2 border-yellow-200 rounded-xl p-6 text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-200 hover:border-yellow-300">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-600 text-white rounded-xl mb-4 group-hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-wallet text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Wallet</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Gérez vos paiements
                        </p>
                        <a href="{{ route('wallet.index') }}" 
                           class="inline-flex items-center justify-center w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors duration-200 font-medium">
                            Accéder au wallet
                        </a>
                    </div>

                    <!-- Paramètres -->
                    <div class="group bg-gradient-to-br from-gray-50 to-gray-100 border-2 border-gray-200 rounded-xl p-6 text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-200 hover:border-gray-300">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-600 text-white rounded-xl mb-4 group-hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-cog text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Paramètres</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Sécurité et préférences
                        </p>
                        <a href="{{ route('profile.edit') }}" 
                           class="inline-flex items-center justify-center w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200 font-medium">
                            Configurer
                        </a>
                    </div>

                    <!-- Statistiques -->
                    <div class="group bg-gradient-to-br from-indigo-50 to-indigo-100 border-2 border-indigo-200 rounded-xl p-6 text-center hover:shadow-lg hover:-translate-y-1 transition-all duration-200 hover:border-indigo-300">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-600 text-white rounded-xl mb-4 group-hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-chart-bar text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Statistiques</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Analyse détaillée
                        </p>
                        <a href="{{ route('profile.stats') }}" 
                           class="inline-flex items-center justify-center w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200 font-medium">
                            Voir les stats
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activité récente -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                <i class="fas fa-clock text-primary-600"></i>
                Activité récente
            </h2>
        </div>
        <div class="p-6 space-y-4">
            @if($stats['unread_messages'] > 0)
                <div class="flex items-start gap-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-envelope text-white"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="text-gray-800">
                            Vous avez <strong class="text-blue-600">{{ $stats['unread_messages'] }}</strong> message(s) non lu(s).
                        </p>
                        <a href="{{ route('messages.index') }}" 
                           class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 font-medium mt-1 hover:underline">
                            Les consulter maintenant
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            @endif

            @if($stats['active_items'] > 0)
                <div class="flex items-start gap-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-white"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="text-gray-800">
                            Vous avez <strong class="text-green-600">{{ $stats['active_items'] }}</strong> article(s) en ligne.
                        </p>
                        <a href="{{ route('items.my-items') }}" 
                           class="inline-flex items-center gap-1 text-green-600 hover:text-green-700 font-medium mt-1 hover:underline">
                            Les gérer
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            @endif

            @if($stats['total_items'] === 0)
                <div class="flex items-start gap-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-yellow-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-plus-circle text-white"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="text-gray-800">
                            Vous n'avez encore publié aucun article.
                        </p>
                        <a href="{{ route('items.create') }}" 
                           class="inline-flex items-center gap-1 text-yellow-600 hover:text-yellow-700 font-medium mt-1 hover:underline">
                            Publier votre premier article
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection