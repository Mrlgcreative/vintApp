@extends('app')

@section('title', 'Paiement - ' . (isset($order) ? "Commande #{$order->order_number}" : 'Rechargement wallet'))

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
        {{-- En-tête --}}
        <div class="flex items-center justify-between mb-6 pb-4 border-b">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    @if(isset($order))
                        Paiement commande
                    @else
                        Rechargement wallet
                    @endif
                </h1>
                @if(isset($order))
                    <p class="text-sm text-gray-600 mt-1">Commande #{{ $order->order_number }}</p>
                @endif
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-600">Montant à payer</div>
                <div class="text-3xl font-bold text-primary">
                    {{ number_format($payment->amount, 0, ',', ' ') }} 
                    <span class="text-lg">{{ $payment->currency }}</span>
                </div>
            </div>
        </div>

        {{-- Détails de la commande --}}
        @if(isset($order))
        <div class="mb-6 space-y-3">
            <h2 class="font-semibold text-gray-900 mb-3">Détails de la commande</h2>
            
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Vendeur</span>
                <span class="font-medium">{{ $order->seller->name }}</span>
            </div>
            
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Article</span>
                <span class="font-medium">{{ $order->item->name }}</span>
            </div>
            
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Prix unitaire</span>
                <span class="font-medium">{{ number_format($order->item_price, 0, ',', ' ') }} XOF</span>
            </div>
            
            @if($order->quantity > 1)
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Quantité</span>
                <span class="font-medium">{{ $order->quantity }}</span>
            </div>
            @endif
            
            <div class="flex justify-between text-sm pt-3 border-t">
                <span class="text-gray-900 font-semibold">Total</span>
                <span class="text-lg font-bold text-primary">{{ number_format($order->total_amount, 0, ',', ' ') }} XOF</span>
            </div>
        </div>
        @endif

        {{-- Informations de sécurité --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h3 class="font-semibold text-blue-900 text-sm">Paiement sécurisé</h3>
                    <p class="text-xs text-blue-700 mt-1">
                        Votre paiement est sécurisé par CinetPay. Vous pouvez payer par carte bancaire ou Mobile Money.
                    </p>
                </div>
            </div>
        </div>

        {{-- Détails de la transaction --}}
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <div class="text-gray-600">ID Transaction</div>
                    <div class="font-mono text-xs mt-1">{{ $payment->transaction_id }}</div>
                </div>
                <div>
                    <div class="text-gray-600">Date</div>
                    <div class="font-medium mt-1">{{ $payment->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>

        {{-- Paiement CinetPay (redirection vers le comptoir) --}}
        <div class="space-y-4">
            @if(isset($paymentUrl))
                <div class="bg-gray-50 rounded-lg p-6 text-center">
                    <p class="text-sm text-gray-600 mb-4">
                        Cliquez sur le bouton ci-dessous pour être redirigé vers le comptoir CinetPay,
                        où vous choisirez votre opérateur Mobile Money et saisirez votre numéro de téléphone.
                    </p>
                    <a href="{{ $paymentUrl }}" target="_blank" rel="noopener"
                       class="inline-block w-full max-w-[340px] bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition text-center">
                        Payer avec CinetPay
                    </a>
                </div>
            @else
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3 flex-shrink-0"></i>
                        <div>
                            <p class="font-semibold text-red-800 text-sm">Paiement temporairement indisponible</p>
                            <p class="text-red-700 text-xs mt-1">{{ $cinetpayError ?? 'Service de paiement CinetPay indisponible.' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="text-center">
                @if(isset($order))
                    <a href="{{ route('orders.show', $order) }}" class="text-sm text-gray-600 hover:text-gray-900">
                        Retour à la commande
                    </a>
                @else
                    <a href="{{ route('wallet.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                        Retour au wallet
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Moyens de paiement acceptés --}}
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600 mb-3">Moyens de paiement acceptés</p>
        <div class="flex justify-center items-center space-x-4 flex-wrap">
            <div class="bg-white rounded px-3 py-2 shadow-sm text-xs font-medium">💳 Visa</div>
            <div class="bg-white rounded px-3 py-2 shadow-sm text-xs font-medium">💳 Mastercard</div>
            <div class="bg-white rounded px-3 py-2 shadow-sm text-xs font-medium">📱 Orange Money</div>
            <div class="bg-white rounded px-3 py-2 shadow-sm text-xs font-medium">📱 MTN Mobile Money</div>
            <div class="bg-white rounded px-3 py-2 shadow-sm text-xs font-medium">📱 Moov Money</div>
        </div>
    </div>
</div>

@endsection
