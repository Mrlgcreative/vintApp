@extends('layouts.admin')

@section('title', 'Modifier la catégorie')
@section('page-title', 'Modifier : ' . $category->name)

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.categories.index') }}"
       class="inline-flex items-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
        <i class="fas fa-arrow-left"></i>Retour à la liste
    </a>
    <a href="{{ route('admin.categories.show', $category) }}"
       class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-eye"></i>Voir
    </a>
    <button type="button"
            data-bs-toggle="modal"
            data-bs-target="#deleteModal"
            class="inline-flex items-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-trash"></i>Supprimer
    </button>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="p-6 sm:p-8">
                <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" id="categoryForm">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Informations principales -->
                        <div class="lg:col-span-2 space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                                    Nom de la catégorie <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('name') border-red-500 @enderror"
                                       id="name" name="name" value="{{ old('name', $category->name) }}" required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Le nom de la catégorie (ex: "Accessoires", "Vêtements")</p>
                            </div>

                            <div>
                                <label for="slug" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                                    Slug <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('slug') border-red-500 @enderror"
                                       id="slug" name="slug" value="{{ old('slug', $category->slug) }}" required>
                                @error('slug')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">URL conviviale (modifiez avec précaution)</p>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Description</label>
                                <textarea class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('description') border-red-500 @enderror"
                                          id="description" name="description" rows="4">{{ old('description', $category->description) }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Description détaillée de la catégorie</p>
                            </div>
                        </div>

                        <!-- Image -->
                        <div class="lg:col-span-1 space-y-4">
                            <div>
                                <label for="image" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Image de la catégorie</label>

                                @if($category->image)
                                    <div class="current-image mb-3">
                                        <img src="{{ $category->image_url }}" class="rounded-xl border border-slate-200 dark:border-slate-700 w-full object-cover" alt="Image actuelle">
                                        <div class="mt-2">
                                            <label class="flex items-center gap-2 text-sm text-red-600 cursor-pointer">
                                                <input class="w-4 h-4 text-red-600 bg-slate-100 dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded focus:ring-red-500 focus:ring-2" type="checkbox" id="remove_image" name="remove_image" value="1">
                                                Supprimer l'image actuelle
                                            </label>
                                        </div>
                                    </div>
                                @endif

                                <input type="file"
                                       class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('image') border-red-500 @enderror"
                                       id="image" name="image" accept="image/*">
                                @error('image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Format recommandé: JPG, PNG (max 2MB)</p>
                            </div>

                            <div id="imagePreview" class="hidden">
                                <img id="previewImg" src="" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm" alt="Aperçu">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Catégorie parente -->
                        <div>
                            <label for="parent_id" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Catégorie parente</label>
                            <select class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-800 focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('parent_id') border-red-500 @enderror" id="parent_id" name="parent_id">
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
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Sélectionnez une catégorie parente pour créer une sous-catégorie</p>
                        </div>

                        <!-- Ordre d'affichage -->
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Ordre d'affichage</label>
                            <input type="number"
                                   class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('sort_order') border-red-500 @enderror"
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0">
                            @error('sort_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Plus le nombre est petit, plus la catégorie apparaît en premier</p>
                        </div>
                    </div>

                    <!-- Couleur et icône -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="color" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Couleur</label>
                            <div class="flex gap-3">
                                <input type="color"
                                       class="w-16 h-10 border border-slate-300 dark:border-slate-600 rounded-xl cursor-pointer @error('color') border-red-500 @enderror"
                                       id="color" name="color" value="{{ old('color', $category->color ?? '#007bff') }}">
                                <input type="text"
                                       class="flex-1 px-3.5 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200"
                                       id="colorHex" readonly>
                            </div>
                            @error('color')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Couleur associée à la catégorie pour l'affichage</p>
                        </div>

                        <div>
                            <label for="icon" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Icône</label>
                            <div class="flex gap-0">
                                <div class="flex items-center justify-center px-3 bg-slate-50 dark:bg-slate-900 border border-r-0 border-slate-300 dark:border-slate-600 rounded-l-xl">
                                    <i id="iconPreview" class="{{ $category->icon ?? 'fas fa-tag' }} text-slate-500 dark:text-slate-400"></i>
                                </div>
                                <input type="text"
                                       class="flex-1 px-3.5 py-2.5 border border-slate-300 dark:border-slate-600 rounded-r-xl focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('icon') border-red-500 @enderror"
                                       id="icon" name="icon" value="{{ old('icon', $category->icon ?? 'fas fa-tag') }}"
                                       placeholder="fas fa-tag">
                            </div>
                            @error('icon')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Classe CSS FontAwesome (ex: fas fa-tshirt)</p>
                        </div>
                    </div>

                    <!-- Meta données SEO -->
                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 mt-8">
                        <div class="flex items-center gap-2 mb-6">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-search text-blue-600"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">SEO et Meta données</h3>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label for="meta_title" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Titre meta</label>
                                <input type="text"
                                       class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('meta_title') border-red-500 @enderror"
                                       id="meta_title" name="meta_title" value="{{ old('meta_title', $category->meta_title) }}" maxlength="60">
                                @error('meta_title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Titre pour les moteurs de recherche (60 caractères max)</p>
                            </div>

                            <div>
                                <label for="meta_description" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Description meta</label>
                                <textarea class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('meta_description') border-red-500 @enderror"
                                          id="meta_description" name="meta_description" rows="3" maxlength="160">{{ old('meta_description', $category->meta_description) }}</textarea>
                                @error('meta_description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Description pour les moteurs de recherche (160 caractères max)</p>
                            </div>

                            <div>
                                <label for="meta_keywords" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Mots-clés</label>
                                <input type="text"
                                       class="w-full px-3.5 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('meta_keywords') border-red-500 @enderror"
                                       id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $category->meta_keywords) }}">
                                @error('meta_keywords')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Mots-clés séparés par des virgules</p>
                            </div>
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 mt-8">
                        <div class="flex items-center gap-2 mb-6">
                            <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-cog text-emerald-600"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Options</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="flex items-center gap-3">
                                    <input type="checkbox" value="1"
                                           id="is_active" name="is_active" {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary-600 bg-slate-100 dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded focus:ring-primary-500 focus:ring-2">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-200">Catégorie active</span>
                                </label>
                                <p class="text-sm text-slate-500 dark:text-slate-400">La catégorie sera visible sur le site</p>
                            </div>

                            <div class="space-y-2">
                                <label class="flex items-center gap-3">
                                    <input type="checkbox" value="1"
                                           id="is_featured" name="is_featured" {{ old('is_featured', $category->is_featured) ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary-600 bg-slate-100 dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded focus:ring-primary-500 focus:ring-2">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-200">Catégorie en vedette</span>
                                </label>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Afficher dans les catégories principales</p>
                            </div>

                            <div class="space-y-2">
                                <label class="flex items-center gap-3">
                                    <input type="checkbox" value="1"
                                           id="show_in_menu" name="show_in_menu" {{ old('show_in_menu', $category->show_in_menu) ? 'checked' : '' }}
                                           class="w-4 h-4 text-primary-600 bg-slate-100 dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded focus:ring-primary-500 focus:ring-2">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-200">Afficher dans le menu</span>
                                </label>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Inclure dans la navigation du site</p>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques (si des données existent) -->
                    @if($category->items_count || $category->subcategories_count)
                        <div class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 mt-8">
                            <div class="flex items-center gap-2 mb-6">
                                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-chart-bar text-indigo-600"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Statistiques</h3>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                                <div class="p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                                    <div class="text-2xl font-bold text-primary-600">{{ $category->items_count ?? 0 }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Articles</div>
                                </div>
                                <div class="p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                                    <div class="text-2xl font-bold text-emerald-600">{{ $category->subcategories_count ?? 0 }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Sous-catégories</div>
                                </div>
                                <div class="p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                                    <div class="text-2xl font-bold text-blue-600">{{ $category->active_items_count ?? 0 }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Articles actifs</div>
                                </div>
                                <div class="p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                                    <div class="text-2xl font-bold text-amber-600">{{ $category->views_count ?? 0 }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Vues</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Boutons d'action -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
                        <button type="button"
                                class="inline-flex items-center gap-2 px-4 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                                onclick="history.back()">
                            <i class="fas fa-times"></i>Annuler
                        </button>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="submit" name="action" value="save"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 border border-transparent rounded-xl text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 shadow-sm transition-colors">
                                <i class="fas fa-save"></i>Mettre à jour
                            </button>
                            <button type="submit" name="action" value="save_and_continue"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 border border-transparent rounded-xl text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition-colors">
                                <i class="fas fa-save"></i>Mettre à jour et continuer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="modal-wrapper hidden fixed inset-0 z-50 flex items-center justify-center p-4" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
    <div class="relative w-full max-w-md rounded-2xl bg-white dark:bg-slate-800 shadow-2xl ring-1 ring-slate-200 dark:ring-slate-700 animate-pop">
        <div class="bg-red-50 dark:bg-red-900/20 px-6 py-4 border-b border-red-100 dark:border-red-800 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-red-800 dark:text-red-200 flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Confirmer la suppression
            </h3>
            <button type="button" data-bs-dismiss="modal" class="text-red-400 hover:text-red-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="px-6 py-4">
            <p class="text-slate-700 dark:text-slate-200 mb-4">
                Êtes-vous sûr de vouloir supprimer la catégorie <strong class="text-slate-900 dark:text-white">{{ $category->name }}</strong> ?
            </p>
            @if($category->items_count > 0)
                <div class="flex items-start gap-3 bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-400 p-4 rounded-xl mb-3">
                    <i class="fas fa-exclamation-triangle text-amber-400 mt-0.5"></i>
                    <p class="text-sm text-amber-700 dark:text-amber-200">
                        Cette catégorie contient {{ $category->items_count }} article(s).
                        La suppression réassignera ces articles à la catégorie par défaut.
                    </p>
                </div>
            @endif
            @if($category->subcategories_count > 0)
                <div class="flex items-start gap-3 bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-400 p-4 rounded-xl mb-3">
                    <i class="fas fa-exclamation-triangle text-amber-400 mt-0.5"></i>
                    <p class="text-sm text-amber-700 dark:text-amber-200">
                        Cette catégorie contient {{ $category->subcategories_count }} sous-catégorie(s).
                    </p>
                </div>
            @endif
            <p class="text-sm text-red-600 dark:text-red-400">Cette action est irréversible.</p>
        </div>
        <div class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 flex flex-col-reverse sm:flex-row justify-end gap-3">
            <button type="button" data-bs-dismiss="modal" class="inline-flex justify-center items-center gap-2 px-4 py-2.5 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                <i class="fas fa-times"></i>Annuler
            </button>
            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="w-full sm:w-auto">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex justify-center items-center gap-2 w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 border border-transparent rounded-xl text-sm font-medium text-white shadow-sm transition-colors">
                    <i class="fas fa-trash"></i>Supprimer définitivement
                </button>
            </form>
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
                imagePreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.classList.add('hidden');
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
</script>
@endpush
