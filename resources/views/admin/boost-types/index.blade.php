@extends('layouts.admin')

@section('title', 'Types de Boost')
@section('page-title', 'Types de Boost')

@section('page-actions')
<div class="flex items-center gap-2">
    <a href="{{ route('admin.boost-types.create') }}"
       class="inline-flex h-9 items-center justify-center gap-2 rounded-md bg-primary-600 px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700">
        <i class="fas fa-plus text-xs"></i>
        Nouveau type
    </a>
</div>
@endsection

@section('content')
@php
    $currentSort = request('sort', 'sort_order');
    $sortUrl = function (string $col) use ($currentSort) {
        $dir = $currentSort === $col ? '-' . $col : $col;
        return request()->fullUrlWithQuery(['sort' => $dir]);
    };
    $sortDir = fn (string $col) => $currentSort === $col ? 'asc' : ($currentSort === '-' . $col ? 'desc' : null);

    $stats = [
        'total' => \App\Models\BoostType::count(),
        'active' => \App\Models\BoostType::where('is_active', true)->count(),
        'premium' => \App\Models\BoostType::where('is_premium', true)->count(),
        'active_boosts' => \App\Models\ProductBoost::where('status', 'active')->count(),
    ];

    $iconColor = fn (string $hex) => ($hex ?? '#7c3aed');

    $inputClasses = 'flex h-9 w-full rounded-md border border-slate-200 bg-white px-3 py-1 text-sm text-slate-900 shadow-sm transition-colors placeholder:text-slate-400 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-primary-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white';
@endphp

<div class="space-y-6">
    <!-- Statistiques -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Total types</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['total']) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300">
                    <i class="fas fa-bolt text-[10px] text-primary-500"></i>
                    Types
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-layer-group text-xs text-primary-500"></i>
                    Catalogue complet
                </div>
                <div class="text-xs text-slate-400">Tous les types de boost</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Actifs</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['active']) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-white px-2 py-0.5 text-xs font-medium text-emerald-700 dark:border-emerald-800/40 dark:bg-slate-900 dark:text-emerald-300">
                    <i class="fas fa-circle-check text-[10px]"></i>
                    Disponibles
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-circle-check text-xs text-emerald-500"></i>
                    En ligne
                </div>
                <div class="text-xs text-slate-400">Types proposés aux vendeurs</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Premium</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['premium']) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-amber-200 bg-white px-2 py-0.5 text-xs font-medium text-amber-700 dark:border-amber-800/40 dark:bg-slate-900 dark:text-amber-300">
                    <i class="fas fa-crown text-[10px]"></i>
                    VIP
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-crown text-xs text-amber-500"></i>
                    Offres premium
                </div>
                <div class="text-xs text-slate-400">Types réservés aux meilleurs</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Boosts actifs</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['active_boosts']) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300">
                    <i class="fas fa-rocket text-[10px] text-sky-500"></i>
                    En cours
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-rocket text-xs text-sky-500"></i>
                    Appliqués aux articles
                </div>
                <div class="text-xs text-slate-400">Boosts actuellement actifs</div>
            </div>
        </div>
    </div>

    <!-- Filtres + Table -->
    <div class="rounded-md border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <form method="GET" class="flex flex-col gap-3 border-b border-slate-200 p-3 dark:border-slate-700 md:flex-row md:items-center">
            <div class="relative flex-1 md:max-w-xs">
                <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Rechercher un type de boost..." class="{{ $inputClasses }} pl-9">
            </div>

            <select name="status" class="{{ $inputClasses }} md:w-44">
                <option value="">Tous les statuts</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
            </select>

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="inline-flex h-9 items-center justify-center gap-2 rounded-md bg-primary-600 px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700">
                    <i class="fas fa-filter text-xs"></i>
                    Filtrer
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.boost-types.index') }}" title="Réinitialiser"
                       class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-500 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-700">
                        <i class="fas fa-rotate-left text-xs"></i>
                    </a>
                @endif
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full caption-bottom text-sm">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-700">
                        <th class="h-11 px-3 text-left align-middle text-xs font-medium text-slate-500 dark:text-slate-400">
                            <a href="{{ $sortUrl('display_name') }}" class="-ml-2 inline-flex h-8 items-center gap-1.5 rounded-md px-2 font-medium transition-colors hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white">
                                Nom
                                @if($sortDir('display_name'))<i class="fas fa-arrow-{{ $sortDir('display_name') === 'desc' ? 'down' : 'up' }} text-[10px]"></i>
                                @else<i class="fas fa-sort text-[10px] text-slate-400"></i>@endif
                            </a>
                        </th>
                        <th class="h-11 px-3 text-left align-middle text-xs font-medium text-slate-500 dark:text-slate-400">Prix</th>
                        <th class="h-11 px-3 text-left align-middle text-xs font-medium text-slate-500 dark:text-slate-400">Durées</th>
                        <th class="h-11 px-3 text-left align-middle text-xs font-medium text-slate-500 dark:text-slate-400">Statut</th>
                        <th class="h-11 px-3 text-right align-middle text-xs font-medium text-slate-500 dark:text-slate-400">Boosts</th>
                        <th class="h-11 px-3 text-right align-middle text-xs font-medium text-slate-500 dark:text-slate-400">
                            <a href="{{ $sortUrl('sort_order') }}" class="-mr-2 inline-flex h-8 items-center gap-1.5 rounded-md px-2 font-medium transition-colors hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white">
                                Ordre
                                @if($sortDir('sort_order'))<i class="fas fa-arrow-{{ $sortDir('sort_order') === 'desc' ? 'down' : 'up' }} text-[10px]"></i>
                                @else<i class="fas fa-sort text-[10px] text-slate-400"></i>@endif
                            </a>
                        </th>
                        <th class="h-11 px-3 text-right align-middle text-xs font-medium text-slate-500 dark:text-slate-400">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($boostTypes as $bt)
                        <tr class="border-b border-slate-100 transition-colors last:border-0 hover:bg-slate-50 dark:border-slate-700/50 dark:hover:bg-slate-800/40 {{ !$bt->is_active ? 'opacity-60' : '' }}">
                            <td class="px-3 py-3 align-middle">
                                <a href="{{ route('admin.boost-types.show', $bt) }}" class="flex items-center gap-3 font-medium text-slate-900 transition-colors hover:text-primary-600 dark:text-white">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md text-white" style="background: {{ $iconColor($bt->color) }}">
                                        <i class="{{ $bt->icon ?? 'fas fa-bolt' }} text-sm"></i>
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block truncate">{{ $bt->display_name }}</span>
                                        <span class="block text-xs text-slate-400">({{ $bt->name }})</span>
                                    </span>
                                </a>
                            </td>
                            <td class="px-3 py-3 align-middle whitespace-nowrap">
                                <div class="font-medium tabular-nums text-slate-900 dark:text-white">${{ number_format($bt->price_usd ?? 0, 2) }}</div>
                                <div class="text-xs tabular-nums text-slate-400">{{ number_format($bt->price_cdf ?? 0, 0, ',', ' ') }} FC</div>
                            </td>
                            <td class="px-3 py-3 align-middle">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($bt->available_durations ?? [] as $d)
                                        <span class="inline-flex items-center rounded-md border border-slate-200 bg-slate-50 px-2 py-0.5 text-xs font-medium text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">{{ $d }}j</span>
                                    @empty
                                        <span class="text-xs text-slate-400">—</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-3 py-3 align-middle whitespace-nowrap">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="inline-flex items-center gap-1.5 rounded-md border border-transparent px-2.5 py-0.5 text-xs font-medium {{ $bt->is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">
                                        <i class="fas fa-circle text-[5px] opacity-70"></i>
                                        {{ $bt->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                    @if($bt->is_premium)
                                        <span class="inline-flex items-center gap-1.5 rounded-md border border-transparent bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                                            <i class="fas fa-crown text-[10px]"></i>
                                            Premium
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-3 text-right align-middle whitespace-nowrap tabular-nums text-slate-700 dark:text-slate-200">
                                {{ number_format($bt->product_boosts_count ?? 0) }}
                            </td>
                            <td class="px-3 py-3 text-right align-middle whitespace-nowrap tabular-nums text-slate-500 dark:text-slate-400">
                                {{ $bt->sort_order }}
                            </td>
                            <td class="px-3 py-3 text-right align-middle whitespace-nowrap">
                                <div class="flex items-center justify-end gap-0.5">
                                    <a href="{{ route('admin.boost-types.show', $bt) }}" title="Voir le détail"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white">
                                        <span class="sr-only">Voir le détail</span>
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('admin.boost-types.edit', $bt) }}" title="Modifier"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 transition-colors hover:bg-primary-50 hover:text-primary-600 dark:text-slate-400 dark:hover:bg-primary-900/20 dark:hover:text-primary-300">
                                        <span class="sr-only">Modifier</span>
                                        <i class="fas fa-pen text-sm"></i>
                                    </a>
                                    <form action="{{ route('admin.boost-types.update-status', $bt) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="is_active" value="{{ $bt->is_active ? '0' : '1' }}">
                                        <button type="submit" title="{{ $bt->is_active ? 'Désactiver' : 'Activer' }}"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-md transition-colors {{ $bt->is_active ? 'text-slate-500 hover:bg-amber-50 hover:text-amber-600 dark:text-slate-400 dark:hover:bg-amber-900/20 dark:hover:text-amber-300' : 'text-slate-500 hover:bg-emerald-50 hover:text-emerald-600 dark:text-slate-400 dark:hover:bg-emerald-900/20 dark:hover:text-emerald-300' }}">
                                            <span class="sr-only">{{ $bt->is_active ? 'Désactiver' : 'Activer' }}</span>
                                            <i class="fas fa-{{ $bt->is_active ? 'pause' : 'play' }} text-sm"></i>
                                        </button>
                                    </form>
                                    <button onclick="confirmDelete({{ $bt->id }}, '{{ $bt->display_name }}')" title="Supprimer"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 transition-colors hover:bg-red-50 hover:text-red-600 dark:text-slate-400 dark:hover:bg-red-900/20 dark:hover:text-red-300">
                                        <span class="sr-only">Supprimer</span>
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="h-40 px-3 text-center align-middle">
                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-md bg-slate-100 dark:bg-slate-800">
                                    <i class="fas fa-bolt text-lg text-slate-400"></i>
                                </div>
                                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Aucun résultat</p>
                                <p class="mt-1 text-xs text-slate-400">Essayez de modifier vos filtres.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($boostTypes->hasPages())
            <div class="flex flex-col gap-3 border-t border-slate-200 px-3 py-3 dark:border-slate-700 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-xs text-slate-500 dark:text-slate-400 sm:text-sm">
                    Affichage de <span class="font-medium text-slate-700 dark:text-slate-200">{{ number_format($boostTypes->firstItem()) }}</span> à <span class="font-medium text-slate-700 dark:text-slate-200">{{ number_format($boostTypes->lastItem()) }}</span> sur <span class="font-medium text-slate-700 dark:text-slate-200">{{ number_format($boostTypes->total()) }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-300">Page {{ $boostTypes->currentPage() }} sur {{ $boostTypes->lastPage() }}</span>
                    <a href="{{ $boostTypes->previousPageUrl() }}" {{ $boostTypes->onFirstPage() ? 'aria-disabled="true" tabindex="-1"' : '' }}
                       class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-500 shadow-sm transition-colors hover:bg-slate-50 {{ $boostTypes->onFirstPage() ? 'pointer-events-none opacity-40' : '' }} dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                        <span class="sr-only">Page précédente</span>
                        <i class="fas fa-chevron-left text-xs"></i>
                    </a>
                    <a href="{{ $boostTypes->nextPageUrl() }}" {{ $boostTypes->hasMorePages() ? '' : 'aria-disabled="true" tabindex="-1"' }}
                       class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-500 shadow-sm transition-colors hover:bg-slate-50 {{ $boostTypes->hasMorePages() ? '' : 'pointer-events-none opacity-40' }} dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                        <span class="sr-only">Page suivante</span>
                        <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modale de confirmation -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()"></div>
    <div class="flex min-h-screen items-center justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
        <div class="inline-block w-full max-w-md animate-pop rounded-xl border border-slate-200 bg-white p-6 text-left align-bottom shadow-2xl sm:align-middle dark:border-slate-700 dark:bg-slate-800">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-md bg-red-50 dark:bg-red-900/20">
                <i class="fas fa-triangle-exclamation text-lg text-red-600 dark:text-red-400"></i>
            </div>
            <h3 class="text-center text-lg font-semibold text-slate-900 dark:text-white">Confirmer la suppression</h3>
            <p class="mt-2 text-center text-sm text-slate-500 dark:text-slate-400">
                Êtes-vous sûr de vouloir supprimer <strong class="text-slate-900 dark:text-white" id="deleteItemName"></strong> ? Cette action est irréversible.
            </p>
            <div class="mt-6 flex items-center justify-end gap-2">
                <button type="button" onclick="closeDeleteModal()"
                        class="inline-flex h-9 items-center justify-center rounded-md border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 shadow-sm transition-colors hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    Annuler
                </button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex h-9 items-center justify-center gap-2 rounded-md bg-red-600 px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-red-700">
                        <i class="fas fa-trash text-xs"></i>
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(id, name) {
    document.getElementById('deleteItemName').textContent = name;
    document.getElementById('deleteForm').action = '{{ url('admin/boost-types') }}/' + id;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
});
</script>
@endpush