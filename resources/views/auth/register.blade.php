@extends('app')

@push('styles')
<style>
    .firebase-auth-form {
        position: relative;
    }
    
    .loading-overlay {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(4px);
    }
    
    .pulse-dot {
        animation: pulse 1.5s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(0.8); opacity: 0.5; }
        50% { transform: scale(1.2); opacity: 1; }
    }
    
    .btn-firebase {
        transition: all 0.3s ease;
    }
    
    .btn-firebase:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .password-strength {
        height: 4px;
        border-radius: 2px;
        margin-top: 8px;
        transition: all 0.3s ease;
    }
    
    .password-strength.weak { background: #dc3545; }
    .password-strength.medium { background: #ffc107; }
    .password-strength.strong { background: #28a745; }
    
    .toast-notification {
        animation: slideInRight 0.3s ease-out;
    }
    
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
</style>
@endpush

@section('content')

<!-- Toast Notifications Container -->
<div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="position-fixed top-0 start-0 w-100 h-100 loading-overlay d-none justify-content-center align-items-center" style="z-index: 9998;">
    <div class="text-center">
        <div class="d-flex justify-content-center mb-3">
            <div class="pulse-dot bg-success rounded-circle me-2" style="width: 12px; height: 12px;"></div>
            <div class="pulse-dot bg-success rounded-circle me-2" style="width: 12px; height: 12px; animation-delay: 0.2s;"></div>
            <div class="pulse-dot bg-success rounded-circle" style="width: 12px; height: 12px; animation-delay: 0.4s;"></div>
        </div>
        <p class="text-success fw-medium">Inscription en cours...</p>
    </div>
</div>

<div class="container-fluid">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 firebase-auth-form">
                <div class="card-body p-5">
                    <!-- Logo et titre -->
                    <div class="text-center mb-4">
                        <h1 class="h3 mb-3 fw-normal">
                            <i class="fas fa-user-plus text-success me-2"></i>
                            Inscription
                        </h1>
                        <p class="text-muted">Créez votre compte VintApp</p>
                    </div>

                    <!-- Inscription Firebase avec Email/Password -->
                    <div class="mb-4">
                        <div class="form-floating mb-3">
                            <input type="text" 
                                   class="form-control" 
                                   id="firebase-name" 
                                   placeholder="Votre nom" 
                                   required>
                            <label for="firebase-name">
                                <i class="fas fa-user me-2"></i>
                                Nom complet
                            </label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="email" 
                                   class="form-control" 
                                   id="firebase-email" 
                                   placeholder="nom@exemple.com" 
                                   required>
                            <label for="firebase-email">
                                <i class="fas fa-envelope me-2"></i>
                                Adresse email
                            </label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="tel" 
                                   class="form-control" 
                                   id="firebase-phone" 
                                   placeholder="+33 6 12 34 56 78">
                            <label for="firebase-phone">
                                <i class="fas fa-phone me-2"></i>
                                Téléphone (optionnel)
                            </label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" 
                                   class="form-control" 
                                   id="firebase-password" 
                                   placeholder="Mot de passe" 
                                   required>
                            <label for="firebase-password">
                                <i class="fas fa-lock me-2"></i>
                                Mot de passe
                            </label>
                            <div id="password-strength" class="password-strength"></div>
                            <div class="form-text">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Au moins 6 caractères
                                </small>
                            </div>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" 
                                   class="form-control" 
                                   id="firebase-password-confirm" 
                                   placeholder="Confirmer le mot de passe" 
                                   required>
                            <label for="firebase-password-confirm">
                                <i class="fas fa-lock me-2"></i>
                                Confirmer le mot de passe
                            </label>
                        </div>

                        <!-- Conditions d'utilisation -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="firebase-terms" 
                                   required>
                            <label class="form-check-label" for="firebase-terms">
                                J'accepte les 
                                <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#termsModal">
                                    conditions d'utilisation
                                </a> 
                                et la 
                                <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#privacyModal">
                                    politique de confidentialité
                                </a>
                            </label>
                        </div>

                        <!-- Newsletter -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="firebase-newsletter">
                            <label class="form-check-label" for="firebase-newsletter">
                                Je souhaite recevoir les newsletters et offres spéciales
                            </label>
                        </div>

                        <!-- Bouton Firebase Register -->
                        <div class="d-grid mb-3">
                            <button onclick="registerWithFirebaseEmail()" class="btn btn-success btn-lg btn-firebase">
                                <i class="fas fa-user-plus me-2"></i>
                                Créer mon compte Firebase
                            </button>
                        </div>
                    </div>

                    <!-- Séparateur -->
                    <div class="position-relative my-4">
                        <hr class="m-0">
                        <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">OU</span>
                    </div>

                    <!-- Inscription avec Google Firebase -->
                    <div class="d-grid mb-3">
                        <button onclick="signUpWithGoogle()" class="btn btn-light btn-lg border d-flex align-items-center justify-content-center btn-firebase" style="background-color: white;">
                            <svg width="18" height="18" viewBox="0 0 18 18" class="me-2" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z" fill="#4285F4"/>
                                <path d="M9.003 18c2.43 0 4.467-.806 5.956-2.18L12.05 13.56c-.806.54-1.836.86-3.047.86-2.344 0-4.328-1.584-5.036-3.711H.96v2.332C2.44 15.983 5.485 18 9.003 18z" fill="#34A853"/>
                                <path d="M3.964 10.712c-.18-.54-.282-1.117-.282-1.71 0-.593.102-1.17.282-1.71V4.96H.957C.347 6.175 0 7.55 0 9.002c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/>
                                <path d="M9.003 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.464.891 11.426 0 9.002 0 5.485 0 2.44 2.017.96 4.958L3.967 7.29c.708-2.127 2.692-3.71 5.036-3.71z" fill="#EA4335"/>
                            </svg>
                            <span style="color: #3c4043; font-weight: 500;">Google</span>
                        </button>
                    </div>

                    <!-- Inscription avec Facebook Firebase -->
                    <div class="d-grid mb-3">
                        <button onclick="signUpWithFacebook()" class="btn btn-primary btn-lg btn-firebase" style="background-color: #1877f2; border-color: #1877f2;">
                            <i class="fab fa-facebook-f me-2"></i>
                            Facebook
                        </button>
                    </div>

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

                    <!-- Fallback vers inscription Laravel classique -->
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Problème d'inscription ? 
                            <a href="#" onclick="showClassicRegister()" class="text-decoration-none">
                                Utiliser l'inscription classique
                            </a>
                        </small>
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

<!-- Formulaire Laravel classique (caché par défaut) -->
<div id="classic-register-modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Inscription classique</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" required>
                                <label>Nom complet</label>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" required>
                                <label>Email</label>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                               name="phone" value="{{ old('phone') }}" required>
                        <label>Téléphone</label>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  name="address" style="height: 80px" required>{{ old('address') }}</textarea>
                        <label>Adresse</label>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       name="password" required>
                                <label>Mot de passe</label>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                       name="password_confirmation" required>
                                <label>Confirmer mot de passe</label>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="terms" required>
                        <label class="form-check-label">
                            J'accepte les conditions d'utilisation
                        </label>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Créer le compte</button>
                </form>
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

<!-- Firebase SDK et JavaScript -->
<script type="module">
import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js';
import { 
    getAuth, 
    createUserWithEmailAndPassword,
    signInWithPopup, 
    GoogleAuthProvider,
    FacebookAuthProvider,
    updateProfile,
    onAuthStateChanged
} from 'https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js';
import { 
    getMessaging, 
    getToken 
} from 'https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js';

// Configuration Firebase
const firebaseConfig = {
    apiKey: "{{ config('firebase.web_config.apiKey') }}",
    authDomain: "{{ config('firebase.web_config.authDomain') }}",
    projectId: "{{ config('firebase.web_config.projectId') }}",
    storageBucket: "{{ config('firebase.web_config.storageBucket') }}",
    messagingSenderId: "{{ config('firebase.web_config.messagingSenderId') }}",
    appId: "{{ config('firebase.web_config.appId') }}"
};

// Initialisation Firebase
const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
let messaging = null;

// Initialiser FCM si supporté
try {
    messaging = getMessaging(app);
} catch (error) {
    console.warn('FCM non supporté:', error);
}

// Providers
const googleProvider = new GoogleAuthProvider();
googleProvider.setCustomParameters({
    prompt: 'select_account'
});

const facebookProvider = new FacebookAuthProvider();

// Fonctions globales
window.registerWithFirebaseEmail = async () => {
    const name = document.getElementById('firebase-name').value.trim();
    const email = document.getElementById('firebase-email').value.trim();
    const password = document.getElementById('firebase-password').value;
    const confirmPassword = document.getElementById('firebase-password-confirm').value;
    const phone = document.getElementById('firebase-phone').value.trim();
    const termsAccepted = document.getElementById('firebase-terms').checked;
    const newsletter = document.getElementById('firebase-newsletter').checked;
    
    // Validation
    if (!name || !email || !password || !confirmPassword) {
        showToast('Veuillez remplir tous les champs obligatoires', 'error');
        return;
    }
    
    if (!validateEmail(email)) {
        showToast('Adresse email invalide', 'error');
        return;
    }
    
    if (password !== confirmPassword) {
        showToast('Les mots de passe ne correspondent pas', 'error');
        return;
    }
    
    if (password.length < 6) {
        showToast('Le mot de passe doit contenir au moins 6 caractères', 'error');
        return;
    }
    
    if (!termsAccepted) {
        showToast('Vous devez accepter les conditions d\'utilisation', 'error');
        return;
    }
    
    showLoading(true);
    
    try {
        const userCredential = await createUserWithEmailAndPassword(auth, email, password);
        
        // Mettre à jour le profil avec le nom
        if (name) {
            await updateProfile(userCredential.user, { displayName: name });
        }
        
        await handleFirebaseAuth(userCredential.user, { phone, newsletter });
    } catch (error) {
        console.error('Erreur inscription email:', error);
        let message = 'Erreur lors de l\'inscription';
        
        switch (error.code) {
            case 'auth/email-already-in-use':
                message = 'Cet email est déjà utilisé';
                break;
            case 'auth/invalid-email':
                message = 'Adresse email invalide';
                break;
            case 'auth/operation-not-allowed':
                message = 'Inscription par email désactivée';
                break;
            case 'auth/weak-password':
                message = 'Mot de passe trop faible';
                break;
            default:
                message = 'Erreur lors de l\'inscription. Réessayez plus tard';
        }
        
        showToast(message, 'error');
        showLoading(false);
    }
};

window.signUpWithGoogle = async () => {
    showLoading(true);
    
    try {
        const result = await signInWithPopup(auth, googleProvider);
        await handleFirebaseAuth(result.user, {
            newsletter: document.getElementById('firebase-newsletter').checked
        });
    } catch (error) {
        console.error('Erreur Google Auth:', error);
        
        let message = 'Erreur d\'inscription Google';
        if (error.code === 'auth/popup-closed-by-user') {
            message = 'Inscription annulée';
        } else if (error.code === 'auth/popup-blocked') {
            message = 'Pop-up bloqué. Autorisez les pop-ups pour ce site';
        } else if (error.code === 'auth/account-exists-with-different-credential') {
            message = 'Un compte existe déjà avec cet email';
        }
        
        showToast(message, 'error');
        showLoading(false);
    }
};

window.signUpWithFacebook = async () => {
    showLoading(true);
    
    try {
        const result = await signInWithPopup(auth, facebookProvider);
        await handleFirebaseAuth(result.user, {
            newsletter: document.getElementById('firebase-newsletter').checked
        });
    } catch (error) {
        console.error('Erreur Facebook Auth:', error);
        
        let message = 'Erreur d\'inscription Facebook';
        if (error.code === 'auth/popup-closed-by-user') {
            message = 'Inscription annulée';
        } else if (error.code === 'auth/account-exists-with-different-credential') {
            message = 'Un compte existe déjà avec cet email via un autre service';
        }
        
        showToast(message, 'error');
        showLoading(false);
    }
};

window.showClassicRegister = () => {
    const modal = new bootstrap.Modal(document.getElementById('classic-register-modal'));
    modal.show();
};

// Gestion de l'authentification Firebase
async function handleFirebaseAuth(user, additionalData = {}) {
    try {
        const idToken = await user.getIdToken();
        
        // Obtenir le token FCM si disponible
        let fcmToken = null;
        if (messaging) {
            try {
                const vapidKey = "{{ config('firebase.messaging.vapid_key', '') }}";
                if (vapidKey) {
                    fcmToken = await getToken(messaging, { vapidKey });
                }
            } catch (fcmError) {
                console.warn('Token FCM non disponible:', fcmError);
            }
        }
        
        // Envoyer au serveur Laravel
        const response = await fetch('{{ route("firebase.register") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                idToken: idToken,
                fcmToken: fcmToken,
                name: user.displayName,
                phone: additionalData.phone || '',
                newsletter: additionalData.newsletter || false
            })
        });
        
        if (!response.ok) {
            throw new Error('Erreur serveur: ' + response.status);
        }
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Inscription réussie ! Redirection...', 'success');
            setTimeout(() => {
                window.location.href = data.redirect || '{{ route("home") }}';
            }, 1500);
        } else {
            throw new Error(data.message || 'Erreur lors de l\'inscription');
        }
    } catch (error) {
        console.error('Erreur handleFirebaseAuth:', error);
        showToast(error.message || 'Erreur lors de l\'inscription', 'error');
        showLoading(false);
    }
}

// Fonctions utilitaires
function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function showLoading(show) {
    const overlay = document.getElementById('loading-overlay');
    if (show) {
        overlay.classList.remove('d-none');
        overlay.classList.add('d-flex');
    } else {
        overlay.classList.add('d-none');
        overlay.classList.remove('d-flex');
    }
}

function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    
    const toastId = 'toast-' + Date.now();
    const bgClass = {
        success: 'bg-success',
        error: 'bg-danger', 
        warning: 'bg-warning',
        info: 'bg-info'
    }[type] || 'bg-info';
    
    const icon = {
        success: '✅',
        error: '❌',
        warning: '⚠️',
        info: 'ℹ️'
    }[type] || 'ℹ️';
    
    const toastHTML = `
        <div id="${toastId}" class="toast toast-notification ${bgClass} text-white" role="alert">
            <div class="toast-header ${bgClass} text-white border-0">
                <span class="me-2">${icon}</span>
                <strong class="me-auto">VintApp</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', toastHTML);
    
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: type === 'success' ? 3000 : 5000
    });
    
    toast.show();
    
    // Nettoyer après fermeture
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}

function checkPasswordStrength(password) {
    const strengthBar = document.getElementById('password-strength');
    
    if (!password) {
        strengthBar.style.width = '0%';
        strengthBar.className = 'password-strength';
        return;
    }
    
    let score = 0;
    
    // Longueur
    if (password.length >= 6) score += 1;
    if (password.length >= 8) score += 1;
    
    // Complexité
    if (/[a-z]/.test(password)) score += 1;
    if (/[A-Z]/.test(password)) score += 1;
    if (/[0-9]/.test(password)) score += 1;
    if (/[^a-zA-Z0-9]/.test(password)) score += 1;
    
    const percentage = (score / 6) * 100;
    strengthBar.style.width = percentage + '%';
    
    if (score <= 2) {
        strengthBar.className = 'password-strength weak';
    } else if (score <= 4) {
        strengthBar.className = 'password-strength medium';
    } else {
        strengthBar.className = 'password-strength strong';
    }
}

// Gestion des événements DOM
document.addEventListener('DOMContentLoaded', () => {
    // Validation en temps réel
    document.getElementById('firebase-email').addEventListener('blur', (e) => {
        const email = e.target.value.trim();
        if (email && !validateEmail(email)) {
            e.target.classList.add('is-invalid');
        } else {
            e.target.classList.remove('is-invalid');
        }
    });
    
    // Force du mot de passe
    document.getElementById('firebase-password').addEventListener('input', (e) => {
        checkPasswordStrength(e.target.value);
    });
    
    // Confirmation du mot de passe
    document.getElementById('firebase-password-confirm').addEventListener('input', (e) => {
        const password = document.getElementById('firebase-password').value;
        const confirm = e.target.value;
        
        if (confirm && password !== confirm) {
            e.target.classList.add('is-invalid');
        } else {
            e.target.classList.remove('is-invalid');
        }
    });
    
    // Soumettre le formulaire avec Entrée
    ['firebase-name', 'firebase-email', 'firebase-password', 'firebase-password-confirm'].forEach(id => {
        document.getElementById(id).addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                registerWithFirebaseEmail();
            }
        });
    });
});

// Écouter les changements d'état d'authentification
onAuthStateChanged(auth, (user) => {
    if (user) {
        // Utilisateur déjà connecté, vérifier avec le serveur
        fetch('{{ route("firebase.check-auth") }}')
            .then(response => response.json())
            .then(data => {
                if (data.authenticated) {
                    showToast('Vous êtes déjà connecté', 'info');
                    setTimeout(() => {
                        window.location.href = '{{ route("home") }}';
                    }, 1000);
                }
            })
            .catch(console.error);
    }
});
</script>

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

@endsection 