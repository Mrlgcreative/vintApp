@extends('layouts.admin')

@section('title', 'Test Vérification #' . $check->id)
@section('page-title', 'Test Vérification #' . $check->id)
@section('page-subtitle', 'Page de débogage pour la vérification')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-5">
            <a href="{{ route('expert.verifications.index') }}"
               class="inline-flex items-center text-sm text-slate-500 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour
            </a>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-info-circle text-primary-500"></i>
                    Détails de la vérification
                </h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl">
                        <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Statut</span>
                        <div class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $check->getStatusBadgeClass() }}">
                                {{ $check->getStatusLabel() }}
                            </span>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl">
                        <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">ID</span>
                        <div class="mt-1 text-base font-semibold text-slate-900 dark:text-white">#{{ $check->id }}</div>
                    </div>
                </div>

                <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nom du produit</span>
                    <div class="mt-1 text-base font-semibold text-slate-900 dark:text-white">{{ $check->item->name ?? 'Produit sans nom' }}</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl">
                        <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Prix</span>
                        <div class="mt-1 text-base font-semibold text-slate-900 dark:text-white">
                            {{ number_format($check->item->price, 0, ',', ' ') }} {{ $check->item->currency }}
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl">
                        <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Catégorie</span>
                        <div class="mt-1 text-base font-semibold text-slate-900 dark:text-white">{{ $check->item->category->name ?? 'Non spécifiée' }}</div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl">
                        <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Marque</span>
                        <div class="mt-1 text-base font-semibold text-slate-900 dark:text-white">{{ $check->item->brand->name ?? 'Non spécifiée' }}</div>
                    </div>
                </div>

                <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">État</span>
                    <div class="mt-1 text-base font-semibold text-slate-900 dark:text-white">{{ ucfirst($check->item->condition ?? 'Non spécifié') }}</div>
                </div>

                @if($check->item->description)
                    <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl">
                        <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Description</span>
                        <div class="mt-2 text-sm text-slate-700 dark:text-slate-200 whitespace-pre-wrap">{{ $check->item->description }}</div>
                    </div>
                @endif

                <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Images</span>
                    <div class="mt-2">
                        @forelse($check->item->images ?? [] as $image)
                            <span class="inline-block bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 px-3 py-1 rounded-lg text-sm font-mono mb-1 mr-1">
                                {{ $image }}
                            </span>
                        @empty
                            <span class="text-slate-400 italic">Aucune image</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
