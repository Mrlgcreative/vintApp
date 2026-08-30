@extends('layouts.admin')

@section('title', 'Gestion des catégories')
@section('page-title', 'Gestion des catégories')
@section('page-subtitle', 'Catalogue des catégories de la plateforme')

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
                <a href="{{ route('admin.categories.index') }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">Toutes</a>
                <a href="{{ route('admin.categories.index', ['parent' => 'null']) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">Principales</a>
                <a href="{{ route('admin.categories.index', ['has_children' => '1']) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">Avec sous-catégories</a>
                <a href="{{ route('admin.categories.index', ['status' => 'active']) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">Actives</a>
                <a href="{{ route('admin.categories.index', ['featured' => '1']) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700">En vedette</a>
            </div>
        </div>
    </div>
    <a href="{{ route('admin.categories.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-plus"></i><span class="hidden sm:inline">Nouvelle Catégorie</span><span class="sm:hidden">Ajouter</span>
    </a>
</div>
@endsection

@section('content')
<!-- Statistiques -->
<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 xl:grid-cols-4">
    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Total catégories</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($categories->total() ?? 0, 0, ',', ' ') }}</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300">
                <i class="fas fa-folder text-[10px] text-sky-500"></i>
                Catégories
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-folder text-xs text-sky-500"></i>
                Catégories enregistrées
            </div>
            <div class="text-xs text-slate-400">Sur la plateforme</div>
        </div>
    </div>

    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Actives</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($categories->where('is_active', 1)->count() ?? 0, 0, ',', ' ') }}</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400">
                <i class="fas fa-circle-check text-[10px]"></i>
                Actives
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-circle-check text-xs text-emerald-500"></i>
                Catégories visibles
            </div>
            <div class="text-xs text-slate-400">Publiées</div>
        </div>
    </div>

    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">En vedette</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($categories->where('is_featured', 1)->count() ?? 0, 0, ',', ' ') }}</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-400">
                <i class="fas fa-star text-[10px]"></i>
                Vedette
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-star text-xs text-amber-500"></i>
                Catégories mises en avant
            </div>
            <div class="text-xs text-slate-400">Sur la page d'accueil</div>
        </div>
    </div>

    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Articles total</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($categories->sum('items_count') ?? 0, 0, ',', ' ') }}</p>
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
            <div class="text-xs text-slate-400">Toutes catégories confondues</div>
        </div>
    </div>
</div>

<!-- Liste des catégories -->
<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-5 py-4 dark:border-slate-700">
        <h3 class="flex items-center gap-2 text-sm sm:text-base font-semibold text-slate-900 dark:text-white">
            <i class="fas fa-folder-tree text-primary-600"></i>
            Liste des catégories
            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                {{ $categories->total() }} total
            </span>
        </h3>
        <div class="flex items-center gap-3">
            <span class="text-xs text-slate-500 dark:text-slate-400">
                Page {{ $categories->currentPage() }}/{{ $categories->lastPage() }}
            </span>
        </div>
    </div>

    <div>
        @if(isset($categories) && $categories->count() > 0)
            <!-- Version Desktop - Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Catégorie</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Parent</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Produits</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Statut</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($categories as $category)
                        <tr class="border-t border-slate-100 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-700/30 {{ !$category->is_active ? 'opacity-60' : '' }}">
                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center gap-3">
                                    @if($category->image)
                                        <img src="{{ $category->image_url }}"
                                             class="h-12 w-12 rounded-lg object-cover border border-slate-200 shadow-sm dark:border-slate-700"
                                             alt="{{ $category->name }}">
                                    @else
                                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 text-white border border-primary-300 shadow-sm">
                                            <i class="fas fa-folder text-xl"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-semibold text-slate-900 dark:text-white">{{ $category->name }}</div>
                                        @if($category->slug)
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $category->slug }}</div>
                                        @endif
                                        @if($category->description)
                                            <div class="mt-0.5 text-xs text-slate-400">{{ Str::limit($category->description, 40) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                @if($category->parent)
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-level-up-alt text-slate-400 text-xs"></i>
                                        <span class="text-sm text-slate-700 dark:text-slate-200">{{ $category->parent->name }}</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300">
                                        <i class="fas fa-folder-open"></i>Principale
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center">
                                    <strong class="text-lg text-primary-600">{{ $category->items_count ?? 0 }}</strong>
                                    <span class="ml-1 text-xs text-slate-500 dark:text-slate-400">articles</span>
                                </div>
                                @if(($category->children_count ?? 0) > 0)
                                    <div class="mt-1 text-xs text-slate-400">
                                        <i class="fas fa-sitemap mr-1"></i>{{ $category->children_count }} sous-catégorie(s)
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="flex flex-col gap-1.5">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $category->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $category->is_active ? 'bg-emerald-500' : 'bg-slate-500' }}"></span>
                                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    @if($category->is_featured)
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300">
                                            <i class="fas fa-star text-amber-500"></i>Vedette
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="flex gap-1 items-center justify-end">
                                    <button onclick="editCategory({{ $category->id }})"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-primary-600 transition-colors hover:bg-primary-50 dark:hover:bg-primary-900/20"
                                            data-bs-toggle="tooltip"
                                            title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <div class="relative">
                                        <button class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-slate-50 dark:hover:bg-slate-700"
                                                type="button"
                                                onclick="toggleActionDropdown({{ $category->id }})">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div id="action-dropdown-{{ $category->id }}" class="absolute right-0 mt-2 hidden z-10 w-48 origin-top-right rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 dark:bg-slate-800">
                                            <div class="py-1.5">
                                                <button class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700" onclick="toggleCategoryStatus('{{ $category->id }}', {{ $category->is_active ? 'false' : 'true' }})">
                                                    <i class="fas fa-{{ $category->is_active ? 'pause' : 'play' }} mr-2 text-{{ $category->is_active ? 'amber' : 'emerald' }}-500"></i>
                                                    {{ $category->is_active ? 'Désactiver' : 'Activer' }}
                                                </button>
                                                <button class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700" onclick="toggleCategoryFeatured('{{ $category->id }}', {{ $category->is_featured ? 'false' : 'true' }})">
                                                    <i class="fas fa-star mr-2 text-amber-500"></i>
                                                    {{ $category->is_featured ? 'Retirer vedette' : 'Mettre en vedette' }}
                                                </button>
                                                <hr class="my-1 border-slate-200 dark:border-slate-700">
                                                <button class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" onclick="deleteCategory({{ $category->id }}, '{{ addslashes($category->name) }}')">
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
                @foreach($categories as $category)
                <div class="border-b border-slate-100 p-4 transition-colors hover:bg-slate-50 dark:border-slate-700/50 dark:hover:bg-slate-700/30 {{ !$category->is_active ? 'opacity-60' : '' }}">
                    <div class="flex gap-3">
                        <div class="flex-shrink-0">
                            @if($category->image)
                                <img src="{{ $category->image_url }}"
                                     class="h-16 w-16 rounded-lg object-cover border border-slate-200 shadow-sm dark:border-slate-700"
                                     alt="{{ $category->name }}">
                            @else
                                <div class="flex h-16 w-16 items-center justify-center rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 text-white border border-primary-300 shadow-sm">
                                    <i class="fas fa-folder text-2xl"></i>
                                </div>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="mb-2 flex items-start justify-between">
                                <div class="min-w-0 flex-1">
                                    <h3 class="truncate font-semibold text-slate-900 dark:text-white">{{ $category->name }}</h3>
                                    @if($category->slug)
                                        <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $category->slug }}</p>
                                    @endif
                                </div>

                                <div class="relative">
                                    <button class="p-1 text-slate-400 transition-colors hover:text-slate-600 dark:text-slate-300" onclick="toggleMobileDropdown({{ $category->id }})">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="absolute right-0 mt-2 hidden z-10 w-48 origin-top-right rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 dark:bg-slate-800"
                                         id="mobile-dropdown-{{ $category->id }}">
                                        <div class="py-1.5">
                                            <button class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700" onclick="editCategory({{ $category->id }})">
                                                <i class="fas fa-edit w-4"></i> Modifier
                                            </button>
                                            <button class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700" onclick="toggleCategoryStatus('{{ $category->id }}', {{ $category->is_active ? 'false' : 'true' }})">
                                                <i class="fas {{ $category->is_active ? 'fa-pause' : 'fa-play' }} w-4"></i>
                                                {{ $category->is_active ? 'Désactiver' : 'Activer' }}
                                            </button>
                                            <button class="w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700" onclick="toggleCategoryFeatured('{{ $category->id }}', {{ $category->is_featured ? 'false' : 'true' }})">
                                                <i class="fas fa-star w-4"></i>
                                                {{ $category->is_featured ? 'Retirer vedette' : 'Mettre en vedette' }}
                                            </button>
                                            <hr class="my-1 border-slate-200 dark:border-slate-700">
                                            <button class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" onclick="deleteCategory({{ $category->id }}, '{{ addslashes($category->name) }}')">
                                                <i class="fas fa-trash w-4"></i> Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($category->description)
                                <p class="mb-2 line-clamp-2 text-xs text-slate-400">{{ $category->description }}</p>
                            @endif

                            <div class="mb-2 flex flex-wrap gap-2">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $category->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $category->is_active ? 'bg-emerald-500' : 'bg-slate-500' }}"></span>
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>

                                @if($category->is_featured)
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300">
                                        <i class="fas fa-star text-amber-500"></i>Vedette
                                    </span>
                                @endif

                                @if(!$category->parent)
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300">
                                        <i class="fas fa-folder-open"></i>Principale
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400">
                                <span>
                                    <i class="fas fa-box text-slate-400"></i>
                                    {{ $category->items_count ?? 0 }} articles
                                </span>
                                @if(($category->children_count ?? 0) > 0)
                                    <span>
                                        <i class="fas fa-sitemap text-slate-400"></i>
                                        {{ $category->children_count }} sous-cat.
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($categories->hasPages())
                <div class="border-t border-slate-100 p-4 bg-white dark:border-slate-700 dark:bg-slate-800">
                    <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                        <div class="text-center text-xs sm:text-left sm:text-sm text-slate-500 dark:text-slate-300">
                            Affichage de <span class="font-medium text-slate-900 dark:text-white">{{ $categories->firstItem() }}</span> à <span class="font-medium text-slate-900 dark:text-white">{{ $categories->lastItem() }}</span>
                            sur <span class="font-medium text-slate-900 dark:text-white">{{ $categories->total() }}</span> résultats
                        </div>
                        <div class="w-full overflow-x-auto sm:w-auto">
                            {{ $categories->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="py-12 text-center">
                @if(request()->has('parent') || request()->has('has_children') || request()->has('status') || request()->has('featured'))
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800">
                        <i class="fas fa-search text-3xl text-slate-400"></i>
                    </div>
                    <h5 class="mb-2 text-lg font-semibold text-slate-900 dark:text-white">Aucune catégorie trouvée</h5>
                    <p class="mb-4 text-slate-500 dark:text-slate-400">Aucune catégorie ne correspond à vos critères de recherche.</p>
                    <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        <i class="fas fa-times"></i>Effacer les filtres
                    </a>
                @else
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/30">
                        <i class="fas fa-folder text-3xl text-primary-600"></i>
                    </div>
                    <h5 class="mb-2 text-lg font-semibold text-slate-900 dark:text-white">Aucune catégorie enregistrée</h5>
                    <p class="mb-4 text-slate-500 dark:text-slate-400">Commencez par ajouter des catégories pour organiser vos articles.</p>
                    <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                        <i class="fas fa-plus"></i>Ajouter une catégorie
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Modal nouvelle catégorie -->
<div id="categoryModal" class="modal-wrapper hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md rounded-xl bg-white shadow-2xl ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-700">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Nouvelle Catégorie</h3>
            <button onclick="closeModal()" class="text-slate-400 transition-colors hover:text-slate-600 dark:text-slate-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="categoryForm" class="p-5 sm:p-6">
            @csrf
            <input type="hidden" id="categoryId">
            <div class="space-y-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                        Nom <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" required
                           class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Catégorie parente</label>
                    <select id="parent_id" name="parent_id"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <option value="">Aucune (catégorie principale)</option>
                        @if(isset($categories))
                            @foreach($categories->whereNull('parent_id') as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Image</label>
                    <input type="file" id="image" name="image" accept="image/*"
                           class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Format: JPG, PNG. Taille max: 2MB</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" id="is_active" name="is_active" checked
                               class="h-4 w-4 rounded border-slate-300 dark:border-slate-600 text-primary-600 focus:ring-primary-500">
                        <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">Active</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" id="is_featured" name="is_featured"
                               class="h-4 w-4 rounded border-slate-300 dark:border-slate-600 text-primary-600 focus:ring-primary-500">
                        <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">En vedette</span>
                    </label>
                </div>
            </div>
            <div class="mt-6 flex flex-col-reverse justify-end gap-3 sm:flex-row">
                <button type="button" onclick="closeModal()"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors sm:w-auto">
                    <i class="fas fa-times"></i>Annuler
                </button>
                <button type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors sm:w-auto">
                    <i class="fas fa-save"></i>Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const allDropdowns = document.querySelectorAll('[id$="-dropdown"], [id^="action-dropdown-"], [id^="mobile-dropdown-"]');

    allDropdowns.forEach(d => {
        if (d.id !== dropdownId) {
            d.classList.add('hidden');
        }
    });

    dropdown.classList.toggle('hidden');
}

function toggleActionDropdown(categoryId) {
    toggleDropdown('action-dropdown-' + categoryId);
}

document.addEventListener('click', function(event) {
    if (!event.target.closest('button[onclick*="toggleDropdown"]') &&
        !event.target.closest('button[onclick*="toggleActionDropdown"]') &&
        !event.target.closest('button[onclick*="toggleMobileDropdown"]')) {
        const allDropdowns = document.querySelectorAll('[id$="-dropdown"], [id^="action-dropdown-"], [id^="mobile-dropdown-"]');
        allDropdowns.forEach(d => d.classList.add('hidden'));
    }
});

function toggleMobileDropdown(categoryId) {
    toggleDropdown('mobile-dropdown-' + categoryId);
}

function editCategory(id) {
    window.location.href = '{{ route("admin.categories.index") }}/' + id + '/edit';
}

function toggleCategoryStatus(id, newStatus) {
    if (confirm('Voulez-vous changer le statut de cette catégorie ?')) {
        fetch('{{ url("admin/categories") }}/' + id + '/status', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ is_active: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 800);
            } else {
                showToast(data.message || 'Une erreur est survenue', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Erreur lors de la mise à jour du statut', 'error');
        });
    }
}

function toggleCategoryFeatured(id, newStatus) {
    if (confirm('Voulez-vous modifier le statut vedette de cette catégorie ?')) {
        fetch('{{ url("admin/categories") }}/' + id + '/featured', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ is_featured: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 800);
            } else {
                showToast(data.message || 'Une erreur est survenue', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Erreur lors de la mise à jour du statut vedette', 'error');
        });
    }
}

function deleteCategory(id, name) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer la catégorie "${name}" ?\n\nCette action est irréversible.`)) {
        fetch('{{ url("admin/categories") }}/' + id, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 800);
            } else {
                showToast(data.message || 'Une erreur est survenue', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Erreur lors de la suppression', 'error');
        });
    }
}

function closeModal() {
    const modal = document.getElementById('categoryModal');
    modal.classList.add('hidden');
}
</script>
@endpush