@extends('app')

@section('title', 'Paiement PawaPay')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Header PawaPay -->
            <div class="bg-gradient-to-r from-purple-600 via-purple-500 to-fuchsia-600 text-white px-6 py-6 relative overflow-hidden">
                <div class="absolute inset-0 opacity-10">
                    <svg class="w-full h-full" viewBox="0 0 400 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="50" cy="50" r="40" fill="white"/>
                        <circle cx="150" cy="30" r="30" fill="white"/>
                        <circle cx="280" cy="60" r="50" fill="white"/>
                        <circle cx="380" cy="20" r="25" fill="white"/>
                    </svg>
                </div>
                <div class="flex items-center justify-between relative z-10">
                    <div>
                        <h4 class="text-xl font-bold flex items-center">
                            <span class="bg-white/20 p-2 rounded-lg mr-3">
                                <i class="fas fa-mobile-alt text-white"></i>
                            </span>
                            Paiement Mobile Money
                        </h4>
                        <p class="text-purple-100 text-sm mt-1">
                            Payez en toute sécurité depuis votre téléphone
                        </p>
                    </div>
                    <span class="bg-white/20 backdrop-blur-sm px-3 py-1.5 rounded-full text-xs font-medium flex items-center">
                        <i class="fas fa-shield-alt mr-1.5"></i>Sécurisé
                    </span>
                </div>
            </div>

            <div class="p-6 sm:p-8">
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

                <!-- Récapitulatif commande -->
                @if(isset($cart) && !empty($cart))
                <div class="mb-8 p-5 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-750 rounded-xl border border-gray-200 dark:border-gray-700">
                    <h5 class="text-base font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <span class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-shopping-cart text-purple-600 dark:text-purple-400 text-sm"></i>
                        </span>
                        Votre commande
                    </h5>
                    <div class="space-y-2 mb-4">
                        @foreach($cart as $item)
                            <div class="flex items-center justify-between py-2.5 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                <div class="flex items-center space-x-3 min-w-0">
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

                    <div class="border-t-2 border-purple-500 pt-4 mt-2">
                        <div class="flex justify-between items-center">
                            <span class="text-base font-bold text-gray-900 dark:text-white">Total à payer</span>
                            <span class="text-xl font-bold text-purple-600 dark:text-purple-400">
                                {{ number_format($total, 2) }} {{ $currency }}
                            </span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Formulaire de paiement -->
                <form action="{{ route('payments.pawapay.initiate') }}" method="POST">
                    @csrf

                    <!-- Opérateur -->
                    <div class="mb-6">
                        <label for="operator" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-tower-broadcast mr-1.5"></i>Opérateur
                        </label>
                        <select id="operator" name="operator" required
                                class="w-full px-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                            <option value="">Sélectionnez votre opérateur</option>
                            <option value="VODACOM" @selected(old('operator') === 'VODACOM')>Vodacom M-Pesa</option>
                            <option value="AIRTEL" @selected(old('operator') === 'AIRTEL')>Airtel Money</option>
                            <option value="ORANGE" @selected(old('operator') === 'ORANGE')>Orange Money</option>
                            <option value="AFRICELL" @selected(old('operator') === 'AFRICELL')>Africell Money</option>
                        </select>
                    </div>

                    <!-- Numéro de téléphone -->
                    <div class="mb-6">
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-phone mr-1.5"></i>Numéro Mobile Money
                        </label>
                        <div class="flex">
                            <span class="inline-flex items-center px-4 py-3.5 bg-gradient-to-b from-purple-600 to-purple-700 text-white border border-r-0 border-purple-600 rounded-l-xl font-medium text-sm shadow-sm">
                                +243
                            </span>
                            <input type="tel"
                                   class="flex-1 px-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-r-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all text-lg tracking-wider bg-white dark:bg-gray-900"
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

                    <!-- Montant -->
                    <div class="mb-6">
                        <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-money-bill-wave mr-1.5"></i>Montant
                        </label>
                        <div class="flex">
                            <input type="number"
                                   class="flex-1 px-4 py-3.5 border border-r-0 border-gray-300 dark:border-gray-600 rounded-l-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all text-lg font-bold bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white"
                                   id="amount"
                                   name="amount"
                                   min="1"
                                   step="0.01"
                                   required
                                   readonly
                                   value="{{ $total }}">
                            <span class="inline-flex items-center px-4 py-3.5 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-r-xl font-medium text-sm text-gray-700 dark:text-gray-200 shadow-sm">
                                {{ $currency }}
                            </span>
                        </div>
                        <input type="hidden" name="currency" value="{{ $currency }}">
                    </div>

                    @if(isset($deliveryAddress))
                    <!-- Adresse de livraison -->
                    <div class="mb-6 p-4 bg-gradient-to-r from-purple-50 to-fuchsia-50 dark:from-purple-900/20 dark:to-fuchsia-900/20 rounded-xl border border-purple-200 dark:border-purple-800">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-truck text-purple-600 dark:text-purple-400 text-sm"></i>
                            </div>
                            <div>
                                <h6 class="font-semibold text-purple-800 dark:text-purple-200 text-sm">Livraison à</h6>
                                <p class="text-sm text-purple-700 dark:text-purple-300 mt-0.5">
                                    <strong>{{ $deliveryAddress->full_name }}</strong><br>
                                    {{ $deliveryAddress->address }}, {{ $deliveryAddress->commune }}<br>
                                    {{ $deliveryAddress->city }}<br>
                                    <i class="fas fa-phone mr-1"></i>{{ $deliveryAddress->phone }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Bouton de paiement -->
                    <button type="submit"
                            id="pay-btn"
                            class="w-full bg-gradient-to-r from-purple-600 via-purple-500 to-fuchsia-600 text-white py-4 px-6 rounded-xl hover:from-purple-700 hover:via-purple-600 hover:to-fuchsia-700 transition-all font-bold text-base shadow-lg hover:shadow-xl active:scale-[0.98] flex items-center justify-center gap-2">
                        <i class="fas fa-lock"></i>
                        Payer avec PawaPay
                    </button>

                    <!-- Sécurité -->
                    <div class="mt-4 flex items-center justify-center gap-5 text-xs text-gray-400">
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
                </form>
            </div>
        </div>

        <!-- Avantages PawaPay -->
        <div class="mt-6 grid grid-cols-3 gap-3 sm:gap-4">
            <div class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 text-center hover:shadow-md transition-shadow">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mx-auto mb-2.5">
                    <span class="text-xl">🌍</span>
                </div>
                <h6 class="font-semibold text-gray-900 dark:text-white text-sm">Panafricain</h6>
                <p class="text-[11px] text-gray-500 mt-0.5">Plusieurs opérateurs</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 text-center hover:shadow-md transition-shadow">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mx-auto mb-2.5">
                    <span class="text-xl">🔒</span>
                </div>
                <h6 class="font-semibold text-gray-900 dark:text-white text-sm">Sécurisé</h6>
                <p class="text-[11px] text-gray-500 mt-0.5">Confirmation USSD</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 sm:p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 text-center hover:shadow-md transition-shadow">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mx-auto mb-2.5">
                    <span class="text-xl">📱</span>
                </div>
                <h6 class="font-semibold text-gray-900 dark:text-white text-sm">Simple</h6>
                <p class="text-[11px] text-gray-500 mt-0.5">Paiement depuis le mobile</p>
            </div>
        </div>
    </div>
</div>
@endsection
