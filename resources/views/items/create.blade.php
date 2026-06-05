@extends('app')
@section('title', 'Vendre un article')
@section('content')

<!-- Loading Overlay -->
<div id="loadingOverlay" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-2xl max-w-sm mx-4 text-center">
        <div class="w-14 h-14 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin mx-auto"></div>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mt-5">Publication en cours...</h3>
        <p class="text-gray-500 dark:text-gray-400 mt-1.5 text-sm" id="loadingMessage">Téléchargement des images</p>
        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 mt-4">
            <div id="progressBar" class="bg-gradient-to-r from-primary-600 to-primary-700 h-1.5 rounded-full transition-all duration-500" style="width: 0%"></div>
        </div>
        <p class="text-xs text-gray-400 mt-1.5" id="progressText">0%</p>
    </div>
</div>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 lg:py-10">
    <div class="container mx-auto px-4">

        <!-- Breadcrumb -->
        {{-- <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
            <a href="{{ route('home') }}" class="hover:text-primary-600 transition-colors">Accueil</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('items.index') }}" class="hover:text-primary-600 transition-colors">Produits</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900 dark:text-white font-medium">Vendre un article</span>
        </nav> --}}

        <div class="max-w-3xl mx-auto">

            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Vendre un article</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Remplissez les informations pour publier votre annonce</p>
            </div>

            <!-- Notifications -->
            @foreach(['success' => 'emerald', 'error' => 'red', 'warning' => 'amber'] as $type => $color)
                @if(session($type))
                <div id="notif-{{ $type }}" class="mb-6 bg-{{ $color }}-50 dark:bg-{{ $color }}-900/20 border border-{{ $color }}-200 dark:border-{{ $color }}-800 text-{{ $color }}-800 dark:text-{{ $color }}-200 rounded-xl p-4 flex items-start gap-3 animate-fade-in">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($type === 'success')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        @elseif($type === 'error')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @endif
                    </svg>
                    <p class="text-sm font-medium flex-1">{{ session($type) }}</p>
                    <button onclick="this.closest('[id^=notif]').remove()" class="text-{{ $color }}-500 hover:text-{{ $color }}-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                @endif
            @endforeach

            <!-- Form Card -->
            <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data" id="createItemForm">
                @csrf

                <!-- ========== SECTION 1 : Photos ========== -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 lg:p-8 mb-5">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/30 text-primary-600 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Photos de l'article</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Minimum 3 photos, max 2 Mo chacune</p>
                        </div>
                    </div>

                    <!-- Drop zone -->
                    <div id="dropZone" class="relative border-2 border-dashed border-gray-200 dark:border-gray-600 rounded-xl p-8 text-center hover:border-primary-400 hover:bg-primary-50/50 dark:hover:bg-primary-900/10 transition-all duration-200 cursor-pointer">
                        <input type="file" id="images" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div class="pointer-events-none">
                            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            </div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Glissez vos images ici</p>
                            <p class="text-xs text-gray-400 mt-1">ou cliquez pour sélectionner &bull; JPG, PNG, WebP</p>
                        </div>
                    </div>
                    @error('images.*')
                        <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $message }}
                        </p>
                    @enderror

                    <!-- Prévisualisation -->
                    <div id="imagePreview" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3 mt-4 hidden"></div>
                    <p id="imageCount" class="text-xs text-gray-400 mt-2 hidden">
                        <span id="imageCountNum">0</span> image(s) sélectionnée(s)
                    </p>
                </div>

                <!-- ========== SECTION 2 : Informations de base ========== -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 lg:p-8 mb-5">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Informations de base</h2>
                    </div>

                    <div class="space-y-5">
                        <!-- Nom -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nom de l'article <span class="text-red-400">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                   class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 @error('name') border-red-400 ring-4 ring-red-500/20 @enderror"
                                   placeholder="Ex: Veste en cuir vintage Levi's">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description <span class="text-red-400">*</span></label>
                            <textarea id="description" name="description" rows="4" required
                                      class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 resize-none @error('description') border-red-400 ring-4 ring-red-500/20 @enderror"
                                      placeholder="Décrivez votre article : état, marque, taille, histoire...">{{ old('description') }}</textarea>
                            <div class="flex justify-between mt-1.5">
                                @error('description')
                                    <p class="text-red-500 text-xs">{{ $message }}</p>
                                @else
                                    <p class="text-xs text-gray-400">Plus votre description est détaillée, plus vite votre article se vendra</p>
                                @enderror
                                <span id="charCount" class="text-xs text-gray-400">0</span>
                            </div>
                        </div>

                        <!-- Prix + Devise -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Prix <span class="text-red-400">*</span></label>
                                <div class="relative">
                                    <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0" required
                                           class="w-full pl-4 pr-12 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 @error('price') border-red-400 ring-4 ring-red-500/20 @enderror"
                                           placeholder="0.00">
                                    <span id="currencySymbol" class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">$</span>
                                </div>
                                @error('price')
                                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="currency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Devise <span class="text-red-400">*</span></label>
                                <select id="currency" name="currency" required
                                        class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 @error('currency') border-red-400 ring-4 ring-red-500/20 @enderror">
                                    <option value="">Choisir</option>
                                    <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                    <option value="CDF" {{ old('currency') == 'CDF' ? 'selected' : '' }}>CDF (FC)</option>
                                </select>
                                @error('currency')
                                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Catégorie + Marque -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Catégorie <span class="text-red-400">*</span></label>
                                <select id="category_id" name="category_id" required
                                        class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 @error('category_id') border-red-400 ring-4 ring-red-500/20 @enderror">
                                    <option value="">Choisir une catégorie</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="brand_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Marque</label>
                                <select id="brand_id" name="brand_id"
                                        class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 @error('brand_id') border-red-400 ring-4 ring-red-500/20 @enderror">
                                    <option value="">Aucune marque</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                                @error('brand_id')
                                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========== SECTION 3 : Détails du produit ========== -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 lg:p-8 mb-5">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-8 h-8 bg-amber-100 dark:bg-amber-900/30 text-amber-600 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Détails du produit</h2>
                    </div>

                    <div class="space-y-5">
                        <!-- État + Quantité -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="condition" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">État <span class="text-red-400">*</span></label>
                                <select id="condition" name="condition" required
                                        class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 @error('condition') border-red-400 ring-4 ring-red-500/20 @enderror">
                                    <option value="">Choisir l'état</option>
                                    <option value="new" {{ old('condition') == 'new' ? 'selected' : '' }}>Neuf avec étiquette</option>
                                    <option value="like_new" {{ old('condition') == 'like_new' ? 'selected' : '' }}>Comme neuf</option>
                                    <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>Bon état</option>
                                    <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>État correct</option>
                                    <option value="poor" {{ old('condition') == 'poor' ? 'selected' : '' }}>Usé</option>
                                </select>
                                @error('condition')
                                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Quantité <span class="text-red-400">*</span></label>
                                <input type="number" id="quantity" name="quantity" value="{{ old('quantity', 1) }}" min="1" required
                                       class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 @error('quantity') border-red-400 ring-4 ring-red-500/20 @enderror">
                                @error('quantity')
                                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Couleur + Taille + Numéro -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Couleur</label>
                                <input type="text" id="color" name="color" value="{{ old('color') }}"
                                       class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 @error('color') border-red-400 ring-4 ring-red-500/20 @enderror"
                                       placeholder="Ex: Noir">
                                @error('color')
                                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="size" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Taille</label>
                                <select id="size" name="size"
                                        class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 @error('size') border-red-400 ring-4 ring-red-500/20 @enderror">
                                    <option value="">—</option>
                                    @foreach(['XS','S','M','L','XL','XXL','XXXL'] as $s)
                                        <option value="{{ $s }}" {{ old('size') == $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                                @error('size')
                                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="item_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Réf. article</label>
                                <input type="text" id="item_number" name="item_number" value="{{ old('item_number') }}"
                                       class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 @error('item_number') border-red-400 ring-4 ring-red-500/20 @enderror"
                                       placeholder="Ex: ABC123">
                                @error('item_number')
                                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========== SECTION 4 : Spécifications ========== -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 lg:p-8 mb-5">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 text-purple-600 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Spécifications</h2>
                                <p class="text-xs text-gray-400">Optionnel — ajoutez des détails supplémentaires</p>
                            </div>
                        </div>
                        <button type="button" id="addSpecification"
                                class="text-sm font-medium text-primary-600 hover:text-primary-700 flex items-center gap-1.5 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Ajouter
                        </button>
                    </div>

                    <div id="specificationsContainer" class="space-y-3">
                        <div class="flex gap-3 items-center specification-row">
                            <input type="text" name="specifications[key][]"
                                   class="flex-1 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200"
                                   placeholder="Ex: Matériau">
                            <input type="text" name="specifications[value][]"
                                   class="flex-1 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200"
                                   placeholder="Ex: Cuir véritable">
                            <button type="button" class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all remove-specification flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ========== Actions ========== -->
                <div class="flex flex-col sm:flex-row justify-between gap-3 mt-6">
                    <a href="{{ route('items.index') }}"
                       class="px-6 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 text-center">
                        Annuler
                    </a>
                    <button type="submit" id="submitBtn"
                            class="px-8 py-3 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white rounded-xl text-sm font-semibold shadow-lg shadow-primary-600/25 hover:shadow-xl hover:shadow-primary-600/30 transition-all duration-200">
                        Publier l'article
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createItemForm');
    const imageInput = document.getElementById('images');
    const preview = document.getElementById('imagePreview');
    const dropZone = document.getElementById('dropZone');
    const countEl = document.getElementById('imageCount');
    const countNum = document.getElementById('imageCountNum');
    const descInput = document.getElementById('description');
    const charCount = document.getElementById('charCount');
    const currencySelect = document.getElementById('currency');
    const currencySymbol = document.getElementById('currencySymbol');
    const specContainer = document.getElementById('specificationsContainer');

    // ---- Character counter ----
    function updateCharCount() {
        charCount.textContent = descInput.value.length;
    }
    descInput.addEventListener('input', updateCharCount);
    updateCharCount();

    // ---- Currency symbol sync ----
    currencySelect.addEventListener('change', function() {
        currencySymbol.textContent = this.value === 'CDF' ? 'FC' : '$';
    });

    // ---- Drag & drop highlight ----
    ['dragenter','dragover'].forEach(e => {
        dropZone.addEventListener(e, function(ev) {
            ev.preventDefault();
            dropZone.classList.add('border-primary-500', 'bg-primary-50/50');
        });
    });
    ['dragleave','drop'].forEach(e => {
        dropZone.addEventListener(e, function(ev) {
            ev.preventDefault();
            dropZone.classList.remove('border-primary-500', 'bg-primary-50/50');
        });
    });
    dropZone.addEventListener('drop', function(ev) {
        ev.preventDefault();
        if (ev.dataTransfer.files.length) {
            imageInput.files = ev.dataTransfer.files;
            imageInput.dispatchEvent(new Event('change'));
        }
    });

    // ---- Image preview ----
    imageInput.addEventListener('change', function() {
        preview.innerHTML = '';
        const files = Array.from(this.files || []);
        if (!files.length) { preview.classList.add('hidden'); countEl.classList.add('hidden'); return; }

        preview.classList.remove('hidden');
        countEl.classList.remove('hidden');
        countNum.textContent = files.length;

        files.forEach((file, i) => {
            if (!file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = function(ev) {
                const div = document.createElement('div');
                div.className = 'relative group aspect-square';
                const overSize = file.size > 5 * 1024 * 1024;
                div.innerHTML = `
                    <img src="${ev.target.result}" class="w-full h-full object-cover rounded-xl border border-gray-200 dark:border-gray-700" alt="">
                    ${i === 0 ? '<span class="absolute top-1.5 left-1.5 bg-primary-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-md">Principal</span>' : ''}
                    ${overSize ? '<span class="absolute bottom-1.5 left-1.5 bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-md">&gt;5Mo</span>' : ''}
                    <button type="button" class="absolute top-1.5 right-1.5 w-6 h-6 bg-black/60 hover:bg-red-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all remove-image" data-index="${i}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });

    // ---- Remove single image ----
    preview.addEventListener('click', function(e) {
        const btn = e.target.closest('.remove-image');
        if (!btn) return;
        const idx = parseInt(btn.dataset.index);
        const dt = new DataTransfer();
        Array.from(imageInput.files).forEach((f, i) => { if (i !== idx) dt.items.add(f); });
        imageInput.files = dt.files;
        imageInput.dispatchEvent(new Event('change'));
    });

    // ---- Specifications ----
    document.getElementById('addSpecification').addEventListener('click', function() {
        const row = document.createElement('div');
        row.className = 'flex gap-3 items-center specification-row';
        row.innerHTML = `
            <input type="text" name="specifications[key][]" placeholder="Nom"
                   class="flex-1 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200">
            <input type="text" name="specifications[value][]" placeholder="Valeur"
                   class="flex-1 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200">
            <button type="button" class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all remove-specification flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        `;
        specContainer.appendChild(row);
        row.querySelector('input').focus();
    });

    specContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-specification')) {
            e.target.closest('.specification-row').remove();
        }
    });

    // ---- Price / Quantity guards ----
    document.getElementById('price').addEventListener('input', function() { if (this.value < 0) this.value = 0; });
    document.getElementById('quantity').addEventListener('input', function() { if (this.value < 1) this.value = 1; });

    // ---- Compress image via Canvas ----
    function compressImage(file, maxWidth = 1920, maxHeight = 1920, quality = 0.82) {
        return new Promise((resolve) => {
            if (file.size <= 2 * 1024 * 1024) { resolve(file); return; }
            const img = new Image();
            const url = URL.createObjectURL(file);
            img.onload = function() {
                URL.revokeObjectURL(url);
                let w = img.width, h = img.height;
                if (w > maxWidth)  { h = Math.round(h * maxWidth / w);  w = maxWidth; }
                if (h > maxHeight) { w = Math.round(w * maxHeight / h); h = maxHeight; }
                const canvas = document.createElement('canvas');
                canvas.width = w; canvas.height = h;
                canvas.getContext('2d').drawImage(img, 0, 0, w, h);
                canvas.toBlob(function(blob) {
                    if (!blob) { resolve(file); return; }
                    resolve(new File([blob], file.name, { type: 'image/jpeg', lastModified: Date.now() }));
                }, 'image/jpeg', quality);
            };
            img.onerror = function() { URL.revokeObjectURL(url); resolve(file); };
            img.src = url;
        });
    }

    // ---- Form submit ----
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        let valid = true;
        form.querySelectorAll('[required]').forEach(f => {
            if (!f.value.trim()) {
                f.classList.add('border-red-400', 'ring-4', 'ring-red-500/20');
                valid = false;
            } else {
                f.classList.remove('border-red-400', 'ring-4', 'ring-red-500/20');
            }
        });

        if (!valid) { showNotif('Veuillez remplir tous les champs obligatoires', 'error'); return; }

        if (!imageInput.files || imageInput.files.length < 3) {
            showNotif('Ajoutez au moins 3 photos de votre article', 'error');
            return;
        }

        showLoading();

        // Compress images before sending
        try {
            const dt = new DataTransfer();
            const files = Array.from(imageInput.files);
            for (let i = 0; i < files.length; i++) {
                const compressed = await compressImage(files[i]);
                dt.items.add(compressed);
                const pct = Math.round(((i + 1) / files.length) * 30);
                document.getElementById('progressBar').style.width = pct + '%';
                document.getElementById('progressText').textContent = pct + '%';
                document.getElementById('loadingMessage').textContent = `Compression image ${i + 1}/${files.length}...`;
            }
            imageInput.files = dt.files;
        } catch(err) {
            // Continue with original files if compression fails
        }

        form.submit();
    });

    // ---- Auto-dismiss notifications ----
    document.querySelectorAll('[id^=notif-]').forEach(n => {
        setTimeout(() => { n.style.transition = 'opacity .3s'; n.style.opacity = '0'; setTimeout(() => n.remove(), 300); }, 5000);
    });
});

// ---- Loading overlay ----
function showLoading() {
    const overlay = document.getElementById('loadingOverlay');
    const bar = document.getElementById('progressBar');
    const text = document.getElementById('progressText');
    const msg = document.getElementById('loadingMessage');
    overlay.classList.remove('hidden');

    const steps = ['Téléchargement des images...', 'Vérification qualité...', 'Analyse d\'authenticité...', 'Finalisation...'];
    let progress = 0;
    const iv = setInterval(() => {
        progress += Math.random() * 15;
        if (progress > 90) progress = 90;
        bar.style.width = progress + '%';
        text.textContent = Math.round(progress) + '%';
        msg.textContent = steps[Math.min(Math.floor(progress / 25), steps.length - 1)];
    }, 500);
    window._loadingIv = iv;
}

// ---- Toast notifications ----
function showNotif(message, type = 'info') {
    const colors = { success: 'bg-emerald-600', error: 'bg-red-600', warning: 'bg-amber-600', info: 'bg-primary-600' };
    const icons = {
        success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
        error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
        warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
    };
    const el = document.createElement('div');
    el.className = `fixed top-5 right-5 z-50 max-w-sm ${colors[type]} text-white rounded-xl shadow-lg px-4 py-3 flex items-center gap-3 transform translate-x-[120%] transition-transform duration-300 text-sm`;
    el.innerHTML = `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">${icons[type]}</svg><span class="font-medium flex-1">${message}</span><button onclick="this.parentElement.remove()" class="hover:opacity-75 flex-shrink-0"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>`;
    document.body.appendChild(el);
    requestAnimationFrame(() => el.classList.remove('translate-x-[120%]'));
    setTimeout(() => { el.classList.add('translate-x-[120%]'); setTimeout(() => el.remove(), 300); }, 4000);
}
</script>
@endsection