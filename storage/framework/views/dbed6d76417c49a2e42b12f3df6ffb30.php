

<?php $__env->startSection('content'); ?>
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
                    <form method="POST" action="<?php echo e(route('register')); ?>">
                        <?php echo csrf_field(); ?>

                        <!-- Nom -->
                        <div class="form-floating mb-3">
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="name" 
                                   name="name" 
                                   value="<?php echo e(old('name')); ?>" 
                                   placeholder="Votre nom" 
                                   required 
                                   autofocus>
                            <label for="name">
                                <i class="fas fa-user me-2"></i>
                                Nom complet
                            </label>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback">
                                    <?php echo e($message); ?>

                                </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Email -->
                        <div class="form-floating mb-3">
                            <input type="email" 
                                   class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="email" 
                                   name="email" 
                                   value="<?php echo e(old('email')); ?>" 
                                   placeholder="nom@exemple.com" 
                                   required>
                            <label for="email">
                                <i class="fas fa-envelope me-2"></i>
                                Adresse email
                            </label>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback">
                                    <?php echo e($message); ?>

                                </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Téléphone -->
                        <div class="form-floating mb-3">
                            <input type="tel" 
                                   class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="phone" 
                                   name="phone" 
                                   value="<?php echo e(old('phone')); ?>" 
                                   placeholder="+33 6 12 34 56 78" 
                                   required>
                            <label for="phone">
                                <i class="fas fa-phone me-2"></i>
                                Numéro de téléphone
                            </label>
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback">
                                    <?php echo e($message); ?>

                                </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Adresse -->
                        <div class="form-floating mb-3">
                            <textarea class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                      id="address" 
                                      name="address" 
                                      placeholder="Votre adresse complète" 
                                      style="height: 100px" 
                                      required><?php echo e(old('address')); ?></textarea>
                            <label for="address">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                Adresse complète
                            </label>
                            <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback">
                                    <?php echo e($message); ?>

                                </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Mot de passe -->
                        <div class="form-floating mb-3">
                            <input type="password" 
                                   class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Mot de passe" 
                                   required>
                            <label for="password">
                                <i class="fas fa-lock me-2"></i>
                                Mot de passe
                            </label>
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback">
                                    <?php echo e($message); ?>

                                </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                                   class="form-control <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   placeholder="Confirmer le mot de passe" 
                                   required>
                            <label for="password_confirmation">
                                <i class="fas fa-lock me-2"></i>
                                Confirmer le mot de passe
                            </label>
                            <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback">
                                    <?php echo e($message); ?>

                                </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Conditions d'utilisation -->
                        <div class="form-check mb-3">
                            <input class="form-check-input <?php $__errorArgs = ['terms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   type="checkbox" 
                                   name="terms" 
                                   id="terms" 
                                   <?php echo e(old('terms') ? 'checked' : ''); ?> 
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
                            <?php $__errorArgs = ['terms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback">
                                    <?php echo e($message); ?>

                                </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Newsletter -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="newsletter" 
                                   id="newsletter" 
                                   <?php echo e(old('newsletter') ? 'checked' : ''); ?>>
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
                    <div class="position-relative my-4">
                        <hr class="m-0">
                        <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">OU</span>
                    </div>

                    <!-- Inscription avec Google -->
                    <div class="d-grid mb-3">
                        <a href="<?php echo e(route('auth.google')); ?>" class="btn btn-light btn-lg border d-flex align-items-center justify-content-center" style="background-color: white;">
                            <svg width="18" height="18" viewBox="0 0 18 18" class="me-2" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z" fill="#4285F4"/>
                                <path d="M9.003 18c2.43 0 4.467-.806 5.956-2.18L12.05 13.56c-.806.54-1.836.86-3.047.86-2.344 0-4.328-1.584-5.036-3.711H.96v2.332C2.44 15.983 5.485 18 9.003 18z" fill="#34A853"/>
                                <path d="M3.964 10.712c-.18-.54-.282-1.117-.282-1.71 0-.593.102-1.17.282-1.71V4.96H.957C.347 6.175 0 7.55 0 9.002c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/>
                                <path d="M9.003 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.464.891 11.426 0 9.002 0 5.485 0 2.44 2.017.96 4.958L3.967 7.29c.708-2.127 2.692-3.71 5.036-3.71z" fill="#EA4335"/>
                            </svg>
                            <span style="color: #3c4043; font-weight: 500;">S'inscrire avec Google</span>
                        </a>
                    </div>

                    <!-- Inscription avec Apple -->
                    <div class="d-grid mb-3">
                        <a href="<?php echo e(route('auth.apple')); ?>" class="btn btn-dark btn-lg d-flex align-items-center justify-content-center" style="background-color: #000000;">
                            <svg width="18" height="18" viewBox="0 0 24 24" class="me-2" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z" fill="white"/>
                            </svg>
                            <span style="color: white; font-weight: 500;">S'inscrire avec Apple</span>
                        </a>
                    </div>

                    <!-- Séparateur -->
                    <hr class="my-4">

                    <!-- Connexion -->
                    <div class="text-center">
                        <p class="mb-0">
                            Déjà un compte ? 
                            <a href="<?php echo e(route('login')); ?>" class="text-decoration-none fw-bold">
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
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/auth/register.blade.php ENDPATH**/ ?>