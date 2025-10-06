@extends('app')

@section('title', 'Conversation - ' . $supportChat->reference)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- En-tête -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <a href="{{ route('support.index') }}" class="btn btn-outline-secondary me-3">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="h4 mb-0">{{ $supportChat->reference }}</h1>
                        <p class="text-muted mb-0 small">
                            {{ $supportChat->subject ?: 'Assistance' }} • 
                            <span class="badge 
                                {{ $supportChat->status === 'open' ? 'bg-danger' : '' }}
                                {{ $supportChat->status === 'in_progress' ? 'bg-warning text-dark' : '' }}
                                {{ $supportChat->status === 'waiting_user' ? 'bg-info' : '' }}
                                {{ $supportChat->status === 'waiting_admin' ? 'bg-secondary' : '' }}
                                {{ $supportChat->status === 'closed' ? 'bg-dark' : '' }}">
                                {{ $supportChat->formatted_status }}
                            </span>
                        </p>
                    </div>
                </div>

                @if($supportChat->status !== 'closed')
                    <button type="button" 
                            class="btn btn-sm btn-outline-danger" 
                            data-bs-toggle="modal" 
                            data-bs-target="#closeModal">
                        <i class="fas fa-times me-1"></i>
                        Fermer
                    </button>
                @endif
            </div>

            <!-- Messages flash -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Informations de la conversation -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Catégorie</small>
                            <strong>{{ ucfirst($supportChat->category) }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Priorité</small>
                            <span class="badge 
                                {{ $supportChat->priority === 'urgent' ? 'bg-danger' : '' }}
                                {{ $supportChat->priority === 'high' ? 'bg-warning text-dark' : '' }}
                                {{ $supportChat->priority === 'normal' ? 'bg-info' : '' }}
                                {{ $supportChat->priority === 'low' ? 'bg-secondary' : '' }}">
                                {{ ucfirst($supportChat->priority) }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Créée le</small>
                            <strong>{{ $supportChat->created_at->format('d/m/Y à H:i') }}</strong>
                        </div>
                        @if($supportChat->admin)
                            <div class="col-md-6">
                                <small class="text-muted d-block">Assigné à</small>
                                <strong>{{ $supportChat->admin->name }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-comments me-2"></i>
                        Conversation
                    </h5>
                </div>
                <div class="card-body p-0" id="messagesContainer" style="max-height: 600px; overflow-y: auto;">
                    @forelse($supportChat->messages as $message)
                        <div class="p-3 border-bottom {{ $message->is_admin ? 'bg-light' : '' }}">
                            <!-- En-tête du message -->
                            <div class="d-flex align-items-start mb-2">
                                <div class="rounded-circle {{ $message->is_admin ? 'bg-primary' : 'bg-secondary' }} text-white d-flex align-items-center justify-content-center me-3" 
                                     style="width: 40px; height: 40px; font-weight: bold;">
                                    {{ strtoupper(substr($message->user->name, 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>{{ $message->user->name }}</strong>
                                            @if($message->is_admin)
                                                <span class="badge bg-primary ms-2">Support</span>
                                            @endif
                                            <br>
                                            <small class="text-muted">{{ $message->created_at->format('d/m/Y à H:i') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contenu du message -->
                            <div class="ms-5">
                                <p class="mb-0" style="white-space: pre-wrap;">{{ $message->message }}</p>

                                <!-- Pièces jointes -->
                                @if($message->attachments && is_array($message->attachments))
                                    <div class="mt-3">
                                        <strong class="d-block mb-2">
                                            <i class="fas fa-paperclip me-1"></i>
                                            Pièces jointes:
                                        </strong>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($message->attachments as $attachment)
                                                <a href="{{ Storage::url($attachment['path']) }}" 
                                                   target="_blank"
                                                   class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-file me-1"></i>
                                                    {{ $attachment['name'] }}
                                                    <small class="text-muted">({{ number_format($attachment['size'] / 1024, 2) }} KB)</small>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>Aucun message dans cette conversation</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Formulaire de réponse -->
            @if($supportChat->status !== 'closed')
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-reply me-2"></i>
                            Répondre
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('support.reply', $supportChat) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Message -->
                            <div class="mb-3">
                                <label for="message" class="form-label fw-semibold">
                                    Votre réponse <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('message') is-invalid @enderror" 
                                          id="message" 
                                          name="message" 
                                          rows="6" 
                                          maxlength="5000"
                                          required
                                          placeholder="Écrivez votre réponse...">{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="d-flex justify-content-end mt-1">
                                    <small class="text-muted">
                                        <span id="replyCharCount">0</span>/5000
                                    </small>
                                </div>
                            </div>

                            <!-- Pièces jointes -->
                            <div class="mb-3">
                                <label for="attachments" class="form-label fw-semibold">
                                    Pièces jointes (optionnel)
                                </label>
                                <input type="file" 
                                       class="form-control @error('attachments.*') is-invalid @enderror" 
                                       id="attachments" 
                                       name="attachments[]" 
                                       multiple
                                       accept="image/*,.pdf,.doc,.docx,.txt">
                                @error('attachments.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Maximum 5 MB par fichier
                                </small>
                            </div>

                            <!-- Boutons -->
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Envoyer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-lock me-2"></i>
                    Cette conversation est fermée. Vous ne pouvez plus y répondre.
                    <a href="{{ route('support.create') }}" class="alert-link">Créer une nouvelle demande</a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de fermeture -->
<div class="modal fade" id="closeModal" tabindex="-1" aria-labelledby="closeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="closeModalLabel">Fermer la conversation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir fermer cette conversation ?</p>
                <p class="text-muted mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    Une fois fermée, vous ne pourrez plus ajouter de messages.
                    Vous pourrez toujours créer une nouvelle demande si nécessaire.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('support.close', $supportChat) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-1"></i>
                        Fermer la conversation
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Faire défiler jusqu'au dernier message
    const messagesContainer = document.getElementById('messagesContainer');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    // Compteur de caractères
    const messageTextarea = document.getElementById('message');
    const charCount = document.getElementById('replyCharCount');
    
    if (messageTextarea && charCount) {
        messageTextarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;
            
            if (length > 4500) {
                charCount.classList.add('text-danger', 'fw-bold');
            } else {
                charCount.classList.remove('text-danger', 'fw-bold');
            }
        });
    }
    
    // Auto-scroll lors de l'ajout de nouveaux messages
    const observer = new MutationObserver(function() {
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    });
    
    if (messagesContainer) {
        observer.observe(messagesContainer, {
            childList: true,
            subtree: true
        });
    }
    
    // Validation du formulaire
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Envoi...';
            }
        });
    }
});
</script>
@endpush
@endsection
