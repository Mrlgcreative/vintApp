@extends('layouts.support')

@section('title', 'Conversation Support')

@section('content')
<div>
    <!-- En-tête de la conversation -->
    <div class="mb-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="border-b border-slate-100 p-5 sm:p-6 dark:border-slate-700">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.support.index') }}" class="text-slate-500 transition-colors hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
                            <i class="fas fa-arrow-left text-xl"></i>
                        </a>
                        <div>
                            <h1 class="text-xl font-bold text-slate-900 dark:text-white">{{ $supportChat->reference }}</h1>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $supportChat->subject ?: 'Demande d\'assistance' }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-4">
                    <!-- Statut -->
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Statut:</label>
                        <select id="statusSelect" data-chat-id="{{ $supportChat->id }}"
                                class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                            <option value="open" {{ $supportChat->status === 'open' ? 'selected' : '' }}>Ouvert</option>
                            <option value="in_progress" {{ $supportChat->status === 'in_progress' ? 'selected' : '' }}>En cours</option>
                            <option value="waiting_user" {{ $supportChat->status === 'waiting_user' ? 'selected' : '' }}>En attente utilisateur</option>
                            <option value="closed" {{ $supportChat->status === 'closed' ? 'selected' : '' }}>Fermé</option>
                        </select>
                    </div>

                    <!-- Priorité -->
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Priorité:</label>
                        <select id="prioritySelect" data-chat-id="{{ $supportChat->id }}"
                                class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                            <option value="low" {{ $supportChat->priority === 'low' ? 'selected' : '' }}>Faible</option>
                            <option value="normal" {{ $supportChat->priority === 'normal' ? 'selected' : '' }}>Normale</option>
                            <option value="high" {{ $supportChat->priority === 'high' ? 'selected' : '' }}>Élevée</option>
                            <option value="urgent" {{ $supportChat->priority === 'urgent' ? 'selected' : '' }}>Urgente</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Informations sur l'utilisateur et l'assignation -->
            <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="flex items-center gap-3">
                    @if($supportChat->user?->avatar)
                        <img class="h-10 w-10 rounded-full object-cover"
                             src="{{ $supportChat->user->avatar_url }}"
                             alt="">
                    @else
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-200 dark:bg-slate-700">
                            <i class="fas fa-user text-slate-500 dark:text-slate-400"></i>
                        </div>
                    @endif
                    <div>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $supportChat->user?->name ?? 'Utilisateur supprimé' }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $supportChat->user?->email ?? '-' }}</p>
                    </div>
                </div>

                <div>
                    <p class="mb-1 text-xs font-medium text-slate-500 dark:text-slate-400">Catégorie</p>
                    <p class="text-sm text-slate-900 dark:text-white">{{ $supportChat->formatted_category }}</p>
                </div>

                <div>
                    <p class="mb-1 text-xs font-medium text-slate-500 dark:text-slate-400">Assigné à</p>
                    @if($supportChat->admin)
                        <div class="flex items-center gap-2">
                            @if($supportChat->admin->avatar)
                                <img class="h-6 w-6 rounded-full object-cover"
                                     src="{{ $supportChat->admin->avatar_url }}"
                                     alt="">
                            @else
                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-sky-100 dark:bg-sky-900/30">
                                    <i class="fas fa-user text-sky-600 dark:text-sky-400 text-xs"></i>
                                </div>
                            @endif
                            <span class="text-sm text-slate-900 dark:text-white">{{ $supportChat->admin->name }}</span>
                        </div>
                    @else
                        <p class="text-sm italic text-slate-400 dark:text-slate-500">Non assigné</p>
                    @endif
                </div>
            </div>

            <!-- Métadonnées si disponibles -->
            @if($supportChat->metadata)
                <div class="mt-4 rounded-lg bg-slate-50 p-4 dark:bg-slate-700/50">
                    <p class="mb-2 text-sm font-medium text-slate-700 dark:text-slate-300">Informations système:</p>
                    <div class="grid grid-cols-2 gap-2 text-sm text-slate-600 md:grid-cols-4 dark:text-slate-400">
                        @if(isset($supportChat->metadata['browser']))
                            <div><span class="font-medium">Navigateur:</span> {{ $supportChat->metadata['browser'] }}</div>
                        @endif
                        @if(isset($supportChat->metadata['os']))
                            <div><span class="font-medium">OS:</span> {{ $supportChat->metadata['os'] }}</div>
                        @endif
                        @if(isset($supportChat->metadata['ip']))
                            <div><span class="font-medium">IP:</span> {{ $supportChat->metadata['ip'] }}</div>
                        @endif
                        @if(isset($supportChat->metadata['url']))
                            <div><span class="font-medium">Page:</span> {{ Str::limit($supportChat->metadata['url'], 30) }}</div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Messages -->
        <div class="lg:col-span-2">
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <!-- En-tête des messages -->
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                    <h2 class="flex items-center gap-2 text-base font-semibold text-slate-900 dark:text-white">
                        <i class="fas fa-comments text-primary-600"></i>
                        Conversation
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            {{ $supportChat->messages->count() }} message(s)
                        </span>
                    </h2>
                </div>

                <!-- Liste des messages -->
                <div class="p-5 sm:p-6">
                    <div class="flex max-h-[500px] flex-col gap-4 overflow-y-auto" id="messagesContainer">
                        @forelse($supportChat->messages as $message)
                            <div class="flex {{ $message->is_admin ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[70%]">
                                    <div class="flex items-start gap-3 {{ $message->is_admin ? 'flex-row-reverse' : '' }}">
                                        <!-- Avatar -->
                                        <div class="flex-shrink-0">
                                            @if($message->user?->avatar)
                                                <img class="h-8 w-8 rounded-full object-cover"
                                                     src="{{ $message->user->avatar_url }}"
                                                     alt="">
                                            @else
                                                <div class="flex h-8 w-8 items-center justify-center rounded-full {{ $message->is_admin ? 'bg-sky-100 dark:bg-sky-900/30' : 'bg-slate-200 dark:bg-slate-700' }}">
                                                    <i class="fas fa-user {{ $message->is_admin ? 'text-sky-600 dark:text-sky-400' : 'text-slate-500 dark:text-slate-400' }} text-xs"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Message -->
                                        <div class="flex flex-col {{ $message->is_admin ? 'items-end' : 'items-start' }}">
                                            <div class="rounded-2xl px-4 py-3 {{ $message->is_admin ? 'bg-primary-600 text-white' : 'bg-slate-100 text-slate-900 dark:bg-slate-700 dark:text-white' }}">
                                                <p class="text-sm whitespace-pre-wrap">{{ $message->message }}</p>

                                                <!-- Pièces jointes -->
                                                @if($message->hasAttachments())
                                                    <div class="mt-2 flex flex-col gap-1">
                                                        @foreach($message->attachments as $attachment)
                                                            <a href="{{ asset('storage/' . $attachment['path']) }}"
                                                               target="_blank"
                                                               class="text-sm {{ $message->is_admin ? 'text-primary-100 hover:text-white' : 'text-sky-600 dark:text-sky-400 hover:underline' }}">
                                                                <i class="fas fa-paperclip mr-1"></i>{{ $attachment['name'] }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Métadonnées du message -->
                                            <div class="mt-1 flex items-center gap-2">
                                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $message->sender_name }}</span>
                                                <span class="text-xs text-slate-400 dark:text-slate-500">{{ $message->created_at->format('d/m/Y H:i') }}</span>
                                                @if($message->is_read)
                                                    <i class="fas fa-check-double text-xs text-emerald-500" title="Lu"></i>
                                                @else
                                                    <i class="fas fa-check text-xs text-slate-400" title="Envoyé"></i>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center">
                                <i class="fas fa-comments text-5xl text-slate-300 dark:text-slate-600 mb-4"></i>
                                <p class="text-slate-500 dark:text-slate-400">Aucun message dans cette conversation</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Formulaire de réponse -->
                @if($supportChat->status !== 'closed')
                    <div class="border-t border-slate-100 bg-slate-50 px-5 py-4 dark:border-slate-700 dark:bg-slate-700/50 sm:px-6">
                        <form action="{{ route('admin.support.reply', $supportChat) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Votre réponse</label>
                                    <textarea name="message" rows="4" required
                                            class="w-full resize-none rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 transition-colors placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                                            placeholder="Tapez votre réponse..."></textarea>
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Pièces jointes (optionnel)</label>
                                    <input type="file" name="attachments[]" multiple
                                           class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 transition-colors file:mr-4 file:rounded-lg file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-primary-700 hover:file:bg-primary-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                                           accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt">
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Formats acceptés: JPG, PNG, PDF, DOC, TXT (5MB max par fichier)</p>
                                </div>

                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" id="changeStatus" class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500 dark:border-slate-600">
                                        <span class="text-sm text-slate-700 dark:text-slate-300">Marquer comme "En attente utilisateur" après envoi</span>
                                    </label>

                                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700">
                                        <i class="fas fa-paper-plane"></i>Envoyer
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="border-t border-slate-100 bg-slate-50 px-6 py-8 text-center dark:border-slate-700 dark:bg-slate-700/50">
                        <i class="fas fa-lock text-3xl text-slate-400 dark:text-slate-500 mb-2"></i>
                        <p class="mb-3 text-slate-500 dark:text-slate-400">Cette conversation est fermée</p>
                        <button onclick="reopenChat({{ $supportChat->id }})"
                                class="text-sm font-medium text-primary-600 transition-colors hover:text-primary-700 dark:text-primary-400">
                            Rouvrir la conversation
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar avec actions -->
        <div class="lg:col-span-1">
            <div class="flex flex-col gap-6">
                <!-- Actions rapides -->
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <div class="p-5">
                        <h3 class="mb-4 flex items-center gap-2 text-base font-semibold text-slate-900 dark:text-white">
                            <i class="fas fa-bolt text-amber-500"></i>
                            Actions rapides
                        </h3>
                        <div class="flex flex-col gap-3">
                            @if($supportChat->status !== 'closed')
                                <button onclick="closeChat({{ $supportChat->id }})"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-red-700">
                                    <i class="fas fa-times-circle"></i>Fermer la conversation
                                </button>

                                @if(!$supportChat->admin_id || $supportChat->admin_id !== auth()->id())
                                    <button onclick="assignToMe({{ $supportChat->id }})"
                                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-emerald-700">
                                        <i class="fas fa-user-check"></i>M'assigner
                                    </button>
                                @endif

                                <button onclick="showAssignModal()"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700">
                                    <i class="fas fa-user-plus"></i>Assigner à un autre admin
                                </button>
                            @else
                                <button onclick="reopenChat({{ $supportChat->id }})"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-emerald-700">
                                    <i class="fas fa-undo"></i>Rouvrir
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Informations -->
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <div class="p-5">
                        <h3 class="mb-4 flex items-center gap-2 text-base font-semibold text-slate-900 dark:text-white">
                            <i class="fas fa-circle-info text-sky-500"></i>
                            Informations
                        </h3>
                        <div class="flex flex-col gap-4 text-sm">
                            <div>
                                <span class="mb-1 block font-medium text-slate-900 dark:text-white">Créé le:</span>
                                <p class="text-slate-500 dark:text-slate-400">{{ $supportChat->created_at->format('d/m/Y à H:i') }}</p>
                            </div>

                            @if($supportChat->closed_at)
                                <div>
                                    <span class="mb-1 block font-medium text-slate-900 dark:text-white">Fermé le:</span>
                                    <p class="text-slate-500 dark:text-slate-400">{{ $supportChat->closed_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            @endif

                            <div>
                                <span class="mb-1 block font-medium text-slate-900 dark:text-white">Dernière activité:</span>
                                <p class="text-slate-500 dark:text-slate-400">{{ $supportChat->last_message_at ? $supportChat->last_message_at->diffForHumans() : 'Aucune' }}</p>
                            </div>

                            <div>
                                <span class="mb-1 block font-medium text-slate-900 dark:text-white">Nombre de messages:</span>
                                <p class="text-slate-500 dark:text-slate-400">{{ $supportChat->messages->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Faire défiler vers le bas des messages au chargement
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('messagesContainer');
    container.scrollTop = container.scrollHeight;
});

// Gestion des changements de statut
document.getElementById('statusSelect').addEventListener('change', function() {
    const chatId = this.dataset.chatId;
    const status = this.value;

    fetch(`/admin/support/${chatId}/status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recharger la page si on ferme la conversation
            if (status === 'closed') {
                location.reload();
            }
        } else {
            alert('Erreur: ' + data.message);
            // Remettre l'ancienne valeur
            location.reload();
        }
    });
});

// Gestion des changements de priorité
document.getElementById('prioritySelect').addEventListener('change', function() {
    const chatId = this.dataset.chatId;
    const priority = this.value;

    fetch(`/admin/support/${chatId}/priority`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ priority: priority })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert('Erreur: ' + data.message);
            location.reload();
        }
    });
});

function closeChat(chatId) {
    if (confirm('Êtes-vous sûr de vouloir fermer cette conversation ?')) {
        fetch(`/admin/support/${chatId}/close`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        });
    }
}

function reopenChat(chatId) {
    if (confirm('Êtes-vous sûr de vouloir rouvrir cette conversation ?')) {
        fetch(`/admin/support/${chatId}/reopen`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        });
    }
}

function assignToMe(chatId) {
    fetch(`/admin/support/${chatId}/assign`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ admin_id: {{ auth()->id() }} })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    });
}
</script>
@endpush