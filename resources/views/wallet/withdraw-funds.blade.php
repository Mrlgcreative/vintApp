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
                        <span class="badge bg-light text-dark">
                            {{ $wallet->currency }}
                        </span>
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

                            <!-- Avertissement -->
                            <div class="alert alert-warning" role="alert">
                                <div class="d-flex">
                                    <i class="fas fa-exclamation-triangle me-3 mt-1"></i>
                                    <div>
                                        <h6 class="mb-1">Attention</h6>
                                        <small>Cette action est irréversible. Assurez-vous que le montant est correct avant de confirmer.</small>
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
                            <i class="fas fa-exclamation text-warning me-2"></i>
                            Les retraits sont définitifs et ne peuvent être annulés
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-exclamation text-warning me-2"></i>
                            Vérifiez le montant deux fois avant de confirmer
                        </li>
                        <li>
                            <i class="fas fa-exclamation text-warning me-2"></i>
                            Gardez une trace de vos retraits pour votre comptabilité
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const confirmBtn = document.getElementById('confirmBtn');
    const preview = document.getElementById('preview');
    const withdrawAmountSpan = document.getElementById('withdrawAmount');
    const newBalanceSpan = document.getElementById('newBalance');
    const currentBalance = {{ $wallet->balance }};
    const currency = '{{ $wallet->currency }}';
    
    if (amountInput) {
        amountInput.addEventListener('input', function() {
            const amount = parseFloat(this.value) || 0;
            
            if (amount > 0 && amount <= currentBalance) {
                confirmBtn.disabled = false;
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
            } else {
                confirmBtn.disabled = true;
                preview.style.display = 'none';
            }
        });
    }
    
    // Animation du formulaire
    const form = document.getElementById('withdrawFundsForm');
    if (form) {
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