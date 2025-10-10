@extends('app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <ul class="nav nav-tabs mb-4" id="paymentTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="order-tab" data-bs-toggle="tab" data-bs-target="#order-payment" type="button" role="tab">
                        <i class="fas fa-shopping-cart me-2"></i>Payer une commande
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="wallet-tab" data-bs-toggle="tab" data-bs-target="#wallet-recharge" type="button" role="tab">
                        <i class="fas fa-wallet me-2"></i>Recharger mon wallet
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="paymentTabsContent">
                <!-- Paiement de commande -->
                <div class="tab-pane fade show active" id="order-payment" role="tabpanel">
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
                            <div id="operator-info" class="mb-3" style="display: none;">
                                <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                    <div class="operator-logo-wrapper me-3" style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                        <img id="operator-logo" src="" alt="" class="w-100 h-100 object-fit-cover">
                                    </div>
                                    <div>
                                        <h5 id="operator-name" class="mb-1 fw-bold"></h5>
                                        <small id="operator-format" class="text-muted d-block"></small>
                                        <div class="signal-strength mt-1">
                                            <i class="fas fa-signal text-success"></i>
                                            <small class="text-success ms-1">Réseau disponible</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <label for="phone" class="form-label">Numéro Mobile Money</label>
                            <div class="input-group">
                                <span class="input-group-text">+243</span>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       maxlength="9" 
                                       placeholder="Saisissez votre numéro" 
                                       required>
                                <input type="hidden" id="provider" name="provider">
                            </div>
                            <small class="text-muted">
                               
                            </small>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Montant à payer</label>
                            <input type="number" class="form-control" id="amount" name="amount" min="1" step="0.01" required value="{{ isset($total) ? $total : '' }}">
                        </div>
                        <div class="mb-3 phone-fields">
                            <!-- Champs de téléphone dynamiques -->
                            <!-- Orange Money -->
                            <div id="orange_money_phone" class="phone-field" style="display: none;">
                                <label class="form-label">Numéro Orange Money</label>
                                <div class="input-group">
                                    <span class="input-group-text">+243</span>
                                    <input type="tel" class="form-control" name="phone" 
                                           pattern="8[45][0-9]{7}" maxlength="9" 
                                           placeholder="84XXXXXXX ou 85XXXXXXX">
                                </div>
                                <small class="text-muted">Format : 84XXXXXXX ou 85XXXXXXX</small>
                            </div>
                            
                            <!-- Mpesa -->
                            <div id="mpesa_phone" class="phone-field" style="display: none;">
                                <label class="form-label">Numéro Vodacom M-Pesa</label>
                                <div class="input-group">
                                    <span class="input-group-text">+243</span>
                                    <input type="tel" class="form-control" name="phone" 
                                           pattern="8[12][0-9]{7}" maxlength="9" 
                                           placeholder="81XXXXXXX ou 82XXXXXXX">
                                </div>
                                <small class="text-muted">Format : 81XXXXXXX ou 82XXXXXXX</small>
                            </div>
                            
                            <!-- Airtel Money -->
                            <div id="airtel_money_phone" class="phone-field" style="display: none;">
                                <label class="form-label">Numéro Airtel Money</label>
                                <div class="input-group">
                                    <span class="input-group-text">+243</span>
                                    <input type="tel" class="form-control" name="phone" 
                                           pattern="9[79][0-9]{7}" maxlength="9" 
                                           placeholder="97XXXXXXX ou 99XXXXXXX">
                                </div>
                                <small class="text-muted">Format : 97XXXXXXX ou 99XXXXXXX</small>
                            </div>
                            
                            <!-- Africell Money -->
                            <div id="africell_phone" class="phone-field" style="display: none;">
                                <label class="form-label">Numéro Africell Money</label>
                                <div class="input-group">
                                    <span class="input-group-text">+243</span>
                                    <input type="tel" class="form-control" name="phone" 
                                           pattern="9[0-3][0-9]{7}" maxlength="9" 
                                           placeholder="90XXXXXXX à 93XXXXXXX">
                                </div>
                                <small class="text-muted">Format : 90XXXXXXX à 93XXXXXXX</small>
                            </div>
                            
                            <!-- Illicocash -->
                            <div id="illicocash_phone" class="phone-field" style="display: none;">
                                <label class="form-label">Numéro Illicocash</label>
                                <div class="input-group">
                                    <span class="input-group-text">+243</span>
                                    <input type="tel" class="form-control" name="phone" 
                                           pattern="[0-9]{9}" maxlength="9" 
                                           placeholder="Votre numéro">
                                </div>
                                <small class="text-muted">Format : Numéro à 9 chiffres</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <input type="hidden" id="purpose" name="purpose">
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

// Configuration des opérateurs
const operators = {
    '84': { 
        name: 'Orange Money', 
        provider: 'orange_money',
        logo: '/images/operators/orange.svg',
        pattern: '^8[45][0-9]{7}$',
        format: '84XXXXXXX ou 85XXXXXXX'
    },
    '85': { 
        name: 'Orange Money', 
        provider: 'orange_money',
        logo: '/images/operators/orange.png',
        pattern: '^8[45][0-9]{7}$',
        format: '84XXXXXXX ou 85XXXXXXX'
    },
    '81': { 
        name: 'Vodacom M-Pesa', 
        provider: 'mpesa',
        logo: '/images/operators/mpesa.png',
        pattern: '^8[12][0-9]{7}$',
        format: '81XXXXXXX ou 82XXXXXXX'
    },
    '82': { 
        name: 'Vodacom M-Pesa', 
        provider: 'mpesa',
        logo: '/images/operators/mpesa.png',
        pattern: '^8[12][0-9]{7}$',
        format: '81XXXXXXX ou 82XXXXXXX'
    },
    '97': { 
        name: 'Airtel Money', 
        provider: 'airtel_money',
        logo: '/images/operators/airtel.png',
        pattern: '^9[79][0-9]{7}$',
        format: '97XXXXXXX ou 99XXXXXXX'
    },
    '99': { 
        name: 'Airtel Money', 
        provider: 'airtel_money',
        logo: '/images/operators/airtel.png',
        pattern: '^9[79][0-9]{7}$',
        format: '97XXXXXXX ou 99XXXXXXX'
    },
    '90': { 
        name: 'Africell Money', 
        provider: 'africell',
        logo: '/images/operators/africell.png',
        pattern: '^9[0-3][0-9]{7}$',
        format: '90XXXXXXX à 93XXXXXXX'
    },
    '91': { 
        name: 'Africell Money', 
        provider: 'africell',
        logo: '/images/operators/africell.png',
        pattern: '^9[0-3][0-9]{7}$',
        format: '90XXXXXXX à 93XXXXXXX'
    },
    '92': { 
        name: 'Africell Money', 
        provider: 'africell',
        logo: '/images/operators/africell.png',
        pattern: '^9[0-3][0-9]{7}$',
        format: '90XXXXXXX à 93XXXXXXX'
    },
    '93': { 
        name: 'Africell Money', 
        provider: 'africell',
        logo: '/images/operators/africell.png',
        pattern: '^9[0-3][0-9]{7}$',
        format: '90XXXXXXX à 93XXXXXXX'
    }
};

// Gestion de la détection automatique de l'opérateur
document.getElementById('phone').addEventListener('input', function(e) {
    const prefix = e.target.value.substring(0, 2);
    const operatorInfo = document.getElementById('operator-info');
    const providerInput = document.getElementById('provider');
    const operatorLogo = document.getElementById('operator-logo');
    const operatorName = document.getElementById('operator-name');
    const operatorFormat = document.getElementById('operator-format');
    
    if (operators[prefix]) {
        // Afficher les informations de l'opérateur avec animation
        operatorInfo.style.opacity = '0';
        operatorInfo.style.display = 'block';
        setTimeout(() => {
            operatorInfo.style.transition = 'opacity 0.3s ease-in-out';
            operatorInfo.style.opacity = '1';
        }, 50);
        
        operatorLogo.src = operators[prefix].logo;
        operatorLogo.alt = operators[prefix].name;
        operatorName.textContent = operators[prefix].name;
        operatorFormat.textContent = operators[prefix].format;
        providerInput.value = operators[prefix].provider;

        // Mettre à jour automatiquement le motif du paiement
        const purposeInput = document.getElementById('purpose');
        const amount = document.getElementById('amount').value;
        purposeInput.value = `Paiement ${operators[prefix].name} - ${amount ? amount + ' USD' : ''}`;
        
        // Valider le format du numéro
        if (new RegExp(operators[prefix].pattern).test(e.target.value)) {
            e.target.setCustomValidity('');
        } else {
            e.target.setCustomValidity('Format de numéro invalide pour ' + operators[prefix].name);
        }
    } else {
        // Cacher les informations de l'opérateur si le préfixe n'est pas reconnu
        operatorInfo.style.display = 'none';
        providerInput.value = '';
        e.target.setCustomValidity('Veuillez entrer un numéro valide');
    }
});
    // Cacher tous les champs de téléphone
    document.querySelectorAll('.phone-field').forEach(field => {
        field.style.display = 'none';
        // Désactiver la validation pour les champs cachés
        const input = field.querySelector('input');
        if (input) input.required = false;
    });
    
    // Afficher le champ correspondant à l'opérateur sélectionné
    const provider = this.value;
    if (provider) {
        const phoneField = document.getElementById(provider + '_phone');
        if (phoneField) {
            document.querySelector('.phone-fields').style.display = 'block';
            phoneField.style.display = 'block';
            // Activer la validation pour le champ affiché
            const input = phoneField.querySelector('input');
            if (input) input.required = true;
        }
    } else {
        document.querySelector('.phone-fields').style.display = 'none';
    }
});

document.getElementById('payment-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Récupération des valeurs du formulaire
    const provider = document.getElementById('provider').value;
    const amount = document.getElementById('amount').value;
    const phone = document.getElementById('phone').value;
    const purpose = document.getElementById('purpose').value;
    
    // Éléments d'interface
    const statusDiv = document.getElementById('payment-status');
    const distDiv = document.getElementById('distribution-summary');
    const submitButton = this.querySelector('button[type="submit"]');
    
    // Validation
    if (!provider || !amount || !phone) {
        statusDiv.innerHTML = '<div class="alert alert-danger">Veuillez remplir tous les champs obligatoires.</div>';
        statusDiv.style.display = 'block';
        return;
    }

    try {
        // Désactiver le bouton et afficher l'indicateur de chargement
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Traitement en cours...';
        
        statusDiv.innerHTML = `
            <div class="alert alert-info">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">Transaction en cours</h5>
                        <p class="mb-0">Veuillez patienter pendant le traitement de votre paiement...</p>
                    </div>
                </div>
            </div>
        `;
        statusDiv.style.display = 'block';
        distDiv.style.display = 'none';

        // Appel API
        const response = await fetch('{{ route("payments.process") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                provider: provider,
                amount: parseFloat(amount),
                phone: phone,
                purpose: purpose,
                buyer_id: buyerId
            })
        });

        const data = await response.json();

        if (response.ok && data.status === 'success') {
            // Rediriger vers la page de suivi du paiement
            window.location.href = '/payments/status/' + data.transaction_id;
        } else {
            // Rediriger vers la page d'erreur
            window.location.href = '/payments/error?error=' + encodeURIComponent(data.message || 'Une erreur est survenue');
        }
    } catch (error) {
        console.error('Erreur lors du paiement:', error);
        statusDiv.innerHTML = `
            <div class="alert alert-danger">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-exclamation-circle fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">Erreur de paiement</h5>
                        <p class="mb-0">Une erreur est survenue lors du traitement de votre paiement. Veuillez réessayer.</p>
                    </div>
                </div>
            </div>
        `;
        statusDiv.style.display = 'block';
    } finally {
        // Réactiver le bouton et restaurer son texte original
        submitButton.disabled = false;
        submitButton.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Payer maintenant';
    }
});

// Gestion de la recharge de wallet
document.getElementById('wallet-recharge-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const walletId = document.getElementById('wallet_id').value;
    const provider = document.getElementById('recharge_provider').value;
    const amount = document.getElementById('recharge_amount').value;
    const phone = document.getElementById('recharge_phone').value;
    const statusDiv = document.getElementById('recharge-status');
    
    if (!walletId || !provider) return;
    
    statusDiv.innerHTML = '<div class="alert alert-info">Traitement de la recharge en cours...</div>';
    statusDiv.style.display = 'block';
    
    fetch(`/wallet/${walletId}/recharge/mobile`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            payment_method: provider,
            amount: amount,
            phone: phone
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'pending' || data.status === 'success') {
            statusDiv.innerHTML = `<div class="alert alert-${data.status === 'success' ? 'success' : 'info'}">${data.message}</div>`;
            if (data.status === 'success') {
                // Rafraîchir la page après 2 secondes pour montrer le nouveau solde
                setTimeout(() => window.location.reload(), 2000);
            }
        } else {
            statusDiv.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Erreur lors de la recharge') + '</div>';
        }
        statusDiv.style.display = 'block';
    })
    .catch(() => {
        statusDiv.innerHTML = '<div class="alert alert-danger">Erreur lors de la requête de recharge.</div>';
        statusDiv.style.display = 'block';
    });
});
</script>
@endsection