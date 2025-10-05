@extends('app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-5">
                    <div class="success-animation mb-4">
                        <div class="checkmark">
                            <i class="fas fa-check-circle fa-5x text-success"></i>
                        </div>
                    </div>
                    
                    <h2 class="mb-4 text-success">Paiement réussi!</h2>
                    
                    @if(isset($transaction))
                        <div class="transaction-details bg-light rounded-3 p-4 mb-4 text-start">
                            <h5 class="border-bottom pb-2 mb-3">Détails de la transaction</h5>
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <strong>Référence :</strong>
                                    <span class="ms-2">{{ $transaction->reference }}</span>
                                </li>
                                <li class="mb-3">
                                    <strong>Montant :</strong>
                                    <span class="ms-2">{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}</span>
                                </li>
                                <li class="mb-3">
                                    <strong>Date :</strong>
                                    <span class="ms-2">{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
                                </li>
                                <li class="mb-3">
                                    <strong>Opérateur :</strong>
                                    <span class="ms-2">{{ $transaction->provider }}</span>
                                </li>
                                <li>
                                    <strong>Statut :</strong>
                                    <span class="ms-2 badge bg-success">Confirmé</span>
                                </li>
                            </ul>
                        </div>
                    @endif
                    
                    <div class="text-center">
                        <p class="text-muted mb-4">
                            Un reçu de paiement a été envoyé à votre adresse email.
                        </p>
                        
                        <div class="d-grid gap-2">
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                <i class="fas fa-home me-2"></i>Retour au tableau de bord
                            </a>
                            @if(isset($transaction))
                                <a href="#" class="btn btn-outline-primary" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i>Imprimer le reçu
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.success-animation {
    animation: scale-in 0.5s ease-out;
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

@keyframes checkmark {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

@media print {
    .btn {
        display: none !important;
    }
}
</style>
@endsection