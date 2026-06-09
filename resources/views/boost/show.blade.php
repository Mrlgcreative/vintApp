@extends('app')

@section('title', $boostType->name . ' - Détails du Boost')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
        <a href="{{ route('boost.index') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Boosts</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-gray-900 dark:text-white font-medium">{{ $boostType->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-6 text-white" style="background: {{ $boostType->color ?? '#3B82F6' }};">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-4">
                            <i class="{{ $boostType->icon ?? 'fas fa-star' }} text-3xl"></i>
                            <div>
                                <h1 class="text-2xl font-bold">{{ $boostType->display_name }}</h1>
                                @if($boostType->is_premium)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-400 text-yellow-900 mt-1">
                                    <i class="fas fa-crown"></i> Premium
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            @php
                                $userCurrency = auth()->user()->preferred_currency ?? 'CDF';
                                $price = $userCurrency === 'USD' ? $boostType->price_usd : $boostType->price_cdf;
                            @endphp
                            <div class="text-2xl font-bold">
                                @if($userCurrency === 'USD')
                                    ${{ number_format($price, 2) }}
                                @else
                                    {{ number_format($price, 0, ',', ' ') }} CDF
                                @endif
                            </div>
                            <div class="text-white/70 text-sm">à partir de</div>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 dark:text-gray-300 text-lg">{{ $boostType->description }}</p>
                    @if($boostType->long_description)
                    <div class="mt-6">
                        <h5 class="font-semibold text-gray-900 dark:text-white mb-2">Description détaillée</h5>
                        <p class="text-gray-500 dark:text-gray-400">{{ $boostType->long_description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            @if($boostType->benefits)
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h5 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-500"></i>
                        Avantages de ce boost
                    </h5>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach(json_decode($boostType->benefits, true) as $benefit)
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check text-emerald-600 dark:text-emerald-400 text-xs"></i>
                        </div>
                        <span class="text-gray-700 dark:text-gray-300">{{ $benefit }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h5 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-calculator text-primary"></i>
                        Calcul du prix selon la durée
                    </h5>
                </div>
                <div class="p-6">
                    @php
                        $availableDurations = json_decode($boostType->available_durations, true) ?? [24, 48, 72, 168];
                        $basePrice = $userCurrency === 'USD' ? $boostType->price_usd : $boostType->price_cdf;
                        $currencySymbol = $userCurrency === 'USD' ? '$' : 'CDF';
                    @endphp
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-700">
                                    <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Durée</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Prix de base</th>
                                    @if($boostType->price_per_day > 0)
                                    <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Coût journalier</th>
                                    @endif
                                    <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Prix total</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Recommandation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($availableDurations as $hours)
                                @php
                                    $days = $hours/24;
                                    $displayText = $hours < 24 ? "$hours heure" . ($hours > 1 ? 's' : '') : floor($days) . " jour" . (floor($days) > 1 ? 's' : '');
                                    $multiplier = 1;
                                    if($hours >= 168) $multiplier = 1.5;
                                    elseif($hours >= 72) $multiplier = 1.2;
                                    elseif($hours >= 48) $multiplier = 1.1;
                                    $totalPrice = $basePrice * $multiplier;
                                    $recommendation = '';
                                    $badgeClass = 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300';
                                    if($hours <= 24) { $recommendation = 'Court terme'; $badgeClass = 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'; }
                                    elseif($hours <= 72) { $recommendation = 'Recommandé'; $badgeClass = 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'; }
                                    elseif($hours <= 168) { $recommendation = 'Optimal'; $badgeClass = 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'; }
                                    else { $recommendation = 'Long terme'; $badgeClass = 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400'; }
                                @endphp
                                <tr class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="py-3 px-4 font-medium text-gray-900 dark:text-white">{{ $displayText }}</td>
                                    <td class="py-3 px-4 text-gray-700 dark:text-gray-300">
                                        @if($userCurrency === 'USD')
                                            ${{ number_format($basePrice, 2) }}
                                        @else
                                            {{ number_format($basePrice, 0, ',', ' ') }} CDF
                                        @endif
                                    </td>
                                    @if($boostType->price_per_day > 0)
                                    <td class="py-3 px-4 text-gray-700 dark:text-gray-300">{{ number_format($boostType->price_per_day * $days, 0, ',', ' ') }} {{ $currencySymbol }}</td>
                                    @endif
                                    <td class="py-3 px-4 font-bold text-gray-900 dark:text-white">
                                        @if($userCurrency === 'USD')
                                            ${{ number_format($totalPrice, 2) }}
                                        @else
                                            {{ number_format($totalPrice, 0, ',', ' ') }} CDF
                                        @endif
                                    </td>
                                    <td class="py-3 px-4"><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">{{ $recommendation }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 p-4 bg-primary-50 dark:bg-primary-900/10 border border-primary-100 dark:border-primary-800 rounded-xl text-sm text-primary-700 dark:text-primary-300 flex items-start gap-2">
                        <i class="fas fa-info-circle mt-0.5"></i>
                        <span><strong>Info :</strong> Les prix peuvent varier selon le produit et sa catégorie.</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h5 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-question-circle text-primary"></i>
                        Questions fréquentes
                    </h5>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    <details class="group" open>
                        <summary class="flex items-center justify-between p-4 cursor-pointer list-none text-sm font-medium text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            Comment fonctionne ce boost ?
                            <i class="fas fa-chevron-down text-gray-400 group-open:rotate-180 transition-transform"></i>
                        </summary>
                        <div class="px-4 pb-4 text-sm text-gray-500 dark:text-gray-400">
                            Ce boost améliore la visibilité de votre produit en le plaçant dans des positions privilégiées sur la plateforme. Plus votre produit est visible, plus il a de chances d'être vu et acheté par les clients.
                        </div>
                    </details>
                    <details class="group">
                        <summary class="flex items-center justify-between p-4 cursor-pointer list-none text-sm font-medium text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            Puis-je annuler mon boost ?
                            <i class="fas fa-chevron-down text-gray-400 group-open:rotate-180 transition-transform"></i>
                        </summary>
                        <div class="px-4 pb-4 text-sm text-gray-500 dark:text-gray-400">
                            Oui, vous pouvez annuler votre boost à tout moment. Le remboursement dépend du temps écoulé :
                            <ul class="mt-2 space-y-1 list-disc list-inside">
                                <li>Annulation dans les 24h : remboursement complet</li>
                                <li>Annulation après 24h : remboursement partiel</li>
                                <li>Annulation après 50% de la durée : aucun remboursement</li>
                            </ul>
                        </div>
                    </details>
                    <details class="group">
                        <summary class="flex items-center justify-between p-4 cursor-pointer list-none text-sm font-medium text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            Quand commence l'activation du boost ?
                            <i class="fas fa-chevron-down text-gray-400 group-open:rotate-180 transition-transform"></i>
                        </summary>
                        <div class="px-4 pb-4 text-sm text-gray-500 dark:text-gray-400">
                            Le boost s'active immédiatement après l'achat et la validation du paiement. Vous recevrez une notification de confirmation et pourrez suivre l'évolution de votre boost dans votre dashboard.
                        </div>
                    </details>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border-2 border-primary/20 dark:border-primary/30 overflow-hidden sticky top-24">
                <div class="p-5 bg-gradient-to-r from-primary to-primary-600 text-white">
                    <h5 class="font-semibold flex items-center gap-2">
                        <i class="fas fa-rocket"></i>
                        Booster maintenant
                    </h5>
                </div>
                <div class="p-6 text-center">
                    <p class="text-gray-500 dark:text-gray-400 mb-6">Prêt à booster un de vos produits avec <strong class="text-gray-900 dark:text-white">{{ $boostType->name }}</strong> ?</p>
                    <button class="w-full bg-primary hover:bg-primary-600 text-white font-medium py-3 px-5 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 select-boost-btn" data-boost-type-id="{{ $boostType->id }}">
                        <i class="fas fa-shopping-cart"></i>
                        Choisir ce boost
                    </button>
                </div>
            </div>

            @if(isset($stats))
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h5 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-chart-bar text-emerald-500"></i>
                        Statistiques {{ $boostType->name }}
                    </h5>
                </div>
                <div class="p-5 grid grid-cols-2 gap-4 text-center">
                    <div class="border-r border-gray-100 dark:border-gray-700">
                        <p class="text-2xl font-bold text-primary">{{ $stats['total_users'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Utilisateurs</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['total_purchases'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Achats</p>
                    </div>
                    <div class="border-r border-gray-100 dark:border-gray-700 border-t border-gray-100 dark:border-gray-700 pt-4">
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['avg_duration'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Durée moy.</p>
                    </div>
                    <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                        <p class="text-2xl font-bold text-primary">{{ $stats['satisfaction'] ?? 0 }}%</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Satisfaction</p>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h5 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-star text-yellow-500"></i>
                        Témoignages
                    </h5>
                </div>
                <div class="p-5 space-y-4">
                    <div class="border-l-4 border-yellow-500 pl-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400 italic">"Ce boost a vraiment augmenté la visibilité de mes produits. Je recommande !"</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">— <cite class="not-italic font-medium text-gray-600 dark:text-gray-300">Marie D.</cite>, Vendeur vérifié</p>
                    </div>
                    <div class="border-l-4 border-yellow-500 pl-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400 italic">"Résultats visibles dès le premier jour. Excellent rapport qualité-prix."</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">— <cite class="not-italic font-medium text-gray-600 dark:text-gray-300">Jean K.</cite>, Vendeur premium</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h5 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-headset text-primary"></i>
                        Besoin d'aide ?
                    </h5>
                </div>
                <div class="p-5 space-y-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Notre équipe est là pour vous accompagner.</p>
                    <a href="#" class="flex items-center justify-center gap-2 w-full px-4 py-2.5 border border-primary text-primary rounded-xl font-medium hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors text-sm">
                        <i class="fas fa-comments"></i>Chat en direct
                    </a>
                    <a href="#" class="flex items-center justify-center gap-2 w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors text-sm">
                        <i class="fas fa-envelope"></i>Envoyer un email
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="productSelectionModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity modal-overlay"></div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full mx-auto shadow-2xl transform transition-all modal-content border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-box text-primary"></i>Choisissez le produit à booster
                </h3>
                <button type="button" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors close-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <div id="productsList">
                    <div class="text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-primary border-t-transparent"></div>
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
.modal-overlay { transition: opacity 0.3s ease-in-out; }
.modal-content { transition: all 0.3s ease-in-out; transform: scale(0.95); opacity: 0; }
.modal-content.show { transform: scale(1); opacity: 1; }
details > summary::-webkit-details-marker { display: none; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function showModal() {
        const modal = document.getElementById('productSelectionModal');
        const modalContent = modal.querySelector('.modal-content');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => modalContent.classList.add('show'), 50);
    }
    function hideModal() {
        const modal = document.getElementById('productSelectionModal');
        const modalContent = modal.querySelector('.modal-content');
        modalContent.classList.remove('show');
        document.body.style.overflow = '';
        setTimeout(() => modal.classList.add('hidden'), 300);
    }
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-overlay') || e.target.classList.contains('close-modal')) {
            hideModal();
        }
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') hideModal();
    });
    document.querySelector('.select-boost-btn')?.addEventListener('click', function() {
        const boostTypeId = this.dataset.boostTypeId;
        const productsList = document.getElementById('productsList');
        productsList.innerHTML = `
            <div class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-primary border-t-transparent"></div>
                <p class="text-gray-500 dark:text-gray-400 mt-2">Chargement...</p>
            </div>
        `;
        fetch('{{ route("boost.user-items") }}', {
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.items.length > 0) {
                let html = '<h6 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Sélectionnez un produit</h6><div class="space-y-2">';
                data.items.forEach(item => {
                    const img = item.images?.[0]?.image_url || 'https://via.placeholder.com/60';
                    html += `
                        <a href="{{ url('boost') }}?item_id=${item.id}" class="flex items-center gap-3 p-4 rounded-xl border-2 border-transparent hover:border-primary-500 dark:hover:border-primary-500 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all cursor-pointer">
                            <img src="${img}" class="w-14 h-14 rounded-lg object-cover flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <h6 class="font-medium text-gray-900 dark:text-white truncate">${item.title}</h6>
                                <p class="text-sm text-gray-500 dark:text-gray-400">${new Intl.NumberFormat('fr-FR').format(item.price)} CDF</p>
                            </div>
                        </a>
                    `;
                });
                html += '</div>';
                productsList.innerHTML = html;
            } else {
                productsList.innerHTML = '<div class="text-center py-12"><i class="fas fa-box-open text-5xl text-gray-300 dark:text-gray-600 mb-4"></i><p class="text-gray-500 dark:text-gray-400">Aucun produit disponible</p></div>';
            }
        });
        showModal();
    });
});
</script>
@endpush