

<?php $__env->startSection('content'); ?>
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
                <h2><i class="fas fa-qr-code text-secondary"></i> Mes Codes de Parrainage</h2>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-plus"></i> Créer un Nouveau Code</h5>
                            </div>
                            <div class="card-body">
                                <form id="createCodeForm">
                                    <div class="mb-3">
                                        <label class="form-label">Titre du code:</label>
                                        <input type="text" class="form-control" id="codeTitle" maxlength="100" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description:</label>
                                        <textarea class="form-control" id="codeDescription" rows="3" maxlength="500"></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Limite d'utilisation:</label>
                                                <input type="number" class="form-control" id="codeMaxUses" min="1" max="10000">
                                                <small class="form-text text-muted">Laissez vide pour illimité</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Points bonus:</label>
                                                <input type="number" class="form-control" id="codeBonusPoints" min="0" max="1000" step="1">
                                                <small class="form-text text-muted">Points bonus pour le filleul</small>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Créer le Code
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h5><i class="fas fa-info-circle"></i> Conseils</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success"></i> Maximum 5 codes actifs par utilisateur</li>
                                    <li><i class="fas fa-check text-success"></i> Codes générés automatiquement</li>
                                    <li><i class="fas fa-check text-success"></i> Partagez vos codes sur les réseaux sociaux</li>
                                    <li><i class="fas fa-check text-success"></i> Plus de parrainages = plus de points</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-list"></i> Mes Codes Existants</h5>
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
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
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/affiliate-dashboard.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/affiliate/dashboard.blade.php ENDPATH**/ ?>