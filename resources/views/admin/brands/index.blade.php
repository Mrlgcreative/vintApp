@extends('layouts.admin')

@section('title', 'Gestion des marques')
@section('page-title', 'Gestion des marques')

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <div class="relative">
        <button class="inline-flex items-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                type="button" onclick="toggleDropdown('filter-dropdown')">
            <i class="fas fa-filter"></i>Filtrer
            <i class="fas fa-chevron-down text-xs"></i>
        </button>
        <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-2xl shadow-lg bg-white dark:bg-slate-800 ring-1 ring-black ring-opacity-5 hidden z-10"
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
        <button class="inline-flex items-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                type="button" onclick="toggleDropdown('export-dropdown')">
            <i class="fas fa-download"></i>Export
            <i class="fas fa-chevron-down text-xs"></i>
        </button>
        <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-2xl shadow-lg bg-white dark:bg-slate-800 ring-1 ring-black ring-opacity-5 hidden z-10"
             id="export-dropdown">
            <div class="py-1.5">
                <a href="#" onclick="exportBrands('csv')" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">CSV</a>
                <a href="#" onclick="exportBrands('excel')" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">Excel</a>
                <a href="#" onclick="exportBrands('pdf')" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">PDF</a>
            </div>
        </div>
    </div>
    <a href="{{ route('admin.brands.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-plus"></i>Ajouter une marque
    </a>
</div>
@endsection

@section('content')
<!-- Messages de succès/erreur -->
@if(session('success'))
    <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 dark:border-emerald-900/30 bg-emerald-50 dark:bg-emerald-900/20 px-4 py-3 text-sm text-emerald-800 dark:text-emerald-300 animate-fade-in mb-6" role="alert">
        <i class="fas fa-check-circle text-emerald-500"></i>
        <span class="flex-1">{{ session('success') }}</span>
        <button type="button" class="text-emerald-400 hover:text-emerald-600 transition-colors" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="flex items-center gap-3 rounded-2xl border border-red-200 dark:border-red-900/30 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm text-red-800 dark:text-red-300 animate-fade-in mb-6" role="alert">
        <i class="fas fa-circle-exclamation text-red-500"></i>
        <span class="flex-1">{{ session('error') }}</span>
        <button type="button" class="text-red-400 hover:text-red-600 transition-colors" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

<!-- Barre de recherche et filtres -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm mb-6">
    <div class="p-5 sm:p-6">
        <form method="GET" action="{{ route('admin.brands.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div class="md:col-span-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-400"></i>
                    </div>
                    <input type="text"
                           class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors"
                           name="search"
                           placeholder="Rechercher une marque..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div>
                <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div>
                <select name="country" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">
                    <option value="">Tous les pays</option>
                    <option value="FR" {{ request('country') === 'FR' ? 'selected' : '' }}>France</option>
                    <option value="IT" {{ request('country') === 'IT' ? 'selected' : '' }}>Italie</option>
                    <option value="US" {{ request('country') === 'US' ? 'selected' : '' }}>États-Unis</option>
                    <option value="GB" {{ request('country') === 'GB' ? 'selected' : '' }}>Royaume-Uni</option>
                </select>
            </div>
            <div>
                <select name="sort" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">
                    <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Nom A-Z</option>
                    <option value="-name" {{ request('sort') === '-name' ? 'selected' : '' }}>Nom Z-A</option>
                    <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Plus ancien</option>
                    <option value="-created_at" {{ request('sort', '-created_at') === '-created_at' ? 'selected' : '' }}>Plus récent</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('admin.brands.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Statistiques -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6 mb-6">
    <!-- Total marques -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-primary-600 uppercase tracking-wider mb-2">Total marques</div>
                    <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ $brands->total() ?? 0 }}</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-tags text-2xl text-primary-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-primary-500 to-primary-600"></div>
    </div>

    <!-- Marques actives -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-emerald-600 uppercase tracking-wider mb-2">Marques actives</div>
                    <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ $brands->where('is_active', 1)->count() ?? 0 }}</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-2xl text-emerald-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-emerald-500 to-emerald-600"></div>
    </div>

    <!-- En vedette -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-amber-600 uppercase tracking-wider mb-2">En vedette</div>
                    <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ $brands->where('is_featured', 1)->count() ?? 0 }}</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-star text-2xl text-amber-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-amber-500 to-amber-600"></div>
    </div>

    <!-- Articles total -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-sky-600 uppercase tracking-wider mb-2">Articles total</div>
                    <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ $brands->sum('items_count') ?? 0 }}</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-sky-100 dark:bg-sky-900/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-box text-2xl text-sky-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-sky-500 to-sky-600"></div>
    </div>
</div>

<!-- Liste des marques -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h5 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Liste des marques</h5>
                @if(isset($brands))
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $brands->total() }} marque(s) trouvée(s)</p>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <label class="flex items-center cursor-pointer gap-2">
                    <input class="h-4 w-4 rounded text-primary-600 border-slate-300 dark:border-slate-600 focus:ring-primary-500" type="checkbox" id="showInactive" onchange="toggleInactive()">
                    <span class="text-xs sm:text-sm text-slate-600 dark:text-slate-300">Voir inactives</span>
                </label>
            </div>
        </div>
    </div>
    <div class="p-0">
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
                    <tbody>
                        @foreach($brands as $brand)
                        <tr data-brand-id="{{ $brand->id }}" class="border-t border-slate-100 dark:border-slate-700/50 transition-colors hover:bg-slate-50 dark:hover:bg-slate-700/30 {{ !$brand->is_active ? 'opacity-60' : '' }}">
                            <td class="px-4 py-3 align-middle">
                                <input class="brand-checkbox h-4 w-4 rounded border-slate-300 dark:border-slate-600 text-primary-600 focus:ring-primary-500" type="checkbox" value="{{ $brand->id }}">
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center gap-3">
                                    @if($brand->logo)
                                        <img src="{{ $brand->logo_url }}"
                                             class="w-12 h-12 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shadow-sm"
                                             alt="Logo {{ $brand->name }}">
                                    @else
                                        <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 text-white rounded-xl flex items-center justify-center border border-primary-300 shadow-sm">
                                            <span class="font-bold text-sm">{{ strtoupper(substr($brand->name, 0, 2)) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-semibold text-slate-900 dark:text-white">{{ $brand->name }}</div>
                                        @if($brand->slug)
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $brand->slug }}</div>
                                        @endif
                                        @if($brand->description)
                                            <div class="text-xs text-slate-400 mt-0.5">{{ Str::limit($brand->description, 40) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="flex flex-col gap-1.5">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $brand->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $brand->is_active ? 'bg-emerald-500' : 'bg-slate-500' }}"></span>
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
                                    <strong class="text-primary-600 text-lg">{{ $brand->items_count ?? 0 }}</strong>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 ml-1">articles</span>
                                </div>
                                @if($brand->items_count > 0)
                                    <div class="text-xs text-slate-400 mt-1">
                                        Dernier: {{ $brand->latest_item_date ? $brand->latest_item_date->format('d/m/Y') : 'N/A' }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="text-sm text-slate-900 dark:text-white">{{ $brand->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ $brand->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="flex gap-1 justify-end items-center">
                                    <a href="{{ route('admin.brands.show', $brand) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sky-600 hover:bg-sky-50 dark:hover:bg-sky-900/20 transition-colors"
                                       data-bs-toggle="tooltip"
                                       title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.brands.edit', $brand) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors"
                                       data-bs-toggle="tooltip"
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <div class="relative">
                                        <button class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                                                type="button"
                                                onclick="toggleActionDropdown({{ $brand->id }})">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div id="action-dropdown-{{ $brand->id }}" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-2xl shadow-lg bg-white dark:bg-slate-800 ring-1 ring-black ring-opacity-5 z-20">
                                            <div class="py-1.5">
                                                <button class="w-full text-left px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700" onclick="toggleBrandStatus({{ $brand->id }})">
                                                    <i class="fas fa-{{ $brand->is_active ? 'pause' : 'play' }} mr-2 text-{{ $brand->is_active ? 'amber' : 'emerald' }}-500"></i>
                                                    {{ $brand->is_active ? 'Désactiver' : 'Activer' }}
                                                </button>
                                                <button class="w-full text-left px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700" onclick="toggleBrandFeatured({{ $brand->id }})">
                                                    <i class="fas fa-star mr-2 text-amber-500"></i>
                                                    {{ $brand->is_featured ? 'Retirer de la vedette' : 'Mettre en vedette' }}
                                                </button>
                                                <hr class="my-1 border-slate-200 dark:border-slate-700">
                                                <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" onclick="deleteBrand({{ $brand->id }}, '{{ $brand->name }}')">
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
                <div data-brand-id="{{ $brand->id }}" class="border-b border-slate-100 dark:border-slate-700/50 p-4 transition-colors hover:bg-slate-50 dark:hover:bg-slate-700/30 {{ !$brand->is_active ? 'opacity-60' : '' }}">
                    <div class="flex gap-3">
                        <!-- Checkbox et Logo -->
                        <div class="flex flex-col items-center gap-2">
                            <input class="brand-checkbox h-4 w-4 rounded border-slate-300 dark:border-slate-600 text-primary-600 focus:ring-primary-500" type="checkbox" value="{{ $brand->id }}">
                            @if($brand->logo)
                                <img src="{{ $brand->logo_url }}"
                                     class="w-16 h-16 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shadow-sm"
                                     alt="Logo {{ $brand->name }}">
                            @else
                                <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 text-white rounded-xl flex items-center justify-center border border-primary-300 shadow-sm">
                                    <span class="font-bold">{{ strtoupper(substr($brand->name, 0, 2)) }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Contenu principal -->
                        <div class="flex-1 min-w-0">
                            <!-- En-tête avec nom et actions -->
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1 min-w-0">
                                    <h6 class="font-semibold text-slate-900 dark:text-white truncate">{{ $brand->name }}</h6>
                                    @if($brand->slug)
                                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $brand->slug }}</p>
                                    @endif
                                </div>

                                <!-- Menu dropdown actions mobile -->
                                <div class="relative">
                                    <button class="text-slate-400 hover:text-slate-600 dark:text-slate-300 p-1" onclick="toggleMobileDropdown({{ $brand->id }})">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-2xl shadow-lg bg-white dark:bg-slate-800 ring-1 ring-black ring-opacity-5 hidden z-10"
                                         id="mobile-dropdown-{{ $brand->id }}">
                                        <div class="py-1.5">
                                            <a href="{{ route('admin.brands.show', $brand) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">
                                                <i class="fas fa-eye w-4"></i> Voir
                                            </a>
                                            <a href="{{ route('admin.brands.edit', $brand) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">
                                                <i class="fas fa-edit w-4"></i> Modifier
                                            </a>
                                            <button class="w-full text-left px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700" onclick="toggleBrandStatus({{ $brand->id }})">
                                                <i class="fas {{ $brand->is_active ? 'fa-pause' : 'fa-play' }} w-4"></i>
                                                {{ $brand->is_active ? 'Désactiver' : 'Activer' }}
                                            </button>
                                            <button class="w-full text-left px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700" onclick="toggleBrandFeatured({{ $brand->id }})">
                                                <i class="fas fa-star w-4"></i>
                                                {{ $brand->is_featured ? 'Retirer vedette' : 'Mettre en vedette' }}
                                            </button>
                                            <hr class="my-1 border-slate-200 dark:border-slate-700">
                                            <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" onclick="deleteBrand({{ $brand->id }}, '{{ $brand->name }}')">
                                                <i class="fas fa-trash w-4"></i> Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            @if($brand->description)
                                <p class="text-xs text-slate-400 mb-2 line-clamp-2">{{ $brand->description }}</p>
                            @endif

                            <!-- Badges et infos -->
                            <div class="flex flex-wrap gap-2 mb-2">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $brand->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $brand->is_active ? 'bg-emerald-500' : 'bg-slate-500' }}"></span>
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

                            <!-- Statistiques -->
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
            <div class="p-4 bg-slate-50 dark:bg-slate-900 border-t border-slate-100 dark:border-slate-700" id="bulkActions" style="display: none;">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
                    <div class="text-xs sm:text-sm">
                        <span class="font-semibold text-slate-900 dark:text-white" id="selectedCount">0</span>
                        <span class="text-slate-500 dark:text-slate-300"> marque(s) sélectionnée(s)</span>
                    </div>
                    <div class="flex flex-wrap gap-2 justify-center sm:justify-end">
                        <button class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs sm:text-sm font-medium text-emerald-700 bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 hover:bg-emerald-200 dark:hover:bg-emerald-900/50 transition-colors" onclick="bulkAction('activate')">
                            <i class="fas fa-check"></i>
                            <span class="hidden sm:inline">Activer</span>
                            <span class="sm:hidden"><i class="fas fa-check"></i></span>
                        </button>
                        <button class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs sm:text-sm font-medium text-amber-700 bg-amber-100 dark:bg-amber-900/30 dark:text-amber-300 hover:bg-amber-200 dark:hover:bg-amber-900/50 transition-colors" onclick="bulkAction('deactivate')">
                            <i class="fas fa-pause"></i>
                            <span class="hidden sm:inline">Désactiver</span>
                            <span class="sm:hidden"><i class="fas fa-pause"></i></span>
                        </button>
                        <button class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs sm:text-sm font-medium text-red-700 bg-red-100 dark:bg-red-900/30 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors" onclick="bulkAction('delete')">
                            <i class="fas fa-trash"></i>
                            <span class="hidden sm:inline">Supprimer</span>
                            <span class="sm:hidden"><i class="fas fa-trash"></i></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            @if($brands->hasPages())
                <div class="p-4 bg-white dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="text-xs sm:text-sm text-slate-500 dark:text-slate-300 text-center sm:text-left">
                            Affichage de <span class="font-medium text-slate-900 dark:text-white">{{ $brands->firstItem() }}</span> à <span class="font-medium text-slate-900 dark:text-white">{{ $brands->lastItem() }}</span>
                            sur <span class="font-medium text-slate-900 dark:text-white">{{ $brands->total() }}</span> résultats
                        </div>
                        <div class="w-full sm:w-auto overflow-x-auto">
                            {{ $brands->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-12">
                @if(request()->has('search') || request()->has('status') || request()->has('country'))
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 mb-4">
                        <i class="fas fa-search text-3xl text-slate-400"></i>
                    </div>
                    <h5 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Aucune marque trouvée</h5>
                    <p class="text-slate-500 dark:text-slate-400 mb-4">Aucune marque ne correspond à vos critères de recherche.</p>
                    <a href="{{ route('admin.brands.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        <i class="fas fa-times"></i>Effacer les filtres
                    </a>
                @else
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900/30 mb-4">
                        <i class="fas fa-tags text-3xl text-primary-600"></i>
                    </div>
                    <h5 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Aucune marque enregistrée</h5>
                    <p class="text-slate-500 dark:text-slate-400 mb-4">Commencez par ajouter des marques pour organiser vos articles.</p>
                    <a href="{{ route('admin.brands.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                        <i class="fas fa-plus"></i>Ajouter une marque
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="modal-wrapper hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-800 shadow-2xl ring-1 ring-slate-200 dark:ring-slate-700 animate-pop">
        <div class="flex items-center justify-between bg-red-50 dark:bg-red-900/20 px-5 py-4 border-b border-red-100 dark:border-red-800">
            <h3 class="text-base font-semibold text-red-800 dark:text-red-200 flex items-center gap-2" id="modal-title">
                <i class="fas fa-exclamation-triangle"></i>
                Confirmer la suppression
            </h3>
            <button type="button" onclick="closeDeleteModal()" class="text-red-400 hover:text-red-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-5 sm:p-6">
            <p class="text-slate-700 dark:text-slate-200 mb-4">
                Êtes-vous sûr de vouloir supprimer la marque
                <strong class="text-slate-900 dark:text-white" id="brandNameToDelete"></strong> ?
            </p>
            <div class="rounded-xl border-l-4 border-amber-400 bg-amber-50 dark:bg-amber-900/20 p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-amber-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-amber-700 dark:text-amber-200 font-medium">
                            Cette action est <strong>irréversible</strong> et supprimera :
                        </p>
                        <ul class="mt-2 text-sm text-amber-700 dark:text-amber-200 list-disc list-inside space-y-1">
                            <li>Toutes les informations de la marque</li>
                            <li>Les associations avec les articles</li>
                            <li>L'historique et les statistiques</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-slate-50 dark:bg-slate-900 rounded-b-2xl px-5 py-4 flex flex-col-reverse sm:flex-row justify-end gap-3">
            <button type="button" onclick="closeDeleteModal()" class="inline-flex justify-center items-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors w-full sm:w-auto">
                <i class="fas fa-times"></i>Annuler
            </button>
            <form id="deleteForm" method="POST" class="w-full sm:w-auto">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex justify-center items-center gap-2 w-full rounded-xl bg-red-600 hover:bg-red-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
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
