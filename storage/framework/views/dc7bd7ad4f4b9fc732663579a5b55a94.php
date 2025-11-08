

<?php $__env->startSection('content'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar Navigation -->
        <div class="lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h6 class="text-lg font-semibold text-blue-600 mb-4 flex items-center">
                    <i class="fas fa-users mr-2"></i> Affiliation
                </h6>
                <nav class="space-y-2">
                    <a class="flex items-center px-3 py-2 rounded-lg bg-blue-600 text-white transition-colors" href="#dashboard" data-section="dashboard">
                        <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                    </a>
                    <a class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors" href="#points" data-section="points">
                        <i class="fas fa-coins mr-3"></i> Mes Points
                    </a>
                    <a class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors" href="#referrals" data-section="referrals">
                        <i class="fas fa-user-friends mr-3"></i> Parrainages
                    </a>
                    <a class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors" href="#codes" data-section="codes">
                        <i class="fas fa-qr-code mr-3"></i> Mes Codes
                    </a>
                    <a class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors" href="#redemptions" data-section="redemptions">
                        <i class="fas fa-exchange-alt mr-3"></i> Rachats
                    </a>
                    <a class="flex items-center px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors" href="#leaderboard" data-section="leaderboard">
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
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center">
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
                        <button class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm font-medium" onclick="openModal('createCodeModal')">
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
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h5 class="text-lg font-semibold text-gray-900 flex items-center">
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
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h5 class="text-lg font-semibold text-gray-900 flex items-center">
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
                <h2 class="text-2xl font-bold text-gray-900 flex items-center mb-6">
                    <i class="fas fa-coins text-yellow-500 mr-3"></i> Gestion des Points
                </h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-sm border border-green-200">
                        <div class="px-6 py-4 bg-green-600 text-white rounded-t-lg">
                            <h5 class="text-lg font-semibold flex items-center">
                                <i class="fas fa-money-bill-wave mr-2"></i> Convertir en Argent
                            </h5>
                        </div>
                        <div class="p-6">
                            <form id="convertCashForm" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Points à convertir:</label>
                                    <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" id="cashPoints" min="100" step="1" required>
                                    <p class="text-sm text-gray-500 mt-1">Minimum 100 points</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Devise:</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" id="cashCurrency" required>
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
                    <div class="bg-white rounded-lg shadow-sm border border-blue-200">
                        <div class="px-6 py-4 bg-blue-600 text-white rounded-t-lg">
                            <h5 class="text-lg font-semibold flex items-center">
                                <i class="fas fa-percentage mr-2"></i> Générer Code Réduction
                            </h5>
                        </div>
                        <div class="p-6">
                            <form id="generateDiscountForm" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Points à utiliser:</label>
                                    <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="discountPoints" min="100" max="5000" step="1" required>
                                    <p class="text-sm text-gray-500 mt-1">100-5000 points (100 pts = 1% de réduction)</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Durée de validité (jours):</label>
                                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="discountExpiry">
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
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h5 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-history mr-2"></i> Historique des Points
                        </h5>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="historyType">
                                <option value="all">Tous les types</option>
                                <option value="earn_referral">Parrainages</option>
                                <option value="earn_purchase">Achats</option>
                                <option value="earn_sale">Ventes</option>
                                <option value="redeem_cash">Conversions argent</option>
                                <option value="redeem_discount">Codes réduction</option>
                            </select>
                            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="historyPeriod">
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
                <h2 class="text-2xl font-bold text-gray-900 flex items-center mb-6">
                    <i class="fas fa-user-friends text-blue-500 mr-3"></i> Mes Parrainages
                </h2>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h5 class="text-lg font-semibold text-gray-900 flex items-center">
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
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-qr-code text-gray-600 mr-3"></i> Mes Codes de Parrainage
                    </h2>
                    <button class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium" onclick="openModal('createCodeModal')">
                        <i class="fas fa-plus mr-2"></i> Nouveau Code
                    </button>
                </div>
                
                <!-- Stats Cards for Codes -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-sm border border-blue-200 p-6 text-center">
                        <div class="text-blue-600 mb-3">
                            <i class="fas fa-qr-code text-3xl"></i>
                        </div>
                        <h4 class="text-2xl font-bold text-blue-600" id="totalCodes">0</h4>
                        <p class="text-gray-600">Codes Créés</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-green-200 p-6 text-center">
                        <div class="text-green-600 mb-3">
                            <i class="fas fa-check-circle text-3xl"></i>
                        </div>
                        <h4 class="text-2xl font-bold text-green-600" id="activeCodes">0</h4>
                        <p class="text-gray-600">Codes Actifs</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-blue-200 p-6 text-center">
                        <div class="text-blue-500 mb-3">
                            <i class="fas fa-users text-3xl"></i>
                        </div>
                        <h4 class="text-2xl font-bold text-blue-500" id="totalUses">0</h4>
                        <p class="text-gray-600">Utilisations</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-yellow-200 p-6 text-center">
                        <div class="text-yellow-500 mb-3">
                            <i class="fas fa-star text-3xl"></i>
                        </div>
                        <h4 class="text-2xl font-bold text-yellow-500" id="bestPerforming">-</h4>
                        <p class="text-gray-600">Meilleur Code</p>
                    </div>
                </div>

                <!-- Codes List -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <h5 class="text-lg font-semibold text-gray-900 flex items-center mb-4 sm:mb-0">
                            <i class="fas fa-list mr-2"></i> Mes Codes Existants
                        </h5>
                        <div class="flex gap-3">
                            <select class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" id="codeStatusFilter">
                                <option value="all">Tous les statuts</option>
                                <option value="active">Actifs</option>
                                <option value="inactive">Inactifs</option>
                                <option value="expired">Expirés</option>
                            </select>
                            <button class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors" onclick="refreshCodesList()">
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
                <h2 class="text-2xl font-bold text-gray-900 flex items-center mb-6">
                    <i class="fas fa-exchange-alt text-red-600 mr-3"></i> Mes Rachats
                </h2>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h5 class="text-lg font-semibold text-gray-900 flex items-center">
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
                <h2 class="text-2xl font-bold text-gray-900 flex items-center mb-6">
                    <i class="fas fa-trophy text-yellow-500 mr-3"></i> Classement
                </h2>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h5 class="text-lg font-semibold text-gray-900 flex items-center">
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
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h5 class="text-xl font-semibold text-gray-900 flex items-center">
                <i class="fas fa-plus mr-2"></i> Créer un Nouveau Code de Parrainage
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeModal('createCodeModal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <form id="createCodeForm">
                <!-- Auto-generated title display -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Titre du code (généré automatiquement):</label>
                    <div class="flex">
                        <input type="text" class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg bg-gray-50" id="codeTitle" readonly>
                        <button class="px-4 py-2 border border-l-0 border-gray-300 rounded-r-lg bg-gray-50 hover:bg-gray-100 transition-colors" type="button" onclick="generateCodeTitle()">
                            <i class="fas fa-refresh"></i> Regénérer
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Le titre sera automatiquement généré lors de la création</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-1">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description (optionnelle):</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="codeDescription" rows="3" maxlength="500" placeholder="Décrivez votre code de parrainage..."></textarea>
                            <p class="text-sm text-gray-500 mt-1">Maximum 500 caractères</p>
                        </div>
                    </div>
                    <div class="md:col-span-1">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type de code:</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="codeType" onchange="updateCodePreview()">
                                <option value="general">Général</option>
                                <option value="limited">Limité</option>
                                <option value="premium">Premium</option>
                                <option value="seasonal">Saisonnier</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Limite d'utilisation:</label>
                            <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="codeMaxUses" min="1" max="10000" placeholder="Illimité">
                            <p class="text-sm text-gray-500 mt-1">Laissez vide pour illimité</p>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Points bonus pour le filleul:</label>
                            <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="codeBonusPoints" min="0" max="1000" step="10" placeholder="0">
                            <p class="text-sm text-gray-500 mt-1">Points supplémentaires à l'inscription</p>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date d'expiration:</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="codeExpiry">
                                <option value="">Pas d'expiration</option>
                                <option value="7">7 jours</option>
                                <option value="30">30 jours</option>
                                <option value="60">60 jours</option>
                                <option value="90">90 jours</option>
                                <option value="365">1 an</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Statut:</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="codeStatus">
                                <option value="active">Actif</option>
                                <option value="inactive">Inactif</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Code Preview -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                    <div class="mb-3">
                        <h6 class="text-sm font-medium text-gray-700 flex items-center">
                            <i class="fas fa-eye mr-2"></i> Aperçu du Code
                        </h6>
                    </div>
                    <div id="codePreview" class="text-center">
                        <div class="bg-white border-2 border-dashed border-blue-500 rounded-lg p-4">
                            <h5 class="text-lg font-semibold text-gray-900 mb-2" id="previewTitle">Code Parrainage #001</h5>
                            <div class="text-xl font-mono font-bold text-blue-600 uppercase tracking-wider mb-2" id="previewCode">PARRAINS001</div>
                            <div>
                                <small class="text-gray-500" id="previewDetails">Général • Illimité • Permanent</small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50">
            <button type="button" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors" onclick="closeModal('createCodeModal')">
                <i class="fas fa-times mr-2"></i> Annuler
            </button>
            <button type="submit" form="createCodeForm" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i> Créer le Code
            </button>
        </div>
    </div>
</div>

<!-- Share Modal -->
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden" id="shareModal">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h5 class="text-xl font-semibold text-gray-900 flex items-center">
                <i class="fas fa-share-alt mr-2"></i> Partager mon Code de Parrainage
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeModal('shareModal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Mon code de parrainage:</label>
                <div class="flex">
                    <input type="text" class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg bg-gray-50" id="shareCode" readonly>
                    <button class="px-4 py-2 border border-l-0 border-gray-300 rounded-r-lg bg-gray-50 hover:bg-gray-100 transition-colors" type="button" onclick="copyToClipboard('#shareCode')">
                        <i class="fas fa-copy"></i> Copier
                    </button>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Lien de parrainage:</label>
                <div class="flex">
                    <input type="text" class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg bg-gray-50" id="shareUrl" readonly>
                    <button class="px-4 py-2 border border-l-0 border-gray-300 rounded-r-lg bg-gray-50 hover:bg-gray-100 transition-colors" type="button" onclick="copyToClipboard('#shareUrl')">
                        <i class="fas fa-copy"></i> Copier
                    </button>
                </div>
            </div>
            <div class="space-y-3">
                <button class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center" onclick="shareOnFacebook()">
                    <i class="fab fa-facebook mr-2"></i> Partager sur Facebook
                </button>
                <button class="w-full bg-blue-400 text-white py-2 px-4 rounded-lg hover:bg-blue-500 transition-colors flex items-center justify-center" onclick="shareOnTwitter()">
                    <i class="fab fa-twitter mr-2"></i> Partager sur Twitter
                </button>
                <button class="w-full bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition-colors flex items-center justify-center" onclick="shareOnWhatsApp()">
                    <i class="fab fa-whatsapp mr-2"></i> Partager sur WhatsApp
                </button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/affiliate-dashboard.js')); ?>"></script>
<script>
// Test de debug pour vérifier que les fonctions sont bien disponibles
console.log('Scripts chargés');
console.log('openModal function exists:', typeof openModal);
console.log('closeModal function exists:', typeof closeModal);

// Test direct du click
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM chargé');
    
    // Test direct des modales
    const testButton = document.querySelector('[onclick*="openModal"]');
    if (testButton) {
        console.log('Bouton trouvé:', testButton);
    }
    
    // Test des éléments de modal
    const createModal = document.getElementById('createCodeModal');
    const shareModal = document.getElementById('shareModal');
    console.log('Modal createCodeModal trouvée:', !!createModal);
    console.log('Modal shareModal trouvée:', !!shareModal);
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/affiliate/dashboard.blade.php ENDPATH**/ ?>