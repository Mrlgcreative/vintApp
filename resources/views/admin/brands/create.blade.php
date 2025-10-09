@extends('layouts.admin')

@section('title', 'Créer une marque')
@section('page-title', 'Créer une marque')

@section('page-actions')
<div class="flex gap-2">
    <a href="{{ route('admin.brands.index') }}" class="inline-flex items-center px-3 sm:px-4 py-2 text-sm sm:text-base bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>
        <span class="hidden sm:inline">Retour à la liste</span>
        <span class="sm:hidden">Retour</span>
    </a>
</div>
@endsection

@section('content')
<form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informations principales -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center mr-3">
                            <i class="fas fa-info-circle text-primary-600"></i>
                        </div>
                        Informations de la marque
                    </h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nom de la marque <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-tag text-gray-400"></i>
                                </div>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('name') border-red-500 @enderror" 
                                       value="{{ old('name') }}" 
                                       placeholder="Ex: Nike, Adidas..."
                                       required>
                            </div>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                                Slug <span class="text-gray-400 text-xs">(généré automatiquement)</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-link text-gray-400"></i>
                                </div>
                                <input type="text" 
                                       name="slug" 
                                       id="slug" 
                                       class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('slug') border-red-500 @enderror" 
                                       value="{{ old('slug') }}" 
                                       readonly>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">URL conviviale générée à partir du nom</p>
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('description') border-red-500 @enderror" 
                                  rows="4"
                                  placeholder="Décrivez cette marque...">{{ old('description') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500 flex items-center justify-between">
                            <span>Description optionnelle de la marque</span>
                            <span id="charCount" class="text-gray-400">0/500</span>
                        </p>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                                Site web
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-globe text-gray-400"></i>
                                </div>
                                <input type="url" 
                                       name="website" 
                                       id="website" 
                                       class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('website') border-red-500 @enderror" 
                                       value="{{ old('website') }}" 
                                       placeholder="https://example.com">
                            </div>
                            @error('website')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                                Pays d'origine
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-flag text-gray-400"></i>
                                </div>
                                <input type="text" 
                                       name="country" 
                                       id="country" 
                                       class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('country') border-red-500 @enderror" 
                                       value="{{ old('country') }}"
                                       placeholder="Ex: FR, US, IT...">
                            </div>
                            @error('country')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="founded_year" class="block text-sm font-medium text-gray-700 mb-2">
                                Année de création
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar text-gray-400"></i>
                                </div>
                                <input type="number" 
                                       name="founded_year" 
                                       id="founded_year" 
                                       class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('founded_year') border-red-500 @enderror" 
                                       value="{{ old('founded_year') }}" 
                                       min="1800" 
                                       max="{{ date('Y') }}"
                                       placeholder="Ex: 1964">
                            </div>
                            @error('founded_year')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                Catégorie
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-layer-group text-gray-400"></i>
                                </div>
                                <select name="category" 
                                        id="category" 
                                        class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors appearance-none @error('category') border-red-500 @enderror">
                                    <option value="">Sélectionner une catégorie</option>
                                    <option value="luxury" {{ old('category') == 'luxury' ? 'selected' : '' }}>🌟 Luxe</option>
                                    <option value="streetwear" {{ old('category') == 'streetwear' ? 'selected' : '' }}>👟 Streetwear</option>
                                    <option value="vintage" {{ old('category') == 'vintage' ? 'selected' : '' }}>🕰️ Vintage</option>
                                    <option value="sports" {{ old('category') == 'sports' ? 'selected' : '' }}>⚽ Sport</option>
                                    <option value="casual" {{ old('category') == 'casual' ? 'selected' : '' }}>👕 Casual</option>
                                    <option value="formal" {{ old('category') == 'formal' ? 'selected' : '' }}>🎩 Formel</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Logo -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                            <i class="fas fa-image text-blue-600"></i>
                        </div>
                        Logo
                    </h3>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">
                            Logo de la marque
                        </label>
                        <div class="relative">
                            <input type="file" 
                                   name="logo" 
                                   id="logo" 
                                   class="hidden" 
                                   accept="image/*">
                            <div id="logo-upload-area" 
                                 class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-primary-500 hover:bg-primary-50 transition-all"
                                 onclick="document.getElementById('logo').click()">
                                <div id="logo-placeholder">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                    <p class="text-sm text-gray-600 font-medium">Cliquez pour télécharger</p>
                                    <p class="text-xs text-gray-500 mt-1">JPG, PNG, SVG (max 2MB)</p>
                                </div>
                            </div>
                        </div>
                        @error('logo')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div id="logo-preview" class="hidden">
                        <div class="relative rounded-lg overflow-hidden border border-gray-200">
                            <img id="logo-preview-img" src="" class="w-full h-auto">
                            <button type="button" 
                                    onclick="clearLogo()"
                                    class="absolute top-2 right-2 w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors shadow-lg">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 text-center mt-2">Prévisualisation du logo</p>
                    </div>
                </div>
            </div>
            
            <!-- Paramètres -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center mr-3">
                            <i class="fas fa-cog text-green-600"></i>
                        </div>
                        Paramètres
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" 
                                   name="is_active" 
                                   id="is_active" 
                                   value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                        </div>
                        <div class="ml-3">
                            <label for="is_active" class="font-medium text-gray-900 text-sm">Marque active</label>
                            <p class="text-xs text-gray-500 mt-0.5">Les marques inactives n'apparaissent pas sur le site</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" 
                                   name="is_featured" 
                                   id="is_featured" 
                                   value="1" 
                                   {{ old('is_featured') ? 'checked' : '' }}
                                   class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                        </div>
                        <div class="ml-3">
                            <label for="is_featured" class="font-medium text-gray-900 text-sm flex items-center">
                                Marque en vedette
                                <span class="ml-1.5 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-star mr-1"></i>Vedette
                                </span>
                            </label>
                            <p class="text-xs text-gray-500 mt-0.5">Apparaît dans la section marques vedettes</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Aide -->
            <div class="bg-blue-50 rounded-xl border border-blue-200 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-blue-900">Besoin d'aide ?</h4>
                        <p class="text-xs text-blue-700 mt-1">
                            Remplissez au minimum le nom de la marque pour créer une nouvelle entrée.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Actions -->
    <div class="mt-6 flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3 bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
        <a href="{{ route('admin.brands.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm sm:text-base text-gray-700 bg-white hover:bg-gray-50 transition-colors">
            <i class="fas fa-times mr-2"></i>Annuler
        </a>
        <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 bg-primary-600 border border-transparent rounded-lg font-medium text-sm sm:text-base text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
            <i class="fas fa-save mr-2"></i>Créer la marque
        </button>
    </div>
</form>
@endsection

@push('styles')
<style>
/* Améliorations responsive pour le formulaire */
@media (max-width: 640px) {
    /* Réduire les padding sur mobile */
    .p-6 {
        padding: 1rem !important;
    }
    
    /* Empiler les champs sur mobile */
    .grid-cols-2 {
        grid-template-columns: 1fr;
    }
    
    /* Adapter les tailles de police */
    input, textarea, select {
        font-size: 16px !important; /* Évite le zoom sur iOS */
    }
    
    /* Améliorer les zones de clic */
    button, a {
        min-height: 44px;
    }
}

@media (max-width: 768px) {
    /* Adapter la sidebar */
    .lg\\:col-span-2 {
        grid-column: span 1;
    }
    
    /* Empiler les actions */
    .flex.justify-between {
        flex-direction: column;
        gap: 0.75rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Génération automatique du slug
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    nameInput.addEventListener('input', function() {
        const slug = this.value
            .toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
        slugInput.value = slug;
    });
    
    // Compteur de caractères pour la description
    const descriptionInput = document.getElementById('description');
    const charCount = document.getElementById('charCount');
    
    descriptionInput.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = `${length}/500`;
        if (length > 500) {
            charCount.classList.add('text-red-600');
            charCount.classList.remove('text-gray-400');
        } else {
            charCount.classList.remove('text-red-600');
            charCount.classList.add('text-gray-400');
        }
    });
    
    // Prévisualisation du logo
    const logoInput = document.getElementById('logo');
    const logoPreview = document.getElementById('logo-preview');
    const logoPreviewImg = document.getElementById('logo-preview-img');
    const logoPlaceholder = document.getElementById('logo-placeholder');
    
    logoInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            // Vérifier la taille du fichier (2MB max)
            if (file.size > 2 * 1024 * 1024) {
                alert('Le fichier est trop volumineux. Taille maximale: 2MB');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                logoPreviewImg.src = e.target.result;
                logoPlaceholder.classList.add('hidden');
                logoPreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Validation du formulaire
    const form = document.querySelector('.needs-validation');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});

function clearLogo() {
    document.getElementById('logo').value = '';
    document.getElementById('logo-preview').classList.add('hidden');
    document.getElementById('logo-placeholder').classList.remove('hidden');
}
</script>
@endpush