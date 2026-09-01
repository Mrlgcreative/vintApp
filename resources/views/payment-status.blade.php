@extends('app')

@section('title', 'Suivi du paiement — ' . config('app.name', 'VintApp'))

@section('content')
@php
    $initialStatus = $transaction->status ?? 'pending';
    $providerName = match ($transaction->provider ?? '') {
        'orange_money' => 'Orange Money',
        'airtel_money' => 'Airtel Money',
        'mpesa' => 'M-Pesa',
        'illicocash' => 'IllicoCash',
        'africell' => 'Africell Money',
        'pawapay' => 'PawaPay',
        'afribapay' => 'AFRIBAPAY',
        'cinetpay' => 'CinetPay',
        default => ucfirst(str_replace('_', ' ', $transaction->provider ?? '')),
    };
    $title = match ($initialStatus) {
        'completed' => 'Paiement réussi',
        'failed', 'cancelled' => 'Paiement échoué',
        default => 'Paiement en cours',
    };
    $message = match ($initialStatus) {
        'completed' => 'Votre transaction a été confirmée.',
        'failed', 'cancelled' => 'Le paiement n\'a pas pu être effectué. Vérifiez votre solde et réessayez.',
        default => 'Confirmez la demande sur votre téléphone',
    };
    $step1 = 'done'; // Demande envoyée : toujours acquise
    $step2 = in_array($initialStatus, ['completed', 'failed', 'cancelled']) ? 'done' : 'active';
    $step3 = $initialStatus === 'completed' ? 'done' : ($initialStatus === 'failed' || $initialStatus === 'cancelled' ? 'failed' : 'todo');
    $steps = [
        1 => ['label' => 'Demande envoyée', 'state' => $step1],
        2 => ['label' => 'Confirmation', 'state' => $step2],
        3 => ['label' => 'Validée', 'state' => $step3],
    ];
    $stepClasses = [
        'done' => 'bg-green-100 text-green-600 dark:bg-green-500/10 dark:text-green-400',
        'active' => 'bg-vinted-primary-100 text-vinted-primary-600 ring-4 ring-vinted-primary-100 dark:bg-vinted-primary-500/10 dark:text-vinted-primary-400 dark:ring-vinted-primary-500/20',
        'failed' => 'bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400',
        'todo' => 'border border-gray-300 dark:border-gray-700 text-gray-400 dark:text-gray-500',
    ];
    $stepLabelClasses = [
        'done' => 'text-green-700 dark:text-green-400',
        'active' => 'text-vinted-primary-700 dark:text-vinted-primary-400',
        'failed' => 'text-red-700 dark:text-red-400',
        'todo' => 'text-gray-500 dark:text-gray-500',
    ];
    $stepIcon = function ($state, $num) {
        if ($state === 'done') {
            return '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';
        }
        if ($state === 'active') {
            return '<span class="h-2 w-2 rounded-full bg-current animate-pulse"></span>';
        }
        if ($state === 'failed') {
            return '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>';
        }
        return $num;
    };
@endphp

<div class="min-h-[70vh] flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-md">
        <div class="mb-4 text-center">
            <p class="inline-flex items-center gap-2 rounded-full border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 px-3 py-1 text-xs font-medium text-gray-500 dark:text-gray-400 shadow-sm">
                Suivi du paiement
                <span class="text-gray-300 dark:text-gray-600">·</span>
                <span class="font-mono">#{{ $transaction->id }}</span>
            </p>
        </div>

        <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm p-6 sm:p-8 text-center">

            {{-- Icône d'état --}}
            <div id="payment-icon" class="mb-5">
                @if($initialStatus === 'pending')
                    <div class="w-16 h-16 mx-auto rounded-full bg-vinted-primary-50 dark:bg-vinted-primary-500/10 flex items-center justify-center">
                        <svg class="w-8 h-8 text-vinted-primary-600 dark:text-vinted-primary-400 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </div>
                @elseif($initialStatus === 'completed')
                    <div class="w-16 h-16 mx-auto rounded-full bg-green-100 dark:bg-green-500/10 flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                @elseif($initialStatus === 'failed')
                    <div class="w-16 h-16 mx-auto rounded-full bg-red-100 dark:bg-red-500/10 flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                @else
                    <div class="w-16 h-16 mx-auto rounded-full bg-yellow-100 dark:bg-yellow-500/10 flex items-center justify-center">
                        <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Badge de statut --}}
            <div id="payment-badge" class="mb-3">
                @if($initialStatus === 'completed')
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-green-200 dark:border-green-500/30 bg-green-50 dark:bg-green-500/10 px-3 py-1 text-xs font-medium text-green-700 dark:text-green-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span> Payé
                    </span>
                @elseif($initialStatus === 'failed' || $initialStatus === 'cancelled')
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-red-200 dark:border-red-500/30 bg-red-50 dark:bg-red-500/10 px-3 py-1 text-xs font-medium text-red-700 dark:text-red-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Échoué
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-vinted-primary-200 dark:border-vinted-primary-500/30 bg-vinted-primary-50 dark:bg-vinted-primary-500/10 px-3 py-1 text-xs font-medium text-vinted-primary-700 dark:text-vinted-primary-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-vinted-primary-500 animate-pulse"></span> En attente
                    </span>
                @endif
            </div>

            <h3 id="payment-title" class="text-lg font-semibold tracking-tight text-gray-900 dark:text-white mb-2">{{ $title }}</h3>
            <p id="payment-message" class="text-sm text-gray-500 dark:text-gray-400 mb-6">{{ $message }}</p>

            {{-- Montant --}}
            <div class="mb-6">
                <p class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                    {{ number_format($transaction->amount, 2) }} <span class="text-base font-semibold text-gray-500 dark:text-gray-400">{{ strtoupper($transaction->currency ?? 'USD') }}</span>
                </p>
            </div>

            {{-- Stepper shadcn --}}
            <div class="mb-6">
                <div class="relative">
                    <div class="absolute top-4 left-[16.66%] right-[16.66%] h-0.5 rounded-full bg-gray-100 dark:bg-gray-800"></div>
                    <div class="relative grid grid-cols-3 gap-2">
                        @foreach($steps as $num => $step)
                            <div class="flex flex-col items-center gap-1.5">
                                <div id="step-circle-{{ $num }}" class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold {{ $stepClasses[$step['state']] }}">
                                    {!! $stepIcon($step['state'], $num) !!}
                                </div>
                                <span id="step-label-{{ $num }}" class="text-[10px] sm:text-xs font-medium text-center leading-tight {{ $stepLabelClasses[$step['state']] }}">{{ $step['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Détails --}}
            <div id="transaction-info" class="rounded-lg border border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-950/50 divide-y divide-gray-100 dark:divide-gray-800 mb-5 text-sm text-left">
                <div class="flex justify-between gap-4 px-4 py-2.5">
                    <span class="text-gray-500 dark:text-gray-400">Opérateur</span>
                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $providerName }}</span>
                </div>
                @if(!empty($transaction->phone))
                    <div class="flex justify-between gap-4 px-4 py-2.5">
                        <span class="text-gray-500 dark:text-gray-400">Téléphone</span>
                        <span class="font-mono text-gray-900 dark:text-gray-100">{{ $transaction->phone }}</span>
                    </div>
                @endif
                @if(!empty($transaction->transaction_ref))
                    <div class="flex justify-between gap-4 px-4 py-2.5">
                        <span class="text-gray-500 dark:text-gray-400">Référence</span>
                        <span class="font-mono text-xs text-gray-900 dark:text-gray-100 truncate">{{ $transaction->transaction_ref }}</span>
                    </div>
                @endif
                <div class="flex justify-between gap-4 px-4 py-2.5">
                    <span class="text-gray-500 dark:text-gray-400">Date</span>
                    <span class="text-gray-900 dark:text-gray-100">{{ $transaction->created_at ? $transaction->created_at->format('d/m/Y H:i') : '—' }}</span>
                </div>
            </div>

            {{-- Minuteur 3 minutes --}}
            <div id="timer-wrap" class="mb-5" @if($initialStatus !== 'pending') style="display:none" @endif>
                <div class="relative w-20 h-20 mx-auto">
                    <svg class="w-20 h-20 -rotate-90" viewBox="0 0 80 80">
                        <circle cx="40" cy="40" r="34" fill="none" stroke-width="5" class="stroke-gray-100 dark:stroke-gray-800"></circle>
                        <circle id="timer-ring" cx="40" cy="40" r="34" fill="none" stroke-width="5" stroke-linecap="round"
                                class="stroke-vinted-primary-500 dark:stroke-vinted-primary-400"
                                stroke-dasharray="213.6" stroke-dashoffset="0" style="transition: stroke-dashoffset 1s linear;"></circle>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span id="timer-text" class="text-lg font-bold tabular-nums text-gray-900 dark:text-white">3:00</span>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Temps restant avant confirmation automatique</p>
            </div>

            {{-- Instructions --}}
            <div id="payment-instructions" @if($initialStatus !== 'pending') style="display:none" @endif class="rounded-lg border border-yellow-200 dark:border-yellow-500/20 bg-yellow-50 dark:bg-yellow-500/5 p-4 mb-5 text-sm text-left text-yellow-800 dark:text-yellow-200">
                <div class="flex items-start gap-2.5">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-medium mb-1.5">Comment procéder</p>
                        <ol class="space-y-1 text-yellow-700 dark:text-yellow-300 list-decimal list-inside">
                            <li>Vérifiez votre téléphone ({{ $providerName }})</li>
                            <li>Entrez votre code PIN pour confirmer</li>
                            <li>Le paiement sera confirmé automatiquement</li>
                        </ol>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div id="payment-actions" class="flex flex-col sm:flex-row gap-2 justify-center">
                <button onclick="checkStatus()" class="inline-flex items-center justify-center gap-2 h-10 px-4 rounded-md text-sm font-medium bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-700 dark:text-vinted-primary-400 hover:bg-vinted-primary-100 dark:hover:bg-vinted-primary-500/20 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Actualiser
                </button>
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center h-10 px-4 rounded-md text-sm font-medium border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</div>

<script>
const transactionId = {{ $transaction->id ?? 'null' }};
const initialStatus = @json($initialStatus);
const totalSeconds = 180;
let timeLeft = totalSeconds;
let timerInterval = null;
let pollingTimeout = null;
let pollingCount = 0;
const maxPollingAttempts = 120;
let pollingBackoff = 1;

function formatCountdown(seconds) {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return `${m}:${String(s).padStart(2, '0')}`;
}

function startTimer() {
    timeLeft = totalSeconds;
    updateTimer();
    timerInterval = setInterval(() => {
        timeLeft--;
        updateTimer();
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            timerInterval = null;
            stopPolling();
            handleTimeout();
        }
    }, 1000);
}

function updateTimer() {
    const text = document.getElementById('timer-text');
    const ring = document.getElementById('timer-ring');
    if (!text || !ring) return;
    text.textContent = formatCountdown(Math.max(timeLeft, 0));
    const circumference = 213.6;
    ring.style.strokeDashoffset = (circumference * (1 - Math.max(timeLeft, 0) / totalSeconds)).toFixed(1);
    if (timeLeft <= 30) {
        text.classList.add('text-red-500', 'dark:text-red-400');
        ring.classList.remove('stroke-vinted-primary-500', 'dark:stroke-vinted-primary-400');
        ring.classList.add('stroke-red-500', 'dark:stroke-red-400');
    }
}

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
            handleTimeout();
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

document.addEventListener('DOMContentLoaded', function() {
    if (initialStatus !== 'pending') {
        renderState(initialStatus);
        return;
    }
    if (transactionId) {
        startPolling();
        startTimer();
    }
});

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
        if (data.status === 'success' && data.transaction) renderState(data.transaction.status);
    } catch (e) {
        console.error(e);
    }
}

function renderSteps(status) {
    const order = status === 'completed' ? ['done', 'done', 'done']
        : (status === 'failed' || status === 'cancelled') ? ['done', 'done', 'failed']
        : ['done', 'active', 'todo'];

    const circleBase = ['w-8', 'h-8', 'rounded-full', 'flex', 'items-center', 'justify-center', 'text-sm', 'font-semibold'];
    const circleState = {
        done: ['bg-green-100', 'text-green-600', 'dark:bg-green-500/10', 'dark:text-green-400'],
        active: ['bg-vinted-primary-100', 'text-vinted-primary-600', 'ring-4', 'ring-vinted-primary-100', 'dark:bg-vinted-primary-500/10', 'dark:text-vinted-primary-400', 'dark:ring-vinted-primary-500/20'],
        failed: ['bg-red-100', 'text-red-600', 'dark:bg-red-500/10', 'dark:text-red-400'],
        todo: ['border', 'border-gray-300', 'dark:border-gray-700', 'text-gray-400', 'dark:text-gray-500'],
    };
    const labelState = {
        done: ['text-green-700', 'dark:text-green-400'],
        active: ['text-vinted-primary-700', 'dark:text-vinted-primary-400'],
        failed: ['text-red-700', 'dark:text-red-400'],
        todo: ['text-gray-500', 'dark:text-gray-500'],
    };
    const icons = {
        done: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>',
        active: '<span class="h-2 w-2 rounded-full bg-current animate-pulse"></span>',
        failed: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>',
        todo: '',
    };

    order.forEach((state, i) => {
        const circle = document.getElementById('step-circle-' + (i + 1));
        const label = document.getElementById('step-label-' + (i + 1));
        if (!circle) return;

        circle.className = [...new Set([...circleBase, ...circleState[state]])].join(' ');
        circle.innerHTML = icons[state] || (i + 1);

        if (label) {
            label.className = 'text-[10px] sm:text-xs font-medium text-center leading-tight ' + labelState[state].join(' ');
        }
    });
}

function renderState(status, statusMessage) {
    const icon = document.getElementById('payment-icon');
    const badge = document.getElementById('payment-badge');
    const title = document.getElementById('payment-title');
    const msg = document.getElementById('payment-message');
    const inst = document.getElementById('payment-instructions');
    const actions = document.getElementById('payment-actions');
    const timerWrap = document.getElementById('timer-wrap');

    renderSteps(status);

    if (status === 'completed') {
        stopPolling();
        if (timerInterval) { clearInterval(timerInterval); timerInterval = null; }
        timerWrap.style.display = 'none';
        icon.innerHTML = '<div class="w-16 h-16 mx-auto rounded-full bg-green-100 dark:bg-green-500/10 flex items-center justify-center"><svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></div>';
        badge.innerHTML = '<span class="inline-flex items-center gap-1.5 rounded-full border border-green-200 dark:border-green-500/30 bg-green-50 dark:bg-green-500/10 px-3 py-1 text-xs font-medium text-green-700 dark:text-green-400"><span class="h-1.5 w-1.5 rounded-full bg-green-500"></span> Payé</span>';
        title.textContent = 'Paiement réussi';
        msg.textContent = 'Votre transaction a été confirmée.';
        inst.style.display = 'none';
        const receiptUrl = '/payments/receipt/' + transactionId;
        const downloadUrl = receiptUrl + '/download';
        actions.classList.remove('flex-col');
        actions.innerHTML = '<a href="' + receiptUrl + '" class="inline-flex items-center justify-center h-10 px-5 rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition-colors">Voir le reçu</a>' +
            '<a href="' + downloadUrl + '" class="inline-flex items-center justify-center gap-1.5 h-10 px-5 rounded-md text-sm font-medium border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> Télécharger</a>';
        setTimeout(() => window.location.href = receiptUrl, 3000);
    } else if (status === 'failed' || status === 'cancelled') {
        stopPolling();
        if (timerInterval) { clearInterval(timerInterval); timerInterval = null; }
        timerWrap.style.display = 'none';
        icon.innerHTML = '<div class="w-16 h-16 mx-auto rounded-full bg-red-100 dark:bg-red-500/10 flex items-center justify-center"><svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></div>';
        badge.innerHTML = '<span class="inline-flex items-center gap-1.5 rounded-full border border-red-200 dark:border-red-500/30 bg-red-50 dark:bg-red-500/10 px-3 py-1 text-xs font-medium text-red-700 dark:text-red-400"><span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Échoué</span>';
        title.textContent = 'Paiement échoué';
        msg.textContent = statusMessage || 'Le paiement n\'a pas pu être effectué. Vérifiez votre solde et réessayez.';
        inst.style.display = 'none';
        actions.classList.remove('flex-col');
        actions.innerHTML = '<a href="/payments/error?transaction_id=' + transactionId + '" class="inline-flex items-center justify-center h-10 px-5 rounded-md text-sm font-medium text-white bg-vinted-primary-600 hover:bg-vinted-primary-700 transition-colors">Réessayer le paiement</a>' +
            '<a href="/dashboard" class="inline-flex items-center justify-center h-10 px-5 rounded-md text-sm font-medium border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Tableau de bord</a>';
    } else {
        return;
    }
}

function handleTimeout() {
    const title = document.getElementById('payment-title');
    const msg = document.getElementById('payment-message');
    if (title) title.textContent = 'Délai dépassé';
    if (msg) msg.textContent = 'Le délai de confirmation de 3 minutes a expiré.';

    if (!transactionId) {
        renderState('failed');
        return;
    }

    fetch('/api/payment-callbacks/' + transactionId + '/timeout', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.transaction) {
                if (data.transaction.status === 'completed') {
                    renderState('completed');
                } else {
                    renderState('failed', 'Le délai de confirmation de 3 minutes a expiré. La transaction a été annulée.');
                }
            } else {
                renderState('failed', 'Le délai de confirmation de 3 minutes a expiré. La transaction a été annulée.');
            }
        })
        .catch(() => {
            renderState('failed', 'Le délai de confirmation de 3 minutes a expiré. La transaction a été annulée.');
        });
}

window.addEventListener('beforeunload', stopPolling);
</script>
@endsection