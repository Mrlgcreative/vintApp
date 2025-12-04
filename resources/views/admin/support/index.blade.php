@extends('app')

@section('content')
<div class="container py-4">
    <!-- En-tête avec statistiques -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="display-5 fw-bold text-dark">Support Client</h1>
                <p class="text-muted">Gérez les demandes d'assistance des utilisateurs</p>
            </div>
            <div class="d-flex gap-2">
                <button onclick="refreshStats()" class="btn btn-primary">
                    <i class="fas fa-sync-alt me-2"></i>Actualiser
                </button>
                <a href="{{ route('admin.support.stats') }}" class="btn btn-success">
                    <i class="fas fa-chart-bar me-2"></i>Statistiques
                </a>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6 col-lg-2">
                <div class="card h-100 border-start border-primary border-4">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="small text-muted mb-1">Total</p>
                                <h3 class="mb-0 fw-bold">{{ $stats['total'] }}</h3>
                            </div>
                            <div class="rounded-circle bg-primary bg-opacity-10 p-2">
                                <i class="fas fa-comments text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-2">
                <div class="card h-100 border-start border-danger border-4">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="small text-muted mb-1">Ouvert</p>
                                <h3 class="mb-0 fw-bold text-danger">{{ $stats['open'] }}</h3>
                            </div>
                            <div class="rounded-circle bg-danger bg-opacity-10 p-2">
                                <i class="fas fa-exclamation-circle text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-2">
                <div class="card h-100 border-start border-warning border-4">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="small text-muted mb-1">En cours</p>
                                <h3 class="mb-0 fw-bold text-warning">{{ $stats['in_progress'] }}</h3>
                            </div>
                            <div class="rounded-circle bg-warning bg-opacity-10 p-2">
                                <i class="fas fa-clock text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-2">
                <div class="card h-100 border-start border-4" style="border-color: #8b5cf6;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="small text-muted mb-1">En attente</p>
                                <h3 class="mb-0 fw-bold" style="color: #8b5cf6;">{{ $stats['waiting_user'] }}</h3>
                            </div>
                            <div class="rounded-circle p-2" style="background-color: rgba(139, 92, 246, 0.1);">
                                <i class="fas fa-hourglass-half" style="color: #8b5cf6;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-2">
                <div class="card h-100 border-start border-success border-4">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="small text-muted mb-1">Fermés aujourd'hui</p>
                                <h3 class="mb-0 fw-bold text-success">{{ $stats['closed_today'] }}</h3>
                            </div>
                            <div class="rounded-circle bg-success bg-opacity-10 p-2">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-2">
                <div class="card h-100 border-start border-4" style="border-color: #f59e0b;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="small text-muted mb-1">Non assignés</p>
                                <h3 class="mb-0 fw-bold" style="color: #f59e0b;">{{ $stats['unassigned'] }}</h3>
                            </div>
                            <div class="rounded-circle p-2" style="background-color: rgba(245, 158, 11, 0.1);">
                                <i class="fas fa-user-times" style="color: #f59e0b;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.support.index') }}" class="row g-3">
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label small fw-medium">Recherche</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Référence, sujet..." 
                           class="form-control form-control-sm">
                </div>

                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label small fw-medium">Statut</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Tous les statuts</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Ouvert</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>En cours</option>
                        <option value="waiting_user" {{ request('status') === 'waiting_user' ? 'selected' : '' }}>En attente utilisateur</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Fermé</option>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label small fw-medium">Priorité</label>
                    <select name="priority" class="form-select form-select-sm">
                        <option value="">Toutes les priorités</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Faible</option>
                        <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normale</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Élevée</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label small fw-medium">Catégorie</label>
                    <select name="category" class="form-select form-select-sm">
                        <option value="">Toutes les catégories</option>
                        <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>Technique</option>
                        <option value="account" {{ request('category') === 'account' ? 'selected' : '' }}>Compte</option>
                        <option value="payment" {{ request('category') === 'payment' ? 'selected' : '' }}>Paiement</option>
                        <option value="order" {{ request('category') === 'order' ? 'selected' : '' }}>Commande</option>
                        <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>Général</option>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label small fw-medium">Assigné à</label>
                    <select name="assigned_to" class="form-select form-select-sm">
                        <option value="">Tous</option>
                        <option value="unassigned" {{ request('assigned_to') === 'unassigned' ? 'selected' : '' }}>Non assigné</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" {{ request('assigned_to') == $admin->id ? 'selected' : '' }}>
                                {{ $admin->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-12 col-lg-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                        <i class="fas fa-search me-1"></i>Filtrer
                    </button>
                    <a href="{{ route('admin.support.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des conversations -->
    <div class="card shadow">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-3 py-3 text-uppercase small">Référence</th>
                        <th class="px-3 py-3 text-uppercase small">Utilisateur</th>
                        <th class="px-3 py-3 text-uppercase small">Sujet</th>
                        <th class="px-3 py-3 text-uppercase small">Statut</th>
                        <th class="px-3 py-3 text-uppercase small">Priorité</th>
                        <th class="px-3 py-3 text-uppercase small">Assigné à</th>
                        <th class="px-3 py-3 text-uppercase small">Dernier message</th>
                        <th class="px-3 py-3 text-uppercase small text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($chats as $chat)
                        <tr>
                            <td class="px-3 py-3">
                                <div class="d-flex align-items-center">
                                    <span class="fw-medium">{{ $chat->reference }}</span>
                                    @if($chat->unread_count_for_admin > 0)
                                        <span class="ms-2 badge bg-danger rounded-pill small">
                                            {{ $chat->unread_count_for_admin }} nouveau(x)
                                        </span>
                                    @endif
                                </div>
                            </td>
                            
                            <td class="px-3 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        @if($chat->user?->avatar)
                                            <img class="rounded-circle" src="{{ $chat->user->avatar_url }}" 
                                                 alt="" style="width: 32px; height: 32px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center" 
                                                 style="width: 32px; height: 32px;">
                                                <i class="fas fa-user text-secondary small"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $chat->user?->name ?? 'Utilisateur supprimé' }}</div>
                                        <div class="small text-muted">{{ $chat->user?->email ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-3 py-3">
                                <div class="fw-medium">
                                    {{ $chat->subject ?: 'Demande d\'assistance' }}
                                </div>
                                <div class="small text-muted">{{ $chat->formatted_category }}</div>
                            </td>
                            
                            <td class="px-3 py-3">
                                <span class="badge
                                    {{ $chat->status === 'open' ? 'bg-danger' : '' }}
                                    {{ $chat->status === 'in_progress' ? 'bg-warning text-dark' : '' }}
                                    {{ $chat->status === 'waiting_user' ? 'text-dark' : '' }}
                                    {{ $chat->status === 'closed' ? 'bg-success' : '' }}"
                                    @if($chat->status === 'waiting_user') style="background-color: #8b5cf6; color: white;" @endif>
                                    {{ $chat->formatted_status }}
                                </span>
                            </td>
                            
                            <td class="px-3 py-3">
                                <span class="badge
                                    {{ $chat->priority === 'low' ? 'bg-secondary' : '' }}
                                    {{ $chat->priority === 'normal' ? 'bg-primary' : '' }}
                                    {{ $chat->priority === 'high' ? 'text-dark' : '' }}
                                    {{ $chat->priority === 'urgent' ? 'bg-danger' : '' }}"
                                    @if($chat->priority === 'high') style="background-color: #f59e0b; color: white;" @endif>
                                    {{ $chat->formatted_priority }}
                                </span>
                            </td>
                            
                            <td class="px-3 py-3">
                                @if($chat->admin)
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            @if($chat->admin->avatar)
                                                <img class="rounded-circle" src="{{ $chat->admin->avatar_url }}" 
                                                     alt="" style="width: 24px; height: 24px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-primary bg-opacity-25 d-flex align-items-center justify-content-center" 
                                                     style="width: 24px; height: 24px;">
                                                    <i class="fas fa-user text-primary" style="font-size: 0.65rem;"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <span class="small">{{ $chat->admin->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted fst-italic small">Non assigné</span>
                                @endif
                            </td>
                            
                            <td class="px-3 py-3 text-muted small">
                                @if($chat->last_message_at)
                                    {{ $chat->last_message_at->diffForHumans() }}
                                @else
                                    -
                                @endif
                            </td>
                            
                            <td class="px-3 py-3">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('admin.support.show', $chat) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($chat->status !== 'closed')
                                        <button onclick="assignChat({{ $chat->id }})" 
                                                class="btn btn-sm btn-outline-success" 
                                                title="Assigner">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                        <button onclick="closeChat({{ $chat->id }})" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Fermer">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    @else
                                        <button onclick="reopenChat({{ $chat->id }})" 
                                                class="btn btn-sm btn-outline-success" 
                                                title="Rouvrir">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-comments display-4 mb-3 d-block"></i>
                                    <p class="fs-5 fw-medium mb-1">Aucune conversation de support</p>
                                    <p class="small">Les demandes d'assistance apparaîtront ici.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($chats->hasPages())
            <div class="card-footer border-top">
                {{ $chats->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal d'assignation -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="assignForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignModalLabel">Assigner la conversation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <select id="adminSelect" class="form-select">
                        <option value="">Choisir un admin...</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Assigner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentChatId = null;
let assignModal = null;

document.addEventListener('DOMContentLoaded', function() {
    assignModal = new bootstrap.Modal(document.getElementById('assignModal'));
});

function assignChat(chatId) {
    currentChatId = chatId;
    assignModal.show();
}

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
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la fermeture de la conversation.');
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
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la réouverture de la conversation.');
        });
    }
}

function refreshStats() {
    location.reload();
}

// Gestion du formulaire d'assignation
document.getElementById('assignForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const adminId = document.getElementById('adminSelect').value;
    
    if (!adminId) {
        alert('Veuillez sélectionner un admin.');
        return;
    }
    
    fetch(`/admin/support/${currentChatId}/assign`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ admin_id: adminId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            assignModal.hide();
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors de l\'assignation.');
    });
});
</script>
@endsection