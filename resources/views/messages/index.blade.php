@extends('app')

@section('title', 'Mes conversations')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- En-tete style WhatsApp -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-5 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('dashboard') }}" class="text-white/80 hover:text-white p-1.5 rounded-full hover:bg-white/10 transition-colors">
                            <i class="fas fa-arrow-left text-lg"></i>
                        </a>
                        <h1 class="text-lg font-bold text-white">Conversations</h1>
                    </div>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('items.index') }}" class="flex items-center gap-2 px-3 py-1.5 bg-white/15 text-white text-sm rounded-xl hover:bg-white/25 transition-colors">
                            <i class="fas fa-store"></i>
                            <span class="hidden sm:inline">Parcourir</span>
                        </a>
                    </div>
                </div>
                <!-- Search bar -->
                <div class="mt-3 relative">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-white/60 text-sm"></i>
                    <input type="text" id="searchConversations" placeholder="Rechercher ou demarrer une conversation..."
                           class="w-full bg-white/15 text-white placeholder-white/60 rounded-xl pl-10 pr-4 py-2.5 text-sm outline-none focus:bg-white/20 focus:ring-2 focus:ring-white/30 transition-all">
                </div>
            </div>

            @if($vendorContacts->count() > 0)
                <!-- Liste des conversations -->
                <div class="divide-y divide-gray-100 dark:divide-gray-700/50 max-h-[600px] overflow-y-auto" id="conversationsList">
                    @foreach($vendorContacts as $contact)
                        @if(!$contact->vendor) @continue @endif
                        <a href="{{ route('messages.show', ['user' => $contact->vendor_id, 'item_id' => $contact->item_id]) }}"
                           class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group @if($contact->unread_count > 0) bg-primary-50/50 dark:bg-primary-900/10 @endif"
                           data-name="{{ strtolower($contact->vendor->name) }} {{ $contact->item ? strtolower($contact->item->name) : '' }}">

                            <!-- Avatar -->
                            <div class="relative flex-shrink-0">
                                @if($contact->vendor->avatar_url)
                                    <img src="{{ $contact->vendor->avatar_url }}"
                                         alt="{{ $contact->vendor->name }}"
                                         class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($contact->vendor->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 border-2 border-white dark:border-gray-800 rounded-full"></div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-0.5">
                                    <h3 class="font-semibold {{ $contact->unread_count > 0 ? 'text-gray-900 dark:text-white' : 'text-gray-800 dark:text-gray-200' }} truncate">
                                        {{ $contact->vendor->name }}
                                    </h3>
                                    <span class="text-xs {{ $contact->unread_count > 0 ? 'text-gray-600 dark:text-gray-400 font-medium' : 'text-gray-400 dark:text-gray-500' }} flex-shrink-0 ml-2">
                                        {{ $contact->last_message ? $contact->last_message_time : $contact->contact_date->diffForHumans() }}
                                    </span>
                                </div>

                                <!-- Article + dernier message -->
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm {{ $contact->unread_count > 0 ? 'text-gray-700 dark:text-gray-300 font-medium' : 'text-gray-500 dark:text-gray-400' }} truncate flex-1">
                                        @if($contact->last_message)
                                            @if($contact->last_message->sender_id === Auth::id())
                                                <span class="text-primary-600 dark:text-primary-400">Vous : </span>
                                            @endif
                                            {{ $contact->last_message->content ?: 'Fichier joint' }}
                                        @else
                                            <span class="italic">Nouveau contact</span>
                                        @endif
                                    </p>
                                    @if($contact->unread_count > 0)
                                        <span class="w-5 h-5 bg-primary-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center flex-shrink-0 shadow-sm">
                                            {{ $contact->unread_count > 9 ? '9+' : $contact->unread_count }}
                                        </span>
                                    @endif
                                </div>

                                @if($contact->item)
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <span class="text-xs text-blue-600 dark:text-blue-400 font-medium truncate">{{ $contact->item->name }}</span>
                                        <span class="text-xs text-blue-600 dark:text-blue-400 font-bold">{{ $contact->item->formatted_price }}</span>
                                        @if($contact->has_discount)
                                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[10px] font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full">
                                                <i class="fas fa-tag"></i>
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <!-- Etat vide style WhatsApp -->
                <div class="text-center py-20 px-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900/30 dark:to-purple-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-comments text-3xl text-blue-400 dark:text-blue-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Aucune conversation</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-sm mx-auto">
                        Contactez un vendeur depuis un article pour demarrer une conversation.
                    </p>
                    <a href="{{ route('items.index') }}"
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-800 shadow-lg hover:shadow-xl transition-all">
                        <i class="fas fa-shopping-bag mr-2"></i>
                        Parcourir les articles
                    </a>
                </div>
            @endif
        </div>

        <!-- Footer info -->
        <div class="mt-4 text-center">
            <p class="text-xs text-gray-400 dark:text-gray-500">
                <i class="fas fa-lock mr-1"></i>
                Messages chiffres de bout en bout
            </p>
        </div>
    </div>
</div>

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
                if (name.includes(query)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endpush

@push('styles')
<style>
#conversationsList::-webkit-scrollbar {
    width: 4px;
}
#conversationsList::-webkit-scrollbar-track {
    background: transparent;
}
#conversationsList::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.1);
    border-radius: 10px;
}
#conversationsList::-webkit-scrollbar-thumb:hover {
    background: rgba(0,0,0,0.2);
}
@media (prefers-color-scheme: dark) {
    #conversationsList::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.1);
    }
    #conversationsList::-webkit-scrollbar-thumb:hover {
        background: rgba(255,255,255,0.2);
    }
}
</style>
@endpush
@endsection
