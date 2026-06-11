@extends('layouts.admin')

@section('title', 'Détails de l\'item #' . $item->id)

@section('page-title', 'Détails de l\'item')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header avec actions -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.items.index') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 mb-2">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour à la liste
            </a>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $item->name }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Créé le {{ $item->created_at->format('d/m/Y à H:i') }}
                @if($item->verified_at)
                    • Vérifié le {{ $item->verified_at->format('d/m/Y à H:i') }}
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Badge de statut vérification -->
            @php
                $statusConfig = [
                    'approved' => ['class' => 'bg-green-100 text-green-800 border-green-300 dark:bg-green-900/30 dark:text-green-400 dark:border-green-700', 'icon' => 'fa-check-circle', 'label' => 'Approuvé'],
                    'pending' => ['class' => 'bg-yellow-100 text-yellow-800 border-yellow-300 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-700', 'icon' => 'fa-clock', 'label' => 'En attente'],
                    'rejected' => ['class' => 'bg-red-100 text-red-800 border-red-300 dark:bg-red-900/30 dark:text-red-400 dark:border-red-700', 'icon' => 'fa-times-circle', 'label' => 'Rejeté'],
                ];
                $status = $statusConfig[$item->verification_status] ?? $statusConfig['pending'];
            @endphp
            <div class="px-4 py-2 rounded-lg border-2 {{ $status['class'] }} flex items-center space-x-2">
                <i class="fas {{ $status['icon'] }}"></i>
                <span class="font-semibold">{{ $status['label'] }}</span>
            </div>

            <!-- Badge de modération -->
            @if($item->is_blocked)
                <div class="px-4 py-2 rounded-lg border-2 bg-red-100 text-red-800 border-red-300 dark:bg-red-900/30 dark:text-red-400 dark:border-red-700 flex items-center space-x-2">
                    <i class="fas fa-ban"></i>
                    <span class="font-semibold">Bloqué</span>
                </div>
            @elseif($item->isCurrentlySuspended())
                <div class="px-4 py-2 rounded-lg border-2 bg-orange-100 text-orange-800 border-orange-300 dark:bg-orange-900/30 dark:text-orange-400 dark:border-orange-700 flex items-center space-x-2">
                    <i class="fas fa-pause-circle"></i>
                    <span class="font-semibold">Suspendu</span>
                    @if($item->suspended_until)
                        <span class="text-xs">jusqu'au {{ $item->suspended_until->format('d/m/Y') }}</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Images -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-images mr-2 text-primary-600"></i>
                        Images ({{ count($item->images ?? []) }})
                    </h3>
                    
                    @if(!empty($item->images))
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            @foreach($item->images as $index => $image)
                                <div class="relative group cursor-pointer" onclick="openImageModal('{{ asset('storage/' . $image) }}', '{{ $item->name }} - Image {{ $index + 1 }}')">
                                    <img src="{{ asset('storage/' . $image) }}" 
                                         class="w-full h-48 object-cover rounded-lg border-2 border-gray-200 dark:border-gray-600 group-hover:border-primary-500 transition"
                                         alt="Image {{ $index + 1 }}">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition rounded-lg flex items-center justify-center">
                                        <i class="fas fa-search-plus text-white text-2xl opacity-0 group-hover:opacity-100 transition"></i>
                                    </div>
                                    <div class="absolute top-2 left-2 bg-black bg-opacity-75 text-white px-2 py-1 rounded text-xs">
                                        {{ $index + 1 }}/{{ count($item->images) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">Aucune image disponible</p>
                    @endif
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-align-left mr-2 text-primary-600"></i>
                        Description
                    </h3>
                    <div class="prose dark:prose-invert max-w-none">
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $item->description }}</p>
                    </div>
                </div>
            </div>

            <!-- Analyse IA -->
            @if($item->verification_details)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-robot mr-2 text-primary-600"></i>
                        Analyse IA
                        @if($item->verification_score)
                            <span class="ml-auto text-2xl font-bold {{ $item->verification_score >= 75 ? 'text-green-600' : ($item->verification_score >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
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
                        <div class="border-l-4 border-blue-500 pl-4 py-3 bg-blue-50 dark:bg-blue-900/20 rounded-r">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-900 dark:text-white flex items-center">
                                    🖼️ Analyse des images (40%)
                                </h4>
                                <span class="text-lg font-bold {{ $imageScore >= 70 ? 'text-green-600' : ($imageScore >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($imageScore, 1) }}/100
                                </span>
                            </div>
                            @if(isset($details['images']['issues']) && !empty($details['images']['issues']))
                                <ul class="space-y-1 mt-2">
                                    @foreach($details['images']['issues'] as $issue)
                                        <li class="flex items-start text-sm text-gray-700 dark:text-gray-300">
                                            <span class="text-red-500 mr-2">⚠️</span>
                                            <span>{{ is_array($issue) ? json_encode($issue) : (string)$issue }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-green-600 dark:text-green-400 mt-2">✓ Aucun problème détecté</p>
                            @endif
                        </div>

                        <!-- Text Analysis -->
                        <div class="border-l-4 border-purple-500 pl-4 py-3 bg-purple-50 dark:bg-purple-900/20 rounded-r">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-900 dark:text-white flex items-center">
                                    📝 Analyse du texte (30%)
                                </h4>
                                <span class="text-lg font-bold {{ $textScore >= 70 ? 'text-green-600' : ($textScore >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($textScore, 1) }}/100
                                </span>
                            </div>
                            @if(isset($details['text']['issues']) && !empty($details['text']['issues']))
                                <ul class="space-y-1 mt-2">
                                    @foreach($details['text']['issues'] as $issue)
                                        <li class="flex items-start text-sm text-gray-700 dark:text-gray-300">
                                            <span class="text-red-500 mr-2">⚠️</span>
                                            <span>{{ is_array($issue) ? json_encode($issue) : (string)$issue }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-green-600 dark:text-green-400 mt-2">✓ Aucun problème détecté</p>
                            @endif
                        </div>

                        <!-- Coherence Analysis -->
                        <div class="border-l-4 border-orange-500 pl-4 py-3 bg-orange-50 dark:bg-orange-900/20 rounded-r">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-900 dark:text-white flex items-center">
                                    🔗 Analyse de cohérence (30%)
                                </h4>
                                <span class="text-lg font-bold {{ $coherenceScore >= 70 ? 'text-green-600' : ($coherenceScore >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($coherenceScore, 1) }}/100
                                </span>
                            </div>
                            @if(isset($details['coherence']['issues']) && !empty($details['coherence']['issues']))
                                <ul class="space-y-1 mt-2">
                                    @foreach($details['coherence']['issues'] as $issue)
                                        <li class="flex items-start text-sm text-gray-700 dark:text-gray-300">
                                            <span class="text-red-500 mr-2">⚠️</span>
                                            <span>{{ is_array($issue) ? json_encode($issue) : (string)$issue }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-green-600 dark:text-green-400 mt-2">✓ Aucun problème détecté</p>
                            @endif
                        </div>
                    </div>

                    <!-- Admin Rejection Reason -->
                    @if(isset($details['admin_rejection']))
                        <div class="mt-4 border-l-4 border-red-500 pl-4 py-3 bg-red-50 dark:bg-red-900/20 rounded-r">
                            <h4 class="font-semibold text-red-800 dark:text-red-400 mb-2">❌ Motif de rejet</h4>
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
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-primary-600"></i>
                        Informations
                    </h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Prix</dt>
                            <dd class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                {{ $item->currency_symbol ?? '' }} {{ number_format($item->price, 2, ',', ' ') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Catégorie</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $item->category->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Marque</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $item->brand->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Condition</dt>
                            <dd class="text-sm text-gray-900 dark:text-white capitalize">{{ $item->condition ?? 'N/A' }}</dd>
                        </div>
                        @if($item->size)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Taille</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $item->size }}</dd>
                        </div>
                        @endif
                        @if($item->color)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Couleur</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $item->color }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Vendeur -->
            @if($item->user)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-user mr-2 text-primary-600"></i>
                        Vendeur
                    </h3>
                    <div class="flex items-center space-x-3">
                        <img src="{{ $item->user->avatar ?? asset('images/default-avatar.png') }}" 
                             class="w-12 h-12 rounded-full object-cover"
                             alt="{{ $item->user->name }}">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $item->user->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $item->user->email }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.show', $item->user) }}" 
                       class="mt-4 block text-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        Voir le profil
                    </a>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-cogs mr-2 text-primary-600"></i>
                        Modération
                    </h3>
                    <div class="space-y-3">
                        @if($item->verification_status === 'pending')
                        <form action="{{ route('admin.items.approve', $item) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-medium flex items-center justify-center">
                                <i class="fas fa-check mr-2"></i>
                                Approuver l'item
                            </button>
                        </form>

                        <button type="button" 
                                onclick="openRejectModal()"
                                class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium flex items-center justify-center">
                            <i class="fas fa-times mr-2"></i>
                            Rejeter l'item
                        </button>
                        @endif

                        <hr class="border-gray-200 dark:border-gray-700">

                        @if($item->is_blocked)
                        <form action="{{ route('admin.items.unblock', $item) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-medium flex items-center justify-center">
                                <i class="fas fa-unlock mr-2"></i>
                                Débloquer l'article
                            </button>
                        </form>
                        @else
                        <button type="button" 
                                onclick="openBlockModal()"
                                class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium flex items-center justify-center">
                            <i class="fas fa-ban mr-2"></i>
                            Bloquer l'article
                        </button>
                        @endif

                        @if($item->isCurrentlySuspended())
                        <form action="{{ route('admin.items.unsuspend', $item) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-medium flex items-center justify-center">
                                <i class="fas fa-play-circle mr-2"></i>
                                Rétablir l'article
                            </button>
                        </form>
                        @else
                        <button type="button" 
                                onclick="openSuspendModal()"
                                class="w-full px-4 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition font-medium flex items-center justify-center">
                            <i class="fas fa-pause-circle mr-2"></i>
                            Suspendre l'article
                        </button>
                        @endif
                    </div>

                    <!-- Bloc motif du blocage -->
                    @if($item->block_reason)
                    <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <p class="text-xs font-semibold text-red-700 dark:text-red-400 uppercase">Motif du blocage</p>
                        <p class="text-sm text-red-600 dark:text-red-300 mt-1">{{ $item->block_reason }}</p>
                        @if($item->blockedBy)
                            <p class="text-xs text-red-500 mt-1">Par {{ $item->blockedBy->name }}, le {{ $item->blocked_at?->format('d/m/Y à H:i') }}</p>
                        @endif
                    </div>
                    @endif

                    <!-- Bloc motif de suspension -->
                    @if($item->suspend_reason)
                    <div class="mt-4 p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                        <p class="text-xs font-semibold text-orange-700 dark:text-orange-400 uppercase">Motif de suspension</p>
                        <p class="text-sm text-orange-600 dark:text-orange-300 mt-1">{{ $item->suspend_reason }}</p>
                        @if($item->suspendedBy)
                            <p class="text-xs text-orange-500 mt-1">Par {{ $item->suspendedBy->name }}, le {{ $item->suspended_at?->format('d/m/Y à H:i') }}</p>
                        @endif
                        @if($item->suspended_until)
                            <p class="text-xs text-orange-500 mt-1">Jusqu'au {{ $item->suspended_until->format('d/m/Y') }}</p>
                        @endif
                    </div>
                    @endif

                    <!-- Bloc motif de rejet -->
                    @if($item->rejection_reason)
                    <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-900/20 rounded-lg">
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-400 uppercase">Motif du rejet</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $item->rejection_reason }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Historique -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-history mr-2 text-primary-600"></i>
                        Historique
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-start">
                            <i class="fas fa-plus-circle text-blue-500 mr-2 mt-1"></i>
                            <div>
                                <p class="text-gray-900 dark:text-white font-medium">Création</p>
                                <p class="text-gray-500 dark:text-gray-400">{{ $item->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                        @if($item->verified_at)
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
                            <div>
                                <p class="text-gray-900 dark:text-white font-medium">Vérification</p>
                                <p class="text-gray-500 dark:text-gray-400">{{ $item->verified_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                        @endif
                        @if($item->updated_at && $item->updated_at != $item->created_at)
                        <div class="flex items-start">
                            <i class="fas fa-edit text-orange-500 mr-2 mt-1"></i>
                            <div>
                                <p class="text-gray-900 dark:text-white font-medium">Dernière modification</p>
                                <p class="text-gray-500 dark:text-gray-400">{{ $item->updated_at->format('d/m/Y à H:i') }}</p>
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
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" onclick="closeModal(event, 'rejectModal')">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="p-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Rejeter l'item</h3>
            
            <form action="{{ route('admin.items.reject', $item) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Motif du rejet
                    </label>
                    <textarea name="reason" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white"
                              placeholder="Raison du rejet..."></textarea>
                </div>
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" onclick="closeModal(event, 'rejectModal')"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        Annuler
                    </button>
                    <button type="submit"
                            class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium">
                        Confirmer le rejet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Block Modal -->
<div id="blockModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" onclick="closeModal(event, 'blockModal')">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="fas fa-ban text-2xl text-red-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Bloquer l'article</h3>
                    <p class="text-sm text-gray-500">L'article sera masqué de la plateforme.</p>
                </div>
            </div>
            <form action="{{ route('admin.items.block', $item) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Motif du blocage</label>
                    <textarea name="reason" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white"
                              placeholder="Raison du blocage..."></textarea>
                </div>
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" onclick="closeModal(event, 'blockModal')"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        Annuler
                    </button>
                    <button type="submit"
                            class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium">
                        Confirmer le blocage
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Suspend Modal -->
<div id="suspendModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" onclick="closeModal(event, 'suspendModal')">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                    <i class="fas fa-pause-circle text-2xl text-orange-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Suspendre l'article</h3>
                    <p class="text-sm text-gray-500">L'article sera temporairement masqué.</p>
                </div>
            </div>
            <form action="{{ route('admin.items.suspend', $item) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Motif de suspension</label>
                    <textarea name="reason" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white"
                              placeholder="Raison de la suspension..."></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Durée (jours)</label>
                    <input type="number" name="days" min="1" max="365" placeholder="Laisser vide pour indéfini"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 dark:bg-gray-700 dark:text-white">
                    <p class="mt-1 text-xs text-gray-500">Laissez vide pour une suspension indéfinie.</p>
                </div>
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" onclick="closeModal(event, 'suspendModal')"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        Annuler
                    </button>
                    <button type="submit"
                            class="px-5 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition font-medium">
                        Confirmer la suspension
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-90 hidden items-center justify-center z-50" onclick="closeImageModal()">
    <div class="relative max-w-6xl max-h-screen p-4">
        <button onclick="closeImageModal()" 
                class="absolute top-6 right-6 text-white hover:text-gray-300 transition z-10">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <div class="text-center">
            <img id="modalImage" src="" class="max-w-full max-h-screen object-contain mx-auto" alt="Image agrandie">
            <p id="modalImageCaption" class="text-white text-sm mt-4"></p>
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
