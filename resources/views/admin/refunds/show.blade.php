@extends('layouts.admin')

@section('title', 'Demande de remboursement #' . $refund->id)
@section('page-title', 'Demande de remboursement #' . $refund->id)
@section('page-subtitle', 'Détails de la demande traitée par la plateforme')

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.refunds.index') }}"
       class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
        <i class="fas fa-arrow-left"></i>
        <span class="hidden sm:inline">Remboursements</span>
        <span class="sm:hidden">Retour</span>
    </a>
</div>
@endsection

@section('content')
@php
    $statusClasses = [
        'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300',
        'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300',
        'rejected' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-300',
        'negotiation' => 'bg-orange-50 text-orange-700 ring-orange-600/20 dark:bg-orange-900/30 dark:text-orange-300',
        'completed' => 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300',
    ];
@endphp

<div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Contenu principal -->
        <div class="space-y-6 lg:col-span-2">

            <!-- Informations de la demande -->
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="flex flex-wrap items-center gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-300">
                        <i class="fas fa-rotate-left text-sm"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Demande #{{ $refund->id }}</h3>
                    <span class="ml-auto inline-flex w-fit items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $statusClasses[$refund->status] ?? 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300' }}">
                        {{ $refund->status_text }}
                    </span>
                </div>

                <div class="p-5 sm:p-6">
                    <!-- Informations de base -->
                    <div class="mb-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <h4 class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Demandé le</h4>
                            <p class="text-base font-semibold text-slate-900 dark:text-white">{{ $refund->created_at?->format('d/m/Y à H:i') ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h4 class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Type</h4>
                            <p class="text-base font-semibold text-slate-900 dark:text-white">{{ $refund->refund_type === 'full' ? 'Remboursement complet' : 'Remboursement partiel' }}</p>
                        </div>
                    </div>

                    <!-- Montants -->
                    <div class="mb-6 rounded-xl bg-slate-50 p-4 dark:bg-slate-900/40">
                        <h4 class="mb-4 text-base font-semibold text-slate-900 dark:text-white">Détails financiers</h4>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div class="rounded-lg bg-white p-3 dark:bg-slate-800">
                                <span class="text-sm text-slate-500 dark:text-slate-400">Montant original</span>
                                <div class="text-xl font-bold tabular-nums text-slate-900 dark:text-white">{{ $refund->currency === 'USD' ? '$' : 'FC' }} {{ number_format($refund->original_amount, 2) }}</div>
                            </div>
                            <div class="rounded-lg bg-white p-3 dark:bg-slate-800">
                                <span class="text-sm text-slate-500 dark:text-slate-400">Remboursement demandé</span>
                                <div class="text-xl font-bold tabular-nums text-primary-600 dark:text-primary-400">{{ $refund->formatted_refund_amount }}</div>
                            </div>
                            @if($refund->status === 'negotiation' && $refund->counter_offer_amount)
                                <div class="rounded-lg bg-white p-3 dark:bg-slate-800">
                                    <span class="text-sm text-slate-500 dark:text-slate-400">Contre-offre</span>
                                    <div class="text-xl font-bold tabular-nums text-amber-600 dark:text-amber-400">{{ $refund->currency === 'USD' ? '$' : 'FC' }} {{ number_format($refund->counter_offer_amount, 2) }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Raison -->
                    <div class="mb-6">
                        <h4 class="mb-3 text-base font-semibold text-slate-900 dark:text-white">Raison de la demande</h4>
                        <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-900/40">
                            <p class="leading-relaxed text-slate-700 dark:text-slate-200">{{ $refund->reason }}</p>
                        </div>
                    </div>

                    <!-- Photos de preuves -->
                    @if($refund->evidence_photos && is_array($refund->evidence_photos) && count($refund->evidence_photos) > 0)
                        <div class="mb-6">
                            <h4 class="mb-3 text-base font-semibold text-slate-900 dark:text-white">Photos de preuves</h4>
                            <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                                @foreach($refund->evidence_photos as $photo)
                                    <div class="group relative">
                                        <img src="{{ asset('storage/' . $photo) }}"
                                             alt="Preuve"
                                             class="h-32 w-full cursor-pointer rounded-lg object-cover ring-1 ring-slate-200 shadow-sm transition-shadow duration-200 group-hover:shadow-md dark:ring-slate-600"
                                             onclick="openImageModal('{{ asset('storage/' . $photo) }}')">
                                        <div class="absolute inset-0 rounded-lg bg-black bg-opacity-0 transition-all duration-200 group-hover:bg-opacity-10"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Notes administratives -->
                    @if($refund->admin_notes)
                        <div>
                            <h4 class="mb-3 text-base font-semibold text-slate-900 dark:text-white">Notes administratives</h4>
                            <div class="rounded-xl border border-sky-200 bg-sky-50 p-4 dark:border-sky-800 dark:bg-sky-900/20">
                                <p class="text-sky-900 dark:text-sky-300">{{ $refund->admin_notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Informations de la commande -->
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-300">
                        <i class="fas fa-box text-sm"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Commande associée</h3>
                </div>
                <div class="p-5 sm:p-6">
                    @if($refund->order)
                    <div class="flex items-start gap-4">
                        @if(!empty($refund->order->item->images) && is_array($refund->order->item->images) && count($refund->order->item->images) > 0)
                            <img src="{{ asset('storage/' . $refund->order->item->images[0]) }}"
                                 alt="{{ $refund->order->item->name }}"
                                 class="h-20 w-20 flex-shrink-0 rounded-lg object-cover ring-1 ring-slate-200 dark:ring-slate-600">
                        @else
                            <div class="flex h-20 w-20 flex-shrink-0 items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-700">
                                <i class="fas fa-image text-xl text-slate-400"></i>
                            </div>
                        @endif

                        <div class="min-w-0 flex-1">
                            <h4 class="mb-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $refund->order->item?->name ?? 'Article supprimé' }}</h4>
                            <div class="space-y-1 text-sm text-slate-600 dark:text-slate-300">
                                <p><span class="font-medium">Numéro de commande :</span> #{{ $refund->order->order_number }}</p>
                                <p><span class="font-medium">Date de commande :</span> {{ $refund->order->created_at?->format('d/m/Y à H:i') ?? 'N/A' }}</p>
                                <p><span class="font-medium">Quantité :</span> {{ $refund->order->quantity }}</p>
                                <p><span class="font-medium">Prix unitaire :</span> ${{ number_format($refund->order->item?->price ?? 0, 2) }}</p>
                            </div>

                            <div class="mt-3">
                                @if(isset($refund->order->order_number))
                                <a href="{{ route('orders.show', $refund->order) }}"
                                   class="inline-flex items-center gap-1.5 text-sm font-medium text-primary-600 transition-colors hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300">
                                    <i class="fas fa-external-link-alt"></i>
                                    Voir la commande complète
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @else
                        <p class="text-slate-500 dark:text-slate-400">Commande supprimée ou indisponible.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6 lg:col-span-1">

            <!-- Informations acheteur -->
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-300">
                        <i class="fas fa-user text-sm"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Acheteur</h3>
                </div>
                <div class="p-5 sm:p-6">
                    @if($refund->buyer)
                    <div class="mb-4 text-center">
                        @if($refund->buyer->avatar)
                            <img src="{{ $refund->buyer->avatar_url }}" alt="{{ $refund->buyer->name }}"
                                 class="mx-auto mb-3 h-16 w-16 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-600">
                        @else
                            <div class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-primary-600 to-cyan-400 text-2xl font-bold text-white">
                                {{ strtoupper(substr($refund->buyer->name, 0, 1)) }}
                            </div>
                        @endif
                        <h4 class="font-semibold text-slate-900 dark:text-white">{{ $refund->buyer->name }}</h4>
                        <p class="text-sm text-slate-600 dark:text-slate-300">{{ $refund->buyer->email }}</p>
                    </div>

                    <div class="space-y-2 text-sm text-slate-700 dark:text-slate-200">
                        <p><span class="font-medium">Membre depuis :</span> {{ $refund->buyer->created_at?->format('M Y') ?? 'N/A' }}</p>
                        <p><span class="font-medium">Commandes totales :</span> {{ $refund->buyer->orders()->count() }}</p>
                    </div>
                    @else
                        <p class="text-slate-500 dark:text-slate-400">Utilisateur supprimé.</p>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            @if($refund->status === 'pending')
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-300">
                            <i class="fas fa-cogs text-sm"></i>
                        </div>
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Actions</h3>
                    </div>
                    <div class="space-y-3 p-5 sm:p-6">
                        <!-- Approuver -->
                        <form method="POST" action="{{ route('refund.process', $refund) }}">
                            @csrf
                            <input type="hidden" name="action" value="approve">
                            <button type="submit"
                                    onclick="return confirm('Approuver cette demande de remboursement ?')"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-3 text-sm font-medium text-white shadow-sm transition-colors">
                                <i class="fas fa-check"></i>
                                Approuver le remboursement
                            </button>
                        </form>

                        <!-- Négocier -->
                        <button onclick="openNegotiationModal()"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-amber-500 hover:bg-amber-600 px-4 py-3 text-sm font-medium text-white shadow-sm transition-colors">
                            <i class="fas fa-handshake"></i>
                            Proposer une contre-offre
                        </button>

                        <!-- Rejeter -->
                        <form method="POST" action="{{ route('refund.process', $refund) }}">
                            @csrf
                            <input type="hidden" name="action" value="reject">
                            <button type="submit"
                                    onclick="return confirm('Rejeter cette demande de remboursement ?')"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-red-600 hover:bg-red-700 px-4 py-3 text-sm font-medium text-white shadow-sm transition-colors">
                                <i class="fas fa-times"></i>
                                Rejeter la demande
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Historique -->
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-300">
                        <i class="fas fa-history text-sm"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Historique</h3>
                </div>
                <div class="p-5 sm:p-6">
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="mt-1.5 h-2.5 w-2.5 flex-shrink-0 rounded-full bg-sky-500"></div>
                            <div>
                                <p class="text-sm font-medium text-slate-900 dark:text-white">Demande créée</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $refund->created_at?->format('d/m/Y à H:i') ?? 'N/A' }}</p>
                            </div>
                        </div>

                        @if($refund->updated_at && $refund->updated_at != $refund->created_at)
                            <div class="flex items-start gap-3">
                                <div class="mt-1.5 h-2.5 w-2.5 flex-shrink-0 rounded-full bg-amber-500"></div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">Dernière modification</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $refund->updated_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de négociation -->
<div id="negotiationModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 p-4 backdrop-blur-sm">
    <div class="relative mx-auto my-16 w-full max-w-md rounded-xl bg-white p-6 shadow-2xl dark:bg-slate-800">
        <div class="mb-6 flex items-center justify-between">
            <h3 class="flex items-center gap-2 text-lg font-bold text-slate-900 dark:text-white">
                <i class="fas fa-handshake text-amber-500"></i>
                Proposer une contre-offre
            </h3>
            <button type="button" onclick="closeNegotiationModal()" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition-colors hover:bg-slate-100 dark:hover:bg-slate-700">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <form id="negotiationForm">
            @csrf
            <input type="hidden" name="action" value="negotiate">

            <div class="mb-4">
                <label for="counterOffer" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Montant proposé</label>
                <div class="relative">
                    <input type="number" id="counterOffer" name="counter_offer" step="0.01" min="0" max="{{ (float) $refund->original_amount }}" required
                           class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-8 pr-3.5 text-sm text-slate-900 placeholder:text-slate-400 transition-colors focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500 dark:text-slate-400">$</span>
                </div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Maximum : ${{ number_format((float) $refund->original_amount, 2) }}</p>
            </div>

            <div class="mb-6">
                <label for="adminNotes" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Notes explicatives</label>
                <textarea id="adminNotes" name="admin_notes" rows="3"
                          placeholder="Expliquez votre contre-offre..."
                          class="w-full resize-none rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 transition-colors focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white"></textarea>
            </div>

            <div class="flex flex-col-reverse gap-2 sm:flex-row">
                <button type="button" onclick="closeNegotiationModal()"
                        class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    Annuler
                </button>
                <button type="submit"
                        class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-amber-500 hover:bg-amber-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                    Envoyer la contre-offre
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal d'image -->
<div id="imageModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-75 p-5" onclick="closeImageModal()">
    <div class="relative mx-auto my-10 w-full max-w-2xl">
        <img id="modalImage" src="" alt="Preuve" class="w-full rounded-lg shadow-2xl">
    </div>
</div>
@endsection

@push('scripts')
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

// Fermer le modal en cliquant en dehors
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
@endpush