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
@section('title', 'Mes Commandes')
@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-950 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="flex items-start gap-4">
                <x-icon icon="fas fa-shopping-cart" size="lg" tone="primary" class="mt-0.5" />
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Mes Commandes</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Suivez vos achats et confirmez la réception de vos commandes.</p>
                </div>
            </div>
            <div class="flex flex-col gap-2.5 sm:flex-row sm:items-center">
                <x-button-primary onclick="showScanInstructions()">
                    <i class="fas fa-qrcode mr-2"></i> Scanner QR Code
                </x-button-primary>
                <x-button-outline href="{{ route('local-delivery.user', 'buyer') }}">
                    <i class="fas fa-shipping-fast mr-2"></i> Livraisons Locales
                </x-button-outline>
                <x-button-outline href="{{ route('orders.my-sales') }}">
                    <i class="fas fa-store mr-2"></i> Mes Ventes
                </x-button-outline>
            </div>
        </div>

        @if($orders->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 mt-10">
                @foreach($orders as $order)
                    @php
                        $badgeVariant = match($order->status) {
                            'pending' => 'soft-warning',
                            'confirmed' => 'soft-info',
                            'shipped' => 'soft-primary',
                            'delivered' => 'soft-success',
                            'cancelled' => 'soft-danger',
                            default => 'soft-secondary',
                        };
                    @endphp
                    <x-card class="flex flex-col">
                        <!-- Header -->
                        <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-gray-100 dark:border-gray-700/50">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Commande</p>
                                <p class="mt-0.5 text-sm font-semibold text-gray-900 dark:text-white">{{ $order->order_number }}</p>
                            </div>
                            <x-badge :variant="$badgeVariant">{{ $order->status_text }}</x-badge>
                        </div>

                        <!-- Body -->
                        <div class="flex-1 p-5">
                            <!-- Produit -->
                            <div class="flex gap-4">
                                <div class="flex-shrink-0">
                                    @if($order->item->images && count($order->item->images) > 0)
                                        <div class="w-20 h-20 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center overflow-hidden">
                                            <img src="{{ asset('storage/' . $order->item->images[0]) }}"
                                                 class="w-full h-full object-cover"
                                                 alt="{{ $order->item->name }}"
                                                 loading="lazy">
                                        </div>
                                    @else
                                        <div class="w-20 h-20 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-sm text-gray-900 dark:text-white leading-snug">{{ Str::limit($order->item->name, 40) }}</p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Quantité : {{ $order->quantity }}</p>
                                    <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white tabular-nums">{{ $order->formatted_total_price }}</p>
                                </div>
                            </div>

                            <!-- Vendeur & date -->
                            <div class="mt-5 grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Vendeur</p>
                                    <p class="mt-0.5 truncate text-sm font-medium text-gray-900 dark:text-white">{{ $order->item->user->name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Date</p>
                                    <p class="mt-0.5 text-sm font-medium text-gray-900 dark:text-white">{{ $order->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>

                            <!-- Adresse de livraison -->
                            @if($order->deliveryAddress)
                                <div class="mt-5 rounded-lg border border-gray-100 dark:border-gray-700/50 bg-gray-50/70 dark:bg-gray-900/40 px-4 py-3">
                                    <div class="flex items-center gap-1.5 text-xs font-medium text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-map-marker-alt"></i> Livraison
                                    </div>
                                    <p class="mt-1.5 text-sm font-medium text-gray-900 dark:text-white">{{ $order->deliveryAddress->city }}</p>
                                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($order->deliveryAddress->address, 30) }}</p>
                                </div>
                            @elseif($order->shipping_city && $order->shipping_city !== 'À définir')
                                <div class="mt-5 rounded-lg border border-gray-100 dark:border-gray-700/50 bg-gray-50/70 dark:bg-gray-900/40 px-4 py-3">
                                    <div class="flex items-center gap-1.5 text-xs font-medium text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-map-marker-alt"></i> Livraison
                                    </div>
                                    <p class="mt-1.5 text-sm font-medium text-gray-900 dark:text-white">{{ $order->shipping_city }}</p>
                                    @if($order->shipping_address && $order->shipping_address !== 'À définir')
                                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($order->shipping_address, 30) }}</p>
                                    @endif
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="mt-6 flex flex-col gap-2.5">
                                <x-button-primary variant="secondary" class="w-full" href="{{ route('orders.show', $order) }}">
                                    <i class="fas fa-eye mr-2"></i>
                                    Voir détails
                                </x-button-primary>

                                @if(in_array($order->status, ['confirmed', 'shipped']))
                                    @if($order->localDelivery)
                                        <x-button-outline class="w-full" href="{{ route('local-delivery.show', $order->localDelivery) }}">
                                            <i class="fas fa-shipping-fast mr-2"></i>
                                            Livraison {{ ucfirst(str_replace('_', ' ', $order->localDelivery->status)) }}
                                        </x-button-outline>
                                    @else
                                        <x-button-outline class="w-full" href="{{ route('local-delivery.user', 'buyer') }}">
                                            <i class="fas fa-handshake mr-2"></i>
                                            Livraison locale disponible
                                        </x-button-outline>
                                    @endif
                                @endif

                                @if($order->scan_token)
                                    <x-button-outline class="w-full" href="{{ route('orders.scan', $order->scan_token) }}">
                                        <i class="fas fa-qrcode mr-2"></i>
                                        Scanner / Confirmer réception
                                    </x-button-outline>
                                @endif

                                @if($order->status === 'pending')
                                    <x-button-primary variant="destructive" class="w-full"
                                                      data-order-id="{{ $order->id }}"
                                                      onclick="cancelOrder(this.dataset.orderId)">
                                        <i class="fas fa-times mr-2"></i>
                                        Annuler
                                    </x-button-primary>
                                @endif

                                @if(in_array($order->status, ['shipped', 'delivered']) && !$order->confirmed_by_buyer_at)
                                    <x-button-primary class="w-full"
                                                      data-order-id="{{ $order->id }}"
                                                      id="confirm-btn-{{ $order->id }}"
                                                      onclick="confirmDelivery(this.dataset.orderId)">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Commande Reçue
                                    </x-button-primary>
                                @endif

                                @if($order->confirmed_by_buyer_at)
                                    <div class="flex items-center gap-2 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                                        <i class="fas fa-check-circle"></i>
                                        Réception confirmée le {{ $order->confirmed_by_buyer_at->format('d/m/Y') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </x-card>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="mt-10 flex justify-center">
                    <x-card class="p-4">
                        {{ $orders->links() }}
                    </x-card>
                </div>
            @endif
        @else
            <!-- État vide -->
            <x-card class="px-6 py-16 mt-10">
                <div class="mx-auto max-w-md text-center">
                    <div class="mx-auto w-16 h-16 rounded-full bg-vinted-primary-50 dark:bg-vinted-primary-500/20 flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-2xl text-vinted-primary-600 dark:text-vinted-primary-300"></i>
                    </div>
                    <h3 class="mt-6 text-lg font-semibold text-gray-900 dark:text-white">Aucune commande</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Vous n'avez pas encore passé de commande. Découvrez nos produits et passez votre première commande !
                    </p>
                    <x-button-primary class="mt-6" href="{{ route('items.index') }}">
                        <i class="fas fa-search mr-2"></i>
                        Découvrir des articles
                    </x-button-primary>
                </div>
            </x-card>
        @endif
    </div>
</div>

<script>
function cancelOrder(orderId) {
    const toast = window.showToast || window.alert;
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
                toast(data.error || 'Erreur lors de l\'annulation', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toast('Une erreur est survenue', 'error');
        });
    }
}

function confirmDelivery(orderId) {
    const btn = document.getElementById('confirm-btn-' + orderId);
    const toast = window.showToast || window.alert;

    const note = prompt('Confirmez-vous avoir reçu votre commande ?\n\nVous pouvez ajouter un commentaire (optionnel) :');

    if (note !== null) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Confirmation...';

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
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(err.error || 'Erreur serveur'); });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                toast(data.message, 'success');
                window.location.reload();
            } else {
                toast(data.error || 'Erreur lors de la confirmation', 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Commande Reçue';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toast('Erreur : ' + error.message, 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Commande Reçue';
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" id="scanModal">
            <div class="w-full max-w-lg overflow-hidden rounded-xl border border-gray-200 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-800">
                <!-- Header -->
                <div class="flex items-start justify-between gap-4 p-6 pb-4">
                    <div class="flex items-start gap-3">
                        <div class="flex w-10 h-10 flex-shrink-0 items-center justify-center rounded-lg bg-vinted-primary-50 text-vinted-primary-600 dark:bg-vinted-primary-500/20 dark:text-vinted-primary-300">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <div>
                            <h4 class="text-base font-semibold text-gray-900 dark:text-white">Scanner un QR Code</h4>
                            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Confirmez la réception en scannant le QR code de votre facture.</p>
                        </div>
                    </div>
                    <button type="button"
                            onclick="closeScanModal()"
                            aria-label="Fermer"
                            class="flex w-8 h-8 items-center justify-center rounded-md text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-300 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 pt-2">
                    <div id="scanner-container" class="text-center">
                        <div id="scanner-status" class="py-10">
                            <div class="mx-auto w-12 h-12 rounded-full bg-vinted-primary-50 dark:bg-vinted-primary-500/20 flex items-center justify-center">
                                <i class="fas fa-spinner fa-spin text-vinted-primary-600 text-xl"></i>
                            </div>
                            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 font-medium">Initialisation de la caméra...</p>
                        </div>
                        <video id="qr-video" class="mx-auto w-full max-w-sm rounded-lg border border-gray-200 shadow-sm dark:border-gray-700 hidden"></video>
                        <canvas id="qr-canvas" class="hidden"></canvas>
                    </div>

                    <!-- Instructions -->
                    <div class="mt-4 rounded-lg border border-gray-100 dark:border-gray-700/50 bg-gray-50/70 dark:bg-gray-900/40 px-4 py-3">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Instructions</p>
                        <ul class="mt-2 space-y-1.5 text-sm text-gray-600 dark:text-gray-300">
                            <li class="flex items-start gap-2">
                                <i class="fas fa-circle text-[6px] mt-1.5 text-vinted-primary-400 flex-shrink-0"></i>
                                Autorisez l'accès à la caméra quand le navigateur vous le demande
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-circle text-[6px] mt-1.5 text-vinted-primary-400 flex-shrink-0"></i>
                                Pointez la caméra vers le QR code sur la facture
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-circle text-[6px] mt-1.5 text-vinted-primary-400 flex-shrink-0"></i>
                                La détection se fait automatiquement
                            </li>
                        </ul>
                    </div>

                    <!-- Success Message -->
                    <div id="scan-result" class="hidden mt-4 rounded-md border border-emerald-200 bg-emerald-50 p-4">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-emerald-500"></i>
                            <span class="text-sm font-medium text-emerald-800">QR Code détecté !</span>
                        </div>
                        <p class="mt-1 text-sm text-emerald-700">Redirection en cours...</p>
                    </div>

                    <!-- Error Message -->
                    <div id="scan-error" class="hidden mt-4 rounded-md border border-red-200 bg-red-50 p-4">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle text-red-500"></i>
                            <span class="text-sm font-medium text-red-800">Erreur :</span>
                        </div>
                        <div id="error-message" class="mt-2 text-sm text-red-700"></div>

                        <button type="button"
                                onclick="retryScanner()"
                                class="mt-3 w-full inline-flex items-center justify-center px-4 py-2 bg-vinted-primary-600 text-white text-sm font-medium rounded-md hover:bg-vinted-primary-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-vinted-primary-300 transition-colors">
                            <i class="fas fa-redo mr-2"></i>
                            Réessayer après avoir autorisé
                        </button>
                    </div>

                    <!-- Alternative Options -->
                    <div id="alternative-options" class="hidden">
                        <div class="my-4 border-t border-gray-100 dark:border-gray-700/50"></div>
                        <h6 class="text-center text-sm font-semibold text-gray-900 dark:text-white">Autres options de confirmation</h6>

                        <div class="mt-3 rounded-md border border-amber-200 bg-amber-50 p-4">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-lightbulb text-amber-500"></i>
                                <span class="text-sm font-medium text-amber-800">Astuce :</span>
                            </div>
                            <p class="mt-1 text-sm text-amber-700">Cliquez sur le bouton "Scanner / Confirmer réception" sur votre commande ci-dessous.</p>
                        </div>

                        <button type="button"
                                onclick="scrollToOrders()"
                                class="mt-3 w-full inline-flex items-center justify-center px-4 py-2.5 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-list mr-2"></i>
                            Voir mes commandes
                        </button>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end gap-2.5 border-t border-gray-100 dark:border-gray-700/50 bg-gray-50/70 dark:bg-gray-900/40 p-4">
                    <button type="button"
                            onclick="closeScanModal()"
                            class="inline-flex items-center justify-center px-4 py-2.5 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
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
                    resultDiv.innerHTML = '<div class="flex items-center gap-2"><i class="fas fa-check-circle text-emerald-500"></i><span class="text-sm font-medium text-emerald-800">QR Code détecté !</span></div><p class="mt-1 text-sm text-emerald-700">' + code.data + '</p>';

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
                <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-900 rounded-md">
                    <strong>Solutions possibles :</strong>
                    <ol class="mb-0 mt-2 space-y-1">
                        <li>Cliquez sur l'icône 🔒 ou ⓘ à gauche de l'URL dans la barre d'adresse</li>
                        <li>Cherchez "Caméra" dans les permissions</li>
                        <li>Changez en "Autoriser"</li>
                        <li>Rechargez la page (F5)</li>
                        <li>Cliquez à nouveau sur "Réessayer"</li>
                    </ol>
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-2 mt-2">
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
    document.getElementById('scanner-status').innerHTML = '<div class="mx-auto w-12 h-12 rounded-full bg-vinted-primary-50 dark:bg-vinted-primary-500/20 flex items-center justify-center"><i class="fas fa-spinner fa-spin text-vinted-primary-600 text-xl"></i></div><p class="mt-3 text-sm text-gray-500 dark:text-gray-400 font-medium">Initialisation de la caméra...</p>';

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