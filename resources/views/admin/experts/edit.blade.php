@extends('layouts.admin')

@section('title', 'Modifier Expert - ' . $expert->user->name)
@section('page-title', 'Modifier l\'Expert')
@section('page-subtitle', 'Modifier les informations de ' . $expert->user->name)

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.experts.show', $expert) }}"
       class="inline-flex items-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
        <i class="fas fa-arrow-left"></i>Retour aux détails
    </a>
    <a href="{{ route('admin.experts.index') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-list"></i>Liste des Experts
    </a>
</div>
@endsection

@section('content')
@if($errors->any())
    <div class="flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-4 text-sm text-red-800 animate-fade-in dark:border-red-900/30 dark:bg-red-900/20 dark:text-red-300 mb-6">
        <i class="fas fa-triangle-exclamation text-red-500 mt-0.5"></i>
        <div>
            <h3 class="font-medium mb-2">Il y a {{ $errors->count() }} erreur(s) dans le formulaire :</h3>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Formulaire principal --}}
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2.5">
                <i class="fas fa-pen text-primary-600"></i>
                <h3 class="font-semibold text-slate-900 dark:text-white">Informations de l'Expert</h3>
            </div>
            <div class="p-5 sm:p-6">
                <form method="POST" action="{{ route('admin.experts.update', $expert) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Niveau de certification --}}
                        <div>
                            <label for="certification_level" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Niveau de Certification
                            </label>
                            <select name="certification_level" id="certification_level"
                                    class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">
                                <option value="junior" {{ $expert->certification_level === 'junior' ? 'selected' : '' }}>
                                    Junior
                                </option>
                                <option value="senior" {{ $expert->certification_level === 'senior' ? 'selected' : '' }}>
                                    Senior
                                </option>
                                <option value="master" {{ $expert->certification_level === 'master' ? 'selected' : '' }}>
                                    Master
                                </option>
                            </select>
                        </div>

                        {{-- Statut actif --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Statut
                            </label>
                            <div class="flex items-center">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" id="is_active" value="1"
                                           class="sr-only peer" {{ $expert->is_active ? 'checked' : '' }}>
                                    <div class="relative w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-500/30 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white dark:bg-slate-800 after:border-slate-300 dark:border-slate-600 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                                    <span class="ml-3 text-sm text-slate-700 dark:text-slate-300">Expert actif</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Spécialisations --}}
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                            Spécialisations
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @php
                                $specializations = [
                                    'luxury' => 'Articles de Luxe',
                                    'sneakers' => 'Sneakers',
                                    'watches' => 'Montres',
                                    'handbags' => 'Sacs à main',
                                    'jewelry' => 'Bijoux',
                                    'electronics' => 'Électronique',
                                    'clothing' => 'Vêtements',
                                    'accessories' => 'Accessoires'
                                ];
                            @endphp
                            @foreach($specializations as $key => $label)
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           name="specialties[]"
                                           value="{{ $key }}"
                                           id="specialty_{{ $key }}"
                                           class="h-4 w-4 text-primary-600 border-slate-300 dark:border-slate-600 rounded focus:ring-primary-500"
                                           {{ in_array($key, $expert->specialties ?? []) ? 'checked' : '' }}>
                                    <label for="specialty_{{ $key }}" class="ml-2 text-sm text-slate-700 dark:text-slate-300">
                                        {{ $label }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Bio/Description --}}
                    <div class="mt-6">
                        <label for="bio" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Biographie / Expertise
                        </label>
                        <textarea name="bio" id="bio" rows="4"
                                  placeholder="Décrivez l'expertise et l'expérience de cet expert..."
                                  class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">{{ old('bio', $expert->bio) }}</textarea>
                    </div>

                    {{-- Tarif par vérification --}}
                    <div class="mt-6">
                        <label for="rate_per_verification" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Tarif par Vérification (€)
                        </label>
                        <input type="number"
                               name="rate_per_verification"
                               id="rate_per_verification"
                               value="{{ old('rate_per_verification', $expert->rate_per_verification) }}"
                               step="0.01"
                               min="0"
                               placeholder="Ex: 25.00"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">
                    </div>

                    {{-- Boutons d'action --}}
                    <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8 pt-6 border-t border-slate-100 dark:border-slate-700">
                        <a href="{{ route('admin.experts.show', $expert) }}"
                           class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                            Annuler
                        </a>
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-6 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                            <i class="fas fa-save"></i>Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Informations de l'utilisateur --}}
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2.5">
                <i class="fas fa-user text-primary-600"></i>
                <h3 class="font-semibold text-slate-900 dark:text-white">Informations Utilisateur</h3>
            </div>
            <div class="p-5">
                <div class="text-center mb-6">
                    @if($expert->user->avatar)
                        <img src="{{ $expert->user->avatar_url }}"
                             class="w-20 h-20 rounded-full mx-auto object-cover ring-2 ring-slate-200 dark:ring-slate-700"
                             alt="{{ $expert->user->name }}"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="w-20 h-20 bg-primary-600 rounded-full mx-auto items-center justify-center hidden" style="display: none;">
                            <span class="text-white font-bold text-2xl">
                                {{ strtoupper(substr($expert->user->name, 0, 1)) }}
                            </span>
                        </div>
                    @else
                        <div class="w-20 h-20 bg-primary-600 rounded-full mx-auto flex items-center justify-center">
                            <span class="text-white font-bold text-2xl">
                                {{ strtoupper(substr($expert->user->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    <h4 class="font-semibold text-slate-900 dark:text-white mt-3">{{ $expert->user->name }}</h4>
                    <p class="text-slate-500 dark:text-slate-400 text-sm">{{ $expert->user->email }}</p>
                </div>

                <div class="space-y-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Membre depuis</span>
                        <span class="font-medium text-slate-900 dark:text-white">
                            {{ $expert->user->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Expert depuis</span>
                        <span class="font-medium text-slate-900 dark:text-white">
                            {{ $expert->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Vérifications</span>
                        <span class="font-medium text-slate-900 dark:text-white">
                            {{ $expert->verification_count }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Taux d'approbation</span>
                        <span class="font-medium text-slate-900 dark:text-white">
                            {{ $expert->approval_rate ? number_format($expert->approval_rate, 1) . '%' : 'N/A' }}
                        </span>
                    </div>
                </div>

                {{-- Statut actuel --}}
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500 dark:text-slate-400">Statut actuel</span>
                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ $expert->is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-900/30 dark:text-red-300' }}">
                            {{ $expert->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistiques récentes --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2.5">
                <i class="fas fa-chart-line text-primary-600"></i>
                <h3 class="font-semibold text-slate-900 dark:text-white">Statistiques Récentes</h3>
            </div>
            <div class="p-5">
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Ce mois</span>
                        <span class="font-medium text-slate-900 dark:text-white">
                            {{ $expert->verifications()->whereMonth('created_at', now()->month)->count() }} vérifications
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Cette semaine</span>
                        <span class="font-medium text-slate-900 dark:text-white">
                            {{ $expert->verifications()->where('created_at', '>=', now()->startOfWeek())->count() }} vérifications
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Dernière activité</span>
                        <span class="font-medium text-slate-900 dark:text-white">
                            {{ $expert->user->last_activity ? $expert->user->last_activity->diffForHumans() : 'Inconnue' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
