@extends('app')

@section('title', 'Retirer des fonds - ' . $wallet->currency)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow">
                <div class="card-header bg-danger text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-minus me-2"></i>
                            Retirer des fonds
                        </h5>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="badge bg-light text-dark">
                                {{ $wallet->currency }}
                            </span>
                            <span class="badge bg-success" title="Traitement automatique via API mobile money">
                                <i class="fas fa-bolt"></i> Auto
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <!-- Solde actuel -->
                    <div class="alert alert-info alert-dismissible" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-wallet fa-2x me-3"></i>
                            <div>
                                <h6 class="mb-1">Solde disponible</h6>
                                <h4 class="mb-0 fw-bold">
                                    @if($wallet->currency === 'CDF')
                                        {{ number_format($wallet->balance, 2, ',', ' ') }} FC
                                    @else
                                        ${{ number_format($wallet->balance, 2, '.', ',') }}
                                    @endif
                                </h4>
                            </div>
                        </div>
                    </div>

                    @if($wallet->balance <= 0)
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                            <h5>Solde insuffisant</h5>
                            <p>Vous n'avez pas de fonds disponibles pour effectuer un retrait.</p>
                            <a href="{{ route('wallet.add-funds', $wallet) }}" class="btn btn-success">
                                <i class="fas fa-plus me-1"></i>Ajouter des fonds
                            </a>
                        </div>
                    @else
                        <form action="{{ route('wallet.store-withdraw-funds', $wallet) }}" method="POST" id="withdrawFundsForm">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="amount" class="form-label fw-semibold">
                                    <i class="fas fa-coins me-1"></i>
                                    Montant à retirer
                                    @if($wallet->currency === 'CDF')
                                        <small class="text-muted">(en Francs Congolais)</small>
                                    @else
                                        <small class="text-muted">(en Dollars US)</small>
                                    @endif
                                </label>
                                <div class="input-group">
                                    @if($wallet->currency === 'USD')
                                        <span class="input-group-text bg-danger text-white">
                                            <i class="fas fa-dollar-sign"></i>
                                        </span>
                                    @endif
                                    <input type="number" 
                                           class="form-control form-control-lg @error('amount') is-invalid @enderror" 
                                           id="amount" 
                                           name="amount" 
                                           value="{{ old('amount') }}"
                                           step="0.01" 
                                           min="0.01" 
                                           max="{{ $wallet->balance }}"
                                           placeholder="0.00"
                                           required>
                                    @if($wallet->currency === 'CDF')
                                        <span class="input-group-text bg-danger text-white">FC</span>
                                    @endif
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Montant maximum : 
                                    @if($wallet->currency === 'CDF')
                                        {{ number_format($wallet->balance, 2, ',', ' ') }} FC
                                    @else
                                        ${{ number_format($wallet->balance, 2, '.', ',') }}
                                    @endif
                                </small>
                            </div>

                            <!-- Numéro de téléphone -->
                            <div class="mb-4">
                                <label for="phone_number" class="form-label fw-semibold">
                                    <i class="fas fa-mobile-alt me-1"></i>
                                    Numéro de téléphone mobile money
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="tel" 
                                       class="form-control form-control-lg @error('phone_number') is-invalid @enderror" 
                                       id="phone_number" 
                                       name="phone_number" 
                                       value="{{ old('phone_number') }}"
                                       placeholder="Ex: 0812345678 ou +243812345678"
                                       pattern="^(\+?243|0)?[0-9]{9}$"
                                       required>
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Le numéro où vous recevrez l'argent (format: 0812345678 ou +243812345678)
                                </small>
                            </div>

                            <!-- Méthode de paiement - MaishaPay uniquement -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-credit-card me-1"></i>
                                    Opérateur de retrait
                                </label>
                                <div class="alert alert-success d-flex align-items-center" role="alert">
                                    <i class="fas fa-bolt fa-2x me-3"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold">MaishaPay</h6>
                                        <small>Tous opérateurs Mobile Money RDC (Orange, M-Pesa, Airtel, Africell)</small>
                                    </div>
                                    <span class="badge bg-success">Actif</span>
                                </div>
                                <input type="hidden" name="payment_method" value="maishapay">
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    MaishaPay détecte automatiquement votre opérateur à partir de votre numéro
                                </small>
                            </div>

                            <div class="mb-4">
                                <label for="description" class="form-label fw-semibold">
                                    <i class="fas fa-comment me-1"></i>
                                    Description <small class="text-muted">(optionnel)</small>
                                </label>
                                <input type="text" 
                                       class="form-control @error('description') is-invalid @enderror" 
                                       id="description" 
                                       name="description" 
                                       value="{{ old('description') }}"
                                       maxlength="255"
                                       placeholder="Ex: Retrait pour achat, Transfer vers banque...">
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Aperçu du nouveau solde -->
                            <div class="card bg-light border-0 mb-4" id="preview" style="display: none;">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">
                                        <i class="fas fa-calculator me-1"></i>
                                        Aperçu du nouveau solde
                                    </h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Solde actuel :</span>
                                        <span class="fw-bold" id="currentBalance">
                                            @if($wallet->currency === 'CDF')
                                                {{ number_format($wallet->balance, 2, ',', ' ') }} FC
                                            @else
                                                ${{ number_format($wallet->balance, 2, '.', ',') }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Montant à retirer :</span>
                                        <span class="text-danger fw-bold" id="withdrawAmount">-0.00</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">Nouveau solde :</span>
                                        <span class="fw-bold text-info fs-5" id="newBalance">
                                            @if($wallet->currency === 'CDF')
                                                {{ number_format($wallet->balance, 2, ',', ' ') }} FC
                                            @else
                                                ${{ number_format($wallet->balance, 2, '.', ',') }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Information sur le traitement MaishaPay -->
                            <div class="alert alert-info" role="alert">
                                <div class="d-flex">
                                    <i class="fas fa-bolt me-3 mt-1"></i>
                                    <div>
                                        <h6 class="mb-1">⚡ Traitement MaishaPay</h6>
                                        <small>Votre retrait sera traité automatiquement via MaishaPay. Les fonds seront envoyés directement vers votre compte mobile sous quelques minutes (2-10 min selon l'opérateur).</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Avertissement -->
                            <div class="alert alert-warning" role="alert">
                                <div class="d-flex">
                                    <i class="fas fa-exclamation-triangle me-3 mt-1"></i>
                                    <div>
                                        <h6 class="mb-1">⚠️ Attention</h6>
                                        <small>
                                            <strong>Votre wallet sera débité immédiatement.</strong> Si le transfert échoue, 
                                            le montant sera automatiquement remboursé dans votre wallet.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-danger btn-lg" id="confirmBtn" disabled>
                                    <i class="fas fa-minus me-2"></i>
                                    Confirmer le retrait
                                </button>
                                <a href="{{ route('wallet.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Retour au portefeuille
                                </a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Conseils de sécurité -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="text-danger mb-3">
                        <i class="fas fa-shield-alt me-2"></i>
                        Important à retenir
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-clock text-info me-2"></i>
                            <strong>Délai de traitement :</strong> 2 à 10 minutes selon l'opérateur
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-mobile-alt text-success me-2"></i>
                            <strong>Numéro correct :</strong> Vérifiez que le numéro correspond bien à l'opérateur sélectionné
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-exclamation text-warning me-2"></i>
                            <strong>Débit immédiat :</strong> Les fonds seront bloqués pendant le traitement
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-undo text-primary me-2"></i>
                            <strong>Remboursement automatique :</strong> En cas d'échec, le montant sera recrédité
                        </li>
                        <li>
                            <i class="fas fa-history text-secondary me-2"></i>
                            <strong>Historique :</strong> Consultez l'historique de vos transactions pour suivre le statut
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Opérateurs supportés par MaishaPay -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="text-success mb-3">
                        <i class="fas fa-bolt me-2"></i>
                        Opérateurs supportés par MaishaPay
                    </h6>
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <div class="p-2 bg-light rounded text-center">
                                <div class="fs-4 mb-1">🟠</div>
                                <small class="fw-semibold">Orange Money</small>
                                <div><small class="text-muted">084/085/089</small></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-2 bg-light rounded text-center">
                                <div class="fs-4 mb-1">🟢</div>
                                <small class="fw-semibold">M-Pesa</small>
                                <div><small class="text-muted">081/082/083</small></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-2 bg-light rounded text-center">
                                <div class="fs-4 mb-1">🔴</div>
                                <small class="fw-semibold">Airtel Money</small>
                                <div><small class="text-muted">097/098/099</small></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-2 bg-light rounded text-center">
                                <div class="fs-4 mb-1">🔵</div>
                                <small class="fw-semibold">Africell</small>
                                <div><small class="text-muted">090/091/092</small></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
/* Animations pour le formulaire de retrait */
.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

#preview {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-control.is-valid,
.form-select.is-valid {
    border-color: #28a745;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(.375em + .1875rem) center;
    background-size: calc(.75em + .375rem) calc(.75em + .375rem);
}

.form-control.is-invalid,
.form-select.is-invalid {
    border-color: #dc3545;
}

/* Style pour les cartes d'opérateurs */
.bg-light.rounded {
    transition: all 0.2s ease;
    cursor: pointer;
}

.bg-light.rounded:hover {
    background-color: #e9ecef !important;
    transform: scale(1.05);
}

/* Badge Auto animation */
.badge.bg-success {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

/* Bouton de confirmation */
#confirmBtn:not(:disabled) {
    animation: glow 1.5s ease-in-out infinite;
}

@keyframes glow {
    0%, 100% {
        box-shadow: 0 0 5px rgba(220, 53, 69, 0.5);
    }
    50% {
        box-shadow: 0 0 20px rgba(220, 53, 69, 0.8);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const phoneInput = document.getElementById('phone_number');
    const confirmBtn = document.getElementById('confirmBtn');
    const preview = document.getElementById('preview');
    const withdrawAmountSpan = document.getElementById('withdrawAmount');
    const newBalanceSpan = document.getElementById('newBalance');
    const currentBalance = {{ $wallet->balance }};
    const currency = '{{ $wallet->currency }}';
    
    // Fonction de validation du formulaire (MaishaPay toujours sélectionné)
    function validateForm() {
        const amount = parseFloat(amountInput.value) || 0;
        const phone = phoneInput.value.trim();
        
        const isAmountValid = amount > 0 && amount <= currentBalance;
        const isPhoneValid = phone.length >= 9 && /^(\+?243|0)?[0-9]{9}$/.test(phone);
        
        confirmBtn.disabled = !(isAmountValid && isPhoneValid);
        
        return {
            isValid: isAmountValid && isPhoneValid,
            amount: amount
        };
    }
    
    // Validation du numéro de téléphone en temps réel
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            const phone = this.value.trim();
            const phoneRegex = /^(\+?243|0)?[0-9]{9}$/;
            
            if (phone.length > 0) {
                if (phoneRegex.test(phone)) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
            
            validateForm();
        });
    }
    
    // Gestion du montant
    if (amountInput) {
        amountInput.addEventListener('input', function() {
            const validation = validateForm();
            const amount = parseFloat(this.value) || 0;
            
            // Afficher l'aperçu dès qu'il y a un montant, même si pas toutes les validations sont passées
            if (amount > 0) {
                preview.style.display = 'block';
                
                // Mise à jour de l'aperçu
                if (currency === 'CDF') {
                    withdrawAmountSpan.textContent = '-' + amount.toLocaleString('fr-FR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) + ' FC';
                    
                    newBalanceSpan.textContent = (currentBalance - amount).toLocaleString('fr-FR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) + ' FC';
                } else {
                    withdrawAmountSpan.textContent = '-$' + amount.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    
                    newBalanceSpan.textContent = '$' + (currentBalance - amount).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
                
                // Changer la couleur du nouveau solde selon si le montant est valide
                if (amount > currentBalance) {
                    newBalanceSpan.classList.add('text-danger');
                    newBalanceSpan.classList.remove('text-info');
                } else {
                    newBalanceSpan.classList.add('text-info');
                    newBalanceSpan.classList.remove('text-danger');
                }
            } else {
                preview.style.display = 'none';
            }
        });
    }
    
    // Confirmation avant soumission
    const form = document.getElementById('withdrawFundsForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const phone = phoneInput.value.trim();
            const amount = parseFloat(amountInput.value);
            
            const confirmMessage = `🔄 RETRAIT VIA MAISHAPAY\n\n` +
                `Montant : ${currency === 'CDF' ? amount.toLocaleString('fr-FR') + ' FC' : '$' + amount.toLocaleString('en-US')}\n` +
                `Vers : ${phone}\n` +
                `Opérateur : MaishaPay (détection automatique)\n\n` +
                `⚡ Le transfert sera traité automatiquement dans les 2-10 minutes.\n` +
                `💰 Votre wallet sera débité immédiatement.\n` +
                `🔄 Remboursement automatique en cas d'échec.\n\n` +
                `Confirmez-vous cette opération ?`;
            
            if (!confirm(confirmMessage)) {
                e.preventDefault();
            } else {
                // Afficher un loader pendant le traitement
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Traitement en cours...';
            }
        });
        
        // Animation du formulaire
        form.style.opacity = '0';
        form.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            form.style.transition = 'all 0.5s ease';
            form.style.opacity = '1';
            form.style.transform = 'translateY(0)';
        }, 200);
    }
});
</script>
@endpush
@endsection