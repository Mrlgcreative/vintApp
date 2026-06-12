@extends('app')

@section('title', 'Reçu - ' . $transaction->receipt_number)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8 px-4">
    <div class="max-w-lg mx-auto">

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden" id="receipt">
            {{-- En-tête reçu --}}
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-5 text-center">
                <div class="text-2xl font-bold tracking-tight">
                    {{ config('app.name', 'VintApp') }}
                </div>
                <p class="text-primary-100 text-xs mt-0.5">Reçu de paiement électronique</p>
            </div>

            <div class="p-6">
                {{-- Statut --}}
                <div class="text-center mb-5">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        Paiement confirmé
                    </div>
                </div>

                {{-- Montant --}}
                <div class="text-center mb-5">
                    <p class="text-xs text-gray-400 uppercase tracking-wider">Montant payé</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">
                        {{ number_format($transaction->amount, 2) }}
                        <span class="text-lg text-gray-500">{{ $transaction->currency }}</span>
                    </p>
                </div>

                {{-- Ligne séparation --}}
                <div class="border-t border-dashed border-gray-200 dark:border-gray-700 my-4"></div>

                {{-- Détails --}}
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">N° Reçu</span>
                        <span class="font-mono font-semibold text-gray-900 dark:text-white text-xs">{{ $transaction->receipt_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Transaction</span>
                        <span class="font-mono text-xs text-gray-700 dark:text-gray-300">{{ $transaction->transaction_id ?? $transaction->id }}</span>
                    </div>
                    @if($transaction->provider)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Opérateur</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $transaction->provider)) }}</span>
                    </div>
                    @endif
                    @if($transaction->phone)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Téléphone</span>
                        <span class="font-mono text-gray-700 dark:text-gray-300">+243 {{ $transaction->phone }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-500">Date</span>
                        <span class="text-gray-700 dark:text-gray-300">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Devise</span>
                        <span class="text-gray-700 dark:text-gray-300">{{ $transaction->currency }}</span>
                    </div>
                </div>

                {{-- Signature --}}
                <div class="border-t border-dashed border-gray-200 dark:border-gray-700 my-4"></div>
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-3">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <div class="min-w-0">
                            <p class="text-[11px] text-gray-400 font-medium uppercase tracking-wider">Signature numérique</p>
                            <p class="text-[10px] font-mono text-gray-500 dark:text-gray-400 break-all mt-1 leading-relaxed">{{ $transaction->receipt_signature }}</p>
                        </div>
                    </div>
                </div>

                {{-- Pied de page --}}
                <div class="text-center mt-5 text-[10px] text-gray-400 space-y-0.5">
                    <p>Reçu généré le {{ $transaction->receipt_generated_at?->format('d/m/Y à H:i:s') ?? $transaction->updated_at->format('d/m/Y à H:i:s') }}</p>
                    <p>Signature valide — Document authentique</p>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 mt-6 justify-center">
            <button onclick="window.print()" class="flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-xl hover:bg-primary-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Télécharger le reçu
            </button>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                Tableau de bord
            </a>
        </div>
    </div>
</div>

<style>
@media print {
    body { background: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .min-h-screen { min-height: auto !important; padding: 0 !important; background: white !important; }
    .shadow-lg { box-shadow: none !important; }
    .rounded-2xl { border-radius: 0 !important; }
    .bg-gradient-to-r { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    nav, footer, .flex.gap-3, .bg-gray-50.dark\:bg-gray-900 { display: none !important; }
    #receipt { border: 1px solid #e5e7eb !important; }
}
</style>
@endsection