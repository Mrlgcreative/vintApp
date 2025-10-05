@extends('layouts.admin')

@section('title', 'Détails de la catégorie')
@section('page-title', $category->name)

@section('page-actions')
<div class="d-flex gap-2">
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
    </a>
    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary">
        <i class="fas fa-edit me-2"></i>Modifier
    </a>
    <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="fas fa-ellipsis-v"></i>
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#" onclick="toggleStatus()">
                <i class="fas fa-{{ $category->is_active ? 'pause' : 'play' }} me-2"></i>
                {{ $category->is_active ? 'Désactiver' : 'Activer' }}
            </a></li>
            <li><a class="dropdown-item" href="#" onclick="toggleFeatured()">
                <i class="fas fa-{{ $category->is_featured ? 'star-half-alt' : 'star' }} me-2"></i>
                {{ $category->is_featured ? 'Retirer de la une' : 'Mettre en une' }}
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="{{ route('admin.categories.create', ['parent' => $category->id]) }}">
                <i class="fas fa-plus me-2"></i>Ajouter une sous-catégorie
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
                        <div class="d-flex align-items-center mb-3">
                            @if($category->icon)
                                <i class="{{ $category->icon }} me-3" style="color: {{ $category->color ?? '#007bff' }}; font-size: 2rem;"></i>
                            @endif
                            <div>
                                <h3 class="card-title mb-1">{{ $category->name }}</h3>
                                <p class="text-muted mb-0">{{ $category->slug }}</p>
                            </div>
                        </div>
                        
                        @if($category->description)
                            <div class="mb-4">
                                <h6>Description</h6>
                                <p class="text-muted">{{ $category->description }}</p>
                            </div>
                        @endif
                        
                        <div class="row">
                            @if($category->parent)
                                <div class="col-md-6 mb-3">
                                    <h6>Catégorie parente</h6>
                                    <a href="{{ route('admin.categories.show', $category->parent) }}" class="text-decoration-none">
                                        <i class="{{ $category->parent->icon ?? 'fas fa-folder' }} me-2"></i>
                                        {{ $category->parent->name }}
                                    </a>
                                </div>
                            @endif
                            
                            @if($category->color)
                                <div class="col-md-6 mb-3">
                                    <h6>Couleur</h6>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2" style="width: 20px; height: 20px; background-color: {{ $category->color }}; border-radius: 3px;"></div>
                                        <span>{{ $category->color }}</span>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="col-md-6 mb-3">
                                <h6>Ordre d'affichage</h6>
                                <p class="mb-0">{{ $category->sort_order }}</p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <h6>Statut</h6>
                                <div>
                                    @if($category->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                    
                                    @if($category->is_featured)
                                        <span class="badge bg-warning">En vedette</span>
                                    @endif
                                    
                                    @if($category->show_in_menu)
                                        <span class="badge bg-info">Affichage menu</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Meta données SEO -->
                        @if($category->meta_title || $category->meta_description || $category->meta_keywords)
                            <div class="mt-4">
                                <h6>SEO et Meta données</h6>
                                @if($category->meta_title)
                                    <div class="mb-2">
                                        <strong>Titre meta:</strong> {{ $category->meta_title }}
                                    </div>
                                @endif
                                @if($category->meta_description)
                                    <div class="mb-2">
                                        <strong>Description meta:</strong> {{ $category->meta_description }}
                                    </div>
                                @endif
                                @if($category->meta_keywords)
                                    <div class="mb-2">
                                        <strong>Mots-clés:</strong> {{ $category->meta_keywords }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                    
                    <div class="col-md-4 text-center">
                        @if($category->image)
                            <div class="mb-3">
                                <img src="{{ $category->image_url }}" 
                                     class="img-thumbnail" 
                                     style="max-width: 200px;" 
                                     alt="Image {{ $category->name }}">
                            </div>
                        @else
                            <div class="mb-3">
                                <div class="border rounded p-4" style="height: 200px; display: flex; align-items: center; justify-content: center; background-color: {{ $category->color ?? '#f8f9fa' }}20;">
                                    <div class="text-center" style="color: {{ $category->color ?? '#6c757d' }};">
                                        <i class="{{ $category->icon ?? 'fas fa-image' }} fa-3x mb-2"></i>
                                        <p class="mb-0">{{ $category->name }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sous-catégories -->
        @if($category->children && $category->children->count() > 0)
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Sous-catégories</h5>
                    <a href="{{ route('admin.categories.create', ['parent' => $category->id]) }}" class="btn btn-sm btn-outline-primary">
                        Ajouter une sous-catégorie
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($category->children as $child)
                            <div class="col-md-6 mb-3">
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            @if($child->icon)
                                                <i class="{{ $child->icon }} me-3" style="color: {{ $child->color ?? '#007bff' }}; font-size: 1.5rem;"></i>
                                            @endif
                                            <div class="flex-grow-1">
                                                <h6 class="card-title mb-1">{{ $child->name }}</h6>
                                                <small class="text-muted">{{ $child->items_count ?? 0 }} article(s)</small>
                                            </div>
                                            <a href="{{ route('admin.categories.show', $child) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Articles de la catégorie -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Articles de la catégorie</h5>
                <a href="{{ route('admin.items.index', ['category' => $category->id]) }}" class="btn btn-sm btn-outline-primary">
                    Voir tous les articles
                </a>
            </div>
            <div class="card-body">
                @if($category->items && $category->items->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Article</th>
                                    <th>Marque</th>
                                    <th>Prix</th>
                                    <th>Statut</th>
                                    <th>Créé le</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->items->take(10) as $item)
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
                                                    <small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $item->brand->name ?? 'Sans marque' }}</td>
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
                        <p>Aucun article dans cette catégorie</p>
                        <a href="{{ route('admin.items.create', ['category' => $category->id]) }}" class="btn btn-primary">
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
                            <h3 class="text-primary">{{ $category->items_count ?? 0 }}</h3>
                            <small class="text-muted">Articles</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <h3 class="text-success">{{ $category->children_count ?? 0 }}</h3>
                        <small class="text-muted">Sous-catégories</small>
                    </div>
                    <div class="col-6">
                        <div class="border-end">
                            <h3 class="text-info">{{ $category->active_items_count ?? 0 }}</h3>
                            <small class="text-muted">Articles actifs</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h3 class="text-warning">{{ $category->views_count ?? 0 }}</h3>
                        <small class="text-muted">Vues</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Hiérarchie des catégories -->
        @if($category->parent || $category->children->count() > 0)
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Hiérarchie</h5>
                </div>
                <div class="card-body">
                    @if($category->parent)
                        <div class="mb-3">
                            <strong>Parent:</strong><br>
                            <a href="{{ route('admin.categories.show', $category->parent) }}" class="text-decoration-none">
                                <i class="{{ $category->parent->icon ?? 'fas fa-folder' }} me-2"></i>
                                {{ $category->parent->name }}
                            </a>
                        </div>
                    @endif
                    
                    @if($category->children->count() > 0)
                        <div>
                            <strong>Enfants:</strong><br>
                            @foreach($category->children as $child)
                                <div class="mt-1">
                                    <a href="{{ route('admin.categories.show', $child) }}" class="text-decoration-none">
                                        <i class="{{ $child->icon ?? 'fas fa-folder' }} me-2"></i>
                                        {{ $child->name }}
                                    </a>
                                    <small class="text-muted">({{ $child->items_count ?? 0 }})</small>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Informations système</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <strong>ID:</strong>
                        <span>{{ $category->id }}</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <strong>Créée le:</strong>
                        <span>{{ $category->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <strong>Modifiée le:</strong>
                        <span>{{ $category->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                
                @if($category->created_by)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <strong>Créée par:</strong>
                            <span>{{ $category->creator->name ?? 'Inconnu' }}</span>
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
                    <a href="{{ route('admin.items.create', ['category' => $category->id]) }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-2"></i>Ajouter un article
                    </a>
                    
                    <a href="{{ route('admin.categories.create', ['parent' => $category->id]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-plus me-2"></i>Ajouter une sous-catégorie
                    </a>
                    
                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline-info">
                        <i class="fas fa-edit me-2"></i>Modifier la catégorie
                    </a>
                    
                    <button class="btn btn-outline-warning" onclick="exportCategoryData()">
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
                <p>Êtes-vous sûr de vouloir supprimer la catégorie <strong>{{ $category->name }}</strong> ?</p>
                @if($category->items_count > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Cette catégorie contient {{ $category->items_count }} article(s).
                    </div>
                @endif
                @if($category->children_count > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Cette catégorie contient {{ $category->children_count }} sous-catégorie(s).
                    </div>
                @endif
                <p class="text-danger small">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline">
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