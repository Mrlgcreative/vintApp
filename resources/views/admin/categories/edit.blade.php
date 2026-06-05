@extends('layouts.admin')

@section('title', 'Modifier la catégorie')
@section('page-title', 'Modifier : ' . $category->name)

@section('page-actions')
<div class="flex gap-2">
    <a href="{{ route('admin.categories.index') }}" 
       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>Retour à la liste
    </a>
    <a href="{{ route('admin.categories.show', $category) }}" 
       class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
        <i class="fas fa-eye mr-2"></i>Voir
    </a>
    <button type="button" onclick="confirmDelete()" 
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
        <i class="fas fa-trash mr-2"></i>Supprimer
    </button>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6">
                <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" id="categoryForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Informations principales -->
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nom de la catégorie <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $category->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Le nom de la catégorie (ex: "Accessoires", "Vêtements")</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                       id="slug" name="slug" value="{{ old('slug', $category->slug) }}" required>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">URL conviviale (modifiez avec précaution)</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4">{{ old('description', $category->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Description détaillée de la catégorie</div>
                            </div>
                        </div>
                        
                        <!-- Image -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="image" class="form-label">Image de la catégorie</label>
                                
                                @if($category->image)
                                    <div class="current-image mb-2">
                                        <img src="{{ $category->image_url }}" class="img-thumbnail" style="max-width: 100%;" alt="Image actuelle">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                                            <label class="form-check-label text-danger" for="remove_image">
                                                Supprimer l'image actuelle
                                            </label>
                                        </div>
                                    </div>
                                @endif
                                
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       id="image" name="image" accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Format recommandé: JPG, PNG (max 2MB)</div>
                            </div>
                            
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <img id="previewImg" src="" class="img-thumbnail" style="max-width: 100%;" alt="Aperçu">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Catégorie parente -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="parent_id" class="form-label">Catégorie parente</label>
                                <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                                    <option value="">Aucune (catégorie racine)</option>
                                    @if(isset($categories))
                                        @foreach($categories as $cat)
                                            @if($cat->id !== $category->id)
                                                <option value="{{ $cat->id }}" {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
                                                    {{ $cat->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                @error('parent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Sélectionnez une catégorie parente pour créer une sous-catégorie</div>
                            </div>
                        </div>
                        
                        <!-- Ordre d'affichage -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Ordre d'affichage</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                       id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Plus le nombre est petit, plus la catégorie apparaît en premier</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Couleur et icône -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="color" class="form-label">Couleur</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                           id="color" name="color" value="{{ old('color', $category->color ?? '#007bff') }}">
                                    <input type="text" class="form-control" id="colorHex" readonly>
                                </div>
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Couleur associée à la catégorie pour l'affichage</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="icon" class="form-label">Icône</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i id="iconPreview" class="{{ $category->icon ?? 'fas fa-tag' }}"></i>
                                    </span>
                                    <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                           id="icon" name="icon" value="{{ old('icon', $category->icon ?? 'fas fa-tag') }}" 
                                           placeholder="fas fa-tag">
                                </div>
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Classe CSS FontAwesome (ex: fas fa-tshirt)</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Meta données SEO -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">SEO et Meta données</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="meta_title" class="form-label">Titre meta</label>
                                <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                       id="meta_title" name="meta_title" value="{{ old('meta_title', $category->meta_title) }}" maxlength="60">
                                @error('meta_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Titre pour les moteurs de recherche (60 caractères max)</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="meta_description" class="form-label">Description meta</label>
                                <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                          id="meta_description" name="meta_description" rows="3" maxlength="160">{{ old('meta_description', $category->meta_description) }}</textarea>
                                @error('meta_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Description pour les moteurs de recherche (160 caractères max)</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="meta_keywords" class="form-label">Mots-clés</label>
                                <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" 
                                       id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $category->meta_keywords) }}">
                                @error('meta_keywords')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Mots-clés séparés par des virgules</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Options -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">Options</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" 
                                               id="is_active" name="is_active" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Catégorie active
                                        </label>
                                    </div>
                                    <div class="form-text">La catégorie sera visible sur le site</div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" 
                                               id="is_featured" name="is_featured" {{ old('is_featured', $category->is_featured) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">
                                            Catégorie en vedette
                                        </label>
                                    </div>
                                    <div class="form-text">Afficher dans les catégories principales</div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" 
                                               id="show_in_menu" name="show_in_menu" {{ old('show_in_menu', $category->show_in_menu) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_in_menu">
                                            Afficher dans le menu
                                        </label>
                                    </div>
                                    <div class="form-text">Inclure dans la navigation du site</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Statistiques (si des données existent) -->
                    @if($category->items_count || $category->subcategories_count)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0">Statistiques</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <h4 class="text-primary">{{ $category->items_count ?? 0 }}</h4>
                                        <small class="text-muted">Articles</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 class="text-success">{{ $category->subcategories_count ?? 0 }}</h4>
                                        <small class="text-muted">Sous-catégories</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 class="text-info">{{ $category->active_items_count ?? 0 }}</h4>
                                        <small class="text-muted">Articles actifs</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 class="text-warning">{{ $category->views_count ?? 0 }}</h4>
                                        <small class="text-muted">Vues</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Boutons d'action -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                                    <i class="fas fa-times me-2"></i>Annuler
                                </button>
                                
                                <div>
                                    <button type="submit" name="action" value="save" class="btn btn-primary me-2">
                                        <i class="fas fa-save me-2"></i>Mettre à jour
                                    </button>
                                    <button type="submit" name="action" value="save_and_continue" class="btn btn-success">
                                        <i class="fas fa-save me-2"></i>Mettre à jour et continuer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
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
                        La suppression réassignera ces articles à la catégorie par défaut.
                    </div>
                @endif
                @if($category->subcategories_count > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Cette catégorie contient {{ $category->subcategories_count }} sous-catégorie(s).
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
document.addEventListener('DOMContentLoaded', function() {
    // Génération automatique du slug (optionnelle lors de l'édition)
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    nameInput.addEventListener('input', function() {
        if (!slugInput.dataset.manual) {
            const slug = this.value
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9\s-]/g, '')
                .trim()
                .replace(/\s+/g, '-');
            slugInput.value = slug;
        }
    });
    
    slugInput.addEventListener('input', function() {
        this.dataset.manual = 'true';
    });
    
    // Marquer le slug comme modifié manuellement si il diffère du nom
    slugInput.dataset.manual = 'true';
    
    // Aperçu de l'image
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
        }
    });
    
    // Synchronisation couleur hex
    const colorInput = document.getElementById('color');
    const colorHex = document.getElementById('colorHex');
    
    colorInput.addEventListener('input', function() {
        colorHex.value = this.value;
    });
    
    colorHex.addEventListener('input', function() {
        if (/^#[0-9A-F]{6}$/i.test(this.value)) {
            colorInput.value = this.value;
        }
    });
    
    // Initialiser la valeur hex
    colorHex.value = colorInput.value;
    
    // Aperçu de l'icône
    const iconInput = document.getElementById('icon');
    const iconPreview = document.getElementById('iconPreview');
    
    iconInput.addEventListener('input', function() {
        iconPreview.className = this.value || 'fas fa-tag';
    });
});

function confirmDelete() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endpush