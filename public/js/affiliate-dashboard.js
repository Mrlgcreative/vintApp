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
        this.setupModalEvents();
        this.loadDashboard();
    }

    setupModalEvents() {
        // Close modal on backdrop click
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('fixed') && e.target.classList.contains('inset-0')) {
                const modals = ['createCodeModal', 'shareModal'];
                modals.forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (modal && !modal.classList.contains('hidden')) {
                        this.closeModal(modalId);
                    }
                });
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const modals = ['createCodeModal', 'shareModal'];
                modals.forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (modal && !modal.classList.contains('hidden')) {
                        this.closeModal(modalId);
                    }
                });
            }
        });
    }

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto'; // Restore scrolling
        }
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

        // Nouveaux événements pour les codes de parrainage
        document.getElementById('codeType')?.addEventListener('change', () => {
            this.generateCodeTitle();
            this.updateCodePreview();
        });

        document.getElementById('codeMaxUses')?.addEventListener('input', () => {
            this.updateCodePreview();
        });

        document.getElementById('codeBonusPoints')?.addEventListener('input', () => {
            this.updateCodePreview();
        });

        document.getElementById('codeExpiry')?.addEventListener('change', () => {
            this.updateCodePreview();
        });

        document.getElementById('codeDescription')?.addEventListener('input', () => {
            this.updateCodePreview();
        });

        document.getElementById('codeStatusFilter')?.addEventListener('change', () => {
            this.loadReferralCodes();
        });

        // Boutons d'action
        document.getElementById('shareReferralBtn')?.addEventListener('click', () => {
            this.showShareModal();
        });

        document.getElementById('refreshDataBtn')?.addEventListener('click', () => {
            this.refreshAllData();
        });

        // Événement pour générer le titre automatiquement lors de l'ouverture du modal
        document.getElementById('createCodeModal')?.addEventListener('show.bs.modal', () => {
            this.generateCodeTitle();
            this.updateCodePreview();
        });
    }

    showSection(sectionName) {
        // Masquer toutes les sections
        document.querySelectorAll('.content-section').forEach(section => {
            section.classList.add('hidden');
        });

        // Afficher la section sélectionnée
        document.getElementById(`section-${sectionName}`)?.classList.remove('hidden');

        // Mettre à jour la navigation - retirer les styles actifs
        document.querySelectorAll('[data-section]').forEach(link => {
            link.classList.remove('bg-blue-600', 'text-white');
            link.classList.add('text-gray-700', 'hover:bg-gray-100');
        });
        
        // Ajouter les styles actifs au lien sélectionné
        const activeLink = document.querySelector(`[data-section="${sectionName}"]`);
        if (activeLink) {
            activeLink.classList.remove('text-gray-700', 'hover:bg-gray-100');
            activeLink.classList.add('bg-blue-600', 'text-white');
        }

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
            
            // Essayer de charger les vraies données depuis l'API
            const response = await fetch('/affiliate/dashboard-data', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            
            if (data.success && data.data) {
                this.userData = data.data;
                this.renderDashboard(data.data);
            } else {
                throw new Error('Données invalides reçues');
            }
        } catch (error) {
            console.error('Erreur dashboard:', error);
            
            // Fallback avec données par défaut mais afficher vraiment les points utilisateur
            this.loadUserPointsFromDOM();
        }
    }
    
    loadUserPointsFromDOM() {
        // Essayer de récupérer les points depuis le contexte Laravel (si disponible)
        const userContext = window.user || {};
        
        const data = {
            user: {
                id: userContext.id || 1,
                name: userContext.name || 'Utilisateur',
                referral_code: userContext.referral_code || 'REF001'
            },
            points: {
                available_points: userContext.available_points || 0,
                level: userContext.level || 1,
                level_name: userContext.level_name || 'Bronze',
                level_progress_percentage: userContext.level_progress || 0,
                points_to_next_level: userContext.points_to_next_level || 1000
            },
            stats: {
                referrals: {
                    completed: userContext.referrals_count || 0
                },
                redemptions: {
                    total_redeemed_value: userContext.total_redeemed || 0
                }
            },
            recent_transactions: userContext.recent_transactions || []
        };
        
        this.userData = data;
        this.renderDashboard(data);
    }

    showOfflineMode() {
        document.getElementById('statsCards').innerHTML = `
            <div class="col-12">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Mode hors ligne</strong><br>
                    Impossible de charger les données. Vérifiez votre connexion ou contactez le support.
                </div>
            </div>
        `;
    }

    renderDashboard(data) {
        // Render stats cards avec gestion des valeurs par défaut
        const points = data.points || {};
        const stats = data.stats || {};
        const referrals = stats.referrals || {};
        const redemptions = stats.redemptions || {};
        
        const statsHtml = `
            <div class="col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="stats-number">${this.formatNumber(points.available_points || 0)}</div>
                        <div>Points Disponibles</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <div class="stats-number">${referrals.completed || 0}</div>
                        <div>Parrainages Complétés</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <div class="stats-number">${points.level || 1}</div>
                        <div>Niveau Actuel</div>
                        <small>${points.level_name || 'Bronze'}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <div class="stats-number">${this.formatCurrency(redemptions.total_redeemed_value || 0)}</div>
                        <div>Total Racheté</div>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('statsCards').innerHTML = statsHtml;

        // Render recent transactions
        this.renderRecentTransactions(data.recent_transactions || []);

        // Render level progress
        this.renderLevelProgress(points);
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
            // Calculer la conversion localement pour éviter les erreurs d'API
            const pointsValue = parseFloat(points);
            
            // Taux de conversion par défaut (à ajuster selon votre système)
            const conversionRates = {
                'USD': 0.001,  // 1000 points = 1 USD
                'CDF': 2.5     // 1 point = 2.5 CDF
            };
            
            const rate = conversionRates[currency] || conversionRates['USD'];
            const baseAmount = pointsValue * rate;
            const fees = baseAmount * 0.05; // Frais de 5%
            const finalAmount = baseAmount - fees;

            document.getElementById('conversionPreview').innerHTML = `
                <div class="alert alert-info">
                    <strong>Aperçu de conversion:</strong><br>
                    Points: ${this.formatNumber(pointsValue)}<br>
                    Taux: 1 point = ${rate} ${currency}<br>
                    Montant de base: ${this.formatCurrency(baseAmount, currency)}<br>
                    Frais (5%): ${this.formatCurrency(fees, currency)}<br>
                    <strong>Montant final: ${this.formatCurrency(finalAmount, currency)}</strong>
                </div>
            `;
        } catch (error) {
            console.error('Erreur preview conversion:', error);
            document.getElementById('conversionPreview').innerHTML = `
                <div class="alert alert-warning">
                    Taux de conversion non disponible. Veuillez contacter le support.
                </div>
            `;
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

        if (parseFloat(points) < 100) {
            this.showAlert('Le minimum est de 100 points', 'warning');
            return;
        }

        try {
            const response = await fetch('/affiliate/points/convert-cash', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({ 
                    points: parseFloat(points), 
                    currency: currency
                })
            });

            if (!response.ok) {
                const errorData = await response.text();
                console.error('Erreur HTTP:', response.status, errorData);
                throw new Error(`Erreur ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            if (data.success) {
                this.showAlert('Demande de conversion créée avec succès !', 'success');
                document.getElementById('convertCashForm').reset();
                document.getElementById('conversionPreview').innerHTML = '';
                this.refreshAllData();
            } else {
                this.showAlert(data.message || 'Erreur lors de la conversion', 'error');
            }
        } catch (error) {
            console.error('Erreur conversion:', error);
            
            if (error.message.includes('400')) {
                this.showAlert('Données invalides. Vérifiez que vous avez assez de points.', 'error');
            } else if (error.message.includes('401')) {
                this.showAlert('Session expirée. Veuillez vous reconnecter.', 'error');
            } else if (error.message.includes('422')) {
                this.showAlert('Données de validation incorrectes.', 'error');
            } else {
                this.showAlert('Erreur de connexion. Veuillez réessayer.', 'error');
            }
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

    generateCodeTitle() {
        const codeType = document.getElementById('codeType')?.value || 'general';
        const currentDate = new Date();
        const month = currentDate.toLocaleString('fr-FR', { month: 'short' });
        const year = currentDate.getFullYear();
        
        const typeNames = {
            'general': 'Général',
            'limited': 'Limité',
            'premium': 'Premium',
            'seasonal': 'Saisonnier'
        };
        
        const randomSuffix = Math.floor(Math.random() * 900) + 100;
        const title = `${typeNames[codeType]} ${month} ${year} #${randomSuffix}`;
        
        const titleInput = document.getElementById('codeTitle');
        if (titleInput) {
            titleInput.value = title;
        }
        
        return title;
    }

    updateCodePreview() {
        const codeType = document.getElementById('codeType')?.value || 'general';
        const title = document.getElementById('codeTitle')?.value || 'Code Parrainage';
        const description = document.getElementById('codeDescription')?.value || '';
        const maxUses = document.getElementById('codeMaxUses')?.value;
        const bonusPoints = document.getElementById('codeBonusPoints')?.value || '0';
        const expiry = document.getElementById('codeExpiry')?.value;
        
        // Générer un code d'aperçu
        const codePrefix = codeType.toUpperCase().substr(0, 3);
        const randomCode = Math.floor(Math.random() * 9000) + 1000;
        const previewCode = `${codePrefix}${randomCode}`;
        
        // Mettre à jour l'aperçu
        document.getElementById('previewTitle').textContent = title;
        document.getElementById('previewCode').textContent = previewCode;
        
        // Construire les détails
        const typeLabels = {
            'general': 'Général',
            'limited': 'Limité',
            'premium': 'Premium',
            'seasonal': 'Saisonnier'
        };
        
        let details = typeLabels[codeType];
        details += maxUses ? ` • Max ${maxUses} utilisations` : ' • Illimité';
        
        if (expiry) {
            const expiryLabel = expiry === '7' ? '7 jours' :
                              expiry === '30' ? '1 mois' :
                              expiry === '60' ? '2 mois' :
                              expiry === '90' ? '3 mois' :
                              expiry === '365' ? '1 an' : `${expiry} jours`;
            details += ` • Expire dans ${expiryLabel}`;
        } else {
            details += ' • Permanent';
        }
        
        if (bonusPoints && bonusPoints !== '0') {
            details += ` • +${bonusPoints} pts bonus`;
        }
        
        document.getElementById('previewDetails').textContent = details;
        
        // Ajouter une classe de type pour le style
        const previewCard = document.querySelector('.code-display');
        if (previewCard) {
            previewCard.className = `code-display p-3 border rounded bg-white code-type-${codeType}`;
        }
    }

    async createReferralCode() {
        const formData = {
            title: document.getElementById('codeTitle').value,
            description: document.getElementById('codeDescription').value,
            type: document.getElementById('codeType').value,
            max_uses: document.getElementById('codeMaxUses').value || null,
            bonus_points: document.getElementById('codeBonusPoints').value || 0,
            expires_days: document.getElementById('codeExpiry').value || null,
            status: document.getElementById('codeStatus').value
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
                
                // Fermer le modal avec Tailwind
                this.closeModal('createCodeModal');
                
                // Recharger les codes et les statistiques
                this.loadReferralCodes();
                this.updateCodesStats();
            } else {
                this.showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Erreur création code:', error);
            this.showAlert('Erreur lors de la création', 'error');
        }
    }

    async updateCodesStats() {
        try {
            const response = await fetch('/api/affiliate/codes/stats');
            const data = await response.json();

            if (data.success) {
                const stats = data.data;
                
                document.getElementById('totalCodes').textContent = stats.total_codes || 0;
                document.getElementById('activeCodes').textContent = stats.active_codes || 0;
                document.getElementById('totalUses').textContent = stats.total_uses || 0;
                document.getElementById('bestPerforming').textContent = stats.best_performing || '-';
            }
        } catch (error) {
            console.error('Erreur stats codes:', error);
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
        const statusFilter = document.getElementById('codeStatusFilter')?.value || 'all';
        
        try {
            const url = statusFilter === 'all' ? '/api/affiliate/referral-codes' : 
                       `/api/affiliate/referral-codes?status=${statusFilter}`;
            
            const response = await fetch(url);
            const data = await response.json();

            if (data.success) {
                this.renderReferralCodes(data.data);
                this.updateCodesStats();
            }
        } catch (error) {
            console.error('Erreur chargement codes:', error);
        }
    }

    renderReferralCodes(codes) {
        if (!codes.length) {
            document.getElementById('referralCodesList').innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-qr-code fa-3x text-gray-400 mb-3"></i>
                        <h5 class="text-gray-600">Aucun code de parrainage</h5>
                        <p class="text-gray-500">Créez votre premier code de parrainage pour commencer à inviter vos amis !</p>
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors" onclick="window.affiliateDashboard.openModal('createCodeModal')">
                            <i class="fas fa-plus mr-2"></i> Créer mon premier code
                        </button>
                    </div>
            `;
            return;
        }

        const codesHtml = codes.map(c => {
            const typeColor = {
                'general': 'primary',
                'limited': 'warning',
                'premium': 'info',
                'seasonal': 'success'
            }[c.type] || 'secondary';

            const statusColor = {
                'active': 'success',
                'inactive': 'secondary',
                'expired': 'danger'
            }[c.status] || 'secondary';

            return `
                <div class="card code-card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h5 class="card-title mb-1">${c.title}</h5>
                                <span class="badge bg-${typeColor} me-2">${c.type.charAt(0).toUpperCase() + c.type.slice(1)}</span>
                                <span class="badge bg-${statusColor}">${c.status.charAt(0).toUpperCase() + c.status.slice(1)}</span>
                            </div>
                            <div class="code-actions">
                                <button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('${c.code}')" title="Copier le code">
                                    <i class="fas fa-copy"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" onclick="shareCode('${c.code}', '${c.share_url}')" title="Partager">
                                    <i class="fas fa-share-alt"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="editCode('${c.id}')" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <div class="d-flex align-items-center mb-1">
                                <strong>Code:</strong>
                                <code class="fs-5 ms-2 me-2">${c.code}</code>
                            </div>
                            ${c.description ? `<p class="text-muted mb-2">${c.description}</p>` : ''}
                        </div>
                        
                        <div class="code-stats mb-2">
                            <div class="code-stat">
                                <i class="fas fa-users text-primary"></i>
                                <strong>${c.stats.total_uses}</strong>${c.stats.max_uses ? `/${c.stats.max_uses}` : ''} utilisations
                            </div>
                            <div class="code-stat">
                                <i class="fas fa-handshake text-success"></i>
                                <strong>${c.stats.completed_referrals}</strong> parrainages
                            </div>
                            <div class="code-stat">
                                <i class="fas fa-star text-warning"></i>
                                <strong>${c.stats.total_points_generated}</strong> points générés
                            </div>
                        </div>
                        
                        ${c.bonus_points > 0 ? `
                            <div class="mb-2">
                                <small class="text-success">
                                    <i class="fas fa-gift"></i> +${c.bonus_points} points bonus pour les filleuls
                                </small>
                            </div>
                        ` : ''}
                        
                        ${c.expires_at ? `
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> Expire le ${new Date(c.expires_at).toLocaleDateString('fr-FR')}
                                </small>
                            </div>
                        ` : ''}
                        
                        <div class="mt-3">
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-${typeColor}" 
                                     style="width: ${c.stats.max_uses ? (c.stats.total_uses / c.stats.max_uses * 100) : 100}%">
                                </div>
                            </div>
                            <small class="text-muted">
                                ${c.stats.max_uses ? `${Math.round(c.stats.total_uses / c.stats.max_uses * 100)}% utilisé` : 'Utilisations illimitées'}
                            </small>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        document.getElementById('referralCodesList').innerHTML = codesHtml;
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
            
            this.openModal('shareModal');
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

    formatCurrency(amount, currency = 'USD') {
        try {
            if (currency === 'CDF') {
                return new Intl.NumberFormat('fr-CD', { 
                    style: 'currency', 
                    currency: 'CDF',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(amount);
            } else {
                return new Intl.NumberFormat('en-US', { 
                    style: 'currency', 
                    currency: currency || 'USD' 
                }).format(amount);
            }
        } catch (error) {
            // Fallback si la devise n'est pas supportée
            return `${amount.toFixed(2)} ${currency}`;
        }
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
        const alertClass = type === 'error' ? 'bg-red-100 border-red-500 text-red-700' : 
                          type === 'success' ? 'bg-green-100 border-green-500 text-green-700' :
                          type === 'warning' ? 'bg-yellow-100 border-yellow-500 text-yellow-700' :
                          'bg-blue-100 border-blue-500 text-blue-700';
        
        const alertHtml = `
            <div class="fixed top-4 right-4 z-50 ${alertClass} border-l-4 p-4 rounded shadow-lg max-w-md" role="alert">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        ${message}
                    </div>
                    <button type="button" class="ml-4 text-current hover:opacity-75" onclick="this.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        
        // Insérer l'alerte en haut de la page
        document.body.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto-supprimer après 5 secondes
        setTimeout(() => {
            const alert = document.querySelector('.fixed.top-4');
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
function openModal(modalId) {
    console.log('openModal appelée avec:', modalId);
    if (window.affiliateDashboard) {
        window.affiliateDashboard.openModal(modalId);
    } else {
        // Fallback si l'instance n'est pas encore créée
        console.log('Instance affiliateDashboard non trouvée, utilisation du fallback');
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }
}

function closeModal(modalId) {
    console.log('closeModal appelée avec:', modalId);
    if (window.affiliateDashboard) {
        window.affiliateDashboard.closeModal(modalId);
    } else {
        // Fallback si l'instance n'est pas encore créée
        console.log('Instance affiliateDashboard non trouvée, utilisation du fallback');
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }
}

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
    
    if (window.affiliateDashboard) {
        window.affiliateDashboard.openModal('shareModal');
    }
}

function generateCodeTitle() {
    console.log('generateCodeTitle appelée');
    if (window.affiliateDashboard) {
        window.affiliateDashboard.generateCodeTitle();
    } else {
        // Fallback simple
        const codeType = document.getElementById('codeType')?.value || 'general';
        const currentDate = new Date();
        const month = currentDate.toLocaleString('fr-FR', { month: 'short' });
        const year = currentDate.getFullYear();
        
        const typeNames = {
            'general': 'Général',
            'limited': 'Limité', 
            'premium': 'Premium',
            'seasonal': 'Saisonnier'
        };
        
        const randomSuffix = Math.floor(Math.random() * 900) + 100;
        const title = `${typeNames[codeType]} ${month} ${year} #${randomSuffix}`;
        
        const titleInput = document.getElementById('codeTitle');
        if (titleInput) {
            titleInput.value = title;
        }
    }
}

function updateCodePreview() {
    console.log('updateCodePreview appelée');
    if (window.affiliateDashboard) {
        window.affiliateDashboard.updateCodePreview();
    } else {
        // Fallback simple
        const titleInput = document.getElementById('codeTitle');
        const typeSelect = document.getElementById('codeType');
        const maxUsesInput = document.getElementById('codeMaxUses');
        const expirySelect = document.getElementById('codeExpiry');
        
        const title = titleInput?.value || 'Code Parrainage #001';
        const type = typeSelect?.value || 'general';
        const maxUses = maxUsesInput?.value || '';
        const expiry = expirySelect?.value || '';
        
        // Generate code based on title
        const code = title.replace(/[^A-Z0-9]/g, '').substring(0, 10) || 'PARRAINS001';
        
        // Type display
        const typeLabels = {
            'general': 'Général',
            'limited': 'Limité',
            'premium': 'Premium',
            'seasonal': 'Saisonnier'
        };
        
        // Usage display
        const usageText = maxUses ? `Max ${maxUses} utilisations` : 'Illimité';
        
        // Expiry display
        const expiryText = expiry ? `${expiry} jours` : 'Permanent';
        
        // Update preview elements
        const previewTitle = document.getElementById('previewTitle');
        const previewCode = document.getElementById('previewCode');
        const previewDetails = document.getElementById('previewDetails');
        
        if (previewTitle) previewTitle.textContent = title;
        if (previewCode) previewCode.textContent = code;
        if (previewDetails) previewDetails.textContent = `${typeLabels[type]} • ${usageText} • ${expiryText}`;
    }
}

function refreshCodesList() {
    if (window.affiliateDashboard) {
        window.affiliateDashboard.loadReferralCodes();
    }
}

function editCode(codeId) {
    // TODO: Implémenter la fonction d'édition
    console.log('Éditer le code:', codeId);
    window.affiliateDashboard.showAlert('Fonction d\'édition à venir', 'info');
}

// Initialiser le dashboard quand le DOM est prêt
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initialisation du dashboard d\'affiliation...');
    window.affiliateDashboard = new AffiliateDashboard();
    console.log('Dashboard d\'affiliation initialisé:', window.affiliateDashboard);
});