@extends('layouts.admin')

@section('title', 'Modifier : ' . $boostType->display_name)
@section('page-title', 'Modifier : ' . $boostType->display_name)

@section('page-actions')
<a href="{{ route('admin.boost-types.index') }}"
   class="inline-flex items-center px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 dark:bg-slate-900 transition-colors">
    <i class="fas fa-arrow-left mr-2"></i>Retour
</a>
@endsection

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <div class="p-6">
        <form method="POST" action="{{ route('admin.boost-types.update', $boostType) }}" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Informations générales</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Nom interne *</label>
                                <input type="text" name="name" value="{{ old('name', $boostType->name) }}" required
                                       class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-slate-900">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Nom affiché *</label>
                                <input type="text" name="display_name" value="{{ old('display_name', $boostType->display_name) }}" required
                                       class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-slate-900">
                                @error('display_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Description</label>
                        <textarea name="description" rows="3"
                                  class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-slate-900">{{ old('description', $boostType->description) }}</textarea>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Tarification</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Prix USD</label>
                                <input type="number" step="0.01" name="price_usd" value="{{ old('price_usd', $boostType->price_usd) }}"
                                       class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-slate-900">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Prix CDF</label>
                                <input type="number" name="price_cdf" value="{{ old('price_cdf', $boostType->price_cdf) }}"
                                       class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-slate-900">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Prix de base</label>
                                <input type="number" step="0.01" name="base_price" value="{{ old('base_price', $boostType->base_price) }}"
                                       class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-slate-900">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Prix/jour</label>
                                <input type="number" step="0.01" name="price_per_day" value="{{ old('price_per_day', $boostType->price_per_day) }}"
                                       class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-slate-900">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Durées disponibles (jours)</h3>
                        <div class="flex flex-wrap gap-2" x-data="{ durations: {{ json_encode(old('available_durations', $boostType->available_durations ?? [1, 3, 7, 14, 30])) }} }">
                            <template x-for="(d, i) in durations" :key="i">
                                <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-900 rounded-lg px-3 py-1.5">
                                    <input type="hidden" name="available_durations[]" :value="d">
                                    <span x-text="d + 'j'" class="text-sm font-medium"></span>
                                    <button type="button" @click="durations.splice(i, 1)" class="text-red-500 hover:text-red-700 ml-1">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            </template>
                            <div class="flex gap-1">
                                <input type="number" min="1"
                                       class="w-20 px-2 py-1.5 border border-slate-300 dark:border-slate-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 dark:bg-slate-900"
                                       placeholder="Jours" id="newDuration">
                                <button type="button" @click="durations.push(parseInt(document.getElementById('newDuration').value)); document.getElementById('newDuration').value = ''"
                                        class="px-3 py-1.5 bg-primary-100 text-primary-700 rounded-lg text-sm font-medium hover:bg-primary-200 transition-colors">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Apparence</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Icône (FontAwesome)</label>
                                <input type="text" name="icon" value="{{ old('icon', $boostType->icon ?? 'fas fa-bolt') }}"
                                       class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-slate-900">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Couleur</label>
                                <div class="flex gap-2">
                                    <input type="color" name="color" value="{{ old('color', $boostType->color ?? '#3B82F6') }}"
                                           class="w-10 h-10 rounded-lg cursor-pointer border border-slate-300">
                                    <input type="text" name="color_hex" value="{{ old('color', $boostType->color ?? '#3B82F6') }}"
                                           class="flex-1 px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-slate-900"
                                           oninput="this.previousElementSibling.value = this.value">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-5">
                        <h4 class="font-semibold text-slate-900 dark:text-white mb-4">Configuration</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Ordre</label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', $boostType->sort_order) }}"
                                       class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-slate-900">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Max simultanés</label>
                                <input type="number" name="max_concurrent" value="{{ old('max_concurrent', $boostType->max_concurrent) }}"
                                       class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-slate-900">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Durée min (jours)</label>
                                <input type="number" name="min_duration" value="{{ old('min_duration', $boostType->min_duration) }}"
                                       class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-slate-900">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Durée max (jours)</label>
                                <input type="number" name="max_duration" value="{{ old('max_duration', $boostType->max_duration) }}"
                                       class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-slate-900">
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-5">
                        <h4 class="font-semibold text-slate-900 dark:text-white mb-4">Statut</h4>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $boostType->is_active) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                                <span class="text-sm text-slate-700 dark:text-slate-200">Actif</span>
                            </label>
                            <label class="flex items-center gap-3">
                                <input type="hidden" name="is_premium" value="0">
                                <input type="checkbox" name="is_premium" value="1" {{ old('is_premium', $boostType->is_premium) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded border-slate-300 text-yellow-600 focus:ring-yellow-500">
                                <span class="text-sm text-slate-700 dark:text-slate-200">Premium</span>
                            </label>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-5">
                        <h4 class="font-semibold text-slate-900 dark:text-white mb-4">Notes internes</h4>
                        <textarea name="admin_notes" rows="4"
                                  class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-slate-900 text-sm">{{ old('admin_notes', $boostType->admin_notes) }}</textarea>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/10 dark:to-emerald-900/10 rounded-xl p-5 border border-green-200 dark:border-green-800">
                        <h4 class="font-semibold text-slate-900 dark:text-white mb-2">Avantages</h4>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">Un par ligne</p>
                        <textarea name="benefits" rows="5"
                                  class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-slate-900 text-sm">{{ old('benefits', $boostType->benefits) ? (is_array(old('benefits', $boostType->benefits)) ? implode("\n", old('benefits', $boostType->benefits)) : old('benefits', $boostType->benefits)) : '' }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
                <a href="{{ route('admin.boost-types.index') }}"
                   class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 transition-colors">
                    Annuler
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
