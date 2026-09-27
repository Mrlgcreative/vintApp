@extends('app')

@section('title', 'Statut de vérification')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">

        <a href="{{ route('authenticity.dashboard') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors mb-6">
            <i class="fas fa-arrow-left text-xs"></i>
            Mes vérifications
        </a>

        <x-card class="p-5 mb-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="flex items-start gap-4 min-w-0">
                    @if(count($item->images) > 0)
                        <img src="{{ Storage::url($item->images[0]) }}" alt="{{ $item->name }}" class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                    @else
                        <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-image text-gray-400"></i>
                        </div>
                    @endif

                    <div class="min-w-0">
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-1">{{ $item->name }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                            {{ $item->brand->name ?? 'Marque non spécifiée' }} · {{ $item->category->name ?? 'Sans catégorie' }}
                        </p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $item->formatted_price }}</p>
                        @if($item->isVerified())
                            <div class="mt-2">{!! $item->getAuthenticityBadgeHtml() !!}</div>
                        @endif
                    </div>
                </div>

                <div class="text-right flex-shrink-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $check->getStatusBadgeClass() }}">
                        {{ $check->getStatusLabel() }}
                    </span>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Demande #{{ $check->id }}</p>
                </div>
            </div>
        </x-card>

        @if($check->isApproved())
            <x-card class="p-5 mb-6 border-emerald-200 dark:border-emerald-500/30">
                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 rounded-full bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Produit authentifié</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Votre produit est vérifié et porte désormais le badge VintApp sur sa fiche publique.
                        </p>
                        @if($check->final_decision_at)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                Décision du {{ $check->final_decision_at->format('d/m/Y à H:i') }}
                            </p>
                        @endif
                    </div>
                </div>
            </x-card>

        @elseif($check->isRejected())
            <x-card class="p-5 mb-6 border-red-200 dark:border-red-500/30">
                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 rounded-full bg-red-50 dark:bg-red-500/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-times text-red-600 dark:text-red-400"></i>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Authenticité non confirmée</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Les éléments fournis n'ont pas permis de certifier ce produit. Vous pouvez corriger les informations et soumettre à nouveau.
                        </p>
                        @if($check->final_decision_at)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                Décision du {{ $check->final_decision_at->format('d/m/Y à H:i') }}
                            </p>
                        @endif
                    </div>
                </div>
            </x-card>

        @else
            <x-card class="p-5 mb-6 border-blue-200 dark:border-blue-500/30">
                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 rounded-full bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-spinner fa-spin text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Vérification en cours</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            @if($check->status === 'pending')
                                Votre demande est enregistrée et attend le démarrage du traitement.
                            @elseif($check->status === 'expert_review')
                                Un expert examine actuellement votre produit.
                            @else
                                L'analyse de vos photos est en cours.
                            @endif
                        </p>
                        @if($check->status === 'expert_review' && $check->expert)
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">
                                Expert assigné : <span class="font-medium text-gray-900 dark:text-white">{{ $check->expert->name }}</span>
                            </p>
                        @endif
                    </div>
                </div>
            </x-card>
        @endif

        @if($check->expert_notes)
            <x-card class="p-5 mb-6">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-2">Notes de l'expert</p>
                <p class="text-sm text-gray-700 dark:text-gray-200">{{ $check->expert_notes }}</p>
            </x-card>
        @endif

        <x-card class="p-5 mb-6">
            <div class="flex items-center justify-between gap-3 mb-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Progression</h2>
                @if(!$check->final_decision_at)
                    <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                        <i class="fas fa-rotate text-[10px]"></i>
                        Mise à jour automatique
                    </span>
                @endif
            </div>

            @php
                $submittedState = $check->submitted_at ? 'done' : 'todo';
                $aiState = $check->ai_completed_at ? 'done' : (($check->payment_completed || $check->status !== 'pending') ? 'current' : 'todo');
                $expertShown = $check->needsExpertReview() || $check->expert_assigned_at || $check->expert_completed_at;
                $expertState = $check->expert_completed_at ? 'done' : ($check->expert_assigned_at ? 'current' : 'todo');
                $decisionState = $check->final_decision_at ? ($check->isApproved() ? 'done' : 'failed') : 'todo';
            @endphp

            <ol class="relative">
                <span class="absolute left-[15px] top-4 bottom-4 w-px bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>

                <li class="relative flex gap-4 pb-6">
                    <span class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                 {{ $submittedState === 'done' ? 'bg-emerald-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-400' }}">
                        <i class="fas fa-check text-[10px]"></i>
                    </span>
                    <div class="pt-1 min-w-0">
                        <p class="font-medium text-gray-900 dark:text-white">Demande soumise</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $check->submitted_at ? $check->submitted_at->format('d/m/Y à H:i') : 'En attente' }}
                        </p>
                        @if($check->payment_completed)
                            <span class="inline-flex items-center gap-1 mt-1.5 text-xs font-medium text-emerald-700 dark:text-emerald-400">
                                <i class="fas fa-credit-card text-[10px]"></i>
                                Paiement confirmé — {{ number_format((float) $check->verification_fee, 2) }} $
                            </span>
                        @endif
                    </div>
                </li>

                <li class="relative flex gap-4 pb-6">
                    <span class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                 @if($aiState === 'done') bg-emerald-600 text-white
                                 @elseif($aiState === 'current') bg-blue-600 text-white
                                 @else bg-gray-200 dark:bg-gray-700 text-gray-400 @endif">
                        <i class="fas @if($aiState === 'done') fa-check @elseif($aiState === 'current') fa-spinner fa-spin @else fa-clock @endif text-[10px]"></i>
                    </span>
                    <div class="pt-1 min-w-0">
                        <p class="font-medium text-gray-900 dark:text-white">Analyse par IA</p>
                        @if($check->ai_completed_at)
                            <p class="text-sm text-gray-500 dark:text-gray-400">Terminée le {{ $check->ai_completed_at->format('d/m/Y à H:i') }}</p>
                            @if($check->ai_confidence_score !== null)
                                <div class="flex items-center gap-2 mt-2">
                                    <div class="w-24 h-1.5 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                        <div class="h-full bg-emerald-600" style="width: {{ min(100, max(0, (int) $check->ai_confidence_score)) }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ $check->ai_confidence_score }} % de confiance</span>
                                </div>
                            @endif
                        @elseif($aiState === 'current')
                            <p class="text-sm text-gray-500 dark:text-gray-400">En cours d'analyse</p>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">En attente du paiement</p>
                        @endif
                    </div>
                </li>

                @if($expertShown)
                    <li class="relative flex gap-4 pb-6">
                        <span class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                     @if($expertState === 'done') bg-emerald-600 text-white
                                     @elseif($expertState === 'current') bg-blue-600 text-white
                                     @else bg-gray-200 dark:bg-gray-700 text-gray-400 @endif">
                            <i class="fas @if($expertState === 'done') fa-check @elseif($expertState === 'current') fa-spinner fa-spin @else fa-user @endif text-[10px]"></i>
                        </span>
                        <div class="pt-1 min-w-0">
                            <p class="font-medium text-gray-900 dark:text-white">Examen par un expert</p>
                            @if($check->expert_completed_at)
                                <p class="text-sm text-gray-500 dark:text-gray-400">Terminé le {{ $check->expert_completed_at->format('d/m/Y à H:i') }}</p>
                            @elseif($check->expert_assigned_at)
                                <p class="text-sm text-gray-500 dark:text-gray-400">En cours d'examen</p>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">En attente d'assignation</p>
                            @endif
                            @if($check->expert)
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Expert : {{ $check->expert->name }}</p>
                            @endif
                        </div>
                    </li>
                @endif

                <li class="relative flex gap-4">
                    <span class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                 @if($decisionState === 'done') bg-emerald-600 text-white
                                 @elseif($decisionState === 'failed') bg-red-600 text-white
                                 @else bg-gray-200 dark:bg-gray-700 text-gray-400 @endif">
                        <i class="fas @if($decisionState === 'done') fa-shield-alt @elseif($decisionState === 'failed') fa-times @else fa-clock @endif text-[10px]"></i>
                    </span>
                    <div class="pt-1 min-w-0">
                        <p class="font-medium text-gray-900 dark:text-white">Décision finale</p>
                        @if($check->final_decision_at)
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $check->final_decision_at->format('d/m/Y à H:i') }}</p>
                            <p class="text-sm font-medium mt-1 {{ $check->isApproved() ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $check->isApproved() ? 'Produit authentifié' : 'Authenticité non confirmée' }}
                            </p>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">En attente</p>
                        @endif
                    </div>
                </li>
            </ol>
        </x-card>

        @if($check->verificationImages->count() > 0)
            <x-card class="p-5 mb-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Pièces analysées</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($check->verificationImages as $image)
                        @php $isPdf = str_ends_with(strtolower($image->image_path), '.pdf'); @endphp
                        <figure>
                            @if($isPdf)
                                <a href="{{ $image->getImageUrl() }}" target="_blank" rel="noopener"
                                   class="flex w-full h-32 rounded-lg border border-gray-200 dark:border-gray-700/50 bg-gray-50 dark:bg-gray-900/50 flex-col items-center justify-center gap-1.5 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                                    <i class="fas fa-file-pdf text-2xl"></i>
                                    <span class="text-xs">Ouvrir le PDF</span>
                                </a>
                            @else
                                <a href="{{ $image->getImageUrl() }}" target="_blank" rel="noopener">
                                    <img src="{{ $image->getImageUrl() }}" alt="{{ $image->getTypeLabel() }}" loading="lazy"
                                         class="w-full h-32 object-cover rounded-lg border border-gray-200 dark:border-gray-700/50">
                                </a>
                            @endif
                            <figcaption class="mt-1.5 flex items-center justify-between gap-2 text-xs">
                                <span class="text-gray-600 dark:text-gray-400 truncate">{{ $image->getTypeLabel() }}</span>
                                @if($image->image_quality_score !== null)
                                    <span class="text-gray-500 dark:text-gray-400 flex-shrink-0">{{ $image->image_quality_score }} %</span>
                                @endif
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </x-card>
        @endif

        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('items.show', $item) }}" class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                <i class="fas fa-arrow-left text-xs"></i>
                Retour au produit
            </a>

            <div class="flex items-center gap-3">
                @if(!$check->payment_completed)
                    <x-button-primary :href="route('authenticity.payment', $check)">
                        <i class="fas fa-credit-card mr-2"></i>Finaliser le paiement
                    </x-button-primary>
                @endif
                <x-button-outline :href="route('authenticity.dashboard')">Mes vérifications</x-button-outline>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@if(!$check->final_decision_at)
<script>
(function () {
    const KEY = 'authenticity-scroll-{{ $check->id }}';

    window.addEventListener('beforeunload', function () {
        sessionStorage.setItem(KEY, String(window.scrollY));
    });

    const saved = sessionStorage.getItem(KEY);
    if (saved) {
        sessionStorage.removeItem(KEY);
        window.scrollTo(0, Number(saved));
    }

    setInterval(function () {
        if (document.visibilityState === 'visible') {
            window.location.reload();
        }
    }, 60000);
})();
</script>
@endif
@endpush
@endsection
