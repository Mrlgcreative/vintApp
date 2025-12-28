@extends('app')

@section('title', 'VintPass non trouvé')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 flex items-center justify-center py-8 px-4">
    <div class="max-w-md mx-auto text-center">
        
        <!-- Icon -->
        <div class="w-24 h-24 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        
        <!-- Message -->
        <h1 class="text-3xl font-bold text-white mb-4">VintPass Non Trouvé</h1>
        <p class="text-gray-400 mb-2">
            Le code <span class="font-mono bg-white/10 px-2 py-1 rounded">{{ $shortCode }}</span> n'existe pas.
        </p>
        <p class="text-gray-500 text-sm mb-8">
            Vérifiez que vous avez scanné le bon QR code ou que le lien est correct.
        </p>

        <!-- Possible Issues -->
        <div class="bg-white/5 rounded-2xl p-6 text-left mb-8">
            <h3 class="text-white font-semibold mb-4">Causes possibles :</h3>
            <ul class="space-y-3 text-gray-400 text-sm">
                <li class="flex items-start gap-3">
                    <span class="text-red-400">•</span>
                    <span>Le VintPass a été révoqué ou suspendu</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-red-400">•</span>
                    <span>Le QR code est endommagé ou illisible</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-red-400">•</span>
                    <span>L'URL a été modifiée ou est incorrecte</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-red-400">•</span>
                    <span>Il s'agit d'une tentative de fraude</span>
                </li>
            </ul>
        </div>

        <!-- Actions -->
        <div class="flex flex-col gap-3">
            <a href="{{ route('home') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-full transition-all font-medium">
                Retour à l'accueil
            </a>
            <a href="{{ route('support.index') }}" 
               class="bg-white/10 hover:bg-white/20 text-white px-6 py-3 rounded-full transition-all">
                Contacter le support
            </a>
        </div>

        <!-- Warning -->
        <div class="mt-8 bg-yellow-500/10 border border-yellow-500/30 rounded-xl p-4">
            <p class="text-yellow-400 text-sm">
                ⚠️ Si vous pensez avoir acheté un article avec un faux VintPass, 
                contactez immédiatement notre support.
            </p>
        </div>
    </div>
</div>
@endsection
