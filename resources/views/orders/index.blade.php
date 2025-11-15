@extends('app')

@push('scripts')
<!-- jsQR Library pour scanner QR codes -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
// Vérifier que jsQR est chargé
console.log('jsQR chargé ?', typeof jsQR !== 'undefined' ? '✅ OUI' : '❌ NON');
if (typeof jsQR === 'undefined') {
    console.error('❌ jsQR n\'a pas pu être chargé depuis le CDN');
    console.log('Tentative de chargement depuis un CDN alternatif...');
    
    // Fallback vers unpkg
    const script = document.createElement('script');
    script.src = 'https://unpkg.com/jsqr@1.4.0/dist/jsQR.js';
    script.onload = () => console.log('✅ jsQR chargé depuis unpkg');
    script.onerror = () => console.error('❌ Échec chargement jsQR depuis unpkg aussi');
    document.head.appendChild(script);
}
</script>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center mb-4 lg:mb-0">
                    <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-shopping-cart text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Mes Commandes</h1>
                        <p class="text-gray-600 dark:text-gray-300 text-sm mt-1">Suivez vos achats et confirmez la réception</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="button" 
                            onclick="showScanInstructions()"
                            class="inline-flex items-center justify-center px-6 py-3 border border-emerald-300 text-emerald-700 font-semibold rounded-xl hover:bg-emerald-50 hover:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200">
                        <i class="fas fa-qrcode mr-2"></i>
                        Scanner QR Code
                    </button>
                    <a href="{{ route('local-delivery.user', 'buyer') }}" 
                       class="inline-flex items-center justify-center px-6 py-3 border border-orange-300 text-orange-700 font-semibold rounded-xl hover:bg-orange-50 hover:border-orange-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200">
                        <i class="fas fa-shipping-fast mr-2"></i>
                        Livraisons Locales
                    </a>
                    <a href="{{ route('orders.my-sales') }}" 
                       class="inline-flex items-center justify-center px-6 py-3 border border-indigo-300 text-indigo-700 font-semibold rounded-xl hover:bg-indigo-50 hover:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                        <i class="fas fa-store mr-2"></i>
                        Mes Ventes
                    </a>
                </div>
            </div>
        </div>

        @if($orders->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($orders as $order)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-indigo-600/10 border border-gray-100/50 overflow-hidden transform hover:scale-105 hover:shadow-2xl transition-all duration-300">
                        <!-- Header de la carte -->
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                            <div class="flex justify-between items-center">
                                <h6 class="text-sm font-bold text-gray-900 dark:text-white">{{ $order->order_number }}</h6>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $order->status_badge_class === 'bg-success' ? 'bg-emerald-100 text-emerald-800' : ($order->status_badge_class === 'bg-warning' ? 'bg-yellow-100 text-yellow-800' : ($order->status_badge_class === 'bg-info' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100')) }}">
                                    {{ $order->status_text }}
                                </span>
                            </div>
                        </div>

                        <div class="p-6">
                            <!-- Produit et détails -->
                            <div class="flex space-x-4 mb-6">
                                <div class="flex-shrink-0">
                                    @if($order->item->images && count($order->item->images) > 0)
                                        <div class="w-20 h-20 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center justify-center overflow-hidden">
                                            <img src="{{ asset('storage/' . $order->item->images[0]) }}" 
                                                 class="w-full h-full object-cover" 
                                                 alt="{{ $order->item->name }}"
                                                 loading="lazy">
                                        </div>
                                    @else
                                        <div class="w-20 h-20 bg-gray-50 dark:bg-gray-900 rounded-xl flex items-center justify-center border border-gray-200 dark:border-gray-700">
                                            <i class="fas fa-image text-gray-400 text-xl"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h6 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">{{ Str::limit($order->item->name, 40) }}</h6>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                                        <span class="font-medium">Quantité:</span> {{ $order->quantity }}
                                    </p>
                                    <p class="text-xl font-bold text-indigo-600">
                                        {{ $order->formatted_total_price }}
                                    </p>
                                </div>
                            </div>

                            <!-- Informations vendeur et date -->
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Vendeur</p>
                                    <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $order->item->user->name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Date</p>
                                    <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $order->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>

                            <!-- Adresse de livraison -->
                            @if($order->deliveryAddress)
                                <div class="mb-6 p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-map-marker-alt text-indigo-500 mr-2"></i>
                                        <span class="text-xs text-indigo-700 font-medium">Livraison</span>
                                    </div>
                                    <p class="font-semibold text-indigo-900 text-sm">{{ $order->deliveryAddress->city }}</p>
                                    <p class="text-xs text-indigo-600 mt-1">{{ Str::limit($order->deliveryAddress->address, 30) }}</p>
                                </div>
                            @elseif($order->shipping_city && $order->shipping_city !== 'À définir')
                                <div class="mb-6 p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-map-marker-alt text-indigo-500 mr-2"></i>
                                        <span class="text-xs text-indigo-700 font-medium">Livraison</span>
                                    </div>
                                    <p class="font-semibold text-indigo-900 text-sm">{{ $order->shipping_city }}</p>
                                    @if($order->shipping_address && $order->shipping_address !== 'À définir')
                                        <p class="text-xs text-indigo-600 mt-1">{{ Str::limit($order->shipping_address, 30) }}</p>
                                    @endif
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="space-y-3">
                                <!-- Voir détails -->
                                <a href="{{ route('orders.show', $order) }}" 
                                   class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 hover:from-indigo-600 hover:to-indigo-700 hover:shadow-xl hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300">
                                    <i class="fas fa-eye mr-2"></i>
                                    Voir détails
                                </a>
                                
                                <!-- Livraison locale -->
                                @if(in_array($order->status, ['confirmed', 'shipped']))
                                    @if($order->localDelivery)
                                        <a href="{{ route('local-delivery.show', $order->localDelivery) }}" 
                                           class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold rounded-xl shadow-lg shadow-orange-500/25 hover:from-orange-600 hover:to-orange-700 hover:shadow-xl hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-300">
                                            <i class="fas fa-shipping-fast mr-2"></i>
                                            Livraison {{ ucfirst(str_replace('_', ' ', $order->localDelivery->status)) }}
                                        </a>
                                    @else
                                        <a href="{{ route('local-delivery.user', 'buyer') }}" 
                                           class="w-full inline-flex items-center justify-center px-4 py-3 border border-orange-300 text-orange-700 font-semibold rounded-xl hover:bg-orange-50 hover:border-orange-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200">
                                            <i class="fas fa-handshake mr-2"></i>
                                            Livraison locale disponible
                                        </a>
                                    @endif
                                @endif
                                
                                <!-- Scanner QR -->
                                @if($order->scan_token)
                                    <a href="{{ route('orders.scan', $order->scan_token) }}" 
                                       class="w-full inline-flex items-center justify-center px-4 py-3 border border-indigo-300 text-indigo-700 font-semibold rounded-xl hover:bg-indigo-50 hover:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                                        <i class="fas fa-qrcode mr-2"></i>
                                        Scanner / Confirmer réception
                                    </a>
                                @endif
                                
                                <!-- Annuler commande -->
                                @if($order->status === 'pending')
                                    <button class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold rounded-xl shadow-lg shadow-red-500/25 hover:from-red-600 hover:to-red-700 hover:shadow-xl hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-300" 
                                            data-order-id="{{ $order->id }}"
                                            onclick="cancelOrder(this.dataset.orderId)">
                                        <i class="fas fa-times mr-2"></i>
                                        Annuler
                                    </button>
                                @endif

                                <!-- Confirmer réception -->
                                @if(in_array($order->status, ['shipped', 'delivered']) && !$order->confirmed_by_buyer_at && !$order->scan_token)
                                    <button class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/25 hover:from-emerald-600 hover:to-emerald-700 hover:shadow-xl hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-300" 
                                            data-order-id="{{ $order->id }}"
                                            onclick="confirmDelivery(this.dataset.orderId)">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Commande Reçue
                                    </button>
                                @endif

                                <!-- Confirmation de réception -->
                                @if($order->confirmed_by_buyer_at)
                                    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                                        <div class="flex items-center">
                                            <i class="fas fa-check-circle text-emerald-500 mr-2"></i>
                                            <span class="text-sm text-emerald-800 font-medium">
                                                Réception confirmée le {{ $order->confirmed_by_buyer_at->format('d/m/Y') }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="mt-12 flex justify-center">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-4">
                        {{ $orders->links() }}
                    </div>
                </div>
            @endif
        @else
            <!-- État vide -->
            <div class="text-center py-16">
                <div class="w-24 h-24 bg-gradient-to-r from-indigo-100 to-indigo-200 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-shopping-cart text-indigo-500 text-3xl"></i>
                </div>
                <h4 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Aucune commande</h4>
                <p class="text-gray-600 dark:text-gray-300 mb-8 max-w-md mx-auto">
                    Vous n'avez pas encore passé de commande. Découvrez nos produits et passez votre première commande !
                </p>
                <a href="{{ route('items.index') }}" 
                   class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 hover:from-indigo-600 hover:to-indigo-700 hover:shadow-xl hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300">
                    <i class="fas fa-search mr-2"></i>
                    Découvrir des articles
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function cancelOrder(orderId) {
    if (confirm('Êtes-vous sûr de vouloir annuler cette commande ?')) {
        fetch(`/orders/${orderId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Erreur lors de l\'annulation');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue');
        });
    }
}

function confirmDelivery(orderId) {
    // Demander une confirmation avec possibilité d'ajouter un commentaire
    const note = prompt('Confirmez-vous avoir reçu votre commande ?\n\nVous pouvez ajouter un commentaire (optionnel) :');
    
    if (note !== null) { // L'utilisateur n'a pas cliqué sur Annuler
        fetch(`/orders/${orderId}/confirm-delivery`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                note: note || ''
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.error || 'Erreur lors de la confirmation');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue lors de la confirmation');
        });
    }
}

async function checkCameraPermission() {
    try {
        // Vérifier si l'API est disponible
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            return {
                granted: false,
                error: 'NotSupportedError',
                message: 'Votre navigateur ne supporte pas l\'accès à la caméra. Utilisez Chrome, Firefox ou Safari récent.'
            };
        }
        
        // Essayer d'accéder à la caméra
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        
        // Si succès, arrêter immédiatement le stream
        stream.getTracks().forEach(track => track.stop());
        
        return { granted: true };
    } catch (err) {
        return {
            granted: false,
            error: err.name,
            message: err.message
        };
    }
}

function showScanInstructions() {
    // Créer le modal avec scanner intégré
    const modalHtml = `
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" id="scanModal">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <!-- Header -->
                <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white p-6 rounded-t-2xl">
                    <div class="flex justify-between items-center">
                        <h5 class="text-xl font-bold flex items-center">
                            <i class="fas fa-qrcode mr-3"></i>
                            Scanner un QR Code
                        </h5>
                        <button type="button" 
                                onclick="closeScanModal()" 
                                class="text-white hover:text-gray-200 transition-colors duration-200">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Body -->
                <div class="p-6">
                    <div id="scanner-container" class="text-center">
                        <div id="scanner-status" class="mb-6">
                            <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-spinner fa-spin text-emerald-500 text-2xl"></i>
                            </div>
                            <p class="text-gray-700 dark:text-gray-200 font-medium">Initialisation de la caméra...</p>
                        </div>
                        <video id="qr-video" class="w-full max-w-md mx-auto rounded-xl shadow-lg hidden"></video>
                        <canvas id="qr-canvas" class="hidden"></canvas>
                    </div>
                    
                    <!-- Instructions -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mt-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-500 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-blue-800 mb-2">Instructions :</h3>
                                <ul class="text-sm text-blue-700 space-y-1">
                                    <li class="flex items-start">
                                        <span class="w-2 h-2 bg-blue-400 rounded-full mt-2 mr-2 flex-shrink-0"></span>
                                        Autorisez l'accès à la caméra quand le navigateur vous le demande
                                    </li>
                                    <li class="flex items-start">
                                        <span class="w-2 h-2 bg-blue-400 rounded-full mt-2 mr-2 flex-shrink-0"></span>
                                        Pointez la caméra vers le QR code sur la facture
                                    </li>
                                    <li class="flex items-start">
                                        <span class="w-2 h-2 bg-blue-400 rounded-full mt-2 mr-2 flex-shrink-0"></span>
                                        La détection se fait automatiquement
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Success Message -->
                    <div id="scan-result" class="hidden bg-emerald-50 border border-emerald-200 rounded-xl p-4 mt-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-emerald-800">QR Code détecté !</h3>
                                <p class="text-sm text-emerald-700 mt-1">Redirection en cours...</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Error Message -->
                    <div id="scan-error" class="hidden bg-red-50 border border-red-200 rounded-xl p-4 mt-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-red-800 mb-2">Erreur :</h3>
                                <div id="error-message" class="text-sm text-red-700"></div>
                                
                                <button type="button" 
                                        onclick="retryScanner()"
                                        class="mt-4 w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-500 text-white font-semibold rounded-lg hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-200">
                                    <i class="fas fa-redo mr-2"></i>
                                    Réessayer après avoir autorisé
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Alternative Options -->
                    <div id="alternative-options" class="hidden">
                        <div class="border-t border-gray-200 dark:border-gray-700 my-6"></div>
                        <h6 class="text-center text-lg font-semibold text-gray-900 dark:text-white mb-4">Autres options de confirmation</h6>
                        
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-lightbulb text-yellow-500 text-lg"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-semibold text-yellow-800">Astuce :</h3>
                                    <p class="text-sm text-yellow-700 mt-1">Cliquez sur le bouton "Scanner / Confirmer réception" sur votre commande ci-dessous.</p>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" 
                                onclick="scrollToOrders()"
                                class="w-full inline-flex items-center justify-center px-4 py-3 border border-indigo-300 text-indigo-700 font-semibold rounded-xl hover:bg-indigo-50 hover:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                            <i class="fas fa-list mr-2"></i>
                            Voir mes commandes
                        </button>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 rounded-b-2xl border-t border-gray-200 dark:border-gray-700">
                    <button type="button" 
                            onclick="closeScanModal()"
                            class="w-full inline-flex items-center justify-center px-4 py-3 bg-gray-600 text-white font-semibold rounded-xl hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Ajouter le modal au DOM s'il n'existe pas déjà
    if (!document.getElementById('scanModal')) {
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }
    
    // Afficher le modal
    document.getElementById('scanModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Démarrer le scanner
    setTimeout(() => {
        startQRScanner();
    }, 100);
}

function closeScanModal() {
    stopScanner();
    const modal = document.getElementById('scanModal');
    if (modal) {
        modal.remove();
    }
    document.body.style.overflow = 'auto';
}

let videoStream = null;
let scanningInterval = null;

function startQRScanner() {
    const video = document.getElementById('qr-video');
    const canvas = document.getElementById('qr-canvas');
    const context = canvas.getContext('2d');
    const statusDiv = document.getElementById('scanner-status');
    const resultDiv = document.getElementById('scan-result');
    const errorDiv = document.getElementById('scan-error');
    
    // VÉRIFICATION CRITIQUE : jsQR est-il chargé ?
    if (typeof jsQR === 'undefined') {
        console.error('❌ ERREUR CRITIQUE : jsQR n\'est pas chargé !');
        statusDiv.classList.add('hidden');
        errorDiv.classList.remove('hidden');
        document.getElementById('error-message').innerHTML = `
            <strong>Erreur de chargement de la bibliothèque jsQR</strong>
            <div class="mt-2">
                <p>La bibliothèque de scan de QR code n'a pas pu être chargée.</p>
                <ol>
                    <li>Vérifiez votre connexion internet</li>
                    <li>Rechargez la page (F5)</li>
                    <li>Si le problème persiste, utilisez le bouton "Scanner / Confirmer réception" sur vos commandes</li>
                </ol>
            </div>
        `;
        document.getElementById('alternative-options').classList.remove('hidden');
        return;
    }
    
    console.log('✅ jsQR est chargé et prêt');
    
    // Vérifier l'API getUserMedia
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        console.error('❌ getUserMedia n\'est pas supporté');
        statusDiv.classList.add('hidden');
        errorDiv.classList.remove('hidden');
        document.getElementById('error-message').innerHTML = `
            <strong>Navigateur non compatible</strong>
            <div class="mt-2">
                <p>Votre navigateur ne supporte pas l'accès à la caméra.</p>
                <p>Utilisez Chrome, Firefox, Safari ou Edge récent.</p>
            </div>
        `;
        document.getElementById('alternative-options').classList.remove('hidden');
        return;
    }
    
    console.log('✅ getUserMedia est supporté');
    console.log('🎥 Demande d\'accès à la caméra...');
    
    // Demander l'accès à la caméra
    navigator.mediaDevices.getUserMedia({ 
        video: { 
            facingMode: 'environment' // Utiliser la caméra arrière sur mobile
        } 
    })
    .then(function(stream) {
        videoStream = stream;
        video.srcObject = stream;
        video.setAttribute('playsinline', true); // iOS
        video.play();
        
        statusDiv.classList.add('hidden');
        video.classList.remove('hidden');
        
        console.log('✅ Caméra activée, démarrage du scan...');
        console.log('📹 Résolution vidéo:', video.videoWidth + 'x' + video.videoHeight);
        
        let scanAttempts = 0;
        
        // Démarrer la détection de QR code
        scanningInterval = setInterval(() => {
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                canvas.height = video.videoHeight;
                canvas.width = video.videoWidth;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                
                // Vérifier que jsQR est toujours disponible
                if (typeof jsQR === 'undefined') {
                    console.error('❌ jsQR n\'est plus disponible pendant le scan');
                    stopScanner();
                    return;
                }
                
                const code = jsQR(imageData.data, imageData.width, imageData.height);
                
                scanAttempts++;
                if (scanAttempts % 10 === 0) {
                    console.log('🔍 Scan en cours... tentative', scanAttempts);
                }
                
                if (code) {
                    // QR Code détecté
                    console.log('✅ QR CODE DÉTECTÉ !');
                    console.log('📝 Données:', code.data);
                    console.log('📍 Position:', code.location);
                    
                    stopScanner();
                    resultDiv.classList.remove('hidden');
                    resultDiv.innerHTML = '<div class="flex"><div class="flex-shrink-0"><i class="fas fa-check-circle text-emerald-500 text-lg"></i></div><div class="ml-3"><h3 class="text-sm font-semibold text-emerald-800">QR Code détecté !</h3><p class="text-sm text-emerald-700 mt-1">' + code.data + '</p></div></div>';
                    
                    // Rediriger vers l'URL du QR code
                    setTimeout(() => {
                        console.log('🔄 Redirection vers:', code.data);
                        window.location.href = code.data;
                    }, 1500);
                }
            }
        }, 300);
    })
    .catch(function(err) {
        console.error('=== ERREUR CAMÉRA ===');
        console.error('Type:', err.name);
        console.error('Message:', err.message);
        console.error('Objet complet:', err);
        console.error('URL actuelle:', window.location.href);
        console.error('Protocol:', window.location.protocol);
        console.error('Hostname:', window.location.hostname);
        
        statusDiv.classList.add('hidden');
        errorDiv.classList.remove('hidden');
        
        let errorMessage = '';
        let detailedInfo = '';
        
        if (err.name === 'NotAllowedError') {
            errorMessage = 'L\'accès à la caméra a été refusé.';
            detailedInfo = `
                <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                    <strong>Solutions possibles :</strong>
                    <ol class="mb-0 mt-2 space-y-1">
                        <li>Cliquez sur l'icône 🔒 ou ⓘ à gauche de l'URL dans la barre d'adresse</li>
                        <li>Cherchez "Caméra" dans les permissions</li>
                        <li>Changez en "Autoriser"</li>
                        <li>Rechargez la page (F5)</li>
                        <li>Cliquez à nouveau sur "Réessayer"</li>
                    </ol>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-2 mt-2">
                        <p class="text-xs text-blue-700"><strong>Info de débogage :</strong><br>
                        URL: ${window.location.href}<br>
                        Protocol: ${window.location.protocol}<br>
                        Hostname: ${window.location.hostname}</p>
                    </div>
                </div>
            `;
        } else if (err.name === 'NotFoundError') {
            errorMessage = 'Aucune caméra détectée sur votre appareil.';
        } else if (err.name === 'NotSupportedError' || err.name === 'TypeError') {
            errorMessage = 'Votre navigateur ne supporte pas l\'accès à la caméra.';
            detailedInfo = '<div class="mt-2"><p class="text-sm">Utilisez Chrome, Firefox ou Safari récent.</p></div>';
        } else if (err.name === 'NotReadableError' || err.name === 'AbortError') {
            errorMessage = 'La caméra est déjà utilisée par une autre application.';
            detailedInfo = '<div class="mt-2"><p class="text-sm">Fermez les autres onglets/applications qui utilisent la caméra.</p></div>';
        } else {
            errorMessage = 'Erreur inattendue: ' + err.message;
            detailedInfo = '<div class="mt-2"><p class="text-sm">Type d\'erreur: ' + err.name + '</p></div>';
        }
        
        document.getElementById('error-message').innerHTML = errorMessage + detailedInfo;
        document.getElementById('alternative-options').classList.remove('hidden');
    });
}

function stopScanner() {
    // Arrêter le flux vidéo
    if (videoStream) {
        videoStream.getTracks().forEach(track => track.stop());
        videoStream = null;
    }
    
    // Arrêter l'intervalle de scan
    if (scanningInterval) {
        clearInterval(scanningInterval);
        scanningInterval = null;
    }
}

function retryScanner() {
    // Réinitialiser l'affichage
    document.getElementById('scan-error').classList.add('hidden');
    document.getElementById('alternative-options').classList.add('hidden');
    document.getElementById('scanner-status').classList.remove('hidden');
    document.getElementById('scanner-status').innerHTML = '<div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fas fa-spinner fa-spin text-emerald-500 text-2xl"></i></div><p class="text-gray-700 dark:text-gray-200 font-medium">Initialisation de la caméra...</p>';
    
    // Réessayer après un court délai
    setTimeout(() => {
        startQRScanner();
    }, 500);
}

function scrollToOrders() {
    // Fermer le modal
    closeScanModal();
    
    // Scroller vers le haut des commandes
    setTimeout(() => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }, 300);
}
</script>
@endsection 