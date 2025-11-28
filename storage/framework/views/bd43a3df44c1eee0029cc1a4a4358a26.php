

<?php $__env->startSection('title', 'Authentification à deux facteurs'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full mb-4">
                <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Authentification à deux facteurs
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Ajoutez une couche de sécurité supplémentaire à votre compte
            </p>
        </div>

        <!-- Alert Messages -->
        <div id="alertContainer" class="mb-6"></div>

        <!-- Main Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
            <?php if($enabled): ?>
                <!-- 2FA Enabled State -->
                <div class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center justify-center w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    2FA Activée
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Votre compte est protégé par l'authentification à deux facteurs
                                </p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                            Actif
                        </span>
                    </div>

                    <!-- Actions -->
                    <div class="grid gap-4 sm:grid-cols-2">
                        <!-- Regenerate Recovery Codes -->
                        <button onclick="showRegenerateModal()" 
                                class="flex items-center gap-3 p-4 border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:border-primary-500 dark:hover:border-primary-500 transition-all group">
                            <div class="flex items-center justify-center w-10 h-10 bg-primary-50 dark:bg-primary-900 rounded-lg group-hover:bg-primary-100 dark:group-hover:bg-primary-800 transition-colors">
                                <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </div>
                            <div class="text-left">
                                <div class="font-medium text-gray-900 dark:text-white">Régénérer les codes</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Codes de récupération</div>
                            </div>
                        </button>

                        <!-- Disable 2FA -->
                        <button onclick="showDisableModal()" 
                                class="flex items-center gap-3 p-4 border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:border-red-500 dark:hover:border-red-500 transition-all group">
                            <div class="flex items-center justify-center w-10 h-10 bg-red-50 dark:bg-red-900 rounded-lg group-hover:bg-red-100 dark:group-hover:bg-red-800 transition-colors">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                            <div class="text-left">
                                <div class="font-medium text-gray-900 dark:text-white">Désactiver 2FA</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Retirer la protection</div>
                            </div>
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <!-- 2FA Disabled State -->
                <div class="p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex items-center justify-center w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full">
                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                2FA Désactivée
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Activez l'authentification à deux facteurs pour plus de sécurité
                            </p>
                        </div>
                    </div>

                    <!-- Benefits -->
                    <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-xl p-6 mb-6">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-3">
                            Pourquoi activer 2FA ?
                        </h3>
                        <ul class="space-y-2">
                            <li class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <svg class="w-5 h-5 text-primary-600 dark:text-primary-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Protection contre les accès non autorisés</span>
                            </li>
                            <li class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <svg class="w-5 h-5 text-primary-600 dark:text-primary-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Sécurité renforcée même si votre mot de passe est compromis</span>
                            </li>
                            <li class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <svg class="w-5 h-5 text-primary-600 dark:text-primary-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Compatible avec Google Authenticator, Authy, et autres apps</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Enable Button -->
                    <button onclick="enable2FA()" 
                            class="w-full sm:w-auto px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02] flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span>Activer l'authentification à deux facteurs</span>
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Info Section -->
        <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm text-blue-800 dark:text-blue-200">
                    <p class="font-semibold mb-1">Note importante</p>
                    <p>Conservez vos codes de récupération en lieu sûr. Ils vous permettront d'accéder à votre compte si vous perdez l'accès à votre application d'authentification.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Setup Modal -->
<div id="setupModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Configurer l'authentification à deux facteurs</h3>
            <button onclick="closeSetupModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="p-6 space-y-6">
            <!-- Step 1: Scan QR Code -->
            <div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 rounded-full text-sm font-bold">1</span>
                    Scannez le QR code
                </h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Utilisez Google Authenticator, Authy ou toute autre application compatible pour scanner ce QR code.
                </p>
                <div id="qrCodeContainer" class="flex justify-center p-4 bg-gray-50 dark:bg-gray-900 rounded-xl">
                    <!-- QR Code will be inserted here -->
                </div>
                <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Ou entrez ce code manuellement :</p>
                    <code id="secretKey" class="text-sm font-mono text-gray-900 dark:text-white select-all"></code>
                </div>
            </div>

            <!-- Step 2: Enter Code -->
            <div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 rounded-full text-sm font-bold">2</span>
                    Vérifiez le code
                </h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    Entrez le code à 6 chiffres généré par votre application.
                </p>
                <form id="confirmForm" onsubmit="confirm2FA(event)" class="space-y-4">
                    <div>
                        <input type="text" 
                               id="verificationCode" 
                               name="code" 
                               placeholder="000000" 
                               maxlength="6" 
                               pattern="[0-9]{6}"
                               class="w-full px-4 py-3 text-center text-2xl font-mono tracking-widest border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:bg-gray-900 dark:text-white"
                               required
                               autocomplete="off">
                    </div>

                    <!-- Recovery Codes Display -->
                    <div id="recoveryCodesContainer" class="hidden">
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-2 border-yellow-200 dark:border-yellow-800 rounded-xl p-4">
                            <h5 class="font-semibold text-yellow-900 dark:text-yellow-100 mb-2 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                Codes de récupération
                            </h5>
                            <p class="text-sm text-yellow-800 dark:text-yellow-200 mb-3">
                                Conservez ces codes en lieu sûr. Ils vous permettront de vous connecter si vous perdez l'accès à votre application d'authentification.
                            </p>
                            <div id="recoveryCodes" class="grid grid-cols-2 gap-2 font-mono text-sm"></div>
                            <button type="button" 
                                    onclick="downloadRecoveryCodes()" 
                                    class="mt-3 w-full px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors text-sm font-medium">
                                Télécharger les codes
                            </button>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition-all">
                        Vérifier et activer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Disable Modal -->
<div id="disableModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-md">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 bg-red-100 dark:bg-red-900 rounded-full mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white text-center mb-2">Désactiver 2FA</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 text-center mb-6">
                Êtes-vous sûr de vouloir désactiver l'authentification à deux facteurs ? Votre compte sera moins sécurisé.
            </p>
            
            <form id="disableForm" onsubmit="disable2FA(event)" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Confirmez votre mot de passe
                    </label>
                    <input type="password" 
                           id="disablePassword" 
                           name="password" 
                           class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:bg-gray-900 dark:text-white"
                           required
                           autocomplete="current-password">
                </div>

                <div class="flex gap-3">
                    <button type="button" 
                            onclick="closeDisableModal()" 
                            class="flex-1 px-4 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all font-medium">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-all font-semibold">
                        Désactiver
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Regenerate Modal -->
<div id="regenerateModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-md">
        <div class="p-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Régénérer les codes de récupération</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Cela invalidera vos codes de récupération actuels et en générera de nouveaux.
            </p>
            
            <form id="regenerateForm" onsubmit="regenerateCodes(event)" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Confirmez votre mot de passe
                    </label>
                    <input type="password" 
                           id="regeneratePassword" 
                           name="password" 
                           class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:bg-gray-900 dark:text-white"
                           required
                           autocomplete="current-password">
                </div>

                <div id="newRecoveryCodesContainer" class="hidden">
                    <div class="bg-green-50 dark:bg-green-900/20 border-2 border-green-200 dark:border-green-800 rounded-xl p-4">
                        <h5 class="font-semibold text-green-900 dark:text-green-100 mb-3">Nouveaux codes de récupération</h5>
                        <div id="newRecoveryCodes" class="grid grid-cols-2 gap-2 font-mono text-sm mb-3"></div>
                        <button type="button" 
                                onclick="downloadNewRecoveryCodes()" 
                                class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors text-sm font-medium">
                            Télécharger les codes
                        </button>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button" 
                            onclick="closeRegenerateModal()" 
                            class="flex-1 px-4 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all font-medium">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl transition-all font-semibold">
                        Régénérer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
let currentRecoveryCodes = [];
let newRecoveryCodes = [];

// Enable 2FA
async function enable2FA() {
    try {
        const response = await fetch('<?php echo e(route("two-factor.enable")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (data.success) {
            // Show QR code
            document.getElementById('qrCodeContainer').innerHTML = `<img src="${data.qrCode}" alt="QR Code" class="w-64 h-64">`;
            document.getElementById('secretKey').textContent = data.secret;
            currentRecoveryCodes = data.recoveryCodes;
            
            // Show setup modal
            document.getElementById('setupModal').classList.remove('hidden');
            document.getElementById('setupModal').classList.add('flex');
        } else {
            showAlert('error', data.message || 'Erreur lors de l\'activation');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'Une erreur est survenue');
    }
}

// Confirm 2FA
async function confirm2FA(event) {
    event.preventDefault();
    
    const code = document.getElementById('verificationCode').value;
    
    try {
        const response = await fetch('<?php echo e(route("two-factor.confirm")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ code })
        });

        const data = await response.json();

        if (data.success) {
            // Show recovery codes
            currentRecoveryCodes = data.recoveryCodes;
            displayRecoveryCodes(data.recoveryCodes, 'recoveryCodes');
            document.getElementById('recoveryCodesContainer').classList.remove('hidden');
            
            // Hide verify button and show success
            showAlert('success', data.message);
            
            // Reload page after 3 seconds
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        } else {
            showAlert('error', data.message || 'Code invalide');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'Une erreur est survenue');
    }
}

// Disable 2FA
async function disable2FA(event) {
    event.preventDefault();
    
    const password = document.getElementById('disablePassword').value;
    
    try {
        const response = await fetch('<?php echo e(route("two-factor.disable")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ password })
        });

        const data = await response.json();

        if (data.success) {
            showAlert('success', data.message);
            closeDisableModal();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert('error', data.message || 'Mot de passe incorrect');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'Une erreur est survenue');
    }
}

// Regenerate codes
async function regenerateCodes(event) {
    event.preventDefault();
    
    const password = document.getElementById('regeneratePassword').value;
    
    try {
        const response = await fetch('<?php echo e(route("two-factor.regenerate-codes")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ password })
        });

        const data = await response.json();

        if (data.success) {
            newRecoveryCodes = data.recoveryCodes;
            displayRecoveryCodes(data.recoveryCodes, 'newRecoveryCodes');
            document.getElementById('newRecoveryCodesContainer').classList.remove('hidden');
            showAlert('success', data.message);
        } else {
            showAlert('error', data.message || 'Mot de passe incorrect');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'Une erreur est survenue');
    }
}

// Display recovery codes
function displayRecoveryCodes(codes, containerId) {
    const container = document.getElementById(containerId);
    container.innerHTML = codes.map(code => 
        `<div class="bg-white dark:bg-gray-800 px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700">
            <code class="text-xs text-gray-900 dark:text-white">${code}</code>
        </div>`
    ).join('');
}

// Download recovery codes
function downloadRecoveryCodes() {
    downloadCodes(currentRecoveryCodes, 'recovery-codes.txt');
}

function downloadNewRecoveryCodes() {
    downloadCodes(newRecoveryCodes, 'new-recovery-codes.txt');
}

function downloadCodes(codes, filename) {
    const text = `VintApp - Codes de récupération 2FA\n\nGénérés le: ${new Date().toLocaleString('fr-FR')}\n\n${codes.join('\n')}\n\nConservez ces codes en lieu sûr. Ils vous permettront de vous connecter si vous perdez l'accès à votre application d'authentification.`;
    const blob = new Blob([text], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
}

// Modal controls
function closeSetupModal() {
    document.getElementById('setupModal').classList.add('hidden');
    document.getElementById('setupModal').classList.remove('flex');
    document.getElementById('confirmForm').reset();
    document.getElementById('recoveryCodesContainer').classList.add('hidden');
}

function showDisableModal() {
    document.getElementById('disableModal').classList.remove('hidden');
    document.getElementById('disableModal').classList.add('flex');
}

function closeDisableModal() {
    document.getElementById('disableModal').classList.add('hidden');
    document.getElementById('disableModal').classList.remove('flex');
    document.getElementById('disableForm').reset();
}

function showRegenerateModal() {
    document.getElementById('regenerateModal').classList.remove('hidden');
    document.getElementById('regenerateModal').classList.add('flex');
}

function closeRegenerateModal() {
    document.getElementById('regenerateModal').classList.add('hidden');
    document.getElementById('regenerateModal').classList.remove('flex');
    document.getElementById('regenerateForm').reset();
    document.getElementById('newRecoveryCodesContainer').classList.add('hidden');
}

// Alert helper
function showAlert(type, message) {
    const alertContainer = document.getElementById('alertContainer');
    const alertColors = {
        success: 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200',
        error: 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200',
        info: 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200'
    };
    
    const alert = document.createElement('div');
    alert.className = `${alertColors[type]} border rounded-xl p-4 flex items-center gap-3`;
    alert.innerHTML = `
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            ${type === 'success' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' : 
              type === 'error' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>' :
              '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'}
        </svg>
        <span class="flex-1">${message}</span>
        <button onclick="this.parentElement.remove()" class="text-current opacity-60 hover:opacity-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    
    alertContainer.appendChild(alert);
    
    setTimeout(() => {
        alert.remove();
    }, 5000);
}

// Auto-focus on verification code input
document.getElementById('verificationCode')?.addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '');
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\vintApp\resources\views/auth/two-factor.blade.php ENDPATH**/ ?>