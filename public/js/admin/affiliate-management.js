/**
 * VintApp - Admin Affiliate Management JavaScript
 * Gère l'interface d'administration du système d'affiliation
 */

class AffiliateAdminManager {
    constructor() {
        this.currentPage = 1;
        this.perPage = 10;
        this.filters = {
            search: '',
            minLevel: '',
            minReferrals: '',
            timePeriod: 'all',
            status: 'all'
        };
        this.selectedReferrers = new Set();
        this.bulkMode = false;
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadDashboardStats();
        this.loadTopPerformers();
        this.loadReferrersTable();
        this.loadRecentActivity();
        this.loadLevelChart();
        this.loadReferrerOptions();
    }

    setupEventListeners() {
        // Recherche en temps réel
        let searchTimeout;
        document.getElementById('searchReferrer')?.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.filters.search = e.target.value;
                this.loadReferrersTable();
            }, 500);
        });

        // Formulaire de récompense
        document.getElementById('rewardForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitReward();
        });

        // Événements de sélection multiple
        document.getElementById('selectAll')?.addEventListener('change', (e) => {
            this.toggleSelectAll();
        });

        // Event listener pour le modal de récompense
        const rewardModal = document.getElementById('rewardModal');
        if (rewardModal) {
            rewardModal.addEventListener('show.bs.modal', () => {
                // Recharger les options de parrainage quand le modal s'ouvre
                this.loadReferrerOptions();
            });
        }
    }

    async loadDashboardStats() {
        try {
            const response = await fetch('/admin/api/affiliate/stats', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.updateStatsCards(data.data);
            }
        } catch (error) {
            console.error('Erreur chargement stats:', error);
            this.showAlert('Erreur lors du chargement des statistiques', 'error');
        }
    }

    updateStatsCards(stats) {
        document.getElementById('totalReferrers').textContent = this.formatNumber(stats.total_referrers);
        document.getElementById('activeReferrals').textContent = this.formatNumber(stats.active_referrals);
        document.getElementById('totalPoints').textContent = this.formatNumber(stats.total_points);
        document.getElementById('totalRewards').textContent = this.formatNumber(stats.total_rewards);
    }

    async loadTopPerformers() {
        try {
            const response = await fetch('/admin/api/affiliate/top-performers', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.renderTopPerformers(data.data);
            }
        } catch (error) {
            console.error('Erreur chargement top performers:', error);
        }
    }

    renderTopPerformers(performers) {
        const container = document.getElementById('topPerformersList');
        
        if (!performers.length) {
            container.innerHTML = '<p class="text-muted">Aucun top performer pour le moment</p>';
            return;
        }

        const performersHtml = performers.map((performer, index) => {
            const rankClass = index === 0 ? 'gold' : index === 1 ? 'silver' : index === 2 ? 'bronze' : '';
            const rankIcon = index === 0 ? '👑' : index === 1 ? '🥈' : index === 2 ? '🥉' : `#${index + 1}`;
            
            return `
                <div class="card top-performer-card ${rankClass}">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-1 text-center">
                                <span class="h4 mb-0">${rankIcon}</span>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    ${performer.avatar ? 
                                        `<img src="${performer.avatar}" class="rounded-circle me-2" width="40" height="40" alt="Avatar">` :
                                        `<div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white me-2" style="width:40px;height:40px;">
                                            ${performer.name.charAt(0).toUpperCase()}
                                        </div>`
                                    }
                                    <div>
                                        <h6 class="mb-0">${performer.name}</h6>
                                        <small class="text-muted">${performer.email}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-center">
                                    <span class="level-badge level-${performer.level}">
                                        <i class="fas fa-star"></i> Niveau ${performer.level}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-2 text-center">
                                <div class="h5 mb-0 text-success">${performer.referrals_count}</div>
                                <small class="text-muted">Parrainages</small>
                            </div>
                            <div class="col-md-2 text-center">
                                <div class="h5 mb-0 text-info">${this.formatNumber(performer.total_points)}</div>
                                <small class="text-muted">Points</small>
                            </div>
                            <div class="col-md-2 text-right">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewReferrerDetails('${performer.id}')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-success" onclick="rewardReferrer('${performer.id}')">
                                        <i class="fas fa-gift"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Performance Meter -->
                        <div class="mt-2">
                            <div class="performance-meter">
                                <div class="fill performance-${this.getPerformanceLevel(performer.performance_score)}" 
                                     style="width: ${performer.performance_score}%"></div>
                            </div>
                            <small class="text-muted">Performance: ${performer.performance_score}%</small>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = performersHtml;
    }

    async loadReferrersTable(page = 1) {
        this.currentPage = page;
        
        try {
            const params = new URLSearchParams({
                page: page,
                per_page: this.perPage,
                ...this.filters
            });

            const response = await fetch(`/admin/api/affiliate/referrers?${params}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.renderReferrersTable(data.data.referrers);
                this.updatePagination(data.data.pagination);
            }
        } catch (error) {
            console.error('Erreur chargement table:', error);
        }
    }

    renderReferrersTable(referrers) {
        const tbody = document.getElementById('referrersTableBody');
        
        if (!referrers.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                        <i class="fas fa-users fa-2x mb-2"></i><br>
                        Aucun parrain trouvé avec ces critères
                    </td>
                </tr>
            `;
            return;
        }

        const referrersHtml = referrers.map((referrer, index) => {
            const globalRank = ((this.currentPage - 1) * this.perPage) + index + 1;
            const statusColor = {
                'active': 'success',
                'inactive': 'secondary',
                'suspended': 'danger',
                'top_performer': 'warning'
            }[referrer.status] || 'secondary';

            return `
                <tr>
                    <td>
                        <input type="checkbox" class="referrer-checkbox" value="${referrer.id}" 
                               onchange="toggleReferrerSelection('${referrer.id}')">
                    </td>
                    <td>
                        <span class="badge badge-light">#${globalRank}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            ${referrer.avatar ? 
                                `<img src="${referrer.avatar}" class="rounded-circle me-2" width="32" height="32" alt="Avatar">` :
                                `<div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white me-2" style="width:32px;height:32px; font-size:0.75rem;">
                                    ${referrer.name.charAt(0).toUpperCase()}
                                </div>`
                            }
                            <div>
                                <div class="fw-bold">${referrer.name}</div>
                                <small class="text-muted">${referrer.email}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="level-badge level-${referrer.level}">
                            <i class="fas fa-star"></i> ${referrer.level}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="fw-bold text-success">${referrer.referrals_count}</span>
                        ${referrer.referrals_this_month > 0 ? `<br><small class="text-muted">+${referrer.referrals_this_month} ce mois</small>` : ''}
                    </td>
                    <td class="text-center">
                        <span class="fw-bold text-info">${this.formatNumber(referrer.total_points)}</span>
                        <br><small class="text-muted">Disponible: ${this.formatNumber(referrer.available_points)}</small>
                    </td>
                    <td>
                        <div>${this.formatDate(referrer.last_activity)}</div>
                        <small class="text-muted">${this.getTimeAgo(referrer.last_activity)}</small>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-info">${referrer.rewards_count}</span>
                        ${referrer.last_reward ? `<br><small class="text-muted">Dernier: ${this.formatDate(referrer.last_reward)}</small>` : ''}
                    </td>
                    <td>
                        <span class="badge bg-${statusColor}">${this.getStatusLabel(referrer.status)}</span>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-outline-primary" onclick="viewReferrerDetails('${referrer.id}')" title="Voir détails">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-success" onclick="rewardReferrer('${referrer.id}')" title="Récompenser">
                                <i class="fas fa-gift"></i>
                            </button>
                            <button class="btn btn-sm btn-info" onclick="sendMessage('${referrer.id}')" title="Message">
                                <i class="fas fa-envelope"></i>
                            </button>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="promoteReferrer('${referrer.id}')">
                                        <i class="fas fa-arrow-up"></i> Promouvoir
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="exportReferrerData('${referrer.id}')">
                                        <i class="fas fa-download"></i> Exporter données
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-warning" href="#" onclick="suspendReferrer('${referrer.id}')">
                                        <i class="fas fa-pause"></i> Suspendre
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        tbody.innerHTML = referrersHtml;
    }

    updatePagination(pagination) {
        document.getElementById('showingFrom').textContent = pagination.from || 0;
        document.getElementById('showingTo').textContent = pagination.to || 0;
        document.getElementById('totalReferrers').textContent = pagination.total || 0;

        const paginationContainer = document.getElementById('pagination');
        let paginationHtml = '';

        // Page précédente
        if (pagination.current_page > 1) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadReferrersTable(${pagination.current_page - 1})">Précédent</a>
                </li>
            `;
        }

        // Pages numériques
        const startPage = Math.max(1, pagination.current_page - 2);
        const endPage = Math.min(pagination.last_page, pagination.current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `
                <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="loadReferrersTable(${i})">${i}</a>
                </li>
            `;
        }

        // Page suivante
        if (pagination.current_page < pagination.last_page) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadReferrersTable(${pagination.current_page + 1})">Suivant</a>
                </li>
            `;
        }

        paginationContainer.innerHTML = paginationHtml;
    }

    async loadRecentActivity() {
        try {
            const response = await fetch('/admin/api/affiliate/recent-activity', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.renderRecentActivity(data.data);
            }
        } catch (error) {
            console.error('Erreur chargement activités:', error);
        }
    }

    renderRecentActivity(activities) {
        const container = document.getElementById('recentActivity');
        
        if (!activities.length) {
            container.innerHTML = '<p class="text-muted">Aucune activité récente</p>';
            return;
        }

        const activitiesHtml = activities.map(activity => `
            <div class="activity-item ${activity.type}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>${activity.title}</strong>
                        <p class="mb-1 text-muted">${activity.description}</p>
                        <small class="text-muted">
                            <i class="fas fa-user"></i> ${activity.user_name} • 
                            <i class="fas fa-clock"></i> ${this.getTimeAgo(activity.created_at)}
                        </small>
                    </div>
                    <div class="text-right">
                        ${activity.amount ? `<span class="badge bg-${activity.type}">${activity.amount}</span>` : ''}
                    </div>
                </div>
            </div>
        `).join('');

        container.innerHTML = activitiesHtml;
    }

    loadLevelChart() {
        const ctx = document.getElementById('levelChart')?.getContext('2d');
        if (!ctx) return;

        // Données simulées - à remplacer par de vraies données API
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Niveau 1', 'Niveau 2', 'Niveau 3', 'Niveau 4', 'Niveau 5+'],
                datasets: [{
                    data: [45, 25, 15, 10, 5],
                    backgroundColor: [
                        '#4e73df',
                        '#1cc88a',
                        '#36b9cc',
                        '#f6c23e',
                        '#e74a3b'
                    ]
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    async loadReferrerOptions() {
        try {
            // Temporairement, utilisons les données des top performers ou créons une requête simple
            const response = await fetch('/admin/api/affiliate/referrers?simple=true', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();
            
            if (data.success) {
                const select = document.getElementById('selectedReferrer');
                if (select) {
                    let optionsHtml = '';
                    
                    // Si nous avons des données de référents
                    if (data.data && data.data.referrers) {
                        optionsHtml = data.data.referrers.map(ref => 
                            `<option value="${ref.id}">${ref.name} (${ref.referrals_count || 0} parrainages)</option>`
                        ).join('');
                    } else if (data.data && Array.isArray(data.data)) {
                        optionsHtml = data.data.map(ref => 
                            `<option value="${ref.id}">${ref.name} (${ref.referrals_count || 0} parrainages)</option>`
                        ).join('');
                    }
                    
                    select.innerHTML = '<option value="">Choisir un parrain...</option>' + optionsHtml;
                }
            }
        } catch (error) {
            console.error('Erreur chargement options:', error);
            // En cas d'erreur, créer des options de test
            this.loadTestReferrerOptions();
        }
    }

    // Méthode de fallback pour charger des options de test
    loadTestReferrerOptions() {
        const select = document.getElementById('selectedReferrer');
        if (select) {
            // Options basées sur nos données de test créées
            const testOptions = `
                <option value="">Choisir un parrain...</option>
                <option value="1">Gloire Lumingu (3 parrainages)</option>
                <option value="7">sky board (2 parrainages)</option>
                <option value="8">Spy (1 parrainage)</option>
            `;
            select.innerHTML = testOptions;
        }
    }

    // Gestion des récompenses
    updateRewardFields() {
        const rewardType = document.getElementById('rewardType').value;
        const detailsSection = document.getElementById('rewardDetailsSection');
        
        // Masquer toutes les sections
        document.querySelectorAll('.reward-section').forEach(section => {
            section.style.display = 'none';
        });

        if (rewardType) {
            detailsSection.style.display = 'block';
            
            const sectionMap = {
                'points': 'pointsSection',
                'cash': 'cashSection',
                'badge': 'badgeSection',
                'level_boost': 'levelSection',
                'custom': 'customSection'
            };

            const targetSection = document.getElementById(sectionMap[rewardType]);
            if (targetSection) {
                targetSection.style.display = 'block';
            }
        } else {
            detailsSection.style.display = 'none';
        }
    }

    async submitReward() {
        const formData = this.collectRewardData();
        
        if (!this.validateRewardData(formData)) {
            return;
        }

        try {
            const response = await fetch('/admin/api/affiliate/rewards', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();
            
            if (data.success) {
                this.showAlert('Récompense attribuée avec succès !', 'success');
                
                // Fermer le modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('rewardModal'));
                if (modal) {
                    modal.hide();
                }
                
                // Réinitialiser le formulaire
                document.getElementById('rewardForm').reset();
                
                // Recharger les données
                this.loadDashboardStats();
                this.loadTopPerformers();
                this.loadReferrersTable();
                this.loadRecentActivity();
            } else {
                this.showAlert(data.message || 'Erreur lors de l\'attribution de la récompense', 'error');
            }
        } catch (error) {
            console.error('Erreur soumission récompense:', error);
            this.showAlert('Erreur de connexion', 'error');
        }
    }

    collectRewardData() {
        const rewardType = document.getElementById('rewardType').value;
        
        const baseData = {
            referrer_id: document.getElementById('selectedReferrer').value,
            type: rewardType,
            reason: document.getElementById('rewardReason').value,
            send_notification: document.getElementById('sendNotification').checked,
            make_public: document.getElementById('makePublic').checked
        };

        // Ajouter les données spécifiques selon le type
        switch (rewardType) {
            case 'points':
                baseData.points = document.getElementById('bonusPoints').value;
                baseData.multiplier = document.getElementById('pointsMultiplier').value;
                break;
            case 'cash':
                baseData.amount = document.getElementById('cashAmount').value;
                baseData.currency = document.getElementById('cashCurrency').value;
                break;
            case 'badge':
                baseData.badge_name = document.getElementById('badgeName').value;
                baseData.duration = document.getElementById('badgeDuration').value;
                break;
            case 'level_boost':
                baseData.level_boost = document.getElementById('levelBoost').value;
                baseData.boost_type = document.getElementById('boostType').value;
                break;
            case 'custom':
                baseData.description = document.getElementById('customRewardDescription').value;
                break;
        }

        return baseData;
    }

    validateRewardData(data) {
        if (!data.referrer_id) {
            this.showAlert('Veuillez sélectionner un parrain', 'warning');
            return false;
        }

        if (!data.type) {
            this.showAlert('Veuillez choisir un type de récompense', 'warning');
            return false;
        }

        // Validations spécifiques selon le type
        switch (data.type) {
            case 'points':
                if (!data.points || data.points < 1) {
                    this.showAlert('Veuillez saisir un nombre de points valide', 'warning');
                    return false;
                }
                break;
            case 'cash':
                if (!data.amount || data.amount < 0.01) {
                    this.showAlert('Veuillez saisir un montant valide', 'warning');
                    return false;
                }
                break;
            case 'custom':
                if (!data.description.trim()) {
                    this.showAlert('Veuillez décrire la récompense personnalisée', 'warning');
                    return false;
                }
                break;
        }

        return true;
    }

    // Filtres et recherche
    applyFilters() {
        this.filters.minLevel = document.getElementById('minLevel').value;
        this.filters.minReferrals = document.getElementById('minReferrals').value;
        this.filters.timePeriod = document.getElementById('timePeriod').value;
        this.filters.status = document.getElementById('statusFilter').value;
        
        this.loadReferrersTable(1);
    }

    resetFilters() {
        document.getElementById('searchReferrer').value = '';
        document.getElementById('minLevel').value = '';
        document.getElementById('minReferrals').value = '';
        document.getElementById('timePeriod').value = 'all';
        document.getElementById('statusFilter').value = 'all';
        
        this.filters = {
            search: '',
            minLevel: '',
            minReferrals: '',
            timePeriod: 'all',
            status: 'all'
        };
        
        this.loadReferrersTable(1);
    }

    searchReferrers() {
        this.filters.search = document.getElementById('searchReferrer').value;
        this.loadReferrersTable(1);
    }

    // Sélection multiple
    toggleBulkSelect() {
        this.bulkMode = !this.bulkMode;
        const bulkActions = document.querySelector('.bulk-actions');
        
        if (this.bulkMode) {
            bulkActions.style.display = 'block';
        } else {
            bulkActions.style.display = 'none';
            this.selectedReferrers.clear();
            document.querySelectorAll('.referrer-checkbox').forEach(cb => cb.checked = false);
        }
    }

    toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.referrer-checkbox');
        
        checkboxes.forEach(cb => {
            cb.checked = selectAll.checked;
            if (selectAll.checked) {
                this.selectedReferrers.add(cb.value);
            } else {
                this.selectedReferrers.delete(cb.value);
            }
        });
    }

    toggleReferrerSelection(referrerId) {
        if (this.selectedReferrers.has(referrerId)) {
            this.selectedReferrers.delete(referrerId);
        } else {
            this.selectedReferrers.add(referrerId);
        }
    }

    // Actions en lot
    async bulkRewardSelected() {
        if (this.selectedReferrers.size === 0) {
            this.showAlert('Veuillez sélectionner au moins un parrain', 'warning');
            return;
        }

        const confirmed = confirm(`Attribuer une récompense à ${this.selectedReferrers.size} parrain(s) sélectionné(s) ?`);
        if (!confirmed) return;

        // Ouvrir le modal de récompense avec sélection multiple
        document.getElementById('selectedReferrer').value = 'bulk';
        const modal = new bootstrap.Modal(document.getElementById('rewardModal'));
        modal.show();
    }

    // Utilitaires
    formatNumber(num) {
        return new Intl.NumberFormat().format(num);
    }

    formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('fr-FR');
    }

    getTimeAgo(dateString) {
        const now = new Date();
        const date = new Date(dateString);
        const diffInSeconds = Math.floor((now - date) / 1000);

        if (diffInSeconds < 60) return 'Il y a quelques secondes';
        if (diffInSeconds < 3600) return `Il y a ${Math.floor(diffInSeconds / 60)} min`;
        if (diffInSeconds < 86400) return `Il y a ${Math.floor(diffInSeconds / 3600)}h`;
        return `Il y a ${Math.floor(diffInSeconds / 86400)} jour(s)`;
    }

    getPerformanceLevel(score) {
        if (score >= 80) return 'excellent';
        if (score >= 60) return 'good';
        if (score >= 40) return 'average';
        return 'poor';
    }

    getStatusLabel(status) {
        const labels = {
            'active': 'Actif',
            'inactive': 'Inactif',
            'suspended': 'Suspendu',
            'top_performer': 'Top Performer'
        };
        return labels[status] || status;
    }

    showAlert(message, type = 'info') {
        const alertClass = type === 'error' ? 'alert-danger' : `alert-${type}`;
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        const container = document.querySelector('.container-fluid');
        container.insertAdjacentHTML('afterbegin', alertHtml);
        
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) alert.remove();
        }, 5000);
    }

    async refreshData() {
        this.showAlert('Actualisation des données...', 'info');
        await Promise.all([
            this.loadDashboardStats(),
            this.loadTopPerformers(),
            this.loadReferrersTable(),
            this.loadRecentActivity()
        ]);
        this.showAlert('Données actualisées', 'success');
    }
}

// Fonctions globales pour les événements
function applyFilters() {
    if (window.affiliateAdmin) {
        window.affiliateAdmin.applyFilters();
    }
}

function resetFilters() {
    if (window.affiliateAdmin) {
        window.affiliateAdmin.resetFilters();
    }
}

function searchReferrers() {
    if (window.affiliateAdmin) {
        window.affiliateAdmin.searchReferrers();
    }
}

function updateRewardFields() {
    if (window.affiliateAdmin) {
        window.affiliateAdmin.updateRewardFields();
    }
}

function submitReward() {
    if (window.affiliateAdmin) {
        window.affiliateAdmin.submitReward();
    }
}

function toggleSelectAll() {
    if (window.affiliateAdmin) {
        window.affiliateAdmin.toggleSelectAll();
    }
}

function toggleReferrerSelection(referrerId) {
    if (window.affiliateAdmin) {
        window.affiliateAdmin.toggleReferrerSelection(referrerId);
    }
}

function toggleBulkSelect() {
    if (window.affiliateAdmin) {
        window.affiliateAdmin.toggleBulkSelect();
    }
}

function bulkRewardSelected() {
    if (window.affiliateAdmin) {
        window.affiliateAdmin.bulkRewardSelected();
    }
}

function loadReferrersTable(page) {
    if (window.affiliateAdmin) {
        window.affiliateAdmin.loadReferrersTable(page);
    }
}

function refreshData() {
    if (window.affiliateAdmin) {
        window.affiliateAdmin.refreshData();
    }
}

// Actions spécifiques
function viewReferrerDetails(referrerId) {
    // TODO: Implémenter la vue détaillée
    console.log('Voir détails parrain:', referrerId);
}

function rewardReferrer(referrerId) {
    document.getElementById('selectedReferrer').value = referrerId;
    const modal = new bootstrap.Modal(document.getElementById('rewardModal'));
    modal.show();
}

function sendMessage(referrerId) {
    // TODO: Implémenter l'envoi de message
    console.log('Envoyer message à:', referrerId);
}

function promoteReferrer(referrerId) {
    // TODO: Implémenter la promotion
    console.log('Promouvoir parrain:', referrerId);
}

function suspendReferrer(referrerId) {
    const confirmed = confirm('Êtes-vous sûr de vouloir suspendre ce parrain ?');
    if (confirmed) {
        // TODO: Implémenter la suspension
        console.log('Suspendre parrain:', referrerId);
    }
}

function exportReferrerData(referrerId) {
    // TODO: Implémenter l'export
    console.log('Exporter données parrain:', referrerId);
}

function exportReport() {
    // TODO: Implémenter l'export du rapport
    console.log('Exporter rapport complet');
}

function exportTopPerformers() {
    // TODO: Implémenter l'export des top performers
    console.log('Exporter top performers');
}

function rewardAllTopPerformers() {
    const confirmed = confirm('Attribuer une récompense à tous les top performers ?');
    if (confirmed) {
        // TODO: Implémenter la récompense en masse
        console.log('Récompenser tous les top performers');
    }
}

// Initialiser quand le DOM est prêt
document.addEventListener('DOMContentLoaded', function() {
    window.affiliateAdmin = new AffiliateAdminManager();
});