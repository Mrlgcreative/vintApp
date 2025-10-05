@extends('layouts.admin')

@section('title', 'Modifier la marque')
@section('page-title', 'Modifier : ' . $brand->name)

@section('page-actions')
<div class="d-flex gap-2">
    <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
    </a>
    <a href="{{ route('admin.brands.show', $brand) }}" class="btn btn-outline-info">
        <i class="fas fa-eye me-2"></i>Voir les détails
    </a>
</div>
@endsection

@section('content')
<form action="{{ route('admin.brands.update', $brand) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations de la marque</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nom de la marque *</label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $brand->name) }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug</label>
                                <input type="text" 
                                       name="slug" 
                                       id="slug" 
                                       class="form-control @error('slug') is-invalid @enderror" 
                                       value="{{ old('slug', $brand->slug) }}">
                                <small class="form-text text-muted">URL conviviale pour la marque</small>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" 
                                  id="description" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  rows="4">{{ old('description', $brand->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="website" class="form-label">Site web</label>
                                <input type="url" 
                                       name="website" 
                                       id="website" 
                                       class="form-control @error('website') is-invalid @enderror" 
                                       value="{{ old('website', $brand->website) }}" 
                                       placeholder="https://...">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="country" class="form-label">Pays d'origine</label>
                                <input type="text" 
                                       name="country" 
                                       id="country" 
                                       class="form-control @error('country') is-invalid @enderror" 
                                       value="{{ old('country', $brand->country) }}">
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="founded_year" class="form-label">Année de création</label>
                                <input type="number" 
                                       name="founded_year" 
                                       id="founded_year" 
                                       class="form-control @error('founded_year') is-invalid @enderror" 
                                       value="{{ old('founded_year', $brand->founded_year) }}" 
                                       min="1800" 
                                       max="{{ date('Y') }}">
                                @error('founded_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label">Catégorie</label>
                                <select name="category" 
                                        id="category" 
                                        class="form-select @error('category') is-invalid @enderror">
                                    <option value="">Sélectionner une catégorie</option>
                                    <option value="luxury" {{ old('category', $brand->category) == 'luxury' ? 'selected' : '' }}>Luxe</option>
                                    <option value="streetwear" {{ old('category', $brand->category) == 'streetwear' ? 'selected' : '' }}>Streetwear</option>
                                    <option value="vintage" {{ old('category', $brand->category) == 'vintage' ? 'selected' : '' }}>Vintage</option>
                                    <option value="sports" {{ old('category', $brand->category) == 'sports' ? 'selected' : '' }}>Sport</option>
                                    <option value="casual" {{ old('category', $brand->category) == 'casual' ? 'selected' : '' }}>Casual</option>
                                    <option value="formal" {{ old('category', $brand->category) == 'formal' ? 'selected' : '' }}>Formel</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Logo et Images</h5>
                </div>
                <div class="card-body">
                    @if($brand->logo)
                        <div class="mb-3 text-center">
                            <label class="form-label">Logo actuel</label>
                            <div>
                                <img src="{{ $brand->logo_url }}" class="img-thumbnail" style="max-width: 200px;" alt="Logo {{ $brand->name }}">
                            </div>
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <label for="logo" class="form-label">{{ $brand->logo ? 'Changer le logo' : 'Ajouter un logo' }}</label>
                        <input type="file" 
                               name="logo" 
                               id="logo" 
                               class="form-control @error('logo') is-invalid @enderror" 
                               accept="image/*">
                        <small class="form-text text-muted">Formats acceptés: JPG, PNG, SVG (max 2MB)</small>
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <div id="logo-preview" class="text-center" style="display: none;">
                            <img id="logo-preview-img" src="" class="img-thumbnail" style="max-width: 200px;">
                        </div>
                    </div>
                    
                    @if($brand->logo)
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="remove_logo" 
                                       id="remove_logo" 
                                       value="1">
                                <label class="form-check-label text-danger" for="remove_logo">
                                    Supprimer le logo actuel
                                </label>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Paramètres</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="is_active" 
                                   id="is_active" 
                                   value="1" 
                                   {{ old('is_active', $brand->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Marque active
                            </label>
                        </div>
                        <small class="form-text text-muted">Les marques inactives n'apparaissent pas sur le site</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="is_featured" 
                                   id="is_featured" 
                                   value="1" 
                                   {{ old('is_featured', $brand->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">
                                Marque mise en avant
                            </label>
                        </div>
                        <small class="form-text text-muted">Apparaît dans la section marques vedettes</small>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Statistiques</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary">{{ $brand->items_count ?? 0 }}</h4>
                                <small class="text-muted">Articles</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">{{ $brand->orders_count ?? 0 }}</h4>
                            <small class="text-muted">Commandes</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="small text-muted">
                        <div class="d-flex justify-content-between">
                            <span>Créée le:</span>
                            <span>{{ $brand->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Modifiée le:</span>
                            <span>{{ $brand->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash me-2"></i>Supprimer la marque
                </button>
                
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Mettre à jour
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

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
document.addEventListener('DOMContentLoaded', function() {
    // Génération automatique du slug
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    nameInput.addEventListener('input', function() {
        const slug = this.value
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
        slugInput.value = slug;
    });
    
    // Prévisualisation du logo
    const logoInput = document.getElementById('logo');
    const logoPreview = document.getElementById('logo-preview');
    const logoPreviewImg = document.getElementById('logo-preview-img');
    
    logoInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                logoPreviewImg.src = e.target.result;
                logoPreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            logoPreview.style.display = 'none';
        }
    });
});

function confirmDelete() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endpush