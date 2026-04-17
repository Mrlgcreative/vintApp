@extends('layouts.agent')

@section('title', $supportChat->reference . ' - Conversation')

@section('content')
<div>
    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-5 mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('agent.tickets') }}" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">{{ $supportChat->subject }}</h1>
                        @php
                            $statusColors = [
                                'open' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                'in_progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                'waiting_user' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                'closed' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400',
                            ];
                            $statusLabels = ['open' => 'Ouvert', 'in_progress' => 'En cours', 'waiting_user' => 'Attente', 'closed' => 'Fermé'];
                            $prioColors = [
                                'urgent' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                'high' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                                'normal' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                'low' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                            ];
                        @endphp
                        <span class="inline-flex px-2 py-0.5 text-[10px] font-medium rounded-full {{ $statusColors[$supportChat->status] ?? '' }}">
                            {{ $statusLabels[$supportChat->status] ?? $supportChat->status }}
                        </span>
                        <span class="inline-flex px-2 py-0.5 text-[10px] font-medium rounded-full {{ $prioColors[$supportChat->priority] ?? '' }}">
                            {{ ucfirst($supportChat->priority) }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        {{ $supportChat->reference }} · {{ $supportChat->user->name ?? '?' }} ({{ $supportChat->user->email ?? '' }}) · {{ ucfirst($supportChat->category) }}
                    </p>
                </div>
            </div>

            {{-- Actions --}}
            @if($supportChat->admin_id === auth()->id())
                <div class="flex items-center gap-2 flex-wrap">
                    {{-- Statut --}}
                    <select id="statusSelect" onchange="updateStatus(this.value)"
                            class="px-2.5 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                        <option value="in_progress" {{ $supportChat->status === 'in_progress' ? 'selected' : '' }}>En cours</option>
                        <option value="waiting_user" {{ $supportChat->status === 'waiting_user' ? 'selected' : '' }}>Attente utilisateur</option>
                        <option value="closed" {{ $supportChat->status === 'closed' ? 'selected' : '' }}>Fermé</option>
                    </select>
                    {{-- Priorité --}}
                    <select id="prioritySelect" onchange="updatePriority(this.value)"
                            class="px-2.5 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                        <option value="low" {{ $supportChat->priority === 'low' ? 'selected' : '' }}>Basse</option>
                        <option value="normal" {{ $supportChat->priority === 'normal' ? 'selected' : '' }}>Normale</option>
                        <option value="high" {{ $supportChat->priority === 'high' ? 'selected' : '' }}>Haute</option>
                        <option value="urgent" {{ $supportChat->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
            @elseif(!$supportChat->admin_id)
                <button onclick="claimTicket()" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-hand-paper mr-2"></i>Prendre en charge
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Conversation --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm flex flex-col" style="max-height: 70vh;">
                {{-- Messages --}}
                <div id="messagesContainer" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-4">
                    @foreach($supportChat->messages as $message)
                        <div class="flex {{ $message->is_admin ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[85%] sm:max-w-[75%]">
                                <div class="flex items-center gap-2 mb-1 {{ $message->is_admin ? 'justify-end' : '' }}">
                                    @if(!$message->is_admin)
                                        <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-[10px] font-bold text-gray-600 dark:text-gray-300">
                                            {{ strtoupper(substr($message->user->name ?? '?', 0, 2)) }}
                                        </div>
                                    @endif
                                    <span class="text-[10px] text-gray-500 dark:text-gray-400">
                                        {{ $message->user->name ?? '?' }} · {{ $message->created_at->format('d/m H:i') }}
                                    </span>
                                    @if($message->is_admin)
                                        <div class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center text-[10px] font-bold text-white">
                                            {{ strtoupper(substr($message->user->name ?? '?', 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="{{ $message->is_admin ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800' : 'bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600' }} border rounded-xl px-4 py-3">
                                    <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap break-words">{{ $message->message }}</p>
                                    
                                    @if($message->attachments)
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            @foreach($message->attachments as $attachment)
                                                <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank"
                                                   class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white dark:bg-gray-600 border border-gray-200 dark:border-gray-500 rounded-lg text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors">
                                                    <i class="fas fa-paperclip text-gray-400"></i>
                                                    <span class="truncate max-w-[120px]">{{ $attachment['name'] }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Formulaire réponse --}}
                @if($supportChat->status !== 'closed')
                    @if($supportChat->admin_id === auth()->id() || !$supportChat->admin_id)
                        <div class="border-t border-gray-200 dark:border-gray-700 p-4">
                            <form action="{{ route('agent.reply', $supportChat) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="flex flex-col gap-3">
                                    <textarea name="message" rows="3" required placeholder="Votre réponse..."
                                              class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent resize-none">{{ old('message') }}</textarea>
                                    <div class="flex items-center justify-between">
                                        <label class="inline-flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 cursor-pointer hover:text-gray-700 dark:hover:text-gray-300">
                                            <i class="fas fa-paperclip"></i>
                                            <span>Joindre des fichiers</span>
                                            <input type="file" name="attachments[]" multiple class="hidden" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.zip">
                                        </label>
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                                            <i class="fas fa-paper-plane mr-2"></i>Envoyer
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="border-t border-gray-200 dark:border-gray-700 p-4 text-center text-sm text-gray-500">
                            <i class="fas fa-lock mr-1"></i>Ce ticket est assigné à un autre agent.
                        </div>
                    @endif
                @else
                    <div class="border-t border-gray-200 dark:border-gray-700 p-4 text-center text-sm text-gray-500">
                        <i class="fas fa-check-circle mr-1 text-green-500"></i>Cette conversation est fermée.
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar infos --}}
        <div class="space-y-4">
            {{-- Infos utilisateur --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-5">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">
                    <i class="fas fa-user mr-2 text-gray-400"></i>Utilisateur
                </h3>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-sm font-bold text-gray-600 dark:text-gray-300">
                        {{ strtoupper(substr($supportChat->user->name ?? '?', 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $supportChat->user->name ?? '?' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $supportChat->user->email ?? '' }}</p>
                    </div>
                </div>
                @php
                    $userTickets = \App\Models\SupportChat::where('user_id', $supportChat->user_id)->count();
                @endphp
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    <i class="fas fa-ticket-alt mr-1"></i>{{ $userTickets }} ticket(s) au total
                </p>
            </div>

            {{-- Détails ticket --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-5">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">
                    <i class="fas fa-info-circle mr-2 text-gray-400"></i>Détails
                </h3>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Référence</span>
                        <span class="font-mono text-gray-700 dark:text-gray-300">{{ $supportChat->reference }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Catégorie</span>
                        <span class="text-gray-700 dark:text-gray-300">{{ ucfirst($supportChat->category) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Créé le</span>
                        <span class="text-gray-700 dark:text-gray-300">{{ $supportChat->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Dernier message</span>
                        <span class="text-gray-700 dark:text-gray-300">{{ $supportChat->last_message_at?->diffForHumans() ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Messages</span>
                        <span class="text-gray-700 dark:text-gray-300">{{ $supportChat->messages->count() }}</span>
                    </div>
                    @if($supportChat->closed_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Fermé le</span>
                            <span class="text-gray-700 dark:text-gray-300">{{ $supportChat->closed_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Actions rapides --}}
            @if($supportChat->admin_id === auth()->id() && $supportChat->status !== 'closed')
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-5">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">
                        <i class="fas fa-bolt mr-2 text-yellow-500"></i>Actions rapides
                    </h3>
                    <div class="space-y-2">
                        <button onclick="updateStatus('waiting_user')" class="w-full text-left px-3 py-2 text-xs font-medium text-yellow-700 bg-yellow-50 dark:bg-yellow-900/20 dark:text-yellow-400 hover:bg-yellow-100 dark:hover:bg-yellow-900/30 rounded-lg transition-colors">
                            <i class="fas fa-clock mr-2"></i>Mettre en attente utilisateur
                        </button>
                        <button onclick="updateStatus('closed')" class="w-full text-left px-3 py-2 text-xs font-medium text-green-700 bg-green-50 dark:bg-green-900/20 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-lg transition-colors">
                            <i class="fas fa-check-circle mr-2"></i>Marquer comme résolu
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const chatId = {{ $supportChat->id }};
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// Scroll to bottom on load
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('messagesContainer');
    if (container) container.scrollTop = container.scrollHeight;
});

function updateStatus(status) {
    fetch(`/agent/ticket/${chatId}/status`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ status })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
        else alert(data.message);
    })
    .catch(() => alert('Erreur réseau.'));
}

function updatePriority(priority) {
    fetch(`/agent/ticket/${chatId}/priority`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ priority })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
        else alert(data.message);
    })
    .catch(() => alert('Erreur réseau.'));
}

function claimTicket() {
    fetch(`/agent/ticket/${chatId}/claim`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
        else alert(data.message);
    })
    .catch(() => alert('Erreur réseau.'));
}
</script>
@endpush
