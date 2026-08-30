@extends('layouts.admin')

@section('title', 'Gestion des marques')
@section('page-title', 'Gestion des marques')
@section('page-subtitle', 'Catalogue des marques de la plateforme')

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <div class="relative">
        <button class="inline-flex items-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                type="button" onclick="toggleDropdown('filter-dropdown')">
            <i class="fas fa-filter"></i>Filtrer
            <i class="fas fa-chevron-down text-xs"></i>
        </button>
        <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-xl shadow-lg bg-white dark:bg-slate-800 ring-1 ring-black ring-opacity-5 hidden z-10"
             id="filter-dropdown">
            <div class="py-1.5">
                <a href="{{ route('admin.brands.index') }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">Toutes</a>
                <a href="{{ route('admin.brands.index', ['status' => 'active']) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">Actives</a>
                <a href="{{ route('admin.brands.index', ['status' => 'inactive']) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">Inactives</a>
                <a href="{{ route('admin.brands.index', ['featured' => '1']) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">En vedette</a>
            </div>
        </div>
    </div>
    <div class="relative">
        <button class="inline-flex items-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                type="button" onclick="toggleDropdown('export-dropdown')">
            <i class="fas fa-download"></i>Export
            <i class="fas fa-chevron-down text-xs"></i>
        </button>
        <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-xl shadow-lg bg-white dark:bg-slate-800 ring-1 ring-black ring-opacity-5 hidden z-10"
             id="export-dropdown">
            <div class="py-1.5">
                <a href="#" onclick="exportBrands('csv')" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">CSV</a>
                <a href="#" onclick="exportBrands('excel')" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">Excel</a>
                <a href="#" onclick="exportBrands('pdf')" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">PDF</a>
            </div>
        </div>
    </div>
    <a href="{{ route('admin.brands.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-plus"></i><span class="hidden sm:inline">Ajouter une marque</span><span class="sm:hidden">Ajouter</span>
    </a>
</div>
@endsection

@section('content')
<!-- Filtres -->
<div class="mb-6 rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <div class="p-5 sm:p-6">
        <form method="GET" action="{{ route('admin.brands.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-6">
            <div class="md:col-span-2">
                <label for="search" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Recherche</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                        <i class="fas fa-search text-slate-400"></i>
                    </div>
                    <input type="text"
                           id="search"
                           class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-3.5 text-sm text-slate-900 placeholder:text-slate-400 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                           name="search"
                           placeholder="Rechercher une marque..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div>
                <label for="status" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Statut</label>
                <select name="status" id="status" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div>
                <label for="country" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Pays</label>
                <select name="country" id="country" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                    <option value="">Tous les pays</option>
                    <option value="FR" {{ request('country') === 'FR' ? 'selected' : '' }}>France</option>
                    <option value="IT" {{ request('country') === 'IT' ? 'selected' : '' }}>Italie</option>
                    <option value="US" {{ request('country') === 'US' ? 'selected' : '' }}>États-Unis</option>
                    <option value="GB" {{ request('country') === 'GB' ? 'selected' : '' }}>Royaume-Uni</option>
                </select>
            </div>
            <div>
                <label for="sort" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Tri</label>
                <select name="sort" id="sort" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                    <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Nom A-Z</option>
                    <option value="-name" {{ request('sort') === '-name' ? 'selected' : '' }}>Nom Z-A</option>
                    <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Plus ancien</option>
                    <option value="-created_at" {{ request('sort', '-created_at') === '-created_at' ? 'selected' : '' }}>Plus récent</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700">
                    <i class="fas fa-search"></i>Filtrer
                </button>
                <a href="{{ route('admin.brands.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Statistiques -->
<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 xl:grid-cols-4">
    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Total marques</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($brands->total() ?? 0, 0, ',', ' ') }}</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300">
                <i class="fas fa-tags text-[10px] text-sky-500"></i>
                Marques
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-tags text-xs text-sky-500"></i>
                Marques enregistrées
            </div>
            <div class="text-xs text-slate-400">Sur la plateforme</div>
        </div>
    </div>

    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Actives</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($brands->where('is_active', 1)->count() ?? 0, 0, ',', ' ') }}</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400">
                <i class="fas fa-circle-check text-[10px]"></i>
                Actives
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-circle-check text-xs text-emerald-500"></i>
                Marques visibles
            </div>
            <div class="text-xs text-slate-400">Publiées</div>
        </div>
    </div>

    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">En vedette</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($brands->where('is_featured', 1)->count() ?? 0, 0, ',', ' ') }}</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-400">
                <i class="fas fa-star text-[10px]"></i>
                Vedette
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-star text-xs text-amber-500"></i>
                Marques mises en avant
            </div>
            <div class="text-xs text-slate-400">Sur la page d'accueil</div>
        </div>
    </div>

    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Articles total</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($brands->sum('items_count') ?? 0, 0, ',', ' ') }}</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300">
                <i class="fas fa-box text-[10px] text-primary-500"></i>
                Articles
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-box text-xs text-primary-500"></i>
                Articles liés
            </div>
            <div class="text-xs text-slate-400">Toutes marques confondues</div>
        </div>
    </div>
</div>

<!-- Liste des marques -->
<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-5 py-4 dark:border-slate-700">
        <h3 class="flex items-center gap-2 text-sm sm:text-base font-semibold text-slate-900 dark:text-white">
            <i class="fas fa-tags text-primary-600"></i>
            Liste des marques
            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                {{ $brands->total() }} total
            </span>
        </h3>
        <div class="flex items-center gap-3">
            <span class="text-xs text-slate-500 dark:text-slate-400">
                Page {{ $brands->currentPage() }}/{{ $brands->lastPage() }}
            </span>
            <label class="flex items-center cursor-pointer gap-2">
                <input class="h-4 w-4 rounded text-primary-600 border-slate-300 dark:border-slate-600 focus:ring-primary-500" type="checkbox" id="showInactive" onchange="toggleInactive()">
                <span class="text-xs sm:text-sm text-slate-600 dark:text-slate-300">Voir inactives</span>
            </label>
        </div>
    </div>

    <div>
        @if(isset($brands) && $brands->count() > 0)
            <!-- Version Desktop - Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                <input class="brand-checkbox-select-all h-4 w-4 rounded border-slate-300 dark:border-slate-600 text-primary-600 focus:ring-primary-500" type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Marque</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Statut</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pays</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Articles</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Créée le</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($brands as $brand)
                        <tr data-brand-id="{{ $brand->id }}" class="border-t border-slate-100 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-700/30 {{ !$brand->is_active ? 'opacity-60' : '' }}">
                            <td class="px-4 py-3 align-middle">
                                <input class="brand-checkbox h-4 w-4 rounded border-slate-300 dark:border-slate-600 text-primary-600 focus:ring-primary-500" type="checkbox" value="{{ $brand->id }}">
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center gap-3">
                                    @if($brand->logo)
                                        <img src="{{ $brand->logo_url }}"
                                             class="h-12 w-12 rounded-lg object-cover border border-slate-200 shadow-sm dark:border-slate-700"
                                             alt="Logo {{ $brand->name }}">
                                    @else
                                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 text-white border border-primary-300 shadow-sm">
                                            <span class="font-bold text-sm">{{ strtoupper(substr($brand->name, 0, 2)) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-semibold text-slate-900 dark:text-white">{{ $brand->name }}</div>
                                        @if($brand->slug)
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $brand->slug }}</div>
                                        @endif
                                        @if($brand->description)
                                            <div class="mt-0.5 text-xs text-slate-400">{{ Str::limit($brand->description, 40) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="flex flex-col gap-1.5">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $brand->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $brand->is_active ? 'bg-emerald-500' : 'bg-slate-500' }}"></span>
                                        {{ $brand->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    @if($brand->is_featured)
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300">
                                            <i class="fas fa-star text-amber-500"></i>Vedette
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                @if($brand->country)
                                    <div class="flex items-center gap-2">
                                        <span class="fi fi-{{ strtolower($brand->country) }}"></span>
                                        <span class="text-sm text-slate-700 dark:text-slate-200">{{ $brand->country }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-slate-400">Non spécifié</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center">
                                    <strong class="text-lg text-primary-600">{{ $brand->items_count ?? 0 }}</strong>
                                    <span class="ml-1 text-xs text-slate-500 dark:text-slate-400">articles</span>
                                </div>
                                @if($brand->items_count > 0)
                                    <div class="mt-1 text-xs text-slate-400">
                                        Dernier : {{ $brand->latest_item_date ? $brand->latest_item_date->format('d/m/Y') : 'N/A' }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="text-sm text-slate-900 dark:text-white">{{ $brand->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ $brand->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.brands.show', $brand) }}"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-sky-600 transition-colors hover:bg-sky-50 dark:hover:bg-sky-900/20"
                                       data-bs-toggle="tooltip"
                                       title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.brands.edit', $brand) }}"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-primary-600 transition-colors hover:bg-primary-50 dark:hover:bg-primary-900/20"
                                       data-bs-toggle="tooltip"
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <div class="relative">
                                        <button class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-slate-50 dark:hover:bg-slate-700"
                                                type="button"
                                                onclick="toggleActionDropdown({{ $brand->id }})">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div id="action-dropdown-{{ $brand->id }}" class="absolute right-0 mt-2 hidden z-10 w-48 origin-top-right rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 dark:bg-slate-800">
                                            <div class="py-1.5">
                                                <button class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700" onclick="toggleBrandStatus({{ $brand->id }})">
                                                    <i class="fas fa-{{ $brand->is_active ? 'pause' : 'play' }} mr-2 text-{{ $brand->is_active ? 'amber' : 'emerald' }}-500"></i>
                                                    {{ $brand->is_active ? 'Désactiver' : 'Activer' }}
                                                </button>
                                                <button class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700" onclick="toggleBrandFeatured({{ $brand->id }})">
                                                    <i class="fas fa-star mr-2 text-amber-500"></i>
                                                    {{ $brand->is_featured ? 'Retirer de la vedette' : 'Mettre en vedette' }}
                                                </button>
                                                <hr class="my-1 border-slate-200 dark:border-slate-700">
                                                <button class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" onclick="deleteBrand({{ $brand->id }}, '{{ $brand->name }}')">
                                                    <i class="fas fa-trash mr-2"></i>Supprimer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Version Mobile/Tablet - Cartes -->
            <div class="lg:hidden">
                @foreach($brands as $brand)
                <div data-brand-id="{{ $brand->id }}" class="border-b border-slate-100 p-4 transition-colors hover:bg-slate-50 dark:border-slate-700/50 dark:hover:bg-slate-700/30 {{ !$brand->is_active ? 'opacity-60' : '' }}">
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center gap-2">
                            <input class="brand-checkbox h-4 w-4 rounded border-slate-300 dark:border-slate-600 text-primary-600 focus:ring-primary-500" type="checkbox" value="{{ $brand->id }}">
                            @if($brand->logo)
                                <img src="{{ $brand->logo_url }}"
                                     class="h-16 w-16 rounded-lg object-cover border border-slate-200 shadow-sm dark:border-slate-700"
                                     alt="Logo {{ $brand->name }}">
                            @else
                                <div class="flex h-16 w-16 items-center justify-center rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 text-white border border-primary-300 shadow-sm">
                                    <span class="font-bold">{{ strtoupper(substr($brand->name, 0, 2)) }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="mb-2 flex items-start justify-between">
                                <div class="min-w-0 flex-1">
                                    <h3 class="truncate font-semibold text-slate-900 dark:text-white">{{ $brand->name }}</h3>
                                    @if($brand->slug)
                                        <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $brand->slug }}</p>
                                    @endif
                                </div>

                                <div class="relative">
                                    <button class="p-1 text-slate-400 transition-colors hover:text-slate-600 dark:text-slate-300" onclick="toggleMobileDropdown({{ $brand->id }})">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="absolute right-0 mt-2 hidden z-10 w-48 origin-top-right rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 dark:bg-slate-800"
                                         id="mobile-dropdown-{{ $brand->id }}">
                                        <div class="py-1.5">
                                            <a href="{{ route('admin.brands.show', $brand) }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700">
                                                <i class="fas fa-eye w-4"></i> Voir
                                            </a>
                                            <a href="{{ route('admin.brands.edit', $brand) }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700">
                                                <i class="fas fa-edit w-4"></i> Modifier
                                            </a>
                                            <button class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700" onclick="toggleBrandStatus({{ $brand->id }})">
                                                <i class="fas {{ $brand->is_active ? 'fa-pause' : 'fa-play' }} w-4"></i>
                                                {{ $brand->is_active ? 'Désactiver' : 'Activer' }}
                                            </button>
                                            <button class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700" onclick="toggleBrandFeatured({{ $brand->id }})">
                                                <i class="fas fa-star w-4"></i>
                                                {{ $brand->is_featured ? 'Retirer vedette' : 'Mettre en vedette' }}
                                            </button>
                                            <hr class="my-1 border-slate-200 dark:border-slate-700">
                                            <button class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" onclick="deleteBrand({{ $brand->id }}, '{{ $brand->name }}')">
                                                <i class="fas fa-trash w-4"></i> Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($brand->description)
                                <p class="mb-2 line-clamp-2 text-xs text-slate-400">{{ $brand->description }}</p>
                            @endif

                            <div class="mb-2 flex flex-wrap gap-2">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $brand->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $brand->is_active ? 'bg-emerald-500' : 'bg-slate-500' }}"></span>
                                    {{ $brand->is_active ? 'Active' : 'Inactive' }}
                                </span>

                                @if($brand->is_featured)
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300">
                                        <i class="fas fa-star text-amber-500"></i>Vedette
                                    </span>
                                @endif

                                @if($brand->country)
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300">
                                        <span class="fi fi-{{ strtolower($brand->country) }} mr-1"></span>
                                        {{ $brand->country }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400">
                                <span>
                                    <i class="fas fa-box text-slate-400"></i>
                                    {{ $brand->items_count ?? 0 }} articles
                                </span>
                                <span>
                                    <i class="fas fa-calendar text-slate-400"></i>
                                    {{ $brand->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Actions en lot -->
            <div class="border-t border-slate-100 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900" id="bulkActions" style="display: none;">
                <div class="flex flex-col items-center justify-between gap-3 sm:flex-row">
                    <div class="text-xs sm:text-sm">
                        <span class="font-semibold text-slate-900 dark:text-white" id="selectedCount">0</span>
                        <span class="text-slate-500 dark:text-slate-300"> marque(s) sélectionnée(s)</span>
                    </div>
                    <div class="flex flex-wrap justify-center gap-2 sm:justify-end">
                        <button class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs sm:text-sm font-medium text-emerald-700 bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 hover:bg-emerald-200 dark:hover:bg-emerald-900/50 transition-colors" onclick="bulkAction('activate')">
                            <i class="fas fa-check"></i>
                            <span class="hidden sm:inline">Activer</span>
                        </button>
                        <button class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs sm:text-sm font-medium text-amber-700 bg-amber-100 dark:bg-amber-900/30 dark:text-amber-300 hover:bg-amber-200 dark:hover:bg-amber-900/50 transition-colors" onclick="bulkAction('deactivate')">
                            <i class="fas fa-pause"></i>
                            <span class="hidden sm:inline">Désactiver</span>
                        </button>
                        <button class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs sm:text-sm font-medium text-red-700 bg-red-100 dark:bg-red-900/30 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors" onclick="bulkAction('delete')">
                            <i class="fas fa-trash"></i>
                            <span class="hidden sm:inline">Supprimer</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            @if($brands->hasPages())
                <div class="border-t border-slate-100 p-4 bg-white dark:border-slate-700 dark:bg-slate-800">
                    <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                        <div class="text-center text-xs sm:text-left sm:text-sm text-slate-500 dark:text-slate-300">
                            Affichage de <span class="font-medium text-slate-900 dark:text-white">{{ $brands->firstItem() }}</span> à <span class="font-medium text-slate-900 dark:text-white">{{ $brands->lastItem() }}</span>
                            sur <span class="font-medium text-slate-900 dark:text-white">{{ $brands->total() }}</span> résultats
                        </div>
                        <div class="w-full overflow-x-auto sm:w-auto">
                            {{ $brands->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="py-12 text-center">
                @if(request()->has('search') || request()->has('status') || request()->has('country'))
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800">
                        <i class="fas fa-search text-3xl text-slate-400"></i>
                    </div>
                    <h5 class="mb-2 text-lg font-semibold text-slate-900 dark:text-white">Aucune marque trouvée</h5>
                    <p class="mb-4 text-slate-500 dark:text-slate-400">Aucune marque ne correspond à vos critères de recherche.</p>
                    <a href="{{ route('admin.brands.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        <i class="fas fa-times"></i>Effacer les filtres
                    </a>
                @else
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/30">
                        <i class="fas fa-tags text-3xl text-primary-600"></i>
                    </div>
                    <h5 class="mb-2 text-lg font-semibold text-slate-900 dark:text-white">Aucune marque enregistrée</h5>
                    <p class="mb-4 text-slate-500 dark:text-slate-400">Commencez par ajouter des marques pour organiser vos articles.</p>
                    <a href="{{ route('admin.brands.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                        <i class="fas fa-plus"></i>Ajouter une marque
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="modal-wrapper hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md rounded-xl bg-white shadow-2xl ring-1 ring-slate-200 animate-pop dark:bg-slate-800 dark:ring-slate-700">
        <div class="flex items-center justify-between border-b border-red-100 bg-red-50 px-5 py-4 dark:border-red-800 dark:bg-red-900/20">
            <h3 class="flex items-center gap-2 text-base font-semibold text-red-800 dark:text-red-200" id="modal-title">
                <i class="fas fa-exclamation-triangle"></i>
                Confirmer la suppression
            </h3>
            <button type="button" onclick="closeDeleteModal()" class="text-red-400 transition-colors hover:text-red-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-5 sm:p-6">
            <p class="mb-4 text-slate-700 dark:text-slate-200">
                Êtes-vous sûr de vouloir supprimer la marque
                <strong class="text-slate-900 dark:text-white" id="brandNameToDelete"></strong> ?
            </p>
            <div class="rounded-lg border-l-4 border-amber-400 bg-amber-50 p-4 dark:bg-amber-900/20">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-amber-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-amber-700 dark:text-amber-200">
                            Cette action est <strong>irréversible</strong> et supprimera :
                        </p>
                        <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-amber-700 dark:text-amber-200">
                            <li>Toutes les informations de la marque</li>
                            <li>Les associations avec les articles</li>
                            <li>L'historique et les statistiques</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col-reverse justify-end gap-3 rounded-b-xl bg-slate-50 px-5 py-4 sm:flex-row dark:bg-slate-900">
            <button type="button" onclick="closeDeleteModal()" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors sm:w-auto">
                <i class="fas fa-times"></i>Annuler
            </button>
            <form id="deleteForm" method="POST" class="w-full sm:w-auto">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-red-600 hover:bg-red-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                    <i class="fas fa-trash"></i>Supprimer définitivement
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Gestion des dropdowns
function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const allDropdowns = document.querySelectorAll('[id$="-dropdown"]');

    // Fermer tous les autres dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== dropdownId) {
            d.classList.add('hidden');
        }
    });

    // Toggle le dropdown actuel
    dropdown.classList.toggle('hidden');
}

// Gestion des dropdowns d'action dans le tableau
function toggleActionDropdown(brandId) {
    const dropdown = document.getElementById(`action-dropdown-${brandId}`);
    const allDropdowns = document.querySelectorAll('[id^="action-dropdown-"]');

    // Fermer tous les autres dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== `action-dropdown-${brandId}`) {
            d.classList.add('hidden');
        }
    });

    // Toggle le dropdown actuel
    dropdown.classList.toggle('hidden');
}

// Fermer les dropdowns en cliquant en dehors
document.addEventListener('click', function(event) {
    if (!event.target.closest('button[onclick*="toggleDropdown"]') && !event.target.closest('button[onclick*="toggleActionDropdown"]')) {
        const allDropdowns = document.querySelectorAll('[id$="-dropdown"], [id^="action-dropdown-"]');
        allDropdowns.forEach(d => d.classList.add('hidden'));
    }
});

// Initialiser les tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Gestion de la sélection multiple
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.brand-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });

    updateBulkActions();
}

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.brand-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');

    if (checkboxes.length > 0) {
        bulkActions.style.display = 'block';
        selectedCount.textContent = checkboxes.length;
    } else {
        bulkActions.style.display = 'none';
    }
}

// Écouteurs pour les checkboxes
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('brand-checkbox')) {
        updateBulkActions();
    }
});

// Changer le statut d'une marque
function toggleBrandStatus(brandId) {
    const row = document.querySelector(`tr[data-brand-id="${brandId}"]`) || document.querySelector(`div[data-brand-id="${brandId}"]`);
    const isCurrentlyInactive = row ? row.classList.contains('opacity-60') : false;

    fetch(`/admin/brands/${brandId}/status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ is_active: isCurrentlyInactive })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de la modification du statut');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la modification du statut');
    });
}

// Mettre en vedette / retirer de la vedette
function toggleBrandFeatured(brandId) {
    // Logique similaire à toggleBrandStatus mais pour is_featured
    alert('Fonctionnalité à implémenter : toggle featured');
}

// Supprimer une marque
function deleteBrand(brandId, brandName) {
    document.getElementById('brandNameToDelete').textContent = brandName;
    document.getElementById('deleteForm').action = `/admin/brands/${brandId}`;

    // Ouvrir le modal
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Empêcher le scroll
}

// Fermer le modal de suppression
function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
    document.body.style.overflow = ''; // Réactiver le scroll
}

// Fermer le modal avec la touche Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDeleteModal();
    }
});

// Actions en lot
function bulkAction(action) {
    const selectedBrands = Array.from(document.querySelectorAll('.brand-checkbox:checked')).map(cb => cb.value);

    if (selectedBrands.length === 0) {
        alert('Veuillez sélectionner au moins une marque.');
        return;
    }

    let confirmMessage = '';
    switch(action) {
        case 'activate':
            confirmMessage = `Activer ${selectedBrands.length} marque(s) ?`;
            break;
        case 'deactivate':
            confirmMessage = `Désactiver ${selectedBrands.length} marque(s) ?`;
            break;
        case 'delete':
            confirmMessage = `Supprimer définitivement ${selectedBrands.length} marque(s) ?`;
            break;
    }

    if (confirm(confirmMessage)) {
        // Implémentation des actions en lot
        console.log(`${action} brands:`, selectedBrands);
        alert(`Fonctionnalité à implémenter : ${action}`);
    }
}

// Afficher/masquer les marques inactives
function toggleInactive() {
    const showInactive = document.getElementById('showInactive').checked;
    const inactiveRows = document.querySelectorAll('[data-brand-id].opacity-60');

    inactiveRows.forEach(row => {
        row.style.display = showInactive ? '' : 'none';
    });
}

// Export des données
function exportBrands(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('export', format);

    window.location.href = `{{ route('admin.brands.index') }}?${params.toString()}`;
}

// Gestion des dropdowns mobile
function toggleMobileDropdown(brandId) {
    const dropdown = document.getElementById(`mobile-dropdown-${brandId}`);
    const allDropdowns = document.querySelectorAll('[id^="mobile-dropdown-"]');

    // Fermer tous les autres dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== `mobile-dropdown-${brandId}`) {
            d.classList.add('hidden');
        }
    });

    // Toggle le dropdown actuel
    dropdown.classList.toggle('hidden');
}

// Fermer les dropdowns en cliquant en dehors
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick^="toggleMobileDropdown"]') && !event.target.closest('[id^="mobile-dropdown-"]')) {
        const allDropdowns = document.querySelectorAll('[id^="mobile-dropdown-"]');
        allDropdowns.forEach(d => d.classList.add('hidden'));
    }
});

// Améliorer la gestion des checkboxes sur mobile
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.brand-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
});
</script>
@endpush