@extends('layouts.admin')

@section('title', 'Statistiques des transactions')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-semibold">Statistiques des transactions</h1>
        <a href="{{ route('admin.transactions.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">
            Retour à la liste
        </a>
    </div>

    <!-- Cartes de statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total des transactions -->
        <div class="bg-white dark:bg-gray-800 dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-primary-100 dark:bg-primary-900 rounded-md p-3">
                    <svg class="h-6 w-6 text-primary-600 dark:text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            Total des transactions
                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                {{ $stats['total_transactions'] }}
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Transactions en attente -->
        <div class="bg-white dark:bg-gray-800 dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 rounded-md p-3">
                    <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            En attente
                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                {{ $stats['pending_transactions'] }}
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Transactions complétées -->
        <div class="bg-white dark:bg-gray-800 dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-md p-3">
                    <svg class="h-6 w-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            Complétées
                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                {{ $stats['completed_transactions'] }}
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Total des transactions Mobile Money -->
        <div class="bg-white dark:bg-gray-800 dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-md p-3">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            Mobile Money
                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                {{ $stats['mobile_money_transactions'] }}
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et tableaux détaillés -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Transactions par méthode de paiement -->
        <div class="bg-white dark:bg-gray-800 dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                Transactions par méthode de paiement
            </h3>
            <div class="relative">
                <canvas id="paymentMethodChart"></canvas>
            </div>
        </div>

        <!-- Montant total aujourd'hui -->
        <div class="bg-white dark:bg-gray-800 dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                Montant total aujourd'hui
            </h3>
            <div class="text-4xl font-bold text-gray-900 dark:text-gray-100">
                {{ number_format($stats['total_amount_today'], 2) }} USD
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