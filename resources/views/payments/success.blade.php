@extends('app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0 success-card">
                <div class="card-body text-center p-5">
                    <!-- Animation de confettis -->
                    <div class="confetti-container"></div>
                    
                    <!-- Icône de succès avec animation -->
                    <div class="success-animation mb-4">
                        <div class="checkmark-circle">
                            <div class="checkmark">
                                <i class="fas fa-check-circle fa-5x text-success"></i>
                            </div>
                        </div>
                    </div>
                    
                    <h2 class="mb-3 text-success fw-bold">Paiement Réussi !</h2>
                    <p class="text-muted mb-4">Votre transaction a été traitée avec succès</p>
                    
                    @if(isset($transaction))
                        <!-- Montant en grand -->
                        <div class="amount-highlight mb-4">
                            <div class="display-4 fw-bold text-primary">
                                {{ number_format($transaction->amount, 2) }} {{ $transaction->currency ?? 'USD' }}
                            </div>
                            @if(isset($transaction->currency))
                                @if($transaction->currency === 'USD')
                                    <div class="text-muted">
                                        Environ {{ number_format($transaction->amount * 2650, 0) }} CDF
                                    </div>
                                @elseif($transaction->currency === 'CDF')
                                    <div class="text-muted">
                                        Environ {{ number_format($transaction->amount / 2650, 2) }} USD
                                    </div>
                                @endif
                            @else
                                <div class="text-muted">
                                    {{ number_format($transaction->amount * 2650, 0) }} CDF
                                </div>
                            @endif
                        </div>
                        
                        <!-- Détails de la transaction -->
                        <div class="transaction-details bg-light rounded-3 p-4 mb-4 text-start">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-receipt me-2"></i>Détails de la transaction
                            </h5>
                            <div class="detail-row">
                                <span class="detail-label">ID Transaction</span>
                                <span class="detail-value text-primary">{{ $transaction->transaction_id }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Opérateur</span>
                                <span class="detail-value">
                                    <i class="fas fa-mobile-alt me-1"></i>{{ $transaction->provider }}
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Téléphone</span>
                                <span class="detail-value">{{ $transaction->phone }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Date</span>
                                <span class="detail-value">
                                    <i class="far fa-calendar me-1"></i>{{ $transaction->created_at->format('d/m/Y à H:i') }}
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Objet</span>
                                <span class="detail-value">{{ $transaction->purpose }}</span>
                            </div>
                            <div class="detail-row border-0">
                                <span class="detail-label">Statut</span>
                                <span class="detail-value">
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Confirmé
                                    </span>
                                </span>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Boutons d'action -->
                    <div class="d-grid gap-3 mt-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-home me-2"></i>Retour au Dashboard
                        </a>
                        @if(isset($transaction))
                            <button class="btn btn-outline-primary" onclick="window.print()">
                                <i class="fas fa-download me-2"></i>Télécharger le Reçu
                            </button>
                        @endif
                    </div>
                    
                    <!-- Message de confirmation -->
                    <div class="mt-4 p-3 bg-success bg-opacity-10 rounded-3">
                        <small class="text-muted">
                            <i class="fas fa-envelope me-1"></i>
                            Un reçu de paiement a été envoyé à votre adresse email.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Animation de la carte */
.success-card {
    animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Animation du checkmark */
.success-animation {
    animation: scale-in 0.5s ease-out;
}

.checkmark-circle {
    animation: pulse 1s ease-in-out;
}

.checkmark {
    animation: checkmark 0.8s ease-in-out;
}

@keyframes scale-in {
    0% {
        transform: scale(0.5);
        opacity: 0;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

@keyframes checkmark {
    0% {
        transform: scale(0) rotate(-45deg);
        opacity: 0;
    }
    50% {
        transform: scale(1.2) rotate(0deg);
    }
    100% {
        transform: scale(1) rotate(0deg);
        opacity: 1;
    }
}

/* Confettis */
.confetti {
    position: fixed;
    width: 10px;
    height: 10px;
    z-index: 9999;
    pointer-events: none;
}

/* Highlight du montant */
.amount-highlight {
    animation: fadeIn 0.8s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Détails de transaction */
.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e9ecef;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    color: #6c757d;
    font-weight: 500;
}

.detail-value {
    color: #212529;
    font-weight: 600;
    text-align: right;
}

/* Impression */
@media print {
    .btn, .confetti-container {
        display: none !important;
    }
    .success-card {
        box-shadow: none !important;
    }
}
</style>

<script>
// Animation de confettis
function createConfetti() {
    const colors = ['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff', '#00ffff', '#ff6b6b', '#4ecdc4'];
    const container = document.querySelector('.confetti-container');
    
    for (let i = 0; i < 50; i++) {
        setTimeout(() => {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.top = '-10px';
            confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.animation = `confetti-fall ${2 + Math.random() * 2}s linear forwards`;
            confetti.style.animationDelay = Math.random() * 0.5 + 's';
            container.appendChild(confetti);
            
            setTimeout(() => confetti.remove(), 4000);
        }, i * 30);
    }
}

// Keyframes pour l'animation des confettis
const style = document.createElement('style');
style.textContent = `
    @keyframes confetti-fall {
        to {
            transform: translateY(100vh) rotate(${Math.random() * 360}deg);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Lancer les confettis au chargement
window.addEventListener('load', createConfetti);
</script>
@endsection