@extends('app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-percentage me-2"></i>
                        Demande de réduction
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Informations sur le produit -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            @if($discount->item->images && count($discount->item->images) > 0)
                                <img src="{{ Storage::url($discount->item->images[0]) }}" 
                                     class="img-fluid rounded" 
                                     alt="{{ $discount->item->name }}"
                                     style="height: 200px; object-fit: cover; width: 100%;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                     style="height: 200px;">
                                    <i class="fas fa-image fa-2x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h6 class="fw-bold">{{ $discount->item->name }}</h6>
                            <p class="text-muted mb-2">{{ Str::limit($discount->item->description, 150) }}</p>
                            <div class="mb-2">
                                <span class="badge bg-info">{{ $discount->item->category->name }}</span>
                                @if($discount->item->brand)
                                    <span class="badge bg-secondary ms-1">{{ $discount->item->brand->name }}</span>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-primary fs-5">{{ $discount->item->formatted_price }}</strong>
                                </div>
                                <div>
                                    <span class="badge {{ $discount->status_class }}">
                                        {{ $discount->status_text }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations sur l'acheteur -->
                    <div class="border rounded p-3 mb-4 bg-light">
                        <h6 class="fw-bold mb-2">
                            <i class="fas fa-user me-2"></i>
                            Demandeur
                        </h6>
                        <div class="d-flex align-items-center">
                            <div class="avatar me-3">
                                <i class="fas fa-user-circle fa-2x text-muted"></i>
                            </div>
                            <div>
                                <div class="fw-bold">{{ $discount->user->name }}</div>
                                <small class="text-muted">
                                    Membre depuis {{ $discount->user->created_at->format('M Y') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Message original -->
                    @if($discount->message)
                        <div class="border rounded p-3 mb-4">
                            <h6 class="fw-bold mb-2">
                                <i class="fas fa-envelope me-2"></i>
                                Message de demande
                            </h6>
                            <p class="mb-0">{{ $discount->message->content }}</p>
                            <small class="text-muted">
                                Envoyé le {{ $discount->message->created_at->format('d/m/Y à H:i') }}
                            </small>
                        </div>
                    @endif

                    @if($discount->status === 'pending')
                        <!-- Formulaires d'action -->
                        <div class="row">
                            <!-- Approuver avec réduction -->
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-check me-2"></i>
                                            Accorder une réduction
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('discounts.approve', $discount) }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label">Pourcentage de réduction</label>
                                                <div class="input-group">
                                                    <input type="number" 
                                                           name="discount_percentage" 
                                                           class="form-control" 
                                                           min="1" 
                                                           max="99" 
                                                           required
                                                           id="discountPercentage">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                <small class="text-muted">Entre 1% et 99%</small>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Nouveau prix</label>
                                                <div class="p-2 bg-light rounded">
                                                    <span id="finalPrice" class="fw-bold text-success fs-5">
                                                        {{ $discount->item->formatted_price }}
                                                    </span>
                                                    <small class="text-muted d-block">
                                                        Économie : <span id="savingAmount">0 FCFA</span>
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Validité (en jours)</label>
                                                <select name="expires_in_days" class="form-select" required>
                                                    <option value="1">1 jour</option>
                                                    <option value="2">2 jours</option>
                                                    <option value="3">3 jours</option>
                                                    <option value="7" selected>1 semaine</option>
                                                    <option value="14">2 semaines</option>
                                                    <option value="30">1 mois</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Message de réponse (optionnel)</label>
                                                <textarea name="response_message" 
                                                          class="form-control" 
                                                          rows="3" 
                                                          placeholder="Message personnalisé pour l'acheteur..."></textarea>
                                            </div>

                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="fas fa-check me-2"></i>
                                                Accorder cette réduction
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Refuser -->
                            <div class="col-md-6">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-times me-2"></i>
                                            Refuser la demande
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('discounts.reject', $discount) }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label">Raison du refus (optionnel)</label>
                                                <textarea name="rejection_reason" 
                                                          class="form-control" 
                                                          rows="4" 
                                                          placeholder="Expliquer pourquoi vous ne pouvez pas accorder de réduction..."></textarea>
                                            </div>

                                            <button type="submit" class="btn btn-danger w-100">
                                                <i class="fas fa-times me-2"></i>
                                                Refuser cette demande
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- État de la réduction -->
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>
                                État de la demande
                            </h6>
                            <p class="mb-0">
                                Cette demande de réduction a été <strong>{{ strtolower($discount->status_text) }}</strong>
                                @if($discount->status === 'approved')
                                    avec une réduction de <strong>{{ $discount->discount_percentage }}%</strong>.
                                    <br>
                                    Prix final : <strong>{{ number_format($discount->final_price, 0, ',', ' ') }} FCFA</strong>
                                    <br>
                                    Valable jusqu'au <strong>{{ $discount->expires_at->format('d/m/Y') }}</strong>
                                @endif
                            </p>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Retour aux messages
                        </a>
                        
                        <a href="{{ route('messages.conversation', ['userId' => $discount->user_id, 'itemId' => $discount->item_id]) }}" 
                           class="btn btn-primary">
                            <i class="fas fa-comments me-2"></i>
                            Voir la conversation
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const discountInput = document.getElementById('discountPercentage');
    const finalPriceElement = document.getElementById('finalPrice');
    const savingAmountElement = document.getElementById('savingAmount');
    const originalPrice = {{ $discount->original_price }};

    if (discountInput) {
        discountInput.addEventListener('input', function() {
            const discountPercentage = parseFloat(this.value) || 0;
            const discountAmount = (originalPrice * discountPercentage) / 100;
            const finalPrice = originalPrice - discountAmount;

            // Mettre à jour l'affichage
            finalPriceElement.textContent = new Intl.NumberFormat('fr-FR').format(finalPrice) + ' FCFA';
            savingAmountElement.textContent = new Intl.NumberFormat('fr-FR').format(discountAmount) + ' FCFA';

            // Changer la couleur selon le pourcentage
            if (discountPercentage > 50) {
                finalPriceElement.className = 'fw-bold text-danger fs-5';
            } else if (discountPercentage > 20) {
                finalPriceElement.className = 'fw-bold text-warning fs-5';
            } else {
                finalPriceElement.className = 'fw-bold text-success fs-5';
            }
        });
    }
});
</script>
@endpush
@endsection