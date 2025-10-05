@extends('app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-5">
                    <div class="error-animation mb-4">
                        <div class="error-icon">
                            <i class="fas fa-exclamation-circle fa-5x text-danger"></i>
                        </div>
                    </div>
                    
                    <h2 class="mb-4 text-danger">Erreur de paiement</h2>
                    
                    <div class="error-details bg-light rounded-3 p-4 mb-4 text-start">
                        <h5 class="border-bottom pb-2 mb-3">Détails de l'erreur</h5>
                        
                        @if(isset($error))
                            <div class="alert alert-danger">
                                {{ $error }}
                            </div>
                        @endif

                        @if(isset($errorDetails))
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <strong>Code d'erreur :</strong>
                                    <span class="ms-2">{{ $errorDetails['code'] ?? 'N/A' }}</span>
                                </li>
                                <li class="mb-3">
                                    <strong>Message :</strong>
                                    <span class="ms-2">{{ $errorDetails['message'] ?? 'Une erreur inattendue s\'est produite' }}</span>
                                </li>
                                @if(isset($errorDetails['transaction_id']))
                                    <li>
                                        <strong>ID Transaction :</strong>
                                        <span class="ms-2">{{ $errorDetails['transaction_id'] }}</span>
                                    </li>
                                @endif
                            </ul>
                        @endif
                    </div>
                    
                    <div class="text-center">
                        <p class="text-muted mb-4">
                            Nous nous excusons pour ce désagrément. Veuillez réessayer ou contacter notre support si le problème persiste.
                        </p>
                        
                        <div class="d-grid gap-2">
                            <a href="{{ url()->previous() }}" class="btn btn-primary">
                                <i class="fas fa-redo me-2"></i>Réessayer
                            </a>
                            <a href="{{ route('support') }}" class="btn btn-outline-primary">
                                <i class="fas fa-headset me-2"></i>Contacter le support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.error-animation {
    animation: shake 0.5s ease-in-out;
}

.error-icon {
    animation: pulse 2s infinite;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

@keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
    100% { transform: scale(1); opacity: 1; }
}
</style>
@endsection