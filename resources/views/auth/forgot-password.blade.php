@extends('app')

@section('title', 'Mot de passe oublié - VintApp')

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
            <div class="flex flex-col gap-2 text-center mb-6">
                <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                    Mot de passe oublié
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Entrez votre email pour recevoir un lien de réinitialisation
                </p>
            </div>

            <!-- Message de succès -->
            @if (session('status'))
                <div class="mb-5 bg-vinted-success-50 dark:bg-vinted-success-500/10 border border-vinted-success-200 dark:border-vinted-success-500/30 rounded-md px-4 py-3 text-sm text-vinted-success-700 dark:text-vinted-success-300">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Messages d'erreur -->
            @if ($errors->any())
                <div class="mb-5 bg-vinted-danger-50 dark:bg-vinted-danger-500/10 border border-vinted-danger-200 dark:border-vinted-danger-500/30 rounded-md px-4 py-3 text-sm text-vinted-danger-600 dark:text-vinted-danger-300">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Formulaire -->
            <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-4">
                @csrf

                <!-- Email -->
                <div class="flex flex-col gap-2">
                    <label for="email" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Adresse e-mail
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
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
                               class="w-full pl-10 pr-3 py-2.5 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-vinted-primary-500 focus:ring-2 focus:ring-vinted-primary-500/30 outline-none transition-colors @error('email') border-red-400 focus:border-red-500 focus:ring-red-100 @enderror">
                    </div>
                </div>

                <!-- Bouton Submit -->
                <button type="submit"
                        class="w-full h-10 inline-flex items-center justify-center gap-2 rounded-md bg-vinted-primary-600 text-white text-sm font-medium hover:bg-vinted-primary-700 active:scale-[0.98] transition-all shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-vinted-primary-300 focus-visible:ring-offset-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Envoyer le lien de réinitialisation
                </button>
            </form>
        </div>

        <!-- Retour à la connexion -->
        <div class="text-center">
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour à la connexion
            </a>
        </div>
    </div>
</div>
@endsection