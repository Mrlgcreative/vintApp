@extends('app')

@section('title', 'Booster vos Produits')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
        <div class="mb-4 lg:mb-0">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Booster vos Produits</h1>
            <p class="text-gray-600 dark:text-gray-300">Augmentez la visibilité de vos produits et boostez vos ventes</p>
            @if(request()->has('item_id'))
                @php
                    $preselectedItem = \App\Models\Item::find(request('item_id'));
                @endphp
                @if($preselectedItem)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-4 flex items-start">
                        <i class="fas fa-info-circle text-green-600 mt-0.5 mr-3"></i>
                        <div>
                            <span class="font-semibold text-green-800">Article présélectionné :</span>
                            <span class="text-green-700">"{{ $preselectedItem->name }}" - Choisissez un type de boost pour cet article.</span>
                        </div>
                    </div>
                @endif
            @endif
        </div>
        <a href="{{ route('boost.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-blue-600 text-blue-600 font-medium rounded-lg hover:bg-blue-50 transition-colors duration-200">
            <i class="fas fa-chart-bar mr-2"></i>Mon Dashboard
        </a>
    </div>

    <!-- Solde du portefeuille -->
    @php
        $walletBalance = auth()->user()->wallet_balance ?? 0;
        $isLowBalance = $walletBalance < 1000; // Considérer comme faible si < 1000 CDF
    @endphp
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8 flex flex-col sm:flex-row sm:items-center {{ $isLowBalance ? 'bg-red-50 border-red-200' : '' }}">
        <div class="flex items-center flex-grow">
            <div class="flex-shrink-0 mr-4">
                <i class="fas fa-wallet text-3xl {{ $isLowBalance ? 'text-red-600' : 'text-blue-600' }}"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold {{ $isLowBalance ? 'text-red-900' : 'text-blue-900' }} mb-1">Solde de votre portefeuille</h3>
                <span class="text-2xl font-bold {{ $isLowBalance ? 'text-red-700' : 'text-blue-700' }}">{{ number_format($walletBalance, 0, ',', ' ') }} CDF</span>
                @if($isLowBalance)
                    <p class="text-sm text-red-600 mt-1"><i class="fas fa-exclamation-triangle mr-1"></i>Solde faible - Rechargez pour acheter des boosts</p>
                @endif
            </div>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-6">
            <a href="#" class="inline-flex items-center px-4 py-2 {{ $isLowBalance ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>Recharger
            </a>
        </div>
    </div>

    <!-- Types de boost disponibles -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($boostTypes as $boostType)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-200 boost-card flex flex-col" data-boost-type="{{ $boostType->id }}">
            <div class="p-6 text-white relative" style="background: {{ $boostType->color ?? '#3B82F6' }};">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold mb-0 flex items-center">
                        <i class="{{ $boostType->icon ?? 'fas fa-star' }} mr-3"></i>
                        {{ $boostType->display_name }}
                    </h3>
                    @if($boostType->is_premium)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-500 text-yellow-900">
                        <i class="fas fa-crown mr-1"></i> Premium
                    </span>
                    @endif
                </div>
            </div>
            <div class="p-6 flex-grow flex flex-col">
                <p class="text-gray-600 dark:text-gray-300 mb-4">{{ $boostType->description }}</p>
                    
                <!-- Prix -->
                <div class="mb-4">
                    @php
                        // Détecter la devise préférée de l'utilisateur (CDF par défaut)
                        $userCurrency = auth()->user()->preferred_currency ?? 'CDF';
                        $price = $userCurrency === 'USD' ? $boostType->price_usd : $boostType->price_cdf;
                        $currencySymbol = $userCurrency === 'USD' ? '$' : 'CDF';
                    @endphp
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-500 dark:text-gray-400 text-sm">Prix de base:</span>
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">
                            @if($userCurrency === 'USD')
                                ${{ number_format($price, 2) }}
                            @else
                                {{ number_format($price, 0, ',', ' ') }} CDF
                            @endif
                        </span>
                    </div>
                    @if($boostType->price_per_day > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400 text-sm">Par jour:</span>
                        <span class="text-sm text-gray-700 dark:text-gray-200">+{{ number_format($boostType->price_per_day, 0, ',', ' ') }} {{ $currencySymbol }}</span>
                    </div>
                    @endif
                </div>

                <!-- Durée -->
                <div class="mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400 text-sm">Durée:</span>
                        <span class="text-gray-900 dark:text-white">{{ $boostType->min_duration }} - {{ $boostType->max_duration }} jours</span>
                    </div>
                </div>

                <!-- Avantages -->
                @if($boostType->benefits)
                <div class="mb-4">
                    <h6 class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-3">Avantages:</h6>
                    <ul class="space-y-2">
                        @foreach(json_decode($boostType->benefits, true) as $benefit)
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-0.5 text-sm"></i>
                            <span class="text-sm text-gray-700 dark:text-gray-200">{{ $benefit }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            
            <!-- Footer avec bouton -->
            <div class="px-6 pb-6">
                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center select-boost-btn" data-boost-type-id="{{ $boostType->id }}">
                    <i class="fas fa-rocket mr-2"></i>Choisir ce boost
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="text-center py-12 bg-gray-50 dark:bg-gray-900 rounded-lg">
                <i class="fas fa-exclamation-circle text-5xl text-gray-400 mb-4"></i>
                <h4 class="text-xl font-medium text-gray-600 dark:text-gray-300 mb-2">Aucun type de boost disponible</h4>
                <p class="text-gray-500 dark:text-gray-400">Veuillez contacter l'administrateur.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal de sélection de produit -->
<div id="productSelectionModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black bg-opacity-25 transition-opacity modal-overlay"></div>
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full mx-auto shadow-xl transform transition-all modal-content">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-box mr-2 text-blue-600"></i>Choisissez le produit à booster
                </h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 dark:text-gray-300 transition-colors close-modal">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Body -->
            <div class="p-6">
                <!-- Formulaire de boost -->
                <form id="boostForm" class="hidden">
                    @csrf
                    <input type="hidden" id="selectedBoostType" name="boost_type_id">
                    <input type="hidden" id="selectedItemId" name="item_id">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Durée (jours)</label>
                            <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="boostDuration" name="duration" required>
                                <!-- Options dynamiques -->
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Prix total</label>
                            <div class="flex">
                                <input type="text" id="totalPrice" class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg bg-gray-50 dark:bg-gray-900" readonly>
                                <span class="px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-l-0 border-gray-300 rounded-r-lg text-sm text-gray-700 dark:text-gray-200">CDF</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" class="px-4 py-2 text-gray-700 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:bg-gray-700 rounded-lg transition-colors close-modal">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center">
                            <i class="fas fa-credit-card mr-2"></i>Acheter le boost
                        </button>
                    </div>
                </form>
                
                <!-- Liste des produits -->
                <div id="productsList">
                    <div class="text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Chargement...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Styles personnalisés pour le modal et interactions */
.product-item {
    border: 2px solid transparent;
    border-radius: 8px;
    transition: all 0.2s ease;
    cursor: pointer;
    padding: 1rem;
    margin-bottom: 0.5rem;
}

.product-item:hover {
    border-color: #3B82F6;
    background-color: #F8FAFC;
}

.product-item.selected {
    border-color: #3B82F6;
    background-color: #EBF8FF;
}

.product-item img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 6px;
    margin-right: 1rem;
}

.modal-overlay {
    transition: opacity 0.3s ease-in-out;
}

.modal-content {
    transition: all 0.3s ease-in-out;
    transform: scale(0.95);
    opacity: 0;
}

.modal-content.show {
    transform: scale(1);
    opacity: 1;
}
</style>
@endpush

@push('scripts')
<script>
    // Variables globales
    const WALLET_BALANCE = {{ auth()->user()->wallet_balance ?? 0 }};

document.addEventListener('DOMContentLoaded', function() {
    let selectedBoostTypeId = null;
    let selectedItemId = null;
    let boostTypeData = null;

    // Fonction pour afficher les notifications
    function showNotification(message, type = 'info') {
        // Créer l'élément de notification
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg border-l-4 transform transition-all duration-500 translate-x-full`;
        
        // Couleurs selon le type
        const colors = {
            success: 'border-green-500 text-green-800',
            error: 'border-red-500 text-red-800',
            warning: 'border-yellow-500 text-yellow-800',
            info: 'border-blue-500 text-blue-800'
        };
        
        const icons = {
            success: 'fas fa-check-circle text-green-500',
            error: 'fas fa-exclamation-circle text-red-500',
            warning: 'fas fa-exclamation-triangle text-yellow-500',
            info: 'fas fa-info-circle text-blue-500'
        };
        
        notification.classList.add(...colors[type].split(' '));
        
        notification.innerHTML = `
            <div class="p-4 flex items-start">
                <div class="flex-shrink-0">
                    <i class="${icons[type]} text-xl"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button class="inline-flex text-gray-400 hover:text-gray-600 dark:text-gray-300" onclick="this.parentElement.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        
        // Ajouter au document
        document.body.appendChild(notification);
        
        // Animation d'entrée
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto-suppression après 5 secondes
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 500);
        }, 5000);
    }

    // Fonctions pour gérer le modal Tailwind
    function showModal() {
        const modal = document.getElementById('productSelectionModal');
        const modalContent = modal.querySelector('.modal-content');
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Animation d'apparition
        setTimeout(() => {
            modalContent.classList.add('show');
        }, 50);
    }

    function hideModal() {
        const modal = document.getElementById('productSelectionModal');
        const modalContent = modal.querySelector('.modal-content');
        
        modalContent.classList.remove('show');
        document.body.style.overflow = '';
        
        // Attendre la fin de l'animation
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Gestion des clics pour fermer le modal
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-overlay') || e.target.classList.contains('close-modal')) {
            hideModal();
        }
    });

    // Fermer avec Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideModal();
        }
    });

    // Gestion de la sélection de type de boost
    document.querySelectorAll('.select-boost-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            selectedBoostTypeId = this.dataset.boostTypeId;
            loadUserProducts();
            showModal();
        });
    });

    // Charger les produits de l'utilisateur
    function loadUserProducts() {
        const productsList = document.getElementById('productsList');
        productsList.innerHTML = `
            <div class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <p class="text-gray-500 dark:text-gray-400 mt-2">Chargement...</p>
            </div>
        `;

        // Récupérer l'ID de l'article depuis l'URL
        const urlParams = new URLSearchParams(window.location.search);
        const preselectedItemId = urlParams.get('item_id');

        // Charger les produits via API
        fetch('{{ route("boost.user-items") }}', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.items.length > 0) {
                let html = `<h6 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Sélectionnez un produit:</h6><div class="space-y-2">`;
                
                data.items.forEach(item => {
                    const imageUrl = item.images && item.images.length > 0 
                        ? item.images[0].image_url 
                        : 'https://via.placeholder.com/60';
                    
                    const hasActiveBoost = item.active_boosts && item.active_boosts.length > 0;
                    const disabledClass = hasActiveBoost ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer hover:bg-blue-50';
                    const disabledAttr = hasActiveBoost ? 'data-disabled="true"' : '';
                    
                    // Vérifier si c'est l'article présélectionné
                    const isPreselected = preselectedItemId && item.id == preselectedItemId;
                    const selectedClass = isPreselected && !hasActiveBoost ? 'selected' : '';
                    const checkIconClass = isPreselected && !hasActiveBoost ? '' : 'hidden';
                    
                    // Information sur le boost actif
                    let boostInfo = '';
                    if (hasActiveBoost) {
                        const boost = item.active_boosts[0];
                        const expiresAt = new Date(boost.expires_at).toLocaleDateString('fr-FR');
                        boostInfo = `<p class="text-sm text-yellow-600"><i class="fas fa-star mr-1"></i>Boost actif jusqu'au ${expiresAt}</p>`;
                    }
                    
                    html += `
                        <div class="product-item flex items-center p-3 border-2 border-transparent rounded-lg transition-all duration-200 ${disabledClass} ${selectedClass}" 
                             data-item-id="${item.id}" ${disabledAttr}>
                            <img src="${imageUrl}" alt="${item.title}" class="w-16 h-16 object-cover rounded-lg mr-3">
                            <div class="flex-1">
                                <h6 class="font-medium text-gray-900 dark:text-white">${item.title}</h6>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Prix: ${new Intl.NumberFormat('fr-FR').format(item.price)} CDF</p>
                                ${boostInfo}
                            </div>
                            <div class="flex items-center">
                                ${hasActiveBoost ? '<i class="fas fa-lock text-gray-400 mr-2"></i>' : ''}
                                <i class="fas fa-check-circle text-green-500 ${checkIconClass}"></i>
                            </div>
                        </div>
                    `;
                    
                    // Si c'est l'article présélectionné, le sélectionner automatiquement
                    if (isPreselected && !hasActiveBoost) {
                        selectedItemId = item.id;
                    }
                });
                
                html += `</div><p class="text-sm text-gray-500 dark:text-gray-400 mt-4">Cliquez sur un produit pour le sélectionner</p>`;
                productsList.innerHTML = html;
                
                // Ajouter les événements de sélection de produit
                addProductSelectionEvents();
                
                // Si un article est présélectionné, afficher automatiquement le formulaire
                if (preselectedItemId && selectedItemId) {
                    showBoostForm();
                }
            } else {
                productsList.innerHTML = `
                    <div class="text-center py-12">
                        <i class="fas fa-box-open text-5xl text-gray-400 mb-4"></i>
                        <h5 class="text-xl font-medium text-gray-600 dark:text-gray-300 mb-2">Aucun produit disponible pour le boost</h5>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Vous n'avez pas de produits actifs dans votre catalogue. Ajoutez des produits pour pouvoir les booster.</p>
                        <a href="/items/create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center">
                            <i class="fas fa-plus mr-2"></i>Ajouter un produit
                        </a>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            productsList.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl mb-2"></i>
                    <p class="text-gray-500 dark:text-gray-400 mb-3">Erreur lors du chargement des produits</p>
                    <button class="bg-blue-100 text-blue-700 px-3 py-1 rounded text-sm hover:bg-blue-200" onclick="loadUserProducts()">Réessayer</button>
                </div>
            `;
        });
    }

    // Ajouter les événements de sélection de produit
    function addProductSelectionEvents() {
        document.querySelectorAll('.product-item').forEach(item => {
            item.addEventListener('click', function() {
                // Vérifier si le produit est désactivé (a déjà un boost)
                if (this.dataset.disabled === 'true') {
                    showNotification('Ce produit a déjà un boost actif. Attendez qu\'il expire pour en ajouter un nouveau.', 'warning');
                    return;
                }

                // Désélectionner tous les produits
                document.querySelectorAll('.product-item').forEach(p => {
                    p.classList.remove('selected');
                    p.querySelector('.fa-check-circle').classList.add('hidden');
                });

                // Sélectionner ce produit
                this.classList.add('selected');
                this.querySelector('.fa-check-circle').classList.remove('hidden');
                selectedItemId = this.dataset.itemId;

                // Afficher le formulaire de boost
                showBoostForm();
            });
        });
    }

    // Afficher le formulaire de boost
    function showBoostForm() {
        document.getElementById('selectedBoostType').value = selectedBoostTypeId;
        document.getElementById('selectedItemId').value = selectedItemId;
        
        // Charger les options de durée
        loadDurationOptions();
        
        document.getElementById('boostForm').classList.remove('hidden');
        document.getElementById('productsList').style.opacity = '0.7';
    }

    // Charger les options de durée selon le type de boost
    function loadDurationOptions() {
        const durationSelect = document.getElementById('boostDuration');
        durationSelect.innerHTML = '<option value="">Chargement...</option>';
        
        // Récupérer les durées disponibles via AJAX
        fetch(`/boost/durations/${selectedBoostTypeId}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            durationSelect.innerHTML = '';
            
            if (data.success && data.durations.length > 0) {
                data.durations.forEach(days => {
                    const option = document.createElement('option');
                    option.value = days; // Maintenant c'est en jours
                    option.textContent = `${days} jour${days > 1 ? 's' : ''}`;
                    durationSelect.appendChild(option);
                });
                
                // Sélectionner la première option par défaut
                durationSelect.value = data.durations[0];
                calculatePrice();
            } else {
                // Fallback aux durées par défaut EN JOURS
                const defaultDurations = [1, 3, 7, 14]; // jours
                defaultDurations.forEach(days => {
                    const option = document.createElement('option');
                    option.value = days;
                    option.textContent = `${days} jour${days > 1 ? 's' : ''}`;
                    durationSelect.appendChild(option);
                });
                
                durationSelect.value = defaultDurations[0];
                calculatePrice();
            }
            
            // Ajouter un événement pour recalculer le prix
            durationSelect.addEventListener('change', calculatePrice);
        })
        .catch(error => {
            console.error('Erreur lors du chargement des durées:', error);
            // Fallback aux durées par défaut EN JOURS
            const defaultDurations = [1, 3, 7, 14];
            durationSelect.innerHTML = '';
            defaultDurations.forEach(days => {
                const option = document.createElement('option');
                option.value = days;
                option.textContent = `${days} jour${days > 1 ? 's' : ''}`;
                durationSelect.appendChild(option);
            });
            
            durationSelect.value = defaultDurations[0];
            calculatePrice();
            durationSelect.addEventListener('change', calculatePrice);
        });
    }

    // Calculer le prix du boost
    function calculatePrice() {
        const duration = document.getElementById('boostDuration').value;
        
        fetch('{{ route("boost.calculate-price") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                boost_type_id: selectedBoostTypeId,
                item_id: selectedItemId,
                duration: duration
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                document.getElementById('totalPrice').value = new Intl.NumberFormat('fr-FR').format(data.price);
                
                // Vérifier si le prix est abordable
                const submitBtn = document.querySelector('#boostForm button[type="submit"]');
                
                if (data.price > WALLET_BALANCE) {
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Solde insuffisant';
                } else {
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    submitBtn.innerHTML = '<i class="fas fa-credit-card mr-2"></i>Acheter le boost';
                }
            } else {
                showNotification(data.message || 'Erreur lors du calcul du prix', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur lors du calcul du prix:', error);
            if (error.name === 'TypeError' && error.message.includes('Failed to fetch')) {
                showNotification('Impossible de contacter le serveur. Vérifiez votre connexion.', 'error');
            } else if (error.message.includes('HTTP:')) {
                showNotification(`Erreur serveur: ${error.message}`, 'error');
            } else {
                showNotification('Erreur inattendue lors du calcul du prix', 'error');
            }
        });
    }

    // Gestion de la soumission du formulaire
    document.getElementById('boostForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Vérifier le prix total et le solde
        const totalPriceText = document.getElementById('totalPrice').value;
        const totalPrice = parseInt(totalPriceText.replace(/[^\d]/g, ''));
        
        if (totalPrice > WALLET_BALANCE) {
            showNotification(
                `Solde insuffisant. Vous avez ${new Intl.NumberFormat('fr-FR').format(WALLET_BALANCE)} CDF mais ${new Intl.NumberFormat('fr-FR').format(totalPrice)} CDF sont requis.`, 
                'error'
            );
            return;
        }
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Log des données envoyées pour debugging
        console.log('Données envoyées:', {
            boost_type_id: formData.get('boost_type_id'),
            item_id: formData.get('item_id'),
            duration: formData.get('duration')
        });
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Traitement...';
        submitBtn.disabled = true;

        fetch('{{ route("boost.purchase") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            // Log de la réponse pour debugging
            console.log('Statut de la réponse:', response.status);
            
            if (!response.ok) {
                // Essayer de récupérer le message d'erreur JSON
                return response.json().then(data => {
                    throw new Error(data.message || `Erreur HTTP: ${response.status}`);
                }).catch(jsonError => {
                    // Si ce n'est pas du JSON, lancer l'erreur HTTP standard
                    throw new Error(`Erreur HTTP: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Réponse reçue:', data);
            
            if (data.success) {
                // Succès
                showNotification('Boost acheté avec succès ! Votre produit est maintenant boosté.', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                // Erreur
                showNotification(data.message || 'Une erreur est survenue lors de l\'achat du boost', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur lors de l\'achat:', error);
            showNotification(error.message || 'Une erreur inattendue est survenue lors de l\'achat.', 'error');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
});
</script>
@endpush