@extends('app')

@section('title', 'Paiement PawaPay')

@section('content')
@php
    $displayTotal = isset($total) ? $total : 0;
    $displayCurrency = isset($currency) ? $currency : (isset($cart) && !empty($cart) ? ($cart[0]['currency'] ?? 'USD') : 'USD');
@endphp

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Erreurs --}}
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
            @foreach($errors->all() as $error)
                <p class="text-sm text-red-700 dark:text-red-300 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>{{ $error }}
                </p>
            @endforeach
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
            <p class="text-sm text-red-700 dark:text-red-300 flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>{{ session('error') }}
            </p>
        </div>
    @endif

    <form action="{{ route('payments.pawapay.initiate') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            {{-- Colonne gauche : formulaire --}}
            <div class="lg:col-span-7 space-y-6">
                {{-- En-tête --}}
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-3">
                        <span class="bg-purple-600 dark:bg-purple-700 p-2.5 rounded-xl flex items-center justify-center shadow-sm">
                            <i class="fas fa-mobile-alt text-white"></i>
                        </span>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">Paiement Mobile Money</h1>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Payez en toute sécurité via PawaPay</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 bg-purple-50 dark:bg-purple-500/10 border border-purple-200 dark:border-purple-800 px-3 py-1.5 rounded-full text-xs font-medium text-purple-700 dark:text-purple-300">
                        <i class="fas fa-shield-alt"></i>Sécurisé
                    </span>
                </div>

                {{-- Formulaire de paiement --}}
                <x-card>
                    <div class="px-5 md:px-6 py-4 border-b border-gray-100 dark:border-gray-700/50">
                        <h5 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-1.5 h-4 bg-purple-500 rounded-full"></span>
                            Détails du paiement
                        </h5>
                    </div>
                    <div class="p-5 md:p-6 space-y-6">
                        {{-- Opérateur --}}
                        <div>
                            <label for="operator" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                                <i class="fas fa-tower-broadcast text-purple-500 mr-1"></i>Opérateur
                            </label>
                            <select id="operator" name="operator" required
                                    class="w-full px-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500/40 focus:border-transparent transition-all bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none">
                                <option value="">Sélectionnez votre opérateur</option>
                                <option value="VODACOM" @selected(old('operator') === 'VODACOM')>Vodacom M-Pesa</option>
                                <option value="AIRTEL" @selected(old('operator') === 'AIRTEL')>Airtel Money</option>
                                <option value="ORANGE" @selected(old('operator') === 'ORANGE')>Orange Money</option>
                                <option value="AFRICELL" @selected(old('operator') === 'AFRICELL')>Africell Money</option>
                            </select>
                        </div>

                        {{-- Numéro de téléphone --}}
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                                <i class="fas fa-phone text-purple-500 mr-1"></i>Numéro Mobile Money
                            </label>
                            <div class="flex shadow-sm">
                                <span class="inline-flex items-center px-4 py-3.5 bg-purple-600 text-white border border-r-0 border-purple-600 rounded-l-xl font-medium text-sm">
                                    +243
                                </span>
                                <input type="tel"
                                       class="flex-1 px-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-r-xl focus:ring-2 focus:ring-purple-500/40 focus:border-transparent transition-all text-lg tracking-wider bg-white dark:bg-gray-900 outline-none"
                                       id="phone"
                                       name="phone"
                                       maxlength="13"
                                       value="{{ old('phone', $deliveryAddress->phone ?? '') }}"
                                       placeholder="0XX XX XX XXX"
                                       required
                                       autocomplete="tel">
                            </div>
                            <p class="text-xs text-gray-400 mt-2 flex items-center gap-1.5">
                                <i class="fas fa-info-circle"></i>
                                Une demande de confirmation sera envoyée sur ce numéro
                            </p>
                        </div>

                        {{-- Montant --}}
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
                                <i class="fas fa-money-bill-wave text-purple-500 mr-1"></i>Montant
                            </label>
                            <div class="flex shadow-sm">
                                <input type="number"
                                       class="flex-1 px-4 py-3.5 border border-r-0 border-gray-300 dark:border-gray-700 rounded-l-xl focus:ring-2 focus:ring-purple-500/40 focus:border-transparent transition-all text-lg font-bold bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none"
                                       id="amount"
                                       name="amount"
                                       min="1"
                                       step="0.01"
                                       required
                                       readonly
                                       value="{{ $displayTotal }}">
                                <span class="inline-flex items-center px-4 py-3.5 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-700 rounded-r-xl font-medium text-sm text-gray-700 dark:text-gray-200">
                                    {{ $displayCurrency }}
                                </span>
                            </div>
                            <input type="hidden" name="currency" value="{{ $displayCurrency }}">
                        </div>
                    </div>
                </x-card>
            </div>

            {{-- Colonne droite : résumé sticky --}}
            <div class="lg:col-span-5">
                <div class="lg:sticky lg:top-5 space-y-6">
                    {{-- Récapitulatif --}}
                    <x-card>
                        <div class="px-5 md:px-6 py-4 border-b border-gray-100 dark:border-gray-700/50 flex items-center gap-3">
                            <span class="w-8 h-8 bg-purple-100 dark:bg-purple-900/40 rounded-lg flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-purple-600 dark:text-purple-400 text-sm"></i>
                            </span>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white">Récapitulatif</h5>
                        </div>
                        <div class="p-5 md:p-6">
                            @if(isset($cart) && !empty($cart))
                                <div class="space-y-3 mb-5">
                                    @foreach($cart as $item)
                                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700/50 last:border-0">
                                            <div class="flex items-center gap-3 min-w-0">
                                                @if(isset($item['image']) && $item['image'])
                                                    <img src="{{ asset('storage/' . $item['image']) }}"
                                                         alt="{{ $item['name'] }}"
                                                         class="w-11 h-11 rounded-lg object-cover flex-shrink-0">
                                                @else
                                                    <div class="w-11 h-11 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                                        <i class="fas fa-box text-gray-400"></i>
                                                    </div>
                                                @endif
                                                <div class="min-w-0">
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200 block truncate">{{ $item['name'] }}</span>
                                                    <span class="text-xs text-gray-400">x{{ $item['quantity'] }}</span>
                                                </div>
                                            </div>
                                            <span class="font-semibold text-gray-900 dark:text-white text-sm flex-shrink-0 ml-3">
                                                {{ number_format($item['price'] * $item['quantity'], 2) }} {{ $item['currency'] }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex items-center justify-between pt-2">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">Total à payer</span>
                                <span class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                    {{ number_format($displayTotal, 2) }} {{ $displayCurrency }}
                                </span>
                            </div>
                        </div>
                    </x-card>

                    {{-- Adresse de livraison --}}
                    @if(isset($deliveryAddress))
                    <x-card>
                        <div class="px-5 md:px-6 py-4 border-b border-gray-100 dark:border-gray-700/50 flex items-center gap-3">
                            <span class="w-8 h-8 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex items-center justify-center">
                                <i class="fas fa-truck text-blue-600 dark:text-blue-400 text-sm"></i>
                            </span>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white">Livraison</h5>
                        </div>
                        <div class="p-5 md:p-6">
                            <p class="text-sm text-gray-700 dark:text-gray-200">
                                <strong class="text-gray-900 dark:text-white">{{ $deliveryAddress->full_name }}</strong> · <i class="fas fa-phone text-gray-400 text-xs"></i> {{ $deliveryAddress->phone }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1.5">
                                {{ $deliveryAddress->address }}, {{ $deliveryAddress->commune }}<br>
                                {{ $deliveryAddress->city }}
                            </p>
                        </div>
                    </x-card>
                    @endif

                    {{-- Bouton de paiement (dans le formulaire) --}}
                    <button type="submit"
                            id="pay-btn"
                            class="w-full bg-purple-600 hover:bg-purple-700 text-white py-4 px-6 rounded-xl transition-all font-bold text-base shadow-lg hover:shadow-xl active:scale-[0.99] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <i class="fas fa-lock"></i>
                        Payer {{ number_format($displayTotal, 2) }} {{ $displayCurrency }}
                    </button>

                    <div class="flex items-center justify-center gap-5 text-xs text-gray-400">
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-shield-alt text-purple-500"></i>SSL sécurisé
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-lock text-purple-500"></i>Paiement crypté
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-clock text-purple-500"></i>Instantanné
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Avantages PawaPay --}}
    <div class="mt-8 grid grid-cols-3 gap-3 sm:gap-4">
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 text-center hover:shadow-md transition-shadow">
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/40 rounded-xl flex items-center justify-center mx-auto mb-2.5">
                <i class="fas fa-globe text-purple-600 dark:text-purple-400"></i>
            </div>
            <h6 class="font-semibold text-gray-900 dark:text-white text-sm">Panafricain</h6>
            <p class="text-[11px] text-gray-500 mt-0.5">Plusieurs opérateurs</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 text-center hover:shadow-md transition-shadow">
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/40 rounded-xl flex items-center justify-center mx-auto mb-2.5">
                <i class="fas fa-shield-alt text-purple-600 dark:text-purple-400"></i>
            </div>
            <h6 class="font-semibold text-gray-900 dark:text-white text-sm">Sécurisé</h6>
            <p class="text-[11px] text-gray-500 mt-0.5">Confirmation USSD</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 text-center hover:shadow-md transition-shadow">
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/40 rounded-xl flex items-center justify-center mx-auto mb-2.5">
                <i class="fas fa-mobile-alt text-purple-600 dark:text-purple-400"></i>
            </div>
            <h6 class="font-semibold text-gray-900 dark:text-white text-sm">Simple</h6>
            <p class="text-[11px] text-gray-500 mt-0.5">Paiement depuis le mobile</p>
        </div>
    </div>
</div>
@endsection
