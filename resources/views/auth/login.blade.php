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
        position: relative;
        overflow: hidden;
    }
    
    .btn-firebase:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
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
            <div class="pulse-dot bg-primary rounded-circle me-2" style="width: 12px; height: 12px;"></div>
            <div class="pulse-dot bg-primary rounded-circle me-2" style="width: 12px; height: 12px; animation-delay: 0.2s;"></div>
            <div class="pulse-dot bg-primary rounded-circle" style="width: 12px; height: 12px; animation-delay: 0.4s;"></div>
        </div>
        <p class="text-primary fw-medium">Connexion en cours...</p>
    </div>
</div>

<div class="container-fluid">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg border-0 firebase-auth-form">
                <div class="card-body p-5">
                    <!-- Logo et titre -->
                    <div class="text-center mb-4">
                        <h1 class="h3 mb-3 fw-normal">
                            <i class="fas fa-user-circle text-primary me-2"></i>
                            Connexion
                        </h1>
                        <p class="text-muted">Connectez-vous à votre compte VintApp</p>
                    </div>

                    <!-- Connexion Firebase avec Email/Password -->
                    <div class="mb-4">
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
                            <input type="password" 
                                   class="form-control" 
                                   id="firebase-password" 
                                   placeholder="Mot de passe" 
                                   required>
                            <label for="firebase-password">
                                <i class="fas fa-lock me-2"></i>
                                Mot de passe
                            </label>
                        </div>

                        <!-- Se souvenir de moi -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="remember-me">
                            <label class="form-check-label" for="remember-me">
                                Se souvenir de moi
                            </label>
                        </div>

                        <!-- Bouton Firebase Login -->
                        <div class="d-grid mb-3">
                            <button onclick="signInWithFirebaseEmail()" class="btn btn-primary btn-lg btn-firebase">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Se connecter
                            </button>
                        </div>

                        <!-- Liens utiles -->
                        <div class="text-center mb-4">
                            <a href="#" onclick="showForgotPassword()" class="text-decoration-none">
                                <i class="fas fa-key me-1"></i>
                                Mot de passe oublié ?
                            </a>
                        </div>
                    </div>

                    <!-- Séparateur -->
                    <div class="position-relative my-4">
                        <hr class="m-0">
                        <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">OU</span>
                    </div>

                    <!-- Connexion avec Google Firebase -->
                    <div class="d-grid mb-3">
                        <button onclick="signInWithGoogle()" class="btn btn-light btn-lg border d-flex align-items-center justify-content-center btn-firebase" style="background-color: white;">
                            <svg width="18" height="18" viewBox="0 0 18 18" class="me-2" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z" fill="#4285F4"/>
                                <path d="M9.003 18c2.43 0 4.467-.806 5.956-2.18L12.05 13.56c-.806.54-1.836.86-3.047.86-2.344 0-4.328-1.584-5.036-3.711H.96v2.332C2.44 15.983 5.485 18 9.003 18z" fill="#34A853"/>
                                <path d="M3.964 10.712c-.18-.54-.282-1.117-.282-1.71 0-.593.102-1.17.282-1.71V4.96H.957C.347 6.175 0 7.55 0 9.002c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/>
                                <path d="M9.003 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.464.891 11.426 0 9.002 0 5.485 0 2.44 2.017.96 4.958L3.967 7.29c.708-2.127 2.692-3.71 5.036-3.71z" fill="#EA4335"/>
                            </svg>
                            <span style="color: #3c4043; font-weight: 500;">Google</span>
                        </button>
                    </div>

                    <!-- Connexion avec Facebook Firebase -->
                    <div class="d-grid mb-3">
                        <button onclick="signInWithFacebook()" class="btn btn-primary btn-lg btn-firebase" style="background-color: #1877f2; border-color: #1877f2;">
                            <i class="fab fa-facebook-f me-2"></i>
                            Facebook
                        </button>
                    </div>

                    <!-- Connexion avec Apple Firebase (masqué si non configuré) -->
                    <div class="d-grid mb-3" id="apple-signin-container" style="display: none;">
                        <button onclick="signInWithApple()" class="btn btn-dark btn-lg btn-firebase">
                            <i class="fab fa-apple me-2"></i>
                            Apple
                        </button>
                    </div>

                    <!-- Message si Apple non configuré -->
                    <div class="d-grid mb-3" id="apple-not-configured">
                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="fab fa-apple me-2"></i>
                            <div>
                                <strong>Apple Sign-In</strong> nécessite un Apple Developer Account (99$/an).
                                <a href="#" onclick="showAppleInfo()" class="alert-link">En savoir plus</a>
                            </div>
                        </div>
                    </div>

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

                    <!-- Fallback vers connexion Laravel classique -->
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Problème de connexion ? 
                            <a href="#" onclick="showClassicLogin()" class="text-decoration-none">
                                Utiliser la connexion classique
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
<div id="classic-login-modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Connexion classique</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" required>
                        <label>Email</label>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               name="password" required>
                        <label>Mot de passe</label>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Firebase SDK et JavaScript -->
<script type="module">
import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js';
import { 
    getAuth, 
    signInWithEmailAndPassword,
    signInWithPopup,
    signInWithRedirect,
    getRedirectResult,
    GoogleAuthProvider,
    FacebookAuthProvider,
    OAuthProvider,
    sendPasswordResetEmail,
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

// Debug configuration
console.log('🔥 Configuration Firebase complète:', firebaseConfig);

// Validation critique de la configuration
const configProblems = [];
if (!firebaseConfig.apiKey || firebaseConfig.apiKey === 'YOUR_API_KEY_HERE') {
    configProblems.push('API Key invalide');
}
if (!firebaseConfig.appId || firebaseConfig.appId.includes('YOUR_APP_ID_HERE')) {
    configProblems.push('APP ID manquant (YOUR_APP_ID_HERE)');
}

if (configProblems.length > 0) {
    console.error('❌ ERREUR CONFIGURATION FIREBASE:', configProblems);
    alert('ERREUR: Configuration Firebase incomplète\n\n' + configProblems.join('\n') + 
          '\n\nVeuillez configurer Firebase dans votre Console Firebase.');
}

// Validation de la configuration
if (!firebaseConfig.apiKey || !firebaseConfig.projectId) {
    console.error('Configuration Firebase incomplète!');
    showToast('Configuration Firebase manquante. Contactez l\'administrateur.', 'error');
}

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

// Ajouter des scopes spécifiques pour Google
googleProvider.addScope('email');
googleProvider.addScope('profile');

const facebookProvider = new FacebookAuthProvider();

// Apple provider
const appleProvider = new OAuthProvider('apple.com');
appleProvider.addScope('email');
appleProvider.addScope('name');
appleProvider.setCustomParameters({
    // Optionnel: forcer l'affichage de l'interface de connexion
    locale: 'fr'
});

// Debug des providers
console.log('🔧 Google Provider configuré:', {
    providerId: googleProvider.providerId,
    customParameters: googleProvider.customParameters
});

// Fonctions globales
window.signInWithFirebaseEmail = async () => {
    const email = document.getElementById('firebase-email').value.trim();
    const password = document.getElementById('firebase-password').value;
    
    if (!email || !password) {
        showToast('Veuillez remplir tous les champs', 'error');
        return;
    }
    
    if (!validateEmail(email)) {
        showToast('Adresse email invalide', 'error');
        return;
    }
    
    showLoading(true);
    
    try {
        console.log('Tentative de connexion Firebase pour:', email);
        const userCredential = await signInWithEmailAndPassword(auth, email, password);
        console.log('Connexion Firebase réussie:', userCredential.user);
        await handleFirebaseAuth(userCredential.user);
    } catch (error) {
        console.error('Erreur Firebase complète:', error);
        console.error('Code erreur:', error.code);
        console.error('Message erreur:', error.message);
        
        let message = 'Erreur de connexion Firebase';
        
        switch (error.code) {
            case 'auth/user-not-found':
                message = 'Aucun compte Firebase trouvé avec cet email. Vous devez d\'abord créer un compte Firebase.';
                break;
            case 'auth/wrong-password':
                message = 'Mot de passe Firebase incorrect';
                break;
            case 'auth/invalid-email':
                message = 'Adresse email invalide';
                break;
            case 'auth/user-disabled':
                message = 'Ce compte Firebase a été désactivé';
                break;
            case 'auth/too-many-requests':
                message = 'Trop de tentatives. Réessayez plus tard';
                break;
            case 'auth/invalid-api-key':
                message = 'Clé API Firebase invalide. Configuration incorrecte.';
                break;
            case 'auth/app-not-authorized':
                message = 'Application non autorisée pour ce projet Firebase';
                break;
            default:
                message = `Erreur Firebase: ${error.code} - ${error.message}`;
        }
        
        showToast(message, 'error');
        showLoading(false);
    }
};

window.signInWithGoogle = async () => {
    console.log('🔍 Tentative de connexion Google...');
    console.log('🔧 Configuration du provider Google:', googleProvider);
    console.log('🔥 État de Firebase Auth:', {
        currentUser: auth.currentUser,
        config: {
            apiKey: auth.config.apiKey.substring(0, 10) + '...',
            authDomain: auth.config.authDomain,
            projectId: auth.config.projectId
        }
    });
    
    // Vérification pré-connexion
    if (!auth.config.apiKey || !auth.config.authDomain) {
        showToast('Configuration Firebase incomplète', 'error');
        return;
    }
    
    showLoading(true);
    
    try {
        console.log('🚀 Ouverture du popup Google Auth...');
        let result;
        
        try {
            result = await signInWithPopup(auth, googleProvider);
        } catch (popupError) {
            if (popupError.code === 'auth/unauthorized-domain' || popupError.code === 'auth/popup-blocked') {
                console.log('🔄 Pop-up échoué, essai avec redirect...');
                await signInWithRedirect(auth, googleProvider);
                return; // La redirection va gérer la suite
            }
            throw popupError; // Re-throw other errors
        }
        
        console.log('✅ Connexion Google réussie:', result.user);
        console.log('👤 Utilisateur:', {
            uid: result.user.uid,
            email: result.user.email,
            displayName: result.user.displayName,
            photoURL: result.user.photoURL
        });
        
        await handleFirebaseAuth(result.user);
    } catch (error) {
        console.error('❌ Erreur Google Auth complète:', error);
        console.error('📝 Code erreur:', error.code);
        console.error('💬 Message erreur:', error.message);
        console.error('🔗 Détails additionnels:', error.customData);
        
        let message = 'Erreur de connexion Google';
        
        switch (error.code) {
            case 'auth/popup-closed-by-user':
                message = 'Connexion Google annulée par l\'utilisateur';
                break;
            case 'auth/popup-blocked':
                message = 'Pop-up bloqué. Veuillez autoriser les pop-ups pour ce site';
                break;
            case 'auth/cancelled-popup-request':
                message = 'Une autre demande de connexion est en cours';
                break;
            case 'auth/operation-not-allowed':
                message = 'Connexion Google désactivée. Vérifiez la configuration Firebase';
                break;
            case 'auth/unauthorized-domain':
                message = 'Domaine non autorisé pour Google Auth. Vérifiez les domaines autorisés dans Firebase';
                break;
            case 'auth/web-storage-unsupported':
                message = 'Stockage web non supporté par ce navigateur';
                break;
            case 'auth/network-request-failed':
                message = 'Erreur réseau. Vérifiez votre connexion internet';
                break;
            case 'auth/too-many-requests':
                message = 'Trop de tentatives. Réessayez plus tard';
                break;
            default:
                message = `Erreur Google Auth: ${error.code} - ${error.message}`;
        }
        
        showToast(message, 'error');
        showLoading(false);
    }
};

window.signInWithFacebook = async () => {
    showLoading(true);
    
    try {
        const result = await signInWithPopup(auth, facebookProvider);
        await handleFirebaseAuth(result.user);
    } catch (error) {
        console.error('Erreur Facebook Auth:', error);
        
        let message = 'Erreur de connexion Facebook';
        if (error.code === 'auth/popup-closed-by-user') {
            message = 'Connexion annulée';
        } else if (error.code === 'auth/account-exists-with-different-credential') {
            message = 'Un compte existe déjà avec cet email via un autre service';
        }
        
        showToast(message, 'error');
        showLoading(false);
    }
};

window.signInWithApple = async () => {
    console.log('🍎 Tentative de connexion Apple...');
    showLoading(true);
    
    try {
        let result;
        
        try {
            result = await signInWithPopup(auth, appleProvider);
        } catch (popupError) {
            if (popupError.code === 'auth/popup-blocked' || popupError.code === 'auth/unauthorized-domain') {
                console.log('🔄 Pop-up Apple échoué, essai avec redirect...');
                await signInWithRedirect(auth, appleProvider);
                return;
            }
            throw popupError;
        }
        
        console.log('✅ Connexion Apple réussie:', result.user);
        await handleFirebaseAuth(result.user);
        
    } catch (error) {
        console.error('❌ Erreur Apple Auth:', error);
        
        let message = 'Erreur de connexion Apple';
        
        switch (error.code) {
            case 'auth/popup-closed-by-user':
                message = 'Connexion Apple annulée';
                break;
            case 'auth/operation-not-allowed':
                message = 'Connexion Apple désactivée. Vérifiez la configuration Firebase';
                break;
            case 'auth/unauthorized-domain':
                message = 'Domaine non autorisé pour Apple Auth';
                break;
            case 'auth/account-exists-with-different-credential':
                message = 'Un compte existe déjà avec cet email via un autre service';
                break;
            default:
                message = `Erreur Apple Auth: ${error.code} - ${error.message}`;
        }
        
        showToast(message, 'error');
        showLoading(false);
    }
};

window.showAppleInfo = () => {
    const message = `
        <strong>Configuration Apple Sign-In :</strong><br><br>
        
        <strong>Prérequis :</strong><br>
        • Apple Developer Account (99$/an)<br>
        • Configuration App ID et Service ID<br>
        • Clés privées Apple<br><br>
        
        <strong>Guide complet :</strong> Consultez APPLE_SIGNIN_COMPLETE_GUIDE.md<br><br>
        
        <strong>Alternatives gratuites :</strong><br>
        • Google OAuth ✅<br>
        • Facebook Login ✅<br>
        • Email/Mot de passe ✅
    `;
    
    showToast(message, 'info');
};

window.checkAppleAuthAvailability = async () => {
    // Tenter de créer le provider Apple pour voir s'il est configuré
    try {
        // Test simple - si ça passe, Apple est potentiellement configuré
        const testProvider = new OAuthProvider('apple.com');
        
        // Vérifier via une tentative de connexion factice (qui échouera mais nous dira si c'est configuré)
        // Pour l'instant, on masque Apple par défaut
        document.getElementById('apple-signin-container').style.display = 'none';
        document.getElementById('apple-not-configured').style.display = 'block';
        
        console.log('🍎 Apple Auth: Non configuré (normal sans Apple Developer Account)');
        
    } catch (error) {
        document.getElementById('apple-signin-container').style.display = 'none';
        document.getElementById('apple-not-configured').style.display = 'block';
        console.log('🍎 Apple Auth: Non disponible');
    }
};

window.showForgotPassword = async () => {
    const email = document.getElementById('firebase-email').value.trim();
    
    if (!email) {
        showToast('Veuillez saisir votre email d\'abord', 'warning');
        document.getElementById('firebase-email').focus();
        return;
    }
    
    if (!validateEmail(email)) {
        showToast('Adresse email invalide', 'error');
        return;
    }
    
    try {
        await sendPasswordResetEmail(auth, email);
        showToast('Email de réinitialisation envoyé !', 'success');
    } catch (error) {
        console.error('Erreur reset password:', error);
        showToast('Erreur lors de l\'envoi de l\'email', 'error');
    }
};

window.showClassicLogin = () => {
    const modal = new bootstrap.Modal(document.getElementById('classic-login-modal'));
    modal.show();
};

// Gestion de l'authentification Firebase
async function handleFirebaseAuth(user) {
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
        const response = await fetch('{{ route("firebase.login") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                idToken: idToken,
                fcmToken: fcmToken
            })
        });
        
        if (!response.ok) {
            throw new Error('Erreur serveur: ' + response.status);
        }
        
        const data = await response.json();
        
        console.log('Réponse serveur:', data);
        
        if (data.success) {
            showToast('Connexion réussie ! Redirection...', 'success');
            
            // Sauvegarder le "se souvenir de moi" dans localStorage
            const rememberMe = document.getElementById('remember-me').checked;
            if (rememberMe) {
                localStorage.setItem('firebase_remember_me', 'true');
            }
            
            setTimeout(() => {
                window.location.href = data.redirect || '{{ route("home") }}';
            }, 1500);
        } else {
            console.error('Erreur serveur:', data);
            throw new Error(data.message || 'Erreur de connexion côté serveur');
        }
    } catch (error) {
        console.error('Erreur handleFirebaseAuth:', error);
        showToast(error.message || 'Erreur lors de l\'authentification', 'error');
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

// Gestion des événements DOM
document.addEventListener('DOMContentLoaded', async () => {
    // Vérifier la disponibilité d'Apple Auth
    checkAppleAuthAvailability();
    
    // Vérifier s'il y a un résultat de redirection Google
    try {
        const result = await getRedirectResult(auth);
        if (result && result.user) {
            console.log('✅ Connexion Google par redirect réussie:', result.user);
            showLoading(true);
            await handleFirebaseAuth(result.user);
        }
    } catch (error) {
        console.error('❌ Erreur redirect result:', error);
        if (error.code !== 'auth/no-auth-result') {
            showToast(`Erreur de connexion Google: ${error.message}`, 'error');
        }
    }
    
    // Récupérer l'état "se souvenir de moi"
    if (localStorage.getItem('firebase_remember_me') === 'true') {
        document.getElementById('remember-me').checked = true;
    }
    
    // Soumettre le formulaire avec Entrée
    ['firebase-email', 'firebase-password'].forEach(id => {
        document.getElementById(id).addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                signInWithFirebaseEmail();
            }
        });
    });
    
    // Validation en temps réel de l'email
    document.getElementById('firebase-email').addEventListener('blur', (e) => {
        const email = e.target.value.trim();
        if (email && !validateEmail(email)) {
            e.target.classList.add('is-invalid');
            if (!e.target.nextElementSibling || !e.target.nextElementSibling.classList.contains('invalid-feedback')) {
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = 'Adresse email invalide';
                e.target.parentNode.appendChild(feedback);
            }
        } else {
            e.target.classList.remove('is-invalid');
            const feedback = e.target.parentNode.querySelector('.invalid-feedback');
            if (feedback) feedback.remove();
        }
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

@endsection 