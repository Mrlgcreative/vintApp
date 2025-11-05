

<?php $__env->startSection('title', 'Gestion des Affiliations'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
        <div class="mb-4 lg:mb-0">
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <svg class="w-8 h-8 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Gestion des Affiliations
            </h1>
            <p class="text-gray-600 mt-1">Gérez les parrains et leurs performances</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3">
            <button onclick="openRewardModal()" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                </svg>
                Nouvelle Récompense
            </button>
            <button onclick="refreshData()" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Actualiser
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Referrers -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Parrains</p>
                    <p class="text-3xl font-bold" id="totalReferrers">-</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-lg p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Referrals -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Parrainages Actifs</p>
                    <p class="text-3xl font-bold" id="activeReferrals">-</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-lg p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zM4 18v-4h3v4h2v-7.5c0-1.1-.9-2-2-2s-2 .9-2 2V16H2v2h2zm14.5-2.5c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5.67 1.5 1.5 1.5 1.5-.67 1.5-1.5z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Points -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Points Distribués</p>
                    <p class="text-3xl font-bold" id="totalPoints">-</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-lg p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Rewards -->
        <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Récompenses Données</p>
                    <p class="text-3xl font-bold" id="totalRewards">-</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-lg p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
            </svg>
            Filtres et Recherche
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher un parrain</label>
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Nom ou email..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Niveau minimum</label>
                <select id="levelFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Tous niveaux</option>
                    <option value="1">Niveau 1+</option>
                    <option value="2">Niveau 2+</option>
                    <option value="3">Niveau 3+</option>
                    <option value="4">Niveau 4+</option>
                    <option value="5">Niveau 5+</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Parrainages min</label>
                <select id="referralsFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Tous</option>
                    <option value="5">5+</option>
                    <option value="10">10+</option>
                    <option value="25">25+</option>
                    <option value="50">50+</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                <select id="periodFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Toutes</option>
                    <option value="this_month">Ce mois</option>
                    <option value="last_month">Mois dernier</option>
                    <option value="this_year">Cette année</option>
                </select>
            </div>
            
            <div class="flex items-end gap-2">
                <button onclick="applyFilters()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    Filtrer
                </button>
                <button onclick="resetFilters()" class="px-3 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Top Performers -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    Top 10 des Parrains
                </h3>
            </div>
            <div class="p-6">
                <div id="topPerformersList" class="space-y-4">
                    <!-- Loading -->
                    <div class="flex justify-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Level Distribution Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Répartition par Niveau
                </h3>
            </div>
            <div class="p-6">
                <canvas id="levelChart" class="w-full h-64"></canvas>
            </div>
        </div>
    </div>

    <!-- Referrers Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 sm:mb-0">Tous les Parrains</h3>
            <button onclick="bulkReward()" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                </svg>
                Récompenser Sélectionnés
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-8">
                            <input type="checkbox" id="selectAll" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parrain</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Niveau</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Parrainages</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Points</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Dernière Activité</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Récompenses</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="referrersTableBody" class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="flex justify-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            <nav class="flex justify-center">
                <ul id="pagination" class="flex space-x-2">
                    <!-- Pagination will be generated here -->
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Reward Modal -->
<div id="rewardModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeRewardModal()"></div>
        
        <!-- Modal Content -->
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-xl shadow-xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                        </svg>
                        Attribuer une Récompense
                    </h3>
                    <button onclick="closeRewardModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Body -->
            <div class="px-6 py-6">
                <form id="rewardForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sélectionner le parrain</label>
                            <select id="selectedReferrer" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Chargement...</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type de récompense</label>
                            <select id="rewardType" required onchange="toggleRewardSections()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Choisir le type...</option>
                                <option value="points">Points Bonus</option>
                                <option value="cash">Récompense en Argent</option>
                                <option value="badge">Badge Spécial</option>
                                <option value="level_boost">Boost de Niveau</option>
                            </select>
                        </div>
                    </div>

                    <!-- Points Section -->
                    <div id="pointsSection" class="reward-section hidden bg-blue-50 rounded-lg p-4">
                        <h4 class="font-medium text-blue-900 mb-3">Configuration des Points</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de points</label>
                                <input type="number" id="bonusPoints" min="1" max="10000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Multiplicateur</label>
                                <select id="pointsMultiplier" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="1">x1 (Normal)</option>
                                    <option value="1.5">x1.5</option>
                                    <option value="2">x2 (Double)</option>
                                    <option value="3">x3 (Triple)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Cash Section -->
                    <div id="cashSection" class="reward-section hidden bg-green-50 rounded-lg p-4">
                        <h4 class="font-medium text-green-900 mb-3">Récompense Financière</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Montant</label>
                                <input type="number" id="cashAmount" min="1" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Devise</label>
                                <select id="cashCurrency" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <option value="USD">USD - Dollar Américain</option>
                                    <option value="CDF">CDF - Franc Congolais</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Badge Section -->
                    <div id="badgeSection" class="reward-section hidden bg-purple-50 rounded-lg p-4">
                        <h4 class="font-medium text-purple-900 mb-3">Badge Spécial</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Type de badge</label>
                                <select id="badgeName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                    <option value="top_referrer">Top Parrain</option>
                                    <option value="super_ambassador">Super Ambassadeur</option>
                                    <option value="loyalty_champion">Champion de Fidélité</option>
                                    <option value="growth_master">Maître de Croissance</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Durée de validité</label>
                                <select id="badgeDuration" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                    <option value="permanent">Permanent</option>
                                    <option value="30">30 jours</option>
                                    <option value="90">90 jours</option>
                                    <option value="365">1 an</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Level Boost Section -->
                    <div id="levelSection" class="reward-section hidden bg-yellow-50 rounded-lg p-4">
                        <h4 class="font-medium text-yellow-900 mb-3">Boost de Niveau</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Niveaux à ajouter</label>
                                <select id="levelBoost" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                    <option value="1">+1 Niveau</option>
                                    <option value="2">+2 Niveaux</option>
                                    <option value="3">+3 Niveaux</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Type de boost</label>
                                <select id="boostType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                    <option value="permanent">Permanent</option>
                                    <option value="temporary">Temporaire (30 jours)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Raison de la récompense</label>
                        <textarea id="rewardReason" rows="3" placeholder="Expliquez pourquoi vous attribuez cette récompense..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center">
                            <input type="checkbox" id="sendNotification" checked class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <label for="sendNotification" class="ml-2 text-sm text-gray-700">Envoyer une notification au parrain</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="makePublic" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <label for="makePublic" class="ml-2 text-sm text-gray-700">Rendre publique dans le classement</label>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <button onclick="closeRewardModal()" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    Annuler
                </button>
                <button onclick="submitReward()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                    </svg>
                    Attribuer la Récompense
                </button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration
    const API_BASE = '/admin/affiliate/api';
    let currentPage = 1;
    let levelChart = null;
    
    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Vérification du token CSRF
    if (!csrfToken) {
        console.error('Token CSRF manquant. Ajoutez <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>"> dans le head.');
        return;
    }
    
    // Initialize
    init();
    
    function init() {
        // Chargement immédiat des statistiques principales
        loadDashboardStats();
        loadReferrers();
        
        // Chargement différé des éléments non critiques
        setTimeout(() => {
            loadTopPerformers();
            initializeLevelChart();
        }, 100);
    }
    
    // Load Dashboard Statistics
    async function loadDashboardStats() {
        try {
            const response = await fetch(`${API_BASE}/stats`, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const result = await response.json();
                if (result.success && result.data) {
                    const data = result.data;
                    document.getElementById('totalReferrers').textContent = data.total_referrers || 0;
                    document.getElementById('activeReferrals').textContent = data.active_referrals || 0;
                    document.getElementById('totalPoints').textContent = data.total_points || 0;
                    document.getElementById('totalRewards').textContent = data.total_rewards || 0;
                } else {
                    console.error('Erreur API:', result.message);
                }
            } else {
                console.error('Erreur HTTP:', response.status, response.statusText);
                // Afficher des valeurs par défaut en cas d'erreur
                document.getElementById('totalReferrers').textContent = '0';
                document.getElementById('activeReferrals').textContent = '0';
                document.getElementById('totalPoints').textContent = '0';
                document.getElementById('totalRewards').textContent = '0';
            }
        } catch (error) {
            console.error('Error loading stats:', error);
            // Afficher des valeurs par défaut en cas d'erreur
            document.getElementById('totalReferrers').textContent = '0';
            document.getElementById('activeReferrals').textContent = '0';
            document.getElementById('totalPoints').textContent = '0';
            document.getElementById('totalRewards').textContent = '0';
        }
    }
    
    // Load Top Performers
    async function loadTopPerformers() {
        try {
            const response = await fetch(`${API_BASE}/top-performers`, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const result = await response.json();
                if (result.success && result.data) {
                    displayTopPerformers(result.data);
                } else {
                    console.error('Erreur API top performers:', result.message);
                    displayTopPerformers([]);
                }
            } else {
                console.error('Erreur HTTP top performers:', response.status);
                displayTopPerformers([]);
            }
        } catch (error) {
            console.error('Error loading top performers:', error);
            document.getElementById('topPerformersList').innerHTML = 
                '<div class="text-center text-red-600 py-8">Erreur de chargement des données</div>';
        }
    }
    
    function displayTopPerformers(performers) {
        const container = document.getElementById('topPerformersList');
        
        if (performers.length === 0) {
            container.innerHTML = '<div class="text-center text-gray-500 py-8">Aucun parrain trouvé</div>';
            return;
        }
        
        let html = '';
        performers.forEach((performer, index) => {
            const medals = ['🥇', '🥈', '🥉'];
            const medal = index < 3 ? medals[index] : '👤';
            const bgColor = index < 3 ? 'bg-gradient-to-r from-yellow-50 to-orange-50' : 'bg-gray-50';
            
            html += `
                <div class="flex items-center justify-between p-4 rounded-lg ${bgColor} hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center">
                        <div class="text-2xl mr-4">${medal}</div>
                        <div>
                            <div class="font-semibold text-gray-900">${performer.name}</div>
                            <div class="text-sm text-gray-600">${performer.email}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-lg text-indigo-600">${performer.referrals_count}</div>
                        <div class="text-sm text-gray-500">parrainages</div>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }
    
    // Load Referrers Table
    async function loadReferrers(page = 1) {
        try {
            const params = new URLSearchParams({
                page: page,
                search: document.getElementById('searchInput')?.value || '',
                level: document.getElementById('levelFilter')?.value || '',
                min_referrals: document.getElementById('referralsFilter')?.value || '',
                period: document.getElementById('periodFilter')?.value || ''
            });
            
            const response = await fetch(`${API_BASE}/referrers?${params}`, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const result = await response.json();
                if (result.success && result.data) {
                    const referrersData = result.data.data || result.data;
                    displayReferrers(referrersData);
                    updatePagination(result.data);
                } else {
                    console.error('Erreur API referrers:', result.message);
                    displayReferrers([]);
                }
            } else {
                console.error('Erreur HTTP referrers:', response.status);
                displayReferrers([]);
            }
        } catch (error) {
            console.error('Error loading referrers:', error);
            document.getElementById('referrersTableBody').innerHTML = 
                '<tr><td colspan="9" class="px-6 py-4 text-center text-red-600">Erreur de chargement</td></tr>';
        }
    }
    
    function displayReferrers(referrers) {
        const tbody = document.getElementById('referrersTableBody');
        
        if (referrers.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="px-6 py-8 text-center text-gray-500">Aucun parrain trouvé</td></tr>';
            return;
        }
        
        let html = '';
        referrers.forEach((referrer, index) => {
            const levelColors = {
                1: 'bg-blue-100 text-blue-800',
                2: 'bg-green-100 text-green-800', 
                3: 'bg-yellow-100 text-yellow-800',
                4: 'bg-purple-100 text-purple-800',
                5: 'bg-red-100 text-red-800'
            };
            
            const levelClass = levelColors[referrer.level] || levelColors[1];
            const lastActivity = referrer.last_activity_at ? 
                new Date(referrer.last_activity_at).toLocaleDateString() : 'Jamais';
            
            html += `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <input type="checkbox" class="referrer-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" value="${referrer.id}">
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                        ${((currentPage - 1) * 10) + index + 1}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold mr-3">
                                ${referrer.name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">${referrer.name}</div>
                                <div class="text-sm text-gray-500">${referrer.email}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${levelClass}">
                            Niveau ${referrer.level || 1}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center font-bold text-lg text-indigo-600">
                        ${referrer.referrals_count || 0}
                    </td>
                    <td class="px-6 py-4 text-center font-semibold text-purple-600">
                        ${referrer.total_points || 0}
                    </td>
                    <td class="px-6 py-4 text-center text-sm text-gray-500">
                        ${lastActivity}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            ${referrer.rewards_count || 0}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center space-x-2">
                            <button onclick="viewReferrer(${referrer.id})" class="text-indigo-600 hover:text-indigo-900 p-1 rounded-md hover:bg-indigo-50" title="Voir détails">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                            <button onclick="rewardReferrer(${referrer.id})" class="text-green-600 hover:text-green-900 p-1 rounded-md hover:bg-green-50" title="Récompenser">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
    }
    
    function updatePagination(data) {
        const pagination = document.getElementById('pagination');
        let html = '';
        
        // Previous button
        if (data.prev_page_url) {
            html += `
                <li>
                    <button onclick="loadReferrers(${data.current_page - 1})" class="px-3 py-2 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-700">
                        Précédent
                    </button>
                </li>
            `;
        }
        
        // Page numbers
        for (let i = 1; i <= data.last_page; i++) {
            const isActive = i === data.current_page;
            html += `
                <li>
                    <button onclick="loadReferrers(${i})" class="px-3 py-2 leading-tight ${isActive ? 'text-indigo-600 bg-indigo-50 border-indigo-300' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700'} border">
                        ${i}
                    </button>
                </li>
            `;
        }
        
        // Next button
        if (data.next_page_url) {
            html += `
                <li>
                    <button onclick="loadReferrers(${data.current_page + 1})" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-700">
                        Suivant
                    </button>
                </li>
            `;
        }
        
        pagination.innerHTML = html;
        currentPage = data.current_page;
    }
    
    // Initialize Chart
    function initializeLevelChart() {
        const ctx = document.getElementById('levelChart').getContext('2d');
        levelChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Niveau 1', 'Niveau 2', 'Niveau 3', 'Niveau 4', 'Niveau 5'],
                datasets: [{
                    data: [5, 3, 2, 1, 0],
                    backgroundColor: [
                        '#3B82F6',
                        '#10B981',
                        '#F59E0B', 
                        '#8B5CF6',
                        '#EF4444'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false, // Désactive les animations pour améliorer les performances
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    
    // Load Referrer Options for Modal
    async function loadReferrerOptions() {
        try {
            const response = await fetch(`${API_BASE}/referrer-options`, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const result = await response.json();
                const referrers = result.success ? result.data : result;
                const select = document.getElementById('selectedReferrer');
                
                select.innerHTML = '<option value="">Sélectionner un parrain...</option>';
                if (Array.isArray(referrers)) {
                    referrers.forEach(referrer => {
                        select.innerHTML += `<option value="${referrer.id}">${referrer.name} (${referrer.referrals_count || 0} parrainages)</option>`;
                    });
                }
            } else {
                document.getElementById('selectedReferrer').innerHTML = 
                    '<option value="">Erreur de chargement</option>';
            }
        } catch (error) {
            console.error('Error loading referrer options:', error);
            document.getElementById('selectedReferrer').innerHTML = 
                '<option value="">Erreur de chargement</option>';
        }
    }
    
    // Global Functions
    window.refreshData = function() {
        loadDashboardStats();
        loadTopPerformers();
        loadReferrers(currentPage);
    };
    
    window.applyFilters = function() {
        loadReferrers(1);
    };
    
    window.resetFilters = function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('levelFilter').value = '';
        document.getElementById('referralsFilter').value = '';
        document.getElementById('periodFilter').value = '';
        loadReferrers(1);
    };
    
    window.openRewardModal = function() {
        document.getElementById('rewardModal').classList.remove('hidden');
        loadReferrerOptions();
    };
    
    window.closeRewardModal = function() {
        document.getElementById('rewardModal').classList.add('hidden');
        document.getElementById('rewardForm').reset();
        document.querySelectorAll('.reward-section').forEach(section => {
            section.classList.add('hidden');
        });
    };
    
    window.toggleRewardSections = function() {
        // Hide all sections
        document.querySelectorAll('.reward-section').forEach(section => {
            section.classList.add('hidden');
        });
        
        // Show selected section
        const type = document.getElementById('rewardType').value;
        if (type) {
            const sectionId = type + 'Section';
            const section = document.getElementById(sectionId);
            if (section) {
                section.classList.remove('hidden');
            }
        }
    };
    
    window.rewardReferrer = function(referrerId) {
        openRewardModal();
        setTimeout(() => {
            document.getElementById('selectedReferrer').value = referrerId;
        }, 500);
    };
    
    window.viewReferrer = function(referrerId) {
        alert('Fonctionnalité à implémenter: Voir détails du parrain ' + referrerId);
    };
    
    window.bulkReward = function() {
        const selected = document.querySelectorAll('.referrer-checkbox:checked');
        if (selected.length === 0) {
            alert('Veuillez sélectionner au moins un parrain');
            return;
        }
        alert('Fonctionnalité à implémenter: Récompenser ' + selected.length + ' parrains');
    };
    
    window.submitReward = async function() {
        const formData = {
            referrer_id: document.getElementById('selectedReferrer').value,
            type: document.getElementById('rewardType').value,
            reason: document.getElementById('rewardReason').value,
            send_notification: document.getElementById('sendNotification').checked,
            make_public: document.getElementById('makePublic').checked
        };
        
        if (!formData.referrer_id || !formData.type) {
            alert('Veuillez remplir tous les champs obligatoires');
            return;
        }
        
        // Add type-specific data
        const type = formData.type;
        if (type === 'points') {
            formData.value = {
                amount: document.getElementById('bonusPoints').value,
                multiplier: document.getElementById('pointsMultiplier').value
            };
        } else if (type === 'cash') {
            formData.value = {
                amount: document.getElementById('cashAmount').value,
                currency: document.getElementById('cashCurrency').value
            };
        } else if (type === 'badge') {
            formData.value = {
                name: document.getElementById('badgeName').value,
                duration: document.getElementById('badgeDuration').value
            };
        } else if (type === 'level_boost') {
            formData.value = {
                levels: document.getElementById('levelBoost').value,
                boost_type: document.getElementById('boostType').value
            };
        }
        
        try {
            const response = await fetch('/admin/affiliate/rewards', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            
            if (response.ok) {
                const result = await response.json();
                if (result.success) {
                    alert('Récompense attribuée avec succès!');
                    closeRewardModal();
                    refreshData();
                } else {
                    alert('Erreur: ' + (result.message || 'Erreur inconnue'));
                }
            } else {
                const error = await response.json();
                alert('Erreur: ' + (error.message || 'Erreur inconnue'));
            }
        } catch (error) {
            console.error('Error submitting reward:', error);
            alert('Erreur lors de l\'attribution de la récompense');
        }
    };
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/admin/affiliate/index.blade.php ENDPATH**/ ?>