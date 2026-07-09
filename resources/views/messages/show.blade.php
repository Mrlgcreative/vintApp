@extends('app')

@section('title', 'Conversation avec ' . $otherUser->name)

@section('content')
<div class="fixed inset-0 bg-gray-50 dark:bg-gray-900 flex flex-col overflow-hidden z-50 pb-16 md:pb-0 [&_footer]:!hidden [&_.breadcrumb]:!hidden [&_main]:pt-0 bg-[repeating-linear-gradient(45deg,transparent,transparent_1px,rgba(0,0,0,0.02)_1px,rgba(0,0,0,0.02)_10px)]">
    {{-- En-tete style WhatsApp --}}
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 text-white shadow-lg">
        <div class="flex items-center px-3 sm:px-4 py-2.5 sm:py-3 gap-2 sm:gap-3">
            <a href="{{ route('messages.index') }}" class="text-white hover:bg-white/10 p-1.5 sm:p-2 rounded-full transition-all duration-200">
                <svg class="w-5 h-5 sm:w-[22px] sm:h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="flex items-center flex-1 gap-2 sm:gap-3 min-w-0 cursor-pointer">
                <div class="relative flex-shrink-0">
                    @if($otherUser->avatar_url)
                        <img src="{{ $otherUser->avatar_url }}"
                             alt="{{ $otherUser->name }}"
                             class="w-8 h-8 sm:w-10 sm:h-10 rounded-full object-cover">
                    @else
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-primary-700 flex items-center justify-center text-white font-semibold text-sm sm:text-base">
                            {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="absolute bottom-0 right-0 w-2.5 h-2.5 sm:w-3 sm:h-3 bg-green-400 border-2 border-primary-600 rounded-full ring-2 ring-green-400/30"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <h6 class="font-semibold text-white text-sm sm:text-base truncate">{{ $otherUser->name }}</h6>
                    <p class="text-[11px] sm:text-xs text-white/70 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full inline-block flex-shrink-0"></span>
                        <span class="truncate">En ligne</span>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-0.5 sm:gap-1">
                <button class="text-white/80 hover:text-white hover:bg-white/10 p-2 sm:p-2.5 rounded-full transition-all duration-200 hidden sm:flex" title="Appel">
                    <svg class="w-4 h-4 sm:w-[18px] sm:h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </button>
                <button class="text-white/80 hover:text-white hover:bg-white/10 p-2 sm:p-2.5 rounded-full transition-all duration-200 hidden sm:flex" title="Video">
                    <svg class="w-4 h-4 sm:w-[18px] sm:h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </button>
                <div class="relative">
                    <button class="text-white/80 hover:text-white hover:bg-white/10 p-2 sm:p-2.5 rounded-full transition-all duration-200" onclick="toggleDropdown()" title="Plus">
                        <svg class="w-4 h-4 sm:w-[18px] sm:h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                        </svg>
                    </button>
                    <div id="dropdown" class="absolute right-0 mt-2 w-44 sm:w-48 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 py-1.5 hidden z-10">
                        <a href="#" class="flex items-center gap-3 px-3 sm:px-4 py-2 sm:py-2.5 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Rechercher
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 sm:px-4 py-2 sm:py-2.5 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            Blocker
                        </a>
                        <hr class="my-1.5 border-gray-100 dark:border-gray-700">
                        <a href="#" class="flex items-center gap-3 px-3 sm:px-4 py-2 sm:py-2.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-sm">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Supprimer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Badge produit concerne --}}
    @if($item)
        <div class="bg-secondary dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700 px-3 sm:px-4 py-2 sm:py-3">
            <div class="flex items-center gap-2 sm:gap-3">
                @if($item->images && count($item->images) > 0)
                    <img src="{{ Storage::url($item->images[0]) }}"
                         alt="{{ $item->name }}"
                         class="w-10 h-10 sm:w-14 sm:h-14 rounded-lg sm:rounded-xl object-cover shadow-sm ring-1 ring-black/5 flex-shrink-0">
                @endif
                <div class="flex-1 min-w-0">
                    <span class="font-semibold text-gray-900 dark:text-white block truncate text-sm sm:text-base">{{ $item->name }}</span>
                    <span class="text-primary-600 dark:text-primary-400 font-bold text-base sm:text-lg">{{ $item->formatted_price }}</span>
                </div>
                <a href="{{ route('items.show', $item) }}"
                   class="text-primary-500 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 p-2 sm:p-2.5 rounded-full hover:bg-primary-50 dark:hover:bg-primary-800/30 transition-colors flex-shrink-0">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            </div>
        </div>

        {{-- Panel de reduction pour le vendeur --}}
        @if($item->user_id === Auth::id())
            <div class="bg-white dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700" id="discountPanel">
                <div class="flex items-center justify-between px-3 sm:px-4 py-2.5 sm:py-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" onclick="toggleDiscountPanel()">
                    <div class="flex items-center gap-2 sm:gap-3 text-gray-700 dark:text-gray-300 min-w-0">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center shadow-sm flex-shrink-0">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-sm sm:text-base truncate">Proposer une reduction</span>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 flex-shrink-0" id="discountToggleBtn">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 transition-transform duration-200" id="discountToggleIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>

                <div class="px-3 sm:px-4 pb-4 hidden" id="discountContent">
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-3 sm:pt-4 space-y-3 sm:space-y-4">
                        <div class="bg-white dark:bg-gray-800/80 backdrop-blur rounded-lg sm:rounded-xl p-3 sm:p-3.5 border border-gray-200 dark:border-gray-700 flex items-center justify-between shadow-sm">
                            <div class="flex items-center gap-2 sm:gap-2.5">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                </div>
                                <span class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300">Prix actuel :</span>
                            </div>
                            <span class="text-primary-600 dark:text-primary-400 font-bold text-lg sm:text-xl tracking-tight">{{ $item->formatted_price }}</span>
                        </div>

                        <div class="grid grid-cols-3 gap-1.5 sm:gap-2.5">
                            @php
                                $rates = [5, 10, 15, 20, 25, 30];
                            @endphp
                            @foreach($rates as $rate)
                                <div class="rate-option bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg sm:rounded-xl p-2 sm:p-3.5 text-center cursor-pointer hover:border-primary-400 hover:-translate-y-1 hover:shadow-lg transition-all duration-200 group"
                                     data-rate="{{ $rate }}">
                                    <div class="text-sm sm:text-lg font-bold text-gray-900 dark:text-white mb-0.5 sm:mb-1.5">{{ $rate }}%</div>
                                    <div class="text-primary-600 dark:text-primary-400 font-semibold text-[10px] sm:text-sm leading-tight sm:leading-normal">
                                        {{ $item->currency_symbol }}{{ number_format($item->price * (1 - $rate/100), 2) }}
                                    </div>
                                    <div class="text-[9px] sm:text-xs text-gray-400 dark:text-gray-500 mt-0.5 sm:mt-1 flex items-center justify-center gap-0.5 sm:gap-1">
                                        <span class="inline-flex items-center gap-0.5 text-red-400">
                                            <svg class="w-2 h-2 sm:w-3 sm:h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                            </svg>
                                            {{ $item->currency_symbol }}{{ number_format($item->price * $rate/100, 2) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg sm:rounded-xl p-2.5 sm:p-3 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-start gap-2 sm:gap-2.5">
                                <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                    La reduction sera appliquee automatiquement sur le prix affiche. L'acheteur recevra une notification de votre offre.
                                </p>
                            </div>
                        </div>

                        <form id="discountForm" method="POST" action="{{ route('discounts.apply-message') }}" onsubmit="return false;">
                            @csrf
                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                            <input type="hidden" name="buyer_id" value="{{ $otherUser->id }}">
                            <input type="hidden" name="discount_percentage" id="discountPercentage" value="">
                            <button type="button" id="applyDiscountBtn"
                                    class="w-full py-2.5 sm:py-3 rounded-xl font-semibold text-sm sm:text-base transition-all duration-200 bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed"
                                    disabled
                                    onclick="submitDiscount()">
                                Appliquer la reduction
                            </button>
                            <div id="discountResult" class="hidden mt-2 text-center text-sm font-medium"></div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- Zone des messages --}}
    <div class="flex-1 overflow-y-auto px-2 sm:px-4 py-3 sm:py-4 pb-24 md:pb-4 bg-gray-50 dark:bg-gray-900/50" id="messagesContainer">
        <div class="max-w-4xl mx-auto space-y-1">
            @if($messages->count() > 0)
                @foreach($messages as $message)
                    <div class="mb-1.5 flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[75%] sm:max-w-xs lg:max-w-md xl:max-w-lg min-w-0 relative {{ $message->sender_id === Auth::id() ? 'bg-primary-500/10 dark:bg-primary-500/20' : 'bg-white dark:bg-gray-800' }} rounded-2xl {{ $message->sender_id === Auth::id() ? 'rounded-br-sm' : 'rounded-bl-sm' }} px-3 sm:px-3.5 py-2 sm:py-2.5 shadow-sm hover:shadow-md transition-shadow animate-fade-in">
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
@keyframes fade-in {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.25s ease-out;
}

#messagesContainer::-webkit-scrollbar { width: 5px; }
#messagesContainer::-webkit-scrollbar-track { background: transparent; }
#messagesContainer::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 10px; }
#messagesContainer::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.25); }

@media (prefers-color-scheme: dark) {
    #messagesContainer::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); }
    #messagesContainer::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.25); }
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
    document.body.classList.add('overflow-hidden');

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
    const btn = document.getElementById('discountToggleBtn');

    content.classList.toggle('hidden');
    if (content.classList.contains('hidden')) {
        icon.style.transform = 'rotate(0deg)';
    } else {
        icon.style.transform = 'rotate(180deg)';
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

// Selection des options de reduction
let selectedRate = null;
let selectedPrice = null;

document.querySelectorAll('.rate-option').forEach(option => {
    option.addEventListener('click', function() {
        document.querySelectorAll('.rate-option').forEach(r => {
            r.classList.remove(
                'border-primary-400', 'bg-gradient-to-br', 'from-primary-600',
                'to-primary-800', 'shadow-lg', 'scale-[1.04]', 'text-white',
                '!border-primary-400'
            );
            r.querySelector('.text-primary-600')?.classList.remove('text-primary-100');
            r.querySelectorAll('.text-gray-400, .text-red-400').forEach(el => {
                el.classList.remove('text-white/70');
            });
        });
        this.classList.add(
            'border-primary-400', 'bg-gradient-to-br', 'from-primary-600',
            'to-primary-800', 'shadow-lg', 'scale-[1.04]', 'text-white'
        );
        this.querySelector('.text-primary-600')?.classList.add('text-primary-100');
        this.querySelectorAll('.text-gray-400, .text-red-400').forEach(el => {
            el.classList.add('text-white/70');
        });
        selectedRate = this.dataset.rate;
        selectedPrice = this.querySelector('.text-primary-600')?.textContent?.trim() || null;

        const btn = document.getElementById('applyDiscountBtn');
        btn.disabled = false;
        btn.className = 'w-full py-2.5 sm:py-3 rounded-xl font-semibold text-sm sm:text-base transition-all duration-200 bg-primary-600 hover:bg-primary-700 active:scale-[0.98] text-white shadow-sm';
    });
});

function submitDiscount() {
    if (!selectedRate) return;
    const btn = document.getElementById('applyDiscountBtn');
    const form = document.getElementById('discountForm');
    const result = document.getElementById('discountResult');

    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>';
    btn.className = 'w-full py-2.5 sm:py-3 rounded-xl font-semibold text-sm sm:text-base transition-all duration-200 bg-primary-600 text-white';

    document.getElementById('discountPercentage').value = selectedRate;

    fetch(form.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
        body: new FormData(form)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            result.className = 'mt-2 text-center text-sm font-medium text-emerald-600 dark:text-emerald-400';
            result.textContent = '✓ ' + data.message;
            btn.innerHTML = 'Appliquee';
            btn.className = 'w-full py-2.5 sm:py-3 rounded-xl font-semibold text-sm sm:text-base bg-emerald-500 text-white cursor-default';
            document.querySelectorAll('.rate-option').forEach(r => r.style.pointerEvents = 'none');
        } else {
            result.className = 'mt-2 text-center text-sm font-medium text-red-500';
            result.textContent = '✗ ' + (data.error || 'Erreur');
            btn.disabled = false;
            btn.innerHTML = 'Appliquer la reduction';
            btn.className = 'w-full py-2.5 sm:py-3 rounded-xl font-semibold text-sm sm:text-base bg-primary-600 hover:bg-primary-700 active:scale-[0.98] text-white shadow-sm';
        }
        result.classList.remove('hidden');
    })
    .catch(e => {
        result.className = 'mt-2 text-center text-sm font-medium text-red-500';
        result.textContent = '✗ Erreur de connexion';
        result.classList.remove('hidden');
        btn.disabled = false;
        btn.innerHTML = 'Appliquer la reduction';
        btn.className = 'w-full py-2.5 sm:py-3 rounded-xl font-semibold text-sm sm:text-base bg-primary-600 hover:bg-primary-700 active:scale-[0.98] text-white shadow-sm';
    });
}

// Nettoyage au depart
window.addEventListener('beforeunload', function() {
    document.body.classList.remove('overflow-hidden');
});
</script>
@endpush