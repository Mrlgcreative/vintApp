              @extends('layouts.admin')

@section('title', 'Gestion des marques')
@section('page-title', 'Gestion des marques')

@section('page-actions')
<div class="flex flex-wrap gap-3">
    <div class="relative">
        <button class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200" 
                type="button" onclick="toggleDropdown('filter-dropdown')">
            <i class="fas fa-filter mr-2"></i>Filtrer
            <i class="fas fa-chevron-down ml-1"></i>
        </button>
        <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden z-10" 
             id="filter-dropdown">
            <div class="py-1">
                <a href="{{ route('admin.brands.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Toutes</a>
                <a href="{{ route('admin.brands.index', ['status' => 'active']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Actives</a>
                <a href="{{ route('admin.brands.index', ['status' => 'inactive']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Inactives</a>
                <a href="{{ route('admin.brands.index', ['featured' => '1']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">En vedette</a>
            </div>
        </div>
    </div>
    <div class="relative">
        <button class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200" 
                type="button" onclick="toggleDropdown('export-dropdown')">
            <i class="fas fa-download mr-2"></i>Export
            <i class="fas fa-chevron-down ml-1"></i>
        </button>
        <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden z-10" 
             id="export-dropdown">
            <div class="py-1">
                <a href="#" onclick="exportBrands('csv')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">CSV</a>
                <a href="#" onclick="exportBrands('excel')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Excel</a>
                <a href="#" onclick="exportBrands('pdf')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">PDF</a>
            </div>
        </div>
    </div>
    <a href="{{ route('admin.brands.create') }}" 
       class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors duration-200">
        <i class="fas fa-plus mr-2"></i>Ajouter une marque
    </a>
</div>
@endsection

@section('content')
<!-- Messages de succès/erreur -->
@if(session('success'))
    <div class="flex items-center rounded-xl bg-green-50 p-4 text-green-800 animate-fade-in mb-6" role="alert">
        <i class="fas fa-check-circle mr-3 text-green-500"></i>
        <span class="flex-1">{{ session('success') }}</span>
        <button type="button" class="ml-4 text-green-500 hover:text-green-700" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="flex items-center rounded-xl bg-red-50 p-4 text-red-800 animate-fade-in mb-6" role="alert">
        <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
        <span class="flex-1">{{ session('error') }}</span>
        <button type="button" class="ml-4 text-red-500 hover:text-red-700" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

<!-- Barre de recherche et filtres -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
    <div class="p-6">
        <form method="GET" action="{{ route('admin.brands.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div class="md:col-span-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                           name="search" 
                           placeholder="Rechercher une marque..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div>
                <select name="country" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Tous les pays</option>
                    <option value="FR" {{ request('country') === 'FR' ? 'selected' : '' }}>France</option>
                    <option value="IT" {{ request('country') === 'IT' ? 'selected' : '' }}>Italie</option>
                    <option value="US" {{ request('country') === 'US' ? 'selected' : '' }}>États-Unis</option>
                    <option value="GB" {{ request('country') === 'GB' ? 'selected' : '' }}>Royaume-Uni</option>
                </select>
            </div>
            <div>
                <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Nom A-Z</option>
                    <option value="-name" {{ request('sort') === '-name' ? 'selected' : '' }}>Nom Z-A</option>
                    <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Plus ancien</option>
                    <option value="-created_at" {{ request('sort', '-created_at') === '-created_at' ? 'selected' : '' }}>Plus récent</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors duration-200">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('admin.brands.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Statistiques -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <!-- Total marques -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-primary-600 uppercase tracking-wider mb-2">Total marques</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $brands->total() ?? 0 }}</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tags text-2xl text-primary-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-primary-500 to-primary-600"></div>
    </div>

    <!-- Marques actives -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-green-600 uppercase tracking-wider mb-2">Marques actives</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $brands->where('is_active', 1)->count() ?? 0 }}</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-green-500 to-green-600"></div>
    </div>

    <!-- En vedette -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-yellow-600 uppercase tracking-wider mb-2">En vedette</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $brands->where('is_featured', 1)->count() ?? 0 }}</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-star text-2xl text-yellow-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-yellow-500 to-yellow-600"></div>
    </div>

    <!-- Articles total -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-2">Articles total</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $brands->sum('items_count') ?? 0 }}</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-1 bg-gradient-to-r from-blue-500 to-blue-600"></div>
    </div>
</div>

<!-- Liste des marques -->
<div class="card shadow-sm">
    <div class="card-header bg-white border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-0">Liste des marques</h5>
                @if(isset($brands))
                    <small class="text-muted">{{ $brands->total() }} marque(s) trouvée(s)</small>
                @endif
            </div>
            <div class="d-flex gap-2">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showInactive" onchange="toggleInactive()">
                    <label class="form-check-label small" for="showInactive">
                        Voir inactives
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        @if(isset($brands) && $brands->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </div>
                            </th>
                            <th class="border-0">Marque</th>
                            <th class="border-0">Statut</th>
                            <th class="border-0">Pays</th>
                            <th class="border-0">Articles</th>
                            <th class="border-0">Créée le</th>
                            <th class="border-0 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($brands as $brand)
                        <tr data-brand-id="{{ $brand->id }}" class="{{ !$brand->is_active ? 'table-secondary' : '' }}">
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input brand-checkbox" type="checkbox" value="{{ $brand->id }}">
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($brand->logo)
                                        <img src="{{ $brand->logo_url }}" 
                                             class="rounded me-3 border" width="45" height="45" style="object-fit: cover;"
                                             alt="Logo {{ $brand->name }}">
                                    @else
                                        <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center me-3 border" 
                                             style="width: 45px; height: 45px;">
                                            <span class="fw-bold">{{ strtoupper(substr($brand->name, 0, 2)) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $brand->name }}</div>
                                        @if($brand->slug)
                                            <small class="text-muted">{{ $brand->slug }}</small>
                                        @endif
                                        @if($brand->description)
                                            <small class="text-muted d-block">{{ Str::limit($brand->description, 40) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <span class="badge bg-{{ $brand->is_active ? 'success' : 'secondary' }} bg-opacity-15 text-{{ $brand->is_active ? 'success' : 'secondary' }} border border-{{ $brand->is_active ? 'success' : 'secondary' }}">
                                        {{ $brand->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    @if($brand->is_featured)
                                        <span class="badge bg-warning bg-opacity-15 text-warning border border-warning">
                                            <i class="fas fa-star me-1"></i>Vedette
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($brand->country)
                                    <div class="d-flex align-items-center">
                                        <span class="fi fi-{{ strtolower($brand->country) }} me-2"></span>
                                        <span>{{ $brand->country }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">Non spécifié</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <strong class="text-primary">{{ $brand->items_count ?? 0 }}</strong>
                                    <small class="text-muted ms-1">articles</small>
                                </div>
                                @if($brand->items_count > 0)
                                    <small class="text-muted">
                                        Dernier: {{ $brand->latest_item_date ? $brand->latest_item_date->format('d/m/Y') : 'N/A' }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <div>{{ $brand->created_at->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $brand->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('admin.brands.show', $brand) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       data-bs-toggle="tooltip" 
                                       title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.brands.edit', $brand) }}" 
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
                                            <li>
                                                <button class="dropdown-item" onclick="toggleBrandStatus({{ $brand->id }})">
                                                    <i class="fas fa-{{ $brand->is_active ? 'pause' : 'play' }} me-2"></i>
                                                    {{ $brand->is_active ? 'Désactiver' : 'Activer' }}
                                                </button>
                                            </li>
                                            <li>
                                                <button class="dropdown-item" onclick="toggleBrandFeatured({{ $brand->id }})">
                                                    <i class="fas fa-star me-2"></i>
                                                    {{ $brand->is_featured ? 'Retirer de la vedette' : 'Mettre en vedette' }}
                                                </button>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item text-danger" onclick="deleteBrand({{ $brand->id }}, '{{ $brand->name }}')">
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
            <div class="card-footer bg-light border-top" id="bulkActions" style="display: none;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold" id="selectedCount">0</span> marque(s) sélectionnée(s)
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-success" onclick="bulkAction('activate')">
                            <i class="fas fa-check me-1"></i>Activer
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="bulkAction('deactivate')">
                            <i class="fas fa-pause me-1"></i>Désactiver
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="bulkAction('delete')">
                            <i class="fas fa-trash me-1"></i>Supprimer
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Pagination -->
            @if($brands->hasPages())
                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Affichage de {{ $brands->firstItem() }} à {{ $brands->lastItem() }} 
                            sur {{ $brands->total() }} résultats
                        </div>
                        <div>
                            {{ $brands->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                @if(request()->has('search') || request()->has('status') || request()->has('country'))
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5>Aucune marque trouvée</h5>
                    <p class="text-muted">Aucune marque ne correspond à vos critères de recherche.</p>
                    <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-times me-2"></i>Effacer les filtres
                    </a>
                @else
                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                    <h5>Aucune marque enregistrée</h5>
                    <p class="text-muted">Commencez par ajouter des marques pour organiser vos articles.</p>
                    <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Ajouter une marque
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer la marque <strong id="brandNameToDelete"></strong> ?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Cette action est irréversible et supprimera toutes les données associées.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                </form>
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
    fetch(`/admin/brands/${brandId}/status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ is_active: !document.querySelector(`tr[data-brand-id="${brandId}"]`).classList.contains('table-secondary') })
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
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

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
    const inactiveRows = document.querySelectorAll('.table-secondary');
    
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
</script>
@endpush