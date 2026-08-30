@extends('app')

@section('title', 'Dashboard Boost')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
        <div class="mb-4 lg:mb-0">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Dashboard Boost</h1>
            <p class="text-gray-500 dark:text-gray-400">Gérez vos boosts et consultez vos statistiques</p>
        </div>
        <a href="{{ route('boost.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-600 text-white font-medium rounded-xl transition-all duration-200">
            <i class="fas fa-plus"></i>Nouveau Boost
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-primary-500 to-primary-700 text-white rounded-2xl p-6 text-center transform hover:-translate-y-1 transition-all duration-200 shadow-lg shadow-primary-500/20">
            <div class="flex justify-center mb-3">
                <i class="fas fa-rocket text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold mb-1">{{ $stats['active_boosts'] }}</h3>
            <p class="text-primary-100">Boosts Actifs</p>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 text-white rounded-2xl p-6 text-center transform hover:-translate-y-1 transition-all duration-200 shadow-lg shadow-emerald-500/20">
            <div class="flex justify-center mb-3">
                <i class="fas fa-chart-line text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold mb-1">{{ number_format($stats['total_spent'], 0, ',', ' ') }} {{ auth()->user()->preferred_currency ?? 'CDF' }}</h3>
            <p class="text-emerald-100">Total Dépensé</p>
        </div>
        <div class="bg-gradient-to-br from-cyan-500 to-cyan-700 text-white rounded-2xl p-6 text-center transform hover:-translate-y-1 transition-all duration-200 shadow-lg shadow-cyan-500/20">
            <div class="flex justify-center mb-3">
                <i class="fas fa-eye text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold mb-1">{{ $stats['total_views'] }}</h3>
            <p class="text-cyan-100">Vues Générées</p>
        </div>
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 text-white rounded-2xl p-6 text-center transform hover:-translate-y-1 transition-all duration-200 shadow-lg shadow-yellow-500/20">
            <div class="flex justify-center mb-3">
                <i class="fas fa-mouse-pointer text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold mb-1">{{ $stats['total_clicks'] }}</h3>
            <p class="text-yellow-100">Clics Générés</p>
        </div>
    </div>

    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
        <nav class="-mb-px flex space-x-6" aria-label="Tabs">
            <button class="tab-button active border-b-2 border-primary py-3 px-1 text-primary font-medium text-sm whitespace-nowrap transition-colors" data-target="#active" type="button" role="tab">
                <i class="fas fa-play-circle mr-2"></i>Boosts Actifs ({{ $activeBoosts->count() }})
            </button>
            <button class="tab-button border-b-2 border-transparent py-3 px-1 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 font-medium text-sm whitespace-nowrap transition-colors" data-target="#expired" type="button" role="tab">
                <i class="fas fa-clock mr-2"></i>Boosts Expirés ({{ $expiredBoosts->count() }})
            </button>
            <button class="tab-button border-b-2 border-transparent py-3 px-1 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 font-medium text-sm whitespace-nowrap transition-colors" data-target="#cancelled" type="button" role="tab">
                <i class="fas fa-times-circle mr-2"></i>Boosts Annulés ({{ $cancelledBoosts->count() }})
            </button>
        </nav>
    </div>

    <div class="tab-content">
        <div class="tab-pane active" id="active" role="tabpanel">
            @forelse($activeBoosts as $boost)
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 mb-4 p-6 hover:shadow-md transition-all duration-200 border-l-4 border-l-primary">
                <div class="flex flex-col lg:flex-row lg:items-center gap-6">
                    <div class="flex-shrink-0">
                        @if($boost->item->images && count($boost->item->images) > 0)
                        <img src="{{ Storage::url($boost->item->images[0]) }}" alt="{{ $boost->item->name }}"
                             class="w-20 h-20 object-cover rounded-xl">
                        @else
                        <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 flex items-center justify-center rounded-xl">
                            <i class="fas fa-image text-2xl text-gray-400"></i>
                        </div>
                        @endif
                    </div>

                    <div class="flex-grow">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">{{ $boost->item->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ $boost->item->category->name ?? 'N/A' }}</p>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium text-white" style="background-color: {{ $boost->boostType->color ?? '#3B82F6' }};">
                            <i class="{{ $boost->boostType->icon ?? 'fas fa-star' }}"></i>
                            {{ $boost->boostType->name }}
                        </span>
                    </div>

                    <div class="text-center">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">Progression</div>
                        @php $progress = $boost->getProgressPercentage(); @endphp
                        <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-1 mx-auto">
                            <div class="bg-emerald-500 h-2 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                        </div>
                        <span class="text-xs text-gray-600 dark:text-gray-400">{{ round($progress) }}%</span>
                    </div>

                    <div class="text-center">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Temps restant</div>
                        <div class="font-semibold text-gray-900 dark:text-white text-sm">{{ $boost->getRemainingTimeForHumans() }}</div>
                    </div>

                    <div class="text-center">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">Statistiques</div>
                        <div class="flex justify-center gap-4 text-sm mb-3">
                            <span class="flex items-center gap-1 text-cyan-600 dark:text-cyan-400">
                                <i class="fas fa-eye"></i>{{ $boost->views_generated ?? 0 }}
                            </span>
                            <span class="flex items-center gap-1 text-yellow-600 dark:text-yellow-400">
                                <i class="fas fa-mouse-pointer"></i>{{ $boost->clicks_generated ?? 0 }}
                            </span>
                        </div>
                        <button class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-red-300 dark:border-red-700 text-sm font-medium rounded-lg text-red-700 dark:text-red-400 bg-white dark:bg-gray-800 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors cancel-boost-btn"
                                data-boost-id="{{ $boost->id }}"
                                data-boost-title="{{ $boost->item->name }}">
                            <i class="fas fa-stop"></i>Annuler
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="flex justify-center mb-4">
                    <i class="fas fa-rocket text-6xl text-gray-200 dark:text-gray-700"></i>
                </div>
                <h4 class="text-xl font-medium text-gray-500 dark:text-gray-400 mb-2">Aucun boost actif</h4>
                <p class="text-gray-400 dark:text-gray-500 mb-6">Commencez par booster un de vos produits.</p>
                <a href="{{ route('boost.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-600 text-white font-medium rounded-xl transition-colors">
                    <i class="fas fa-plus"></i>Créer un boost
                </a>
            </div>
            @endforelse
        </div>

        <div class="tab-pane hidden" id="expired" role="tabpanel">
            @forelse($expiredBoosts as $boost)
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 mb-4 p-6 opacity-75 border-l-4 border-l-gray-400 dark:border-l-gray-600">
                <div class="flex flex-col lg:flex-row lg:items-center gap-6">
                    <div class="flex-shrink-0">
                        @if($boost->item->images && count($boost->item->images) > 0)
                        <img src="{{ Storage::url($boost->item->images[0]) }}" alt="{{ $boost->item->name }}" class="w-20 h-20 object-cover rounded-xl">
                        @else
                        <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 flex items-center justify-center rounded-xl">
                            <i class="fas fa-image text-2xl text-gray-400"></i>
                        </div>
                        @endif
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">{{ $boost->item->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ $boost->item->category->name ?? 'N/A' }}</p>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-500 text-white">
                            <i class="{{ $boost->boostType->icon ?? 'fas fa-star' }}"></i>
                            {{ $boost->boostType->name }}
                        </span>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Durée</div>
                        <div class="font-semibold text-gray-900 dark:text-white text-sm">{{ $boost->duration }} jour{{ $boost->duration > 1 ? 's' : '' }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Coût total</div>
                        <div class="font-semibold text-gray-900 dark:text-white text-sm">{{ number_format($boost->total_price, 0, ',', ' ') }} {{ $boost->currency ?? 'CDF' }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">Résultats</div>
                        <div class="flex justify-center gap-4 text-sm mb-1">
                            <span class="flex items-center gap-1 text-cyan-600 dark:text-cyan-400"><i class="fas fa-eye"></i>{{ $boost->views_generated ?? 0 }}</span>
                            <span class="flex items-center gap-1 text-yellow-600 dark:text-yellow-400"><i class="fas fa-mouse-pointer"></i>{{ $boost->clicks_generated ?? 0 }}</span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Expiré le {{ $boost->expires_at->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="flex justify-center mb-4">
                    <i class="fas fa-clock text-6xl text-gray-200 dark:text-gray-700"></i>
                </div>
                <h4 class="text-xl font-medium text-gray-500 dark:text-gray-400 mb-2">Aucun boost expiré</h4>
                <p class="text-gray-400 dark:text-gray-500">Les boosts expirés apparaîtront ici.</p>
            </div>
            @endforelse
        </div>

        <div class="tab-pane hidden" id="cancelled" role="tabpanel">
            @forelse($cancelledBoosts as $boost)
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 mb-4 p-6 opacity-75 border-l-4 border-l-red-500">
                <div class="flex flex-col lg:flex-row lg:items-center gap-6">
                    <div class="flex-shrink-0">
                        @if($boost->item->images && count($boost->item->images) > 0)
                        <img src="{{ Storage::url($boost->item->images[0]) }}" alt="{{ $boost->item->name }}" class="w-20 h-20 object-cover rounded-xl">
                        @else
                        <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 flex items-center justify-center rounded-xl">
                            <i class="fas fa-image text-2xl text-gray-400"></i>
                        </div>
                        @endif
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">{{ $boost->item->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ $boost->item->category->name ?? 'N/A' }}</p>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500 text-white">
                            <i class="fas fa-times"></i>Annulé
                        </span>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Durée prévue</div>
                        <div class="font-semibold text-gray-900 dark:text-white text-sm">{{ $boost->duration }} jour{{ $boost->duration > 1 ? 's' : '' }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Remboursé</div>
                        <div class="font-semibold text-emerald-600 dark:text-emerald-400 text-sm">{{ number_format($boost->refund_amount ?? 0, 0, ',', ' ') }} {{ $boost->currency ?? 'CDF' }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">Résultats</div>
                        <div class="flex justify-center gap-4 text-sm mb-1">
                            <span class="flex items-center gap-1 text-cyan-600 dark:text-cyan-400"><i class="fas fa-eye"></i>{{ $boost->views_generated ?? 0 }}</span>
                            <span class="flex items-center gap-1 text-yellow-600 dark:text-yellow-400"><i class="fas fa-mouse-pointer"></i>{{ $boost->clicks_generated ?? 0 }}</span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Annulé le {{ $boost->cancelled_at?->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="flex justify-center mb-4">
                    <i class="fas fa-times-circle text-6xl text-gray-200 dark:text-gray-700"></i>
                </div>
                <h4 class="text-xl font-medium text-gray-500 dark:text-gray-400 mb-2">Aucun boost annulé</h4>
                <p class="text-gray-400 dark:text-gray-500">Les boosts annulés apparaîtront ici.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<div class="fixed inset-0 z-50 hidden" id="cancelBoostModal" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeCancelModal()"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Annuler le boost</h3>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" onclick="closeCancelModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <p class="text-gray-700 dark:text-gray-300 mb-4">
                    Êtes-vous sûr de vouloir annuler le boost pour <strong class="text-gray-900 dark:text-white" id="boostItemTitle"></strong> ?
                </p>
                <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                        <div>
                            <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-2">Politique de remboursement :</h4>
                            <ul class="text-sm text-blue-800 dark:text-blue-300 space-y-1 list-disc list-inside">
                                <li>Annulation dans les 24h : remboursement complet</li>
                                <li>Annulation après 24h : remboursement partiel</li>
                                <li>Annulation après 50% de la durée : aucun remboursement</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-b-2xl">
                <button type="button" class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" onclick="closeCancelModal()">
                    Non, garder le boost
                </button>
                <button type="button" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-red-600 border border-transparent rounded-xl hover:bg-red-700 transition-colors" id="confirmCancelBoost">
                    <i class="fas fa-stop"></i>Oui, annuler
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedBoostId = null;
    const cancelBoostUrlTemplate = '{{ url("boost/cancel") }}';

    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            const target = this.dataset.target;
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active', 'border-primary', 'text-primary');
                btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300', 'hover:border-gray-300', 'dark:hover:border-gray-600');
            });
            this.classList.add('active', 'border-primary', 'text-primary');
            this.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300', 'hover:border-gray-300', 'dark:hover:border-gray-600');
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.add('hidden');
                pane.classList.remove('active');
            });
            const targetPane = document.querySelector(target);
            if (targetPane) {
                targetPane.classList.remove('hidden');
                targetPane.classList.add('active');
            }
        });
    });

    window.showCancelModal = function() { document.getElementById('cancelBoostModal').classList.remove('hidden'); };
    window.closeCancelModal = function() { document.getElementById('cancelBoostModal').classList.add('hidden'); };

    document.querySelectorAll('.cancel-boost-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            selectedBoostId = this.dataset.boostId;
            document.getElementById('boostItemTitle').textContent = this.dataset.boostTitle;
            showCancelModal();
        });
    });

    document.getElementById('confirmCancelBoost').addEventListener('click', function() {
        if (!selectedBoostId) return;
        const btn = this;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Annulation...';
        btn.disabled = true;

        fetch(`${cancelBoostUrlTemplate}/${selectedBoostId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 z-50 bg-emerald-500 text-white px-6 py-3 rounded-xl shadow-lg';
                toast.textContent = 'Boost annulé avec succès !';
                document.body.appendChild(toast);
                setTimeout(() => { toast.remove(); window.location.reload(); }, 1500);
            } else {
                alert(data.message || 'Erreur lors de l\'annulation');
            }
        })
        .catch(() => alert('Erreur lors de l\'annulation'))
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            closeCancelModal();
        });
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') closeCancelModal();
    });
});
</script>
@endpush