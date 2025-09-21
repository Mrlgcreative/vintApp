@extends('app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0"><i class="fas fa-mobile-alt me-2"></i>Paiement Mobile Money</h4>
                </div>
                <div class="card-body">
                    @if(isset($cart) && !empty($cart))
                        <div class="mb-4">
                            <h5>Votre commande</h5>
                            <ul class="list-group mb-2">
                                @foreach($cart as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            @if($item['image'])
                                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" width="32" class="me-2 rounded">
                                            @endif
                                            {{ $item['name'] }} x {{ $item['quantity'] }}
                                        </span>
                                        <span>{{ number_format($item['price'] * $item['quantity'], 2) }} {{ $item['currency'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="text-end fw-bold">Total : {{ number_format($total, 2) }} {{ $item['currency'] ?? '' }}</div>
                        </div>
                    @endif
                    <form id="payment-form">
                        <div class="mb-3">
                            <label for="provider" class="form-label">Opérateur</label>
                            <select class="form-select" id="provider" name="provider" required>
                                <option value="">Choisir...</option>
                                <option value="illicocash">Illicocash (Rawbank)</option>
                                <option value="orange_money">Orange Money</option>
                                <option value="airtel_money">Airtel Money</option>
                                <option value="mpesa">Vodacom Mpesa</option>
                                <option value="africell">Africell Money</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Montant à payer</label>
                            <input type="number" class="form-control" id="amount" name="amount" min="1" step="0.01" required value="{{ isset($total) ? $total : '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Numéro Mobile Money</label>
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="Ex: 08xxxxxxxx" required>
                        </div>
                        <div class="mb-3">
                            <label for="purpose" class="form-label">Motif du paiement</label>
                            <input type="text" class="form-control" id="purpose" name="purpose" placeholder="Achat, commande, etc." required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane me-2"></i>Payer maintenant
                        </button>
                    </form>
                    <div id="payment-status" class="mt-4" style="display:none;"></div>
                    <div id="distribution-summary" class="mt-3" style="display:none;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
const buyerId = {{ Auth::id() !== null ? Auth::id() : 'null' }};
document.getElementById('payment-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const provider = document.getElementById('provider').value;
    const amount = document.getElementById('amount').value;
    const phone = document.getElementById('phone').value;
    const purpose = document.getElementById('purpose').value;
    const statusDiv = document.getElementById('payment-status');
    const distDiv = document.getElementById('distribution-summary');
    statusDiv.style.display = 'none';
    distDiv.style.display = 'none';
    if (!provider) return;
    statusDiv.innerHTML = '<div class="alert alert-info">Paiement en cours...</div>';
    statusDiv.style.display = 'block';
    fetch(`/payments/${provider.replace('_', '-')}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            buyer_id: buyerId,
            amount: amount,
            phone: phone,
            purpose: purpose
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            statusDiv.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
            if (data.distribution) {
                let html = '<h6>Répartition des fonds :</h6><ul class="list-group">';
                data.distribution.forEach(part => {
                    html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>${part.beneficiary_type}</span>
                        <span><b>${part.amount}</b> FCFA</span>
                    </li>`;
                });
                html += '</ul>';
                distDiv.innerHTML = html;
                distDiv.style.display = 'block';
            }
        } else {
            statusDiv.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Erreur de paiement') + '</div>';
        }
        statusDiv.style.display = 'block';
    })
    .catch(() => {
        statusDiv.innerHTML = '<div class="alert alert-danger">Erreur lors de la requête paiement.</div>';
        statusDiv.style.display = 'block';
    });
});
</script>
@endsection 