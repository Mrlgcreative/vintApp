@extends('layouts.admin')

@section('title', 'Boosts appliqués')
@section('page-title', 'Boosts appliqués')

@section('content')
@php
    $currentSort = request('sort', '-created_at');
    $sortUrl = function (string $col) use ($currentSort) {
        $dir = $currentSort === $col ? '-' . $col : $col;
        return request()->fullUrlWithQuery(['sort' => $dir]);
    };
    $sortDir = fn (string $col) => $currentSort === $col ? 'asc' : ($currentSort === '-' . $col ? 'desc' : null);

    $formatPrice = function ($price, $currency) {
        $currency = $currency ?? 'USD';
        return $currency === 'CDF'
            ? number_format((float) $price, 0, ',', ' ') . ' FC'
            : '$' . number_format((float) $price, 2);
    };

    $statusBadge = [
        'active' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
        'expired' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300',
        'cancelled' => 'bg-red-50 text-red-700 dark:bg-red-900/40 dark:text-red-300',
    ];
    $statusLabel = [
        'active' => 'Actif',
        'expired' => 'Expiré',
        'cancelled' => 'Annulé',
    ];

    $inputClasses = 'flex h-9 w-full rounded-md border border-slate-200 bg-white px-3 py-1 text-sm text-slate-900 shadow-sm transition-colors placeholder:text-slate-400 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-primary-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white';
@endphp

<div class="space-y-6">
    @php
    $stats['active_pct'] = $stats['total'] > 0 ? round($stats['active'] / $stats['total'] * 100) : 0;
@endphp

    <!-- Statistiques -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Boosts au total</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['total']) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300">
                    <i class="fas fa-bolt text-[10px] text-primary-500"></i>
                    Boost
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-fire text-xs text-primary-500"></i>
                    Tous statuts confondus
                </div>
                <div class="text-xs text-slate-400">Sur l'ensemble des articles</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Actifs</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['active']) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-white px-2 py-0.5 text-xs font-medium text-emerald-700 dark:border-emerald-800/40 dark:bg-slate-900 dark:text-emerald-300">
                    <i class="fas fa-arrow-trend-up text-[10px]"></i>
                    {{ $stats['active_pct'] }}%
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-circle-check text-xs text-emerald-500"></i>
                    En cours d'exécution
                </div>
                <div class="text-xs text-slate-400">Des boosts au total</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Expirés</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['expired']) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300">
                    <i class="fas fa-hourglass-end text-[10px] text-amber-500"></i>
                    Terminés
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-hourglass-end text-xs text-amber-500"></i>
                    Durée écoulée
                </div>
                <div class="text-xs text-slate-400">Boosts arrivés à expiration</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Annulés</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['cancelled']) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300">
                    <i class="fas fa-ban text-[10px] text-red-500"></i>
                    Stoppés
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-xmark text-xs text-red-500"></i>
                    Annulés par l'admin
                </div>
                <div class="text-xs text-slate-400">Boosts interrompus</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Revenus (actifs)</p>
            <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white sm:text-2xl">${{ number_format($stats['revenue'], 2) }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300">
                    USD
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-chart-line text-xs text-sky-500"></i>
                    Gains générés
                </div>
                <div class="text-xs text-slate-400">Spotlight des boosts actifs</div>
            </div>
        </div>
    </div>

    <!-- Filtres + Table -->
    <div class="rounded-md border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <form method="GET" class="flex flex-col gap-3 border-b border-slate-200 p-3 dark:border-slate-700 md:flex-row md:items-center">
            <div class="relative flex-1 md:max-w-xs">
                <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Filtrer article, vendeur..." class="{{ $inputClasses }} pl-9">
            </div>

            <select name="status" class="{{ $inputClasses }} md:w-44">
                <option value="">Tous les statuts</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expiré</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulé</option>
            </select>

            <select name="boost_type" class="{{ $inputClasses }} md:w-44">
                <option value="">Tous les types</option>
                @foreach($boostTypes as $bt)
                    <option value="{{ $bt->id }}" {{ request('boost_type') == $bt->id ? 'selected' : '' }}>{{ $bt->display_name }}</option>
                @endforeach
            </select>

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="inline-flex h-9 items-center justify-center gap-2 rounded-md bg-primary-600 px-4 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700">
                    <i class="fas fa-filter text-xs"></i>
                    Filtrer
                </button>
                @if(request()->hasAny(['search', 'status', 'boost_type']))
                    <a href="{{ route('admin.product-boosts.index') }}" title="Réinitialiser"
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
                        <th class="h-11 px-3 text-left align-middle text-xs font-medium text-slate-500 dark:text-slate-400">Article</th>
                        <th class="h-11 px-3 text-left align-middle text-xs font-medium text-slate-500 dark:text-slate-400">Vendeur</th>
                        <th class="h-11 px-3 text-left align-middle text-xs font-medium text-slate-500 dark:text-slate-400">Type</th>
                        <th class="h-11 px-3 text-right align-middle text-xs font-medium text-slate-500 dark:text-slate-400">Durée</th>
                        <th class="h-11 px-3 text-right align-middle text-xs font-medium text-slate-500 dark:text-slate-400">
                            <a href="{{ $sortUrl('total_price') }}" class="-mr-2 inline-flex h-8 items-center gap-1.5 rounded-md px-2 font-medium transition-colors hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white">
                                Prix
                                @if($sortDir('total_price'))<i class="fas fa-arrow-{{ $sortDir('total_price') === 'desc' ? 'down' : 'up' }} text-[10px]"></i>
                                @else<i class="fas fa-sort text-[10px] text-slate-400"></i>@endif
                            </a>
                        </th>
                        <th class="h-11 px-3 text-right align-middle text-xs font-medium text-slate-500 dark:text-slate-400">
                            <a href="{{ $sortUrl('views_generated') }}" class="-mr-2 inline-flex h-8 items-center gap-1.5 rounded-md px-2 font-medium transition-colors hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white">
                                Vues
                                @if($sortDir('views_generated'))<i class="fas fa-arrow-{{ $sortDir('views_generated') === 'desc' ? 'down' : 'up' }} text-[10px]"></i>
                                @else<i class="fas fa-sort text-[10px] text-slate-400"></i>@endif
                            </a>
                        </th>
                        <th class="h-11 px-3 text-left align-middle text-xs font-medium text-slate-500 dark:text-slate-400">Statut</th>
                        <th class="h-11 px-3 text-left align-middle text-xs font-medium text-slate-500 dark:text-slate-400">
                            <a href="{{ $sortUrl('created_at') }}" class="-ml-2 inline-flex h-8 items-center gap-1.5 rounded-md px-2 font-medium transition-colors hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white">
                                Date
                                @if($sortDir('created_at'))<i class="fas fa-arrow-{{ $sortDir('created_at') === 'desc' ? 'down' : 'up' }} text-[10px]"></i>
                                @else<i class="fas fa-sort text-[10px] text-slate-400"></i>@endif
                            </a>
                        </th>
                        <th class="h-11 px-3 text-right align-middle text-xs font-medium text-slate-500 dark:text-slate-400">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($boosts as $boost)
                        <tr class="border-b border-slate-100 transition-colors last:border-0 hover:bg-slate-50 dark:border-slate-700/50 dark:hover:bg-slate-800/40">
                            <td class="px-3 py-3 align-middle">
                                <a href="{{ route('admin.product-boosts.show', $boost) }}" class="flex items-center gap-3 font-medium text-slate-900 transition-colors hover:text-primary-600 dark:text-white">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-md bg-slate-100 dark:bg-slate-700">
                                        @if($boost->item?->images)
                                            <img src="{{ $boost->item->images[0] }}" alt="" class="h-full w-full object-cover">
                                        @else
                                            <i class="fas fa-image text-sm text-slate-400"></i>
                                        @endif
                                    </span>
                                    <span class="max-w-[200px] truncate">{{ $boost->item?->name ?? 'N/A' }}</span>
                                </a>
                            </td>
                            <td class="px-3 py-3 align-middle">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-600/10 text-xs font-semibold text-primary-600 dark:bg-primary-400/10 dark:text-primary-300">
                                        {{ strtoupper(substr($boost->user?->name ?? '?', 0, 2)) }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate font-medium text-slate-900 dark:text-white">{{ $boost->user?->name ?? 'N/A' }}</p>
                                        @if($boost->user?->email)
                                            <p class="truncate text-xs text-slate-400">{{ $boost->user->email }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-3 align-middle whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 rounded-md border border-transparent px-2.5 py-0.5 text-xs font-medium"
                                      style="background: {{ $boost->boostType?->color ?? '#7c3aed' }}1a; color: {{ $boost->boostType?->color ?? '#7c3aed' }}; border-color: {{ $boost->boostType?->color ?? '#7c3aed' }}40;">
                                    <i class="{{ $boost->boostType?->icon ?? 'fas fa-bolt' }} text-[10px]"></i>
                                    {{ $boost->boostType?->display_name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-right align-middle whitespace-nowrap tabular-nums text-slate-700 dark:text-slate-200">
                                {{ $boost->duration }}<span class="text-xs text-slate-400"> j</span>
                            </td>
                            <td class="px-3 py-3 text-right align-middle whitespace-nowrap font-medium tabular-nums text-slate-900 dark:text-white">
                                {{ $formatPrice($boost->total_price, $boost->currency) }}
                            </td>
                            <td class="px-3 py-3 text-right align-middle whitespace-nowrap tabular-nums text-slate-600 dark:text-slate-300">
                                {{ number_format($boost->views_generated) }}
                            </td>
                            <td class="px-3 py-3 align-middle whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 rounded-md border border-transparent px-2.5 py-0.5 text-xs font-medium {{ $statusBadge[$boost->status] ?? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">
                                    <i class="fas fa-circle text-[5px] opacity-70"></i>
                                    {{ $statusLabel[$boost->status] ?? ucfirst($boost->status) }}
                                </span>
                            </td>
                            <td class="px-3 py-3 align-middle whitespace-nowrap">
                                <div class="font-medium text-slate-900 dark:text-white">{{ $boost->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-slate-400">{{ $boost->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-3 py-3 text-right align-middle whitespace-nowrap">
                                <a href="{{ route('admin.product-boosts.show', $boost) }}"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white" title="Voir le détail">
                                    <span class="sr-only">Voir le détail</span>
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="h-40 px-3 text-center align-middle">
                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-md bg-slate-100 dark:bg-slate-800">
                                    <i class="fas fa-rocket text-lg text-slate-400"></i>
                                </div>
                                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Aucun résultat</p>
                                <p class="mt-1 text-xs text-slate-400">Essayez de modifier vos filtres.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($boosts->hasPages())
            <div class="flex flex-col gap-3 border-t border-slate-200 px-3 py-3 dark:border-slate-700 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-xs text-slate-500 dark:text-slate-400 sm:text-sm">
                    Affichage de <span class="font-medium text-slate-700 dark:text-slate-200">{{ number_format($boosts->firstItem()) }}</span> à <span class="font-medium text-slate-700 dark:text-slate-200">{{ number_format($boosts->lastItem()) }}</span> sur <span class="font-medium text-slate-700 dark:text-slate-200">{{ number_format($boosts->total()) }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-300">Page {{ $boosts->currentPage() }} sur {{ $boosts->lastPage() }}</span>
                    <a href="{{ $boosts->previousPageUrl() }}" {{ $boosts->onFirstPage() ? 'aria-disabled="true" tabindex="-1"' : '' }}
                       class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-500 shadow-sm transition-colors hover:bg-slate-50 {{ $boosts->onFirstPage() ? 'pointer-events-none opacity-40' : '' }} dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                        <span class="sr-only">Page précédente</span>
                        <i class="fas fa-chevron-left text-xs"></i>
                    </a>
                    <a href="{{ $boosts->nextPageUrl() }}" {{ $boosts->hasMorePages() ? '' : 'aria-disabled="true" tabindex="-1"' }}
                       class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-500 shadow-sm transition-colors hover:bg-slate-50 {{ $boosts->hasMorePages() ? '' : 'pointer-events-none opacity-40' }} dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                        <span class="sr-only">Page suivante</span>
                        <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection