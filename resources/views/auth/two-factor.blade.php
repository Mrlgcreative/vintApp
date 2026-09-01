@extends('app')

@section('title', 'Authentification à deux facteurs')

@section('content')
<div class="min-h-[calc(100vh-4rem)] bg-gray-50 dark:bg-gray-900 py-10 px-6 md:p-10">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="flex items-center gap-2.5 justify-center mb-4">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 font-medium group">
                    <div class="w-9 h-9 rounded-lg bg-vinted-primary-600 text-white flex items-center justify-center shadow-md shadow-vinted-primary-600/30 group-hover:shadow-lg group-hover:shadow-vinted-primary-600/40 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $appName ?? config('app.name', 'VintApp') }}
                    </span>
                </a>
            </div>
            <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white mb-2">
                Authentification à deux facteurs
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Ajoutez une couche de sécurité supplémentaire à votre compte
            </p>
        </div>

        <!-- Alert Messages -->
        <div id="alertContainer" class="mb-6"></div>

        <!-- Main Card -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            @if($enabled)
                <!-- 2FA Enabled State -->
                <div class="p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center justify-center w-11 h-11 bg-vinted-success-50 dark:bg-vinted-success-500/10 rounded-full">
                                <svg class="w-5 h-5 text-vinted-success-600 dark:text-vinted-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">2FA Activée</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Votre compte est protégé par l'authentification à deux facteurs
                                </p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-vinted-success-50 dark:bg-vinted-success-500/10 text-vinted-success-600 dark:text-vinted-success-400">
                            Actif
                        </span>
                    </div>

                    <!-- Actions -->
                    <div class="grid gap-3 sm:grid-cols-2">
                        <!-- Regenerate Recovery Codes -->
                        <button onclick="showRegenerateModal()"
                                class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 dark:border-gray-800 hover:border-vinted-primary-500 dark:hover:border-vinted-primary-500 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all group">
                            <div class="flex items-center justify-center w-10 h-10 bg-vinted-primary-50 dark:bg-vinted-primary-500/10 rounded-lg group-hover:bg-vinted-primary-100 dark:group-hover:bg-vinted-primary-500/20 transition-colors">
                                <svg class="w-5 h-5 text-vinted-primary-600 dark:text-vinted-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </div>
                            <div class="text-left">
                                <div class="font-medium text-gray-900 dark:text-white text-sm">Régénérer les codes</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Codes de récupération</div>
                            </div>
                        </button>

                        <!-- Disable 2FA -->
                        <button onclick="showDisableModal()"
                                class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 dark:border-gray-800 hover:border-vinted-danger-500 dark:hover:border-vinted-danger-500 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all group">
                            <div class="flex items-center justify-center w-10 h-10 bg-vinted-danger-50 dark:bg-vinted-danger-500/10 rounded-lg group-hover:bg-vinted-danger-100 dark:group-hover:bg-vinted-danger-500/20 transition-colors">
                                <svg class="w-5 h-5 text-vinted-danger-600 dark:text-vinted-danger-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                            <div class="text-left">
                                <div class="font-medium text-gray-900 dark:text-white text-sm">Désactiver 2FA</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Retirer la protection</div>
                            </div>
                        </button>
                    </div>
                </div>
            @else
                <!-- 2FA Disabled State -->
                <div class="p-6 sm:p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex items-center justify-center w-11 h-11 bg-gray-100 dark:bg-gray-800 rounded-full">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">2FA Désactivée</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Activez l'authentification à deux facteurs pour plus de sécurité
                            </p>
                        </div>
                    </div>

                    <!-- Benefits -->
                    <div class="rounded-lg border border-vinted-primary-100 dark:border-vinted-primary-500/30 bg-vinted-primary-50 dark:bg-vinted-primary-500/10 p-5 mb-6">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Pourquoi activer 2FA ?</h3>
                        <ul class="space-y-2">
                            <li class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <svg class="w-5 h-5 text-vinted-primary-600 dark:text-vinted-primary-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Protection contre les accès non autorisés</span>
                            </li>
                            <li class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <svg class="w-5 h-5 text-vinted-primary-600 dark:text-vinted-primary-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Sécurité renforcée même si votre mot de passe est compromis</span>
                            </li>
                            <li class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <svg class="w-5 h-5 text-vinted-primary-600 dark:text-vinted-primary-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Compatible avec Google Authenticator, Authy, et autres apps</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Enable Button -->
                    <button onclick="enable2FA()"
                            class="w-full sm:w-auto h-10 inline-flex items-center justify-center gap-2 px-6 rounded-md bg-vinted-primary-600 text-white text-sm font-medium hover:bg-vinted-primary-700 active:scale-[0.98] transition-all shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-vinted-primary-300 focus-visible:ring-offset-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span>Activer l'authentification à deux facteurs</span>
                    </button>
                </div>
            @endif
        </div>

        <!-- Info Section -->
        <div class="mt-6 rounded-lg border border-blue-100 dark:border-blue-500/30 bg-blue-50 dark:bg-blue-500/10 p-5">
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
    <div class="bg-white dark:bg-gray-900 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-800 shadow-2xl">
        <div class="sticky top-0 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Configurer l'authentification à deux facteurs</h3>
            <button onclick="closeSetupModal()" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="p-6 space-y-6">
            <!-- Step 1: Scan QR Code -->
            <div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-600 dark:text-vinted-primary-400 rounded-full text-sm font-bold">1</span>
                    Scannez le QR code
                </h4>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Utilisez Google Authenticator, Authy ou toute autre application compatible pour scanner ce QR code.
                </p>
                <div id="qrCodeContainer" class="flex justify-center p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <!-- QR Code will be inserted here -->
                </div>
                <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-md">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Ou entrez ce code manuellement :</p>
                    <code id="secretKey" class="text-sm font-mono text-gray-900 dark:text-white select-all"></code>
                </div>
            </div>

            <!-- Step 2: Enter Code -->
            <div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 bg-vinted-primary-50 dark:bg-vinted-primary-500/10 text-vinted-primary-600 dark:text-vinted-primary-400 rounded-full text-sm font-bold">2</span>
                    Vérifiez le code
                </h4>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                    Entrez le code à 6 chiffres généré par votre application.
                </p>
                <form id="confirmForm" onsubmit="confirm2FA(event)" class="flex flex-col gap-4">
                    <input type="text"
                           id="verificationCode"
                           name="code"
                           placeholder="000000"
                           maxlength="6"
                           pattern="[0-9]{6}"
                           class="w-full px-4 py-3 text-center text-2xl tracking-[0.5em] font-mono bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-vinted-primary-500 focus:ring-2 focus:ring-vinted-primary-500/30 outline-none transition-colors"
                           required
                           autocomplete="off">

                    <!-- Recovery Codes Display -->
                    <div id="recoveryCodesContainer" class="hidden">
                        <div class="rounded-lg border border-vinted-warning-200 dark:border-vinted-warning-500/30 bg-vinted-warning-50 dark:bg-vinted-warning-500/10 p-4">
                            <h5 class="font-semibold text-gray-900 dark:text-white mb-2 flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-vinted-warning-600 dark:text-vinted-warning-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                Codes de récupération
                            </h5>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                Conservez ces codes en lieu sûr. Ils vous permettront de vous connecter si vous perdez l'accès à votre application d'authentification.
                            </p>
                            <div id="recoveryCodes" class="grid grid-cols-2 gap-2 font-mono text-sm"></div>
                            <button type="button"
                                    onclick="downloadRecoveryCodes()"
                                    class="mt-3 w-full h-10 inline-flex items-center justify-center rounded-md bg-vinted-primary-600 text-white text-sm font-medium hover:bg-vinted-primary-700 active:scale-[0.98] transition-all shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-vinted-primary-300 focus-visible:ring-offset-1">
                                Télécharger les codes
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full h-10 inline-flex items-center justify-center rounded-md bg-vinted-primary-600 text-white text-sm font-medium hover:bg-vinted-primary-700 active:scale-[0.98] transition-all shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-vinted-primary-300 focus-visible:ring-offset-1">
                        Vérifier et activer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Disable Modal -->
<div id="disableModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-xl w-full max-w-md border border-gray-200 dark:border-gray-800 shadow-2xl">
        <div class="p-6">
            <div class="flex items-center justify-center w-11 h-11 bg-vinted-danger-50 dark:bg-vinted-danger-500/10 rounded-full mx-auto mb-4">
                <svg class="w-5 h-5 text-vinted-danger-600 dark:text-vinted-danger-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-2">Désactiver 2FA</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">
                Êtes-vous sûr de vouloir désactiver l'authentification à deux facteurs ? Votre compte sera moins sécurisé.
            </p>
            
            <form id="disableForm" onsubmit="disable2FA(event)" class="flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Confirmez votre mot de passe</label>
                    <input type="password"
                           id="disablePassword"
                           name="password"
                           class="w-full px-4 py-2.5 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white focus:border-vinted-primary-500 focus:ring-2 focus:ring-vinted-primary-500/30 outline-none transition-colors"
                           required
                           autocomplete="current-password">
                </div>

                <div class="flex gap-3">
                    <button type="button"
                            onclick="closeDisableModal()"
                            class="flex-1 h-10 inline-flex items-center justify-center rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 h-10 inline-flex items-center justify-center rounded-md bg-vinted-danger-600 text-white text-sm font-medium hover:bg-vinted-danger-700 active:scale-[0.98] transition-all shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-vinted-danger-300 focus-visible:ring-offset-1">
                        Désactiver
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Regenerate Modal -->
<div id="regenerateModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-xl w-full max-w-md border border-gray-200 dark:border-gray-800 shadow-2xl">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Régénérer les codes de récupération</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                Cela invalidera vos codes de récupération actuels et en générera de nouveaux.
            </p>
            
            <form id="regenerateForm" onsubmit="regenerateCodes(event)" class="flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Confirmez votre mot de passe</label>
                    <input type="password"
                           id="regeneratePassword"
                           name="password"
                           class="w-full px-4 py-2.5 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white focus:border-vinted-primary-500 focus:ring-2 focus:ring-vinted-primary-500/30 outline-none transition-colors"
                           required
                           autocomplete="current-password">
                </div>

                <div id="newRecoveryCodesContainer" class="hidden">
                    <div class="rounded-lg border border-vinted-success-200 dark:border-vinted-success-500/30 bg-vinted-success-50 dark:bg-vinted-success-500/10 p-4">
                        <h5 class="font-semibold text-gray-900 dark:text-white mb-3 text-sm">Nouveaux codes de récupération</h5>
                        <div id="newRecoveryCodes" class="grid grid-cols-2 gap-2 font-mono text-sm mb-3"></div>
                        <button type="button"
                                onclick="downloadNewRecoveryCodes()"
                                class="w-full h-10 inline-flex items-center justify-center rounded-md bg-vinted-primary-600 text-white text-sm font-medium hover:bg-vinted-primary-700 active:scale-[0.98] transition-all shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-vinted-primary-300 focus-visible:ring-offset-1">
                            Télécharger les codes
                        </button>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button"
                            onclick="closeRegenerateModal()"
                            class="flex-1 h-10 inline-flex items-center justify-center rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 h-10 inline-flex items-center justify-center rounded-md bg-vinted-primary-600 text-white text-sm font-medium hover:bg-vinted-primary-700 active:scale-[0.98] transition-all shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-vinted-primary-300 focus-visible:ring-offset-1">
                        Régénérer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentRecoveryCodes = [];
let newRecoveryCodes = [];

// Enable 2FA
async function enable2FA() {
    try {
        const response = await fetch('{{ route("two-factor.enable") }}', {
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
        const response = await fetch('{{ route("two-factor.confirm") }}', {
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
        const response = await fetch('{{ route("two-factor.disable") }}', {
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
        const response = await fetch('{{ route("two-factor.regenerate-codes") }}', {
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
        `<div class="bg-white dark:bg-gray-800 px-3 py-2 rounded-md border border-gray-200 dark:border-gray-800">
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
        success: 'bg-vinted-success-50 dark:bg-vinted-success-500/10 border-vinted-success-200 dark:border-vinted-success-500/30 text-vinted-success-600 dark:text-vinted-success-300',
        error: 'bg-vinted-danger-50 dark:bg-vinted-danger-500/10 border-vinted-danger-200 dark:border-vinted-danger-500/30 text-vinted-danger-600 dark:text-vinted-danger-300',
        info: 'bg-blue-50 dark:bg-blue-500/10 border-blue-200 dark:border-blue-500/30 text-blue-600 dark:text-blue-300'
    };
    
    const alert = document.createElement('div');
    alert.className = `${alertColors[type]} border rounded-md p-4 flex items-center gap-3`;
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
@endpush
@endsection