@extends('app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <!-- Logo et titre -->
                    <div class="text-center mb-4">
                        <h1 class="h3 mb-3 fw-normal">
                            <i class="fas fa-user-plus text-success me-2"></i>
                            Inscription
                        </h1>
                        <p class="text-muted">Créez votre compte VintApp</p>
                    </div>

                    <!-- Formulaire d'inscription -->
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Nom -->
                        <div class="form-floating mb-3">
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="Votre nom" 
                                   required 
                                   autofocus>
                            <label for="name">
                                <i class="fas fa-user me-2"></i>
                                Nom complet
                            </label>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-floating mb-3">
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="nom@exemple.com" 
                                   required>
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

                        <!-- Téléphone -->
                        <div class="form-floating mb-3">
                            <input type="tel" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone') }}" 
                                   placeholder="+33 6 12 34 56 78" 
                                   required>
                            <label for="phone">
                                <i class="fas fa-phone me-2"></i>
                                Numéro de téléphone
                            </label>
                            @error('phone')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Adresse -->
                        <div class="form-floating mb-3">
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" 
                                      name="address" 
                                      placeholder="Votre adresse complète" 
                                      style="height: 100px" 
                                      required>{{ old('address') }}</textarea>
                            <label for="address">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                Adresse complète
                            </label>
                            @error('address')
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
                            <div class="form-text">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Au moins 8 caractères avec lettres, chiffres et symboles
                                </small>
                            </div>
                        </div>

                        <!-- Confirmation du mot de passe -->
                        <div class="form-floating mb-3">
                            <input type="password" 
                                   class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   placeholder="Confirmer le mot de passe" 
                                   required>
                            <label for="password_confirmation">
                                <i class="fas fa-lock me-2"></i>
                                Confirmer le mot de passe
                            </label>
                            @error('password_confirmation')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Conditions d'utilisation -->
                        <div class="form-check mb-3">
                            <input class="form-check-input @error('terms') is-invalid @enderror" 
                                   type="checkbox" 
                                   name="terms" 
                                   id="terms" 
                                   {{ old('terms') ? 'checked' : '' }} 
                                   required>
                            <label class="form-check-label" for="terms">
                                J'accepte les 
                                <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#termsModal">
                                    conditions d'utilisation
                                </a> 
                                et la 
                                <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#privacyModal">
                                    politique de confidentialité
                                </a>
                            </label>
                            @error('terms')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Newsletter -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="newsletter" 
                                   id="newsletter" 
                                   {{ old('newsletter') ? 'checked' : '' }}>
                            <label class="form-check-label" for="newsletter">
                                Je souhaite recevoir les newsletters et offres spéciales
                            </label>
                        </div>

                        <!-- Bouton d'inscription -->
                        <div class="d-grid mb-3">
                            <button class="btn btn-success btn-lg" type="submit">
                                <i class="fas fa-user-plus me-2"></i>
                                Créer mon compte
                            </button>
                        </div>
                    </form>

                    <!-- Séparateur -->
                    <hr class="my-4">

                    <!-- Connexion -->
                    <div class="text-center">
                        <p class="mb-0">
                            Déjà un compte ? 
                            <a href="{{ route('login') }}" class="text-decoration-none fw-bold">
                                <i class="fas fa-sign-in-alt me-1"></i>
                                Se connecter
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

<!-- Modal Conditions d'utilisation -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">
                    <i class="fas fa-file-contract me-2"></i>
                    Conditions d'utilisation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. Acceptation des conditions</h6>
                <p>En utilisant VintApp, vous acceptez d'être lié par ces conditions d'utilisation.</p>
                
                <h6>2. Utilisation du service</h6>
                <p>Vous vous engagez à utiliser le service de manière légale et éthique.</p>
                
                <h6>3. Responsabilités</h6>
                <p>Vous êtes responsable du contenu que vous publiez et des transactions que vous effectuez.</p>
                
                <h6>4. Confidentialité</h6>
                <p>Nous nous engageons à protéger vos données personnelles conformément à notre politique de confidentialité.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Politique de confidentialité -->
<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="privacyModalLabel">
                    <i class="fas fa-user-shield me-2"></i>
                    Politique de confidentialité
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. Collecte des données</h6>
                <p>Nous collectons uniquement les données nécessaires au fonctionnement du service.</p>
                
                <h6>2. Utilisation des données</h6>
                <p>Vos données sont utilisées pour améliorer votre expérience et sécuriser le service.</p>
                
                <h6>3. Protection des données</h6>
                <p>Nous mettons en place des mesures de sécurité appropriées pour protéger vos données.</p>
                
                <h6>4. Vos droits</h6>
                <p>Vous avez le droit d'accéder, de modifier et de supprimer vos données personnelles.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
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
    background-color: var(--success-color);
    border-color: var(--success-color);
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
    color: var(--success-color);
}

/* Styles pour le focus */
.form-control:focus {
    border-color: var(--success-color);
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

/* Styles pour les icônes */
.fas {
    font-size: 0.875rem;
}

/* Styles pour les modales */
.modal-content {
    border-radius: 1rem;
    border: none;
}

.modal-header {
    border-bottom: 1px solid #dee2e6;
}

.modal-footer {
    border-top: 1px solid #dee2e6;
}

/* Styles responsives */
@media (max-width: 768px) {
    .card-body {
        padding: 2rem !important;
    }
}

/* Styles pour la validation en temps réel */
.form-control.is-valid {
    border-color: var(--success-color);
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.form-control.is-invalid {
    border-color: var(--danger-color);
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

/* Styles pour les tooltips */
.tooltip {
    font-size: 0.875rem;
}

/* Styles pour les progress bars */
.password-strength {
    height: 0.25rem;
    border-radius: 0.125rem;
    margin-top: 0.5rem;
}

.password-strength.weak {
    background-color: var(--danger-color);
}

.password-strength.medium {
    background-color: var(--warning-color);
}

.password-strength.strong {
    background-color: var(--success-color);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation en temps réel du mot de passe
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    
    function validatePassword() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        // Vérifier la force du mot de passe
        const hasLetter = /[a-zA-Z]/.test(password);
        const hasNumber = /\d/.test(password);
        const hasSymbol = /[!@#$%^&*(),.?":{}|<>]/.test(password);
        const isLongEnough = password.length >= 8;
        
        if (password && confirmPassword && password !== confirmPassword) {
            confirmPasswordInput.setCustomValidity('Les mots de passe ne correspondent pas');
        } else {
            confirmPasswordInput.setCustomValidity('');
        }
        
        // Afficher les critères de force
        const criteria = document.querySelectorAll('.password-criteria');
        criteria.forEach(criterion => {
            const type = criterion.dataset.type;
            let isValid = false;
            
            switch(type) {
                case 'length':
                    isValid = isLongEnough;
                    break;
                case 'letter':
                    isValid = hasLetter;
                    break;
                case 'number':
                    isValid = hasNumber;
                    break;
                case 'symbol':
                    isValid = hasSymbol;
                    break;
            }
            
            criterion.classList.toggle('text-success', isValid);
            criterion.classList.toggle('text-muted', !isValid);
        });
    }
    
    passwordInput.addEventListener('input', validatePassword);
    confirmPasswordInput.addEventListener('input', validatePassword);
    
    // Validation du formulaire
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>
@endsection 