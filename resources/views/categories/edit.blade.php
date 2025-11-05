@extends('app')

@section('content')
@php
use Illuminate\Support\Facades\Storage;
@endphp
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <h4 class="mb-0 fw-bold"><i class="fas fa-edit me-2"></i>Modifier la catégorie</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $category->name) }}" 
                                   required 
                                   maxlength="100"
                                   placeholder="Ex: Vêtements, Électronique...">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" 
                                      id="description" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      rows="3" 
                                      maxlength="500"
                                      placeholder="Description de la catégorie...">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="icon" class="form-label">Icône</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i id="icon-preview" class="{{ old('icon', $category->icon ?: 'fas fa-folder') }}"></i>
                                </span>
                                <input type="text" 
                                       name="icon" 
                                       id="icon" 
                                       class="form-control @error('icon') is-invalid @enderror" 
                                       value="{{ old('icon', $category->icon) }}" 
                                       placeholder="fas fa-tshirt"
                                       maxlength="50">
                            </div>
                            <div class="form-text">
                                Utilisez les classes FontAwesome (ex: fas fa-tshirt, fas fa-laptop, fas fa-car)
                            </div>
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Image de la catégorie</label>
                            
                            @if($category->image && Storage::disk('public')->exists($category->image))
                                <div class="mb-2">
                                    <img src="{{ Storage::url($category->image) }}" 
                                         alt="{{ $category->name }}" 
                                         class="img-thumbnail" 
                                         style="max-width: 150px; max-height: 150px;">
                                    <p class="text-muted small mt-1">Image actuelle</p>
                                </div>
                            @endif
                            
                            <input type="file" 
                                   name="image" 
                                   id="image" 
                                   class="form-control @error('image') is-invalid @enderror" 
                                   accept="image/*">
                            <div class="form-text">
                                Formats acceptés : JPEG, PNG, JPG, GIF, SVG. Taille maximale : 2MB.
                                @if($category->image)
                                    <br>Laisser vide pour garder l'image actuelle.
                                @endif
                            </div>
                            <div id="image-preview" class="mt-2" style="display: none;">
                                <img id="preview-img" src="" alt="Aperçu" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                            </div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="parent_id" class="form-label">Catégorie parente (optionnel)</label>
                            <select name="parent_id" 
                                    id="parent_id" 
                                    class="form-select @error('parent_id') is-invalid @enderror">
                                <option value="">-- Aucune (catégorie principale) --</option>
                                @if(isset($parentCategories))
                                    @foreach($parentCategories as $parent)
                                        <option value="{{ $parent->id }}" 
                                                {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Ordre d'affichage</label>
                            <input type="number" 
                                   name="sort_order" 
                                   id="sort_order" 
                                   class="form-control @error('sort_order') is-invalid @enderror" 
                                   value="{{ old('sort_order', $category->sort_order ?? 0) }}" 
                                   min="0"
                                   placeholder="0">
                            <div class="form-text">
                                Plus le nombre est petit, plus la catégorie apparaîtra en premier
                            </div>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Catégorie active
                            </label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('categories.show', $category) }}" class="btn btn-outline-info me-2">
                                    <i class="fas fa-eye me-1"></i> Voir la catégorie
                                </a>
                            </div>
                            <div>
                                <a href="{{ route('categories.index') }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-arrow-left me-1"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Mettre à jour
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Informations supplémentaires -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Articles dans cette catégorie:</strong>
                            <span class="badge bg-info ms-2">{{ $category->items_count ?? 0 }}</span>
                        </div>
                        <div class="col-sm-6">
                            <strong>Créée le:</strong>
                            <span class="text-muted">{{ $category->created_at?->format('d/m/Y H:i') ?? 'Inconnue' }}</span>
                        </div>
                    </div>
                    @if($category->updated_at && $category->updated_at != $category->created_at)
                    <div class="row mt-2">
                        <div class="col-12">
                            <strong>Dernière modification:</strong>
                            <span class="text-muted">{{ $category->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const iconInput = document.getElementById('icon');
    const iconPreview = document.getElementById('icon-preview');
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    // Prévisualisation de l'icône
    iconInput.addEventListener('input', function() {
        const iconClass = this.value || 'fas fa-folder';
        iconPreview.className = iconClass;
    });
    
    // Prévisualisation de l'image
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
});
</script>
@endsection