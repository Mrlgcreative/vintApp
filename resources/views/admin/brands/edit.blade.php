@extends('layouts.admin')

@section('title', 'Modifier la marque')
@section('page-title', 'Modifier : ' . $brand->name)

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.brands.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>Retour à la liste
    </a>
    <a href="{{ route('admin.brands.show', $brand) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-blue-300 text-blue-600 text-sm font-medium rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
        <i class="fas fa-eye mr-2"></i>Voir les détails
    </a>
</div>
@endsection

@section('content')
<form action="{{ route('admin.brands.update', $brand) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informations principales -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center mr-3">
                            <i class="fas fa-info-circle text-primary-600"></i>
                        </div>
                        Informations de la marque
                    </h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Nom de la marque <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-tag text-gray-400"></i>
                                </div>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       class="w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white transition-colors @error('name') border-red-500 @enderror" 
                                       value="{{ old('name', $brand->name) }}" 
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
                            <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Slug <span class="text-gray-400 text-xs">(généré automatiquement)</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-link text-gray-400"></i>
                                </div>
                                <input type="text" 
                                       name="slug" 
                                       id="slug" 
                                       class="w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:text-white transition-colors @error('slug') border-red-500 @enderror" 
                                       value="{{ old('slug', $brand->slug) }}" 
                                       readonly>
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">URL conviviale générée à partir du nom</p>
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            Description
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white transition-colors @error('description') border-red-500 @enderror" 
                                  rows="4"
                                  placeholder="Décrivez cette marque...">{{ old('description', $brand->description) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center justify-between">
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
                            <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Site web
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-globe text-gray-400"></i>
                                </div>
                                <input type="url" 
                                       name="website" 
                                       id="website" 
                                       class="w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white transition-colors @error('website') border-red-500 @enderror" 
                                       value="{{ old('website', $brand->website) }}" 
                                       placeholder="https://example.com">
                            </div>
                            @error('website')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Pays d'origine
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-flag text-gray-400"></i>
                                </div>
                                <input type="text" 
                                       name="country" 
                                       id="country" 
                                       class="w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white transition-colors @error('country') border-red-500 @enderror" 
                                       value="{{ old('country', $brand->country) }}"
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
                            <label for="founded_year" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Année de création
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar text-gray-400"></i>
                                </div>
                                <input type="number" 
                                       name="founded_year" 
                                       id="founded_year" 
                                       class="w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white transition-colors @error('founded_year') border-red-500 @enderror" 
                                       value="{{ old('founded_year', $brand->founded_year) }}" 
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
                            <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                Catégorie
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-layer-group text-gray-400"></i>
                                </div>
                                <select name="category" 
                                        id="category" 
                                        class="w-full pl-10 pr-10 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white appearance-none transition-colors @error('category') border-red-500 @enderror">
                                    <option value="">Sélectionner une catégorie</option>
                                    <option value="luxury" {{ old('category', $brand->category) == 'luxury' ? 'selected' : '' }}>🌟 Luxe</option>
                                    <option value="streetwear" {{ old('category', $brand->category) == 'streetwear' ? 'selected' : '' }}>👟 Streetwear</option>
                                    <option value="vintage" {{ old('category', $brand->category) == 'vintage' ? 'selected' : '' }}>🕰️ Vintage</option>
                                    <option value="sports" {{ old('category', $brand->category) == 'sports' ? 'selected' : '' }}>⚽ Sport</option>
                                    <option value="casual" {{ old('category', $brand->category) == 'casual' ? 'selected' : '' }}>👕 Casual</option>
                                    <option value="formal" {{ old('category', $brand->category) == 'formal' ? 'selected' : '' }}>🎩 Formel</option>
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
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                            <i class="fas fa-image text-blue-600"></i>
                        </div>
                        Logo
                    </h3>
                </div>
                <div class="p-6">
                    @if($brand->logo)
                        <div class="mb-4 text-center">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Logo actuel</p>
                            <img src="{{ $brand->logo_url }}" 
                                 class="w-32 h-32 object-contain rounded-lg border border-gray-200 dark:border-gray-700 bg-white p-2 mx-auto" 
                                 alt="Logo {{ $brand->name }}">
                        </div>
                    @endif
                    
                    <div class="mb-4">
                        <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            {{ $brand->logo ? 'Changer le logo' : 'Ajouter un logo' }}
                        </label>
                        <div class="relative">
                            <input type="file" 
                                   name="logo" 
                                   id="logo" 
                                   class="hidden" 
                                   accept="image/*">
                            <div id="logo-upload-area" 
                                 class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center cursor-pointer hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/10 transition-all"
                                 onclick="document.getElementById('logo').click()">
                                <div id="logo-placeholder">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 font-medium">Cliquez pour télécharger</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">JPG, PNG, SVG (max 2MB)</p>
                                </div>
                            </div>
                        </div>
                        @error('logo')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div id="logo-preview" class="hidden mb-4">
                        <div class="relative rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                            <img id="logo-preview-img" src="" class="w-full h-auto">
                            <button type="button" 
                                    onclick="clearLogo()"
                                    class="absolute top-2 right-2 w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors shadow-lg">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-2">Nouvelle image sélectionnée</p>
                    </div>
                    
                    @if($brand->logo)
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" 
                                       name="remove_logo" 
                                       id="remove_logo" 
                                       value="1"
                                       class="w-4 h-4 text-red-600 border-gray-300 dark:border-gray-600 rounded focus:ring-red-500">
                            </div>
                            <div class="ml-3">
                                <label for="remove_logo" class="font-medium text-red-600 text-sm">Supprimer le logo actuel</label>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Paramètres -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
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
                                   {{ old('is_active', $brand->is_active) ? 'checked' : '' }}
                                   class="w-4 h-4 text-primary-600 border-gray-300 dark:border-gray-600 rounded focus:ring-primary-500">
                        </div>
                        <div class="ml-3">
                            <label for="is_active" class="font-medium text-gray-900 dark:text-white text-sm">Marque active</label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Les marques inactives n'apparaissent pas sur le site</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" 
                                   name="is_featured" 
                                   id="is_featured" 
                                   value="1" 
                                   {{ old('is_featured', $brand->is_featured) ? 'checked' : '' }}
                                   class="w-4 h-4 text-yellow-600 border-gray-300 dark:border-gray-600 rounded focus:ring-yellow-500">
                        </div>
                        <div class="ml-3">
                            <label for="is_featured" class="font-medium text-gray-900 dark:text-white text-sm flex items-center">
                                Marque en vedette
                                <span class="ml-1.5 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-star mr-1"></i>Vedette
                                </span>
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Apparaît dans la section marques vedettes</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Statistiques -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center mr-3">
                            <i class="fas fa-chart-bar text-purple-600"></i>
                        </div>
                        Statistiques
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-2xl font-bold text-primary-600">{{ $brand->items_count ?? 0 }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Articles</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $brand->orders_count ?? 0 }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Commandes</div>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Créée le</span>
                            <span class="text-gray-900 dark:text-white">{{ $brand->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Modifiée le</span>
                            <span class="text-gray-900 dark:text-white">{{ $brand->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Actions -->
    <div class="mt-6 flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
        <button type="button" onclick="confirmDelete()" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-red-700 transition-colors">
            <i class="fas fa-trash mr-2"></i>Supprimer la marque
        </button>
        
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('admin.brands.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-times mr-2"></i>Annuler
            </a>
            <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 bg-green-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                <i class="fas fa-save mr-2"></i>Mettre à jour
            </button>
        </div>
    </div>
</form>

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDeleteModal()"></div>
    
    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <!-- Header -->
            <div class="bg-red-50 dark:bg-red-900/20 px-6 py-4 border-b border-red-100 dark:border-red-800">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-red-800 dark:text-red-200 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Confirmer la suppression
                    </h3>
                    <button type="button" onclick="closeDeleteModal()" class="text-red-400 hover:text-red-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Body -->
            <div class="px-6 py-4">
                <p class="text-gray-700 dark:text-gray-200 mb-4">
                    Êtes-vous sûr de vouloir supprimer la marque 
                    <strong class="text-gray-900 dark:text-white">{{ $brand->name }}</strong> ?
                </p>
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 rounded">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700 dark:text-yellow-200 font-medium">
                                Cette action est <strong>irréversible</strong> et supprimera :
                            </p>
                            <ul class="mt-2 text-sm text-yellow-700 dark:text-yellow-200 list-disc list-inside space-y-1">
                                <li>Toutes les informations de la marque</li>
                                <li>Les associations avec les articles</li>
                                <li>L'historique et les statistiques</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 flex flex-col-reverse sm:flex-row justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors w-full sm:w-auto">
                    <i class="fas fa-times mr-2"></i>Annuler
                </button>
                <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex justify-center items-center w-full px-4 py-2 bg-red-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>Supprimer définitivement
                    </button>
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
    
    function updateCharCount() {
        const length = descriptionInput.value.length;
        charCount.textContent = `${length}/500`;
        if (length > 500) {
            charCount.classList.add('text-red-600');
            charCount.classList.remove('text-gray-400');
        } else {
            charCount.classList.remove('text-red-600');
            charCount.classList.add('text-gray-400');
        }
    }
    
    descriptionInput.addEventListener('input', updateCharCount);
    updateCharCount(); // Initialiser le compteur
    
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
});

function clearLogo() {
    document.getElementById('logo').value = '';
    document.getElementById('logo-preview').classList.add('hidden');
    document.getElementById('logo-placeholder').classList.remove('hidden');
}

function confirmDelete() {
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Fermer le modal avec Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDeleteModal();
    }
});
</script>
@endpush
