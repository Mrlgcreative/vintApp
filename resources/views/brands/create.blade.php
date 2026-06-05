@extends('app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('brands.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-indigo-600 transition-colors duration-200">
                        <i class="fas fa-tags mr-2"></i>
                        Marques
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-sm font-medium text-indigo-600">Ajouter une marque</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-plus text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Ajouter une marque</h1>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mt-1">Créez une nouvelle marque pour vos produits</p>
                </div>
            </div>
        </div>

        <!-- Formulaire -->
        <div class="max-w-2xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-indigo-600/10 border border-gray-100/50 overflow-hidden">
                <!-- Header du formulaire -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-edit text-indigo-500 mr-2"></i>
                        Informations de la marque
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

                    <form action="{{ route('brands.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Nom -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                Nom <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 @error('name') border-red-300 ring-red-500 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   value="{{ old('name') }}" 
                                   required 
                                   maxlength="100"
                                   placeholder="Ex: Nike, Adidas, Apple...">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                Description
                            </label>
                            <textarea name="description" 
                                      id="description" 
                                      rows="3" 
                                      maxlength="255"
                                      class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 resize-none"
                                      placeholder="Décrivez brièvement la marque...">{{ old('description') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maximum 255 caractères</p>
                        </div>

                        <!-- Site web -->
                        <div>
                            <label for="website" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                Site web
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-globe text-gray-400"></i>
                                </div>
                                <input type="url" 
                                       name="website" 
                                       id="website" 
                                       class="block w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" 
                                       value="{{ old('website') }}" 
                                       maxlength="255"
                                       placeholder="https://www.exemple.com">
                            </div>
                        </div>

                        <!-- Logo -->
                        <div>
                            <label for="logo" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                Logo
                            </label>
                            <div class="relative">
                                <input type="file" 
                                       name="logo" 
                                       id="logo" 
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-colors duration-200 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Formats acceptés : JPG, PNG, SVG (max 2MB)</p>
                        </div>

                        <!-- Pays d'origine -->
                        <div>
                            <label for="country" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                Pays d'origine
                            </label>
                            <select name="country" 
                                    id="country" 
                                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200">
                                <option value="">-- Sélectionner un pays --</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country }}" {{ old('country') == $country ? 'selected' : '' }}>
                                        {{ $country }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Type de marque -->
                        <div>
                            <label for="type" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                Type de marque
                            </label>
                            <select name="type" 
                                    id="type" 
                                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200">
                                <option value="">-- Sélectionner un type --</option>
                                @foreach($types as $type)
                                    <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Statut actif -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-xl">
                            <div class="flex items-center">
                                <i class="fas fa-toggle-on text-indigo-500 mr-3"></i>
                                <div>
                                    <label for="is_active" class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                        Marque active
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">La marque sera visible et utilisable</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       class="sr-only peer" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white dark:bg-gray-800 after:border-gray-300 dark:border-gray-600 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('brands.index') }}" 
                               class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 dark:bg-gray-900 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                                <i class="fas fa-times mr-2"></i>
                                Annuler
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 hover:from-indigo-600 hover:to-indigo-700 hover:shadow-xl hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300">
                                <i class="fas fa-save mr-2"></i>
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 