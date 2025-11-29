@extends('app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar Navigation -->
        <div class="lg:w-64 flex-shrink-0">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h6 class="text-lg font-semibold text-blue-600 mb-4 flex items-center">
                    <i class="fas fa-users mr-2"></i> Affiliation
                </h6>
                <nav class="space-y-2">
                    <a class="flex items-center px-3 py-2 rounded-lg bg-blue-600 text-white transition-colors" href="#dashboard" data-section="dashboard">
                        <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                    </a>
                    <a class="flex items-center px-3 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800 transition-colors" href="#points" data-section="points">
                        <i class="fas fa-coins mr-3"></i> Mes Points
                    </a>
                    <a class="flex items-center px-3 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800 transition-colors" href="#referrals" data-section="referrals">
                        <i class="fas fa-user-friends mr-3"></i> Parrainages
                    </a>
                    <a class="flex items-center px-3 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800 transition-colors" href="#codes" data-section="codes">
                        <i class="fas fa-qr-code mr-3"></i> Mes Codes
                    </a>
                    <a class="flex items-center px-3 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800 transition-colors" href="#redemptions" data-section="redemptions">
                        <i class="fas fa-exchange-alt mr-3"></i> Rachats
                    </a>
                    <a class="flex items-center px-3 py-2 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800 transition-colors" href="#leaderboard" data-section="leaderboard">
                        <i class="fas fa-trophy mr-3"></i> Classement
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 min-w-0">
            <!-- Dashboard Section -->
            <div id="section-dashboard" class="content-section">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-tachometer-alt text-blue-600 mr-3"></i> Dashboard Affiliation
                    </h2>
                    <div class="flex gap-3 mt-4 sm:mt-0">
                        <button class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium" id="shareReferralBtn">
                            <i class="fas fa-share-alt mr-2"></i> Partager mon code
                        </button>
                        <button class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium" id="refreshDataBtn">
                            <i class="fas fa-sync-alt mr-2"></i> Actualiser
                        </button>
                        <!-- Bouton de test pour les modales -->
                        <button class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm font-medium" onclick="window.openModal('createCodeModal')">
                            <i class="fas fa-plus mr-2"></i> Test Modal
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" id="statsCards">
                    <!-- Stats will be loaded here -->
                </div>

                <!-- Recent Activity -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h5 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                    <i class="fas fa-history mr-2"></i> Activité Récente
                                </h5>
                            </div>
                            <div class="p-6">
                                <div id="recentTransactions" class="space-y-4">
                                    <!-- Recent transactions will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lg:col-span-1">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h5 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                    <i class="fas fa-chart-line mr-2"></i> Progression
                                </h5>
                            </div>
                            <div class="p-6 text-center">
                                <div id="levelProgress">
                                    <!-- Level progress will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Points Section -->
            <div id="section-points" class="content-section hidden">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center mb-6">
                    <i class="fas fa-coins text-yellow-500 mr-3"></i> Gestion des Points
                </h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-green-200">
                        <div class="px-6 py-4 bg-green-600 text-white rounded-t-lg">
                            <h5 class="text-lg font-semibold flex items-center">
                                <i class="fas fa-money-bill-wave mr-2"></i> Convertir en Argent
                            </h5>
                        </div>
                        <div class="p-6">
                            <form id="convertCashForm" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Points à convertir:</label>
                                    <input type="number" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" id="cashPoints" min="100" step="1" required>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Minimum 100 points</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Devise:</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" id="cashCurrency" required>
                                        <option value="USD">USD - Dollar Américain</option>
                                        <option value="CDF">CDF - Franc Congolais</option>
                                    </select>
                                </div>
                                <div id="conversionPreview">
                                    <!-- Conversion preview will appear here -->
                                </div>
                                <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors font-medium">
                                    <i class="fas fa-exchange-alt mr-2"></i> Convertir
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-blue-200">
                        <div class="px-6 py-4 bg-blue-600 text-white rounded-t-lg">
                            <h5 class="text-lg font-semibold flex items-center">
                                <i class="fas fa-percentage mr-2"></i> Générer Code Réduction
                            </h5>
                        </div>
                        <div class="p-6">
                            <form id="generateDiscountForm" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Points à utiliser:</label>
                                    <input type="number" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="discountPoints" min="100" max="5000" step="1" required>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">100-5000 points (100 pts = 1% de réduction)</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Durée de validité (jours):</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="discountExpiry">
                                        <option value="7">7 jours</option>
                                        <option value="15">15 jours</option>
                                        <option value="30" selected>30 jours</option>
                                        <option value="60">60 jours</option>
                                    </select>
                                </div>
                                <div id="discountPreview">
                                    <!-- Discount preview will appear here -->
                                </div>
                                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                    <i class="fas fa-ticket-alt mr-2"></i> Générer Code
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Points History -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h5 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-history mr-2"></i> Historique des Points
                        </h5>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <select class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="historyType">
                                <option value="all">Tous les types</option>
                                <option value="earn_referral">Parrainages</option>
                                <option value="earn_purchase">Achats</option>
                                <option value="earn_sale">Ventes</option>
                                <option value="redeem_cash">Conversions argent</option>
                                <option value="redeem_discount">Codes réduction</option>
                            </select>
                            <select class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="historyPeriod">
                                <option value="all">Toute période</option>
                                <option value="today">Aujourd'hui</option>
                                <option value="this_week">Cette semaine</option>
                                <option value="this_month">Ce mois</option>
                            </select>
                        </div>
                        <div id="pointsHistory">
                            <!-- Points history will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Referrals Section -->
            <div id="section-referrals" class="content-section hidden">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center mb-6">
                    <i class="fas fa-user-friends text-blue-500 mr-3"></i> Mes Parrainages
                </h2>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h5 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-list mr-2"></i> Liste des Parrainages
                        </h5>
                    </div>
                    <div class="p-6">
                        <div id="referralsList">
                            <!-- Referrals list will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Codes Section -->
            <div id="section-codes" class="content-section hidden">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-qr-code text-gray-600 dark:text-gray-300 mr-3"></i> Mes Codes de Parrainage
                    </h2>
                    <button class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium" onclick="window.openModal('createCodeModal')">
                        <i class="fas fa-plus mr-2"></i> Nouveau Code
                    </button>
                </div>
                
                <!-- Stats Cards for Codes -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-blue-200 p-6 text-center">
                        <div class="text-blue-600 mb-3">
                            <i class="fas fa-qr-code text-3xl"></i>
                        </div>
                        <h4 class="text-2xl font-bold text-blue-600" id="totalCodes">0</h4>
                        <p class="text-gray-600 dark:text-gray-300">Codes Créés</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-green-200 p-6 text-center">
                        <div class="text-green-600 mb-3">
                            <i class="fas fa-check-circle text-3xl"></i>
                        </div>
                        <h4 class="text-2xl font-bold text-green-600" id="activeCodes">0</h4>
                        <p class="text-gray-600 dark:text-gray-300">Codes Actifs</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-blue-200 p-6 text-center">
                        <div class="text-blue-500 mb-3">
                            <i class="fas fa-users text-3xl"></i>
                        </div>
                        <h4 class="text-2xl font-bold text-blue-500" id="totalUses">0</h4>
                        <p class="text-gray-600 dark:text-gray-300">Utilisations</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-yellow-200 p-6 text-center">
                        <div class="text-yellow-500 mb-3">
                            <i class="fas fa-star text-3xl"></i>
                        </div>
                        <h4 class="text-2xl font-bold text-yellow-500" id="bestPerforming">-</h4>
                        <p class="text-gray-600 dark:text-gray-300">Meilleur Code</p>
                    </div>
                </div>

                <!-- Codes List -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <h5 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center mb-4 sm:mb-0">
                            <i class="fas fa-list mr-2"></i> Mes Codes Existants
                        </h5>
                        <div class="flex gap-3">
                            <select class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" id="codeStatusFilter">
                                <option value="all">Tous les statuts</option>
                                <option value="active">Actifs</option>
                                <option value="inactive">Inactifs</option>
                                <option value="expired">Expirés</option>
                            </select>
                            <button class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-900 transition-colors" onclick="window.refreshCodesList()">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div id="referralCodesList">
                            <!-- Referral codes list will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Redemptions Section -->
            <div id="section-redemptions" class="content-section hidden">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center mb-6">
                    <i class="fas fa-exchange-alt text-red-600 mr-3"></i> Mes Rachats
                </h2>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h5 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-list mr-2"></i> Historique des Rachats
                        </h5>
                    </div>
                    <div class="p-6">
                        <div id="redemptionsList">
                            <!-- Redemptions list will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leaderboard Section -->
            <div id="section-leaderboard" class="content-section hidden">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center mb-6">
                    <i class="fas fa-trophy text-yellow-500 mr-3"></i> Classement
                </h2>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h5 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-medal mr-2"></i> Top 50 des Parrains
                        </h5>
                    </div>
                    <div class="p-6">
                        <div id="leaderboardList">
                            <!-- Leaderboard will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Code Modal -->
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden" id="createCodeModal">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h5 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-plus mr-2"></i> Créer un Nouveau Code de Parrainage
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600 dark:text-gray-300 transition-colors" onclick="window.closeModal('createCodeModal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <form id="createCodeForm">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Formulaire -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                <i class="fas fa-heading text-blue-500 mr-1"></i> Titre du code
                            </label>
                            <div class="flex gap-2">
                                <input type="text" class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" 
                                       id="codeTitle" 
                                       placeholder="Ex: PARRAINS2024" 
                                       maxlength="50"
                                       oninput="window.updateCodePreview()">
                                <button type="button" 
                                        class="px-3 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all" 
                                        onclick="window.generateCodeTitle()"
                                        title="Générer automatiquement">
                                    <i class="fas fa-magic"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <i class="fas fa-info-circle"></i> Le titre sera utilisé pour identifier votre code
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                <i class="fas fa-align-left text-green-500 mr-1"></i> Description (optionnelle)
                            </label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" 
                                      id="codeDescription" 
                                      rows="3" 
                                      maxlength="500" 
                                      placeholder="Décrivez votre code de parrainage..."
                                      oninput="window.updateCodePreview()"></textarea>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <span id="descriptionCount">0</span>/500 caractères
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                <i class="fas fa-tag text-purple-500 mr-1"></i> Type de code
                            </label>
                            <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" 
                                    id="codeType"
                                    onchange="window.updateCodePreview()">
                                <option value="general">🌍 Général - Pour tous</option>
                                <option value="limited">⏱️ Limité - Nombre d'utilisations restreint</option>
                                <option value="premium">⭐ Premium - Bonus supplémentaires</option>
                                <option value="seasonal">🎄 Saisonnier - Événement spécial</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                <i class="fas fa-users text-orange-500 mr-1"></i> Limite d'utilisation
                            </label>
                            <input type="number" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" 
                                   id="codeMaxUses" 
                                   placeholder="Illimité si vide" 
                                   min="1"
                                   oninput="window.updateCodePreview()">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <i class="fas fa-info-circle"></i> Laissez vide pour un usage illimité
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                <i class="fas fa-gift text-yellow-500 mr-1"></i> Points bonus (optionnel)
                            </label>
                            <input type="number" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" 
                                   id="codeBonusPoints" 
                                   placeholder="0" 
                                   min="0"
                                   oninput="window.updateCodePreview()">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <i class="fas fa-coins"></i> Points bonus pour les utilisateurs de ce code
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                <i class="fas fa-calendar-alt text-red-500 mr-1"></i> Date d'expiration (optionnelle)
                            </label>
                            <input type="date" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" 
                                   id="codeExpiresAt"
                                   oninput="window.updateCodePreview()">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <i class="fas fa-info-circle"></i> Laissez vide si le code n'expire pas
                            </p>
                        </div>
                    </div>

                    <!-- Prévisualisation -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                            <i class="fas fa-eye text-indigo-500 mr-1"></i> Aperçu du Code
                        </label>
                        <div id="codePreview" class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-700 dark:to-gray-800 border-2 border-dashed border-blue-300 dark:border-blue-600 rounded-lg p-6 min-h-[400px]">
                            <div class="text-center text-gray-400 dark:text-gray-500">
                                <i class="fas fa-code text-4xl mb-3"></i>
                                <p>Remplissez le formulaire pour voir l'aperçu</p>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <h6 class="font-semibold text-blue-900 dark:text-blue-200 mb-2 flex items-center">
                                <i class="fas fa-lightbulb mr-2"></i> Conseils
                            </h6>
                            <ul class="text-sm text-blue-800 dark:text-blue-300 space-y-1">
                                <li><i class="fas fa-check-circle text-green-500 mr-1"></i> Utilisez un titre unique et mémorable</li>
                                <li><i class="fas fa-check-circle text-green-500 mr-1"></i> Ajoutez une description pour expliquer l'usage</li>
                                <li><i class="fas fa-check-circle text-green-500 mr-1"></i> Limitez les utilisations pour les codes spéciaux</li>
                                <li><i class="fas fa-check-circle text-green-500 mr-1"></i> Les codes Premium offrent plus de points</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                <i class="fas fa-info-circle"></i> Votre code sera généré automatiquement
            </div>
            <div class="flex gap-3">
                <button type="button" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors" 
                        onclick="window.closeModal('createCodeModal')">
                    <i class="fas fa-times mr-2"></i> Annuler
                </button>
                <button type="submit" 
                        form="createCodeForm" 
                        class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl">
                    <i class="fas fa-plus mr-2"></i> Créer le Code
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Share Modal -->
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden" id="shareModal">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h5 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-share-alt mr-2"></i> Partager mon Code
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600 dark:text-gray-300 transition-colors" onclick="window.closeModal('shareModal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    <i class="fas fa-code mr-1"></i> Code de parrainage
                </label>
                <div class="flex">
                    <input type="text" 
                           class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-l-lg bg-gray-50 dark:bg-gray-900 dark:text-white" 
                           id="shareCode" 
                           readonly>
                    <button class="px-4 py-2 border border-l-0 border-gray-300 dark:border-gray-600 rounded-r-lg bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" 
                            type="button" 
                            onclick="window.copyToClipboard('#shareCode')">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    <i class="fas fa-link mr-1"></i> Lien de partage
                </label>
                <div class="flex">
                    <input type="text" 
                           class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-l-lg bg-gray-50 dark:bg-gray-900 dark:text-white text-sm" 
                           id="shareUrl" 
                           readonly>
                    <button class="px-4 py-2 border border-l-0 border-gray-300 dark:border-gray-600 rounded-r-lg bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" 
                            type="button" 
                            onclick="window.copyToClipboard('#shareUrl')">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
            
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-3">
                    <i class="fas fa-share-nodes mr-1"></i> Partager sur les réseaux sociaux
                </label>
                <div class="grid grid-cols-3 gap-3">
                    <button type="button" 
                            class="flex flex-col items-center justify-center p-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                            onclick="window.shareOnFacebook()">
                        <i class="fab fa-facebook-f text-xl mb-1"></i>
                        <span class="text-xs">Facebook</span>
                    </button>
                    <button type="button" 
                            class="flex flex-col items-center justify-center p-3 bg-sky-500 hover:bg-sky-600 text-white rounded-lg transition-colors"
                            onclick="window.shareOnTwitter()">
                        <i class="fab fa-twitter text-xl mb-1"></i>
                        <span class="text-xs">Twitter</span>
                    </button>
                    <button type="button" 
                            class="flex flex-col items-center justify-center p-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors"
                            onclick="window.shareOnWhatsApp()">
                        <i class="fab fa-whatsapp text-xl mb-1"></i>
                        <span class="text-xs">WhatsApp</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts pour le dashboard affilié -->
<script>
// Compteur de caractères pour la description
document.addEventListener('DOMContentLoaded', function() {
    const descriptionField = document.getElementById('codeDescription');
    const descriptionCount = document.getElementById('descriptionCount');
    
    if (descriptionField && descriptionCount) {
        descriptionField.addEventListener('input', function() {
            descriptionCount.textContent = this.value.length;
        });
    }
});
</script>
<script src="{{ asset('js/affiliate-dashboard.js') }}?v={{ now()->timestamp }}"></script>
@endsection