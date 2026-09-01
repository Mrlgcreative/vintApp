@extends('app')

@section('title', 'Vérification Email - VintApp')

@section('content')

<!-- Toast Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

<div class="min-h-[calc(100vh-4rem)] flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-900 py-10 px-6 md:p-10 gap-8">
    <!-- Logo -->
    <a href="{{ url('/') }}" class="flex items-center gap-2.5 self-center font-medium group">
        <div class="w-9 h-9 rounded-lg bg-vinted-primary-600 text-white flex items-center justify-center shadow-md shadow-vinted-primary-600/30 group-hover:shadow-lg group-hover:shadow-vinted-primary-600/40 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
            </svg>
        </div>
        <span class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ $appName ?? config('app.name', 'VintApp') }}
        </span>
    </a>

    <!-- Card principale -->
    <div class="w-full max-w-sm flex flex-col gap-6">
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-6 sm:p-8">
            <!-- Header -->
            <div class="flex flex-col gap-2 text-center mb-6">
                <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                    Vérification par code
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Saisissez le code à 6 chiffres envoyé à votre email
                </p>
                <div class="mt-1.5 inline-flex items-center justify-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-200">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    {{ Auth::user()->email }}
                </div>
            </div>

            @if (session('success'))
                <div class="mb-5 bg-vinted-success-50 dark:bg-vinted-success-500/10 border border-vinted-success-200 dark:border-vinted-success-500/30 rounded-md px-4 py-3 text-sm text-vinted-success-700 dark:text-vinted-success-300">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-5 bg-vinted-danger-50 dark:bg-vinted-danger-500/10 border border-vinted-danger-200 dark:border-vinted-danger-500/30 rounded-md px-4 py-3 text-sm text-vinted-danger-600 dark:text-vinted-danger-300">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 bg-vinted-danger-50 dark:bg-vinted-danger-500/10 border border-vinted-danger-200 dark:border-vinted-danger-500/30 rounded-md px-4 py-3 text-sm text-vinted-danger-600 dark:text-vinted-danger-300">
                    {{ $errors->first('verification_code') }}
                </div>
            @endif

            <!-- Formulaire de saisie du code -->
            <form method="POST" action="{{ route('verification.code.verify') }}" id="verifyForm">
                @csrf
                <input type="hidden" name="verification_code" id="verification_code_hidden">

                <div class="grid grid-cols-6 gap-2 mb-6" id="otpBoxes">
                    @for($i = 0; $i < 6; $i++)
                        <input
                            type="text"
                            inputmode="numeric"
                            maxlength="1"
                            pattern="[0-9]"
                            autocomplete="off"
                            aria-label="Chiffre {{ $i + 1 }}"
                            class="otp-input w-full aspect-square text-center text-xl font-semibold font-mono text-gray-900 dark:text-white bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md focus:border-vinted-primary-500 focus:ring-2 focus:ring-vinted-primary-500/30 outline-none transition-shadow shadow-sm"
                        >
                    @endfor
                </div>

                <button type="submit" class="w-full h-10 inline-flex items-center justify-center gap-2 rounded-md bg-vinted-primary-600 text-white text-sm font-medium hover:bg-vinted-primary-700 active:scale-[0.98] transition-all shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-vinted-primary-300 focus-visible:ring-offset-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Vérifier le code
                </button>
            </form>

            <div class="flex items-center gap-3 my-6">
                <div class="h-px flex-1 bg-gray-200 dark:bg-gray-800"></div>
                <span class="text-xs text-gray-500 dark:text-gray-400">Vous n'avez pas reçu de code ?</span>
                <div class="h-px flex-1 bg-gray-200 dark:bg-gray-800"></div>
            </div>

            <!-- Renvoyer -->
            <form method="POST" action="{{ route('verification.code.resend') }}">
                @csrf
                <button type="submit" class="w-full h-10 inline-flex items-center justify-center gap-2 rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Renvoyer le code
                </button>
            </form>

            <p class="text-center text-xs text-gray-500 dark:text-gray-400 mt-4">
                Le code expire dans 15 minutes
            </p>
        </div>

        <!-- Actions -->
        <div class="text-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 inline-flex items-center gap-1.5 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Se déconnecter
                </button>
            </form>

            <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">
                Problème avec la vérification ?
                <a href="mailto:{{ config('mail.from.address') }}" class="text-vinted-primary-600 dark:text-vinted-primary-400 hover:text-vinted-primary-700 dark:hover:text-vinted-primary-300 font-medium">
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
        success: 'bg-vinted-success-600',
        error: 'bg-vinted-danger-500',
        warning: 'bg-vinted-warning-500',
        info: 'bg-vinted-primary-600'
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

    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);

    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 5000);
}
</script>

<script>
// OTP : auto-focus, navigation clavier, copier-coller et soumission automatique
(function() {
    const boxes = Array.from(document.querySelectorAll('.otp-input'));
    const hidden = document.getElementById('verification_code_hidden');
    const form = document.getElementById('verifyForm');

    if (!boxes.length || !hidden) return;

    function updateHidden() {
        hidden.value = boxes.map(b => b.value).join('');
    }

    function focusBox(index) {
        if (boxes[index]) boxes[index].focus();
    }

    function handleInput(index) {
        const box = boxes[index];
        box.value = box.value.replace(/[^0-9]/g, '').substring(0, 1);
        updateHidden();
        if (box.value && index < boxes.length - 1) {
            focusBox(index + 1);
        }
        if (hidden.value.length === 6) {
            form.submit();
        }
    }

    function handleKeydown(index, e) {
        if (e.key === 'Backspace') {
            if (!boxes[index].value && index > 0) {
                e.preventDefault();
                focusBox(index - 1);
                boxes[index - 1].value = '';
                updateHidden();
            }
        } else if (e.key === 'ArrowLeft' && index > 0) {
            focusBox(index - 1);
        } else if (e.key === 'ArrowRight' && index < boxes.length - 1) {
            focusBox(index + 1);
        }
    }

    function handlePaste(e) {
        e.preventDefault();
        const digits = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').substring(0, 6);
        if (!digits) return;
        boxes.forEach((box, i) => {
            box.value = digits[i] || '';
        });
        updateHidden();
        focusBox(Math.min(digits.length, boxes.length - 1));
        if (digits.length === 6) {
            form.submit();
        }
    }

    boxes.forEach((box, index) => {
        box.addEventListener('input', () => handleInput(index));
        box.addEventListener('keydown', (e) => handleKeydown(index, e));
        box.addEventListener('paste', handlePaste);
    });

    boxes[0].focus();
})();
</script>

@endsection