@extends('app')

@section('title', 'Vérifiez votre email - VintApp')

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
        <!-- Card Principal -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-6 sm:p-8">
            <!-- Header -->
            <div class="flex flex-col items-center gap-2 text-center mb-6">
                <div class="w-11 h-11 rounded-full bg-vinted-primary-50 dark:bg-vinted-primary-500/10 flex items-center justify-center mb-1">
                    <svg class="w-5 h-5 text-vinted-primary-600 dark:text-vinted-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                    Vérifiez votre email
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Dernière étape avant d'accéder à VintApp
                </p>
            </div>

            <!-- Avertissement accès restreint -->
            <x-alert variant="danger">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium">Accès restreint</p>
                        <p class="text-sm opacity-90 mt-1">
                            Votre compte est en attente de vérification d'email. Certaines fonctionnalités restent indisponibles tant que votre email n'est pas vérifié.
                        </p>
                    </div>
                </div>
            </x-alert>

            @if (session('status') == 'verification-link-sent')
                <x-alert variant="success" class="mt-3">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium">Email envoyé !</p>
                            <p class="text-sm opacity-90 mt-1">Un nouveau lien de vérification a été envoyé à votre adresse email.</p>
                        </div>
                    </div>
                </x-alert>
            @endif

            @if (session('success'))
                <x-alert variant="success" class="mt-3">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                </x-alert>
            @endif

            @if (session('warning'))
                <x-alert variant="warning" class="mt-3">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.008v.008H12v-.008z"></path>
                        </svg>
                        <p class="text-sm font-medium">{{ session('warning') }}</p>
                    </div>
                </x-alert>
            @endif

            @if (session('info'))
                <x-alert variant="info" class="mt-3">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm font-medium">{{ session('info') }}</p>
                    </div>
                </x-alert>
            @endif

            <!-- Message d'information -->
            <div class="text-center mt-5">
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                    Merci de vous être inscrit ! Avant de commencer, vérifiez votre adresse email en cliquant sur le lien que nous venons de vous envoyer.
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Vérifiez votre boîte de réception et vos spams
                </p>
            </div>

            <!-- Email utilisateur -->
            <div class="mt-5 rounded-md border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 px-4 py-3 flex items-center justify-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ Auth::user()->email }}</span>
            </div>

            <!-- Étapes -->
            <div class="mt-5 rounded-md border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 p-4 space-y-3">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-vinted-primary-600 text-white flex items-center justify-center text-xs font-bold">1</div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 pt-0.5">Ouvrez votre boîte email</p>
                </div>
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-vinted-primary-600 text-white flex items-center justify-center text-xs font-bold">2</div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 pt-0.5">Cliquez sur le lien de vérification</p>
                </div>
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-vinted-primary-600 text-white flex items-center justify-center text-xs font-bold">3</div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 pt-0.5">Revenez sur VintApp pour commencer</p>
                </div>
            </div>

            <!-- Bouton renvoyer -->
            <form method="POST" action="{{ route('verification.send') }}" class="mt-6">
                @csrf
                <button type="submit"
                        class="w-full h-10 inline-flex items-center justify-center gap-2 rounded-md bg-vinted-primary-600 text-white text-sm font-medium hover:bg-vinted-primary-700 active:scale-[0.98] transition-all shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-vinted-primary-300 focus-visible:ring-offset-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"></path>
                    </svg>
                    Renvoyer l'email de vérification
                </button>
            </form>

            <!-- Déconnexion -->
            <div class="mt-4 text-center">
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 py-2 inline-flex items-center gap-1.5 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Se déconnecter
                    </button>
                </form>
            </div>
        </div>

        <!-- Aide supplémentaire -->
        <div class="text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Vous n'avez pas reçu l'email ?
                <a href="mailto:{{ config('mail.from.address') }}" class="text-vinted-primary-600 dark:text-vinted-primary-400 hover:text-vinted-primary-700 dark:hover:text-vinted-primary-300 font-medium">
                    Contactez le support
                </a>
            </p>
        </div>
    </div>
</div>
@endsection