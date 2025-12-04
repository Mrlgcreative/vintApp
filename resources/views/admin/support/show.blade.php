@extends('app')

@section('content')
<div class="container py-4">
    <!-- En-tête de la conversation -->
    <div class="card shadow-sm mb-4">
        <div class="card-body border-bottom">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ route('admin.support.index') }}" class="text-secondary">
                            <i class="fas fa-arrow-left fs-5"></i>
                        </a>
                        <div>
                            <h1 class="h4 fw-bold mb-1">{{ $supportChat->reference }}</h1>
                            <p class="text-muted mb-0">{{ $supportChat->subject ?: 'Demande d\'assistance' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <!-- Statut -->
                    <div class="d-flex align-items-center gap-2">
                        <label class="small fw-medium mb-0">Statut:</label>
                        <select id="statusSelect" data-chat-id="{{ $supportChat->id }}" 
                                class="form-select form-select-sm">
                            <option value="open" {{ $supportChat->status === 'open' ? 'selected' : '' }}>Ouvert</option>
                            <option value="in_progress" {{ $supportChat->status === 'in_progress' ? 'selected' : '' }}>En cours</option>
                            <option value="waiting_user" {{ $supportChat->status === 'waiting_user' ? 'selected' : '' }}>En attente utilisateur</option>
                            <option value="closed" {{ $supportChat->status === 'closed' ? 'selected' : '' }}>Fermé</option>
                        </select>
                    </div>
                    
                    <!-- Priorité -->
                    <div class="d-flex align-items-center gap-2">
                        <label class="small fw-medium mb-0">Priorité:</label>
                        <select id="prioritySelect" data-chat-id="{{ $supportChat->id }}" 
                                class="form-select form-select-sm">
                            <option value="low" {{ $supportChat->priority === 'low' ? 'selected' : '' }}>Faible</option>
                            <option value="normal" {{ $supportChat->priority === 'normal' ? 'selected' : '' }}>Normale</option>
                            <option value="high" {{ $supportChat->priority === 'high' ? 'selected' : '' }}>Élevée</option>
                            <option value="urgent" {{ $supportChat->priority === 'urgent' ? 'selected' : '' }}>Urgente</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Informations sur l'utilisateur et l'assignation -->
            <div class="mt-4 row g-3">
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="flex-shrink-0">
                            @if($supportChat->user->avatar)
                                <img class="rounded-circle" 
                                     style="width: 40px; height: 40px; object-fit: cover;" 
                                     src="{{ $supportChat->user->avatar_url }}" 
                                     alt="">
                            @else
                                <div class="rounded-circle bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center"
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-user text-secondary"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <p class="small fw-medium mb-0">{{ $supportChat->user->name }}</p>
                            <p class="small text-muted mb-0">{{ $supportChat->user->email }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <p class="small fw-medium text-muted mb-1">Catégorie</p>
                    <p class="small mb-0">{{ $supportChat->formatted_category }}</p>
                </div>
                
                <div class="col-md-4">
                    <p class="small fw-medium text-muted mb-1">Assigné à</p>
                    @if($supportChat->admin)
                        <div class="d-flex align-items-center gap-2">
                            @if($supportChat->admin->avatar)
                                <img class="rounded-circle" 
                                     style="width: 40px; height: 40px; object-fit: cover;" 
                                     src="{{ $supportChat->admin->avatar_url }}" 
                                     alt="">
                            @else
                                <div class="rounded-circle bg-primary bg-opacity-25 d-flex align-items-center justify-content-center"
                                     style="width: 24px; height: 24px;">
                                    <i class="fas fa-user text-primary" style="font-size: 0.7rem;"></i>
                                </div>
                            @endif
                            <span class="small">{{ $supportChat->admin->name }}</span>
                        </div>
                    @else
                        <p class="small text-muted fst-italic mb-0">Non assigné</p>
                    @endif
                </div>
            </div>
            
            <!-- Métadonnées si disponibles -->
            @if($supportChat->metadata)
                <div class="mt-3 p-3 bg-light rounded">
                    <p class="small fw-medium mb-2">Informations système:</p>
                    <div class="row g-2 small text-muted">
                        @if(isset($supportChat->metadata['browser']))
                            <div class="col-6 col-md-3"><strong>Navigateur:</strong> {{ $supportChat->metadata['browser'] }}</div>
                        @endif
                        @if(isset($supportChat->metadata['os']))
                            <div class="col-6 col-md-3"><strong>OS:</strong> {{ $supportChat->metadata['os'] }}</div>
                        @endif
                        @if(isset($supportChat->metadata['ip']))
                            <div class="col-6 col-md-3"><strong>IP:</strong> {{ $supportChat->metadata['ip'] }}</div>
                        @endif
                        @if(isset($supportChat->metadata['url']))
                            <div class="col-6 col-md-3"><strong>Page:</strong> {{ Str::limit($supportChat->metadata['url'], 30) }}</div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <!-- Messages -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <!-- En-tête des messages -->
                <div class="card-header bg-light">
                    <h2 class="h6 mb-0">Conversation</h2>
                    <p class="small text-muted mb-0">{{ $supportChat->messages->count() }} message(s)</p>
                </div>
                
                <!-- Liste des messages -->
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3" style="max-height: 500px; overflow-y: auto;" id="messagesContainer">
                        @forelse($supportChat->messages as $message)
                            <div class="d-flex {{ $message->is_admin ? 'justify-content-end' : 'justify-content-start' }}">
                                <div style="max-width: 70%;">
                                    <div class="d-flex align-items-start gap-2 {{ $message->is_admin ? 'flex-row-reverse' : '' }}">
                                        <!-- Avatar -->
                                        <div class="flex-shrink-0">
                                            @if($message->user->avatar)
                                                <img class="rounded-circle" 
                                                     style="width: 30px; height: 30px; object-fit: cover;" 
                                                     src="{{ $message->user->avatar_url }}" 
                                                     alt="">
                                            @else
                                                <div class="rounded-circle {{ $message->is_admin ? 'bg-primary' : 'bg-secondary' }} bg-opacity-25 d-flex align-items-center justify-content-center"
                                                     style="width: 32px; height: 32px;">
                                                    <i class="fas fa-user {{ $message->is_admin ? 'text-primary' : 'text-secondary' }}" style="font-size: 0.75rem;"></i>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Message -->
                                        <div class="d-flex flex-column {{ $message->is_admin ? 'align-items-end' : 'align-items-start' }}">
                                            <div class="px-3 py-2 rounded {{ $message->is_admin ? 'bg-primary text-white' : 'bg-light' }}">
                                                <p class="small mb-0" style="white-space: pre-wrap;">{{ $message->message }}</p>
                                                
                                                <!-- Pièces jointes -->
                                                @if($message->hasAttachments())
                                                    <div class="mt-2 d-flex flex-column gap-1">
                                                        @foreach($message->attachments as $attachment)
                                                            <a href="{{ asset('storage/' . $attachment['path']) }}" 
                                                               target="_blank"
                                                               class="small {{ $message->is_admin ? 'text-white text-opacity-75' : 'text-primary' }} text-decoration-underline">
                                                                <i class="fas fa-paperclip me-1"></i>{{ $attachment['name'] }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Métadonnées du message -->
                                            <div class="d-flex align-items-center gap-2 mt-1">
                                                <span class="small text-muted">{{ $message->sender_name }}</span>
                                                <span class="small text-muted opacity-75">{{ $message->created_at->format('d/m/Y H:i') }}</span>
                                                @if($message->is_read)
                                                    <i class="fas fa-check-double small text-success" title="Lu"></i>
                                                @else
                                                    <i class="fas fa-check small text-muted" title="Envoyé"></i>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-comments display-4 text-muted opacity-25 mb-3"></i>
                                <p class="text-muted">Aucun message dans cette conversation</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                <!-- Formulaire de réponse -->
                @if($supportChat->status !== 'closed')
                    <div class="card-footer bg-light">
                        <form action="{{ route('admin.support.reply', $supportChat) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="d-flex flex-column gap-3">
                                <div>
                                    <label class="form-label small fw-medium">Votre réponse</label>
                                    <textarea name="message" rows="4" required
                                            class="form-control"
                                            placeholder="Tapez votre réponse..."></textarea>
                                </div>
                                
                                <div>
                                    <label class="form-label small fw-medium">Pièces jointes (optionnel)</label>
                                    <input type="file" name="attachments[]" multiple 
                                           class="form-control"
                                           accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt">
                                    <small class="form-text text-muted">Formats acceptés: JPG, PNG, PDF, DOC, TXT (5MB max par fichier)</small>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                    <div class="form-check">
                                        <input type="checkbox" id="changeStatus" class="form-check-input">
                                        <label for="changeStatus" class="form-check-label small">Marquer comme "En attente utilisateur" après envoi</label>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i>Envoyer
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="card-footer bg-light text-center">
                        <div class="py-3">
                            <i class="fas fa-lock text-muted fs-3 mb-2"></i>
                            <p class="text-muted mb-2">Cette conversation est fermée</p>
                            <button onclick="reopenChat({{ $supportChat->id }})" 
                                    class="btn btn-sm btn-link">
                                Rouvrir la conversation
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Sidebar avec actions -->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4">
                <!-- Actions rapides -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="h6 fw-semibold mb-3">Actions rapides</h3>
                        <div class="d-grid gap-2">
                            @if($supportChat->status !== 'closed')
                                <button onclick="closeChat({{ $supportChat->id }})" 
                                        class="btn btn-danger">
                                    <i class="fas fa-times-circle me-2"></i>Fermer la conversation
                                </button>
                                
                                @if(!$supportChat->admin_id || $supportChat->admin_id !== auth()->id())
                                    <button onclick="assignToMe({{ $supportChat->id }})" 
                                            class="btn btn-success">
                                        <i class="fas fa-user-check me-2"></i>M'assigner
                                    </button>
                                @endif
                                
                                <button onclick="showAssignModal()" 
                                        class="btn btn-primary">
                                    <i class="fas fa-user-plus me-2"></i>Assigner à un autre admin
                                </button>
                            @else
                                <button onclick="reopenChat({{ $supportChat->id }})" 
                                        class="btn btn-success">
                                    <i class="fas fa-undo me-2"></i>Rouvrir
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Informations -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="h6 fw-semibold mb-3">Informations</h3>
                        <div class="d-flex flex-column gap-3 small">
                            <div>
                                <span class="fw-medium d-block mb-1">Créé le:</span>
                                <p class="text-muted mb-0">{{ $supportChat->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                            
                            @if($supportChat->closed_at)
                                <div>
                                    <span class="fw-medium d-block mb-1">Fermé le:</span>
                                    <p class="text-muted mb-0">{{ $supportChat->closed_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            @endif
                            
                            <div>
                                <span class="fw-medium d-block mb-1">Dernière activité:</span>
                                <p class="text-muted mb-0">{{ $supportChat->last_message_at ? $supportChat->last_message_at->diffForHumans() : 'Aucune' }}</p>
                            </div>
                            
                            <div>
                                <span class="fw-medium d-block mb-1">Nombre de messages:</span>
                                <p class="text-muted mb-0">{{ $supportChat->messages->count() }}</p>
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