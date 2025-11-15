@extends('app')
@section('title', 'creation-cat')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('categories.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-primary-600 transition-colors duration-200">
                        <i class="fas fa-layer-group mr-2"></i>
                        Catégories
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-sm font-medium text-primary-600">Ajouter une catégorie</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-plus text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Ajouter une catégorie</h1>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mt-1">Créez une nouvelle catégorie pour organiser vos produits</p>
                </div>
            </div>
        </div>

        <!-- Formulaire -->
        <div class="max-w-2xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-primary-600/10 border border-gray-100/50 overflow-hidden">
                <!-- Header du formulaire -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-edit text-primary-500 mr-2"></i>
                        Informations de la catégorie
                    </h3>
                </div>

                <div class="p-6">
                    <!-- Messages d'erreur -->
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Des erreurs ont été détectées :</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm text-red-700">{{ session('error') }}</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <!-- Nom -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                Nom <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('name') border-red-300 ring-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   value="{{ old('name') }}" 
                                   required 
                                   maxlength="100"
                                   placeholder="Ex: Vêtements, Électronique, Automobile...">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                Description
                            </label>
                            <textarea name="description" 
                                      id="description" 
                                      rows="3" 
                                      maxlength="500"
                                      class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 resize-none @error('description') border-red-300 ring-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                      placeholder="Description détaillée de la catégorie...">{{ old('description') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maximum 500 caractères</p>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Icône -->
                        <div>
                            <label for="icon" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                Icône FontAwesome
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center">
                                        <i id="icon-preview" class="{{ old('icon', 'fas fa-folder') }} text-primary-600"></i>
                                    </div>
                                </div>
                                <input type="text" 
                                       name="icon" 
                                       id="icon" 
                                       class="block w-full pl-16 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('icon') border-red-300 ring-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                       value="{{ old('icon') }}" 
                                       placeholder="fas fa-tshirt"
                                       maxlength="50">
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Utilisez les classes FontAwesome (ex: fas fa-tshirt, fas fa-laptop, fas fa-car)
                                <a href="https://fontawesome.com/icons" target="_blank" class="text-primary-600 hover:text-primary-800 underline ml-1">
                                    Voir la liste des icônes
                                </a>
                            </p>
                            @error('icon')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Image -->
                        <div>
                            <label for="image" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                Image de la catégorie
                            </label>
                            <div class="relative">
                                <input type="file" 
                                       name="image" 
                                       id="image" 
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-colors duration-200 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('image') border-red-300 ring-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Formats acceptés : JPEG, PNG, JPG, GIF, SVG. Taille maximale : 2MB.
                            </p>
                            
                            <!-- Prévisualisation de l'image -->
                            <div id="image-preview" class="mt-4 hidden">
                                <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700">
                                    <div class="flex-shrink-0">
                                        <img id="preview-img" src="" alt="Aperçu" class="w-24 h-24 object-cover rounded-xl border border-gray-300 dark:border-gray-600">
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Aperçu de l'image</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Cette image sera utilisée comme représentation visuelle de la catégorie</p>
                                    </div>
                                </div>
                            </div>
                            
                            @error('image')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Catégorie parente -->
                        <div>
                            <label for="parent_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                Catégorie parente (optionnel)
                            </label>
                            <select name="parent_id" 
                                    id="parent_id" 
                                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('parent_id') border-red-300 ring-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
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
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Sélectionnez une catégorie parente pour créer une sous-catégorie
                            </p>
                            @error('parent_id')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Ordre d'affichage -->
                        <div>
                            <label for="sort_order" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                Ordre d'affichage
                            </label>
                            <input type="number" 
                                   name="sort_order" 
                                   id="sort_order" 
                                   class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('sort_order') border-red-300 ring-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   value="{{ old('sort_order', 0) }}" 
                                   min="0"
                                   placeholder="0">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Plus le nombre est petit, plus la catégorie apparaîtra en premier dans la liste
                            </p>
                            @error('sort_order')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Statut actif -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-xl">
                            <div class="flex items-center">
                                <i class="fas fa-toggle-on text-primary-500 mr-3"></i>
                                <div>
                                    <label for="is_active" class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                        Catégorie active
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">La catégorie sera visible et utilisable</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       class="sr-only peer" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white dark:bg-gray-800 after:border-gray-300 dark:border-gray-600 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                            </label>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('categories.index') }}" 
                               class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 dark:bg-gray-900 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Annuler
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/25 hover:from-primary-600 hover:to-primary-700 hover:shadow-xl hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-300">
                                <i class="fas fa-save mr-2"></i>
                                Enregistrer
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
        const iconClass = this.value.trim() || 'fas fa-folder';
        iconPreview.className = iconClass + ' text-primary-600';
    });
    
    // Prévisualisation de l'image
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.classList.add('hidden');
        }
    });
});
</script>
@endsection