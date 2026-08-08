@extends('app')

@section('title', 'Vérifiez votre email - VintApp')

@section('content')

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-50 to-accent-50 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md">
        <!-- Card principale -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-primary-600 to-accent-600 px-8 py-8 text-center">
                <div class="flex items-center justify-center w-20 h-20 mx-auto mb-4 bg-white/20 backdrop-blur-sm rounded-full">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white">Vérifiez votre email</h1>
                <p class="text-primary-100 mt-2">Dernière étape avant d'accéder à VintApp</p>
            </div>

            <!-- Contenu -->
            <div class="px-8 py-8">
                <!-- Avertissement accès restreint -->
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-r-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-bold text-red-800 dark:text-red-200">Accès restreint</p>
                            <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                                Votre compte est actuellement en attente de vérification d'email. Vous ne pouvez pas accéder aux fonctionnalités tant que votre email n'est pas vérifié.
                            </p>
                        </div>
                    </div>
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded-r-lg">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-green-800 dark:text-green-200">Email envoyé !</p>
                                <p class="text-xs text-green-700 dark:text-green-300 mt-1">
                                    Un nouveau lien de vérification a été envoyé à votre adresse email.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

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

                @if (session('warning'))
                    <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 rounded-r-lg">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.008v.008H12v-.008z"></path>
                            </svg>
                            <p class="text-sm font-medium text-amber-800 dark:text-amber-200">{{ session('warning') }}</p>
                        </div>
                    </div>
                @endif

                @if (session('info'))
                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded-r-lg">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm font-medium text-blue-800 dark:text-blue-200">{{ session('info') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Message d'information -->
                <div class="text-center mb-6">
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                        Merci de vous être inscrit ! Avant de commencer, pourriez-vous vérifier votre adresse email en cliquant sur le lien que nous venons de vous envoyer ?
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center justify-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Vérifiez votre boîte de réception et vos spams
                    </p>
                </div>

                <!-- Email utilisateur -->
                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="font-semibold text-gray-800 dark:text-gray-100">{{ Auth::user()->email }}</span>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-accent-500 text-white flex items-center justify-center text-sm font-bold">
                            1
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 pt-1.5">Ouvrez votre boîte email</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-accent-500 text-white flex items-center justify-center text-sm font-bold">
                            2
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 pt-1.5">Cliquez sur le lien de vérification</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-accent-500 text-white flex items-center justify-center text-sm font-bold">
                            3
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 pt-1.5">Revenez sur VintApp pour commencer</p>
                    </div>
                </div>

                <!-- Bouton renvoyer -->
                <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
                    @csrf
                    <button type="submit" class="w-full px-6 py-4 bg-gradient-to-r from-primary-600 to-accent-600 hover:from-primary-700 hover:to-accent-700 text-white rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"></path>
                        </svg>
                        Renvoyer l'email de vérification
                    </button>
                </form>

                <!-- Déconnexion -->
                <div class="text-center">
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

            <!-- Footer -->
            <div class="px-8 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"></path>
                    </svg>
                    Vos données sont sécurisées et confidentielles
                </div>
            </div>
        </div>

        <!-- Aide supplémentaire -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Vous n'avez pas reçu l'email ?
                <a href="mailto:{{ config('mail.from.address') }}" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium">
                    Contactez le support
                </a>
            </p>
        </div>
    </div>
</div>

@endsection
