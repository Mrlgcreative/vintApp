@extends('app')

@section('title', 'Mes demandes de support — ' . config('app.name', 'VintApp'))

@section('content')
<div class="bg-gray-50 dark:bg-gray-950 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- En-tête -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div class="flex items-start gap-4">
                <x-icon icon="fas fa-life-ring" size="lg" tone="primary" class="mt-0.5" />
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Mes demandes de support</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gérez vos conversations avec notre équipe d'assistance</p>
                </div>
            </div>
            <x-button-primary href="{{ route('support.create') }}" size="lg" class="w-full sm:w-auto justify-center">
                <i class="fas fa-plus mr-2"></i> Nouvelle demande
            </x-button-primary>
        </div>

        <!-- Statistiques rapides -->
        @if($chats->count() > 0)
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <x-stat-card value="{{ $chats->count() }}" label="Total" icon="fas fa-comments" tone="primary" />
                <x-stat-card value="{{ $chats->whereIn('status', ['open', 'in_progress'])->count() }}" label="En cours" icon="fas fa-clock" tone="amber" />
                <x-stat-card value="{{ $chats->where('status', 'waiting_user')->count() }}" label="En attente" icon="fas fa-hourglass-half" tone="blue" />
                <x-stat-card value="{{ $chats->where('status', 'closed')->count() }}" label="Résolues" icon="fas fa-circle-check" tone="emerald" />
            </div>
        @endif

        <!-- Liste des conversations -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/50 shadow-sm overflow-hidden">
            @if($chats->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Référence</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sujet</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Priorité</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Agent</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dernière activité</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700/50">
                            @foreach($chats as $chat)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $chat->reference }}</span>
                                            @if($chat->unread_count_for_user > 0)
                                                <x-badge variant="danger" class="animate-pulse">{{ $chat->unread_count_for_user }} nouveau(x)</x-badge>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $chat->subject ?: 'Demande d\'assistance' }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $chat->formatted_category }}</div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusVariant = match ($chat->status) {
                                                'open' => 'danger',
                                                'in_progress' => 'warning',
                                                'waiting_user' => 'info',
                                                'closed' => 'success',
                                                default => 'secondary',
                                            };
                                        @endphp
                                        <x-badge variant="{{ $statusVariant }}">{{ $chat->formatted_status }}</x-badge>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $priorityVariant = match ($chat->priority) {
                                                'low' => 'secondary',
                                                'normal' => 'info',
                                                'high' => 'warning',
                                                'urgent' => 'danger',
                                                default => 'secondary',
                                            };
                                        @endphp
                                        <x-badge variant="{{ $priorityVariant }}">{{ $chat->formatted_priority }}</x-badge>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($chat->admin)
                                            <div class="flex items-center gap-2">
                                                @if($chat->admin->avatar)
                                                    <img class="w-8 h-8 rounded-full object-cover ring-2 ring-gray-100 dark:ring-gray-700"
                                                         src="{{ asset('storage/' . $chat->admin->avatar) }}"
                                                         alt="{{ $chat->admin->name }}">
                                                @else
                                                    <div class="w-8 h-8 rounded-full bg-vinted-primary-100 dark:bg-vinted-primary-500/20 flex items-center justify-center ring-2 ring-gray-100 dark:ring-gray-700">
                                                        <span class="text-xs font-semibold text-vinted-primary-700 dark:text-vinted-primary-300">{{ substr($chat->admin->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $chat->admin->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-sm italic text-gray-400 dark:text-gray-500">En attente d'assignation</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        @if($chat->last_message_at)
                                            {{ $chat->last_message_at->diffForHumans() }}
                                        @else
                                            {{ $chat->created_at->diffForHumans() }}
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('support.show', $chat) }}"
                                               class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                               title="Voir">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            @if($chat->status !== 'closed')
                                                <button onclick="closeChat('{{ $chat->id }}')"
                                                        class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"
                                                        title="Fermer">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <!-- État vide -->
                <div class="text-center py-16 px-4">
                    <div class="w-20 h-20 mx-auto rounded-full bg-gray-100 dark:bg-gray-700/40 flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold tracking-tight text-gray-900 dark:text-white mb-2">Aucune demande de support</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Vous n'avez pas encore créé de demande d'assistance.</p>
                    <x-button-primary href="{{ route('support.create') }}">
                        <i class="fas fa-plus mr-2"></i> Créer ma première demande
                    </x-button-primary>
                </div>
            @endif
        </div>

        <!-- Aide et informations -->
        <div class="mt-8 rounded-xl border border-blue-200 dark:border-blue-500/20 bg-blue-50 dark:bg-blue-500/5 p-6">
            <div class="flex items-start gap-4">
                <x-icon icon="fas fa-circle-info" tone="blue" class="mt-0.5" />
                <div class="flex-1">
                    <h5 class="text-base font-semibold tracking-tight text-gray-900 dark:text-white mb-3">Comment fonctionne le support ?</h5>
                    <ul class="space-y-2 mb-4 text-sm">
                        <li class="flex items-start gap-2">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-vinted-primary-600 text-white text-xs font-medium flex-shrink-0 mt-0.5">1</span>
                            <span class="text-gray-700 dark:text-gray-300"><strong>Créez votre demande</strong> - Décrivez votre problème en détail</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-vinted-primary-600 text-white text-xs font-medium flex-shrink-0 mt-0.5">2</span>
                            <span class="text-gray-700 dark:text-gray-300"><strong>Assignation automatique</strong> - Un agent vous sera assigné selon la priorité</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-vinted-primary-600 text-white text-xs font-medium flex-shrink-0 mt-0.5">3</span>
                            <span class="text-gray-700 dark:text-gray-300"><strong>Échange en temps réel</strong> - Communiquez directement avec votre agent</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-vinted-primary-600 text-white text-xs font-medium flex-shrink-0 mt-0.5">4</span>
                            <span class="text-gray-700 dark:text-gray-300"><strong>Résolution</strong> - Une fois votre problème résolu, la conversation sera fermée</span>
                        </li>
                    </ul>
                    <div class="flex flex-wrap gap-4 text-sm">
                        <span class="inline-flex items-center gap-2"><x-badge variant="danger">Ouvert</x-badge> Ouvert</span>
                        <span class="inline-flex items-center gap-2"><x-badge variant="warning">En cours</x-badge> En cours</span>
                        <span class="inline-flex items-center gap-2"><x-badge variant="info">En attente</x-badge> En attente de votre réponse</span>
                        <span class="inline-flex items-center gap-2"><x-badge variant="success">Résolu</x-badge> Résolu</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function closeChat(chatId) {
    if (confirm('Êtes-vous sûr de vouloir fermer cette conversation ? Elle sera marquée comme résolue.')) {
        fetch(`/support/${chatId}/close`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la fermeture de la conversation.');
        });
    }
}
</script>
@endsection