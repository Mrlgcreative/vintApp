@extends('app')

@section('title', 'Finaliser votre commande')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- En-tête --}}
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <x-icon icon="fas fa-shopping-cart" tone="primary" size="md" />
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                    Finaliser votre commande
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Complétez vos informations de livraison pour procéder au paiement.
                </p>
            </div>
        </div>
    </div>

    @if(empty($cart))
        <x-alert variant="warning">
            <i class="fas fa-info-circle mr-2"></i> Votre panier est vide.
        </x-alert>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        {{-- Colonne gauche : Informations de livraison --}}
        <div class="lg:col-span-7">
            <x-card>
                <x-card-header icon="fas fa-truck" tone="primary" title="Informations de livraison" />

                <div class="p-6">
                    <form id="deliveryForm">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- Nom complet --}}
                            <div>
                                <x-label for="full_name" icon="fas fa-user" iconTone="primary">Nom complet <span class="text-red-500">*</span></x-label>
                                <x-input type="text" id="full_name" name="full_name" value="{{ Auth::user()->name ?? '' }}" required autocomplete="name" />
                                <div class="invalid-feedback text-red-500 text-sm mt-1 hidden">Veuillez entrer votre nom complet.</div>
                            </div>

                            {{-- Téléphone --}}
                            <div>
                                <x-label for="phone" icon="fas fa-phone" iconTone="primary">Téléphone <span class="text-red-500">*</span></x-label>
                                <x-input type="tel" id="phone" name="phone" placeholder="+243 800 000 000" required autocomplete="tel" />
                                <div class="invalid-feedback text-red-500 text-sm mt-1 hidden">Veuillez entrer un numéro de téléphone valide.</div>
                            </div>

                            {{-- Email --}}
                            <div>
                                <x-label for="email" icon="fas fa-envelope" iconTone="primary">Email <span class="text-red-500">*</span></x-label>
                                <x-input type="email" id="email" name="email" value="{{ Auth::user()->email ?? '' }}" required autocomplete="email" />
                                <div class="invalid-feedback text-red-500 text-sm mt-1 hidden">Veuillez entrer une adresse email valide.</div>
                            </div>

                            {{-- Ville --}}
                            <div>
                                <x-label for="city" icon="fas fa-city" iconTone="primary">Ville <span class="text-red-500">*</span></x-label>
                                <x-select id="city" name="city" required>
                                    <option value="">Sélectionnez une ville</option>
                                    <option value="Kinshasa">Kinshasa</option>
                                    <option value="Lubumbashi">Lubumbashi</option>
                                    <option value="Goma">Goma</option>
                                    <option value="Bukavu">Bukavu</option>
                                    <option value="Matadi">Matadi</option>
                                    <option value="Kolwezi">Kolwezi</option>
                                    <option value="Kisangani">Kisangani</option>
                                    <option value="Autre">Autre ville</option>
                                </x-select>
                                <div class="invalid-feedback text-red-500 text-sm mt-1 hidden">Veuillez sélectionner une ville.</div>
                            </div>

                            {{-- Commune/Quartier --}}
                            <div>
                                <x-label for="commune" icon="fas fa-map-marker-alt" iconTone="primary">Commune/Quartier <span class="text-red-500">*</span></x-label>
                                <x-input type="text" id="commune" name="commune" placeholder="Ex: Gombe, Lemba, etc." required />
                                <div class="invalid-feedback text-red-500 text-sm mt-1 hidden">Veuillez entrer votre commune ou quartier.</div>
                            </div>

                            {{-- Adresse complète --}}
                            <div>
                                <x-label for="address" icon="fas fa-home" iconTone="primary">Adresse complète <span class="text-red-500">*</span></x-label>
                                <x-input type="text" id="address" name="address" placeholder="Avenue, numéro, bâtiment..." required />
                                <div class="invalid-feedback text-red-500 text-sm mt-1 hidden">Veuillez entrer votre adresse complète.</div>
                            </div>

                            {{-- Position GPS (optionnelle) --}}
                            <div class="md:col-span-2">
                                <div class="bg-vinted-primary-50 dark:bg-vinted-primary-500/5 border border-vinted-primary-200 dark:border-vinted-primary-500/30 rounded-xl overflow-hidden">
                                    <div class="px-4 py-3 border-b border-vinted-primary-200 dark:border-vinted-primary-500/30 flex items-center gap-2">
                                        <i class="fas fa-map-marker-alt text-vinted-primary-600"></i>
                                        <p class="text-sm font-semibold text-vinted-primary-800 dark:text-vinted-primary-300">
                                            Position GPS (Optionnelle - pour un suivi précis)
                                        </p>
                                    </div>
                                    <div class="p-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <x-label for="latitude" icon="fas fa-crosshairs" iconTone="primary">Latitude</x-label>
                                                <x-input type="number" step="0.00000001" id="latitude" name="latitude" placeholder="-4.325000" />
                                                <small class="text-gray-500 dark:text-gray-400 text-xs">Ex: -4.325000 (Kinshasa)</small>
                                            </div>
                                            <div>
                                                <x-label for="longitude" icon="fas fa-crosshairs" iconTone="primary">Longitude</x-label>
                                                <x-input type="number" step="0.00000001" id="longitude" name="longitude" placeholder="15.307778" />
                                                <small class="text-gray-500 dark:text-gray-400 text-xs">Ex: 15.307778 (Kinshasa)</small>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <button type="button"
                                                    class="inline-flex items-center px-4 py-2 bg-vinted-primary-100 text-vinted-primary-700 border border-vinted-primary-300 rounded-lg hover:bg-vinted-primary-200 transition-colors text-sm font-medium"
                                                    id="getCurrentLocationBtn">
                                                <i class="fas fa-location-arrow mr-2"></i>
                                                Utiliser ma position actuelle (GPS)
                                            </button>
                                            <p class="text-gray-500 dark:text-gray-400 text-xs mt-2">
                                                Cela permettra un suivi de livraison plus précis sur la carte.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Notes de livraison --}}
                            <div class="md:col-span-2">
                                <x-label for="notes" icon="fas fa-sticky-note" iconTone="primary">Instructions de livraison (optionnel)</x-label>
                                <x-textarea id="notes" name="notes" rows="3" placeholder="Point de repère, instructions particulières..."></x-textarea>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full mt-6 bg-vinted-primary-600 hover:bg-vinted-primary-700 text-white py-3 px-6 rounded-lg shadow-sm transition-colors font-semibold text-base flex items-center justify-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            Confirmer les informations de livraison
                        </button>
                    </form>

                    {{-- Informations confirmées --}}
                    <div id="deliveryInfoConfirmed" class="mt-6 p-5 bg-emerald-50 dark:bg-emerald-500/5 border border-emerald-200 dark:border-emerald-500/30 rounded-xl hidden">
                        <h6 class="text-emerald-700 dark:text-emerald-300 font-semibold mb-4 flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Informations de livraison confirmées
                        </h6>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2.5">
                                <p class="flex items-center text-sm text-gray-700 dark:text-gray-200">
                                    <i class="fas fa-user mr-3 w-4 text-vinted-primary-500"></i>
                                    <strong>Nom :</strong>
                                    <span id="confirmed_name" class="ml-2"></span>
                                </p>
                                <p class="flex items-center text-sm text-gray-700 dark:text-gray-200">
                                    <i class="fas fa-phone mr-3 w-4 text-vinted-primary-500"></i>
                                    <strong>Téléphone :</strong>
                                    <span id="confirmed_phone" class="ml-2"></span>
                                </p>
                                <p class="flex items-center text-sm text-gray-700 dark:text-gray-200">
                                    <i class="fas fa-envelope mr-3 w-4 text-vinted-primary-500"></i>
                                    <strong>Email :</strong>
                                    <span id="confirmed_email" class="ml-2"></span>
                                </p>
                            </div>
                            <div class="space-y-2.5">
                                <p class="flex items-center text-sm text-gray-700 dark:text-gray-200">
                                    <i class="fas fa-city mr-3 w-4 text-vinted-primary-500"></i>
                                    <strong>Ville :</strong>
                                    <span id="confirmed_city" class="ml-2"></span>
                                </p>
                                <p class="flex items-center text-sm text-gray-700 dark:text-gray-200">
                                    <i class="fas fa-map-marker-alt mr-3 w-4 text-vinted-primary-500"></i>
                                    <strong>Commune :</strong>
                                    <span id="confirmed_commune" class="ml-2"></span>
                                </p>
                                <p class="flex items-center text-sm text-gray-700 dark:text-gray-200">
                                    <i class="fas fa-home mr-3 w-4 text-vinted-primary-500"></i>
                                    <strong>Adresse :</strong>
                                    <span id="confirmed_address" class="ml-2"></span>
                                </p>
                                <p class="flex items-center text-sm text-gray-700 dark:text-gray-200" id="confirmed_gps_container" style="display: none;">
                                    <i class="fas fa-crosshairs mr-3 w-4 text-vinted-primary-500"></i>
                                    <strong>GPS :</strong>
                                    <span id="confirmed_gps" class="ml-2 text-emerald-600"></span>
                                </p>
                            </div>
                            <div id="confirmed_notes_container" class="md:col-span-2 hidden">
                                <p class="flex items-start text-sm text-gray-700 dark:text-gray-200">
                                    <i class="fas fa-sticky-note mr-3 w-4 mt-1 text-vinted-primary-500"></i>
                                    <strong>Instructions :</strong>
                                    <span id="confirmed_notes" class="ml-2"></span>
                                </p>
                            </div>
                        </div>
                        <button type="button"
                                class="mt-4 inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-vinted-primary-300 text-vinted-primary-700 rounded-lg hover:bg-vinted-primary-50 transition-colors text-sm"
                                id="editDeliveryBtn">
                            <i class="fas fa-edit mr-2"></i>Modifier
                        </button>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Colonne droite : Récapitulatif --}}
        <div class="lg:col-span-5">
            <x-card class="sticky top-5">
                <x-card-header icon="fas fa-receipt" tone="primary" title="Récapitulatif de la commande" />

                <div class="p-6">
                    {{-- Articles --}}
                    <div class="space-y-4 mb-6">
                        @foreach($cart as $item)
                            <div class="flex items-center gap-4 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                                <div class="flex-shrink-0">
                                    @if($item['image'])
                                        <img src="{{ asset('storage/' . $item['image']) }}"
                                             alt="{{ $item['name'] }}"
                                             class="w-16 h-16 object-cover rounded-lg border border-gray-200 dark:border-gray-700">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 flex items-center justify-center rounded-lg border border-gray-200 dark:border-gray-700">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-gray-900 dark:text-white truncate">{{ $item['name'] }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Qté: {{ $item['quantity'] }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-gray-900 dark:text-white">
                                        {{ number_format($item['price'] * $item['quantity'], 2) }} {{ $item['currency'] }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Totaux --}}
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-gray-700 dark:text-gray-200">Sous-total :</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($subtotal, 2) }} {{ $currency }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-gray-700 dark:text-gray-200">
                                Frais de livraison :
                                <span class="text-sm text-gray-500 dark:text-gray-400">({{ $transportFeePercentage }}%)</span>
                            </span>
                            <span class="font-semibold text-vinted-primary-600 dark:text-vinted-primary-400">+{{ number_format($transportFee, 2) }} {{ $currency }}</span>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-900 dark:text-white">Total :</span>
                                <span class="text-xl font-bold text-vinted-primary-600 dark:text-vinted-primary-400">{{ number_format($total, 2) }} {{ $currency }}</span>
                            </div>

                            <div class="mt-3 p-3 bg-amber-50 dark:bg-amber-500/10 rounded-lg border border-amber-200 dark:border-amber-500/30">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-amber-500 mt-0.5 mr-2 flex-shrink-0"></i>
                                    <p class="text-xs text-amber-700 dark:text-amber-300">
                                        <strong>Note :</strong> Des frais de transaction de l'opérateur mobile money (environ 3-4%) peuvent s'ajouter au moment du paiement.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bouton de paiement (masqué par défaut) --}}
                    <div id="paymentButtonContainer" class="hidden mt-6">
                        <div class="mb-4">
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3 flex items-center gap-2">
                                <i class="fas fa-wallet text-vinted-primary-500"></i>Méthode de paiement
                            </p>
                                    <div class="grid grid-cols-1 gap-3">
                                        @php
                                            $payMethods = [
                                                'maishapay' => [
                                                    'icon' => 'fa-bolt',
                                                    'iconBg' => 'bg-emerald-500',
                                                    'name' => 'Payer via Mobile Money',
                                                    'desc' => 'Orange, M-Pesa, Airtel',
                                                ],
                                            ];
                                        @endphp
                                        @foreach($payMethods as $key => $m)
                                        <button type="button"
                                                data-method="{{ $key }}"
                                                class="pay-method-card relative rounded-xl border-2 border-emerald-500 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/30 dark:to-teal-900/30 p-4 text-left transition-all duration-200">
                                            <span class="pay-method-badge absolute top-2 right-2 w-4 h-4 rounded-full border-2 border-emerald-500 bg-emerald-500 flex items-center justify-center">
                                                <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/></svg>
                                            </span>
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 {{ $m['iconBg'] }} rounded-xl flex items-center justify-center">
                                                    <i class="fas {{ $m['icon'] }} text-white"></i>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-900 dark:text-white">{{ $m['name'] }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $m['desc'] }}</p>
                                                </div>
                                            </div>
                                        </button>
                                        @endforeach
                                    </div>
                        </div>

                        <div id="payMethodInfo" class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-xl p-4 mb-4">
                            <div class="flex items-start">
                                <i class="fas fa-bolt text-emerald-600 mt-0.5 mr-3 flex-shrink-0 text-xl"></i>
                                <div>
                                    <h3 class="font-semibold text-emerald-900 dark:text-emerald-300 text-sm mb-1" id="payMethodInfoTitle">Paiement sécurisé via Mobile Money</h3>
                                    <p class="text-emerald-800 dark:text-emerald-400 text-sm" id="payMethodInfoText">Tous les opérateurs Mobile Money RDC sont acceptés.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Formulaire mobile money --}}
                        <form action="{{ route('payments.maishapay.checkout') }}" method="POST" id="maishapayForm" class="payment-gateway-form">
                            @csrf
                            <input type="hidden" name="delivery_address_id" class="delivery_address_id_input" value="">
                            <input type="hidden" name="cart_items" value="{{ json_encode($cart) }}">
                            <input type="hidden" name="total_amount" value="{{ $total }}">
                            <input type="hidden" name="currency" value="{{ $currency }}">

                            <button type="submit"
                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 px-6 rounded-lg shadow-sm transition-colors font-semibold text-base flex items-center justify-center gap-2">
                                <i class="fas fa-bolt"></i>
                                Payer {{ number_format($total, 2) }} {{ $currency }} via Mobile Money
                            </button>
                        </form>

                    </div>

                    {{-- Badges de sécurité --}}
                    <div class="mt-4 text-center">
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">Paiements 100% sécurisés</p>
                        <div class="flex justify-center items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center gap-1 bg-white dark:bg-gray-900 rounded-full px-3 py-1.5 text-xs font-medium border border-gray-200 dark:border-gray-700"><i class="fas fa-lock text-vinted-primary-500"></i> SSL</span>
                            <span class="inline-flex items-center gap-1 bg-white dark:bg-gray-900 rounded-full px-3 py-1.5 text-xs font-medium border border-gray-200 dark:border-gray-700"><i class="fas fa-check-circle text-emerald-500"></i> Vérifié</span>
                            <span class="inline-flex items-center gap-1 bg-emerald-50 dark:bg-emerald-900 rounded-full px-3 py-1.5 text-xs font-medium border border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300"><i class="fas fa-bolt"></i> Mobile Money</span>
                        </div>
                    </div>

                    {{-- Message d'instruction --}}
                    <div id="deliveryInstructionMessage" class="mt-6 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 rounded-xl p-4">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-exclamation-triangle text-amber-600"></i>
                            <span class="text-amber-800 dark:text-amber-300 text-sm">Veuillez d'abord remplir vos informations de livraison ci-contre.</span>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deliveryForm = document.getElementById('deliveryForm');
    const deliveryInfoConfirmed = document.getElementById('deliveryInfoConfirmed');
    const paymentButtonContainer = document.getElementById('paymentButtonContainer');
    const deliveryInstructionMessage = document.getElementById('deliveryInstructionMessage');
    const editDeliveryBtn = document.getElementById('editDeliveryBtn');
    let savedAddressId = null;

    loadDefaultAddress();

    const getCurrentLocationBtn = document.getElementById('getCurrentLocationBtn');
    if (getCurrentLocationBtn) {
        getCurrentLocationBtn.addEventListener('click', function() {
            if ("geolocation" in navigator) {
                const originalText = this.innerHTML;
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Récupération de la position...';
                this.className = 'inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg cursor-not-allowed text-sm font-medium';

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        document.getElementById('latitude').value = position.coords.latitude.toFixed(8);
                        document.getElementById('longitude').value = position.coords.longitude.toFixed(8);

                        getCurrentLocationBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Position obtenue !';
                        getCurrentLocationBtn.className = 'inline-flex items-center px-4 py-2 bg-green-100 text-green-700 border border-green-300 rounded-lg text-sm font-medium';

                        setTimeout(() => {
                            getCurrentLocationBtn.innerHTML = originalText;
                            getCurrentLocationBtn.className = 'inline-flex items-center px-4 py-2 bg-vinted-primary-100 text-vinted-primary-700 border border-vinted-primary-300 rounded-lg hover:bg-vinted-primary-200 transition-colors text-sm font-medium';
                            getCurrentLocationBtn.disabled = false;
                        }, 2000);
                    },
                    function(error) {
                        console.error('Erreur de géolocalisation:', error);
                        let errorMsg = 'Impossible d\'obtenir votre position.';

                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMsg = 'Permission refusée. Autorisez l\'accès à votre localisation.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMsg = 'Position non disponible. Vérifiez votre GPS.';
                                break;
                            case error.TIMEOUT:
                                errorMsg = 'Délai d\'attente dépassé. Réessayez.';
                                break;
                        }

                        showToast(errorMsg, 'error');
                        getCurrentLocationBtn.innerHTML = originalText;
                        getCurrentLocationBtn.className = 'inline-flex items-center px-4 py-2 bg-vinted-primary-100 text-vinted-primary-700 border border-vinted-primary-300 rounded-lg hover:bg-vinted-primary-200 transition-colors text-sm font-medium';
                        getCurrentLocationBtn.disabled = false;
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            } else {
                showToast('La géolocalisation n\'est pas supportée par votre navigateur', 'error');
            }
        });
    }

    function loadDefaultAddress() {
        fetch('/delivery-address/default', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                const address = data.data;
                savedAddressId = address.id;

                document.getElementById('full_name').value = address.full_name;
                document.getElementById('phone').value = address.phone;
                document.getElementById('email').value = address.email;
                document.getElementById('city').value = address.city;
                document.getElementById('commune').value = address.commune;
                document.getElementById('address').value = address.address;
                document.getElementById('notes').value = address.notes || '';

                if (address.latitude) {
                    document.getElementById('latitude').value = address.latitude;
                }
                if (address.longitude) {
                    document.getElementById('longitude').value = address.longitude;
                }

                showConfirmedInfo(address);
            }
        })
        .catch(error => console.error('Erreur lors du chargement de l\'adresse:', error));
    }

    function showConfirmedInfo(data) {
        document.getElementById('confirmed_name').textContent = data.full_name;
        document.getElementById('confirmed_phone').textContent = data.phone;
        document.getElementById('confirmed_email').textContent = data.email;
        document.getElementById('confirmed_city').textContent = data.city;
        document.getElementById('confirmed_commune').textContent = data.commune;
        document.getElementById('confirmed_address').textContent = data.address;

        if (data.latitude && data.longitude) {
            document.getElementById('confirmed_gps').textContent = `Lat: ${parseFloat(data.latitude).toFixed(6)}, Lng: ${parseFloat(data.longitude).toFixed(6)}`;
            document.getElementById('confirmed_gps_container').style.display = 'flex';
        } else {
            document.getElementById('confirmed_gps_container').style.display = 'none';
        }

        if (data.notes) {
            document.getElementById('confirmed_notes').textContent = data.notes;
            document.getElementById('confirmed_notes_container').classList.remove('hidden');
        } else {
            document.getElementById('confirmed_notes_container').classList.add('hidden');
        }

        deliveryForm.classList.add('hidden');
        deliveryInfoConfirmed.classList.remove('hidden');

        paymentButtonContainer.classList.remove('hidden');
        deliveryInstructionMessage.classList.add('hidden');
    }

    deliveryForm.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!deliveryForm.checkValidity()) {
            e.stopPropagation();
            deliveryForm.classList.add('was-validated');
            return;
        }

        const formData = {
            full_name: document.getElementById('full_name').value,
            phone: document.getElementById('phone').value,
            email: document.getElementById('email').value,
            city: document.getElementById('city').value,
            commune: document.getElementById('commune').value,
            address: document.getElementById('address').value,
            latitude: document.getElementById('latitude').value || null,
            longitude: document.getElementById('longitude').value || null,
            notes: document.getElementById('notes').value,
            is_default: true
        };

        const submitBtn = deliveryForm.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enregistrement...';
        submitBtn.className = 'w-full mt-6 bg-gray-400 text-white py-3 px-6 rounded-lg cursor-not-allowed font-medium text-lg';

        const url = savedAddressId ? `/delivery-address/${savedAddressId}` : '/delivery-address';
        const method = savedAddressId ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                savedAddressId = data.data.id;

                showConfirmedInfo(data.data);

                showToast('Adresse de livraison enregistrée avec succès', 'success');

                if (window.innerWidth < 1024) {
                    document.getElementById('paymentButtonContainer').scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            } else {
                showToast('Erreur: ' + (data.message || 'Une erreur est survenue'), 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showToast('Erreur lors de l\'enregistrement de l\'adresse', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
            submitBtn.className = 'w-full mt-6 bg-vinted-primary-600 hover:bg-vinted-primary-700 text-white py-3 px-6 rounded-lg transition-colors font-medium text-lg';
        });
    });

    editDeliveryBtn.addEventListener('click', function() {
        deliveryForm.classList.remove('hidden');
        deliveryInfoConfirmed.classList.add('hidden');
        paymentButtonContainer.classList.add('hidden');
        deliveryInstructionMessage.classList.remove('hidden');

        deliveryForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    function showToast(message, type = 'success') {
        const bgColor = type === 'success' ? 'bg-emerald-500' : 'bg-red-500';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-4 rounded-lg shadow-lg z-50 min-w-80 flex items-center`;
        toast.innerHTML = `
            <i class="fas ${icon} mr-3"></i>
            <span>${message}</span>
        `;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    const payMethodMeta = {
        maishapay: {
            active: 'border-emerald-500 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/30 dark:to-teal-900/30',
            badge: 'border-emerald-500 bg-emerald-500',
            title: 'Paiement sécurisé via Mobile Money',
            text: 'Tous les opérateurs Mobile Money RDC sont acceptés.',
            icon: 'fa-bolt',
            iconClass: 'text-emerald-600',
            form: 'maishapayForm',
            btn: 'bg-emerald-600 hover:bg-emerald-700',
            label: 'Mobile Money'
        }
    };
    const payCheckSvg = `<svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/></svg>`;

    function selectPayMethod(method) {
        const meta = payMethodMeta[method];
        const base = 'pay-method-card relative rounded-xl border-2 p-4 text-left transition-all duration-200 ';

        document.querySelectorAll('.pay-method-card').forEach(function(card) {
            const m = card.dataset.method;
            const isActive = m === method;
            card.className = base + (isActive ? payMethodMeta[m].active : 'border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800');
            const badge = card.querySelector('.pay-method-badge');
            badge.className = 'pay-method-badge absolute top-2 right-2 w-4 h-4 rounded-full border-2 flex items-center justify-center ' +
                (isActive ? payMethodMeta[m].badge : 'border-gray-300 dark:border-gray-500');
            badge.innerHTML = isActive ? payCheckSvg : '';
        });

        document.querySelectorAll('.payment-gateway-form').forEach(function(form) {
            form.classList.toggle('hidden', form.id !== meta.form);
        });

        const infoIcon = document.querySelector('#payMethodInfo i');
        infoIcon.className = 'fas ' + meta.icon + ' ' + meta.iconClass + ' mt-0.5 mr-3 flex-shrink-0 text-xl';
        document.getElementById('payMethodInfoTitle').textContent = meta.title;
        document.getElementById('payMethodInfoText').textContent = meta.text;
    }

    document.querySelectorAll('.pay-method-card').forEach(function(card) {
        card.addEventListener('click', function() {
            selectPayMethod(this.dataset.method);
        });
    });

    document.querySelectorAll('.payment-gateway-form').forEach(function(gatewayForm) {
        gatewayForm.addEventListener('submit', function(e) {
            if (savedAddressId) {
                const addressInput = this.querySelector('.delivery_address_id_input');
                if (addressInput) {
                    addressInput.value = savedAddressId;
                }
            } else {
                e.preventDefault();
                showToast('Veuillez d\'abord confirmer votre adresse de livraison', 'error');
                return false;
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Redirection vers la plateforme de paiement...';
            submitBtn.className = 'w-full bg-gray-400 text-white py-3 px-6 rounded-lg cursor-not-allowed font-medium text-lg flex items-center justify-center';
        });
    });
});
</script>
@endpush
@endsection
