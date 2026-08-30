@extends('layouts.admin')

@section('title', 'Gestion des articles')
@section('page-title', 'Articles')

@section('page-actions')
<div class="flex flex-wrap gap-3">
    <a href="{{ route('admin.items.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-3 py-2 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-plus"></i>Nouvel article
    </a>
</div>
@endsection

@section('content')
@if(session('success'))
    <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 animate-fade-in dark:border-emerald-900/30 dark:bg-emerald-900/20 dark:text-emerald-300" role="alert">
        <i class="fas fa-circle-check text-emerald-500"></i>
        <span class="flex-1">{{ session('success') }}</span>
        <button type="button" class="text-emerald-400 transition-colors hover:text-emerald-600" onclick="this.parentElement.remove()"><i class="fas fa-xmark"></i></button>
    </div>
@endif
@if(session('error'))
    <div class="mb-6 flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 animate-fade-in dark:border-red-900/30 dark:bg-red-900/20 dark:text-red-300" role="alert">
        <i class="fas fa-circle-exclamation text-red-500"></i>
        <span class="flex-1">{{ session('error') }}</span>
        <button type="button" class="text-red-400 transition-colors hover:text-red-600" onclick="this.parentElement.remove()"><i class="fas fa-xmark"></i></button>
    </div>
@endif

<div class="mb-6 rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <div class="p-5 sm:p-6">
        <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-6">
            <div class="md:col-span-2">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                        <i class="fas fa-search text-slate-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 pl-10 text-sm text-slate-900 placeholder:text-slate-400 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                           placeholder="Rechercher un article...">
                </div>
            </div>
            <div>
                <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="sold" {{ request('status') === 'sold' ? 'selected' : '' }}>Vendu</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            <div>
                <select name="moderation" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                    <option value="">Tous</option>
                    <option value="blocked" {{ request('moderation') === 'blocked' ? 'selected' : '' }}>Bloqués</option>
                    <option value="suspended" {{ request('moderation') === 'suspended' ? 'selected' : '' }}>Suspendus</option>
                    <option value="normal" {{ request('moderation') === 'normal' ? 'selected' : '' }}>Normaux</option>
                </select>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row sm:space-x-2">
                <button type="submit" class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('admin.items.index') }}" class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 xl:grid-cols-5">
    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Total</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ $stats['total'] ?? $items->total() }}</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300">
                <i class="fas fa-boxes-stacked text-[10px] text-primary-500"></i>
                Catalogue
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-box text-xs text-primary-500"></i>
                Tous les articles
            </div>
            <div class="text-xs text-slate-400">Sur toute la plateforme</div>
        </div>
    </div>

    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Actifs</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ $stats['active'] ?? \App\Models\Item::where('status', 'active')->count() }}</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-white px-2 py-0.5 text-xs font-medium text-emerald-700 dark:border-emerald-800/40 dark:bg-slate-900 dark:text-emerald-300">
                <i class="fas fa-circle-check text-[10px]"></i>
                En vente
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-circle-check text-xs text-emerald-500"></i>
                Actuellement en vente
            </div>
            <div class="text-xs text-slate-400">Statut actif</div>
        </div>
    </div>

    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">En attente</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ $stats['pending'] ?? \App\Models\Item::where('status', 'pending')->count() }}</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-amber-200 bg-white px-2 py-0.5 text-xs font-medium text-amber-700 dark:border-amber-800/40 dark:bg-slate-900 dark:text-amber-300">
                <i class="fas fa-clock text-[10px]"></i>
                Modération
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-hourglass-half text-xs text-amber-500"></i>
                En attente de validation
            </div>
            <div class="text-xs text-slate-400">À vérifier</div>
        </div>
    </div>

    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Bloqués</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ $stats['blocked'] ?? \App\Models\Item::where('is_blocked', true)->count() }}</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-white px-2 py-0.5 text-xs font-medium text-red-700 dark:border-red-800/40 dark:bg-slate-900 dark:text-red-300">
                <i class="fas fa-ban text-[10px]"></i>
                Interdits
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-ban text-xs text-red-500"></i>
                Bloqués par l'admin
            </div>
            <div class="text-xs text-slate-400">Retirés de la vente</div>
        </div>
    </div>

    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Suspendus</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ $stats['suspended'] ?? \App\Models\Item::where('is_suspended', true)->count() }}</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-orange-200 bg-white px-2 py-0.5 text-xs font-medium text-orange-700 dark:border-orange-800/40 dark:bg-slate-900 dark:text-orange-300">
                <i class="fas fa-pause-circle text-[10px]"></i>
                Pausés
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-pause-circle text-xs text-orange-500"></i>
                Suspendus temporairement
            </div>
            <div class="text-xs text-slate-400">En pause</div>
        </div>
    </div>
</div>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-700">
        <h5 class="font-semibold text-slate-900 dark:text-white">Liste des articles</h5>
        <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-slate-50 px-2.5 py-0.5 text-xs font-medium text-slate-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-400">
            <i class="fas fa-box text-[10px]"></i>
            {{ number_format($items->total()) }} article(s)
        </span>
    </div>
    <div>
        @if($items->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Article</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Vendeur</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Prix</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Modération</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Vues</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Date</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    @foreach($items as $item)
                    <tr class="border-t border-slate-100 transition-colors hover:bg-slate-50 dark:border-slate-700/50 dark:hover:bg-slate-700/30 {{ $item->is_blocked ? 'bg-red-50 dark:bg-red-900/10' : ($item->is_suspended ? 'bg-orange-50 dark:bg-orange-900/10' : '') }}">
                        <td class="px-4 py-3 align-middle">
                            <div class="flex items-center gap-3">
                                @if($item->images && count($item->images) > 0)
                                    <img src="{{ asset('storage/' . $item->images[0]) }}" alt="" class="h-11 w-11 flex-shrink-0 rounded-lg object-cover ring-1 ring-slate-200 dark:ring-slate-600">
                                @else
                                    <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-lg bg-slate-100 ring-1 ring-slate-200 dark:bg-slate-700 dark:ring-slate-600">
                                        <i class="fas fa-image text-base text-slate-400"></i>
                                    </div>
                                @endif
                                <div>
                                    <a href="{{ route('admin.items.show', $item) }}" class="font-medium text-slate-900 hover:text-primary-600 dark:text-white">
                                        {{ Str::limit($item->name, 40) }}
                                    </a>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ $item->category?->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 align-middle">
                            <a href="{{ route('admin.users.show', $item->user) }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400">
                                {{ $item->user?->name ?? 'N/A' }}
                            </a>
                        </td>
                        <td class="px-4 py-3 align-middle">
                            <div class="text-sm font-semibold text-slate-900 dark:text-white">{{ $item->formatted_price ?? ($item->currency === 'USD' ? '$' : '') . number_format($item->price, 2) . ($item->currency !== 'USD' ? ' FC' : '') }}</div>
                        </td>
                        <td class="px-4 py-3 align-middle">
                            @php
                                $statusClass = match($item->status) {
                                    'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300',
                                    'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300',
                                    'sold' => 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300',
                                    'inactive' => 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300',
                                    default => 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $statusClass }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 align-middle">
                            @if($item->is_blocked)
                                <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-900/30 dark:text-red-300">
                                    <i class="fas fa-ban mr-1"></i>Bloqué
                                </span>
                            @elseif($item->is_suspended)
                                <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300">
                                    <i class="fas fa-pause-circle mr-1"></i>Suspendu
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">
                                    <i class="fas fa-check mr-1"></i>Normal
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 align-middle text-sm text-slate-500 dark:text-slate-400">{{ number_format($item->views ?? 0) }}</td>
                        <td class="px-4 py-3 align-middle text-sm text-slate-500 dark:text-slate-400">{{ $item->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 align-middle text-right">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('admin.items.show', $item) }}"
                                   class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-sky-600 transition-colors hover:bg-sky-50 dark:text-sky-400 dark:hover:bg-sky-900/20" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.items.edit', $item) }}"
                                   class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-primary-600 transition-colors hover:bg-primary-50 dark:text-primary-400 dark:hover:bg-primary-900/20" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
        <div class="border-t border-slate-100 px-5 py-4 dark:border-slate-700">
            <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                <div class="text-xs text-slate-600 dark:text-slate-400 sm:text-sm">
                    Affichage de {{ $items->firstItem() }} à {{ $items->lastItem() }} sur {{ $items->total() }}
                </div>
                {{ $items->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
        @else
        <div class="text-center py-12">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700">
                <i class="fas fa-box text-3xl text-slate-400"></i>
            </div>
            <h5 class="mb-2 text-lg font-semibold text-slate-900 dark:text-white">Aucun article</h5>
            <p class="text-slate-500 dark:text-slate-400">Aucun article trouvé.</p>
        </div>
        @endif
    </div>
</div>
@endsection
