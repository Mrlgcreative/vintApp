@extends('app')
@section('title', 'creation-cat')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <h4 class="mb-0 fw-bold"><i class="fas fa-plus me-2"></i>Ajouter une catégorie</h4>
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

                    <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" 
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
                                      placeholder="Description de la catégorie...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="icon" class="form-label">Icône</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i id="icon-preview" class="{{ old('icon', 'fas fa-folder') }}"></i>
                                </span>
                                <input type="text" 
                                       name="icon" 
                                       id="icon" 
                                       class="form-control @error('icon') is-invalid @enderror" 
                                       value="{{ old('icon') }}" 
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
                            <input type="file" 
                                   name="image" 
                                   id="image" 
                                   class="form-control @error('image') is-invalid @enderror" 
                                   accept="image/*">
                            <div class="form-text">
                                Formats acceptés : JPEG, PNG, JPG, GIF, SVG. Taille maximale : 2MB.
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
                                                {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
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
                                   value="{{ old('sort_order', 0) }}" 
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
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Catégorie active
                            </label>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('categories.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-arrow-left me-1"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Enregistrer
                            </button>
                        </div>
                    </form>
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