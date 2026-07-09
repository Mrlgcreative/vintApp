@extends('app')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-8 text-center">
            <div id="payment-icon" class="mb-5">
                <div class="w-16 h-16 mx-auto rounded-full bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center">
                    <svg class="w-8 h-8 text-primary-600 dark:text-primary-400 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </div>
            </div>

            <h3 id="payment-title" class="text-lg font-bold text-gray-900 dark:text-white mb-2">Paiement en cours</h3>
            <p id="payment-message" class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                Confirmez sur votre téléphone
            </p>

            <div id="transaction-info" class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 mb-5 text-sm text-left space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-500">Montant</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($transaction->amount, 2) }} {{ $transaction->currency ?? 'USD' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Opérateur</span>
                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $transaction->provider ?? '...')) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Téléphone</span>
                    <span class="font-mono text-sm">{{ $transaction->phone ?? '...' }}</span>
                </div>
            </div>

            <div class="w-full h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full mb-5 overflow-hidden">
                <div id="progress-bar" class="h-full rounded-full bg-primary-500 dark:bg-primary-400 transition-all duration-500" style="width: 30%"></div>
            </div>

            <div id="payment-instructions" class="bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-100 dark:border-yellow-900/20 rounded-xl p-4 mb-5 text-sm text-yellow-800 dark:text-yellow-200">
                <div class="flex items-start gap-2.5">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-medium mb-1">Instructions</p>
                        <ol class="space-y-1 text-yellow-700 dark:text-yellow-300 list-decimal list-inside">
                            <li>Vérifiez votre téléphone</li>
                            <li>Entrez votre code PIN Mobile Money</li>
                            <li>Confirmez le paiement</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div id="payment-actions" class="flex gap-2 justify-center">
                <button onclick="checkStatus()" class="px-4 py-2 text-sm font-medium text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900/30 transition-colors">
                    <svg class="w-4 h-4 inline mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Actualiser
                </button>
                <a href="{{ route('home') }}" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Retour
                </a>
            </div>
        </div>
    </div>
</div>

<script>
const transactionId = {{ $transaction->id ?? 'null' }};
let pollingTimeout = null;
let pollingCount = 0;
const maxPollingAttempts = 120;
let pollingBackoff = 1;

document.addEventListener('DOMContentLoaded', function() {
    if (transactionId) startPolling();
});

function startPolling() {
    pollingCount = 0;
    pollingBackoff = 1;
    schedulePoll();
}

function schedulePoll() {
    const interval = 2000 * pollingBackoff;
    pollingTimeout = setTimeout(() => {
        checkStatus();
        pollingCount++;
        if (pollingCount >= maxPollingAttempts) {
            stopPolling();
            showTimeout();
        } else {
            schedulePoll();
        }
    }, interval);
}

function stopPolling() {
    if (pollingTimeout) {
        clearTimeout(pollingTimeout);
        pollingTimeout = null;
    }
}

async function checkStatus() {
    if (!transactionId) return;
    try {
        const response = await fetch(`/api/payment-callbacks/status?transaction_id=${transactionId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        });

        if (response.status === 429) {
            pollingBackoff = Math.min(pollingBackoff + 1, 5);
            return;
        }

        pollingBackoff = 1;
        const data = await response.json();
        if (data.status === 'success' && data.transaction) updateUI(data.transaction);
    } catch (e) {
        console.error(e);
    }
}

function updateUI(tx) {
    const status = tx.status;
    const icon = document.getElementById('payment-icon');
    const title = document.getElementById('payment-title');
    const msg = document.getElementById('payment-message');
    const inst = document.getElementById('payment-instructions');
    const actions = document.getElementById('payment-actions');
    const bar = document.getElementById('progress-bar');

    if (status === 'completed') {
        stopPolling();
        icon.innerHTML = '<div class="w-16 h-16 mx-auto rounded-full bg-green-100 dark:bg-green-900/20 flex items-center justify-center"><svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></div>';
        title.textContent = 'Paiement réussi';
        msg.textContent = 'Votre transaction a été confirmée.';
        inst.remove();
        bar.style.width = '100%';
        bar.className = 'h-full rounded-full bg-green-500 transition-all duration-500';
        const receiptUrl = '/payments/receipt/' + transactionId;
        const downloadUrl = '/payments/receipt/' + transactionId + '/download';
        actions.innerHTML = '<a href="' + receiptUrl + '" class="px-5 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">Voir le reçu</a>' +
            '<a href="' + downloadUrl + '" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors"><svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> Télécharger</a>';
        setTimeout(() => window.location.href = receiptUrl, 3000);
    } else if (status === 'failed') {
        stopPolling();
        window.location.href = '/payments/error?transaction_id=' + transactionId;
        return;
    } else {
        const progress = Math.min(30 + (pollingCount / maxPollingAttempts) * 60, 90);
        bar.style.width = progress + '%';
    }
}

function showTimeout() {
    const icon = document.getElementById('payment-icon');
    const title = document.getElementById('payment-title');
    const msg = document.getElementById('payment-message');
    const inst = document.getElementById('payment-instructions');
    const actions = document.getElementById('payment-actions');

    icon.innerHTML = '<div class="w-16 h-16 mx-auto rounded-full bg-yellow-100 dark:bg-yellow-900/20 flex items-center justify-center"><svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>';
    title.textContent = 'Délai dépassé';
    msg.textContent = 'Si vous avez confirmé sur votre téléphone, cliquez sur "J\'ai confirmé".';
    inst.remove();
    const useManualBtn = {{ app()->environment('local') ? 'true' : 'false' }};
    actions.innerHTML = `
        ${useManualBtn ? `<button onclick="confirmManually()" class="px-5 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
            J'ai confirmé
        </button>` : ''}
        <button onclick="location.reload()" class="px-4 py-2.5 text-sm font-medium text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900/30 transition-colors">
            Réessayer
        </button>
        <a href="/dashboard" class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            Tableau de bord
        </a>
    `;
}

async function confirmManually() {
    if (!confirm('Avez-vous vraiment confirmé le paiement sur votre téléphone ?\n\nCette action est irreversible.')) return;
    const btn = document.querySelector('#payment-actions button');
    if (btn) btn.disabled = true;
    try {
        const resp = await fetch('/api/payment-callbacks/' + transactionId + '/force-complete', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        });
        const data = await resp.json();
        if (data.status === 'success' && data.transaction) {
            updateUI(data.transaction);
        } else {
            alert(data.message || 'Erreur lors de la confirmation');
        }
    } catch (e) {
        alert('Erreur réseau. Veuillez réessayer.');
        console.error(e);
    }
    if (btn) btn.disabled = false;
}

window.addEventListener('beforeunload', stopPolling);
</script>
@endsection
