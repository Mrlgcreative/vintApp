@extends('layouts.admin')

@section('title', 'Test Vérification #' . $check->id)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
    <div class="max-w-3xl mx-auto">
        <!-- En-tête -->
        <div class="bg-gradient-to-r from-indigo-500 to-primary-600 rounded-xl p-6 text-white mb-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">
                        <i class="fas fa-flask mr-2"></i>
                        Test Vérification #{{ $check->id }}
                    </h1>
                    <p class="text-indigo-100 text-sm mt-1">Page de débogage pour la vérification</p>
                </div>
                <a href="{{ route('expert.verifications.index') }}"
                   class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg transition-colors text-sm">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Retour
                </a>
            </div>
        </div>

        <!-- Informations de la vérification -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-info-circle text-indigo-600"></i>
                    Détails de la vérification
                </h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Statut</span>
                        <div class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $check->getStatusBadgeClass() }}">
                                {{ $check->getStatusLabel() }}
                            </span>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</span>
                        <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">#{{ $check->id }}</div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nom du produit</span>
                    <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $check->item->name ?? 'Produit sans nom' }}</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Prix</span>
                        <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                            {{ number_format($check->item->price, 0, ',', ' ') }} {{ $check->item->currency }}
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Catégorie</span>
                        <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $check->item->category->name ?? 'Non spécifiée' }}</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Marque</span>
                        <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $check->item->brand->name ?? 'Non spécifiée' }}</div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">État</span>
                    <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ ucfirst($check->item->condition ?? 'Non spécifié') }}</div>
                </div>

                @if($check->item->description)
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</span>
                        <div class="mt-2 text-gray-700 dark:text-gray-200 whitespace-pre-wrap">{{ $check->item->description }}</div>
                    </div>
                @endif

                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Images</span>
                    <div class="mt-2">
                        @forelse($check->item->images ?? [] as $image)
                            <span class="inline-block bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-1 rounded text-sm font-mono mb-1 mr-1">
                                {{ $image }}
                            </span>
                        @empty
                            <span class="text-gray-400 italic">Aucune image</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
