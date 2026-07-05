<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejoignez VintApp - Pré-inscription</title>
    <meta name="description" content="Inscrivez-vous dès maintenant à VintApp et bénéficiez d'un accès prioritaire à notre plateforme innovante.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @production
        <link rel="stylesheet" href="{{ asset('css/dynamic-colors.css') }}?v={{ filemtime(public_path('css/dynamic-colors.css')) }}">
    @else
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = '{{ asset('css/dynamic-colors.css') }}?v={{ filemtime(public_path('css/dynamic-colors.css')) }}';
                document.head.appendChild(link);
            });
        </script>
    @endproduction
    
    <link rel="preconnect" href="https://www.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://www.gstatic.com">
    
    <script defer src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
    <script defer src="https://www.gstatic.com/firebasejs/10.7.1/firebase-auth-compat.js"></script>
    <script defer src="https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore-compat.js"></script>
</head>
<body class="min-h-screen bg-stone-100 dark:bg-stone-950">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-5xl">
            <!-- Main Card -->
            <div class="bg-white dark:bg-stone-900 rounded-3xl shadow-lg shadow-stone-200/50 dark:shadow-stone-950 border border-stone-200 dark:border-stone-800 overflow-hidden">
                <!-- Header -->
                <div class="relative px-8 lg:px-12 pt-12 pb-8 text-center overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-200 via-orange-300 to-amber-200"></div>
                    <div class="w-16 h-16 bg-gradient-to-br from-amber-100 to-orange-100 dark:from-amber-900/40 dark:to-orange-900/40 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    
                    <h1 class="text-3xl lg:text-4xl font-bold text-stone-800 dark:text-stone-100 mb-3">
                        Rejoignez <span class="text-amber-600 dark:text-amber-400">VintApp</span>
                    </h1>
                    <p class="text-lg text-stone-500 dark:text-stone-400 max-w-2xl mx-auto">
                        La marketplace du vintage qui révolutionne l'achat et la vente de pièces uniques
                    </p>
                    
                    <!-- Stats -->
                    <div class="flex items-center justify-center gap-8 mt-8">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-stone-800 dark:text-stone-100">{{ \App\Models\UserWaiting::count() }}+</p>
                            <p class="text-sm text-stone-500 dark:text-stone-400">Inscrits</p>
                        </div>
                        <div class="w-px h-10 bg-stone-200 dark:bg-stone-700"></div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-stone-800 dark:text-stone-100">{{ \App\Models\UserWaiting::approved()->count() }}</p>
                            <p class="text-sm text-stone-500 dark:text-stone-400">Approuvés</p>
                        </div>
                        <div class="w-px h-10 bg-stone-200 dark:bg-stone-700"></div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-stone-800 dark:text-stone-100">Bientôt</p>
                            <p class="text-sm text-stone-500 dark:text-stone-400">Lancement</p>
                        </div>
                    </div>
                </div>

                <!-- Form Body -->
                <div class="px-8 lg:px-12 pb-12">
                    @if(session('success'))
                        <div class="mb-8 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-4 flex items-start gap-3">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-emerald-800 dark:text-emerald-200 font-medium">{{ session('success') }}</p>
                            </div>
                            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-8 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-red-800 dark:text-red-200 font-medium">{{ session('error') }}</p>
                            </div>
                            <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-8 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-red-800 dark:text-red-200 font-medium mb-2">Veuillez corriger les erreurs suivantes :</p>
                                    <ul class="space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li class="text-red-700 dark:text-red-300 text-sm flex items-start gap-2">
                                                <span>•</span>
                                                <span>{{ $error }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Benefits -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
                        <div class="bg-amber-50/50 dark:bg-amber-900/10 rounded-xl p-5 border border-amber-200/50 dark:border-amber-800/30">
                            <div class="w-10 h-10 bg-gradient-to-br from-amber-200 to-orange-200 dark:from-amber-700 dark:to-orange-700 rounded-lg flex items-center justify-center mb-3">
                                <svg class="w-5 h-5 text-amber-700 dark:text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-stone-800 dark:text-stone-200 mb-1">Accès prioritaire</h3>
                            <p class="text-sm text-stone-500 dark:text-stone-400">Soyez parmi les premiers utilisateurs de la plateforme</p>
                        </div>
                        <div class="bg-amber-50/50 dark:bg-amber-900/10 rounded-xl p-5 border border-amber-200/50 dark:border-amber-800/30">
                            <div class="w-10 h-10 bg-gradient-to-br from-amber-200 to-orange-200 dark:from-amber-700 dark:to-orange-700 rounded-lg flex items-center justify-center mb-3">
                                <svg class="w-5 h-5 text-amber-700 dark:text-amber-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-1 1h1zm3 0a1 1 0 10-1-1v1h1z" clip-rule="evenodd"/>
                                    <path d="M9 11H3v5a2 2 0 002 2h4v-7zM11 18h4a2 2 0 002-2v-5h-6v7z"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-stone-800 dark:text-stone-200 mb-1">Bonus exclusif</h3>
                            <p class="text-sm text-stone-500 dark:text-stone-400">Crédits de bienvenue offerts à l'inscription</p>
                        </div>
                        <div class="bg-amber-50/50 dark:bg-amber-900/10 rounded-xl p-5 border border-amber-200/50 dark:border-amber-800/30">
                            <div class="w-10 h-10 bg-gradient-to-br from-amber-200 to-orange-200 dark:from-amber-700 dark:to-orange-700 rounded-lg flex items-center justify-center mb-3">
                                <svg class="w-5 h-5 text-amber-700 dark:text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-stone-800 dark:text-stone-200 mb-1">Infos exclusives</h3>
                            <p class="text-sm text-stone-500 dark:text-stone-400">Restez informé du lancement en avant-première</p>
                        </div>
                    </div>

                    <!-- Registration Form -->
                    <form method="POST" action="{{ route('preregistration.store') }}" id="preregistrationForm" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Full Name -->
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-stone-600 dark:text-stone-300 mb-2">
                                    Nom complet <span class="text-amber-500">*</span>
                                </label>
                                <input type="text" 
                                       class="w-full px-4 py-3 bg-white dark:bg-stone-800 border border-stone-300 dark:border-stone-600 rounded-xl focus:ring-2 focus:ring-amber-300 dark:focus:ring-amber-700 focus:border-amber-400 dark:focus:border-amber-500 transition-all text-stone-800 dark:text-stone-200 placeholder-stone-400" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}"
                                       required
                                       placeholder="Ex: Jean Dupont">
                                @error('name')
                                    <p class="mt-1.5 text-sm text-rose-500 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="md:col-span-2">
                                <label for="email" class="block text-sm font-medium text-stone-600 dark:text-stone-300 mb-2">
                                    Adresse email <span class="text-amber-500">*</span>
                                </label>
                                <input type="email" 
                                       class="w-full px-4 py-3 bg-white dark:bg-stone-800 border border-stone-300 dark:border-stone-600 rounded-xl focus:ring-2 focus:ring-amber-300 dark:focus:ring-amber-700 focus:border-amber-400 dark:focus:border-amber-500 transition-all text-stone-800 dark:text-stone-200 placeholder-stone-400" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}"
                                       required
                                       placeholder="votre.email@example.com">
                                @error('email')
                                    <p class="mt-1.5 text-sm text-rose-500 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-stone-600 dark:text-stone-300 mb-2">
                                    Téléphone <span class="text-stone-400 font-normal">(optionnel)</span>
                                </label>
                                <input type="tel" 
                                       class="w-full px-4 py-3 bg-white dark:bg-stone-800 border border-stone-300 dark:border-stone-600 rounded-xl focus:ring-2 focus:ring-amber-300 dark:focus:ring-amber-700 focus:border-amber-400 dark:focus:border-amber-500 transition-all text-stone-800 dark:text-stone-200 placeholder-stone-400" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone') }}"
                                       placeholder="+243812345678"
                                       pattern="^(\+?243|0)?[0-9]{9}$">
                                <p class="mt-1.5 text-xs text-stone-400">Format: 0812345678 ou +243812345678</p>
                                @error('phone')
                                    <p class="mt-1.5 text-sm text-rose-500 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Country -->
                            <div>
                                <label for="country" class="block text-sm font-medium text-stone-600 dark:text-stone-300 mb-2">
                                    Pays <span class="text-amber-500">*</span>
                                </label>
                                <select class="w-full px-4 py-3 bg-white dark:bg-stone-800 border border-stone-300 dark:border-stone-600 rounded-xl focus:ring-2 focus:ring-amber-300 dark:focus:ring-amber-700 focus:border-amber-400 dark:focus:border-amber-500 transition-all text-stone-800 dark:text-stone-200" 
                                        id="country" 
                                        name="country" 
                                        required>
                                    <option value="Congo (RDC)" {{ old('country', 'Congo (RDC)') == 'Congo (RDC)' ? 'selected' : '' }}>Congo (RDC)</option>
                                    <option value="Congo (Brazzaville)" {{ old('country') == 'Congo (Brazzaville)' ? 'selected' : '' }}>Congo (Brazzaville)</option>
                                    <option value="France" {{ old('country') == 'France' ? 'selected' : '' }}>France</option>
                                    <option value="Belgique" {{ old('country') == 'Belgique' ? 'selected' : '' }}>Belgique</option>
                                    <option value="Canada" {{ old('country') == 'Canada' ? 'selected' : '' }}>Canada</option>
                                    <option value="Autre" {{ old('country') == 'Autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('country')
                                    <p class="mt-1.5 text-sm text-rose-500 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Motivations -->
                        <div>
                            <label for="reason" class="block text-sm font-medium text-stone-600 dark:text-stone-300 mb-2">
                                Pourquoi voulez-vous rejoindre VintApp ?
                                <span class="text-stone-400 font-normal">(optionnel)</span>
                            </label>
                            <select class="w-full px-4 py-3 bg-white dark:bg-stone-800 border border-stone-300 dark:border-stone-600 rounded-xl focus:ring-2 focus:ring-amber-300 dark:focus:ring-amber-700 focus:border-amber-400 dark:focus:border-amber-500 transition-all text-stone-800 dark:text-stone-200" 
                                    id="reason" 
                                    name="reasons[]">
                                <option value="">Sélectionnez une raison</option>
                                <option value="Acheter des produits vintage de qualité" {{ is_array(old('reasons')) && in_array('Acheter des produits vintage de qualité', old('reasons')) ? 'selected' : '' }}>
                                    Acheter des produits vintage
                                </option>
                                <option value="Vendre mes articles vintage" {{ is_array(old('reasons')) && in_array('Vendre mes articles vintage', old('reasons')) ? 'selected' : '' }}>
                                    Vendre mes articles vintage
                                </option>
                                <option value="Développer mon business vintage" {{ is_array(old('reasons')) && in_array('Développer mon business vintage', old('reasons')) ? 'selected' : '' }}>
                                    Développer mon business
                                </option>
                                <option value="Rejoindre une communauté de passionnés" {{ is_array(old('reasons')) && in_array('Rejoindre une communauté de passionnés', old('reasons')) ? 'selected' : '' }}>
                                    Rejoindre la communauté
                                </option>
                                <option value="Enrichir ma collection personnelle" {{ is_array(old('reasons')) && in_array('Enrichir ma collection personnelle', old('reasons')) ? 'selected' : '' }}>
                                    Enrichir ma collection
                                </option>
                                <option value="Consommer de manière responsable et durable" {{ is_array(old('reasons')) && in_array('Consommer de manière responsable et durable', old('reasons')) ? 'selected' : '' }}>
                                    Consommation responsable
                                </option>
                                <option value="Découvrir une nouvelle plateforme innovante" {{ is_array(old('reasons')) && in_array('Découvrir une nouvelle plateforme innovante', old('reasons')) ? 'selected' : '' }}>
                                    Curiosité et découverte
                                </option>
                            </select>
                            @error('reasons')
                                <p class="mt-1.5 text-sm text-rose-500 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold py-3.5 px-6 rounded-xl transition-all shadow-sm shadow-amber-200/50 dark:shadow-amber-900/30 text-base">
                            <span class="flex items-center justify-center gap-2">
                                Rejoindre VintApp
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        </button>

                        <!-- Security Message -->
                        <div class="flex items-center justify-center gap-2 pt-2">
                            <svg class="w-4 h-4 text-stone-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-stone-400">
                                Vos données sont sécurisées et ne seront jamais partagées
                            </p>
                        </div>
                    </form>

                    <!-- Footer -->
                    <div class="mt-10 pt-6 border-t border-stone-200 dark:border-stone-800">
                        <div class="text-center space-y-4">
                            <a href="{{ route('preregistration.stats') }}" 
                               class="inline-flex items-center gap-1.5 text-sm text-stone-500 dark:text-stone-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                                </svg>
                                <span>Voir les statistiques d'inscription</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            
                            <div class="flex flex-wrap justify-center gap-6 text-sm">
                                <a href="#" class="text-stone-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors">FAQ</a>
                                <a href="#" class="text-stone-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors">Confidentialité</a>
                                <a href="#" class="text-stone-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors">Contact</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="text-center mt-8">
                <p class="text-stone-400 text-sm">
                    &copy; {{ date('Y') }} VintApp. Tous droits réservés.
                </p>
            </div>
        </div>
    </div>

    <script>
        const firebaseConfig = {
            apiKey: "{{ env('FIREBASE_API_KEY') }}",
            authDomain: "{{ env('FIREBASE_AUTH_DOMAIN') }}",
            projectId: "{{ env('FIREBASE_PROJECT_ID') }}",
            storageBucket: "{{ env('FIREBASE_STORAGE_BUCKET') }}",
            messagingSenderId: "{{ env('FIREBASE_MESSAGING_SENDER_ID') }}",
            appId: "{{ env('FIREBASE_APP_ID') }}"
        };
        
        let auth, db;
        
        function initializeFirebase() {
            if (typeof firebase === 'undefined') {
                console.error('Firebase SDK not loaded');
                return false;
            }
            
            if (!firebaseConfig.apiKey || !firebaseConfig.authDomain || !firebaseConfig.projectId) {
                console.error('Invalid Firebase config');
                return false;
            }
            
            try {
                firebase.initializeApp(firebaseConfig);
                auth = firebase.auth();
                db = firebase.firestore();
                return true;
            } catch (error) {
                console.error('Firebase init error:', error);
                return false;
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            initializeFirebase();
            
            const form = document.getElementById('preregistrationForm');
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnContent = submitBtn.innerHTML;
            
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                submitBtn.innerHTML = `
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Inscription en cours...
                    </span>
                `;
                submitBtn.disabled = true;
                
                const formData = new FormData(form);
                const name = formData.get('name');
                const email = formData.get('email');
                const phone = formData.get('phone') || '';
                const country = formData.get('country');
                const reasons = formData.getAll('reasons[]');
                const firebaseUid = 'temp_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                
                // Firestore optionnel — ne pas bloquer la requête
                if (auth && db) {
                    db.collection('preregistrations').doc(firebaseUid).set({
                        uid: firebaseUid, name, email, phone, country, reasons,
                        status: 'pending', approved: false,
                        createdAt: firebase.firestore.FieldValue.serverTimestamp(),
                        accountType: 'preregistration'
                    }).catch(e => console.warn('Firestore error (non-critical):', e));
                }
                
                try {
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 30000);
                    
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ name, email, phone, country, reasons, firebase_uid: firebaseUid }),
                        signal: controller.signal
                    });
                    
                    clearTimeout(timeoutId);
                    
                    if (!response.ok) {
                        const errorData = await response.json();
                        if (errorData.errors) {
                            throw new Error(Object.values(errorData.errors).flat().join('. '));
                        }
                        throw new Error(errorData.message || 'Erreur lors de l\'enregistrement');
                    }
                    
                    showSuccessMessage('Inscription réussie ! Vous recevrez un email de confirmation.');
                    form.reset();
                    
                    setTimeout(() => {
                        window.location.href = "{{ route('preregistration.stats') }}";
                    }, 3000);
                    
                } catch (error) {
                    let errorMessage = 'Une erreur est survenue lors de l\'inscription.';
                    if (error.name === 'AbortError') {
                        errorMessage = 'La requête a pris trop de temps. Vérifiez votre connexion.';
                    } else if (error.message) {
                        errorMessage = error.message;
                    }
                    showErrorMessage(errorMessage);
                    submitBtn.innerHTML = originalBtnContent;
                    submitBtn.disabled = false;
                }
            });
            
            function createAlert(type, message, autoDismissDelay = 5000) {
                const config = {
                    success: {
                        bgClass: 'bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800',
                        iconColor: 'text-emerald-600 dark:text-emerald-400',
                        textColor: 'text-emerald-800 dark:text-emerald-200',
                        iconPath: 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z',
                        showCloseBtn: false
                    },
                    error: {
                        bgClass: 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800',
                        iconColor: 'text-red-600 dark:text-red-400',
                        textColor: 'text-red-800 dark:text-red-200',
                        iconPath: 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z',
                        showCloseBtn: true
                    }
                };
                
                const alertConfig = config[type];
                const alertDiv = document.createElement('div');
                alertDiv.className = `mb-8 ${alertConfig.bgClass} rounded-xl p-4 flex items-start gap-3`;
                
                const closeButton = alertConfig.showCloseBtn ? `
                    <button onclick="this.parentElement.remove()" class="${alertConfig.iconColor} hover:opacity-70 transition-opacity">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                ` : '';
                
                alertDiv.innerHTML = `
                    <svg class="w-5 h-5 ${alertConfig.iconColor} flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="${alertConfig.iconPath}" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <p class="${alertConfig.textColor} font-medium">${message}</p>
                    </div>
                    ${closeButton}
                `;
                
                const formContainer = form.parentElement;
                formContainer.insertBefore(alertDiv, formContainer.firstChild);
                
                if (autoDismissDelay > 0) {
                    setTimeout(() => {
                        alertDiv.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                        alertDiv.style.opacity = '0';
                        alertDiv.style.transform = 'translateY(-20px)';
                        setTimeout(() => alertDiv.remove(), 500);
                    }, autoDismissDelay);
                }
                
                return alertDiv;
            }
            
            function showSuccessMessage(message) {
                return createAlert('success', message, 5000);
            }
            
            function showErrorMessage(message) {
                return createAlert('error', message, 8000);
            }
        });
    </script>
</body>
</html>
