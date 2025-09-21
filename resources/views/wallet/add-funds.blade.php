@extends('app')

@section('title', 'Ajouter des fonds - ' . $wallet->currency)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow">
                <div class="card-header bg-{{ $wallet->currency === 'USD' ? 'success' : 'warning' }} text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-plus me-2"></i>
                            Ajouter des fonds
                        </h5>
                        <span class="badge bg-light text-dark">
                            {{ $wallet->currency }}
                        </span>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <!-- Solde actuel -->
                    <div class="alert alert-{{ $wallet->currency === 'USD' ? 'success' : 'warning' }} alert-dismissible" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-wallet fa-2x me-3"></i>
                            <div>
                                <h6 class="mb-1">Solde actuel</h6>
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

                    <form action="{{ route('wallet.store-add-funds', $wallet) }}" method="POST" id="addFundsForm">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="amount" class="form-label fw-semibold">
                                <i class="fas fa-coins me-1"></i>
                                Montant à ajouter
                                @if($wallet->currency === 'CDF')
                                    <small class="text-muted">(en Francs Congolais)</small>
                                @else
                                    <small class="text-muted">(en Dollars US)</small>
                                @endif
                            </label>
                            <div class="input-group">
                                @if($wallet->currency === 'USD')
                                    <span class="input-group-text bg-success text-white">
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
                                       max="999999.99"
                                       placeholder="0.00"
                                       required>
                                @if($wallet->currency === 'CDF')
                                    <span class="input-group-text bg-warning text-dark">FC</span>
                                @endif
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Montant minimum : 
                                {{ $wallet->currency === 'CDF' ? '0,01 FC' : '$0.01' }}
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
                                   placeholder="Ex: Rechargement de compte, Dépôt initial...">
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
                                    <span>Montant à ajouter :</span>
                                    <span class="text-success fw-bold" id="addAmount">+0.00</span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">Nouveau solde :</span>
                                    <span class="fw-bold text-{{ $wallet->currency === 'USD' ? 'success' : 'warning' }} fs-5" id="newBalance">
                                        @if($wallet->currency === 'CDF')
                                            {{ number_format($wallet->balance, 2, ',', ' ') }} FC
                                        @else
                                            ${{ number_format($wallet->balance, 2, '.', ',') }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-{{ $wallet->currency === 'USD' ? 'success' : 'warning' }} btn-lg">
                                <i class="fas fa-plus me-2"></i>
                                Ajouter les fonds
                            </button>
                            <a href="{{ route('wallet.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Retour au portefeuille
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Conseils de sécurité -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-shield-alt me-2"></i>
                        Conseils de sécurité
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Vérifiez toujours le montant avant de confirmer
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Gardez un historique de vos transactions
                        </li>
                        <li>
                            <i class="fas fa-check text-success me-2"></i>
                            N'ajoutez que des montants que vous pouvez vous permettre
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
    const preview = document.getElementById('preview');
    const addAmountSpan = document.getElementById('addAmount');
    const newBalanceSpan = document.getElementById('newBalance');
    const currentBalance = {{ $wallet->balance }};
    const currency = '{{ $wallet->currency }}';
    
    amountInput.addEventListener('input', function() {
        const amount = parseFloat(this.value) || 0;
        
        if (amount > 0) {
            preview.style.display = 'block';
            
            // Mise à jour de l'aperçu
            if (currency === 'CDF') {
                addAmountSpan.textContent = '+' + amount.toLocaleString('fr-FR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' FC';
                
                newBalanceSpan.textContent = (currentBalance + amount).toLocaleString('fr-FR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' FC';
            } else {
                addAmountSpan.textContent = '+$' + amount.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                
                newBalanceSpan.textContent = '$' + (currentBalance + amount).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
        } else {
            preview.style.display = 'none';
        }
    });
    
    // Animation du formulaire
    const form = document.getElementById('addFundsForm');
    form.style.opacity = '0';
    form.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
        form.style.transition = 'all 0.5s ease';
        form.style.opacity = '1';
        form.style.transform = 'translateY(0)';
    }, 200);
});
</script>
@endpush
@endsection