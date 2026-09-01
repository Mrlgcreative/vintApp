@extends('app')

@section('title', 'Paiement échoué — ' . config('app.name', 'VintApp'))

@section('content')
@php
    $errorClean = is_string($error) ? $error : 'Une erreur est survenue lors du paiement.';
    $providerName = match (strtolower((string) $provider)) {
        'orange_money' => 'Orange Money',
        'airtel_money' => 'Airtel Money',
        'mpesa' => 'M-Pesa',
        'illicocash' => 'IllicoCash',
        'africell_money' => 'Africell Money',
        'maishapay' => 'MaishaPay',
        'pawapay' => 'PawaPay',
        default => $provider,
    };
@endphp

<div class="min-h-[70vh] flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-md">
        <div class="mb-4 text-center">
            <p class="inline-flex items-center gap-2 rounded-full border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 px-3 py-1 text-xs font-medium text-gray-500 dark:text-gray-400 shadow-sm">
                Échec du paiement
                @if(!empty($error) && is_string($error) && preg_match('/([A-Z0-9\-]{6,})/', $error, $m))
                    <span class="text-gray-300 dark:text-gray-600">·</span>
                    <span class="font-mono">{{ $m[1] }}</span>
                @endif
            </p>
        </div>

        <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm p-6 sm:p-8 text-center">

            {{-- Icône --}}
            <div class="mb-5">
                <div class="w-16 h-16 mx-auto rounded-full bg-red-100 dark:bg-red-500/10 flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
            </div>

            {{-- Badge + titre --}}
            <div class="mb-3">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-red-200 dark:border-red-500/30 bg-red-50 dark:bg-red-500/10 px-3 py-1 text-xs font-medium text-red-700 dark:text-red-400">
                    <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Échec
                </span>
            </div>
            <h3 class="text-lg font-semibold tracking-tight text-gray-900 dark:text-white mb-2">Paiement échoué</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Votre transaction n'a pas pu être traitée</p>

            {{-- Message d'erreur --}}
            @if(!empty($errorClean))
                <div class="rounded-lg border border-red-200 dark:border-red-500/20 bg-red-50 dark:bg-red-500/5 p-4 mb-5 text-sm text-left text-red-700 dark:text-red-300">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <span>{{ $errorClean }}</span>
                    </div>
                </div>
            @endif

            {{-- Montant --}}
            <div class="mb-6">
                <p class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                    @if($amount > 0)
                        {{ number_format($amount, 2) }} <span class="text-base font-semibold text-gray-500 dark:text-gray-400">{{ strtoupper($currency) }}</span>
                    @else
                        —
                    @endif
                </p>
            </div>

            {{-- Détails --}}
            <div class="rounded-lg border border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-950/50 divide-y divide-gray-100 dark:divide-gray-800 mb-5 text-sm text-left">
                @if($amount > 0)
                    <div class="flex justify-between gap-4 px-4 py-2.5">
                        <span class="text-gray-500 dark:text-gray-400">Montant tenté</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($amount, 2) }} {{ strtoupper($currency) }}</span>
                    </div>
                @endif
                @if($providerName)
                    <div class="flex justify-between gap-4 px-4 py-2.5">
                        <span class="text-gray-500 dark:text-gray-400">Opérateur</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $providerName }}</span>
                    </div>
                @endif
                <div class="flex justify-between gap-4 px-4 py-2.5">
                    <span class="text-gray-500 dark:text-gray-400">Date</span>
                    <span class="text-gray-900 dark:text-gray-100">{{ now()->format('d/m/Y à H:i') }}</span>
                </div>
            </div>

            {{-- Causes possibles --}}
            <div class="rounded-lg border border-yellow-200 dark:border-yellow-500/20 bg-yellow-50 dark:bg-yellow-500/5 p-4 mb-5 text-sm text-left text-yellow-800 dark:text-yellow-200">
                <h6 class="font-medium mb-2 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    Causes possibles
                </h6>
                <ul class="space-y-1 text-yellow-700 dark:text-yellow-300 list-disc list-inside text-xs">
                    <li>Solde insuffisant sur votre compte Mobile Money</li>
                    <li>Numéro de téléphone invalide ou inactif</li>
                    <li>Délai d'attente de l'opérateur dépassé</li>
                    <li>Transaction refusée par l'opérateur</li>
                    <li>Problème de connexion réseau</li>
                </ul>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col gap-2.5 mt-5">
                <a href="{{ route('cart.pay') }}" class="inline-flex items-center justify-center h-10 w-full rounded-md text-sm font-medium text-white bg-vinted-primary-600 hover:bg-vinted-primary-700 transition-colors">
                    Réessayer le paiement
                </a>
                <a href="{{ route('support.index') }}" class="inline-flex items-center justify-center h-10 w-full rounded-md text-sm font-medium border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    Contacter le support
                </a>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center h-10 w-full rounded-md text-sm font-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                    Retour au tableau de bord
                </a>
            </div>

            <p class="mt-5 text-xs text-gray-500 dark:text-gray-500">
                Besoin d'aide ? Notre équipe support est disponible 24/7
            </p>
        </div>
    </div>
</div>
@endsection