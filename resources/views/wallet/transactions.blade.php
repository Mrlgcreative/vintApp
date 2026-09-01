@extends('app')

@section('title', 'Historique des transactions - ' . $wallet->currency)

@section('content')
<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 py-8 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <!-- En-tête -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="flex items-center gap-2.5 text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-vinted-primary-50 text-vinted-primary-500 dark:bg-vinted-primary-500/10">
                        <i class="fas fa-clock-rotate-left"></i>
                    </div>
                    Historique des transactions
                </h1>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Wallet {{ $wallet->currency }} - Solde actuel :
                    <strong class="text-zinc-900 dark:text-white">
                        @if($wallet->currency === 'CDF')
                            {{ number_format($wallet->balance, 2, ',', ' ') }} FC
                        @else
                            ${{ number_format($wallet->balance, 2, '.', ',') }}
                        @endif
                    </strong>
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('wallet.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-700">
                    <i class="fas fa-plus"></i>Ajouter
                </a>
                <a href="{{ route('wallet.withdraw-funds', $wallet) }}" class="inline-flex items-center gap-2 rounded-lg border border-red-200 px-4 py-2 text-sm font-medium text-red-600 transition-colors hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-500/10">
                    <i class="fas fa-minus"></i>Retirer
                </a>
                <a href="{{ route('wallet.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-zinc-200 px-4 py-2 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">
                    <i class="fas fa-arrow-left"></i>Retour
                </a>
            </div>
        </div>

        <!-- Statistiques rapides -->
        @if($transactions->total() > 0)
            <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div class="rounded-xl border border-zinc-200 bg-white p-4 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <i class="fas fa-receipt mb-2 text-2xl text-vinted-primary-500"></i>
                    <h5 class="mb-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $transactions->total() }}</h5>
                    <small class="text-zinc-500 dark:text-zinc-400">Transactions totales</small>
                </div>
                <div class="rounded-xl border border-zinc-200 bg-white p-4 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <i class="fas fa-plus mb-2 text-2xl text-emerald-500"></i>
                    <h5 class="mb-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $wallet->transactions()->where('type', 'credit')->count() }}</h5>
                    <small class="text-zinc-500 dark:text-zinc-400">Crédits</small>
                </div>
                <div class="rounded-xl border border-zinc-200 bg-white p-4 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <i class="fas fa-minus mb-2 text-2xl text-red-500"></i>
                    <h5 class="mb-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $wallet->transactions()->where('type', 'debit')->count() }}</h5>
                    <small class="text-zinc-500 dark:text-zinc-400">Débits</small>
                </div>
                <div class="rounded-xl border border-zinc-200 bg-white p-4 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <i class="fas fa-coins mb-2 text-2xl text-amber-500"></i>
                    <h5 class="mb-1 text-2xl font-bold text-zinc-900 dark:text-white">
                        @if($wallet->currency === 'CDF')
                            {{ number_format($wallet->balance, 0, ',', ' ') }}
                        @else
                            {{ number_format($wallet->balance, 0, '.', ',') }}
                        @endif
                    </h5>
                    <small class="text-zinc-500 dark:text-zinc-400">Solde {{ $wallet->currency }}</small>
                </div>
            </div>
        @endif

        <!-- Liste des transactions -->
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center justify-between border-b border-zinc-200 px-6 py-4 dark:border-zinc-800">
                <h5 class="flex items-center gap-2 text-lg font-semibold text-zinc-900 dark:text-white">
                    <i class="fas fa-list text-vinted-primary-500"></i>
                    Transactions
                </h5>
                @if($transactions->total() > 0)
                    <span class="rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                        {{ $transactions->firstItem() }} - {{ $transactions->lastItem() }}
                        sur {{ $transactions->total() }}
                    </span>
                @endif
            </div>

            <div class="p-0">
                @if($transactions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-800/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Date & Heure</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Type</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Description</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 hidden lg:table-cell">Référence</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Montant</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Solde après</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                @foreach($transactions as $transaction)
                                    <tr class="transition-colors duration-200 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-zinc-900 dark:text-white">{{ $transaction->created_at->format('d/m/Y') }}</div>
                                            <small class="text-zinc-500 dark:text-zinc-400">{{ $transaction->created_at->format('H:i:s') }}</small>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($transaction->type === 'credit')
                                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-400">
                                                    <i class="fas fa-plus text-[10px]"></i>Crédit
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-800 dark:bg-red-500/10 dark:text-red-400">
                                                    <i class="fas fa-minus text-[10px]"></i>Débit
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-zinc-900 dark:text-white">{{ $transaction->description }}</div>
                                            @if($transaction->reference)
                                                <small class="text-zinc-500 dark:text-zinc-400">Réf: {{ $transaction->reference }}</small>
                                            @endif
                                        </td>
                                        <td class="hidden px-6 py-4 lg:table-cell">
                                            @if($transaction->reference)
                                                <code class="rounded bg-zinc-100 px-1.5 py-0.5 text-xs text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $transaction->reference }}</code>
                                            @else
                                                <span class="text-zinc-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="font-bold {{ $transaction->type === 'credit' ? 'text-emerald-600' : 'text-red-600' }}">
                                                {{ $transaction->type === 'credit' ? '+' : '-' }}
                                                @if($wallet->currency === 'CDF')
                                                    {{ number_format($transaction->amount, 2, ',', ' ') }} FC
                                                @else
                                                    ${{ number_format($transaction->amount, 2, '.', ',') }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right text-zinc-500 dark:text-zinc-400">
                                            @if($wallet->currency === 'CDF')
                                                {{ number_format($transaction->balance_after, 2, ',', ' ') }} FC
                                            @else
                                                ${{ number_format($transaction->balance_after, 2, '.', ',') }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($transactions->hasPages())
                        <div class="flex justify-center border-t border-zinc-200 p-3 dark:border-zinc-800">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                @else
                    <div class="py-5 text-center">
                        <i class="fas fa-receipt mb-3 text-4xl text-zinc-300 dark:text-zinc-600"></i>
                        <h5 class="text-lg font-semibold text-zinc-700 dark:text-zinc-200">Aucune transaction</h5>
                        <p class="mb-4 text-zinc-500 dark:text-zinc-400">Ce wallet n'a encore aucune transaction.</p>
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('wallet.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-700">
                                <i class="fas fa-plus"></i>
                                Ajouter des fonds {{ $wallet->currency }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions rapides -->
        @if($transactions->count() > 0)
            <div class="mt-4 rounded-xl border border-zinc-200 bg-white px-6 py-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h6 class="mb-3 flex items-center gap-2 font-semibold text-vinted-primary-600 dark:text-vinted-primary-400">
                    <i class="fas fa-bolt"></i>
                    Actions rapides
                </h6>
                <div class="grid grid-cols-1 gap-2 md:grid-cols-3">
                    <a href="{{ route('wallet.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-emerald-200 px-4 py-2 text-sm font-medium text-emerald-700 transition-colors hover:bg-emerald-50 dark:border-emerald-500/30 dark:text-emerald-400 dark:hover:bg-emerald-500/10">
                        <i class="fas fa-plus"></i>Ajouter des fonds
                    </a>
                    <a href="{{ route('wallet.withdraw-funds', $wallet) }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 px-4 py-2 text-sm font-medium text-red-600 transition-colors hover:bg-red-50 dark:border-red-500/30 dark:text-red-400 dark:hover:bg-red-500/10">
                        <i class="fas fa-minus"></i>Retirer des fonds
                    </a>
                    <button class="inline-flex items-center justify-center gap-2 rounded-lg border border-vinted-primary-200 px-4 py-2 text-sm font-medium text-vinted-primary-600 transition-colors hover:bg-vinted-primary-50 dark:border-vinted-primary-500/30 dark:text-vinted-primary-400 dark:hover:bg-vinted-primary-500/10" onclick="window.print()">
                        <i class="fas fa-print"></i>Imprimer l'historique
                    </button>
                </div>
            </div>
        @endif
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
    background-color: transparent;
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
    const statCards = document.querySelectorAll('.grid .rounded-xl');
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
