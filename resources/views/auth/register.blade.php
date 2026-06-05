@extends('app')

@section('title', 'Inscription - VintApp')

@section('content')

<!-- Firebase Scripts -->
<script src="https://www.gstatic.com/firebasejs/9.17.1/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.17.1/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.17.1/firebase-firestore-compat.js"></script>

<!-- Toast Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 hidden items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-2xl flex flex-col items-center gap-4">
        <div class="w-12 h-12 border-4 border-purple-200 border-t-purple-600 rounded-full animate-spin"></div>
        <p class="text-gray-700 dark:text-gray-200 font-medium">Inscription en cours...</p>
    </div>
</div>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex">

    <!-- Panneau gauche décoratif (desktop) -->
    <div class="hidden lg:flex lg:w-5/12 relative overflow-hidden bg-gradient-to-br from-purple-700 via-purple-800 to-gray-900 items-center justify-center">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-1/4 -left-20 w-80 h-80 bg-pink-500 rounded-full blur-3xl"></div>
            <div class="absolute bottom-1/3 right-0 w-96 h-96 bg-purple-400 rounded-full blur-3xl"></div>
            <div class="absolute top-2/3 left-1/3 w-64 h-64 bg-fuchsia-400 rounded-full blur-3xl"></div>
        </div>
        <div class="absolute inset-0 opacity-5 bg-[linear-gradient(rgba(255,255,255,.1)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,.1)_1px,transparent_1px)] bg-[size:40px_40px]"></div>

        <div class="relative z-10 max-w-sm px-10 text-white space-y-8">
            <div class="w-14 h-14 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl flex items-center justify-center">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold leading-tight">
                Rejoignez la communauté
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-300 to-pink-300">VintApp</span>
            </h2>
            <p class="text-white/60 text-lg leading-relaxed">
                Achetez et vendez des pièces vintage uniques. Créez votre compte en quelques secondes.
            </p>
            <div class="space-y-4 pt-2">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-white/70 text-sm">Inscription gratuite et rapide</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-white/70 text-sm">Articles authentifiés par nos experts</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-white/70 text-sm">Paiements sécurisés</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Panneau droit : formulaire -->
    <div class="flex-1 flex items-center justify-center p-6 sm:p-8 overflow-y-auto">
        <div class="w-full max-w-lg py-8">

            <!-- Logo mobile -->
            <div class="lg:hidden text-center mb-6">
                <div class="w-14 h-14 bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg shadow-purple-500/25">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>

            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Créer un compte</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-2">Rejoignez VintApp en quelques étapes</p>
            </div>

            <!-- Boutons sociaux en haut -->
            <div class="space-y-3 mb-6">
                <button onclick="signUpWithGoogle()" 
                        type="button"
                        class="w-full flex items-center justify-center gap-3 px-4 py-3 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-750 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-purple-500/20">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285f4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34a853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#fbbc05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#ea4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span class="text-gray-700 dark:text-gray-200 font-medium text-sm">Continuer avec Google</span>
                </button>

                <button onclick="signUpWithFacebook()" 
                        type="button"
                        class="w-full flex items-center justify-center gap-3 px-4 py-3 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-750 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-purple-500/20">
                    <svg class="w-5 h-5" fill="#1877F2" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span class="text-gray-700 dark:text-gray-200 font-medium text-sm">Continuer avec Facebook</span>
                </button>
            </div>

            <!-- Séparateur -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-3 bg-gray-50 dark:bg-gray-900 text-gray-400 font-medium uppercase tracking-wider">ou par email</span>
                </div>
            </div>

            <!-- Formulaire d'inscription -->
            <div class="space-y-4">
                <!-- Nom + Email en grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="firebase-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Nom complet
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input type="text" 
                                   id="firebase-name" 
                                   required
                                   class="w-full pl-10 pr-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-200"
                                   placeholder="Votre nom">
                        </div>
                    </div>

                    <div>
                        <label for="firebase-email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Adresse email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="email" 
                                   id="firebase-email" 
                                   required
                                   class="w-full pl-10 pr-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-200"
                                   placeholder="nom@exemple.com">
                        </div>
                    </div>
                </div>

                <!-- Téléphone + Code parrainage en grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="firebase-phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Téléphone <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <input type="tel" 
                                   id="firebase-phone"
                                   class="w-full pl-10 pr-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-200"
                                   placeholder="+243 89 612 345 67">
                        </div>
                    </div>

                    <div>
                        <label for="firebase-referral-code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Code parrainage <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                </svg>
                            </div>
                            <input type="text" 
                                   id="firebase-referral-code"
                                   value="{{ session('referral_code', '') }}"
                                   class="w-full pl-10 pr-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-200 uppercase"
                                   placeholder="CODE123">
                        </div>
                    </div>
                </div>

                <!-- Mot de passe -->
                <div>
                    <label for="firebase-password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Mot de passe
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input type="password" 
                               id="firebase-password" 
                               required
                               class="w-full pl-10 pr-11 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-200"
                               placeholder="Min. 12 caractères, chiffres et symboles">
                        <button type="button" onclick="togglePassword('firebase-password', 'pw-eye-1')" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <svg id="pw-eye-1" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Barre de force -->
                    <div id="password-strength" class="mt-2 h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div id="password-strength-bar" class="h-full transition-all duration-300 rounded-full" style="width:0%"></div>
                    </div>
                    
                    <!-- Critères de sécurité en grid compact -->
                    <div class="mt-2.5 grid grid-cols-2 gap-x-4 gap-y-1.5">
                        <div class="flex items-center text-xs">
                            <svg id="length-check" class="w-3.5 h-3.5 mr-1.5 text-gray-300 transition-colors flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <circle cx="10" cy="10" r="5"/>
                            </svg>
                            <span class="text-gray-500 dark:text-gray-400">12+ caractères</span>
                        </div>
                        <div class="flex items-center text-xs">
                            <svg id="number-check" class="w-3.5 h-3.5 mr-1.5 text-gray-300 transition-colors flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <circle cx="10" cy="10" r="5"/>
                            </svg>
                            <span class="text-gray-500 dark:text-gray-400">1+ chiffre</span>
                        </div>
                        <div class="flex items-center text-xs">
                            <svg id="symbol-check" class="w-3.5 h-3.5 mr-1.5 text-gray-300 transition-colors flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <circle cx="10" cy="10" r="5"/>
                            </svg>
                            <span class="text-gray-500 dark:text-gray-400">2+ symboles</span>
                        </div>
                        <div class="flex items-center text-xs">
                            <svg id="case-check" class="w-3.5 h-3.5 mr-1.5 text-gray-300 transition-colors flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <circle cx="10" cy="10" r="5"/>
                            </svg>
                            <span class="text-gray-500 dark:text-gray-400">Maj. et min.</span>
                        </div>
                    </div>
                </div>

                <!-- Confirmation mot de passe -->
                <div>
                    <label for="firebase-password-confirm" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Confirmer le mot de passe
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input type="password" 
                               id="firebase-password-confirm" 
                               required
                               class="w-full pl-10 pr-11 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-200"
                               placeholder="Répétez votre mot de passe">
                        <button type="button" onclick="togglePassword('firebase-password-confirm', 'pw-eye-2')" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <svg id="pw-eye-2" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <p id="confirm-mismatch" class="mt-1.5 text-xs text-red-500 hidden">Les mots de passe ne correspondent pas</p>
                </div>

                <!-- Cases à cocher -->
                <div class="space-y-3 pt-1">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" 
                               id="firebase-terms" 
                               required
                               class="w-4 h-4 text-purple-600 border-gray-300 dark:border-gray-600 rounded focus:ring-purple-500 focus:ring-offset-0 mt-0.5">
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            J'accepte les 
                            <button type="button" onclick="showTermsModal()" class="text-purple-600 dark:text-purple-400 hover:text-purple-700 font-medium">
                                conditions d'utilisation
                            </button> 
                            et la 
                            <button type="button" onclick="showPrivacyModal()" class="text-purple-600 dark:text-purple-400 hover:text-purple-700 font-medium">
                                politique de confidentialité
                            </button>
                        </span>
                    </label>
                    
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" 
                               id="firebase-newsletter"
                               class="w-4 h-4 text-purple-600 border-gray-300 dark:border-gray-600 rounded focus:ring-purple-500 focus:ring-offset-0 mt-0.5">
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            Recevoir les newsletters et offres spéciales
                        </span>
                    </label>
                </div>

                <!-- Bouton principal -->
                <button id="register-btn" onclick="registerWithFirebaseEmail()" 
                        class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg shadow-purple-500/25 hover:shadow-xl hover:shadow-purple-500/30 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-purple-500/50 text-sm">
                    Créer mon compte
                </button>
            </div>

            <!-- Lien connexion -->
            <p class="text-center mt-6 text-sm text-gray-500 dark:text-gray-400">
                Déjà un compte ?
                <a href="{{ route('login') }}" class="text-purple-600 dark:text-purple-400 hover:text-purple-700 font-semibold transition-colors">
                    Se connecter
                </a>
            </p>

            <div class="text-center mt-4">
                <button onclick="showClassicRegister()" class="text-xs text-gray-400 dark:text-gray-500 hover:text-purple-600 transition-colors">
                    Utiliser le formulaire classique
                </button>
            </div>

            <!-- Sécurité -->
            <div class="text-center mt-6">
                <p class="text-xs text-gray-400 dark:text-gray-500 flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Vos données sont protégées et sécurisées
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Conditions d'utilisation -->
<div id="terms-modal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full max-h-[80vh] overflow-y-auto shadow-2xl">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Conditions d'utilisation
                </h3>
                <button onclick="hideTermsModal()" class="w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">1. Acceptation des conditions</h4>
                <p class="text-gray-600 dark:text-gray-300 text-sm">En utilisant VintApp, vous acceptez d'être lié par ces conditions d'utilisation.</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">2. Utilisation du service</h4>
                <p class="text-gray-600 dark:text-gray-300 text-sm">Vous vous engagez à utiliser le service de manière légale et éthique.</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">3. Responsabilités</h4>
                <p class="text-gray-600 dark:text-gray-300 text-sm">Vous êtes responsable du contenu que vous publiez et des transactions que vous effectuez.</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">4. Confidentialité</h4>
                <p class="text-gray-600 dark:text-gray-300 text-sm">Nous nous engageons à protéger vos données personnelles conformément à notre politique de confidentialité.</p>
            </div>
        </div>
        <div class="p-6 border-t border-gray-200 dark:border-gray-700">
            <button onclick="hideTermsModal()" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-xl font-medium hover:from-purple-700 hover:to-pink-700 transition-all">
                Fermer
            </button>
        </div>
    </div>
</div>

<!-- Modal Politique de confidentialité -->
<div id="privacy-modal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full max-h-[80vh] overflow-y-auto shadow-2xl">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Politique de confidentialité
                </h3>
                <button onclick="hidePrivacyModal()" class="w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">1. Collecte des données</h4>
                <p class="text-gray-600 dark:text-gray-300 text-sm">Nous collectons uniquement les données nécessaires au fonctionnement du service.</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">2. Utilisation des données</h4>
                <p class="text-gray-600 dark:text-gray-300 text-sm">Vos données sont utilisées pour améliorer votre expérience et sécuriser le service.</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">3. Protection des données</h4>
                <p class="text-gray-600 dark:text-gray-300 text-sm">Nous mettons en place des mesures de sécurité appropriées pour protéger vos données.</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-2">4. Vos droits</h4>
                <p class="text-gray-600 dark:text-gray-300 text-sm">Vous avez le droit d'accéder, de modifier et de supprimer vos données personnelles.</p>
            </div>
        </div>
        <div class="p-6 border-t border-gray-200 dark:border-gray-700">
            <button onclick="hidePrivacyModal()" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-xl font-medium hover:from-purple-700 hover:to-pink-700 transition-all">
                Fermer
            </button>
        </div>
    </div>
</div>

<!-- Modal Formulaire Classique -->
<div id="classic-register-modal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full max-h-[80vh] overflow-y-auto shadow-2xl">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Inscription classique</h3>
                <button onclick="hideClassicRegister()" class="w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nom complet</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full px-3 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-purple-500 focus:border-purple-500 focus:outline-none">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-3 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-purple-500 focus:border-purple-500 focus:outline-none">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Téléphone</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required
                           class="w-full px-3 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-purple-500 focus:border-purple-500 focus:outline-none">
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Adresse</label>
                    <textarea name="address" required class="w-full px-3 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-purple-500 focus:border-purple-500 focus:outline-none" rows="3">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Code de parrainage (optionnel)</label>
                    <input type="text" name="referral_code" value="{{ old('referral_code') }}"
                           class="w-full px-3 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-purple-500 focus:border-purple-500 focus:outline-none uppercase">
                    @error('referral_code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Mot de passe</label>
                        <input type="password" name="password" required
                               class="w-full px-3 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-purple-500 focus:border-purple-500 focus:outline-none">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Confirmer mot de passe</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full px-3 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-purple-500 focus:border-purple-500 focus:outline-none">
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="terms" required class="w-4 h-4 text-purple-600 border-gray-300 dark:border-gray-600 rounded focus:ring-purple-500">
                    <label class="text-sm text-gray-600 dark:text-gray-400">J'accepte les conditions d'utilisation</label>
                </div>
                
                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-xl font-medium hover:from-purple-700 hover:to-pink-700 transition-all">
                    Créer le compte
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// ============ FIREBASE CONFIG ============
const firebaseConfig = {
    apiKey: "{{ config('firebase.web_config.apiKey') }}",
    authDomain: "{{ config('firebase.web_config.authDomain') }}",
    projectId: "{{ config('firebase.web_config.projectId') }}",
    storageBucket: "{{ config('firebase.web_config.storageBucket') }}",
    messagingSenderId: "{{ config('firebase.web_config.messagingSenderId') }}",
    appId: "{{ config('firebase.web_config.appId') }}"
};

try {
    if (!firebase.apps.length) {
        firebase.initializeApp(firebaseConfig);
    }
} catch (error) {
    console.error('Firebase init error:', error);
    showToast('Erreur de configuration Firebase.', 'error');
}

// ============ EMAIL REGISTRATION ============
window.registerWithFirebaseEmail = async function() {
    const name = document.getElementById('firebase-name').value.trim();
    const email = document.getElementById('firebase-email').value.trim();
    const password = document.getElementById('firebase-password').value;
    const confirmPassword = document.getElementById('firebase-password-confirm').value;
    const phone = document.getElementById('firebase-phone').value.trim();
    const referralCode = document.getElementById('firebase-referral-code').value.trim().toUpperCase();
    const termsAccepted = document.getElementById('firebase-terms').checked;
    const newsletter = document.getElementById('firebase-newsletter').checked;
    
    if (!name || !email || !password || !confirmPassword) {
        showToast('Veuillez remplir tous les champs obligatoires', 'error');
        return;
    }
    
    if (password !== confirmPassword) {
        showToast('Les mots de passe ne correspondent pas', 'error');
        return;
    }
    
    if (!validatePasswordStrength(password)) {
        showToast('Mot de passe non sécurisé : min 12 car., 1 chiffre, 2 symboles, maj./min.', 'error');
        return;
    }
    
    if (!termsAccepted) {
        showToast('Vous devez accepter les conditions d\'utilisation', 'error');
        return;
    }
    
    showLoading(true);
    
    try {
        const userCredential = await firebase.auth().createUserWithEmailAndPassword(email, password);
        const user = userCredential.user;
        
        await user.updateProfile({ displayName: name });
        await user.sendEmailVerification();
        
        const idToken = await user.getIdToken(true);
        
        const response = await fetch('{{ route("auth.firebase.login") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                idToken: idToken,
                name: name,
                phone: phone,
                referral_code: referralCode,
                newsletter: newsletter,
                provider: 'email'
            })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            showLoading(false);
            showToast('Inscription réussie ! Vérifiez votre email.', 'success');
            window.location.href = '{{ route("verification.code") }}';
        } else {
            throw new Error(data.message || 'Erreur lors de l\'inscription');
        }
        
    } catch (error) {
        showLoading(false);
        
        // DEBUG: afficher l'erreur complète dans la console
        console.error('Firebase signUp error:', {
            code: error.code,
            message: error.message,
            fullError: error
        });
        
        const messages = {
            'auth/email-already-in-use': 'Cette adresse email est déjà utilisée',
            'auth/weak-password': 'Le mot de passe est trop faible',
            'auth/invalid-email': 'Adresse email invalide',
            'auth/operation-not-allowed': 'L\'inscription par email est désactivée',
            'auth/admin-restricted-operation': 'Opération restreinte par l\'administrateur',
            'auth/invalid-api-key': 'Clé API Firebase invalide',
            'auth/api-key-not-valid': 'Clé API Firebase non valide pour ce projet',
            'auth/too-many-requests': 'Trop de tentatives, réessayez plus tard',
            'auth/network-request-failed': 'Erreur réseau, vérifiez votre connexion'
        };
        
        showToast(messages[error.code] || error.message || 'Erreur lors de l\'inscription', 'error');
    }
};

// ============ GOOGLE SIGNUP ============
window.signUpWithGoogle = async function() {
    showLoading(true);
    
    try {
        if (!firebase.apps.length) throw new Error('Firebase non initialisé');
        
        const provider = new firebase.auth.GoogleAuthProvider();
        provider.addScope('email');
        provider.addScope('profile');
        provider.setCustomParameters({ prompt: 'select_account' });
        
        const result = await firebase.auth().signInWithPopup(provider);
        const user = result.user;
        if (!user) throw new Error('Aucune information utilisateur reçue');
        
        const idToken = await user.getIdToken(true);
        
        const response = await fetch('{{ route("auth.firebase.login") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                idToken: idToken,
                name: user.displayName || 'Utilisateur Google',
                email: user.email,
                provider: 'google',
                firebase_uid: user.uid,
                photo_url: user.photoURL,
                newsletter: false
            })
        });
        
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Réponse serveur invalide. Veuillez réessayer.');
        }
        
        const data = await response.json();
        
        if (response.ok) {
            showLoading(false);
            showToast('Inscription Google réussie !', 'success');
            setTimeout(() => { window.location.href = data.redirect || '{{ route("verification.code") }}'; }, 800);
        } else {
            throw new Error(data.message || 'Erreur lors de l\'inscription');
        }
        
    } catch (error) {
        showLoading(false);
        
        const messages = {
            'auth/popup-closed-by-user': 'Connexion annulée',
            'auth/popup-blocked': 'Popup bloqué. Autorisez les popups pour ce site.',
            'auth/account-exists-with-different-credential': 'Un compte existe déjà avec cette adresse email',
            'auth/too-many-requests': 'Trop de tentatives. Réessayez plus tard.'
        };
        
        showToast(messages[error.code] || error.message || 'Erreur lors de l\'inscription Google', 'error');
    }
};

// ============ FACEBOOK SIGNUP ============
window.signUpWithFacebook = async function() {
    showLoading(true);
    
    try {
        const provider = new firebase.auth.FacebookAuthProvider();
        provider.addScope('email');
        
        const result = await firebase.auth().signInWithPopup(provider);
        const user = result.user;
        if (!user) throw new Error('Aucune information utilisateur reçue');
        
        const idToken = await user.getIdToken(true);
        
        const response = await fetch('{{ route("auth.firebase.login") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                idToken: idToken,
                name: user.displayName || 'Utilisateur Facebook',
                email: user.email,
                provider: 'facebook',
                firebase_uid: user.uid,
                photo_url: user.photoURL,
                newsletter: false
            })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            showLoading(false);
            showToast('Inscription Facebook réussie !', 'success');
            setTimeout(() => { window.location.href = data.redirect || '{{ route("verification.code") }}'; }, 800);
        } else {
            throw new Error(data.message || 'Erreur lors de l\'inscription');
        }
        
    } catch (error) {
        showLoading(false);
        const msg = error.code === 'auth/popup-closed-by-user' ? 'Connexion annulée' : 
                    error.code === 'auth/account-exists-with-different-credential' ? 'Un compte existe déjà avec cette adresse email' :
                    (error.message || 'Erreur lors de l\'inscription Facebook');
        showToast(msg, 'error');
    }
};

// ============ MODALS ============
function toggleModal(id, show) {
    const el = document.getElementById(id);
    if (show) { el.classList.remove('hidden'); el.classList.add('flex'); }
    else { el.classList.add('hidden'); el.classList.remove('flex'); }
}
window.showTermsModal = () => toggleModal('terms-modal', true);
window.hideTermsModal = () => toggleModal('terms-modal', false);
window.showPrivacyModal = () => toggleModal('privacy-modal', true);
window.hidePrivacyModal = () => toggleModal('privacy-modal', false);
window.showClassicRegister = () => toggleModal('classic-register-modal', true);
window.hideClassicRegister = () => toggleModal('classic-register-modal', false);

// ============ UI HELPERS ============
function togglePassword(inputId, eyeId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(eyeId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    }
}

function showLoading(show) {
    const overlay = document.getElementById('loading-overlay');
    if (show) { overlay.classList.remove('hidden'); overlay.classList.add('flex'); }
    else { overlay.classList.add('hidden'); overlay.classList.remove('flex'); }
}

function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    const colors = { success: 'bg-emerald-600', error: 'bg-red-600', warning: 'bg-amber-600', info: 'bg-purple-600' };
    const icons = {
        success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
        error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
        warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>',
        info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
    };
    
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `${colors[type]} text-white px-5 py-3 rounded-xl shadow-lg flex items-center gap-3 transform translate-x-full transition-transform duration-300 text-sm`;
    toast.innerHTML = `
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">${icons[type]}</svg>
        <span class="font-medium flex-1">${message}</span>
        <button onclick="document.getElementById('${toastId}').remove()" class="hover:opacity-75 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    
    container.appendChild(toast);
    requestAnimationFrame(() => { toast.classList.remove('translate-x-full'); });
    
    setTimeout(() => {
        const el = document.getElementById(toastId);
        if (el) { el.classList.add('translate-x-full'); setTimeout(() => el.remove(), 300); }
    }, 4000);
}

// ============ PASSWORD VALIDATION ============
function validatePasswordStrength(password) {
    const symbols = password.match(/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]/g);
    return password.length >= 12 && /[0-9]/.test(password) && /[A-Z]/.test(password) && /[a-z]/.test(password) && symbols && symbols.length >= 2;
}

function checkPasswordStrength(password) {
    const bar = document.getElementById('password-strength-bar');
    const checks = {
        length: { el: document.getElementById('length-check'), pass: password.length >= 12 },
        number: { el: document.getElementById('number-check'), pass: /[0-9]/.test(password) },
        symbol: { el: document.getElementById('symbol-check'), pass: (password.match(/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]/g) || []).length >= 2 },
        case:   { el: document.getElementById('case-check'), pass: /[A-Z]/.test(password) && /[a-z]/.test(password) }
    };
    
    let score = 0;
    const checkIcon = '<path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>';
    const circleIcon = '<circle cx="10" cy="10" r="5"/>';
    
    Object.values(checks).forEach(c => {
        if (c.pass) {
            score++;
            c.el.classList.remove('text-gray-300', 'text-red-400');
            c.el.classList.add('text-emerald-500');
            c.el.innerHTML = checkIcon;
        } else if (password.length > 0) {
            c.el.classList.remove('text-gray-300', 'text-emerald-500');
            c.el.classList.add('text-red-400');
            c.el.innerHTML = circleIcon;
        } else {
            c.el.classList.remove('text-red-400', 'text-emerald-500');
            c.el.classList.add('text-gray-300');
            c.el.innerHTML = circleIcon;
        }
    });
    
    const pct = (score / 4) * 100;
    bar.style.width = pct + '%';
    const barColors = ['bg-transparent', 'bg-red-500', 'bg-orange-500', 'bg-amber-500', 'bg-emerald-500'];
    bar.className = `h-full transition-all duration-300 rounded-full ${barColors[score]}`;
}

// ============ DOM EVENTS ============
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('firebase-password');
    const confirmInput = document.getElementById('firebase-password-confirm');
    const referralInput = document.getElementById('firebase-referral-code');
    
    passwordInput.addEventListener('input', function() {
        checkPasswordStrength(this.value);
    });
    
    confirmInput.addEventListener('input', function() {
        const mismatch = document.getElementById('confirm-mismatch');
        if (this.value && passwordInput.value !== this.value) {
            this.classList.add('border-red-400');
            this.classList.remove('border-gray-200', 'dark:border-gray-700');
            mismatch.classList.remove('hidden');
        } else {
            this.classList.remove('border-red-400');
            this.classList.add('border-gray-200');
            mismatch.classList.add('hidden');
        }
    });
    
    referralInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    // ESC to close modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { hideTermsModal(); hidePrivacyModal(); hideClassicRegister(); }
    });
    
    // Click outside to close modals
    ['terms-modal', 'privacy-modal', 'classic-register-modal'].forEach(id => {
        document.getElementById(id).addEventListener('click', function(e) {
            if (e.target === this) toggleModal(id, false);
        });
    });
    
    document.getElementById('firebase-name').focus();
});
</script>

@endsection
