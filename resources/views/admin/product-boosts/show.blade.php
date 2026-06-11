@extends('layouts.admin')

@section('title', 'Détails du boost')
@section('page-title', 'Détails du boost #' . $productBoost->id)

@section('page-actions')
<div class="flex flex-wrap gap-3">
    @if($productBoost->status === 'active')
    <button type="button" onclick="showCancelModal()"
            class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
        <i class="fas fa-ban mr-2"></i>Annuler le boost
    </button>
    @endif
    <a href="{{ route('admin.product-boosts.index') }}"
       class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 dark:bg-gray-900 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>Retour
    </a>
</div>
@endsection

@section('content')
@if(session('success'))
    <div class="flex items-center rounded-xl bg-green-50 p-4 text-green-800 animate-fade-in mb-6">
        <i class="fas fa-check-circle mr-3 text-green-500"></i>
        <span class="flex-1">{{ session('success') }}</span>
        <button type="button" class="ml-4 text-green-500 hover:text-green-700" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
@endif
@if(session('error'))
    <div class="flex items-center rounded-xl bg-red-50 p-4 text-red-800 animate-fade-in mb-6">
        <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
        <span class="flex-1">{{ session('error') }}</span>
        <button type="button" class="ml-4 text-red-500 hover:text-red-700" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Informations générales</h4>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-500">Statut</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $productBoost->status === 'active' ? 'bg-green-100 text-green-800 border border-green-200' : '' }}
                        {{ $productBoost->status === 'expired' ? 'bg-gray-100 text-gray-800 border border-gray-200' : '' }}
                        {{ $productBoost->status === 'cancelled' ? 'bg-red-100 text-red-800 border border-red-200' : '' }}">
                        <span class="w-1.5 h-1.5 rounded-full mr-1.5
                            {{ $productBoost->status === 'active' ? 'bg-green-500' : '' }}
                            {{ $productBoost->status === 'expired' ? 'bg-gray-500' : '' }}
                            {{ $productBoost->status === 'cancelled' ? 'bg-red-500' : '' }}">
                        </span>
                        {{ ucfirst($productBoost->status) }}
                    </span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-500">Type de boost</span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium"
                          style="background: {{ $productBoost->boostType?->color ?? '#3B82F6' }}20; color: {{ $productBoost->boostType?->color ?? '#3B82F6' }}">
                        <i class="{{ $productBoost->boostType?->icon ?? 'fas fa-bolt' }} text-xs"></i>
                        {{ $productBoost->boostType?->display_name ?? 'N/A' }}
                    </span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-500">Durée</span>
                    <span class="font-semibold">{{ $productBoost->duration }} jours</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-500">Prix total</span>
                    <span class="font-semibold">${{ number_format($productBoost->total_price, 2) }}</span>
                </div>
                @if($productBoost->refund_amount)
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-500">Remboursement</span>
                    <span class="font-semibold text-red-600">{{ number_format($productBoost->refund_amount, 2) }} CDF</span>
                </div>
                @endif
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-500">Date de début</span>
                    <span class="font-semibold">{{ $productBoost->started_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-500">Date de fin</span>
                    <span class="font-semibold">{{ $productBoost->expires_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                </div>
                @if($productBoost->cancelled_at)
                <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-500">Annulé le</span>
                    <span class="font-semibold text-red-600">{{ $productBoost->cancelled_at->format('d/m/Y H:i') }}</span>
                </div>
                @endif
                <div class="flex justify-between py-2">
                    <span class="text-gray-500">Créé le</span>
                    <span class="font-semibold">{{ $productBoost->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Performance</h4>
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($productBoost->views_generated) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Vues générées</p>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($productBoost->clicks_generated) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Clics générés</p>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Article concerné</h4>
            @if($productBoost->item)
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                    @if($productBoost->item->images)
                        <img src="{{ $productBoost->item->images[0] ?? '' }}" alt="" class="w-full h-full object-cover">
                    @else
                        <i class="fas fa-image text-2xl text-gray-400"></i>
                    @endif
                </div>
                <div>
                    <h5 class="font-semibold text-gray-900 dark:text-white">{{ $productBoost->item->name }}</h5>
                    <p class="text-sm text-gray-500">{{ Str::limit($productBoost->item->description ?? '', 100) }}</p>
                </div>
            </div>
            @else
            <p class="text-gray-500">Article non disponible.</p>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Vendeur</h4>
            @if($productBoost->user)
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-primary-600 font-bold text-lg">{{ strtoupper(substr($productBoost->user->name, 0, 2)) }}</span>
                </div>
                <div>
                    <h5 class="font-semibold text-gray-900 dark:text-white">{{ $productBoost->user->name }}</h5>
                    <p class="text-sm text-gray-500">{{ $productBoost->user->email }}</p>
                </div>
            </div>
            @else
            <p class="text-gray-500">Utilisateur non disponible.</p>
            @endif
        </div>

        @if($productBoost->cancellation_reason)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h4 class="font-semibold text-red-600 mb-2">Motif d'annulation</h4>
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $productBoost->cancellation_reason }}</p>
        </div>
        @endif
    </div>
</div>

@if($productBoost->status === 'active')
<div id="cancelModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="flex items-center justify-center min-h-full p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-6 relative">
            <button onclick="hideCancelModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-500"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Annuler ce boost ?</h3>
                <p class="text-sm text-gray-500 mt-2">Un remboursement partiel sera calculé et crédité sur le portefeuille du vendeur.</p>
            </div>
            <form action="{{ route('admin.product-boosts.cancel', $productBoost) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Motif d'annulation</label>
                    <textarea name="reason" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                              placeholder="Raison de l'annulation..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="hideCancelModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        Fermer
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                        Confirmer l'annulation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
}
function hideCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
}
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') hideCancelModal();
});
</script>
@endif
@endsection
