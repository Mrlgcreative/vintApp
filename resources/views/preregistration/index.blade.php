<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejoignez VintApp - Pré-inscription</title>
    <meta name="description" content="Inscrivez-vous dès maintenant à VintApp et bénéficiez d'un accès prioritaire à notre plateforme innovante.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Color Palette Variables (loaded AFTER Vite to override default colors) -->
    <link rel="stylesheet" href="{{ asset('css/dynamic-colors.css') }}">
    
    <!-- Preconnect pour optimiser le chargement Firebase -->
    <link rel="preconnect" href="https://www.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://www.gstatic.com">
    
    <!-- Firebase SDK - Chargement asynchrone -->
    <script defer src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
    <script defer src="https://www.gstatic.com/firebasejs/10.7.1/firebase-auth-compat.js"></script>
    <script defer src="https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore-compat.js"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-violet-600 via-purple-600 to-indigo-700 dark:from-gray-900 dark:via-purple-900 dark:to-indigo-900">
    <!-- Background Pattern -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <!-- Floating Shapes -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-72 h-72 bg-white/5 dark:bg-white/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-pink-500/10 dark:bg-pink-500/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 left-1/2 w-80 h-80 bg-cyan-500/10 dark:bg-cyan-500/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 4s;"></div>
    </div>
    
    <div class="relative min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-5xl">
            <!-- Main Card -->
            <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl overflow-hidden backdrop-blur-sm bg-opacity-95 dark:bg-opacity-95">
                <!-- Hero Header -->
                <div class="relative bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 px-8 lg:px-12 py-12 lg:py-16 overflow-hidden">
                    <!-- Decorative Elements -->
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32 backdrop-blur-sm"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24 backdrop-blur-sm"></div>
                    
                    <div class="relative z-10">
                        <!-- Logo/Icon -->
                        <div class="flex justify-center mb-6">
                            <div class="w-20 h-20 lg:w-24 lg:h-24 bg-white/20 backdrop-blur-md rounded-3xl flex items-center justify-center transform hover:scale-110 transition-transform duration-300">
                                <svg class="w-12 h-12 lg:w-14 lg:h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Title -->
                        <h1 class="text-4xl lg:text-6xl font-black text-white text-center mb-4 tracking-tight">
                            Bienvenue sur <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-200 to-pink-300">VintApp</span>
                        </h1>
                        <p class="text-xl lg:text-2xl text-white/90 text-center mb-8 max-w-3xl mx-auto font-light">
                            Rejoignez la révolution du vintage et découvrez une expérience unique
                        </p>
                        
                        <!-- Stats Badges -->
                        <div class="flex flex-wrap justify-center gap-4">
                            <div class="bg-white/20 backdrop-blur-md rounded-2xl px-6 py-3 border border-white/30 hover:bg-white/30 transition-all duration-300 transform hover:scale-105">
                                <div class="flex items-center gap-3">
                                    <svg class="w-6 h-6 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                    </svg>
                                    <div class="text-left">
                                        <p class="text-2xl font-bold text-white">{{ \App\Models\UserWaiting::count() }}+</p>
                                        <p class="text-xs text-white/80">Inscrits</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-white/20 backdrop-blur-md rounded-2xl px-6 py-3 border border-white/30 hover:bg-white/30 transition-all duration-300 transform hover:scale-105">
                                <div class="flex items-center gap-3">
                                    <svg class="w-6 h-6 text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="text-left">
                                        <p class="text-2xl font-bold text-white">{{ \App\Models\UserWaiting::approved()->count() }}</p>
                                        <p class="text-xs text-white/80">Approuvés</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-white/20 backdrop-blur-md rounded-2xl px-6 py-3 border border-white/30 hover:bg-white/30 transition-all duration-300 transform hover:scale-105">
                                <div class="flex items-center gap-3">
                                    <svg class="w-6 h-6 text-pink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="text-left">
                                        <p class="text-lg font-bold text-white">Bientôt</p>
                                        <p class="text-xs text-white/80">Lancement</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Body -->
                <div class="px-8 lg:px-12 py-12">
                    <!-- Success/Error Messages -->
                    @if(session('success'))
                        <div class="mb-8 bg-emerald-50 dark:bg-emerald-900/20 border-l-4 border-emerald-500 rounded-xl p-5 flex items-start gap-4 animate-pulse">
                            <svg class="w-6 h-6 text-emerald-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-emerald-900 dark:text-emerald-100 font-semibold">{{ session('success') }}</p>
                            </div>
                            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-8 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-xl p-5 flex items-start gap-4 animate-pulse">
                            <svg class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-red-900 dark:text-red-100 font-semibold">{{ session('error') }}</p>
                            </div>
                            <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-8 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-xl p-5">
                            <div class="flex items-start gap-4">
                                <svg class="w-6 h-6 text-red-500 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-red-900 dark:text-red-100 font-semibold mb-3">Veuillez corriger les erreurs suivantes :</p>
                                    <ul class="space-y-2">
                                        @foreach ($errors->all() as $error)
                                            <li class="text-red-800 dark:text-red-200 flex items-start gap-2">
                                                <span class="text-red-500 mt-1">•</span>
                                                <span>{{ $error }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Benefits Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                        <div class="group relative bg-gradient-to-br from-violet-50 to-purple-100 dark:from-violet-900/20 dark:to-purple-900/20 rounded-2xl p-6 border border-violet-200 dark:border-violet-800 hover:shadow-xl hover:scale-105 transition-all duration-300">
                            <div class="absolute inset-0 bg-gradient-to-br from-violet-500/0 to-purple-500/0 group-hover:from-violet-500/10 group-hover:to-purple-500/10 rounded-2xl transition-all duration-300"></div>
                            <div class="relative">
                                <div class="w-14 h-14 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center mb-4 transform group-hover:rotate-12 transition-transform duration-300">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <h3 class="font-bold text-gray-900 dark:text-white mb-2 text-lg">Accès prioritaire</h3>
                                <p class="text-gray-600 dark:text-gray-300 text-sm">Soyez parmi les premiers utilisateurs de la plateforme</p>
                            </div>
                        </div>
                        
                        <div class="group relative bg-gradient-to-br from-pink-50 to-rose-100 dark:from-pink-900/20 dark:to-rose-900/20 rounded-2xl p-6 border border-pink-200 dark:border-pink-800 hover:shadow-xl hover:scale-105 transition-all duration-300">
                            <div class="absolute inset-0 bg-gradient-to-br from-pink-500/0 to-rose-500/0 group-hover:from-pink-500/10 group-hover:to-rose-500/10 rounded-2xl transition-all duration-300"></div>
                            <div class="relative">
                                <div class="w-14 h-14 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center mb-4 transform group-hover:rotate-12 transition-transform duration-300">
                                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-1 1h1zm3 0a1 1 0 10-1-1v1h1z" clip-rule="evenodd"/>
                                        <path d="M9 11H3v5a2 2 0 002 2h4v-7zM11 18h4a2 2 0 002-2v-5h-6v7z"/>
                                    </svg>
                                </div>
                                <h3 class="font-bold text-gray-900 dark:text-white mb-2 text-lg">Bonus exclusif</h3>
                                <p class="text-gray-600 dark:text-gray-300 text-sm">Crédits de bienvenue offerts à l'inscription</p>
                            </div>
                        </div>
                        
                        <div class="group relative bg-gradient-to-br from-cyan-50 to-blue-100 dark:from-cyan-900/20 dark:to-blue-900/20 rounded-2xl p-6 border border-cyan-200 dark:border-cyan-800 hover:shadow-xl hover:scale-105 transition-all duration-300">
                            <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/0 to-blue-500/0 group-hover:from-cyan-500/10 group-hover:to-blue-500/10 rounded-2xl transition-all duration-300"></div>
                            <div class="relative">
                                <div class="w-14 h-14 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center mb-4 transform group-hover:rotate-12 transition-transform duration-300">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                </div>
                                <h3 class="font-bold text-gray-900 dark:text-white mb-2 text-lg">Infos exclusives</h3>
                                <p class="text-gray-600 dark:text-gray-300 text-sm">Restez informé du lancement en avant-première</p>
                            </div>
                        </div>
                    </div>

                    <!-- Registration Form -->
                    <form method="POST" action="{{ route('preregistration.store') }}" id="preregistrationForm" class="space-y-8">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Full Name -->
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-bold text-gray-900 dark:text-white mb-3">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-violet-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                        </svg>
                                        Nom complet <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <input type="text" 
                                       class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800 border-2 @error('name') border-red-400 @else border-gray-200 dark:border-gray-700 @enderror rounded-2xl focus:border-violet-500 focus:ring-4 focus:ring-violet-100 dark:focus:ring-violet-900/30 transition-all duration-300 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}"
                                       required
                                       placeholder="Ex: Jean Dupont">
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="md:col-span-2">
                                <label for="email" class="block text-sm font-bold text-gray-900 dark:text-white mb-3">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-violet-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                        </svg>
                                        Adresse email <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <input type="email" 
                                       class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800 border-2 @error('email') border-red-400 @else border-gray-200 dark:border-gray-700 @enderror rounded-2xl focus:border-violet-500 focus:ring-4 focus:ring-violet-100 dark:focus:ring-violet-900/30 transition-all duration-300 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}"
                                       required
                                       placeholder="votre.email@example.com">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-bold text-gray-900 dark:text-white mb-3">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-violet-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                        </svg>
                                        Téléphone <span class="text-gray-500 text-xs font-normal">(optionnel)</span>
                                    </span>
                                </label>
                                <input type="tel" 
                                       class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800 border-2 @error('phone') border-red-400 @else border-gray-200 dark:border-gray-700 @enderror rounded-2xl focus:border-violet-500 focus:ring-4 focus:ring-violet-100 dark:focus:ring-violet-900/30 transition-all duration-300 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone') }}"
                                       placeholder="+243812345678"
                                       pattern="^(\+?243|0)?[0-9]{9}$">
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Format: 0812345678 ou +243812345678</p>
                                @error('phone')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Country -->
                            <div>
                                <label for="country" class="block text-sm font-bold text-gray-900 dark:text-white mb-3">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-violet-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd"/>
                                        </svg>
                                        Pays <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <select class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800 border-2 @error('country') border-red-400 @else border-gray-200 dark:border-gray-700 @enderror rounded-2xl focus:border-violet-500 focus:ring-4 focus:ring-violet-100 dark:focus:ring-violet-900/30 transition-all duration-300 text-gray-900 dark:text-white" 
                                        id="country" 
                                        name="country" 
                                        required>
                                    <option value="Congo (RDC)" {{ old('country', 'Congo (RDC)') == 'Congo (RDC)' ? 'selected' : '' }}>🇨🇩 Congo (RDC)</option>
                                    <option value="Congo (Brazzaville)" {{ old('country') == 'Congo (Brazzaville)' ? 'selected' : '' }}>🇨🇬 Congo (Brazzaville)</option>
                                    <option value="France" {{ old('country') == 'France' ? 'selected' : '' }}>🇫🇷 France</option>
                                    <option value="Belgique" {{ old('country') == 'Belgique' ? 'selected' : '' }}>🇧🇪 Belgique</option>
                                    <option value="Canada" {{ old('country') == 'Canada' ? 'selected' : '' }}>🇨🇦 Canada</option>
                                    <option value="Autre" {{ old('country') == 'Autre' ? 'selected' : '' }}>🌍 Autre</option>
                                </select>
                                @error('country')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <!-- Motivations -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-4">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-violet-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                    </svg>
                                    Pourquoi voulez-vous rejoindre VintApp ?
                                    <span class="text-gray-500 text-xs font-normal">(optionnel)</span>
                                </span>
                        <!-- Motivations -->
                        <div>
                            <label for="reason" class="block text-sm font-bold text-gray-900 dark:text-white mb-3">
                               
                            </label>
                            <select class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800 border-2 @error('reasons') border-red-400 @else border-gray-200 dark:border-gray-700 @enderror rounded-2xl focus:border-violet-500 focus:ring-4 focus:ring-violet-100 dark:focus:ring-violet-900/30 transition-all duration-300 text-gray-900 dark:text-white" 
                                    id="reason" 
                                    name="reasons[]">
                                <option value="">Sélectionnez une raison</option>
                                <option value="Acheter des produits vintage de qualité" {{ is_array(old('reasons')) && in_array('Acheter des produits vintage de qualité', old('reasons')) ? 'selected' : '' }}>
                                    🛍️ Acheter des produits vintage
                                </option>
                                <option value="Vendre mes articles vintage" {{ is_array(old('reasons')) && in_array('Vendre mes articles vintage', old('reasons')) ? 'selected' : '' }}>
                                    💼 Vendre mes articles vintage
                                </option>
                                <option value="Développer mon business vintage" {{ is_array(old('reasons')) && in_array('Développer mon business vintage', old('reasons')) ? 'selected' : '' }}>
                                    🚀 Développer mon business
                                </option>
                                <option value="Rejoindre une communauté de passionnés" {{ is_array(old('reasons')) && in_array('Rejoindre une communauté de passionnés', old('reasons')) ? 'selected' : '' }}>
                                    👥 Rejoindre la communauté
                                </option>
                                <option value="Enrichir ma collection personnelle" {{ is_array(old('reasons')) && in_array('Enrichir ma collection personnelle', old('reasons')) ? 'selected' : '' }}>
                                    ⭐ Enrichir ma collection
                                </option>
                                <option value="Consommer de manière responsable et durable" {{ is_array(old('reasons')) && in_array('Consommer de manière responsable et durable', old('reasons')) ? 'selected' : '' }}>
                                    ♻️ Consommation responsable
                                </option>
                                <option value="Découvrir une nouvelle plateforme innovante" {{ is_array(old('reasons')) && in_array('Découvrir une nouvelle plateforme innovante', old('reasons')) ? 'selected' : '' }}>
                                    🔍 Curiosité et découverte
                                </option>
                            </select>
                            @error('reasons')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>


                        <!-- Submit Button -->
                        <button type="submit" 
                                class="group relative w-full bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 hover:from-violet-700 hover:via-purple-700 hover:to-indigo-700 text-white font-bold py-5 px-8 rounded-2xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 text-lg overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-yellow-400/20 via-pink-400/20 to-purple-400/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <span class="relative flex items-center justify-center gap-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Rejoindre VintApp maintenant !
                                <svg class="w-5 h-5 transform group-hover:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        </button>

                        <!-- Security Message -->
                        <div class="flex items-center justify-center gap-3 pt-4">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Vos données sont <span class="font-semibold text-gray-900 dark:text-white">100% sécurisées</span> et ne seront jamais partagées
                            </p>
                        </div>
                    </form>

                    <!-- Footer Links -->
                    <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-center space-y-4">
                            <a href="{{ route('preregistration.stats') }}" 
                               class="inline-flex items-center gap-2 text-violet-600 dark:text-violet-400 hover:text-violet-700 dark:hover:text-violet-300 font-semibold transition-colors group">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                                </svg>
                                <span>Voir les statistiques d'inscription</span>
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            
                            <div class="flex flex-wrap justify-center gap-6 text-sm">
                                <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-violet-600 dark:hover:text-violet-400 transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                    </svg>
                                    FAQ
                                </a>
                                <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-violet-600 dark:hover:text-violet-400 transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Confidentialité
                                </a>
                                <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-violet-600 dark:hover:text-violet-400 transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                    </svg>
                                    Contact
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- External Footer -->
            <div class="text-center mt-8 space-y-3">
                <p class="text-white/90 font-medium text-lg">
                    © {{ date('Y') }} VintApp. Tous droits réservés.
                </p>
                <p class="text-white/70 text-sm max-w-2xl mx-auto">
                    La marketplace du vintage qui révolutionne l'achat et la vente de pièces uniques
                </p>
            </div>
        </div>
    </div>

    <script>
        // Configuration Firebase (initialisée quand les scripts sont chargés)
        const firebaseConfig = {
            apiKey: "{{ env('FIREBASE_API_KEY') }}",
            authDomain: "{{ env('FIREBASE_AUTH_DOMAIN') }}",
            projectId: "{{ env('FIREBASE_PROJECT_ID') }}",
            storageBucket: "{{ env('FIREBASE_STORAGE_BUCKET') }}",
            messagingSenderId: "{{ env('FIREBASE_MESSAGING_SENDER_ID') }}",
            appId: "{{ env('FIREBASE_APP_ID') }}"
        };
        
        let auth, db;
        
        // Initialiser Firebase quand les scripts sont chargés
        function initializeFirebase() {
            if (typeof firebase === 'undefined') {
                console.error('❌ Firebase SDK non chargé');
                return false;
            }
            
            // Vérifier que la configuration est valide
            const configValid = firebaseConfig.apiKey && 
                              firebaseConfig.authDomain && 
                              firebaseConfig.projectId;
            
            if (!configValid) {
                console.error('❌ Configuration Firebase invalide. Vérifiez le fichier .env');
                return false;
            }
            
            try {
                firebase.initializeApp(firebaseConfig);
                auth = firebase.auth();
                db = firebase.firestore();

                return true;
            } catch (error) {
                console.error('❌ Erreur d\'initialisation Firebase:', error);
                return false;
            }
        }
        
        // Fonction utilitaire pour auto-dismiss des alertes
        function autoDismissAlerts() {
            const alerts = document.querySelectorAll('[class*="bg-emerald-50"], [class*="bg-red-50"]');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-20px)';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            // Initialiser Firebase
            const firebaseInitialized = initializeFirebase();
            
            if (!firebaseInitialized) {
                // Afficher un message d'erreur si Firebase n'est pas initialisé
                const errorDiv = document.createElement('div');
                errorDiv.className = 'mb-8 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-xl p-5';
                errorDiv.innerHTML = `
                    <div class="flex items-start gap-4">
                        <svg class="w-6 h-6 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1">
                            <p class="text-red-900 dark:text-red-100 font-semibold">Configuration Firebase manquante</p>
                            <p class="text-sm text-red-700 dark:text-red-300 mt-1">Veuillez configurer Firebase dans le fichier .env avant de pouvoir vous inscrire.</p>
                        </div>
                    </div>
                `;
                
                const formContainer = document.querySelector('form').parentElement;
                formContainer.insertBefore(errorDiv, formContainer.firstChild);
            }
            
            // Auto-dismiss alerts
            autoDismissAlerts();
            
            // Mise en cache des éléments DOM
            const form = document.getElementById('preregistrationForm');
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnContent = submitBtn.innerHTML;
            
            // Form submission with Firebase
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Vérifier que Firebase est initialisé
                if (!auth || !db) {
                    showErrorMessage('Erreur de chargement. Veuillez rafraîchir la page.');
                    return;
                }
                
                // Show loading state
                submitBtn.innerHTML = `
                    <span class="relative flex items-center justify-center gap-3">
                        <svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Inscription en cours...
                    </span>
                `;
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                
                try {
                    // Récupérer les données du formulaire
                    const formData = new FormData(form);
                    const name = formData.get('name');
                    const email = formData.get('email');
                    const phone = formData.get('phone') || '';
                    const country = formData.get('country');
                    const reasons = formData.getAll('reasons[]');
                    
                    // Générer un UID temporaire unique (sans créer de compte Firebase Auth)
                    const firebaseUid = 'temp_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                    
                    // Enregistrer dans Firestore (optionnel - pour analytics)
                    console.log('🔄 Tentative d\'enregistrement dans Firestore...');
                    try {
                        await db.collection('preregistrations').doc(firebaseUid).set({
                            uid: firebaseUid,
                            name: name,
                            email: email,
                            phone: phone,
                            country: country,
                            reasons: reasons,
                            status: 'pending',
                            approved: false,
                            createdAt: firebase.firestore.FieldValue.serverTimestamp(),
                            accountType: 'preregistration'
                        });
                        console.log('✅ Enregistrement Firestore réussi');
                    } catch (firestoreError) {
                        console.warn('⚠️ Erreur Firestore (non critique):', firestoreError);
                        // Continuer même si Firestore échoue
                    }
                    
                    // Enregistrer dans la DB Laravel via AJAX
                    console.log('🔄 Envoi de la requête AJAX vers Laravel...');
                    console.log('📤 Données envoyées:', {
                        name, email, phone, country, reasons, firebase_uid: firebaseUid
                    });
                    
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                     document.querySelector('input[name="_token"]')?.value;
                    
                    console.log('🔑 CSRF Token trouvé:', csrfToken ? 'Oui' : 'Non', csrfToken);
                    console.log('🎯 URL cible:', form.action);
                    
                    // Ajouter un timeout
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 secondes
                    
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name: name,
                            email: email,
                            phone: phone,
                            country: country,
                            reasons: reasons,
                            firebase_uid: firebaseUid
                        }),
                        signal: controller.signal
                    });
                    
                    clearTimeout(timeoutId);
                    console.log('📥 Réponse reçue:', response.status, response.statusText);
                    
                    if (!response.ok) {
                        console.error('❌ Réponse HTTP non OK:', response.status, response.statusText);
                        const errorData = await response.json();
                        console.error('📄 Données d\'erreur:', errorData);
                        
                        // Si c'est une erreur de validation Laravel
                        if (errorData.errors) {
                            const errorMessages = Object.values(errorData.errors).flat();
                            throw new Error(errorMessages.join('. '));
                        }
                        
                        throw new Error(errorData.message || 'Erreur lors de l\'enregistrement dans la base de données');
                    }
                    
                    const responseData = await response.json();
                    console.log('✅ Réponse Laravel réussie:', responseData);
                    
                    // Afficher le succès
                    showSuccessMessage('Inscription réussie ! Vous recevrez un email de confirmation avec vos identifiants.');
                    
                    // Réinitialiser le formulaire
                    form.reset();
                    
                    // Rediriger vers la page de stats après 3 secondes
                    setTimeout(() => {
                        window.location.href = "{{ route('preregistration.stats') }}";
                    }, 3000);
                    
                } catch (error) {
                    console.error('❌ Erreur complète:', error);
                    
                    // Messages d'erreur personnalisés
                    let errorMessage = 'Une erreur est survenue lors de l\'inscription.';
                    
                    if (error.name === 'AbortError') {
                        errorMessage = 'La requête a pris trop de temps. Vérifiez votre connexion internet.';
                    } else if (error.message) {
                        errorMessage = error.message;
                    }
                    
                    console.error('💬 Message d\'erreur affiché:', errorMessage);
                    showErrorMessage(errorMessage);
                    
                    // Restaurer le bouton
                    submitBtn.innerHTML = originalBtnContent;
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                }
            });
            
            // Fonction générique pour créer des alertes
            function createAlert(type, message, autoDismissDelay = 5000) {
                const config = {
                    success: {
                        bgClass: 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-500',
                        iconColor: 'text-emerald-500',
                        textColor: 'text-emerald-900 dark:text-emerald-100',
                        iconPath: 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z',
                        showCloseBtn: false
                    },
                    error: {
                        bgClass: 'bg-red-50 dark:bg-red-900/20 border-red-500',
                        iconColor: 'text-red-500',
                        textColor: 'text-red-900 dark:text-red-100',
                        iconPath: 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z',
                        showCloseBtn: true
                    }
                };
                
                const alertConfig = config[type];
                const alertDiv = document.createElement('div');
                alertDiv.className = `mb-8 ${alertConfig.bgClass} border-l-4 rounded-xl p-5 flex items-start gap-4 animate-pulse`;
                
                const closeButton = alertConfig.showCloseBtn ? `
                    <button onclick="this.parentElement.remove()" class="${alertConfig.iconColor} hover:opacity-70 transition-opacity">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                ` : '';
                
                alertDiv.innerHTML = `
                    <svg class="w-6 h-6 ${alertConfig.iconColor} flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="${alertConfig.iconPath}" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <p class="${alertConfig.textColor} font-semibold">${message}</p>
                    </div>
                    ${closeButton}
                `;
                
                const formContainer = form.parentElement;
                formContainer.insertBefore(alertDiv, formContainer.firstChild);
                
                // Auto-dismiss
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
            
            // Fonction pour afficher un message de succès
            function showSuccessMessage(message) {
                return createAlert('success', message, 5000);
            }
            
            // Fonction pour afficher un message d'erreur
            function showErrorMessage(message) {
                return createAlert('error', message, 8000);
            }
        });
    </script>
</body>
</html>
