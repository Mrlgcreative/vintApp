<!-- Widget d'assistance rapide avec Tailwind CSS -->
<div id="supportWidget" class="fixed bottom-6 right-6 z-50">
    <!-- Bouton flottant -->
    <button id="supportToggle" 
            class="relative w-16 h-16 rounded-full bg-gradient-to-r from-purple-600 to-blue-600 text-white shadow-2xl hover:shadow-purple-500/50 hover:scale-110 transition-all duration-300 group">
        <svg class="w-8 h-8 mx-auto group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        @if(Auth::check() && isset($openChats) && $openChats->count() > 0)
            <span class="absolute -top-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white text-xs font-bold animate-pulse">
                {{ $openChats->count() }}
            </span>
        @endif
    </button>

    <!-- Popup du widget -->
    <div id="supportPopup" 
         class="hidden absolute bottom-20 right-0 w-96 max-w-[calc(100vw-2rem)] bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden transform origin-bottom-right transition-all duration-300">
        <!-- En-tête -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 text-white p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-xl font-bold mb-1">Assistance</h3>
                    <p class="text-purple-100 text-sm">Nous sommes là pour vous aider</p>
                </div>
                <button id="supportClose" class="text-white hover:bg-white/20 rounded-lg p-1 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div>
                        <h4 class="text-sm font-bold text-gray-900 mb-3">Vos conversations en cours</h4>
                        <div class="space-y-2 max-h-48 overflow-y-auto mb-4">
                            @foreach($openChats as $chat)
                                <a href="{{ route('support.show', $chat) }}" 
                                   class="block p-3 rounded-xl bg-gradient-to-r from-purple-50 to-blue-50 hover:from-purple-100 hover:to-blue-100 border border-purple-200 transition-all duration-200 group">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-gray-900 text-sm truncate">{{ $chat->reference }}</p>
                                            <p class="text-xs text-gray-600 truncate">{{ $chat->subject ?: 'Assistance' }}</p>
                                        </div>
                                        <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold flex-shrink-0
                                            {{ $chat->status === 'open' ? 'bg-red-100 text-red-700' : '' }}
                                            {{ $chat->status === 'in_progress' ? 'bg-amber-100 text-amber-700' : '' }}
                                            {{ $chat->status === 'waiting_user' ? 'bg-blue-100 text-blue-700' : '' }}">
                                            {{ $chat->formatted_status }}
                                        </span>
                                    </div>
                                    @if($chat->unread_count_for_user > 0)
                                        <div class="flex items-center gap-1 text-xs">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-purple-600 text-white font-semibold">
                                                {{ $chat->unread_count_for_user }} nouveau(x) message(s)
                                            </span>
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                        <div class="pt-3 border-t border-gray-200">
                            <a href="{{ route('support.index') }}" 
                               class="text-purple-600 hover:text-purple-700 text-sm font-semibold flex items-center gap-1 group">
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                                Voir toutes mes conversations
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Chat rapide -->
                <div>
                    <h4 class="text-sm font-bold text-gray-900 mb-3">Besoin d'aide ?</h4>
                    <div id="quickChatSuccess" class="hidden mb-3 p-4 rounded-xl bg-green-50 border border-green-200" x-data="{ show: false }" x-show="show" x-transition>
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span id="successMessage" class="text-sm text-green-800 flex-1"></span>
                        </div>
                    </div>
                    <form id="quickChatForm" class="space-y-3">
                        <div>
                            <textarea id="quickMessage" 
                                      placeholder="Décrivez votre problème..." 
                                      rows="4" 
                                      maxlength="1000"
                                      required
                                      class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 outline-none resize-none text-sm"></textarea>
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-xs text-gray-500">
                                    <span id="charCount">0</span>/1000
                                </span>
                            </div>
                        </div>
                        <button type="submit" 
                                class="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold hover:from-purple-700 hover:to-blue-700 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Envoyer
                        </button>
                    </form>
                </div>

                <!-- Liens utiles -->
                <div class="pt-4 border-t border-gray-200 space-y-2">
                    <a href="{{ route('support.create') }}" 
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-purple-50 transition-colors group">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">Nouvelle demande détaillée</p>
                            <p class="text-xs text-gray-500">Pour des problèmes complexes</p>
                        </div>
                    </a>
                    <a href="{{ route('support.index') }}" 
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-purple-50 transition-colors group">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">Historique de mes demandes</p>
                            <p class="text-xs text-gray-500">Voir toutes vos conversations</p>
                        </div>
                    </a>
                </div>
            @else
                <!-- Utilisateur non connecté -->
                <div class="text-center py-8">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gradient-to-br from-purple-100 to-blue-100 flex items-center justify-center">
                        <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Connectez-vous</h3>
                    <p class="text-sm text-gray-600 mb-4">Pour accéder au support personnalisé</p>
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold hover:from-purple-700 hover:to-blue-700 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
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
        box-shadow: 0 10px 25px rgba(147, 51, 234, 0.3);
    }
    50% {
        box-shadow: 0 10px 40px rgba(147, 51, 234, 0.6);
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
