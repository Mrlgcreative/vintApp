@extends('app')

@section('title', 'Vérification Email - VintApp')

@section('content')

<!-- Toast Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-50 to-accent-50 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md">
        <!-- Card principale -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-primary-600 to-accent-600 px-8 py-8 text-center">
                <div class="flex items-center justify-center w-20 h-20 mx-auto mb-4 bg-white/20 backdrop-blur-sm rounded-full">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white">Code de vérification</h1>
                <p class="text-primary-100 mt-2">Saisissez le code envoyé à votre email</p>
            </div>

            <!-- Contenu -->
            <div class="px-8 py-8">
                <!-- Avertissement accès bloqué -->
                <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 rounded-r-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">Accès bloqué</p>
                            <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">
                                Vous devez vérifier votre email pour accéder à VintApp
                            </p>
                        </div>
                    </div>
                </div>

                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded-r-lg">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-r-lg">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Email utilisateur -->
                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="font-semibold text-gray-800 dark:text-gray-100">{{ Auth::user()->email }}</span>
                    </div>
                </div>

                <!-- Formulaire de saisie du code -->
                <form method="POST" action="{{ route('verification.code.verify') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="verification_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Code de vérification (6 chiffres)
                        </label>
                        <input
                            type="text"
                            id="verification_code"
                            name="verification_code"
                            maxlength="6"
                            pattern="[0-9]{6}"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            placeholder="000000"
                            class="w-full px-4 py-4 text-center text-2xl tracking-widest font-mono border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:bg-gray-900 dark:text-white transition-all @error('verification_code') border-red-500 @enderror"
                            required
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 6)"
                        >
                        @error('verification_code')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full px-6 py-4 bg-gradient-to-r from-primary-600 to-accent-600 hover:from-primary-700 hover:to-accent-700 text-white rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Vérifier le code
                    </button>
                </form>

                <!-- Actions supplémentaires -->
                <div class="mt-6 space-y-4">
                    <!-- Renvoyer le code -->
                    <form method="POST" action="{{ route('verification.code.resend') }}">
                        @csrf
                        <button
                            type="submit"
                            class="w-full px-6 py-3 bg-white dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-800 hover:border-gray-400 dark:hover:border-gray-500 transition-all flex items-center justify-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Renvoyer le code
                        </button>
                    </form>

                    <!-- Déconnexion -->
                    <form method="POST" action="{{ route('logout') }}" class="text-center">
                        @csrf
                        <button
                            type="submit"
                            class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 py-2 inline-flex items-center gap-1.5 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Se déconnecter
                        </button>
                    </form>
                </div>

                <!-- Informations -->
                <div class="mt-8 p-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1 flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Le code expire dans 15 minutes
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Vérifiez vos spams si vous ne recevez pas l'email
                    </p>
                </div>
            </div>
        </div>

        <!-- Aide supplémentaire -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Problème avec la vérification ?
                <a href="mailto:{{ config('mail.from.address') }}" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium">
                    Contactez le support
                </a>
            </p>
        </div>
    </div>
</div>

<!-- Système de toast -->
<script>
function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');

    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-amber-500',
        info: 'bg-blue-500'
    };

    const icons = {
        success: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        error: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        warning: 'M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z',
        info: 'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z'
    };

    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `${colors[type]} text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-2 transform translate-x-full transition-transform duration-300`;
    toast.innerHTML = `
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="${icons[type]}"/></svg>
        <span>${message}</span>
    `;

    container.appendChild(toast);

    // Animation d'entrée
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);

    // Auto-remove après 5 secondes
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 5000);
}

// Auto-focus sur le champ code
document.addEventListener('DOMContentLoaded', function() {
    const codeInput = document.getElementById('verification_code');
    if (codeInput) {
        codeInput.focus();
    }
});
</script>

@endsection
