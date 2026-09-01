@extends('app')

@section('title', 'Conversation - ' . $supportChat->reference)

@section('content')
@php
    $statusVariant = match ($supportChat->status) {
        'open' => 'danger',
        'in_progress' => 'warning',
        'waiting_user' => 'info',
        'waiting_admin' => 'secondary',
        'closed' => 'success',
        default => 'secondary',
    };
    $priorityVariant = match ($supportChat->priority) {
        'urgent' => 'danger',
        'high' => 'warning',
        'normal' => 'info',
        'low' => 'secondary',
        default => 'secondary',
    };
@endphp

<div class="bg-gray-50 dark:bg-gray-950 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <!-- En-tête -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('support.index') }}"
                   class="inline-flex items-center justify-center w-10 h-10 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $supportChat->reference }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ $supportChat->subject ?: 'Assistance' }}
                        <span class="mx-1">•</span>
                        <x-badge variant="{{ $statusVariant }}">{{ $supportChat->formatted_status }}</x-badge>
                    </p>
                </div>
            </div>

            @if($supportChat->status !== 'closed')
                <x-button-outline tone="danger" onclick="document.getElementById('closeModal').classList.remove('hidden')" class="pointer-events-auto">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Fermer
                </x-button-outline>
            @endif
        </div>

        <!-- Messages flash -->
        @if(session('success'))
            <x-alert variant="success" class="mb-6" x-data="{ show: true }" x-show="show" x-transition>
                <div class="flex items-start gap-2">
                    <p class="flex-1">{{ session('success') }}</p>
                    <button @click="show = false" class="flex-shrink-0 hover:opacity-70"><i class="fas fa-times text-sm"></i></button>
                </div>
            </x-alert>
        @endif

        @if(session('error'))
            <x-alert variant="danger" class="mb-6" x-data="{ show: true }" x-show="show" x-transition>
                <div class="flex items-start gap-2">
                    <p class="flex-1">{{ session('error') }}</p>
                    <button @click="show = false" class="flex-shrink-0 hover:opacity-70"><i class="fas fa-times text-sm"></i></button>
                </div>
            </x-alert>
        @endif

        <!-- Informations de la conversation -->
        <x-card class="p-6 mb-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Catégorie</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ ucfirst($supportChat->category) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Priorité</p>
                    <x-badge variant="{{ $priorityVariant }}">{{ ucfirst($supportChat->priority) }}</x-badge>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Créée le</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $supportChat->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                @if($supportChat->admin)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Assigné à</p>
                        <div class="flex items-center gap-2">
                            @if($supportChat->admin->avatar)
                                <img class="w-6 h-6 rounded-full object-cover"
                                     src="{{ asset('storage/' . $supportChat->admin->avatar) }}"
                                     alt="{{ $supportChat->admin->name }}">
                            @else
                                <div class="w-6 h-6 rounded-full bg-vinted-primary-100 dark:bg-vinted-primary-500/20 flex items-center justify-center">
                                    <span class="text-xs font-semibold text-vinted-primary-700 dark:text-vinted-primary-300">{{ substr($supportChat->admin->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $supportChat->admin->name }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </x-card>

        <!-- Messages -->
        <x-card class="mb-6 overflow-hidden">
            <div class="border-b border-gray-200 dark:border-gray-700/50 px-6 py-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-vinted-primary-600 dark:text-vinted-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <h5 class="font-semibold text-gray-900 dark:text-white">Conversation</h5>
            </div>
            <div id="messagesContainer" class="max-h-[600px] overflow-y-auto">
                @forelse($supportChat->messages as $message)
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700/50 last:border-0 {{ $message->is_admin ? 'bg-vinted-primary-50/50 dark:bg-vinted-primary-500/5' : '' }}">
                        <div class="flex items-start gap-4">
                            <!-- Avatar -->
                            <div class="flex-shrink-0">
                                @if($message->user->avatar)
                                    <img class="w-10 h-10 rounded-full object-cover ring-2 {{ $message->is_admin ? 'ring-vinted-primary-200 dark:ring-vinted-primary-500/40' : 'ring-gray-200 dark:ring-gray-600' }}"
                                         src="{{ asset('storage/' . $message->user->avatar) }}"
                                         alt="{{ $message->user->name }}">
                                @else
                                    <div class="w-10 h-10 rounded-full {{ $message->is_admin ? 'bg-vinted-primary-600' : 'bg-gray-400 dark:bg-gray-600' }} flex items-center justify-center ring-2 {{ $message->is_admin ? 'ring-vinted-primary-200 dark:ring-vinted-primary-500/40' : 'ring-gray-200 dark:ring-gray-600' }}">
                                        <span class="text-base font-semibold text-white">{{ substr($message->user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Contenu -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="font-semibold text-gray-900 dark:text-white text-sm">{{ $message->user->name }}</span>
                                    @if($message->is_admin)
                                        <x-badge variant="soft-primary">Support</x-badge>
                                    @endif
                                    <span class="text-sm text-gray-500">{{ $message->created_at->format('d/m/Y à H:i') }}</span>
                                </div>

                                <div class="prose prose-sm max-w-none">
                                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $message->message }}</p>
                                </div>

                                <!-- Pièces jointes -->
                                @if($message->attachments && is_array($message->attachments))
                                    <div class="mt-4">
                                        <p class="font-medium text-gray-900 dark:text-white mb-2 flex items-center gap-2 text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                            Pièces jointes:
                                        </p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($message->attachments as $attachment)
                                                <a href="{{ Storage::url($attachment['path']) }}"
                                                   target="_blank"
                                                   class="inline-flex items-center gap-2 px-3 py-2 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-sm">
                                                    <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $attachment['name'] }}</span>
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
                        <div class="w-16 h-16 mx-auto rounded-full bg-gray-100 dark:bg-gray-700/40 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">Aucun message dans cette conversation</p>
                    </div>
                @endforelse
            </div>
        </x-card>

        <!-- Formulaire de réponse -->
        @if($supportChat->status !== 'closed')
            <x-card class="overflow-hidden">
                <div class="border-b border-gray-200 dark:border-gray-700/50 px-6 py-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-vinted-primary-600 dark:text-vinted-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    <h5 class="font-semibold text-gray-900 dark:text-white">Répondre</h5>
                </div>
                <div class="p-6">
                    <form action="{{ route('support.reply', $supportChat) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div>
                            <x-label icon="fas fa-comment-dots">Votre réponse <span class="text-red-500">*</span></x-label>
                            <x-textarea id="message"
                                        name="message"
                                        rows="6"
                                        maxlength="5000"
                                        required
                                        placeholder="Écrivez votre réponse..."
                                        class="resize-none @error('message') border-red-300 @enderror">{{ old('message') }}</x-textarea>
                            @error('message')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <div class="flex justify-end mt-2">
                                <span class="text-sm text-gray-500">
                                    <span id="replyCharCount">0</span>/5000
                                </span>
                            </div>
                        </div>

                        <div>
                            <x-label icon="fas fa-paperclip">Pièces jointes <span class="text-gray-400 font-normal">(optionnel)</span></x-label>
                            <input type="file"
                                   id="attachments"
                                   name="attachments[]"
                                   multiple
                                   accept="image/*,.pdf,.doc,.docx,.txt"
                                   class="w-full cursor-pointer rounded-md border border-dashed border-gray-300 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 outline-none transition-colors file:mr-4 file:rounded-md file:border-0 file:bg-vinted-primary-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-vinted-primary-700 hover:border-vinted-primary-400 hover:file:bg-vinted-primary-100 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:file:bg-vinted-primary-500/20 dark:file:text-vinted-primary-300 @error('attachments.*') border-red-300 @enderror">
                            @error('attachments.*')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                <i class="fas fa-circle-info text-xs"></i>
                                Maximum 5 MB par fichier
                            </p>
                        </div>

                        <div class="flex justify-end">
                            <x-button-primary type="submit">
                                <i class="fas fa-paper-plane mr-2"></i> Envoyer
                            </x-button-primary>
                        </div>
                    </form>
                </div>
            </x-card>
        @else
            <div class="rounded-xl border border-amber-200 dark:border-amber-500/20 bg-amber-50 dark:bg-amber-500/5 p-5">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-amber-900 dark:text-amber-300 font-semibold mb-1">Cette conversation est fermée</p>
                        <p class="text-amber-800 dark:text-amber-200/90 text-sm">
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
<div id="closeModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Fermer la conversation</h3>
                <button onclick="document.getElementById('closeModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-6">
            <p class="text-gray-700 dark:text-gray-300 mb-2 text-sm">Êtes-vous sûr de vouloir fermer cette conversation ?</p>
            <div class="flex items-start gap-2 p-4 rounded-lg bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm text-blue-800 dark:text-blue-300">
                    Une fois fermée, vous ne pourrez plus ajouter de messages. Vous pourrez toujours créer une nouvelle demande si nécessaire.
                </p>
            </div>
        </div>
        <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-b-xl flex gap-3">
            <button onclick="document.getElementById('closeModal').classList.add('hidden')"
                    class="flex-1 h-10 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium text-sm hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                Annuler
            </button>
            <form action="{{ route('support.close', $supportChat) }}" method="POST" class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full h-10 rounded-md bg-vinted-danger-500 text-white font-medium text-sm hover:bg-vinted-danger-600 transition-colors">
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
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Envoi...';
            }
        });
    }
});
</script>
@endpush
@endsection