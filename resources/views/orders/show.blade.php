@extends('app')
@section('title', 'Détails de la commande')
@section('content')
<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('orders.index') }}" class="inline-flex items-center text-sm font-medium text-zinc-600 dark:text-zinc-300 hover:text-vinted-primary-600 dark:hover:text-vinted-primary-400 transition-colors">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Mes Commandes
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-zinc-400 mx-2"></i>
                        <span class="text-sm font-medium text-vinted-primary-600 dark:text-vinted-primary-400">{{ $order->order_number }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
            <!-- Contenu principal -->
            <div class="xl:col-span-3 space-y-6">
                <!-- Carte commande -->
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm overflow-hidden">
                    <!-- Header -->
                    <div class="border-b border-zinc-200 dark:border-zinc-800 bg-vinted-primary-600 px-6 py-5">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-white/15 text-xl text-white">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div>
                                    <h1 class="text-lg font-semibold tracking-tight text-white">Commande {{ $order->order_number }}</h1>
                                    <p class="text-sm text-vinted-primary-100">Détails complets de votre commande</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center self-start rounded-full bg-white px-3 py-1 text-xs font-semibold text-zinc-800 md:self-auto">
                                {{ $order->status_text }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 space-y-8">
                        <!-- Article -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex justify-center">
                                @if($order->item->images && count($order->item->images) > 0)
                                    <div class="w-full max-w-sm overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800">
                                        <img src="{{ asset('storage/' . $order->item->images[0]) }}"
                                             class="aspect-square w-full object-cover"
                                             alt="{{ $order->item->name }}"
                                             loading="lazy">
                                    </div>
                                @else
                                    <div class="flex h-64 w-full max-w-sm items-center justify-center rounded-xl border border-zinc-200 bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-800">
                                        <i class="fas fa-image text-4xl text-zinc-400"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="space-y-6">
                                <div>
                                    <h2 class="mb-2 text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">{{ $order->item->name }}</h2>
                                    <p class="leading-relaxed text-zinc-600 dark:text-zinc-300">{{ $order->item->description }}</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="rounded-lg border border-vinted-primary-200 bg-vinted-primary-50 p-4 dark:border-vinted-primary-500/30 dark:bg-vinted-primary-500/5">
                                        <p class="mb-1 text-xs font-medium text-vinted-primary-600 dark:text-vinted-primary-400">Prix unitaire</p>
                                        <p class="text-xl font-bold text-vinted-primary-700 dark:text-vinted-primary-300">{{ $order->formatted_unit_price }}</p>
                                    </div>
                                    <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-500/30 dark:bg-emerald-500/5">
                                        <p class="mb-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">Quantité</p>
                                        <p class="text-xl font-bold text-emerald-700 dark:text-emerald-300">{{ $order->quantity }}</p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <span class="inline-flex items-center rounded-full bg-vinted-primary-100 px-3 py-1 text-xs font-semibold text-vinted-primary-700 dark:bg-vinted-primary-500/10 dark:text-vinted-primary-300">
                                        {{ $order->item->category->name }}
                                    </span>
                                    @if($order->item->brand)
                                        <span class="inline-flex items-center rounded-full bg-zinc-100 px-3 py-1 text-xs font-semibold text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">
                                            {{ $order->item->brand->name }}
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                                        {{ $order->item->condition === 'excellent' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-300' :
                                           ($order->item->condition === 'good' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-500/10 dark:text-yellow-300' :
                                            ($order->item->condition === 'fair' ? 'bg-orange-100 text-orange-800 dark:bg-orange-500/10 dark:text-orange-300' : 'bg-red-100 text-red-800 dark:bg-red-500/10 dark:text-red-300')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $order->item->condition)) }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
                                        <p class="mb-1 text-xs font-medium text-zinc-500 dark:text-zinc-400">Vendeur</p>
                                        <p class="font-semibold text-zinc-900 dark:text-white">{{ $order->item->user->name }}</p>
                                    </div>
                                    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
                                        <p class="mb-1 text-xs font-medium text-zinc-500 dark:text-zinc-400">Acheteur</p>
                                        <p class="font-semibold text-zinc-900 dark:text-white">{{ $order->buyer->name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Séparateur -->
                        <div class="border-t border-zinc-200 dark:border-zinc-800"></div>

                        <!-- Livraison & Paiement -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Adresse de livraison -->
                            <div class="rounded-lg border border-zinc-200 dark:border-zinc-800 overflow-hidden">
                                <div class="border-b border-zinc-200 bg-zinc-50 px-5 py-3 dark:border-zinc-800 dark:bg-zinc-800/50">
                                    <h3 class="flex items-center gap-2 text-sm font-semibold text-zinc-900 dark:text-white">
                                        <i class="fas fa-map-marker-alt text-vinted-primary-500"></i>
                                        Adresse de livraison
                                    </h3>
                                </div>
                                <div class="p-5">
                                    @if($order->deliveryAddress)
                                        <dl class="space-y-3">
                                            <div class="flex items-start gap-3">
                                                <i class="fas fa-user mt-0.5 w-4 text-vinted-primary-500"></i>
                                                <div>
                                                    <dt class="font-medium text-zinc-900 dark:text-white">Destinataire</dt>
                                                    <dd class="text-sm text-zinc-600 dark:text-zinc-300">{{ $order->deliveryAddress->full_name }}</dd>
                                                </div>
                                            </div>
                                            @if($order->deliveryAddress->email)
                                                <div class="flex items-start gap-3">
                                                    <i class="fas fa-envelope mt-0.5 w-4 text-zinc-400"></i>
                                                    <div>
                                                        <dt class="font-medium text-zinc-900 dark:text-white">Email</dt>
                                                        <dd><a href="mailto:{{ $order->deliveryAddress->email }}" class="text-sm text-vinted-primary-600 hover:underline dark:text-vinted-primary-400">{{ $order->deliveryAddress->email }}</a></dd>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="flex items-start gap-3">
                                                <i class="fas fa-phone mt-0.5 w-4 text-emerald-500"></i>
                                                <div>
                                                    <dt class="font-medium text-zinc-900 dark:text-white">Téléphone</dt>
                                                    <dd><a href="tel:{{ $order->deliveryAddress->phone }}" class="text-sm text-vinted-primary-600 hover:underline dark:text-vinted-primary-400">{{ $order->deliveryAddress->phone }}</a></dd>
                                                </div>
                                            </div>
                                            <div class="flex items-start gap-3">
                                                <i class="fas fa-city mt-0.5 w-4 text-indigo-500"></i>
                                                <div>
                                                    <dt class="font-medium text-zinc-900 dark:text-white">Ville / Commune</dt>
                                                    <dd class="text-sm text-zinc-600 dark:text-zinc-300">{{ $order->deliveryAddress->city }}, {{ $order->deliveryAddress->commune }}</dd>
                                                </div>
                                            </div>
                                            <div class="flex items-start gap-3">
                                                <i class="fas fa-home mt-0.5 w-4 text-zinc-400"></i>
                                                <div>
                                                    <dt class="font-medium text-zinc-900 dark:text-white">Adresse complète</dt>
                                                    <dd class="text-sm text-zinc-600 dark:text-zinc-300">{{ $order->deliveryAddress->address }}</dd>
                                                </div>
                                            </div>
                                            @if($order->deliveryAddress->notes)
                                                <div class="mt-3 rounded-md border border-yellow-200 bg-yellow-50 p-3 dark:border-yellow-500/30 dark:bg-yellow-500/5">
                                                    <div class="flex items-start gap-2">
                                                        <i class="fas fa-sticky-note mt-0.5 text-yellow-500"></i>
                                                        <div>
                                                            <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Note :</p>
                                                            <p class="text-sm text-yellow-700 dark:text-yellow-200">{{ $order->deliveryAddress->notes }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </dl>
                                    @else
                                        <dl class="space-y-3">
                                            @if($order->shipping_city && $order->shipping_city !== 'À définir')
                                                <div class="flex items-start gap-3">
                                                    <i class="fas fa-city mt-0.5 w-4 text-indigo-500"></i>
                                                    <div>
                                                        <dt class="font-medium text-zinc-900 dark:text-white">Ville</dt>
                                                        <dd class="text-sm text-zinc-600 dark:text-zinc-300">{{ $order->shipping_city }}</dd>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($order->shipping_phone)
                                                <div class="flex items-start gap-3">
                                                    <i class="fas fa-phone mt-0.5 w-4 text-emerald-500"></i>
                                                    <div>
                                                        <dt class="font-medium text-zinc-900 dark:text-white">Téléphone</dt>
                                                        <dd><a href="tel:{{ $order->shipping_phone }}" class="text-sm text-vinted-primary-600 hover:underline dark:text-vinted-primary-400">{{ $order->shipping_phone }}</a></dd>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($order->shipping_address && $order->shipping_address !== 'À définir')
                                                <div class="flex items-start gap-3">
                                                    <i class="fas fa-home mt-0.5 w-4 text-zinc-400"></i>
                                                    <div>
                                                        <dt class="font-medium text-zinc-900 dark:text-white">Adresse complète</dt>
                                                        <dd class="text-sm text-zinc-600 dark:text-zinc-300">{{ $order->shipping_address }}</dd>
                                                    </div>
                                                </div>
                                            @endif
                                            @if((!$order->shipping_city || $order->shipping_city === 'À définir') &&
                                                (!$order->shipping_address || $order->shipping_address === 'À définir') &&
                                                !$order->deliveryAddress)
                                                <div class="rounded-md border border-yellow-200 bg-yellow-50 p-3 dark:border-yellow-500/30 dark:bg-yellow-500/5">
                                                    <div class="flex gap-2">
                                                        <i class="fas fa-exclamation-triangle mt-0.5 text-yellow-500"></i>
                                                        <div>
                                                            <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Adresse non définie</h4>
                                                            <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-200">L'adresse de livraison n'a pas encore été définie pour cette commande.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </dl>
                                    @endif
                                </div>
                            </div>

                            <!-- Détails du paiement -->
                            <div class="rounded-lg border border-zinc-200 dark:border-zinc-800 overflow-hidden">
                                <div class="border-b border-zinc-200 bg-zinc-50 px-5 py-3 dark:border-zinc-800 dark:bg-zinc-800/50">
                                    <h3 class="flex items-center gap-2 text-sm font-semibold text-zinc-900 dark:text-white">
                                        <i class="fas fa-money-bill text-emerald-500"></i>
                                        Détails du paiement
                                    </h3>
                                </div>
                                <div class="p-5">
                                    <dl class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <dt class="text-sm text-zinc-600 dark:text-zinc-300">Prix unitaire :</dt>
                                            <dd class="font-semibold text-zinc-900 dark:text-white">{{ $order->formatted_unit_price }}</dd>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <dt class="text-sm text-zinc-600 dark:text-zinc-300">Quantité :</dt>
                                            <dd class="font-semibold text-zinc-900 dark:text-white">{{ $order->quantity }}</dd>
                                        </div>
                                        <div class="mt-2 flex items-center justify-between rounded-md bg-vinted-primary-50 px-3 py-2.5 dark:bg-vinted-primary-500/5">
                                            <dt class="font-bold text-zinc-900 dark:text-white">Total</dt>
                                            <dd class="text-lg font-bold text-vinted-primary-600 dark:text-vinted-primary-400">{{ $order->formatted_total_amount }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        @if($order->notes)
                            <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-5 dark:border-yellow-500/30 dark:bg-yellow-500/5">
                                <h3 class="mb-2 flex items-center gap-2 text-sm font-semibold text-yellow-900 dark:text-yellow-300">
                                    <i class="fas fa-sticky-note text-yellow-600"></i>
                                    Notes
                                </h3>
                                <p class="text-sm text-yellow-800 dark:text-yellow-200">{{ $order->notes }}</p>
                            </div>
                        @endif

                        <!-- Historique des statuts -->
                        <div>
                            <h3 class="mb-6 flex items-center gap-2 text-base font-semibold text-zinc-900 dark:text-white">
                                <i class="fas fa-history text-vinted-primary-500"></i>
                                Historique
                            </h3>
                            <ol class="relative border-l-2 border-zinc-200 dark:border-zinc-800">
                                <!-- Commande créée -->
                                <li class="relative mb-6 pl-8">
                                    <span class="absolute left-0 top-1 h-4 w-4 -translate-x-1/2 rounded-full bg-emerald-500 ring-4 ring-emerald-100 dark:ring-emerald-500/20"></span>
                                    <div>
                                        <h4 class="text-sm font-semibold text-zinc-900 dark:text-white">Commande créée</h4>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-300">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </li>
                                @if($order->paid_at)
                                    <li class="relative mb-6 pl-8">
                                        <span class="absolute left-0 top-1 h-4 w-4 -translate-x-1/2 rounded-full bg-blue-500 ring-4 ring-blue-100 dark:ring-blue-500/20"></span>
                                        <div>
                                            <h4 class="text-sm font-semibold text-zinc-900 dark:text-white">Paiement confirmé</h4>
                                            <p class="text-sm text-zinc-600 dark:text-zinc-300">{{ $order->paid_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </li>
                                @endif
                                @if($order->shipped_at)
                                    <li class="relative mb-6 pl-8">
                                        <span class="absolute left-0 top-1 h-4 w-4 -translate-x-1/2 rounded-full bg-indigo-500 ring-4 ring-indigo-100 dark:ring-indigo-500/20"></span>
                                        <div>
                                            <h4 class="text-sm font-semibold text-zinc-900 dark:text-white">Expédiée</h4>
                                            <p class="text-sm text-zinc-600 dark:text-zinc-300">{{ $order->shipped_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </li>
                                @endif
                                @if($order->delivered_at)
                                    <li class="relative mb-6 pl-8">
                                        <span class="absolute left-0 top-1 h-4 w-4 -translate-x-1/2 rounded-full bg-emerald-500 ring-4 ring-emerald-100 dark:ring-emerald-500/20"></span>
                                        <div>
                                            <h4 class="text-sm font-semibold text-zinc-900 dark:text-white">Livrée</h4>
                                            <p class="text-sm text-zinc-600 dark:text-zinc-300">{{ $order->delivered_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </li>
                                @endif
                            </ol>
                        </div>

                        <!-- Actions principales -->
                        <div class="flex flex-col gap-4 border-t border-zinc-200 pt-6 dark:border-zinc-800 sm:flex-row sm:items-center sm:justify-between">
                            <a href="{{ route('orders.index') }}"
                               class="inline-flex items-center justify-center rounded-lg border border-zinc-300 bg-white px-5 py-2.5 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Retour aux commandes
                            </a>

                            <div class="flex flex-col gap-2 sm:flex-row">
                                {{-- Acheteur : Bouton payer si commande en attente (pending) --}}
                                @if($order->buyer_id === Auth::id() && $order->status === 'pending')
                                    <form method="POST" action="{{ route('orders.confirm-payment', $order) }}">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Êtes-vous sûr de vouloir confirmer le paiement de cette commande ?')"
                                                class="inline-flex w-full items-center justify-center rounded-lg bg-vinted-primary-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-vinted-primary-700 sm:w-auto">
                                            <i class="fas fa-credit-card mr-2"></i>
                                            Confirmer le paiement
                                        </button>
                                    </form>
                                @endif

                                {{-- Vendeur : Bouton expédier si commande confirmée (payée) --}}
                                @if($order->item->user_id === Auth::id() && $order->status === 'confirmed')
                                    <form method="POST" action="{{ route('orders.mark-shipped', $order) }}">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Marquer cette commande comme expédiée ?')"
                                                class="inline-flex w-full items-center justify-center rounded-lg bg-vinted-primary-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-vinted-primary-700 sm:w-auto">
                                            <i class="fas fa-shipping-fast mr-2"></i>
                                            Expédier la commande
                                        </button>
                                    </form>
                                @endif

                                {{-- Vendeur : Bouton marquer comme livrée si commande expédiée --}}
                                @if($order->item->user_id === Auth::id() && $order->status === 'shipped')
                                    <form method="POST" action="{{ route('orders.mark-delivered', $order) }}">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Marquer cette commande comme livrée ?')"
                                                class="inline-flex w-full items-center justify-center rounded-lg bg-vinted-primary-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-vinted-primary-700 sm:w-auto">
                                            <i class="fas fa-check-circle mr-2"></i>
                                            Marquer comme livrée
                                        </button>
                                    </form>
                                @endif

                                {{-- Acheteur : Annuler si pas encore payé --}}
                                @if($order->buyer_id === Auth::id() && $order->status === 'pending')
                                    <form method="POST" action="{{ route('orders.destroy', $order) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex w-full items-center justify-center rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-red-700 sm:w-auto">
                                            <i class="fas fa-times mr-2"></i>
                                            Annuler
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="xl:col-span-1 space-y-6">
                <!-- Actions rapides -->
                <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm">
                    <div class="border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-800">
                        <h3 class="flex items-center gap-2 text-sm font-semibold text-zinc-900 dark:text-white">
                            <i class="fas fa-cogs text-zinc-400"></i>
                            Actions rapides
                        </h3>
                    </div>
                    <div class="space-y-3 p-5">
                        <a href="{{ route('items.show', $order->item) }}"
                           class="flex w-full items-center justify-center rounded-lg border border-vinted-primary-200 bg-white px-4 py-2.5 text-sm font-medium text-vinted-primary-700 transition-colors hover:bg-vinted-primary-50 dark:border-vinted-primary-500/30 dark:bg-zinc-900 dark:text-vinted-primary-300 dark:hover:bg-vinted-primary-500/5">
                            <i class="fas fa-eye mr-2"></i>
                            Voir l'article
                        </a>

                        @if($order->item->user_id === Auth::id())
                            <a href="{{ route('items.edit', $order->item) }}"
                               class="flex w-full items-center justify-center rounded-lg border border-yellow-200 bg-white px-4 py-2.5 text-sm font-medium text-yellow-700 transition-colors hover:bg-yellow-50 dark:border-yellow-500/30 dark:bg-zinc-900 dark:text-yellow-300 dark:hover:bg-yellow-500/5">
                                <i class="fas fa-edit mr-2"></i>
                                Modifier l'article
                            </a>
                        @endif

                        <a href="{{ route('items.show', $order->item) }}#contact"
                           class="flex w-full items-center justify-center rounded-lg border border-indigo-200 bg-white px-4 py-2.5 text-sm font-medium text-indigo-700 transition-colors hover:bg-indigo-50 dark:border-indigo-500/30 dark:bg-zinc-900 dark:text-indigo-300 dark:hover:bg-indigo-500/5">
                            <i class="fas fa-envelope mr-2"></i>
                            Contacter {{ $order->buyer_id === Auth::id() ? 'le vendeur' : 'l\'acheteur' }}
                        </a>

                        {{-- Bouton demande de remboursement pour l'acheteur --}}
                        @if($order->buyer_id === Auth::id() && $order->confirmed_by_buyer_at && !$order->refunds()->exists())
                            <button onclick="openRefundModal()"
                                    class="flex w-full items-center justify-center rounded-lg border border-red-200 bg-white px-4 py-2.5 text-sm font-medium text-red-700 transition-colors hover:bg-red-50 dark:border-red-500/30 dark:bg-zinc-900 dark:text-red-300 dark:hover:bg-red-500/5">
                                <i class="fas fa-undo mr-2"></i>
                                Demander un remboursement
                            </button>
                        @endif

                        {{-- Affichage du statut de remboursement existant --}}
                        @if($order->refunds()->exists())
                            @php $refund = $order->refunds()->latest()->first(); @endphp
                            <div class="rounded-lg border border-orange-200 bg-orange-50 p-4 dark:border-orange-500/30 dark:bg-orange-500/5">
                                <div class="flex gap-3">
                                    <i class="fas fa-undo mt-0.5 text-orange-500"></i>
                                    <div>
                                        <h4 class="text-sm font-medium text-orange-800 dark:text-orange-300">Demande de remboursement</h4>
                                        <p class="mt-1 text-sm text-orange-700 dark:text-orange-200">
                                            Statut : {{ $refund->status_display }}
                                            @if($refund->status === 'negotiation')
                                                <br>Contre-offre : {{ $refund->formatted_counter_offer }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Messages d'état selon le statut de la commande --}}
                        @if($order->status === 'pending')
                            <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-500/30 dark:bg-yellow-500/5">
                                <div class="flex gap-3">
                                    <i class="fas fa-clock mt-0.5 text-yellow-500"></i>
                                    <div>
                                        <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-300">En attente de paiement</h4>
                                        <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-200">
                                            @if($order->buyer_id === Auth::id())
                                                Veuillez confirmer le paiement pour continuer
                                            @else
                                                L'acheteur n'a pas encore payé
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @elseif($order->status === 'confirmed')
                            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-500/30 dark:bg-blue-500/5">
                                <div class="flex gap-3">
                                    <i class="fas fa-box mt-0.5 text-blue-500"></i>
                                    <div>
                                        <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300">Paiement confirmé</h4>
                                        <p class="mt-1 text-sm text-blue-700 dark:text-blue-200">
                                            @if($order->item->user_id === Auth::id())
                                                Vous pouvez maintenant expédier la commande
                                            @else
                                                En attente d'expédition par le vendeur
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @elseif($order->status === 'shipped' && !$order->confirmed_by_buyer_at)
                            @if($order->buyer_id === Auth::id())
                                <button id="btn-confirm-delivery" onclick="confirmDelivery()"
                                        class="flex w-full items-center justify-center rounded-lg bg-vinted-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-vinted-primary-700">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    <span id="btn-confirm-text">Confirmer réception</span>
                                    <svg id="btn-confirm-spinner" class="ml-2 hidden h-5 w-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </button>
                                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-500/30 dark:bg-blue-500/5">
                                    <div class="flex gap-3">
                                        <i class="fas fa-truck mt-0.5 text-blue-500"></i>
                                        <div>
                                            <p class="text-sm text-blue-700 dark:text-blue-200">Cliquez sur "Commande Reçue" une fois la livraison effectuée</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-500/30 dark:bg-blue-500/5">
                                    <div class="flex gap-3">
                                        <i class="fas fa-shipping-fast mt-0.5 text-blue-500"></i>
                                        <div>
                                            <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300">Commande expédiée</h4>
                                            <p class="mt-1 text-sm text-blue-700 dark:text-blue-200">En attente de confirmation de réception par l'acheteur</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @elseif($order->status === 'delivered' && !$order->confirmed_by_buyer_at)
                            @if($order->buyer_id === Auth::id())
                                <button id="btn-confirm-delivery" onclick="confirmDelivery()"
                                        class="flex w-full items-center justify-center rounded-lg bg-vinted-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-vinted-primary-700">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    <span id="btn-confirm-text">Confirmer réception</span>
                                    <svg id="btn-confirm-spinner" class="ml-2 hidden h-5 w-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </button>
                                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-500/30 dark:bg-blue-500/5">
                                    <div class="flex gap-3">
                                        <i class="fas fa-home mt-0.5 text-blue-500"></i>
                                        <div>
                                            <p class="text-sm text-blue-700 dark:text-blue-200">Confirmez la réception pour finaliser la transaction</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-500/30 dark:bg-emerald-500/5">
                                    <div class="flex gap-3">
                                        <i class="fas fa-check mt-0.5 text-emerald-500"></i>
                                        <div>
                                            <h4 class="text-sm font-medium text-emerald-800 dark:text-emerald-300">Commande livrée</h4>
                                            <p class="mt-1 text-sm text-emerald-700 dark:text-emerald-200">En attente de confirmation par l'acheteur</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        {{-- Confirmation de réception effectuée --}}
                        @if($order->confirmed_by_buyer_at)
                            <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-500/30 dark:bg-emerald-500/5">
                                <div class="flex gap-3">
                                    <i class="fas fa-check-circle mt-0.5 text-emerald-500"></i>
                                    <div>
                                        <h4 class="text-sm font-medium text-emerald-800 dark:text-emerald-300">Réception confirmée</h4>
                                        <p class="mt-1 text-sm text-emerald-700 dark:text-emerald-200">
                                            Le {{ $order->confirmed_by_buyer_at->format('d/m/Y à H:i') }}
                                        </p>
                                        @if($order->buyer_confirmation_note)
                                            <p class="mt-2 text-sm italic text-emerald-600 dark:text-emerald-300">"{{ $order->buyer_confirmation_note }}"</p>
                                        @endif
                                        <div class="mt-3 border-t border-emerald-200 pt-3 dark:border-emerald-500/20">
                                            <p class="flex items-center text-xs text-emerald-600 dark:text-emerald-300">
                                                <i class="fas fa-money-bill-wave mr-1"></i>
                                                La distribution des fonds a été effectuée
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informations supplémentaires -->
                <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm">
                    <div class="border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-800">
                        <h3 class="flex items-center gap-2 text-sm font-semibold text-zinc-900 dark:text-white">
                            <i class="fas fa-info-circle text-zinc-400"></i>
                            Informations
                        </h3>
                    </div>
                    <dl class="space-y-3 p-5">
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-zinc-600 dark:text-zinc-300">Numéro :</dt>
                            <dd class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $order->order_number }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-zinc-600 dark:text-zinc-300">Créée le :</dt>
                            <dd class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $order->created_at->format('d/m/Y') }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-zinc-600 dark:text-zinc-300">Devise :</dt>
                            <dd class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $order->currency }}</dd>
                        </div>
                        @if($order->updated_at !== $order->created_at)
                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-zinc-600 dark:text-zinc-300">Modifiée le :</dt>
                                <dd class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $order->updated_at->format('d/m/Y') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de demande de remboursement -->
<div id="refundModal" class="fixed inset-0 z-50 hidden h-full w-full overflow-y-auto bg-zinc-900/60 backdrop-blur-sm">
    <div class="relative top-20 mx-auto w-11/12 rounded-xl border border-zinc-200 bg-white p-6 shadow-lg dark:border-zinc-700 dark:bg-zinc-900 md:w-1/2 lg:w-1/3">
        <h3 class="mb-4 flex items-center text-lg font-bold text-zinc-900 dark:text-white">
            <i class="fas fa-undo mr-2 text-red-500"></i>
            Demande de remboursement
        </h3>

        <form id="refundForm">
            @csrf

            <div class="mb-4">
                <label for="refundType" class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Type de remboursement</label>
                <select id="refundType" name="refund_type" required class="h-10 w-full rounded-lg border border-zinc-300 bg-white px-3 text-sm text-zinc-900 focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white">
                    <option value="">Sélectionnez un type</option>
                    <option value="full">Remboursement complet</option>
                    <option value="partial">Remboursement partiel</option>
                </select>
            </div>

            <div class="mb-4" id="partialAmountDiv" style="display: none;">
                <label for="refundAmount" class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Montant souhaité</label>
                <div class="relative">
                    <input type="number" id="refundAmount" name="refund_amount" step="0.01" min="0" max="{{ $order->total_amount }}"
                           class="h-10 w-full rounded-lg border border-zinc-300 bg-white pl-8 pr-3 text-sm text-zinc-900 focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white">
                    <span class="absolute left-3 top-2.5 text-zinc-500 dark:text-zinc-400">$</span>
                </div>
            </div>

            <div class="mb-4">
                <label for="refundReason" class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Raison du remboursement</label>
                <textarea id="refundReason" name="reason" rows="4" required
                          placeholder="Décrivez pourquoi vous demandez ce remboursement..."
                          class="w-full resize-none rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white"></textarea>
            </div>

            <div class="mb-6">
                <label for="evidencePhotos" class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Photos de preuves (optionnel)</label>
                <input type="file" id="evidencePhotos" name="evidence_photos[]" multiple accept="image/*"
                       class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:border-zinc-600 dark:bg-zinc-800">
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Vous pouvez joindre des photos pour appuyer votre demande</p>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeRefundModal()"
                        class="flex-1 rounded-lg border border-zinc-300 bg-white px-4 py-2 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-50 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
                    Annuler
                </button>
                <button type="submit"
                        class="flex-1 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-red-700">
                    Soumettre la demande
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Fonctions pour gérer le modal de remboursement
function openRefundModal() {
    document.getElementById('refundModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeRefundModal() {
    document.getElementById('refundModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('refundForm').reset();
    document.getElementById('partialAmountDiv').style.display = 'none';
}

// Gérer l'affichage du champ montant selon le type de remboursement
document.getElementById('refundType').addEventListener('change', function() {
    const partialDiv = document.getElementById('partialAmountDiv');
    if (this.value === 'partial') {
        partialDiv.style.display = 'block';
        document.getElementById('refundAmount').required = true;
    } else {
        partialDiv.style.display = 'none';
        document.getElementById('refundAmount').required = false;
    }
});

// Fermer le modal en cliquant en dehors
document.getElementById('refundModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRefundModal();
    }
});

// Soumission du formulaire de remboursement
document.getElementById('refundForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;

    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    console.log('Données à envoyer:');
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }

    submitButton.disabled = true;
    submitButton.textContent = 'Traitement...';

    fetch('{{ route('refund.request', $order) }}', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);

        if (!response.ok) {
            return response.text().then(text => {
                console.log('Error response body:', text);
                throw new Error(`HTTP error! status: ${response.status} - ${text.substring(0, 200)}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert(data.message || 'Demande de remboursement soumise avec succès !');
            closeRefundModal();
            window.location.reload();
        } else {
            alert(data.error || 'Erreur lors de la soumission de la demande');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue lors de la soumission: ' + error.message);
    })
    .finally(() => {
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    });
});

// Script pour confirmer la réception de la commande
function confirmDelivery() {
    const btn = document.getElementById('btn-confirm-delivery');
    const text = document.getElementById('btn-confirm-text');
    const spinner = document.getElementById('btn-confirm-spinner');

    const note = prompt('Confirmez-vous avoir reçu votre commande ?\n\nVous pouvez ajouter un commentaire (optionnel) :');

    if (note !== null) {
        btn.disabled = true;
        text.textContent = 'Confirmation...';
        spinner.classList.remove('hidden');

        fetch('{{ route('orders.confirm-delivery', $order) }}', {
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
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.error || 'Erreur lors de la confirmation');
                btn.disabled = false;
                text.textContent = 'Confirmer réception';
                spinner.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur : ' + error.message);
            btn.disabled = false;
            text.textContent = 'Confirmer réception';
            spinner.classList.add('hidden');
        });
    }
}

console.log('Page de commande chargée');
</script>
@endsection
