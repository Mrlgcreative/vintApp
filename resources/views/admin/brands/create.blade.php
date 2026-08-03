@extends('layouts.admin')

@section('title', 'Créer une marque')
@section('page-title', 'Créer une marque')

@section('page-actions')
<a href="{{ route('admin.brands.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
    <i class="fas fa-arrow-left"></i>
    <span class="hidden sm:inline">Retour à la liste</span>
    <span class="sm:hidden">Retour</span>
</a>
@endsection

@section('content')
<form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informations principales -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="flex items-center gap-3 font-semibold text-slate-900 dark:text-white">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-900/30">
                            <i class="fas fa-info-circle text-primary-600 dark:text-primary-300"></i>
                        </span>
                        Informations de la marque
                    </h3>
                </div>
                <div class="p-5 sm:p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Nom de la marque <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="fas fa-tag text-slate-400"></i>
                                </div>
                                <input type="text"
                                       name="name"
                                       id="name"
                                       class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('name') border-red-500 focus:ring-red-500/40 @enderror"
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
                            <label for="slug" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Slug <span class="text-slate-400 text-xs">(généré automatiquement)</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="fas fa-link text-slate-400"></i>
                                </div>
                                <input type="text"
                                       name="slug"
                                       id="slug"
                                       class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('slug') border-red-500 focus:ring-red-500/40 @enderror"
                                       value="{{ old('slug') }}"
                                       readonly>
                            </div>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">URL conviviale générée à partir du nom</p>
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Description
                        </label>
                        <textarea name="description"
                                  id="description"
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('description') border-red-500 focus:ring-red-500/40 @enderror"
                                  rows="4"
                                  placeholder="Décrivez cette marque...">{{ old('description') }}</textarea>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 flex items-center justify-between">
                            <span>Description optionnelle de la marque</span>
                            <span id="charCount" class="text-slate-400">0/500</span>
                        </p>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="website" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Site web
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="fas fa-globe text-slate-400"></i>
                                </div>
                                <input type="url"
                                       name="website"
                                       id="website"
                                       class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('website') border-red-500 focus:ring-red-500/40 @enderror"
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
                            <label for="country" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Pays d'origine
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="fas fa-flag text-slate-400"></i>
                                </div>
                                <input type="text"
                                       name="country"
                                       id="country"
                                       class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('country') border-red-500 focus:ring-red-500/40 @enderror"
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
                            <label for="founded_year" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Année de création
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar text-slate-400"></i>
                                </div>
                                <input type="number"
                                       name="founded_year"
                                       id="founded_year"
                                       class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors @error('founded_year') border-red-500 focus:ring-red-500/40 @enderror"
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
                            <label for="category" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Catégorie
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="fas fa-layer-group text-slate-400"></i>
                                </div>
                                <select name="category"
                                        id="category"
                                        class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors appearance-none @error('category') border-red-500 focus:ring-red-500/40 @enderror">
                                    <option value="">Sélectionner une catégorie</option>
                                    <option value="luxury" {{ old('category') == 'luxury' ? 'selected' : '' }}>🌟 Luxe</option>
                                    <option value="streetwear" {{ old('category') == 'streetwear' ? 'selected' : '' }}>👟 Streetwear</option>
                                    <option value="vintage" {{ old('category') == 'vintage' ? 'selected' : '' }}>🕰️ Vintage</option>
                                    <option value="sports" {{ old('category') == 'sports' ? 'selected' : '' }}>⚽ Sport</option>
                                    <option value="casual" {{ old('category') == 'casual' ? 'selected' : '' }}>👕 Casual</option>
                                    <option value="formal" {{ old('category') == 'formal' ? 'selected' : '' }}>🎩 Formel</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-chevron-down text-slate-400"></i>
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
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="flex items-center gap-3 font-semibold text-slate-900 dark:text-white">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                            <i class="fas fa-image text-blue-600 dark:text-blue-300"></i>
                        </span>
                        Logo
                    </h3>
                </div>
                <div class="p-5 sm:p-6">
                    <div class="mb-4">
                        <label for="logo" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Logo de la marque
                        </label>
                        <div class="relative">
                            <input type="file"
                                   name="logo"
                                   id="logo"
                                   class="hidden"
                                   accept="image/*">
                            <div id="logo-upload-area"
                                 class="rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-600 p-6 text-center cursor-pointer hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/10 transition-all"
                                 onclick="document.getElementById('logo').click()">
                                <div id="logo-placeholder">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-slate-400 mb-3"></i>
                                    <p class="text-sm text-slate-600 dark:text-slate-300 font-medium">Cliquez pour télécharger</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">JPG, PNG, SVG (max 2MB)</p>
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
                        <div class="relative rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700">
                            <img id="logo-preview-img" src="" class="w-full h-auto">
                            <button type="button"
                                    onclick="clearLogo()"
                                    class="absolute top-2 right-2 w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors shadow-lg">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 text-center mt-2">Prévisualisation du logo</p>
                    </div>
                </div>
            </div>

            <!-- Paramètres -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="flex items-center gap-3 font-semibold text-slate-900 dark:text-white">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/30">
                            <i class="fas fa-cog text-emerald-600 dark:text-emerald-300"></i>
                        </span>
                        Paramètres
                    </h3>
                </div>
                <div class="p-5 sm:p-6 space-y-4">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox"
                                   name="is_active"
                                   id="is_active"
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="w-4 h-4 text-primary-600 border-slate-300 dark:border-slate-600 rounded focus:ring-primary-500">
                        </div>
                        <div class="ml-3">
                            <label for="is_active" class="font-medium text-slate-900 dark:text-white text-sm">Marque active</label>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Les marques inactives n'apparaissent pas sur le site</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox"
                                   name="is_featured"
                                   id="is_featured"
                                   value="1"
                                   {{ old('is_featured') ? 'checked' : '' }}
                                   class="w-4 h-4 text-yellow-600 border-slate-300 dark:border-slate-600 rounded focus:ring-yellow-500">
                        </div>
                        <div class="ml-3">
                            <label for="is_featured" class="font-medium text-slate-900 dark:text-white text-sm flex items-center">
                                Marque en vedette
                                <span class="ml-1.5 inline-flex items-center rounded-full bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300 px-2.5 py-0.5 text-xs font-medium">
                                    <i class="fas fa-star mr-1"></i>Vedette
                                </span>
                            </label>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Apparaît dans la section marques vedettes</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aide -->
            <div class="rounded-2xl border border-sky-200 dark:border-sky-800 bg-sky-50 dark:bg-sky-900/20 px-4 py-3 text-sm text-sky-800 dark:text-sky-300">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-sky-600 dark:text-sky-300"></i>
                    </div>
                    <div>
                        <h4 class="font-medium">Besoin d'aide ?</h4>
                        <p class="mt-1">
                            Remplissez au minimum le nom de la marque pour créer une nouvelle entrée.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="mt-6 flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-4 sm:p-6">
        <a href="{{ route('admin.brands.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
            <i class="fas fa-times"></i>Annuler
        </a>
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-6 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
            <i class="fas fa-save"></i>Créer la marque
        </button>
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
            charCount.classList.remove('text-slate-400');
        } else {
            charCount.classList.remove('text-red-600');
            charCount.classList.add('text-slate-400');
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
        // Retirer novalidate pour permettre la soumission native
        // La validation sera gérée côté serveur
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Création en cours...';
        }
    });
});

function clearLogo() {
    document.getElementById('logo').value = '';
    document.getElementById('logo-preview').classList.add('hidden');
    document.getElementById('logo-placeholder').classList.remove('hidden');
}
</script>
@endpush
