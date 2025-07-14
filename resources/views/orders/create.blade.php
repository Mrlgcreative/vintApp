@extends('app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Passer une commande
                    </h3>
                </div>
                <div class="card-body">
                    @if($item)
                        <!-- Informations de l'article -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                @if($item->images && count($item->images) > 0)
                                    <img src="{{ asset('storage/' . $item->images[0]) }}" 
                                         class="img-fluid rounded" 
                                         alt="{{ $item->name }}">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                         style="height: 200px;">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-8">
                                <h4>{{ $item->name }}</h4>
                                <p class="text-muted">{{ Str::limit($item->description, 150) }}</p>
                                
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">Prix unitaire</small>
                                        <div class="h5 text-primary fw-bold">{{ $item->formatted_price }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Stock disponible</small>
                                        <div class="h5 fw-bold">{{ $item->quantity }}</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    @if($item->category)
                                        <span class="badge bg-primary me-2">{{ $item->category->name }}</span>
                                    @endif
                                    @if($item->brand)
                                        <span class="badge bg-secondary me-2">{{ $item->brand->name }}</span>
                                    @endif
                                    <span class="badge condition-badge condition-{{ $item->condition }}">
                                        {{ ucfirst(str_replace('_', ' ', $item->condition)) }}
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted">Vendeur</small>
                                    <div class="fw-bold">{{ $item->user->name ?? 'Vendeur inconnu' }}</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('orders.store') }}" method="POST">
                        @csrf
                        
                        @if($item)
                            <input type="hidden" 
                                   name="item_id" 
                                   value="{{ $item->id }}"
                                   aria-label="Identifiant de l'article {{ $item->name }}">
                        @else
                            <div class="mb-3">
                                <label for="item_id" class="form-label">Article *</label>
                                <select class="form-select @error('item_id') is-invalid @enderror" 
                                        id="item_id" 
                                        name="item_id" 
                                        required>
                                    <option value="">Sélectionner un article</option>
                                    @foreach(App\Models\Item::with(['category', 'brand'])->where('status', 'active')->get() as $availableItem)
                                        <option value="{{ $availableItem->id }}" 
                                                {{ old('item_id') == $availableItem->id ? 'selected' : '' }}>
                                            {{ $availableItem->name }} - {{ $availableItem->formatted_price }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('item_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="quantity" class="form-label">Quantité *</label>
                                <input type="number" 
                                       class="form-control @error('quantity') is-invalid @enderror" 
                                       id="quantity" 
                                       name="quantity" 
                                       value="{{ old('quantity', 1) }}" 
                                       min="1" 
                                       max="{{ $item ? $item->quantity : 999 }}" 
                                       required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <span class="form-label">Prix total</span>
                                <div class="form-control-plaintext" 
                                     id="total-price"
                                     @if($item)
                                         data-unit-price="{{ $item->price }}"
                                         data-currency="{{ $item->currency }}"
                                     @endif>
                                    @if($item)
                                        {{ $item->formatted_price }}
                                    @else
                                        Sélectionnez un article
                                    @endif
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h5 class="mb-3">
                            <i class="fas fa-truck me-2"></i>
                            Informations de livraison
                        </h5>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="shipping_city" class="form-label">Ville *</label>
                                <input type="text" 
                                       class="form-control @error('shipping_city') is-invalid @enderror" 
                                       id="shipping_city" 
                                       name="shipping_city" 
                                       value="{{ old('shipping_city') }}" 
                                       placeholder="Ex: Kinshasa"
                                       required>
                                @error('shipping_city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="shipping_phone" class="form-label">Téléphone *</label>
                                <input type="tel" 
                                       class="form-control @error('shipping_phone') is-invalid @enderror" 
                                       id="shipping_phone" 
                                       name="shipping_phone" 
                                       value="{{ old('shipping_phone') }}" 
                                       placeholder="Ex: +243 123 456 789"
                                       required>
                                @error('shipping_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Adresse de livraison *</label>
                            <textarea class="form-control @error('shipping_address') is-invalid @enderror" 
                                      id="shipping_address" 
                                      name="shipping_address" 
                                      rows="3" 
                                      placeholder="Adresse complète pour la livraison"
                                      required>{{ old('shipping_address') }}</textarea>
                            @error('shipping_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">Notes (optionnel)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3" 
                                      placeholder="Informations supplémentaires pour le vendeur">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ $item ? route('items.show', $item) : route('items.index') }}" 
                               class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Annuler
                            </a>
                            <button type="submit" 
                                    class="btn btn-primary btn-lg"
                                    aria-label="Confirmer la commande pour {{ $item ? $item->name : 'l\'article sélectionné' }}">
                                <i class="fas fa-check me-2"></i>
                                Confirmer la commande
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    const totalPriceDiv = document.getElementById('total-price');
    
    // Récupérer les données depuis les attributs data
    const unitPrice = totalPriceDiv.getAttribute('data-unit-price');
    const currency = totalPriceDiv.getAttribute('data-currency');
    
    if (unitPrice && currency) {
        const symbol = currency === 'USD' ? '$' : 'FC';
        
        function updateTotalPrice() {
            const quantity = parseInt(quantityInput.value) || 0;
            const total = parseFloat(unitPrice) * quantity;
            totalPriceDiv.textContent = symbol + ' ' + total.toFixed(2);
        }
        
        quantityInput.addEventListener('input', updateTotalPrice);
    }
});
</script>
@endsection 