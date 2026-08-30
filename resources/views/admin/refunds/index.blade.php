@extends('layouts.admin')

@section('title', 'Remboursements')
@section('page-title', 'Gestion des remboursements')
@section('page-subtitle', 'Suivi des demandes de remboursement de la plateforme')

@section('content')
{{-- Statistiques --}}
<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 xl:grid-cols-4">
    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Total des demandes</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['total'], 0, ',', ' ') }}</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300">
                <i class="fas fa-rotate-left text-[10px] text-sky-500"></i>
                Demandes
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-file-invoice-dollar text-xs text-sky-500"></i>
                Demandes reçues
            </div>
            <div class="text-xs text-slate-400">Sur toute la plateforme</div>
        </div>
    </div>

    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">En attente</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['pending'], 0, ',', ' ') }}</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-400">
                <i class="fas fa-clock text-[10px]"></i>
                À traiter
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-hourglass-half text-xs text-amber-500"></i>
                En attente de traitement
            </div>
            <div class="text-xs text-slate-400">À examiner</div>
        </div>
    </div>

    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">En négociation</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['negotiation'], 0, ',', ' ') }}</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-orange-200 bg-orange-50 px-2 py-0.5 text-xs font-medium text-orange-700 dark:border-orange-500/30 dark:bg-orange-500/10 dark:text-orange-400">
                <i class="fas fa-handshake text-[10px]"></i>
                Contre-offre
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-handshake text-xs text-orange-500"></i>
                En cours de discussion
            </div>
            <div class="text-xs text-slate-400">Négociation en cours</div>
        </div>
    </div>

    <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">Remboursés</p>
        <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ number_format($stats['completed'] + $stats['approved'], 0, ',', ' ') }}</p>
        <div class="absolute right-4 top-4">
            <span class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400">
                <i class="fas fa-circle-check text-[10px]"></i>
                Terminés
            </span>
        </div>
        <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
            <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                <i class="fas fa-circle-check text-xs text-emerald-500"></i>
                Remboursements effectués
            </div>
            <div class="text-xs text-slate-400">Approuvés + terminés</div>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="mb-6 rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <div class="p-5 sm:p-6">
        <form method="GET" action="{{ route('admin.refunds.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div>
                <label for="status" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Statut</label>
                <select name="status" id="status" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
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
                <select name="refund_type" id="refund_type" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                    <option value="">Tous les types</option>
                    <option value="full" {{ request('refund_type') === 'full' ? 'selected' : '' }}>Complet</option>
                    <option value="partial" {{ request('refund_type') === 'partial' ? 'selected' : '' }}>Partiel</option>
                </select>
            </div>

            <div>
                <label for="search" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Recherche</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="Numéro de commande, acheteur..."
                       class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
            </div>

            <div class="flex items-end gap-2 sm:col-span-2 md:col-span-1">
                <button type="submit" class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700">
                    <i class="fas fa-search"></i>Filtrer
                </button>
                <a href="{{ route('admin.refunds.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Liste des demandes -->
<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-5 py-4 dark:border-slate-700">
        <h3 class="flex items-center gap-2 text-sm sm:text-base font-semibold text-slate-900 dark:text-white">
            <i class="fas fa-rotate-left text-primary-600"></i>
            Demandes de remboursement
            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                {{ $refunds->total() ?? 0 }} total
            </span>
        </h3>
        <span class="text-xs text-slate-500 dark:text-slate-400">
            Page {{ $refunds->currentPage() }}/{{ $refunds->lastPage() }}
        </span>
    </div>

    <div class="divide-y divide-slate-100 dark:divide-slate-700">
        @php
            $statusClasses = [
                'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300',
                'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300',
                'rejected' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-300',
                'negotiation' => 'bg-orange-50 text-orange-700 ring-orange-600/20 dark:bg-orange-900/30 dark:text-orange-300',
                'completed' => 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300',
            ];
        @endphp

        @forelse($refunds as $refund)
            <div class="p-5 transition-colors sm:p-6 hover:bg-slate-50/50 dark:hover:bg-slate-700/20">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <!-- Informations principales -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                @if(!empty($refund->order?->item?->images) && count($refund->order->item->images) > 0)
                                    <img src="{{ asset('storage/' . $refund->order->item->images[0]) }}"
                                         alt="{{ $refund->order?->item?->name ?? 'Article' }}"
                                         class="h-16 w-16 rounded-lg object-cover ring-1 ring-slate-200 dark:ring-slate-600">
                                @else
                                    <div class="flex h-16 w-16 items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-700">
                                        <i class="fas fa-image text-slate-400"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="truncate text-lg font-semibold text-slate-900 dark:text-white">
                                        {{ $refund->order?->item?->name ?? 'Article supprimé' }}
                                    </h3>
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $statusClasses[$refund->status] ?? 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300' }}">
                                        {{ $refund->status_text }}
                                    </span>
                                </div>
                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    Commande #{{ $refund->order?->order_number ?? 'N/A' }}
                                </p>
                                <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-slate-600 dark:text-slate-300">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-user text-xs text-slate-400"></i>
                                        {{ $refund->buyer?->name ?? 'Utilisateur supprimé' }}
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-calendar-day text-xs text-slate-400"></i>
                                        {{ $refund->created_at?->format('d/m/Y à H:i') ?? 'N/A' }}
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-tag text-xs text-slate-400"></i>
                                        {{ $refund->refund_type === 'full' ? 'Complet' : 'Partiel' }}
                                    </span>
                                </div>
                                <p class="mt-3 text-sm text-slate-700 dark:text-slate-200">
                                    <span class="font-medium">Raison :</span> {{ Str::limit($refund->reason, 100) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Montants -->
                    <div class="lg:w-56 lg:shrink-0">
                        <div class="space-y-1.5 rounded-lg bg-slate-50 p-3 dark:bg-slate-900/40">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-xs text-slate-500 dark:text-slate-400">Montant original</span>
                                <div class="text-sm font-semibold tabular-nums text-slate-900 dark:text-white">{{ $refund->currency === 'USD' ? '$' : 'FC' }} {{ number_format($refund->original_amount, 2) }}</div>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-xs text-slate-500 dark:text-slate-400">Remboursement</span>
                                <div class="text-sm font-semibold tabular-nums text-primary-600 dark:text-primary-400">{{ $refund->formatted_refund_amount }}</div>
                            </div>
                            @if($refund->status === 'negotiation' && $refund->counter_offer_amount)
                                <div class="flex items-center justify-between gap-3 border-t border-slate-200 pt-1.5 dark:border-slate-700">
                                    <span class="text-xs text-slate-500 dark:text-slate-400">Contre-offre</span>
                                    <div class="text-sm font-semibold tabular-nums text-amber-600 dark:text-amber-400">{{ $refund->currency === 'USD' ? '$' : 'FC' }} {{ number_format($refund->counter_offer_amount, 2) }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                @if($refund->status === 'pending')
                    <div class="mt-5 flex flex-col gap-2 border-t border-slate-100 pt-5 sm:flex-row sm:flex-wrap dark:border-slate-700">
                    <a href="{{ route('admin.refunds.show', $refund) }}"
                               class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 sm:flex-1 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                                <i class="fas fa-eye"></i>Examiner
                            </a>

                            <form method="POST" action="{{ route('refund.process', $refund) }}" class="inline-flex sm:flex-1">
                                @csrf
                                <input type="hidden" name="action" value="approve">
                                <button type="submit"
                                        onclick="return confirm('Approuver cette demande de remboursement ?')"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                                    <i class="fas fa-check"></i>Approuver
                                </button>
                            </form>

                            <button onclick="openNegotiationModal('{{ $refund->id }}')"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-amber-500 hover:bg-amber-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors sm:flex-1">
                                <i class="fas fa-handshake"></i>Négocier
                            </button>

                            <form method="POST" action="{{ route('refund.process', $refund) }}" class="inline-flex sm:flex-1">
                                @csrf
                                <input type="hidden" name="action" value="reject">
                                <button type="submit"
                                        onclick="return confirm('Rejeter cette demande de remboursement ?')"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-red-600 hover:bg-red-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                                    <i class="fas fa-times"></i>Rejeter
                                </button>
                            </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="p-12 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700">
                    <i class="fas fa-inbox text-slate-400"></i>
                </div>
                <h3 class="mb-2 text-lg font-medium text-slate-900 dark:text-white">Aucune demande de remboursement</h3>
                <p class="text-slate-500 dark:text-slate-400">Il n'y a actuellement aucune demande de remboursement à traiter.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Pagination -->
@if($refunds->hasPages())
    <div class="mt-6">
        {{ $refunds->links() }}
    </div>
@endif

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
            <input type="hidden" id="refundId" name="refund_id">
            <input type="hidden" name="action" value="negotiate">

            <div class="mb-4">
                <label for="counterOffer" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Montant proposé</label>
                <div class="relative">
                    <input type="number" id="counterOffer" name="counter_offer" step="0.01" min="0" required
                           class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-8 pr-3.5 text-sm text-slate-900 placeholder:text-slate-400 transition-colors focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500 dark:text-slate-400">$</span>
                </div>
            </div>

            <div class="mb-6">
                <label for="adminNotes" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Notes (optionnel)</label>
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