@extends('layouts.admin')

@section('title', 'Transactions')

@section('page-title', 'Gestion des Transactions')

@section('page-actions')
<div class="flex gap-3">
    <button type="button" onclick="exportTransactions()" 
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
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
        
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        Filtrer les transactions
                    </h3>
                    <form action="{{ route('admin.transactions.index') }}" method="GET" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Statut</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                <option value="">Tous</option>
                                <option value="pending" @if(request('status') == 'pending') selected @endif>En attente</option>
                                <option value="completed" @if(request('status') == 'completed') selected @endif>Complété</option>
                                <option value="failed" @if(request('status') == 'failed') selected @endif>Échoué</option>
                                <option value="refunded" @if(request('status') == 'refunded') selected @endif>Remboursé</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Méthode de paiement</label>
                            <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                <option value="">Toutes</option>
                                <option value="wallet" @if(request('payment_method') == 'wallet') selected @endif>Wallet</option>
                                <option value="airtel_money" @if(request('payment_method') == 'airtel_money') selected @endif>Airtel Money</option>
                                <option value="orange_money" @if(request('payment_method') == 'orange_money') selected @endif>Orange Money</option>
                                <option value="mpesa" @if(request('payment_method') == 'mpesa') selected @endif>M-Pesa</option>
                                <option value="afrimoney" @if(request('payment_method') == 'afrimoney') selected @endif>Afrimoney</option>
                            <option value="bank" @if(request('payment_method') == 'bank') selected @endif>Banque</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Date début</label>
                            <input type="date" name="start_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors" value="{{ request('start_date') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Date fin</label>
                            <input type="date" name="end_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors" value="{{ request('end_date') }}">
                        </div>
                    </div>
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 dark:bg-gray-900 transition-colors" onclick="toggleFilterModal()">Fermer</button>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">Appliquer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tableau des transactions -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Utilisateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Méthode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                    @forelse ($transactions as $transaction)
                        <tr class="hover:bg-gray-50 dark:bg-gray-900">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $transaction->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($transaction->user?->avatar)
                                        <img src="{{ $transaction->user->avatar_url }}" class="w-8 h-8 rounded-full mr-3" alt="Avatar">
                                    @else
                                        <div class="w-8 h-8 bg-gray-500 rounded-full mr-3 flex items-center justify-center">
                                            <span class="text-white text-sm font-medium">{{ $transaction->user?->initial ?? 'U' }}</span>
                                        </div>
                                    @endif
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $transaction->user?->name ?? 'Utilisateur supprimé' }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ number_format($transaction->amount, 2) }}
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $transaction->currency }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center text-sm text-gray-900 dark:text-white">
                                    @switch($transaction->payment_method)
                                        @case('wallet')
                                            <i class="fas fa-wallet text-blue-500 mr-2"></i>
                                            @break
                                        @case('airtel_money')
                                            <i class="fas fa-mobile-alt text-red-500 mr-2"></i>
                                            @break
                                        @case('orange_money')
                                            <i class="fas fa-mobile-alt text-orange-500 mr-2"></i>
                                            @break
                                        @case('mpesa')
                                            <i class="fas fa-mobile-alt text-green-500 mr-2"></i>
                                            @break
                                        @default
                                            <i class="fas fa-money-bill-wave text-gray-500 dark:text-gray-400 mr-2"></i>
                                    @endswitch
                                    {{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($transaction->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100')) }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $transaction->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.transactions.show', $transaction) }}" 
                                       class="inline-flex items-center px-3 py-1 border border-blue-300 text-blue-700 rounded-md hover:bg-blue-50 transition-colors">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($transaction->status === 'pending')
                                        <button type="button" 
                                                class="inline-flex items-center px-3 py-1 border border-green-300 text-green-700 rounded-md hover:bg-green-50 transition-colors" 
                                                onclick="updateStatus({{ $transaction->id }}, 'completed')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" 
                                                class="inline-flex items-center px-3 py-1 border border-red-300 text-red-700 rounded-md hover:bg-red-50 transition-colors" 
                                                onclick="updateStatus({{ $transaction->id }}, 'failed')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-4 text-gray-400"></i>
                                    <p class="text-lg font-medium">Aucune transaction trouvée</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-700 dark:text-gray-200">
                Affichage de {{ $transactions->firstItem() ?? 0 }}-{{ $transactions->lastItem() ?? 0 }} sur {{ $transactions->total() }} transactions
            </div>
            <div class="pagination-wrapper">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleFilterModal() {
        const modal = document.getElementById('filterModal');
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    // Fermer le modal en cliquant à l'extérieur
    document.getElementById('filterModal').addEventListener('click', function(e) {
        if (e.target === this) {
            toggleFilterModal();
        }
    });

    // Fermer le modal avec la touche Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('filterModal');
            if (!modal.classList.contains('hidden')) {
                toggleFilterModal();
            }
        }
    });

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