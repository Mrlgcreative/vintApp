@extends('layouts.admin')

@section('title', 'Détails de l\'item #' . $item->id)

@section('page-title', 'Détails de l\'item')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header avec actions -->
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="min-w-0">
            <a href="{{ route('admin.items.index') }}" class="mb-2 inline-flex items-center text-sm text-slate-600 hover:text-primary-600 dark:text-slate-400 dark:hover:text-primary-400">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour à la liste
            </a>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $item->name }}</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Créé le {{ $item->created_at->format('d/m/Y à H:i') }}
                @if($item->verified_at)
                    • Vérifié le {{ $item->verified_at->format('d/m/Y à H:i') }}
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Badge de statut vérification -->
            @php
                $statusConfig = [
                    'approved' => ['class' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300 dark:ring-emerald-500/30', 'icon' => 'fa-circle-check', 'label' => 'Approuvé'],
                    'pending' => ['class' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300 dark:ring-amber-500/30', 'icon' => 'fa-clock', 'label' => 'En attente'],
                    'rejected' => ['class' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-300 dark:ring-red-500/30', 'icon' => 'fa-circle-xmark', 'label' => 'Rejeté'],
                ];
                $status = $statusConfig[$item->verification_status] ?? $statusConfig['pending'];
            @endphp
            <span class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold ring-1 ring-inset {{ $status['class'] }}">
                <i class="fas {{ $status['icon'] }}"></i>
                <span>{{ $status['label'] }}</span>
            </span>

            <!-- Badge de modération -->
            @if($item->is_blocked)
                <span class="inline-flex items-center gap-2 rounded-full bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-900/30 dark:text-red-300 dark:ring-red-500/30">
                    <i class="fas fa-ban"></i>
                    <span>Bloqué</span>
                </span>
            @elseif($item->isCurrentlySuspended())
                <span class="inline-flex items-center gap-2 rounded-full bg-orange-50 px-4 py-2 text-sm font-semibold text-orange-700 ring-1 ring-inset ring-orange-600/20 dark:bg-orange-900/30 dark:text-orange-300 dark:ring-orange-500/30">
                    <i class="fas fa-pause-circle"></i>
                    <span>Suspendu</span>
                    @if($item->suspended_until)
                        <span class="text-xs">jusqu'au {{ $item->suspended_until->format('d/m/Y') }}</span>
                    @endif
                </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Images -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="p-5 sm:p-6">
                    <h3 class="mb-4 flex items-center font-semibold text-slate-900 dark:text-white">
                        <i class="fas fa-images mr-2 text-primary-600"></i>
                        Images ({{ count($item->images ?? []) }})
                    </h3>
                    
                    @if(!empty($item->images))
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                            @foreach($item->images as $index => $image)
                                <div class="group relative cursor-pointer" onclick="openImageModal('{{ asset('storage/' . $image) }}', '{{ $item->name }} - Image {{ $index + 1 }}')">
                                    <img src="{{ asset('storage/' . $image) }}" 
                                         class="h-48 w-full rounded-xl border-2 border-slate-200 object-cover transition group-hover:border-primary-500 dark:border-slate-600"
                                         alt="Image {{ $index + 1 }}">
                                    <div class="absolute inset-0 flex items-center justify-center rounded-xl bg-black bg-opacity-0 transition group-hover:bg-opacity-20">
                                        <i class="fas fa-search-plus text-2xl text-white opacity-0 transition group-hover:opacity-100"></i>
                                    </div>
                                    <div class="absolute top-2 left-2 rounded bg-black bg-opacity-75 px-2 py-1 text-xs text-white">
                                        {{ $index + 1 }}/{{ count($item->images) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="py-8 text-center text-slate-500 dark:text-slate-400">Aucune image disponible</p>
                    @endif
                </div>
            </div>

            <!-- Description -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="p-5 sm:p-6">
                    <h3 class="mb-4 flex items-center font-semibold text-slate-900 dark:text-white">
                        <i class="fas fa-align-left mr-2 text-primary-600"></i>
                        Description
                    </h3>
                    <div class="prose dark:prose-invert max-w-none">
                        <p class="whitespace-pre-wrap text-slate-700 dark:text-slate-300">{{ $item->description }}</p>
                    </div>
                </div>
            </div>

            <!-- Analyse IA -->
            @if($item->verification_details)
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="p-5 sm:p-6">
                    <h3 class="mb-4 flex items-center font-semibold text-slate-900 dark:text-white">
                        <i class="fas fa-robot mr-2 text-primary-600"></i>
                        Analyse IA
                        @if($item->verification_score)
                            <span class="ml-auto text-2xl font-bold {{ $item->verification_score >= 75 ? 'text-emerald-600' : ($item->verification_score >= 50 ? 'text-amber-600' : 'text-red-600') }}">
                                {{ number_format($item->verification_score, 0) }}/100
                            </span>
                        @endif
                    </h3>

                    @php
                        $details = $item->verification_details;
                        $imageScore = $details['images']['score'] ?? 0;
                        $textScore = $details['text']['score'] ?? 0;
                        $coherenceScore = $details['coherence']['score'] ?? 0;
                    @endphp

                    <div class="space-y-4">
                        <!-- Images Analysis -->
                        <div class="rounded-r-xl border-l-4 border-sky-500 bg-sky-50 py-3 pl-4 dark:bg-sky-900/20">
                            <div class="mb-2 flex items-center justify-between">
                                <h4 class="flex items-center font-semibold text-slate-900 dark:text-white">
                                    🖼️ Analyse des images (40%)
                                </h4>
                                <span class="text-lg font-bold {{ $imageScore >= 70 ? 'text-emerald-600' : ($imageScore >= 50 ? 'text-amber-600' : 'text-red-600') }}">
                                    {{ number_format($imageScore, 1) }}/100
                                </span>
                            </div>
                            @if(isset($details['images']['issues']) && !empty($details['images']['issues']))
                                <ul class="mt-2 space-y-1">
                                    @foreach($details['images']['issues'] as $issue)
                                        <li class="flex items-start text-sm text-slate-700 dark:text-slate-300">
                                            <span class="mr-2 text-red-500">⚠️</span>
                                            <span>{{ is_array($issue) ? json_encode($issue) : (string)$issue }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="mt-2 text-sm text-emerald-600 dark:text-emerald-400">✓ Aucun problème détecté</p>
                            @endif
                        </div>

                        <!-- Text Analysis -->
                        <div class="rounded-r-xl border-l-4 border-violet-500 bg-violet-50 py-3 pl-4 dark:bg-violet-900/20">
                            <div class="mb-2 flex items-center justify-between">
                                <h4 class="flex items-center font-semibold text-slate-900 dark:text-white">
                                    📝 Analyse du texte (30%)
                                </h4>
                                <span class="text-lg font-bold {{ $textScore >= 70 ? 'text-emerald-600' : ($textScore >= 50 ? 'text-amber-600' : 'text-red-600') }}">
                                    {{ number_format($textScore, 1) }}/100
                                </span>
                            </div>
                            @if(isset($details['text']['issues']) && !empty($details['text']['issues']))
                                <ul class="mt-2 space-y-1">
                                    @foreach($details['text']['issues'] as $issue)
                                        <li class="flex items-start text-sm text-slate-700 dark:text-slate-300">
                                            <span class="mr-2 text-red-500">⚠️</span>
                                            <span>{{ is_array($issue) ? json_encode($issue) : (string)$issue }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="mt-2 text-sm text-emerald-600 dark:text-emerald-400">✓ Aucun problème détecté</p>
                            @endif
                        </div>

                        <!-- Coherence Analysis -->
                        <div class="rounded-r-xl border-l-4 border-orange-500 bg-orange-50 py-3 pl-4 dark:bg-orange-900/20">
                            <div class="mb-2 flex items-center justify-between">
                                <h4 class="flex items-center font-semibold text-slate-900 dark:text-white">
                                    🔗 Analyse de cohérence (30%)
                                </h4>
                                <span class="text-lg font-bold {{ $coherenceScore >= 70 ? 'text-emerald-600' : ($coherenceScore >= 50 ? 'text-amber-600' : 'text-red-600') }}">
                                    {{ number_format($coherenceScore, 1) }}/100
                                </span>
                            </div>
                            @if(isset($details['coherence']['issues']) && !empty($details['coherence']['issues']))
                                <ul class="mt-2 space-y-1">
                                    @foreach($details['coherence']['issues'] as $issue)
                                        <li class="flex items-start text-sm text-slate-700 dark:text-slate-300">
                                            <span class="mr-2 text-red-500">⚠️</span>
                                            <span>{{ is_array($issue) ? json_encode($issue) : (string)$issue }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="mt-2 text-sm text-emerald-600 dark:text-emerald-400">✓ Aucun problème détecté</p>
                            @endif
                        </div>
                    </div>

                    <!-- Admin Rejection Reason -->
                    @if(isset($details['admin_rejection']))
                        <div class="mt-4 rounded-r-xl border-l-4 border-red-500 bg-red-50 py-3 pl-4 dark:bg-red-900/20">
                            <h4 class="mb-2 font-semibold text-red-800 dark:text-red-400">❌ Motif de rejet</h4>
                            <p class="text-sm text-red-700 dark:text-red-300">{{ $details['admin_rejection'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Informations principales -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="p-5 sm:p-6">
                    <h3 class="mb-4 flex items-center font-semibold text-slate-900 dark:text-white">
                        <i class="fas fa-info-circle mr-2 text-primary-600"></i>
                        Informations
                    </h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs font-medium uppercase text-slate-500 dark:text-slate-400">Prix</dt>
                            <dd class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                {{ $item->currency_symbol ?? '' }} {{ number_format($item->price, 2, ',', ' ') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase text-slate-500 dark:text-slate-400">Catégorie</dt>
                            <dd class="text-sm text-slate-900 dark:text-white">{{ $item->category->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase text-slate-500 dark:text-slate-400">Marque</dt>
                            <dd class="text-sm text-slate-900 dark:text-white">{{ $item->brand->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase text-slate-500 dark:text-slate-400">Condition</dt>
                            <dd class="text-sm text-slate-900 capitalize dark:text-white">{{ $item->condition ?? 'N/A' }}</dd>
                        </div>
                        @if($item->size)
                        <div>
                            <dt class="text-xs font-medium uppercase text-slate-500 dark:text-slate-400">Taille</dt>
                            <dd class="text-sm text-slate-900 dark:text-white">{{ $item->size }}</dd>
                        </div>
                        @endif
                        @if($item->color)
                        <div>
                            <dt class="text-xs font-medium uppercase text-slate-500 dark:text-slate-400">Couleur</dt>
                            <dd class="text-sm text-slate-900 dark:text-white">{{ $item->color }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Vendeur -->
            @if($item->user)
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="p-5 sm:p-6">
                    <h3 class="mb-4 flex items-center font-semibold text-slate-900 dark:text-white">
                        <i class="fas fa-user mr-2 text-primary-600"></i>
                        Vendeur
                    </h3>
                    <div class="flex items-center gap-3">
                        @if($item->user->avatar)
                            <img src="{{ $item->user->avatar }}" 
                                 class="h-12 w-12 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-600"
                                 alt="{{ $item->user->name }}"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="hidden h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-primary-600 to-cyan-400 text-sm font-semibold text-white">
                                {{ strtoupper(substr($item->user->name, 0, 1)) }}
                            </div>
                        @else
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-primary-600 to-cyan-400 text-sm font-semibold text-white">
                                {{ strtoupper(substr($item->user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <p class="font-medium text-slate-900 dark:text-white">{{ $item->user->name }}</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $item->user->email }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.show', $item->user) }}" 
                       class="mt-4 block w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-center text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                        Voir le profil
                    </a>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="p-5 sm:p-6">
                    <h3 class="mb-4 flex items-center font-semibold text-slate-900 dark:text-white">
                        <i class="fas fa-cogs mr-2 text-primary-600"></i>
                        Modération
                    </h3>
                    <div class="space-y-3">
                        @if($item->verification_status === 'pending')
                        <form action="{{ route('admin.items.approve', $item) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 font-medium text-white shadow-sm transition-colors hover:bg-emerald-700">
                                <i class="fas fa-check"></i>
                                Approuver l'item
                            </button>
                        </form>

                        <button type="button" 
                                onclick="openRejectModal()"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-3 font-medium text-white shadow-sm transition-colors hover:bg-red-700">
                            <i class="fas fa-times"></i>
                            Rejeter l'item
                        </button>
                        @endif

                        <hr class="border-slate-100 dark:border-slate-700">

                        @if($item->is_blocked)
                        <form action="{{ route('admin.items.unblock', $item) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 font-medium text-white shadow-sm transition-colors hover:bg-emerald-700">
                                <i class="fas fa-unlock"></i>
                                Débloquer l'article
                            </button>
                        </form>
                        @else
                        <button type="button" 
                                onclick="openBlockModal()"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-3 font-medium text-white shadow-sm transition-colors hover:bg-red-700">
                            <i class="fas fa-ban"></i>
                            Bloquer l'article
                        </button>
                        @endif

                        @if($item->isCurrentlySuspended())
                        <form action="{{ route('admin.items.unsuspend', $item) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 font-medium text-white shadow-sm transition-colors hover:bg-emerald-700">
                                <i class="fas fa-play-circle"></i>
                                Rétablir l'article
                            </button>
                        </form>
                        @else
                        <button type="button" 
                                onclick="openSuspendModal()"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-orange-600 px-4 py-3 font-medium text-white shadow-sm transition-colors hover:bg-orange-700">
                            <i class="fas fa-pause-circle"></i>
                            Suspendre l'article
                        </button>
                        @endif
                    </div>

                    <!-- Bloc motif du blocage -->
                    @if($item->block_reason)
                    <div class="mt-4 rounded-xl bg-red-50 p-3 dark:bg-red-900/20">
                        <p class="text-xs font-semibold uppercase text-red-700 dark:text-red-400">Motif du blocage</p>
                        <p class="mt-1 text-sm text-red-600 dark:text-red-300">{{ $item->block_reason }}</p>
                        @if($item->blockedBy)
                            <p class="mt-1 text-xs text-red-500">Par {{ $item->blockedBy->name }}, le {{ $item->blocked_at?->format('d/m/Y à H:i') }}</p>
                        @endif
                    </div>
                    @endif

                    <!-- Bloc motif de suspension -->
                    @if($item->suspend_reason)
                    <div class="mt-4 rounded-xl bg-orange-50 p-3 dark:bg-orange-900/20">
                        <p class="text-xs font-semibold uppercase text-orange-700 dark:text-orange-400">Motif de suspension</p>
                        <p class="mt-1 text-sm text-orange-600 dark:text-orange-300">{{ $item->suspend_reason }}</p>
                        @if($item->suspendedBy)
                            <p class="mt-1 text-xs text-orange-500">Par {{ $item->suspendedBy->name }}, le {{ $item->suspended_at?->format('d/m/Y à H:i') }}</p>
                        @endif
                        @if($item->suspended_until)
                            <p class="mt-1 text-xs text-orange-500">Jusqu'au {{ $item->suspended_until->format('d/m/Y') }}</p>
                        @endif
                    </div>
                    @endif

                    <!-- Bloc motif de rejet -->
                    @if($item->rejection_reason)
                    <div class="mt-4 rounded-xl bg-slate-50 p-3 dark:bg-slate-900/20">
                        <p class="text-xs font-semibold uppercase text-slate-700 dark:text-slate-400">Motif du rejet</p>
                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ $item->rejection_reason }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Historique -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="p-5 sm:p-6">
                    <h3 class="mb-4 flex items-center font-semibold text-slate-900 dark:text-white">
                        <i class="fas fa-history mr-2 text-primary-600"></i>
                        Historique
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-start">
                            <i class="fas fa-plus-circle mt-1 mr-2 text-sky-500"></i>
                            <div>
                                <p class="font-medium text-slate-900 dark:text-white">Création</p>
                                <p class="text-slate-500 dark:text-slate-400">{{ $item->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                        @if($item->verified_at)
                        <div class="flex items-start">
                            <i class="fas fa-check-circle mt-1 mr-2 text-emerald-500"></i>
                            <div>
                                <p class="font-medium text-slate-900 dark:text-white">Vérification</p>
                                <p class="text-slate-500 dark:text-slate-400">{{ $item->verified_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                        @endif
                        @if($item->updated_at && $item->updated_at != $item->created_at)
                        <div class="flex items-start">
                            <i class="fas fa-edit mt-1 mr-2 text-orange-500"></i>
                            <div>
                                <p class="font-medium text-slate-900 dark:text-white">Dernière modification</p>
                                <p class="text-slate-500 dark:text-slate-400">{{ $item->updated_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm" onclick="closeModal(event, 'rejectModal')">
    <div class="mx-4 w-full max-w-md rounded-2xl bg-white shadow-xl dark:bg-slate-800" onclick="event.stopPropagation()">
        <div class="p-6">
            <h3 class="mb-4 text-xl font-bold text-slate-900 dark:text-white">Rejeter l'item</h3>
            
            <form action="{{ route('admin.items.reject', $item) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                        Motif du rejet
                    </label>
                    <textarea name="reason" rows="4"
                              class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                              placeholder="Raison du rejet..."></textarea>
                </div>
                <div class="flex flex-col-reverse items-center justify-end gap-2 sm:flex-row">
                    <button type="button" onclick="closeModal(event, 'rejectModal')"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 sm:w-auto dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                        Annuler
                    </button>
                    <button type="submit"
                            class="w-full rounded-xl bg-red-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-red-700 sm:w-auto">
                        Confirmer le rejet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Block Modal -->
<div id="blockModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm" onclick="closeModal(event, 'blockModal')">
    <div class="mx-4 w-full max-w-md rounded-2xl bg-white shadow-xl dark:bg-slate-800" onclick="event.stopPropagation()">
        <div class="p-6">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                    <i class="fas fa-ban text-2xl text-red-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Bloquer l'article</h3>
                    <p class="text-sm text-slate-500">L'article sera masqué de la plateforme.</p>
                </div>
            </div>
            <form action="{{ route('admin.items.block', $item) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Motif du blocage</label>
                    <textarea name="reason" rows="4"
                              class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                              placeholder="Raison du blocage..."></textarea>
                </div>
                <div class="flex flex-col-reverse items-center justify-end gap-2 sm:flex-row">
                    <button type="button" onclick="closeModal(event, 'blockModal')"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 sm:w-auto dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                        Annuler
                    </button>
                    <button type="submit"
                            class="w-full rounded-xl bg-red-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-red-700 sm:w-auto">
                        Confirmer le blocage
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Suspend Modal -->
<div id="suspendModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm" onclick="closeModal(event, 'suspendModal')">
    <div class="mx-4 w-full max-w-md rounded-2xl bg-white shadow-xl dark:bg-slate-800" onclick="event.stopPropagation()">
        <div class="p-6">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-orange-100 dark:bg-orange-900/30">
                    <i class="fas fa-pause-circle text-2xl text-orange-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Suspendre l'article</h3>
                    <p class="text-sm text-slate-500">L'article sera temporairement masqué.</p>
                </div>
            </div>
            <form action="{{ route('admin.items.suspend', $item) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Motif de suspension</label>
                    <textarea name="reason" rows="3"
                              class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                              placeholder="Raison de la suspension..."></textarea>
                </div>
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Durée (jours)</label>
                    <input type="number" name="days" min="1" max="365" placeholder="Laisser vide pour indéfini"
                           class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                    <p class="mt-1 text-xs text-slate-500">Laissez vide pour une suspension indéfinie.</p>
                </div>
                <div class="flex flex-col-reverse items-center justify-end gap-2 sm:flex-row">
                    <button type="button" onclick="closeModal(event, 'suspendModal')"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 sm:w-auto dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                        Annuler
                    </button>
                    <button type="submit"
                            class="w-full rounded-xl bg-orange-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-orange-700 sm:w-auto">
                        Confirmer la suspension
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-90" onclick="closeImageModal()">
    <div class="relative max-h-screen max-w-6xl p-4">
        <button onclick="closeImageModal()" 
                class="absolute top-6 right-6 z-10 text-white transition hover:text-slate-300">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <div class="text-center">
            <img id="modalImage" src="" class="mx-auto max-h-screen max-w-full object-contain" alt="Image agrandie">
            <p id="modalImageCaption" class="mt-4 text-sm text-white"></p>
        </div>
    </div>
</div>

<script>
function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
}

function openBlockModal() {
    document.getElementById('blockModal').classList.remove('hidden');
    document.getElementById('blockModal').classList.add('flex');
}

function openSuspendModal() {
    document.getElementById('suspendModal').classList.remove('hidden');
    document.getElementById('suspendModal').classList.add('flex');
}

function closeModal(event, modalId) {
    if (!event || event.target.id === modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.getElementById(modalId).classList.remove('flex');
    }
}

function openImageModal(imageUrl, caption) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalCaption = document.getElementById('modalImageCaption');
    modalImage.src = imageUrl;
    modalCaption.textContent = caption || '';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('[id$=Modal]').forEach(m => {
            m.classList.add('hidden');
            m.classList.remove('flex');
        });
    }
});
</script>
@endsection
