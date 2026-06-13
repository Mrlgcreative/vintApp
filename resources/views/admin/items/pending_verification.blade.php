@extends('layouts.admin')

@section('title', 'Articles en attente de vérification')
@section('page-title', 'Articles en attente de vérification')

@section('content')
<div x-data="{ filterOpen: false }">
    {{-- Flash message --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-check-circle flex-shrink-0"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4 sm:mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-clock text-purple-600 dark:text-purple-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">En attente</p>
                    <p class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">{{ $items->total() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-box text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total articles</p>
                    <p class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">{{ \App\Models\Item::count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Rechercher par nom ou vendeur..." 
                       class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            </div>
            <div class="sm:w-48">
                <select name="category" 
                        class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                    <option value="">Toutes catégories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors">
                <i class="fas fa-filter mr-1"></i> Filtrer
            </button>
            @if(request()->anyFilled(['search', 'category']))
                <a href="{{ route('admin.items.pending_verification') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-1"></i> Réinitialiser
                </a>
            @endif
        </form>
    </div>

    @if($items->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-2xl text-gray-400 dark:text-gray-500"></i>
            </div>
            <h3 class="text-base font-medium text-gray-900 dark:text-white mb-1">Aucun article en attente</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Tous les articles ont été vérifiés.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($items as $item)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden" x-data="{ showRejectForm: false }">
                    <div class="p-4 sm:p-5">
                        <div class="flex flex-col lg:flex-row lg:gap-5">
                            {{-- Image --}}
                            <div class="flex-shrink-0 mb-4 lg:mb-0">
                                <div class="w-full lg:w-40 h-32 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                                    @if($item->images && count($item->images) > 0)
                                        <img src="{{ asset('storage/' . $item->images[0]) }}" 
                                             class="w-full h-full object-cover" loading="lazy" alt="{{ $item->name }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <i class="fas fa-image text-2xl"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Détails --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white truncate">
                                                {{ $item->name }}
                                            </h3>
                                            <span class="text-xs text-gray-400 font-normal">#{{ $item->id }}</span>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            @if($item->brand)
                                                <span><i class="fas fa-tag"></i> {{ $item->brand->name }}</span>
                                                <span>•</span>
                                            @endif
                                            <span>{{ $item->category->name ?? 'N/A' }}</span>
                                            <span>•</span>
                                            <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $item->currency_symbol ?? '' }} {{ number_format($item->price, 2, ',', ' ') }}</span>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 whitespace-nowrap">
                                        <i class="fas fa-clock mr-1"></i> En attente
                                    </span>
                                </div>

                                <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2 mb-3">{{ $item->description }}</p>

                                {{-- Vendeur --}}
                                <div class="flex items-center gap-2 mb-3">
                                    @if($item->user)
                                        <div class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                                            @if($item->user->avatar)
                                                <img src="{{ $item->user->avatar_url ?? $item->user->avatar }}" class="w-5 h-5 rounded-full" alt="">
                                            @else
                                                <div class="w-5 h-5 rounded-full bg-primary-500 flex items-center justify-center text-white text-[10px] font-semibold">{{ substr($item->user->name, 0, 1) }}</div>
                                            @endif
                                            <span>{{ $item->user->name }}</span>
                                            <span class="text-gray-300 dark:text-gray-600">•</span>
                                            <span>{{ $item->created_at->diffForHumans() }}</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Détails supplémentaires (si spécifications existent) --}}
                                @if($item->size || $item->color || $item->condition)
                                    <div class="flex flex-wrap gap-1.5 mb-3">
                                        @if($item->condition)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                                {{ ucfirst($item->condition) }}
                                            </span>
                                        @endif
                                        @if($item->size)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                                {{ $item->size }}
                                            </span>
                                        @endif
                                        @if($item->color)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                                {{ $item->color }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-gray-100 dark:border-gray-700 mt-3">
                            <a href="{{ route('admin.items.show', $item) }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                <i class="fas fa-eye text-xs"></i> Voir détails
                            </a>

                            <form action="{{ route('admin.items.approve', $item) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="inline-flex items-center gap-1.5 px-4 py-1.5 text-sm font-medium bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                                        onclick="return confirm('Approuver et publier cet article ?')">
                                    <i class="fas fa-check text-xs"></i> Approuver
                                </button>
                            </form>

                            <button type="button" 
                                    @click="showRejectForm = !showRejectForm"
                                    class="inline-flex items-center gap-1.5 px-4 py-1.5 text-sm font-medium bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                <i class="fas fa-times text-xs"></i> Rejeter
                            </button>
                        </div>

                        {{-- Formulaire de rejet --}}
                        <div x-show="showRejectForm" x-transition style="display:none;" class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                            <form action="{{ route('admin.items.reject', $item) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Motif du rejet</label>
                                    <textarea name="reason" rows="2" required
                                              class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none resize-none"
                                              placeholder="Raison du rejet..."></textarea>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="submit" 
                                            class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                                        <i class="fas fa-times mr-1"></i> Confirmer le rejet
                                    </button>
                                    <button type="button" 
                                            @click="showRejectForm = false"
                                            class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                        Annuler
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($items->hasPages())
            <div class="mt-6">
                {{ $items->appends(request()->query())->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
