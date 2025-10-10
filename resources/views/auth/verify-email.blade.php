@extends('app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center align-items-center min-vh-100 bg-light">
        <div class="col-md-8 col-lg-6 col-xl-5">
            <!-- Card principale -->
            <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
                <!-- Header avec icône -->
                <div class="card-header bg-gradient text-white text-center py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-envelope-open-text fa-3x text-primary"></i>
                    </div>
                    <h1 class="h3 fw-bold mb-2">Vérifiez votre email</h1>
                    <p class="mb-0 opacity-90 small">Dernière étape avant d'accéder à VintApp</p>
                </div>

                <!-- Contenu -->
                <div class="card-body p-4 p-md-5">
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
                            <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <span class="text-primary fw-bold small">1</span>
                            </div>
                            <p class="mb-0 small text-muted">Ouvrez votre boîte email</p>
                        </div>
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <span class="text-primary fw-bold small">2</span>
                            </div>
                            <p class="mb-0 small text-muted">Cliquez sur le lien de vérification</p>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <span class="text-primary fw-bold small">3</span>
                            </div>
                            <p class="mb-0 small text-muted">Revenez sur VintApp pour commencer</p>
                        </div>
                    </div>

                    <!-- Bouton renvoyer -->
                    <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center gap-2">
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
</div>

<style>
.bg-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.btn-primary {
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

@media (max-width: 768px) {
    .card-body {
        padding: 1.5rem !important;
    }
}
</style>
@endsection
<div class="min-h-screen bg-gradient-to-br from-primary-50 via-white to-purple-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <!-- Card principale -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header avec icône -->
            <div class="bg-gradient-to-r from-primary-600 to-purple-600 px-6 py-8 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full mb-4">
                    <i class="fas fa-envelope-open-text text-4xl text-primary-600"></i>
                </div>
                <h1 class="text-2xl font-bold text-white">Vérifiez votre email</h1>
                <p class="text-primary-100 text-sm mt-2">Dernière étape avant d'accéder à VintApp</p>
            </div>

            <!-- Contenu -->
            <div class="px-6 py-8">
                @if (session('status') == 'verification-link-sent')
                    <!-- Message de succès -->
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 text-xl mt-0.5 mr-3"></i>
                            <div>
                                <p class="text-green-800 font-semibold">Email envoyé !</p>
                                <p class="text-green-700 text-sm mt-1">
                                    Un nouveau lien de vérification a été envoyé à votre adresse email.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Message d'information -->
                <div class="text-center mb-6">
                    <p class="text-gray-700 mb-2">
                        Merci de vous être inscrit ! Avant de commencer, pourriez-vous vérifier votre adresse email en cliquant sur le lien que nous venons de vous envoyer ?
                    </p>
                    <p class="text-gray-600 text-sm">
                        <i class="fas fa-info-circle mr-1"></i>
                        Vérifiez votre boîte de réception et vos spams
                    </p>
                </div>

                <!-- Email utilisateur -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-center gap-2">
                        <i class="fas fa-at text-primary-600"></i>
                        <span class="font-semibold text-gray-900">{{ Auth::user()->email }}</span>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="space-y-3 mb-6">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center">
                            <span class="text-primary-600 text-sm font-bold">1</span>
                        </div>
                        <p class="text-sm text-gray-600">Ouvrez votre boîte email</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center">
                            <span class="text-primary-600 text-sm font-bold">2</span>
                        </div>
                        <p class="text-sm text-gray-600">Cliquez sur le lien de vérification</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center">
                            <span class="text-primary-600 text-sm font-bold">3</span>
                        </div>
                        <p class="text-sm text-gray-600">Revenez sur VintApp pour commencer</p>
                    </div>
                </div>

                <!-- Bouton renvoyer -->
                <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
                    @csrf
                    <button type="submit" 
                            class="w-full px-6 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors shadow-md hover:shadow-lg">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Renvoyer l'email de vérification
                    </button>
                </form>

                <!-- Lien déconnexion -->
                <div class="text-center">
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 underline">
                            <i class="fas fa-sign-out-alt mr-1"></i>
                            Se déconnecter
                        </button>
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-shield-alt text-green-600"></i>
                    <span>Vos données sont sécurisées et confidentielles</span>
                </div>
            </div>
        </div>

        <!-- Aide supplémentaire -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Vous n'avez pas reçu l'email ? 
                <a href="#" class="text-primary-600 hover:text-primary-700 font-medium">
                    Contactez le support
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
