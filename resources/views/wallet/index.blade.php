@extends('app')

@section('title', 'Mon Portefeuille')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-wallet text-primary me-2"></i>
                    Mon Portefeuille
                </h1>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="refreshBalances()">
                        <i class="fas fa-sync-alt"></i> Actualiser
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes des soldes -->
    <div class="row mb-4">
        <!-- Wallet USD -->
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title text-success mb-1">
                                <i class="fas fa-dollar-sign me-2"></i>Dollar Américain
                            </h5>
                            <p class="text-muted small mb-0">USD</p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('wallet.add-funds', $usdWallet) }}">
                                    <i class="fas fa-plus text-success me-2"></i>Ajouter des fonds
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('wallet.withdraw-funds', $usdWallet) }}">
                                    <i class="fas fa-minus text-danger me-2"></i>Retirer des fonds
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('wallet.transactions', $usdWallet) }}">
                                    <i class="fas fa-history text-info me-2"></i>Voir l'historique
                                </a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="text-center py-3">
                        <h2 class="display-5 fw-bold text-success mb-2" id="usd-balance">
                            ${{ number_format($usdWallet->balance, 2, '.', ',') }}
                        </h2>
                        <p class="text-muted mb-0">Solde disponible</p>
                    </div>
                    
                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('wallet.add-funds', $usdWallet) }}" class="btn btn-success flex-fill">
                            <i class="fas fa-plus me-1"></i>Ajouter
                        </a>
                        <a href="{{ route('wallet.withdraw-funds', $usdWallet) }}" class="btn btn-outline-danger flex-fill">
                            <i class="fas fa-minus me-1"></i>Retirer
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wallet CDF -->
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title text-warning mb-1">
                                <i class="fas fa-coins me-2"></i>Franc Congolais
                            </h5>
                            <p class="text-muted small mb-0">CDF</p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('wallet.add-funds', $cdfWallet) }}">
                                    <i class="fas fa-plus text-success me-2"></i>Ajouter des fonds
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('wallet.withdraw-funds', $cdfWallet) }}">
                                    <i class="fas fa-minus text-danger me-2"></i>Retirer des fonds
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('wallet.transactions', $cdfWallet) }}">
                                    <i class="fas fa-history text-info me-2"></i>Voir l'historique
                                </a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="text-center py-3">
                        <h2 class="display-5 fw-bold text-warning mb-2" id="cdf-balance">
                            {{ number_format($cdfWallet->balance, 2, ',', ' ') }} FC
                        </h2>
                        <p class="text-muted mb-0">Solde disponible</p>
                    </div>
                    
                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('wallet.add-funds', $cdfWallet) }}" class="btn btn-warning flex-fill">
                            <i class="fas fa-plus me-1"></i>Ajouter
                        </a>
                        <a href="{{ route('wallet.withdraw-funds', $cdfWallet) }}" class="btn btn-outline-danger flex-fill">
                            <i class="fas fa-minus me-1"></i>Retirer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions récentes -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history text-info me-2"></i>
                            Transactions récentes
                        </h5>
                        <small class="text-muted">10 dernières transactions</small>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentTransactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Montant</th>
                                        <th>Devise</th>
                                        <th>Solde après</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $transaction)
                                        <tr>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $transaction->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                @if($transaction->type === 'credit')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-plus me-1"></i>Crédit
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-minus me-1"></i>Débit
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $transaction->description }}</td>
                                            <td>
                                                <span class="fw-bold {{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->type === 'credit' ? '+' : '-' }}
                                                    @if($transaction->wallet->currency === 'CDF')
                                                        {{ number_format($transaction->amount, 2, ',', ' ') }} FC
                                                    @else
                                                        ${{ number_format($transaction->amount, 2, '.', ',') }}
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $transaction->wallet->currency === 'USD' ? 'success' : 'warning' }}">
                                                    {{ $transaction->wallet->currency }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    @if($transaction->wallet->currency === 'CDF')
                                                        {{ number_format($transaction->balance_after, 2, ',', ' ') }} FC
                                                    @else
                                                        ${{ number_format($transaction->balance_after, 2, '.', ',') }}
                                                    @endif
                                                </small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune transaction</h5>
                            <p class="text-muted">Vos transactions apparaîtront ici une fois que vous aurez effectué des opérations.</p>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('wallet.add-funds', $usdWallet) }}" class="btn btn-success">
                                    <i class="fas fa-plus me-1"></i>Ajouter des fonds USD
                                </a>
                                <a href="{{ route('wallet.add-funds', $cdfWallet) }}" class="btn btn-warning">
                                    <i class="fas fa-plus me-1"></i>Ajouter des fonds CDF
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function refreshBalances() {
    // Animation des soldes pendant le chargement
    const usdBalance = document.getElementById('usd-balance');
    const cdfBalance = document.getElementById('cdf-balance');
    
    // Ajouter un spinner temporaire
    const originalUsd = usdBalance.innerHTML;
    const originalCdf = cdfBalance.innerHTML;
    
    usdBalance.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    cdfBalance.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    // Simuler un délai pour l'effet visuel
    setTimeout(() => {
        location.reload();
    }, 500);
}

// Animation d'entrée pour les cartes
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endpush
@endsection