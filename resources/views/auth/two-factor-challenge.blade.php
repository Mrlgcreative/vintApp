@extends('app')

@section('title', 'Authentification à deux facteurs')

@section('content')
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
        <!-- Card -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-6 sm:p-8">
            <!-- Header -->
            <div class="flex flex-col items-center gap-2 text-center mb-6">
                <div class="w-11 h-11 rounded-full bg-vinted-primary-50 dark:bg-vinted-primary-500/10 flex items-center justify-center mb-1">
                    <svg class="w-5 h-5 text-vinted-primary-600 dark:text-vinted-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Authentification requise</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Entrez votre code de sécurité</p>
            </div>

            <!-- Info User -->
            <div class="flex items-center gap-3 mb-5 rounded-md border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 p-3">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}"
                         alt="{{ $user->name }}"
                         class="w-10 h-10 rounded-full object-cover">
                @else
                    <div class="w-10 h-10 rounded-full bg-vinted-primary-600 text-white flex items-center justify-center font-bold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <div class="min-w-0">
                    <h3 class="font-medium text-sm text-gray-900 dark:text-white truncate">{{ $user->name }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</p>
                </div>
            </div>

            <!-- Alert Info -->
            <x-alert variant="info" class="mb-5">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm">
                        Votre compte est protégé par l'authentification à deux facteurs. Ouvrez votre application d'authentification (Google Authenticator, Authy, etc.) et entrez le code à 6 chiffres.
                    </p>
                </div>
            </x-alert>

            <!-- Form -->
            <form id="verify2FAForm" class="flex flex-col gap-4">
                @csrf

                <div class="flex flex-col gap-2">
                    <label for="code" class="text-sm font-medium text-gray-700 dark:text-gray-300">Code d'authentification</label>
                    <input type="text"
                           id="code"
                           name="code"
                           maxlength="6"
                           pattern="[0-9]*"
                           inputmode="numeric"
                           autocomplete="one-time-code"
                           placeholder="000000"
                           class="w-full px-4 py-3 text-center text-2xl tracking-[0.5em] font-mono bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-vinted-primary-500 focus:ring-2 focus:ring-vinted-primary-500/30 outline-none transition-colors"
                           required
                           autofocus>
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                        Entrez le code à 6 chiffres de votre application
                    </p>
                </div>

                <!-- Error Message -->
                <div id="errorMessage" class="hidden rounded-md bg-vinted-danger-50 dark:bg-vinted-danger-500/10 border border-vinted-danger-200 dark:border-vinted-danger-500/30 px-4 py-3">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-vinted-danger-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-vinted-danger-600 dark:text-vinted-danger-300" id="errorText"></p>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        id="verifyButton"
                        class="w-full h-10 inline-flex items-center justify-center gap-2 rounded-md bg-vinted-primary-600 text-white text-sm font-medium hover:bg-vinted-primary-700 active:scale-[0.98] transition-all shadow-sm disabled:opacity-50 disabled:cursor-not-allowed disabled:active:scale-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-vinted-primary-300 focus-visible:ring-offset-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Vérifier le code</span>
                </button>
            </form>

            <!-- Recovery Code Link -->
            <div class="mt-4 text-center">
                <button onclick="showRecoveryInput()"
                        class="text-sm text-vinted-primary-600 dark:text-vinted-primary-400 hover:text-vinted-primary-700 dark:hover:text-vinted-primary-300 font-medium transition-colors">
                    Utiliser un code de récupération
                </button>
            </div>

            <!-- Recovery Code Form (Hidden by default) -->
            <div id="recoveryForm" class="hidden mt-5 rounded-md border border-vinted-danger-200 dark:border-vinted-danger-500/30 bg-vinted-danger-50 dark:bg-vinted-danger-500/10 p-4">
                <h4 class="font-medium text-gray-900 dark:text-white mb-2 flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4 text-vinted-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                       class="w-full px-4 py-2.5 text-sm font-mono bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-vinted-primary-500 focus:ring-2 focus:ring-vinted-primary-500/30 outline-none transition-colors mb-3">
                <div class="flex gap-2">
                    <button onclick="verifyRecoveryCode()"
                            class="flex-1 h-10 inline-flex items-center justify-center rounded-md bg-vinted-primary-600 text-white text-sm font-medium hover:bg-vinted-primary-700 active:scale-[0.98] transition-all shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-vinted-primary-300 focus-visible:ring-offset-1">
                        Vérifier
                    </button>
                    <button onclick="hideRecoveryInput()"
                            class="px-4 h-10 inline-flex items-center justify-center rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        Annuler
                    </button>
                </div>
            </div>

            <!-- Warning -->
            <div class="mt-5 rounded-md border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">Sécurité de votre compte</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Vous devez vérifier votre identité pour accéder à votre compte. Cette étape ne peut pas être ignorée.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logout -->
        <div class="flex items-center justify-between">
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Se déconnecter
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>

            <p class="text-xs text-gray-500 dark:text-gray-400">Protégé par 2FA</p>
        </div>

        <!-- Help Link -->
        <div class="text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Problème avec votre code ?
                <a href="#" class="text-vinted-primary-600 dark:text-vinted-primary-400 hover:text-vinted-primary-700 dark:hover:text-vinted-primary-300 font-medium">
                    Contactez le support
                </a>
            </p>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-900 rounded-xl p-8 shadow-2xl border border-gray-200 dark:border-gray-800">
        <div class="flex flex-col items-center gap-4">
            <div class="w-12 h-12 border-4 border-gray-200 dark:border-gray-700 border-t-vinted-primary-600 rounded-full animate-spin"></div>
            <p class="text-gray-900 dark:text-white font-medium text-sm">Vérification en cours...</p>
        </div>
    </div>
</div>

@push('scripts')
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
        const response = await fetch('{{ route("two-factor.verify") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code: code })
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            // Succès - redirection
            window.location.href = data.redirect || '{{ route("home") }}';
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
        const response = await fetch('{{ route("two-factor.verify") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code: code })
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            window.location.href = data.redirect || '{{ route("home") }}';
        } else {
            loadingOverlay.classList.add('hidden');
            errorText.textContent = data.message || 'Code de récupération invalide.';
            errorMessage.classList.remove('hidden');
            document.getElementById('recoveryCode').value = '';
            document.getElementById('recoveryCode').focus();
        }
    } catch (error) {
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
@endpush
@endsection