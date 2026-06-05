@extends('app')

@section('title', 'Application indisponible — VintApp')

@section('content')
<div class="min-h-[calc(100vh-8rem)] flex flex-col items-center justify-center px-4 py-16 sm:px-6 bg-zinc-50">
    <div class="w-full max-w-lg text-center">
        <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500 mb-3">
            VintApp
        </p>
        <h1 class="text-2xl sm:text-3xl font-semibold text-zinc-900 tracking-tight">
            Application indisponible
        </h1>
        <p class="mt-4 text-base text-zinc-600 leading-relaxed">
            @if($reason === 'city')
                Malheureusement, VintApp n’est pas disponible pour la localisation indiquée pour le moment.
            @elseif($reason === 'coverage')
                Nous ne pouvons pas vérifier automatiquement votre position par rapport à nos zones desservies. Vérifiez plus tard ou contactez le support si le problème persiste.
            @else
                Malheureusement, VintApp n’est disponible que dans certaines zones pour le moment. Si vous pensez recevoir ce message par erreur, contactez le support.
            @endif
        </p>

        @if($reason === 'city' && $cityName !== '')
            <p class="mt-6 text-sm text-zinc-500">
                Localisation détectée ou saisie : <span class="font-medium text-zinc-700">« {{ $cityName }} »</span>
            </p>
        @endif

        <div class="mt-10 flex flex-col sm:flex-row gap-3 justify-center items-stretch sm:items-center">
            <a href="#zones-desservies"
               class="inline-flex justify-center items-center rounded-lg border border-zinc-300 bg-white px-5 py-2.5 text-sm font-medium text-zinc-800 shadow-sm hover:bg-zinc-50 transition-colors">
                Voir les zones desservies
            </a>
            <a href="mailto:{{ config('mail.from.address', 'support@example.com') }}"
               class="inline-flex justify-center items-center rounded-lg px-5 py-2.5 text-sm font-medium text-zinc-600 hover:text-zinc-900 transition-colors">
                Contacter le support
            </a>
        </div>
    </div>

    <div id="zones-desservies" class="w-full max-w-md mt-16 scroll-mt-24">
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm text-left">
            <h2 class="text-sm font-semibold text-zinc-900 uppercase tracking-wide">
                Zones desservies
            </h2>
            @if($allowedCities->isNotEmpty())
                <ul class="mt-4 space-y-2 text-sm text-zinc-700">
                    @foreach($allowedCities as $city)
                        <li class="flex items-start gap-2">
                            <span class="mt-1.5 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-emerald-500" aria-hidden="true"></span>
                            <span>{{ $city }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="mt-4 text-sm text-zinc-500 italic">
                    Aucune zone n’est configurée pour le moment. Veuillez contacter l’administrateur.
                </p>
            @endif

            <div class="mt-8 pt-6 border-t border-zinc-100">
                <a href="{{ route('location.validate') }}"
                   class="text-sm font-medium text-violet-700 hover:text-violet-800">
                    ← Retour à la validation de localisation
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
