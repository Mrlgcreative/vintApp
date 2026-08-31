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
        z-index: 0;
    }
    #tracking-map .leaflet-container {
        z-index: 0;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- En-tête avec actions -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400">
            <i class="fas fa-arrow-left mr-2"></i> Retour aux détails
        </a>
        
        <div class="flex flex-col gap-2 sm:flex-row">
            <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-sky-700">
                <i class="fas fa-file-invoice"></i> Voir Facture
            </a>
            <button type="button" onclick="openUpdateTrackingModal()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700">
                <i class="fas fa-map-marker-alt"></i> Mettre à jour Position
            </button>
        </div>
    </div>

    <!-- Informations de la commande -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="mb-1 text-sm text-slate-600 dark:text-slate-300">Numéro de commande</p>
                    <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $order->order_number }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/30">
                    <i class="fas fa-hashtag text-xl text-primary-600 dark:text-primary-400"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="mb-1 text-sm text-slate-600 dark:text-slate-300">Statut</p>
                    @if($currentTracking)
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset ring-slate-500/10 {{ $currentTracking->status_badge_class }}">
                            <i class="fas {{ $currentTracking->status_icon }} mr-1"></i>
                            {{ $currentTracking->status_text }}
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300">
                            Pas de suivi
                        </span>
                    @endif
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-sky-100 dark:bg-sky-900/30">
                    <i class="fas fa-truck text-xl text-sky-600 dark:text-sky-400"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="mb-1 text-sm text-slate-600 dark:text-slate-300">Distance restante</p>
                    @if($currentTracking && $currentTracking->distance_to_customer)
                        <p class="text-lg font-bold text-primary-600 dark:text-primary-400">{{ $currentTracking->distance_to_customer }} km</p>
                    @else
                        <p class="text-lg font-bold text-slate-400">-</p>
                    @endif
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30">
                    <i class="fas fa-route text-xl text-emerald-600 dark:text-emerald-400"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="mb-1 text-sm text-slate-600 dark:text-slate-300">Livraison estimée</p>
                    @if($currentTracking && $currentTracking->estimated_delivery)
                        <p class="text-sm font-semibold {{ $currentTracking->is_late ? 'text-red-600' : 'text-slate-900 dark:text-white' }}">
                            {{ $currentTracking->formatted_estimated_delivery }}
                        </p>
                    @else
                        <p class="text-sm font-semibold text-slate-400">Non définie</p>
                    @endif
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/30">
                    <i class="fas fa-clock text-xl text-amber-600 dark:text-amber-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte de suivi GPS -->
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800 sm:p-6">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">
                <i class="fas fa-map-marked-alt mr-2 text-primary-600"></i>
                Carte de Suivi GPS
            </h3>
            @if($currentTracking && $currentTracking->distance_to_customer)
                <span class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-2 text-sm font-semibold text-white shadow-lg">
                    <i class="fas fa-location-arrow"></i>
                    {{ $currentTracking->distance_to_customer }} km restants
                </span>
            @endif
        </div>
        
        <div id="tracking-map"></div>
        
        <!-- Affichage de la distance -->
        <div id="distanceInfo" class="mt-4 rounded-xl border-l-4 border-primary-500 bg-gradient-to-r from-primary-50 to-sky-50 p-4 dark:from-primary-900/20 dark:to-sky-900/20" style="display: none;">
            <div class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
                <div class="flex items-center">
                    <i class="fas fa-route mr-3 text-2xl text-primary-600"></i>
                    <div>
                        <p class="text-sm text-slate-600 dark:text-slate-300">Distance entre livreur et client</p>
                        <p class="text-2xl font-bold text-primary-700 dark:text-primary-400" id="distanceValue">- km</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-500 dark:text-slate-400">Temps estimé</p>
                    <p class="text-lg font-semibold text-sky-600" id="estimatedTime">-</p>
                </div>
            </div>
        </div>
        
        @if($currentTracking)
        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="rounded-xl bg-sky-50 p-4 dark:bg-sky-900/20">
                <h4 class="mb-2 font-semibold text-sky-900 dark:text-sky-300">
                    <i class="fas fa-map-pin mr-2 text-sky-600"></i>
                    Position Actuelle
                </h4>
                <p class="text-sm text-sky-800 dark:text-sky-300">
                    {{ $currentTracking->address ?? 'Adresse non renseignée' }}
                </p>
                @if($currentTracking->city)
                    <p class="mt-1 text-xs text-sky-600 dark:text-sky-400">{{ $currentTracking->city }}, {{ $currentTracking->country }}</p>
                @endif
            </div>

            <div class="rounded-xl bg-emerald-50 p-4 dark:bg-emerald-900/20">
                <h4 class="mb-2 font-semibold text-emerald-900 dark:text-emerald-300">
                    <i class="fas fa-flag-checkered mr-2 text-emerald-600"></i>
                    Destination (Client)
                </h4>
                @if($order->deliveryAddress)
                    {{-- Afficher depuis delivery_addresses --}}
                    <p class="text-sm font-semibold text-emerald-900 dark:text-emerald-300">{{ $order->deliveryAddress->full_name }}</p>
                    <p class="mt-1 text-sm text-emerald-800 dark:text-emerald-300">
                        {{ $order->deliveryAddress->address }}
                    </p>
                    <p class="mt-1 text-xs text-emerald-600 dark:text-emerald-400">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        {{ $order->deliveryAddress->commune }}, {{ $order->deliveryAddress->city }}
                    </p>
                    <p class="mt-1 text-xs text-emerald-600 dark:text-emerald-400">
                        <i class="fas fa-phone mr-1"></i>
                        {{ $order->deliveryAddress->phone }}
                    </p>
                    @if($order->deliveryAddress->email)
                        <p class="mt-1 text-xs text-emerald-600 dark:text-emerald-400">
                            <i class="fas fa-envelope mr-1"></i>
                            {{ $order->deliveryAddress->email }}
                        </p>
                    @endif
                @elseif(($order->shipping_address && $order->shipping_address !== 'À définir') || ($currentTracking && $currentTracking->customer_address))
                    {{-- Fallback sur shipping_address --}}
                    <p class="text-sm text-emerald-800 dark:text-emerald-300">
                        {{ $currentTracking->customer_address ?? $order->shipping_address }}
                    </p>
                    @if($currentTracking->customer_city || ($order->shipping_city && $order->shipping_city !== 'À définir'))
                        <p class="mt-1 text-xs text-emerald-600 dark:text-emerald-400">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            {{ $currentTracking->customer_city ?? $order->shipping_city }}
                        </p>
                    @endif
                    @if($order->shipping_phone)
                        <p class="mt-1 text-xs text-emerald-600 dark:text-emerald-400">
                            <i class="fas fa-phone mr-1"></i>
                            {{ $order->shipping_phone }}
                        </p>
                    @endif
                @else
                    <div class="rounded border border-amber-200 bg-amber-50 px-2 py-1 text-sm text-amber-700 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-300">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Adresse non définie
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Historique de traçage -->
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800 sm:p-6">
        <h3 class="mb-6 text-xl font-bold text-slate-900 dark:text-white">
            <i class="fas fa-history mr-2 text-primary-600"></i>
            Historique de Suivi
        </h3>
        
        @if($trackingHistory && $trackingHistory->count() > 0)
            <div class="relative">
                <div class="absolute bottom-2 left-[11px] top-2 w-0.5 rounded-full bg-gradient-to-b from-primary-600 to-slate-200 dark:to-slate-700"></div>
                <div class="space-y-6">
                    @foreach($trackingHistory->sortByDesc('tracked_at') as $index => $tracking)
                        <div class="relative pl-10">
                            <span class="absolute left-0 top-1 flex h-6 w-6 items-center justify-center rounded-full {{ $index === 0 ? 'bg-emerald-500 ring-4 ring-emerald-500/20 animate-pulse' : 'bg-primary-600 ring-4 ring-primary-600/10' }}">
                                <i class="fas fa-circle text-[6px] text-white"></i>
                            </span>
                            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                                <div class="mb-2 flex flex-col items-start justify-between gap-2 sm:flex-row sm:items-center">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset ring-slate-500/10 {{ $tracking->status_badge_class }}">
                                            <i class="fas {{ $tracking->status_icon }} mr-1"></i>
                                            {{ $tracking->status_text }}
                                        </span>
                                        @if($tracking->carrier)
                                            <span class="text-xs text-slate-600 dark:text-slate-300">
                                                <i class="fas fa-shipping-fast mr-1"></i>
                                                {{ $tracking->carrier }}
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-sm text-slate-500 dark:text-slate-400">
                                        <i class="far fa-clock mr-1"></i>
                                        {{ $tracking->formatted_tracked_at }}
                                    </span>
                                </div>
                                
                                @if($tracking->description)
                                    <p class="mb-2 text-sm text-slate-700 dark:text-slate-200">{{ $tracking->description }}</p>
                                @endif
                                
                                @if($tracking->address)
                                    <p class="text-xs text-slate-600 dark:text-slate-300">
                                        <i class="fas fa-map-marker-alt mr-1 text-slate-400"></i>
                                        {{ $tracking->address }}
                                        @if($tracking->city)
                                            , {{ $tracking->city }}
                                        @endif
                                    </p>
                                @endif
                                
                                @if($tracking->tracking_code)
                                    <p class="mt-2 text-xs text-primary-600 dark:text-primary-400">
                                        <i class="fas fa-barcode mr-1"></i>
                                        Code de suivi: <strong>{{ $tracking->tracking_code }}</strong>
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="py-12 text-center">
                <i class="fas fa-exclamation-circle mb-4 text-5xl text-slate-300 dark:text-slate-600"></i>
                <p class="text-lg text-slate-500 dark:text-slate-400">Aucun historique de suivi disponible</p>
                <button onclick="openUpdateTrackingModal()" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700">
                    <i class="fas fa-plus"></i> Ajouter une position
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Modal pour mettre à jour le tracking -->
<div id="updateTrackingModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm" style="display: none;">
    <div class="mx-4 max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white shadow-2xl dark:bg-slate-800">
        <div class="bg-gradient-to-r from-primary-600 to-primary-800 rounded-t-2xl px-6 py-4">
            <h3 class="text-xl font-bold text-white">
                <i class="fas fa-map-marker-alt mr-2"></i>
                Mettre à jour la Position de Suivi
            </h3>
        </div>
        
        <form id="trackingUpdateForm" class="p-6" onsubmit="updateTracking(event)">
            @csrf
            
            <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Statut *</label>
                    <select name="status" required class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
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
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Transporteur</label>
                    <input type="text" name="carrier" placeholder="Ex: DHL, FedEx, Local..." class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                </div>
            </div>

            <div class="mb-4">
                <button type="button" onclick="getCurrentLocation(event)" class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700 transition-colors hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 dark:hover:bg-emerald-900/50">
                    <i class="fas fa-crosshairs"></i>
                    Utiliser ma position actuelle (GPS)
                </button>
            </div>

            <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Latitude</label>
                    <input type="number" step="0.00000001" name="latitude" id="modal_latitude" placeholder="-4.325000" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white" onchange="calculateModalDistance()">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Longitude</label>
                    <input type="number" step="0.00000001" name="longitude" id="modal_longitude" placeholder="15.307778" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white" onchange="calculateModalDistance()">
                </div>
            </div>

            <!-- Affichage de la distance calculée dans le modal -->
            <div id="modalDistanceInfo" class="mb-4 rounded-xl border-l-4 border-primary-500 bg-primary-50 p-3 dark:bg-primary-900/20" style="display: none;">
                <div class="flex flex-col items-start gap-3 sm:flex-row sm:items-center">
                    <i class="fas fa-route mr-3 text-xl text-primary-600"></i>
                    <div>
                        <p class="text-xs text-slate-600 dark:text-slate-300">Distance jusqu'au client</p>
                        <p class="text-lg font-bold text-primary-700 dark:text-primary-400" id="modalDistanceValue">- km</p>
                    </div>
                    <div class="text-right sm:ml-auto">
                        <p class="text-xs text-slate-600 dark:text-slate-300">Temps estimé</p>
                        <p class="text-sm font-semibold text-sky-600" id="modalEstimatedTime">-</p>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Adresse Actuelle</label>
                <input type="text" name="address" placeholder="Ex: Avenue de la Libération, Gombe" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
            </div>

            <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Ville</label>
                    <input type="text" name="city" placeholder="Kinshasa" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Code de Suivi</label>
                    <div class="flex gap-2">
                        <input type="text" id="tracking_code" name="tracking_code" placeholder="Ex: TRACK123456" readonly class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                        <button type="button" onclick="generateTrackingCode()" class="inline-flex items-center whitespace-nowrap rounded-xl bg-primary-100 px-4 py-2.5 text-sm font-medium text-primary-700 transition-colors hover:bg-primary-200 dark:bg-primary-900/30 dark:text-primary-300 dark:hover:bg-primary-900/50">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Code généré automatiquement</p>
                </div>
            </div>

            <div class="mb-4">
                <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Description</label>
                <textarea name="description" rows="3" placeholder="Décrivez l'état actuel de la livraison..." class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white"></textarea>
            </div>

            <div class="mb-6">
                <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Livraison Estimée</label>
                <input type="datetime-local" name="estimated_delivery" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
            </div>

            <div class="mb-6 rounded-xl border-l-4 border-sky-400 bg-sky-50 p-4 dark:bg-sky-900/20">
                <div class="flex items-start">
                    <i class="fas fa-info-circle mt-1 mr-3 text-sky-600"></i>
                    <div class="w-full text-sm text-sky-800 dark:text-sky-300">
                        <p class="mb-1 font-semibold">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            Informations de Destination (Client)
                        </p>
                        @if($order->deliveryAddress)
                            {{-- Afficher depuis delivery_addresses --}}
                            <div class="mt-2 space-y-1 text-xs">
                                <div class="flex items-start">
                                    <i class="fas fa-user mt-0.5 mr-2 text-sky-500"></i>
                                    <div>
                                        <strong>Destinataire:</strong> {{ $order->deliveryAddress->full_name }}
                                    </div>
                                </div>
                                @if($order->deliveryAddress->email)
                                    <div class="flex items-start">
                                        <i class="fas fa-envelope mt-0.5 mr-2 text-sky-500"></i>
                                        <div>
                                            <strong>Email:</strong> {{ $order->deliveryAddress->email }}
                                        </div>
                                    </div>
                                @endif
                                <div class="flex items-start">
                                    <i class="fas fa-phone mt-0.5 mr-2 text-sky-500"></i>
                                    <div>
                                        <strong>Téléphone:</strong> {{ $order->deliveryAddress->phone }}
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-city mt-0.5 mr-2 text-sky-500"></i>
                                    <div>
                                        <strong>Ville / Commune:</strong> {{ $order->deliveryAddress->city }}, {{ $order->deliveryAddress->commune }}
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-home mt-0.5 mr-2 text-sky-500"></i>
                                    <div>
                                        <strong>Adresse:</strong> {{ $order->deliveryAddress->address }}
                                    </div>
                                </div>
                                @if($order->deliveryAddress->notes)
                                    <div class="mt-2 flex items-start border-t border-sky-200 pt-2 dark:border-sky-800">
                                        <i class="fas fa-sticky-note mt-0.5 mr-2 text-sky-500"></i>
                                        <div>
                                            <strong>Note:</strong> {{ $order->deliveryAddress->notes }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @elseif($order->shipping_address && $order->shipping_address !== 'À définir')
                            {{-- Fallback sur shipping_address --}}
                            <div class="mt-2 space-y-1 text-xs">
                                <div class="flex items-start">
                                    <i class="fas fa-home mt-0.5 mr-2 text-sky-500"></i>
                                    <div>
                                        <strong>Adresse:</strong> {{ $order->shipping_address }}
                                    </div>
                                </div>
                                @if($order->shipping_city && $order->shipping_city !== 'À définir')
                                    <div class="flex items-start">
                                        <i class="fas fa-city mt-0.5 mr-2 text-sky-500"></i>
                                        <div>
                                            <strong>Ville:</strong> {{ $order->shipping_city }}
                                        </div>
                                    </div>
                                @endif
                                @if($order->shipping_phone)
                                    <div class="flex items-start">
                                        <i class="fas fa-phone mt-0.5 mr-2 text-sky-500"></i>
                                        <div>
                                            <strong>Téléphone:</strong> {{ $order->shipping_phone }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="mt-2 rounded bg-amber-50 px-3 py-2 text-xs text-amber-800 dark:bg-amber-900/20 dark:text-amber-300">
                                <i class="fas fa-exclamation-triangle mr-1 text-amber-600"></i>
                                Adresse de livraison non définie pour cette commande
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse justify-end gap-3 sm:flex-row">
                <button type="button" onclick="closeUpdateTrackingModal()" class="rounded-xl border border-slate-300 bg-white px-6 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    Annuler
                </button>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700">
                    <i class="fas fa-save"></i> Enregistrer
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
    @elseif($order->deliveryAddress)
        // Utiliser les coordonnées effectives de l'adresse de livraison (GPS ou par défaut selon la ville)
        const destinationLat = {{ $order->deliveryAddress->effective_latitude }};
        const destinationLng = {{ $order->deliveryAddress->effective_longitude }};
    @else
        // Par défaut, centrer sur Kinshasa si aucune adresse de livraison
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

        // Calculer la distance
        const distance = calculateDistance(currentLat, currentLng, destinationLat, destinationLng);
        
        // Afficher la distance sur la carte
        updateDistanceDisplay(distance);

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

        // Ajouter un marqueur au milieu de la ligne avec la distance
        const midLat = (currentLat + destinationLat) / 2;
        const midLng = (currentLng + destinationLng) / 2;
        
        const distanceIcon = L.divIcon({
            html: `<div style="background: white; padding: 8px 12px; border-radius: 20px; border: 2px solid #6A0DAD; box-shadow: 0 2px 8px rgba(0,0,0,0.2); font-weight: bold; color: #6A0DAD; white-space: nowrap;">
                    <i class="fas fa-route"></i> ${distance} km
                   </div>`,
            className: 'distance-marker',
            iconSize: [100, 30],
            iconAnchor: [50, 15]
        });
        
        L.marker([midLat, midLng], {icon: distanceIcon}).addTo(map);

        // Ajuster la vue pour afficher les deux marqueurs
        const bounds = L.latLngBounds([
            [currentLat, currentLng],
            [destinationLat, destinationLng]
        ]);
        map.fitBounds(bounds, {padding: [50, 50]});
    }
}

// Fonction pour générer un code de suivi unique
function generateTrackingCode() {
    const prefix = 'TRK';
    const timestamp = Date.now().toString(36).toUpperCase();
    const random = Math.random().toString(36).substring(2, 6).toUpperCase();
    const code = `${prefix}-${timestamp}-${random}`;
    document.getElementById('tracking_code').value = code;
}

// Fonction pour calculer la distance (formule de Haversine)
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Rayon de la Terre en km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    const distance = R * c;
    return distance.toFixed(2);
}

// Fonction pour estimer le temps de trajet
function estimateTime(distanceKm) {
    const avgSpeedKmh = 40; // Vitesse moyenne de 40 km/h
    const hours = distanceKm / avgSpeedKmh;
    const minutes = Math.round(hours * 60);
    
    if (minutes < 60) {
        return `${minutes} min`;
    } else {
        const h = Math.floor(minutes / 60);
        const m = minutes % 60;
        return `${h}h ${m}min`;
    }
}

// Fonction pour mettre à jour l'affichage de la distance
function updateDistanceDisplay(distance) {
    const distanceInfo = document.getElementById('distanceInfo');
    const distanceValue = document.getElementById('distanceValue');
    const estimatedTime = document.getElementById('estimatedTime');
    
    if (distance && distance > 0) {
        distanceInfo.style.display = 'block';
        distanceValue.textContent = `${distance} km`;
        estimatedTime.textContent = estimateTime(parseFloat(distance));
    } else {
        distanceInfo.style.display = 'none';
    }
}

// Obtenir la position actuelle avec GPS
// Tente d'abord en haute précision, puis retombe en basse précision (plus fiable / plus rapide).
function getCurrentLocation(event) {
    if (!("geolocation" in navigator)) {
        alert('La géolocalisation n\'est pas supportée par votre navigateur');
        return;
    }

    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    let highAccuracy = true;

    const resetBtn = () => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    };

    const onSuccess = (position) => {
        console.log('Position obtenue:', position.coords);
        document.getElementById('modal_latitude').value = position.coords.latitude.toFixed(8);
        document.getElementById('modal_longitude').value = position.coords.longitude.toFixed(8);
        calculateModalDistance();
        btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Position obtenue !';
        setTimeout(() => { btn.innerHTML = originalText; btn.disabled = false; }, 2000);
    };

    const onError = (error) => {
        console.error('Erreur de géolocalisation (code ' + error.code + '):', error.message);

        // En cas de timeout / indisponibilité, retenter une fois en basse précision (plus fiable)
        if (highAccuracy && (error.code === 1 || error.code === 2 || error.code === 3)) {
            highAccuracy = false;
            console.log('Nouvelle tentative en basse précision...');
            navigator.geolocation.getCurrentPosition(onSuccess, onError, {
                enableHighAccuracy: false,
                timeout: 20000,
                maximumAge: 0
            });
            return;
        }

        const PERMISSION_DENIED = 1;
        const POSITION_UNAVAILABLE = 2;
        const TIMEOUT = 3;

        let errorMsg = 'Impossible d\'obtenir votre position.';
        switch (error.code) {
            case PERMISSION_DENIED:
                errorMsg = 'Permission refusée. Veuillez autoriser l\'accès à votre localisation dans les paramètres du navigateur.';
                break;
            case POSITION_UNAVAILABLE:
                errorMsg = 'Position non disponible. Vérifiez votre connexion GPS ou réseau.';
                break;
            case TIMEOUT:
                errorMsg = 'Délai d\'attente dépassé pour la localisation. Vérifiez votre connexion et réessayez.';
                break;
        }

        alert(errorMsg);
        resetBtn();
    };

    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Récupération de la position...';
    btn.disabled = true;

    console.log('Demande de géolocalisation (haute précision)...');
    navigator.geolocation.getCurrentPosition(onSuccess, onError, {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
    });
}

// Calculer la distance dans le modal en temps réel
function calculateModalDistance() {
    const lat = parseFloat(document.getElementById('modal_latitude').value);
    const lng = parseFloat(document.getElementById('modal_longitude').value);
    
    @if($order->deliveryAddress)
        // Utiliser les coordonnées effectives de l'adresse de livraison (GPS ou par défaut selon la ville)
        const customerLat = {{ $order->deliveryAddress->effective_latitude }};
        const customerLng = {{ $order->deliveryAddress->effective_longitude }};
    @elseif($currentTracking && $currentTracking->customer_latitude && $currentTracking->customer_longitude)
        // Utiliser les coordonnées du tracking
        const customerLat = {{ $currentTracking->customer_latitude }};
        const customerLng = {{ $currentTracking->customer_longitude }};
    @else
        // Coordonnées par défaut (Kinshasa)
        const customerLat = -4.325;
        const customerLng = 15.308;
    @endif
    
    if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
        const distance = calculateDistance(lat, lng, customerLat, customerLng);
        const modalDistanceInfo = document.getElementById('modalDistanceInfo');
        const modalDistanceValue = document.getElementById('modalDistanceValue');
        const modalEstimatedTime = document.getElementById('modalEstimatedTime');
        
        modalDistanceInfo.style.display = 'block';
        modalDistanceValue.textContent = `${distance} km`;
        modalEstimatedTime.textContent = estimateTime(parseFloat(distance));
    } else {
        document.getElementById('modalDistanceInfo').style.display = 'none';
    }
}

// Ouvrir le modal de mise à jour
function openUpdateTrackingModal() {
    document.getElementById('updateTrackingModal').style.display = 'flex';
    // Générer automatiquement un code de suivi
    generateTrackingCode();
    // Réinitialiser l'affichage de la distance
    document.getElementById('modalDistanceInfo').style.display = 'none';
}

// Fermer le modal de mise à jour
function closeUpdateTrackingModal() {
    document.getElementById('updateTrackingModal').style.display = 'none';
    document.getElementById('trackingUpdateForm').reset();
    document.getElementById('modalDistanceInfo').style.display = 'none';
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
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
            // Ne pas définir Content-Type, le navigateur le fera automatiquement pour FormData
        },
        body: formData
    })
    .then(response => {
        // Log du statut de la réponse pour debug
        console.log('Status:', response.status);
        console.log('Headers:', response.headers);
        
        if (!response.ok) {
            // Essayer de parser comme JSON, sinon comme texte
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json().then(err => {
                    throw new Error(err.message || JSON.stringify(err.errors || 'Erreur de validation'));
                });
            } else {
                return response.text().then(text => {
                    console.error('Réponse HTML:', text.substring(0, 500));
                    throw new Error('Le serveur a renvoyé une erreur HTML. Vérifiez les logs Laravel.');
                });
            }
        }
        return response.json();
    })
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
        console.error('Erreur complète:', error);
        alert('Une erreur est survenue lors de la mise à jour: ' + error.message);
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
