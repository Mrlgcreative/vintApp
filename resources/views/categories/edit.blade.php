@extends('app')

@section('content')
@php
use Illuminate\Support\Facades\Storage;
@endphp
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('categories.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-amber-600 transition-colors duration-200">
                        <i class="fas fa-layer-group mr-2"></i>
                        Catégories
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-sm font-medium text-amber-600">Modifier {{ $category->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-edit text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Modifier la catégorie</h1>
                    <p class="text-gray-600 text-sm mt-1">Modifiez les informations de "{{ $category->name }}"</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Formulaire principal -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl shadow-amber-600/10 border border-gray-100/50 overflow-hidden">
                    <!-- Header du formulaire -->
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-edit text-amber-500 mr-2"></i>
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

                        <form action="{{ route('categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            @method('PUT')
                            
                            <!-- Nom -->
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Nom <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       class="block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors duration-200 @error('name') border-red-300 ring-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                       value="{{ old('name', $category->name) }}" 
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
                                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Description
                                </label>
                                <textarea name="description" 
                                          id="description" 
                                          rows="3" 
                                          maxlength="500"
                                          class="block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors duration-200 resize-none @error('description') border-red-300 ring-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                          placeholder="Description détaillée de la catégorie...">{{ old('description', $category->description) }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">Maximum 500 caractères</p>
                                @error('description')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Icône -->
                            <div>
                                <label for="icon" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Icône FontAwesome
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                                            <i id="icon-preview" class="{{ old('icon', $category->icon ?: 'fas fa-folder') }} text-amber-600"></i>
                                        </div>
                                    </div>
                                    <input type="text" 
                                           name="icon" 
                                           id="icon" 
                                           class="block w-full pl-16 pr-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors duration-200 @error('icon') border-red-300 ring-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                           value="{{ old('icon', $category->icon) }}" 
                                           placeholder="fas fa-tshirt"
                                           maxlength="50">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    Utilisez les classes FontAwesome (ex: fas fa-tshirt, fas fa-laptop, fas fa-car)
                                    <a href="https://fontawesome.com/icons" target="_blank" class="text-amber-600 hover:text-amber-800 underline ml-1">
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
                                <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Image de la catégorie
                                </label>
                                
                                @if($category->image && Storage::disk('public')->exists($category->image))
                                    <div class="mb-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                <div class="w-20 h-20 bg-white rounded-xl shadow-sm border border-gray-200 flex items-center justify-center overflow-hidden">
                                                    <img src="{{ Storage::url($category->image) }}" 
                                                         alt="{{ $category->name }}" 
                                                         class="w-full h-full object-cover">
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900">Image actuelle</p>
                                                <p class="text-xs text-gray-500">Sélectionnez un nouveau fichier pour la remplacer</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="relative">
                                    <input type="file" 
                                           name="image" 
                                           id="image" 
                                           accept="image/*"
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 transition-colors duration-200 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('image') border-red-300 ring-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    Formats acceptés : JPEG, PNG, JPG, GIF, SVG. Taille maximale : 2MB.
                                    @if($category->image)
                                        <br>Laissez vide pour conserver l'image actuelle.
                                    @endif
                                </p>
                                
                                <!-- Prévisualisation de la nouvelle image -->
                                <div id="image-preview" class="mt-4 hidden">
                                    <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                                        <div class="flex-shrink-0">
                                            <img id="preview-img" src="" alt="Aperçu" class="w-20 h-20 object-cover rounded-xl border border-gray-300">
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">Nouvelle image</p>
                                            <p class="text-xs text-gray-500">Cette image remplacera l'image actuelle</p>
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
                                <label for="parent_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Catégorie parente (optionnel)
                                </label>
                                <select name="parent_id" 
                                        id="parent_id" 
                                        class="block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors duration-200 @error('parent_id') border-red-300 ring-red-500 focus:ring-red-500 focus:border-red-500 @enderror">
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
                                <p class="mt-1 text-xs text-gray-500">
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
                                <label for="sort_order" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Ordre d'affichage
                                </label>
                                <input type="number" 
                                       name="sort_order" 
                                       id="sort_order" 
                                       class="block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors duration-200 @error('sort_order') border-red-300 ring-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                       value="{{ old('sort_order', $category->sort_order ?? 0) }}" 
                                       min="0"
                                       placeholder="0">
                                <p class="mt-1 text-xs text-gray-500">
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
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div class="flex items-center">
                                    <i class="fas fa-toggle-on text-amber-500 mr-3"></i>
                                    <div>
                                        <label for="is_active" class="text-sm font-semibold text-gray-700">
                                            Catégorie active
                                        </label>
                                        <p class="text-xs text-gray-500">La catégorie sera visible et utilisable</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1"
                                           class="sr-only peer" 
                                           {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
                                </label>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="flex flex-col sm:flex-row sm:justify-between gap-3 pt-6 border-t border-gray-200">
                                <a href="{{ route('categories.show', $category) }}" 
                                   class="inline-flex items-center justify-center px-6 py-3 border border-blue-300 text-blue-700 font-semibold rounded-xl hover:bg-blue-50 hover:border-blue-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                    <i class="fas fa-eye mr-2"></i>
                                    Voir la catégorie
                                </a>
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <a href="{{ route('categories.index') }}" 
                                       class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Annuler
                                    </a>
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-amber-500 to-amber-600 text-white font-semibold rounded-xl shadow-lg shadow-amber-500/25 hover:from-amber-600 hover:to-amber-700 hover:shadow-xl hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all duration-300">
                                        <i class="fas fa-save mr-2"></i>
                                        Mettre à jour
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Informations supplémentaires -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl shadow-amber-600/10 border border-gray-100/50 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-info-circle text-amber-500 mr-2"></i>
                            Informations
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Articles dans cette catégorie -->
                        <div class="flex items-center justify-between p-4 bg-blue-50 rounded-xl">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-box text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Articles</p>
                                    <p class="text-xs text-gray-500">Dans cette catégorie</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                {{ $category->items_count ?? 0 }}
                            </span>
                        </div>

                        <!-- Date de création -->
                        <div class="flex items-center justify-between p-4 bg-emerald-50 rounded-xl">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-calendar-plus text-emerald-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Créée le</p>
                                    <p class="text-xs text-gray-500">{{ $category->created_at?->format('d/m/Y H:i') ?? 'Inconnue' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Date de modification -->
                        @if($category->updated_at && $category->updated_at != $category->created_at)
                        <div class="flex items-center justify-between p-4 bg-amber-50 rounded-xl">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-calendar-edit text-amber-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Modifiée le</p>
                                    <p class="text-xs text-gray-500">{{ $category->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Catégorie parente actuelle -->
                        @if($category->parent)
                        <div class="flex items-center justify-between p-4 bg-primary-50 rounded-xl">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-level-up-alt text-primary-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Catégorie parente</p>
                                    <p class="text-xs text-gray-500">{{ $category->parent->name }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
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
        iconPreview.className = iconClass + ' text-amber-600';
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