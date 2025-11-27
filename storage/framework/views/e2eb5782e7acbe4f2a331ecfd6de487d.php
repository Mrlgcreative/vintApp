

<?php $__env->startSection('title', 'Connexion - VintApp'); ?>

<?php $__env->startSection('content'); ?>

<!-- Firebase Scripts -->
<script src="https://www.gstatic.com/firebasejs/9.17.1/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.17.1/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.17.1/firebase-firestore-compat.js"></script>

<!-- Toast Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 hidden items-center justify-center">
    <div class="text-center">
        <div class="flex justify-center space-x-2 mb-4">
            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse" style="animation-delay: 0.2s;"></div>
            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse" style="animation-delay: 0.4s;"></div>
        </div>
        <p class="text-white font-medium">Connexion en cours...</p>
    </div>
</div>

<div class="min-h-screen bg-gradient-to-br from-primary-50 via-accent-50 to-blue-50 flex items-center justify-center p-4">
    <div class="w-full max-w-lg">
        <!-- Card Principal -->
        <div class="bg-white dark:bg-gray-800/95 backdrop-blur-sm rounded-3xl shadow-2xl p-8 transform transition-all duration-700 animate-fade-in-up">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-r from-green-600 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                </div>
                
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Connexion</h1>
                <p class="text-gray-600 dark:text-gray-300">Connectez-vous à votre compte VintApp</p>
            </div>

            <!-- Messages d'erreur/succès -->
            <?php if($errors->any()): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div class="flex items-center text-red-800 mb-2">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <span class="font-medium">Erreur de connexion</span>
                    </div>
                    <ul class="text-sm text-red-700 list-disc list-inside">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if(session('status')): ?>
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                    <div class="flex items-center text-green-800">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-sm font-medium"><?php echo e(session('status')); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div class="flex items-center text-red-800">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span class="text-sm font-medium"><?php echo e(session('error')); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Formulaire de connexion -->
            <form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-6">
                <?php echo csrf_field(); ?>
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Adresse e-mail
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               required 
                               value="<?php echo e(old('email')); ?>"
                               autocomplete="email"
                               class="w-full pl-10 pr-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-4 focus:ring-green-500/20 focus:border-green-500 transition-all duration-300 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 focus:border-red-500 focus:ring-red-100 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="votre@email.com">
                    </div>
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Mot de passe -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Mot de passe
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required
                               autocomplete="current-password"
                               class="w-full pl-10 pr-12 py-3 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-4 focus:ring-green-500/20 focus:border-green-500 transition-all duration-300 <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 focus:border-red-500 focus:ring-red-100 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="Votre mot de passe">
                        <button type="button" 
                                onclick="togglePassword()" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:text-gray-300 transition-colors">
                            <svg id="password-eye" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Options -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="remember" 
                               name="remember" 
                               class="w-4 h-4 text-green-600 border-gray-300 dark:border-gray-600 rounded focus:ring-green-500 focus:ring-offset-0">
                        <label for="remember" class="ml-2 text-sm text-gray-600 dark:text-gray-300">
                            Se souvenir de moi
                        </label>
                    </div>
                    <a href="<?php echo e(route('password.request')); ?>" 
                       class="text-sm text-green-600 hover:text-green-700 font-medium transition-colors">
                        Mot de passe oublié ?
                    </a>
                </div>

                <!-- Bouton principal -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-green-500/50">
                    <span class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Se connecter
                    </span>
                </button>
            </form>

            <!-- Séparateur -->
            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-medium">Ou continuer avec</span>
                </div>
            </div>

            <!-- Boutons sociaux -->
            <div class="space-y-3">
                <!-- Google -->
                <button onclick="signInWithGoogle()" 
                        type="button"
                        class="w-full flex items-center justify-center px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:border-gray-300 dark:border-gray-600 hover:shadow-md transform hover:-translate-y-1 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-gray-300">
                    <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                        <path fill="#4285f4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34a853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#fbbc05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#ea4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span class="text-gray-700 dark:text-gray-200 font-medium">Continuer avec Google</span>
                </button>

                <!-- Facebook -->
                <button onclick="signInWithFacebook()" 
                        type="button"
                        class="w-full flex items-center justify-center px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:border-gray-300 dark:border-gray-600 hover:shadow-md transform hover:-translate-y-1 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-gray-300">
                    <svg class="w-5 h-5 mr-3" fill="#1877F2" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span class="text-gray-700 dark:text-gray-200 font-medium">Continuer avec Facebook</span>
                </button>

                <!-- Apple (désactivé) -->
                <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-center text-gray-400">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.1.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.746-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001 12.017.001z"/>
                        </svg>
                        <span class="text-sm font-medium">Apple ID (bientôt disponible)</span>
                    </div>
                </div>
            </div>

            <!-- Liens footer -->
            <div class="text-center mt-8 space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Pas encore de compte ?
                    <a href="<?php echo e(route('register')); ?>" class="text-green-600 hover:text-green-700 font-medium transition-colors">
                        Créez votre compte gratuitement
                    </a>
                </p>
                
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Problème de connexion ? 
                    <a href="#" class="text-green-600 hover:text-green-700 transition-colors">
                        Consultez notre centre d'aide
                    </a>
                </p>
            </div>
        </div>

        <!-- Sécurité info -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Connexion sécurisée et protégée
            </p>
        </div>
    </div>
</div>

<script>
// Fonctions globales pour Firebase et connexion
window.signInWithGoogle = async function() {
    showLoading(true);
    
    try {
        // Vérifier que Firebase est initialisé
        if (!firebase.apps.length) {
            throw new Error('Firebase n\'est pas initialisé');
        }
        
        const provider = new firebase.auth.GoogleAuthProvider();
        provider.addScope('email');
        provider.addScope('profile');
        
        // Forcer la sélection de compte
        provider.setCustomParameters({
            prompt: 'select_account'
        });
        
        const result = await firebase.auth().signInWithPopup(provider);
        const user = result.user;
        
        if (!user) {
            throw new Error('Aucune information utilisateur reçue');
        }
        
        console.log('Utilisateur Google connecté:', user);
        
        // Obtenir le token Firebase pour Laravel
        const idToken = await user.getIdToken();
        
        // Envoyer les données à Laravel pour créer la session
        const response = await fetch('<?php echo e(route("firebase.login")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({
                idToken: idToken,
                name: user.displayName,
                email: user.email,
                provider: 'google',
                firebase_uid: user.uid,
                email_verified: user.emailVerified,
                photo_url: user.photoURL
            })
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            showLoading(false);
            showToast('Connexion Google réussie !', 'success');
            
            // Redirection vers la page d'accueil
            setTimeout(() => {
                window.location.href = data.redirect || '<?php echo e(route("home")); ?>';
            }, 1500);
        } else {
            throw new Error(data.message || 'Erreur lors de la synchronisation avec le serveur');
        }
        
    } catch (error) {
        showLoading(false);
        console.error('Erreur détaillée lors de la connexion Google:', error);
        
        let errorMessage = 'Erreur lors de la connexion Google';
        
        switch (error.code) {
            case 'auth/popup-closed-by-user':
                errorMessage = 'Connexion annulée par l\'utilisateur';
                break;
            case 'auth/popup-blocked':
                errorMessage = 'Popup bloqué par le navigateur. Autorisez les popups pour ce site.';
                break;
            case 'auth/account-exists-with-different-credential':
                errorMessage = 'Un compte existe déjà avec cette adresse email';
                break;
            default:
                errorMessage = `Erreur Google: ${error.message}`;
        }
        
        showToast(errorMessage, 'error');
    }
};

window.signInWithFacebook = async function() {
    showLoading(true);
    
    try {
        const provider = new firebase.auth.FacebookAuthProvider();
        provider.addScope('email');
        
        const result = await firebase.auth().signInWithPopup(provider);
        const user = result.user;
        
        showLoading(false);
        showToast('Connexion Facebook réussie !', 'success');
        
        setTimeout(() => {
            window.location.href = '<?php echo e(route("home")); ?>';
        }, 1500);
        
    } catch (error) {
        showLoading(false);
        console.error('Erreur lors de la connexion Facebook:', error);
        
        let errorMessage = 'Erreur lors de la connexion Facebook';
        if (error.code === 'auth/popup-closed-by-user') {
            errorMessage = 'Connexion annulée par l\'utilisateur';
        }
        
        showToast(errorMessage, 'error');
    }
};

// Toggle password visibility
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('password-eye');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
        `;
    } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        `;
    }
}

// Gestion du loading
function showLoading(show) {
    const overlay = document.getElementById('loading-overlay');
    if (show) {
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
    } else {
        overlay.classList.add('hidden');
        overlay.classList.remove('flex');
    }
}

// Système de toast
function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };
    
    const icons = {
        success: '✅',
        error: '❌',
        warning: '⚠️',
        info: 'ℹ️'
    };
    
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `${colors[type]} text-white px-6 py-3 rounded-xl shadow-lg flex items-center space-x-2 transform translate-x-full transition-transform duration-300`;
    toast.innerHTML = `
        <span>${icons[type]}</span>
        <span class="font-medium">${message}</span>
        <button onclick="document.getElementById('${toastId}').remove()" class="ml-4 hover:opacity-75">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    
    container.appendChild(toast);
    
    // Animation d'entrée
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Auto-remove après 5 secondes
    setTimeout(() => {
        if (document.getElementById(toastId)) {
            toast.classList.add('translate-x-full');
            setTimeout(() => document.getElementById(toastId).remove(), 300);
        }
    }, 5000);
}

// Validation email
function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// Gestion des événements DOM
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const form = document.querySelector('form');
    
    // Validation email en temps réel
    emailInput.addEventListener('blur', function() {
        const email = this.value.trim();
        if (email && !validateEmail(email)) {
            this.classList.add('border-red-400', 'focus:border-red-500', 'focus:ring-red-100');
            this.classList.remove('border-gray-200', 'focus:border-green-500', 'focus:ring-green-100');
            this.classList.remove('dark:border-gray-700');
        } else {
            this.classList.remove('border-red-400', 'focus:border-red-500', 'focus:ring-red-100');
            this.classList.add('border-gray-200', 'focus:border-green-500', 'focus:ring-green-100');
            this.classList.add('dark:border-gray-700');
        }
    });
    
    // Gestion soumission formulaire
    form.addEventListener('submit', function(e) {
        const email = emailInput.value.trim();
        const password = passwordInput.value;
        
        if (!email || !password) {
            e.preventDefault();
            showToast('Veuillez remplir tous les champs requis.', 'error');
            return;
        }
        
        if (!validateEmail(email)) {
            e.preventDefault();
            showToast('Format d\'email invalide.', 'error');
            emailInput.focus();
            return;
        }
        
        // Show loading state
        showLoading(true);
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <span class="flex items-center justify-center">
                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Connexion...
            </span>
        `;
    });
    
    // Auto-focus sur email
    emailInput.focus();
    
    // Animation CSS pour fade-in-up
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
    `;
    document.head.appendChild(style);
});

// Configuration Firebase - Valeurs réelles du fichier .env
const firebaseConfig = {
    apiKey: "AIzaSyBe0WQbkZ0A3Cz9vKyQWsE-edxLfWrV1_E",
    authDomain: "vintapp-e6fa7.firebaseapp.com",
    projectId: "vintapp-e6fa7",
    storageBucket: "vintapp-e6fa7.appspot.com",
    messagingSenderId: "880178183981",
    appId: "1:880178183981:web:395604645bd7d758a35da4"
};

// Initialiser Firebase avec vérification d'erreur
try {
    if (!firebase.apps.length) {
        firebase.initializeApp(firebaseConfig);
        console.log('Firebase initialisé avec succès');
    } else {
        console.log('Firebase déjà initialisé');
    }
    
    // Test de connexion
    firebase.auth().onAuthStateChanged((user) => {
        if (user) {
            console.log('Utilisateur connecté:', user.email);
        } else {
            console.log('Aucun utilisateur connecté');
        }
    });
    
} catch (error) {
    console.error('Erreur d\'initialisation Firebase:', error);
    showToast('Erreur de configuration Firebase. Contactez l\'administrateur.', 'error');
}
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/auth/login.blade.php ENDPATH**/ ?>