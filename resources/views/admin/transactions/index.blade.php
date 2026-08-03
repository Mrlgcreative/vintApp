@extends('layouts.admin')

@section('title', 'Transactions')

@section('page-title', 'Gestion des Transactions')

@section('page-actions')
<div class="flex flex-col sm:flex-row gap-2">
    <button type="button" onclick="exportTransactions()"
            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
        <i class="fas fa-download"></i>Exporter
    </button>
    <button type="button" onclick="toggleFilterModal()"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-filter"></i>Filtres
    </button>
</div>
@endsection

@section('content')
<!-- Filtres Modal -->
<div id="filterModal" class="fixed inset-0 z-50 hidden flex items-start justify-center overflow-y-auto bg-slate-900/60 p-4 backdrop-blur-sm sm:items-center">
    <div class="w-full max-w-lg animate-pop rounded-2xl bg-white shadow-xl ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-700">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Filtrer les transactions</h3>
            <button type="button" onclick="toggleFilterModal()"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition-colors hover:bg-slate-100 dark:hover:bg-slate-700">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <form action="{{ route('admin.transactions.index') }}" method="GET" class="space-y-4 p-5 sm:p-6">
            <div>
                <label for="status-filter" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Statut</label>
                <select name="status" id="status-filter"
                        class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">
                    <option value="">Tous</option>
                    <option value="pending" @if(request('status') == 'pending') selected @endif>En attente</option>
                    <option value="completed" @if(request('status') == 'completed') selected @endif>Complété</option>
                    <option value="failed" @if(request('status') == 'failed') selected @endif>Échoué</option>
                    <option value="refunded" @if(request('status') == 'refunded') selected @endif>Remboursé</option>
                </select>
            </div>
            <div>
                <label for="payment-method-filter" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Méthode de paiement</label>
                <select name="payment_method" id="payment-method-filter"
                        class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">
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
                    <label for="start-date-filter" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Date début</label>
                    <input type="date" name="start_date" id="start-date-filter"
                           class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors"
                           value="{{ request('start_date') }}">
                </div>
                <div>
                    <label for="end-date-filter" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Date fin</label>
                    <input type="date" name="end_date" id="end-date-filter"
                           class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors"
                           value="{{ request('end_date') }}">
                </div>
            </div>
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 pt-2">
                <button type="button" onclick="toggleFilterModal()"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    Fermer
                </button>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                    <i class="fas fa-check"></i>Appliquer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tableau des transactions -->
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-900">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Utilisateur</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Montant</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Méthode</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Statut</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                @forelse ($transactions as $transaction)
                    <tr class="border-t border-slate-100 transition-colors hover:bg-slate-50 dark:border-slate-700/50 dark:hover:bg-slate-700/30">
                        <td class="px-4 py-3 align-middle text-sm font-medium whitespace-nowrap text-slate-900 dark:text-white">{{ $transaction->id }}</td>
                        <td class="px-4 py-3 align-middle whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                @if($transaction->user?->avatar)
                                    <img src="{{ $transaction->user->avatar_url }}" class="h-8 w-8 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-600" alt="Avatar">
                                @else
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-primary-600 to-cyan-400">
                                        <span class="text-sm font-medium text-white">{{ $transaction->user?->initial ?? 'U' }}</span>
                                    </div>
                                @endif
                                <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $transaction->user?->name ?? 'Utilisateur supprimé' }}</div>
                            </div>
                        </td>
                        <td class="px-4 py-3 align-middle whitespace-nowrap">
                            <div class="text-sm font-semibold tabular-nums text-slate-900 dark:text-white">
                                {{ number_format($transaction->amount, 2) }}
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $transaction->currency }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 align-middle whitespace-nowrap">
                            <div class="flex items-center gap-2 text-sm text-slate-900 dark:text-white">
                                @switch($transaction->payment_method)
                                    @case('wallet')
                                        <i class="fas fa-wallet text-blue-500"></i>
                                        @break
                                    @case('airtel_money')
                                        <i class="fas fa-mobile-alt text-red-500"></i>
                                        @break
                                    @case('orange_money')
                                        <i class="fas fa-mobile-alt text-orange-500"></i>
                                        @break
                                    @case('mpesa')
                                        <i class="fas fa-mobile-alt text-green-500"></i>
                                        @break
                                    @default
                                        <i class="fas fa-money-bill-wave text-slate-500 dark:text-slate-400"></i>
                                @endswitch
                                {{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}
                            </div>
                        </td>
                        <td class="px-4 py-3 align-middle whitespace-nowrap">
                            @if($transaction->status === 'completed')
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            @elseif($transaction->status === 'pending')
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            @elseif($transaction->status === 'failed')
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-300">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 align-middle whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                            {{ $transaction->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3 align-middle whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.transactions.show', $transaction) }}"
                                   class="inline-flex items-center gap-1.5 rounded-lg border border-sky-300 px-2.5 py-1.5 text-xs font-medium text-sky-700 transition-colors hover:bg-sky-50 dark:border-sky-700 dark:text-sky-300 dark:hover:bg-sky-900/20">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($transaction->status === 'pending')
                                    <button type="button"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-300 px-2.5 py-1.5 text-xs font-medium text-emerald-700 transition-colors hover:bg-emerald-50 dark:border-emerald-700 dark:text-emerald-300 dark:hover:bg-emerald-900/20"
                                            onclick="updateStatus({{ $transaction->id }}, 'completed')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-red-300 px-2.5 py-1.5 text-xs font-medium text-red-700 transition-colors hover:bg-red-50 dark:border-red-700 dark:text-red-300 dark:hover:bg-red-900/20"
                                            onclick="updateStatus({{ $transaction->id }}, 'failed')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-3">
                            <div class="text-center py-12">
                                <i class="fas fa-inbox mb-3 text-4xl text-slate-200 dark:text-slate-600"></i>
                                <p class="text-sm text-slate-400">Aucune transaction trouvée</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex flex-col items-center justify-between gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row dark:border-slate-700">
        <div class="text-sm text-slate-600 dark:text-slate-300">
            Affichage de {{ $transactions->firstItem() ?? 0 }}-{{ $transactions->lastItem() ?? 0 }} sur {{ $transactions->total() }} transactions
        </div>
        <div class="pagination-wrapper">
            {{ $transactions->links() }}
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
