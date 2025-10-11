@extends('app')

@section('content')
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center py-5 px-3" style="background: linear-gradient(135deg, #f0f4ff 0%, #ffffff 50%, #faf5ff 100%);">
    <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
        <!-- Card principale -->
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Header avec icône -->
            <div class="card-header text-white text-center py-4 border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-envelope-open-text fa-3x text-primary"></i>
                </div>
                <h1 class="h3 fw-bold mb-2">Vérifiez votre email</h1>
                <p class="mb-0 opacity-90 small">Dernière étape avant d'accéder à VintApp</p>
            </div>

            <!-- Contenu -->
            <div class="card-body p-4 p-md-5">
                <!-- ⚠️ Message d'avertissement FORT -->
                <div class="alert alert-danger border-danger shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-lock fa-2x text-danger me-3"></i>
                        </div>
                        <div>
                            <h5 class="alert-heading fw-bold mb-2">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Accès Restreint
                            </h5>
                            <p class="mb-2 fw-semibold">
                                Votre compte est actuellement <span class="text-danger">suspendu</span> en attente de vérification d'email.
                            </p>
                            <p class="mb-0 small">
                                <i class="fas fa-ban me-1"></i>
                                Vous ne pouvez pas accéder aux fonctionnalités tant que votre email n'est pas vérifié.
                            </p>
                        </div>
                    </div>
                </div>

                @if (session('status') == 'verification-link-sent')
                    <!-- Message de succès -->
                    <div class="alert alert-success d-flex align-items-start mb-4" role="alert">
                        <i class="fas fa-check-circle fs-4 me-3 mt-1"></i>
                        <div>
                            <h6 class="alert-heading mb-1">Email envoyé !</h6>
                            <p class="mb-0 small">
                                Un nouveau lien de vérification a été envoyé à votre adresse email.
                            </p>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success d-flex align-items-start mb-4" role="alert">
                        <i class="fas fa-check-circle fs-4 me-3 mt-1"></i>
                        <div>
                            <p class="mb-0 fw-semibold">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-warning d-flex align-items-start mb-4" role="alert">
                        <i class="fas fa-exclamation-triangle fs-4 me-3 mt-1"></i>
                        <div>
                            <p class="mb-0 fw-semibold">{{ session('warning') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Message d'information -->
                <div class="text-center mb-4">
                    <p class="text-dark mb-3">
                        Merci de vous être inscrit ! Avant de commencer, pourriez-vous vérifier votre adresse email en cliquant sur le lien que nous venons de vous envoyer ?
                    </p>
                    <p class="text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        Vérifiez votre boîte de réception et vos spams
                    </p>
                </div>

                <!-- Email utilisateur -->
                <div class="bg-light rounded-3 p-3 mb-4">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <i class="fas fa-at text-primary"></i>
                        <span class="fw-semibold text-dark">{{ Auth::user()->email }}</span>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="mb-4">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10" style="width: 36px; height: 36px; min-width: 36px;">
                            <span class="text-primary fw-bold small">1</span>
                        </div>
                        <p class="mb-0 text-muted small pt-1">Ouvrez votre boîte email</p>
                    </div>
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10" style="width: 36px; height: 36px; min-width: 36px;">
                            <span class="text-primary fw-bold small">2</span>
                        </div>
                        <p class="mb-0 text-muted small pt-1">Cliquez sur le lien de vérification</p>
                    </div>
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10" style="width: 36px; height: 36px; min-width: 36px;">
                            <span class="text-primary fw-bold small">3</span>
                        </div>
                        <p class="mb-0 text-muted small pt-1">Revenez sur VintApp pour commencer</p>
                    </div>
                </div>

                <!-- Bouton renvoyer -->
                <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center gap-2 shadow-sm">
                        <i class="fas fa-paper-plane"></i>
                        <span>Renvoyer l'email de vérification</span>
                    </button>
                </form>

                <!-- Lien déconnexion -->
                <div class="text-center">
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link text-muted text-decoration-none small">
                            <i class="fas fa-sign-out-alt me-1"></i>
                            Se déconnecter
                        </button>
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <div class="card-footer bg-light border-top py-3">
                <div class="d-flex align-items-center justify-content-center gap-2 text-muted small">
                    <i class="fas fa-shield-alt text-success"></i>
                    <span>Vos données sont sécurisées et confidentielles</span>
                </div>
            </div>
        </div>

        <!-- Aide supplémentaire -->
        <div class="text-center mt-4">
            <p class="small text-muted">
                Vous n'avez pas reçu l'email ? 
                <a href="mailto:{{ config('mail.from.address') }}" class="text-primary text-decoration-none fw-medium">
                    Contactez le support
                </a>
            </p>
        </div>
    </div>
</div>

<style>
    .btn-primary {
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4) !important;
    }

    .card {
        transition: transform 0.3s ease;
    }

    @media (max-width: 768px) {
        .card-body {
            padding: 1.5rem !important;
        }
    }
</style>
@endsection
