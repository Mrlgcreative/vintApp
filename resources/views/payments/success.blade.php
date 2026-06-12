@extends('app')

@section('title', 'Paiement')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-md">
        @php
            $isPending = isset($transaction) && $transaction->status === 'pending';
            $isCompleted = isset($transaction) && $transaction->status === 'completed';
        @endphp

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-8 text-center">
            <div class="w-16 h-16 mx-auto rounded-full {{ $isPending ? 'bg-yellow-100 dark:bg-yellow-900/20' : 'bg-green-100 dark:bg-green-900/20' }} flex items-center justify-center mb-5">
                @if($isPending)
                    <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @else
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                @endif
            </div>

            @if($isPending)
                <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Paiement en attente</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Confirmez le paiement sur votre téléphone</p>
            @else
                <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Paiement réussi</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Transaction confirmée</p>
            @endif

            @if(isset($transaction))
                <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                    {{ number_format($transaction->amount, 2) }} {{ $transaction->currency ?? 'USD' }}
                </div>

                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 my-6 text-sm text-left space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Transaction</span>
                        <span class="font-mono text-xs text-gray-700 dark:text-gray-300">{{ $transaction->transaction_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Opérateur</span>
                        <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $transaction->provider)) }}</span>
                    </div>
                    @if($transaction->phone)
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Téléphone</span>
                        <span class="font-mono text-sm">+243 {{ $transaction->phone }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Date</span>
                        <span class="text-gray-700 dark:text-gray-300">{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Statut</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $isPending ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                            {{ $isPending ? 'En attente' : 'Confirmé' }}
                        </span>
                    </div>
                </div>

                <div class="space-y-2">
                    <a href="{{ route('dashboard') }}" class="block w-full py-2.5 text-sm font-medium text-white bg-primary-600 rounded-xl hover:bg-primary-700 transition-colors">
                        Tableau de bord
                    </a>
                    @if($isCompleted)
                    <button onclick="window.print()" class="block w-full py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Télécharger le reçu
                    </button>
                    @endif
                </div>
            @endif
        </div>

        @if($isPending)
        <div class="mt-4 text-center">
            <a href="{{ route('payments.status', $transaction->id) }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium">
                Voir le statut en temps réel
            </a>
        </div>
        @endif
    </div>
</div>

<style>
@media print {
    body { background: white !important; }
    .min-h-\[80vh\] { min-height: auto !important; }
    .shadow-lg { box-shadow: none !important; }
}
</style>
@endsection
