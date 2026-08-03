@extends('layouts.admin')

@section('title', 'Statistiques des transactions')

@section('page-title', 'Statistiques des transactions')

@section('page-actions')
<a href="{{ route('admin.transactions.index') }}"
   class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
    <i class="fas fa-arrow-left"></i>Retour à la liste
</a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Cartes de statistiques -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <!-- Total des transactions -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-300">
                    <i class="fas fa-arrow-right-arrow-left"></i>
                </div>
                <div class="min-w-0">
                    <p class="truncate text-sm font-medium text-slate-500 dark:text-slate-400">Total des transactions</p>
                    <p class="text-2xl font-bold tabular-nums text-slate-900 dark:text-white">{{ $stats['total_transactions'] }}</p>
                </div>
            </div>
        </div>

        <!-- Transactions en attente -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-300">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="min-w-0">
                    <p class="truncate text-sm font-medium text-slate-500 dark:text-slate-400">En attente</p>
                    <p class="text-2xl font-bold tabular-nums text-slate-900 dark:text-white">{{ $stats['pending_transactions'] }}</p>
                </div>
            </div>
        </div>

        <!-- Transactions complétées -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-300">
                    <i class="fas fa-circle-check"></i>
                </div>
                <div class="min-w-0">
                    <p class="truncate text-sm font-medium text-slate-500 dark:text-slate-400">Complétées</p>
                    <p class="text-2xl font-bold tabular-nums text-slate-900 dark:text-white">{{ $stats['completed_transactions'] }}</p>
                </div>
            </div>
        </div>

        <!-- Total des transactions Mobile Money -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-600 dark:bg-sky-900/20 dark:text-sky-300">
                    <i class="fas fa-mobile-screen-button"></i>
                </div>
                <div class="min-w-0">
                    <p class="truncate text-sm font-medium text-slate-500 dark:text-slate-400">Mobile Money</p>
                    <p class="text-2xl font-bold tabular-nums text-slate-900 dark:text-white">{{ $stats['mobile_money_transactions'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et tableaux détaillés -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Transactions par méthode de paiement -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                <h3 class="font-semibold text-slate-900 dark:text-white">Transactions par méthode de paiement</h3>
            </div>
            <div class="p-5 sm:p-6">
                <div class="relative h-80">
                    <canvas id="paymentMethodChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Montant total aujourd'hui -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                <h3 class="font-semibold text-slate-900 dark:text-white">Montant total aujourd'hui</h3>
            </div>
            <div class="flex h-full flex-col items-center justify-center p-5 sm:p-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-300">
                    <i class="fas fa-sack-dollar text-2xl"></i>
                </div>
                <div class="mt-4 text-4xl font-bold tabular-nums text-slate-900 dark:text-white">
                    {{ number_format($stats['total_amount_today'], 2) }} USD
                </div>
                <p class="mt-2 text-sm text-slate-400">Transactions réalisées aujourd'hui</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données pour le graphique des méthodes de paiement
    const paymentMethodData = @json($stats['transactions_by_payment_method']);

    // Configuration du graphique des méthodes de paiement
    const paymentMethodChart = new Chart(
        document.getElementById('paymentMethodChart'),
        {
            type: 'doughnut',
            data: {
                labels: paymentMethodData.map(item => item.payment_method),
                datasets: [{
                    data: paymentMethodData.map(item => item.count),
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)', // blue
                        'rgba(16, 185, 129, 0.8)', // green
                        'rgba(245, 158, 11, 0.8)', // yellow
                        'rgba(239, 68, 68, 0.8)',  // red
                        'rgba(139, 92, 246, 0.8)'  // purple
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
                        }
                    }
                }
            }
        }
    );

    // Mettre à jour les couleurs du graphique en mode sombre
    if (document.documentElement.classList.contains('dark')) {
        Chart.defaults.color = '#fff';
    }
});
</script>
@endpush
