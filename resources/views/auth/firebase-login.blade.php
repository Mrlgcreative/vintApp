@extends('app')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    * { font-family: 'Inter', sans-serif; }
    
    .auth-bg { 
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }
    
    .glass-effect { 
        backdrop-filter: blur(16px); 
        background: rgba(255, 255, 255, 0.1); 
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
    }
    
    .btn-firebase { 
        background: linear-gradient(135deg, #4285f4, #34a853); 
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px 0 rgba(66, 133, 244, 0.3);
    }
    
    .btn-firebase:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 8px 25px rgba(66, 133, 244, 0.4);
    }
    
    .btn-facebook { 
        background: linear-gradient(135deg, #1877f2, #166fe5); 
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px 0 rgba(24, 119, 242, 0.3);
    }
    
    .btn-facebook:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 8px 25px rgba(24, 119, 242, 0.4);
    }
    
    .input-glass {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
    }
    
    .input-glass:focus {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
    }
    
    .loading-overlay {
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(4px);
    }
    
    .pulse-dot {
        animation: pulse 1.5s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(0.8); opacity: 0.5; }
        50% { transform: scale(1.2); opacity: 1; }
    }
    
    .fade-in {
        animation: fadeIn 0.6s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>
@endpush

@section('content')

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 loading-overlay z-50 hidden items-center justify-center">
    <div class="text-center">
        <div class="flex space-x-2 justify-center mb-4">
            <div class="w-3 h-3 bg-white dark:bg-gray-800 rounded-full pulse-dot"></div>
            <div class="w-3 h-3 bg-white dark:bg-gray-800 rounded-full pulse-dot" style="animation-delay: 0.2s;"></div>
            <div class="w-3 h-3 bg-white dark:bg-gray-800 rounded-full pulse-dot" style="animation-delay: 0.4s;"></div>
        </div>
        <p class="text-white text-lg font-medium">Connexion en cours...</p>
    </div>
</div>

<!-- Toast Notifications -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

<div class="auth-bg flex items-center justify-center p-4">
    <div class="w-full max-w-md fade-in">
        
        <!-- Logo VintApp -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-white dark:bg-gray-800 rounded-2xl mx-auto mb-4 flex items-center justify-center shadow-2xl">
                <span class="text-3xl">🛒</span>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">VintApp</h1>
            <p class="text-white/80 text-lg">Authentification sécurisée</p>
        </div>

        <!-- Card de connexion -->
        <div class="glass-effect rounded-2xl p-8 shadow-2xl">
            
            <!-- Mode Selection -->
            <div class="flex bg-white dark:bg-gray-800/10 rounded-xl p-1 mb-8">
                <button onclick="switchMode('login')" id="login-tab" class="flex-1 py-3 text-center rounded-lg font-semibold transition-all bg-white dark:bg-gray-800 text-primary-600">
                    Connexion
                </button>
                <button onclick="switchMode('register')" id="register-tab" class="flex-1 py-3 text-center rounded-lg font-semibold transition-all text-white/80 hover:text-white">
                    Inscription
                </button>
            </div>

            <div id="auth-content">
                <!-- Login Form -->
                <div id="login-form">
                    <h2 class="text-2xl font-bold text-white mb-6 text-center">Se connecter</h2>

                    <!-- Email/Password Login -->
                    <div class="space-y-4 mb-6">
                        <div>
                            <input type="email" id="login-email" placeholder="Adresse email" 
                                   class="w-full px-4 py-3 input-glass rounded-xl text-white placeholder-white/60 focus:outline-none transition-all">
                        </div>
                        
                        <div>
                            <input type="password" id="login-password" placeholder="Mot de passe" 
                                   class="w-full px-4 py-3 input-glass rounded-xl text-white placeholder-white/60 focus:outline-none transition-all">
                        </div>
                        
                        <button onclick="signInWithEmail('login')" class="w-full py-3 bg-white dark:bg-gray-800/20 hover:bg-white dark:bg-gray-800/30 border border-white/30 text-white rounded-xl font-semibold transition-all">
                            Se connecter
                        </button>
                    </div>

                    <div class="flex items-center my-6">
                        <div class="flex-1 h-px bg-white dark:bg-gray-800/30"></div>
                        <span class="px-4 text-white/70 text-sm">ou continuer avec</span>
                        <div class="flex-1 h-px bg-white dark:bg-gray-800/30"></div>
                    </div>
                </div>

                <!-- Register Form -->
                <div id="register-form" class="hidden">
                    <h2 class="text-2xl font-bold text-white mb-6 text-center">Créer un compte</h2>

                    <!-- Email/Password Register -->
                    <div class="space-y-4 mb-6">
                        <div>
                            <input type="text" id="register-name" placeholder="Nom complet" 
                                   class="w-full px-4 py-3 input-glass rounded-xl text-white placeholder-white/60 focus:outline-none transition-all">
                        </div>
                        
                        <div>
                            <input type="email" id="register-email" placeholder="Adresse email" 
                                   class="w-full px-4 py-3 input-glass rounded-xl text-white placeholder-white/60 focus:outline-none transition-all">
                        </div>
                        
                        <div>
                            <input type="password" id="register-password" placeholder="Mot de passe" 
                                   class="w-full px-4 py-3 input-glass rounded-xl text-white placeholder-white/60 focus:outline-none transition-all">
                        </div>
                        
                        <button onclick="signInWithEmail('register')" class="w-full py-3 bg-white dark:bg-gray-800/20 hover:bg-white dark:bg-gray-800/30 border border-white/30 text-white rounded-xl font-semibold transition-all">
                            Créer le compte
                        </button>
                    </div>

                    <div class="flex items-center my-6">
                        <div class="flex-1 h-px bg-white dark:bg-gray-800/30"></div>
                        <span class="px-4 text-white/70 text-sm">ou continuer avec</span>
                        <div class="flex-1 h-px bg-white dark:bg-gray-800/30"></div>
                    </div>
                </div>

                <!-- Social Login Buttons -->
                <div class="space-y-3">
                    <!-- Google Sign-In -->
                    <button onclick="signInWithGoogle()" class="w-full py-3 btn-firebase text-white rounded-xl font-semibold flex items-center justify-center gap-3">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Google
                    </button>

                    <!-- Facebook Sign-In -->
                    <button onclick="signInWithFacebook()" class="w-full py-3 btn-facebook text-white rounded-xl font-semibold transition-all flex items-center justify-center gap-3">
                        <span class="text-xl">📘</span>
                        Facebook
                    </button>
                </div>

                <!-- Footer Links -->
                <div class="mt-8 text-center text-sm text-white/70">
                    <p class="mb-2">
                        En continuant, vous acceptez nos 
                        <a href="#" class="text-white hover:underline">Conditions d'utilisation</a>
                    </p>
                    <a href="#" onclick="showForgotPassword()" class="text-white hover:underline">
                        Mot de passe oublié ?
                    </a>
                </div>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="text-center mt-6">
            <a href="{{ route('home') }}" class="text-white/80 hover:text-white transition-colors">
                ← Retour à l'accueil
            </a>
        </div>
    </div>
</div>

<!-- Firebase SDK -->
<script type="module">
import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js';
import { 
    getAuth, 
    signInWithEmailAndPassword, 
    createUserWithEmailAndPassword,
    signInWithPopup, 
    GoogleAuthProvider,
    FacebookAuthProvider,
    onAuthStateChanged,
    updateProfile
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

// Initialiser FCM seulement si supporté
try {
    messaging = getMessaging(app);
} catch (error) {
    // FCM not supported
}

// Providers
const googleProvider = new GoogleAuthProvider();
const facebookProvider = new FacebookAuthProvider();

// Variables globales
let currentMode = 'login';

// Fonctions globales
window.switchMode = (mode) => {
    currentMode = mode;
    
    // Mise à jour des onglets
    document.getElementById('login-tab').className = mode === 'login' 
        ? 'flex-1 py-3 text-center rounded-lg font-semibold transition-all bg-white dark:bg-gray-800 text-primary-600'
        : 'flex-1 py-3 text-center rounded-lg font-semibold transition-all text-white/80 hover:text-white';
    
    document.getElementById('register-tab').className = mode === 'register' 
        ? 'flex-1 py-3 text-center rounded-lg font-semibold transition-all bg-white dark:bg-gray-800 text-primary-600'
        : 'flex-1 py-3 text-center rounded-lg font-semibold transition-all text-white/80 hover:text-white';
    
    // Affichage des formulaires
    document.getElementById('login-form').classList.toggle('hidden', mode !== 'login');
    document.getElementById('register-form').classList.toggle('hidden', mode !== 'register');
};

window.signInWithEmail = async (mode) => {
    const isLogin = mode === 'login';
    const email = document.getElementById(isLogin ? 'login-email' : 'register-email').value;
    const password = document.getElementById(isLogin ? 'login-password' : 'register-password').value;
    const name = isLogin ? null : document.getElementById('register-name').value;
    
    if (!email || !password || (!isLogin && !name)) {
        showToast('Veuillez remplir tous les champs', 'error');
        return;
    }
    
    showLoading(true);
    
    try {
        let userCredential;
        
        if (isLogin) {
            userCredential = await signInWithEmailAndPassword(auth, email, password);
        } else {
            userCredential = await createUserWithEmailAndPassword(auth, email, password);
            // Mettre à jour le profil avec le nom
            if (name) {
                await updateProfile(userCredential.user, { displayName: name });
            }
        }
        
        await handleFirebaseAuth(userCredential.user);
    } catch (error) {
        let message = 'Erreur de connexion';
        
        switch (error.code) {
            case 'auth/user-not-found':
            case 'auth/wrong-password':
                message = 'Email ou mot de passe incorrect';
                break;
            case 'auth/email-already-in-use':
                message = 'Cet email est déjà utilisé';
                break;
            case 'auth/weak-password':
                message = 'Le mot de passe doit contenir au moins 6 caractères';
                break;
            case 'auth/invalid-email':
                message = 'Adresse email invalide';
                break;
        }
        
        showToast(message, 'error');
        showLoading(false);
    }
};

window.signInWithGoogle = async () => {
    showLoading(true);
    
    try {
        const result = await signInWithPopup(auth, googleProvider);
        await handleFirebaseAuth(result.user);
    } catch (error) {
        showLoading(false);
        
        // Gérer les erreurs spécifiques
        if (error.code === 'auth/popup-closed-by-user') {
            // L'utilisateur a fermé le popup - ne rien afficher
            return;
        }
        
        if (error.code === 'auth/cancelled-popup-request') {
            // Popup annulé car un autre était déjà ouvert
            return;
        }
        
        if (error.code === 'auth/popup-blocked') {
            showToast('Le popup a été bloqué. Autorisez les popups pour ce site.', 'error');
            return;
        }
        
        showToast('Erreur de connexion Google', 'error');
    }
};

window.signInWithFacebook = async () => {
    showLoading(true);
    
    try {
        const result = await signInWithPopup(auth, facebookProvider);
        await handleFirebaseAuth(result.user);
    } catch (error) {
        showLoading(false);
        
        // Gérer les erreurs spécifiques
        if (error.code === 'auth/popup-closed-by-user' || error.code === 'auth/cancelled-popup-request') {
            return;
        }
        
        if (error.code === 'auth/popup-blocked') {
            showToast('Le popup a été bloqué. Autorisez les popups pour ce site.', 'error');
            return;
        }
        
        showToast('Erreur de connexion Facebook', 'error');
    }
};

window.showForgotPassword = () => {
    showToast('Fonctionnalité à venir', 'info');
};

// Gérer l'authentification Firebase
async function handleFirebaseAuth(user) {
    try {
        const idToken = await user.getIdToken();
        
        // Obtenir le token FCM si possible
        let fcmToken = null;
        if (messaging) {
            try {
                fcmToken = await getToken(messaging, {
                    vapidKey: "{{ config('firebase.messaging.vapid_key', '') }}" || undefined
                });
            } catch (fcmError) {
                // Token FCM non disponible
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
                fcmToken: fcmToken,
                name: user.displayName
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Connexion réussie !', 'success');
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1000);
        } else {
            showToast(data.message || 'Erreur de connexion', 'error');
            showLoading(false);
        }
    } catch (error) {
        showToast('Erreur lors de l\'authentification', 'error');
        showLoading(false);
    }
}

function showLoading(show) {
    document.getElementById('loading-overlay').classList.toggle('hidden', !show);
    document.getElementById('loading-overlay').classList.toggle('flex', show);
}

function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    
    const toast = document.createElement('div');
    toast.className = `px-6 py-4 rounded-xl shadow-lg text-white flex items-center gap-3 transform transition-all duration-300 translate-x-full`;
    
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
        warning: 'bg-yellow-500'
    };
    
    const icons = {
        success: '✅',
        error: '❌',
        info: 'ℹ️',
        warning: '⚠️'
    };
    
    toast.className += ` ${colors[type] || colors.info}`;
    toast.innerHTML = `
        <span class="text-xl">${icons[type] || icons.info}</span>
        <span class="font-medium">${message}</span>
    `;
    
    container.appendChild(toast);
    
    // Animation d'entrée
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Auto-suppression
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 4000);
}

// Écouter les changements d'état d'authentification
onAuthStateChanged(auth, (user) => {
    if (user && window.location.pathname.includes('/login')) {
        // L'utilisateur est déjà connecté, vérifier avec le serveur
        fetch('{{ route("firebase.check-auth") }}')
            .then(response => response.json())
            .then(data => {
                if (data.authenticated) {
                    window.location.href = '{{ route("home") }}';
                }
            })
            .catch(() => {});
    }
});
</script>

@endsection