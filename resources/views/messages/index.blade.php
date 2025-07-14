@extends('app')

@section('content')
<div class="container-fluid h-100">
    <div class="row h-100">
        <!-- Sidebar des conversations -->
        <div class="col-md-4 col-lg-3 p-0 border-end bg-white shadow-sm" id="conversations-sidebar" style="min-height: 100vh;">
            <div class="d-flex flex-column h-100">
                <!-- Header de la sidebar -->
                <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-comments me-2"></i>Messages
                    </h5>
                    <button class="btn btn-outline-primary btn-sm rounded-circle" data-bs-toggle="modal" data-bs-target="#newMessageModal">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <!-- Liste des conversations -->
                <div class="flex-grow-1 overflow-auto custom-scrollbar">
                    @forelse($conversations as $conversation)
                        <div class="conversation-item d-flex align-items-center gap-3 p-3 border-bottom position-relative bg-white conversation-hover"
                             data-conversation-id="{{ $conversation->id }}"
                             onclick="loadConversation('{{ $conversation->id }}')">
                            <div class="avatar flex-shrink-0 rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow" style="width: 48px; height: 48px; font-size: 1.3rem;">
                                @if($conversation->other_user && $conversation->other_user->name)
                                    {{ strtoupper(substr($conversation->other_user->name,0,1)) }}
                                @else
                                    <i class="fas fa-user"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold text-dark">{{ $conversation->other_user->name }}</span>
                                    <small class="text-muted">{{ $conversation->last_message_time }}</small>
                                </div>
                                <div class="text-muted small text-truncate" style="max-width: 180px;">
                                    @if($conversation->last_message)
                                        {{ Str::limit($conversation->last_message->content, 50) }}
                                    @else
                                        Aucun message
                                    @endif
                                </div>
                            </div>
                            @if($conversation->unread_count > 0)
                                <span class="badge bg-danger rounded-pill position-absolute top-0 end-0 translate-middle-y shadow">{{ $conversation->unread_count }}</span>
                            @endif
                        </div>
                    @empty
                        <div class="text-center p-4">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucune conversation</p>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newMessageModal">
                                Commencer une conversation
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        <!-- Zone principale du chat -->
        <div class="col-md-8 col-lg-9 p-0 d-flex flex-column bg-light position-relative" id="chat-container" style="min-height: 100vh;">
            <!-- Header du chat -->
            <div class="chat-header p-3 border-bottom bg-white d-flex align-items-center gap-3 shadow-sm sticky-top" id="chat-header" style="display: none; z-index: 10;">
                <!-- Bouton retour (visible uniquement sur mobile) -->
                <button class="btn btn-link text-muted d-md-none me-2" id="back-button" onclick="showConversationsList()">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <div class="avatar rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow" style="width: 40px; height: 40px; font-size: 1.1rem;">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold" id="chat-user-name"></h6>
                    <small class="text-muted" id="chat-user-status">En ligne</small>
                </div>
                <div class="ms-auto dropdown">
                    <button class="btn btn-link text-muted" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-search me-2"></i>Rechercher</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-phone me-2"></i>Appeler</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash me-2"></i>Supprimer</a></li>
                    </ul>
                </div>
            </div>
            <!-- Zone des messages -->
            <div class="chat-messages flex-grow-1 p-4 overflow-auto custom-scrollbar" id="chat-messages" style="background: linear-gradient(135deg, #f8fafc 0%, #e3eafc 100%); min-height: 0;">
                <div class="text-center p-4">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Sélectionnez une conversation pour commencer</p>
                </div>
            </div>
            <!-- Zone de saisie -->
            <div class="chat-input p-3 border-top bg-white shadow-lg position-sticky bottom-0 w-100" id="chat-input" style="display: none; z-index: 10;">
                <form id="message-form" enctype="multipart/form-data">
                    <div class="input-group rounded-pill shadow-sm bg-light">
                        <!-- Bouton d'import de fichier -->
                        <button class="btn btn-light rounded-start-pill px-2" type="button" id="file-upload-btn">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <input type="file" id="file-input" name="attachment" style="display: none;" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar">
                        <input type="text" class="form-control border-0 bg-light px-3" id="message-input" placeholder="Tapez votre message..." autocomplete="off" style="height: 48px;">
                        <button class="btn btn-primary rounded-end-pill px-4" type="submit">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    <div id="file-name-preview" class="small text-muted mt-1" style="display:none;"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour nouveau message -->
<div class="modal fade" id="newMessageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouveau message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('messages.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="recipient_id" class="form-label">Destinataire</label>
                        <select class="form-select" name="recipient_id" id="recipient_id" required>
                            <option value="">Sélectionnez un utilisateur</option>
                            @foreach($users as $user)
                                @if($user->id !== auth()->id())
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Message</label>
                        <textarea class="form-control" name="content" id="content" rows="3" placeholder="Tapez votre message..." required></textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Envoyer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #b3b3b3 #f5f5f5;
}
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
    background: #f5f5f5;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #b3b3b3;
    border-radius: 4px;
}
.conversation-item {
    cursor: pointer;
    transition: background 0.2s, box-shadow 0.2s;
    border-radius: 0 !important;
}
.conversation-hover:hover, .conversation-item.active {
    background: #f1f7ff !important;
    box-shadow: 0 2px 8px rgba(0,123,255,0.05);
}
.badge.bg-danger {
    font-size: 0.8rem;
    padding: 0.4em 0.7em;
    right: 12px;
    top: 12px;
}
.chat-messages {
    min-height: 300px;
    max-height: calc(100vh - 180px);
    overflow-y: auto;
    padding-bottom: 80px;
}
.message {
    margin-bottom: 1.2rem;
    max-width: 70%;
    display: flex;
    flex-direction: column;
    word-break: break-word;
}
.message.sent {
    margin-left: auto;
    align-items: flex-end;
}
.message.received {
    margin-right: auto;
    align-items: flex-start;
}
.message-content {
    padding: 0.9rem 1.2rem;
    border-radius: 1.5rem;
    word-wrap: break-word;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    font-size: 1rem;
    line-height: 1.5;
}
.message.sent .message-content {
    background: linear-gradient(135deg, #007bff 60%, #339af0 100%);
    color: white;
}
.message.received .message-content {
    background: #fff;
    border: 1px solid #e3eafc;
    color: #222;
}
.message-time {
    font-size: 0.75rem;
    color: #6c757d;
    margin-top: 0.25rem;
    text-align: right;
}

/* Styles pour le responsive comme WhatsApp */
@media (max-width: 991px) {
    #conversations-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        z-index: 1000;
        transition: transform 0.3s ease;
    }
    
    #chat-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        z-index: 1001;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    }
    
    #chat-container.active {
        transform: translateX(0);
    }
    
    #conversations-sidebar.hidden {
        transform: translateX(-100%);
    }
    
    .chat-header, .chat-input {
        position: static !important;
    }
    
    .chat-messages {
        max-height: calc(100vh - 140px);
        padding-bottom: 20px;
    }
}

@media (max-width: 767px) {
    .chat-messages {
        padding: 1rem 0.5rem;
    }
    
    .message {
        max-width: 85%;
    }
    
    .conversation-item {
        padding: 0.75rem !important;
    }
    
    .avatar {
        width: 40px !important;
        height: 40px !important;
        font-size: 1.1rem !important;
    }
    
    .chat-header {
        padding: 0.75rem !important;
    }
    
    .chat-input {
        padding: 0.75rem !important;
    }
    
    .input-group {
        height: 44px;
    }
    
    .form-control {
        font-size: 16px;
    }
}

@media (max-width: 576px) {
    .chat-messages {
        padding: 0.5rem 0.25rem;
    }
    
    .message {
        max-width: 90%;
        margin-bottom: 1rem;
    }
    
    .message-content {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }
    
    .conversation-item {
        padding: 0.5rem !important;
        gap: 0.5rem !important;
    }
    
    .avatar {
        width: 36px !important;
        height: 36px !important;
        font-size: 1rem !important;
    }
    
    .chat-header {
        padding: 0.5rem !important;
    }
    
    .chat-input {
        padding: 0.5rem !important;
    }
    
    .input-group {
        height: 40px;
    }
    
    .btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.9rem;
    }
}

@media (max-width: 480px) {
    .message {
        max-width: 95%;
    }
    
    .message-content {
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
    }
    
    .conversation-item {
        padding: 0.375rem !important;
    }
    
    .avatar {
        width: 32px !important;
        height: 32px !important;
        font-size: 0.9rem !important;
    }
    
    .chat-header {
        padding: 0.375rem !important;
    }
    
    .chat-input {
        padding: 0.375rem !important;
    }
    
    .input-group {
        height: 36px;
    }
    
    .form-control {
        font-size: 16px;
        padding: 0.375rem 0.5rem;
    }
    
    .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }
}

/* Styles pour les écrans tactiles */
@media (hover: none) and (pointer: coarse) {
    .conversation-item {
        min-height: 44px;
    }
    
    .btn {
        min-height: 44px;
    }
    
    .form-control {
        min-height: 44px;
    }
    
    .dropdown-item {
        min-height: 44px;
        display: flex;
        align-items: center;
    }
}
.seen-checks i {
    font-size: 1.1em;
    vertical-align: middle;
    margin-left: 2px;
}
</style>

<script>
let currentConversationId = null;
let isMobile = window.innerWidth <= 991;
const currentUserId = {{ auth()->id() }};
let isOtherUserOnline = false;

function loadConversation(conversationId) {
    currentConversationId = conversationId;
    
    // Afficher la zone de chat
    document.getElementById('chat-header').style.display = 'flex';
    document.getElementById('chat-input').style.display = 'block';
    
    // Sur mobile/tablette, basculer vers la vue chat
    if (isMobile) {
        document.getElementById('conversations-sidebar').classList.add('hidden');
        document.getElementById('chat-container').classList.add('active');
    }
    
    // Charger les messages via AJAX
    fetch(`/messages/${conversationId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('chat-user-name').textContent = data.user.name;
            isOtherUserOnline = data.is_online;
            displayMessages(data.messages);
        })
        .catch(error => {
            console.error('Erreur lors du chargement des messages:', error);
        });
}

function showConversationsList() {
    if (isMobile) {
        document.getElementById('conversations-sidebar').classList.remove('hidden');
        document.getElementById('chat-container').classList.remove('active');
        
        // Masquer la zone de chat
        document.getElementById('chat-header').style.display = 'none';
        document.getElementById('chat-input').style.display = 'none';
        
        currentConversationId = null;
    }
}

function displayMessages(messages) {
    const chatMessages = document.getElementById('chat-messages');
    chatMessages.innerHTML = '';
    
    messages.forEach(message => {
        const messageDiv = document.createElement('div');
        const isSent = message.sender_id === currentUserId;
        messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;
        
        const messageTime = new Date(message.created_at).toLocaleTimeString([], {
            hour: '2-digit', 
            minute: '2-digit'
        });

        let attachmentHtml = '';
        if (message.attachment) {
            // On suppose que le backend retourne le chemin relatif (ex: messages/xxxx.jpg)
            const url = `/storage/${message.attachment}`;
            const ext = message.attachment.split('.').pop().toLowerCase();
            if (["jpg","jpeg","png","gif","webp","bmp"].includes(ext)) {
                attachmentHtml = `<div class='mt-2'><a href='${url}' target='_blank'><img src='${url}' alt='Image' style='max-width:180px;max-height:180px;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,0.08);'></a></div>`;
            } else {
                attachmentHtml = `<div class='mt-2'><a href='${url}' target='_blank' class='text-decoration-underline'><i class='fas fa-file-alt me-1'></i>Télécharger le fichier</a></div>`;
            }
        }

        // Mention vu/non vu pour les messages envoyés
        let seenHtml = '';
        if (isSent) {
            if (message.is_read) {
                seenHtml = `<span class="seen-checks ms-2" title="Vu"><i class="fas fa-check-double" style="color:#22c55e;"></i></span>`;
            } else if (isOtherUserOnline) {
                seenHtml = `<span class="seen-checks ms-2" title="Reçu"><i class="fas fa-check-double" style="color:#aaa;"></i></span>`;
            } else {
                seenHtml = `<span class="seen-checks ms-2" title="Envoyé"><i class="fas fa-check" style="color:#aaa;"></i></span>`;
            }
        }

        messageDiv.innerHTML = `
            <div class="message-content">${message.content ? message.content : ''}${attachmentHtml}</div>
            <div class="message-time">${messageTime}${seenHtml}</div>
        `;
        
        chatMessages.appendChild(messageDiv);
    });
    
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

document.getElementById('file-upload-btn').addEventListener('click', function() {
    document.getElementById('file-input').click();
});

document.getElementById('file-input').addEventListener('change', function() {
    const file = this.files[0];
    const preview = document.getElementById('file-name-preview');
    if (file) {
        preview.textContent = 'Fichier sélectionné : ' + file.name;
        preview.style.display = 'block';
    } else {
        preview.textContent = '';
        preview.style.display = 'none';
    }
});

document.getElementById('message-form').addEventListener('submit', function(e) {
    e.preventDefault();
    if (!currentConversationId) return;

    const input = document.getElementById('message-input');
    const fileInput = document.getElementById('file-input');
    const content = input.value.trim();
    const file = fileInput.files[0];
    if (!content && !file) return;

    const formData = new FormData();
    formData.append('recipient_id', currentConversationId);
    formData.append('content', content);
    if (file) {
        formData.append('attachment', file);
    }

    fetch(`/messages`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        input.value = '';
        fileInput.value = '';
        document.getElementById('file-name-preview').style.display = 'none';
        if (data.success) {
            loadConversation(currentConversationId);
        }
    })
    .catch(error => {
        input.value = '';
        fileInput.value = '';
        document.getElementById('file-name-preview').style.display = 'none';
        console.error('Erreur lors de l\'envoi du message:', error);
    });
});

// Gestion du redimensionnement de la fenêtre
window.addEventListener('resize', function() {
    isMobile = window.innerWidth <= 991;
});

// Actualisation automatique des messages
setInterval(() => {
    if (currentConversationId) {
        loadConversation(currentConversationId);
    }
}, 5000);
</script>
@endsection 