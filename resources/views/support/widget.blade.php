<!-- Widget d'assistance rapide -->
<div id="supportWidget" style="position: fixed; bottom: 20px; right: 20px; z-index: 1040;">
    <!-- Bouton flottant -->
    <button id="supportToggle" 
            class="btn btn-primary rounded-circle p-3 shadow-lg position-relative"
            style="transition: all 0.3s ease; border-radius: 50% !important;">
        <i class="fas fa-headset"></i>
        @if(Auth::check() && isset($openChats) && $openChats->count() > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ $openChats->count() }}
            </span>
        @endif
    </button>

    <!-- Popup du widget -->
    <div id="supportPopup" class="card shadow-lg border-0 d-none" 
         style="position: absolute; bottom: 70px; right: 0; width: 320px; max-height: 400px; transition: all 0.3s ease;">
        <!-- En-tête -->
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-semibold">Assistance</h5>
                    <small class="text-white-50">Nous sommes là pour vous aider</small>
                </div>
                <button id="supportClose" class="btn-close btn-close-white" aria-label="Close"></button>
            </div>
        </div>

        <!-- Contenu -->
        <div class="card-body p-3" style="max-height: 400px; overflow-y: auto;">
            @auth
                @if(isset($openChats) && $openChats->count() > 0)
                    <!-- Conversations existantes -->
                    <div class="mb-3">
                        <h6 class="fw-semibold text-dark mb-2">Vos conversations en cours</h6>
                        <div class="d-grid gap-2 mb-3" style="max-height: 150px; overflow-y: auto;">
                            @foreach($openChats as $chat)
                                <a href="{{ route('support.show', $chat) }}" 
                                   class="text-decoration-none p-2 bg-light rounded hover-shadow"
                                   style="transition: all 0.2s;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <p class="mb-0 fw-medium text-dark small">{{ $chat->reference }}</p>
                                            <p class="mb-0 text-muted" style="font-size: 0.75rem;">{{ $chat->subject ?: 'Assistance' }}</p>
                                        </div>
                                        <span class="badge 
                                            {{ $chat->status === 'open' ? 'bg-danger' : '' }}
                                            {{ $chat->status === 'in_progress' ? 'bg-warning text-dark' : '' }}
                                            {{ $chat->status === 'waiting_user' ? 'bg-info' : '' }}"
                                            style="font-size: 0.7rem;">
                                            {{ $chat->formatted_status }}
                                        </span>
                                    </div>
                                    @if($chat->unread_count_for_user > 0)
                                        <div class="mt-1">
                                            <span class="badge bg-primary" style="font-size: 0.7rem;">
                                                {{ $chat->unread_count_for_user }} nouveau(x) message(s)
                                            </span>
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                        <div class="border-top pt-2">
                            <a href="{{ route('support.index') }}" 
                               class="text-primary text-decoration-none small fw-medium">
                                <i class="fas fa-arrow-right me-1"></i>Voir toutes mes conversations
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Chat rapide -->
                <div class="mb-3">
                    <h6 class="fw-semibold text-dark mb-2">Besoin d'aide ?</h6>
                    <div id="quickChatSuccess" class="alert alert-success alert-dismissible fade show d-none" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <span id="successMessage"></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <form id="quickChatForm">
                        <textarea id="quickMessage" 
                                  placeholder="Décrivez votre problème..." 
                                  class="form-control form-control-sm mb-2"
                                  rows="3" 
                                  maxlength="1000"
                                  required></textarea>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <span id="charCount">0</span>/1000
                            </small>
                            <button type="submit" 
                                    class="btn btn-primary btn-sm">
                                <i class="fas fa-paper-plane me-1"></i>Envoyer
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Liens utiles -->
                <div class="border-top pt-2">
                    <div class="d-grid gap-1">
                        <a href="{{ route('support.create') }}" 
                           class="text-primary text-decoration-none small d-flex align-items-center">
                            <i class="fas fa-plus-circle me-2"></i>Nouvelle demande détaillée
                        </a>
                        <a href="{{ route('support.index') }}" 
                           class="text-primary text-decoration-none small d-flex align-items-center">
                            <i class="fas fa-history me-2"></i>Historique de mes demandes
                        </a>
                    </div>
                </div>
            @else
                <!-- Utilisateur non connecté -->
                <div class="text-center py-4">
                    <i class="fas fa-user-lock text-secondary mb-3" style="font-size: 3rem;"></i>
                    <p class="text-muted mb-3">Connectez-vous pour accéder au support</p>
                    <a href="{{ route('login') }}" 
                       class="btn btn-primary btn-sm">
                        <i class="fas fa-sign-in-alt me-1"></i>Se connecter
                    </a>
                </div>
            @endauth
        </div>
    </div>
</div>

<style>
#supportWidget {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

#supportToggle {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    font-size: 1.25rem;
    width: 56px;
    height: 56px;
}

#supportToggle:hover {
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    transform: scale(1.05);
}

#supportPopup {
    animation: slideUp 0.3s ease-out;
}

.hover-shadow:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    background-color: #f8f9fa !important;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(10px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Animation pour le bouton pulsant quand il y a des notifications */
#supportToggle.has-notification {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    50% {
        box-shadow: 0 4px 20px rgba(220, 38, 38, 0.4);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('supportToggle');
    const popup = document.getElementById('supportPopup');
    const close = document.getElementById('supportClose');
    const quickForm = document.getElementById('quickChatForm');
    const quickMessage = document.getElementById('quickMessage');
    const charCount = document.getElementById('charCount');
    const quickChatSuccess = document.getElementById('quickChatSuccess');
    const successMessage = document.getElementById('successMessage');

    // Vérifier si le bouton a des notifications
    const badge = toggle.querySelector('.badge');
    if (badge) {
        toggle.classList.add('has-notification');
    }

    // Toggle popup
    if (toggle) {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            popup.classList.toggle('d-none');
            
            // Focus sur le textarea si le popup s'ouvre
            if (!popup.classList.contains('d-none') && quickMessage) {
                setTimeout(() => quickMessage.focus(), 100);
            }
        });
    }

    // Fermer popup
    if (close) {
        close.addEventListener('click', function(e) {
            e.stopPropagation();
            popup.classList.add('d-none');
        });
    }

    // Fermer en cliquant en dehors
    document.addEventListener('click', function(e) {
        const widget = document.getElementById('supportWidget');
        if (widget && !widget.contains(e.target)) {
            popup.classList.add('d-none');
        }
    });

    // Empêcher la fermeture lors du clic dans le popup
    if (popup) {
        popup.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Compteur de caractères
    if (quickMessage && charCount) {
        quickMessage.addEventListener('input', function() {
            charCount.textContent = this.value.length;
            
            // Changer la couleur si proche de la limite
            if (this.value.length > 900) {
                charCount.classList.add('text-danger', 'fw-bold');
            } else {
                charCount.classList.remove('text-danger', 'fw-bold');
            }
        });
    }

    // Formulaire de chat rapide
    if (quickForm) {
        quickForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const message = quickMessage.value.trim();
            if (!message) {
                alert('Veuillez saisir votre message.');
                quickMessage.focus();
                return;
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalHTML = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Envoi...';
            submitBtn.disabled = true;
            
            // Masquer le message de succès précédent
            quickChatSuccess.classList.add('d-none');

            fetch('{{ route("support.quick-chat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: message })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Afficher le message de succès
                    successMessage.innerHTML = `
                        ${data.message}<br>
                        <small class="text-muted">Référence: <strong>${data.reference}</strong></small>
                    `;
                    quickChatSuccess.classList.remove('d-none');
                    
                    // Réinitialiser le formulaire
                    quickMessage.value = '';
                    charCount.textContent = '0';
                    
                    // Masquer l'alerte après 5 secondes
                    setTimeout(() => {
                        quickChatSuccess.classList.add('d-none');
                    }, 5000);
                    
                    // Rafraîchir le widget après 1 seconde pour afficher la nouvelle conversation
                    setTimeout(() => {
                        refreshWidget();
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Erreur lors de l\'envoi');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'envoi du message. Veuillez réessayer.');
            })
            .finally(() => {
                submitBtn.innerHTML = originalHTML;
                submitBtn.disabled = false;
            });
        });
    }

    // Fonction pour rafraîchir le widget
    function refreshWidget() {
        fetch('{{ route("support.widget") }}')
            .then(response => response.text())
            .then(html => {
                // Parser le HTML retourné
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Mettre à jour le badge de notification
                const newBadge = doc.querySelector('#supportToggle .badge');
                const currentBadge = toggle.querySelector('.badge');
                
                if (newBadge && !currentBadge) {
                    toggle.appendChild(newBadge.cloneNode(true));
                    toggle.classList.add('has-notification');
                } else if (newBadge && currentBadge) {
                    currentBadge.textContent = newBadge.textContent;
                } else if (!newBadge && currentBadge) {
                    currentBadge.remove();
                    toggle.classList.remove('has-notification');
                }
                
                // Mettre à jour le contenu du popup si fermé
                if (popup.classList.contains('d-none')) {
                    const newContent = doc.querySelector('#supportPopup .card-body');
                    const currentContent = popup.querySelector('.card-body');
                    if (newContent && currentContent) {
                        currentContent.innerHTML = newContent.innerHTML;
                        // Réattacher les événements après la mise à jour
                        reattachEvents();
                    }
                }
            })
            .catch(error => {
                console.log('Erreur lors du rafraîchissement du widget:', error);
            });
    }

    // Fonction pour réattacher les événements après une mise à jour
    function reattachEvents() {
        const newQuickForm = document.getElementById('quickChatForm');
        const newQuickMessage = document.getElementById('quickMessage');
        const newCharCount = document.getElementById('charCount');
        
        if (newQuickMessage && newCharCount) {
            newQuickMessage.addEventListener('input', function() {
                newCharCount.textContent = this.value.length;
                if (this.value.length > 900) {
                    newCharCount.classList.add('text-danger', 'fw-bold');
                } else {
                    newCharCount.classList.remove('text-danger', 'fw-bold');
                }
            });
        }
        
        // Réattacher le gestionnaire de soumission du formulaire
        if (newQuickForm) {
            newQuickForm.addEventListener('submit', quickForm.onsubmit);
        }
    }

    // Vérifier les nouveaux messages toutes les 30 secondes (seulement si connecté)
    @auth
    setInterval(refreshWidget, 30000);
    @endauth
});
</script>