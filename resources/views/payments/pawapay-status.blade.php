@extends('app')

@section('title', 'Statut du paiement')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 via-purple-500 to-fuchsia-600 text-white px-6 py-6">
            <h4 class="text-xl font-bold flex items-center">
                <span class="bg-white/20 p-2 rounded-lg mr-3">
                    <i class="fas fa-hourglass-half text-white"></i>
                </span>
                Paiement en cours
            </h4>
            <p class="text-purple-100 text-sm mt-1">
                Veuillez confirmer la transaction sur votre téléphone
            </p>
        </div>

        <div class="p-6 sm:p-8">
            <!-- Statut en attente -->
            <div id="status-pending" class="text-center py-6">
                <div class="mx-auto w-16 h-16 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-spinner fa-spin text-purple-600 dark:text-purple-400 text-2xl"></i>
                </div>
                <h5 class="font-semibold text-gray-900 dark:text-white text-lg">En attente de confirmation</h5>
                <p class="text-sm text-gray-500 mt-1">Une demande de paiement a été envoyée à votre numéro.<br>Confirmez-la depuis votre téléphone.</p>
            </div>

            <!-- Statut complété -->
            <div id="status-completed" class="hidden text-center py-6">
                <div class="mx-auto w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-3xl"></i>
                </div>
                <h5 class="font-semibold text-green-700 dark:text-green-300 text-lg">Paiement confirmé !</h5>
                <p class="text-sm text-gray-500 mt-1">Votre paiement de {{ number_format($transaction->amount, 2) }} {{ $transaction->currency }} a été accepté.</p>
                <a href="{{ route('payments.receipt', $transaction->id) }}"
                   class="mt-5 inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-receipt mr-2"></i>Voir le reçu
                </a>
            </div>

            <!-- Statut échoué -->
            <div id="status-failed" class="hidden text-center py-6">
                <div class="mx-auto w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-times-circle text-red-600 dark:text-red-400 text-3xl"></i>
                </div>
                <h5 class="font-semibold text-red-700 dark:text-red-300 text-lg">Paiement échoué</h5>
                <p class="text-sm text-gray-500 mt-1">La transaction n'a pas pu être complétée. Veuillez réessayer.</p>
                <a href="{{ route('cart.checkout') }}"
                   class="mt-5 inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-shopping-cart mr-2"></i>Retour au checkout
                </a>
            </div>

            <!-- Détails de la transaction -->
            <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-900 rounded-xl">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Montant</div>
                        <div class="font-mono font-semibold text-gray-900 dark:text-white mt-0.5">
                            {{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}
                        </div>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Référence</div>
                        <div class="font-mono text-xs text-gray-900 dark:text-white mt-0.5">{{ $transaction->transaction_id }}</div>
                    </div>
                    <div class="col-span-2">
                        <div class="text-gray-500 dark:text-gray-400">Statut</div>
                        <div id="status-label" class="font-semibold text-purple-600 dark:text-purple-400 mt-0.5 capitalize">{{ $transaction->status }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const transactionId = {{ $transaction->id }};
    const checkUrl = '{{ route("payments.pawapay.check-status", ":id") }}'.replace(':id', transactionId);
    const statusLabel = document.getElementById('status-label');
    let checked = false;

    function showOnly(id) {
        document.getElementById('status-pending').classList.toggle('hidden', id !== 'pending');
        document.getElementById('status-completed').classList.toggle('hidden', id !== 'completed');
        document.getElementById('status-failed').classList.toggle('hidden', id !== 'failed');
    }

    function poll() {
        if (checked) return;

        fetch(checkUrl, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            const status = data.status || 'pending';
            statusLabel.textContent = status;
            statusLabel.className = 'font-semibold capitalize mt-0.5 ' +
                (status === 'completed' ? 'text-green-600 dark:text-green-400' :
                 status === 'failed' ? 'text-red-600 dark:text-red-400' : 'text-purple-600 dark:text-purple-400');

            if (status === 'completed') {
                checked = true;
                showOnly('completed');
            } else if (status === 'failed') {
                checked = true;
                showOnly('failed');
            }
        })
        .catch(() => {});
    }

    poll();
    setInterval(poll, 3000);
})();
</script>
@endpush
@endsection
