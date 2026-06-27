<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques - VintApp</title>
    <meta name="description" content="Statistiques de pré-inscription VintApp">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/dynamic-colors.css') }}?v={{ filemtime(public_path('css/dynamic-colors.css')) }}">
</head>
<body class="min-h-screen bg-stone-100 dark:bg-stone-950">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-2xl">
            <div class="bg-white dark:bg-stone-900 rounded-3xl shadow-lg shadow-stone-200/50 dark:shadow-stone-950 border border-stone-200 dark:border-stone-800 overflow-hidden">
                <div class="relative px-8 lg:px-12 pt-12 pb-8 text-center overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-200 via-orange-300 to-amber-200"></div>
                    <div class="w-16 h-16 bg-gradient-to-br from-amber-100 to-orange-100 dark:from-amber-900/40 dark:to-orange-900/40 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h1 class="text-3xl lg:text-4xl font-bold text-stone-800 dark:text-stone-100 mb-2">
                        Statistiques
                    </h1>
                    <p class="text-stone-500 dark:text-stone-400">
                        Pré-inscriptions VintApp
                    </p>
                </div>

                <div class="px-8 lg:px-12 pb-12">
                    @if(session('success'))
                        <div class="mb-8 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-4 flex items-start gap-3">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-emerald-800 dark:text-emerald-200 font-medium">{{ session('success') }}</p>
                            </div>
                            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-stone-50 dark:bg-stone-800/50 rounded-xl p-5 border border-stone-200 dark:border-stone-700 text-center">
                            <p class="text-3xl font-bold text-stone-800 dark:text-stone-100">{{ $stats['total'] }}</p>
                            <p class="text-sm text-stone-500 dark:text-stone-400 mt-1">Total inscrits</p>
                        </div>
                        <div class="bg-stone-50 dark:bg-stone-800/50 rounded-xl p-5 border border-stone-200 dark:border-stone-700 text-center">
                            <p class="text-3xl font-bold text-stone-800 dark:text-stone-100">{{ $stats['confirmed'] }}</p>
                            <p class="text-sm text-stone-500 dark:text-stone-400 mt-1">Emails confirmés</p>
                        </div>
                        <div class="bg-stone-50 dark:bg-stone-800/50 rounded-xl p-5 border border-stone-200 dark:border-stone-700 text-center">
                            <p class="text-3xl font-bold text-stone-800 dark:text-stone-100">{{ $stats['approved'] }}</p>
                            <p class="text-sm text-stone-500 dark:text-stone-400 mt-1">Approuvés</p>
                        </div>
                        <div class="bg-stone-50 dark:bg-stone-800/50 rounded-xl p-5 border border-stone-200 dark:border-stone-700 text-center">
                            <p class="text-3xl font-bold text-stone-800 dark:text-stone-100">{{ $stats['converted'] }}</p>
                            <p class="text-sm text-stone-500 dark:text-stone-400 mt-1">Comptes créés</p>
                        </div>
                    </div>

                    @php $conversionRate = $stats['total'] > 0 ? round(($stats['converted'] / $stats['total']) * 100) : 0; @endphp
                    <div class="bg-stone-50 dark:bg-stone-800/50 rounded-xl p-5 border border-stone-200 dark:border-stone-700 mb-6">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm font-medium text-stone-700 dark:text-stone-300">Taux de conversion</p>
                            <p class="text-sm font-bold text-stone-800 dark:text-stone-100">{{ $conversionRate }}%</p>
                        </div>
                        <div class="h-2.5 bg-stone-200 dark:bg-stone-700 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-amber-400 to-orange-400 rounded-full transition-all" style="width: {{ $conversionRate }}%"></div>
                        </div>
                    </div>

                    <a href="{{ route('preregistration.index') }}" 
                       class="w-full bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold py-3.5 px-6 rounded-xl transition-all shadow-sm shadow-amber-200/50 dark:shadow-amber-900/30 text-base flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Rejoignez-nous !
                    </a>
                </div>
            </div>

            <div class="text-center mt-8">
                <a href="{{ route('preregistration.index') }}" class="inline-flex items-center gap-1.5 text-sm text-stone-500 dark:text-stone-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Retour à l'inscription
                </a>
            </div>
        </div>
    </div>
</body>
</html>
