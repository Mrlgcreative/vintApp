@extends('layouts.admin')

@section('title', 'Articles en attente de vérification')
@section('page-title', 'Articles à vérifier')
@section('page-subtitle', 'Vérifiez les articles qui correspondent à votre domaine d\'expertise')

@section('content')

    {{-- Statistiques rapides --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-5 mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 bg-orange-50 dark:bg-orange-900/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-orange-500"></i>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">En attente</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $items->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-blue-500"></i>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Votre niveau</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white capitalize">{{ auth()->user()->expertProfile?->certification_level ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-tag text-primary-500"></i>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Spécialités</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-white">{{ count(auth()->user()->expertProfile?->specialties ?? []) }} domaines</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres et recherche --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Recherche</label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Nom du produit..."
                       class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 focus:outline-none transition-colors">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Catégorie</label>
                <select name="category" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 focus:outline-none transition-colors">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Tri</label>
                <select name="sort" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-600 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 focus:outline-none transition-colors">
                    <option value="-created_at" {{ request('sort') === '-created_at' ? 'selected' : '' }}>Plus récents</option>
                    <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Plus anciens</option>
                    <option value="price" {{ request('sort') === 'price' ? 'selected' : '' }}>Prix croissant</option>
                    <option value="-price" {{ request('sort') === '-price' ? 'selected' : '' }}>Prix décroissant</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-semibold transition flex items-center justify-center shadow-sm shadow-primary-600/20">
                    <i class="fas fa-search mr-2"></i>
                    Rechercher
                </button>
            </div>
        </form>
    </div>

    {{-- Liste des articles --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        @forelse($items as $item)
            <a href="{{ route('expert.items.show-for-verification', $item) }}"
               class="block border-b border-slate-100 dark:border-slate-700 last:border-b-0 hover:bg-slate-50 dark:hover:bg-slate-700/40 transition p-5">
                <div class="flex gap-5">
                    <div class="flex-shrink-0">
                        @if($item->getFirstImageUrl())
                            <img src="{{ $item->getFirstImageUrl() }}"
                                 class="w-24 h-24 object-cover rounded-xl ring-1 ring-slate-200 dark:ring-slate-700"
                                 alt="{{ $item->name }}">
                        @else
                            <div class="w-24 h-24 bg-slate-100 dark:bg-slate-700 rounded-xl flex items-center justify-center">
                                <i class="fas fa-image text-slate-400 text-xl"></i>
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between mb-2 gap-3">
                            <div class="min-w-0">
                                <h3 class="text-base font-semibold text-slate-900 dark:text-white truncate">
                                    {{ $item->name }}
                                </h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                    <span class="font-medium">{{ $item->category?->name }}</span>
                                    @if($item->brand)
                                        • <span class="font-medium">{{ $item->brand->name }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-lg font-bold text-slate-900 dark:text-white">
                                    {{ number_format($item->price, 0, ',', ' ') }}
                                    <span class="text-xs text-slate-500 dark:text-slate-400">FCFA</span>
                                </p>
                                <span class="inline-block mt-1.5 px-2.5 py-0.5 bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-xs font-semibold rounded-full">
                                    <i class="fas fa-clock mr-1"></i>
                                    En attente
                                </span>
                            </div>
                        </div>

                        <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2 mb-3">
                            {{ Str::limit($item->description, 150) }}
                        </p>

                        <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                            <div class="flex items-center gap-4">
                                <span class="inline-flex items-center">
                                    <i class="fas fa-user mr-2"></i>
                                    {{ $item->user->name }}
                                </span>
                                <span class="inline-flex items-center">
                                    <i class="fas fa-calendar mr-2"></i>
                                    {{ $item->created_at->format('d/m/Y') }}
                                </span>
                                <span class="inline-flex items-center">
                                    <i class="fas fa-images mr-2"></i>
                                    {{ count($item->images ?? []) }} image(s)
                                </span>
                            </div>
                            <span class="text-primary-600 dark:text-primary-400 font-semibold">
                                Vérifier <i class="fas fa-arrow-right ml-1"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="p-12 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                    <i class="fas fa-inbox text-xl text-slate-400 dark:text-slate-500"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">
                    Aucun article à vérifier
                </h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm">
                    Tous les articles correspondant à vos spécialités ont déjà été vérifiés.
                    <br>Revenez plus tard pour de nouveaux articles.
                </p>
            </div>
        @endforelse
    </div>

    @if($items->hasPages())
        <div class="mt-8">
            {{ $items->links('pagination::tailwind') }}
        </div>
    @endif

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endsection
