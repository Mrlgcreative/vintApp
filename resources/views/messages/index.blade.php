@extends('app')

@section('title', 'Mes conversations')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-950 py-8 md:py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="flex items-start gap-4">
                <x-icon icon="fas fa-comments" size="lg" tone="primary" class="mt-0.5" />
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Messages</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Retrouvez vos conversations avec les vendeurs et acheteurs.</p>
                </div>
            </div>
            <x-button-outline href="{{ route('items.index') }}">
                <i class="fas fa-store mr-2"></i>
                Parcourir les articles
            </x-button-outline>
        </div>

        <div class="mt-8">
            <x-card class="overflow-hidden">
                <!-- Search -->
                <div class="border-b border-gray-100 p-4 dark:border-gray-700/50">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400 dark:text-gray-500"></i>
                        <input type="text" id="searchConversations"
                               placeholder="Rechercher une conversation..."
                               class="w-full rounded-md border border-gray-200 bg-gray-50 py-2 pl-9 pr-3 text-sm text-gray-900 placeholder-gray-400 outline-none transition-colors focus:border-vinted-primary-300 focus:bg-white focus:ring-2 focus:ring-vinted-primary-100 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-vinted-primary-500/50 dark:focus:bg-gray-800 dark:focus:ring-vinted-primary-500/20">
                    </div>
                </div>

                @if($vendorContacts->count() > 0)
                    <!-- Liste des conversations -->
                    <div class="divide-y divide-gray-100 dark:divide-gray-700/50" id="conversationsList">
                        @foreach($vendorContacts as $contact)
                            @if(!$contact->vendor) @continue @endif
                            @php
                                $isUnread = $contact->unread_count > 0;
                                $avatarUrl = $contact->vendor->avatar_url;
                            @endphp
                            <a href="{{ route('messages.show', ['user' => $contact->vendor_id, 'item_id' => $contact->item_id]) }}"
                               class="flex items-center gap-4 px-4 py-3.5 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/20 @if($isUnread) bg-vinted-primary-50/40 dark:bg-vinted-primary-500/5 @endif"
                               data-name="{{ strtolower($contact->vendor->name) }} {{ $contact->item ? strtolower($contact->item->name) : '' }}">

                                <!-- Avatar -->
                                <div class="relative flex-shrink-0">
                                    @if($avatarUrl)
                                        <span class="block h-11 w-11 overflow-hidden rounded-full">
                                            <img src="{{ $avatarUrl }}"
                                                 alt="{{ $contact->vendor->name }}"
                                                 class="h-full w-full object-cover"
                                                 loading="lazy">
                                        </span>
                                    @else
                                        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-vinted-primary-100 text-sm font-semibold text-vinted-primary-700 dark:bg-vinted-primary-500/20 dark:text-vinted-primary-300">
                                            {{ $contact->vendor->initial ?? strtoupper(substr($contact->vendor->name, 0, 1)) }}
                                        </span>
                                    @endif
                                    <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white bg-vinted-success-500 dark:border-gray-800"></span>
                                </div>

                                <!-- Content -->
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-3">
                                        <h3 class="truncate text-sm font-medium {{ $isUnread ? 'text-gray-900 dark:text-white' : 'text-gray-800 dark:text-gray-200' }}">
                                            {{ $contact->vendor->name }}
                                        </h3>
                                        @if($contact->last_message)
                                            <span class="flex-shrink-0 text-xs {{ $isUnread ? 'font-semibold text-gray-600 dark:text-gray-300' : 'text-gray-400 dark:text-gray-500' }}">
                                                {{ $contact->last_message_time }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Dernier message -->
                                    <div class="mt-0.5 flex items-center justify-between gap-2">
                                        <p class="min-w-0 flex-1 truncate text-sm {{ $isUnread ? 'font-medium text-gray-700 dark:text-gray-300' : 'text-gray-500 dark:text-gray-400' }}">
                                            @if($contact->last_message)
                                                @if($contact->last_message->sender_id === Auth::id())
                                                    <span class="text-vinted-primary-600 dark:text-vinted-primary-400">Vous : </span>
                                                @endif
                                                {{ $contact->last_message->content ?: 'Fichier joint' }}
                                            @else
                                                <span class="italic">Nouveau contact</span>
                                            @endif
                                        </p>
                                        @if($isUnread)
                                            <span class="flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-vinted-primary-600 px-1.5 text-[10px] font-semibold text-white">
                                                {{ $contact->unread_count > 9 ? '9+' : $contact->unread_count }}
                                            </span>
                                        @endif
                                    </div>

                                    @if($contact->item)
                                        <div class="mt-1.5 flex items-center gap-1.5">
                                            <span class="truncate text-xs font-medium text-vinted-primary-600 dark:text-vinted-primary-400">{{ $contact->item->name }}</span>
                                            <span class="text-xs font-semibold text-vinted-primary-700 dark:text-vinted-primary-300">{{ $contact->item->formatted_price }}</span>
                                            @if($contact->has_discount)
                                                <x-badge variant="soft-success">
                                                    <i class="fas fa-tag mr-1"></i> Offre
                                                </x-badge>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <!-- État vide -->
                    <div class="px-6 py-20 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-vinted-primary-50 dark:bg-vinted-primary-500/20">
                            <i class="fas fa-comments text-2xl text-vinted-primary-500 dark:text-vinted-primary-300"></i>
                        </div>
                        <h3 class="mt-6 text-lg font-semibold text-gray-900 dark:text-white">Aucune conversation</h3>
                        <p class="mx-auto mt-2 max-w-sm text-sm text-gray-500 dark:text-gray-400">
                            Contactez un vendeur depuis un article pour démarrer une conversation.
                        </p>
                        <x-button-primary class="mt-6" href="{{ route('items.index') }}">
                            <i class="fas fa-store mr-2"></i>
                            Parcourir les articles
                        </x-button-primary>
                    </div>
                @endif
            </x-card>

            <!-- Footer info -->
            <p class="mt-4 text-center text-xs text-gray-400 dark:text-gray-500">
                <i class="fas fa-lock mr-1"></i>
                Messages chiffrés de bout en bout
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchConversations');
    const conversationsList = document.getElementById('conversationsList');

    if (searchInput && conversationsList) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const items = conversationsList.querySelectorAll('a');

            items.forEach(item => {
                const name = item.getAttribute('data-name') || '';
                item.style.display = name.includes(query) ? 'flex' : 'none';
            });
        });
    }
});
</script>
@endpush