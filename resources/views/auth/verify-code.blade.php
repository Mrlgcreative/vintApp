@extends('app')

@section('title', 'Vérification Email - VintApp')

@section('content')

<!-- Toast Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Card principale -->
        <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-2xl overflow-hidden border border-white/50">
            <!-- Header avec icône -->
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 text-white text-center py-8">
                <div class="inline-flex items-center justify-center bg-white/20 backdrop-blur-sm rounded-full mb-4 w-20 h-20">
                    <i class="fas fa-shield-check text-4xl"></i>
                </div>
                <h1 class="text-2xl font-bold mb-2">Code de vérification</h1>
                <p class="text-purple-100 text-sm">Saisissez le code envoyé à votre email</p>
            </div>

            <!-- Contenu -->
            <div class="p-8">
                <!-- Message d'avertissement -->
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-lock text-amber-600 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-semibold text-amber-800">Accès bloqué</h3>
                            <p class="text-sm text-amber-700 mt-1">
                                Vous devez vérifier votre email pour accéder à VintApp
                            </p>
                        </div>
                    </div>
                </div>

                @if (session('success'))
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 text-lg mr-3"></i>
                            <p class="text-green-800 text-sm font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-600 text-lg mr-3"></i>
                            <p class="text-red-800 text-sm font-medium">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Email utilisateur -->
                <div class="bg-gray-50 rounded-xl p-4 mb-6 text-center">
                    <div class="flex items-center justify-center">
                        <i class="fas fa-envelope text-gray-500 mr-2"></i>
                        <span class="font-semibold text-gray-800">{{ Auth::user()->email }}</span>
                    </div>
                </div>

                <!-- Formulaire de saisie du code -->
                <form method="POST" action="{{ route('verification.code.verify') }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="verification_code" class="block text-sm font-semibold text-gray-700 mb-3">
                            Code de vérification (6 chiffres)
                        </label>
                        <input 
                            type="text" 
                            id="verification_code"
                            name="verification_code"
                            maxlength="6"
                            pattern="[0-9]{6}"
                            class="w-full text-center text-2xl font-bold tracking-widest bg-white border-2 border-gray-200 rounded-xl py-4 px-6 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all duration-300 @error('verification_code') border-red-500 @enderror"
                            placeholder="000000"
                            required
                            autocomplete="off"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 6)"
                        >
                        @error('verification_code')
                            <p class="text-red-500 text-sm mt-2 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 focus:ring-4 focus:ring-purple-200"
                    >
                        <i class="fas fa-check mr-2"></i>
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
                            class="w-full bg-white border-2 border-gray-200 text-gray-700 font-medium py-3 px-6 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all duration-300"
                        >
                            <i class="fas fa-redo mr-2"></i>
                            Renvoyer le code
                        </button>
                    </form>

                    <!-- Déconnexion -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button 
                            type="submit" 
                            class="w-full text-gray-500 hover:text-gray-700 text-sm py-2 transition-colors duration-300"
                        >
                            <i class="fas fa-sign-out-alt mr-1"></i>
                            Se déconnecter
                        </button>
                    </form>
                </div>

                <!-- Informations -->
                <div class="mt-8 text-center text-sm text-gray-500">
                    <p class="mb-2">
                        <i class="fas fa-clock mr-1"></i>
                        Le code expire dans 15 minutes
                    </p>
                    <p class="text-xs">
                        Vérifiez vos spams si vous ne recevez pas l'email
                    </p>
                </div>
            </div>
        </div>

        <!-- Aide supplémentaire -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-600">
                Problème avec la vérification ? 
                <a href="mailto:{{ config('mail.from.address') }}" class="text-purple-600 hover:text-purple-800 font-medium">
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
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    };
    
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `${colors[type]} text-white px-6 py-3 rounded-xl shadow-lg flex items-center space-x-2 transform translate-x-full transition-transform duration-300`;
    toast.innerHTML = `
        <i class="${icons[type]}"></i>
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