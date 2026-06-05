@extends('app')

@section('title', 'Paiement Réussi')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Carte de succès principale -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 overflow-hidden animate-slide-up">
            <div class="px-8 py-12 text-center">
                <!-- Animation de confettis -->
                <div id="confetti-container" class="fixed inset-0 pointer-events-none z-50"></div>
                
                <!-- Icône de succès avec animation -->
                <div class="mb-6 animate-scale-in">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-green-100 rounded-full mb-4">
                        <svg class="w-12 h-12 text-green-600 animate-bounce-once" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">Paiement Réussi !</h1>
                <p class="text-gray-600 dark:text-gray-300 mb-8">Votre transaction a été traitée avec succès</p>
                
                @if(isset($transaction))
                    <!-- Montant en grand -->
                    <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl p-6 mb-8 animate-fade-in">
                        <div class="text-4xl font-bold text-primary-600 mb-2">
                            {{ number_format($transaction->amount, 2) }} {{ $transaction->currency ?? 'USD' }}
                        </div>
                        @if(isset($transaction->currency))
                            @if($transaction->currency === 'USD')
                                <div class="text-gray-500 dark:text-gray-400 text-sm">
                                    Environ {{ number_format($transaction->amount * 2650, 0) }} CDF
                                </div>
                            @elseif($transaction->currency === 'CDF')
                                <div class="text-gray-500 dark:text-gray-400 text-sm">
                                    Environ {{ number_format($transaction->amount / 2650, 2) }} USD
                                </div>
                            @endif
                        @else
                            <div class="text-gray-500 dark:text-gray-400 text-sm">
                                {{ number_format($transaction->amount * 2650, 0) }} CDF
                            </div>
                        @endif
                    </div>
                    
                    <!-- Détails de la transaction -->
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6 mb-8 text-left">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Détails de la transaction
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-300">ID Transaction</span>
                                <span class="font-medium text-primary-600">{{ $transaction->transaction_id }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-300">Opérateur</span>
                                <span class="font-medium flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ ucfirst(str_replace('_', ' ', $transaction->provider)) }}
                                </span>
                            </div>
                            @if($transaction->phone)
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-300">Téléphone</span>
                                <span class="font-medium">{{ $transaction->phone }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-300">Date</span>
                                <span class="font-medium">{{ $transaction->created_at->format('d/m/Y à H:i') }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-300">Objet</span>
                                <span class="font-medium">{{ $transaction->purpose }}</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600 dark:text-gray-300">Statut</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Confirmé
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Boutons d'action -->
                <div class="space-y-3">
                    <a href="{{ route('dashboard') }}" class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-primary-600 hover:bg-primary-700 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Retour au Dashboard
                    </a>
                    @if(isset($transaction))
                        <button onclick="window.print()" class="w-full inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-xl text-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Télécharger le Reçu
                        </button>
                    @endif
                    
                    @if(isset($unratedOrders) && $unratedOrders->count() > 0)
                        <button id="openRatingModal" class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 transition-all duration-200 transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            Notez vos vendeurs ({{ $unratedOrders->count() }})
                        </button>
                    @endif
                </div>
                
                <!-- Message de confirmation email -->
                <div class="mt-6 p-4 bg-blue-50 rounded-xl">
                    <p class="text-sm text-blue-700 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Un reçu de paiement a été envoyé à votre adresse email.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@if(isset($unratedOrders) && $unratedOrders->count() > 0)
<!-- Modal de notation moderne -->
<div id="ratingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-screen overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-7 h-7 mr-3 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    Notez vos achats
                </h2>
                <button id="closeRatingModal" class="text-gray-400 hover:text-gray-600 dark:text-gray-300 transition-colors duration-200 p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Partagez votre expérience avec vos vendeurs pour aider la communauté</p>
        </div>

        <div class="p-6 space-y-6" id="ratingsContainer">
            @foreach($unratedOrders as $order)
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-5 border border-gray-200 dark:border-gray-700" data-order-id="{{ $order->id }}">
                    <div class="flex items-start space-x-4 mb-4">
                        <div class="flex-shrink-0">
                            @if($order->item && $order->item->images && is_array($order->item->images) && count($order->item->images) > 0)
                                <img src="{{ Storage::url($order->item->images[0]) }}" 
                                     alt="{{ $order->item->name }}" 
                                     class="w-16 h-16 rounded-lg object-cover">
                            @else
                                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">{{ $order->item ? $order->item->name : 'Article' }}</h4>
                            <p class="text-gray-600 dark:text-gray-300 text-sm mb-2">Vendeur: {{ $order->seller ? $order->seller->name : 'Vendeur' }}</p>
                            <p class="text-primary-600 font-medium">{{ number_format($order->total_amount, 2) }} {{ $order->currency }}</p>
                        </div>
                    </div>

                    <form class="rating-form" data-order-id="{{ $order->id }}">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        
                        <!-- Système d'étoiles -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Note (cliquez sur les étoiles)</label>
                            <div class="flex space-x-1" data-rating="0">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" class="star text-3xl text-gray-300 hover:text-yellow-400 transition-colors duration-150" data-value="{{ $i }}">
                                        ★
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" value="0">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Cliquez sur une étoile pour noter de 1 à 5</p>
                        </div>

                        <!-- Commentaire optionnel -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Commentaire (optionnel)</label>
                            <textarea name="comment" rows="3" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm placeholder-gray-500 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200" placeholder="Partagez votre expérience avec ce vendeur..."></textarea>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white font-medium py-2.5 px-4 rounded-lg hover:from-primary-700 hover:to-primary-800 transition-all duration-200 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed">
                            <span class="submit-text">Envoyer la note</span>
                            <span class="loading-text hidden">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Envoi...
                            </span>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="p-6 border-t border-gray-200 dark:border-gray-700">
            <button id="skipRating" class="w-full text-center text-gray-500 hover:text-gray-700 dark:text-gray-200 transition-colors duration-200 font-medium">
                Passer pour le moment
            </button>
        </div>
    </div>
</div>
@endif

@push('styles')
<style>
/* Animations personnalisées */
@keyframes slide-up {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes scale-in {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes bounce-once {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.animate-slide-up {
    animation: slide-up 0.6s ease-out;
}

.animate-scale-in {
    animation: scale-in 0.5s ease-out;
}

.animate-fade-in {
    animation: fade-in 0.8s ease-out;
}

.animate-bounce-once {
    animation: bounce-once 0.6s ease-out;
}

/* Confettis */
.confetti {
    position: fixed;
    width: 8px;
    height: 8px;
    z-index: 9999;
    pointer-events: none;
    border-radius: 50%;
}

/* Impression */
@media print {
    #confetti-container,
    #ratingModal,
    #openRatingModal,
    button {
        display: none !important;
    }
    
    .shadow-xl {
        box-shadow: none !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation de confettis
    function createConfetti() {
        const colors = ['#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#ec4899'];
        const container = document.getElementById('confetti-container');
        
        if (!container) return;
        
        for (let i = 0; i < 50; i++) {
            setTimeout(() => {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.top = '-10px';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.animation = 'confetti-fall ' + (2 + Math.random() * 2) + 's linear forwards';
                confetti.style.animationDelay = Math.random() * 0.5 + 's';
                container.appendChild(confetti);
                
                setTimeout(() => confetti.remove(), 4000);
            }, i * 30);
        }
    }

    // Ajouter l'animation des confettis
    const style = document.createElement('style');
    style.textContent = '@keyframes confetti-fall { to { transform: translateY(100vh) rotate(360deg); opacity: 0; } }';
    document.head.appendChild(style);

    // Lancer les confettis au chargement
    setTimeout(createConfetti, 500);

    @if(isset($unratedOrders) && $unratedOrders->count() > 0)
    // Gestion du modal de notation
    const ratingModal = document.getElementById('ratingModal');
    const modalContent = document.getElementById('modalContent');
    const openBtn = document.getElementById('openRatingModal');
    const closeBtn = document.getElementById('closeRatingModal');
    const skipBtn = document.getElementById('skipRating');

    if (!ratingModal || !modalContent) return;

    // Afficher automatiquement le modal après 3 secondes
    setTimeout(() => {
        showModal();
    }, 3000);

    function showModal() {
        ratingModal.classList.remove('hidden');
        ratingModal.classList.add('flex');
        
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 50);
    }

    function hideModal() {
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            ratingModal.classList.add('hidden');
            ratingModal.classList.remove('flex');
        }, 200);
    }

    if (openBtn) openBtn.addEventListener('click', showModal);
    if (closeBtn) closeBtn.addEventListener('click', hideModal);
    if (skipBtn) skipBtn.addEventListener('click', hideModal);

    // Fermer en cliquant en dehors
    if (ratingModal) {
        ratingModal.addEventListener('click', (e) => {
            if (e.target === ratingModal) {
                hideModal();
            }
        });
    }

    // Gestion des étoiles
    document.querySelectorAll('.star').forEach(star => {
        star.addEventListener('click', function() {
            const container = this.closest('[data-rating]');
            const value = parseInt(this.dataset.value);
            const stars = container.querySelectorAll('.star');
            const hiddenInput = container.closest('form').querySelector('input[name="rating"]');

            container.dataset.rating = value;
            hiddenInput.value = value;

            stars.forEach((s, index) => {
                if (index < value) {
                    s.classList.remove('text-gray-300');
                    s.classList.add('text-yellow-400');
                } else {
                    s.classList.remove('text-yellow-400');
                    s.classList.add('text-gray-300');
                }
            });
        });

        star.addEventListener('mouseover', function() {
            const container = this.closest('[data-rating]');
            const value = parseInt(this.dataset.value);
            const stars = container.querySelectorAll('.star');

            stars.forEach((s, index) => {
                if (index < value) {
                    s.classList.add('text-yellow-400');
                    s.classList.remove('text-gray-300');
                } else {
                    s.classList.add('text-gray-300');
                    s.classList.remove('text-yellow-400');
                }
            });
        });

        star.addEventListener('mouseout', function() {
            const container = this.closest('[data-rating]');
            const currentRating = parseInt(container.dataset.rating);
            const stars = container.querySelectorAll('.star');

            stars.forEach((s, index) => {
                if (index < currentRating) {
                    s.classList.add('text-yellow-400');
                    s.classList.remove('text-gray-300');
                } else {
                    s.classList.add('text-gray-300');
                    s.classList.remove('text-yellow-400');
                }
            });
        });
    });

    // Soumission des formulaires de notation
    document.querySelectorAll('.rating-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const submitText = submitBtn.querySelector('.submit-text');
            const loadingText = submitBtn.querySelector('.loading-text');
            const formData = new FormData(this);
            
            // Vérifier qu'une note a été sélectionnée
            if (!formData.get('rating') || formData.get('rating') === '0') {
                alert('Veuillez sélectionner une note avant d\'envoyer');
                return;
            }

            // Afficher l'état de chargement
            submitBtn.disabled = true;
            submitText.classList.add('hidden');
            loadingText.classList.remove('hidden');

            try {
                const response = await fetch('{{ route("reviews.post-payment") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Masquer le formulaire avec une animation
                    const orderContainer = this.closest('[data-order-id]');
                    orderContainer.style.transition = 'all 0.3s ease-out';
                    orderContainer.style.transform = 'scale(0.95)';
                    orderContainer.style.opacity = '0.5';
                    
                    setTimeout(() => {
                        orderContainer.innerHTML = '<div class="text-center py-6"><div class="inline-flex items-center justify-center w-12 h-12 bg-green-100 rounded-full mb-3"><svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div><p class="text-green-700 font-medium">Merci pour votre avis !</p><p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Votre note a été enregistrée</p></div>';
                        orderContainer.style.transform = 'scale(1)';
                        orderContainer.style.opacity = '1';
                    }, 300);

                    // Vérifier s'il reste des formulaires
                    setTimeout(() => {
                        const remainingForms = document.querySelectorAll('.rating-form').length;
                        if (remainingForms === 0) {
                            setTimeout(() => {
                                hideModal();
                            }, 2000);
                        }
                    }, 1000);
                } else {
                    alert(data.message || 'Une erreur est survenue');
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de l\'envoi de la note');
            } finally {
                // Restaurer l'état du bouton
                submitBtn.disabled = false;
                submitText.classList.remove('hidden');
                loadingText.classList.add('hidden');
            }
        });
    });
    @endif
});
</script>
@endpush
@endsection