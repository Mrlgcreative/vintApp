@extends('layouts.admin')

@section('title', 'Détails de la commande #' . $order->id)
@section('page-title', 'Détails de la commande #' . $order->id)

@push('styles')
<style>
@media print {
    .sidebar, nav, button, a[href] {
        display: none !important;
    }
    .lg\:col-span-2 {
        grid-column: span 3 / span 3 !important;
    }
}
</style>
@endpush

@section('page-actions')
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center text-sm font-medium text-slate-600 hover:text-primary-600 dark:text-slate-400 dark:hover:text-primary-400 print:hidden">
        <i class="fas fa-arrow-left mr-2"></i> Retour aux commandes
    </a>

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.orders.tracking', $order->id) }}"
            class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700 print:hidden">
            <i class="fas fa-map-marked-alt"></i> Traçage GPS
        </a>
        <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank"
            class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-sky-700 print:hidden">
            <i class="fas fa-file-invoice"></i> Facture
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Colonne principale -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Informations de la commande -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="border-b border-slate-100 bg-gradient-to-r from-primary-50 to-primary-100 px-5 py-4 dark:border-slate-700 dark:from-primary-900/30 dark:to-primary-900/10">
                <h3 class="font-semibold text-slate-900 dark:text-white">
                    <i class="fas fa-shopping-cart mr-2 text-primary-600"></i>
                    Commande #{{ $order->id }}
                </h3>
            </div>
            <div class="p-5 sm:p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <p class="mb-1 text-sm font-medium text-slate-500 dark:text-slate-400">Date de commande</p>
                        <p class="text-base text-slate-900 dark:text-white">
                            <i class="far fa-calendar mr-2 text-slate-400"></i>
                            {{ $order->created_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>
                    <div>
                        <p class="mb-1 text-sm font-medium text-slate-500 dark:text-slate-400">Statut</p>
                        <p>
                            @php
                                $statusColors = [
                                    'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300',
                                    'confirmed' => 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300',
                                    'shipped' => 'bg-primary-50 text-primary-700 ring-primary-600/20 dark:bg-primary-900/30 dark:text-primary-300',
                                    'delivered' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300',
                                    'cancelled' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-300',
                                ];
                                $statusLabels = [
                                    'pending' => 'En attente',
                                    'confirmed' => 'Confirmée',
                                    'shipped' => 'Expédiée',
                                    'delivered' => 'Livrée',
                                    'cancelled' => 'Annulée',
                                ];
                            @endphp
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium ring-1 ring-inset {{ $statusColors[$order->status] ?? 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300' }}">
                                {{ $statusLabels[$order->status] ?? $order->status }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="mb-1 text-sm font-medium text-slate-500 dark:text-slate-400">Quantité</p>
                        <p class="text-base text-slate-900 dark:text-white">
                            <i class="fas fa-box mr-2 text-slate-400"></i>
                            {{ $order->quantity }} article(s)
                        </p>
                    </div>
                    <div>
                        <p class="mb-1 text-sm font-medium text-slate-500 dark:text-slate-400">Montant total</p>
                        <p class="text-xl font-bold text-primary-600 dark:text-primary-400">
                            {{ number_format($order->total_price, 2) }} {{ $order->currency ?? 'USD' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Article commandé -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                <h3 class="font-semibold text-slate-900 dark:text-white">
                    <i class="fas fa-tag mr-2 text-primary-600"></i>
                    Article commandé
                </h3>
            </div>
            <div class="p-5 sm:p-6">
                @if($order->item)
                <div class="flex flex-col items-start gap-4 sm:flex-row">
                    @if(!empty($order->item->images) && is_array($order->item->images))
                    <img src="{{ Storage::url($order->item->images[0]) }}" 
                         alt="{{ $order->item->name }}" 
                         class="h-24 w-24 flex-shrink-0 rounded-xl border border-slate-200 object-cover dark:border-slate-700">
                    @else
                    <div class="flex h-24 w-24 flex-shrink-0 items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800">
                        <i class="fas fa-image text-2xl text-slate-400"></i>
                    </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <h4 class="mb-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $order->item->name }}</h4>
                        <p class="mb-3 line-clamp-2 text-sm text-slate-600 dark:text-slate-300">{{ $order->item->description }}</p>
                        <div class="flex flex-wrap gap-3">
                            @if($order->item->category)
                            <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-0.5 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300">
                                <i class="fas fa-folder mr-1"></i> {{ $order->item->category->name }}
                            </span>
                            @endif
                            @if($order->item->brand)
                            <span class="inline-flex items-center rounded-full bg-primary-50 px-2.5 py-0.5 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-600/20 dark:bg-primary-900/30 dark:text-primary-300">
                                <i class="fas fa-copyright mr-1"></i> {{ $order->item->brand->name }}
                            </span>
                            @endif
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">
                                <i class="fas fa-dollar-sign mr-1"></i> {{ number_format($order->item->price, 2) }} {{ $order->item->currency ?? 'USD' }}
                            </span>
                        </div>
                    </div>
                </div>
                @else
                <p class="py-4 text-center text-slate-500 dark:text-slate-400">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Article non disponible
                </p>
                @endif
            </div>
        </div>

        <!-- Transaction associée -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                <h3 class="font-semibold text-slate-900 dark:text-white">
                    <i class="fas fa-receipt mr-2 text-primary-600"></i>
                    Informations de paiement
                </h3>
            </div>
            <div class="p-5 sm:p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <p class="mb-1 text-sm font-medium text-slate-500 dark:text-slate-400">Numéro de commande</p>
                        <p class="font-mono text-base text-slate-900 dark:text-white">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="mb-1 text-sm font-medium text-slate-500 dark:text-slate-400">Montant payé</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-white">
                            {{ number_format($order->total_amount, 2) }} {{ $order->currency ?? 'USD' }}
                        </p>
                    </div>
                    <div>
                        <p class="mb-1 text-sm font-medium text-slate-500 dark:text-slate-400">Statut du paiement</p>
                        <p>
                            @if($order->paid_at)
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-sm font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">
                                <i class="fas fa-check-circle mr-1"></i> Payé
                            </span>
                            @else
                            <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-sm font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300">
                                <i class="fas fa-clock mr-1"></i> En attente
                            </span>
                            @endif
                        </p>
                    </div>
                    @if($order->paid_at)
                    <div>
                        <p class="mb-1 text-sm font-medium text-slate-500 dark:text-slate-400">Date de paiement</p>
                        <p class="text-base text-slate-900 dark:text-white">
                            <i class="far fa-calendar mr-2 text-slate-400"></i>
                            {{ $order->paid_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Colonne latérale -->
    <div class="space-y-6">
        <!-- Acheteur -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="border-b border-slate-100 bg-sky-50 px-5 py-4 dark:border-slate-700 dark:bg-sky-900/20">
                <h3 class="font-semibold text-slate-900 dark:text-white">
                    <i class="fas fa-user mr-2 text-sky-600"></i>
                    Acheteur
                </h3>
            </div>
            <div class="p-5 sm:p-6">
                @if($order->buyer)
                <div class="mb-4 text-center">
                    @if($order->buyer->profile_image)
                    <img src="{{ Storage::url($order->buyer->profile_image) }}" 
                         alt="{{ $order->buyer->name }}" 
                         class="mx-auto mb-3 h-20 w-20 rounded-full border-2 border-sky-200 object-cover dark:border-sky-800">
                    @else
                    <div class="mx-auto mb-3 flex h-20 w-20 items-center justify-center rounded-full bg-sky-100 dark:bg-sky-900/30">
                        <i class="fas fa-user text-2xl text-sky-600 dark:text-sky-400"></i>
                    </div>
                    @endif
                    <h4 class="font-semibold text-slate-900 dark:text-white">{{ $order->buyer->name }}</h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $order->buyer->email }}</p>
                </div>
                <div class="space-y-2 border-t border-slate-100 pt-4 dark:border-slate-700">
                    @if($order->buyer->phone)
                    <p class="text-sm">
                        <i class="fas fa-phone mr-2 text-slate-400"></i>
                        {{ $order->buyer->phone }}
                    </p>
                    @endif
                    @if($order->buyer->city)
                    <p class="text-sm">
                        <i class="fas fa-map-marker-alt mr-2 text-slate-400"></i>
                        {{ $order->buyer->city }}
                    </p>
                    @endif
                    <a href="{{ route('admin.users.show', $order->buyer_id) }}" 
                       class="mt-2 inline-flex items-center text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400">
                        <i class="fas fa-external-link-alt mr-1"></i> Voir le profil
                    </a>
                </div>
                @else
                <p class="text-center text-slate-500 dark:text-slate-400">Utilisateur non disponible</p>
                @endif
            </div>
        </div>

        <!-- Vendeur -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="border-b border-slate-100 bg-emerald-50 px-5 py-4 dark:border-slate-700 dark:bg-emerald-900/20">
                <h3 class="font-semibold text-slate-900 dark:text-white">
                    <i class="fas fa-store mr-2 text-emerald-600"></i>
                    Vendeur
                </h3>
            </div>
            <div class="p-5 sm:p-6">
                @if($order->seller)
                <div class="mb-4 text-center">
                    @if($order->seller->profile_image)
                    <img src="{{ Storage::url($order->seller->profile_image) }}" 
                         alt="{{ $order->seller->name }}" 
                         class="mx-auto mb-3 h-20 w-20 rounded-full border-2 border-emerald-200 object-cover dark:border-emerald-800">
                    @else
                    <div class="mx-auto mb-3 flex h-20 w-20 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30">
                        <i class="fas fa-user text-2xl text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                    @endif
                    <h4 class="font-semibold text-slate-900 dark:text-white">{{ $order->seller->name }}</h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $order->seller->email }}</p>
                </div>
                <div class="space-y-2 border-t border-slate-100 pt-4 dark:border-slate-700">
                    @if($order->seller->phone)
                    <p class="text-sm">
                        <i class="fas fa-phone mr-2 text-slate-400"></i>
                        {{ $order->seller->phone }}
                    </p>
                    @endif
                    @if($order->seller->city)
                    <p class="text-sm">
                        <i class="fas fa-map-marker-alt mr-2 text-slate-400"></i>
                        {{ $order->seller->city }}
                    </p>
                    @endif
                    <a href="{{ route('admin.users.show', $order->seller_id) }}" 
                       class="mt-2 inline-flex items-center text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400">
                        <i class="fas fa-external-link-alt mr-1"></i> Voir le profil
                    </a>
                </div>
                @else
                <p class="text-center text-slate-500 dark:text-slate-400">Utilisateur non disponible</p>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                <h3 class="font-semibold text-slate-900 dark:text-white">
                    <i class="fas fa-cog mr-2 text-primary-600"></i>
                    Actions
                </h3>
            </div>
            <div class="space-y-3 p-5 sm:p-6">
                <button onclick="window.print()" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-slate-700 print:hidden">
                    <i class="fas fa-print"></i> Imprimer
                </button>
                <a href="{{ route('admin.orders.index') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700 print:hidden">
                    <i class="fas fa-list"></i> Toutes les commandes
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
