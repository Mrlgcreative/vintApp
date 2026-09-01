@extends('layouts.admin')

@section('title', 'Gestion des offres')
@section('page-title', 'Gestion des offres')
@section('page-subtitle', 'Offres et promotions de la plateforme')

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.offers.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-gray-900 hover:bg-gray-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-plus"></i><span class="hidden sm:inline">Nouvelle offre</span><span class="sm:hidden">Ajouter</span>
    </a>
</div>
@endsection

@section('content')
<!-- Statistiques -->
<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 xl:grid-cols-4">
    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Total offres</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($offers->total() ?? 0, 0, ',', ' ') }}</p>
        <span class="absolute right-4 top-4 inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300">
            <i class="fas fa-tags text-[10px] text-emerald-500"></i> Offres
        </span>
    </div>
    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Actives</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($offers->where('status', 'active')->count(), 0, ',', ' ') }}</p>
        <span class="absolute right-4 top-4 inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400">
            <i class="fas fa-circle-check text-[10px]"></i> Actives
        </span>
    </div>
    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Ventes flash</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($offers->where('is_flash_sale', 1)->count(), 0, ',', ' ') }}</p>
        <span class="absolute right-4 top-4 inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-400">
            <i class="fas fa-bolt text-[10px]"></i> Flash
        </span>
    </div>
    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Vedette</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($offers->where('is_featured', 1)->count(), 0, ',', ' ') }}</p>
        <span class="absolute right-4 top-4 inline-flex items-center gap-1 rounded-lg border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-400">
            <i class="fas fa-star text-[10px]"></i> Vedette
        </span>
    </div>
</div>

<!-- Liste des offres -->
<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-5 py-4 dark:border-slate-700">
        <h3 class="flex items-center gap-2 text-sm sm:text-base font-semibold text-slate-900 dark:text-white">
            <i class="fas fa-tags text-gray-900"></i>
            Liste des offres
            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                {{ $offers->total() }} total
            </span>
        </h3>
        <span class="text-xs text-slate-500 dark:text-slate-400">Page {{ $offers->currentPage() }}/{{ $offers->lastPage() }}</span>
    </div>

    <div>
        @if($offers->count() > 0)
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Offre</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Réduction</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Périmètre</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Validité</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Statut</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($offers as $offer)
                        <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-700/30">
                            <td class="px-4 py-3 align-middle">
                                <div class="font-semibold text-slate-900 dark:text-white">{{ $offer->title }}</div>
                                @if($offer->description)
                                    <div class="mt-0.5 text-xs text-slate-400">{{ Str::limit($offer->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold ring-1 ring-inset {{ $offer->is_flash_sale ? 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-300' : 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300' }}">
                                    {{ $offer->discountLabel() }}
                                    @if($offer->is_flash_sale) <i class="fas fa-bolt"></i> @endif
                                </span>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <span class="text-sm text-slate-700 dark:text-slate-200">
                                    @if($offer->scope === 'global') Boutique entière
                                    @elseif($offer->scope === 'categories') {{ $offer->categories->count() }} catégorie(s)
                                    @else {{ $offer->items->count() }} produit(s) @endif
                                </span>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                    @if($offer->starts_at)
                                        <div><i class="fas fa-play mr-1"></i>{{ $offer->starts_at->format('d/m/Y H:i') }}</div>
                                    @else
                                        <div><i class="fas fa-play mr-1"></i>Immédiate</div>
                                    @endif
                                    @if($offer->ends_at)
                                        <div><i class="fas fa-stop mr-1"></i>{{ $offer->ends_at->format('d/m/Y H:i') }}</div>
                                    @else
                                        <div><i class="fas fa-stop mr-1"></i>Illimitée</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $offer->status === 'active' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300' : ($offer->status === 'paused' ? 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300') }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $offer->status === 'active' ? 'bg-emerald-500' : ($offer->status === 'paused' ? 'bg-amber-500' : 'bg-slate-500') }}"></span>
                                    {{ $offer->status === 'active' ? 'Active' : ($offer->status === 'paused' ? 'En pause' : 'Expirée') }}
                                </span>
                                @if($offer->is_featured)
                                    <span class="mt-1 inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300">
                                        <i class="fas fa-star text-amber-500"></i>Vedette
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.offers.edit', $offer) }}"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-slate-50 dark:hover:bg-slate-700"
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.offers.status', $offer) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-slate-50 dark:hover:bg-slate-700"
                                                title="{{ $offer->status === 'active' ? 'Mettre en pause' : 'Activer' }}">
                                            <i class="fas {{ $offer->status === 'active' ? 'fa-pause' : 'fa-play' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.offers.destroy', $offer) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Supprimer cette offre ?');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-500 transition-colors hover:bg-red-50 dark:hover:bg-red-900/20"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="lg:hidden">
                @foreach($offers as $offer)
                <div class="border-b border-slate-100 p-4 hover:bg-slate-50 dark:border-slate-700/50 dark:hover:bg-slate-700/30">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <h3 class="truncate font-semibold text-slate-900 dark:text-white">{{ $offer->title }}</h3>
                            <span class="mt-1 inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold ring-1 ring-inset {{ $offer->is_flash_sale ? 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-300' : 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300' }}">
                                {{ $offer->discountLabel() }} @if($offer->is_flash_sale) <i class="fas fa-bolt"></i> @endif
                            </span>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $offer->status === 'active' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-slate-100 text-slate-600 ring-slate-500/20' }}">
                                {{ $offer->status === 'active' ? 'Active' : ($offer->status === 'paused' ? 'En pause' : 'Expirée') }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-2 flex flex-wrap gap-2 text-xs text-slate-500 dark:text-slate-400">
                        <span><i class="fas fa-tag mr-1"></i>{{ $offer->scope === 'global' ? 'Boutique entière' : ($offer->scope === 'categories' ? $offer->categories->count().' catégorie(s)' : $offer->items->count().' produit(s)') }}</span>
                        @if($offer->ends_at)
                            <span><i class="fas fa-stop mr-1"></i>{{ $offer->ends_at->format('d/m/Y H:i') }}</span>
                        @endif
                    </div>
                    <div class="mt-3 flex items-center gap-2">
                        <a href="{{ route('admin.offers.edit', $offer) }}" class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <form action="{{ route('admin.offers.status', $offer) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700">
                                <i class="fas {{ $offer->status === 'active' ? 'fa-pause' : 'fa-play' }}"></i>
                                {{ $offer->status === 'active' ? 'Pause' : 'Activer' }}
                            </button>
                        </form>
                        <form action="{{ route('admin.offers.destroy', $offer) }}" method="POST" onsubmit="return confirm('Supprimer cette offre ?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm text-red-600 hover:bg-red-50">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>

            @if($offers->hasPages())
                <div class="border-t border-slate-100 p-4 bg-white dark:border-slate-700 dark:bg-slate-800">
                    <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                        <div class="text-center text-xs sm:text-left sm:text-sm text-slate-500 dark:text-slate-300">
                            Affichage de <span class="font-medium text-slate-900 dark:text-white">{{ $offers->firstItem() }}</span> à <span class="font-medium text-slate-900 dark:text-white">{{ $offers->lastItem() }}</span>
                            sur <span class="font-medium text-slate-900 dark:text-white">{{ $offers->total() }}</span> résultats
                        </div>
                        <div class="w-full overflow-x-auto sm:w-auto">
                            {{ $offers->links() }}
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="py-12 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-slate-800">
                    <i class="fas fa-tags text-3xl text-gray-400"></i>
                </div>
                <h5 class="mb-2 text-lg font-semibold text-slate-900 dark:text-white">Aucune offre</h5>
                <p class="mb-4 text-slate-500 dark:text-slate-400">Créez votre première promotion pour booster vos ventes.</p>
                <a href="{{ route('admin.offers.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-900 hover:bg-gray-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                    <i class="fas fa-plus"></i>Ajouter une offre
                </a>
            </div>
        @endif
    </div>
</div>
@endsection