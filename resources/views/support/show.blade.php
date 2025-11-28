@extends('app')

@section('title', 'Conversation - ' . $supportChat->reference)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <!-- En-tête -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('support.index') }}" 
                   class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-white shadow-md hover:shadow-lg hover:scale-105 transition-all duration-200 group">
                    <svg class="w-5 h-5 text-gray-600 group-hover:text-purple-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $supportChat->reference }}</h1>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ $supportChat->subject ?: 'Assistance' }} • 
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                            {{ $supportChat->status === 'open' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $supportChat->status === 'in_progress' ? 'bg-amber-100 text-amber-800' : '' }}
                            {{ $supportChat->status === 'waiting_user' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $supportChat->status === 'waiting_admin' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $supportChat->status === 'closed' ? 'bg-gray-800 text-white' : '' }}">
                            {{ $supportChat->formatted_status }}
                        </span>
                    </p>
                </div>
            </div>

            @if($supportChat->status !== 'closed')
                <button type="button" 
                        onclick="document.getElementById('closeModal').classList.remove('hidden')"
                        class="inline-flex items-center px-4 py-2 rounded-xl border-2 border-red-300 text-red-700 font-semibold hover:bg-red-50 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Fermer
                </button>
            @endif
        </div>

        <!-- Messages flash -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 flex items-start gap-3" x-data="{ show: true }" x-show="show" x-transition>
                <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-green-800 flex-1">{{ session('success') }}</p>
                <button @click="show = false" class="text-green-600 hover:text-green-800">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 flex items-start gap-3" x-data="{ show: true }" x-show="show" x-transition>
                <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="text-red-800 flex-1">{{ session('error') }}</p>
                <button @click="show = false" class="text-red-600 hover:text-red-800">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        @endif

        <!-- Informations de la conversation -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Catégorie</p>
                    <p class="font-semibold text-gray-900">{{ ucfirst($supportChat->category) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Priorité</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                        {{ $supportChat->priority === 'urgent' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $supportChat->priority === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                        {{ $supportChat->priority === 'normal' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $supportChat->priority === 'low' ? 'bg-gray-100 text-gray-800' : '' }}">
                        {{ ucfirst($supportChat->priority) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Créée le</p>
                    <p class="font-semibold text-gray-900">{{ $supportChat->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                @if($supportChat->admin)
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Assigné à</p>
                        <div class="flex items-center gap-2">
                            @if($supportChat->admin->avatar)
                                <img class="w-6 h-6 rounded-full object-cover" 
                                     src="{{ asset('storage/' . $supportChat->admin->avatar) }}" 
                                     alt="{{ $supportChat->admin->name }}">
                            @else
                                <div class="w-6 h-6 rounded-full bg-gradient-to-br from-purple-400 to-blue-500 flex items-center justify-center">
                                    <span class="text-xs font-bold text-white">{{ substr($supportChat->admin->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <p class="font-semibold text-gray-900">{{ $supportChat->admin->name }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Messages -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 mb-6 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-50 to-blue-50 px-6 py-4 border-b border-gray-200">
                <h5 class="font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Conversation
                </h5>
            </div>
            <div id="messagesContainer" class="max-h-[600px] overflow-y-auto">
                @forelse($supportChat->messages as $message)
                    <div class="p-6 border-b border-gray-100 last:border-0 {{ $message->is_admin ? 'bg-purple-50/30' : 'bg-white' }} hover:bg-gray-50 transition-colors">
                        <div class="flex items-start gap-4">
                            <!-- Avatar -->
                            <div class="flex-shrink-0">
                                @if($message->user->avatar)
                                    <img class="w-12 h-12 rounded-full object-cover ring-2 {{ $message->is_admin ? 'ring-purple-300' : 'ring-gray-200' }}" 
                                         src="{{ asset('storage/' . $message->user->avatar) }}" 
                                         alt="{{ $message->user->name }}">
                                @else
                                    <div class="w-12 h-12 rounded-full {{ $message->is_admin ? 'bg-gradient-to-br from-purple-500 to-blue-600' : 'bg-gradient-to-br from-gray-400 to-gray-600' }} flex items-center justify-center ring-2 {{ $message->is_admin ? 'ring-purple-300' : 'ring-gray-200' }}">
                                        <span class="text-lg font-bold text-white">{{ substr($message->user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Contenu -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="font-semibold text-gray-900">{{ $message->user->name }}</span>
                                    @if($message->is_admin)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-600 text-white">
                                            Support
                                        </span>
                                    @endif
                                    <span class="text-sm text-gray-500">{{ $message->created_at->format('d/m/Y à H:i') }}</span>
                                </div>

                                <div class="prose prose-sm max-w-none">
                                    <p class="text-gray-700 whitespace-pre-wrap">{{ $message->message }}</p>
                                </div>

                                <!-- Pièces jointes -->
                                @if($message->attachments && is_array($message->attachments))
                                    <div class="mt-4">
                                        <p class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                            Pièces jointes:
                                        </p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($message->attachments as $attachment)
                                                <a href="{{ Storage::url($attachment['path']) }}" 
                                                   target="_blank"
                                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                                                    <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="text-sm font-medium text-gray-700">{{ $attachment['name'] }}</span>
                                                    <span class="text-xs text-gray-500">({{ number_format($attachment['size'] / 1024, 2) }} KB)</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 px-4">
                        <svg class="mx-auto w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="text-gray-500">Aucun message dans cette conversation</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Formulaire de réponse -->
        @if($supportChat->status !== 'closed')
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-50 to-blue-50 px-6 py-4 border-b border-gray-200">
                    <h5 class="font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        Répondre
                    </h5>
                </div>
                <div class="p-6">
                    <form action="{{ route('support.reply', $supportChat) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div>
                            <label for="message" class="block text-sm font-semibold text-gray-900 mb-2">
                                Votre réponse <span class="text-red-500">*</span>
                            </label>
                            <textarea id="message" 
                                      name="message" 
                                      rows="6" 
                                      maxlength="5000"
                                      required
                                      placeholder="Écrivez votre réponse..."
                                      class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 outline-none resize-none @error('message') border-red-300 @enderror">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <div class="flex justify-end mt-2">
                                <span class="text-sm text-gray-500">
                                    <span id="replyCharCount">0</span>/5000
                                </span>
                            </div>
                        </div>

                        <div>
                            <label for="attachments" class="block text-sm font-semibold text-gray-900 mb-2">
                                Pièces jointes <span class="text-gray-400 font-normal">(optionnel)</span>
                            </label>
                            <input type="file" 
                                   id="attachments" 
                                   name="attachments[]" 
                                   multiple
                                   accept="image/*,.pdf,.doc,.docx,.txt"
                                   class="w-full px-4 py-3 rounded-xl border-2 border-dashed border-gray-300 hover:border-purple-400 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 @error('attachments.*') border-red-300 @enderror">
                            @error('attachments.*')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                Maximum 5 MB par fichier
                            </p>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center justify-center px-8 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold hover:from-purple-700 hover:to-blue-700 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                Envoyer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <div class="p-6 rounded-2xl bg-amber-50 border border-amber-200">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-amber-900 font-semibold mb-1">Cette conversation est fermée</p>
                        <p class="text-amber-800 text-sm">
                            Vous ne pouvez plus y répondre.
                            <a href="{{ route('support.create') }}" class="font-semibold underline hover:no-underline">Créer une nouvelle demande</a>
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal de fermeture -->
<div id="closeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Fermer la conversation</h3>
                <button onclick="document.getElementById('closeModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-6">
            <p class="text-gray-700 mb-2">Êtes-vous sûr de vouloir fermer cette conversation ?</p>
            <div class="flex items-start gap-2 p-4 rounded-xl bg-blue-50 border border-blue-200">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm text-blue-800">
                    Une fois fermée, vous ne pourrez plus ajouter de messages. Vous pourrez toujours créer une nouvelle demande si nécessaire.
                </p>
            </div>
        </div>
        <div class="p-6 bg-gray-50 rounded-b-2xl flex gap-3">
            <button onclick="document.getElementById('closeModal').classList.add('hidden')" 
                    class="flex-1 px-4 py-2 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-100 transition-all duration-200">
                Annuler
            </button>
            <form action="{{ route('support.close', $supportChat) }}" method="POST" class="flex-1">
                @csrf
                <button type="submit" 
                        class="w-full px-4 py-2 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 transition-all duration-200">
                    Fermer la conversation
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messagesContainer');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    const messageTextarea = document.getElementById('message');
    const charCount = document.getElementById('replyCharCount');
    
    if (messageTextarea && charCount) {
        messageTextarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;
            
            if (length > 4500) {
                charCount.classList.add('text-red-600', 'font-bold');
            } else {
                charCount.classList.remove('text-red-600', 'font-bold');
            }
        });
    }
    
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
    
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Envoi...';
            }
        });
    }
});
</script>
@endpush
@endsection
