@extends('app')

@section('title', 'Conversation avec ' . $otherUser->name)

@section('content')
<div class="fixed inset-0 bg-gray-50 dark:bg-gray-900 flex flex-col overflow-hidden z-50 pb-16 md:pb-0" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 1px, rgba(0,0,0,0.02) 1px, rgba(0,0,0,0.02) 10px);">
    <!-- En-tête style WhatsApp -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 text-white shadow-lg">
        <div class="flex items-center px-4 py-3 gap-3">
            <a href="{{ route('messages.index') }}" class="text-white hover:bg-white/10 p-2 rounded-full transition-all duration-200">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div class="flex items-center flex-1 gap-3 cursor-pointer">
                <div class="relative">
                    @if($otherUser->avatar_url)
                        <img src="{{ $otherUser->avatar_url }}" 
                             alt="{{ $otherUser->name }}" 
                             class="w-10 h-10 rounded-full object-cover">
                    @else
                        <div class="w-10 h-10 rounded-full bg-primary-700 flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 border-2 border-primary-600 rounded-full ring-2 ring-green-400/30"></div>
                </div>
                <div class="flex-1">
                    <h6 class="font-semibold text-white">{{ $otherUser->name }}</h6>
                    <p class="text-xs text-white/70 flex items-center gap-1"><span class="w-1.5 h-1.5 bg-green-400 rounded-full inline-block"></span> En ligne</p>
                </div>
            </div>
            <div class="flex gap-1">
                <button class="text-white/80 hover:text-white hover:bg-white/10 p-2.5 rounded-full transition-all duration-200">
                    <i class="fas fa-phone text-sm"></i>
                </button>
                <button class="text-white/80 hover:text-white hover:bg-white/10 p-2.5 rounded-full transition-all duration-200">
                    <i class="fas fa-video text-sm"></i>
                </button>
                <div class="relative">
                    <button class="text-white/80 hover:text-white hover:bg-white/10 p-2.5 rounded-full transition-all duration-200" onclick="toggleDropdown()">
                        <i class="fas fa-ellipsis-v text-sm"></i>
                    </button>
                    <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 py-1.5 hidden z-10">
                        <a href="#" class="flex items-center px-4 py-2.5 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-search mr-3 text-gray-400"></i>Rechercher
                        </a>
                        <a href="#" class="flex items-center px-4 py-2.5 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-ban mr-3 text-gray-400"></i>Bloquer
                        </a>
                        <hr class="my-1.5 border-gray-100 dark:border-gray-700">
                        <a href="#" class="flex items-center px-4 py-2.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                            <i class="fas fa-trash mr-3"></i>Supprimer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Badge produit concerné -->
    @if($item)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-b border-yellow-200 dark:border-yellow-800/50 px-4 py-3">
            <div class="flex items-center gap-3">
                @if($item->images && count($item->images) > 0)
                    <img src="{{ Storage::url($item->images[0]) }}" 
                         alt="{{ $item->name }}" 
                         class="w-14 h-14 rounded-xl object-cover shadow-sm ring-1 ring-black/5">
                @endif
                <div class="flex-1 min-w-0">
                    <span class="font-semibold text-gray-900 dark:text-white block truncate">{{ $item->name }}</span>
                    <span class="text-primary-600 dark:text-primary-400 font-bold text-lg">{{ $item->formatted_price }}</span>
                </div>
                <a href="{{ route('items.show', $item) }}" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 p-2.5 rounded-full hover:bg-yellow-100 dark:hover:bg-yellow-800/30 transition-colors">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
        </div>

        <!-- Panel de réduction pour le vendeur -->
        @if($item->user_id === Auth::id())
            <div class="bg-green-50 dark:bg-green-900/20 border-b border-green-200 dark:border-green-800/50" id="discountPanel">
                <div class="flex items-center justify-between px-4 py-3 cursor-pointer hover:bg-green-100 dark:hover:bg-green-800/30 transition-colors" onclick="toggleDiscountPanel()">
                    <div class="flex items-center text-green-700 dark:text-green-400">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-800/40 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-percent text-green-600 dark:text-green-400 text-sm"></i>
                        </div>
                        <span class="font-semibold">Proposer une réduction</span>
                    </div>
                    <button class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <i class="fas fa-chevron-down transition-transform" id="discountToggleIcon"></i>
                    </button>
                </div>
                
                <div class="px-4 pb-4 hidden" id="discountContent">
                    <div class="border-t border-green-200 dark:border-green-800/50 pt-4">
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
    <div class="flex-1 overflow-y-auto px-4 py-4 pb-24 md:pb-4 bg-gray-50 dark:bg-gray-900/50" id="messagesContainer">
        <div class="max-w-4xl mx-auto space-y-1">
            @if($messages->count() > 0)
                @foreach($messages as $message)
                    <div class="mb-1.5 flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xs lg:max-w-md xl:max-w-lg min-w-0 relative {{ $message->sender_id === Auth::id() ? 'bg-primary-500/10 dark:bg-primary-500/20' : 'bg-white dark:bg-gray-800' }} rounded-2xl {{ $message->sender_id === Auth::id() ? 'rounded-br-sm' : 'rounded-bl-sm' }} px-3.5 py-2.5 shadow-sm hover:shadow-md transition-shadow animate-fade-in">
                            @if($message->subject)
                                <div class="bg-black/5 dark:bg-white/5 rounded-lg px-2.5 py-1.5 mb-2 text-xs font-semibold flex items-center gap-1.5 text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-tag text-primary-500"></i>
                                    {{ $message->subject }}
                                </div>
                            @endif
                            
                            @if($message->content)
                                <div class="text-sm leading-relaxed text-gray-900 dark:text-white mb-1">
                                    {!! nl2br(e($message->content)) !!}
                                </div>
                            @endif
                            
                            @if($message->type === 'audio' && $message->attachment)
                                <div class="my-1 voice-msg" data-src="{{ Storage::url($message->attachment) }}" data-duration="{{ $message->duration ?? 0 }}">
                                    <div class="flex items-center gap-2 min-w-48">
                                        <button type="button" class="voice-play-btn w-9 h-9 rounded-full bg-primary-600 hover:bg-primary-700 text-white flex items-center justify-center flex-shrink-0 transition-all active:scale-95 shadow-sm">
                                            <i class="fas fa-play text-xs ml-0.5"></i>
                                        </button>
                                        <div class="flex-1 min-w-0">
                                            <div class="voice-progress h-1 bg-gray-300 dark:bg-gray-600 rounded-full overflow-hidden">
                                                <div class="voice-progress-fill h-full bg-primary-600 rounded-full" style="width: 0%"></div>
                                            </div>
                                        </div>
                                        <span class="voice-time text-xs text-gray-500 dark:text-gray-400 tabular-nums flex-shrink-0">{{ gmdate('i:s', intval($message->duration ?? 0)) }}</span>
                                        <i class="fas fa-microphone text-primary-600 dark:text-primary-400 text-xs flex-shrink-0 opacity-60"></i>
                                    </div>
                                </div>
                            @elseif($message->attachment)
                                <div class="my-2">
                                    @if(Str::startsWith($message->attachment, 'items/') || in_array(pathinfo($message->attachment, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                        <img src="{{ Storage::url($message->attachment) }}" 
                                             alt="Image jointe" 
                                             class="max-w-48 max-h-48 rounded-xl cursor-pointer hover:scale-[1.02] transition-transform shadow-sm"
                                             onclick="showImageModal('{{ Storage::url($message->attachment) }}')">
                                    @else
                                        <a href="{{ Storage::url($message->attachment) }}" 
                                           target="_blank" 
                                           class="flex items-center gap-2 bg-black/5 dark:bg-white/5 rounded-xl p-3 text-primary-600 dark:text-primary-400 hover:bg-black/10 dark:hover:bg-white/10 transition-colors">
                                            <div class="w-9 h-9 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-file text-primary-600 dark:text-primary-400"></i>
                                            </div>
                                            <span class="text-sm font-medium">Fichier joint</span>
                                            <i class="fas fa-download ml-auto text-xs opacity-50"></i>
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
                <div class="text-center py-20 text-gray-500 dark:text-gray-400">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-5">
                        <i class="fas fa-comments text-3xl text-gray-300 dark:text-gray-600"></i>
                    </div>
                    <p class="text-lg font-medium text-gray-700 dark:text-gray-300">Démarrez la conversation</p>
                    <p class="text-sm mt-1 text-gray-400">Envoyez un message à {{ $otherUser->name }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Zone de saisie -->
    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg border-t border-gray-200 dark:border-gray-700 p-3 pb-20 md:pb-3 fixed md:relative bottom-0 left-0 right-0 z-40">
        <form id="messageForm" method="POST" action="{{ route('messages.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="recipient_id" value="{{ $otherUser->id }}">
            <input type="hidden" name="type" id="messageType" value="text">
            <input type="hidden" name="duration" id="messageDuration" value="">

            <!-- Mode normal (texte/pièces jointes) -->
            <div id="normalInput" class="flex items-end gap-2 bg-gray-100 dark:bg-gray-700/50 rounded-2xl px-3 py-2 border border-gray-200/50 dark:border-gray-600/50 transition-all">
                <button type="button" class="text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex-shrink-0" onclick="document.getElementById('attachmentInput').click()">
                    <i class="fas fa-paperclip text-lg"></i>
                </button>

                <div class="flex-1 min-w-0 relative">
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

                <button type="submit" id="sendBtn" class="bg-primary-600 text-white p-2.5 rounded-xl hover:bg-primary-700 hover:scale-105 active:scale-95 transition-all min-w-10 h-10 flex items-center justify-center shadow-sm flex-shrink-0">
                    <i class="fas fa-paper-plane text-sm"></i>
                </button>
            </div>

            <!-- Mode enregistrement vocal -->
            <div id="recordingInput" class="hidden items-center gap-3 bg-gray-100 dark:bg-gray-700/50 rounded-2xl px-4 py-2 border border-gray-200/50 dark:border-gray-600/50 transition-all">
                <button type="button" id="cancelRecordBtn" class="text-gray-500 hover:text-red-500 p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex-shrink-0" title="Annuler">
                    <i class="fas fa-arrow-left text-lg"></i>
                </button>
                <div class="flex items-center gap-2 flex-1 min-w-0">
                    <span class="w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse flex-shrink-0" id="recordDot"></span>
                    <span id="recordingTimer" class="text-sm font-medium text-gray-900 dark:text-white tabular-nums">0:00</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">Appuyez pour arrêter</span>
                </div>
                <button type="button" id="stopRecordBtn" class="bg-red-500 text-white p-2.5 rounded-full hover:bg-red-600 active:scale-95 transition-all w-10 h-10 flex items-center justify-center shadow-sm flex-shrink-0" title="Arrêter">
                    <i class="fas fa-stop text-sm"></i>
                </button>
            </div>

            <div id="attachmentPreview" class="mt-2 hidden">
                <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800/50 rounded-xl p-3 flex items-center gap-3 text-primary-700 dark:text-primary-300">
                    <div class="w-8 h-8 bg-primary-100 dark:bg-primary-800/40 rounded-lg flex items-center justify-center">
                        <i class="fas fa-paperclip text-sm"></i>
                    </div>
                    <span id="attachmentName" class="text-sm flex-1 truncate font-medium"></span>
                    <button type="button" onclick="removeAttachment()" class="text-red-500 hover:text-red-700 hover:bg-red-100 dark:hover:bg-red-900/30 p-1.5 rounded-full transition-colors">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal pour l'affichage des images -->
<div class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden items-center justify-center z-50 transition-opacity" id="imageModal" onclick="closeImageModal()">
    <div class="relative max-w-4xl max-h-full p-4">
        <button onclick="closeImageModal()" class="absolute -top-2 -right-2 text-white hover:text-gray-300 bg-black/60 hover:bg-black/80 rounded-full w-10 h-10 flex items-center justify-center z-10 transition-colors shadow-lg">
            <i class="fas fa-times text-lg"></i>
        </button>
        <img id="modalImage" src="" alt="Image" class="max-w-full max-h-[85vh] object-contain rounded-xl shadow-2xl">
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
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.25s ease-out;
}

/* Style pour les options de réduction sélectionnées */
.rate-option.selected {
    @apply border-primary-600 bg-primary-600 text-white shadow-lg scale-[1.02];
}

.rate-option.selected .text-green-600 {
    @apply text-green-100;
}

.rate-option.selected .text-gray-500,
.rate-option.selected .dark\:text-gray-400 {
    @apply text-white/80;
}

/* Style pour la barre de défilement */
#messagesContainer::-webkit-scrollbar {
    width: 5px;
}

#messagesContainer::-webkit-scrollbar-track {
    background: transparent;
}

#messagesContainer::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.15);
    border-radius: 10px;
}

#messagesContainer::-webkit-scrollbar-thumb:hover {
    background: rgba(0,0,0,0.25);
}

@media (prefers-color-scheme: dark) {
    #messagesContainer::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.15);
    }
    #messagesContainer::-webkit-scrollbar-thumb:hover {
        background: rgba(255,255,255,0.25);
    }
}

/* Animation enregistrement */
@keyframes record-pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.1); }
}
#recordDot {
    animation: record-pulse 1s ease-in-out infinite;
}

/* Style barre de progression vocale */
.voice-progress {
    background: rgba(128, 128, 128, 0.25);
}
.voice-progress-fill {
    transition: width 0.3s linear;
    background: linear-gradient(90deg, #7c3aed, #a78bfa);
}

/* Bouton lecture vocal */
.voice-play-btn {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.voice-play-btn:active {
    transform: scale(0.9);
}

/* Message vocal bulle */
.voice-msg {
    user-select: none;
}
.voice-msg .voice-time {
    font-variant-numeric: tabular-nums;
    min-width: 2.5rem;
}

/* Bordures arrondies pour le mode enregistrement */
#recordingInput {
    @apply rounded-2xl;
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
let mediaRecorder = null;
let recorderStream = null;
let voiceFile = null;
let voiceTimerInterval = null;

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
                this.closest('form').querySelector('button[type="submit"]').click();
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
        messageBubble.className = `max-w-xs lg:max-w-md xl:max-w-lg min-w-0 relative ${isAuthor ? 'bg-primary-500/10 dark:bg-primary-500/20' : 'bg-white dark:bg-gray-800'} rounded-2xl ${isAuthor ? 'rounded-br-sm' : 'rounded-bl-sm'} px-3.5 py-2.5 shadow-sm animate-fade-in`;
        
        // Contenu du message
        if (message.content && message.type !== 'audio') {
            const messageText = document.createElement('div');
            messageText.className = 'text-sm leading-relaxed text-gray-900 dark:text-white mb-1';
            messageText.innerHTML = message.content.replace(/\n/g, '<br>');
            messageBubble.appendChild(messageText);
        }
        
        // Pièce jointe
        if (message.attachment) {
            const attachmentDiv = document.createElement('div');
            attachmentDiv.className = 'my-2';
            
            if (message.type === 'audio') {
                const wrapper = document.createElement('div');
                wrapper.className = 'voice-msg flex items-center gap-2 min-w-48';
                const dur = message.duration || 0;
                const mins = Math.floor(dur / 60);
                const secs = Math.floor(dur % 60);
                wrapper.dataset.src = message.attachment;
                wrapper.dataset.duration = dur;
                wrapper.innerHTML = `
                    <button type="button" class="voice-play-btn w-9 h-9 rounded-full bg-primary-600 hover:bg-primary-700 text-white flex items-center justify-center flex-shrink-0 transition-all active:scale-95 shadow-sm">
                        <i class="fas fa-play text-xs ml-0.5"></i>
                    </button>
                    <div class="flex-1 min-w-0">
                        <div class="voice-progress h-1 bg-gray-300 dark:bg-gray-600 rounded-full overflow-hidden">
                            <div class="voice-progress-fill h-full bg-primary-600 rounded-full" style="width: 0%"></div>
                        </div>
                    </div>
                    <span class="voice-time text-xs text-gray-500 dark:text-gray-400 tabular-nums flex-shrink-0">${mins}:${secs.toString().padStart(2, '0')}</span>
                    <i class="fas fa-microphone text-primary-600 dark:text-primary-400 text-xs flex-shrink-0 opacity-60"></i>
                `;
                attachmentDiv.appendChild(wrapper);
            } else if (message.attachment.match(/\.(jpg|jpeg|png|gif)$/i)) {
                const img = document.createElement('img');
                img.src = message.attachment;
                img.className = 'max-w-48 max-h-48 rounded-xl cursor-pointer hover:scale-[1.02] transition-transform shadow-sm';
                img.onclick = () => showImageModal(message.attachment);
                attachmentDiv.appendChild(img);
            } else {
                const link = document.createElement('a');
                link.href = message.attachment;
                link.className = 'flex items-center gap-2 bg-black/5 dark:bg-white/5 rounded-xl p-3 text-primary-600 dark:text-primary-400 hover:bg-black/10 dark:hover:bg-white/10 transition-colors';
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
    function sendMessage() {
        const form = document.getElementById('messageForm');
        if (!form) return false;

        // Arrêter l'enregistrement vocal si en cours
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            mediaRecorder.stop();
            return false;
        }

        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalIcon = submitButton ? submitButton.innerHTML : '';
        const messageContent = form.querySelector('#messageContent').value.trim();
        const attachmentFile = form.querySelector('#attachmentInput').files[0];
        const msgType = document.getElementById('messageType').value;

        // Si message vocal, ajouter le blob
        if (msgType === 'audio' && voiceFile) {
            formData.append('voice', voiceFile, 'voice.webm');
        }

        if (!messageContent && !attachmentFile && !voiceFile) return false;

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }

        // Ajouter immédiatement le message dans l'interface
        const tempMessage = {
            content: messageContent,
            attachment: voiceFile ? URL.createObjectURL(voiceFile) : (attachmentFile ? URL.createObjectURL(attachmentFile) : null),
            type: msgType,
            duration: msgType === 'audio' ? parseFloat(document.getElementById('messageDuration').value) : null
        };
        const newMessageElement = appendNewMessage(tempMessage, true);
        document.querySelector('#messagesContainer > div').appendChild(newMessageElement);

        container.scrollTop = container.scrollHeight;

        // Réinitialiser le formulaire
        form.querySelector('#messageContent').value = '';
        form.querySelector('#attachmentInput').value = '';
        if (attachmentPreview) attachmentPreview.classList.add('hidden');
        removeVoice();

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok && response.status !== 422) {
                throw new Error('Erreur serveur: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                newMessageElement.remove();
                alert(data.error || 'Erreur lors de l\'envoi du message');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            if (newMessageElement.parentNode) {
                newMessageElement.remove();
            }
        })
        .finally(() => {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = originalIcon;
            }
        });

        return true;
    }

    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            sendMessage();
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
            
            fetch('{{ route('discounts.apply-message') }}', {
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

    // === Enregistrement vocal (WhatsApp-style) ===
    let audioChunks = [];
    let recordingSeconds = 0;

    const normalInput = document.getElementById('normalInput');
    const recordingInput = document.getElementById('recordingInput');
    const stopRecordBtn = document.getElementById('stopRecordBtn');
    const cancelRecordBtn = document.getElementById('cancelRecordBtn');
    const recordingTimerEl = document.getElementById('recordingTimer');
    const messageType = document.getElementById('messageType');
    const messageDuration = document.getElementById('messageDuration');

    function startRecording() {
        navigator.mediaDevices.getUserMedia({ audio: true }).then(stream => {
            recorderStream = stream;
            const mimeType = MediaRecorder.isTypeSupported('audio/webm;codecs=opus')
                ? 'audio/webm;codecs=opus'
                : 'audio/webm';
            mediaRecorder = new MediaRecorder(stream, { mimeType });
            audioChunks = [];

            mediaRecorder.ondataavailable = (e) => {
                if (e.data.size > 0) audioChunks.push(e.data);
            };

            mediaRecorder.onstop = () => {
                stream.getTracks().forEach(track => track.stop());
                if (voiceTimerInterval) clearInterval(voiceTimerInterval);

                const blob = new Blob(audioChunks, { type: 'audio/webm' });
                voiceFile = new File([blob], 'voice.webm', { type: 'audio/webm' });

                messageType.value = 'audio';
                messageDuration.value = recordingSeconds;

                recordingInput.classList.add('hidden');

                // Envoyer automatiquement
                sendMessage();
            };

            mediaRecorder.onerror = () => {
                stream.getTracks().forEach(track => track.stop());
                if (voiceTimerInterval) clearInterval(voiceTimerInterval);
                recordingInput.classList.add('hidden');
                normalInput.classList.remove('hidden');

                const sb = document.querySelector('#messageForm button[type="submit"]');
                if (sb) sb.disabled = false;
            };

            mediaRecorder.start(250);
            recordingSeconds = 0;
            recordingTimerEl.textContent = '0:00';

            if (voiceTimerInterval) clearInterval(voiceTimerInterval);
            voiceTimerInterval = setInterval(() => {
                recordingSeconds++;
                recordingTimerEl.textContent = formatTime(recordingSeconds);
            }, 1000);

            normalInput.classList.add('hidden');
            recordingInput.classList.remove('hidden');

            const sb = document.querySelector('#messageForm button[type="submit"]');
            if (sb) sb.disabled = true;

        }).catch(err => {
            console.error('Erreur micro:', err);
            alert('Impossible d\'accéder au microphone. Vérifiez les permissions.');
        });
    }

    // Quand on appuie sur le mic dans le mode normal, passer en mode enregistrement
    // On ajoute un bouton mic dans normalInput
    const normalMicBtn = document.createElement('button');
    normalMicBtn.type = 'button';
    normalMicBtn.className = 'text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex-shrink-0';
    normalMicBtn.title = 'Message vocal';
    normalMicBtn.innerHTML = '<i class="fas fa-microphone text-lg"></i>';
    normalMicBtn.addEventListener('click', startRecording);
    normalInput.insertBefore(normalMicBtn, normalInput.querySelector('#sendBtn'));

    stopRecordBtn.addEventListener('click', () => {
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            mediaRecorder.stop();
        }
    });

    cancelRecordBtn.addEventListener('click', () => {
        if (mediaRecorder) {
            if (mediaRecorder.state === 'recording') {
                mediaRecorder.ondataavailable = null;
                mediaRecorder.onstop = null;
                mediaRecorder.stop();
            }
            if (recorderStream) {
                recorderStream.getTracks().forEach(t => t.stop());
                recorderStream = null;
            }
        }
        if (voiceTimerInterval) clearInterval(voiceTimerInterval);
        recordingInput.classList.add('hidden');
        normalInput.classList.remove('hidden');
        const sb = document.querySelector('#messageForm button[type="submit"]');
        if (sb) sb.disabled = false;
    });

    // === Lecteur vocal WhatsApp-style (play/pause avec progression) ===
    let currentVoiceAudio = null;
    let currentVoiceBtn = null;
    let currentVoiceFill = null;
    let currentVoiceTime = null;
    let voiceAnimFrame = null;

    document.addEventListener('click', function(e) {
        const playBtn = e.target.closest('.voice-play-btn');
        if (!playBtn) return;

        const container = playBtn.closest('.voice-msg');
        if (!container) return;

        const src = container.dataset.src;
        const fill = container.querySelector('.voice-progress-fill');
        const timeEl = container.querySelector('.voice-time');
        const totalDur = parseFloat(container.dataset.duration) || 0;

        // Si on clique sur le même bouton en cours de lecture
        if (currentVoiceAudio && currentVoiceBtn === playBtn && !currentVoiceAudio.paused) {
            currentVoiceAudio.pause();
            cancelAnimationFrame(voiceAnimFrame);
            playBtn.innerHTML = '<i class="fas fa-play text-xs ml-0.5"></i>';
            return;
        }

        // Arrêter la lecture précédente
        if (currentVoiceAudio) {
            currentVoiceAudio.pause();
            cancelAnimationFrame(voiceAnimFrame);
            if (currentVoiceBtn) {
                currentVoiceBtn.innerHTML = '<i class="fas fa-play text-xs ml-0.5"></i>';
            }
            if (currentVoiceFill) {
                currentVoiceFill.style.width = '0%';
            }
            if (currentVoiceTime && currentVoiceAudio._totalDur) {
                const m = Math.floor(currentVoiceAudio._totalDur / 60);
                const s = Math.floor(currentVoiceAudio._totalDur % 60);
                currentVoiceTime.textContent = m + ':' + s.toString().padStart(2, '0');
            }
        }

        // Nouvelle lecture
        const audio = new Audio(src);
        audio._totalDur = totalDur;
        currentVoiceAudio = audio;
        currentVoiceBtn = playBtn;
        currentVoiceFill = fill;
        currentVoiceTime = timeEl;

        playBtn.innerHTML = '<i class="fas fa-pause text-xs"></i>';

        function updateProgress() {
            if (audio.paused || audio.ended) return;
            const current = audio.currentTime;
            const dur = audio.duration || totalDur;
            const pct = dur > 0 ? (current / dur) * 100 : 0;
            if (fill) fill.style.width = Math.min(pct, 100) + '%';
            const m = Math.floor(current / 60);
            const s = Math.floor(current % 60);
            if (timeEl) timeEl.textContent = m + ':' + s.toString().padStart(2, '0');
            voiceAnimFrame = requestAnimationFrame(updateProgress);
        }

        audio.addEventListener('loadedmetadata', () => {
            audio._totalDur = audio.duration;
            updateProgress();
        });

        audio.addEventListener('timeupdate', updateProgress);

        audio.addEventListener('ended', () => {
            cancelAnimationFrame(voiceAnimFrame);
            playBtn.innerHTML = '<i class="fas fa-play text-xs ml-0.5"></i>';
            if (fill) fill.style.width = '0%';
            if (timeEl) {
                const m = Math.floor(totalDur / 60);
                const s = Math.floor(totalDur % 60);
                timeEl.textContent = m + ':' + s.toString().padStart(2, '0');
            }
            currentVoiceAudio = null;
            currentVoiceBtn = null;
            currentVoiceFill = null;
            currentVoiceTime = null;
        });

        audio.play().catch(() => {
            playBtn.innerHTML = '<i class="fas fa-play text-xs ml-0.5"></i>';
        });
    });

    // === Real-time messages via Pusher ===
    const otherUserId = {{ $otherUser->id }};
    if (typeof window.Echo !== 'undefined' && window.Echo) {
        window.Echo.private('user.{{ Auth::id() }}')
            .listen('.message.sent', (message) => {
                if (message.sender_id !== otherUserId) return;

                const newMsg = {
                    content: message.content,
                    attachment: message.attachment,
                    type: message.type,
                    duration: message.duration,
                };
                const el = appendNewMessage(newMsg, false);
                document.querySelector('#messagesContainer > div').appendChild(el);
                container.scrollTop = container.scrollHeight;
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

function removeVoice() {
    if (mediaRecorder && mediaRecorder.state === 'recording') {
        mediaRecorder.stop();
    }
    if (recorderStream) {
        recorderStream.getTracks().forEach(t => t.stop());
        recorderStream = null;
    }
    if (voiceTimerInterval) clearInterval(voiceTimerInterval);
    voiceFile = null;
    document.getElementById('messageType').value = 'text';
    document.getElementById('messageDuration').value = '';
    const ri = document.getElementById('recordingInput');
    const ni = document.getElementById('normalInput');
    if (ri) ri.classList.add('hidden');
    if (ni) ni.classList.remove('hidden');
    const sb = document.querySelector('#messageForm button[type="submit"]');
    if (sb) sb.disabled = false;
}

function formatTime(seconds) {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return m + ':' + s.toString().padStart(2, '0');
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