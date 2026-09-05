@extends('app')

@section('title', 'Connexion - VintApp')

@section('content')

<!-- Toast Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 hidden items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-2xl flex flex-col items-center gap-4">
        <div class="w-12 h-12 border-4 border-primary-300 border-t-primary rounded-full animate-spin"></div>
        <p class="text-gray-700 dark:text-gray-200 font-medium">Connexion en cours...</p>
    </div>
</div>

<div class="min-h-screen bg-slate-50 dark:bg-gray-900 flex">

    <!-- Panneau gauche décoratif (desktop) -->
    <div class="hidden lg:flex lg:w-[42%] relative overflow-hidden bg-primary items-center justify-center">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-1/4 -left-20 w-80 h-80 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-1/3 right-0 w-96 h-96 bg-purple-300 rounded-full blur-3xl"></div>
            <div class="absolute top-2/3 left-1/3 w-64 h-64 bg-purple-400 rounded-full blur-3xl"></div>
        </div>
        <div class="absolute inset-0 opacity-5 bg-[linear-gradient(rgba(255,255,255,.1)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,.1)_1px,transparent_1px)] bg-[size:40px_40px]"></div>
        <div class="absolute -bottom-24 -right-24 w-80 h-80 rounded-full border-2 border-white/20"></div>

        <div class="relative z-10 max-w-sm px-10 text-white space-y-8">
            <div class="w-16 h-16 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl flex items-center justify-center shadow-xl">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold leading-tight">
                Bon retour sur
                <span class="text-white">VintApp</span>
            </h2>
            <p class="text-white/70 text-lg leading-relaxed">
                Accédez à votre espace, suivez vos commandes et continuez à chiner des pièces vintage uniques.
            </p>
            <div class="space-y-4 pt-2">
                @php
                    $points = [
                        'Connexion sécurisée par email ou réseaux sociaux',
                        'Historique et favoris synchronisés',
                        'Paiements et données protégés',
                    ];
                @endphp
                @foreach ($points as $point)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/15 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-white/80 text-sm">{{ $point }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Panneau droit : formulaire -->
    <div class="flex-1 flex items-center justify-center p-6 sm:p-8 overflow-y-auto">
        <div class="w-full max-w-md py-8">

            <!-- Logo mobile -->
            <div class="lg:hidden text-center mb-6">
                <div class="w-14 h-14 bg-primary rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg ">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                </div>
            </div>

            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Se connecter</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-2">Entrez vos identifiants pour accéder à votre compte</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                    <div class="flex items-center text-red-800 dark:text-red-200 mb-2">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <span class="font-medium text-sm">Erreur de connexion</span>
                    </div>
                    <ul class="text-sm text-red-700 dark:text-red-300 list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl">
                    <div class="flex items-center text-emerald-800 dark:text-emerald-200">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-sm font-medium">{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                    <div class="flex items-center text-red-800 dark:text-red-200">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Boutons sociaux en haut -->
            <div class="space-y-3 mb-6">
                <button onclick="signInWithGoogle()"
                        type="button"
                        class="w-full flex items-center justify-center gap-3 px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm hover:border-primary-200 hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-primary-100">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285f4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34a853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#fbbc05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#ea4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span class="text-gray-700 dark:text-gray-200 font-medium text-sm">Continuer avec Google</span>
                </button>
            </div>

            <!-- Séparateur -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-3 bg-slate-50 dark:bg-gray-900 text-gray-400 font-medium uppercase tracking-wider">ou par email</span>
                </div>
            </div>

            <!-- Formulaire de connexion -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Adresse email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input type="email"
                               id="email"
                               name="email"
                               required
                               value="{{ old('email') }}"
                               autocomplete="email"
                               class="w-full pl-10 pr-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary transition-all duration-200 @error('email') border-red-400 focus:border-red-500 @enderror"
                               placeholder="nom@exemple.com">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Mot de passe
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input type="password"
                               id="password"
                               name="password"
                               required
                               autocomplete="current-password"
                               class="w-full pl-10 pr-11 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary transition-all duration-200 @error('password') border-red-400 focus:border-red-500 @enderror"
                               placeholder="Votre mot de passe">
                        <button type="button"
                                onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <svg id="password-eye" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox"
                               id="remember"
                               name="remember"
                               class="w-4 h-4 text-primary border-gray-300 dark:border-gray-600 rounded focus:ring-primary focus:ring-offset-0">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Se souvenir de moi</span>
                    </label>
                    <a href="{{ route('password.request') }}"
                       class="text-sm text-primary dark:text-primary-300 hover:text-primary-600 dark:hover:text-primary-200 font-medium transition-colors sm:text-right">
                        Mot de passe oublié ?
                    </a>
                </div>

                <button type="submit"
                        id="login-submit-btn"
                        class="w-full bg-primary hover:bg-primary-600 text-white font-semibold py-3 px-6 rounded-xl shadow-lg  hover:shadow-xl  transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-primary text-sm">
                    Se connecter
                </button>
            </form>

            <p class="text-center mt-6 text-sm text-gray-500 dark:text-gray-400">
                Pas encore de compte ?
                <a href="{{ route('register') }}" class="text-primary dark:text-primary-300 hover:text-primary-600 font-semibold transition-colors">
                    Créer un compte
                </a>
            </p>

            <div class="text-center mt-6">
                <p class="text-xs text-gray-400 dark:text-gray-500 flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Vos données sont protégées et sécurisées
                </p>
            </div>
        </div>
    </div>
</div>

<script>
const isCapacitorNative = typeof window !== 'undefined' &&
    (typeof window.Capacitor !== 'undefined' ||
     /capacitor/i.test(navigator.userAgent || ''));

window.signInWithGoogle = async function() {
    showLoading(true);

    // Dans l'app mobile (Capacitor), le popup Firebase est bloqué par la WebView.
    // On passe par le plugin natif @capacitor-firebase/authentication qui renvoie
    // un utilisateur Firebase (avec idToken compatible avec le backend Laravel).
    if (isCapacitorNative) {
        try {
            const { FirebaseAuthentication } = window.Capacitor?.Plugins || {};
            if (!FirebaseAuthentication) {
                throw new Error('Plugin FirebaseAuthentication non disponible');
            }

            const result = await FirebaseAuthentication.signInWithGoogle();
            const nativeUser = result.user;

            if (!nativeUser) {
                throw new Error('Aucune information utilisateur reçue');
            }

            const tokenResult = await FirebaseAuthentication.getIdToken();
            const idToken = tokenResult.token;

            // Token FCM natif pour les notifications push de l'app mobile
            let fcmToken = null;
            try {
                const messaging = window.Capacitor?.Plugins?.FirebaseMessaging;
                if (messaging) {
                    const perm = await messaging.requestPermissions();
                    if (perm && perm.receive !== 'denied') {
                        const t = await messaging.getToken();
                        fcmToken = (t && t.token) || null;
                    }
                }
            } catch (e) {
                console.warn('[login] Token FCM indisponible:', e);
            }

            const response = await fetch('{{ route("firebase.login") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    idToken: idToken,
                    name: nativeUser.displayName,
                    email: nativeUser.email,
                    provider: 'google',
                    firebase_uid: nativeUser.uid,
                    email_verified: nativeUser.emailVerified,
                    photo_url: nativeUser.photoUrl,
                    fcmToken: fcmToken,
                    deviceType: 'mobile'
                })
            });

            let data;
            try {
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Le serveur a retourné une réponse invalide. Veuillez réessayer.');
                }
                data = await response.json();
            } catch (parseError) {
                if (parseError.message.includes('invalide')) {
                    throw parseError;
                }
                throw new Error('Impossible de traiter la réponse du serveur');
            }

            if (response.ok && data.success) {
                showLoading(false);
                showToast('Connexion Google réussie !', 'success');
                setTimeout(() => {
                    window.location.href = data.redirect || '{{ route("home") }}';
                }, 800);
            } else {
                throw new Error(data.message || 'Erreur lors de la synchronisation avec le serveur');
            }
        } catch (error) {
            showLoading(false);
            let errorMessage = 'Erreur lors de la connexion Google';
            if (error && (error.code === 'auth/popup-closed-by-user' || error.code === 'canceled')) {
                errorMessage = 'Connexion annulée par l\'utilisateur';
            } else if (error && error.message) {
                errorMessage = `Erreur Google: ${error.message}`;
            }
            showToast(errorMessage, 'error');
        }
        return;
    }

    try {
        if (!firebase.apps.length) {
            throw new Error('Firebase n\'est pas initialisé');
        }

        const provider = new firebase.auth.GoogleAuthProvider();
        provider.addScope('email');
        provider.addScope('profile');
        provider.setCustomParameters({ prompt: 'select_account' });

        const result = await firebase.auth().signInWithPopup(provider);
        const user = result.user;

        if (!user) {
            throw new Error('Aucune information utilisateur reçue');
        }

        const idToken = await user.getIdToken();

        const response = await fetch('{{ route("firebase.login") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
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

        let data;
        try {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Le serveur a retourné une réponse invalide. Veuillez réessayer.');
            }
            data = await response.json();
        } catch (parseError) {
            if (parseError.message.includes('invalide')) {
                throw parseError;
            }
            throw new Error('Impossible de traiter la réponse du serveur');
        }

        if (response.ok && data.success) {
            showLoading(false);
            showToast('Connexion Google réussie !', 'success');
            setTimeout(() => {
                window.location.href = data.redirect || '{{ route("home") }}';
            }, 800);
        } else {
            throw new Error(data.message || 'Erreur lors de la synchronisation avec le serveur');
        }

    } catch (error) {
        showLoading(false);

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
                if (error.message && !error.code) {
                    errorMessage = error.message;
                } else if (error.message) {
                    errorMessage = `Erreur Google: ${error.message}`;
                }
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
            window.location.href = '{{ route("home") }}';
        }, 800);

    } catch (error) {
        showLoading(false);

        let errorMessage = 'Erreur lors de la connexion Facebook';
        if (error.code === 'auth/popup-closed-by-user') {
            errorMessage = 'Connexion annulée par l\'utilisateur';
        }

        showToast(errorMessage, 'error');
    }
};

function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('password-eye');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>';
    } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    }
}

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

function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    const colors = { success: 'bg-emerald-600', error: 'bg-red-600', warning: 'bg-amber-600', info: 'bg-primary' };
    const icons = {
        success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
        error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
        warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>',
        info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
    };

    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `${colors[type]} text-white px-5 py-3 rounded-xl shadow-lg flex items-center gap-3 transform translate-x-full transition-transform duration-300 text-sm`;
    toast.innerHTML = `
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">${icons[type]}</svg>
        <span class="font-medium flex-1">${message}</span>
        <button onclick="document.getElementById('${toastId}').remove()" class="hover:opacity-75 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;

    container.appendChild(toast);
    requestAnimationFrame(() => { toast.classList.remove('translate-x-full'); });

    setTimeout(() => {
        const el = document.getElementById(toastId);
        if (el) { el.classList.add('translate-x-full'); setTimeout(() => el.remove(), 300); }
    }, 4000);
}

function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('email');
    const form = document.querySelector('form');

    emailInput.addEventListener('blur', function() {
        const email = this.value.trim();
        if (email && !validateEmail(email)) {
            this.classList.add('border-red-400', 'focus:border-red-500');
            this.classList.remove('border-gray-200', 'dark:border-gray-700', 'focus:border-primary');
        } else {
            this.classList.remove('border-red-400', 'focus:border-red-500');
            this.classList.add('border-gray-200', 'dark:border-gray-700');
        }
    });

    form.addEventListener('submit', function(e) {
        const email = emailInput.value.trim();
        const password = document.getElementById('password').value;

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

        showLoading(true);
        const submitButton = document.getElementById('login-submit-btn');
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <span class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Connexion...
            </span>
        `;
    });

    emailInput.focus();
});

try {
    // Le SDK Firebase (app + auth) est initialisé par la layout 'app'
    firebase.auth().onAuthStateChanged(() => {});
} catch (error) {
    showToast('Erreur de configuration Firebase. Contactez l\'administrateur.', 'error');
}
</script>

@endsection
