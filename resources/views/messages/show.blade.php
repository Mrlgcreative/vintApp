@extends('app')

@section('title', 'Conversation avec ' . $otherUser->name)

@section('content')
<div class="fixed inset-0 bg-gray-50 dark:bg-gray-950 flex flex-col overflow-hidden z-50 [&_footer]:!hidden [&_.breadcrumb]:!hidden [&_main]:pt-0">
    {{-- En-tete --}}
    <div class="border-b border-gray-200 bg-white dark:border-gray-700/50 dark:bg-gray-900">
        <div class="flex items-center px-3 sm:px-4 py-2.5 sm:py-3 gap-2 sm:gap-1.5">
            <a href="{{ route('messages.index') }}" class="flex h-9 w-9 items-center justify-center rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white" aria-label="Retour">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="flex items-center flex-1 gap-2 sm:gap-3 min-w-0 cursor-pointer">
                <div class="relative flex-shrink-0">
                    @if($otherUser->avatar_url)
                        <img src="{{ $otherUser->avatar_url }}"
                             alt="{{ $otherUser->name }}"
                             class="h-9 w-9 sm:h-10 sm:w-10 rounded-full object-cover">
                    @else
                        <span class="flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-full bg-vinted-primary-100 text-sm font-semibold text-vinted-primary-700 dark:bg-vinted-primary-500/20 dark:text-vinted-primary-300">
                            {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                        </span>
                    @endif
                    <span class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full border-2 border-white bg-vinted-success-500 dark:border-gray-900"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <h6 class="truncate text-sm font-medium text-gray-900 dark:text-white sm:text-base">{{ $otherUser->name }}</h6>
                    <p class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                        <span class="inline-block h-1.5 w-1.5 flex-shrink-0 rounded-full bg-vinted-success-500"></span>
                        <span class="truncate">En ligne</span>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-0.5 sm:gap-0.5">
                <button class="flex h-9 w-9 items-center justify-center rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white hidden sm:flex" title="Appel">
                    <svg class="w-4 h-4 sm:w-[18px] sm:h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </button>
                <button class="flex h-9 w-9 items-center justify-center rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white hidden sm:flex" title="Video">
                    <svg class="w-4 h-4 sm:w-[18px] sm:h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </button>
                <div class="relative">
                    <button class="flex h-9 w-9 items-center justify-center rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white" onclick="toggleDropdown()" title="Plus">
                        <svg class="w-4 h-4 sm:w-[18px] sm:h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                        </svg>
                    </button>
                    <div id="dropdown" class="absolute right-0 mt-2 hidden z-10 w-44 sm:w-48 rounded-xl border border-gray-200 bg-white py-1.5 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                        <a href="#" class="flex items-center gap-3 px-3 sm:px-4 py-2 sm:py-2.5 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Rechercher
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 sm:px-4 py-2 sm:py-2.5 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            Blocker
                        </a>
                        <hr class="my-1.5 border-gray-100 dark:border-gray-700">
                        <a href="#" class="flex items-center gap-3 px-3 sm:px-4 py-2 sm:py-2.5 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20">
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
        <div class="border-b border-gray-200 bg-white px-3 dark:border-gray-700/50 dark:bg-gray-900 sm:px-4 py-2 sm:py-3">
            <div class="flex items-center gap-2 sm:gap-3">
                @if($item->images && count($item->images) > 0)
                    <img src="{{ Storage::url($item->images[0]) }}"
                         alt="{{ $item->name }}"
                         class="h-10 w-10 flex-shrink-0 rounded-lg object-cover shadow-sm ring-1 ring-black/5 sm:h-14 sm:w-14 sm:rounded-xl">
                @endif
                <div class="flex-1 min-w-0">
                    <span class="block truncate text-sm font-medium text-gray-900 dark:text-white sm:text-base">{{ $item->name }}</span>
                    <span class="text-base font-semibold text-vinted-primary-600 dark:text-vinted-primary-400 sm:text-lg">{{ $item->formatted_price }}</span>
                </div>
                <a href="{{ route('items.show', $item) }}"
                   class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            </div>
        </div>

        {{-- Panel de reduction pour le vendeur --}}
        @if($item->user_id === Auth::id())
            <div class="border-b border-gray-200 bg-white dark:border-gray-700/50 dark:bg-gray-900" id="discountPanel">
                <div class="flex items-center justify-between px-3 sm:px-4 py-2.5 sm:py-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50" onclick="toggleDiscountPanel()">
                    <div class="flex items-center gap-2 sm:gap-3 text-gray-700 dark:text-gray-300 min-w-0">
                        <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-vinted-primary-100 dark:bg-vinted-primary-500/20 sm:h-8 sm:w-8">
                            <svg class="w-3.5 h-3.5 text-vinted-primary-600 dark:text-vinted-primary-300 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="truncate text-sm font-medium sm:text-base">Proposer une reduction</span>
                    </div>
                    <button class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300" id="discountToggleBtn">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 transition-transform duration-200" id="discountToggleIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>

                <div class="hidden px-3 sm:px-4 pb-4" id="discountContent">
                    <div class="space-y-3 border-t border-gray-200 pt-3 dark:border-gray-700 sm:space-y-4 sm:pt-4">
                        <div class="flex items-center justify-between rounded-md border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/50 sm:rounded-lg sm:p-3.5">
                            <div class="flex items-center gap-2 sm:gap-2.5">
                                <div class="flex h-7 w-7 items-center justify-center rounded-md bg-vinted-primary-100 dark:bg-vinted-primary-500/20 sm:h-8 sm:w-8">
                                    <svg class="w-3.5 h-3.5 text-vinted-primary-600 dark:text-vinted-primary-300 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                </div>
                                <span class="text-xs font-medium text-gray-600 dark:text-gray-300 sm:text-sm">Prix actuel :</span>
                            </div>
                            <span class="text-lg font-semibold tracking-tight text-vinted-primary-600 dark:text-vinted-primary-400 sm:text-xl">{{ $item->formatted_price }}</span>
                        </div>

                        <div class="grid grid-cols-3 gap-1.5 sm:gap-2.5">
                            @php
                                $rates = [5, 10, 15, 20, 25, 30];
                            @endphp
                            @foreach($rates as $rate)
                                <button type="button"
                                        class="rate-option rounded-lg border border-gray-200 bg-white p-2 text-center transition-colors hover:border-vinted-primary-300 dark:border-gray-700 dark:bg-gray-800 sm:rounded-xl sm:p-3.5"
                                        data-rate="{{ $rate }}">
                                    <div class="mb-0.5 text-sm font-semibold text-gray-900 dark:text-white sm:mb-1.5 sm:text-lg">{{ $rate }}%</div>
                                    <div class="text-xs font-medium leading-tight text-vinted-primary-600 dark:text-vinted-primary-400 sm:text-sm">
                                        {{ $item->currency_symbol }}{{ number_format($item->price * (1 - $rate/100), 2) }}
                                    </div>
                                    <div class="mt-0.5 flex items-center justify-center gap-0.5 text-red-400 text-[9px] sm:mt-1 sm:gap-1 sm:text-xs">
                                        <span class="inline-flex items-center gap-0.5">
                                            <svg class="w-2 h-2 sm:w-3 sm:h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                            </svg>
                                            {{ $item->currency_symbol }}{{ number_format($item->price * $rate/100, 2) }}
                                        </span>
                                    </div>
                                </button>
                            @endforeach
                        </div>

                        <div class="rounded-md border border-gray-200 bg-gray-50 p-2.5 dark:border-gray-700 dark:bg-gray-800/50 sm:rounded-lg sm:p-3">
                            <div class="flex items-start gap-2 sm:gap-2.5">
                                <div class="mt-0.5 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-vinted-primary-100 dark:bg-vinted-primary-500/20 sm:h-7 sm:w-7">
                                    <svg class="w-3 h-3 text-vinted-primary-600 dark:text-vinted-primary-300 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-[10px] leading-relaxed text-gray-500 dark:text-gray-400 sm:text-xs">
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
                                    class="w-full rounded-md py-2.5 text-sm font-semibold text-gray-400 transition-colors dark:text-gray-500 bg-gray-200 dark:bg-gray-700 cursor-not-allowed sm:py-3"
                                    disabled
                                    onclick="submitDiscount()">
                                Appliquer la reduction
                            </button>
                            <div id="discountResult" class="mt-2 hidden text-center text-sm font-medium"></div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- Zone des messages --}}
    <div class="flex-1 overflow-y-auto bg-gray-50 px-2 py-3 dark:bg-gray-950 sm:px-4 sm:py-4 pb-24 md:pb-4" id="messagesContainer">
        <div class="mx-auto max-w-4xl">
            @if($messages->count() > 0)
                @foreach($messages as $message)
                    <div class="mb-1.5 flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                        <div class="relative min-w-0 max-w-[75%] {{ $message->sender_id === Auth::id() ? 'bg-vinted-primary-100/70 dark:bg-vinted-primary-500/20' : 'bg-white dark:bg-gray-800' }} rounded-xl {{ $message->sender_id === Auth::id() ? 'rounded-br-sm' : 'rounded-bl-sm' }} px-3 py-2 shadow-sm dark:shadow-none sm:max-w-xs sm:px-3.5 sm:py-2.5 lg:max-w-md xl:max-w-lg animate-fade-in">
                            @if($message->subject)
                                <div class="mb-2 flex items-center gap-1.5 rounded-md bg-black/5 px-2.5 py-1.5 text-xs font-semibold text-gray-700 dark:bg-white/10 dark:text-gray-300">
                                    <i class="fas fa-tag text-vinted-primary-500"></i>
                                    {{ $message->subject }}
                                </div>
                            @endif

                            @if($message->content)
                                <div class="mb-1 text-sm leading-relaxed text-gray-900 dark:text-white">
                                    {!! nl2br(e($message->content)) !!}
                                </div>
                            @endif

                            @if($message->type === 'audio' && $message->attachment)
                                <div class="my-1 voice-msg" data-src="{{ Storage::url($message->attachment) }}" data-duration="{{ $message->duration ?? 0 }}">
                                    <div class="flex items-center gap-2 min-w-48">
                                        <button type="button" class="voice-play-btn flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-vinted-primary-600 text-white shadow-sm transition-all hover:bg-vinted-primary-700 active:scale-95">
                                            <i class="fas fa-play text-xs ml-0.5"></i>
                                        </button>
                                        <div class="flex-1 min-w-0">
                                            <div class="voice-progress h-1 overflow-hidden rounded-full bg-black/10 dark:bg-white/10">
                                                <div class="voice-progress-fill h-full rounded-full bg-vinted-primary-600" style="width: 0%"></div>
                                            </div>
                                        </div>
                                        <span class="voice-time flex-shrink-0 text-xs tabular-nums text-gray-500 dark:text-gray-400">{{ gmdate('i:s', intval($message->duration ?? 0)) }}</span>
                                        <i class="fas fa-microphone flex-shrink-0 text-xs text-vinted-primary-600 opacity-60 dark:text-vinted-primary-400"></i>
                                    </div>
                                </div>
                            @elseif($message->attachment)
                                <div class="my-2">
                                    @if(Str::startsWith($message->attachment, 'items/') || in_array(pathinfo($message->attachment, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                        <img src="{{ Storage::url($message->attachment) }}"
                                             alt="Image jointe"
                                             class="max-w-48 max-h-48 cursor-pointer rounded-xl shadow-sm transition-transform hover:scale-[1.02]"
                                             onclick="showImageModal('{{ Storage::url($message->attachment) }}')">
                                    @else
                                        <a href="{{ Storage::url($message->attachment) }}"
                                           target="_blank"
                                           class="flex items-center gap-2 rounded-lg bg-black/5 p-3 text-vinted-primary-600 transition-colors hover:bg-black/10 dark:bg-white/5 dark:text-vinted-primary-400 dark:hover:bg-white/10">
                                            <div class="flex h-9 w-9 items-center justify-center rounded-md bg-vinted-primary-100 dark:bg-vinted-primary-500/20">
                                                <i class="fas fa-file text-vinted-primary-600 dark:text-vinted-primary-400"></i>
                                            </div>
                                            <span class="text-sm font-medium">Fichier joint</span>
                                            <i class="fas fa-download ml-auto text-xs opacity-50"></i>
                                        </a>
                                    @endif
                                </div>
                            @endif

                            <div class="mt-1 flex items-center justify-end gap-1 text-xs text-gray-500 dark:text-gray-400">
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
                <div class="py-20 text-center text-gray-500 dark:text-gray-400">
                    <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                        <i class="fas fa-comments text-3xl text-gray-300 dark:text-gray-600"></i>
                    </div>
                    <p class="text-lg font-medium text-gray-700 dark:text-gray-300">Démarrez la conversation</p>
                    <p class="mt-1 text-sm text-gray-400">Envoyez un message à {{ $otherUser->name }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Zone de saisie -->
    <div class="fixed bottom-0 left-0 right-0 z-40 border-t border-gray-200 bg-white/80 p-3 backdrop-blur-lg dark:border-gray-700/50 dark:bg-gray-900/80 md:relative md:pb-3">
        <form id="messageForm" method="POST" action="{{ route('messages.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="recipient_id" value="{{ $otherUser->id }}">
            <input type="hidden" name="type" id="messageType" value="text">
            <input type="hidden" name="duration" id="messageDuration" value="">

            <!-- Mode normal (texte/pièces jointes) -->
            <div id="normalInput" class="flex items-end gap-1.5 rounded-lg border border-gray-200 bg-white px-2 py-1.5 transition-all dark:border-gray-700 dark:bg-gray-800">
                <button type="button" class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-md text-gray-400 hover:bg-gray-100 hover:text-vinted-primary-600 dark:hover:bg-gray-700 dark:hover:text-vinted-primary-400" onclick="document.getElementById('attachmentInput').click()">
                    <i class="fas fa-paperclip text-lg"></i>
                </button>

                <div class="relative min-w-0 flex-1">
                    <textarea name="content"
                              class="min-h-5 max-h-24 w-full resize-none border-0 bg-transparent py-2 text-gray-900 outline-none placeholder-gray-500 dark:text-white"
                              placeholder="Tapez un message..."
                              rows="1"
                              id="messageContent"></textarea>

                    <input type="file"
                           name="attachment"
                           id="attachmentInput"
                           class="hidden"
                           accept="image/*,.pdf,.doc,.docx">
                </div>

                <button type="submit" id="sendBtn" class="flex h-10 min-w-10 flex-shrink-0 items-center justify-center rounded-md bg-vinted-primary-600 text-white shadow-sm transition-colors hover:bg-vinted-primary-700">
                    <i class="fas fa-paper-plane text-sm"></i>
                </button>
            </div>

            <!-- Mode enregistrement vocal -->
            <div id="recordingInput" class="hidden items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-1.5 transition-all dark:border-gray-700 dark:bg-gray-800">
                <button type="button" id="cancelRecordBtn" class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-md text-gray-500 hover:bg-gray-100 hover:text-red-500 dark:hover:bg-gray-700" title="Annuler">
                    <i class="fas fa-arrow-left text-lg"></i>
                </button>
                <div class="flex min-w-0 flex-1 items-center gap-2">
                    <span class="h-2.5 w-2.5 flex-shrink-0 animate-pulse rounded-full bg-red-500" id="recordDot"></span>
                    <span id="recordingTimer" class="text-sm font-medium tabular-nums text-gray-900 dark:text-white">0:00</span>
                    <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">Appuyez pour arrêter</span>
                </div>
                <button type="button" id="stopRecordBtn" class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-red-500 text-white shadow-sm transition-colors hover:bg-red-600" title="Arrêter">
                    <i class="fas fa-stop text-sm"></i>
                </button>
            </div>

            <div id="attachmentPreview" class="mt-2 hidden">
                <div class="flex items-center gap-3 rounded-md border border-vinted-primary-200 bg-vinted-primary-50 p-2.5 text-vinted-primary-700 dark:border-vinted-primary-500/30 dark:bg-vinted-primary-500/10 dark:text-vinted-primary-300">
                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-vinted-primary-100 dark:bg-vinted-primary-500/20">
                        <i class="fas fa-paperclip text-sm"></i>
                    </div>
                    <span id="attachmentName" class="flex-1 truncate text-sm font-medium"></span>
                    <button type="button" onclick="removeAttachment()" class="rounded-full p-1.5 text-red-500 transition-colors hover:bg-red-100 hover:text-red-700 dark:hover:bg-red-900/30">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal pour l'affichage des images -->
<div class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-sm transition-opacity" id="imageModal" onclick="closeImageModal()">
    <div class="relative max-h-full max-w-4xl p-4">
        <button onclick="closeImageModal()" class="absolute -top-2 -right-2 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-black/60 text-white shadow-lg transition-colors hover:bg-black/80">
            <i class="fas fa-times text-lg"></i>
        </button>
        <img id="modalImage" src="" alt="Image" class="max-h-[85vh] w-full rounded-xl object-contain shadow-2xl">
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

#messagesContainer::-webkit-scrollbar { width: 5px; height: 5px; }
#messagesContainer::-webkit-scrollbar-track { background: transparent; }
#messagesContainer::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 10px; }
#messagesContainer::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.25); }

.rate-option-selected {
    border-color: var(--color-primary-400, #c7d2fe) !important;
    background-color: var(--color-primary-50, #eef2ff) !important;
    box-shadow: inset 0 0 0 1px var(--color-primary-200, #c7d2fe);
}

.dark .rate-option-selected {
    background-color: rgba(129, 140, 248, 0.15) !important;
}

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
        messageBubble.className = `max-w-xs lg:max-w-md xl:max-w-lg min-w-0 relative ${isAuthor ? 'bg-vinted-primary-100/70 dark:bg-vinted-primary-500/20' : 'bg-white dark:bg-gray-800'} rounded-xl px-3.5 py-2.5 shadow-sm animate-fade-in`;
        
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
                    <button type="button" class="voice-play-btn w-9 h-9 rounded-full bg-vinted-primary-600 hover:bg-vinted-primary-700 text-white flex items-center justify-center flex-shrink-0 transition-all active:scale-95 shadow-sm">
                        <i class="fas fa-play text-xs ml-0.5"></i>
                    </button>
                    <div class="flex-1 min-w-0">
                        <div class="voice-progress h-1 bg-gray-300 dark:bg-gray-600 rounded-full overflow-hidden">
                            <div class="voice-progress-fill h-full bg-vinted-primary-600 rounded-full" style="width: 0%"></div>
                        </div>
                    </div>
                    <span class="voice-time text-xs text-gray-500 dark:text-gray-400 tabular-nums flex-shrink-0">${mins}:${secs.toString().padStart(2, '0')}</span>
                    <i class="fas fa-microphone text-vinted-primary-600 dark:text-vinted-primary-400 text-xs flex-shrink-0 opacity-60"></i>
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
                link.className = 'flex items-center gap-2 bg-black/5 dark:bg-white/5 rounded-xl p-3 text-vinted-primary-600 dark:text-vinted-primary-400 hover:bg-black/10 dark:hover:bg-white/10 transition-colors';
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
    normalMicBtn.className = 'text-gray-400 hover:text-vinted-primary-600 dark:hover:text-vinted-primary-400 p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex-shrink-0';
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
            r.classList.remove('rate-option-selected');
        });
        this.classList.add('rate-option-selected');
        selectedRate = this.dataset.rate;
        selectedPrice = this.querySelector('.leading-tight')?.textContent?.trim() || null;

        const btn = document.getElementById('applyDiscountBtn');
        btn.disabled = false;
        btn.className = 'w-full py-2.5 sm:py-3 rounded-md font-semibold text-sm sm:text-base transition-colors bg-vinted-primary-600 text-white hover:bg-vinted-primary-700';
    });
});

function submitDiscount() {
    if (!selectedRate) return;
    const btn = document.getElementById('applyDiscountBtn');
    const form = document.getElementById('discountForm');
    const result = document.getElementById('discountResult');

    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>';
    btn.className = 'w-full py-2.5 sm:py-3 rounded-md font-semibold text-sm sm:text-base transition-colors bg-vinted-primary-600 text-white';

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
            btn.className = 'w-full py-2.5 sm:py-3 rounded-md font-semibold text-sm sm:text-base bg-emerald-500 text-white cursor-default';
            document.querySelectorAll('.rate-option').forEach(r => r.style.pointerEvents = 'none');
        } else {
            result.className = 'mt-2 text-center text-sm font-medium text-red-500';
            result.textContent = '✗ ' + (data.error || 'Erreur');
            btn.disabled = false;
            btn.innerHTML = 'Appliquer la reduction';
            btn.className = 'w-full py-2.5 sm:py-3 rounded-md font-semibold text-sm sm:text-base bg-vinted-primary-600 hover:bg-vinted-primary-700 text-white transition-colors';
        }
        result.classList.remove('hidden');
    })
    .catch(e => {
        result.className = 'mt-2 text-center text-sm font-medium text-red-500';
        result.textContent = '✗ Erreur de connexion';
        result.classList.remove('hidden');
        btn.disabled = false;
        btn.innerHTML = 'Appliquer la reduction';
        btn.className = 'w-full py-2.5 sm:py-3 rounded-md font-semibold text-sm sm:text-base bg-vinted-primary-600 hover:bg-vinted-primary-700 text-white transition-colors';
    });
}

// Nettoyage au depart
window.addEventListener('beforeunload', function() {
    document.body.classList.remove('overflow-hidden');
});
</script>
@endpush