@extends('app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    // Contexte utilisateur pour JavaScript
    window.user = @json([
        'id' => auth()->user()->id ?? 0,
        'name' => auth()->user()->name ?? 'Utilisateur',
        'referral_code' => auth()->user()->referral_code ?? 'REF001',
        'available_points' => auth()->user()->getOrCreatePoints()->available_points ?? 0,
        'level' => auth()->user()->getOrCreatePoints()->level ?? 1,
        'level_name' => auth()->user()->getOrCreatePoints()->level_name ?? 'Bronze',
        'level_progress' => auth()->user()->getOrCreatePoints()->level_progress_percentage ?? 0,
        'points_to_next_level' => auth()->user()->getOrCreatePoints()->points_to_next_level ?? 1000,
        'referrals_count' => auth()->user()->referrals()->where('status', 'completed')->count() ?? 0,
        'total_redeemed' => auth()->user()->pointRedemptions()->sum('cash_amount') ?? 0
    ]);
</script>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3 col-lg-2">
            <div class="card">
                <div class="card-body p-2">
                    <h6 class="card-title text-primary mb-3">
                        <i class="fas fa-users"></i> Affiliation
                    </h6>
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="#dashboard" data-section="dashboard">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a class="nav-link" href="#points" data-section="points">
                            <i class="fas fa-coins"></i> Mes Points
                        </a>
                        <a class="nav-link" href="#referrals" data-section="referrals">
                            <i class="fas fa-user-friends"></i> Parrainages
                        </a>
                        <a class="nav-link" href="#codes" data-section="codes">
                            <i class="fas fa-qr-code"></i> Mes Codes
                        </a>
                        <a class="nav-link" href="#redemptions" data-section="redemptions">
                            <i class="fas fa-exchange-alt"></i> Rachats
                        </a>
                        <a class="nav-link" href="#leaderboard" data-section="leaderboard">
                            <i class="fas fa-trophy"></i> Classement
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <!-- Dashboard Section -->
            <div id="section-dashboard" class="content-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-tachometer-alt text-primary"></i> Dashboard Affiliation</h2>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary btn-sm" id="shareReferralBtn">
                            <i class="fas fa-share-alt"></i> Partager mon code
                        </button>
                        <button class="btn btn-success btn-sm" id="refreshDataBtn">
                            <i class="fas fa-sync-alt"></i> Actualiser
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4" id="statsCards">
                    <!-- Stats will be loaded here -->
                </div>

                <!-- Recent Activity -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-history"></i> Activité Récente</h5>
                            </div>
                            <div class="card-body">
                                <div id="recentTransactions" class="list-group">
                                    <!-- Recent transactions will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-chart-line"></i> Progression</h5>
                            </div>
                            <div class="card-body text-center">
                                <div id="levelProgress">
                                    <!-- Level progress will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Points Section -->
            <div id="section-points" class="content-section d-none">
                <h2><i class="fas fa-coins text-warning"></i> Gestion des Points</h2>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h5><i class="fas fa-money-bill-wave"></i> Convertir en Argent</h5>
                            </div>
                            <div class="card-body">
                                <form id="convertCashForm">
                                    <div class="mb-3">
                                        <label class="form-label">Points à convertir:</label>
                                        <input type="number" class="form-control" id="cashPoints" min="100" step="1" required>
                                        <small class="form-text text-muted">Minimum 100 points</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Devise:</label>
                                        <select class="form-control" id="cashCurrency" required>
                                            <option value="USD">USD - Dollar Américain</option>
                                            <option value="CDF">CDF - Franc Congolais</option>
                                        </select>
                                    </div>
                                    <div class="mb-3" id="conversionPreview">
                                        <!-- Conversion preview will appear here -->
                                    </div>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-exchange-alt"></i> Convertir
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h5><i class="fas fa-percentage"></i> Générer Code Réduction</h5>
                            </div>
                            <div class="card-body">
                                <form id="generateDiscountForm">
                                    <div class="mb-3">
                                        <label class="form-label">Points à utiliser:</label>
                                        <input type="number" class="form-control" id="discountPoints" min="100" max="5000" step="1" required>
                                        <small class="form-text text-muted">100-5000 points (100 pts = 1% de réduction)</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Durée de validité (jours):</label>
                                        <select class="form-control" id="discountExpiry">
                                            <option value="7">7 jours</option>
                                            <option value="15">15 jours</option>
                                            <option value="30" selected>30 jours</option>
                                            <option value="60">60 jours</option>
                                        </select>
                                    </div>
                                    <div class="mb-3" id="discountPreview">
                                        <!-- Discount preview will appear here -->
                                    </div>
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-ticket-alt"></i> Générer Code
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Points History -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-history"></i> Historique des Points</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <select class="form-control" id="historyType">
                                    <option value="all">Tous les types</option>
                                    <option value="earn_referral">Parrainages</option>
                                    <option value="earn_purchase">Achats</option>
                                    <option value="earn_sale">Ventes</option>
                                    <option value="redeem_cash">Conversions argent</option>
                                    <option value="redeem_discount">Codes réduction</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control" id="historyPeriod">
                                    <option value="all">Toute période</option>
                                    <option value="today">Aujourd'hui</option>
                                    <option value="this_week">Cette semaine</option>
                                    <option value="this_month">Ce mois</option>
                                </select>
                            </div>
                        </div>
                        <div id="pointsHistory">
                            <!-- Points history will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Referrals Section -->
            <div id="section-referrals" class="content-section d-none">
                <h2><i class="fas fa-user-friends text-info"></i> Mes Parrainages</h2>
                
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-list"></i> Liste des Parrainages</h5>
                    </div>
                    <div class="card-body">
                        <div id="referralsList">
                            <!-- Referrals list will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Codes Section -->
            <div id="section-codes" class="content-section d-none">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-qr-code text-secondary"></i> Mes Codes de Parrainage</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCodeModal">
                        <i class="fas fa-plus"></i> Nouveau Code
                    </button>
                </div>
                
                <!-- Stats Cards for Codes -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <div class="text-primary mb-2">
                                    <i class="fas fa-qr-code fa-2x"></i>
                                </div>
                                <h4 class="card-title text-primary" id="totalCodes">0</h4>
                                <p class="card-text text-muted">Codes Créés</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <div class="text-success mb-2">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                                <h4 class="card-title text-success" id="activeCodes">0</h4>
                                <p class="card-text text-muted">Codes Actifs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <div class="text-info mb-2">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                                <h4 class="card-title text-info" id="totalUses">0</h4>
                                <p class="card-text text-muted">Utilisations</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <div class="text-warning mb-2">
                                    <i class="fas fa-star fa-2x"></i>
                                </div>
                                <h4 class="card-title text-warning" id="bestPerforming">-</h4>
                                <p class="card-text text-muted">Meilleur Code</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Codes List -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Mes Codes Existants</h5>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" id="codeStatusFilter">
                                <option value="all">Tous les statuts</option>
                                <option value="active">Actifs</option>
                                <option value="inactive">Inactifs</option>
                                <option value="expired">Expirés</option>
                            </select>
                            <button class="btn btn-sm btn-outline-secondary" onclick="refreshCodesList()">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="referralCodesList">
                            <!-- Referral codes list will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Redemptions Section -->
            <div id="section-redemptions" class="content-section d-none">
                <h2><i class="fas fa-exchange-alt text-danger"></i> Mes Rachats</h2>
                
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-list"></i> Historique des Rachats</h5>
                    </div>
                    <div class="card-body">
                        <div id="redemptionsList">
                            <!-- Redemptions list will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leaderboard Section -->
            <div id="section-leaderboard" class="content-section d-none">
                <h2><i class="fas fa-trophy text-warning"></i> Classement</h2>
                
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-medal"></i> Top 50 des Parrains</h5>
                    </div>
                    <div class="card-body">
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
<div class="modal fade" id="createCodeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Créer un Nouveau Code de Parrainage</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createCodeForm">
                    <!-- Auto-generated title display -->
                    <div class="mb-3">
                        <label class="form-label">Titre du code (généré automatiquement):</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="codeTitle" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="generateCodeTitle()">
                                <i class="fas fa-refresh"></i> Regénérer
                            </button>
                        </div>
                        <small class="form-text text-muted">Le titre sera automatiquement généré lors de la création</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Description (optionnelle):</label>
                                <textarea class="form-control" id="codeDescription" rows="3" maxlength="500" placeholder="Décrivez votre code de parrainage..."></textarea>
                                <small class="form-text text-muted">Maximum 500 caractères</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Type de code:</label>
                                <select class="form-control" id="codeType" onchange="updateCodePreview()">
                                    <option value="general">Général</option>
                                    <option value="limited">Limité</option>
                                    <option value="premium">Premium</option>
                                    <option value="seasonal">Saisonnier</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Limite d'utilisation:</label>
                                <input type="number" class="form-control" id="codeMaxUses" min="1" max="10000" placeholder="Illimité">
                                <small class="form-text text-muted">Laissez vide pour illimité</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Points bonus pour le filleul:</label>
                                <input type="number" class="form-control" id="codeBonusPoints" min="0" max="1000" step="10" placeholder="0">
                                <small class="form-text text-muted">Points supplémentaires à l'inscription</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Date d'expiration:</label>
                                <select class="form-control" id="codeExpiry">
                                    <option value="">Pas d'expiration</option>
                                    <option value="7">7 jours</option>
                                    <option value="30">30 jours</option>
                                    <option value="60">60 jours</option>
                                    <option value="90">90 jours</option>
                                    <option value="365">1 an</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Statut:</label>
                                <select class="form-control" id="codeStatus">
                                    <option value="active">Actif</option>
                                    <option value="inactive">Inactif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Code Preview -->
                    <div class="card bg-light mb-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-eye"></i> Aperçu du Code</h6>
                        </div>
                        <div class="card-body">
                            <div id="codePreview" class="text-center">
                                <div class="code-display p-3 border rounded bg-white">
                                    <h5 id="previewTitle">Code Parrainage #001</h5>
                                    <div class="code-value h4 text-primary font-monospace" id="previewCode">PARRAINS001</div>
                                    <div class="code-details">
                                        <small class="text-muted" id="previewDetails">Général • Illimité • Permanent</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Annuler
                </button>
                <button type="submit" form="createCodeForm" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Créer le Code
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-share-alt"></i> Partager mon Code de Parrainage</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Mon code de parrainage:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="shareCode" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('#shareCode')">
                            <i class="fas fa-copy"></i> Copier
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Lien de parrainage:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="shareUrl" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('#shareUrl')">
                            <i class="fas fa-copy"></i> Copier
                        </button>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" onclick="shareOnFacebook()">
                        <i class="fab fa-facebook"></i> Partager sur Facebook
                    </button>
                    <button class="btn btn-info" onclick="shareOnTwitter()">
                        <i class="fab fa-twitter"></i> Partager sur Twitter
                    </button>
                    <button class="btn btn-success" onclick="shareOnWhatsApp()">
                        <i class="fab fa-whatsapp"></i> Partager sur WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.content-section {
    min-height: 600px;
}

.nav-link {
    color: #6c757d;
    border-radius: 0.375rem;
    margin-bottom: 2px;
}

.nav-link:hover {
    background-color: #f8f9fa;
    color: #0d6efd;
}

.nav-link.active {
    background-color: #0d6efd;
    color: white !important;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stats-card .card-body {
    text-align: center;
    padding: 1.5rem;
}

.stats-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.progress-circle {
    width: 120px;
    height: 120px;
    margin: 0 auto;
}

.list-group-item {
    border: none;
    border-bottom: 1px solid #dee2e6;
    padding: 1rem 0;
}

.list-group-item:last-child {
    border-bottom: none;
}

.badge-points {
    background-color: #28a745;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}

.badge-level {
    background: linear-gradient(45deg, #ffd700, #ffed4e);
    color: #000;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-weight: bold;
}

.referral-code-card {
    border: 2px dashed #0d6efd;
    background-color: #f8f9ff;
}

.share-buttons .btn {
    margin: 0.25rem;
}

/* Code Section Styles */
.code-display {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px dashed #0d6efd !important;
}

.code-value {
    letter-spacing: 2px;
    font-weight: bold;
    text-transform: uppercase;
}

.modal-lg {
    max-width: 800px;
}

/* Stats cards hover effect */
.card:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease-in-out;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Code type badges */
.code-type-general { background-color: #6c757d; }
.code-type-limited { background-color: #fd7e14; }
.code-type-premium { background-color: #6f42c1; }
.code-type-seasonal { background-color: #20c997; }

/* Form enhancements */
.form-select-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* Code list enhancements */
.code-card {
    border-left: 4px solid #0d6efd;
    margin-bottom: 1rem;
    transition: all 0.2s ease-in-out;
}

.code-card:hover {
    border-left-color: #0a58ca;
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
}

.code-stats {
    display: flex;
    gap: 1rem;
    margin-top: 0.5rem;
}

.code-stat {
    font-size: 0.875rem;
    color: #6c757d;
}

.code-actions {
    display: flex;
    gap: 0.5rem;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .modal-lg {
        max-width: 95%;
        margin: 1rem auto;
    }
    
    .code-stats {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .code-actions {
        flex-direction: column;
    }
    
    .code-actions .btn {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/affiliate-dashboard.js') }}"></script>
@endpush