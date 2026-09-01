@extends('app')

@section('title', 'Espace Vendeur')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="flex">
        @include('seller.partials.sidebar')

        <!-- Main content -->
        <main class="flex-1 p-6 lg:p-8 pb-20 lg:pb-8">
            <div class="max-w-7xl mx-auto space-y-6">
                <!-- Header / Hero -->
                <div class="flex items-center justify-between gap-4 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-4 sm:p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-vinted-primary-100 dark:bg-vinted-primary-500/20 rounded-lg flex items-center justify-center text-vinted-primary-600 dark:text-vinted-primary-400">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900 dark:text-white">Tableau de bord</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Bienvenue dans votre espace vendeur</p>
                        </div>
                    </div>
                    <a href="{{ route('items.create') }}" class="inline-flex items-center gap-2 shrink-0 h-10 px-4 bg-vinted-primary-600 hover:bg-vinted-primary-700 text-white text-sm font-medium rounded-md transition-colors">
                        <i class="fas fa-plus text-xs"></i> Publier un article
                    </a>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-5">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-vinted-primary-100 dark:bg-vinted-primary-500/20 flex items-center justify-center text-vinted-primary-600 dark:text-vinted-primary-400">
                                <i class="fas fa-box text-sm"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums">{{ $stats['total_items'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Articles <span class="hidden sm:inline">· {{ $stats['active_items'] }} actifs</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-5">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                <i class="fas fa-shopping-cart text-sm"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums">{{ $stats['total_sales'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Ventes totales</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-5">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                <i class="fas fa-wallet text-sm"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums">${{ number_format($stats['total_revenue'] ?? 0, 2, '.', ',') }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Revenus totaux</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-5">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-500/20 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                <i class="fas fa-star text-sm"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums">{{ number_format($stats['average_rating'], 1) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $stats['total_reviews'] }} avis</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenus par devise -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-5 relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-emerald-400 to-emerald-500"></div>
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-lg flex items-center justify-center flex-shrink-0 bg-emerald-50 dark:bg-emerald-500/20">
                                    <i class="fas fa-dollar-sign text-emerald-600 dark:text-emerald-400 text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">${{ number_format($usdWallet?->balance ?? 0, 2, '.', ',') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Revenus en dollars (USD)</p>
                                </div>
                            </div>
                            <a href="{{ route('seller.wallet') }}" class="text-xs font-medium text-vinted-primary-600 dark:text-vinted-primary-400 hover:text-vinted-primary-700 dark:hover:text-vinted-primary-300 transition-colors shrink-0">Wallet →</a>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-5 relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-amber-400 to-yellow-500"></div>
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-lg flex items-center justify-center flex-shrink-0 bg-amber-50 dark:bg-amber-500/20">
                                    <i class="fas fa-coins text-amber-600 dark:text-amber-400 text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-3xl font-bold text-amber-600 dark:text-amber-400 tabular-nums">{{ number_format($cdfWallet?->balance ?? 0, 2, ',', ' ') }} <span class="text-xl font-semibold">FC</span></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Revenus en Francs Congolais (CDF)</p>
                                </div>
                            </div>
                            <a href="{{ route('seller.wallet') }}" class="text-xs font-medium text-vinted-primary-600 dark:text-vinted-primary-400 hover:text-vinted-primary-700 dark:hover:text-vinted-primary-300 transition-colors shrink-0">Wallet →</a>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Articles récents -->
                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                            <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="fas fa-box text-sm text-vinted-primary-600 dark:text-vinted-primary-400"></i>
                                Mes articles
                            </h3>
                            <a href="{{ route('seller.items') }}" class="text-sm font-medium text-vinted-primary-600 dark:text-vinted-primary-400 hover:text-vinted-primary-700 dark:hover:text-vinted-primary-300 transition-colors">Voir tout</a>
                        </div>
                        <div class="p-4">
                            @if($items->count() > 0)
                                <div class="space-y-2">
                                    @foreach($items as $item)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg hover:bg-vinted-primary-50 dark:hover:bg-vinted-primary-500/10 transition-colors">
                                            <div class="min-w-0 flex-1">
                                                <h6 class="font-semibold text-gray-900 dark:text-white text-sm truncate">{{ $item->name }}</h6>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->category->name ?? 'N/A' }}</p>
                                            </div>
                                            <span class="ml-3 px-2.5 py-1 bg-vinted-primary-50 dark:bg-vinted-primary-500/15 text-vinted-primary-700 dark:text-vinted-primary-300 text-xs font-semibold rounded-md flex-shrink-0">
                                                {{ $item->formatted_price }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-10">
                                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-box text-gray-300 dark:text-gray-600"></i>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Aucun article pour le moment</p>
                                    <a href="{{ route('items.create') }}" class="mt-3 inline-flex items-center text-sm text-vinted-primary-600 dark:text-vinted-primary-400 font-medium">Publier un article</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Ventes récentes -->
                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                            <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="fas fa-shopping-cart text-sm text-emerald-600 dark:text-emerald-400"></i>
                                Dernières ventes
                            </h3>
                            <a href="{{ route('seller.sales') }}" class="text-sm font-medium text-vinted-primary-600 dark:text-vinted-primary-400 hover:text-vinted-primary-700 dark:hover:text-vinted-primary-300 transition-colors">Voir tout</a>
                        </div>
                        <div class="p-4">
                            @if($sales->count() > 0)
                                <div class="space-y-2">
                                    @foreach($sales as $sale)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg hover:bg-emerald-50 dark:hover:bg-emerald-500/10 transition-colors">
                                            <div class="min-w-0 flex-1">
                                                <h6 class="font-semibold text-gray-900 dark:text-white text-sm">Commande #{{ $sale->id }}</h6>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $sale->item->name ?? 'N/A' }}</p>
                                            </div>
                                            <span class="ml-3 px-2.5 py-1 text-xs font-semibold rounded-md flex-shrink-0
                                                @if($sale->status === 'completed') bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-300
                                                @elseif($sale->status === 'cancelled' || $sale->status === 'refunded') bg-red-100 dark:bg-red-500/15 text-red-700 dark:text-red-300
                                                @elseif($sale->status === 'delivered') bg-blue-100 dark:bg-blue-500/15 text-blue-700 dark:text-blue-300
                                                @else bg-yellow-100 dark:bg-yellow-500/15 text-yellow-700 dark:text-yellow-300 @endif">
                                                {{ $sale->status_text }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-10">
                                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-shopping-cart text-gray-300 dark:text-gray-600"></i>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Aucune vente pour le moment</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Derniers avis -->
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
                        <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-star text-sm text-amber-500 dark:text-amber-400"></i>
                            Derniers avis
                        </h3>
                        <a href="{{ route('seller.reviews') }}" class="text-sm font-medium text-vinted-primary-600 dark:text-vinted-primary-400 hover:text-vinted-primary-700 dark:hover:text-vinted-primary-300 transition-colors">Voir tout</a>
                    </div>
                    <div class="p-4">
                        @if($reviews->count() > 0)
                            <div class="space-y-3">
                                @foreach($reviews as $review)
                                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                                <div class="w-9 h-9 bg-vinted-primary-600 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
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
                                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-star text-gray-300 dark:text-gray-600"></i>
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