<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pré-inscriptions fermées - VintApp</title>
    <meta name="description" content="Les pré-inscriptions VintApp sont actuellement fermées.">

    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-stone-100 dark:bg-stone-950">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-lg">
            <div class="bg-white dark:bg-stone-900 rounded-3xl shadow-lg shadow-stone-200/50 dark:shadow-stone-950 border border-stone-200 dark:border-stone-800 overflow-hidden">
                <div class="relative px-8 lg:px-12 pt-12 pb-8 text-center overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-200 via-orange-300 to-amber-200"></div>

                    <div class="w-20 h-20 bg-gradient-to-br from-amber-100 to-orange-100 dark:from-amber-900/40 dark:to-orange-900/40 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-9 h-9 text-stone-500 dark:text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>

                    <h1 class="text-3xl font-bold text-stone-800 dark:text-stone-100 mb-3">
                        Pré-inscriptions fermées
                    </h1>

                    <p class="text-base text-stone-500 dark:text-stone-400 leading-relaxed max-w-md mx-auto">
                        {{ $message }}
                    </p>
                </div>

                <div class="px-8 lg:px-12 pb-10">
                    <div class="bg-stone-50 dark:bg-stone-800/50 rounded-xl p-5 border border-stone-200 dark:border-stone-700">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-sm text-stone-600 dark:text-stone-300 font-medium mb-1">Information</p>
                                <p class="text-sm text-stone-500 dark:text-stone-400">Les pré-inscriptions sont temporairement suspendues. Revenez bientôt pour ne pas manquer l'ouverture !</p>
                            </div>
                        </div>
                    </div>

                    <a href="{{ url('/') }}"
                       class="mt-6 w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold py-3 px-6 rounded-xl transition-all shadow-sm shadow-amber-200/50 dark:shadow-amber-900/30 text-base">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Retour à l'accueil
                    </a>

                    <div class="mt-6 text-center">
                        <p class="text-sm text-stone-400">
                            <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Contactez-nous à <a href="mailto:{{ $contactEmail ?? 'contact@vintapp.com' }}" class="text-amber-600 dark:text-amber-400 hover:underline">{{ $contactEmail ?? 'contact@vintapp.com' }}</a>
                        </p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-8">
                <p class="text-stone-400 text-sm">
                    &copy; {{ date('Y') }} VintApp. Tous droits réservés.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
