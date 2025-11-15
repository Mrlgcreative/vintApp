@extends('app')

@section('title', 'Conversation avec ' . $otherUser->name)

@section('content')
<div class="fixed inset-0 bg-gray-50 dark:bg-gray-900 flex flex-col overflow-hidden z-50 pb-16 md:pb-0" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 1px, rgba(0,0,0,0.02) 1px, rgba(0,0,0,0.02) 10px);">
    <!-- En-tête style WhatsApp -->
    <div class="bg-primary-600 text-white shadow-lg">
        <div class="flex items-center px-4 py-3 gap-3">
            <a href="{{ route('messages.index') }}" class="text-white hover:bg-white dark:bg-gray-800 hover:bg-opacity-10 p-2 rounded-full transition-colors">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div class="flex items-center flex-1 gap-3 cursor-pointer">
                <div class="relative">
                    @if($otherUser->avatar)
                        <img src="{{ Storage::url($otherUser->avatar) }}" 
                             alt="{{ $otherUser->name }}" 
                             class="w-10 h-10 rounded-full object-cover">
                    @else
                        <div class="w-10 h-10 rounded-full bg-primary-700 flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                </div>
                <div class="flex-1">
                    <h6 class="font-medium text-white">{{ $otherUser->name }}</h6>
                    <p class="text-sm text-white text-opacity-80">En ligne</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button class="text-white hover:bg-white dark:bg-gray-800 hover:bg-opacity-10 p-2 rounded-full transition-colors">
                    <i class="fas fa-phone"></i>
                </button>
                <button class="text-white hover:bg-white dark:bg-gray-800 hover:bg-opacity-10 p-2 rounded-full transition-colors">
                    <i class="fas fa-video"></i>
                </button>
                <div class="relative">
                    <button class="text-white hover:bg-white dark:bg-gray-800 hover:bg-opacity-10 p-2 rounded-full transition-colors" onclick="toggleDropdown()">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-2 hidden z-10">
                        <a href="#" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800">
                            <i class="fas fa-search mr-3"></i>Rechercher
                        </a>
                        <a href="#" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:bg-gray-800">
                            <i class="fas fa-ban mr-3"></i>Bloquer
                        </a>
                        <hr class="my-2">
                        <a href="#" class="flex items-center px-4 py-2 text-red-600 hover:bg-gray-100 dark:bg-gray-800">
                            <i class="fas fa-trash mr-3"></i>Supprimer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Badge produit concerné -->
    @if($item)
        <div class="bg-yellow-100 border-b border-yellow-200 px-4 py-3">
            <div class="flex items-center gap-3">
                @if($item->images && count($item->images) > 0)
                    <img src="{{ Storage::url($item->images[0]) }}" 
                         alt="{{ $item->name }}" 
                         class="w-12 h-12 rounded-lg object-cover">
                @endif
                <div class="flex-1">
                    <span class="font-semibold text-gray-900 dark:text-white block">{{ $item->name }}</span>
                    <span class="text-primary-600 font-bold">{{ $item->formatted_price }}</span>
                </div>
                <a href="{{ route('items.show', $item) }}" class="text-primary-600 hover:text-primary-700 p-2">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
        </div>

        <!-- Panel de réduction pour le vendeur -->
        @if($item->user_id === Auth::id())
            <div class="bg-green-50 border-b border-green-200" id="discountPanel">
                <div class="flex items-center justify-between px-4 py-3 cursor-pointer hover:bg-green-100 transition-colors" onclick="toggleDiscountPanel()">
                    <div class="flex items-center text-green-700">
                        <i class="fas fa-percent text-green-600 mr-2"></i>
                        <span class="font-semibold">Proposer une réduction</span>
                    </div>
                    <button class="text-gray-500 hover:text-gray-700 dark:text-gray-200">
                        <i class="fas fa-chevron-down transition-transform" id="discountToggleIcon"></i>
                    </button>
                </div>
                
                <div class="px-4 pb-4 hidden" id="discountContent">
                    <div class="border-t border-green-200 pt-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 mb-4 border border-gray-200 dark:border-gray-700 flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300 font-medium">Prix actuel:</span>
                            <span class="text-primary-600 font-bold text-lg">{{ $item->formatted_price }}</span>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mb-4">
                            <div class="rate-option bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-lg p-3 text-center cursor-pointer hover:border-primary-600 hover:-translate-y-1 hover:shadow-md transition-all" data-rate="5">
                                <div class="font-bold text-lg mb-1">5%</div>
                                <div class="text-green-600 font-semibold">{{ $item->currency_symbol }} {{ number_format($item->price * 0.95, 2) }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">-{{ $item->currency_symbol }} {{ number_format($item->price * 0.05, 2) }}</div>
                            </div>
                            <div class="rate-option bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-lg p-3 text-center cursor-pointer hover:border-primary-600 hover:-translate-y-1 hover:shadow-md transition-all" data-rate="10">
                                <div class="font-bold text-lg mb-1">10%</div>
                                <div class="text-green-600 font-semibold">{{ $item->currency_symbol }} {{ number_format($item->price * 0.90, 2) }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">-{{ $item->currency_symbol }} {{ number_format($item->price * 0.10, 2) }}</div>
                            </div>
                            <div class="rate-option bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-lg p-3 text-center cursor-pointer hover:border-primary-600 hover:-translate-y-1 hover:shadow-md transition-all" data-rate="15">
                                <div class="font-bold text-lg mb-1">15%</div>
                                <div class="text-green-600 font-semibold">{{ $item->currency_symbol }} {{ number_format($item->price * 0.85, 2) }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">-{{ $item->currency_symbol }} {{ number_format($item->price * 0.15, 2) }}</div>
                            </div>
                            <div class="rate-option bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-lg p-3 text-center cursor-pointer hover:border-primary-600 hover:-translate-y-1 hover:shadow-md transition-all" data-rate="20">
                                <div class="font-bold text-lg mb-1">20%</div>
                                <div class="text-green-600 font-semibold">{{ $item->currency_symbol }} {{ number_format($item->price * 0.80, 2) }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">-{{ $item->currency_symbol }} {{ number_format($item->price * 0.20, 2) }}</div>
                            </div>
                            <div class="rate-option bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-lg p-3 text-center cursor-pointer hover:border-primary-600 hover:-translate-y-1 hover:shadow-md transition-all" data-rate="25">
                                <div class="font-bold text-lg mb-1">25%</div>
                                <div class="text-green-600 font-semibold">{{ $item->currency_symbol }} {{ number_format($item->price * 0.75, 2) }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">-{{ $item->currency_symbol }} {{ number_format($item->price * 0.25, 2) }}</div>
                            </div>
                            <div class="rate-option bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-lg p-3 text-center cursor-pointer hover:border-primary-600 hover:-translate-y-1 hover:shadow-md transition-all" data-rate="30">
                                <div class="font-bold text-lg mb-1">30%</div>
                                <div class="text-green-600 font-semibold">{{ $item->currency_symbol }} {{ number_format($item->price * 0.70, 2) }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">-{{ $item->currency_symbol }} {{ number_format($item->price * 0.30, 2) }}</div>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-primary-600 hidden" id="selectedDiscountInfo">
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-primary-600 selected-rate"></span>
                                    <span class="font-bold text-green-600 text-lg selected-price"></span>
                                </div>
                            </div>
                            <button class="w-full bg-primary-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-primary-700 hover:-translate-y-0.5 hover:shadow-lg transition-all disabled:bg-gray-400 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none flex items-center justify-center gap-2" id="applyDiscountBtn" disabled>
                                <i class="fas fa-check"></i>
                                Appliquer la réduction
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- Zone des messages -->
    <div class="flex-1 overflow-y-auto px-4 py-4 pb-24 md:pb-4" id="messagesContainer" style="background-image: url(&quot;data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M20 20c0-11.046-8.954-20-20-20s-20 8.954-20 20 8.954 20 20 20 20-8.954 20-20zm-30 0c0-5.523 4.477-10 10-10s10 4.477 10 10-4.477 10-10 10-10-4.477-10-10z'/%3E%3C/g%3E%3C/svg%3E&quot;);">
        <div class="max-w-4xl mx-auto">
            @if($messages->count() > 0)
                @foreach($messages as $message)
                    <div class="mb-2 flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xs lg:max-w-md xl:max-w-lg min-w-0 relative {{ $message->sender_id === Auth::id() ? 'bg-primary-100' : 'bg-white dark:bg-gray-800' }} rounded-2xl {{ $message->sender_id === Auth::id() ? 'rounded-br' : 'rounded-bl' }} px-3 py-2 shadow-sm animate-fade-in">
                            @if($message->subject)
                                <div class="bg-black bg-opacity-10 rounded-xl px-2 py-1 mb-2 text-sm font-semibold flex items-center gap-1">
                                    <i class="fas fa-tag"></i>
                                    {{ $message->subject }}
                                </div>
                            @endif
                            
                            @if($message->content)
                                <div class="text-sm leading-relaxed text-gray-900 dark:text-white mb-1">
                                    {!! nl2br(e($message->content)) !!}
                                </div>
                            @endif
                            
                            @if($message->attachment)
                                <div class="my-2">
                                    @if(Str::startsWith($message->attachment, 'items/') || in_array(pathinfo($message->attachment, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                        <img src="{{ Storage::url($message->attachment) }}" 
                                             alt="Image jointe" 
                                             class="max-w-48 max-h-48 rounded-lg cursor-pointer hover:scale-105 transition-transform"
                                             onclick="showImageModal('{{ Storage::url($message->attachment) }}')">
                                    @else
                                        <a href="{{ Storage::url($message->attachment) }}" 
                                           target="_blank" 
                                           class="flex items-center gap-2 bg-black bg-opacity-5 rounded-lg p-3 text-primary-600 hover:bg-opacity-10 transition-colors">
                                            <i class="fas fa-paperclip"></i>
                                            <span class="text-sm">Fichier joint</span>
                                        </a>
                                    @endif
                                </div>
                            @endif
                            
                            <div class="flex items-center justify-end gap-1 text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $message->created_at->format('H:i') }}
                                @if($message->sender_id === Auth::id())
                                    <span class="ml-1">
                                        @if($message->is_read)
                                            <i class="fas fa-check-double text-blue-500"></i>
                                        @else
                                            <i class="fas fa-check-double text-gray-400"></i>
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-16 text-gray-500 dark:text-gray-400">
                    <div class="mb-4">
                        <i class="fas fa-comments text-6xl opacity-30"></i>
                    </div>
                    <p class="text-lg">Aucun message dans cette conversation</p>
                    <p class="text-sm">Envoyez un message pour commencer</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Zone de saisie style WhatsApp -->
    <div class="bg-gray-100 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 pb-20 md:pb-4 fixed md:relative bottom-0 left-0 right-0 z-40">
        <form id="messageForm" method="POST" action="{{ route('messages.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="recipient_id" value="{{ $otherUser->id }}">
            
            <div class="flex items-end gap-2 bg-white dark:bg-gray-800 rounded-3xl px-3 py-2 shadow-sm">
                <button type="button" class="text-gray-500 dark:text-gray-400 hover:text-primary-600 p-2 rounded-full hover:bg-gray-100 dark:bg-gray-800 transition-colors" onclick="document.getElementById('attachmentInput').click()">
                    <i class="fas fa-paperclip text-xl"></i>
                </button>
                
                <div class="flex-1 relative">
                    <textarea name="content" 
                              class="w-full border-0 outline-none bg-transparent resize-none text-gray-900 dark:text-white placeholder-gray-500 max-h-24 min-h-5 py-2" 
                              placeholder="Tapez un message..."
                              rows="1"
                              id="messageContent"></textarea>
                    
                    <input type="file" 
                           name="attachment" 
                           id="attachmentInput" 
                           class="hidden" 
                           accept="image/*,.pdf,.doc,.docx">
                </div>
                
                <button type="submit" class="bg-primary-600 text-white p-2 rounded-full hover:bg-primary-700 hover:scale-105 transition-all min-w-10 h-10 flex items-center justify-center">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
            
            <div id="attachmentPreview" class="mt-2 hidden">
                <div class="bg-primary-100 border border-primary-600 rounded-lg p-3 flex items-center gap-2 text-primary-700">
                    <i class="fas fa-paperclip"></i>
                    <span id="attachmentName" class="text-sm flex-1"></span>
                    <button type="button" onclick="removeAttachment()" class="text-red-500 hover:text-red-700 hover:bg-red-100 p-1 rounded-full transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal pour l'affichage des images -->
<div class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50" id="imageModal" onclick="closeImageModal()">
    <div class="relative max-w-4xl max-h-full p-4">
        <button onclick="closeImageModal()" class="absolute top-2 right-2 text-white hover:text-gray-300 bg-black bg-opacity-50 rounded-full p-2 z-10">
            <i class="fas fa-times text-xl"></i>
        </button>
        <img id="modalImage" src="" alt="Image" class="max-w-full max-h-full object-contain rounded-lg">
    </div>
</div>

@endsection

@push('styles')
<style>
/* Masquer les éléments de navigation pour l'expérience chat */
body {
    overflow: hidden;
}

footer,
.breadcrumb {
    display: none !important;
}

main.min-vh-100 {
    padding-top: 0 !important;
}

/* Animation d'apparition des messages */
@keyframes fade-in {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(10px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

/* Style pour les options de réduction sélectionnées */
.rate-option.selected {
    @apply border-primary-600 bg-primary-600 text-white shadow-lg;
}

.rate-option.selected .text-green-600 {
    @apply text-green-100;
}

.rate-option.selected .text-gray-500 dark:text-gray-400 {
    @apply text-white text-opacity-80;
}

/* Style pour la barre de défilement */
#messagesContainer::-webkit-scrollbar {
    width: 6px;
}

#messagesContainer::-webkit-scrollbar-track {
    background: transparent;
}

#messagesContainer::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.2);
    border-radius: 3px;
}

#messagesContainer::-webkit-scrollbar-thumb:hover {
    background: rgba(0,0,0,0.3);
}

/* Responsive pour mobile */
@media (max-width: 768px) {
    .max-w-xs {
        max-width: 16rem;
    }
    
    .max-w-4xl {
        max-width: 100%;
    }
    
    .grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('messagesContainer');
    const messageInput = document.getElementById('messageContent');
    const attachmentInput = document.getElementById('attachmentInput');
    const attachmentPreview = document.getElementById('attachmentPreview');
    const attachmentName = document.getElementById('attachmentName');
    const messageForm = document.getElementById('messageForm');

    // Auto-scroll vers le bas
    if (container) {
        container.scrollTop = container.scrollHeight;
    }

    // Auto-resize du textarea
    if (messageInput) {
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        });

        // Enter pour envoyer (Shift+Enter pour nouvelle ligne)
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                messageForm.dispatchEvent(new Event('submit'));
            }
        });
    }

    // Gestion des fichiers joints
    if (attachmentInput) {
        attachmentInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                attachmentName.textContent = file.name;
                attachmentPreview.classList.remove('hidden');
            } else {
                attachmentPreview.classList.add('hidden');
            }
        });
    }

    // Fonction pour créer un nouveau message dans l'interface
    function appendNewMessage(message, isAuthor) {
        const messageContainer = document.createElement('div');
        messageContainer.className = `mb-2 flex ${isAuthor ? 'justify-end' : 'justify-start'}`;
        
        const messageBubble = document.createElement('div');
        messageBubble.className = `max-w-xs lg:max-w-md xl:max-w-lg min-w-0 relative ${isAuthor ? 'bg-primary-100' : 'bg-white dark:bg-gray-800'} rounded-2xl ${isAuthor ? 'rounded-br' : 'rounded-bl'} px-3 py-2 shadow-sm animate-fade-in`;
        
        // Contenu du message
        if (message.content) {
            const messageText = document.createElement('div');
            messageText.className = 'text-sm leading-relaxed text-gray-900 dark:text-white mb-1';
            messageText.innerHTML = message.content.replace(/\n/g, '<br>');
            messageBubble.appendChild(messageText);
        }
        
        // Pièce jointe
        if (message.attachment) {
            const attachmentDiv = document.createElement('div');
            attachmentDiv.className = 'my-2';
            
            if (message.attachment.match(/\.(jpg|jpeg|png|gif)$/i)) {
                const img = document.createElement('img');
                img.src = message.attachment;
                img.className = 'max-w-48 max-h-48 rounded-lg cursor-pointer hover:scale-105 transition-transform';
                img.onclick = () => showImageModal(message.attachment);
                attachmentDiv.appendChild(img);
            } else {
                const link = document.createElement('a');
                link.href = message.attachment;
                link.className = 'flex items-center gap-2 bg-black bg-opacity-5 rounded-lg p-3 text-primary-600 hover:bg-opacity-10 transition-colors';
                link.innerHTML = `<i class="fas fa-paperclip"></i><span class="text-sm">Fichier joint</span>`;
                attachmentDiv.appendChild(link);
            }
            messageBubble.appendChild(attachmentDiv);
        }
        
        // Horodatage
        const timeDiv = document.createElement('div');
        timeDiv.className = 'flex items-center justify-end gap-1 text-xs text-gray-500 dark:text-gray-400 mt-1';
        timeDiv.textContent = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        messageBubble.appendChild(timeDiv);
        
        messageContainer.appendChild(messageBubble);
        return messageContainer;
    }

    // Envoi du formulaire optimisé
    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalIcon = submitButton.innerHTML;
            const messageContent = this.querySelector('#messageContent').value.trim();
            const attachmentFile = this.querySelector('#attachmentInput').files[0];
            
            if (!messageContent && !attachmentFile) return;
            
            // Désactiver le bouton et changer l'icône
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            // Ajouter immédiatement le message dans l'interface
            const tempMessage = {
                content: messageContent,
                attachment: attachmentFile ? URL.createObjectURL(attachmentFile) : null
            };
            const newMessageElement = appendNewMessage(tempMessage, true);
            document.querySelector('#messagesContainer > div').appendChild(newMessageElement);
            
            // Scroll vers le bas
            container.scrollTop = container.scrollHeight;
            
            // Réinitialiser le formulaire
            this.querySelector('#messageContent').value = '';
            this.querySelector('#attachmentInput').value = '';
            if (attachmentPreview) {
                attachmentPreview.classList.add('hidden');
            }
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    // En cas d'erreur, supprimer le message temporaire et afficher l'erreur
                    newMessageElement.remove();
                    alert(data.error || 'Erreur lors de l\'envoi du message');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                newMessageElement.remove();
                alert('Une erreur est survenue lors de l\'envoi');
            })
            .finally(() => {
                // Réactiver le bouton
                submitButton.disabled = false;
                submitButton.innerHTML = originalIcon;
            });
        });
    }

    // Gestion du panel de réduction
    const rateOptions = document.querySelectorAll('.rate-option');
    const applyDiscountBtn = document.getElementById('applyDiscountBtn');
    const selectedDiscountInfo = document.getElementById('selectedDiscountInfo');

    // Gestion de la sélection des taux de réduction
    let selectedRate = null;
    
    rateOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Retirer la sélection précédente
            rateOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Ajouter la sélection à l'option actuelle
            this.classList.add('selected');
            
            // Récupérer les informations
            selectedRate = this.dataset.rate;
            const ratePercentage = this.children[0].textContent;
            const ratePrice = this.children[1].textContent;
            
            // Afficher les informations sélectionnées
            selectedDiscountInfo.querySelector('.selected-rate').textContent = `Réduction de ${ratePercentage}`;
            selectedDiscountInfo.querySelector('.selected-price').textContent = `Prix final: ${ratePrice}`;
            selectedDiscountInfo.classList.remove('hidden');
            
            // Activer le bouton d'application
            applyDiscountBtn.disabled = false;
        });
    });

    // Gestion de l'application de la réduction
    if (applyDiscountBtn) {
        applyDiscountBtn.addEventListener('click', function() {
            if (!selectedRate) return;
            
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Application...';
            
            const formData = new FormData();
            formData.append('item_id', '{{ $item ? $item->id : '' }}');
            formData.append('buyer_id', '{{ $otherUser->id }}');
            formData.append('discount_percentage', selectedRate);
            formData.append('expires_hours', 24);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            fetch('/discounts/apply', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                
                if (data.success) {
                    // Afficher un message de succès
                    alert('Réduction appliquée avec succès ! Le client a été notifié.');
                    
                    // Masquer le panel de réduction
                    document.getElementById('discountContent').classList.add('hidden');
                    document.getElementById('discountToggleIcon').classList.add('fa-chevron-down');
                    document.getElementById('discountToggleIcon').classList.remove('fa-chevron-up');
                    
                    // Optionnel: recharger la page pour afficher le message automatique
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    console.error('Server error:', data.error);
                    alert(data.error || 'Erreur lors de l\'application de la réduction');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Une erreur est survenue lors de l\'application de la réduction: ' + error.message);
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = originalText;
            });
        });
    }
});

// Fonctions globales
function toggleDropdown() {
    const dropdown = document.getElementById('dropdown');
    dropdown.classList.toggle('hidden');
}

function toggleDiscountPanel() {
    const content = document.getElementById('discountContent');
    const icon = document.getElementById('discountToggleIcon');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        content.classList.add('hidden');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}

function showImageModal(src) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    modalImage.src = src;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function removeAttachment() {
    document.getElementById('attachmentInput').value = '';
    document.getElementById('attachmentPreview').classList.add('hidden');
}

// Fermer le dropdown en cliquant ailleurs
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('dropdown');
    if (dropdown && !e.target.closest('button')) {
        dropdown.classList.add('hidden');
    }
});
</script>
@endpush