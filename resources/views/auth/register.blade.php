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
<div id="loading-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 hidden items-center justify-center">
    <div class="text-center">
        <div class="flex justify-center space-x-2 mb-4">
            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse" style="animation-delay: 0.2s;"></div>
            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse" style="animation-delay: 0.4s;"></div>
        </div>
        <p class="text-white font-medium">Inscription en cours...</p>
    </div>
</div>

<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50 flex items-center justify-center p-4">
    <div class="w-full max-w-lg">
        <!-- Card Principal -->
        <div class="bg-white/95 backdrop-blur-sm rounded-3xl shadow-2xl p-8 transform transition-all duration-700 animate-fade-in-up">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-r from-green-600 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Inscription</h1>
                <p class="text-gray-600">Créez votre compte VintApp</p>
            </div>

            <!-- Formulaire d'inscription -->
            <div class="space-y-6">
                <!-- Nom complet -->
                <div>
                    <label for="firebase-name" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Nom complet
                    </label>
                    <input type="text" 
                           id="firebase-name" 
                           required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-green-500 focus:outline-none focus:ring-4 focus:ring-green-100 transition-all duration-300"
                           placeholder="Votre nom complet">
                </div>

                <!-- Email -->
                <div>
                    <label for="firebase-email" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                        Adresse email
                    </label>
                    <input type="email" 
                           id="firebase-email" 
                           required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-green-500 focus:outline-none focus:ring-4 focus:ring-green-100 transition-all duration-300"
                           placeholder="nom@exemple.com">
                </div>

                <!-- Téléphone -->
                <div>
                    <label for="firebase-phone" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        Téléphone (optionnel)
                    </label>
                    <input type="tel" 
                           id="firebase-phone"
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-green-500 focus:outline-none focus:ring-4 focus:ring-green-100 transition-all duration-300"
                           placeholder="+243 89 612 345 67">
                </div>

                <!-- Code de parrainage -->
                <div>
                    <label for="firebase-referral-code" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                        </svg>
                        Code de parrainage (optionnel)
                    </label>
                    <input type="text" 
                           id="firebase-referral-code"
                           value="{{ session('referral_code', '') }}"
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-green-500 focus:outline-none focus:ring-4 focus:ring-green-100 transition-all duration-300 uppercase"
                           placeholder="CODE123">
                    <p class="text-xs text-gray-500 mt-1 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Obtenez des points bonus avec un code de parrainage
                    </p>
                </div>

                <!-- Mot de passe -->
                <div>
                    <label for="firebase-password" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Mot de passe sécurisé
                    </label>
                    <input type="password" 
                           id="firebase-password" 
                           required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-green-500 focus:outline-none focus:ring-4 focus:ring-green-100 transition-all duration-300"
                           placeholder="Minimum 12 caractères avec chiffres et symboles">
                    <div id="password-strength" class="mt-2 h-1 bg-gray-200 rounded-full overflow-hidden">
                        <div id="password-strength-bar" class="h-full transition-all duration-300 rounded-full"></div>
                    </div>
                    
                    <!-- Critères de sécurité -->
                    <div class="mt-3 space-y-2">
                        <div class="flex items-center text-xs">
                            <div id="length-check" class="w-3 h-3 rounded-full bg-gray-300 mr-2"></div>
                            <span class="text-gray-600">Au moins 12 caractères</span>
                        </div>
                        <div class="flex items-center text-xs">
                            <div id="number-check" class="w-3 h-3 rounded-full bg-gray-300 mr-2"></div>
                            <span class="text-gray-600">Au moins 1 chiffre</span>
                        </div>
                        <div class="flex items-center text-xs">
                            <div id="symbol-check" class="w-3 h-3 rounded-full bg-gray-300 mr-2"></div>
                            <span class="text-gray-600">Au moins 2 symboles (!@#$%^&*)</span>
                        </div>
                        <div class="flex items-center text-xs">
                            <div id="case-check" class="w-3 h-3 rounded-full bg-gray-300 mr-2"></div>
                            <span class="text-gray-600">Majuscules et minuscules</span>
                        </div>
                    </div>
                </div>

                <!-- Confirmation mot de passe -->
                <div>
                    <label for="firebase-password-confirm" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Confirmer le mot de passe
                    </label>
                    <input type="password" 
                           id="firebase-password-confirm" 
                           required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-green-500 focus:outline-none focus:ring-4 focus:ring-green-100 transition-all duration-300"
                           placeholder="Répétez votre mot de passe">
                </div>

                <!-- Cases à cocher -->
                <div class="space-y-3">
                    <label class="flex items-start space-x-3">
                        <input type="checkbox" 
                               id="firebase-terms" 
                               required
                               class="w-5 h-5 text-green-600 border-2 border-gray-300 rounded focus:ring-green-500 focus:ring-2 mt-0.5">
                        <span class="text-sm text-gray-700">
                            J'accepte les 
                            <button type="button" onclick="showTermsModal()" class="text-green-600 hover:text-green-800 underline">
                                conditions d'utilisation
                            </button> 
                            et la 
                            <button type="button" onclick="showPrivacyModal()" class="text-green-600 hover:text-green-800 underline">
                                politique de confidentialité
                            </button>
                        </span>
                    </label>
                    
                    <label class="flex items-start space-x-3">
                        <input type="checkbox" 
                               id="firebase-newsletter"
                               class="w-5 h-5 text-green-600 border-2 border-gray-300 rounded focus:ring-green-500 focus:ring-2 mt-0.5">
                        <span class="text-sm text-gray-700">
                            Je souhaite recevoir les newsletters et offres spéciales
                        </span>
                    </label>
                </div>

                <!-- Bouton principal -->
                <button onclick="registerWithFirebaseEmail()" 
                        class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white font-bold py-4 rounded-xl hover:from-green-700 hover:to-emerald-700 transform hover:-translate-y-1 hover:shadow-xl transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-green-300">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Créer mon compte
                </button>
            </div>

            <!-- Séparateur -->
            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-500 font-medium">OU</span>
                </div>
            </div>

            <!-- Boutons sociaux -->
            <div class="space-y-3">
                <!-- Google -->
                <button onclick="signUpWithGoogle()" 
                        class="w-full flex items-center justify-center px-4 py-3 border-2 border-gray-200 rounded-xl hover:border-gray-300 hover:shadow-md transform hover:-translate-y-1 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-gray-300">
                    <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                        <path fill="#4285f4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34a853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#fbbc05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#ea4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span class="text-gray-700 font-medium">Continuer avec Google</span>
                </button>

                <!-- Facebook -->
                <button onclick="signUpWithFacebook()" 
                        class="w-full flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transform hover:-translate-y-1 hover:shadow-xl transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-blue-300">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span class="font-medium">Continuer avec Facebook</span>
                </button>

                <!-- Apple (désactivé) -->
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Apple Sign-In</p>
                            <p class="text-xs text-gray-500">Nécessite un compte développeur Apple (99$/an)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liens footer -->
            <div class="text-center mt-8 space-y-4">
                <p class="text-sm text-gray-600">
                    Déjà un compte ? 
                    <a href="{{ route('login') }}" class="text-green-600 hover:text-green-800 font-semibold underline">
                        Se connecter
                    </a>
                </p>
                
                <p class="text-xs text-gray-500">
                    Problème d'inscription ? 
                    <button onclick="showClassicRegister()" class="text-green-600 hover:text-green-800 underline">
                        Utiliser le formulaire classique
                    </button>
                </p>
            </div>
        </div>

        <!-- Sécurité info -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-500 flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                Vos données sont protégées et sécurisées
            </p>
        </div>
    </div>
</div>

<!-- Modal Conditions d'utilisation -->
<div id="terms-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[80vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Conditions d'utilisation
                </h3>
                <button onclick="hideTermsModal()" class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <h4 class="font-semibold text-gray-900 mb-2">1. Acceptation des conditions</h4>
                <p class="text-gray-600 text-sm">En utilisant VintApp, vous acceptez d'être lié par ces conditions d'utilisation.</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 mb-2">2. Utilisation du service</h4>
                <p class="text-gray-600 text-sm">Vous vous engagez à utiliser le service de manière légale et éthique.</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 mb-2">3. Responsabilités</h4>
                <p class="text-gray-600 text-sm">Vous êtes responsable du contenu que vous publiez et des transactions que vous effectuez.</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 mb-2">4. Confidentialité</h4>
                <p class="text-gray-600 text-sm">Nous nous engageons à protéger vos données personnelles conformément à notre politique de confidentialité.</p>
            </div>
        </div>
        <div class="p-6 border-t border-gray-200">
            <button onclick="hideTermsModal()" class="w-full bg-gray-900 text-white py-3 rounded-xl font-medium hover:bg-gray-800 transition-colors">
                Fermer
            </button>
        </div>
    </div>
</div>

<!-- Modal Politique de confidentialité -->
<div id="privacy-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[80vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Politique de confidentialité
                </h3>
                <button onclick="hidePrivacyModal()" class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <h4 class="font-semibold text-gray-900 mb-2">1. Collecte des données</h4>
                <p class="text-gray-600 text-sm">Nous collectons uniquement les données nécessaires au fonctionnement du service.</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 mb-2">2. Utilisation des données</h4>
                <p class="text-gray-600 text-sm">Vos données sont utilisées pour améliorer votre expérience et sécuriser le service.</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 mb-2">3. Protection des données</h4>
                <p class="text-gray-600 text-sm">Nous mettons en place des mesures de sécurité appropriées pour protéger vos données.</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 mb-2">4. Vos droits</h4>
                <p class="text-gray-600 text-sm">Vous avez le droit d'accéder, de modifier et de supprimer vos données personnelles.</p>
            </div>
        </div>
        <div class="p-6 border-t border-gray-200">
            <button onclick="hidePrivacyModal()" class="w-full bg-gray-900 text-white py-3 rounded-xl font-medium hover:bg-gray-800 transition-colors">
                Fermer
            </button>
        </div>
    </div>
</div>

<!-- Modal Formulaire Classique -->
<div id="classic-register-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[80vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Inscription classique</h3>
                <button onclick="hideClassicRegister()" class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom complet</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                    <textarea name="address" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" rows="3">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Code de parrainage (optionnel)</label>
                    <input type="text" name="referral_code" value="{{ old('referral_code') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 uppercase">
                    @error('referral_code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
                        <input type="password" name="password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirmer mot de passe</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
                
                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="terms" required class="w-4 h-4 text-green-600 border-gray-300 rounded">
                    <label class="text-sm text-gray-700">J'accepte les conditions d'utilisation</label>
                </div>
                
                <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg font-medium hover:bg-green-700 transition-colors">
                    Créer le compte
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// Fonctions globales pour Firebase et registration
window.registerWithFirebaseEmail = async function() {
    const name = document.getElementById('firebase-name').value.trim();
    const email = document.getElementById('firebase-email').value.trim();
    const password = document.getElementById('firebase-password').value;
    const confirmPassword = document.getElementById('firebase-password-confirm').value;
    const phone = document.getElementById('firebase-phone').value.trim();
    const referralCode = document.getElementById('firebase-referral-code').value.trim().toUpperCase();
    const termsAccepted = document.getElementById('firebase-terms').checked;
    const newsletter = document.getElementById('firebase-newsletter').checked;
    
    // Validation
    if (!name || !email || !password || !confirmPassword) {
        showToast('Veuillez remplir tous les champs obligatoires', 'error');
        return;
    }
    
    if (!validateEmail(email)) {
        showToast('Adresse email invalide', 'error');
        return;
    }
    
    if (password !== confirmPassword) {
        showToast('Les mots de passe ne correspondent pas', 'error');
        return;
    }
    
    if (!validatePasswordStrength(password)) {
        showToast('Mot de passe non sécurisé : min 12 caractères, 1 chiffre, 2 symboles, majuscules/minuscules', 'error');
        return;
    }
    
    if (!termsAccepted) {
        showToast('Vous devez accepter les conditions d\'utilisation', 'error');
        return;
    }
    
    showLoading(true);
    
    try {
        // Créer l'utilisateur avec Firebase
        const userCredential = await firebase.auth().createUserWithEmailAndPassword(email, password);
        const user = userCredential.user;
        
        // Mettre à jour le profil avec le nom
        await user.updateProfile({
            displayName: name
        });
        
        // Envoyer l'email de vérification
        await user.sendEmailVerification();
        
        // Obtenir l'ID token pour l'authentification Laravel
        const idToken = await user.getIdToken();
        
        // Envoyer les données au backend Laravel pour créer la session
        const response = await fetch('{{ route("auth.firebase.login") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
            showToast('Inscription réussie ! Vérifiez votre email pour activer votre compte.', 'success');
            
            // Redirection immédiate vers la vérification par code
            window.location.href = '{{ route("verification.code") }}';
        } else {
            throw new Error(data.message || 'Erreur lors de l\'inscription');
        }
        
    } catch (error) {
        showLoading(false);
        console.error('Erreur lors de l\'inscription:', error);
        
        // Gestion des erreurs Firebase
        let errorMessage = 'Erreur lors de l\'inscription';
        switch (error.code) {
            case 'auth/email-already-in-use':
                errorMessage = 'Cette adresse email est déjà utilisée';
                break;
            case 'auth/weak-password':
                errorMessage = 'Le mot de passe est trop faible';
                break;
            case 'auth/invalid-email':
                errorMessage = 'Adresse email invalide';
                break;
            case 'auth/operation-not-allowed':
                errorMessage = 'L\'inscription par email est désactivée';
                break;
            default:
                errorMessage = error.message;
        }
        
        showToast(errorMessage, 'error');
    }
};

window.signUpWithGoogle = async function() {
    showLoading(true);
    
    try {
        // Vérifier que Firebase est initialisé
        if (!firebase.apps.length) {
            throw new Error('Firebase n\'est pas initialisé');
        }
        
        const provider = new firebase.auth.GoogleAuthProvider();
        provider.addScope('email');
        provider.addScope('profile');
        
        // Forcer la sélection de compte
        provider.setCustomParameters({
            prompt: 'select_account'
        });
        
        const result = await firebase.auth().signInWithPopup(provider);
        const user = result.user;
        
        if (!user) {
            throw new Error('Aucune information utilisateur reçue');
        }
        
        console.log('Utilisateur Google connecté:', user);
        
        // Obtenir l'ID token pour l'authentification Laravel
        const idToken = await user.getIdToken();
        
        // Envoyer les données au backend Laravel pour créer la session
        const response = await fetch('{{ route("auth.firebase.login") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                idToken: idToken,
                name: user.displayName || 'Utilisateur Google',
                provider: 'google',
                newsletter: false
            })
        });
        
        let data;
        try {
            data = await response.json();
        } catch (parseError) {
            console.error('JSON Parse Error:', parseError);
            
            // Essayons de récupérer le texte pour voir ce qui ne va pas
            const responseClone = response.clone();
            try {
                const responseText = await responseClone.text();
                console.error('Response was:', responseText);
                throw new Error('Le serveur a retourné une réponse invalide: ' + responseText.substring(0, 100));
            } catch (textError) {
                throw new Error('Erreur de communication avec le serveur');
            }
        }
        
        if (response.ok) {
            showLoading(false);
            showToast('Inscription Google réussie !', 'success');
            
            // Redirection immédiate vers la vérification
            window.location.href = '{{ route("verification.code") }}';
        } else {
            throw new Error(data.message || 'Erreur lors de l\'inscription');
        }
        
    } catch (error) {
        showLoading(false);
        console.error('Erreur détaillée lors de l\'inscription Google:', error);
        console.error('Code d\'erreur:', error.code);
        console.error('Message d\'erreur:', error.message);
        
        let errorMessage = 'Erreur lors de l\'inscription Google';
        
        switch (error.code) {
            case 'auth/popup-closed-by-user':
                errorMessage = 'Connexion annulée par l\'utilisateur';
                break;
            case 'auth/popup-blocked':
                errorMessage = 'Popup bloqué par le navigateur. Autorisez les popups pour ce site.';
                break;
            case 'auth/cancelled-popup-request':
                errorMessage = 'Demande de popup annulée';
                break;
            case 'auth/account-exists-with-different-credential':
                errorMessage = 'Un compte existe déjà avec cette adresse email';
                break;
            case 'auth/invalid-api-key':
                errorMessage = 'Clé API Firebase invalide';
                break;
            case 'auth/operation-not-allowed':
                errorMessage = 'Connexion Google désactivée. Contactez l\'administrateur.';
                break;
            case 'auth/too-many-requests':
                errorMessage = 'Trop de tentatives. Réessayez plus tard.';
                break;
            default:
                errorMessage = `Erreur Google: ${error.message}`;
        }
        
        showToast(errorMessage, 'error');
    }
};

window.signUpWithFacebook = async function() {
    showLoading(true);
    
    try {
        const provider = new firebase.auth.FacebookAuthProvider();
        provider.addScope('email');
        
        const result = await firebase.auth().signInWithPopup(provider);
        const user = result.user;
        
        // Obtenir l'ID token pour l'authentification Laravel
        const idToken = await user.getIdToken();
        
        // Envoyer les données au backend Laravel pour créer la session
        const response = await fetch('{{ route("auth.firebase.login") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                idToken: idToken,
                name: user.displayName || 'Utilisateur Facebook',
                provider: 'facebook',
                newsletter: false
            })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            showLoading(false);
            showToast('Inscription Facebook réussie !', 'success');
            
            // Redirection immédiate vers la vérification
            window.location.href = '{{ route("verification.code") }}';
        } else {
            throw new Error(data.message || 'Erreur lors de l\'inscription');
        }
        
    } catch (error) {
        showLoading(false);
        console.error('Erreur lors de l\'inscription Facebook:', error);
        
        let errorMessage = 'Erreur lors de l\'inscription Facebook';
        if (error.code === 'auth/popup-closed-by-user') {
            errorMessage = 'Connexion annulée par l\'utilisateur';
        } else if (error.code === 'auth/account-exists-with-different-credential') {
            errorMessage = 'Un compte existe déjà avec cette adresse email';
        }
        
        showToast(errorMessage, 'error');
    }
};

// Gestion des modals
window.showTermsModal = function() {
    document.getElementById('terms-modal').classList.remove('hidden');
    document.getElementById('terms-modal').classList.add('flex');
};

window.hideTermsModal = function() {
    document.getElementById('terms-modal').classList.add('hidden');
    document.getElementById('terms-modal').classList.remove('flex');
};

window.showPrivacyModal = function() {
    document.getElementById('privacy-modal').classList.remove('hidden');
    document.getElementById('privacy-modal').classList.add('flex');
};

window.hidePrivacyModal = function() {
    document.getElementById('privacy-modal').classList.add('hidden');
    document.getElementById('privacy-modal').classList.remove('flex');
};

window.showClassicRegister = function() {
    document.getElementById('classic-register-modal').classList.remove('hidden');
    document.getElementById('classic-register-modal').classList.add('flex');
};

window.hideClassicRegister = function() {
    document.getElementById('classic-register-modal').classList.add('hidden');
    document.getElementById('classic-register-modal').classList.remove('flex');
};

// Gestion du loading
function showLoading(show) {
    const overlay = document.getElementById('loading-overlay');
    if (show) {
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
    } else {
        overlay.classList.add('hidden');
        overlay.classList.remove('flex');
    }
}

// Système de toast
function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };
    
    const icons = {
        success: '✅',
        error: '❌',
        warning: '⚠️',
        info: 'ℹ️'
    };
    
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `${colors[type]} text-white px-6 py-3 rounded-xl shadow-lg flex items-center space-x-2 transform translate-x-full transition-transform duration-300`;
    toast.innerHTML = `
        <span>${icons[type]}</span>
        <span class="font-medium">${message}</span>
        <button onclick="document.getElementById('${toastId}').remove()" class="ml-4 hover:opacity-75">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    
    container.appendChild(toast);
    
    // Animation d'entrée
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Auto-remove après 5 secondes
    setTimeout(() => {
        if (document.getElementById(toastId)) {
            toast.classList.add('translate-x-full');
            setTimeout(() => document.getElementById(toastId).remove(), 300);
        }
    }, 5000);
}

// Validation email
function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// Validation du mot de passe sécurisé
function validatePasswordStrength(password) {
    const hasMinLength = password.length >= 12;
    const hasNumber = /[0-9]/.test(password);
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    
    // Compter les symboles
    const symbols = password.match(/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]/g);
    const hasTwoSymbols = symbols && symbols.length >= 2;
    
    return hasMinLength && hasNumber && hasUpperCase && hasLowerCase && hasTwoSymbols;
}

// Force du mot de passe avec critères visuels
function checkPasswordStrength(password) {
    const strengthBar = document.getElementById('password-strength-bar');
    const lengthCheck = document.getElementById('length-check');
    const numberCheck = document.getElementById('number-check');
    const symbolCheck = document.getElementById('symbol-check');
    const caseCheck = document.getElementById('case-check');
    
    if (!password) {
        strengthBar.style.width = '0%';
        strengthBar.className = 'h-full transition-all duration-300 rounded-full';
        // Réinitialiser tous les indicateurs
        [lengthCheck, numberCheck, symbolCheck, caseCheck].forEach(check => {
            check.className = 'w-3 h-3 rounded-full bg-gray-300 mr-2 transition-all duration-300';
        });
        return;
    }
    
    let score = 0;
    
    // Vérifier la longueur (minimum 12 caractères)
    const hasMinLength = password.length >= 12;
    if (hasMinLength) {
        score += 1;
        lengthCheck.className = 'w-3 h-3 rounded-full bg-green-500 mr-2 transition-all duration-300';
    } else {
        lengthCheck.className = 'w-3 h-3 rounded-full bg-red-500 mr-2 transition-all duration-300';
    }
    
    // Vérifier les chiffres
    const hasNumber = /[0-9]/.test(password);
    if (hasNumber) {
        score += 1;
        numberCheck.className = 'w-3 h-3 rounded-full bg-green-500 mr-2 transition-all duration-300';
    } else {
        numberCheck.className = 'w-3 h-3 rounded-full bg-red-500 mr-2 transition-all duration-300';
    }
    
    // Vérifier les symboles (minimum 2)
    const symbols = password.match(/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]/g);
    const hasTwoSymbols = symbols && symbols.length >= 2;
    if (hasTwoSymbols) {
        score += 1;
        symbolCheck.className = 'w-3 h-3 rounded-full bg-green-500 mr-2 transition-all duration-300';
    } else {
        symbolCheck.className = 'w-3 h-3 rounded-full bg-red-500 mr-2 transition-all duration-300';
    }
    
    // Vérifier majuscules et minuscules
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    if (hasUpperCase && hasLowerCase) {
        score += 1;
        caseCheck.className = 'w-3 h-3 rounded-full bg-green-500 mr-2 transition-all duration-300';
    } else {
        caseCheck.className = 'w-3 h-3 rounded-full bg-red-500 mr-2 transition-all duration-300';
    }
    
    // Mettre à jour la barre de force
    const percentage = (score / 4) * 100;
    strengthBar.style.width = percentage + '%';
    
    if (score <= 1) {
        strengthBar.className = 'h-full transition-all duration-300 rounded-full bg-red-500';
    } else if (score <= 2) {
        strengthBar.className = 'h-full transition-all duration-300 rounded-full bg-orange-500';
    } else if (score <= 3) {
        strengthBar.className = 'h-full transition-all duration-300 rounded-full bg-yellow-500';
    } else {
        strengthBar.className = 'h-full transition-all duration-300 rounded-full bg-green-500';
    }
}

// Gestion des événements DOM
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('firebase-email');
    const passwordInput = document.getElementById('firebase-password');
    const confirmPasswordInput = document.getElementById('firebase-password-confirm');
    const referralCodeInput = document.getElementById('firebase-referral-code');
    
    // Validation email en temps réel
    emailInput.addEventListener('blur', function() {
        const email = this.value.trim();
        if (email && !validateEmail(email)) {
            this.classList.add('border-red-400', 'focus:border-red-500', 'focus:ring-red-100');
            this.classList.remove('border-gray-200', 'focus:border-green-500', 'focus:ring-green-100');
        } else {
            this.classList.remove('border-red-400', 'focus:border-red-500', 'focus:ring-red-100');
            this.classList.add('border-gray-200', 'focus:border-green-500', 'focus:ring-green-100');
        }
    });
    
    // Force du mot de passe
    passwordInput.addEventListener('input', function() {
        checkPasswordStrength(this.value);
    });
    
    // Confirmation mot de passe
    confirmPasswordInput.addEventListener('input', function() {
        const password = passwordInput.value;
        const confirm = this.value;
        
        if (confirm && password !== confirm) {
            this.classList.add('border-red-400', 'focus:border-red-500', 'focus:ring-red-100');
            this.classList.remove('border-gray-200', 'focus:border-green-500', 'focus:ring-green-100');
        } else {
            this.classList.remove('border-red-400', 'focus:border-red-500', 'focus:ring-red-100');
            this.classList.add('border-gray-200', 'focus:border-green-500', 'focus:ring-green-100');
        }
    });
    
    // Code de parrainage en majuscules
    referralCodeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    // Fermer modals avec ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideTermsModal();
            hidePrivacyModal();
            hideClassicRegister();
        }
    });
    
    // Fermer modals en cliquant à l'extérieur
    ['terms-modal', 'privacy-modal', 'classic-register-modal'].forEach(modalId => {
        const modal = document.getElementById(modalId);
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                this.classList.remove('flex');
            }
        });
    });
    
    // Animation CSS pour fade-in-up
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
    `;
    document.head.appendChild(style);
});

// Configuration Firebase - Valeurs réelles du fichier .env
const firebaseConfig = {
    apiKey: "AIzaSyBe0WQbkZ0A3Cz9vKyQWsE-edxLfWrV1_E",
    authDomain: "vintapp-e6fa7.firebaseapp.com",
    projectId: "vintapp-e6fa7",
    storageBucket: "vintapp-e6fa7.appspot.com",
    messagingSenderId: "880178183981",
    appId: "1:880178183981:web:395604645bd7d758a35da4"
};

// Initialiser Firebase avec vérification d'erreur
try {
    if (!firebase.apps.length) {
        firebase.initializeApp(firebaseConfig);
        console.log('Firebase initialisé avec succès');
    } else {
        console.log('Firebase déjà initialisé');
    }
    
    // Test de connexion
    firebase.auth().onAuthStateChanged((user) => {
        if (user) {
            console.log('Utilisateur connecté:', user.email);
        } else {
            console.log('Aucun utilisateur connecté');
        }
    });
    
} catch (error) {
    console.error('Erreur d\'initialisation Firebase:', error);
    showToast('Erreur de configuration Firebase. Contactez l\'administrateur.', 'error');
}
</script>

@endsection
