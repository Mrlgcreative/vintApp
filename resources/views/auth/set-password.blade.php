@extends('app')

@section('title', 'Définir votre mot de passe - VintApp')

@section('content')

<!-- Toast Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

<div class="min-h-[calc(100vh-4rem)] flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-900 py-10 px-6 md:p-10 gap-8">
    <!-- Logo -->
    <a href="{{ url('/') }}" class="flex items-center gap-2.5 self-center font-medium group">
        <div class="w-9 h-9 rounded-lg bg-vinted-primary-600 text-white flex items-center justify-center shadow-md shadow-vinted-primary-600/30 group-hover:shadow-lg group-hover:shadow-vinted-primary-600/40 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
            </svg>
        </div>
        <span class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ $appName ?? config('app.name', 'VintApp') }}
        </span>
    </a>

    <div class="w-full max-w-sm flex flex-col gap-6">
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-6 sm:p-8">
            <!-- Header -->
            <div class="flex flex-col gap-2 text-center mb-6">
                <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Définissez votre mot de passe</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Bonjour <strong class="text-vinted-primary-600 dark:text-vinted-primary-400">{{ $name }}</strong><br>
                    Votre compte a été approuvé. Créez votre mot de passe pour accéder à VintApp.
                </p>
            </div>

            @if(session('error'))
                <x-alert variant="danger" class="mb-5">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                </x-alert>
            @endif

            @if ($errors->any())
                <x-alert variant="danger" class="mb-5">
                    <ul class="text-sm space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif

            <form action="{{ route('password.setup.store') }}" method="POST" id="passwordForm" class="flex flex-col gap-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="flex flex-col gap-2">
                    <label for="password" class="text-sm font-medium text-gray-700 dark:text-gray-300">Nouveau mot de passe</label>
                    <div class="relative">
                        <input type="password"
                               class="w-full pl-4 pr-12 py-2.5 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-vinted-primary-500 focus:ring-2 focus:ring-vinted-primary-500/30 outline-none transition-colors @error('password') border-red-400 focus:border-red-500 focus:ring-red-100 @enderror"
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
                    <div class="h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full mt-1 overflow-hidden" id="passwordStrength"></div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-2">
                    <label for="password_confirmation" class="text-sm font-medium text-gray-700 dark:text-gray-300">Confirmer le mot de passe</label>
                    <div class="relative">
                        <input type="password"
                               class="w-full pl-4 pr-12 py-2.5 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-vinted-primary-500 focus:ring-2 focus:ring-vinted-primary-500/30 outline-none transition-colors @error('password_confirmation') border-red-400 focus:border-red-500 focus:ring-red-100 @enderror"
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

                <div class="rounded-md border-l-4 border-vinted-primary-600 bg-gray-50 dark:bg-gray-800/50 p-4">
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100 flex items-center gap-1.5 mb-1">
                        <svg class="w-4 h-4 text-vinted-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        class="w-full h-10 inline-flex items-center justify-center gap-2 rounded-md bg-vinted-primary-600 text-white text-sm font-medium hover:bg-vinted-primary-700 active:scale-[0.98] transition-all shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-vinted-primary-300 focus-visible:ring-offset-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Définir mon mot de passe et me connecter</span>
                </button>
            </form>

            <p class="mt-5 text-center text-xs text-gray-400 dark:text-gray-500 flex items-center justify-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Connexion sécurisée - Vos données sont protégées
            </p>
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
    const colors = { success: 'bg-emerald-600', error: 'bg-red-600', warning: 'bg-amber-600', info: 'bg-vinted-primary-600' };
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