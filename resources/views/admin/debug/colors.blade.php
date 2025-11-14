@extends('layouts.admin')

@section('title', 'Debug des Couleurs')

@section('content')
<div class="max-w-7xl mx-auto py-4 px-3 sm:py-6 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Debug du Système de Couleurs</h1>
        
        <!-- Variables disponibles -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-4">Variables disponibles dans cette vue :</h2>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p><strong>activePaletteName:</strong> {{ $activePaletteName ?? 'NON DÉFINI' }}</p>
                <p><strong>activeColors disponibles:</strong> {{ isset($activeColors) ? 'OUI' : 'NON' }}</p>
                <p><strong>activePaletteCSS disponible:</strong> {{ isset($activePaletteCSS) ? 'OUI (' . strlen($activePaletteCSS) . ' caractères)' : 'NON' }}</p>
                <p><strong>colorService disponible:</strong> {{ isset($colorService) ? 'OUI' : 'NON' }}</p>
            </div>
        </div>

        @if(isset($activeColors))
        <!-- Couleurs actuelles -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-4">Couleurs de la palette active :</h2>
            <div class="grid grid-cols-3 md:grid-cols-5 gap-4">
                @foreach($activeColors as $colorName => $colorValue)
                    @if($colorName !== 'name')
                        <div class="text-center">
                            <div class="w-16 h-16 rounded-lg border border-gray-200 mx-auto mb-2"
                                 style="background-color: {{ $colorValue }}"></div>
                            <div class="text-sm font-medium">{{ $colorName }}</div>
                            <div class="text-xs text-gray-500 font-mono">{{ $colorValue }}</div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        @if(isset($activePaletteCSS))
        <!-- CSS généré -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-4">CSS généré (premiers 500 caractères) :</h2>
            <pre class="bg-gray-900 text-green-400 p-4 rounded-lg text-sm overflow-x-auto">{{ substr($activePaletteCSS, 0, 500) }}...</pre>
        </div>
        @endif

        <!-- Test des variables CSS -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-4">Test des variables CSS :</h2>
            <div class="space-y-4">
                <div class="p-4 rounded-lg" style="background-color: var(--color-primary, #ff0000); color: white;">
                    Bloc avec var(--color-primary) - Si rouge, la variable n'est pas chargée
                </div>
                <div class="p-4 rounded-lg" style="background-color: var(--color-secondary, #ff0000); color: white;">
                    Bloc avec var(--color-secondary) - Si rouge, la variable n'est pas chargée
                </div>
                <div class="p-4 rounded-lg" style="background-color: var(--color-success, #ff0000); color: white;">
                    Bloc avec var(--color-success) - Si rouge, la variable n'est pas chargée
                </div>
            </div>
        </div>

        <!-- Test direct avec couleurs -->
        @if(isset($activeColors))
        <div>
            <h2 class="text-lg font-semibold mb-4">Test avec couleurs directes :</h2>
            <div class="space-y-4">
                <div class="p-4 rounded-lg text-white" style="background-color: {{ $activeColors['primary'] ?? '#ff0000' }}">
                    Bloc avec couleur primary directe : {{ $activeColors['primary'] ?? 'NON DÉFINI' }}
                </div>
                <div class="p-4 rounded-lg text-white" style="background-color: {{ $activeColors['secondary'] ?? '#ff0000' }}">
                    Bloc avec couleur secondary directe : {{ $activeColors['secondary'] ?? 'NON DÉFINI' }}
                </div>
                <div class="p-4 rounded-lg text-white" style="background-color: {{ $activeColors['success'] ?? '#ff0000' }}">
                    Bloc avec couleur success directe : {{ $activeColors['success'] ?? 'NON DÉFINI' }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Injecter le CSS dans le head -->
@if(isset($activePaletteCSS))
<style>
{!! $activePaletteCSS !!}
</style>
@endif
@endsection