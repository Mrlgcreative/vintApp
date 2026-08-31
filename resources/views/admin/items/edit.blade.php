@extends('layouts.admin')

@section('title', 'Modifier l\'item #' . $item->id)
@section('page-title', 'Modifier l\'item #' . $item->id)
@section('page-subtitle', $item->name)

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.items.show', $item) }}"
       class="inline-flex items-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
        <i class="fas fa-eye"></i>
        <span class="hidden sm:inline">Voir</span>
    </a>
    <a href="{{ route('admin.items.index') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-arrow-left"></i>
        <span class="hidden sm:inline">Retour à la liste</span>
        <span class="sm:hidden">Retour</span>
    </a>
</div>
@endsection

@push('styles')
<style>
    .form-label { font-size: .875rem; font-weight: 500; color: #334155; }
    .dark .form-label { color: #cbd5e1; }
    .form-control {
        width: 100%;
        border-radius: .75rem;
        border: 1px solid #cbd5e1;
        background: #fff;
        padding: .625rem .875rem;
        font-size: .875rem;
        color: #0f172a;
        transition: border-color .15s, box-shadow .15s;
        outline: none;
    }
    .dark .form-control { background: #0f172a; border-color: #475569; color: #f1f5f9; }
    .form-control:focus {
        border-color: var(--primary-color, #6366f1);
        box-shadow: 0 0 0 2px rgba(99, 102, 241, .25);
    }
    .form-control:disabled { background: #f1f5f9; cursor: not-allowed; }
    .dark .form-control:disabled { background: #1e293b; }
    .input-error { border-color: #ef4444 !important; }
    .err-msg { font-size: .75rem; color: #ef4444; margin-top: .25rem; }
    .img-preview-box {
        position: relative; overflow: hidden; border-radius: .75rem;
        border: 2px solid #e2e8f0; background: #f8fafc;
    }
    .dark .img-preview-box { border-color: #334155; background: #1e293b; }
    .remove-img-btn {
        position: absolute; top: .4rem; right: .4rem;
        width: 1.5rem; height: 1.5rem; border-radius: 9999px;
        background: rgba(239, 68, 68, .9); color: #fff; font-size: .8rem;
        display: flex; align-items: center; justify-content: center; cursor: pointer; border: none;
    }
    .remove-img-btn:hover { background: #dc2626; }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto">
    @if($errors->any())
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/30 dark:bg-red-900/20 dark:text-red-300">
            <ul class="list-disc space-y-1 pl-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.items.update', $item) }}" method="POST" enctype="multipart/form-data" id="itemForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-4 lg:space-y-6">
                <!-- Informations de base -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                        <h2 class="font-semibold text-slate-900 dark:text-white">Informations générales</h2>
                    </div>
                    <div class="p-5 sm:p-6 space-y-5">
                        <div>
                            <label class="form-label" for="name">Nom de l'article</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $item->name) }}"
                                   class="form-control @error('name') input-error @enderror" required>
                            @error('name') <p class="err-msg">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="form-label" for="description">Description</label>
                            <textarea id="description" name="description" rows="5"
                                      class="form-control @error('description') input-error @enderror" required>{{ old('description', $item->description) }}</textarea>
                            @error('description') <p class="err-msg">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="form-label" for="price">Prix</label>
                                <input type="number" id="price" name="price" step="0.01" min="0"
                                       value="{{ old('price', $item->price) }}"
                                       class="form-control @error('price') input-error @enderror" required>
                                @error('price') <p class="err-msg">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="form-label" for="currency">Devise</label>
                                <select id="currency" name="currency" class="form-control" required>
                                    @foreach(['USD' => 'USD ($)', 'CDF' => 'CDF (FC)'] as $val => $lab)
                                        <option value="{{ $val }}" @selected(old('currency', $item->currency) === $val)>{{ $lab }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label" for="quantity">Quantité</label>
                                <input type="number" id="quantity" name="quantity" min="1"
                                       value="{{ old('quantity', $item->quantity) }}"
                                       class="form-control @error('quantity') input-error @enderror" required>
                                @error('quantity') <p class="err-msg">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Catégorie, marque, condition -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                        <h2 class="font-semibold text-slate-900 dark:text-white">Classification</h2>
                    </div>
                    <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label class="form-label" for="category_id">Catégorie</label>
                            <select id="category_id" name="category_id" class="form-control" required>
                                <option value="">-- Sélectionner --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('category_id', $item->category_id) == $cat->id)>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <p class="err-msg">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label" for="brand_id">Marque</label>
                            <select id="brand_id" name="brand_id" class="form-control">
                                <option value="">-- Sélectionner --</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" @selected(old('brand_id', $item->brand_id) == $brand->id)>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                            @error('brand_id') <p class="err-msg">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label" for="condition">État</label>
                            <select id="condition" name="condition" class="form-control" required>
                                @php $conditions = ['new' => 'Neuf avec étiquette', 'like_new' => 'Comme neuf', 'good' => 'Bon état', 'fair' => 'État correct', 'poor' => 'Usé']; @endphp
                                @foreach($conditions as $val => $lab)
                                    <option value="{{ $val }}" @selected(old('condition', $item->condition) === $val)>{{ $lab }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="status">Statut vente</label>
                            <select id="status" name="status" class="form-control" required>
                                @php $statuss = ['pending' => 'En attente', 'active' => 'Actif', 'sold' => 'Vendu', 'inactive' => 'Inactif']; @endphp
                                @foreach($statuss as $val => $lab)
                                    <option value="{{ $val }}" @selected(old('status', $item->status) === $val)>{{ $lab }}</option>
                                @endforeach
                            </select>
                            @error('status') <p class="err-msg">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Spécifications -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                        <h2 class="font-semibold text-slate-900 dark:text-white">Spécifications</h2>
                        <button type="button" id="addSpecBtn"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-primary-600 hover:bg-primary-700 px-3 py-1.5 text-xs font-medium text-white transition-colors">
                            <i class="fas fa-plus"></i> Ajouter
                        </button>
                    </div>
                    <div class="p-5 sm:p-6">
                        <div id="specsContainer" class="space-y-3">
                            @php $specs = $item->specifications ?? []; @endphp
                            @forelse($specs as $sKey => $sVal)
                                <div class="spec-row flex items-start gap-2">
                                    <input type="text" name="specifications[{{ $sKey }}][key]" value="{{ $sKey }}"
                                           placeholder="Clé" class="form-control">
                                    <input type="text" name="specifications[{{ $sKey }}][value]" value="{{ $sVal }}"
                                           placeholder="Valeur" class="form-control">
                                    <button type="button" class="remove-spec-btn mt-2 flex-shrink-0 h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 transition-colors">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            @empty
                                <p id="noSpecs" class="text-sm text-slate-500 dark:text-slate-400">Aucune spécification.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                        <h2 class="font-semibold text-slate-900 dark:text-white">Images</h2>
                    </div>
                    <div class="p-5 sm:p-6 space-y-4">
                        @if(!empty($item->images))
                            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3" id="existingImages">
                                @foreach($item->images as $image)
                                    <div class="img-preview-box aspect-square">
                                        <img src="{{ asset('storage/' . $image) }}" class="h-full w-full object-cover" alt="">
                                        <button type="button" data-image="{{ $image }}"
                                                class="remove-img-btn removeExistingImage" title="Retirer cette image">
                                            <i class="fas fa-xmark"></i>
                                        </button>
                                        <input type="hidden" name="keep_images[]" value="{{ $image }}">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <div>
                            <label class="form-label">Ajouter de nouvelles images</label>
                            <input type="file" name="images[]" id="images" multiple accept="image/*"
                                   class="form-control">
                            <p class="mt-2 text-xs text-slate-400">Les nouvelles images seront ajoutées aux images existantes. Formats: jpeg, png, jpg, gif — max 2 Mo chacune.</p>
                        </div>
                        <div id="newImagesPreview" class="grid grid-cols-2 gap-4 sm:grid-cols-3"></div>
                    </div>
                </div>
            </div>

            <!-- Colonne latérale -->
            <div class="space-y-4 lg:space-y-6">
                <!-- Propriétaire -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                        <h2 class="font-semibold text-slate-900 dark:text-white">Propriétaire</h2>
                    </div>
                    <div class="p-5">
                        <label class="form-label" for="user_id">Vendeur (utilisateur)</label>
                        <select id="user_id" name="user_id" class="form-control" required>
                            <option value="">-- Sélectionner --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" @selected(old('user_id', $item->user_id) == $u->id)>
                                    {{ $u->name }} ({{ $u->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id') <p class="err-msg">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Résumé -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                        <h2 class="font-semibold text-slate-900 dark:text-white">Résumé</h2>
                    </div>
                    <div class="p-5 space-y-2.5 text-sm">
                        <div class="flex justify-between"><span class="text-slate-500 dark:text-slate-400">ID</span><span class="font-medium text-slate-900 dark:text-white">#{{ $item->id }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-500 dark:text-slate-400">Prix actuel</span><span class="font-semibold text-slate-900 dark:text-white">{{ number_format($item->price, 2) }} {{ $item->currency }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-500 dark:text-slate-400">Vues</span><span class="font-medium text-slate-900 dark:text-white">{{ number_format($item->views ?? 0) }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-500 dark:text-slate-400">Créé le</span><span class="font-medium text-slate-900 dark:text-white">{{ $item->created_at?->format('d/m/Y') }}</span></div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm p-5 space-y-3">
                    <button type="submit" id="submitBtn"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-3 text-sm font-semibold text-white shadow-sm transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                    <a href="{{ route('admin.items.show', $item) }}"
                       class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        <i class="fas fa-ban"></i> Annuler
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function() {
    // ===== Retirer une image existante =====
    document.querySelectorAll('.removeExistingImage').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (!confirm('Retirer cette image de l\'article ?')) return;
            var box = this.closest('.img-preview-box');
            if (box) box.remove();
        });
    });

    // ===== Prévisualisation des nouvelles images =====
    var imagesInput = document.getElementById('images');
    if (imagesInput) {
        imagesInput.addEventListener('change', function() {
            var preview = document.getElementById('newImagesPreview');
            preview.innerHTML = '';
            Array.from(this.files).forEach(function(file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var div = document.createElement('div');
                    div.className = 'img-preview-box aspect-square';
                    div.innerHTML = '<img src="' + e.target.result + '" class="h-full w-full object-cover" alt="">';
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
    }

    // ===== Spécifications dynamiques =====
    var noSpecs = document.getElementById('noSpecs');
    var container = document.getElementById('specsContainer');
    function toggleNoSpecs() {
        if (noSpecs) noSpecs.style.display = container.querySelectorAll('.spec-row').length ? 'none' : '';
    }
    toggleNoSpecs();

    document.getElementById('addSpecBtn').addEventListener('click', function() {
        var row = document.createElement('div');
        row.className = 'spec-row flex items-start gap-2';
        var i = Date.now();
        row.innerHTML =
            '<input type="text" name="specifications[' + i + '][key]" placeholder="Clé" class="form-control">' +
            '<input type="text" name="specifications[' + i + '][value]" placeholder="Valeur" class="form-control">' +
            '<button type="button" class="remove-spec-btn flex-shrink-0 h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 transition-colors" style="display:inline-flex;margin-top:.5rem"><i class="fas fa-trash-can text-xs"></i></button>';
        container.appendChild(row);
        bindRemoveSpec(row.querySelector('.remove-spec-btn'));
        if (noSpecs) noSpecs.remove();
    });

    function bindRemoveSpec(btn) {
        btn.addEventListener('click', function() {
            var row = this.closest('.spec-row');
            if (row) row.remove();
            toggleNoSpecs();
        });
    }
    document.querySelectorAll('.remove-spec-btn').forEach(bindRemoveSpec);

    // ===== Bouton de soumission avec spinner =====
    var form = document.getElementById('itemForm');
    if (form) {
        form.addEventListener('submit', function() {
            var btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
        });
    }
})();
</script>
@endpush
