/**
 * VintApp - Affiliate Dashboard JavaScript
 * Gère l'interface du système d'affiliation
 */

class AffiliateDashboard {
    constructor() {
        this.currentSection = 'dashboard';
        this.userData = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadDashboard();
    }

    setupEventListeners() {
        // Navigation entre sections
        document.querySelectorAll('[data-section]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const section = e.target.dataset.section;
                this.showSection(section);
            });
        });

        // Formulaire de conversion en argent
        document.getElementById('convertCashForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.convertPointsToCash();
        });

        // Formulaire de génération de code de réduction
        document.getElementById('generateDiscountForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.generateDiscountCode();
        });

        // Formulaire de création de code de parrainage
        document.getElementById('createCodeForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.createReferralCode();
        });

        // Preview de conversion
        document.getElementById('cashPoints')?.addEventListener('input', (e) => {
            this.updateConversionPreview();
        });

        document.getElementById('cashCurrency')?.addEventListener('change', (e) => {
            this.updateConversionPreview();
        });

        // Preview de code de réduction
        document.getElementById('discountPoints')?.addEventListener('input', (e) => {
            this.updateDiscountPreview();
        });

        // Filtres d'historique
        document.getElementById('historyType')?.addEventListener('change', () => {
            this.loadPointsHistory();
        });

        document.getElementById('historyPeriod')?.addEventListener('change', () => {
            this.loadPointsHistory();
        });

        // Boutons d'action
        document.getElementById('shareReferralBtn')?.addEventListener('click', () => {
            this.showShareModal();
        });

        document.getElementById('refreshDataBtn')?.addEventListener('click', () => {
            this.refreshAllData();
        });
    }

    showSection(sectionName) {
        // Masquer toutes les sections
        document.querySelectorAll('.content-section').forEach(section => {
            section.classList.add('d-none');
        });

        // Afficher la section sélectionnée
        document.getElementById(`section-${sectionName}`)?.classList.remove('d-none');

        // Mettre à jour la navigation
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });
        document.querySelector(`[data-section="${sectionName}"]`)?.classList.add('active');

        this.currentSection = sectionName;

        // Charger les données spécifiques à la section
        this.loadSectionData(sectionName);
    }

    loadSectionData(section) {
        switch(section) {
            case 'dashboard':
                this.loadDashboard();
                break;
            case 'points':
                this.loadPointsHistory();
                break;
            case 'referrals':
                this.loadReferrals();
                break;
            case 'codes':
                this.loadReferralCodes();
                break;
            case 'redemptions':
                this.loadRedemptions();
                break;
            case 'leaderboard':
                this.loadLeaderboard();
                break;
        }
    }

    async loadDashboard() {
        try {
            this.showLoader('statsCards');
            
            const response = await fetch('/api/affiliate/dashboard', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            
            if (data.success || data.stats) {
                this.userData = data.data || data;
                this.renderDashboard(data.data || data);
            } else {
                this.showError('Erreur lors du chargement du dashboard');
            }
        } catch (error) {
            console.error('Erreur dashboard:', error);
            this.showError('Erreur de connexion - ' + error.message);
            this.showOfflineMode();
        }
    }

    renderDashboard(data) {
        // Render stats cards
        const statsHtml = `
            <div class="col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="stats-number">${this.formatNumber(data.points.available_points)}</div>
                        <div>Points Disponibles</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <div class="stats-number">${data.stats.referrals.completed}</div>
                        <div>Parrainages Complétés</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <div class="stats-number">${data.points.level}</div>
                        <div>Niveau Actuel</div>
                        <small>${data.points.level_name}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <div class="stats-number">${this.formatCurrency(data.stats.redemptions.total_redeemed_value)}</div>
                        <div>Total Racheté</div>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('statsCards').innerHTML = statsHtml;

        // Render recent transactions
        this.renderRecentTransactions(data.recent_transactions);

        // Render level progress
        this.renderLevelProgress(data.points);
    }

    renderRecentTransactions(transactions) {
        if (!transactions.length) {
            document.getElementById('recentTransactions').innerHTML = 
                '<p class="text-muted">Aucune transaction récente</p>';
            return;
        }

        const transactionsHtml = transactions.map(t => `
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="${t.icon} me-2 ${t.color_class}"></i>
                    <div>
                        <div class="fw-bold">${t.type}</div>
                        <small class="text-muted">${t.description}</small>
                    </div>
                </div>
                <div class="text-end">
                    <div class="${t.color_class} fw-bold">${t.amount}</div>
                    <small class="text-muted">${t.date}</small>
                </div>
            </div>
        `).join('');

        document.getElementById('recentTransactions').innerHTML = transactionsHtml;
    }

    renderLevelProgress(points) {
        const progress = points.level_progress_percentage || 0;
        const nextLevelPoints = points.points_to_next_level;
        
        let progressHtml = `
            <div class="mb-3">
                <h4 class="badge-level">${points.level_name}</h4>
                <div class="h5">Niveau ${points.level}</div>
            </div>
            <div class="progress mb-2" style="height: 10px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: ${progress}%"></div>
            </div>
            <small class="text-muted">
        `;

        if (nextLevelPoints) {
            progressHtml += `${this.formatNumber(nextLevelPoints)} points pour le niveau ${points.level + 1}`;
        } else {
            progressHtml += 'Niveau maximum atteint !';
        }

        progressHtml += '</small>';

        document.getElementById('levelProgress').innerHTML = progressHtml;
    }

    async updateConversionPreview() {
        const points = document.getElementById('cashPoints').value;
        const currency = document.getElementById('cashCurrency').value;
        
        if (!points || points < 100) {
            document.getElementById('conversionPreview').innerHTML = '';
            return;
        }

        try {
            const response = await fetch('/api/affiliate/calculate-conversion', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ points: parseFloat(points), currency })
            });

            const data = await response.json();

            if (data.success) {
                const calc = data.data;
                document.getElementById('conversionPreview').innerHTML = `
                    <div class="alert alert-info">
                        <strong>Aperçu de conversion:</strong><br>
                        Points: ${this.formatNumber(calc.points_used)}<br>
                        Montant de base: ${this.formatCurrency(calc.base_amount)} ${currency}<br>
                        Frais: ${this.formatCurrency(calc.fees)} ${currency}<br>
                        <strong>Montant final: ${this.formatCurrency(calc.final_amount)} ${currency}</strong>
                    </div>
                `;
            } else {
                document.getElementById('conversionPreview').innerHTML = `
                    <div class="alert alert-danger">${data.message}</div>
                `;
            }
        } catch (error) {
            console.error('Erreur preview conversion:', error);
        }
    }

    updateDiscountPreview() {
        const points = document.getElementById('discountPoints').value;
        
        if (!points || points < 100) {
            document.getElementById('discountPreview').innerHTML = '';
            return;
        }

        const discountPercentage = Math.min(points / 100, 50);
        
        document.getElementById('discountPreview').innerHTML = `
            <div class="alert alert-info">
                <strong>Aperçu du code:</strong><br>
                Points utilisés: ${this.formatNumber(points)}<br>
                <strong>Réduction: ${discountPercentage}%</strong><br>
                <small>Maximum 50% de réduction</small>
            </div>
        `;
    }

    async convertPointsToCash() {
        const points = document.getElementById('cashPoints').value;
        const currency = document.getElementById('cashCurrency').value;

        if (!points || !currency) {
            this.showAlert('Veuillez remplir tous les champs', 'warning');
            return;
        }

        try {
            const response = await fetch('/api/affiliate/convert-points', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ points: parseFloat(points), currency })
            });

            const data = await response.json();

            if (data.success) {
                this.showAlert('Demande de conversion créée avec succès !', 'success');
                document.getElementById('convertCashForm').reset();
                document.getElementById('conversionPreview').innerHTML = '';
                this.refreshAllData();
            } else {
                this.showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Erreur conversion:', error);
            this.showAlert('Erreur lors de la conversion', 'error');
        }
    }

    async generateDiscountCode() {
        const points = document.getElementById('discountPoints').value;
        const expiresDays = document.getElementById('discountExpiry').value;

        if (!points) {
            this.showAlert('Veuillez saisir le nombre de points', 'warning');
            return;
        }

        try {
            const response = await fetch('/api/affiliate/convert-points', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ 
                    points: parseFloat(points), 
                    expires_days: parseInt(expiresDays) 
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showAlert(
                    `Code de réduction généré: <strong>${data.data.discount_code}</strong><br>` +
                    `Réduction: ${data.data.discount_percentage}%`, 
                    'success'
                );
                document.getElementById('generateDiscountForm').reset();
                document.getElementById('discountPreview').innerHTML = '';
                this.refreshAllData();
            } else {
                this.showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Erreur génération code:', error);
            this.showAlert('Erreur lors de la génération', 'error');
        }
    }

    async createReferralCode() {
        const formData = {
            title: document.getElementById('codeTitle').value,
            description: document.getElementById('codeDescription').value,
            max_uses: document.getElementById('codeMaxUses').value || null,
            bonus_points: document.getElementById('codeBonusPoints').value || 0
        };

        try {
            const response = await fetch('/api/affiliate/referral-codes', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (data.success) {
                this.showAlert(`Code créé avec succès: <strong>${data.data.code}</strong>`, 'success');
                document.getElementById('createCodeForm').reset();
                this.loadReferralCodes();
            } else {
                this.showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Erreur création code:', error);
            this.showAlert('Erreur lors de la création', 'error');
        }
    }

    async loadPointsHistory() {
        const type = document.getElementById('historyType')?.value || 'all';
        const period = document.getElementById('historyPeriod')?.value || 'all';

        try {
            const response = await fetch(`/affiliate/points/history?type=${type}&period=${period}`);
            const data = await response.json();

            if (data.success) {
                this.renderPointsHistory(data.data.transactions);
            }
        } catch (error) {
            console.error('Erreur historique points:', error);
        }
    }

    renderPointsHistory(transactions) {
        if (!transactions.length) {
            document.getElementById('pointsHistory').innerHTML = 
                '<p class="text-muted">Aucune transaction trouvée</p>';
            return;
        }

        const historyHtml = transactions.map(t => `
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex align-items-center">
                        <i class="${t.icon} me-2 ${t.color_class}"></i>
                        <div>
                            <div class="fw-bold">${t.type_description}</div>
                            <div class="text-muted">${t.description}</div>
                            <small class="text-muted">${t.date}</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="${t.color_class} fw-bold">${t.formatted_amount}</div>
                        <small class="text-muted">Solde: ${t.balance_after}</small>
                    </div>
                </div>
            </div>
        `).join('');

        document.getElementById('pointsHistory').innerHTML = `
            <div class="list-group">${historyHtml}</div>
        `;
    }

    async loadReferrals() {
        try {
            const response = await fetch('/api/affiliate/referrals');
            const data = await response.json();

            if (data.success) {
                this.renderReferrals(data.data);
            }
        } catch (error) {
            console.error('Erreur chargement parrainages:', error);
        }
    }

    renderReferrals(referrals) {
        if (!referrals.length) {
            document.getElementById('referralsList').innerHTML = 
                '<p class="text-muted">Aucun parrainage pour le moment</p>';
            return;
        }

        const referralsHtml = referrals.map(r => `
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">${r.referred_name}</h6>
                        <p class="mb-1">Code utilisé: <code>${r.code_used}</code></p>
                        <small class="text-muted">Date: ${r.created_at}</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-${this.getStatusColor(r.status)}">${r.status}</span>
                        ${r.points_earned > 0 ? `<div class="badge-points mt-1">+${r.points_earned} pts</div>` : ''}
                    </div>
                </div>
            </div>
        `).join('');

        document.getElementById('referralsList').innerHTML = `
            <div class="list-group">${referralsHtml}</div>
        `;
    }

    async loadReferralCodes() {
        try {
            const response = await fetch('/api/affiliate/referral-codes');
            const data = await response.json();

            if (data.success) {
                this.renderReferralCodes(data.data);
            }
        } catch (error) {
            console.error('Erreur chargement codes:', error);
        }
    }

    renderReferralCodes(codes) {
        if (!codes.length) {
            document.getElementById('referralCodesList').innerHTML = 
                '<p class="text-muted">Aucun code de parrainage créé</p>';
            return;
        }

        const codesHtml = codes.map(c => `
            <div class="col-md-6 mb-3">
                <div class="card referral-code-card">
                    <div class="card-body">
                        <h5 class="card-title">${c.title}</h5>
                        <p class="card-text">${c.description || 'Aucune description'}</p>
                        <div class="mb-2">
                            <strong>Code:</strong> <code class="fs-5">${c.code}</code>
                            <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('${c.code}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <div class="mb-2">
                            <strong>Statut:</strong> 
                            <span class="badge bg-${c.is_active ? 'success' : 'secondary'}">${c.status}</span>
                        </div>
                        <div class="row text-center">
                            <div class="col">
                                <small class="text-muted">Utilisations</small><br>
                                <strong>${c.stats.total_uses}${c.stats.max_uses ? '/' + c.stats.max_uses : ''}</strong>
                            </div>
                            <div class="col">
                                <small class="text-muted">Parrainages</small><br>
                                <strong>${c.stats.completed_referrals}</strong>
                            </div>
                            <div class="col">
                                <small class="text-muted">Points gagnés</small><br>
                                <strong>${c.stats.total_points_generated}</strong>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-primary" onclick="shareCode('${c.code}', '${c.share_url}')">
                                <i class="fas fa-share"></i> Partager
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        document.getElementById('referralCodesList').innerHTML = `
            <div class="row">${codesHtml}</div>
        `;
    }

    async loadRedemptions() {
        try {
            const response = await fetch('/api/affiliate/redemptions');
            const data = await response.json();

            if (data.success) {
                this.renderRedemptions(data.data);
            }
        } catch (error) {
            console.error('Erreur chargement rachats:', error);
        }
    }

    renderRedemptions(redemptions) {
        if (!redemptions.length) {
            document.getElementById('redemptionsList').innerHTML = 
                '<p class="text-muted">Aucun rachat pour le moment</p>';
            return;
        }

        const redemptionsHtml = redemptions.map(r => `
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">${r.type} - ${r.redemption_id}</h6>
                        <p class="mb-1">${r.description}</p>
                        <small class="text-muted">Créé le: ${r.created_at}</small>
                        ${r.processed_at ? `<br><small class="text-muted">Traité le: ${r.processed_at}</small>` : ''}
                    </div>
                    <div class="text-end">
                        <div class="badge bg-${this.getStatusColor(r.status)}">${r.status}</div>
                        <div class="mt-1">
                            <strong>${r.points_used}</strong>
                            ${r.cash_amount ? `<br><small>${r.cash_amount}</small>` : ''}
                        </div>
                        ${r.redemption_code ? `<br><code>${r.redemption_code}</code>` : ''}
                    </div>
                </div>
            </div>
        `).join('');

        document.getElementById('redemptionsList').innerHTML = `
            <div class="list-group">${redemptionsHtml}</div>
        `;
    }

    async loadLeaderboard() {
        try {
            const response = await fetch('/api/affiliate/referrals'); // Leaderboard via referrals
            const data = await response.json();

            if (data.success) {
                this.renderLeaderboard(data.data);
            }
        } catch (error) {
            console.error('Erreur chargement classement:', error);
        }
    }

    renderLeaderboard(leaderboard) {
        if (!leaderboard.length) {
            document.getElementById('leaderboardList').innerHTML = 
                '<p class="text-muted">Aucune donnée de classement</p>';
            return;
        }

        const leaderboardHtml = leaderboard.map((user, index) => `
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="badge ${index < 3 ? 'bg-warning text-dark' : 'bg-secondary'} fs-6">
                                #${user.rank}
                            </span>
                        </div>
                        <div class="me-3">
                            ${user.avatar ? 
                                `<img src="${user.avatar}" class="rounded-circle" width="40" height="40" alt="Avatar">` :
                                `<div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" style="width:40px;height:40px;">
                                    ${user.user_name.charAt(0).toUpperCase()}
                                </div>`
                            }
                        </div>
                        <div>
                            <h6 class="mb-0">${user.user_name}</h6>
                            <small class="text-muted">
                                ${user.level_name} (Niveau ${user.level})
                            </small>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold">${this.formatNumber(user.total_points)} pts</div>
                        <small class="text-muted">${user.referrals_count} parrainages</small>
                    </div>
                </div>
            </div>
        `).join('');

        document.getElementById('leaderboardList').innerHTML = `
            <div class="list-group">${leaderboardHtml}</div>
        `;
    }

    showShareModal() {
        if (this.userData && this.userData.user.referral_code) {
            document.getElementById('shareCode').value = this.userData.user.referral_code;
            document.getElementById('shareUrl').value = 
                `${window.location.origin}/register?ref=${this.userData.user.referral_code}`;
            
            const modal = new bootstrap.Modal(document.getElementById('shareModal'));
            modal.show();
        }
    }

    refreshAllData() {
        this.loadSectionData(this.currentSection);
        this.showAlert('Données actualisées', 'success');
    }

    // Utility methods
    formatNumber(num) {
        return new Intl.NumberFormat().format(num);
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', { 
            style: 'currency', 
            currency: 'USD' 
        }).format(amount);
    }

    getStatusColor(status) {
        const colors = {
            'pending': 'warning',
            'En attente': 'warning',
            'active': 'info',
            'Actif': 'info',
            'completed': 'success',
            'Complété': 'success',
            'cancelled': 'danger',
            'Annulé': 'danger',
            'failed': 'danger',
            'Échoué': 'danger'
        };
        return colors[status] || 'secondary';
    }

    showAlert(message, type = 'info') {
        const alertClass = type === 'error' ? 'alert-danger' : `alert-${type}`;
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Insérer l'alerte en haut de la page
        const container = document.querySelector('.container-fluid');
        container.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto-supprimer après 5 secondes
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }

    showLoader(elementId) {
        const element = document.getElementById(elementId);
        if (element) {
            element.innerHTML = `
                <div class="text-center p-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            `;
        }
    }

    showError(message) {
        this.showAlert(message, 'error');
    }
}

// Fonctions globales pour les événements
function copyToClipboard(text) {
    const textToCopy = typeof text === 'string' && text.startsWith('#') ? 
        document.querySelector(text).value : text;
    
    navigator.clipboard.writeText(textToCopy).then(() => {
        // Créer une notification temporaire
        const notification = document.createElement('div');
        notification.className = 'position-fixed top-50 start-50 translate-middle alert alert-success';
        notification.style.zIndex = '9999';
        notification.textContent = 'Copié dans le presse-papiers !';
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 2000);
    });
}

function shareOnFacebook() {
    const url = document.getElementById('shareUrl').value;
    const shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
    window.open(shareUrl, '_blank', 'width=600,height=400');
}

function shareOnTwitter() {
    const code = document.getElementById('shareCode').value;
    const text = `Rejoignez VintApp avec mon code de parrainage ${code} et gagnez des points !`;
    const url = document.getElementById('shareUrl').value;
    const shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`;
    window.open(shareUrl, '_blank', 'width=600,height=400');
}

function shareOnWhatsApp() {
    const code = document.getElementById('shareCode').value;
    const url = document.getElementById('shareUrl').value;
    const text = `🎉 Rejoignez VintApp avec mon code de parrainage *${code}* et gagnez des points ! ${url}`;
    const shareUrl = `https://wa.me/?text=${encodeURIComponent(text)}`;
    window.open(shareUrl, '_blank');
}

function shareCode(code, url) {
    document.getElementById('shareCode').value = code;
    document.getElementById('shareUrl').value = url;
    
    const modal = new bootstrap.Modal(document.getElementById('shareModal'));
    modal.show();
}

// Initialiser le dashboard quand le DOM est prêt
document.addEventListener('DOMContentLoaded', function() {
    window.affiliateDashboard = new AffiliateDashboard();
});