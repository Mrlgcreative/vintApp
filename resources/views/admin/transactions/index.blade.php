@extends('layouts.admin')

@section('title', 'Transactions')

@section('page-title', 'Gestion des Transactions')

@section('page-actions')
<div class="flex gap-3">
    <button type="button" onclick="exportTransactions()" 
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
        <i class="fas fa-download mr-2"></i>Exporter
    </button>
    <button type="button" onclick="toggleFilterModal()" 
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
        <i class="fas fa-filter mr-2"></i>Filtres
    </button>
</div>
@endsection

@section('content')
<!-- Filtres Modal -->
<div id="filterModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Filtrer les transactions
                    </h3>
                    <form action="{{ route('admin.transactions.index') }}" method="GET" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                <option value="">Tous</option>
                                <option value="pending" @if(request('status') == 'pending') selected @endif>En attente</option>
                                <option value="completed" @if(request('status') == 'completed') selected @endif>Complété</option>
                                <option value="failed" @if(request('status') == 'failed') selected @endif>Échoué</option>
                                <option value="refunded" @if(request('status') == 'refunded') selected @endif>Remboursé</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Méthode de paiement</label>
                            <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                <option value="">Toutes</option>
                                <option value="wallet" @if(request('payment_method') == 'wallet') selected @endif>Wallet</option>
                                <option value="airtel_money" @if(request('payment_method') == 'airtel_money') selected @endif>Airtel Money</option>
                                <option value="orange_money" @if(request('payment_method') == 'orange_money') selected @endif>Orange Money</option>
                                <option value="mpesa" @if(request('payment_method') == 'mpesa') selected @endif>M-Pesa</option>
                                <option value="afrimoney" @if(request('payment_method') == 'afrimoney') selected @endif>Afrimoney</option>
                            <option value="bank" @if(request('payment_method') == 'bank') selected @endif>Banque</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Date début</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Date fin</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Appliquer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tableau des transactions -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Utilisateur</th>
                        <th>Montant</th>
                        <th>Méthode</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($transaction->user->avatar)
                                        <img src="{{ $transaction->user->avatar_url }}" class="rounded-circle me-2" width="32" height="32">
                                    @else
                                        <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px">
                                            <span class="text-white">{{ $transaction->user->initial }}</span>
                                        </div>
                                    @endif
                                    {{ $transaction->user->name }}
                                </div>
                            </td>
                            <td class="transaction-amount">
                                {{ number_format($transaction->amount, 2) }}
                                <small>{{ $transaction->currency }}</small>
                            </td>
                            <td>
                                @switch($transaction->payment_method)
                                    @case('wallet')
                                        <i class="fas fa-wallet text-primary me-1"></i>
                                        @break
                                    @case('airtel_money')
                                        <i class="fas fa-mobile-alt text-danger me-1"></i>
                                        @break
                                    @case('orange_money')
                                        <i class="fas fa-mobile-alt text-warning me-1"></i>
                                        @break
                                    @case('mpesa')
                                        <i class="fas fa-mobile-alt text-success me-1"></i>
                                        @break
                                    @default
                                        <i class="fas fa-money-bill-wave text-secondary me-1"></i>
                                @endswitch
                                {{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}
                            </td>
                            <td>
                                <span class="badge status-badge bg-{{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : ($transaction->status === 'failed' ? 'danger' : 'secondary')) }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.transactions.show', $transaction) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($transaction->status === 'pending')
                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="updateStatus({{ $transaction->id }}, 'completed')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="updateStatus({{ $transaction->id }}, 'failed')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>Aucune transaction trouvée</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <div>
                Affichage de {{ $transactions->firstItem() ?? 0 }}-{{ $transactions->lastItem() ?? 0 }} sur {{ $transactions->total() }} transactions
            </div>
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateStatus(transactionId, newStatus) {
        if (!confirm('Êtes-vous sûr de vouloir mettre à jour le statut de cette transaction ?')) {
            return;
        }

        fetch(`/admin/transactions/${transactionId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload();
            } else {
                alert('Erreur : ' + data.message);
            }
        })
        .catch(error => {
            alert('Une erreur est survenue');
            console.error('Error:', error);
        });
    }

    function exportTransactions() {
        const currentUrl = new URL(window.location.href);
        const searchParams = currentUrl.searchParams;
        searchParams.append('export', 'true');
        window.location.href = currentUrl.toString();
    }
</script>
@endpush