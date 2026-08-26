@extends('app')

@section('title', 'Politique de confidentialité')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-700 dark:to-indigo-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/10 backdrop-blur-lg rounded-2xl mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white mb-4">
                    Politique de confidentialité
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
                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Notre engagement</h2>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        Chez VintApp, nous prenons très au sérieux la protection de vos données personnelles. Cette politique de confidentialité 
                        explique comment nous collectons, utilisons et protégeons vos informations.
                    </p>
                </div>
            </div>
        </div>

        <!-- Section 1: Collecte des données -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 sm:p-10 mb-8">
            <div class="border-l-4 border-blue-500 pl-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">1. Données collectées</h2>
            </div>
            <p class="text-gray-600 dark:text-gray-300 mb-6">
                Nous collectons différents types de données pour vous fournir et améliorer nos services :
            </p>
            
            <div class="space-y-4">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-800/50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Informations personnelles</h3>
                            <ul class="space-y-2 text-gray-600 dark:text-gray-300 text-sm">
                                <li class="flex items-start">
                                    <span class="text-blue-500 mr-2">•</span>
                                    Nom, prénom, pseudonyme
                                </li>
                                <li class="flex items-start">
                                    <span class="text-blue-500 mr-2">•</span>
                                    Adresse email et numéro de téléphone
                                </li>
                                <li class="flex items-start">
                                    <span class="text-blue-500 mr-2">•</span>
                                    Adresse de livraison et de facturation
                                </li>
                                <li class="flex items-start">
                                    <span class="text-blue-500 mr-2">•</span>
                                    Date de naissance (si fournie)
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-10 h-10 bg-purple-100 dark:bg-purple-800/50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Données de transaction</h3>
                            <ul class="space-y-2 text-gray-600 dark:text-gray-300 text-sm">
                                <li class="flex items-start">
                                    <span class="text-purple-500 mr-2">•</span>
                                    Historique des achats et ventes
                                </li>
                                <li class="flex items-start">
                                    <span class="text-purple-500 mr-2">•</span>
                                    Informations de paiement (cryptées)
                                </li>
                                <li class="flex items-start">
                                    <span class="text-purple-500 mr-2">•</span>
                                    Historique des transactions de portefeuille
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-10 h-10 bg-green-100 dark:bg-green-800/50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Données d'utilisation</h3>
                            <ul class="space-y-2 text-gray-600 dark:text-gray-300 text-sm">
                                <li class="flex items-start">
                                    <span class="text-green-500 mr-2">•</span>
                                    Adresse IP et données de connexion
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-500 mr-2">•</span>
                                    Type de navigateur et système d'exploitation
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-500 mr-2">•</span>
                                    Pages visitées et durée de navigation
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-500 mr-2">•</span>
                                    Cookies et technologies similaires
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-amber-50 to-yellow-50 dark:from-amber-900/20 dark:to-yellow-900/20 rounded-xl p-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-10 h-10 bg-amber-100 dark:bg-amber-800/50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Données de l'appareil mobile</h3>
                            <ul class="space-y-2 text-gray-600 dark:text-gray-300 text-sm">
                                <li class="flex items-start">
                                    <span class="text-amber-500 mr-2">•</span>
                                    Type d'appareil (mobile, tablette, ordinateur)
                                </li>
                                <li class="flex items-start">
                                    <span class="text-amber-500 mr-2">•</span>
                                    Système d'exploitation (Android, iOS) et version
                                </li>
                                <li class="flex items-start">
                                    <span class="text-amber-500 mr-2">•</span>
                                    Navigateur utilisé (Chrome, Safari, etc.)
                                </li>
                                <li class="flex items-start">
                                    <span class="text-amber-500 mr-2">•</span>
                                    Token de notification push (FCM / Expo)
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 1b: Notifications push & géolocalisation -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 sm:p-10 mb-8">
            <div class="border-l-4 border-cyan-500 pl-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">1b. Notifications push et géolocalisation</h2>
            </div>

            <div class="space-y-6">
                <div class="bg-cyan-50 dark:bg-cyan-900/20 rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Notifications push</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                        VintApp utilise les services de notification push de <strong>Firebase Cloud Messaging (FCM)</strong> et <strong>Expo Push</strong> pour vous envoyer des alertes concernant :
                    </p>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-300 text-sm">
                        <li class="flex items-start">
                            <span class="text-cyan-500 mr-2">•</span>
                            Nouveaux messages entre utilisateurs
                        </li>
                        <li class="flex items-start">
                            <span class="text-cyan-500 mr-2">•</span>
                            Statut de vos commandes (expédition, livraison)
                        </li>
                        <li class="flex items-start">
                            <span class="text-cyan-500 mr-2">•</span>
                            Approbation ou rejet de vos articles
                        </li>
                        <li class="flex items-start">
                            <span class="text-cyan-500 mr-2">•</span>
                            Offres personnalisées et promotions
                        </li>
                    </ul>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-3">
                        Vous pouvez désactiver les notifications push à tout moment depuis les paramètres de votre appareil. Le token de notification est supprimé lors de la déconnexion.
                    </p>
                </div>

                <div class="bg-cyan-50 dark:bg-cyan-900/20 rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Géolocalisation</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                        VintApp collecte des données de localisation dans les cas suivants :
                    </p>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-300 text-sm">
                        <li class="flex items-start">
                            <span class="text-cyan-500 mr-2">•</span>
                            <strong>Géolocalisation par adresse IP</strong> : utilisée pour déterminer votre ville et pays de connexion (via ip-api.com). Cette donnée est collectée automatiquement lors de chaque session.
                        </li>
                        <li class="flex items-start">
                            <span class="text-cyan-500 mr-2">•</span>
                            <strong>Coordonnées GPS</strong> : si vous activez la géolocalisation sur votre appareil, les coordonnées latitude/longitude peuvent être utilisées pour la livraison locale entre utilisateurs proches.
                        </li>
                        <li class="flex items-start">
                            <span class="text-cyan-500 mr-2">•</span>
                            <strong>Adresses de livraison</strong> : les coordonnées GPS de vos adresses de livraison sont stockées pour faciliter la livraison locale.
                        </li>
                    </ul>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-3">
                        La géolocalisation GPS n'est jamais activée sans votre consentement explicite. Vous pouvez refuser l'accès à votre position dans les paramètres de votre appareil.
                    </p>
                </div>
            </div>
        </div>

        <!-- Section 1c: Interactions sociales -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 sm:p-10 mb-8">
            <div class="border-l-4 border-pink-500 pl-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">1c. Interactions sociales</h2>
            </div>

            <p class="text-gray-600 dark:text-gray-300 mb-6">
                VintApp est une plateforme de marketplace. Certaines de vos données sont visibles par d'autres utilisateurs :
            </p>

            <div class="space-y-4">
                <div class="bg-pink-50 dark:bg-pink-900/20 rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Données visibles par les autres utilisateurs</h3>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-300 text-sm">
                        <li class="flex items-start">
                            <span class="text-pink-500 mr-2">•</span>
                            Nom, pseudonyme et avatar
                        </li>
                        <li class="flex items-start">
                            <span class="text-pink-500 mr-2">•</span>
                            Articles publiés (photos, descriptions, prix)
                        </li>
                        <li class="flex items-start">
                            <span class="text-pink-500 mr-2">•</span>
                            Avis et évaluations reçus
                        </li>
                        <li class="flex items-start">
                            <span class="text-pink-500 mr-2">•</span>
                            Ville de publication
                        </li>
                    </ul>
                </div>

                <div class="bg-pink-50 dark:bg-pink-900/20 rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Données privées (non partagées)</h3>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-300 text-sm">
                        <li class="flex items-start">
                            <span class="text-pink-500 mr-2">•</span>
                            Messages privés entre utilisateurs
                        </li>
                        <li class="flex items-start">
                            <span class="text-pink-500 mr-2">•</span>
                            Articles mis en favoris
                        </li>
                        <li class="flex items-start">
                            <span class="text-pink-500 mr-2">•</span>
                            Historique des achats et ventes
                        </li>
                        <li class="flex items-start">
                            <span class="text-pink-500 mr-2">•</span>
                            Adresses de livraison
                        </li>
                        <li class="flex items-start">
                            <span class="text-pink-500 mr-2">•</span>
                            Solde du portefeuille
                        </li>
                    </ul>
                </div>

                <div class="bg-pink-50 dark:bg-pink-900/20 rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Messagerie privée</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Les messages échangés entre utilisateurs sont stockés de manière sécurisée et ne sont accessibles qu'aux participants de la conversation. Les messages sont conservés tant que votre compte est actif.
                    </p>
                </div>
            </div>
        </div>

        <!-- Section 2: Utilisation des données -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 sm:p-10 mb-8">
            <div class="border-l-4 border-indigo-500 pl-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">2. Utilisation de vos données</h2>
            </div>
            <p class="text-gray-600 dark:text-gray-300 mb-6">
                Nous utilisons vos données personnelles pour les finalités suivantes :
            </p>
            
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl p-5">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-800/50 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Fourniture des services</h3>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Traiter vos commandes, gérer votre compte et fournir un support client
                    </p>
                </div>

                <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl p-5">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-800/50 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Amélioration continue</h3>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Analyser l'utilisation pour améliorer nos services et votre expérience
                    </p>
                </div>

                <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl p-5">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-800/50 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Communication</h3>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Vous envoyer des notifications importantes et des offres personnalisées
                    </p>
                </div>

                <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl p-5">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-800/50 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Sécurité</h3>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Prévenir la fraude, détecter les abus et assurer la sécurité de la plateforme
                    </p>
                </div>
            </div>
        </div>

        <!-- Section 3: Partage des données -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 sm:p-10 mb-8">
            <div class="border-l-4 border-purple-500 pl-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">3. Partage de vos données</h2>
            </div>
            
            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-xl p-6 mb-6">
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-700 dark:text-gray-200">
                        <strong>Nous ne vendons jamais vos données personnelles.</strong> Nous pouvons partager vos informations uniquement dans les cas suivants :
                    </p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-start space-x-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                    <span class="flex-shrink-0 w-8 h-8 bg-purple-100 dark:bg-purple-800/50 rounded-full flex items-center justify-center text-purple-600 dark:text-purple-400 font-bold">1</span>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-1">Prestataires de services</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Avec des partenaires de confiance qui nous aident à fournir nos services (paiement, livraison, hébergement)
                        </p>
                    </div>
                </div>

                <div class="flex items-start space-x-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                    <span class="flex-shrink-0 w-8 h-8 bg-purple-100 dark:bg-purple-800/50 rounded-full flex items-center justify-center text-purple-600 dark:text-purple-400 font-bold">2</span>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-1">Obligations légales</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Lorsque la loi l'exige ou pour protéger nos droits et ceux de nos utilisateurs
                        </p>
                    </div>
                </div>

                <div class="flex items-start space-x-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                    <span class="flex-shrink-0 w-8 h-8 bg-purple-100 dark:bg-purple-800/50 rounded-full flex items-center justify-center text-purple-600 dark:text-purple-400 font-bold">3</span>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-1">Avec votre consentement</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Dans tout autre cas, uniquement avec votre autorisation explicite
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Protection des données -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 sm:p-10 mb-8">
            <div class="border-l-4 border-green-500 pl-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">4. Protection de vos données</h2>
            </div>
            
            <p class="text-gray-600 dark:text-gray-300 mb-6">
                Nous mettons en œuvre des mesures de sécurité techniques et organisationnelles pour protéger vos données :
            </p>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="text-center p-6 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl">
                    <div class="w-14 h-14 bg-green-100 dark:bg-green-800/50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Cryptage SSL/TLS</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Toutes les données transitent par des connexions sécurisées
                    </p>
                </div>

                <div class="text-center p-6 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl">
                    <div class="w-14 h-14 bg-green-100 dark:bg-green-800/50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Pare-feu avancés</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Protection contre les accès non autorisés
                    </p>
                </div>

                <div class="text-center p-6 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl">
                    <div class="w-14 h-14 bg-green-100 dark:bg-green-800/50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Mots de passe cryptés</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Hashage sécurisé avec bcrypt
                    </p>
                </div>

                <div class="text-center p-6 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl">
                    <div class="w-14 h-14 bg-green-100 dark:bg-green-800/50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Sauvegardes régulières</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Backups quotidiens automatisés
                    </p>
                </div>

                <div class="text-center p-6 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl">
                    <div class="w-14 h-14 bg-green-100 dark:bg-green-800/50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Surveillance 24/7</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Monitoring continu de la sécurité
                    </p>
                </div>

                <div class="text-center p-6 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl">
                    <div class="w-14 h-14 bg-green-100 dark:bg-green-800/50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Audits de sécurité</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Tests de pénétration réguliers
                    </p>
                </div>
            </div>
        </div>

        <!-- Section 5: Vos droits -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 sm:p-10 mb-8">
            <div class="border-l-4 border-orange-500 pl-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">5. Vos droits (RGPD)</h2>
            </div>
            
            <p class="text-gray-600 dark:text-gray-300 mb-6">
                Conformément au Règlement Général sur la Protection des Données (RGPD), vous disposez des droits suivants :
            </p>

            <div class="grid sm:grid-cols-2 gap-4">
                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-xl p-5 border border-orange-200 dark:border-orange-800/50">
                    <div class="flex items-center space-x-3 mb-3">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Droit d'accès</h3>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Accéder à vos données personnelles que nous détenons
                    </p>
                </div>

                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-xl p-5 border border-orange-200 dark:border-orange-800/50">
                    <div class="flex items-center space-x-3 mb-3">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Droit de rectification</h3>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Corriger ou mettre à jour vos informations inexactes
                    </p>
                </div>

                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-xl p-5 border border-orange-200 dark:border-orange-800/50">
                    <div class="flex items-center space-x-3 mb-3">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Droit à l'effacement</h3>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Demander la suppression de vos données personnelles
                    </p>
                </div>

                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-xl p-5 border border-orange-200 dark:border-orange-800/50">
                    <div class="flex items-center space-x-3 mb-3">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                        </svg>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Droit d'opposition</h3>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Vous opposer au traitement de vos données
                    </p>
                </div>

                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-xl p-5 border border-orange-200 dark:border-orange-800/50">
                    <div class="flex items-center space-x-3 mb-3">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Droit à la portabilité</h3>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Récupérer vos données dans un format structuré
                    </p>
                </div>

                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-xl p-5 border border-orange-200 dark:border-orange-800/50">
                    <div class="flex items-center space-x-3 mb-3">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Droit à la limitation</h3>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Limiter le traitement de vos données personnelles
                    </p>
                </div>
            </div>

            <div class="mt-6 p-5 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded-r-xl">
                <p class="text-sm text-blue-900 dark:text-blue-200">
                    <strong>Pour exercer vos droits :</strong> Envoyez-nous un email à 
                    <a href="mailto:privacy@vintapp.com" class="underline font-semibold hover:text-blue-700">privacy@vintapp.com</a> 
                    avec une copie de votre pièce d'identité. Nous répondrons dans un délai de 30 jours maximum.
                </p>
            </div>
        </div>

        <!-- Section 6: Cookies -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 sm:p-10 mb-8">
            <div class="border-l-4 border-teal-500 pl-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">6. Cookies et technologies similaires</h2>
            </div>
            
            <p class="text-gray-600 dark:text-gray-300 mb-6">
                Nous utilisons des cookies et technologies similaires pour améliorer votre expérience :
            </p>

            <div class="space-y-4">
                <div class="bg-teal-50 dark:bg-teal-900/20 rounded-xl p-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-teal-100 dark:bg-teal-800/50 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Cookies essentiels</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Nécessaires au fonctionnement de la plateforme (authentification, panier, préférences de langue)
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-teal-50 dark:bg-teal-900/20 rounded-xl p-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-teal-100 dark:bg-teal-800/50 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Cookies analytiques</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Pour comprendre comment vous utilisez notre plateforme et améliorer nos services
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-teal-50 dark:bg-teal-900/20 rounded-xl p-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-teal-100 dark:bg-teal-800/50 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Cookies marketing</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Pour vous proposer des publicités et offres personnalisées (avec votre consentement)
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                    Vous pouvez gérer vos préférences de cookies à tout moment dans les paramètres de votre navigateur.
                </p>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <p class="mb-2"><strong>Cookies utilisés par VintApp :</strong></p>
                    <ul class="space-y-1">
                        <li>• <code>laravel_session</code> — session utilisateur (essentiel, expire à la fermeture du navigateur)</li>
                        <li>• <code>csrf_token</code> — protection contre les attaques CSRF (essentiel, expire à la fermeture)</li>
                        <li>• <code>XSRF-TOKEN</code> — token de sécurité pour les requêtes AJAX (essentiel, 24h)</li>
                        <li>• <code>remember_web_*</code> — « Rester connecté » (optionnel, 5 ans)</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Section 7: Conservation des données -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 sm:p-10 mb-8">
            <div class="border-l-4 border-red-500 pl-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">7. Conservation de vos données</h2>
            </div>
            
            <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-6">
                <p class="text-gray-700 dark:text-gray-200 mb-4">
                    Nous conservons vos données personnelles uniquement le temps nécessaire pour :
                </p>
                <ul class="space-y-2 text-gray-600 dark:text-gray-300">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Fournir nos services et maintenir votre compte actif</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Respecter nos obligations légales et comptables (minimum 10 ans pour les factures)</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Résoudre d'éventuels litiges et faire respecter nos accords</span>
                    </li>
                </ul>

                <div class="mt-4 pt-4 border-t border-red-200 dark:border-red-800/50">
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Durées de conservation spécifiques :</h4>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-300 text-sm">
                        <li class="flex items-start">
                            <span class="text-red-500 mr-2">•</span>
                            <strong>Sessions mobile et web</strong> : 30 jours (nettoyage automatique des sessions inactives)
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-500 mr-2">•</span>
                            <strong>Tokens FCM</strong> : supprimés lors de la déconnexion
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-500 mr-2">•</span>
                            <strong>Historique de navigation</strong> : 30 jours
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-500 mr-2">•</span>
                            <strong>Données de favoris</strong> : tant que le compte est actif
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-500 mr-2">•</span>
                            <strong>Messages privés</strong> : tant que le compte est actif
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-500 mr-2">•</span>
                            <strong>Données de commande</strong> : 10 ans (obligation comptable)
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Section 8: Modifications -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 sm:p-10 mb-8">
            <div class="border-l-4 border-yellow-500 pl-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">8. Modifications de la politique</h2>
            </div>
            
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0 w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Nous pouvons modifier cette politique de confidentialité à tout moment. En cas de changements significatifs, 
                        nous vous en informerons par :
                    </p>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-300">
                        <li class="flex items-start">
                            <span class="text-yellow-500 mr-2">→</span>
                            Email à l'adresse associée à votre compte
                        </li>
                        <li class="flex items-start">
                            <span class="text-yellow-500 mr-2">→</span>
                            Notification sur la plateforme
                        </li>
                        <li class="flex items-start">
                            <span class="text-yellow-500 mr-2">→</span>
                            Mise à jour de la date en haut de cette page
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Section 8b: Conformité légale RDC -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 sm:p-10 mb-8">
            <div class="border-l-4 border-indigo-700 pl-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">8b. Conformité légale — République Démocratique du Congo</h2>
            </div>

            <div class="space-y-6">
                <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Cadre juridique applicable</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                        VintApp est une application opérant en République Démocratique du Congo. Nous respectons les textes légaux suivants en matière de protection des données personnelles :
                    </p>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-300 text-sm">
                        <li class="flex items-start">
                            <span class="text-indigo-600 mr-2">•</span>
                            <strong>Constitution de la RDC (2006, telle que révisée)</strong> — Article 19 : droit à la vie privée et au secret de la correspondance
                        </li>
                        <li class="flex items-start">
                            <span class="text-indigo-600 mr-2">•</span>
                            <strong>Loi n° 20/017 du 25 novembre 2020</strong> portant régime juridique des télécommunications et des services de télécommunication
                        </li>
                        <li class="flex items-start">
                            <span class="text-indigo-600 mr-2">•</span>
                            <strong>Ordonnance-loi n° 23/010 du 13 mars 2023</strong> portant protection des données à caractère personnel en RDC
                        </li>
                        <li class="flex items-start">
                            <span class="text-indigo-600 mr-2">•</span>
                            <strong>RGPD (UE)</strong> — applicable aux utilisateurs résidant dans l'Espace Économique Européen
                        </li>
                    </ul>
                </div>

                <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Vos droits en RDC</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                        Conformément à la législation congolaise, vous disposez des droits suivants sur vos données personnelles :
                    </p>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-300 text-sm">
                        <li class="flex items-start">
                            <span class="text-indigo-600 mr-2">•</span>
                            <strong>Droit d'accès</strong> : obtenir une copie de vos données personnelles
                        </li>
                        <li class="flex items-start">
                            <span class="text-indigo-600 mr-2">•</span>
                            <strong>Droit de rectification</strong> : corriger les données inexactes
                        </li>
                        <li class="flex items-start">
                            <span class="text-indigo-600 mr-2">•</span>
                            <strong>Droit de suppression</strong> : demander l'effacement de vos données
                        </li>
                        <li class="flex items-start">
                            <span class="text-indigo-600 mr-2">•</span>
                            <strong>Droit d'opposition</strong> : vous opposer au traitement de vos données
                        </li>
                        <li class="flex items-start">
                            <span class="text-indigo-600 mr-2">•</span>
                            <strong>Droit de porter plainte</strong> : auprès de l'autorité nationale de protection des données
                        </li>
                    </ul>
                </div>

                <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Transferts internationaux de données</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Vos données peuvent être stockées sur des serveurs situés en dehors de la RDC (hébergement cloud). 
                        Dans ce cas, nous nous assurons que des garanties appropriées sont en place pour protéger vos données, 
                        notamment des clauses contractuelles standards conformes au RGPD et à la législation congolaise.
                    </p>
                </div>

                <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Autorité de contrôle</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        En cas de litige relatif à la protection de vos données personnelles, vous pouvez adresser une réclamation à notre Délégué à la Protection des Données (DPO) à 
                        <a href="mailto:privacy@vintapp.com" class="underline font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800">privacy@vintapp.com</a>.
                        Vous disposez également du droit d'introduire une réclamation auprès de l'autorité compétente en RDC.
                    </p>
                </div>
            </div>
        </div>

        <!-- Section 9: Contact -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-700 dark:to-indigo-700 rounded-2xl shadow-xl p-8 sm:p-10">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white/10 backdrop-blur-lg rounded-2xl mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white mb-3">Délégué à la protection des données</h2>
                <p class="text-white/90 mb-6 max-w-xl mx-auto">
                    Pour toute question concernant vos données personnelles ou cette politique de confidentialité, contactez notre DPO.
                </p>
                <div class="space-y-3 mb-6">
                    <div class="flex items-center justify-center text-white/90">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <a href="mailto:privacy@vintapp.com" class="hover:text-white font-medium">privacy@vintapp.com</a>
                    </div>
                    <div class="flex items-center justify-center text-white/90">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>VintApp, 123 Rue de la Mode, 75001 Paris, France</span>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="mailto:privacy@vintapp.com" class="inline-flex items-center justify-center px-6 py-3 bg-white text-blue-600 rounded-xl font-medium hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Contacter le DPO
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
            <a href="{{ route('terms') }}" class="inline-flex items-center text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Conditions d'utilisation
            </a>
            <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/20">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Retour à l'accueil
            </a>
        </div>
    </div>
</div>
@endsection
