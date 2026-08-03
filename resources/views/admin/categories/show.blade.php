@extends('layouts.admin')

@section('title', 'Détails de la catégorie')
@section('page-title', $category->name)

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.categories.index') }}"
       class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
        <i class="fas fa-arrow-left"></i>Retour à la liste
    </a>
    <a href="{{ route('admin.categories.edit', $category) }}"
       class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-edit"></i>Modifier
    </a>
    <button type="button" onclick="toggleStatus()"
            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
        <i class="fas fa-{{ $category->is_active ? 'pause' : 'play' }}"></i>
        {{ $category->is_active ? 'Désactiver' : 'Activer' }}
    </button>
    <button type="button" onclick="toggleFeatured()"
            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
        <i class="fas fa-{{ $category->is_featured ? 'star-half-alt' : 'star' }}"></i>
        {{ $category->is_featured ? 'Retirer de la une' : 'Mettre en une' }}
    </button>
    <a href="{{ route('admin.categories.create', ['parent' => $category->id]) }}"
       class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
        <i class="fas fa-plus"></i>Sous-catégorie
    </a>
    <button type="button" onclick="confirmDelete()"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-trash"></i>Supprimer
    </button>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Colonne principale -->
    <div class="space-y-6 lg:col-span-2">
        <!-- Informations principales -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
            <div class="p-5 sm:p-6">
                <div class="flex flex-col gap-6 md:flex-row">
                    <div class="flex-1">
                        <div class="flex items-center gap-4 mb-4">
                            @if($category->icon)
                                <i class="{{ $category->icon }}" style="color: {{ $category->color ?? '#7c3aed' }}; font-size: 2rem;"></i>
                            @endif
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $category->name }}</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $category->slug }}</p>
                            </div>
                        </div>

                        @if($category->description)
                            <div class="mb-6">
                                <h6 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Description</h6>
                                <p class="text-sm text-slate-700 dark:text-slate-200">{{ $category->description }}</p>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @if($category->parent)
                                <div>
                                    <h6 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Catégorie parente</h6>
                                    <a href="{{ route('admin.categories.show', $category->parent) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400">
                                        <i class="{{ $category->parent->icon ?? 'fas fa-folder' }} mr-2"></i>
                                        {{ $category->parent->name }}
                                    </a>
                                </div>
                            @endif

                            @if($category->color)
                                <div>
                                    <h6 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Couleur</h6>
                                    <div class="flex items-center">
                                        <span class="mr-2 inline-block h-5 w-5 rounded" style="background-color: {{ $category->color }}"></span>
                                        <span class="text-sm text-slate-700 dark:text-slate-200">{{ $category->color }}</span>
                                    </div>
                                </div>
                            @endif

                            <div>
                                <h6 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Ordre d'affichage</h6>
                                <p class="text-sm text-slate-700 dark:text-slate-200">{{ $category->sort_order }}</p>
                            </div>

                            <div>
                                <h6 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Statut</h6>
                                <div class="flex flex-wrap gap-2">
                                    @if($category->is_active)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">Active</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-300">Inactive</span>
                                    @endif

                                    @if($category->is_featured)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300">En vedette</span>
                                    @endif

                                    @if($category->show_in_menu)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300">Affichage menu</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($category->meta_title || $category->meta_description || $category->meta_keywords)
                            <div class="mt-6 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                                <h6 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3">SEO et Meta données</h6>
                                <div class="space-y-2 text-sm">
                                    @if($category->meta_title)
                                        <div class="text-slate-700 dark:text-slate-200"><strong>Titre meta:</strong> {{ $category->meta_title }}</div>
                                    @endif
                                    @if($category->meta_description)
                                        <div class="text-slate-700 dark:text-slate-200"><strong>Description meta:</strong> {{ $category->meta_description }}</div>
                                    @endif
                                    @if($category->meta_keywords)
                                        <div class="text-slate-700 dark:text-slate-200"><strong>Mots-clés:</strong> {{ $category->meta_keywords }}</div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="shrink-0">
                        @if($category->image)
                            <img src="{{ $category->image_url }}" class="rounded-xl border border-slate-200 dark:border-slate-700 object-cover" style="max-width: 200px;" alt="Image {{ $category->name }}">
                        @else
                            <div class="flex h-48 w-44 items-center justify-center rounded-xl border border-dashed border-slate-300 dark:border-slate-600" style="background-color: {{ $category->color ?? '#f1f5f9' }}20;">
                                <div class="text-center" style="color: {{ $category->color ?? '#64748b' }};">
                                    <i class="{{ $category->icon ?? 'fas fa-image' }} text-4xl mb-2 block"></i>
                                    <p class="text-sm">{{ $category->name }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sous-catégories -->
        @if($category->children && $category->children->count() > 0)
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                    <h5 class="text-base font-semibold text-slate-900 dark:text-white">Sous-catégories</h5>
                    <a href="{{ route('admin.categories.create', ['parent' => $category->id]) }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-3 py-1.5 text-xs font-medium text-white transition-colors">
                        <i class="fas fa-plus"></i>Ajouter une sous-catégorie
                    </a>
                </div>
                <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2">
                    @foreach($category->children as $child)
                        <div class="flex items-center gap-3 rounded-xl border border-slate-200 dark:border-slate-700 p-4 transition-colors hover:border-primary-300 dark:hover:border-primary-700">
                            @if($child->icon)
                                <i class="{{ $child->icon }}" style="color: {{ $child->color ?? '#7c3aed' }}; font-size: 1.5rem;"></i>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h6 class="font-semibold text-slate-900 dark:text-white truncate">{{ $child->name }}</h6>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $child->items_count ?? 0 }} article(s)</p>
                            </div>
                            <a href="{{ route('admin.categories.show', $child) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sky-600 hover:bg-sky-50 dark:hover:bg-sky-900/20 transition-colors" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Articles de la catégorie -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                <h5 class="text-base font-semibold text-slate-900 dark:text-white">Articles de la catégorie</h5>
                <a href="{{ route('admin.items.index', ['category' => $category->id]) }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 px-3 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    Voir tous les articles
                </a>
            </div>
            @if($category->items && $category->items->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Article</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Marque</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Prix</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Statut</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Créé le</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->items->take(10) as $item)
                                <tr class="border-t border-slate-100 dark:border-slate-700/50 transition-colors hover:bg-slate-50 dark:hover:bg-slate-700/30">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            @if($item->image)
                                                <img src="{{ $item->image_url }}" class="h-10 w-10 rounded-lg object-cover" alt="{{ $item->title }}">
                                            @endif
                                            <div class="min-w-0">
                                                <div class="font-semibold text-slate-900 dark:text-white truncate">{{ $item->title }}</div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ Str::limit($item->description, 50) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $item->brand->name ?? 'Sans marque' }}</td>
                                    <td class="px-4 py-3 font-medium tabular-nums text-slate-900 dark:text-white">{{ number_format($item->price, 2) }} €</td>
                                    <td class="px-4 py-3">
                                        @if($item->status === 'active')
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">Active</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300">{{ ucfirst($item->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $item->created_at->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('admin.items.show', $item) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sky-600 hover:bg-sky-50 dark:hover:bg-sky-900/20 transition-colors" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-12 text-center">
                    <i class="fas fa-box text-4xl text-slate-200 dark:text-slate-600 mb-3 block"></i>
                    <p class="text-slate-400 mb-4">Aucun article dans cette catégorie</p>
                    <a href="{{ route('admin.items.create', ['category' => $category->id]) }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                        <i class="fas fa-plus"></i>Ajouter un article
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Sidebar avec statistiques -->
    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
            <div class="border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                <h5 class="text-base font-semibold text-slate-900 dark:text-white">Statistiques</h5>
            </div>
            <div class="grid grid-cols-2 gap-4 p-5">
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $category->items_count ?? 0 }}</div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Articles</p>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $category->children_count ?? 0 }}</div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Sous-catégories</p>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-sky-600 dark:text-sky-400">{{ $category->active_items_count ?? 0 }}</div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Articles actifs</p>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $category->views_count ?? 0 }}</div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Vues</p>
                </div>
            </div>
        </div>

        <!-- Hiérarchie des catégories -->
        @if($category->parent || $category->children->count() > 0)
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
                <div class="border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                    <h5 class="text-base font-semibold text-slate-900 dark:text-white">Hiérarchie</h5>
                </div>
                <div class="p-5">
                    @if($category->parent)
                        <div class="mb-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Parent</p>
                            <a href="{{ route('admin.categories.show', $category->parent) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400">
                                <i class="{{ $category->parent->icon ?? 'fas fa-folder' }} mr-2"></i>
                                {{ $category->parent->name }}
                            </a>
                        </div>
                    @endif

                    @if($category->children->count() > 0)
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Enfants</p>
                            <div class="space-y-1">
                                @foreach($category->children as $child)
                                    <div>
                                        <a href="{{ route('admin.categories.show', $child) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400">
                                            <i class="{{ $child->icon ?? 'fas fa-folder' }} mr-2"></i>
                                            {{ $child->name }}
                                        </a>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">({{ $child->items_count ?? 0 }})</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
            <div class="border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                <h5 class="text-base font-semibold text-slate-900 dark:text-white">Informations système</h5>
            </div>
            <div class="p-5 space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="font-medium text-slate-500 dark:text-slate-400">ID</span>
                    <span class="text-slate-900 dark:text-white">{{ $category->id }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="font-medium text-slate-500 dark:text-slate-400">Créée le</span>
                    <span class="text-slate-900 dark:text-white">{{ $category->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="font-medium text-slate-500 dark:text-slate-400">Modifiée le</span>
                    <span class="text-slate-900 dark:text-white">{{ $category->updated_at->format('d/m/Y H:i') }}</span>
                </div>
                @if($category->created_by)
                    <div class="flex justify-between text-sm">
                        <span class="font-medium text-slate-500 dark:text-slate-400">Créée par</span>
                        <span class="text-slate-900 dark:text-white">{{ $category->creator->name ?? 'Inconnu' }}</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
            <div class="border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                <h5 class="text-base font-semibold text-slate-900 dark:text-white">Actions rapides</h5>
            </div>
            <div class="p-5 space-y-3">
                <a href="{{ route('admin.items.create', ['category' => $category->id]) }}"
                   class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    <i class="fas fa-plus"></i>Ajouter un article
                </a>
                <a href="{{ route('admin.categories.create', ['parent' => $category->id]) }}"
                   class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    <i class="fas fa-plus"></i>Ajouter une sous-catégorie
                </a>
                <a href="{{ route('admin.categories.edit', $category) }}"
                   class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    <i class="fas fa-edit"></i>Modifier la catégorie
                </a>
                <button type="button" onclick="exportCategoryData()"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    <i class="fas fa-download"></i>Exporter les données
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="modal-wrapper hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-800 shadow-2xl ring-1 ring-slate-200 dark:ring-slate-700 animate-pop">
        <div class="flex items-center justify-between bg-red-50 dark:bg-red-900/20 px-5 py-4 border-b border-red-100 dark:border-red-800">
            <h3 class="text-base font-semibold text-red-800 dark:text-red-200 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i>Confirmer la suppression
            </h3>
            <button type="button" onclick="closeDeleteModal()" class="text-red-400 hover:text-red-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-5 sm:p-6">
            <p class="text-slate-700 dark:text-slate-200 mb-4">
                Êtes-vous sûr de vouloir supprimer la catégorie
                <strong class="text-slate-900 dark:text-white">{{ $category->name }}</strong> ?
            </p>

            @if($category->items_count > 0)
                <div class="rounded-xl border-l-4 border-amber-400 bg-amber-50 dark:bg-amber-900/20 p-4 mb-3">
                    <p class="text-sm text-amber-700 dark:text-amber-200">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Cette catégorie contient {{ $category->items_count }} article(s).
                    </p>
                </div>
            @endif

            @if($category->children_count > 0)
                <div class="rounded-xl border-l-4 border-amber-400 bg-amber-50 dark:bg-amber-900/20 p-4 mb-3">
                    <p class="text-sm text-amber-700 dark:text-amber-200">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Cette catégorie contient {{ $category->children_count }} sous-catégorie(s).
                    </p>
                </div>
            @endif

            <p class="text-sm font-medium text-red-600 dark:text-red-400">Cette action est irréversible.</p>
        </div>

        <div class="bg-slate-50 dark:bg-slate-900 rounded-b-2xl px-5 py-4 flex flex-col-reverse sm:flex-row justify-end gap-3">
            <button type="button" onclick="closeDeleteModal()" class="inline-flex justify-center items-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors w-full sm:w-auto">
                <i class="fas fa-times"></i>Annuler
            </button>
            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="w-full sm:w-auto">
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
function confirmDelete() {
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

function toggleStatus() {
    fetch(`/admin/categories/{{ $category->id }}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de la modification du statut');
        }
    });
}

function toggleFeatured() {
    fetch(`/admin/categories/{{ $category->id }}/toggle-featured`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de la modification du statut vedette');
        }
    });
}

function exportCategoryData() {
    window.location.href = `/admin/categories/{{ $category->id }}/export`;
}
</script>
@endpush
