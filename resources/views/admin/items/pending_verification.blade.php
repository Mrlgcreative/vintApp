@extends('layouts.admin')

@section('title', 'Articles en attente de vérification')
@section('page-title', 'Articles en attente de vérification')
@section('page-subtitle', "Validez ou rejetez les articles soumis")

@push('styles')
<style>
    @keyframes pulse-dot { 0%,100%{ box-shadow:0 0 0 0 rgba(16,185,129,.55) } 50%{ box-shadow:0 0 0 6px rgba(16,185,129,0) } }
    .pulse-dot{ animation:pulse-dot 2s infinite }
</style>
@endpush

@section('content')
<div x-data="{ filterOpen: false }">
    {{-- Stats --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5">
        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">En attente</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($items->total()) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-400">
                    <i class="fas fa-clock text-[10px]"></i>
                    À vérifier
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-hourglass-half text-xs text-amber-500"></i>
                    Soumissions en cours
                </div>
                <div class="text-xs text-slate-400">En attente de modération</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Total articles</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format(\App\Models\Item::count()) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300">
                    <i class="fas fa-box text-[10px] text-sky-500"></i>
                    Catalogue
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-boxes-stacked text-xs text-sky-500"></i>
                    Plateau complet
                </div>
                <div class="text-xs text-slate-400">Sur toute la plateforme</div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="mb-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800 sm:p-5">
        <form method="GET" class="flex flex-col gap-3 sm:flex-row">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Rechercher par nom ou vendeur..." 
                       class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
            </div>
            <div class="sm:w-48">
                <select name="category" 
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                    <option value="">Toutes catégories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700">
                <i class="fas fa-filter"></i> Filtrer
            </button>
            @if(request()->anyFilled(['search', 'category']))
                <a href="{{ route('admin.items.pending_verification') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    <i class="fas fa-times"></i> Réinitialiser
                </a>
            @endif
        </form>
    </div>

    @if($items->isEmpty())
        <div class="rounded-xl border border-slate-200 bg-white p-12 text-center shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700">
                <i class="fas fa-check-circle text-2xl text-slate-400 dark:text-slate-500"></i>
            </div>
            <h3 class="mb-1 text-base font-medium text-slate-900 dark:text-white">Aucun article en attente</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Tous les articles ont été vérifiés.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($items as $item)
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800" x-data="{ showRejectForm: false }">
                    <div class="p-4 sm:p-5">
                        <div class="flex flex-col lg:flex-row lg:gap-5">
                            {{-- Image --}}
                            <div class="mb-4 flex-shrink-0 lg:mb-0">
                                <div class="h-32 w-full overflow-hidden rounded-lg border border-slate-200 bg-slate-100 dark:border-slate-600 dark:bg-slate-700 lg:w-40">
                                    @if($item->images && count($item->images) > 0)
                                        <img src="{{ asset('storage/' . $item->images[0]) }}" 
                                             class="h-full w-full object-cover" loading="lazy" alt="{{ $item->name }}">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-slate-400">
                                            <i class="fas fa-image text-2xl"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Détails --}}
                            <div class="min-w-0 flex-1">
                                <div class="mb-2 flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <h3 class="truncate text-base font-semibold text-slate-900 dark:text-white sm:text-lg">
                                                {{ $item->name }}
                                            </h3>
                                            <span class="text-xs font-normal text-slate-400">#{{ $item->id }}</span>
                                        </div>
                                        <div class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-slate-500 dark:text-slate-400">
                                            @if($item->brand)
                                                <span><i class="fas fa-tag"></i> {{ $item->brand->name }}</span>
                                                <span>•</span>
                                            @endif
                                            <span>{{ $item->category->name ?? 'N/A' }}</span>
                                            <span>•</span>
                                            <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $item->currency_symbol ?? '' }} {{ number_format($item->price, 2, ',', ' ') }}</span>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center whitespace-nowrap rounded-full bg-amber-50 px-2.5 py-0.5 text-[10px] font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300">
                                        <i class="fas fa-clock mr-1"></i> En attente
                                    </span>
                                </div>

                                <p class="mb-3 line-clamp-2 text-sm text-slate-600 dark:text-slate-300">{{ $item->description }}</p>

                                {{-- Vendeur --}}
                                <div class="mb-3 flex items-center gap-2">
                                    @if($item->user)
                                        <div class="flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-400">
                                            @if($item->user->avatar)
                                                <img src="{{ $item->user->avatar_url ?? $item->user->avatar }}" class="h-5 w-5 rounded-full" alt="">
                                            @else
                                                <div class="flex h-5 w-5 items-center justify-center rounded-full bg-primary-500 text-[10px] font-semibold text-white">{{ substr($item->user->name, 0, 1) }}</div>
                                            @endif
                                            <span>{{ $item->user->name }}</span>
                                            <span class="text-slate-300 dark:text-slate-600">•</span>
                                            <span>{{ $item->created_at->diffForHumans() }}</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Détails supplémentaires (si spécifications existent) --}}
                                @if($item->size || $item->color || $item->condition)
                                    <div class="mb-3 flex flex-wrap gap-1.5">
                                        @if($item->condition)
                                            <span class="inline-flex items-center rounded bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                                {{ ucfirst($item->condition) }}
                                            </span>
                                        @endif
                                        @if($item->size)
                                            <span class="inline-flex items-center rounded bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                                {{ $item->size }}
                                            </span>
                                        @endif
                                        @if($item->color)
                                            <span class="inline-flex items-center rounded bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                                {{ $item->color }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="mt-3 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-3 dark:border-slate-700">
                            <a href="{{ route('admin.items.show', $item) }}" 
                               class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                                <i class="fas fa-eye text-xs"></i> Voir détails
                            </a>

                            <form action="{{ route('admin.items.approve', $item) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-emerald-700"
                                        onclick="return confirm('Approuver et publier cet article ?')">
                                    <i class="fas fa-check text-xs"></i> Approuver
                                </button>
                            </form>

                            <button type="button" 
                                    @click="showRejectForm = !showRejectForm"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-red-700">
                                <i class="fas fa-times text-xs"></i> Rejeter
                            </button>
                        </div>

                        {{-- Formulaire de rejet --}}
                        <div x-show="showRejectForm" x-transition style="display:none;" class="mt-3 border-t border-slate-100 pt-3 dark:border-slate-700">
                            <form action="{{ route('admin.items.reject', $item) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="mb-3">
                                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Motif du rejet</label>
                                    <textarea name="reason" rows="2" required
                                              class="w-full resize-none rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                                              placeholder="Raison du rejet..."></textarea>
                                </div>
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-red-700">
                                        <i class="fas fa-times"></i> Confirmer le rejet
                                    </button>
                                    <button type="button" 
                                            @click="showRejectForm = false"
                                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
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
