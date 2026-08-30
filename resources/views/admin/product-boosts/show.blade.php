@extends('layouts.admin')

@section('title', 'Détails du boost')
@section('page-title', 'Détails du boost #' . $productBoost->id)

@section('page-actions')
<div class="flex flex-wrap items-center gap-3">
    @if($productBoost->status === 'active')
    <button type="button" onclick="showCancelModal()"
            class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-red-700">
        <i class="fas fa-ban"></i>Annuler le boost
    </button>
    @endif
    <a href="{{ route('admin.product-boosts.index') }}"
       class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
        <i class="fas fa-arrow-left"></i>Retour
    </a>
</div>
@endsection

@section('content')
@php
    $formatPrice = function ($price, $currency) {
        $currency = $currency ?? 'USD';
        return $currency === 'CDF'
            ? number_format((float) $price, 0, ',', ' ') . ' FC'
            : '$' . number_format((float) $price, 2);
    };

    $statusBadge = [
        'active' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
        'expired' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300',
        'cancelled' => 'bg-red-50 text-red-700 dark:bg-red-900/40 dark:text-red-300',
    ];
    $statusLabel = [
        'active' => 'Actif',
        'expired' => 'Expiré',
        'cancelled' => 'Annulé',
    ];
@endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Colonne gauche -->
    <div class="space-y-6 lg:col-span-1">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-300">
                    <i class="fas fa-circle-info"></i>
                </div>
                <h4 class="font-semibold text-slate-900 dark:text-white">Informations générales</h4>
            </div>
            <dl class="space-y-0 divide-y divide-slate-100 text-sm dark:divide-slate-700/60">
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-slate-500 dark:text-slate-400">Statut</dt>
                    <dd>
                        <span class="inline-flex items-center gap-1.5 rounded-md border border-transparent px-2.5 py-0.5 text-xs font-medium {{ $statusBadge[$productBoost->status] ?? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">
                            <i class="fas fa-circle text-[5px] opacity-70"></i>
                            {{ $statusLabel[$productBoost->status] ?? ucfirst($productBoost->status) }}
                        </span>
                    </dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-slate-500 dark:text-slate-400">Type de boost</dt>
                    <dd>
                        <span class="inline-flex items-center gap-1.5 rounded-md border border-transparent px-2.5 py-0.5 text-xs font-medium"
                              style="background: {{ $productBoost->boostType?->color ?? '#7c3aed' }}1a; color: {{ $productBoost->boostType?->color ?? '#7c3aed' }}; border-color: {{ $productBoost->boostType?->color ?? '#7c3aed' }}40;">
                            <i class="{{ $productBoost->boostType?->icon ?? 'fas fa-bolt' }} text-[10px]"></i>
                            {{ $productBoost->boostType?->display_name ?? 'N/A' }}
                        </span>
                    </dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-slate-500 dark:text-slate-400">Durée</dt>
                    <dd class="font-semibold tabular-nums text-slate-900 dark:text-white">{{ $productBoost->duration }} jours</dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-slate-500 dark:text-slate-400">Prix total</dt>
                    <dd class="font-semibold tabular-nums text-slate-900 dark:text-white">{{ $formatPrice($productBoost->total_price, $productBoost->currency) }}</dd>
                </div>
                @if($productBoost->refund_amount)
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-slate-500 dark:text-slate-400">Remboursement</dt>
                    <dd class="font-semibold tabular-nums text-red-600 dark:text-red-400">{{ $formatPrice($productBoost->refund_amount, $productBoost->currency) }}</dd>
                </div>
                @endif
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-slate-500 dark:text-slate-400">Date de début</dt>
                    <dd class="font-semibold text-slate-900 dark:text-white">{{ $productBoost->activated_at?->format('d/m/Y H:i') ?? 'N/A' }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-slate-500 dark:text-slate-400">Date de fin</dt>
                    <dd class="font-semibold text-slate-900 dark:text-white">{{ $productBoost->expires_at?->format('d/m/Y H:i') ?? 'N/A' }}</dd>
                </div>
                @if($productBoost->cancelled_at)
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-slate-500 dark:text-slate-400">Annulé le</dt>
                    <dd class="font-semibold text-red-600 dark:text-red-400">{{ $productBoost->cancelled_at->format('d/m/Y H:i') }}</dd>
                </div>
                @endif
                <div class="flex items-center justify-between gap-4 pt-3">
                    <dt class="text-slate-500 dark:text-slate-400">Créé le</dt>
                    <dd class="font-semibold text-slate-900 dark:text-white">{{ $productBoost->created_at->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-sky-50 text-sky-600 dark:bg-sky-900/20 dark:text-sky-300">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h4 class="font-semibold text-slate-900 dark:text-white">Performance</h4>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="rounded-xl bg-slate-50 p-4 text-center dark:bg-slate-900/50">
                    <i class="fas fa-eye mb-2 text-slate-400"></i>
                    <p class="text-2xl font-bold tabular-nums text-slate-900 dark:text-white">{{ number_format($productBoost->views_generated) }}</p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Vues générées</p>
                </div>
                <div class="rounded-xl bg-slate-50 p-4 text-center dark:bg-slate-900/50">
                    <i class="fas fa-mouse-pointer mb-2 text-slate-400"></i>
                    <p class="text-2xl font-bold tabular-nums text-slate-900 dark:text-white">{{ number_format($productBoost->clicks_generated) }}</p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Clics générés</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Colonne droite -->
    <div class="space-y-6 lg:col-span-2">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-300">
                    <i class="fas fa-box-open"></i>
                </div>
                <h4 class="font-semibold text-slate-900 dark:text-white">Article concerné</h4>
            </div>
            @if($productBoost->item)
            <div class="flex items-center gap-4">
                <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-slate-100 dark:bg-slate-700">
                    @if($productBoost->item->images)
                        <img src="{{ $productBoost->item->images[0] }}" alt="" class="h-full w-full object-cover">
                    @else
                        <i class="fas fa-image text-2xl text-slate-400"></i>
                    @endif
                </div>
                <div class="min-w-0">
                    <h5 class="truncate font-semibold text-slate-900 dark:text-white">{{ $productBoost->item->name }}</h5>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ Str::limit($productBoost->item->description ?? '', 120) }}</p>
                </div>
            </div>
            @else
            <p class="text-sm text-slate-500">Article non disponible.</p>
            @endif
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-300">
                    <i class="fas fa-user"></i>
                </div>
                <h4 class="font-semibold text-slate-900 dark:text-white">Vendeur</h4>
            </div>
            @if($productBoost->user)
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary-50 text-sm font-bold text-primary-600 dark:bg-primary-900/30 dark:text-primary-300">
                    {{ strtoupper(substr($productBoost->user->name, 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <h5 class="truncate font-semibold text-slate-900 dark:text-white">{{ $productBoost->user->name }}</h5>
                    <p class="truncate text-sm text-slate-500 dark:text-slate-400">{{ $productBoost->user->email }}</p>
                </div>
            </div>
            @else
            <p class="text-sm text-slate-500">Utilisateur non disponible.</p>
            @endif
        </div>

        @if($productBoost->cancellation_reason)
        <div class="rounded-xl border border-red-200 bg-red-50 p-6 shadow-sm dark:border-red-900/30 dark:bg-red-900/20">
            <div class="mb-3 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-300">
                    <i class="fas fa-circle-exclamation"></i>
                </div>
                <h4 class="font-semibold text-red-700 dark:text-red-300">Motif d'annulation</h4>
            </div>
            <p class="text-sm text-red-800/80 dark:text-red-200/80">{{ $productBoost->cancellation_reason }}</p>
        </div>
        @endif
    </div>
</div>

@if($productBoost->status === 'active')
<div id="cancelModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm">
    <div class="animate-pop w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-800">
        <div class="flex items-start justify-between">
            <div class="text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-red-100 dark:bg-red-900/40">
                    <i class="fas fa-triangle-exclamation text-2xl text-red-500"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Annuler ce boost ?</h3>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Un remboursement partiel sera calculé et crédité sur le portefeuille du vendeur.</p>
            </div>
            <button type="button" onclick="hideCancelModal()" class="text-slate-400 transition-colors hover:text-slate-600 dark:hover:text-slate-200">
                <i class="fas fa-xmark text-xl"></i>
            </button>
        </div>

        <form action="{{ route('admin.product-boosts.cancel', $productBoost) }}" method="POST" class="mt-5">
            @csrf
            <div class="mb-5">
                <label for="cancelReason" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Motif d'annulation</label>
                <textarea name="reason" id="cancelReason" rows="3" placeholder="Raison de l'annulation..."
                          class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white"></textarea>
            </div>
            <div class="flex flex-col-reverse gap-2 sm:flex-row">
                <button type="button" onclick="hideCancelModal()"
                        class="flex-1 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    Fermer
                </button>
                <button type="submit"
                        class="flex-1 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-red-700">
                    <i class="fas fa-ban mr-1.5"></i>Confirmer l'annulation
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function hideCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}
document.addEventListener('click', function (event) {
    if (event.target === document.getElementById('cancelModal')) hideCancelModal();
});
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') hideCancelModal();
});
</script>
@endif
@endsection