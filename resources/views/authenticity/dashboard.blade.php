@extends('app')

@section('title', 'Mes certifications')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">

        <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Mes certifications</h1>
                <p class="text-gray-600 dark:text-gray-300">Suivez vos demandes et vos badges d'authenticité.</p>
            </div>
            <x-button-primary :href="route('items.index')">
                <i class="fas fa-plus mr-2"></i>Voir mes produits
            </x-button-primary>
        </div>

        <div class="grid sm:grid-cols-3 gap-4 mb-8">
            <x-stat-card :value="$stats['total_requests']" label="Demandes envoyées" icon="fas fa-clipboard-list" tone="slate" />
            <x-stat-card :value="$stats['verified_items']" label="Produits vérifiés" icon="fas fa-badge-check" tone="emerald" />
            <x-stat-card :value="$stats['pending_verifications']" label="En cours de traitement" icon="fas fa-clock" tone="sky" />
        </div>

        <x-card class="mb-6">
            <div class="flex flex-wrap items-center gap-2 p-4">
                @php
                    $tabs = [
                        '' => 'Toutes',
                        'in_progress' => 'En cours',
                        'approved' => 'Approuvées',
                        'rejected' => 'Rejetées',
                    ];
                @endphp
                @foreach($tabs as $key => $label)
                    <a href="{{ route('authenticity.dashboard', $key ? ['status' => $key] : []) }}"
                       class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                              {{ $status === $key
                                  ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900'
                                  : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </x-card>

        <x-card>
            @if($checks->count() > 0)
                <ul class="divide-y divide-gray-200 dark:divide-gray-700/50">
                    @foreach($checks as $check)
                        @php
                            $progress = 20;
                            if ($check->payment_completed) $progress = 40;
                            if ($check->ai_completed_at) $progress = 60;
                            if ($check->expert_assigned_at) $progress = 80;
                            if ($check->final_decision_at) $progress = 100;

                            $barClass = $check->isApproved() ? 'bg-emerald-600' : ($check->isRejected() ? 'bg-red-600' : 'bg-blue-600');
                        @endphp

                        <li class="p-5">
                            @if(!$check->item)
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-box-open text-gray-400"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-900 dark:text-white">Produit supprimé</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Demande #{{ $check->id }} · {{ $check->created_at->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $check->getStatusBadgeClass() }}">
                                        {{ $check->getStatusLabel() }}
                                    </span>
                                </div>
                            @else
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div class="flex items-start gap-4 min-w-0">
                                    @if(count($check->item->images) > 0)
                                        <img src="{{ Storage::url($check->item->images[0]) }}" alt="{{ $check->item->name }}" loading="lazy"
                                             class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                                    @else
                                        <div class="w-16 h-16 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @endif

                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 dark:text-white truncate">{{ $check->item->name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate mb-1">
                                            {{ $check->item->brand->name ?? 'Marque non spécifiée' }} · {{ $check->item->category->name ?? 'Sans catégorie' }}
                                        </p>
                                        <p class="font-semibold text-gray-900 dark:text-white mb-2">{{ $check->item->formatted_price }}</p>

                                        @if($check->item->isVerified())
                                            <div class="mb-2">{!! $check->item->getAuthenticityBadgeHtml() !!}</div>
                                        @endif

                                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                                            <span>Demande #{{ $check->id }}</span>
                                            <span>{{ $check->created_at->format('d/m/Y') }}</span>
                                            @if($check->expert)
                                                <span>Expert : {{ $check->expert->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $check->getStatusBadgeClass() }}">
                                        {{ $check->getStatusLabel() }}
                                    </span>

                                    <div class="flex items-center gap-2">
                                        @if(!$check->payment_completed)
                                            <x-button-primary size="sm" variant="success" :href="route('authenticity.payment', $check)">
                                                Payer
                                            </x-button-primary>
                                        @endif
                                        <x-button-outline size="sm" :href="route('authenticity.status', $check->item)">Détails</x-button-outline>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 mt-4">
                                <div class="flex-1 h-1.5 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                    <div class="h-full rounded-full transition-all {{ $barClass }}" style="width: {{ $progress }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 w-9 text-right">{{ $progress }} %</span>
                            </div>

                            @if($check->expert_notes)
                                <div class="mt-4 rounded-lg bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700/50 p-3">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Notes de l'expert</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-200">{{ Str::limit($check->expert_notes, 150) }}</p>
                                </div>
                            @endif
                            </div>
                            @endif
                        </li>
                    @endforeach
                </ul>

                @if($checks->hasPages())
                    <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700/50">
                        {{ $checks->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-14 px-6">
                    <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-gray-400"></i>
                    </div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Aucune certification demandée</h2>
                    <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-sm mx-auto">
                        Ouvrez l'un de vos produits pour lancer une demande de certification.
                    </p>
                    <x-button-primary :href="route('items.index')">Voir mes produits</x-button-primary>
                </div>
            @endif
        </x-card>

        <x-card class="p-5 mt-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Comment ça marche ?</h2>
            <ol class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach([
                    ['fa-camera', 'Soumission', 'Photos nettes, certificat et reçu éventuel.'],
                    ['fa-robot', 'Analyse IA', 'Les images et le numéro de série sont analysés.'],
                    ['fa-user-check', 'Expertise', 'Un expert tranche si nécessaire.'],
                    ['fa-badge-check', 'Badge', 'Le produit est marqué VintApp vérifié.'],
                ] as $index => [$icon, $title, $description])
                    <li class="flex items-start gap-3">
                        <span class="w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-semibold flex items-center justify-center flex-shrink-0">{{ $index + 1 }}</span>
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="fas {{ $icon }} text-gray-400 text-xs"></i>{{ $title }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </x-card>
    </div>
</div>
@endsection
