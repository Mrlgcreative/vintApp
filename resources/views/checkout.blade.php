@extends('app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-shopping-cart mr-3 text-blue-600"></i>
            Finaliser votre commande
        </h2>
    </div>

    @if(empty($cart))
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center">
            <i class="fas fa-info-circle mr-3 text-blue-600"></i>
            <span class="text-blue-800">Votre panier est vide.</span>
        </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Colonne gauche : Formulaire de livraison -->
        <div class="lg:col-span-7">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-blue-600 text-white px-6 py-4">
                    <h5 class="text-lg font-semibold flex items-center">
                        <i class="fas fa-truck mr-3"></i>Informations de livraison
                    </h5>
                </div>
                <div class="p-6">
                    <form id="deliveryForm">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nom complet -->
                            <div class="md:col-span-1">
                                <label for="full_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    <i class="fas fa-user mr-2"></i>Nom complet <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors" 
                                       id="full_name" 
                                       name="full_name" 
                                       value="{{ Auth::user()->name ?? '' }}" 
                                       required>
                                <div class="invalid-feedback text-red-500 text-sm mt-1 hidden">Veuillez entrer votre nom complet.</div>
                            </div>

                            <!-- Téléphone -->
                            <div class="md:col-span-1">
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    <i class="fas fa-phone mr-2"></i>Téléphone <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" 
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors" 
                                       id="phone" 
                                       name="phone" 
                                       placeholder="+243 800 000 000" 
                                       required>
                                <div class="invalid-feedback text-red-500 text-sm mt-1 hidden">Veuillez entrer un numéro de téléphone valide.</div>
                            </div>

                            <!-- Email -->
                            <div class="md:col-span-1">
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    <i class="fas fa-envelope mr-2"></i>Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" 
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors" 
                                       id="email" 
                                       name="email" 
                                       value="{{ Auth::user()->email ?? '' }}" 
                                       required>
                                <div class="invalid-feedback text-red-500 text-sm mt-1 hidden">Veuillez entrer une adresse email valide.</div>
                            </div>

                            <!-- Ville -->
                            <div class="md:col-span-1">
                                <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    <i class="fas fa-city mr-2"></i>Ville <span class="text-red-500">*</span>
                                </label>
                                <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors" 
                                        id="city" 
                                        name="city" 
                                        required>
                                    <option value="">Sélectionnez une ville</option>
                                    <option value="Kinshasa">Kinshasa</option>
                                    <option value="Lubumbashi">Lubumbashi</option>
                                    <option value="Goma">Goma</option>
                                    <option value="Bukavu">Bukavu</option>
                                    <option value="Matadi">Matadi</option>
                                    <option value="Kolwezi">Kolwezi</option>
                                    <option value="Kisangani">Kisangani</option>
                                    <option value="Autre">Autre ville</option>
                                </select>
                                <div class="invalid-feedback text-red-500 text-sm mt-1 hidden">Veuillez sélectionner une ville.</div>
                            </div>

                            <!-- Commune/Quartier -->
                            <div class="md:col-span-1">
                                <label for="commune" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    <i class="fas fa-map-marker-alt mr-2"></i>Commune/Quartier <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors" 
                                       id="commune" 
                                       name="commune" 
                                       placeholder="Ex: Gombe, Lemba, etc." 
                                       required>
                                <div class="invalid-feedback text-red-500 text-sm mt-1 hidden">Veuillez entrer votre commune ou quartier.</div>
                            </div>

                            <!-- Adresse complète -->
                            <div class="md:col-span-1">
                                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    <i class="fas fa-home mr-2"></i>Adresse complète <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors" 
                                       id="address" 
                                       name="address" 
                                       placeholder="Avenue, numéro, bâtiment..." 
                                       required>
                                <div class="invalid-feedback text-red-500 text-sm mt-1 hidden">Veuillez entrer votre adresse complète.</div>
                            </div>

                            <!-- Position GPS (optionnelle) -->
                            <div class="md:col-span-2">
                                <div class="bg-white dark:bg-gray-800 border border-blue-200 rounded-lg overflow-hidden">
                                    <div class="bg-blue-50 px-4 py-3 border-b border-blue-200">
                                        <h6 class="text-sm font-semibold text-blue-800 flex items-center">
                                            <i class="fas fa-map-marker-alt mr-2"></i>
                                            Position GPS (Optionnelle - pour un suivi précis)
                                        </h6>
                                    </div>
                                    <div class="p-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="latitude" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                                    <i class="fas fa-crosshairs mr-2"></i>Latitude
                                                </label>
                                                <input type="number" 
                                                       step="0.00000001" 
                                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors" 
                                                       id="latitude" 
                                                       name="latitude" 
                                                       placeholder="-4.325000">
                                                <small class="text-gray-500 dark:text-gray-400 text-xs">Ex: -4.325000 (Kinshasa)</small>
                                            </div>
                                            <div>
                                                <label for="longitude" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                                    <i class="fas fa-crosshairs mr-2"></i>Longitude
                                                </label>
                                                <input type="number" 
                                                       step="0.00000001" 
                                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors" 
                                                       id="longitude" 
                                                       name="longitude" 
                                                       placeholder="15.307778">
                                                <small class="text-gray-500 dark:text-gray-400 text-xs">Ex: 15.307778 (Kinshasa)</small>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <button type="button" 
                                                    class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 border border-blue-300 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium" 
                                                    id="getCurrentLocationBtn">
                                                <i class="fas fa-location-arrow mr-2"></i>
                                                Utiliser ma position actuelle (GPS)
                                            </button>
                                            <p class="text-gray-500 dark:text-gray-400 text-xs mt-2">
                                                Cela permettra un suivi de livraison plus précis sur la carte
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes de livraison -->
                            <div class="md:col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    <i class="fas fa-sticky-note mr-2"></i>Instructions de livraison (optionnel)
                                </label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors" 
                                          id="notes" 
                                          name="notes" 
                                          rows="3" 
                                          placeholder="Point de repère, instructions particulières..."></textarea>
                            </div>
                        </div>

                        <button type="submit" 
                                class="w-full mt-6 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium text-lg">
                            <i class="fas fa-check-circle mr-2"></i>Confirmer les informations de livraison
                        </button>
                    </form>

                    <!-- Zone d'affichage des informations confirmées -->
                    <div id="deliveryInfoConfirmed" class="mt-6 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg hidden">
                        <h6 class="text-green-600 font-semibold mb-4 flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>Informations de livraison confirmées
                        </h6>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <p class="flex items-center text-gray-700 dark:text-gray-200">
                                    <i class="fas fa-user mr-3 w-4"></i>
                                    <strong>Nom :</strong> 
                                    <span id="confirmed_name" class="ml-2"></span>
                                </p>
                                <p class="flex items-center text-gray-700 dark:text-gray-200">
                                    <i class="fas fa-phone mr-3 w-4"></i>
                                    <strong>Téléphone :</strong> 
                                    <span id="confirmed_phone" class="ml-2"></span>
                                </p>
                                <p class="flex items-center text-gray-700 dark:text-gray-200">
                                    <i class="fas fa-envelope mr-3 w-4"></i>
                                    <strong>Email :</strong> 
                                    <span id="confirmed_email" class="ml-2"></span>
                                </p>
                            </div>
                            <div class="space-y-2">
                                <p class="flex items-center text-gray-700 dark:text-gray-200">
                                    <i class="fas fa-city mr-3 w-4"></i>
                                    <strong>Ville :</strong> 
                                    <span id="confirmed_city" class="ml-2"></span>
                                </p>
                                <p class="flex items-center text-gray-700 dark:text-gray-200">
                                    <i class="fas fa-map-marker-alt mr-3 w-4"></i>
                                    <strong>Commune :</strong> 
                                    <span id="confirmed_commune" class="ml-2"></span>
                                </p>
                                <p class="flex items-center text-gray-700 dark:text-gray-200">
                                    <i class="fas fa-home mr-3 w-4"></i>
                                    <strong>Adresse :</strong> 
                                    <span id="confirmed_address" class="ml-2"></span>
                                </p>
                                <p class="flex items-center text-gray-700 dark:text-gray-200" id="confirmed_gps_container" style="display: none;">
                                    <i class="fas fa-crosshairs mr-3 w-4"></i>
                                    <strong>GPS :</strong> 
                                    <span id="confirmed_gps" class="ml-2 text-green-600"></span>
                                </p>
                            </div>
                            <div id="confirmed_notes_container" class="md:col-span-2 hidden">
                                <p class="flex items-start text-gray-700 dark:text-gray-200">
                                    <i class="fas fa-sticky-note mr-3 w-4 mt-1"></i>
                                    <strong>Instructions :</strong> 
                                    <span id="confirmed_notes" class="ml-2"></span>
                                </p>
                            </div>
                        </div>
                        <button type="button" 
                                class="mt-4 inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-blue-300 text-blue-700 rounded-lg hover:bg-blue-50 transition-colors text-sm" 
                                id="editDeliveryBtn">
                            <i class="fas fa-edit mr-2"></i>Modifier
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne droite : Récapitulatif de la commande -->
        <div class="lg:col-span-5">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 sticky top-5">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h5 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-receipt mr-3"></i>Récapitulatif
                    </h5>
                </div>
                <div class="p-6">
                    <div class="space-y-4 mb-6">
                        @foreach($cart as $item)
                            <div class="flex items-center space-x-4 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                <div class="flex-shrink-0">
                                    @if($item['image'])
                                        <img src="{{ asset('storage/' . $item['image']) }}" 
                                             alt="{{ $item['name'] }}" 
                                             class="w-16 h-16 object-cover rounded-lg border border-gray-200 dark:border-gray-700">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 flex items-center justify-content-center rounded-lg border border-gray-200 dark:border-gray-700">
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

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-gray-700 dark:text-gray-200">Sous-total :</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($subtotal, 2) }} {{ $item['currency'] ?? '' }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-gray-700 dark:text-gray-200">
                                Frais de livraison :
                                <span class="text-sm text-gray-500 dark:text-gray-400">({{ $transportFeePercentage }}%)</span>
                            </span>
                            <span class="font-semibold text-blue-600">+{{ number_format($transportFee, 2) }} {{ $item['currency'] ?? '' }}</span>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-semibold text-gray-900 dark:text-white">Total :</span>
                                <span class="text-xl font-bold text-blue-600">{{ number_format($total, 2) }} {{ $item['currency'] ?? '' }}</span>
                            </div>
                            
                            <!-- Avertissement frais opérateur -->
                            <div class="mt-3 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-700">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-amber-500 mt-0.5 mr-2 flex-shrink-0"></i>
                                    <p class="text-xs text-amber-700 dark:text-amber-300">
                                        <strong>Note :</strong> Des frais de transaction de l'opérateur mobile money (environ 3-4%) peuvent s'ajouter au moment du paiement.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bouton de paiement (masqué par défaut) -->
                    <div id="paymentButtonContainer" class="hidden mt-6">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <div class="flex items-start">
                                <i class="fas fa-bolt text-green-600 mt-0.5 mr-3 flex-shrink-0 text-xl"></i>
                                <div>
                                    <h3 class="font-semibold text-green-900 text-sm mb-1">Paiement sécurisé via MaishaPay</h3>
                                    <p class="text-green-800 text-sm">Tous les opérateurs Mobile Money RDC sont acceptés.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Opérateurs supportés -->
                        <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-2 text-center">Opérateurs acceptés :</p>
                            <div class="flex justify-center items-center gap-3">
                                <div class="text-center">
                                    <span class="text-lg">🟠</span>
                                    <p class="text-xs text-gray-600">Orange</p>
                                </div>
                                <div class="text-center">
                                    <span class="text-lg">🟢</span>
                                    <p class="text-xs text-gray-600">M-Pesa</p>
                                </div>
                                <div class="text-center">
                                    <span class="text-lg">🔴</span>
                                    <p class="text-xs text-gray-600">Airtel</p>
                                </div>
                                <div class="text-center">
                                    <span class="text-lg">🟡</span>
                                    <p class="text-xs text-gray-600">Africell</p>
                                </div>
                            </div>
                        </div>

                        <!-- Formulaire MaishaPay -->
                        <form action="{{ route('payments.maishapay.checkout') }}" method="POST" id="maishapayForm" class="payment-gateway-form">
                            @csrf
                            <input type="hidden" name="delivery_address_id" class="delivery_address_id_input" value="">
                            <input type="hidden" name="cart_items" value="{{ json_encode($cart) }}">
                            <input type="hidden" name="total_amount" value="{{ $total }}">
                            <input type="hidden" name="currency" value="{{ $item['currency'] ?? 'CDF' }}">
                            
                            <button type="submit" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-lg transition-colors font-medium text-lg flex items-center justify-center">
                                <i class="fas fa-bolt mr-2"></i>
                                Payer {{ number_format($total, 2) }} {{ $item['currency'] ?? 'CDF' }} avec MaishaPay
                            </button>
                        </form>

                        <!-- Moyens de paiement acceptés -->
                        <div class="mt-4 text-center">
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">Paiements 100% sécurisés</p>
                            <div class="flex justify-center items-center space-x-2 flex-wrap gap-2">
                                <div class="bg-white dark:bg-gray-900 rounded px-2 py-1 shadow-sm text-xs font-medium border border-gray-200 dark:border-gray-700">🔒 SSL</div>
                                <div class="bg-white dark:bg-gray-900 rounded px-2 py-1 shadow-sm text-xs font-medium border border-gray-200 dark:border-gray-700">✅ Vérifié</div>
                                <div class="bg-green-100 dark:bg-green-900 rounded px-2 py-1 shadow-sm text-xs font-medium border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300">⚡ MaishaPay</div>
                            </div>
                        </div>
                    </div>

                    <!-- Message d'instruction (affiché par défaut) -->
                    <div id="deliveryInstructionMessage" class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle mr-3 text-yellow-600"></i>
                            <span class="text-yellow-800 text-sm">Veuillez d'abord remplir vos informations de livraison ci-contre.</span>
                        </div>
                    </div>
                </div>
            </div>
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

    // Charger l'adresse par défaut au chargement de la page
    loadDefaultAddress();

    // Gestion du bouton de géolocalisation
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
                            getCurrentLocationBtn.className = 'inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 border border-blue-300 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium';
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
                        getCurrentLocationBtn.className = 'inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 border border-blue-300 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium';
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

    // Fonction pour charger l'adresse par défaut
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
                
                // Pré-remplir le formulaire
                document.getElementById('full_name').value = address.full_name;
                document.getElementById('phone').value = address.phone;
                document.getElementById('email').value = address.email;
                document.getElementById('city').value = address.city;
                document.getElementById('commune').value = address.commune;
                document.getElementById('address').value = address.address;
                document.getElementById('notes').value = address.notes || '';
                
                // Charger les coordonnées GPS si disponibles
                if (address.latitude) {
                    document.getElementById('latitude').value = address.latitude;
                }
                if (address.longitude) {
                    document.getElementById('longitude').value = address.longitude;
                }

                // Afficher les informations confirmées
                showConfirmedInfo(address);
            }
        })
        .catch(error => console.error('Erreur lors du chargement de l\'adresse:', error));
    }

    // Fonction pour afficher les informations confirmées
    function showConfirmedInfo(data) {
        document.getElementById('confirmed_name').textContent = data.full_name;
        document.getElementById('confirmed_phone').textContent = data.phone;
        document.getElementById('confirmed_email').textContent = data.email;
        document.getElementById('confirmed_city').textContent = data.city;
        document.getElementById('confirmed_commune').textContent = data.commune;
        document.getElementById('confirmed_address').textContent = data.address;
        
        // Afficher les coordonnées GPS si disponibles
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

        // Masquer le formulaire et afficher les infos confirmées
        deliveryForm.classList.add('hidden');
        deliveryInfoConfirmed.classList.remove('hidden');

        // Afficher le bouton de paiement
        paymentButtonContainer.classList.remove('hidden');
        deliveryInstructionMessage.classList.add('hidden');
    }

    // Gestion de la soumission du formulaire
    deliveryForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validation du formulaire
        if (!deliveryForm.checkValidity()) {
            e.stopPropagation();
            deliveryForm.classList.add('was-validated');
            return;
        }

        // Récupérer les données du formulaire
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

        // Afficher un loader
        const submitBtn = deliveryForm.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enregistrement...';
        submitBtn.className = 'w-full mt-6 bg-gray-400 text-white py-3 px-6 rounded-lg cursor-not-allowed font-medium text-lg';

        // Déterminer si c'est une création ou une mise à jour
        const url = savedAddressId ? `/delivery-address/${savedAddressId}` : '/delivery-address';
        const method = savedAddressId ? 'PUT' : 'POST';

        // Envoyer les données au serveur
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
                
                // Afficher les informations confirmées
                showConfirmedInfo(data.data);

                // Afficher un message de succès
                showToast('Adresse de livraison enregistrée avec succès', 'success');

                // Scroll vers le récapitulatif sur mobile
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
            submitBtn.className = 'w-full mt-6 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium text-lg';
        });
    });

    // Bouton pour modifier les informations
    editDeliveryBtn.addEventListener('click', function() {
        deliveryForm.classList.remove('hidden');
        deliveryInfoConfirmed.classList.add('hidden');
        paymentButtonContainer.classList.add('hidden');
        deliveryInstructionMessage.classList.remove('hidden');
        
        // Scroll vers le formulaire
        deliveryForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    // Fonction pour afficher un toast
    function showToast(message, type = 'success') {
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
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

    // Gestion du formulaire de paiement MaishaPay
    const maishapayForm = document.getElementById('maishapayForm');
    if (maishapayForm) {
        maishapayForm.addEventListener('submit', function(e) {
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

            // Afficher un loader sur le bouton
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Redirection vers MaishaPay...';
            submitBtn.className = 'w-full bg-gray-400 text-white py-3 px-6 rounded-lg cursor-not-allowed font-medium text-lg flex items-center justify-center';
        });
    }
});
</script>
@endpush
@endsection 