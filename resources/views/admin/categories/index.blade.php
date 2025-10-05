@extends('layouts.admin')

@section('title', 'Gestion des catégories')
@section('page-title', 'Gestion des catégories')

@section('page-actions')
<div class="d-flex gap-2">
    <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="fas fa-filter me-2"></i>Filtrer
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('admin.categories.index') }}">Toutes</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.categories.index', ['parent' => 'null']) }}">Principales</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.categories.index', ['has_children' => '1']) }}">Avec sous-catégories</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.categories.index', ['status' => 'active']) }}">Actives</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.categories.index', ['featured' => '1']) }}">En vedette</a></li>
        </ul>
    </div>
    <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="fas fa-sort me-2"></i>Trier
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('admin.categories.index', ['sort' => 'name']) }}">Nom A-Z</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.categories.index', ['sort' => '-name']) }}">Nom Z-A</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.categories.index', ['sort' => 'sort_order']) }}">Ordre</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.categories.index', ['sort' => '-created_at']) }}">Plus récentes</a></li>
        </ul>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Ajouter une catégorie
    </a>
</div>
@endsection

@section('content')
<!-- Messages de succès/erreur -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Barre de recherche et filtres -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" 
                           class="form-control" 
                           name="search" 
                           placeholder="Rechercher une catégorie..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="parent" class="form-select">
                    <option value="">Toutes les catégories</option>
                    <option value="null" {{ request('parent') === 'null' ? 'selected' : '' }}>Principales seulement</option>
                    <option value="has_parent" {{ request('parent') === 'has_parent' ? 'selected' : '' }}>Sous-catégories</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="sort" class="form-select">
                    <option value="sort_order" {{ request('sort', 'sort_order') === 'sort_order' ? 'selected' : '' }}>Ordre</option>
                    <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Nom A-Z</option>
                    <option value="-name" {{ request('sort') === '-name' ? 'selected' : '' }}>Nom Z-A</option>
                    <option value="-created_at" {{ request('sort') === '-created_at' ? 'selected' : '' }}>Plus récente</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i>
                    </button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statistiques -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="text-primary mb-1">{{ $categories->total() ?? 0 }}</h3>
                        <p class="text-muted mb-0 small">Total catégories</p>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                        <i class="fas fa-layer-group text-primary fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="text-success mb-1">{{ $categories->where('parent_id', null)->count() ?? 0 }}</h3>
                        <p class="text-muted mb-0 small">Principales</p>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded">
                        <i class="fas fa-sitemap text-success fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="text-warning mb-1">{{ $categories->where('is_featured', 1)->count() ?? 0 }}</h3>
                        <p class="text-muted mb-0 small">En vedette</p>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded">
                        <i class="fas fa-star text-warning fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="text-info mb-1">{{ $categories->sum('items_count') ?? 0 }}</h3>
                        <p class="text-muted mb-0 small">Articles total</p>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded">
                        <i class="fas fa-box text-info fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Liste des catégories -->
<div class="card shadow-sm">
    <div class="card-header bg-white border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-0">Structure hiérarchique</h5>
                @if(isset($categories))
                    <small class="text-muted">{{ $categories->total() }} catégorie(s) trouvée(s)</small>
                @endif
            </div>
            <div class="d-flex gap-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="expandAll" onchange="toggleExpandAll()">
                    <label class="form-check-label small" for="expandAll">
                        Développer tout
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="showInactive" onchange="toggleInactive()">
                    <label class="form-check-label small" for="showInactive">
                        Voir inactives
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        @if(isset($categories) && $categories->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </div>
                            </th>
                            <th class="border-0">Catégorie</th>
                            <th class="border-0">Hiérarchie</th>
                            <th class="border-0">Statut</th>
                            <th class="border-0">Articles</th>
                            <th class="border-0">Ordre</th>
                            <th class="border-0">Créée le</th>
                            <th class="border-0 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="categoriesTable">
                        @foreach($categories as $category)
                        <tr data-category-id="{{ $category->id }}" 
                            data-parent-id="{{ $category->parent_id }}"
                            class="{{ !$category->is_active ? 'table-secondary' : '' }}">
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input category-checkbox" type="checkbox" value="{{ $category->id }}">
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($category->parent_id)
                                        <div class="me-3" style="width: 20px;">
                                            <i class="fas fa-level-up-alt text-muted fa-rotate-90"></i>
                                        </div>
                                    @endif
                                    
                                    @if($category->image)
                                        <img src="{{ $category->image_url }}" 
                                             class="rounded me-3 border" width="40" height="40" style="object-fit: cover;"
                                             alt="Image {{ $category->name }}">
                                    @elseif($category->icon)
                                        <div class="me-3 text-center" style="width: 40px;">
                                            <i class="{{ $category->icon }} fa-lg" style="color: {{ $category->color ?? '#6c757d' }}"></i>
                                        </div>
                                    @else
                                        <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center me-3" 
                                             style="width: 40px; height: 40px; background-color: {{ $category->color ?? '#6c757d' }} !important;">
                                            <i class="fas fa-tag"></i>
                                        </div>
                                    @endif
                                    
                                    <div>
                                        <div class="fw-semibold">{{ $category->name }}</div>
                                        @if($category->slug)
                                            <small class="text-muted">{{ $category->slug }}</small>
                                        @endif
                                        @if($category->description)
                                            <small class="text-muted d-block">{{ Str::limit($category->description, 40) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($category->parent)
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-light text-dark border">{{ $category->parent->name }}</span>
                                        <i class="fas fa-arrow-right mx-2 text-muted small"></i>
                                        <span class="small">{{ $category->name }}</span>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary bg-opacity-15 text-primary border border-primary">Principale</span>
                                        @if($category->children_count > 0)
                                            <span class="badge bg-info bg-opacity-15 text-info border border-info ms-1">
                                                {{ $category->children_count }} enfant(s)
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }} bg-opacity-15 text-{{ $category->is_active ? 'success' : 'secondary' }} border border-{{ $category->is_active ? 'success' : 'secondary' }}">
                                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    @if($category->is_featured)
                                        <span class="badge bg-warning bg-opacity-15 text-warning border border-warning">
                                            <i class="fas fa-star me-1"></i>Vedette
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <strong class="text-primary">{{ $category->items_count ?? 0 }}</strong>
                                    <small class="text-muted ms-1">articles</small>
                                </div>
                                @if($category->children_count > 0)
                                    <small class="text-muted">
                                        + {{ $category->total_items_count ?? 0 }} dans sous-cat.
                                    </small>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light text-dark">{{ $category->sort_order ?? 0 }}</span>
                                    <div class="ms-2">
                                        <button class="btn btn-sm btn-outline-secondary me-1" onclick="moveCategory({{ $category->id }}, 'up')" title="Monter">
                                            <i class="fas fa-chevron-up"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="moveCategory({{ $category->id }}, 'down')" title="Descendre">
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>{{ $category->created_at->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $category->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('admin.categories.show', $category) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       data-bs-toggle="tooltip" 
                                       title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category) }}" 
                                       class="btn btn-sm btn-outline-primary"
                                       data-bs-toggle="tooltip" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                type="button" 
                                                data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if(!$category->parent_id)
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.categories.create', ['parent' => $category->id]) }}">
                                                        <i class="fas fa-plus me-2"></i>Ajouter sous-catégorie
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                            @endif
                                            <li>
                                                <button class="dropdown-item" onclick="toggleCategoryStatus({{ $category->id }})">
                                                    <i class="fas fa-{{ $category->is_active ? 'pause' : 'play' }} me-2"></i>
                                                    {{ $category->is_active ? 'Désactiver' : 'Activer' }}
                                                </button>
                                            </li>
                                            <li>
                                                <button class="dropdown-item" onclick="toggleCategoryFeatured({{ $category->id }})">
                                                    <i class="fas fa-star me-2"></i>
                                                    {{ $category->is_featured ? 'Retirer de la vedette' : 'Mettre en vedette' }}
                                                </button>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item text-danger" onclick="deleteCategory({{ $category->id }}, '{{ $category->name }}')">
                                                    <i class="fas fa-trash me-2"></i>Supprimer
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Actions en lot -->
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4" id="bulkActions" style="display: none;">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <span class="font-semibold text-gray-900" id="selectedCount">0</span>
                        <span class="text-gray-600 ml-1">catégorie(s) sélectionnée(s)</span>
                    </div>
                    <div class="flex gap-2">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 transition-colors" onclick="bulkAction('activate')">
                            <i class="fas fa-check mr-2"></i>Activer
                        </button>
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-yellow-600 hover:bg-yellow-700 transition-colors" onclick="bulkAction('deactivate')">
                            <i class="fas fa-pause mr-2"></i>Désactiver
                        </button>
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-colors" onclick="bulkAction('reorder')">
                            <i class="fas fa-sort mr-2"></i>Réorganiser
                        </button>
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 transition-colors" onclick="bulkAction('delete')">
                            <i class="fas fa-trash mr-2"></i>Supprimer
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Pagination -->
            @if($categories->hasPages())
                <div class="bg-white border-t border-gray-200 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-700">
                            Affichage de <span class="font-medium">{{ $categories->firstItem() }}</span> à 
                            <span class="font-medium">{{ $categories->lastItem() }}</span> sur 
                            <span class="font-medium">{{ $categories->total() }}</span> résultats
                        </div>
                        <div>
                            {{ $categories->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-12">
                @if(request()->has('search') || request()->has('status') || request()->has('parent'))
                    <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-search text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucune catégorie trouvée</h3>
                    <p class="text-gray-500 mb-6">Aucune catégorie ne correspond à vos critères de recherche.</p>
                    <a href="{{ route('admin.categories.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-primary-300 text-sm font-medium rounded-lg text-primary-700 bg-primary-50 hover:bg-primary-100 transition-colors">
                        <i class="fas fa-times mr-2"></i>Effacer les filtres
                    </a>
                @else
                    <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-layer-group text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucune catégorie enregistrée</h3>
                    <p class="text-gray-500 mb-6">Commencez par créer des catégories pour organiser vos articles.</p>
                    <a href="{{ route('admin.categories.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Ajouter une catégorie
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Confirmer la suppression
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Êtes-vous sûr de vouloir supprimer la catégorie <strong id="categoryNameToDelete"></strong> ?
                        </p>
                        <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        Cette action supprimera également toutes les sous-catégories associées.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Supprimer définitivement
                    </button>
                </form>
                <button type="button" onclick="closeDeleteModal()" 
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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
    const checkboxes = document.querySelectorAll('.category-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateBulkActions();
}

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.category-checkbox:checked');
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
    if (e.target.classList.contains('category-checkbox')) {
        updateBulkActions();
    }
});

// Développer/réduire tout
function toggleExpandAll() {
    const expandAll = document.getElementById('expandAll').checked;
    const subCategories = document.querySelectorAll('tr[data-parent-id]:not([data-parent-id=""])');
    
    subCategories.forEach(row => {
        row.style.display = expandAll ? '' : 'none';
    });
}

// Afficher/masquer les catégories inactives
function toggleInactive() {
    const showInactive = document.getElementById('showInactive').checked;
    const inactiveRows = document.querySelectorAll('.table-secondary');
    
    inactiveRows.forEach(row => {
        row.style.display = showInactive ? '' : 'none';
    });
}

// Changer le statut d'une catégorie
function toggleCategoryStatus(categoryId) {
    fetch(`/admin/categories/${categoryId}/status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ is_active: !document.querySelector(`tr[data-category-id="${categoryId}"]`).classList.contains('table-secondary') })
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
function toggleCategoryFeatured(categoryId) {
    alert('Fonctionnalité à implémenter : toggle featured');
}

// Déplacer une catégorie (ordre)
function moveCategory(categoryId, direction) {
    fetch(`/admin/categories/${categoryId}/move`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ direction: direction })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors du déplacement');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors du déplacement');
    });
}

// Supprimer une catégorie
function deleteCategory(categoryId, categoryName) {
    document.getElementById('categoryNameToDelete').textContent = categoryName;
    document.getElementById('deleteForm').action = `/admin/categories/${categoryId}`;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Actions en lot
function bulkAction(action) {
    const selectedCategories = Array.from(document.querySelectorAll('.category-checkbox:checked')).map(cb => cb.value);
    
    if (selectedCategories.length === 0) {
        alert('Veuillez sélectionner au moins une catégorie.');
        return;
    }
    
    let confirmMessage = '';
    switch(action) {
        case 'activate':
            confirmMessage = `Activer ${selectedCategories.length} catégorie(s) ?`;
            break;
        case 'deactivate':
            confirmMessage = `Désactiver ${selectedCategories.length} catégorie(s) ?`;
            break;
        case 'reorder':
            confirmMessage = `Réorganiser ${selectedCategories.length} catégorie(s) ?`;
            break;
        case 'delete':
            confirmMessage = `Supprimer définitivement ${selectedCategories.length} catégorie(s) et leurs sous-catégories ?`;
            break;
    }
    
    if (confirm(confirmMessage)) {
        console.log(`${action} categories:`, selectedCategories);
        alert(`Fonctionnalité à implémenter : ${action}`);
    }
}

// Organiser les catégories par hiérarchie
function organizeHierarchy() {
    const table = document.getElementById('categoriesTable');
    const rows = Array.from(table.querySelectorAll('tr'));
    
    // Séparer les catégories principales et les sous-catégories
    const mainCategories = rows.filter(row => row.dataset.parentId === '' || !row.dataset.parentId);
    const subCategories = rows.filter(row => row.dataset.parentId && row.dataset.parentId !== '');
    
    // Réorganiser
    mainCategories.forEach(mainRow => {
        table.appendChild(mainRow);
        
        // Ajouter les sous-catégories de cette catégorie principale
        const categoryId = mainRow.dataset.categoryId;
        const children = subCategories.filter(row => row.dataset.parentId === categoryId);
        
        children.forEach(childRow => {
            table.appendChild(childRow);
        });
    });
}

// Exécuter au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    organizeHierarchy();
});
</script>
@endpush