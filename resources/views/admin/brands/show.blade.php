@extends('layouts.admin')

@section('title', 'Détails de la marque')
@section('page-title', $brand->name)

@section('page-actions')
<div class="d-flex gap-2">
    <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
    </a>
    <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-primary">
        <i class="fas fa-edit me-2"></i>Modifier
    </a>
    <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="fas fa-ellipsis-v"></i>
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#" onclick="toggleStatus()">
                <i class="fas fa-{{ $brand->is_active ? 'pause' : 'play' }} me-2"></i>
                {{ $brand->is_active ? 'Désactiver' : 'Activer' }}
            </a></li>
            <li><a class="dropdown-item" href="#" onclick="toggleFeatured()">
                <i class="fas fa-{{ $brand->is_featured ? 'star-half-alt' : 'star' }} me-2"></i>
                {{ $brand->is_featured ? 'Retirer de la une' : 'Mettre en une' }}
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete()">
                <i class="fas fa-trash me-2"></i>Supprimer
            </a></li>
        </ul>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Informations principales -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title">{{ $brand->name }}</h3>
                        <p class="text-muted mb-3">{{ $brand->slug }}</p>
                        
                        @if($brand->description)
                            <div class="mb-4">
                                <h6>Description</h6>
                                <p class="text-muted">{{ $brand->description }}</p>
                            </div>
                        @endif
                        
                        <div class="row">
                            @if($brand->website)
                                <div class="col-md-6 mb-3">
                                    <h6>Site web</h6>
                                    <a href="{{ $brand->website }}" target="_blank" class="text-decoration-none">
                                        <i class="fas fa-external-link-alt me-2"></i>
                                        {{ $brand->website }}
                                    </a>
                                </div>
                            @endif
                            
                            @if($brand->country)
                                <div class="col-md-6 mb-3">
                                    <h6>Pays d'origine</h6>
                                    <p class="mb-0">{{ $brand->country }}</p>
                                </div>
                            @endif
                            
                            @if($brand->founded_year)
                                <div class="col-md-6 mb-3">
                                    <h6>Année de création</h6>
                                    <p class="mb-0">{{ $brand->founded_year }}</p>
                                </div>
                            @endif
                            
                            @if($brand->category)
                                <div class="col-md-6 mb-3">
                                    <h6>Catégorie</h6>
                                    <span class="badge bg-primary">{{ ucfirst($brand->category) }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h6>Statut</h6>
                                @if($brand->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                                
                                @if($brand->is_featured)
                                    <span class="badge bg-warning">En vedette</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 text-center">
                        @if($brand->logo)
                            <div class="mb-3">
                                <img src="{{ $brand->logo_url }}" 
                                     class="img-thumbnail" 
                                     style="max-width: 200px;" 
                                     alt="Logo {{ $brand->name }}">
                            </div>
                        @else
                            <div class="mb-3">
                                <div class="border rounded p-4" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                    <div class="text-muted">
                                        <i class="fas fa-image fa-3x mb-2"></i>
                                        <p>Aucun logo</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Articles de la marque -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Articles de la marque</h5>
                <a href="{{ route('admin.items.index', ['brand' => $brand->id]) }}" class="btn btn-sm btn-outline-primary">
                    Voir tous les articles
                </a>
            </div>
            <div class="card-body">
                @if($brand->items && $brand->items->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Article</th>
                                    <th>Prix</th>
                                    <th>Statut</th>
                                    <th>Créé le</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($brand->items->take(10) as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->image)
                                                    <img src="{{ $item->image_url }}" 
                                                         class="rounded me-2" 
                                                         width="40" 
                                                         height="40" 
                                                         alt="{{ $item->title }}">
                                                @endif
                                                <div>
                                                    <div class="fw-bold">{{ $item->title }}</div>
                                                    <small class="text-muted">{{ $item->category->name ?? 'Sans catégorie' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ number_format($item->price, 2) }} €</td>
                                        <td>
                                            <span class="badge bg-{{ $item->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.items.show', $item) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-box fa-3x mb-3"></i>
                        <p>Aucun article pour cette marque</p>
                        <a href="{{ route('admin.items.create', ['brand' => $brand->id]) }}" class="btn btn-primary">
                            Ajouter un article
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Sidebar avec statistiques -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Statistiques</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="border-end">
                            <h3 class="text-primary">{{ $brand->items_count ?? 0 }}</h3>
                            <small class="text-muted">Articles</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <h3 class="text-success">{{ $brand->orders_count ?? 0 }}</h3>
                        <small class="text-muted">Commandes</small>
                    </div>
                    <div class="col-6">
                        <div class="border-end">
                            <h3 class="text-info">{{ $brand->active_items_count ?? 0 }}</h3>
                            <small class="text-muted">Articles actifs</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h3 class="text-warning">{{ $brand->views_count ?? 0 }}</h3>
                        <small class="text-muted">Vues</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Informations système</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <strong>ID:</strong>
                        <span>{{ $brand->id }}</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <strong>Créée le:</strong>
                        <span>{{ $brand->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <strong>Modifiée le:</strong>
                        <span>{{ $brand->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                
                @if($brand->created_by)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <strong>Créée par:</strong>
                            <span>{{ $brand->creator->name ?? 'Inconnu' }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Actions rapides</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.items.create', ['brand' => $brand->id]) }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-2"></i>Ajouter un article
                    </a>
                    
                    <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-edit me-2"></i>Modifier la marque
                    </a>
                    
                    <button class="btn btn-outline-info" onclick="exportBrandData()">
                        <i class="fas fa-download me-2"></i>Exporter les données
                    </button>
                </div>
            </div>
        </div>
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
                <p>Êtes-vous sûr de vouloir supprimer la marque <strong>{{ $brand->name }}</strong> ?</p>
                <p class="text-danger small">Cette action est irréversible et supprimera tous les articles associés.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="d-inline">
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
function confirmDelete() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function toggleStatus() {
    fetch(`/admin/brands/{{ $brand->id }}/toggle-status`, {
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
    fetch(`/admin/brands/{{ $brand->id }}/toggle-featured`, {
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

function exportBrandData() {
    window.location.href = `/admin/brands/{{ $brand->id }}/export`;
}
</script>
@endpush