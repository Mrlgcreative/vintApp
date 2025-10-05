@extends('app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-percentage me-2"></i>
                    Demandes de réduction
                </h2>
                <div class="badge bg-info fs-6">
                    {{ $discounts->total() }} demande(s)
                </div>
            </div>

            <!-- Filtres -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Statut</label>
                            <select name="status" class="form-select">
                                <option value="">Tous les statuts</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approuvée</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Refusée</option>
                                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expirée</option>
                                <option value="used" {{ request('status') === 'used' ? 'selected' : '' }}>Utilisée</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Période</label>
                            <select name="period" class="form-select">
                                <option value="">Toutes les périodes</option>
                                <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                                <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Cette semaine</option>
                                <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Ce mois</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Rechercher un produit</label>
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nom du produit...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>
                                Filtrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des demandes -->
            @if($discounts->count() > 0)
                <div class="row">
                    @foreach($discounts as $discount)
                        <div class="col-12 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <!-- Image du produit -->
                                        <div class="col-md-2">
                                            @if($discount->item->images && count($discount->item->images) > 0)
                                                <img src="{{ Storage::url($discount->item->images[0]) }}" 
                                                     class="img-fluid rounded" 
                                                     alt="{{ $discount->item->name }}"
                                                     style="height: 80px; object-fit: cover; width: 100%;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                                     style="height: 80px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Informations -->
                                        <div class="col-md-4">
                                            <h6 class="fw-bold mb-1">{{ $discount->item->name }}</h6>
                                            <p class="text-muted small mb-1">{{ Str::limit($discount->item->description, 80) }}</p>
                                            <div class="small">
                                                <strong>Prix original:</strong> {{ number_format($discount->original_price, 0, ',', ' ') }} FCFA
                                            </div>
                                        </div>
                                        
                                        <!-- Demandeur -->
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                <i class="fas fa-user-circle fa-2x text-muted mb-1"></i>
                                                <div class="small fw-bold">{{ $discount->user->name }}</div>
                                                <div class="text-muted" style="font-size: 0.75rem;">
                                                    {{ $discount->created_at->format('d/m/Y') }}
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Réduction proposée -->
                                        <div class="col-md-2">
                                            @if($discount->status === 'approved')
                                                <div class="text-center">
                                                    <div class="fw-bold text-success">-{{ $discount->discount_percentage }}%</div>
                                                    <div class="small text-success">
                                                        {{ number_format($discount->final_price, 0, ',', ' ') }} FCFA
                                                    </div>
                                                    @if($discount->expires_at)
                                                        <div class="text-muted" style="font-size: 0.75rem;">
                                                            Expire le {{ $discount->expires_at->format('d/m/Y') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="text-center">
                                                    <span class="badge {{ $discount->status_class }}">
                                                        {{ $discount->status_text }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Actions -->
                                        <div class="col-md-2">
                                            <div class="d-grid gap-1">
                                                <a href="{{ route('discounts.show', $discount) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i>
                                                    Voir
                                                </a>
                                                
                                                @if($discount->status === 'pending')
                                                    <a href="{{ route('discounts.show', $discount) }}" 
                                                       class="btn btn-sm btn-success">
                                                        <i class="fas fa-check me-1"></i>
                                                        Répondre
                                                    </a>
                                                @endif
                                                
                                                @if($discount->message)
                                                    <a href="{{ route('messages.conversation', ['userId' => $discount->user_id, 'itemId' => $discount->item_id]) }}" 
                                                       class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-comments me-1"></i>
                                                        Message
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $discounts->withQueryString()->links() }}
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-percentage fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucune demande de réduction</h5>
                        <p class="text-muted">
                            @if(request()->hasAny(['status', 'period', 'search']))
                                Aucune demande ne correspond à vos critères de recherche.
                            @else
                                Vous n'avez encore reçu aucune demande de réduction sur vos produits.
                            @endif
                        </p>
                        @if(request()->hasAny(['status', 'period', 'search']))
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                <i class="fas fa-times me-2"></i>
                                Effacer les filtres
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection