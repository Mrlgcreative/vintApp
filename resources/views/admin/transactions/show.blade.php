@extends('layouts.admin')

@section('title', 'Détails de la transaction')

@section('page-title', 'Détails de la transaction')

@section('page-actions')
<a href="{{ route('admin.transactions.index') }}"
   class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
    <i class="fas fa-arrow-left"></i>Retour à la liste
</a>
@endsection

@section('content')
<div class="space-y-6">
    @php
        $statusClasses = [
            'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300',
            'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300',
            'failed' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-300',
            'refunded' => 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300',
        ];
        $statusLabels = [
            'pending' => 'En attente',
            'completed' => 'Complétée',
            'failed' => 'Échouée',
            'refunded' => 'Remboursée',
        ];
    @endphp

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
        <!-- Informations principales -->
        <div class="border-b border-slate-100 p-5 sm:p-6 dark:border-slate-700">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <h2 class="mb-4 text-base font-semibold text-slate-900 dark:text-white">Informations générales</h2>
                    <dl class="grid grid-cols-1 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">ID Transaction</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $transaction->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Montant</dt>
                            <dd class="mt-1 text-sm font-semibold tabular-nums text-slate-900 dark:text-white">
                                {{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Type</dt>
                            <dd class="mt-1">
                                @if($transaction->type === 'deposit')
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">Dépôt</span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-300">Retrait</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Méthode de paiement</dt>
                            <dd class="mt-1 text-sm text-slate-900 dark:text-white">
                                {{ ucfirst($transaction->payment_method) }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Date</dt>
                            <dd class="mt-1 text-sm text-slate-900 dark:text-white">
                                {{ $transaction->created_at->format('d/m/Y H:i') }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h2 class="mb-4 text-base font-semibold text-slate-900 dark:text-white">Informations utilisateur</h2>
                    <div class="mb-4 flex items-center gap-4">
                        <img class="h-12 w-12 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-600"
                             src="{{ $transaction->user->profile_photo_url }}" alt="{{ $transaction->user->name }}">
                        <div>
                            <div class="text-sm font-semibold text-slate-900 dark:text-white">
                                {{ $transaction->user->name }}
                            </div>
                            <div class="text-sm text-slate-500 dark:text-slate-400">
                                {{ $transaction->user->email }}
                            </div>
                        </div>
                    </div>
                    <dl class="grid grid-cols-1 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">ID Utilisateur</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $transaction->user->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Date d'inscription</dt>
                            <dd class="mt-1 text-sm text-slate-900 dark:text-white">
                                {{ $transaction->user->created_at->format('d/m/Y') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Statut et actions -->
        <div class="bg-slate-50 p-5 sm:p-6 dark:bg-slate-900/40">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-base font-medium text-slate-900 dark:text-white">Statut actuel</h3>
                    <span class="mt-2 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $statusClasses[$transaction->status] }}">
                        {{ $statusLabels[$transaction->status] }}
                    </span>
                </div>

                @if($transaction->status === 'pending')
                <div class="flex flex-col gap-2 sm:flex-row">
                    <button type="button"
                            data-status-update="completed"
                            data-transaction-id="{{ $transaction->id }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                        <i class="fas fa-check"></i>Marquer comme complétée
                    </button>
                    <button type="button"
                            data-status-update="failed"
                            data-transaction-id="{{ $transaction->id }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                        <i class="fas fa-times"></i>Marquer comme échouée
                    </button>
                </div>
                @elseif($transaction->status === 'completed')
                <button type="button"
                        data-status-update="refunded"
                        data-transaction-id="{{ $transaction->id }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    <i class="fas fa-rotate-left"></i>Marquer comme remboursée
                </button>
                @endif
            </div>
        </div>

        <!-- Historique des mises à jour -->
        <div class="border-t border-slate-100 p-5 sm:p-6 dark:border-slate-700">
            <h3 class="mb-4 text-base font-medium text-slate-900 dark:text-white">Historique des mises à jour</h3>
            <div class="space-y-4">
                @foreach($transaction->status_history ?? [] as $history)
                <div class="flex items-center gap-4">
                    <span class="inline-block h-2.5 w-2.5 shrink-0 rounded-full ring-1 ring-inset {{ $statusClasses[$history->status] }}"></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-slate-900 dark:text-white">
                            {{ $statusLabels[$history->status] }}
                        </p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            {{ $history->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des mises à jour de statut
    const updateTransactionStatus = async (transactionId, newStatus) => {
        try {
            const response = await fetch(`/admin/transactions/${transactionId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status: newStatus })
            });

            const data = await response.json();

            if (response.ok) {
                // Rafraîchir la page pour montrer le nouveau statut
                window.location.reload();
            } else {
                alert('Erreur lors de la mise à jour du statut: ' + data.message);
            }
        } catch (error) {
            alert('Une erreur est survenue lors de la mise à jour du statut');
            console.error('Erreur:', error);
        }
    };

    // Gestionnaire pour les boutons de mise à jour de statut
    document.querySelectorAll('[data-status-update]').forEach(button => {
        button.addEventListener('click', function() {
            const transactionId = this.dataset.transactionId;
            const newStatus = this.dataset.statusUpdate;

            if (confirm('Êtes-vous sûr de vouloir modifier le statut de cette transaction ?')) {
                updateTransactionStatus(transactionId, newStatus);
            }
        });
    });
});
</script>
@endpush
