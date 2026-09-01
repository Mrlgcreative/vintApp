@extends('layouts.agent')

@section('title', 'Tableau de bord')

@section('content')
<div>
    {{-- Bienvenue --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                Bonjour, {{ auth()->user()->name }} 👋
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Voici un aperçu de votre activité support</p>
        </div>
        <a href="{{ route('agent.unassigned') }}" class="inline-flex items-center justify-center h-10 rounded-md px-4 text-sm font-medium bg-emerald-600 text-white hover:bg-emerald-700 transition-colors">
            <i class="fas fa-inbox mr-2"></i>Tickets non assignés
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        {{-- Tickets actifs --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Mes tickets actifs</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active'] }}</h3>
                    <p class="text-[10px] text-gray-400 mt-0.5">sur {{ $stats['max_chats'] }} max</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                    <i class="fas fa-inbox text-emerald-600 dark:text-emerald-400"></i>
                </div>
            </div>
            @php $pct = $stats['max_chats'] > 0 ? min(100, ($stats['active'] / $stats['max_chats']) * 100) : 0; @endphp
            <div class="mt-2 w-full h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full">
                <div class="{{ $pct >= 90 ? 'bg-red-500' : ($pct >= 70 ? 'bg-yellow-500' : 'bg-emerald-500') }} h-full rounded-full transition-all" style="width: {{ $pct }}%"></div>
            </div>
        </div>

        {{-- En attente réponse --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Attendent ma réponse</p>
                    <h3 class="text-2xl font-bold {{ $stats['waiting_reply'] > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-gray-900 dark:text-white' }}">{{ $stats['waiting_reply'] }}</h3>
                </div>
                <div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                    <i class="fas fa-reply text-orange-600 dark:text-orange-400"></i>
                </div>
            </div>
        </div>

        {{-- Résolus aujourd'hui --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Résolus aujourd'hui</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['closed_today'] }}</h3>
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $stats['closed_week'] }} cette semaine</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <i class="fas fa-check-circle text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>

        {{-- Non assignés --}}
        <a href="{{ route('agent.unassigned') }}" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-sm p-4 hover:shadow-md hover:border-gray-300 dark:hover:border-gray-600 transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Non assignés</p>
                    <h3 class="text-2xl font-bold {{ $stats['unassigned'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">{{ $stats['unassigned'] }}</h3>
                    <p class="text-[10px] text-emerald-600 mt-0.5">Cliquer pour voir →</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Mes tickets récents --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-inbox mr-2 text-emerald-500"></i>Tickets récents
                </h2>
                <a href="{{ route('agent.tickets') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                    Voir tous →
                </a>
            </div>
            @if($recentTickets->isEmpty())
                <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-check-circle text-3xl text-gray-300 mb-2"></i>
                    <p class="text-sm">Aucun ticket actif</p>
                </div>
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($recentTickets as $ticket)
                        @php
                            $statusVariant = match ($ticket->status) {
                                'open' => 'danger',
                                'in_progress' => 'info',
                                'waiting_user' => 'warning',
                                'closed' => 'secondary',
                                default => 'secondary',
                            };
                            $statusLabels = ['open' => 'Ouvert', 'in_progress' => 'En cours', 'waiting_user' => 'Attente', 'closed' => 'Fermé'];
                        @endphp
                        <a href="{{ route('agent.show', $ticket) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex-shrink-0">
                                <div class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300">
                                    {{ strtoupper(substr($ticket->user->name ?? '?', 0, 2)) }}
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $ticket->subject }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $ticket->reference }} · {{ $ticket->user->name ?? 'Utilisateur' }}
                                </p>
                            </div>
                            <div class="flex-shrink-0 flex flex-col items-end gap-1">
                                <x-badge variant="{{ $statusVariant }}">{{ $statusLabels[$ticket->status] ?? $ticket->status }}</x-badge>
                                <span class="text-[10px] text-gray-400">{{ $ticket->last_message_at?->diffForHumans() }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Tickets urgents non assignés --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-fire mr-2 text-red-500"></i>Urgents non assignés
                </h2>
                <a href="{{ route('agent.unassigned') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                    Voir tous →
                </a>
            </div>
            @if($urgentUnassigned->isEmpty())
                <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-shield-alt text-3xl text-gray-300 mb-2"></i>
                    <p class="text-sm">Aucun ticket urgent</p>
                </div>
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($urgentUnassigned as $ticket)
                        <div class="flex items-center gap-3 px-5 py-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $ticket->subject }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $ticket->reference }} · {{ $ticket->user->name ?? '?' }} · {{ $ticket->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                @php
                                    $prioVariant = $ticket->priority === 'urgent' ? 'danger' : 'warning';
                                    $prioLabels = ['urgent' => 'Urgent', 'high' => 'Haute'];
                                @endphp
                                <x-badge variant="{{ $prioVariant }}">{{ $prioLabels[$ticket->priority] ?? $ticket->priority }}</x-badge>
                                <button onclick="claimTicket({{ $ticket->id }})" class="inline-flex items-center h-8 px-3 rounded-md text-xs font-medium bg-emerald-600 hover:bg-emerald-700 text-white transition-colors">
                                    <i class="fas fa-hand-paper mr-1"></i>Prendre
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function claimTicket(chatId) {
    fetch(`/agent/ticket/${chatId}/claim`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(() => alert('Erreur réseau.'));
}
</script>
@endpush
