@extends('layouts.admin')

@section('title', 'Traçage Commande #' . $order->order_number)
@section('page-title', 'Traçage de la Commande #' . $order->order_number)

@push('styles')
<!-- Leaflet CSS pour la carte -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #tracking-map {
        height: 500px;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }
    
    .tracking-timeline {
        position: relative;
        padding-left: 3rem;
    }
    
    .tracking-timeline::before {
        content: '';
        position: absolute;
        left: 1rem;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(to bottom, #6A0DAD, #e0e0e0);
    }
    
    .tracking-item {
        position: relative;
        padding-bottom: 2rem;
    }
    
    .tracking-item::before {
        content: '';
        position: absolute;
        left: -2.3rem;
        top: 0.25rem;
        width: 1.5rem;
        height: 1.5rem;
        border-radius: 50%;
        background: white;
        border: 3px solid #6A0DAD;
        box-shadow: 0 0 0 4px rgba(106, 13, 173, 0.1);
        z-index: 1;
    }
    
    .tracking-item.completed::before {
        background: #6A0DAD;
        border-color: #4f0080;
    }
    
    .tracking-item.current::before {
        background: #10B981;
        border-color: #059669;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2);
        }
        50% {
            box-shadow: 0 0 0 8px rgba(16, 185, 129, 0.1);
        }
    }
    
    .distance-badge {
        background: linear-gradient(135deg, #6A0DAD 0%, #8B0DC7 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(106, 13, 173, 0.3);
    }
    
    .info-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
    }
    
    .info-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- En-tête avec actions -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center text-purple-600 hover:text-purple-700 font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Retour aux détails
        </a>
        
        <div class="flex gap-3">
            <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-file-invoice mr-2"></i> Voir Facture
            </a>
            <button type="button" onclick="openUpdateTrackingModal()" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-map-marker-alt mr-2"></i> Mettre à jour Position
            </button>
        </div>
    </div>

    <!-- Informations de la commande -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="info-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Numéro de commande</p>
                    <p class="text-lg font-bold text-gray-900">{{ $order->order_number }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-hashtag text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="info-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Statut</p>
                    @if($currentTracking)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $currentTracking->status_badge_class }}">
                            <i class="fas {{ $currentTracking->status_icon }} mr-1"></i>
                            {{ $currentTracking->status_text }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-secondary">
                            Pas de suivi
                        </span>
                    @endif
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-truck text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="info-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Distance restante</p>
                    @if($currentTracking && $currentTracking->distance_to_customer)
                        <p class="text-lg font-bold text-purple-600">{{ $currentTracking->distance_to_customer }} km</p>
                    @else
                        <p class="text-lg font-bold text-gray-400">-</p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-route text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="info-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Livraison estimée</p>
                    @if($currentTracking && $currentTracking->estimated_delivery)
                        <p class="text-sm font-semibold {{ $currentTracking->is_late ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $currentTracking->formatted_estimated_delivery }}
                        </p>
                    @else
                        <p class="text-sm font-semibold text-gray-400">Non définie</p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte de suivi GPS -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">
                <i class="fas fa-map-marked-alt text-purple-600 mr-2"></i>
                Carte de Suivi GPS
            </h3>
            @if($currentTracking && $currentTracking->distance_to_customer)
                <div class="distance-badge">
                    <i class="fas fa-location-arrow mr-2"></i>
                    {{ $currentTracking->distance_to_customer }} km restants
                </div>
            @endif
        </div>
        
        <div id="tracking-map"></div>
        
        @if($currentTracking)
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-blue-50 rounded-lg p-4">
                <h4 class="font-semibold text-blue-900 mb-2">
                    <i class="fas fa-map-pin text-blue-600 mr-2"></i>
                    Position Actuelle
                </h4>
                <p class="text-sm text-blue-800">
                    {{ $currentTracking->address ?? 'Adresse non renseignée' }}
                </p>
                @if($currentTracking->city)
                    <p class="text-xs text-blue-600 mt-1">{{ $currentTracking->city }}, {{ $currentTracking->country }}</p>
                @endif
            </div>

            <div class="bg-green-50 rounded-lg p-4">
                <h4 class="font-semibold text-green-900 mb-2">
                    <i class="fas fa-flag-checkered text-green-600 mr-2"></i>
                    Destination (Client)
                </h4>
                <p class="text-sm text-green-800">
                    {{ $currentTracking->customer_address ?? $order->shipping_address }}
                </p>
                @if($currentTracking->customer_city || $order->shipping_city)
                    <p class="text-xs text-green-600 mt-1">{{ $currentTracking->customer_city ?? $order->shipping_city }}</p>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Historique de traçage -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-6">
            <i class="fas fa-history text-purple-600 mr-2"></i>
            Historique de Suivi
        </h3>
        
        @if($trackingHistory && $trackingHistory->count() > 0)
            <div class="tracking-timeline">
                @foreach($trackingHistory->sortByDesc('tracked_at') as $index => $tracking)
                    <div class="tracking-item {{ $index === 0 ? 'current' : 'completed' }}">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $tracking->status_badge_class }}">
                                        <i class="fas {{ $tracking->status_icon }} mr-1"></i>
                                        {{ $tracking->status_text }}
                                    </span>
                                    @if($tracking->carrier)
                                        <span class="ml-2 text-xs text-gray-600">
                                            <i class="fas fa-shipping-fast mr-1"></i>
                                            {{ $tracking->carrier }}
                                        </span>
                                    @endif
                                </div>
                                <span class="text-sm text-gray-500">
                                    <i class="far fa-clock mr-1"></i>
                                    {{ $tracking->formatted_tracked_at }}
                                </span>
                            </div>
                            
                            @if($tracking->description)
                                <p class="text-sm text-gray-700 mb-2">{{ $tracking->description }}</p>
                            @endif
                            
                            @if($tracking->address)
                                <p class="text-xs text-gray-600">
                                    <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>
                                    {{ $tracking->address }}
                                    @if($tracking->city)
                                        , {{ $tracking->city }}
                                    @endif
                                </p>
                            @endif
                            
                            @if($tracking->tracking_code)
                                <p class="text-xs text-purple-600 mt-2">
                                    <i class="fas fa-barcode mr-1"></i>
                                    Code de suivi: <strong>{{ $tracking->tracking_code }}</strong>
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-exclamation-circle text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500 text-lg">Aucun historique de suivi disponible</p>
                <button onclick="openUpdateTrackingModal()" class="mt-4 inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-plus mr-2"></i> Ajouter une position
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Modal pour mettre à jour le tracking -->
<div id="updateTrackingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-purple-600 to-purple-800 px-6 py-4 rounded-t-2xl">
            <h3 class="text-xl font-bold text-white">
                <i class="fas fa-map-marker-alt mr-2"></i>
                Mettre à jour la Position de Suivi
            </h3>
        </div>
        
        <form id="trackingUpdateForm" class="p-6" onsubmit="updateTracking(event)">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut *</label>
                    <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="pending">En attente</option>
                        <option value="picked_up">Collecté</option>
                        <option value="in_transit">En transit</option>
                        <option value="out_for_delivery">En cours de livraison</option>
                        <option value="delivered">Livré</option>
                        <option value="failed">Échec</option>
                        <option value="returned">Retourné</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Transporteur</label>
                    <input type="text" name="carrier" placeholder="Ex: DHL, FedEx, Local..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                    <input type="number" step="0.00000001" name="latitude" placeholder="-4.325000" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                    <input type="number" step="0.00000001" name="longitude" placeholder="15.307778" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Adresse Actuelle</label>
                <input type="text" name="address" placeholder="Ex: Avenue de la Libération, Gombe" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ville</label>
                    <input type="text" name="city" placeholder="Kinshasa" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Code de Suivi</label>
                    <input type="text" name="tracking_code" placeholder="Ex: TRACK123456" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" placeholder="Décrivez l'état actuel de la livraison..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Livraison Estimée</label>
                <input type="datetime-local" name="estimated_delivery" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Informations de Destination (Client)</p>
                        <p class="text-xs">
                            <strong>Adresse:</strong> {{ $order->shipping_address }}<br>
                            <strong>Ville:</strong> {{ $order->shipping_city }}<br>
                            <strong>Téléphone:</strong> {{ $order->shipping_phone }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeUpdateTrackingModal()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Annuler
                </button>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-save mr-2"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<!-- Leaflet JS pour la carte -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let map;
let currentMarker;
let destinationMarker;
let routeLine;

// Initialiser la carte
document.addEventListener('DOMContentLoaded', function() {
    initMap();
});

function initMap() {
    @if($currentTracking && $currentTracking->customer_latitude && $currentTracking->customer_longitude)
        const destinationLat = {{ $currentTracking->customer_latitude }};
        const destinationLng = {{ $currentTracking->customer_longitude }};
    @elseif($order->shipping_address)
        // Par défaut, centrer sur Kinshasa si pas de coordonnées
        const destinationLat = -4.325;
        const destinationLng = 15.308;
    @else
        const destinationLat = -4.325;
        const destinationLng = 15.308;
    @endif

    @if($currentTracking && $currentTracking->latitude && $currentTracking->longitude)
        const currentLat = {{ $currentTracking->latitude }};
        const currentLng = {{ $currentTracking->longitude }};
        const centerLat = (currentLat + destinationLat) / 2;
        const centerLng = (currentLng + destinationLng) / 2;
    @else
        const currentLat = null;
        const currentLng = null;
        const centerLat = destinationLat;
        const centerLng = destinationLng;
    @endif

    // Créer la carte
    map = L.map('tracking-map').setView([centerLat, centerLng], 12);

    // Ajouter la couche de tuiles (OpenStreetMap)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    // Icône personnalisée pour la position actuelle
    const currentIcon = L.divIcon({
        html: '<i class="fas fa-truck text-blue-600 text-2xl"></i>',
        className: 'custom-div-icon',
        iconSize: [30, 30],
        iconAnchor: [15, 15]
    });

    // Icône personnalisée pour la destination
    const destinationIcon = L.divIcon({
        html: '<i class="fas fa-home text-green-600 text-2xl"></i>',
        className: 'custom-div-icon',
        iconSize: [30, 30],
        iconAnchor: [15, 15]
    });

    // Ajouter le marqueur de destination
    destinationMarker = L.marker([destinationLat, destinationLng], {icon: destinationIcon})
        .addTo(map)
        .bindPopup('<div class="text-center"><strong>Destination (Client)</strong><br/>{{ $currentTracking->customer_address ?? $order->shipping_address }}</div>');

    // Ajouter le marqueur de position actuelle si disponible
    if (currentLat && currentLng) {
        currentMarker = L.marker([currentLat, currentLng], {icon: currentIcon})
            .addTo(map)
            .bindPopup('<div class="text-center"><strong>Position Actuelle</strong><br/>{{ $currentTracking->address ?? "En cours..." }}</div>');

        // Tracer une ligne entre la position actuelle et la destination
        routeLine = L.polyline([
            [currentLat, currentLng],
            [destinationLat, destinationLng]
        ], {
            color: '#6A0DAD',
            weight: 3,
            opacity: 0.7,
            dashArray: '10, 10'
        }).addTo(map);

        // Ajuster la vue pour afficher les deux marqueurs
        const bounds = L.latLngBounds([
            [currentLat, currentLng],
            [destinationLat, destinationLng]
        ]);
        map.fitBounds(bounds, {padding: [50, 50]});
    }
}

// Ouvrir le modal de mise à jour
function openUpdateTrackingModal() {
    document.getElementById('updateTrackingModal').style.display = 'flex';
}

// Fermer le modal de mise à jour
function closeUpdateTrackingModal() {
    document.getElementById('updateTrackingModal').style.display = 'none';
    document.getElementById('trackingUpdateForm').reset();
}

// Soumettre le formulaire de mise à jour
function updateTracking(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    // Afficher un loader
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Enregistrement...';
    submitBtn.disabled = true;
    
    fetch('{{ route("admin.orders.tracking.update", $order->id) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Afficher un message de succès
            alert('Position de tracking mise à jour avec succès!');
            
            // Recharger la page
            window.location.reload();
        } else {
            alert('Erreur: ' + (data.message || 'Une erreur est survenue'));
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors de la mise à jour');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Fermer le modal en cliquant en dehors
document.getElementById('updateTrackingModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeUpdateTrackingModal();
    }
});
</script>
@endpush
