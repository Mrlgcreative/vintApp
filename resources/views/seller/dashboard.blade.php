@extends('app')

@section('title', 'Espace Vendeur')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="flex">
        @include('seller.partials.sidebar')

        <!-- Main content -->
        <main class="flex-1 p-6 lg:p-8 pb-20 lg:pb-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header / Hero -->
                <div class="mb-8">
                    <div class="bg-gradient-to-r from-vinted-primary-600 via-vinted-primary-500 to-vinted-primary-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
                        <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4"></div>
                        <div class="relative flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-bold flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white/15 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                        <i class="fas fa-chart-pie text-base"></i>
                                    </div>
                                    Tableau de bord
                                </h1>
                                <p class="text-white/80 mt-2 text-sm sm:text-base">Bienvenue dans votre espace vendeur</p>
                            </div>
                            <a href="{{ route('items.create') }}" class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm rounded-xl px-4 py-2.5 text-sm font-medium hover:bg-white/25 active:scale-95 transition-all duration-200">
                                <i class="fas fa-plus"></i> Publier un article
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 hover:shadow-lg transition-all">
                        <div class="flex items-center gap-4">
                            <x-icon :icon="'fas fa-box'" tone="primary" size="md" />
                            <div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_items'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Articles <span class="hidden sm:inline">· {{ $stats['active_items'] }} actifs</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 hover:shadow-lg transition-all">
                        <div class="flex items-center gap-4">
                            <x-icon :icon="'fas fa-shopping-cart'" tone="emerald" size="md" />
                            <div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_sales'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Ventes totales</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 hover:shadow-lg transition-all">
                        <div class="flex items-center gap-4">
                            <x-icon :icon="'fas fa-dollar-sign'" tone="amber" size="md" />
                            <div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_revenue'], 2) }} $</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Revenu total</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 hover:shadow-lg transition-all">
                        <div class="flex items-center gap-4">
                            <x-icon :icon="'fas fa-star'" tone="indigo" size="md" />
                            <div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['average_rating'], 1) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $stats['total_reviews'] }} avis</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Articles récents -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2.5">
                                <x-icon :icon="'fas fa-box'" tone="primary" size="sm" />
                                Mes articles
                            </h3>
                            <a href="{{ route('seller.items') }}" class="text-sm font-medium text-vinted-primary-600 dark:text-vinted-primary-400 hover:text-vinted-primary-700 dark:hover:text-vinted-primary-300 transition-colors">Voir tout</a>
                        </div>
                        <div class="p-4">
                            @if($items->count() > 0)
                                <div class="space-y-2">
                                    @foreach($items as $item)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl hover:bg-vinted-primary-50 dark:hover:bg-vinted-primary-500/10 transition-colors">
                                            <div class="min-w-0 flex-1">
                                                <h6 class="font-semibold text-gray-900 dark:text-white text-sm truncate">{{ $item->name }}</h6>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->category->name ?? 'N/A' }}</p>
                                            </div>
                                            <span class="ml-3 px-2.5 py-1 bg-vinted-primary-50 dark:bg-vinted-primary-500/15 text-vinted-primary-700 dark:text-vinted-primary-300 text-xs font-semibold rounded-lg flex-shrink-0">
                                                {{ $item->formatted_price }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-10">
                                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-box text-gray-300 dark:text-gray-500"></i>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Aucun article pour le moment</p>
                                    <a href="{{ route('items.create') }}" class="mt-3 inline-flex items-center text-sm text-primary font-medium">Publier un article</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Ventes récentes -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2.5">
                                <x-icon :icon="'fas fa-shopping-cart'" tone="emerald" size="sm" />
                                Dernières ventes
                            </h3>
                            <a href="{{ route('seller.sales') }}" class="text-sm font-medium text-vinted-primary-600 dark:text-vinted-primary-400 hover:text-vinted-primary-700 dark:hover:text-vinted-primary-300 transition-colors">Voir tout</a>
                        </div>
                        <div class="p-4">
                            @if($sales->count() > 0)
                                <div class="space-y-2">
                                    @foreach($sales as $sale)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/10 transition-colors">
                                            <div class="min-w-0 flex-1">
                                                <h6 class="font-semibold text-gray-900 dark:text-white text-sm">Commande #{{ $sale->id }}</h6>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $sale->item->name ?? 'N/A' }}</p>
                                            </div>
                                            <span class="ml-3 px-2.5 py-1 text-xs font-semibold rounded-lg flex-shrink-0
                                                @if($sale->status === 'completed') bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300
                                                @elseif($sale->status === 'cancelled' || $sale->status === 'refunded') bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300
                                                @elseif($sale->status === 'delivered') bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300
                                                @else bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 @endif">
                                                {{ $sale->status_text }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-10">
                                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-shopping-cart text-gray-300 dark:text-gray-500"></i>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Aucune vente pour le moment</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Derniers avis -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2.5">
                            <x-icon :icon="'fas fa-star'" tone="amber" size="sm" />
                            Derniers avis
                        </h3>
                        <a href="{{ route('seller.reviews') }}" class="text-sm font-medium text-vinted-primary-600 dark:text-vinted-primary-400 hover:text-vinted-primary-700 dark:hover:text-vinted-primary-300 transition-colors">Voir tout</a>
                    </div>
                    <div class="p-4">
                        @if($reviews->count() > 0)
                            <div class="space-y-3">
                                @foreach($reviews as $review)
                                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                                <div class="w-9 h-9 bg-gradient-to-br from-primary to-primary-600 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                                    {{ strtoupper(substr($review->reviewer->name ?? '?', 0, 1)) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <h6 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $review->reviewer->name ?? 'Anonyme' }}</h6>
                                                    <div class="flex items-center gap-1">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }} text-xs"></i>
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                        @if($review->comment)
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">{{ $review->comment }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-10">
                                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-star text-gray-300 dark:text-gray-500"></i>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Aucun avis pour le moment</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection