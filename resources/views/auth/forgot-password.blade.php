@extends('app')

@section('title', 'Mot de passe oublié - VintApp')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-gray-900 flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Halos décoratifs -->
    <div class="pointer-events-none absolute -top-32 -right-32 w-96 h-96 bg-primary-100 rounded-full blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-32 -left-32 w-96 h-96 bg-primary-100 rounded-full blur-3xl"></div>

    <div class="w-full max-w-lg">
        <!-- Card Principal -->
        <div class="bg-white dark:bg-gray-800/95 backdrop-blur-sm rounded-3xl border border-gray-100 dark:border-gray-700 shadow-2xl p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg ">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Mot de passe oublié</h1>
                <p class="text-gray-600 dark:text-gray-300">Entrez votre email pour recevoir un lien de réinitialisation</p>
            </div>

            <!-- Message de succès -->
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

            <!-- Messages d'erreur -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                    <div class="flex items-center text-red-800 dark:text-red-200 mb-2">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <span class="font-medium">Erreur</span>
                    </div>
                    <ul class="text-sm text-red-700 dark:text-red-300 list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Formulaire -->
            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                        Adresse e-mail
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               required 
                               autofocus
                               autocomplete="email"
                               placeholder="votre@email.com"
                               class="w-full pl-10 pr-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary-100 focus:border-primary transition-all duration-300 @error('email') border-red-400 focus:border-red-500 focus:ring-red-100 @enderror">
                    </div>
                </div>

                <!-- Bouton Submit -->
                <button type="submit" 
                        class="w-full bg-primary hover:bg-primary-600 text-white font-semibold py-3 px-4 rounded-xl shadow-lg  hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>Envoyer le lien de réinitialisation</span>
                </button>
            </form>

            <!-- Retour à la connexion -->
            <div class="mt-8 text-center">
                <a href="{{ route('login') }}" 
                   class="inline-flex items-center text-primary dark:text-primary-300 hover:text-primary-600 transition-colors duration-300 text-sm font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Retour à la connexion
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
