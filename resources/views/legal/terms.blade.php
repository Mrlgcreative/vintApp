@extends('app')

@section('title', 'Conditions d\'utilisation')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-green-600 to-emerald-600 dark:from-green-700 dark:to-emerald-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/10 backdrop-blur-lg rounded-2xl mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white mb-4">
                    Conditions d'utilisation
                </h1>
                <p class="text-lg sm:text-xl text-white/90 max-w-2xl mx-auto">
                    Dernière mise à jour : {{ date('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <!-- Introduction -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 sm:p-10 mb-8">
            <div class="flex items-start space-x-4 mb-6">
                <div class="flex-shrink-0 w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Introduction</h2>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        Bienvenue sur VintApp. En utilisant notre plateforme, vous acceptez d'être lié par les présentes conditions d'utilisation. 
                        Veuillez les lire attentivement avant d'utiliser nos services.
                    </p>
                </div>
            </div>
        </div>

        <!-- Section 1: Acceptation des conditions -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 sm:p-10 mb-8">
            <div class="border-l-4 border-green-500 pl-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">1. Acceptation des conditions</h2>
            </div>
            <div class="prose prose-lg dark:prose-invert max-w-none">
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    En accédant et en utilisant VintApp, vous acceptez d'être lié par ces conditions d'utilisation et toutes les lois et réglementations applicables.
                </p>
                <ul class="space-y-2 text-gray-600 dark:text-gray-300">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Vous devez avoir au moins 18 ans pour utiliser nos services
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Vous vous engagez à fournir des informations exactes et à jour
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-500 mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Vous êtes responsable de la confidentialité de votre compte
                    </li>
                </ul>
            </div>
        </div>

        <!-- Section 2: Utilisation de la plateforme -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 sm:p-10 mb-8">
            <div class="border-l-4 border-blue-500 pl-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">2. Utilisation de la plateforme</h2>
            </div>
            <div class="space-y-4">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Droits d'utilisation</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        VintApp vous accorde une licence limitée, non exclusive, non transférable et révocable pour utiliser notre plateforme à des fins personnelles et non commerciales.
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Comportements interdits</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-3">Vous vous engagez à ne pas :</p>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-300">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            Utiliser la plateforme à des fins illégales ou frauduleuses
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            Vendre des produits contrefaits ou de contrebande
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            Perturber le fonctionnement de la plateforme
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            Collecter des données personnelles d'autres utilisateurs
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Section 3: Transactions et paiements -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 sm:p-10 mb-8">
            <div class="border-l-4 border-purple-500 pl-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">3. Transactions et paiements</h2>
            </div>
            <div class="grid sm:grid-cols-2 gap-6">
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-6">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-800/50 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Paiements sécurisés</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        Tous les paiements sont traités de manière sécurisée via nos partenaires de paiement certifiés (CinetPay, Stripe).
                    </p>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-xl p-6">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-800/50 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Frais de transaction</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        Des frais peuvent s'appliquer selon le mode de paiement choisi. Consultez notre grille tarifaire pour plus de détails.
                    </p>
                </div>
            </div>
            <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 rounded-r-xl">
                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                    <strong>Important :</strong> Les vendeurs sont responsables de la livraison des produits vendus. VintApp agit uniquement comme intermédiaire.
                </p>
            </div>
        </div>

        <!-- Section 4: Propriété intellectuelle -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 sm:p-10 mb-8">
            <div class="border-l-4 border-indigo-500 pl-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">4. Propriété intellectuelle</h2>
            </div>
            <p class="text-gray-600 dark:text-gray-300 mb-6">
                Tout le contenu présent sur VintApp, y compris les textes, graphiques, logos, images, clips audio et vidéo, 
                est la propriété de VintApp ou de ses concédants de licence et est protégé par les lois sur le droit d'auteur.
            </p>
            <div class="grid sm:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl">
                    <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Marques déposées</p>
                </div>
                <div class="text-center p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl">
                    <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Images protégées</p>
                </div>
                <div class="text-center p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl">
                    <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Code source</p>
                </div>
            </div>
        </div>

        <!-- Section 5: Limitation de responsabilité -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 sm:p-10 mb-8">
            <div class="border-l-4 border-red-500 pl-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">5. Limitation de responsabilité</h2>
            </div>
            <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-6 mb-6">
                <p class="text-gray-700 dark:text-gray-200 mb-4">
                    VintApp ne peut être tenu responsable de :
                </p>
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <span class="flex-shrink-0 w-6 h-6 bg-red-100 dark:bg-red-800/50 rounded-full flex items-center justify-center mr-3 mt-0.5">
                            <span class="text-red-600 dark:text-red-400 text-xs font-bold">1</span>
                        </span>
                        <span class="text-gray-700 dark:text-gray-200">Tout dommage indirect, accessoire ou consécutif résultant de l'utilisation de nos services</span>
                    </li>
                    <li class="flex items-start">
                        <span class="flex-shrink-0 w-6 h-6 bg-red-100 dark:bg-red-800/50 rounded-full flex items-center justify-center mr-3 mt-0.5">
                            <span class="text-red-600 dark:text-red-400 text-xs font-bold">2</span>
                        </span>
                        <span class="text-gray-700 dark:text-gray-200">Tout litige entre acheteurs et vendeurs concernant les transactions</span>
                    </li>
                    <li class="flex items-start">
                        <span class="flex-shrink-0 w-6 h-6 bg-red-100 dark:bg-red-800/50 rounded-full flex items-center justify-center mr-3 mt-0.5">
                            <span class="text-red-600 dark:text-red-400 text-xs font-bold">3</span>
                        </span>
                        <span class="text-gray-700 dark:text-gray-200">La qualité, l'authenticité ou la conformité des produits vendus sur la plateforme</span>
                    </li>
                    <li class="flex items-start">
                        <span class="flex-shrink-0 w-6 h-6 bg-red-100 dark:bg-red-800/50 rounded-full flex items-center justify-center mr-3 mt-0.5">
                            <span class="text-red-600 dark:text-red-400 text-xs font-bold">4</span>
                        </span>
                        <span class="text-gray-700 dark:text-gray-200">Toute interruption ou dysfonctionnement du service</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Section 6: Résiliation -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 sm:p-10 mb-8">
            <div class="border-l-4 border-orange-500 pl-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">6. Résiliation</h2>
            </div>
            <div class="space-y-4">
                <p class="text-gray-600 dark:text-gray-300">
                    Nous nous réservons le droit de suspendre ou de résilier votre compte à tout moment, sans préavis, 
                    si nous estimons que vous avez violé ces conditions d'utilisation.
                </p>
                <div class="flex items-start space-x-3 p-4 bg-orange-50 dark:bg-orange-900/20 rounded-xl">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Motifs de résiliation</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Violation des conditions, activité frauduleuse, comportement abusif, ou non-paiement des frais dus.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 7: Modifications -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 sm:p-10 mb-8">
            <div class="border-l-4 border-teal-500 pl-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">7. Modifications des conditions</h2>
            </div>
            <p class="text-gray-600 dark:text-gray-300 mb-4">
                Nous nous réservons le droit de modifier ces conditions à tout moment. Les modifications entreront en vigueur 
                dès leur publication sur la plateforme.
            </p>
            <div class="bg-teal-50 dark:bg-teal-900/20 rounded-xl p-6">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-teal-100 dark:bg-teal-800/50 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-700 dark:text-gray-200">
                        Vous serez informé par email en cas de modifications importantes des conditions d'utilisation.
                    </p>
                </div>
            </div>
        </div>

        <!-- Section 8: Contact -->
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 dark:from-green-700 dark:to-emerald-700 rounded-2xl shadow-xl p-8 sm:p-10">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white/10 backdrop-blur-lg rounded-2xl mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white mb-3">Des questions ?</h2>
                <p class="text-white/90 mb-6 max-w-xl mx-auto">
                    Pour toute question concernant ces conditions d'utilisation, n'hésitez pas à nous contacter.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="mailto:support@vintapp.com" class="inline-flex items-center justify-center px-6 py-3 bg-white text-green-600 rounded-xl font-medium hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Envoyer un email
                    </a>
                    <a href="{{ route('help.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-white/10 backdrop-blur-lg text-white rounded-xl font-medium hover:bg-white/20 transition-colors border border-white/20">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Centre d'aide
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer Navigation -->
        <div class="mt-12 flex flex-col sm:flex-row items-center justify-between gap-4 p-6 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl">
            <a href="{{ route('privacy') }}" class="inline-flex items-center text-gray-600 dark:text-gray-300 hover:text-green-600 dark:hover:text-green-400 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                Politique de confidentialité
            </a>
            <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 transition-colors shadow-lg shadow-green-600/20">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Retour à l'accueil
            </a>
        </div>
    </div>
</div>
@endsection
