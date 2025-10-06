@extends('app')

@section('title', 'Mon Portefeuille')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-wallet text-primary me-2"></i>
                    Mon Portefeuille
                </h1>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="refreshBalances()">
                        <i class="fas fa-sync-alt"></i> Actualiser
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes des soldes -->
    <div class="row mb-4">
        <!-- Wallet USD -->
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title text-success mb-1">
                                <i class="fas fa-dollar-sign me-2"></i>Dollar Américain
                            </h5>
                            <p class="text-muted small mb-0">USD</p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('wallet.add-funds', $usdWallet) }}">
                                    <i class="fas fa-plus text-success me-2"></i>Ajouter des fonds
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('wallet.withdraw-funds', $usdWallet) }}">
                                    <i class="fas fa-minus text-danger me-2"></i>Retirer des fonds
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('wallet.transactions', $usdWallet) }}">
                                    <i class="fas fa-history text-info me-2"></i>Voir l'historique
                                </a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="text-center py-3">
                        <h2 class="display-5 fw-bold text-success mb-2" id="usd-balance">
                            ${{ number_format($usdWallet->balance, 2, '.', ',') }}
                        </h2>
                        <p class="text-muted mb-0">Solde disponible</p>
                    </div>
                    
                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('wallet.add-funds', $usdWallet) }}" class="btn btn-success flex-fill">
                            <i class="fas fa-plus me-1"></i>Ajouter
                        </a>
                        <a href="{{ route('wallet.withdraw-funds', $usdWallet) }}" class="btn btn-outline-danger flex-fill">
                            <i class="fas fa-minus me-1"></i>Retirer
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wallet CDF -->
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title text-warning mb-1">
                                <i class="fas fa-coins me-2"></i>Franc Congolais
                            </h5>
                            <p class="text-muted small mb-0">CDF</p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('wallet.add-funds', $cdfWallet) }}">
                                    <i class="fas fa-plus text-success me-2"></i>Ajouter des fonds
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('wallet.withdraw-funds', $cdfWallet) }}">
                                    <i class="fas fa-minus text-danger me-2"></i>Retirer des fonds
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('wallet.transactions', $cdfWallet) }}">
                                    <i class="fas fa-history text-info me-2"></i>Voir l'historique
                                </a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="text-center py-3">
                        <h2 class="display-5 fw-bold text-warning mb-2" id="cdf-balance">
                            {{ number_format($cdfWallet->balance, 2, ',', ' ') }} FC
                        </h2>
                        <p class="text-muted mb-0">Solde disponible</p>
                    </div>
                    
                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('wallet.add-funds', $cdfWallet) }}" class="btn btn-warning flex-fill">
                            <i class="fas fa-plus me-1"></i>Ajouter
                        </a>
                        <a href="{{ route('wallet.withdraw-funds', $cdfWallet) }}" class="btn btn-outline-danger flex-fill">
                            <i class="fas fa-minus me-1"></i>Retirer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte de conversion de devises -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exchange-alt me-2"></i>
                        Convertir entre devises (USD ⇄ CDF)
                    </h5>
                    <small class="mb-0">Convertissez facilement vos dollars en francs congolais et vice-versa</small>
                </div>
                <div class="card-body">
                    <!-- Options de conversion rapide -->
                    <div class="d-flex gap-2 mb-3">
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="quickConvert('USD', 'CDF')">
                            <i class="fas fa-arrow-right me-1"></i> USD → CDF
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="quickConvert('CDF', 'USD')">
                            <i class="fas fa-arrow-right me-1"></i> CDF → USD
                        </button>
                    </div>

                    <form id="conversionForm">
                        @csrf
                        <div class="row g-3">
                            <!-- Source -->
                            <div class="col-md-5">
                                <label class="form-label fw-bold">De</label>
                                <select class="form-select form-select-lg mb-2" id="fromWallet" name="from_wallet_id" required>
                                    <option value="{{ $usdWallet->id }}" data-currency="USD">
                                        USD - ${{ number_format($usdWallet->balance, 2) }}
                                    </option>
                                    <option value="{{ $cdfWallet->id }}" data-currency="CDF">
                                        CDF - {{ number_format($cdfWallet->balance, 2) }} FC
                                    </option>
                                </select>
                                <div class="input-group input-group-lg">
                                    <input type="number" 
                                           class="form-control" 
                                           id="fromAmount" 
                                           name="amount" 
                                           placeholder="0.00" 
                                           step="0.01" 
                                           min="0.01" 
                                           required>
                                    <span class="input-group-text fw-bold" id="fromCurrency">USD</span>
                                </div>
                                <small class="text-muted">
                                    Solde disponible: <span id="fromBalance">${{ number_format($usdWallet->balance, 2) }}</span>
                                </small>
                            </div>

                            <!-- Bouton d'échange -->
                            <div class="col-md-2 d-flex align-items-center justify-content-center">
                                <button type="button" class="btn btn-outline-primary btn-lg rounded-circle" id="swapBtn" style="width: 60px; height: 60px;">
                                    <i class="fas fa-exchange-alt fa-lg"></i>
                                </button>
                            </div>

                            <!-- Destination -->
                            <div class="col-md-5">
                                <label class="form-label fw-bold">Vers</label>
                                <select class="form-select form-select-lg mb-2" id="toWallet" name="to_wallet_id" required>
                                    <option value="{{ $cdfWallet->id }}" data-currency="CDF">
                                        CDF - {{ number_format($cdfWallet->balance, 2) }} FC
                                    </option>
                                    <option value="{{ $usdWallet->id }}" data-currency="USD">
                                        USD - ${{ number_format($usdWallet->balance, 2) }}
                                    </option>
                                </select>
                                <div class="input-group input-group-lg">
                                    <input type="text" 
                                           class="form-control bg-light" 
                                           id="toAmount" 
                                           placeholder="0.00" 
                                           readonly>
                                    <span class="input-group-text fw-bold" id="toCurrency">CDF</span>
                                </div>
                                <small class="text-muted">
                                    Solde actuel: <span id="toBalance">{{ number_format($cdfWallet->balance, 2) }} FC</span>
                                </small>
                            </div>
                        </div>

                        <!-- Taux de change -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <div>
                                            <i class="fas fa-info-circle me-2"></i>
                                            <span>Taux actuel: <strong id="exchangeRate">1 USD = 2,500 CDF</strong></span>
                                        </div>
                                        <div class="d-flex gap-3 mt-2 mt-md-0">
                                            <small class="text-muted">
                                                <i class="fas fa-arrow-right me-1"></i>
                                                <strong>USD → CDF:</strong> × {{ number_format(2500, 0, ',', ' ') }}
                                            </small>
                                            <small class="text-muted">
                                                <i class="fas fa-arrow-left me-1"></i>
                                                <strong>CDF → USD:</strong> ÷ {{ number_format(2500, 0, ',', ' ') }}
                                            </small>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-clock me-1"></i>
                                        Mis à jour il y a quelques instants
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Bouton de conversion -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100" id="convertBtn">
                                    <i class="fas fa-exchange-alt me-2"></i>
                                    Convertir maintenant
                                </button>
                            </div>
                        </div>

                        <!-- Exemples de conversion -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card bg-light border-0">
                                    <div class="card-body py-2">
                                        <small class="text-muted d-block mb-2">
                                            <i class="fas fa-calculator me-1"></i> 
                                            <strong>Exemples de conversion :</strong>
                                        </small>
                                        <div class="row g-2 text-center">
                                            <div class="col-md-3 col-6">
                                                <div class="p-2 bg-white rounded">
                                                    <small class="text-success fw-bold">$1</small>
                                                    <div class="small text-muted">→</div>
                                                    <small class="text-warning fw-bold" id="example1">2,500 FC</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-6">
                                                <div class="p-2 bg-white rounded">
                                                    <small class="text-success fw-bold">$10</small>
                                                    <div class="small text-muted">→</div>
                                                    <small class="text-warning fw-bold" id="example2">25,000 FC</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-6">
                                                <div class="p-2 bg-white rounded">
                                                    <small class="text-warning fw-bold">10,000 FC</small>
                                                    <div class="small text-muted">→</div>
                                                    <small class="text-success fw-bold" id="example3">$4.00</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-6">
                                                <div class="p-2 bg-white rounded">
                                                    <small class="text-warning fw-bold">50,000 FC</small>
                                                    <div class="small text-muted">→</div>
                                                    <small class="text-success fw-bold" id="example4">$20.00</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Messages -->
                        <div id="conversionMessage" class="mt-3" style="display: none;"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions récentes -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history text-info me-2"></i>
                            Transactions récentes
                        </h5>
                        <small class="text-muted">10 dernières transactions</small>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentTransactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Montant</th>
                                        <th>Devise</th>
                                        <th>Solde après</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $transaction)
                                        <tr>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $transaction->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                @if($transaction->type === 'credit')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-plus me-1"></i>Crédit
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-minus me-1"></i>Débit
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $transaction->description }}</td>
                                            <td>
                                                <span class="fw-bold {{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->type === 'credit' ? '+' : '-' }}
                                                    @if($transaction->wallet->currency === 'CDF')
                                                        {{ number_format($transaction->amount, 2, ',', ' ') }} FC
                                                    @else
                                                        ${{ number_format($transaction->amount, 2, '.', ',') }}
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $transaction->wallet->currency === 'USD' ? 'success' : 'warning' }}">
                                                    {{ $transaction->wallet->currency }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    @if($transaction->wallet->currency === 'CDF')
                                                        {{ number_format($transaction->balance_after, 2, ',', ' ') }} FC
                                                    @else
                                                        ${{ number_format($transaction->balance_after, 2, '.', ',') }}
                                                    @endif
                                                </small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune transaction</h5>
                            <p class="text-muted">Vos transactions apparaîtront ici une fois que vous aurez effectué des opérations.</p>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('wallet.add-funds', $usdWallet) }}" class="btn btn-success">
                                    <i class="fas fa-plus me-1"></i>Ajouter des fonds USD
                                </a>
                                <a href="{{ route('wallet.add-funds', $cdfWallet) }}" class="btn btn-warning">
                                    <i class="fas fa-plus me-1"></i>Ajouter des fonds CDF
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Taux de change global
let currentRate = 2500;

// Récupérer le taux de change actuel
async function fetchExchangeRate() {
    try {
        const response = await fetch('{{ route("exchange.rate") }}');
        const data = await response.json();
        if (data.status === 'success') {
            currentRate = data.rate;
            updateRateDisplay();
            updateExamples();
        }
    } catch (error) {
        console.error('Erreur lors de la récupération du taux:', error);
    }
}

// Mettre à jour les exemples de conversion
function updateExamples() {
    document.getElementById('example1').textContent = (1 * currentRate).toLocaleString('fr-FR') + ' FC';
    document.getElementById('example2').textContent = (10 * currentRate).toLocaleString('fr-FR') + ' FC';
    document.getElementById('example3').textContent = '$' + (10000 / currentRate).toFixed(2);
    document.getElementById('example4').textContent = '$' + (50000 / currentRate).toFixed(2);
}

// Mettre à jour l'affichage du taux
function updateRateDisplay() {
    const fromCurrency = document.getElementById('fromWallet').selectedOptions[0].dataset.currency;
    const toCurrency = document.getElementById('toWallet').selectedOptions[0].dataset.currency;
    
    let rateText;
    if (fromCurrency === 'USD' && toCurrency === 'CDF') {
        rateText = `1 USD = ${currentRate.toLocaleString('fr-FR')} CDF`;
    } else if (fromCurrency === 'CDF' && toCurrency === 'USD') {
        rateText = `${currentRate.toLocaleString('fr-FR')} CDF = 1 USD`;
    } else {
        rateText = '1:1 (même devise)';
    }
    
    document.getElementById('exchangeRate').textContent = rateText;
}

// Calculer la conversion
function calculateConversion() {
    const fromAmount = parseFloat(document.getElementById('fromAmount').value) || 0;
    const fromCurrency = document.getElementById('fromWallet').selectedOptions[0].dataset.currency;
    const toCurrency = document.getElementById('toWallet').selectedOptions[0].dataset.currency;
    
    if (fromAmount <= 0) {
        document.getElementById('toAmount').value = '';
        return;
    }
    
    let convertedAmount;
    if (fromCurrency === 'USD' && toCurrency === 'CDF') {
        convertedAmount = fromAmount * currentRate;
    } else if (fromCurrency === 'CDF' && toCurrency === 'USD') {
        convertedAmount = fromAmount / currentRate;
    } else {
        convertedAmount = fromAmount;
    }
    
    document.getElementById('toAmount').value = convertedAmount.toFixed(2);
}

// Échanger les wallets source et destination
function swapWallets() {
    const fromWallet = document.getElementById('fromWallet');
    const toWallet = document.getElementById('toWallet');
    
    // Échanger les valeurs
    const tempValue = fromWallet.value;
    const tempIndex = fromWallet.selectedIndex;
    
    fromWallet.value = toWallet.value;
    fromWallet.selectedIndex = toWallet.selectedIndex;
    
    toWallet.value = tempValue;
    toWallet.selectedIndex = tempIndex;
    
    // Mettre à jour l'interface
    updateCurrencyLabels();
    updateBalanceLabels();
    calculateConversion();
}

// Conversion rapide (boutons USD→CDF et CDF→USD)
function quickConvert(fromCurrency, toCurrency) {
    const fromWallet = document.getElementById('fromWallet');
    const toWallet = document.getElementById('toWallet');
    
    // Trouver les wallets correspondants
    const fromOptions = Array.from(fromWallet.options);
    const toOptions = Array.from(toWallet.options);
    
    const fromOption = fromOptions.find(opt => opt.dataset.currency === fromCurrency);
    const toOption = toOptions.find(opt => opt.dataset.currency === toCurrency);
    
    if (fromOption && toOption) {
        fromWallet.value = fromOption.value;
        toWallet.value = toOption.value;
        
        updateCurrencyLabels();
        updateBalanceLabels();
        calculateConversion();
        
        // Focus sur le champ montant
        document.getElementById('fromAmount').focus();
        
        // Animation de confirmation
        const fromBtn = event.target;
        const originalText = fromBtn.innerHTML;
        fromBtn.innerHTML = '<i class="fas fa-check me-1"></i> Sélectionné !';
        fromBtn.classList.remove('btn-outline-success', 'btn-outline-warning');
        fromBtn.classList.add('btn-success');
        
        setTimeout(() => {
            fromBtn.innerHTML = originalText;
            fromBtn.classList.remove('btn-success');
            fromBtn.classList.add(fromCurrency === 'USD' ? 'btn-outline-success' : 'btn-outline-warning');
        }, 1500);
    }
}

// Mettre à jour les labels de devise
function updateCurrencyLabels() {
    const fromCurrency = document.getElementById('fromWallet').selectedOptions[0].dataset.currency;
    const toCurrency = document.getElementById('toWallet').selectedOptions[0].dataset.currency;
    
    document.getElementById('fromCurrency').textContent = fromCurrency;
    document.getElementById('toCurrency').textContent = toCurrency;
    
    updateRateDisplay();
}

// Mettre à jour les labels de solde
function updateBalanceLabels() {
    const fromWallet = document.getElementById('fromWallet');
    const toWallet = document.getElementById('toWallet');
    
    const fromBalance = fromWallet.selectedOptions[0].text.split(' - ')[1];
    const toBalance = toWallet.selectedOptions[0].text.split(' - ')[1];
    
    document.getElementById('fromBalance').textContent = fromBalance;
    document.getElementById('toBalance').textContent = toBalance;
}

// Soumettre le formulaire de conversion
async function submitConversion(event) {
    event.preventDefault();
    
    const convertBtn = document.getElementById('convertBtn');
    const messageDiv = document.getElementById('conversionMessage');
    const originalBtnText = convertBtn.innerHTML;
    
    // Validation
    const fromAmount = parseFloat(document.getElementById('fromAmount').value);
    if (!fromAmount || fromAmount <= 0) {
        showMessage('error', 'Veuillez entrer un montant valide');
        return;
    }
    
    // Désactiver le bouton
    convertBtn.disabled = true;
    convertBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Conversion en cours...';
    
    try {
        const formData = new FormData(event.target);
        
        const response = await fetch('{{ route("wallet.convert") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                from_wallet_id: formData.get('from_wallet_id'),
                to_wallet_id: formData.get('to_wallet_id'),
                amount: fromAmount
            })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            showMessage('success', `
                <strong>Conversion réussie !</strong><br>
                ${data.amount} ${data.from_currency} → ${data.converted_amount} ${data.to_currency}<br>
                <small>Taux appliqué: ${data.rate.toLocaleString('fr-FR')}</small>
            `);
            
            // Rafraîchir la page après 2 secondes
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showMessage('error', data.message || 'Erreur lors de la conversion');
            convertBtn.disabled = false;
            convertBtn.innerHTML = originalBtnText;
        }
    } catch (error) {
        console.error('Erreur:', error);
        showMessage('error', 'Erreur de connexion au serveur');
        convertBtn.disabled = false;
        convertBtn.innerHTML = originalBtnText;
    }
}

// Afficher un message
function showMessage(type, message) {
    const messageDiv = document.getElementById('conversionMessage');
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
    
    messageDiv.innerHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas fa-${icon} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    messageDiv.style.display = 'block';
}

function refreshBalances() {
    // Animation des soldes pendant le chargement
    const usdBalance = document.getElementById('usd-balance');
    const cdfBalance = document.getElementById('cdf-balance');
    
    // Ajouter un spinner temporaire
    const originalUsd = usdBalance.innerHTML;
    const originalCdf = cdfBalance.innerHTML;
    
    usdBalance.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    cdfBalance.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    // Simuler un délai pour l'effet visuel
    setTimeout(() => {
        location.reload();
    }, 500);
}

// Animation d'entrée pour les cartes
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer le taux de change
    fetchExchangeRate();
    
    // Event listeners
    document.getElementById('fromAmount').addEventListener('input', calculateConversion);
    document.getElementById('fromWallet').addEventListener('change', function() {
        updateCurrencyLabels();
        updateBalanceLabels();
        calculateConversion();
    });
    document.getElementById('toWallet').addEventListener('change', function() {
        updateCurrencyLabels();
        updateBalanceLabels();
        calculateConversion();
    });
    document.getElementById('swapBtn').addEventListener('click', swapWallets);
    document.getElementById('conversionForm').addEventListener('submit', submitConversion);
    
    // Animation des cartes
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endpush
@endsection