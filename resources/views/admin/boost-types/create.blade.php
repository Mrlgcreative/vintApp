@extends('layouts.admin')

@section('title', 'Nouveau type de boost')
@section('page-title', 'Nouveau type de boost')

@section('page-actions')
<a href="{{ route('admin.boost-types.index') }}"
   class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 dark:bg-gray-900 transition-colors">
    <i class="fas fa-arrow-left mr-2"></i>Retour
</a>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="p-6">
        <form method="POST" action="{{ route('admin.boost-types.store') }}" class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informations générales</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Nom interne *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-900"
                                       placeholder="Ex: featured, premium">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Nom affiché *</label>
                                <input type="text" name="display_name" value="{{ old('display_name') }}" required
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-900"
                                       placeholder="Ex: Produit Vedette">
                                @error('display_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Description</label>
                        <textarea name="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-900"
                                  placeholder="Description du type de boost">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tarification</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Prix USD</label>
                                <input type="number" step="0.01" name="price_usd" value="{{ old('price_usd', 0) }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-900">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Prix CDF</label>
                                <input type="number" name="price_cdf" value="{{ old('price_cdf', 0) }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-900">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Prix de base</label>
                                <input type="number" step="0.01" name="base_price" value="{{ old('base_price', 0) }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-900">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Prix/jour</label>
                                <input type="number" step="0.01" name="price_per_day" value="{{ old('price_per_day', 0) }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-900">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Durées disponibles (jours)</h3>
                        <div class="flex flex-wrap gap-2" x-data="{ durations: {{ json_encode(old('available_durations', [1, 3, 7, 14, 30])) }} }">
                            <template x-for="(d, i) in durations" :key="i">
                                <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-900 rounded-lg px-3 py-1.5">
                                    <input type="hidden" name="available_durations[]" :value="d">
                                    <span x-text="d + 'j'" class="text-sm font-medium"></span>
                                    <button type="button" @click="durations.splice(i, 1)" class="text-red-500 hover:text-red-700 ml-1">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            </template>
                            <div class="flex gap-1">
                                <input type="number" min="1"
                                       class="w-20 px-2 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 dark:bg-gray-900"
                                       placeholder="Jours"
                                       id="newDuration">
                                <button type="button" @click="durations.push(parseInt(document.getElementById('newDuration').value)); document.getElementById('newDuration').value = ''"
                                        class="px-3 py-1.5 bg-primary-100 text-primary-700 rounded-lg text-sm font-medium hover:bg-primary-200 transition-colors">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Apparence</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Icône (FontAwesome)</label>
                                <input type="text" name="icon" value="{{ old('icon', 'fas fa-bolt') }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-900"
                                       placeholder="fas fa-bolt">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Couleur</label>
                                <div class="flex gap-2">
                                    <input type="color" name="color" value="{{ old('color', '#3B82F6') }}"
                                           class="w-10 h-10 rounded-lg cursor-pointer border border-gray-300">
                                    <input type="text" name="color_hex" value="{{ old('color', '#3B82F6') }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-900"
                                           placeholder="#3B82F6"
                                           oninput="this.previousElementSibling.value = this.value">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-5">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Configuration</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Ordre d'affichage</label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-900">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Max simultanés</label>
                                <input type="number" name="max_concurrent" value="{{ old('max_concurrent', 1) }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-900">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Durée min (jours)</label>
                                <input type="number" name="min_duration" value="{{ old('min_duration', 1) }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-900">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Durée max (jours)</label>
                                <input type="number" name="max_duration" value="{{ old('max_duration', 30) }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-900">
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-5">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Statut</h4>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                                       class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <span class="text-sm text-gray-700 dark:text-gray-200">Type actif</span>
                            </label>
                            <label class="flex items-center gap-3">
                                <input type="hidden" name="is_premium" value="0">
                                <input type="checkbox" name="is_premium" value="1" {{ old('is_premium') ? 'checked' : '' }}
                                       class="w-4 h-4 rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                                <span class="text-sm text-gray-700 dark:text-gray-200">Premium</span>
                            </label>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-5">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Notes internes</h4>
                        <textarea name="admin_notes" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-900 text-sm"
                                  placeholder="Notes pour l'administration...">{{ old('admin_notes') }}</textarea>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/10 dark:to-emerald-900/10 rounded-xl p-5 border border-green-200 dark:border-green-800">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Avantages</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Liste des avantages (un par ligne)</p>
                        <textarea name="benefits" rows="5"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-900 text-sm"
                                  placeholder="Visibilité maximale&#10;Badge exclusif&#10;Priorité dans les recherches">{{ old('benefits') ? (is_array(old('benefits')) ? implode("\n", old('benefits')) : old('benefits')) : '' }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.boost-types.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 transition-colors">
                    Annuler
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Créer le type
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
