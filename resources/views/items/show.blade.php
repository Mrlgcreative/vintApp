@extends('app')
@section('title', $item->name)
@section('meta_description', $item->description ? Str::limit($item->description, 160) : 'Détails de l\'article')
@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-900 animate-fade-in-up">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-10 lg:px-8">

        <!-- Fil d'Ariane -->
        <nav class="mb-6 flex flex-wrap items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400">
            <a href="{{ route('items.index') }}" class="transition-colors hover:text-primary-600">Catalogue</a>
            <i class="fas fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>
            @if($item->category)
                <a href="{{ route('items.index') }}" class="transition-colors hover:text-primary-600">{{ $item->category->name }}</a>
                <i class="fas fa-chevron-right text-[10px] text-slate-300 dark:text-slate-600"></i>
            @endif
            <span class="max-w-[200px] truncate font-medium text-slate-800 sm:max-w-xs dark:text-slate-200">{{ $item->name }}</span>
        </nav>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:gap-10 xl:gap-14">
            <!-- Galerie -->
            <div class="lg:col-span-7">
                <!-- Miniatures horizontales (mobile) -->
                <div class="mb-3 xl:hidden">
                    @if($item->images && count($item->images) > 0)
                        <div class="flex gap-2 overflow-x-auto pb-1">
                            @foreach($item->images as $index => $image)
                                <div class="thumbnail-item h-16 w-16 flex-shrink-0 cursor-pointer overflow-hidden rounded-lg border-2 transition-all duration-200 {{ $index === 0 ? 'border-primary-500' : 'border-slate-200 dark:border-slate-700' }}"
                                    data-index="{{ $index }}" onclick="changeMainImage('{{ Storage::url($image) }}', this)">
                                    <img src="{{ Storage::url($image) }}" class="h-full w-full object-cover" alt="Miniature {{ $index + 1 }}">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Image principale -->
                <div class="overflow-hidden rounded-2xl bg-slate-100 dark:bg-slate-800">
                    @if($item->images && count($item->images) > 0)
                        <img id="mainProductImg"
                             data-src="{{ Storage::url($item->images[0]) }}"
                             src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600'%3E%3Crect fill='%23f1f5f9' width='800' height='600'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' fill='%2394a3b8' font-size='20'%3EChargement...%3C/text%3E%3C/svg%3E"
                             loading="eager"
                             class="h-[320px] w-full object-contain transition-opacity duration-300 sm:h-[440px] lg:h-[540px] xl:h-[600px]"
                             alt="{{ $item->name }}">
                    @else
                        <div class="flex h-[320px] w-full flex-col items-center justify-center text-slate-400 sm:h-[440px] lg:h-[540px] dark:text-slate-500">
                            <i class="fas fa-image mb-4 text-5xl"></i>
                            <p class="text-base font-medium">Aucune image disponible</p>
                        </div>
                    @endif
                </div>

                <!-- Miniatures verticales (desktop) -->
                @if($item->images && count($item->images) > 1)
                    <div class="mt-3 hidden flex-wrap gap-2 xl:flex">
                        @foreach($item->images as $index => $image)
                            <div class="thumbnail-item h-16 w-16 cursor-pointer overflow-hidden rounded-lg border-2 transition-all duration-200 {{ $index === 0 ? 'border-primary-500' : 'border-slate-200 hover:border-primary-300 dark:border-slate-700 dark:hover:border-primary-600' }}"
                                data-index="{{ $index }}" onclick="changeMainImage('{{ Storage::url($image) }}', this)">
                                <img src="{{ Storage::url($image) }}" class="h-full w-full object-cover" alt="Miniature {{ $index + 1 }}">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Informations produit -->
            <div class="lg:col-span-5">
                <div class="lg:sticky lg:top-24">
                    <!-- Titre + favori -->
                    <div class="mb-4 flex items-start justify-between gap-4">
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl dark:text-white">{{ $item->name }}</h1>
                        @auth
                            <button class="favorite-btn flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white text-red-500 shadow-sm transition-all duration-200 hover:scale-105 hover:border-red-300 dark:border-slate-700 dark:bg-slate-800 {{ $isFavorited ? 'border-red-400 bg-red-50' : '' }}"
                                data-item-id="{{ $item->id }}">
                                <i class="{{ $isFavorited ? 'fas' : 'far' }} fa-heart"></i>
                            </button>
                        @endauth
                    </div>

                    <!-- Badges -->
                    <div class="mb-6 flex flex-wrap items-center gap-2">
                        @if($item->isVerified())
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">
                                <i class="fas fa-shield-alt text-xs"></i>
                                @if($item->authenticity_badge_type === 'expert_certified') Certifié Expert @else Vérifié VintApp @endif
                            </span>
                        @elseif($item->authenticityCheck && $item->authenticityCheck->status === 'pending')
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-yellow-50 px-2.5 py-1 text-xs font-semibold text-yellow-700 ring-1 ring-inset ring-yellow-600/20 dark:bg-yellow-900/30 dark:text-yellow-300">
                                <i class="fas fa-clock text-xs"></i>
                                Vérification en cours
                            </span>
                        @endif
                        @php
                            $conditionClass = match($item->condition) {
                                'new' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300',
                                'used', 'like_new' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300',
                                default => 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-700 dark:text-slate-300'
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $conditionClass }}">
                            <i class="fas fa-certificate text-xs"></i>
                            {{ ucfirst(str_replace('_', ' ', $item->condition)) }}
                        </span>
                        @if($item->quantity > 0 && $item->quantity <= 5)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-50 px-2.5 py-1 text-xs font-semibold text-orange-700 ring-1 ring-inset ring-orange-600/20 dark:bg-orange-900/30 dark:text-orange-300">
                                <i class="fas fa-fire text-xs"></i>
                                Plus que {{ $item->quantity }} en stock
                            </span>
                        @elseif($item->quantity > 5)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">
                                <i class="fas fa-box-open text-xs"></i>
                                {{ $item->quantity }} en stock
                            </span>
                        @endif
                    </div>

                    <!-- Prix -->
                    <div class="mb-6">
                        <div class="flex items-end justify-between gap-4">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500">Prix</p>
                                <p class="mt-1 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl dark:text-white">{{ $item->formatted_price }}</p>
                            </div>
                            @if($item->quantity > 0)
                                <span class="inline-flex items-center gap-1.5 pb-1 text-sm font-medium text-emerald-600 dark:text-emerald-400">
                                    <i class="fas fa-circle text-[8px]"></i>
                                    {{ $item->quantity }} en stock
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 pb-1 text-sm font-medium text-red-500">
                                    <i class="fas fa-circle text-[8px]"></i>
                                    Rupture de stock
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Quantité + CTA panier -->
                    <form method="POST" action="{{ route('cart.add', $item->id) }}" id="addToCartForm" class="mb-6">
                        @csrf
                        <div class="mb-6">
                            <p class="mb-2.5 text-sm font-medium text-slate-700 dark:text-slate-300">Quantité</p>
                            <div class="inline-flex items-center rounded-xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
                                <button type="button" id="decrementBtn" onclick="decrementQuantity()" {{ $item->quantity == 0 ? 'disabled' : '' }}
                                    class="flex h-11 w-11 items-center justify-center rounded-l-xl text-slate-500 transition-colors hover:bg-slate-50 hover:text-primary-600 disabled:cursor-not-allowed disabled:opacity-40 dark:text-slate-400 dark:hover:bg-slate-700">
                                    <i class="fas fa-minus text-sm"></i>
                                </button>
                                <input type="number" name="quantity" id="quantityInput" value="1" min="1" max="{{ max($item->quantity, 1) }}"
                                    class="h-11 w-16 border-x border-slate-200 bg-transparent text-center text-base font-bold text-slate-900 outline-none dark:border-slate-700 dark:text-white [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none" {{ $item->quantity == 0 ? 'disabled' : '' }}>
                                <button type="button" id="incrementBtn" onclick="incrementQuantity()" {{ $item->quantity == 0 ? 'disabled' : '' }}
                                    class="flex h-11 w-11 items-center justify-center rounded-r-xl text-slate-500 transition-colors hover:bg-slate-50 hover:text-primary-600 disabled:cursor-not-allowed disabled:opacity-40 dark:text-slate-400 dark:hover:bg-slate-700">
                                    <i class="fas fa-plus text-sm"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" id="addToCartBtn"
                            class="flex w-full items-center justify-center rounded-xl bg-primary-600 py-3.5 text-base font-bold text-white shadow-lg shadow-primary-600/25 transition-all duration-200 hover:bg-primary-700 hover:shadow-xl active:scale-[0.99]">
                            <i class="fas fa-shopping-cart mr-2.5"></i>
                            <span>Ajouter au panier</span>
                        </button>
                    </form>

                    <!-- Réduction disponible -->
                    @auth
                        @if(Auth::id() !== $item->user_id)
                            <div id="discountSection" class="mb-6 hidden rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800/40 dark:bg-emerald-900/20">
                                <div class="flex gap-3.5">
                                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white">
                                        <i class="fas fa-tags"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h6 class="mb-1 text-sm font-bold text-emerald-700 dark:text-emerald-300">Réduction disponible !</h6>
                                        <div id="discountInfo" class="text-sm text-slate-600 dark:text-slate-300"></div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endauth

                    <!-- Actions vendeur -->
                    <div class="mb-8 space-y-3">
                        @auth
                            @if(Auth::id() !== $item->user_id)
                                <form id="contactForm" method="POST" action="{{ route('contact.seller', $item) }}">
                                    @csrf
                                    <button type="button" onclick="openModal('contactModal')"
                                        class="mb-3 flex w-full items-center justify-center rounded-xl border-2 border-primary-600 py-2.5 text-sm font-semibold text-primary-600 transition-all duration-200 hover:bg-primary-50 dark:border-primary-500 dark:text-primary-400 dark:hover:bg-primary-900/20">
                                        <i class="fas fa-tags mr-2"></i>
                                        Demander une réduction
                                    </button>
                                </form>
                                <button onclick="contactSeller()"
                                    class="flex w-full items-center justify-center rounded-xl border border-slate-200 bg-white py-2.5 text-sm font-semibold text-slate-700 transition-all duration-200 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                                    <i class="fas fa-envelope mr-2"></i>
                                    Contacter le vendeur
                                </button>
                            @else
                                <div class="grid grid-cols-2 gap-3">
                                    <a href="{{ route('items.edit', $item) }}"
                                        class="inline-flex items-center justify-center rounded-xl bg-amber-500 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:bg-amber-600">
                                        <i class="fas fa-edit mr-2"></i>
                                        Modifier
                                    </a>
                                    <button onclick="deleteItem()"
                                        class="flex items-center justify-center rounded-xl bg-red-500 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:bg-red-600">
                                        <i class="fas fa-trash-alt mr-2"></i>
                                        Supprimer
                                    </button>
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}"
                                class="flex w-full items-center justify-center rounded-xl bg-primary-600 py-3.5 text-base font-bold text-white shadow-lg shadow-primary-600/25 transition-all duration-200 hover:bg-primary-700">
                                <i class="fas fa-sign-in-alt mr-2.5"></i>
                                Se connecter pour acheter
                            </a>
                        @endauth
                    </div>

                    <!-- Réassurance -->
                    <div class="mb-8 grid grid-cols-3 gap-3 border-y border-slate-200 py-4 dark:border-slate-700">
                        <div class="flex flex-col items-center gap-1.5 text-center">
                            <i class="fas fa-shield-alt text-primary-500"></i>
                            <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Achat sécurisé</span>
                        </div>
                        <div class="flex flex-col items-center gap-1.5 text-center">
                            <i class="fas fa-undo text-primary-500"></i>
                            <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Retours faciles</span>
                        </div>
                        <div class="flex flex-col items-center gap-1.5 text-center">
                            <i class="fas fa-headset text-primary-500"></i>
                            <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Support 24/7</span>
                        </div>
                    </div>

                    <!-- Vendeur -->
                    <div class="mb-8 flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-primary-600 text-lg font-bold text-white">
                            {{ strtoupper(substr($item->user->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-slate-900 dark:text-white">{{ $item->user->name }}</p>
                            <div class="mt-0.5 flex items-center text-sm text-slate-500 dark:text-slate-400">
                                <i class="fas fa-star mr-1 text-xs text-yellow-400"></i>
                                <span>{{ $averageRating > 0 ? $averageRating : 'Nouveau' }}</span>
                                @if($totalReviews > 0)
                                    <span class="ml-1.5">· {{ $totalReviews }} avis</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('messages.index') }}" class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full border border-slate-200 text-slate-500 transition-colors hover:border-primary-500 hover:text-primary-600 dark:border-slate-600 dark:text-slate-400">
                            <i class="fas fa-paper-plane text-sm"></i>
                        </a>
                    </div>

                    <!-- Métadonnées -->
                    <dl class="divide-y divide-slate-200 dark:divide-slate-700">
                        @if($item->category)
                            <div class="flex items-center justify-between py-3">
                                <dt class="text-sm text-slate-500 dark:text-slate-400">Catégorie</dt>
                                <dd class="text-sm font-medium text-slate-900 dark:text-white">{{ $item->category->name }}</dd>
                            </div>
                        @endif
                        @if($item->brand)
                            <div class="flex items-center justify-between py-3">
                                <dt class="text-sm text-slate-500 dark:text-slate-400">Marque</dt>
                                <dd class="text-sm font-medium text-slate-900 dark:text-white">{{ $item->brand->name }}</dd>
                            </div>
                        @endif
                        @if($item->color)
                            <div class="flex items-center justify-between py-3">
                                <dt class="text-sm text-slate-500 dark:text-slate-400">Couleur</dt>
                                <dd class="text-sm font-medium text-slate-900 dark:text-white">{{ $item->color }}</dd>
                            </div>
                        @endif
                        @if($item->size)
                            <div class="flex items-center justify-between py-3">
                                <dt class="text-sm text-slate-500 dark:text-slate-400">Taille</dt>
                                <dd><span class="rounded-md bg-primary-600 px-2 py-0.5 text-xs font-semibold text-white">{{ $item->size }}</span></dd>
                            </div>
                        @endif
                        <div class="flex items-center justify-between py-3">
                            <dt class="text-sm text-slate-500 dark:text-slate-400">Vues</dt>
                            <dd class="flex items-center gap-1.5 text-sm font-medium text-slate-900 dark:text-white">
                                <i class="fas fa-eye text-xs text-slate-400"></i>
                                {{ $item->views }}
                            </dd>
                        </div>
                        @if($item->item_number)
                            <div class="flex items-center justify-between py-3">
                                <dt class="text-sm text-slate-500 dark:text-slate-400">N° Article</dt>
                                <dd class="text-sm font-medium text-slate-900 dark:text-white">{{ $item->item_number }}</dd>
                            </div>
                        @endif
                        <div class="flex items-center justify-between py-3">
                            <dt class="text-sm text-slate-500 dark:text-slate-400">Publié le</dt>
                            <dd class="text-sm font-medium text-slate-900 dark:text-white">{{ $item->created_at->format('d/m/Y') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Description + Spécifications + Authentification -->
        <div class="mt-12 grid grid-cols-1 gap-8 lg:grid-cols-12">
            <div class="lg:col-span-7">
                <h2 class="mb-4 flex items-center gap-2.5 text-xl font-bold tracking-tight text-slate-900 dark:text-white">
                    <i class="fas fa-align-left text-primary-500"></i>
                    Description
                </h2>
                <p class="text-[15px] leading-relaxed text-slate-600 dark:text-slate-300">{{ $item->description }}</p>

                @if($item->specifications && is_array($item->specifications) && count($item->specifications) > 0)
                    <h2 class="mb-4 mt-10 flex items-center gap-2.5 text-xl font-bold tracking-tight text-slate-900 dark:text-white">
                        <i class="fas fa-list-ul text-primary-500"></i>
                        Spécifications
                    </h2>
                    <dl class="grid grid-cols-1 gap-x-8 gap-y-0 sm:grid-cols-2">
                        @foreach($item->specifications as $key => $value)
                            <div class="flex items-center justify-between border-b border-slate-200 py-2.5 dark:border-slate-700">
                                <dt class="text-sm text-slate-500 dark:text-slate-400">{{ is_string($key) ? ucfirst($key) : '' }}</dt>
                                <dd class="text-sm font-medium text-slate-900 dark:text-white">{{ is_string($value) ? $value : (is_array($value) ? json_encode($value) : '') }}</dd>
                            </div>
                        @endforeach
                    </dl>
                @endif
            </div>

            <!-- Authentification -->
            <div class="lg:col-span-5">
                @auth
                    @if($item->user_id === auth()->id())
                        @if($item->canRequestVerification())
                            <div class="rounded-2xl border border-primary-200 bg-primary-50/60 p-6 dark:border-primary-800/40 dark:bg-primary-900/10">
                                <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-full bg-primary-600 text-white">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <h3 class="mb-1.5 text-lg font-bold text-slate-900 dark:text-white">Authentifiez votre produit</h3>
                                <p class="mb-4 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                                    Obtenez le badge <strong>Vérifié VintApp</strong> pour rassurer les acheteurs et vendre plus rapidement.
                                </p>
                                <a href="{{ route('authenticity.request', $item) }}"
                                   class="inline-flex items-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-primary-700">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Demander la vérification
                                </a>
                            </div>
                        @elseif($item->authenticityCheck)
                            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-4">
                                        <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700">
                                            @if($item->authenticityCheck->isApproved())
                                                <i class="fas fa-check-circle text-xl text-green-600"></i>
                                            @elseif($item->authenticityCheck->isRejected())
                                                <i class="fas fa-times-circle text-xl text-red-600"></i>
                                            @else
                                                <i class="fas fa-clock text-xl text-yellow-600"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <h3 class="mb-0.5 font-bold text-slate-900 dark:text-white">Vérification d'authenticité</h3>
                                            <p class="text-sm text-slate-600 dark:text-slate-300">{{ $item->authenticityCheck->getStatusLabel() }}</p>
                                            @if($item->authenticityCheck->final_decision_at)
                                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Terminée le {{ $item->authenticityCheck->final_decision_at->format('d/m/Y') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ route('authenticity.status', $item) }}"
                                       class="whitespace-nowrap text-sm font-medium text-primary-600 hover:text-primary-800 dark:text-primary-400">
                                        Voir détails →
                                    </a>
                                </div>
                            </div>
                        @endif
                    @endif
                @endauth
            </div>
        </div>

        <!-- Avis clients -->
        @if($reviews->count() > 0 || $totalReviews > 0)
            <div class="mt-14">
                <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                    <h2 class="flex items-center gap-2.5 text-xl font-bold tracking-tight text-slate-900 sm:text-2xl dark:text-white">
                        <i class="fas fa-star text-yellow-400"></i>
                        Avis clients
                    </h2>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ $averageRating > 0 ? $averageRating : '0' }}<span class="text-base font-medium text-slate-400">/5</span></span>
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-xs {{ $i <= round($averageRating) ? 'text-yellow-400' : 'text-slate-300 dark:text-slate-600' }}"></i>
                            @endfor
                        </div>
                        <span class="text-sm text-slate-500 dark:text-slate-400">({{ $totalReviews }})</span>
                    </div>
                </div>

                @if($reviews->count() > 0)
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        @foreach($reviews as $review)
                            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                                <div class="mb-3 flex items-center gap-3">
                                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-primary-600 text-sm font-semibold text-white">
                                        {{ strtoupper(substr($review->reviewer->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="truncate font-semibold text-slate-900 dark:text-white">{{ $review->reviewer->name ?? 'Utilisateur anonyme' }}</h4>
                                        <div class="flex items-center gap-1.5">
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star text-xs {{ $i <= $review->rating ? 'text-yellow-400' : 'text-slate-300 dark:text-slate-600' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="text-xs text-slate-400">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                                @if($review->comment)
                                    <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-300">{{ $review->comment }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @if($totalReviews > 2)
                        <p class="mt-4 text-center text-sm text-slate-400 dark:text-slate-500">Affichage de 2 avis sur {{ $totalReviews }}</p>
                    @endif
                @else
                    <div class="rounded-2xl border border-dashed border-slate-300 py-12 text-center dark:border-slate-700">
                        <i class="fas fa-comment-alt mb-3 text-3xl text-slate-300 dark:text-slate-600"></i>
                        <p class="font-medium text-slate-500 dark:text-slate-400">Aucun commentaire pour le moment</p>
                        <p class="mt-1 text-sm text-slate-400">Soyez le premier à donner votre avis après achat</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Articles similaires -->
        @if($similarItems->count() > 0)
            <div class="mt-14">
                <div class="mb-6 flex items-end justify-between">
                    <h2 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl dark:text-white">Vous aimerez aussi</h2>
                    <span class="hidden text-sm font-medium text-slate-400 sm:block">{{ $similarItems->count() }} articles</span>
                </div>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 lg:gap-6">
                    @foreach($similarItems as $similarItem)
                        <a href="{{ route('items.show', $similarItem) }}" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-slate-700 dark:bg-slate-800">
                            <div class="aspect-square overflow-hidden bg-slate-100 dark:bg-slate-700/40">
                                @if($similarItem->images && count($similarItem->images) > 0)
                                    <img src="{{ Storage::url($similarItem->images[0]) }}"
                                         class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                         alt="{{ $similarItem->name }}"
                                         loading="lazy">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-2xl text-slate-300 dark:text-slate-600">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="p-3.5">
                                <h3 class="mb-1.5 truncate text-sm font-semibold text-slate-900 dark:text-white">{{ Str::limit($similarItem->name, 35) }}</h3>
                                <div class="flex items-center justify-between">
                                    <span class="text-base font-bold text-slate-900 dark:text-white">{{ $similarItem->formatted_price }}</span>
                                    <span class="text-xs font-semibold text-primary-600 transition-colors group-hover:text-primary-700 dark:text-primary-400">
                                        Voir <i class="fas fa-arrow-right ml-1"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal de demande de réduction -->
@auth
    @if(Auth::id() !== $item->user_id)
        <div id="contactModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 opacity-0 invisible backdrop-blur-sm transition-all duration-300">
            <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl bg-white shadow-2xl transition-all duration-300 scale-95 dark:bg-slate-800">
                <!-- Header -->
                <div class="flex items-start justify-between border-b border-slate-100 px-6 py-5 dark:border-slate-700">
                    <div>
                        <div class="mb-1 flex items-center gap-2.5">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-600 text-white">
                                <i class="fas fa-tags text-sm"></i>
                            </div>
                            <h5 class="text-lg font-bold text-slate-900 dark:text-white">Demander une réduction</h5>
                        </div>
                        <p class="ml-[46px] text-sm text-slate-500 dark:text-slate-400">Négociez directement avec le vendeur</p>
                    </div>
                    <button type="button" onclick="closeModal('contactModal')"
                        class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600 dark:text-slate-500 dark:hover:bg-slate-700"
                        aria-label="Fermer">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="space-y-5 p-6">
                    <div class="flex gap-4">
                        @if($item->images && count($item->images) > 0)
                            <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-lg bg-slate-100">
                                <img src="{{ Storage::url($item->images[0]) }}"
                                     class="h-full w-full object-cover"
                                     alt="{{ $item->name }}"
                                     loading="lazy">
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <h6 class="truncate text-sm font-bold text-slate-900 dark:text-white">{{ $item->name }}</h6>
                            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ Str::limit($item->description, 80) }}</p>
                            <div class="mt-1.5 flex items-center justify-between">
                                <span class="text-base font-bold text-slate-900 dark:text-white">{{ $item->formatted_price }}</span>
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                    <i class="fas fa-tag text-xs"></i>
                                    {{ $item->category->name }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="customMessage" class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white">
                            <i class="fas fa-comment-dots mr-1.5 text-primary-600"></i>
                            Votre message (optionnel)
                        </label>
                        <textarea name="custom_message"
                                  id="customMessage"
                                  rows="4"
                                  class="w-full resize-none rounded-xl border border-slate-200 bg-white p-3.5 text-sm text-slate-900 outline-none transition-all focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                  placeholder="Bonjour, je suis très intéressé(e) par votre produit. Serait-il possible de négocier le prix ?"></textarea>
                        <p class="mt-2 flex items-center gap-1.5 text-[11px] text-slate-400">
                            <i class="fas fa-info-circle"></i>
                            Un message automatique sera envoyé si vous laissez ce champ vide
                        </p>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-900/50">
                        <h6 class="mb-3 flex items-center gap-2 text-sm font-bold text-slate-900 dark:text-white">
                            <i class="fas fa-lightbulb text-primary-600"></i>
                            Comment ça fonctionne ?
                        </h6>
                        <ul class="space-y-2.5">
                            <li class="flex items-start gap-2.5">
                                <i class="fas fa-check mt-0.5 text-xs text-primary-600"></i>
                                <span class="text-xs text-slate-600 dark:text-slate-300">Votre demande est envoyée instantanément au vendeur</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fas fa-check mt-0.5 text-xs text-primary-600"></i>
                                <span class="text-xs text-slate-600 dark:text-slate-300">Le vendeur peut vous proposer une réduction personnalisée</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fas fa-check mt-0.5 text-xs text-primary-600"></i>
                                <span class="text-xs text-slate-600 dark:text-slate-300">La réduction est appliquée automatiquement si acceptée</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fas fa-check mt-0.5 text-xs text-primary-600"></i>
                                <span class="text-xs text-slate-600 dark:text-slate-300">Vous recevez une notification de la réponse</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="flex flex-col gap-3 border-t border-slate-100 px-6 py-4 sm:flex-row dark:border-slate-700">
                    <button type="button" onclick="closeModal('contactModal')"
                        class="flex flex-1 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white py-2.5 text-sm font-semibold text-slate-700 transition-all hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                        <i class="fas fa-times"></i>
                        Annuler
                    </button>
                    <button type="button" id="submitDiscountBtn" onclick="submitDiscountRequest()"
                        class="flex flex-[2] items-center justify-center gap-2 rounded-xl bg-primary-600 py-2.5 text-sm font-semibold text-white transition-all hover:bg-primary-700">
                        <i class="fas fa-paper-plane"></i>
                        Envoyer la demande
                    </button>
                </div>
            </div>
        </div>
    @endif
@endauth

<script>
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('opacity-0', 'invisible');
        modal.classList.add('opacity-100', 'visible');
        const modalContent = modal.querySelector('.scale-95');
        if (modalContent) {
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('opacity-100', 'visible');
        modal.classList.add('opacity-0', 'invisible');
        const modalContent = modal.querySelector('.scale-100');
        if (modalContent) {
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
        }
        document.body.style.overflow = '';
    }
}

document.addEventListener('click', function(e) {
    const modal = document.getElementById('contactModal');
    if (modal && e.target === modal) {
        closeModal('contactModal');
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal('contactModal');
    }
});

function changeMainImage(src, element) {
    const mainImg = document.getElementById('mainProductImg');
    const thumbnails = document.querySelectorAll('.thumbnail-item');
    thumbnails.forEach(thumb => {
        thumb.classList.remove('border-primary-500');
        thumb.classList.add('border-slate-200', 'dark:border-slate-700');
    });
    element.classList.remove('border-slate-200', 'dark:border-slate-700');
    element.classList.add('border-primary-500');
    mainImg.style.opacity = '0';
    setTimeout(() => {
        mainImg.src = src;
        mainImg.style.opacity = '1';
    }, 150);
}

const quantityInput = document.getElementById('quantityInput');
const incrementBtn = document.getElementById('incrementBtn');
const decrementBtn = document.getElementById('decrementBtn');

function updateQuantityButtons() {
    if (!quantityInput) return;
    const max = parseInt(quantityInput.getAttribute('max'), 10);
    const value = parseInt(quantityInput.value, 10);
    const atMax = isNaN(value) || value >= max;
    const atMin = isNaN(value) || value <= 1;
    if (incrementBtn) incrementBtn.disabled = atMax;
    if (decrementBtn) decrementBtn.disabled = atMin;
}

function incrementQuantity() {
    if (!quantityInput) return;
    const max = parseInt(quantityInput.getAttribute('max'), 10);
    const value = parseInt(quantityInput.value, 10);
    quantityInput.value = isNaN(value) ? 1 : Math.min(max, value + 1);
    updateQuantityButtons();
}

function decrementQuantity() {
    if (!quantityInput) return;
    const value = parseInt(quantityInput.value, 10);
    quantityInput.value = isNaN(value) ? 1 : Math.max(1, value - 1);
    updateQuantityButtons();
}

if (quantityInput) {
    quantityInput.addEventListener('change', function () {
        const min = parseInt(this.getAttribute('min'), 10) || 1;
        const max = parseInt(this.getAttribute('max'), 10);
        let value = parseInt(this.value, 10);
        if (isNaN(value) || value < min) value = min;
        if (!isNaN(max) && value > max) value = max;
        this.value = value;
        updateQuantityButtons();
    });
    updateQuantityButtons();
}

function contactSeller() {
    window.location.href = `/messages/conversation/{{ $item->user_id }}?item={{ $item->id }}`;
}

function submitDiscountRequest() {
    const form = document.getElementById('contactForm');
    const customMessage = document.getElementById('customMessage').value;
    const submitBtn = document.getElementById('submitDiscountBtn');
    if (customMessage.trim()) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'custom_message';
        input.value = customMessage;
        form.appendChild(input);
    }
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Envoi en cours...';
    showNotification('Envoi de votre demande en cours...', 'info');
    setTimeout(() => {
        form.submit();
    }, 500);
}

@auth
    @if(Auth::id() !== $item->user_id)
        document.addEventListener('DOMContentLoaded', function() {
            checkAvailableDiscounts();
        });

        function checkAvailableDiscounts() {
            fetch(`/discounts/item/{{ $item->id }}/available`)
                .then(response => response.json())
                .then(discounts => {
                    if (discounts.length > 0) {
                        const discount = discounts[0];
                        showDiscountSection(discount);
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la vérification des réductions:', error);
                });
        }

        function showDiscountSection(discount) {
            const section = document.getElementById('discountSection');
            const info = document.getElementById('discountInfo');
            const savings = discount.original_price - discount.final_price;
            const formattedSavings = new Intl.NumberFormat('fr-FR').format(savings);
            const formattedFinalPrice = new Intl.NumberFormat('fr-FR').format(discount.final_price);
            const currencySymbol = '{{ $item->currency_symbol }}';
            info.innerHTML = `
                <div class="mb-2">
                    <strong class="mb-1 block">Réduction de ${discount.discount_percentage}% !</strong>
                    <small class="block text-slate-500 dark:text-slate-400">
                        Prix original: <span class="line-through">${currencySymbol} ${new Intl.NumberFormat('fr-FR').format(discount.original_price)}</span>
                    </small>
                    <small class="mt-1 block">
                        <span class="font-bold text-emerald-600">
                            Nouveau prix: ${currencySymbol} ${formattedFinalPrice}
                        </span>
                    </small>
                    <small class="mt-1 block text-slate-500 dark:text-slate-400">
                        Économie: <span class="font-bold text-emerald-600">${currencySymbol} ${formattedSavings}</span>
                    </small>
                    <small class="mt-1 block text-slate-500 dark:text-slate-400">
                        <i class="fas fa-clock mr-1"></i>
                        Valable jusqu'au ${new Date(discount.expires_at).toLocaleDateString('fr-FR')}
                    </small>
                </div>
            `;
            section.classList.remove('hidden');
            section.dataset.discountId = discount.id;
            updateAddToCartButton(true);
        }
    @endif
@endauth

function deleteItem() {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet article ? Cette action est irréversible.')) {
        fetch(`/items/{{ $item->id }}`, {
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
                showNotification('Article supprimé avec succès', 'success');
                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 1500);
            } else {
                showNotification('Erreur lors de la suppression', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Une erreur est survenue', 'danger');
        });
    }
}

const favoriteBtn = document.querySelector('.favorite-btn');
if (favoriteBtn) {
    let favoritePending = false;
    favoriteBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (favoritePending) return;
        favoritePending = true;
        const itemId = this.dataset.itemId;
        const btn = this;
        fetch(`/api/items/${itemId}/favorite`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            favoritePending = false;
            if (data.success) {
                const icon = btn.querySelector('i');
                if (data.is_favorite) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    btn.classList.add('bg-red-50', 'border-red-400');
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    btn.classList.remove('bg-red-50', 'border-red-400');
                }
                showNotification(data.message, 'success');
            }
        })
        .catch(error => {
            favoritePending = false;
            console.error('Error:', error);
            showNotification('Une erreur est survenue', 'danger');
        });
    });
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    const iconMap = { success: 'check-circle', danger: 'exclamation-circle', warning: 'exclamation-triangle', info: 'info-circle' };
    const colorMap = { success: 'emerald', danger: 'red', warning: 'amber', info: 'blue' };
    const color = colorMap[type];
    notification.className = `fixed top-5 right-5 z-50 min-w-80 max-w-md rounded-2xl border-l-4 border-${color}-500 bg-white p-5 shadow-lg dark:bg-slate-800`;
    notification.innerHTML = `
        <div class="flex items-start gap-4">
            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-${color}-100">
                <i class="fas fa-${iconMap[type]} text-lg text-${color}-600"></i>
            </div>
            <div class="flex-1">
                <p class="font-semibold text-slate-900 dark:text-white">${message}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()"
                class="flex h-6 w-6 items-center justify-center rounded-full text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-700">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    `;
    document.body.appendChild(notification);
    setTimeout(() => {
        if (notification.parentNode) notification.remove();
    }, 5000);
}

function updateAddToCartButton(hasDiscount) {
    const addToCartBtn = document.getElementById('addToCartBtn');
    if (addToCartBtn && hasDiscount) {
        addToCartBtn.innerHTML = `
            <i class="fas fa-shopping-cart mr-2.5"></i>
            <span>Ajouter avec réduction</span>
            <i class="fas fa-tag ml-2.5"></i>
        `;
        addToCartBtn.className = 'flex w-full items-center justify-center rounded-xl bg-emerald-600 py-3.5 text-base font-bold text-white shadow-lg shadow-emerald-600/25 transition-all duration-200 hover:bg-emerald-700 hover:shadow-xl active:scale-[0.99]';
    }
}
</script>
@endsection
