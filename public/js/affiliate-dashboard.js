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

    /**
     * Helper pour gérer les réponses fetch avec validation JSON
     */
    async fetchJSON(url, options = {}) {
        try {
            // Ajouter les headers nécessaires par défaut
            const defaultHeaders = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };

            // Fusionner avec les headers existants
            options.headers = {
                ...defaultHeaders,
                ...options.headers
            };

            const response = await fetch(url, options);

            // Vérifier le statut HTTP
            if (!response.ok) {
                if (response.status === 401 || response.status === 403) {
                    this.showAlert('Session expirée. Veuillez vous reconnecter.', 'error');
                    setTimeout(() => window.location.href = '/login', 2000);
                    throw new Error('Session expirée');
                }
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // Vérifier le Content-Type
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Réponse non-JSON reçue:', text.substring(0, 200));
                throw new Error('La réponse n\'est pas au format JSON. Vérifiez que vous êtes authentifié.');
            }

            // Parser le JSON
            return await response.json();
        } catch (error) {
            if (error.message === 'Session expirée') {
                throw error;
            }
            console.error('Erreur fetch:', error);
            throw error;
        }
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
            const data = await this.fetchJSON('/affiliate/points/generate-discount', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ 
                    points: parseFloat(points), 
                    expires_days: parseInt(expiresDays) 
                })
            });

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
        const title = document.getElementById('codeTitle')?.value || '';
        const description = document.getElementById('codeDescription')?.value || '';
        const maxUses = document.getElementById('codeMaxUses')?.value;
        const bonusPoints = document.getElementById('codeBonusPoints')?.value || '0';
        const expiresAt = document.getElementById('codeExpiresAt')?.value;
        
        const previewContainer = document.getElementById('codePreview');
        if (!previewContainer) return;
        
        // Si aucun champ n'est rempli, afficher le message par défaut
        if (!title && !description && !maxUses && !bonusPoints && !expiresAt) {
            previewContainer.innerHTML = `
                <div class="text-center text-gray-400 dark:text-gray-500">
                    <i class="fas fa-code text-4xl mb-3"></i>
                    <p>Remplissez le formulaire pour voir l'aperçu</p>
                </div>
            `;
            return;
        }
        
        // Générer un code d'aperçu
        const codePrefix = codeType.toUpperCase().substr(0, 3);
        const randomCode = Math.floor(Math.random() * 9000) + 1000;
        const previewCode = `${codePrefix}${randomCode}`;
        
        // Construire les détails
        const typeLabels = {
            'general': '🌍 Général',
            'limited': '⏱️ Limité',
            'premium': '⭐ Premium',
            'seasonal': '🎄 Saisonnier'
        };
        
        const typeColors = {
            'general': 'bg-blue-500',
            'limited': 'bg-orange-500',
            'premium': 'bg-purple-500',
            'seasonal': 'bg-green-500'
        };
        
        let detailsHTML = '';
        
        if (maxUses) {
            detailsHTML += `
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                    <i class="fas fa-users text-orange-500 mr-2"></i>
                    <span>Max ${maxUses} utilisations</span>
                </div>
            `;
        } else {
            detailsHTML += `
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                    <i class="fas fa-infinity text-blue-500 mr-2"></i>
                    <span>Utilisations illimitées</span>
                </div>
            `;
        }
        
        if (expiresAt) {
            const expiryDate = new Date(expiresAt);
            const formattedDate = expiryDate.toLocaleDateString('fr-FR', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            detailsHTML += `
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                    <i class="fas fa-calendar-alt text-red-500 mr-2"></i>
                    <span>Expire le ${formattedDate}</span>
                </div>
            `;
        } else {
            detailsHTML += `
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <span>Permanent</span>
                </div>
            `;
        }
        
        if (bonusPoints && bonusPoints !== '0') {
            detailsHTML += `
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                    <i class="fas fa-gift text-yellow-500 mr-2"></i>
                    <span>+${bonusPoints} points bonus</span>
                </div>
            `;
        }
        
        // Générer l'aperçu complet
        previewContainer.innerHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-2 ${typeColors[codeType]} border-opacity-50">
                <div class="flex items-center justify-between mb-4">
                    <span class="px-3 py-1 ${typeColors[codeType]} text-white rounded-full text-xs font-semibold">
                        ${typeLabels[codeType]}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        <i class="fas fa-clock mr-1"></i> Créé maintenant
                    </span>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                    ${title || 'Titre du code'}
                </h3>
                
                <div class="bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-700 dark:to-gray-600 rounded-lg p-4 mb-4 text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Code généré automatiquement</p>
                    <p class="text-2xl font-mono font-bold text-gray-900 dark:text-white tracking-wider">
                        ${previewCode}
                    </p>
                </div>
                
                ${description ? `
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 italic">
                        "${description}"
                    </p>
                ` : ''}
                
                <div class="space-y-2 pt-4 border-t border-gray-200 dark:border-gray-600">
                    ${detailsHTML}
                </div>
                
                <div class="mt-4 flex items-center justify-center">
                    <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                        <i class="fas fa-share-alt mr-1"></i>
                        <span>Partageable sur tous les réseaux</span>
                    </div>
                </div>
            </div>
        `;
    }

    async createReferralCode() {
        const title = document.getElementById('codeTitle').value;
        const expiresAt = document.getElementById('codeExpiresAt').value;
        
        if (!title || title.trim() === '') {
            this.showAlert('Veuillez saisir un titre pour le code', 'warning');
            return;
        }
        
        const formData = {
            title: title,
            description: document.getElementById('codeDescription').value,
            max_uses: document.getElementById('codeMaxUses').value || null,
            bonus_points: document.getElementById('codeBonusPoints').value || 0,
            expires_at: expiresAt || null,
            is_active: true
        };

        // Désactiver le bouton et afficher un loader
        const submitBtn = document.querySelector('#createCodeForm button[type="submit"], button[form="createCodeForm"]');
        const originalBtnContent = submitBtn?.innerHTML;
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Création en cours...';
        }

        try {
            const data = await this.fetchJSON('/affiliate/referral-codes', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(formData)
            });

            if (data.success) {
                this.showAlert(`Code créé avec succès: <strong>${data.data.code}</strong>`, 'success');
                
                // Réinitialiser le formulaire
                document.getElementById('createCodeForm').reset();
                
                // Réinitialiser la prévisualisation
                const previewContainer = document.getElementById('codePreview');
                if (previewContainer) {
                    previewContainer.innerHTML = `
                        <div class="text-center text-gray-400 dark:text-gray-500">
                            <i class="fas fa-code text-4xl mb-3"></i>
                            <p>Remplissez le formulaire pour voir l'aperçu</p>
                        </div>
                    `;
                }
                
                // Réinitialiser le compteur de caractères
                const descriptionCount = document.getElementById('descriptionCount');
                if (descriptionCount) {
                    descriptionCount.textContent = '0';
                }
                
                // Fermer le modal
                this.closeModal('createCodeModal');
                
                // Recharger les codes et les statistiques
                this.loadReferralCodes();
                this.updateCodesStats();
            } else {
                this.showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Erreur création code:', error);
            if (error.message !== 'Session expirée') {
                this.showAlert('Erreur lors de la création du code', 'error');
            }
        } finally {
            // Réactiver le bouton
            if (submitBtn && originalBtnContent) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnContent;
            }
        }
    }

    async updateCodesStats() {
        try {
            const data = await this.fetchJSON('/affiliate/referral-codes');

            if (data.success) {
                const stats = data.data;
                
                document.getElementById('totalCodes').textContent = stats.total_codes || 0;
                document.getElementById('activeCodes').textContent = stats.active_codes || 0;
                document.getElementById('totalUses').textContent = stats.total_uses || 0;
                document.getElementById('bestPerforming').textContent = stats.best_performing || '-';
            }
        } catch (error) {
            console.error('Erreur stats codes:', error);
            // Silencieux pour les stats
        }
    }

    async loadPointsHistory() {
        const type = document.getElementById('historyType')?.value || 'all';
        const period = document.getElementById('historyPeriod')?.value || 'all';

        try {
            const data = await this.fetchJSON(`/affiliate/points/history?type=${type}&period=${period}`);

            if (data.success) {
                this.renderPointsHistory(data.data.transactions);
            }
        } catch (error) {
            console.error('Erreur historique points:', error);
            this.showAlert('Impossible de charger l\'historique', 'error');
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
            const data = await this.fetchJSON('/affiliate/referrals');

            if (data.success) {
                this.renderReferrals(data.data);
            }
        } catch (error) {
            console.error('Erreur chargement parrainages:', error);
            this.showAlert('Impossible de charger les parrainages', 'error');
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
            const url = statusFilter === 'all' ? '/affiliate/referral-codes' : 
                       `/affiliate/referral-codes?status=${statusFilter}`;
            
            const data = await this.fetchJSON(url);

            if (data.success) {
                this.renderReferralCodes(data.data);
                this.updateCodesStats();
            } else {
                this.showAlert(data.message || 'Erreur lors du chargement des codes', 'error');
            }
        } catch (error) {
            console.error('Erreur chargement codes:', error);
            this.showAlert('Impossible de charger les codes de parrainage', 'error');
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
            // Déterminer le statut basé sur is_active et expires_at
            let status = 'inactive';
            let statusLabel = 'Inactif';
            let statusColor = 'bg-gray-400';
            
            if (c.is_active) {
                if (c.expires_at && new Date(c.expires_at) < new Date()) {
                    status = 'expired';
                    statusLabel = 'Expiré';
                    statusColor = 'bg-red-500';
                } else {
                    status = 'active';
                    statusLabel = 'Actif';
                    statusColor = 'bg-green-500';
                }
            }
            
            return `
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-4 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">${c.title || 'Code sans titre'}</h5>
                            <div class="flex gap-2 mb-2">
                                <span class="px-3 py-1 ${statusColor} text-white rounded-full text-xs font-semibold">
                                    ${statusLabel}
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" 
                                    onclick="window.copyToClipboard('#code-${c.id}')" 
                                    title="Copier le code">
                                <i class="fas fa-copy"></i>
                            </button>
                            <button class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors" 
                                    onclick="window.shareCode('${c.code}', '${c.share_url || ''}')" 
                                    title="Partager">
                                <i class="fas fa-share-alt"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex items-center gap-2 mb-2">
                            <strong class="text-sm text-gray-600 dark:text-gray-400">Code:</strong>
                            <code id="code-${c.id}" class="text-lg font-mono font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 px-3 py-1 rounded">${c.code}</code>
                        </div>
                        ${c.description ? `<p class="text-sm text-gray-600 dark:text-gray-300 italic">"${c.description}"</p>` : ''}
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4 mb-4 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <div class="text-center">
                            <div class="flex items-center justify-center text-blue-600 dark:text-blue-400 mb-1">
                                <i class="fas fa-users mr-1"></i>
                            </div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white">
                                ${c.current_uses || 0}${c.max_uses ? `/${c.max_uses}` : ''}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Utilisations</div>
                        </div>
                        <div class="text-center">
                            <div class="flex items-center justify-center text-green-600 dark:text-green-400 mb-1">
                                <i class="fas fa-handshake mr-1"></i>
                            </div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white">
                                ${c.stats?.completed_referrals || 0}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Parrainages</div>
                        </div>
                        <div class="text-center">
                            <div class="flex items-center justify-center text-yellow-600 dark:text-yellow-400 mb-1">
                                <i class="fas fa-star mr-1"></i>
                            </div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white">
                                ${c.stats?.total_points_generated || 0}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Points</div>
                        </div>
                    </div>
                    
                    ${c.bonus_points > 0 ? `
                        <div class="mb-2">
                            <span class="inline-flex items-center text-sm text-green-600 dark:text-green-400">
                                <i class="fas fa-gift mr-1"></i> +${c.bonus_points} points bonus pour les filleuls
                            </span>
                        </div>
                    ` : ''}
                    
                    ${c.expires_at ? `
                        <div class="mb-2">
                            <span class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-calendar-alt mr-1"></i> Expire le ${new Date(c.expires_at).toLocaleDateString('fr-FR')}
                            </span>
                        </div>
                    ` : ''}
                    
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-clock mr-1"></i> Créé le ${new Date(c.created_at).toLocaleDateString('fr-FR')}
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        document.getElementById('referralCodesList').innerHTML = codesHtml;
    }

    async loadRedemptions() {
        try {
            const data = await this.fetchJSON('/affiliate/redemptions');

            if (data.success) {
                this.renderRedemptions(data.data);
            }
        } catch (error) {
            console.error('Erreur chargement rachats:', error);
            this.showAlert('Impossible de charger les rachats', 'error');
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
            const data = await this.fetchJSON('/affiliate/leaderboard');

            if (data.success) {
                this.renderLeaderboard(data.data);
            }
        } catch (error) {
            console.error('Erreur chargement classement:', error);
            this.showAlert('Impossible de charger le classement', 'error');
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
window.openModal = function openModal(modalId) {
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

window.closeModal = function closeModal(modalId) {
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

window.copyToClipboard = function copyToClipboard(text) {
    let textToCopy = text;
    
    if (typeof text === 'string' && text.startsWith('#')) {
        const element = document.querySelector(text);
        if (element) {
            textToCopy = element.value || element.textContent || element.innerText;
        }
    }
    
    navigator.clipboard.writeText(textToCopy).then(() => {
        // Créer une notification temporaire avec Tailwind
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-[9999] animate-fade-in';
        notification.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Copié dans le presse-papiers !';
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 2000);
    }).catch(err => {
        console.error('Erreur copie:', err);
    });
}

window.shareOnFacebook = function shareOnFacebook() {
    const url = document.getElementById('shareUrl').value;
    const shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
    window.open(shareUrl, '_blank', 'width=600,height=400');
}

window.shareOnTwitter = function shareOnTwitter() {
    const code = document.getElementById('shareCode').value;
    const text = `Rejoignez VintApp avec mon code de parrainage ${code} et gagnez des points !`;
    const url = document.getElementById('shareUrl').value;
    const shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`;
    window.open(shareUrl, '_blank', 'width=600,height=400');
}

window.shareOnWhatsApp = function shareOnWhatsApp() {
    const code = document.getElementById('shareCode').value;
    const url = document.getElementById('shareUrl').value;
    const text = `🎉 Rejoignez VintApp avec mon code de parrainage *${code}* et gagnez des points ! ${url}`;
    const shareUrl = `https://wa.me/?text=${encodeURIComponent(text)}`;
    window.open(shareUrl, '_blank');
}

window.shareCode = function shareCode(code, url) {
    document.getElementById('shareCode').value = code;
    document.getElementById('shareUrl').value = url;
    window.openModal('shareModal');
}

window.refreshCodesList = function refreshCodesList() {
    if (window.affiliateDashboard) {
        window.affiliateDashboard.loadReferralCodes();
    }
}

window.generateCodeTitle = function generateCodeTitle() {
    const randomNum = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
    const title = `PARRAINS${randomNum}`;
    document.getElementById('codeTitle').value = title;
}

window.updateCodePreview = function updateCodePreview() {
    if (window.affiliateDashboard) {
        window.affiliateDashboard.updateCodePreview();
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