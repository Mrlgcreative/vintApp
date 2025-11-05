@extends('app')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-users-cog text-primary"></i> Administration des Parrains</h2>
            <p class="text-muted">Gérez les parrains, récompenses et statistiques d'affiliation</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success" id="bulkRewardBtn">
                <i class="fas fa-gift"></i> Récompenses en Masse
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRewardModal">
                <i class="fas fa-plus"></i> Nouvelle Récompense
            </button>
            <button class="btn btn-outline-secondary" onclick="exportData()">
                <i class="fas fa-download"></i> Exporter
            </button>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0" id="totalSponsors">0</h3>
                            <p class="mb-0">Total Parrains</p>
                        </div>
                        <div class="fs-1 opacity-75">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm bg-gradient-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0" id="activeSponsors">0</h3>
                            <p class="mb-0">Parrains Actifs</p>
                        </div>
                        <div class="fs-1 opacity-75">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm bg-gradient-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0" id="totalReferrals">0</h3>
                            <p class="mb-0">Total Parrainages</p>
                        </div>
                        <div class="fs-1 opacity-75">
                            <i class="fas fa-handshake"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm bg-gradient-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-0" id="totalRewards">0</h3>
                            <p class="mb-0">Récompenses Données</p>
                        </div>
                        <div class="fs-1 opacity-75">
                            <i class="fas fa-gift"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Rechercher</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchSponsors" placeholder="Nom, email, code...">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Niveau</label>
                    <select class="form-select" id="levelFilter">
                        <option value="">Tous niveaux</option>
                        <option value="1">Débutant (1-2)</option>
                        <option value="3">Intermédiaire (3-5)</option>
                        <option value="6">Avancé (6-8)</option>
                        <option value="9">Expert (9+)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">Tous statuts</option>
                        <option value="active">Actif</option>
                        <option value="inactive">Inactif</option>
                        <option value="top_performer">Top Performeur</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Période</label>
                    <select class="form-select" id="periodFilter">
                        <option value="all">Toute période</option>
                        <option value="this_month">Ce mois</option>
                        <option value="last_month">Mois dernier</option>
                        <option value="this_year">Cette année</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tri</label>
                    <select class="form-select" id="sortFilter">
                        <option value="referrals_desc">+ Parrainages</option>
                        <option value="points_desc">+ Points</option>
                        <option value="level_desc">+ Niveau</option>
                        <option value="recent">+ Récent</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary w-100" onclick="applyFilters()">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performers Section -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <!-- Main Sponsors Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-trophy"></i> Classement des Parrains</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                            <i class="fas fa-check-double"></i> Tout sélectionner
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="refreshData()">
                            <i class="fas fa-sync-alt"></i> Actualiser
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">
                                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                                    </th>
                                    <th>Rang</th>
                                    <th>Parrain</th>
                                    <th>Niveau</th>
                                    <th>Parrainages</th>
                                    <th>Points</th>
                                    <th>Dernière Activité</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="sponsorsTable">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Affichage <span id="showing">0</span> sur <span id="total">0</span> parrains
                        </div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0" id="pagination">
                                <!-- Pagination will be generated here -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Top 3 Podium -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-medal"></i> Podium du Mois</h5>
                </div>
                <div class="card-body">
                    <div id="podium" class="text-center">
                        <!-- Podium will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Recent Rewards -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Récompenses Récentes</h5>
                </div>
                <div class="card-body">
                    <div id="recentRewards">
                        <!-- Recent rewards will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Section -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Évolution des Parrainages</h5>
                </div>
                <div class="card-body">
                    <canvas id="referralsChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Répartition par Niveau</h5>
                </div>
                <div class="card-body">
                    <canvas id="levelsChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Reward Modal -->
<div class="modal fade" id="createRewardModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-gift"></i> Créer une Récompense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createRewardForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Type de récompense</label>
                                <select class="form-select" id="rewardType" onchange="updateRewardForm()">
                                    <option value="points">Points Bonus</option>
                                    <option value="badge">Badge/Titre</option>
                                    <option value="cash">Argent</option>
                                    <option value="product">Produit Gratuit</option>
                                    <option value="discount">Code Réduction</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Destinataires</label>
                                <select class="form-select" id="rewardRecipients">
                                    <option value="selected">Parrains sélectionnés</option>
                                    <option value="top_10">Top 10 du mois</option>
                                    <option value="level_5_plus">Niveau 5+</option>
                                    <option value="active_month">Actifs ce mois</option>
                                    <option value="all">Tous les parrains</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Titre de la récompense</label>
                        <input type="text" class="form-control" id="rewardTitle" placeholder="Ex: Bonus Parrain du Mois">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="rewardDescription" rows="3" placeholder="Description de la récompense..."></textarea>
                    </div>

                    <!-- Dynamic reward fields -->
                    <div id="rewardFields">
                        <!-- Fields will change based on reward type -->
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Date d'expiration</label>
                                <input type="date" class="form-control" id="rewardExpiry">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Notification</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="sendNotification" checked>
                                    <label class="form-check-label" for="sendNotification">
                                        Envoyer notification aux destinataires
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recipients Preview -->
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6><i class="fas fa-users"></i> Destinataires (<span id="recipientCount">0</span>)</h6>
                            <div id="recipientsList" class="small">
                                <!-- Recipients will be shown here -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-gift"></i> Créer la Récompense
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Sponsor Details Modal -->
<div class="modal fade" id="sponsorDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user"></i> Détails du Parrain</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="sponsorDetailsContent">
                <!-- Sponsor details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Bulk Reward Modal -->
<div class="modal fade" id="bulkRewardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-gifts"></i> Récompenses en Masse</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Sélectionnez des parrains dans le tableau puis choisissez le type de récompense à attribuer.
                </div>
                <div id="bulkRewardContent">
                    <!-- Bulk reward form will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}
.bg-gradient-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}
.bg-gradient-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.sponsor-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.sponsor-avatar-placeholder {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
}

.level-badge {
    background: linear-gradient(45deg, #ffd700, #ffed4e);
    color: #000;
    border: none;
    font-weight: bold;
    padding: 0.25rem 0.5rem;
    border-radius: 0.5rem;
}

.rank-medal {
    font-size: 1.2em;
}

.rank-1 { color: #ffd700; }
.rank-2 { color: #c0c0c0; }
.rank-3 { color: #cd7f32; }

.podium-card {
    border: none;
    border-radius: 1rem;
    transition: transform 0.2s;
}

.podium-card:hover {
    transform: translateY(-2px);
}

.podium-1 {
    background: linear-gradient(135deg, #ffd700, #ffed4e);
    color: #000;
}

.podium-2 {
    background: linear-gradient(135deg, #c0c0c0, #e8e8e8);
    color: #000;
}

.podium-3 {
    background: linear-gradient(135deg, #cd7f32, #e6a85c);
    color: #000;
}

.reward-item {
    border-left: 4px solid #28a745;
    background-color: #f8f9fa;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    border-radius: 0.375rem;
}

.reward-points { border-left-color: #007bff; }
.reward-badge { border-left-color: #ffc107; }
.reward-cash { border-left-color: #28a745; }
.reward-product { border-left-color: #6f42c1; }
.reward-discount { border-left-color: #fd7e14; }

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.sponsor-status {
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: bold;
}

.status-active {
    background-color: #d4edda;
    color: #155724;
}

.status-inactive {
    background-color: #f8d7da;
    color: #721c24;
}

.status-top-performer {
    background-color: #fff3cd;
    color: #856404;
}

.action-btn {
    padding: 0.25rem 0.5rem;
    margin: 0 0.125rem;
    border-radius: 0.375rem;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .action-btn {
        padding: 0.125rem 0.25rem;
        font-size: 0.75rem;
    }
    
    .sponsor-avatar,
    .sponsor-avatar-placeholder {
        width: 30px;
        height: 30px;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/affiliate-admin.js') }}"></script>
@endpush