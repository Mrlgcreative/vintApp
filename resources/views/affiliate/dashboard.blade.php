@extends('app')
@section('title', 'Tableau de bord Affiliation')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 md:py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- En-tête de page --}}
        <div class="mb-6 md:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                    <x-icon icon="fas fa-users" size="md" class="mr-3" />
                    Espace Affiliation
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                    <i class="fas fa-circle-info"></i>
                    Gagnez des points en faisant parrainer vos amis
                </p>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-5 lg:gap-6">

            <!-- Sidebar Navigation -->
            <div class="lg:w-64 flex-shrink-0">
                <x-card class="p-5 lg:sticky lg:top-24">
                    <div class="flex items-center gap-2.5 px-2 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-vinted-primary-600 text-white flex items-center justify-center">
                            <i class="fas fa-hands-helping text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">Affiliation</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Programme de parrainage</p>
                        </div>
                    </div>
                    <nav class="space-y-1.5">
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-vinted-primary-600 text-white font-medium transition-colors" href="#dashboard" data-section="dashboard">
                            <i class="fas fa-tachometer-alt w-4 text-center"></i> Dashboard
                        </a>
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 font-medium transition-colors" href="#points" data-section="points">
                            <i class="fas fa-coins w-4 text-center"></i> Mes Points
                        </a>
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 font-medium transition-colors" href="#referrals" data-section="referrals">
                            <i class="fas fa-user-friends w-4 text-center"></i> Parrainages
                        </a>
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 font-medium transition-colors" href="#codes" data-section="codes">
                            <i class="fas fa-qr-code w-4 text-center"></i> Mes Codes
                        </a>
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 font-medium transition-colors" href="#redemptions" data-section="redemptions">
                            <i class="fas fa-arrow-right-arrow-left w-4 text-center"></i> Rachats
                        </a>
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 font-medium transition-colors" href="#leaderboard" data-section="leaderboard">
                            <i class="fas fa-trophy w-4 text-center"></i> Classement
                        </a>
                    </nav>
                </x-card>
            </div>

            <!-- Main Content -->
            <div class="flex-1 min-w-0">

                <!-- Dashboard Section -->
                <div id="section-dashboard" class="content-section">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-tachometer-alt text-vinted-primary-600 mr-3"></i> Dashboard Affiliation
                        </h2>
                        <div class="flex flex-wrap gap-2.5">
                            <x-button-primary size="sm" id="shareReferralBtn">
                                <i class="fas fa-share-alt mr-1.5"></i> Partager mon code
                            </x-button-primary>
                            <x-button-outline size="sm" id="refreshDataBtn">
                                <i class="fas fa-arrows-rotate mr-1.5"></i> Actualiser
                            </x-button-outline>
                            <x-button-primary size="sm" onclick="window.openModal('createCodeModal')">
                                <i class="fas fa-plus mr-1.5"></i> Nouveau Code
                            </x-button-primary>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-5 mb-8" id="statsCards">
                        <!-- Stats will be loaded here -->
                    </div>

                    <!-- Recent Activity -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                        <div class="lg:col-span-2">
                            <x-card>
                                <x-card-header icon="fas fa-clock-rotate-left" tone="blue" title="Activité récente" />
                                <div class="p-4 md:p-6">
                                    <div id="recentTransactions" class="space-y-2">
                                        <!-- Recent transactions will be loaded here -->
                                    </div>
                                </div>
                            </x-card>
                        </div>
                        <div class="lg:col-span-1">
                            <x-card>
                                <x-card-header icon="fas fa-chart-line" tone="indigo" title="Progression" />
                                <div class="p-4 md:p-6">
                                    <div id="levelProgress" class="text-center">
                                        <!-- Level progress will be loaded here -->
                                    </div>
                                </div>
                            </x-card>
                        </div>
                    </div>
                </div>

                <!-- Points Section -->
                <div id="section-points" class="content-section hidden">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white flex items-center mb-6">
                        <i class="fas fa-coins text-yellow-500 mr-3"></i> Gestion des Points
                    </h2>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-8">
                        <x-card>
                            <x-card-header icon="fas fa-money-bill-wave" tone="emerald" title="Convertir en Argent" />
                            <div class="p-5 md:p-6">
                                <form id="convertCashForm" class="space-y-4">
                                    <div>
                                        <x-label for="cashPoints" icon="fas fa-coins" iconTone="emerald">Points à convertir</x-label>
                                        <x-input id="cashPoints" type="number" min="100" step="1" required />
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 flex items-center gap-1.5">
                                            <i class="fas fa-circle-info"></i> Minimum 100 points
                                        </p>
                                    </div>
                                    <div>
                                        <x-label for="cashCurrency" icon="fas fa-money-bill" iconTone="emerald">Devise</x-label>
                                        <x-select id="cashCurrency" required>
                                            <option value="USD">USD - Dollar Américain</option>
                                            <option value="CDF">CDF - Franc Congolais</option>
                                        </x-select>
                                    </div>
                                    <div id="conversionPreview">
                                        <!-- Conversion preview will appear here -->
                                    </div>
                                    <x-button-primary type="submit" class="w-full inline-flex items-center justify-center gap-2">
                                        <i class="fas fa-arrow-right-arrow-left"></i> Convertir
                                    </x-button-primary>
                                </form>
                            </div>
                        </x-card>
                        <x-card>
                            <x-card-header icon="fas fa-percent" tone="blue" title="Générer un Code Réduction" />
                            <div class="p-5 md:p-6">
                                <form id="generateDiscountForm" class="space-y-4">
                                    <div>
                                        <x-label for="discountPoints" icon="fas fa-coins" iconTone="blue">Points à utiliser</x-label>
                                        <x-input id="discountPoints" type="number" min="100" max="5000" step="1" required />
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 flex items-center gap-1.5">
                                            <i class="fas fa-circle-info"></i> 100-5000 points (100 pts = 1% de réduction)
                                        </p>
                                    </div>
                                    <div>
                                        <x-label for="discountExpiry" icon="fas fa-calendar-days" iconTone="blue">Durée de validité (jours)</x-label>
                                        <x-select id="discountExpiry">
                                            <option value="7">7 jours</option>
                                            <option value="15">15 jours</option>
                                            <option value="30" selected>30 jours</option>
                                            <option value="60">60 jours</option>
                                        </x-select>
                                    </div>
                                    <div id="discountPreview">
                                        <!-- Discount preview will appear here -->
                                    </div>
                                    <x-button-primary type="submit" class="w-full inline-flex items-center justify-center gap-2">
                                        <i class="fas fa-ticket-simple"></i> Générer Code
                                    </x-button-primary>
                                </form>
                            </div>
                        </x-card>
                    </div>

                    <!-- Points History -->
                    <x-card>
                        <x-card-header icon="fas fa-clock-rotate-left" tone="blue" title="Historique des Points">
                            <x-slot name="actions">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <x-select id="historyType" class="sm:w-44">
                                        <option value="all">Tous les types</option>
                                        <option value="earn_referral">Parrainages</option>
                                        <option value="earn_purchase">Achats</option>
                                        <option value="earn_sale">Ventes</option>
                                        <option value="redeem_cash">Conversions argent</option>
                                        <option value="redeem_discount">Codes réduction</option>
                                    </x-select>
                                    <x-select id="historyPeriod" class="sm:w-44">
                                        <option value="all">Toute période</option>
                                        <option value="today">Aujourd'hui</option>
                                        <option value="this_week">Cette semaine</option>
                                        <option value="this_month">Ce mois</option>
                                    </x-select>
                                </div>
                            </x-slot>
                        </x-card-header>
                        <div class="p-4 md:p-6">
                            <div id="pointsHistory">
                                <!-- Points history will be loaded here -->
                            </div>
                        </div>
                    </x-card>
                </div>

                <!-- Referrals Section -->
                <div id="section-referrals" class="content-section hidden">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white flex items-center mb-6">
                        <i class="fas fa-user-friends text-blue-500 mr-3"></i> Mes Parrainages
                    </h2>

                    <x-card>
                        <x-card-header icon="fas fa-list" tone="blue" title="Liste des Parrainages" />
                        <div class="p-4 md:p-6">
                            <div id="referralsList" class="space-y-2">
                                <!-- Referrals list will be loaded here -->
                            </div>
                        </div>
                    </x-card>
                </div>

                <!-- Codes Section -->
                <div id="section-codes" class="content-section hidden">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-qr-code text-gray-600 dark:text-gray-300 mr-3"></i> Mes Codes de Parrainage
                        </h2>
                        <x-button-primary size="sm" onclick="window.openModal('createCodeModal')">
                            <i class="fas fa-plus mr-1.5"></i> Nouveau Code
                        </x-button-primary>
                    </div>

                    <!-- Stats Cards for Codes -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-5 mb-8">
                        <x-stat-card id="totalCodes" value="0" label="Codes Créés" icon="fas fa-qr-code" tone="blue" />
                        <x-stat-card id="activeCodes" value="0" label="Codes Actifs" icon="fas fa-circle-check" tone="emerald" />
                        <x-stat-card id="totalUses" value="0" label="Utilisations" icon="fas fa-users" tone="sky" />
                        <x-stat-card id="bestPerforming" value="-" label="Meilleur Code" icon="fas fa-star" tone="amber" />
                    </div>

                    <!-- Codes List -->
                    <x-card>
                        <x-card-header icon="fas fa-list" tone="blue" title="Mes Codes Existants">
                            <x-slot name="actions">
                                <div class="flex gap-2.5">
                                    <x-select id="codeStatusFilter" class="sm:w-44">
                                        <option value="all">Tous les statuts</option>
                                        <option value="active">Actifs</option>
                                        <option value="inactive">Inactifs</option>
                                        <option value="expired">Expirés</option>
                                    </x-select>
                                    <x-button-outline size="sm" onclick="window.refreshCodesList()" title="Actualiser la liste">
                                        <i class="fas fa-arrows-rotate"></i>
                                    </x-button-outline>
                                </div>
                            </x-slot>
                        </x-card-header>
                        <div class="p-4 md:p-6">
                            <div id="referralCodesList" class="space-y-4">
                                <!-- Referral codes list will be loaded here -->
                            </div>
                        </div>
                    </x-card>
                </div>

                <!-- Redemptions Section -->
                <div id="section-redemptions" class="content-section hidden">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white flex items-center mb-6">
                        <i class="fas fa-arrow-right-arrow-left text-red-600 mr-3"></i> Mes Rachats
                    </h2>

                    <x-card>
                        <x-card-header icon="fas fa-receipt" tone="red" title="Historique des Rachats" />
                        <div class="p-4 md:p-6">
                            <div id="redemptionsList" class="space-y-2">
                                <!-- Redemptions list will be loaded here -->
                            </div>
                        </div>
                    </x-card>
                </div>

                <!-- Leaderboard Section -->
                <div id="section-leaderboard" class="content-section hidden">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white flex items-center mb-6">
                        <i class="fas fa-trophy text-yellow-500 mr-3"></i> Classement
                    </h2>

                    <x-card>
                        <x-card-header icon="fas fa-medal" tone="amber" title="Top 50 des Parrains" />
                        <div class="p-4 md:p-6">
                            <div id="leaderboardList" class="space-y-2">
                                <!-- Leaderboard will be loaded here -->
                            </div>
                        </div>
                    </x-card>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Code Modal -->
<x-modal id="createCodeModal" maxWidth="4xl" title="Créer un Nouveau Code de Parrainage" icon="fas fa-plus" tone="blue">
    <form id="createCodeForm">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-5">
                <div>
                    <x-label for="codeTitle" icon="fas fa-heading" iconTone="blue">Titre du code</x-label>
                    <div class="flex gap-2">
                        <x-input
                            id="codeTitle"
                            placeholder="Ex: PARRAINS2024"
                            maxlength="50"
                            oninput="window.updateCodePreview()"
                        />
                        <button
                            type="button"
                            onclick="window.generateCodeTitle()"
                            title="Générer automatiquement"
                            class="flex-shrink-0 px-3 py-2.5 rounded-lg bg-vinted-primary-600 hover:bg-vinted-primary-700 text-white transition-colors"
                        >
                            <i class="fas fa-wand-magic-sparkles"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 flex items-center gap-1.5">
                        <i class="fas fa-circle-info"></i> Le titre sera utilisé pour identifier votre code
                    </p>
                </div>

                <div>
                    <x-label for="codeDescription" icon="fas fa-align-left" iconTone="emerald">Description (optionnelle)</x-label>
                    <x-textarea
                        id="codeDescription"
                        rows="3"
                        maxlength="500"
                        placeholder="Décrivez votre code de parrainage..."
                        oninput="window.updateCodePreview()"></x-textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                        <span id="descriptionCount">0</span>/500 caractères
                    </p>
                </div>

                <div>
                    <x-label for="codeType" icon="fas fa-tag" iconTone="purple">Type de code</x-label>
                    <x-select id="codeType" onchange="window.updateCodePreview()">
                        <option value="general">Général - Pour tous</option>
                        <option value="limited">Limité - Nombre d'utilisations restreint</option>
                        <option value="premium">Premium - Bonus supplémentaires</option>
                        <option value="seasonal">Saisonnier - Événement spécial</option>
                    </x-select>
                </div>

                <div>
                    <x-label for="codeMaxUses" icon="fas fa-users" iconTone="orange">Limite d'utilisation</x-label>
                    <x-input
                        id="codeMaxUses"
                        type="number"
                        placeholder="Illimité si vide"
                        min="1"
                        oninput="window.updateCodePreview()"
                    />
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 flex items-center gap-1.5">
                        <i class="fas fa-circle-info"></i> Laissez vide pour un usage illimité
                    </p>
                </div>

                <div>
                    <x-label for="codeBonusPoints" icon="fas fa-gift" iconTone="yellow">Points bonus (optionnel)</x-label>
                    <x-input
                        id="codeBonusPoints"
                        type="number"
                        placeholder="0"
                        min="0"
                        oninput="window.updateCodePreview()"
                    />
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 flex items-center gap-1.5">
                        <i class="fas fa-coins"></i> Points bonus pour les utilisateurs de ce code
                    </p>
                </div>

                <div>
                    <x-label for="codeExpiresAt" icon="fas fa-calendar-days" iconTone="red">Date d'expiration (optionnelle)</x-label>
                    <x-input
                        id="codeExpiresAt"
                        type="date"
                        oninput="window.updateCodePreview()"
                    />
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 flex items-center gap-1.5">
                        <i class="fas fa-circle-info"></i> Laissez vide si le code n'expire pas
                    </p>
                </div>
            </div>

            <!-- Preview -->
            <div>
                <x-label icon="fas fa-eye" iconTone="indigo">Aperçu du Code</x-label>
                <div id="codePreview" class="bg-gray-50 dark:bg-gray-900/40 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl p-5 min-h-[400px]">
                    <div class="text-center text-gray-400 dark:text-gray-500 py-10">
                        <i class="fas fa-code text-4xl mb-3"></i>
                        <p>Remplissez le formulaire pour voir l'aperçu</p>
                    </div>
                </div>

                <x-alert variant="info" class="mt-4">
                    <h6 class="font-semibold flex items-center mb-2">
                        <i class="fas fa-lightbulb mr-2"></i> Conseils
                    </h6>
                    <ul class="opacity-90 space-y-1.5 text-sm">
                        <li><i class="fas fa-circle-check text-emerald-500 mr-1.5"></i> Utilisez un titre unique et mémorable</li>
                        <li><i class="fas fa-circle-check text-emerald-500 mr-1.5"></i> Ajoutez une description pour expliquer l'usage</li>
                        <li><i class="fas fa-circle-check text-emerald-500 mr-1.5"></i> Limitez les utilisations pour les codes spéciaux</li>
                        <li><i class="fas fa-circle-check text-emerald-500 mr-1.5"></i> Les codes Premium offrent plus de points</li>
                    </ul>
                </x-alert>
            </div>
        </div>
    </form>

    <x-slot name="footer">
        <div class="text-sm text-gray-500 dark:text-gray-400 hidden sm:flex items-center gap-1.5">
            <i class="fas fa-circle-info"></i> Votre code sera généré automatiquement
        </div>
        <div class="flex gap-3">
            <x-button-outline onclick="window.closeModal('createCodeModal')">
                <i class="fas fa-xmark mr-2"></i> Annuler
            </x-button-outline>
            <x-button-primary type="submit" form="createCodeForm">
                <i class="fas fa-plus mr-2"></i> Créer le Code
            </x-button-primary>
        </div>
    </x-slot>
</x-modal>

<!-- Share Modal -->
<x-modal id="shareModal" maxWidth="md" title="Partager mon Code" icon="fas fa-share-nodes" tone="blue">
    <div class="space-y-4">
        <div>
            <x-label for="shareCode" icon="fas fa-code" iconTone="blue">Code de parrainage</x-label>
            <x-copy-field target="#shareCode" id="shareCode" mono />
        </div>

        <div>
            <x-label for="shareUrl" icon="fas fa-link" iconTone="blue">Lien de partage</x-label>
            <x-copy-field target="#shareUrl" id="shareUrl" />
        </div>

        <div class="pt-4 border-t border-gray-100 dark:border-gray-700/50">
            <x-label icon="fas fa-share-nodes" iconTone="blue">Partager sur les réseaux sociaux</x-label>
            <div class="grid grid-cols-3 gap-3">
                <button
                    type="button"
                    class="flex flex-col items-center justify-center gap-1 p-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-colors shadow-sm"
                    onclick="window.shareOnFacebook()"
                >
                    <i class="fab fa-facebook-f text-lg"></i>
                    <span class="text-xs font-medium">Facebook</span>
                </button>
                <button
                    type="button"
                    class="flex flex-col items-center justify-center gap-1 p-3 bg-sky-500 hover:bg-sky-600 text-white rounded-xl transition-colors shadow-sm"
                    onclick="window.shareOnTwitter()"
                >
                    <i class="fab fa-twitter text-lg"></i>
                    <span class="text-xs font-medium">Twitter</span>
                </button>
                <button
                    type="button"
                    class="flex flex-col items-center justify-center gap-1 p-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition-colors shadow-sm"
                    onclick="window.shareOnWhatsApp()"
                >
                    <i class="fab fa-whatsapp text-lg"></i>
                    <span class="text-xs font-medium">WhatsApp</span>
                </button>
            </div>
        </div>
    </div>
</x-modal>

<script>
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