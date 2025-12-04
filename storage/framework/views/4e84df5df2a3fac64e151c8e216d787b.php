

<?php $__env->startSection('title', 'Authentification à deux facteurs'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-50 to-accent-50 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <!-- Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-primary-600 to-accent-600 px-8 py-6 text-center">
                <div class="flex items-center justify-center w-20 h-20 mx-auto mb-4 bg-white/20 backdrop-blur-sm rounded-full">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white">Authentification requise</h2>
                <p class="text-primary-100 mt-2">Entrez votre code de sécurité</p>
            </div>

            <!-- Body -->
            <div class="px-8 py-8">
                <!-- Info User -->
                <div class="flex items-center gap-4 mb-6 p-4 bg-gray-50 dark:bg-gray-900 rounded-xl">
                    <?php if($user->avatar): ?>
                        <img src="<?php echo e(filter_var($user->avatar, FILTER_VALIDATE_URL) ? $user->avatar : Storage::url($user->avatar)); ?>" 
                             alt="<?php echo e($user->name); ?>" 
                             class="w-12 h-12 rounded-full object-cover">
                    <?php else: ?>
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary-500 to-accent-500 text-white flex items-center justify-center text-xl font-bold">
                            <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                        </div>
                    <?php endif; ?>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white"><?php echo e($user->name); ?></h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo e($user->email); ?></p>
                    </div>
                </div>

                <!-- Alert Info -->
                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded-r-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            Votre compte est protégé par l'authentification à deux facteurs. Ouvrez votre application d'authentification (Google Authenticator, Authy, etc.) et entrez le code à 6 chiffres.
                        </p>
                    </div>
                </div>

                <!-- Form -->
                <form id="verify2FAForm" class="space-y-6">
                    <?php echo csrf_field(); ?>
                    
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Code d'authentification
                        </label>
                        <input type="text" 
                               id="code" 
                               name="code" 
                               maxlength="6"
                               pattern="[0-9]*"
                               inputmode="numeric"
                               autocomplete="one-time-code"
                               placeholder="000000"
                               class="w-full px-4 py-4 text-center text-2xl tracking-widest font-mono border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:bg-gray-900 dark:text-white transition-all"
                               required
                               autofocus>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 text-center">
                            Entrez le code à 6 chiffres de votre application
                        </p>
                    </div>

                    <!-- Error Message -->
                    <div id="errorMessage" class="hidden p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-r-lg">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-red-800 dark:text-red-200" id="errorText"></p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            id="verifyButton"
                            class="w-full px-6 py-4 bg-gradient-to-r from-primary-600 to-accent-600 hover:from-primary-700 hover:to-accent-700 text-white rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Vérifier le code</span>
                    </button>
                </form>

                <!-- Recovery Code Link -->
                <div class="mt-6 text-center">
                    <button onclick="showRecoveryInput()" 
                            class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium transition-colors">
                        Utiliser un code de récupération
                    </button>
                </div>

                <!-- Recovery Code Form (Hidden by default) -->
                <div id="recoveryForm" class="hidden mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border-2 border-yellow-300 dark:border-yellow-700 rounded-xl">
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                        Code de récupération
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        Entrez l'un de vos codes de récupération de secours
                    </p>
                    <input type="text" 
                           id="recoveryCode" 
                           placeholder="xxxxx-xxxxx-xxxxx"
                           class="w-full px-4 py-3 font-mono border-2 border-yellow-300 dark:border-yellow-700 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 dark:bg-gray-900 dark:text-white transition-all mb-3">
                    <div class="flex gap-2">
                        <button onclick="verifyRecoveryCode()" 
                                class="flex-1 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-medium transition-colors">
                            Vérifier
                        </button>
                        <button onclick="hideRecoveryInput()" 
                                class="px-4 py-2 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            Annuler
                        </button>
                    </div>
                </div>

                <!-- Warning -->
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">
                                Sécurité de votre compte
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                Vous devez vérifier votre identité pour accéder à votre compte. Cette étape ne peut pas être ignorée.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-8 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <a href="<?php echo e(route('logout')); ?>" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Se déconnecter
                    </a>
                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="hidden">
                        <?php echo csrf_field(); ?>
                    </form>
                    
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Protégé par 2FA
                    </p>
                </div>
            </div>
        </div>

        <!-- Help Link -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Problème avec votre code ?
                <a href="#" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium">
                    Contactez le support
                </a>
            </p>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-2xl">
        <div class="flex flex-col items-center gap-4">
            <div class="w-16 h-16 border-4 border-primary-200 dark:border-primary-800 border-t-primary-600 dark:border-t-primary-400 rounded-full animate-spin"></div>
            <p class="text-gray-900 dark:text-white font-medium">Vérification en cours...</p>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// Auto-focus sur le champ de code
document.addEventListener('DOMContentLoaded', function() {
    const codeInput = document.getElementById('code');
    codeInput.focus();
    
    // Auto-submit quand 6 chiffres sont entrés
    codeInput.addEventListener('input', function(e) {
        // Autoriser seulement les chiffres
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Auto-submit si 6 chiffres
        if (this.value.length === 6) {
            setTimeout(() => {
                document.getElementById('verify2FAForm').dispatchEvent(new Event('submit'));
            }, 300);
        }
    });
});

// Vérifier le code 2FA
document.getElementById('verify2FAForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const code = document.getElementById('code').value.trim();
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const verifyButton = document.getElementById('verifyButton');
    
    // Validation
    if (code.length !== 6 || !/^\d+$/.test(code)) {
        errorText.textContent = 'Le code doit contenir exactement 6 chiffres.';
        errorMessage.classList.remove('hidden');
        return;
    }
    
    // Masquer les erreurs précédentes
    errorMessage.classList.add('hidden');
    
    // Afficher le loading
    loadingOverlay.classList.remove('hidden');
    verifyButton.disabled = true;
    
    try {
        const response = await fetch('<?php echo e(route("two-factor.verify")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({ code: code })
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            // Succès - redirection
            window.location.href = data.redirect || '<?php echo e(route("home")); ?>';
        } else {
            // Erreur
            loadingOverlay.classList.add('hidden');
            verifyButton.disabled = false;
            errorText.textContent = data.message || 'Code invalide. Veuillez réessayer.';
            errorMessage.classList.remove('hidden');
            document.getElementById('code').value = '';
            document.getElementById('code').focus();
        }
    } catch (error) {
        console.error('Erreur lors de la vérification 2FA:', error);
        loadingOverlay.classList.add('hidden');
        verifyButton.disabled = false;
        errorText.textContent = 'Erreur de connexion. Veuillez réessayer.';
        errorMessage.classList.remove('hidden');
    }
});

// Afficher le formulaire de code de récupération
function showRecoveryInput() {
    document.getElementById('recoveryForm').classList.remove('hidden');
    document.getElementById('recoveryCode').focus();
}

// Masquer le formulaire de code de récupération
function hideRecoveryInput() {
    document.getElementById('recoveryForm').classList.add('hidden');
    document.getElementById('recoveryCode').value = '';
}

// Vérifier le code de récupération
async function verifyRecoveryCode() {
    const code = document.getElementById('recoveryCode').value.trim();
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    const loadingOverlay = document.getElementById('loadingOverlay');
    
    if (!code) {
        errorText.textContent = 'Veuillez entrer un code de récupération.';
        errorMessage.classList.remove('hidden');
        return;
    }
    
    errorMessage.classList.add('hidden');
    loadingOverlay.classList.remove('hidden');
    
    try {
        const response = await fetch('<?php echo e(route("two-factor.verify")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({ code: code })
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            window.location.href = data.redirect || '<?php echo e(route("home")); ?>';
        } else {
            loadingOverlay.classList.add('hidden');
            errorText.textContent = data.message || 'Code de récupération invalide.';
            errorMessage.classList.remove('hidden');
            document.getElementById('recoveryCode').value = '';
            document.getElementById('recoveryCode').focus();
        }
    } catch (error) {
        console.error('Erreur lors de la vérification du code de récupération:', error);
        loadingOverlay.classList.add('hidden');
        errorText.textContent = 'Erreur de connexion. Veuillez réessayer.';
        errorMessage.classList.remove('hidden');
    }
}

// Empêcher la navigation arrière
history.pushState(null, null, location.href);
window.onpopstate = function () {
    history.go(1);
};
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/auth/two-factor-challenge.blade.php ENDPATH**/ ?>