@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.refunds.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-blue-600 transition-colors duration-200">
                        <i class="fas fa-undo mr-2"></i>
                        Remboursements
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-sm font-medium text-blue-600">Demande #{{ $refund->id }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Contenu principal -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Informations de la demande -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6">
                        <div class="flex items-center justify-between">
                            <h1 class="text-2xl font-bold">Demande de remboursement #{{ $refund->id }}</h1>
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'approved' => 'bg-green-100 text-green-800 border-green-200',
                                    'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                    'negotiation' => 'bg-orange-100 text-orange-800 border-orange-200',
                                    'completed' => 'bg-blue-100 text-blue-800 border-blue-200'
                                ];
                            @endphp
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium border {{ $statusClasses[$refund->status] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100 border-gray-200 dark:border-gray-700' }}">
                                {{ $refund->status_text }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <!-- Informations de base -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Demandé le</h3>
                                <p class="text-lg text-gray-900 dark:text-white">{{ $refund->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Type</h3>
                                <p class="text-lg text-gray-900 dark:text-white">{{ $refund->refund_type === 'full' ? 'Remboursement complet' : 'Remboursement partiel' }}</p>
                            </div>
                        </div>

                        <!-- Montants -->
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Détails financiers</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Montant original</span>
                                    <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $refund->currency === 'USD' ? '$' : 'FC' }} {{ number_format($refund->original_amount, 2) }}</div>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Remboursement demandé</span>
                                    <div class="text-xl font-bold text-blue-600">{{ $refund->formatted_refund_amount }}</div>
                                </div>
                                @if($refund->status === 'negotiation' && $refund->counter_offer_amount)
                                    <div>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Contre-offre</span>
                                        <div class="text-xl font-bold text-orange-600">{{ $refund->currency === 'USD' ? '$' : 'FC' }} {{ number_format($refund->counter_offer_amount, 2) }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Raison -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Raison de la demande</h3>
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4">
                                <p class="text-gray-700 dark:text-gray-200 leading-relaxed">{{ $refund->reason }}</p>
                            </div>
                        </div>

                        <!-- Photos de preuves -->
                        @if($refund->evidence_photos && is_array($refund->evidence_photos) && count($refund->evidence_photos) > 0)
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Photos de preuves</h3>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    @foreach($refund->evidence_photos as $photo)
                                        <div class="relative group">
                                            <img src="{{ asset('storage/' . $photo) }}" 
                                                 alt="Preuve" 
                                                 class="w-full h-32 object-cover rounded-lg shadow-sm group-hover:shadow-md transition-shadow duration-200 cursor-pointer"
                                                 onclick="openImageModal('{{ asset('storage/' . $photo) }}')">
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 rounded-lg transition-all duration-200"></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Notes administratives -->
                        @if($refund->admin_notes)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Notes administratives</h3>
                                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                    <p class="text-blue-900">{{ $refund->admin_notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informations de la commande -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Commande associée</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            @if(!empty($refund->order->item->images) && is_array($refund->order->item->images) && count($refund->order->item->images) > 0)
                                <img src="{{ asset('storage/' . $refund->order->item->images[0]) }}" 
                                     alt="{{ $refund->order->item->name }}" 
                                     class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                            @else
                                <div class="w-20 h-20 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-image text-gray-400 text-xl"></i>
                                </div>
                            @endif
                            
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $refund->order->item->name }}</h4>
                                <div class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                                    <p><strong>Numéro de commande:</strong> #{{ $refund->order->order_number }}</p>
                                    <p><strong>Date de commande:</strong> {{ $refund->order->created_at->format('d/m/Y à H:i') }}</p>
                                    <p><strong>Quantité:</strong> {{ $refund->order->quantity }}</p>
                                    <p><strong>Prix unitaire:</strong> ${{ number_format($refund->order->item->price, 2) }}</p>
                                </div>
                                
                                <div class="mt-3">
                                    <a href="{{ route('orders.show', $refund->order) }}" 
                                       class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        <i class="fas fa-external-link-alt mr-1"></i>
                                        Voir la commande complète
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                
                <!-- Informations acheteur -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Acheteur</h3>
                    </div>
                    <div class="p-6">
                        <div class="text-center mb-4">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-user text-blue-600 text-xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">{{ $refund->buyer->name }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $refund->buyer->email }}</p>
                        </div>
                        
                        <div class="space-y-2 text-sm">
                            <p><strong>Membre depuis:</strong> {{ $refund->buyer->created_at->format('M Y') }}</p>
                            <p><strong>Commandes totales:</strong> {{ $refund->buyer->orders()->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                @if($refund->status === 'pending')
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Actions</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            
                            <!-- Approuver -->
                            <form method="POST" action="{{ route('refund.process', $refund) }}">
                                @csrf
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" 
                                        onclick="return confirm('Approuver cette demande de remboursement ?')"
                                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-xl shadow-lg shadow-green-500/25 hover:from-green-600 hover:to-green-700 hover:shadow-xl hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-300">
                                    <i class="fas fa-check mr-2"></i>
                                    Approuver le remboursement
                                </button>
                            </form>
                            
                            <!-- Négocier -->
                            <button onclick="openNegotiationModal()" 
                                    class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold rounded-xl shadow-lg shadow-orange-500/25 hover:from-orange-600 hover:to-orange-700 hover:shadow-xl hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-300">
                                <i class="fas fa-handshake mr-2"></i>
                                Proposer une contre-offre
                            </button>
                            
                            <!-- Rejeter -->
                            <form method="POST" action="{{ route('refund.process', $refund) }}">
                                @csrf
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" 
                                        onclick="return confirm('Rejeter cette demande de remboursement ?')"
                                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold rounded-xl shadow-lg shadow-red-500/25 hover:from-red-600 hover:to-red-700 hover:shadow-xl hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-300">
                                    <i class="fas fa-times mr-2"></i>
                                    Rejeter la demande
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Historique -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Historique</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Demande créée</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $refund->created_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            </div>
                            
                            @if($refund->updated_at != $refund->created_at)
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2 flex-shrink-0"></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Dernière modification</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $refund->updated_at->format('d/m/Y à H:i') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de négociation -->
<div id="negotiationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-2xl bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-handshake text-orange-500 mr-2"></i>
                Proposer une contre-offre
            </h3>
            
            <form id="negotiationForm">
                @csrf
                <input type="hidden" name="action" value="negotiate">
                
                <div class="mb-4">
                    <label for="counterOffer" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Montant proposé</label>
                    <div class="relative">
                        <input type="number" id="counterOffer" name="counter_offer" step="0.01" min="0" max="{{ $refund->original_amount }}" required
                               class="w-full pl-8 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <span class="absolute left-3 top-2 text-gray-500 dark:text-gray-400">$</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Maximum: ${{ number_format($refund->original_amount, 2) }}</p>
                </div>

                <div class="mb-6">
                    <label for="adminNotes" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Notes explicatives</label>
                    <textarea id="adminNotes" name="admin_notes" rows="3"
                              placeholder="Expliquez votre contre-offre..."
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 resize-none"></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeNegotiationModal()"
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 dark:text-gray-200 font-semibold rounded-lg hover:bg-gray-400 transition-colors duration-200">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-200">
                        Envoyer la contre-offre
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal d'image -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 overflow-y-auto h-full w-full hidden z-50" onclick="closeImageModal()">
    <div class="relative top-10 mx-auto p-5 w-11/12 md:w-3/4 lg:w-1/2">
        <img id="modalImage" src="" alt="Preuve" class="w-full h-auto rounded-lg shadow-xl">
    </div>
</div>

<script>
// Fonctions pour gérer le modal de négociation
function openNegotiationModal() {
    document.getElementById('negotiationModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeNegotiationModal() {
    document.getElementById('negotiationModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('negotiationForm').reset();
}

// Fonctions pour gérer le modal d'image
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Fermer les modals en cliquant en dehors
document.getElementById('negotiationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeNegotiationModal();
    }
});

// Soumission du formulaire de négociation
document.getElementById('negotiationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    
    // Désactiver le bouton et afficher le chargement
    submitButton.disabled = true;
    submitButton.textContent = 'Traitement...';
    
    fetch('{{ route('refund.process', $refund) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Contre-offre envoyée avec succès !');
            closeNegotiationModal();
            window.location.reload();
        } else {
            alert(data.error || 'Erreur lors de l\'envoi de la contre-offre');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue lors de l\'envoi');
    })
    .finally(() => {
        // Réactiver le bouton
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    });
});
</script>

@endsection