@extends('app')

@section('title', $boostType->name . ' - Détails du Boost')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('boost.index') }}">Boosts</a></li>
                    <li class="breadcrumb-item active">{{ $boostType->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- En-tête du boost -->
            <div class="card mb-4">
                <div class="card-header bg-gradient text-white d-flex justify-content-between align-items-center" 
                     style="background: {{ $boostType->color ?? '#007bff' }};">
                    <div class="d-flex align-items-center">
                        <i class="{{ $boostType->icon ?? 'fas fa-star' }} fa-2x me-3"></i>
                        <div>
                            <h1 class="h3 mb-1">{{ $boostType->display_name }}</h1>
                            @if($boostType->is_premium)
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-crown"></i> Premium
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="text-end">
                        @php
                            $userCurrency = auth()->user()->preferred_currency ?? 'CDF';
                            $price = $userCurrency === 'USD' ? $boostType->price_usd : $boostType->price_cdf;
                        @endphp
                        <div class="h4 mb-0">
                            @if($userCurrency === 'USD')
                                ${{ number_format($price, 2) }}
                            @else
                                {{ number_format($price, 0, ',', ' ') }} CDF
                            @endif
                        </div>
                        <small>à partir de</small>
                    </div>
                </div>
                <div class="card-body">
                    <p class="lead">{{ $boostType->description }}</p>
                    
                    @if($boostType->long_description)
                    <div class="mt-4">
                        <h5>Description détaillée</h5>
                        <p class="text-muted">{{ $boostType->long_description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Avantages -->
            @if($boostType->benefits)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Avantages de ce boost
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach(json_decode($boostType->benefits, true) as $index => $benefit)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                     style="width: 30px; height: 30px; min-width: 30px;">
                                    <i class="fas fa-check small"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $benefit }}</h6>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Comparaison des durées -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calculator text-primary me-2"></i>
                        Calcul du prix selon la durée
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Durée</th>
                                    <th>Prix de base</th>
                                    @if($boostType->price_per_day > 0)
                                    <th>Coût journalier</th>
                                    @endif
                                    <th>Prix total</th>
                                    <th>Recommandation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $availableDurations = json_decode($boostType->available_durations, true) ?? [24, 48, 72, 168];
                                    $userCurrency = auth()->user()->preferred_currency ?? 'CDF';
                                    $basePrice = $userCurrency === 'USD' ? $boostType->price_usd : $boostType->price_cdf;
                                    $currencySymbol = $userCurrency === 'USD' ? '$' : 'CDF';
                                @endphp
                                
                                @foreach($availableDurations as $hours)
                                @php
                                    $days = $hours < 24 ? $hours/24 : $hours/24;
                                    $displayText = $hours < 24 ? "$hours heure" . ($hours > 1 ? 's' : '') : floor($hours/24) . " jour" . (floor($hours/24) > 1 ? 's' : '');
                                    
                                    // Calcul progressif du prix (plus long = plus cher)
                                    $multiplier = 1;
                                    if($hours >= 168) { // 7 jours ou plus
                                        $multiplier = 1.5;
                                    } elseif($hours >= 72) { // 3 jours ou plus
                                        $multiplier = 1.2;
                                    } elseif($hours >= 48) { // 2 jours ou plus
                                        $multiplier = 1.1;
                                    }
                                    
                                    $totalPrice = $basePrice * $multiplier;
                                    
                                    $recommendation = '';
                                    if($hours <= 24) {
                                        $recommendation = '<span class="badge bg-info">Court terme</span>';
                                    } elseif($hours <= 72) {
                                        $recommendation = '<span class="badge bg-warning">Recommandé</span>';
                                    } elseif($hours <= 168) {
                                        $recommendation = '<span class="badge bg-success">Optimal</span>';
                                    } else {
                                        $recommendation = '<span class="badge bg-primary">Long terme</span>';
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $displayText }}</td>
                                    <td>
                                        @if($userCurrency === 'USD')
                                            ${{ number_format($basePrice, 2) }}
                                        @else
                                            {{ number_format($basePrice, 0, ',', ' ') }} CDF
                                        @endif
                                    </td>
                                    @if($boostType->price_per_day > 0)
                                    <td>{{ number_format($boostType->price_per_day * $days, 0, ',', ' ') }} {{ $currencySymbol }}</td>
                                    @endif
                                    <td class="fw-bold">
                                        @if($userCurrency === 'USD')
                                            ${{ number_format($totalPrice, 2) }}
                                        @else
                                            {{ number_format($totalPrice, 0, ',', ' ') }} CDF
                                        @endif
                                    </td>
                                    <td>{!! $recommendation !!}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Info :</strong> Les prix peuvent varier selon le produit et sa catégorie.
                    </div>
                </div>
            </div>

            <!-- FAQ -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-question-circle text-info me-2"></i>
                        Questions fréquentes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Comment fonctionne ce boost ?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Ce boost améliore la visibilité de votre produit en le plaçant dans des positions privilégiées 
                                    sur la plateforme. Plus votre produit est visible, plus il a de chances d'être vu et acheté par les clients.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Puis-je annuler mon boost ?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Oui, vous pouvez annuler votre boost à tout moment. Le remboursement dépend du temps écoulé :
                                    <ul class="mt-2">
                                        <li>Annulation dans les 24h : remboursement complet</li>
                                        <li>Annulation après 24h : remboursement partiel</li>
                                        <li>Annulation après 50% de la durée : aucun remboursement</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Quand commence l'activation du boost ?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Le boost s'active immédiatement après l'achat et la validation du paiement. 
                                    Vous recevrez une notification de confirmation et pourrez suivre l'évolution 
                                    de votre boost dans votre dashboard.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Action rapide -->
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-rocket me-2"></i>
                        Booster maintenant
                    </h5>
                </div>
                <div class="card-body text-center">
                    <p class="card-text">Prêt à booster un de vos produits avec <strong>{{ $boostType->name }}</strong> ?</p>
                    <button class="btn btn-primary btn-lg w-100 select-boost-btn" 
                            data-boost-type-id="{{ $boostType->id }}">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Choisir ce boost
                    </button>
                </div>
            </div>

            <!-- Statistiques du boost type -->
            @if(isset($stats))
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar text-success me-2"></i>
                        Statistiques {{ $boostType->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border-end">
                                <h4 class="text-primary mb-1">{{ $stats['total_users'] ?? 0 }}</h4>
                                <small class="text-muted">Utilisateurs</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <h4 class="text-success mb-1">{{ $stats['total_purchases'] ?? 0 }}</h4>
                            <small class="text-muted">Achats</small>
                        </div>
                    </div>
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-warning mb-1">{{ $stats['avg_duration'] ?? 0 }}</h4>
                            <small class="text-muted">Durée moy.</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-info mb-1">{{ $stats['satisfaction'] ?? 0 }}%</h4>
                            <small class="text-muted">Satisfaction</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Témoignages -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-star text-warning me-2"></i>
                        Témoignages
                    </h5>
                </div>
                <div class="card-body">
                    <div class="testimonial mb-3">
                        <blockquote class="blockquote small">
                            <p>"Ce boost a vraiment augmenté la visibilité de mes produits. Je recommande !"</p>
                        </blockquote>
                        <figcaption class="blockquote-footer small">
                            <cite title="Vendeur vérifié">Marie D.</cite> - Vendeur vérifié
                        </figcaption>
                    </div>
                    
                    <div class="testimonial">
                        <blockquote class="blockquote small">
                            <p>"Résultats visibles dès le premier jour. Excellent rapport qualité-prix."</p>
                        </blockquote>
                        <figcaption class="blockquote-footer small">
                            <cite title="Vendeur premium">Jean K.</cite> - Vendeur premium
                        </figcaption>
                    </div>
                </div>
            </div>

            <!-- Support -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-headset text-info me-2"></i>
                        Besoin d'aide ?
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text small">Notre équipe est là pour vous accompagner dans l'utilisation des boosts.</p>
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-comments me-1"></i>Chat en direct
                        </a>
                        <a href="#" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-envelope me-1"></i>Envoyer un email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de sélection de produit (même que dans index.blade.php) -->
<div class="modal fade" id="productSelectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-box me-2"></i>Choisissez le produit à booster
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Le contenu sera identique à celui d'index.blade.php -->
                <div id="productsList">
                    <div class="text-center py-3">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.bg-gradient {
    background: linear-gradient(135deg, var(--bs-primary), var(--bs-primary-dark, #0056b3)) !important;
}

.testimonial {
    border-left: 3px solid var(--bs-warning);
    padding-left: 1rem;
}

.accordion-button:not(.collapsed) {
    background-color: var(--bs-primary-subtle, rgba(13, 110, 253, 0.1));
}

.table tbody tr:hover {
    background-color: var(--bs-light, #f8f9fa);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Réutiliser le même JavaScript que dans index.blade.php pour la sélection de produit
    document.querySelector('.select-boost-btn').addEventListener('click', function() {
        const boostTypeId = this.dataset.boostTypeId;
        // Logique de sélection de produit identique à index.blade.php
        const modal = new bootstrap.Modal(document.getElementById('productSelectionModal'));
        modal.show();
    });
});
</script>
@endpush