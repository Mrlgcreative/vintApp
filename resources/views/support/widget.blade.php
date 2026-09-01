<!-- Widget d'assistance rapide -->
<div id="supportWidget" class="fixed bottom-6 right-6 z-50">
    <!-- Bouton flottant -->
    <button id="supportToggle" 
            class="relative w-14 h-14 rounded-full bg-vinted-primary-600 text-white shadow-lg hover:shadow-xl hover:bg-vinted-primary-700 hover:scale-105 transition-all duration-200 group">
        <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        @if(Auth::check() && isset($openChats) && $openChats->count() > 0)
            <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-vinted-danger-500 text-white text-xs font-semibold animate-pulse">
                {{ $openChats->count() }}
            </span>
        @endif
    </button>

    <!-- Popup du widget -->
    <div id="supportPopup" 
         class="hidden absolute bottom-16 right-0 w-96 max-w-[calc(100vw-2rem)] bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden transform origin-bottom-right transition-all duration-300">
        <!-- En-tête -->
        <div class="bg-vinted-primary-600 text-white p-5">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-semibold mb-0.5">Assistance</h3>
                    <p class="text-vinted-primary-100 text-sm">Nous sommes là pour vous aider</p>
                </div>
                <button id="supportClose" class="text-white hover:bg-white/10 rounded-md p-1 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Contenu -->
        <div class="max-h-[500px] overflow-y-auto p-4 space-y-4">
            @auth
                @if(isset($openChats) && $openChats->count() > 0)
                    <!-- Conversations existantes -->
                    <div class="dark:text-gray-100">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Vos conversations en cours</h4>
                        <div class="space-y-2 max-h-48 overflow-y-auto mb-4">
                            @foreach($openChats as $chat)
                                @php
                                    $wStatus = match ($chat->status) {
                                        'open' => 'danger',
                                        'in_progress' => 'warning',
                                        'waiting_user' => 'info',
                                        default => 'secondary',
                                    };
                                @endphp
                                <a href="{{ route('support.show', $chat) }}"
                                   class="block p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                                    <div class="flex justify-between items-start mb-1.5">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-900 dark:text-white text-sm truncate">{{ $chat->reference }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $chat->subject ?: 'Assistance' }}</p>
                                        </div>
                                        <x-badge variant="{{ $wStatus }}" class="ml-2 flex-shrink-0">{{ $chat->formatted_status }}</x-badge>
                                    </div>
                                    @if($chat->unread_count_for_user > 0)
                                        <div class="flex items-center gap-1 text-xs mt-1">
                                            <x-badge variant="danger">{{ $chat->unread_count_for_user }} nouveau(x) message(s)</x-badge>
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('support.index') }}"
                               class="text-vinted-primary-600 dark:text-vinted-primary-400 hover:text-vinted-primary-700 text-sm font-medium flex items-center gap-1">
                                <i class="fas fa-arrow-right text-xs"></i>
                                Voir toutes mes conversations
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Chat rapide -->
                <div class="dark:text-gray-100">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Besoin d'aide ?</h4>
                    <x-alert variant="success" class="hidden mb-3" id="quickChatSuccess" x-data="{ show: false }" x-show="show" x-transition>
                        <div class="flex items-start gap-2">
                            <span id="successMessage" class="text-sm flex-1"></span>
                        </div>
                    </x-alert>
                    <form id="quickChatForm" class="space-y-3">
                        <div>
                            <textarea id="quickMessage"
                                      placeholder="Décrivez votre problème..."
                                      rows="4"
                                      maxlength="1000"
                                      required
                                      class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white outline-none transition-colors focus:border-vinted-primary-400 focus:ring-2 focus:ring-vinted-primary-100 dark:focus:ring-vinted-primary-500/20 resize-none"></textarea>
                            <div class="flex justify-end items-center mt-1">
                                <span class="text-xs text-gray-500">
                                    <span id="charCount">0</span>/1000
                                </span>
                            </div>
                        </div>
                        <button type="submit"
                                class="w-full h-10 inline-flex items-center justify-center gap-2 rounded-md bg-vinted-primary-600 text-white text-sm font-medium hover:bg-vinted-primary-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Envoyer
                        </button>
                    </form>
                </div>

                <!-- Liens utiles -->
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700 space-y-1">
                    <a href="{{ route('support.create') }}"
                       class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                        <div class="w-9 h-9 rounded-lg bg-vinted-primary-100 dark:bg-vinted-primary-500/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-vinted-primary-700 dark:text-vinted-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Nouvelle demande détaillée</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Pour des problèmes complexes</p>
                        </div>
                    </a>
                    <a href="{{ route('support.index') }}"
                       class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                        <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Historique de mes demandes</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Voir toutes vos conversations</p>
                        </div>
                    </a>
                </div>
            @else
                <!-- Utilisateur non connecté -->
                <div class="text-center py-8">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700/40 flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold tracking-tight text-gray-900 dark:text-white mb-1.5">Connectez-vous</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Pour accéder au support personnalisé</p>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center justify-center h-10 px-6 rounded-md bg-vinted-primary-600 text-white text-sm font-medium hover:bg-vinted-primary-700 transition-colors">
                        Se connecter
                    </a>
                </div>
            @endauth
        </div>
    </div>
</div>

<style>
@keyframes slideUpScale {
    from {
        opacity: 0;
        transform: scale(0.8) translateY(20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

#supportPopup:not(.hidden) {
    animation: slideUpScale 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

#supportToggle.has-notification {
    animation: pulse-glow 2s infinite;
}

@keyframes pulse-glow {
    0%, 100% {
        box-shadow: 0 10px 25px rgba(240, 84, 122, 0.3);
    }
    50% {
        box-shadow: 0 10px 40px rgba(240, 84, 122, 0.6);
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
    const badge = toggle.querySelector('span');
    if (badge) {
        toggle.classList.add('has-notification');
    }

    // Toggle popup
    if (toggle) {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            popup.classList.toggle('hidden');
            
            if (!popup.classList.contains('hidden') && quickMessage) {
                setTimeout(() => quickMessage.focus(), 100);
            }
        });
    }

    // Fermer popup
    if (close) {
        close.addEventListener('click', function(e) {
            e.stopPropagation();
            popup.classList.add('hidden');
        });
    }

    // Fermer en cliquant en dehors
    document.addEventListener('click', function(e) {
        const widget = document.getElementById('supportWidget');
        if (widget && !widget.contains(e.target)) {
            popup.classList.add('hidden');
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
            const length = this.value.length;
            charCount.textContent = length;
            
            if (length > 900) {
                charCount.classList.add('text-red-600', 'font-bold');
            } else {
                charCount.classList.remove('text-red-600', 'font-bold');
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
            submitBtn.innerHTML = '<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Envoi...';
            submitBtn.disabled = true;
            
            quickChatSuccess.classList.add('hidden');

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
                    successMessage.innerHTML = `
                        ${data.message}<br>
                        <span class="text-xs text-green-700">Référence: <strong>${data.reference}</strong></span>
                    `;
                    quickChatSuccess.classList.remove('hidden');
                    
                    quickMessage.value = '';
                    charCount.textContent = '0';
                    
                    setTimeout(() => {
                        quickChatSuccess.classList.add('hidden');
                    }, 5000);
                    
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
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const newBadge = doc.querySelector('#supportToggle span');
                const currentBadge = toggle.querySelector('span');
                
                if (newBadge && !currentBadge) {
                    toggle.appendChild(newBadge.cloneNode(true));
                    toggle.classList.add('has-notification');
                } else if (newBadge && currentBadge) {
                    currentBadge.textContent = newBadge.textContent;
                } else if (!newBadge && currentBadge) {
                    currentBadge.remove();
                    toggle.classList.remove('has-notification');
                }
                
                if (popup.classList.contains('hidden')) {
                    const newContent = doc.querySelector('#supportPopup > div:last-child');
                    const currentContent = popup.querySelector('div:last-child');
                    if (newContent && currentContent) {
                        currentContent.innerHTML = newContent.innerHTML;
                        reattachEvents();
                    }
                }
            })
            .catch(error => {
                console.log('Erreur lors du rafraîchissement du widget:', error);
            });
    }

    function reattachEvents() {
        const newQuickForm = document.getElementById('quickChatForm');
        const newQuickMessage = document.getElementById('quickMessage');
        const newCharCount = document.getElementById('charCount');
        
        if (newQuickMessage && newCharCount) {
            newQuickMessage.addEventListener('input', function() {
                newCharCount.textContent = this.value.length;
                if (this.value.length > 900) {
                    newCharCount.classList.add('text-red-600', 'font-bold');
                } else {
                    newCharCount.classList.remove('text-red-600', 'font-bold');
                }
            });
        }
        
        if (newQuickForm) {
            newQuickForm.addEventListener('submit', quickForm.onsubmit);
        }
    }

    @auth
    setInterval(refreshWidget, 30000);
    @endauth
});
</script>
