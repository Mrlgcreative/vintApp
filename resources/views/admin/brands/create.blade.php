@extends('layouts.admin')

@section('title', 'Créer une marque')
@section('page-title', 'Créer une marque')

@section('page-actions')
<div class="d-flex gap-2">
    <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
    </a>
</div>
@endsection

@section('content')
<form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
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
                                       value="{{ old('name') }}" 
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
                                       value="{{ old('slug') }}" 
                                       readonly>
                                <small class="form-text text-muted">Généré automatiquement à partir du nom</small>
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
                                  rows="4">{{ old('description') }}</textarea>
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
                                       value="{{ old('website') }}" 
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
                                       value="{{ old('country') }}">
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
                                       value="{{ old('founded_year') }}" 
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
                                    <option value="luxury" {{ old('category') == 'luxury' ? 'selected' : '' }}>Luxe</option>
                                    <option value="streetwear" {{ old('category') == 'streetwear' ? 'selected' : '' }}>Streetwear</option>
                                    <option value="vintage" {{ old('category') == 'vintage' ? 'selected' : '' }}>Vintage</option>
                                    <option value="sports" {{ old('category') == 'sports' ? 'selected' : '' }}>Sport</option>
                                    <option value="casual" {{ old('category') == 'casual' ? 'selected' : '' }}>Casual</option>
                                    <option value="formal" {{ old('category') == 'formal' ? 'selected' : '' }}>Formel</option>
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
                    <div class="mb-3">
                        <label for="logo" class="form-label">Logo de la marque</label>
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
                                   {{ old('is_active', true) ? 'checked' : '' }}>
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
                                   {{ old('is_featured') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">
                                Marque mise en avant
                            </label>
                        </div>
                        <small class="form-text text-muted">Apparaît dans la section marques vedettes</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Annuler
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-2"></i>Créer la marque
                </button>
            </div>
        </div>
    </div>
</form>
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
</script>
@endpush