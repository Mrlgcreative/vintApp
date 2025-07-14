@extends('app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <!-- Logo et titre -->
                    <div class="text-center mb-4">
                        <h1 class="h3 mb-3 fw-normal">
                            <i class="fas fa-user-circle text-primary me-2"></i>
                            Connexion
                        </h1>
                        <p class="text-muted">Connectez-vous à votre compte VintApp</p>
                    </div>

                    <!-- Formulaire de connexion -->
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email -->
                        <div class="form-floating mb-3">
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="nom@exemple.com" 
                                   required 
                                   autofocus>
                            <label for="email">
                                <i class="fas fa-envelope me-2"></i>
                                Adresse email
                            </label>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Mot de passe -->
                        <div class="form-floating mb-3">
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Mot de passe" 
                                   required>
                            <label for="password">
                                <i class="fas fa-lock me-2"></i>
                                Mot de passe
                            </label>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Se souvenir de moi -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="remember" 
                                   id="remember" 
                                   {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Se souvenir de moi
                            </label>
                        </div>

                        <!-- Bouton de connexion -->
                        <div class="d-grid mb-3">
                            <button class="btn btn-primary btn-lg" type="submit">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Se connecter
                            </button>
                        </div>

                        <!-- Liens utiles -->
                        <div class="text-center">
                            @if (Route::has('password.request'))
                                <a class="text-decoration-none" href="{{ route('password.request') }}">
                                    <i class="fas fa-key me-1"></i>
                                    Mot de passe oublié ?
                                </a>
                            @endif
                        </div>
                    </form>

                    <!-- Séparateur -->
                    <hr class="my-4">

                    <!-- Inscription -->
                    <div class="text-center">
                        <p class="mb-0">
                            Pas encore de compte ? 
                            <a href="{{ route('register') }}" class="text-decoration-none fw-bold">
                                <i class="fas fa-user-plus me-1"></i>
                                S'inscrire
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Informations supplémentaires -->
            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="fas fa-shield-alt me-1"></i>
                    Vos données sont protégées et sécurisées
                </small>
            </div>
        </div>
    </div>
</div>

<style>
.min-vh-100 {
    min-height: 100vh;
}

.card {
    border-radius: 1rem;
}

.form-floating > .form-control {
    border-radius: 0.5rem;
}

.btn-lg {
    border-radius: 0.5rem;
    padding: 0.75rem 1.5rem;
}

.form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

/* Animation d'entrée */
.card {
    animation: slideInUp 0.5s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Styles pour les erreurs */
.invalid-feedback {
    font-size: 0.875rem;
}

/* Styles pour les liens */
a:hover {
    color: var(--primary-color);
}

/* Styles pour le focus */
.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Styles pour les icônes */
.fas {
    font-size: 0.875rem;
}

/* Styles responsives */
@media (max-width: 768px) {
    .card-body {
        padding: 2rem !important;
    }
}
</style>
@endsection 