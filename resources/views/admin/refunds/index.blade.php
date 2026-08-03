@extends('layouts.admin')

@section('title', 'Remboursements')

@section('page-title', 'Gestion des remboursements')

@section('page-actions')
<div class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 dark:border-slate-700 dark:bg-slate-800">
    <span class="text-sm text-slate-500 dark:text-slate-400">Total des demandes :</span>
    <span class="text-base font-semibold tabular-nums text-primary-600 dark:text-primary-400">{{ $refunds->total() }}</span>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Messages Flash -->
    @if(session('success'))
        <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 animate-fade-in dark:border-emerald-900/30 dark:bg-emerald-900/20 dark:text-emerald-300" role="alert">
            <i class="fas fa-check-circle text-emerald-500"></i>
            <span class="flex-1">{{ session('success') }}</span>
            <button type="button" class="text-emerald-400 transition-colors hover:text-emerald-600" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 animate-fade-in dark:border-red-900/30 dark:bg-red-900/20 dark:text-red-300" role="alert">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            <span class="flex-1">{{ session('error') }}</span>
            <button type="button" class="text-red-400 transition-colors hover:text-red-600" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Filtres -->
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-slate-700 dark:bg-slate-800">
        <form method="GET" action="{{ route('admin.refunds.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div>
                <label for="status" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Statut</label>
                <select name="status" id="status" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">
                    <option value="">Tous les statuts</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approuvé</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejeté</option>
                    <option value="negotiation" {{ request('status') === 'negotiation' ? 'selected' : '' }}>Négociation</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Terminé</option>
                </select>
            </div>

            <div>
                <label for="refund_type" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Type</label>
                <select name="refund_type" id="refund_type" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">
                    <option value="">Tous les types</option>
                    <option value="full" {{ request('refund_type') === 'full' ? 'selected' : '' }}>Complet</option>
                    <option value="partial" {{ request('refund_type') === 'partial' ? 'selected' : '' }}>Partiel</option>
                </select>
            </div>

            <div>
                <label for="search" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Recherche</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="Numéro de commande, acheteur..."
                       class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">
            </div>

            <div class="flex items-end">
                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                    <i class="fas fa-search"></i>Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Liste des demandes -->
    <div class="space-y-4">
        @php
            $statusClasses = [
                'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300',
                'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300',
                'rejected' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-300',
                'negotiation' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300',
                'completed' => 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300',
            ];
        @endphp

        @forelse($refunds as $refund)
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-shadow duration-200 hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
                <div class="p-5 sm:p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <!-- Informations principales -->
                        <div class="flex-1">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    @if(!empty($refund->order?->item?->images) && count($refund->order->item->images) > 0)
                                        <img src="{{ asset('storage/' . $refund->order->item->images[0]) }}"
                                             alt="{{ $refund->order?->item?->name ?? 'Article' }}"
                                             class="h-16 w-16 rounded-xl object-cover ring-1 ring-slate-200 dark:ring-slate-600">
                                    @else
                                        <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-700">
                                            <i class="fas fa-image text-slate-400"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <h3 class="truncate text-lg font-semibold text-slate-900 dark:text-white">
                                        {{ $refund->order?->item?->name ?? 'Article supprimé' }}
                                    </h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">
                                        Commande #{{ $refund->order?->order_number ?? 'N/A' }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                                        Acheteur: {{ $refund->buyer?->name ?? 'Utilisateur supprimé' }} ({{ $refund->buyer?->email ?? 'N/A' }})
                                    </p>
                                    <p class="text-sm text-slate-600 dark:text-slate-300">
                                        Demandé le: {{ $refund->created_at?->format('d/m/Y à H:i') ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4 text-sm text-slate-700 dark:text-slate-200">
                                <strong>Raison:</strong> {{ Str::limit($refund->reason, 100) }}
                            </div>
                        </div>

                        <!-- Montants et statut -->
                        <div class="lg:text-right">
                            <div class="space-y-2">
                                <div>
                                    <span class="text-sm text-slate-500 dark:text-slate-400">Montant original:</span>
                                    <div class="text-lg font-semibold tabular-nums text-slate-900 dark:text-white">{{ $refund->currency === 'USD' ? '$' : 'FC' }} {{ number_format($refund->original_amount, 2) }}</div>
                                </div>
                                <div>
                                    <span class="text-sm text-slate-500 dark:text-slate-400">Remboursement demandé:</span>
                                    <div class="text-lg font-semibold tabular-nums text-primary-600 dark:text-primary-400">{{ $refund->formatted_refund_amount }}</div>
                                </div>
                                @if($refund->status === 'negotiation' && $refund->counter_offer_amount)
                                    <div>
                                        <span class="text-sm text-slate-500 dark:text-slate-400">Contre-offre:</span>
                                        <div class="text-lg font-semibold tabular-nums text-amber-600 dark:text-amber-400">{{ $refund->currency === 'USD' ? '$' : 'FC' }} {{ number_format($refund->counter_offer_amount, 2) }}</div>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $statusClasses[$refund->status] ?? 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300' }}">
                                    {{ $refund->status_text }}
                                </span>

                                <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                    Type: {{ $refund->refund_type === 'full' ? 'Complet' : 'Partiel' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    @if($refund->status === 'pending')
                        <div class="mt-6 flex flex-col gap-2 border-t border-slate-100 pt-6 sm:flex-row sm:flex-wrap dark:border-slate-700">
                            <a href="{{ route('admin.refunds.show', $refund) }}"
                               class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                                <i class="fas fa-eye"></i>Examiner
                            </a>

                            <form method="POST" action="{{ route('refund.process', $refund) }}" class="inline-flex">
                                @csrf
                                <input type="hidden" name="action" value="approve">
                                <button type="submit"
                                        onclick="return confirm('Approuver cette demande de remboursement ?')"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                                    <i class="fas fa-check"></i>Approuver
                                </button>
                            </form>

                            <button onclick="openNegotiationModal('{{ $refund->id }}')"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 hover:bg-amber-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                                <i class="fas fa-handshake"></i>Négocier
                            </button>

                            <form method="POST" action="{{ route('refund.process', $refund) }}" class="inline-flex">
                                @csrf
                                <input type="hidden" name="action" value="reject">
                                <button type="submit"
                                        onclick="return confirm('Rejeter cette demande de remboursement ?')"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                                    <i class="fas fa-times"></i>Rejeter
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <i class="fas fa-inbox mb-3 text-4xl text-slate-200 dark:text-slate-600"></i>
                <h3 class="mb-2 text-lg font-medium text-slate-900 dark:text-white">Aucune demande de remboursement</h3>
                <p class="text-slate-500 dark:text-slate-400">Il n'y a actuellement aucune demande de remboursement à traiter.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($refunds->hasPages())
        <div class="mt-8">
            {{ $refunds->links() }}
        </div>
    @endif
</div>

<!-- Modal de négociation -->
<div id="negotiationModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 p-4 backdrop-blur-sm">
    <div class="relative mx-auto my-16 w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-800">
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
            <input type="hidden" id="refundId" name="refund_id">
            <input type="hidden" name="action" value="negotiate">

            <div class="mb-4">
                <label for="counterOffer" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Montant proposé</label>
                <div class="relative">
                    <input type="number" id="counterOffer" name="counter_offer" step="0.01" min="0" required
                           class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 py-2.5 pl-8 pr-3.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/40 focus:border-amber-500 transition-colors">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500 dark:text-slate-400">$</span>
                </div>
            </div>

            <div class="mb-6">
                <label for="adminNotes" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Notes (optionnel)</label>
                <textarea id="adminNotes" name="admin_notes" rows="3"
                          placeholder="Expliquez votre contre-offre..."
                          class="w-full resize-none rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/40 focus:border-amber-500 transition-colors"></textarea>
            </div>

            <div class="flex flex-col-reverse gap-2 sm:flex-row">
                <button type="button" onclick="closeNegotiationModal()"
                        class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    Annuler
                </button>
                <button type="submit"
                        class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-amber-500 hover:bg-amber-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                    Proposer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Fonctions pour gérer le modal de négociation
function openNegotiationModal(refundId) {
    document.getElementById('refundId').value = refundId;
    document.getElementById('negotiationModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeNegotiationModal() {
    document.getElementById('negotiationModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('negotiationForm').reset();
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
    const refundId = document.getElementById('refundId').value;
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;

    // Désactiver le bouton et afficher le chargement
    submitButton.disabled = true;
    submitButton.textContent = 'Traitement...';

    fetch(`/refunds/${refundId}/process`, {
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
