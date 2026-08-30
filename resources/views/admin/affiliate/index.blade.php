@extends('layouts.admin')

@section('title', 'Gestion des Affiliations')
@section('page-title', 'Affiliation')
@section('page-subtitle', 'Gérez les parrains et leurs performances')

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <button onclick="refreshData()"
            class="inline-flex items-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
        <i class="fas fa-rotate"></i>Actualiser
    </button>
    <button onclick="openRewardModal()"
            class="inline-flex items-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-gift"></i><span class="hidden sm:inline">Nouvelle Récompense</span><span class="sm:hidden">Récompense</span>
    </button>
</div>
@endsection

@section('content')
<!-- Statistiques -->
<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 xl:grid-cols-4">
    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Total Parrains</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white" id="totalReferrers">-</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300">
                <i class="fas fa-user-group text-[10px] text-sky-500"></i>
                Parrains
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-user-group text-xs text-sky-500"></i>
                Parrains ayant référencé
            </div>
            <div class="text-xs text-slate-400">Utilisateurs avec filleuls</div>
        </div>
    </div>

    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Parrainages Actifs</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white" id="activeReferrals">-</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400">
                <i class="fas fa-circle-check text-[10px]"></i>
                Actifs
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-user-check text-xs text-emerald-500"></i>
                Parrainages complétés
            </div>
            <div class="text-xs text-slate-400">Références réussies</div>
        </div>
    </div>

    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Points Distribués</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white" id="totalPoints">-</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-violet-200 bg-violet-50 px-2 py-0.5 text-xs font-medium text-violet-700 dark:border-violet-500/30 dark:bg-violet-500/10 dark:text-violet-400">
                <i class="fas fa-coins text-[10px]"></i>
                Points
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-coins text-xs text-violet-500"></i>
                Points earn_referral / earn_bonus
            </div>
            <div class="text-xs text-slate-400">Distribués aux parrains</div>
        </div>
    </div>

    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Récompenses Données</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white" id="totalRewards">-</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-400">
                <i class="fas fa-gift text-[10px]"></i>
                Récompenses
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-gift text-xs text-amber-500"></i>
                Récompenses attribuées
            </div>
            <div class="text-xs text-slate-400">Créées par l'administration</div>
        </div>
    </div>
</div>

<!-- Filtres et Recherche -->
<div class="mb-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold text-slate-900 sm:text-base dark:text-white">
        <i class="fas fa-filter text-primary-600"></i>
        Filtres et Recherche
    </h3>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-6">
        <div class="lg:col-span-2">
            <label for="searchInput" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Rechercher un parrain</label>
            <div class="relative">
                <i class="fas fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                <input type="text" id="searchInput" placeholder="Nom ou email..."
                       class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
            </div>
        </div>

        <div>
            <label for="levelFilter" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Niveau minimum</label>
            <select id="levelFilter" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                <option value="">Tous niveaux</option>
                <option value="1">Niveau 1+</option>
                <option value="2">Niveau 2+</option>
                <option value="3">Niveau 3+</option>
                <option value="4">Niveau 4+</option>
                <option value="5">Niveau 5+</option>
            </select>
        </div>

        <div>
            <label for="referralsFilter" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Parrainages min</label>
            <select id="referralsFilter" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                <option value="">Tous</option>
                <option value="5">5+</option>
                <option value="10">10+</option>
                <option value="25">25+</option>
                <option value="50">50+</option>
            </select>
        </div>

        <div>
            <label for="periodFilter" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Période</label>
            <select id="periodFilter" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                <option value="">Toutes</option>
                <option value="this_month">Ce mois</option>
                <option value="last_month">Mois dernier</option>
                <option value="this_year">Cette année</option>
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button onclick="applyFilters()"
                    class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                <i class="fas fa-filter"></i>Filtrer
            </button>
            <button onclick="resetFilters()" title="Réinitialiser"
                    class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-slate-600 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                <i class="fas fa-rotate-left"></i>
            </button>
        </div>
    </div>
</div>

<!-- Top Performers + Répartition -->
<div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm lg:col-span-2 dark:border-slate-700 dark:bg-slate-800">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-700">
            <h3 class="flex items-center gap-2 text-sm font-semibold text-slate-900 sm:text-base dark:text-white">
                <i class="fas fa-trophy text-amber-500"></i>
                Top 10 des Parrains
            </h3>
        </div>
        <div class="p-5">
            <div id="topPerformersList" class="space-y-4">
                <!-- Loading -->
                <div class="flex justify-center py-8">
                    <div class="h-8 w-8 animate-spin rounded-full border-b-2 border-primary-600"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-700">
            <h3 class="flex items-center gap-2 text-sm font-semibold text-slate-900 sm:text-base dark:text-white">
                <i class="fas fa-chart-pie text-primary-500"></i>
                Répartition par Niveau
            </h3>
        </div>
        <div class="p-5">
            <canvas id="levelChart" class="h-64 w-full"></canvas>
        </div>
    </div>
</div>

<!-- Tous les Parrains -->
<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-5 py-4 dark:border-slate-700">
        <h3 class="flex items-center gap-2 text-sm font-semibold text-slate-900 sm:text-base dark:text-white">
            <i class="fas fa-users text-primary-600"></i>
            Tous les Parrains
        </h3>
        <button onclick="bulkReward()"
                class="inline-flex items-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
            <i class="fas fa-gift"></i>Récompenser Sélectionnés
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-900">
                <tr>
                    <th class="w-8 px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <input type="checkbox" id="selectAll" class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500 dark:border-slate-600">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Parrain</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Niveau</th>
                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Parrainages</th>
                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Points</th>
                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Dernière Activité</th>
                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Récompenses</th>
                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                </tr>
            </thead>
            <tbody id="referrersTableBody" class="divide-y divide-slate-200 bg-white dark:bg-slate-800">
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center">
                        <div class="flex justify-center">
                            <div class="h-8 w-8 animate-spin rounded-full border-b-2 border-primary-600"></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center border-t border-slate-100 px-5 py-4 dark:border-slate-700">
        <nav>
            <ul id="pagination" class="flex space-x-2">
                <!-- Pagination will be generated here -->
            </ul>
        </nav>
    </div>
</div>
@endsection

<!-- Reward Modal -->
<div id="rewardModal" class="modal-wrapper fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="w-full max-w-2xl rounded-xl bg-white shadow-2xl ring-1 ring-slate-200 animate-pop dark:bg-slate-800 dark:ring-slate-700">
        <form id="rewardForm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">
                    <i class="fas fa-gift mr-2 text-primary-600"></i>Attribuer une Récompense
                </h3>
                <button type="button" onclick="closeRewardModal()" class="rounded-lg text-slate-400 transition-colors hover:text-slate-600 dark:text-slate-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="space-y-4 p-5 sm:p-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label for="selectedReferrer" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Sélectionner le parrain</label>
                        <select id="selectedReferrer" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                            <option value="">Chargement...</option>
                        </select>
                    </div>
                    <div>
                        <label for="rewardType" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Type de récompense</label>
                        <select id="rewardType" required onchange="toggleRewardSections()" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                            <option value="">Choisir le type...</option>
                            <option value="points">Points Bonus</option>
                            <option value="cash">Récompense en Argent</option>
                            <option value="badge">Badge Spécial</option>
                            <option value="level_boost">Boost de Niveau</option>
                        </select>
                    </div>
                </div>

                <!-- Points Section -->
                <div id="pointsSection" class="reward-section hidden rounded-lg bg-sky-50 p-4">
                    <h4 class="mb-3 font-medium text-sky-900">Configuration des Points</h4>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="bonusPoints" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Nombre de points</label>
                            <input type="number" id="bonusPoints" min="1" max="10000" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        </div>
                        <div>
                            <label for="pointsMultiplier" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Multiplicateur</label>
                            <select id="pointsMultiplier" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                                <option value="1">x1 (Normal)</option>
                                <option value="1.5">x1.5</option>
                                <option value="2">x2 (Double)</option>
                                <option value="3">x3 (Triple)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Cash Section -->
                <div id="cashSection" class="reward-section hidden rounded-lg bg-emerald-50 p-4">
                    <h4 class="mb-3 font-medium text-emerald-900">Récompense Financière</h4>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="cashAmount" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Montant</label>
                            <input type="number" id="cashAmount" min="1" step="0.01" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        </div>
                        <div>
                            <label for="cashCurrency" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Devise</label>
                            <select id="cashCurrency" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                                <option value="USD">USD - Dollar Américain</option>
                                <option value="CDF">CDF - Franc Congolais</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Badge Section -->
                <div id="badgeSection" class="reward-section hidden rounded-lg bg-primary-50 p-4">
                    <h4 class="mb-3 font-medium text-primary-900">Badge Spécial</h4>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="badgeName" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Type de badge</label>
                            <select id="badgeName" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                                <option value="top_referrer">Top Parrain</option>
                                <option value="super_ambassador">Super Ambassadeur</option>
                                <option value="loyalty_champion">Champion de Fidélité</option>
                                <option value="growth_master">Maître de Croissance</option>
                            </select>
                        </div>
                        <div>
                            <label for="badgeDuration" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Durée de validité</label>
                            <select id="badgeDuration" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                                <option value="permanent">Permanent</option>
                                <option value="30">30 jours</option>
                                <option value="90">90 jours</option>
                                <option value="365">1 an</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Level Boost Section -->
                <div id="levelSection" class="reward-section hidden rounded-lg bg-amber-50 p-4">
                    <h4 class="mb-3 font-medium text-amber-900">Boost de Niveau</h4>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="levelBoost" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Niveaux à ajouter</label>
                            <select id="levelBoost" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                                <option value="1">+1 Niveau</option>
                                <option value="2">+2 Niveaux</option>
                                <option value="3">+3 Niveaux</option>
                            </select>
                        </div>
                        <div>
                            <label for="boostType" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Type de boost</label>
                            <select id="boostType" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                                <option value="permanent">Permanent</option>
                                <option value="temporary">Temporaire (30 jours)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="rewardReason" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Raison de la récompense</label>
                    <textarea id="rewardReason" rows="3" placeholder="Expliquez pourquoi vous attribuez cette récompense..." class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white"></textarea>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div class="flex items-center">
                        <input type="checkbox" id="sendNotification" checked class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <label for="sendNotification" class="ml-2 text-sm text-slate-700 dark:text-slate-200">Envoyer une notification au parrain</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="makePublic" class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <label for="makePublic" class="ml-2 text-sm text-slate-700 dark:text-slate-200">Rendre publique dans le classement</label>
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse justify-end gap-3 rounded-b-xl bg-slate-50 px-5 py-4 sm:flex-row dark:bg-slate-900">
                <button type="button" onclick="closeRewardModal()" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors sm:w-auto">
                    Annuler
                </button>
                <button type="button" onclick="submitReward()" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors sm:w-auto">
                    <i class="fas fa-gift"></i>Attribuer la Récompense
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
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
        console.error('Token CSRF manquant. Ajoutez <meta name="csrf-token" content="{{ csrf_token() }}"> dans le head.');
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
            container.innerHTML = '<div class="text-center text-slate-500 dark:text-slate-400 py-8">Aucun parrain trouvé</div>';
            return;
        }
        
        let html = '';
        performers.forEach((performer, index) => {
            const medals = ['🥇', '🥈', '🥉'];
            const medal = index < 3 ? medals[index] : '👤';
            const bgColor = index < 3 ? 'bg-gradient-to-r from-amber-50 to-orange-50' : 'bg-slate-50 dark:bg-slate-900';
            
            html += `
                <div class="flex items-center justify-between p-4 rounded-lg ${bgColor} hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center">
                        <div class="text-2xl mr-4">${medal}</div>
                        <div>
                            <div class="font-semibold text-slate-900 dark:text-white">${performer.name}</div>
                            <div class="text-sm text-slate-600 dark:text-slate-300">${performer.email}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-lg text-primary-600">${performer.referrals_count}</div>
                        <div class="text-sm text-slate-500 dark:text-slate-400">parrainages</div>
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
            tbody.innerHTML = '<tr><td colspan="9" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">Aucun parrain trouvé</td></tr>';
            return;
        }
        
        let html = '';
        referrers.forEach((referrer, index) => {
            const levelColors = {
                1: 'bg-sky-100 text-sky-800',
                2: 'bg-emerald-100 text-emerald-800', 
                3: 'bg-amber-100 text-amber-800',
                4: 'bg-primary-100 text-primary-800',
                5: 'bg-red-100 text-red-800'
            };
            
            const levelClass = levelColors[referrer.level] || levelColors[1];
            const lastActivity = referrer.last_activity_at ? 
                new Date(referrer.last_activity_at).toLocaleDateString() : 'Jamais';
            
            html += `
                <tr class="hover:bg-slate-50 dark:bg-slate-900">
                    <td class="px-6 py-4">
                        <input type="checkbox" class="referrer-checkbox h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500 dark:border-slate-600" value="${referrer.id}">
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-white">
                        ${((currentPage - 1) * 10) + index + 1}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-primary-600 to-cyan-400 flex items-center justify-center text-white font-semibold mr-3">
                                ${referrer.name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div class="font-medium text-slate-900 dark:text-white">${referrer.name}</div>
                                <div class="text-sm text-slate-500 dark:text-slate-400">${referrer.email}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${levelClass}">
                            Niveau ${referrer.level || 1}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center font-bold text-lg text-primary-600">
                        ${referrer.referrals_count || 0}
                    </td>
                    <td class="px-6 py-4 text-center font-semibold text-primary-600">
                        ${referrer.total_points || 0}
                    </td>
                    <td class="px-6 py-4 text-center text-sm text-slate-500 dark:text-slate-400">
                        ${lastActivity}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                            ${referrer.rewards_count || 0}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center space-x-2">
                            <button onclick="viewReferrer(${referrer.id})" class="text-primary-600 hover:text-primary-900 p-1 rounded-md hover:bg-primary-50" title="Voir détails">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="rewardReferrer(${referrer.id})" class="text-emerald-600 hover:text-emerald-900 p-1 rounded-md hover:bg-emerald-50" title="Récompenser">
                                <i class="fas fa-gift"></i>
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
                    <button onclick="loadReferrers(${data.current_page - 1})" class="px-3 py-2 ml-0 leading-tight text-slate-500 bg-white dark:bg-slate-800 border border-slate-300 rounded-l-lg hover:bg-slate-100 dark:bg-slate-800 hover:text-slate-700 dark:text-slate-200">
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
                    <button onclick="loadReferrers(${i})" class="px-3 py-2 leading-tight ${isActive ? 'text-white bg-primary-600 border-primary-600' : 'text-slate-500 bg-white dark:bg-slate-800 border-slate-300 hover:bg-slate-100 dark:bg-slate-800 hover:text-slate-700 dark:text-slate-200'} border">
                        ${i}
                    </button>
                </li>
            `;
        }
        
        // Next button
        if (data.next_page_url) {
            html += `
                <li>
                    <button onclick="loadReferrers(${data.current_page + 1})" class="px-3 py-2 leading-tight text-slate-500 bg-white dark:bg-slate-800 border border-slate-300 rounded-r-lg hover:bg-slate-100 dark:bg-slate-800 hover:text-slate-700 dark:text-slate-200">
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
                        '#0EA5E9',
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
                animation: false,
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
        const modal = document.getElementById('rewardModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        loadReferrerOptions();
    };
    
    window.closeRewardModal = function() {
        const modal = document.getElementById('rewardModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
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
@endpush