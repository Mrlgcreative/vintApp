@extends('app')

@section('title', 'Définir votre mot de passe - VintApp')

@section('content')

<!-- Toast Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

<div class="min-h-screen bg-slate-50 dark:bg-gray-900 flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Halos décoratifs -->
    <div class="pointer-events-none absolute -top-32 -right-32 w-96 h-96 bg-primary-100 rounded-full blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-32 -left-32 w-96 h-96 bg-primary-100 rounded-full blur-3xl"></div>

    <div class="w-full max-w-lg">
        <div class="bg-white dark:bg-gray-800/95 backdrop-blur-sm rounded-3xl border border-gray-100 dark:border-gray-700 shadow-2xl p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg ">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">Définissez votre mot de passe</h1>
                <p class="text-gray-600 dark:text-gray-300">
                    Bonjour <strong class="text-primary">{{ $name }}</strong><br>
                    Votre compte a été approuvé. Créez votre mot de passe pour accéder à VintApp.
                </p>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                    <div class="flex items-center text-red-800 dark:text-red-200">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                    <div class="flex items-center text-red-800 dark:text-red-200 mb-2">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <span class="font-medium text-sm">Erreur</span>
                    </div>
                    <ul class="text-sm text-red-700 dark:text-red-300 list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.setup.store') }}" method="POST" id="passwordForm" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Nouveau mot de passe
                    </label>
                    <div class="relative">
                        <input type="password"
                               class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary transition-all duration-300 @error('password') border-red-400 focus:border-red-500 focus:ring-red-100 @enderror pr-12"
                               id="password"
                               name="password"
                               required
                               autocomplete="new-password"
                               placeholder="Minimum 8 caractères"
                               onkeyup="checkPasswordStrength()">
                        <button type="button"
                                onclick="togglePasswordVisibility('password', 'togglePassword')"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <svg id="togglePassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full mt-2 overflow-hidden" id="passwordStrength"></div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Confirmer le mot de passe
                    </label>
                    <div class="relative">
                        <input type="password"
                               class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary transition-all duration-300 @error('password_confirmation') border-red-400 focus:border-red-500 focus:ring-red-100 @enderror pr-12"
                               id="password_confirmation"
                               name="password_confirmation"
                               required
                               autocomplete="new-password"
                               placeholder="Répétez le mot de passe">
                        <button type="button"
                                onclick="togglePasswordVisibility('password_confirmation', 'togglePasswordConfirm')"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <svg id="togglePasswordConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 border-l-4 border-primary rounded-r-xl p-4">
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-1.5 mb-1">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Exigences du mot de passe :
                    </p>
                    <ul class="text-sm text-gray-600 dark:text-gray-400 list-disc list-inside space-y-0.5">
                        <li>Au moins 8 caractères</li>
                        <li>Recommdé : majuscules, minuscules et chiffres</li>
                        <li>Évitez les mots de passe trop simples</li>
                    </ul>
                </div>

                <button type="submit" id="submitBtn"
                        class="w-full bg-primary hover:bg-primary-600 text-white font-semibold py-3 px-4 rounded-xl shadow-lg  hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Définir mon mot de passe et me connecter</span>
                </button>
            </form>

            <div class="text-center mt-5">
                <p class="text-xs text-gray-400 dark:text-gray-500 flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Connexion sécurisée - Vos données sont protégées
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    }
}

function checkPasswordStrength() {
    const password = document.getElementById('password').value;
    const strengthBar = document.getElementById('passwordStrength');

    let strength = 0;

    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^a-zA-Z\d]/.test(password)) strength++;

    const colors = ['bg-red-500', 'bg-orange-500', 'bg-amber-500', 'bg-emerald-500', 'bg-emerald-600'];
    strengthBar.innerHTML = '';
    if (password.length > 0) {
        const bar = document.createElement('div');
        const pct = strength <= 2 ? 33 : strength <= 3 ? 66 : 100;
        bar.className = `h-full rounded-full transition-all duration-300 ${colors[Math.min(strength, 5) - 1]}`;
        bar.style.width = pct + '%';
        strengthBar.appendChild(bar);
    }
}

document.getElementById('passwordForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirmation').value;
    const submitBtn = document.getElementById('submitBtn');

    if (password !== passwordConfirm) {
        e.preventDefault();
        showToast('Les mots de passe ne correspondent pas !', 'error');
        return false;
    }

    if (password.length < 8) {
        e.preventDefault();
        showToast('Le mot de passe doit contenir au moins 8 caractères !', 'error');
        return false;
    }

    submitBtn.disabled = true;
    submitBtn.innerHTML = `
        <span class="flex items-center justify-center gap-2">
            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Création du compte...
        </span>
    `;
});

function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    if (!container) return;
    const colors = { success: 'bg-emerald-600', error: 'bg-red-600', warning: 'bg-amber-600', info: 'bg-primary' };
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `${colors[type]} text-white px-5 py-3 rounded-xl shadow-lg flex items-center gap-3 transform translate-x-full transition-transform duration-300 text-sm`;
    toast.innerHTML = `<span class="font-medium flex-1">${message}</span>`;
    container.appendChild(toast);
    requestAnimationFrame(() => toast.classList.remove('translate-x-full'));
    setTimeout(() => {
        const el = document.getElementById(toastId);
        if (el) { el.classList.add('translate-x-full'); setTimeout(() => el.remove(), 300); }
    }, 4000);
}
</script>

@endsection
