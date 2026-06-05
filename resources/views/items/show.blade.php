@extends('app')
@section('title', 'Détails de l\'article')
@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900" data-page-type="product-detail">
    <div class="container mx-auto px-4 py-8 lg:py-16">
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400 mb-8">
            <a href="{{ route('home') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Accueil</a>
            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('items.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Produits</a>
            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900 dark:text-white font-medium">{{ Str::limit($item->name, 30) }}</span>
        </nav>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
            <!-- Galerie d'images verticale -->
            <div class="hidden xl:block xl:col-span-1">
                @if($item->images && count($item->images) > 0)
                    <div class="sticky top-20 flex flex-col items-center gap-2">
                        @foreach($item->images as $index => $image)
                            <div class="thumbnail-item w-14 h-14 lg:w-16 lg:h-16 rounded-xl overflow-hidden border-2 transition-all duration-300 cursor-pointer relative 
                                {{ $index === 0 ? 'border-primary-600 shadow-lg shadow-primary-600/25 scale-105' : 'border-gray-200 dark:border-gray-700 hover:border-primary-300' }}"
                                data-index="{{ $index }}" onclick="changeMainImage('{{ Storage::url($image) }}', this)">
                                <img src="{{ Storage::url($image) }}" 
                                     class="w-full h-full object-cover transition-transform duration-300 hover:scale-110" 
                                     alt="Miniature {{ $index + 1 }}">
                                <div class="absolute inset-0 bg-gradient-to-br from-primary-600/10 to-primary-700/15 opacity-0 transition-opacity duration-300
                                    {{ $index === 0 ? 'opacity-100' : 'hover:opacity-100' }}"></div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Images mobiles horizontales -->
            <div class="xl:hidden mb-4">
                @if($item->images && count($item->images) > 0)
                    <div class="flex gap-2 overflow-x-auto pb-2">
                        @foreach($item->images as $index => $image)
                            <div class="thumbnail-item flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition-all duration-300 cursor-pointer 
                                {{ $index === 0 ? 'border-primary-600 shadow-lg shadow-primary-600/25' : 'border-gray-200 dark:border-gray-700' }}"
                                data-index="{{ $index }}" onclick="changeMainImage('{{ Storage::url($image) }}', this)">
                                <img src="{{ Storage::url($image) }}" 
                                     class="w-full h-full object-cover transition-transform duration-300" 
                                     alt="Miniature {{ $index + 1 }}">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Image principale -->
            <div class="xl:col-span-6 order-2 xl:order-1">
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl lg:rounded-3xl p-4 lg:p-6 shadow-xl shadow-primary-600/10 overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                    @if($item->images && count($item->images) > 0)
                        <img id="mainProductImg" 
                             data-src="{{ Storage::url($item->images[0]) }}" 
                             src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600'%3E%3Crect fill='%23e5e7eb' width='800' height='600'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' fill='%239ca3af' font-size='20'%3EChargement...%3C/text%3E%3C/svg%3E"
                             loading="eager"
                             class="w-full h-64 sm:h-80 lg:h-[400px] xl:h-[500px] object-contain rounded-xl lg:rounded-2xl transition-all duration-300" 
                             alt="{{ $item->name }}">
                        <div class="absolute inset-4 lg:inset-6 bg-black/70 backdrop-blur-sm rounded-xl lg:rounded-2xl flex items-center justify-center opacity-0 hover:opacity-100 transition-all duration-300 cursor-zoom-in">
                            <svg class="w-12 h-12 lg:w-16 lg:h-16 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                        </div>
                    @else
                        <div class="h-64 sm:h-80 lg:h-[400px] xl:h-[500px] flex flex-col items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-xl lg:rounded-2xl text-gray-500 dark:text-gray-400">
                            <svg class="w-16 h-16 lg:w-24 lg:h-24 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-base lg:text-lg font-medium">Aucune image disponible</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card produit -->
            <div class="xl:col-span-5 order-1 xl:order-2">
                <div class="sticky top-4 lg:top-6 bg-white dark:bg-gray-800 rounded-2xl lg:rounded-3xl p-6 lg:p-8 shadow-xl shadow-primary-600/10 transform transition-all duration-300 animate-fade-in border border-gray-100/50">
                    <!-- En-tête avec titre et bouton favori -->
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex-1 pr-3 lg:pr-4">
                            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white leading-tight mb-3 lg:mb-4">{{ $item->name }}</h1>
                            <div class="flex flex-wrap gap-2">
                                <span class="inline-flex items-center px-3 py-1.5 lg:px-4 lg:py-2 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs lg:text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    {{ $item->category->name }}
                                </span>
                                @if($item->brand)
                                    <span class="inline-flex items-center px-3 py-1.5 lg:px-4 lg:py-2 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300 text-xs lg:text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                        {{ $item->brand->name }}
                                    </span>
                                @endif
                                @php
                                    $conditionClass = match($item->condition) {
                                        'new' => 'from-emerald-50 to-emerald-100 text-emerald-600 border-emerald-200/50',
                                        'used', 'like_new' => 'from-amber-50 to-amber-100 text-amber-600 border-amber-200/50',
                                        default => 'from-gray-50 to-gray-100 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-700/50'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-3 py-1.5 lg:px-4 lg:py-2 rounded-lg lg:rounded-xl bg-gradient-to-r {{ $conditionClass }} text-xs lg:text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md">
                                    <i class="fas fa-certificate mr-1.5 lg:mr-2 text-xs lg:text-sm"></i>
                                    {{ ucfirst(str_replace('_', ' ', $item->condition)) }}
                                </span>
                                
                                {{-- Badge d'authenticité --}}
                                @if($item->isVerified())
                                    <span class="inline-flex items-center px-3 py-1.5 lg:px-4 lg:py-2 rounded-lg lg:rounded-xl bg-gradient-to-r from-green-50 to-emerald-100 text-green-700 border border-green-200/50 text-xs lg:text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md">
                                        <i class="fas fa-shield-alt mr-1.5 lg:mr-2 text-xs lg:text-sm"></i>
                                        @if($item->authenticity_badge_type === 'expert_certified')
                                            Certifié Expert
                                        @else
                                            Vérifié VintApp
                                        @endif
                                    </span>
                                @elseif($item->authenticityCheck && $item->authenticityCheck->status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1.5 lg:px-4 lg:py-2 rounded-lg lg:rounded-xl bg-gradient-to-r from-yellow-50 to-amber-100 text-yellow-700 border border-yellow-200/50 text-xs lg:text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md">
                                        <i class="fas fa-clock mr-1.5 lg:mr-2 text-xs lg:text-sm animate-pulse"></i>
                                        Vérification en cours
                                    </span>
                                @elseif($item->canRequestVerification())
                                    <span class="inline-flex items-center px-3 py-1.5 lg:px-4 lg:py-2 rounded-lg lg:rounded-xl bg-gradient-to-r from-blue-50 to-blue-100 text-blue-600 border border-blue-200/50 text-xs lg:text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md">
                                        <i class="fas fa-question-circle mr-1.5 lg:mr-2 text-xs lg:text-sm"></i>
                                        Non vérifié
                                    </span>
                                @endif
                            </div>
                        </div>
                        @auth
                            <button class="favorite-btn w-10 h-10 lg:w-12 lg:h-12 rounded-full border-2 border-primary-200/50 bg-white dark:bg-gray-800 flex items-center justify-center transition-all duration-300 hover:bg-red-50 hover:border-red-400 hover:scale-110 flex-shrink-0" 
                                data-item-id="{{ $item->id }}">
                                <i class="fas fa-heart text-red-500 text-sm lg:text-lg transition-transform duration-300 hover:scale-125"></i>
                            </button>
                        @endauth
                    </div>

                    <!-- Section prix -->
                    <div class="bg-gradient-to-r from-blue-50/50 to-indigo-50/50 dark:from-blue-900/20 dark:to-indigo-900/20 p-4 lg:p-6 rounded-xl lg:rounded-2xl border-2 border-blue-200/30 dark:border-blue-700/30 mb-4 lg:mb-6">
                        <div class="flex items-center justify-between flex-wrap gap-3 lg:gap-4">
                            <span class="text-3xl sm:text-4xl lg:text-5xl font-black bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                                {{ $item->formatted_price }}
                            </span>
                            <div class="flex items-center">
                                @if($item->quantity > 0)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-sm font-semibold">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        En stock
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 text-sm font-semibold">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Rupture de stock
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Métadonnées du produit -->
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 lg:p-6 rounded-xl lg:rounded-2xl border border-blue-200/20 dark:border-gray-700 mb-4 lg:mb-6">
                        <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-4">
                            <div class="bg-white dark:bg-gray-800 p-3 lg:p-4 rounded-lg lg:rounded-xl flex items-center gap-2 lg:gap-3 transition-all duration-300 hover:shadow-md hover:-translate-y-1">
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-medium">Vues</p>
                                    <p class="text-gray-900 dark:text-white font-bold text-sm lg:text-base">{{ $item->views }}</p>
                                </div>
                            </div>
                            
                            <div class="bg-white dark:bg-gray-800 p-3 lg:p-4 rounded-lg lg:rounded-xl flex items-center gap-2 lg:gap-3 transition-all duration-300 hover:shadow-md hover:-translate-y-1">
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-medium">Quantité</p>
                                    <p class="text-gray-900 dark:text-white font-bold text-sm lg:text-base">{{ $item->quantity }}</p>
                                </div>
                            </div>

                            @if($item->color)
                            <div class="bg-white dark:bg-gray-800 p-3 lg:p-4 rounded-lg lg:rounded-xl flex items-center gap-2 lg:gap-3 transition-all duration-300 hover:shadow-md hover:-translate-y-1">
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-medium">Couleur</p>
                                    <p class="text-gray-900 dark:text-white font-bold text-sm lg:text-base">{{ $item->color }}</p>
                                </div>
                            </div>
                            @endif

                            @if($item->size)
                            <div class="bg-white dark:bg-gray-800 p-3 lg:p-4 rounded-lg lg:rounded-xl flex items-center gap-2 lg:gap-3 transition-all duration-300 hover:shadow-md hover:-translate-y-1">
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-medium">Taille</p>
                                    <div class="text-gray-900 dark:text-white font-bold text-sm lg:text-base">
                                        <span class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-2 py-1 rounded text-xs font-semibold">
                                            {{ $item->size }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($item->item_number)
                            <div class="bg-white dark:bg-gray-800 p-3 lg:p-4 rounded-lg lg:rounded-xl flex items-center gap-2 lg:gap-3 transition-all duration-300 hover:shadow-md hover:-translate-y-1">
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-medium">N° Article</p>
                                    <p class="text-gray-900 dark:text-white font-bold text-sm lg:text-base">{{ $item->item_number }}</p>
                                </div>
                            </div>
                            @endif

                            <div class="bg-white dark:bg-gray-800 p-3 lg:p-4 rounded-lg lg:rounded-xl flex items-center gap-2 lg:gap-3 transition-all duration-300 hover:shadow-md hover:-translate-y-1">
                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-medium">Publié le</p>
                                    <p class="text-gray-900 dark:text-white font-bold text-sm lg:text-base">{{ $item->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section panier -->
                    <div class="bg-gray-50 dark:bg-gray-900 p-6 rounded-2xl border border-blue-200/20 dark:border-gray-700 mb-6">
                        <form method="POST" action="{{ route('cart.add', $item->id) }}" id="addToCartForm">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-gray-700 dark:text-gray-200 font-semibold mb-2">Quantité</label>
                                <div class="flex items-center max-w-xs">
                                    <button type="button" onclick="decrementQuantity()" 
                                        class="w-11 h-11 bg-white dark:bg-gray-800 border-2 border-blue-200/50 dark:border-gray-600 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center font-semibold transition-all duration-300 hover:bg-blue-600 hover:text-white hover:border-blue-600 hover:scale-105">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                    </button>
                                    <input type="number" name="quantity" id="quantityInput" value="1" min="1" max="{{ max($item->quantity, 1) }}" 
                                        class="flex-1 h-11 border-2 border-blue-200/50 dark:border-gray-600 dark:bg-gray-800 text-center font-bold text-gray-900 dark:text-white text-lg focus:border-blue-600 focus:ring-4 focus:ring-blue-600/20 outline-none transition-all duration-300" {{ $item->quantity == 0 ? 'disabled' : '' }}>
                                    <button type="button" onclick="incrementQuantity()" 
                                        class="w-11 h-11 bg-white dark:bg-gray-800 border-2 border-blue-200/50 dark:border-gray-600 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center font-semibold transition-all duration-300 hover:bg-blue-600 hover:text-white hover:border-blue-600 hover:scale-105">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    </button>
                                </div>
                                @if($item->quantity == 0)
                                    <p class="text-red-500 text-sm mt-2 font-medium flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        Article en rupture de stock
                                    </p>
                                @elseif($item->quantity <= 5)
                                    <p class="text-amber-500 text-sm mt-2 font-medium flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Attention : plus que {{ $item->quantity }} en stock
                                    </p>
                                @endif
                            </div>
                            <button type="submit" id="addToCartBtn" 
                                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold text-lg py-4 rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl relative overflow-hidden group flex items-center justify-center">
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-500"></div>
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Ajouter au panier</span>
                            </button>
                        </form>
                    </div>

                    {{-- Section de vérification d'authenticité --}}
                    @auth
                        @if($item->user_id === auth()->id())
                            @if($item->canRequestVerification())
                                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 p-6 rounded-2xl border border-blue-200/50 dark:border-blue-700/30 mb-6">
                                    <div class="flex items-start space-x-4">
                                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center flex-shrink-0">
                                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Authentifiez votre produit</h4>
                                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                                Obtenez le badge <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Vérifié VintApp</span> 
                                                pour rassurer les acheteurs et vendre plus rapidement.
                                            </p>
                                            <div class="flex flex-wrap gap-3">
                                                <a href="{{ route('authenticity.request', $item) }}" 
                                                   class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition-colors flex items-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                                    Demander la vérification
                                                </a>
                                                <span class="text-sm text-gray-600 dark:text-gray-300 flex items-center">
                                                    À partir de ${{ number_format(5.00, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($item->authenticityCheck)
                                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 rounded-2xl mb-6">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start space-x-4">
                                            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center flex-shrink-0">
                                                @if($item->authenticityCheck->isApproved())
                                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @elseif($item->authenticityCheck->isRejected())
                                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @else
                                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Vérification d'authenticité</h4>
                                                <p class="text-gray-600 dark:text-gray-300 mb-2">{{ $item->authenticityCheck->getStatusLabel() }}</p>
                                                @if($item->authenticityCheck->final_decision_at)
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        Terminée le {{ $item->authenticityCheck->final_decision_at->format('d/m/Y') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <a href="{{ route('authenticity.status', $item) }}" 
                                           class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                            Voir détails →
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endauth

                    <!-- Description -->
                    <div class="bg-gray-50 dark:bg-gray-900 p-6 rounded-2xl border-l-4 border-blue-600 mb-6">
                        <h5 class="text-lg font-bold text-gray-900 dark:text-white flex items-center mb-4">
                            <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                            Description
                        </h5>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">{{ $item->description }}</p>
                    </div>

                    <!-- Spécifications -->
                    @if($item->specifications && is_array($item->specifications) && count($item->specifications) > 0)
                        <div class="bg-gray-50 dark:bg-gray-900 p-6 rounded-2xl border border-blue-200/20 dark:border-gray-700 mb-6">
                            <h5 class="text-lg font-bold text-gray-900 dark:text-white flex items-center mb-4">
                                <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                Spécifications
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($item->specifications as $key => $value)
                                    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl flex justify-between items-center transition-all duration-300 hover:shadow-md hover:translate-x-1">
                                        <span class="font-semibold text-gray-600 dark:text-gray-300 text-sm">{{ is_string($key) ? ucfirst($key) : '' }}</span>
                                        <span class="font-bold text-gray-900 dark:text-white">{{ is_string($value) ? $value : (is_array($value) ? json_encode($value) : '') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Vendeur -->
                    <div class="bg-gradient-to-r from-blue-50/30 to-indigo-50/30 dark:from-blue-900/10 dark:to-indigo-900/10 p-6 rounded-2xl border border-blue-200/20 dark:border-gray-700 mb-6">
                        <h5 class="text-lg font-bold text-gray-900 dark:text-white flex items-center mb-4">
                            <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Vendeur
                        </h5>
                        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl transition-all duration-300 hover:shadow-lg">
                            <div class="flex items-center">
                                <div class="w-15 h-15 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 flex items-center justify-center text-white text-2xl mr-4 flex-shrink-0">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div class="flex-1">
                                    <div class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ $item->user->name }}</div>
                                    <small class="text-gray-500 dark:text-gray-400 text-sm flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Membre depuis {{ $item->user->created_at?->format('M Y') ?? 'N/A' }}
                                    </small>
                                </div>
                                <div class="flex items-center text-lg font-bold text-gray-900 dark:text-white">
                                    <svg class="w-5 h-5 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span>{{ $averageRating > 0 ? $averageRating : 'Aucun avis' }}</span>
                                    @if($totalReviews > 0)
                                        <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">({{ $totalReviews }} avis)</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Réductions disponibles -->
                    @auth
                        @if(Auth::id() !== $item->user_id)
                            <div id="discountSection" class="hidden bg-gradient-to-r from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-900/30 border-2 border-emerald-300/50 dark:border-emerald-700/30 rounded-2xl p-6 mb-6 animate-fade-in">
                                <div class="flex gap-4 mb-4">
                                    <div class="w-12 h-12 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xl flex-shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <h6 class="font-bold text-emerald-600 dark:text-emerald-400 mb-2">Réduction disponible !</h6>
                                        <div id="discountInfo" class="text-gray-600 dark:text-gray-300 text-sm"></div>
                                    </div>
                                </div>
                                <button onclick="applyDiscount()" 
                                    class="w-full bg-emerald-500 text-white font-semibold py-3 rounded-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Appliquer la réduction
                                </button>
                            </div>
                        @endif
                    @endauth

                    <!-- Boutons d'action -->
                    <div class="space-y-4">
                        @auth
                            @if(Auth::id() !== $item->user_id)
                                <!-- Bouton demande de réduction -->
                                <form id="contactForm" method="POST" action="{{ route('contact.seller', $item) }}">
                                    @csrf
                                    <button type="button" onclick="openModal('contactModal')"
                                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold py-4 rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl mb-3 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/></svg>
                                        Demander une réduction
                                    </button>
                                </form>
                                
                                <!-- Bouton contact vendeur -->
                                <button onclick="contactSeller()" 
                                    class="w-full bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 border-2 border-blue-600 dark:border-blue-500 font-semibold py-4 rounded-2xl transition-all duration-300 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 dark:hover:text-white hover:-translate-y-1 hover:shadow-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    Contacter le vendeur
                                </button>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <a href="{{ route('items.edit', $item) }}" 
                                        class="bg-gradient-to-r from-amber-500 to-amber-600 text-white font-semibold py-4 rounded-2xl text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg inline-flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Modifier
                                    </a>
                                    <button onclick="deleteItem()" 
                                        class="bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-4 rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Supprimer
                                    </button>
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" 
                                class="block w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold text-lg py-5 rounded-2xl text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-3xl">
                                <svg class="w-5 h-5 mr-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                Se connecter pour acheter
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <!-- Section des commentaires et avis -->
        @if($reviews->count() > 0 || $totalReviews > 0)
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 p-6 lg:p-8 mb-8 lg:mb-12">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-7 h-7 text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        Avis clients
                    </h2>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $averageRating > 0 ? $averageRating : '0' }}/5
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $totalReviews }} avis</div>
                    </div>
                </div>

                @if($reviews->count() > 0)
                    <div class="space-y-6">
                        @foreach($reviews as $review)
                            <div class="border border-gray-100 rounded-2xl p-6 bg-gradient-to-br from-gray-50/50 to-white">
                                <div class="flex items-start space-x-4">
                                    <!-- Avatar utilisateur -->
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary-600 to-primary-700 flex items-center justify-center text-white font-semibold text-lg">
                                            {{ strtoupper(substr($review->reviewer->name ?? 'U', 0, 1)) }}
                                        </div>
                                    </div>
                                    
                                    <!-- Contenu du commentaire -->
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="font-semibold text-gray-900 dark:text-white">
                                                {{ $review->reviewer->name ?? 'Utilisateur anonyme' }}
                                            </h4>
                                            <div class="flex items-center space-x-2">
                                                <!-- Étoiles -->
                                                <div class="flex items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $review->rating)
                                                            <i class="fas fa-star text-yellow-400 text-sm"></i>
                                                        @else
                                                            <i class="far fa-star text-gray-300 text-sm"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $review->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        @if($review->comment)
                                            <p class="text-gray-700 dark:text-gray-200 leading-relaxed">{{ $review->comment }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($totalReviews > 2)
                        <div class="mt-6 text-center">
                            <p class="text-gray-500 dark:text-gray-400 text-sm">
                                Affichage de 2 avis sur {{ $totalReviews }}
                            </p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-comment-alt text-gray-400 text-xl"></i>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400">Aucun commentaire pour le moment</p>
                        <p class="text-sm text-gray-400 mt-2">Soyez le premier à donner votre avis après achat</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Articles similaires -->
        @if($similarItems->count() > 0)
            <div class="mt-12 lg:mt-20 bg-white dark:bg-gray-800 rounded-2xl lg:rounded-3xl p-6 lg:p-12 shadow-xl shadow-primary-600/5">
                <div class="text-center mb-6 lg:mb-8">
                    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-2">
                        <i class="fas fa-heart text-primary-600 mr-2 lg:mr-3"></i>
                        Vous aimerez aussi
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 text-base lg:text-lg">Découvrez d'autres articles similaires</p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
                    @foreach($similarItems as $similarItem)
                        <div class="bg-white dark:bg-gray-800 rounded-xl lg:rounded-2xl overflow-hidden shadow-lg transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 group">
                            <div class="relative overflow-hidden h-32 sm:h-40 lg:h-48 bg-gray-100 dark:bg-gray-800">
                                @if($similarItem->images && count($similarItem->images) > 0)
                                    <img src="{{ Storage::url($similarItem->images[0]) }}" 
                                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110" 
                                         alt="{{ $similarItem->name }}"
                                         loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-2xl lg:text-4xl">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-primary-600/80 backdrop-blur-sm flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                                    <a href="{{ route('items.show', $similarItem) }}" 
                                        class="w-8 h-8 lg:w-12 lg:h-12 bg-white dark:bg-gray-800 text-primary-600 rounded-full flex items-center justify-center text-sm lg:text-lg transition-transform duration-300 hover:scale-125">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="p-3 lg:p-5">
                                <h6 class="font-bold text-gray-900 dark:text-white mb-2 lg:mb-3 min-h-[2rem] lg:min-h-[2.5rem] leading-tight text-sm lg:text-base">
                                    {{ Str::limit($similarItem->name, 35) }}
                                </h6>
                                <div class="flex justify-between items-center">
                                    <span class="text-base lg:text-xl font-bold bg-gradient-to-r from-primary-600 to-primary-700 bg-clip-text text-transparent">
                                        {{ $similarItem->formatted_price }}
                                    </span>
                                    <a href="{{ route('items.show', $similarItem) }}" 
                                        class="text-primary-600 font-semibold text-xs lg:text-sm transition-all duration-300 hover:text-primary-800 hover:translate-x-1">
                                        Voir <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal de demande de réduction -->
@auth
    @if(Auth::id() !== $item->user_id)
        <!-- Overlay Background -->
        <div id="contactModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 opacity-0 invisible transition-all duration-300">
            <!-- Modal Container -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl lg:rounded-3xl overflow-hidden shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300">
                <!-- Header -->
                <div class="bg-gradient-to-r from-primary-600 to-primary-700 text-white p-6 lg:p-8 flex justify-between items-start">
                    <div>
                        <h5 class="text-xl lg:text-2xl font-bold mb-1" id="contactModalLabel">
                            <i class="fas fa-percentage mr-2 lg:mr-3"></i>
                            Demander une réduction
                        </h5>
                        <p class="text-primary-100 text-sm">Négociez directement avec le vendeur</p>
                    </div>
                    <button type="button" onclick="closeModal('contactModal')" 
                        class="w-8 h-8 lg:w-9 lg:h-9 rounded-full bg-white dark:bg-gray-800/20 border-0 text-white flex items-center justify-center transition-all duration-300 hover:bg-white dark:bg-gray-800/30 hover:scale-110" 
                        aria-label="Fermer">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
                <!-- Body -->
                <div class="p-4 lg:p-6">
                    <!-- Aperçu du produit -->
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 lg:p-6 rounded-xl lg:rounded-2xl mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                            <div>
                                @if($item->images && count($item->images) > 0)
                                    <img src="{{ Storage::url($item->images[0]) }}" 
                                         class="w-full h-24 lg:h-32 object-cover rounded-lg lg:rounded-xl" 
                                         alt="{{ $item->name }}"
                                         loading="lazy">
                                @endif
                            </div>
                            <div class="md:col-span-2">
                                <h6 class="font-bold text-gray-900 dark:text-white mb-2 text-sm lg:text-base">{{ $item->name }}</h6>
                                <p class="text-gray-600 dark:text-gray-300 text-xs lg:text-sm mb-4">{{ Str::limit($item->description, 120) }}</p>
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                                    <span class="text-lg lg:text-2xl font-bold bg-gradient-to-r from-primary-600 to-primary-700 bg-clip-text text-transparent">
                                        {{ $item->formatted_price }}
                                    </span>
                                    <span class="bg-primary-100 text-primary-700 px-2 py-1 lg:px-3 lg:py-1 rounded-lg lg:rounded-xl text-xs lg:text-sm font-semibold w-fit">
                                        <i class="fas fa-tag mr-1"></i>
                                        {{ $item->category->name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Message personnalisé -->
                    <div class="mb-6">
                        <label for="customMessage" class="block font-semibold text-gray-900 dark:text-white mb-2 text-sm lg:text-base">
                            <i class="fas fa-comment-dots text-primary-600 mr-2"></i>
                            Votre message (optionnel)
                        </label>
                        <textarea name="custom_message" 
                                  id="customMessage" 
                                  rows="4" 
                                  class="w-full border-2 border-primary-200/50 rounded-lg lg:rounded-xl p-3 lg:p-4 transition-all duration-300 focus:border-primary-600 focus:ring-4 focus:ring-primary-600/20 outline-none text-sm resize-none"
                                  placeholder="Bonjour, je suis très intéressé(e) par votre produit. Serait-il possible de négocier le prix ?"></textarea>
                        <small class="text-gray-500 dark:text-gray-400 text-xs lg:text-sm mt-2 block">
                            <i class="fas fa-info-circle mr-1"></i>
                            Un message automatique sera envoyé si vous laissez ce champ vide
                        </small>
                    </div>

                    <!-- Informations sur le processus -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200/50 rounded-xl lg:rounded-2xl p-4 lg:p-6">
                        <div class="font-bold text-blue-600 mb-4 flex items-center text-sm lg:text-base">
                            <i class="fas fa-lightbulb mr-2"></i>
                            Comment ça fonctionne ?
                        </div>
                        <ul class="space-y-2">
                            <li class="flex items-start text-gray-600 dark:text-gray-300 text-xs lg:text-sm">
                                <i class="fas fa-check text-blue-600 mr-2 lg:mr-3 mt-1 text-xs"></i>
                                Votre demande est envoyée instantanément au vendeur
                            </li>
                            <li class="flex items-start text-gray-600 dark:text-gray-300 text-xs lg:text-sm">
                                <i class="fas fa-check text-blue-600 mr-2 lg:mr-3 mt-1 text-xs"></i>
                                Le vendeur peut vous proposer une réduction personnalisée
                            </li>
                            <li class="flex items-start text-gray-600 dark:text-gray-300 text-xs lg:text-sm">
                                <i class="fas fa-check text-blue-600 mr-2 lg:mr-3 mt-1 text-xs"></i>
                                La réduction est appliquée automatiquement si acceptée
                            </li>
                            <li class="flex items-start text-gray-600 dark:text-gray-300 text-xs lg:text-sm">
                                <i class="fas fa-check text-blue-600 mr-2 lg:mr-3 mt-1 text-xs"></i>
                                Vous recevez une notification de la réponse
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="bg-gray-50 dark:bg-gray-900 p-4 lg:p-6 flex flex-col sm:flex-row gap-3 lg:gap-4">
                    <button type="button" onclick="closeModal('contactModal')" 
                        class="flex-1 bg-white dark:bg-gray-800 text-gray-700 border-2 border-gray-300 font-semibold py-2.5 lg:py-3 rounded-lg lg:rounded-xl transition-all duration-300 hover:bg-gray-50 dark:bg-gray-900 hover:-translate-y-0.5 text-sm lg:text-base">
                        <i class="fas fa-times mr-2"></i>
                        Annuler
                    </button>
                    <button type="button" id="submitDiscountBtn" onclick="submitDiscountRequest()" 
                        class="flex-2 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold py-2.5 lg:py-3 rounded-lg lg:rounded-xl transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg text-sm lg:text-base">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Envoyer la demande
                    </button>
                </div>
            </div>
        </div>
    @endif
@endauth

<script>
// ===== GESTION DU MODAL TAILWIND =====
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('opacity-0', 'invisible');
        modal.classList.add('opacity-100', 'visible');
        
        const modalContent = modal.querySelector('.bg-white dark:bg-gray-800');
        if (modalContent) {
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }
        
        // Empêcher le scroll du body
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('opacity-100', 'visible');
        modal.classList.add('opacity-0', 'invisible');
        
        const modalContent = modal.querySelector('.bg-white dark:bg-gray-800');
        if (modalContent) {
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
        }
        
        // Rétablir le scroll du body
        document.body.style.overflow = '';
    }
}

// Fermer le modal en cliquant en dehors
document.addEventListener('click', function(e) {
    const modal = document.getElementById('contactModal');
    if (modal && e.target === modal) {
        closeModal('contactModal');
    }
});

// Fermer le modal avec Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal('contactModal');
    }
});

// ===== GALERIE D'IMAGES =====
function changeMainImage(src, element) {
    const mainImg = document.getElementById('mainProductImg');
    const thumbnails = document.querySelectorAll('.thumbnail-item');
    
    // Retirer active de tous les thumbnails
    thumbnails.forEach(thumb => {
        thumb.classList.remove('border-primary-600', 'shadow-lg', 'shadow-primary-600/25', 'scale-105');
        thumb.classList.add('border-transparent');
    });
    
    // Ajouter active au thumbnail cliqué
    element.classList.remove('border-transparent');
    element.classList.add('border-primary-600', 'shadow-lg', 'shadow-primary-600/25', 'scale-105');
    
    // Changer l'image avec transition
    mainImg.style.opacity = '0';
    setTimeout(() => {
        mainImg.src = src;
        mainImg.style.opacity = '1';
    }, 150);
}

// ===== GESTION DE LA QUANTITÉ =====
function incrementQuantity() {
    const input = document.getElementById('quantityInput');
    const max = parseInt(input.getAttribute('max'));
    const currentValue = parseInt(input.value) || 1;
    
    if (currentValue < max && max > 0) {
        input.value = currentValue + 1;
    }
}

function decrementQuantity() {
    const input = document.getElementById('quantityInput');
    const min = parseInt(input.getAttribute('min'));
    const currentValue = parseInt(input.value) || 1;
    
    if (currentValue > min && currentValue > 0) {
        input.value = currentValue - 1;
    }
}

// ===== CONTACT VENDEUR =====
function contactSeller() {
    window.location.href = `/messages/conversation/{{ $item->user_id }}?item={{ $item->id }}`;
}

// ===== DEMANDE DE RÉDUCTION =====
function submitDiscountRequest() {
    const form = document.getElementById('contactForm');
    const customMessage = document.getElementById('customMessage').value;
    const submitBtn = document.getElementById('submitDiscountBtn');
    
    // Ajouter le message personnalisé au formulaire
    if (customMessage.trim()) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'custom_message';
        input.value = customMessage;
        form.appendChild(input);
    }
    
    // Désactiver le bouton et afficher l'état de chargement
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Envoi en cours...';
    
    // Afficher une notification
    showNotification('Envoi de votre demande en cours...', 'info');
    
    // Soumettre le formulaire après un court délai
    setTimeout(() => {
        form.submit();
    }, 500);
}

// ===== VÉRIFIER LES RÉDUCTIONS DISPONIBLES =====
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
                    <strong class="block mb-1">Réduction de ${discount.discount_percentage}% !</strong>
                    <small class="text-gray-500 dark:text-gray-400 block">
                        Prix original: <span class="line-through">${currencySymbol} ${new Intl.NumberFormat('fr-FR').format(discount.original_price)}</span>
                    </small>
                    <small class="block mt-1">
                        <span class="font-bold text-emerald-600">
                            Nouveau prix: ${currencySymbol} ${formattedFinalPrice}
                        </span>
                    </small>
                    <small class="text-gray-500 dark:text-gray-400 block mt-1">
                        Économie: <span class="font-bold text-emerald-600">${currencySymbol} ${formattedSavings}</span>
                    </small>
                    <small class="text-gray-500 dark:text-gray-400 block mt-1">
                        <i class="fas fa-clock mr-1"></i>
                        Valable jusqu'au ${new Date(discount.expires_at).toLocaleDateString('fr-FR')}
                    </small>
                </div>
            `;
            
            section.classList.remove('hidden');
            section.dataset.discountId = discount.id;
            
            updateAddToCartButton(true);
        }
        
        function applyDiscount() {
            const section = document.getElementById('discountSection');
            const discountId = section.dataset.discountId;
            
            if (!discountId) return;
            
            fetch(`/discounts/${discountId}/apply`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const priceElement = document.querySelector('.bg-gradient-to-r.from-primary-600.to-primary-800.bg-clip-text.text-transparent');
                    if (priceElement && priceElement.parentElement) {
                        const currencySymbol = '{{ $item->currency_symbol }}';
                        priceElement.parentElement.innerHTML = `
                            <div class="flex items-baseline justify-between flex-wrap gap-4">
                                <span class="text-2xl lg:text-3xl line-through text-gray-400">{{ $item->formatted_price }}</span>
                                <span class="text-4xl lg:text-5xl font-black text-emerald-600">
                                    ${currencySymbol} ${new Intl.NumberFormat('fr-FR').format(data.final_price)}
                                </span>
                                <span class="bg-emerald-500 text-white px-3 py-1 rounded-lg text-sm font-semibold">
                                    -${data.discount_percentage}%
                                </span>
                            </div>
                        `;
                    }
                    
                    section.classList.add('hidden');
                    updateAddToCartButton(true);
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.error || 'Erreur lors de l\'application de la réduction', 'danger');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Une erreur est survenue', 'danger');
            });
        }
    @endif
@endauth

// ===== SUPPRESSION D'ARTICLE =====
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

// ===== GESTION DES FAVORIS =====
const favoriteBtn = document.querySelector('.favorite-btn');
if (favoriteBtn) {
    favoriteBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const itemId = this.dataset.itemId;
        
        fetch(`/items/${itemId}/favorite`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const icon = this.querySelector('i');
                if (data.is_favorite) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    this.classList.add('bg-red-50', 'border-red-400');
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    this.classList.remove('bg-red-50', 'border-red-400');
                }
                showNotification(data.message, 'success');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Une erreur est survenue', 'danger');
        });
    });
}

// ===== SYSTÈME DE NOTIFICATIONS =====
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    
    const iconMap = {
        success: 'check-circle',
        danger: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    
    const colorMap = {
        success: 'emerald',
        danger: 'red',
        warning: 'amber',
        info: 'blue'
    };
    
    const color = colorMap[type];
    
    notification.className = `fixed top-5 right-5 z-50 min-w-80 max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-5 border-l-4 border-${color}-500 animate-slide-in-right`;
    
    notification.innerHTML = `
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-${color}-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-${iconMap[type]} text-${color}-600 text-lg"></i>
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-900 dark:text-white leading-relaxed">${message}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" 
                class="w-6 h-6 rounded-full hover:bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 hover:text-gray-600 dark:text-gray-300 transition-all duration-200">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.classList.add('animate-slide-out-right');
            setTimeout(() => notification.remove(), 400);
        }
    }, 5000);
}

// ===== MISE À JOUR DU BOUTON PANIER =====
function updateAddToCartButton(hasDiscount) {
    const addToCartBtn = document.getElementById('addToCartBtn');
    if (addToCartBtn && hasDiscount) {
        addToCartBtn.innerHTML = `
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-500"></div>
            <i class="fas fa-shopping-cart mr-3"></i>
            <span>Ajouter avec réduction</span>
            <i class="fas fa-tag ml-3"></i>
        `;
        addToCartBtn.className = 'w-full bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-bold text-lg py-4 rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl relative overflow-hidden group';
    }
}

// Animations CSS personnalisées via Tailwind
document.head.insertAdjacentHTML('beforeend', `
<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes slide-in-right {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slide-out-right {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(100%); opacity: 0; }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}

.animate-slide-in-right {
    animation: slide-in-right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.animate-slide-out-right {
    animation: slide-out-right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.shadow-3xl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}
</style>
`);
</script>
@endsection