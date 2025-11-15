@extends('layouts.admin')

@section('title', 'Modifier Expert - ' . $expert->user->name)

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <i class="fas fa-user-edit text-blue-600"></i>
                Modifier l'Expert
            </h1>
            <p class="text-gray-600 dark:text-gray-300 mt-1">Modifier les informations de {{ $expert->user->name }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.experts.show', $expert) }}" 
               class="bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Retour aux détails
            </a>
            <a href="{{ route('admin.experts.index') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                <i class="fas fa-list"></i>
                Liste des Experts
            </a>
        </div>
    </div>

    <!-- Messages d'erreur -->
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-red-500 mr-3 mt-1"></i>
                <div>
                    <h3 class="text-sm font-medium text-red-800 mb-2">
                        Il y a {{ $errors->count() }} erreur(s) dans le formulaire :
                    </h3>
                    <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Formulaire principal -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-edit text-blue-600"></i>
                        Informations de l'Expert
                    </h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.experts.update', $expert) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Niveau de certification -->
                            <div>
                                <label for="certification_level" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                    Niveau de Certification
                                </label>
                                <select name="certification_level" id="certification_level" 
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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

                            <!-- Statut actif -->
                            <div>
                                <label for="is_active" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-3">
                                    Statut
                                </label>
                                <div class="flex items-center">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_active" id="is_active" value="1"
                                               class="sr-only peer" {{ $expert->is_active ? 'checked' : '' }}>
                                        <div class="relative w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white dark:bg-gray-800 after:border-gray-300 dark:border-gray-600 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-200">Expert actif</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Spécialisations -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-3">
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
                                               class="h-4 w-4 text-blue-600 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500"
                                               {{ in_array($key, $expert->specialties ?? []) ? 'checked' : '' }}>
                                        <label for="specialty_{{ $key }}" class="ml-2 text-sm text-gray-700 dark:text-gray-200">
                                            {{ $label }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Bio/Description -->
                        <div class="mt-6">
                            <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                Biographie / Expertise
                            </label>
                            <textarea name="bio" id="bio" rows="4" 
                                      placeholder="Décrivez l'expertise et l'expérience de cet expert..."
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('bio', $expert->bio) }}</textarea>
                        </div>

                        <!-- Tarif par vérification -->
                        <div class="mt-6">
                            <label for="rate_per_verification" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                Tarif par Vérification (€)
                            </label>
                            <input type="number" 
                                   name="rate_per_verification" 
                                   id="rate_per_verification" 
                                   value="{{ old('rate_per_verification', $expert->rate_per_verification) }}"
                                   step="0.01" 
                                   min="0"
                                   placeholder="Ex: 25.00"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Boutons d'action -->
                        <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('admin.experts.show', $expert) }}" 
                               class="px-4 py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg font-medium transition-colors">
                                Annuler
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informations de l'utilisateur -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-user text-blue-600"></i>
                        Informations Utilisateur
                    </h3>
                </div>
                <div class="p-6">
                    <div class="text-center mb-6">
                        @if($expert->user->avatar)
                            @php
                                $avatarUrl = filter_var($expert->user->avatar, FILTER_VALIDATE_URL) 
                                    ? $expert->user->avatar 
                                    : Storage::url($expert->user->avatar);
                            @endphp
                            <img src="{{ $avatarUrl }}" 
                                 class="w-20 h-20 rounded-full mx-auto object-cover" 
                                 alt="{{ $expert->user->name }}"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-20 h-20 bg-blue-600 rounded-full mx-auto hidden items-center justify-center">
                                <span class="text-white font-bold text-2xl">
                                    {{ strtoupper(substr($expert->user->name, 0, 1)) }}
                                </span>
                            </div>
                        @else
                            <div class="w-20 h-20 bg-blue-600 rounded-full mx-auto flex items-center justify-center">
                                <span class="text-white font-bold text-2xl">
                                    {{ strtoupper(substr($expert->user->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <h4 class="font-semibold text-gray-900 dark:text-white mt-3">{{ $expert->user->name }}</h4>
                        <p class="text-gray-600 dark:text-gray-300 text-sm">{{ $expert->user->email }}</p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Membre depuis</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $expert->user->created_at->format('d/m/Y') }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Expert depuis</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $expert->created_at->format('d/m/Y') }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Vérifications</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $expert->verification_count }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Taux d'approbation</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $expert->approval_rate ? number_format($expert->approval_rate, 1) . '%' : 'N/A' }}
                            </span>
                        </div>
                    </div>

                    <!-- Statut actuel -->
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Statut actuel</span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($expert->is_active) bg-green-100 text-green-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $expert->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques récentes -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mt-6">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-chart-line text-blue-600"></i>
                        Statistiques Récentes
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Ce mois</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $expert->verifications()->whereMonth('created_at', now()->month)->count() }} vérifications
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Cette semaine</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $expert->verifications()->where('created_at', '>=', now()->startOfWeek())->count() }} vérifications
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Dernière activité</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $expert->user->last_activity ? $expert->user->last_activity->diffForHumans() : 'Inconnue' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection