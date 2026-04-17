@extends('layouts.support')

@section('title', 'Conversation Support')

@section('content')
<div>
    <!-- En-tête de la conversation -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm mb-6">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.support.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                            <i class="fas fa-arrow-left text-xl"></i>
                        </a>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $supportChat->reference }}</h1>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $supportChat->subject ?: 'Demande d\'assistance' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center gap-4">
                    <!-- Statut -->
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Statut:</label>
                        <select id="statusSelect" data-chat-id="{{ $supportChat->id }}" 
                                class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="open" {{ $supportChat->status === 'open' ? 'selected' : '' }}>Ouvert</option>
                            <option value="in_progress" {{ $supportChat->status === 'in_progress' ? 'selected' : '' }}>En cours</option>
                            <option value="waiting_user" {{ $supportChat->status === 'waiting_user' ? 'selected' : '' }}>En attente utilisateur</option>
                            <option value="closed" {{ $supportChat->status === 'closed' ? 'selected' : '' }}>Fermé</option>
                        </select>
                    </div>
                    
                    <!-- Priorité -->
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Priorité:</label>
                        <select id="prioritySelect" data-chat-id="{{ $supportChat->id }}" 
                                class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="low" {{ $supportChat->priority === 'low' ? 'selected' : '' }}>Faible</option>
                            <option value="normal" {{ $supportChat->priority === 'normal' ? 'selected' : '' }}>Normale</option>
                            <option value="high" {{ $supportChat->priority === 'high' ? 'selected' : '' }}>Élevée</option>
                            <option value="urgent" {{ $supportChat->priority === 'urgent' ? 'selected' : '' }}>Urgente</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Informations sur l'utilisateur et l'assignation -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center gap-3">
                    @if($supportChat->user?->avatar)
                        <img class="w-10 h-10 rounded-full object-cover" 
                             src="{{ $supportChat->user->avatar_url }}" 
                             alt="">
                    @else
                        <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                            <i class="fas fa-user text-gray-500 dark:text-gray-400"></i>
                        </div>
                    @endif
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $supportChat->user?->name ?? 'Utilisateur supprimé' }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $supportChat->user?->email ?? '-' }}</p>
                    </div>
                </div>
                
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Catégorie</p>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $supportChat->formatted_category }}</p>
                </div>
                
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Assigné à</p>
                    @if($supportChat->admin)
                        <div class="flex items-center gap-2">
                            @if($supportChat->admin->avatar)
                                <img class="w-6 h-6 rounded-full object-cover" 
                                     src="{{ $supportChat->admin->avatar_url }}" 
                                     alt="">
                            @else
                                <div class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600 dark:text-blue-400 text-xs"></i>
                                </div>
                            @endif
                            <span class="text-sm text-gray-900 dark:text-white">{{ $supportChat->admin->name }}</span>
                        </div>
                    @else
                        <p class="text-sm text-gray-400 dark:text-gray-500 italic">Non assigné</p>
                    @endif
                </div>
            </div>
            
            <!-- Métadonnées si disponibles -->
            @if($supportChat->metadata)
                <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Informations système:</p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm text-gray-600 dark:text-gray-400">
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Messages -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <!-- En-tête des messages -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Conversation</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $supportChat->messages->count() }} message(s)</p>
                </div>
                
                <!-- Liste des messages -->
                <div class="p-6">
                    <div class="flex flex-col gap-4 max-h-[500px] overflow-y-auto" id="messagesContainer">
                        @forelse($supportChat->messages as $message)
                            <div class="flex {{ $message->is_admin ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[70%]">
                                    <div class="flex items-start gap-3 {{ $message->is_admin ? 'flex-row-reverse' : '' }}">
                                        <!-- Avatar -->
                                        <div class="flex-shrink-0">
                                            @if($message->user?->avatar)
                                                <img class="w-8 h-8 rounded-full object-cover" 
                                                     src="{{ $message->user->avatar_url }}" 
                                                     alt="">
                                            @else
                                                <div class="w-8 h-8 rounded-full {{ $message->is_admin ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-gray-200 dark:bg-gray-600' }} flex items-center justify-center">
                                                    <i class="fas fa-user {{ $message->is_admin ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }} text-xs"></i>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Message -->
                                        <div class="flex flex-col {{ $message->is_admin ? 'items-end' : 'items-start' }}">
                                            <div class="px-4 py-3 rounded-2xl {{ $message->is_admin ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' }}">
                                                <p class="text-sm whitespace-pre-wrap">{{ $message->message }}</p>
                                                
                                                <!-- Pièces jointes -->
                                                @if($message->hasAttachments())
                                                    <div class="mt-2 flex flex-col gap-1">
                                                        @foreach($message->attachments as $attachment)
                                                            <a href="{{ asset('storage/' . $attachment['path']) }}" 
                                                               target="_blank"
                                                               class="text-sm {{ $message->is_admin ? 'text-blue-100 hover:text-white' : 'text-blue-600 dark:text-blue-400 hover:underline' }}">
                                                                <i class="fas fa-paperclip mr-1"></i>{{ $attachment['name'] }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Métadonnées du message -->
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $message->sender_name }}</span>
                                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $message->created_at->format('d/m/Y H:i') }}</span>
                                                @if($message->is_read)
                                                    <i class="fas fa-check-double text-xs text-green-500" title="Lu"></i>
                                                @else
                                                    <i class="fas fa-check text-xs text-gray-400" title="Envoyé"></i>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <i class="fas fa-comments text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                <p class="text-gray-500 dark:text-gray-400">Aucun message dans cette conversation</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                <!-- Formulaire de réponse -->
                @if($supportChat->status !== 'closed')
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                        <form action="{{ route('admin.support.reply', $supportChat) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Votre réponse</label>
                                    <textarea name="message" rows="4" required
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                            placeholder="Tapez votre réponse..."></textarea>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pièces jointes (optionnel)</label>
                                    <input type="file" name="attachments[]" multiple 
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                           accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Formats acceptés: JPG, PNG, PDF, DOC, TXT (5MB max par fichier)</p>
                                </div>
                                
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" id="changeStatus" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">Marquer comme "En attente utilisateur" après envoi</span>
                                    </label>
                                    
                                    <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                        <i class="fas fa-paper-plane mr-2"></i>Envoyer
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="px-6 py-8 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 text-center">
                        <i class="fas fa-lock text-3xl text-gray-400 dark:text-gray-500 mb-2"></i>
                        <p class="text-gray-500 dark:text-gray-400 mb-3">Cette conversation est fermée</p>
                        <button onclick="reopenChat({{ $supportChat->id }})" 
                                class="text-blue-600 hover:text-blue-700 dark:text-blue-400 text-sm font-medium">
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
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="p-5">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Actions rapides</h3>
                        <div class="flex flex-col gap-3">
                            @if($supportChat->status !== 'closed')
                                <button onclick="closeChat({{ $supportChat->id }})" 
                                        class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                                    <i class="fas fa-times-circle mr-2"></i>Fermer la conversation
                                </button>
                                
                                @if(!$supportChat->admin_id || $supportChat->admin_id !== auth()->id())
                                    <button onclick="assignToMe({{ $supportChat->id }})" 
                                            class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                                        <i class="fas fa-user-check mr-2"></i>M'assigner
                                    </button>
                                @endif
                                
                                <button onclick="showAssignModal()" 
                                        class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                    <i class="fas fa-user-plus mr-2"></i>Assigner à un autre admin
                                </button>
                            @else
                                <button onclick="reopenChat({{ $supportChat->id }})" 
                                        class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                                    <i class="fas fa-undo mr-2"></i>Rouvrir
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Informations -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="p-5">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Informations</h3>
                        <div class="flex flex-col gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-900 dark:text-white block mb-1">Créé le:</span>
                                <p class="text-gray-500 dark:text-gray-400">{{ $supportChat->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                            
                            @if($supportChat->closed_at)
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white block mb-1">Fermé le:</span>
                                    <p class="text-gray-500 dark:text-gray-400">{{ $supportChat->closed_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            @endif
                            
                            <div>
                                <span class="font-medium text-gray-900 dark:text-white block mb-1">Dernière activité:</span>
                                <p class="text-gray-500 dark:text-gray-400">{{ $supportChat->last_message_at ? $supportChat->last_message_at->diffForHumans() : 'Aucune' }}</p>
                            </div>
                            
                            <div>
                                <span class="font-medium text-gray-900 dark:text-white block mb-1">Nombre de messages:</span>
                                <p class="text-gray-500 dark:text-gray-400">{{ $supportChat->messages->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
@endsection