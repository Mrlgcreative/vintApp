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
        'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300',
        'expired' => 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300',
        'cancelled' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-300',
    ];
    $statusDot = [
        'active' => 'bg-emerald-500',
        'expired' => 'bg-slate-400',
        'cancelled' => 'bg-red-500',
    ];
    $statusLabel = [
        'active' => 'Actif',
        'expired' => 'Expiré',
        'cancelled' => 'Annulé',
    ];
@endphp

<div class="space-y-6">
    <!-- Statistiques Principales -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-300">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Boosts au total</p>
                    <p class="text-2xl font-bold tabular-nums text-slate-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-300">
                    <i class="fas fa-circle-check"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Actifs</p>
                    <p class="text-2xl font-bold tabular-nums text-emerald-600 dark:text-emerald-400">{{ number_format($stats['active']) }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-300">
                    <i class="fas fa-hourglass-end"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Expirés</p>
                    <p class="text-2xl font-bold tabular-nums text-amber-600 dark:text-amber-400">{{ number_format($stats['expired']) }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-300">
                    <i class="fas fa-ban"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Annulés</p>
                    <p class="text-2xl font-bold tabular-nums text-red-600 dark:text-red-400">{{ number_format($stats['cancelled']) }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-600 dark:bg-sky-900/20 dark:text-sky-300">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Revenus (actifs)</p>
                    <p class="text-xl font-bold tabular-nums text-slate-900 dark:text-white sm:text-2xl">${{ number_format($stats['revenue'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-700">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-300">
                <i class="fas fa-filter text-sm"></i>
            </div>
            <h5 class="text-sm font-semibold text-slate-900 dark:text-white">Recherche & filtres</h5>
        </div>
        <div class="p-5 sm:p-6">
            <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-12">
                <div class="relative md:col-span-5">
                    <i class="fas fa-search pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Rechercher un article ou un vendeur..."
                           class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                </div>

                <div class="md:col-span-3">
                    <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expiré</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulé</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <select name="boost_type" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <option value="">Tous les types</option>
                        @foreach($boostTypes as $bt)
                            <option value="{{ $bt->id }}" {{ request('boost_type') == $bt->id ? 'selected' : '' }}>{{ $bt->display_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2 md:col-span-2">
                    <button type="submit"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700">
                        <i class="fas fa-filter"></i>
                        Filtrer
                    </button>
                    @if(request()->hasAny(['search', 'status', 'boost_type']))
                        <a href="{{ route('admin.product-boosts.index') }}" title="Réinitialiser"
                           class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-300 text-slate-500 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700">
                            <i class="fas fa-rotate-left text-sm"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des boosts -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-700">
            <h5 class="flex items-center gap-3 text-base font-semibold text-slate-900 dark:text-white">
                <i class="fas fa-rocket text-primary-600 dark:text-primary-400"></i>
                Liste des boosts
                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600 dark:bg-slate-900 dark:text-slate-300">{{ number_format($boosts->total()) }}</span>
            </h5>
        </div>

        <div class="overflow-x-auto">
            @if($boosts->count() > 0)
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Article</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Vendeur</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Type</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Durée</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                <a href="{{ $sortUrl('total_price') }}" class="inline-flex items-center gap-1 transition-colors hover:text-primary-600">
                                    Prix
                                    @if($sortDir('total_price')) <i class="fas fa-chevron-{{ $sortDir('total_price') === 'desc' ? 'down' : 'up' }} text-[10px]"></i> @endif
                                </a>
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                <a href="{{ $sortUrl('views_generated') }}" class="inline-flex items-center gap-1 transition-colors hover:text-primary-600">
                                    Vues
                                    @if($sortDir('views_generated')) <i class="fas fa-chevron-{{ $sortDir('views_generated') === 'desc' ? 'down' : 'up' }} text-[10px]"></i> @endif
                                </a>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Statut</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                <a href="{{ $sortUrl('created_at') }}" class="inline-flex items-center gap-1 transition-colors hover:text-primary-600">
                                    Date
                                    @if($sortDir('created_at')) <i class="fas fa-chevron-{{ $sortDir('created_at') === 'desc' ? 'down' : 'up' }} text-[10px]"></i> @endif
                                </a>
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @foreach($boosts as $boost)
                            <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-700/30">
                                <td class="px-4 py-3 align-middle">
                                    <a href="{{ route('admin.product-boosts.show', $boost) }}" class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-slate-100 dark:bg-slate-700">
                                            @if($boost->item?->images)
                                                <img src="{{ $boost->item->images[0] }}" alt="" class="h-full w-full object-cover">
                                            @else
                                                <i class="fas fa-image text-sm text-slate-400"></i>
                                            @endif
                                        </div>
                                        <span class="max-w-[200px] truncate font-medium text-slate-900 transition-colors hover:text-primary-600 dark:text-white">{{ $boost->item?->name ?? 'N/A' }}</span>
                                    </a>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-50 text-xs font-bold text-primary-600 dark:bg-primary-900/30 dark:text-primary-300">
                                            {{ strtoupper(substr($boost->user?->name ?? '?', 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="truncate font-medium text-slate-900 dark:text-white">{{ $boost->user?->name ?? 'N/A' }}</p>
                                            @if($boost->user?->email)
                                                <p class="truncate text-xs text-slate-400">{{ $boost->user->email }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-middle whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset"
                                          style="background: {{ $boost->boostType?->color ?? '#7c3aed' }}1a; color: {{ $boost->boostType?->color ?? '#7c3aed' }}; border-color: {{ $boost->boostType?->color ?? '#7c3aed' }}40;">
                                        <i class="{{ $boost->boostType?->icon ?? 'fas fa-bolt' }} text-[10px]"></i>
                                        {{ $boost->boostType?->display_name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right align-middle whitespace-nowrap tabular-nums text-slate-700 dark:text-slate-200">
                                    {{ $boost->duration }}<span class="text-xs text-slate-400"> j</span>
                                </td>
                                <td class="px-4 py-3 text-right align-middle whitespace-nowrap font-semibold tabular-nums text-slate-900 dark:text-white">
                                    {{ $formatPrice($boost->total_price, $boost->currency) }}
                                </td>
                                <td class="px-4 py-3 text-right align-middle whitespace-nowrap tabular-nums text-slate-600 dark:text-slate-300">
                                    {{ number_format($boost->views_generated) }}
                                </td>
                                <td class="px-4 py-3 align-middle whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $statusBadge[$boost->status] ?? 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300' }}">
                                        <span class="mr-1.5 h-1.5 w-1.5 rounded-full {{ $statusDot[$boost->status] ?? 'bg-slate-400' }}"></span>
                                        {{ $statusLabel[$boost->status] ?? ucfirst($boost->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 align-middle whitespace-nowrap">
                                    <div class="font-semibold text-slate-900 dark:text-white">{{ $boost->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-slate-400">{{ $boost->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-4 py-3 align-middle whitespace-nowrap text-right">
                                    <a href="{{ route('admin.product-boosts.show', $boost) }}" title="Voir le détail"
                                       class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 text-slate-500 transition-colors hover:border-primary-300 hover:bg-primary-50 hover:text-primary-600 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-primary-900/20">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($boosts->hasPages())
                    <div class="border-t border-slate-100 px-5 py-4 dark:border-slate-700">
                        <div class="flex flex-col items-center justify-between gap-3 sm:flex-row">
                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                Affichage de <span class="font-semibold text-slate-700 dark:text-slate-200">{{ number_format($boosts->firstItem()) }}</span> à <span class="font-semibold text-slate-700 dark:text-slate-200">{{ number_format($boosts->lastItem()) }}</span> sur <span class="font-semibold text-slate-700 dark:text-slate-200">{{ number_format($boosts->total()) }}</span>
                            </div>
                            {{ $boosts->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-900">
                        <i class="fas fa-rocket text-2xl text-slate-300 dark:text-slate-600"></i>
                    </div>
                    <h5 class="mb-1 text-base font-semibold text-slate-900 dark:text-white">Aucun boost trouvé</h5>
                    <p class="mx-auto max-w-md text-sm text-slate-400">Aucun boost ne correspond à vos critères. Modifiez les filtres ou revenez plus tard.</p>
                    @if(request()->hasAny(['search', 'status', 'boost_type']))
                        <a href="{{ route('admin.product-boosts.index') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700">
                            <i class="fas fa-rotate-left"></i>
                            Réinitialiser les filtres
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection