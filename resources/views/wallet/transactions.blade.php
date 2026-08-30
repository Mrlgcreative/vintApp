    @extends('app')

@section('title', 'Historique des transactions - ' . $wallet->currency)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-history text-info me-2"></i>
                        Historique des transactions
                    </h1>
                    <p class="text-muted mb-0">
                        Wallet {{ $wallet->currency }} - 
                        Solde actuel : 
                        <strong class="text-{{ $wallet->currency === 'USD' ? 'success' : 'warning' }}">
                            @if($wallet->currency === 'CDF')
                                {{ number_format($wallet->balance, 2, ',', ' ') }} FC
                            @else
                                ${{ number_format($wallet->balance, 2, '.', ',') }}
                            @endif
                        </strong>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('wallet.index') }}" class="btn btn-{{ $wallet->currency === 'USD' ? 'success' : 'warning' }}">
                        <i class="fas fa-plus me-1"></i>Ajouter
                    </a>
                    <a href="{{ route('wallet.withdraw-funds', $wallet) }}" class="btn btn-outline-danger">
                        <i class="fas fa-minus me-1"></i>Retirer
                    </a>
                    <a href="{{ route('wallet.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                </div>
            </div>

            <!-- Statistiques rapides -->
            @if($transactions->total() > 0)
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-receipt fa-2x text-info mb-2"></i>
                                <h5 class="mb-1">{{ $transactions->total() }}</h5>
                                <small class="text-muted">Transactions totales</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-plus fa-2x text-success mb-2"></i>
                                <h5 class="mb-1">{{ $wallet->transactions()->where('type', 'credit')->count() }}</h5>
                                <small class="text-muted">Crédits</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-minus fa-2x text-danger mb-2"></i>
                                <h5 class="mb-1">{{ $wallet->transactions()->where('type', 'debit')->count() }}</h5>
                                <small class="text-muted">Débits</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-coins fa-2x text-{{ $wallet->currency === 'USD' ? 'success' : 'warning' }} mb-2"></i>
                                <h5 class="mb-1">
                                    @if($wallet->currency === 'CDF')
                                        {{ number_format($wallet->balance, 0, ',', ' ') }}
                                    @else
                                        {{ number_format($wallet->balance, 0, '.', ',') }}
                                    @endif
                                </h5>
                                <small class="text-muted">Solde {{ $wallet->currency }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Liste des transactions -->
            <div class="card border-0 shadow">
                <div class="card-header bg-white dark:bg-gray-800 border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Transactions
                        </h5>
                        @if($transactions->total() > 0)
                            <span class="badge bg-light text-dark">
                                {{ $transactions->firstItem() }} - {{ $transactions->lastItem() }} 
                                sur {{ $transactions->total() }}
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="card-body p-0">
                    @if($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date & Heure</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Référence</th>
                                        <th class="text-end">Montant</th>
                                        <th class="text-end">Solde après</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $transaction->created_at->format('d/m/Y') }}</strong>
                                                </div>
                                                <small class="text-muted">{{ $transaction->created_at->format('H:i:s') }}</small>
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
                                            <td>
                                                <div>{{ $transaction->description }}</div>
                                                @if($transaction->reference)
                                                    <small class="text-muted">Réf: {{ $transaction->reference }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($transaction->reference)
                                                    <code class="small">{{ $transaction->reference }}</code>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold {{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->type === 'credit' ? '+' : '-' }}
                                                    @if($wallet->currency === 'CDF')
                                                        {{ number_format($transaction->amount, 2, ',', ' ') }} FC
                                                    @else
                                                        ${{ number_format($transaction->amount, 2, '.', ',') }}
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <span class="text-muted">
                                                    @if($wallet->currency === 'CDF')
                                                        {{ number_format($transaction->balance_after, 2, ',', ' ') }} FC
                                                    @else
                                                        ${{ number_format($transaction->balance_after, 2, '.', ',') }}
                                                    @endif
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($transactions->hasPages())
                            <div class="d-flex justify-content-center p-3 border-top">
                                {{ $transactions->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune transaction</h5>
                            <p class="text-muted mb-4">Ce wallet n'a encore aucune transaction.</p>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('wallet.index') }}" class="btn btn-{{ $wallet->currency === 'USD' ? 'success' : 'warning' }}">
                                    <i class="fas fa-plus me-1"></i>
                                    Ajouter des fonds {{ $wallet->currency }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions rapides -->
            @if($transactions->count() > 0)
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-bolt me-2"></i>
                            Actions rapides
                        </h6>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <a href="{{ route('wallet.index') }}" class="btn btn-outline-{{ $wallet->currency === 'USD' ? 'success' : 'warning' }} w-100">
                                    <i class="fas fa-plus me-2"></i>Ajouter des fonds
                                </a>
                            </div>
                            <div class="col-md-4 mb-2">
                                <a href="{{ route('wallet.withdraw-funds', $wallet) }}" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-minus me-2"></i>Retirer des fonds
                                </a>
                            </div>
                            <div class="col-md-4 mb-2">
                                <button class="btn btn-outline-info w-100" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i>Imprimer l'historique
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    .btn, .dropdown, .card-header .d-flex > div:last-child {
        display: none !important;
    }
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
}

.transaction-row:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s ease;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation des lignes de transaction
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach((row, index) => {
        row.classList.add('transaction-row');
        row.style.opacity = '0';
        row.style.transform = 'translateX(-20px)';
        
        setTimeout(() => {
            row.style.transition = 'all 0.3s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateX(0)';
        }, index * 50);
    });
    
    // Animation des cartes de statistiques
    const statCards = document.querySelectorAll('.row .card');
    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(-10px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.4s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endpush
@endsection