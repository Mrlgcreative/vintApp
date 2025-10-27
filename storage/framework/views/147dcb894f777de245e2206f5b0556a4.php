

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0"><i class="fas fa-mobile-alt me-2"></i>Paiement Mobile Money</h4>
                </div>
                <div class="card-body">
                    <?php if(isset($cart) && !empty($cart)): ?>
                        <div class="mb-4">
                            <h5>Votre commande</h5>
                            <ul class="list-group mb-2">
                                <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <?php if(isset($item['image']) && $item['image']): ?>
                                                <img src="<?php echo e(asset('storage/' . $item['image'])); ?>" alt="<?php echo e($item['name']); ?>" width="32" class="me-2 rounded">
                                            <?php endif; ?>
                                            <?php echo e($item['name']); ?> x <?php echo e($item['quantity']); ?>

                                        </span>
                                        <span><?php echo e(number_format($item['price'] * $item['quantity'], 2)); ?> <?php echo e($item['currency']); ?></span>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <?php
                                // Déterminer la devise prioritaire (la plus fréquente)
                                $currencyCounts = [];
                                foreach($cart as $item) {
                                    $currency = $item['currency'] ?? 'USD';
                                    $currencyCounts[$currency] = ($currencyCounts[$currency] ?? 0) + 1;
                                }
                                arsort($currencyCounts);
                                $priorityCurrency = array_key_first($currencyCounts);
                                
                                // Calculer le sous-total dans la devise prioritaire
                                $exchangeRate = 2650; // Taux par défaut, sera récupéré via API
                                $subtotalInPriority = 0;
                                
                                foreach($cart as $item) {
                                    $itemTotal = $item['price'] * $item['quantity'];
                                    $itemCurrency = $item['currency'] ?? 'USD';
                                    
                                    if ($itemCurrency !== $priorityCurrency) {
                                        if ($priorityCurrency === 'USD' && $itemCurrency === 'CDF') {
                                            $itemTotal = $itemTotal / $exchangeRate;
                                        } elseif ($priorityCurrency === 'CDF' && $itemCurrency === 'USD') {
                                            $itemTotal = $itemTotal * $exchangeRate;
                                        }
                                    }
                                    
                                    $subtotalInPriority += $itemTotal;
                                }
                                
                                $subtotalInPriority = round($subtotalInPriority, 2);
                                
                                // Calculer les frais de transport dans la devise prioritaire
                                $transportFeeInPriority = ($subtotalInPriority * $transportFeePercentage) / 100;
                                $transportFeeInPriority = round($transportFeeInPriority, 2);
                                
                                // Total final
                                $totalInPriority = $subtotalInPriority + $transportFeeInPriority;
                            ?>
                            
                            <!-- Récapitulatif des montants -->
                            <div class="border-top pt-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Sous-total :</span>
                                    <strong><?php echo e(number_format($subtotalInPriority, 2)); ?> <?php echo e($priorityCurrency); ?></strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>
                                        Frais de livraison :
                                        <small class="text-muted">(<?php echo e($transportFeePercentage); ?>%)</small>
                                    </span>
                                    <strong class="text-primary">+<?php echo e(number_format($transportFeeInPriority, 2)); ?> <?php echo e($priorityCurrency); ?></strong>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span class="h5 mb-0">Total à payer :</span>
                                    <span class="h5 mb-0 text-success fw-bold"><?php echo e(number_format($totalInPriority, 2)); ?> <?php echo e($priorityCurrency); ?></span>
                                </div>
                                <?php if(count($currencyCounts) > 1): ?>
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle"></i> Devises mixtes converties en <?php echo e($priorityCurrency); ?>

                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <form id="payment-form">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Numéro Mobile Money</label>
                            <div id="operator-info" class="mb-3" style="display: none;">
                                <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                    <div class="operator-logo-wrapper me-3" style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); background: white;">
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
                            <div class="input-group">
                                <span class="input-group-text">+243</span>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       maxlength="9" 
                                       placeholder="Ex: 850123456" 
                                       required>
                            </div>
                            <small class="text-muted">
                                Entrez votre numéro Mobile Money (Orange: 84/85, M-Pesa: 81/82, Airtel: 97/99, Africell: 90-93)
                            </small>
                            <input type="hidden" id="provider" name="provider">
                        </div>
                        
                        <div class="mb-3">
                            <label for="amount" class="form-label">Montant à payer</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="amount" name="amount" 
                                       min="1" step="0.01" required readonly
                                       value="<?php echo e(isset($totalInPriority) ? $totalInPriority : (isset($total) ? $total : '')); ?>">
                                <span class="input-group-text"><?php echo e(isset($priorityCurrency) ? $priorityCurrency : (isset($cart) && !empty($cart) ? ($cart[0]['currency'] ?? 'USD') : 'USD')); ?></span>
                            </div>
                            <?php
                                $displayCurrency = isset($priorityCurrency) ? $priorityCurrency : (isset($cart) && !empty($cart) ? ($cart[0]['currency'] ?? 'USD') : 'USD');
                            ?>
                            <?php if($displayCurrency === 'USD'): ?>
                                <small class="text-muted">
                                    Environ <span id="amount-cdf">0</span> CDF (1 USD = <span id="rate-display"></span>2650</span> CDF)
                                </small>
                            <?php elseif($displayCurrency === 'CDF'): ?>
                                <small class="text-muted">
                                    Environ <span id="amount-usd">0</span> USD (1 USD = <span id="rate-display">2650</span> CDF)
                                </small>
                            <?php endif; ?>
                        </div>
                        
                        <input type="hidden" id="purpose" name="purpose" value="Paiement commande">
                        
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-paper-plane me-2"></i>Payer maintenant
                        </button>
                    </form>
                    
                    <div id="payment-status" class="mt-4" style="display:none;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const buyerId = <?php echo e(Auth::id() !== null ? Auth::id() : 'null'); ?>;

// Récupérer la devise du produit (devise prioritaire du panier)
const productCurrency = '<?php echo e(isset($priorityCurrency) ? $priorityCurrency : (isset($cart) && !empty($cart) ? ($cart[0]["currency"] ?? "USD") : "USD")); ?>';

// Taux de change (sera récupéré dynamiquement)
let exchangeRate = 2650; // Valeur par défaut

// Récupérer le taux de change en temps réel
fetch('/exchange/rate')
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            exchangeRate = data.rate;
            console.log('Taux de change USD/CDF:', exchangeRate);
            
            // Mettre à jour l'affichage du taux
            const rateDisplayElements = document.querySelectorAll('#rate-display');
            rateDisplayElements.forEach(element => {
                element.textContent = exchangeRate.toLocaleString('fr-FR');
            });
            
            // Mettre à jour la conversion affichée
            updateCurrencyConversion();
        }
    })
    .catch(error => {
        console.error('Erreur récupération taux:', error);
    });

// Configuration des opérateurs
const operators = {
    '84': { 
        name: 'Orange Money', 
        provider: 'Orange Money',
        logo: '/images/operators/orange.png',
        pattern: '^8[45][0-9]{7}$',
        format: '84XXXXXXX ou 85XXXXXXX'
    },
    '85': { 
        name: 'Orange Money', 
        provider: 'Orange Money',
        logo: '/images/operators/orange.png',
        pattern: '^8[45][0-9]{7}$',
        format: '84XXXXXXX ou 85XXXXXXX'
    },
    '89': { 
        name: 'Orange Money', 
        provider: 'Orange Money',
        logo: '/images/operators/orange.png',
        pattern: '^8[45][0-9]{7}$',
        format: '84XXXXXXX ou 85XXXXXXX'
    },
    '81': { 
        name: 'Vodacom M-Pesa', 
        provider: 'Vodacom M-Pesa',
        logo: '/images/operators/mpesa.png',
        pattern: '^8[12][0-9]{7}$',
        format: '81XXXXXXX ou 82XXXXXXX'
    },
    '83': { 
        name: 'Vodacom M-Pesa', 
        provider: 'Vodacom M-Pesa',
        logo: '/images/operators/mpesa.png',
        pattern: '^8[12][0-9]{7}$',
        format: '81XXXXXXX ou 82XXXXXXX'
    },
    '82': { 
        name: 'Vodacom M-Pesa', 
        provider: 'Vodacom M-Pesa',
        logo: '/images/operators/mpesa.png',
        pattern: '^8[12][0-9]{7}$',
        format: '81XXXXXXX ou 82XXXXXXX'
    },
    '97': { 
        name: 'Airtel Money', 
        provider: 'Airtel Money',
        logo: '/images/operators/airtel.png',
        pattern: '^9[79][0-9]{7}$',
        format: '97XXXXXXX ou 99XXXXXXX'
    },
    '98': { 
        name: 'Airtel Money', 
        provider: 'Airtel Money',
        logo: '/images/operators/airtel.png',
        pattern: '^9[79][0-9]{7}$',
        format: '97XXXXXXX ou 99XXXXXXX'
    },
    '99': { 
        name: 'Airtel Money', 
        provider: 'Airtel Money',
        logo: '/images/operators/airtel.png',
        pattern: '^9[79][0-9]{7}$',
        format: '97XXXXXXX ou 99XXXXXXX'
    },
    '90': { 
        name: 'Africell Money', 
        provider: 'Africell Money',
        logo: '/images/operators/africell.png',
        pattern: '^9[0-3][0-9]{7}$',
        format: '90XXXXXXX à 93XXXXXXX'
    },
    '91': { 
        name: 'Africell Money', 
        provider: 'Africell Money',
        logo: '/images/operators/africell.png',
        pattern: '^9[0-3][0-9]{7}$',
        format: '90XXXXXXX à 93XXXXXXX'
    },
    '92': { 
        name: 'Africell Money', 
        provider: 'Africell Money',
        logo: '/images/operators/africell.png',
        pattern: '^9[0-3][0-9]{7}$',
        format: '90XXXXXXX à 93XXXXXXX'
    },
    '93': { 
        name: 'Africell Money', 
        provider: 'Africell Money',
        logo: '/images/operators/africell.png',
        pattern: '^9[0-3][0-9]{7}$',
        format: '90XXXXXXX à 93XXXXXXX'
    }
};

// Conversion de devise en temps réel (USD <-> CDF)
function updateCurrencyConversion() {
    const amountInput = document.getElementById('amount');
    const amount = parseFloat(amountInput.value) || 0;
    
    if (productCurrency === 'USD') {
        // Si le produit est en USD, afficher l'équivalent en CDF
        const amountCDF = Math.round(amount * exchangeRate);
        const cdfElement = document.getElementById('amount-cdf');
        if (cdfElement) {
            cdfElement.textContent = amountCDF.toLocaleString('fr-FR');
        }
    } else if (productCurrency === 'CDF') {
        // Si le produit est en CDF, afficher l'équivalent en USD
        const amountUSD = (amount / exchangeRate).toFixed(2);
        const usdElement = document.getElementById('amount-usd');
        if (usdElement) {
            usdElement.textContent = parseFloat(amountUSD).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }
}

// Calculer la conversion au chargement
updateCurrencyConversion();

// Détecter l'opérateur à partir du numéro
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
            e.target.classList.remove('is-invalid');
            e.target.classList.add('is-valid');
        } else {
            e.target.classList.remove('is-valid');
            if (e.target.value.length === 9) {
                e.target.classList.add('is-invalid');
            }
        }
    } else {
        // Cacher les informations de l'opérateur si le préfixe n'est pas reconnu
        operatorInfo.style.display = 'none';
        providerInput.value = '';
        e.target.classList.remove('is-valid', 'is-invalid');
    }
});

// Gestion de la soumission du formulaire
document.getElementById('payment-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    console.log('Formulaire soumis !'); // Debug
    
    // Récupération des valeurs du formulaire
    const provider = document.getElementById('provider').value;
    const amount = document.getElementById('amount').value;
    const phone = document.getElementById('phone').value;
    const purpose = document.getElementById('purpose').value;
    
    console.log('Données:', { provider, amount, phone, purpose, buyerId }); // Debug
    
    // Éléments d'interface
    const statusDiv = document.getElementById('payment-status');
    const submitButton = this.querySelector('button[type="submit"]');
    
    // Validation
    if (!provider) {
        statusDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Veuillez entrer un numéro Mobile Money valide</div>';
        statusDiv.style.display = 'block';
        return;
    }
    
    if (!amount || amount <= 0) {
        statusDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Veuillez entrer un montant valide</div>';
        statusDiv.style.display = 'block';
        return;
    }
    
    if (!phone || phone.length !== 9) {
        statusDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Veuillez entrer un numéro de téléphone valide (9 chiffres)</div>';
        statusDiv.style.display = 'block';
        return;
    }
    
    if (!buyerId) {
        statusDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Vous devez être connecté pour effectuer un paiement</div>';
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
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">Transaction en cours</h5>
                        <p class="mb-0">Veuillez patienter pendant le traitement de votre paiement...</p>
                        <small class="text-muted">Cela peut prendre quelques secondes</small>
                    </div>
                </div>
            </div>
        `;
        statusDiv.style.display = 'block';

        console.log('Envoi de la requête...'); // Debug

        // Déterminer la route API selon le provider
        let apiRoute;
        switch(provider) {
            case 'Vodacom M-Pesa':
                apiRoute = '<?php echo e(route("payments.mpesa")); ?>';
                break;
            case 'Orange Money':
                apiRoute = '<?php echo e(route("payments.orange_money")); ?>';
                break;
            case 'Airtel Money':
                apiRoute = '<?php echo e(route("payments.airtel_money")); ?>';
                break;
            case 'Africell Money':
                apiRoute = '<?php echo e(route("payments.africell")); ?>';
                break;
            default:
                // Fallback vers simulation pour tests
                apiRoute = '<?php echo e(route("payments.simulate")); ?>';
        }

        // Appel API réelle du provider
        const response = await fetch(apiRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                provider: provider,
                amount: parseFloat(amount),
                currency: productCurrency,  // Devise du produit (USD ou CDF)
                phone: phone,
                purpose: purpose,
                buyer_id: buyerId
            })
        });

        console.log('Réponse reçue:', response.status); // Debug

        const data = await response.json();
        console.log('Données reçues:', data); // Debug

        if (response.ok && data.status === 'success') {
            // Rediriger vers la page de succès avec l'ID de transaction
            console.log('Redirection vers success'); // Debug
            window.location.href = '<?php echo e(route("payments.success", ":transaction_id")); ?>'.replace(':transaction_id', data.transaction_id);
        } else {
            // Rediriger vers la page d'erreur avec les détails
            console.log('Redirection vers error'); // Debug
            const errorParams = new URLSearchParams({
                error: data.message || 'Une erreur est survenue',
                amount: amount,
                provider: provider,
                currency: productCurrency
            });
            window.location.href = '<?php echo e(route("payments.error")); ?>?' + errorParams.toString();
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
                        <small class="text-muted">Erreur: ${error.message}</small>
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
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/payments.blade.php ENDPATH**/ ?>