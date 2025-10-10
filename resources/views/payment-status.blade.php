@extends('app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg">
                <div class="card-body text-center py-5">
                    <!-- Icône animée de chargement -->
                    <div id="payment-icon" class="mb-4">
                        <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>

                    <!-- Statut du paiement -->
                    <h3 id="payment-title" class="mb-3">Paiement en cours...</h3>
                    <p id="payment-message" class="text-muted mb-4">
                        Nous attendons la confirmation de votre paiement Mobile Money.
                        Cela peut prendre quelques secondes.
                    </p>

                    <!-- Informations de la transaction -->
                    <div id="transaction-info" class="alert alert-info mb-4">
                        <div class="row text-start">
                            <div class="col-6"><strong>Montant:</strong></div>
                            <div class="col-6" id="amount">{{ $transaction->amount ?? '...' }} {{ $transaction->currency ?? 'USD' }}</div>
                            
                            <div class="col-6 mt-2"><strong>Opérateur:</strong></div>
                            <div class="col-6 mt-2" id="provider">{{ ucfirst(str_replace('_', ' ', $transaction->provider ?? '...')) }}</div>
                            
                            <div class="col-6 mt-2"><strong>Téléphone:</strong></div>
                            <div class="col-6 mt-2" id="phone">{{ $transaction->phone_number ?? '...' }}</div>
                            
                            <div class="col-6 mt-2"><strong>Transaction ID:</strong></div>
                            <div class="col-6 mt-2" id="transaction-id">{{ $transaction->id ?? '...' }}</div>
                        </div>
                    </div>

                    <!-- Barre de progression -->
                    <div class="progress mb-4" style="height: 8px;">
                        <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" style="width: 30%"></div>
                    </div>

                    <!-- Instructions -->
                    <div id="payment-instructions" class="alert alert-warning">
                        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Instructions importantes</h6>
                        <ul class="text-start mb-0 small">
                            <li>Vérifiez votre téléphone pour la demande de paiement</li>
                            <li>Entrez votre code PIN Mobile Money</li>
                            <li>Confirmez le paiement</li>
                            <li>Ne fermez pas cette page</li>
                        </ul>
                    </div>

                    <!-- Actions -->
                    <div id="payment-actions" class="mt-4">
                        <button onclick="checkStatus()" class="btn btn-outline-primary me-2">
                            <i class="fas fa-sync-alt me-2"></i>Actualiser
                        </button>
                        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                    </div>
                </div>
            </div>

            <!-- Informations supplémentaires -->
            <div class="card mt-4 border-0">
                <div class="card-body">
                    <h6 class="text-muted mb-3"><i class="fas fa-question-circle me-2"></i>Questions fréquentes</h6>
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item border-0">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Combien de temps prend le paiement ?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Le paiement prend généralement entre 10 et 60 secondes. Si après 2 minutes vous n'avez rien reçu, contactez le support.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item border-0">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Je n'ai pas reçu la demande de paiement
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Vérifiez que votre téléphone a du réseau et que vous avez suffisamment de solde. Si le problème persiste, réessayez ou contactez le support.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Le paiement a échoué
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Vérifiez votre solde, votre code PIN et réessayez. Aucun montant ne sera débité en cas d'échec.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes pulse-icon {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.success-icon {
    animation: pulse-icon 2s ease-in-out infinite;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    75% { transform: translateX(10px); }
}

.error-icon {
    animation: shake 0.5s ease-in-out;
}
</style>

<script>
const transactionId = {{ $transaction->id ?? 'null' }};
let pollingInterval = null;
let pollingCount = 0;
const maxPollingAttempts = 120; // 2 minutes (120 * 1 seconde)

// Démarrer le polling automatique
document.addEventListener('DOMContentLoaded', function() {
    if (transactionId) {
        startPolling();
    }
});

function startPolling() {
    pollingCount = 0;
    pollingInterval = setInterval(() => {
        checkStatus();
        pollingCount++;

        // Arrêter après le nombre maximum de tentatives
        if (pollingCount >= maxPollingAttempts) {
            stopPolling();
            showTimeout();
        }
    }, 1000); // Vérifier toutes les secondes
}

function stopPolling() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
}

async function checkStatus() {
    if (!transactionId) return;

    try {
        const response = await fetch(`/api/payment-callbacks/status?transaction_id=${transactionId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();

        if (data.status === 'success' && data.transaction) {
            updateUI(data.transaction);
        }
    } catch (error) {
        console.error('Erreur lors de la vérification du statut:', error);
    }
}

function updateUI(transaction) {
    const status = transaction.status;
    const icon = document.getElementById('payment-icon');
    const title = document.getElementById('payment-title');
    const message = document.getElementById('payment-message');
    const instructions = document.getElementById('payment-instructions');
    const actions = document.getElementById('payment-actions');
    const progressBar = document.getElementById('progress-bar');

    switch (status) {
        case 'completed':
            stopPolling();
            icon.innerHTML = '<i class="fas fa-check-circle text-success success-icon" style="font-size: 4rem;"></i>';
            title.textContent = 'Paiement réussi !';
            title.className = 'mb-3 text-success';
            message.textContent = 'Votre paiement a été traité avec succès. Vous allez être redirigé...';
            instructions.classList.add('d-none');
            progressBar.style.width = '100%';
            progressBar.classList.remove('progress-bar-animated');
            progressBar.classList.add('bg-success');
            
            actions.innerHTML = '<a href="/dashboard" class="btn btn-success"><i class="fas fa-home me-2"></i>Retour au tableau de bord</a>';
            
            // Rediriger automatiquement après 3 secondes
            setTimeout(() => {
                window.location.href = '/payments/success?transaction_id=' + transactionId;
            }, 3000);
            break;

        case 'failed':
            stopPolling();
            icon.innerHTML = '<i class="fas fa-times-circle text-danger error-icon" style="font-size: 4rem;"></i>';
            title.textContent = 'Paiement échoué';
            title.className = 'mb-3 text-danger';
            message.textContent = 'Votre paiement n\'a pas pu être traité. Veuillez réessayer.';
            instructions.classList.add('d-none');
            progressBar.style.width = '100%';
            progressBar.classList.remove('progress-bar-animated');
            progressBar.classList.add('bg-danger');
            
            actions.innerHTML = `
                <a href="/payments" class="btn btn-primary me-2"><i class="fas fa-redo me-2"></i>Réessayer</a>
                <a href="/dashboard" class="btn btn-outline-secondary"><i class="fas fa-home me-2"></i>Tableau de bord</a>
            `;
            break;

        case 'cancelled':
            stopPolling();
            icon.innerHTML = '<i class="fas fa-ban text-warning" style="font-size: 4rem;"></i>';
            title.textContent = 'Paiement annulé';
            title.className = 'mb-3 text-warning';
            message.textContent = 'Vous avez annulé le paiement.';
            instructions.classList.add('d-none');
            progressBar.style.width = '100%';
            progressBar.classList.remove('progress-bar-animated');
            progressBar.classList.add('bg-warning');
            
            actions.innerHTML = `
                <a href="/payments" class="btn btn-primary me-2"><i class="fas fa-redo me-2"></i>Réessayer</a>
                <a href="/dashboard" class="btn btn-outline-secondary"><i class="fas fa-home me-2"></i>Tableau de bord</a>
            `;
            break;

        case 'pending':
            // Mettre à jour la barre de progression
            const progress = Math.min(30 + (pollingCount / maxPollingAttempts) * 60, 90);
            progressBar.style.width = progress + '%';
            break;
    }
}

function showTimeout() {
    const icon = document.getElementById('payment-icon');
    const title = document.getElementById('payment-title');
    const message = document.getElementById('payment-message');
    const instructions = document.getElementById('payment-instructions');
    const actions = document.getElementById('payment-actions');

    icon.innerHTML = '<i class="fas fa-clock text-warning" style="font-size: 4rem;"></i>';
    title.textContent = 'Temps d\'attente écoulé';
    title.className = 'mb-3 text-warning';
    message.textContent = 'La vérification du paiement prend plus de temps que prévu. Vérifiez votre historique de transactions ou contactez le support.';
    instructions.classList.add('d-none');
    
    actions.innerHTML = `
        <button onclick="location.reload()" class="btn btn-primary me-2"><i class="fas fa-sync-alt me-2"></i>Réessayer</button>
        <a href="/dashboard" class="btn btn-outline-secondary"><i class="fas fa-home me-2"></i>Tableau de bord</a>
        <a href="/support" class="btn btn-outline-info"><i class="fas fa-headset me-2"></i>Support</a>
    `;
}

// Nettoyer le polling quand on quitte la page
window.addEventListener('beforeunload', function() {
    stopPolling();
});
</script>
@endsection
