@extends('layouts.agent')

@section('title', 'Tableau de bord')

@section('content')
<div class="space-y-6">
    {{-- Bienvenue --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                Bonjour, {{ auth()->user()->name }}
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Voici un aperçu de votre activité support du jour</p>
        </div>
        <x-button-outline :href="route('agent.unassigned')" tone="success" class="gap-2">
            <i class="fas fa-inbox text-xs"></i> Tickets non assignés
            @if($stats['unassigned'] > 0)
                <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-bold rounded-full bg-red-500 text-white">{{ $stats['unassigned'] }}</span>
            @endif
        </x-button-outline>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <x-stat-card :value="$stats['active']" label="Mes tickets actifs" icon="fas fa-inbox" tone="emerald" :id="'statActive'" />
        <x-stat-card :value="$stats['waiting_reply']" label="Attendent ma réponse" icon="fas fa-reply" tone="amber" />
        <x-stat-card :value="$stats['closed_today']" label="Résolus aujourd'hui" icon="fas fa-check-circle" tone="blue" />
        <x-stat-card :value="$stats['unassigned']" label="Tickets non assignés" icon="fas fa-exclamation-circle" tone="{{ $stats['unassigned'] > 0 ? 'amber' : 'blue' }}" />
    </div>

    {{-- Détails @if agent en charge --}}
    @php
        $pct = $stats['max_chats'] > 0 ? min(100, ($stats['active'] / $stats['max_chats']) * 100) : 0;
        $loadColor = $pct >= 90 ? 'bg-red-500' : ($pct >= 70 ? 'bg-yellow-500' : 'bg-emerald-500');
        $loadText = $pct >= 90 ? 'text-red-600 dark:text-red-400' : ($pct >= 70 ? 'text-yellow-600 dark:text-yellow-400' : 'text-emerald-600 dark:text-emerald-400');
    @endphp
    <x-card class="p-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">Charge de travail</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ $stats['active'] }} / {{ $stats['max_chats'] }} tickets actifs · <span class="{{ $loadText }}">{{ round($pct) }}% de la capacité</span>
                </p>
            </div>
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ round($pct) }}%</span>
        </div>
        <div class="mt-3 w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
            <div class="{{ $loadColor }} h-full rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
        </div>
    </x-card>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Mes tickets récents --}}
        <x-card>
            <x-card-header title="Tickets récents" icon="fas fa-inbox" tone="emerald">
                <x-slot name="actions">
                    <a href="{{ route('agent.tickets') }}" class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 transition-colors">
                        Voir tous <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </x-slot>
            </x-card-header>
            @if($recentTickets->isEmpty())
                <div class="p-10 text-center">
                    <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-3">
                        <i class="fas fa-check-circle text-gray-400"></i>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Aucun ticket actif</p>
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
                        <a href="{{ route('agent.show', $ticket) }}" class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-xs font-bold text-emerald-600 dark:text-emerald-300 flex-shrink-0">
                                {{ strtoupper(substr($ticket->user->name ?? '?', 0, 2)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $ticket->subject }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
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
        </x-card>

        {{-- Tickets urgents non assignés --}}
        <x-card>
            <x-card-header title="Urgents non assignés" icon="fas fa-fire" tone="red">
                <x-slot name="actions">
                    <a href="{{ route('agent.unassigned') }}" class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 transition-colors">
                        Voir tous <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </x-slot>
            </x-card-header>
            @if($urgentUnassigned->isEmpty())
                <div class="p-10 text-center">
                    <div class="w-12 h-12 mx-auto rounded-full bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center mb-3">
                        <i class="fas fa-shield-halved text-emerald-500"></i>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Aucun ticket urgent</p>
                </div>
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($urgentUnassigned as $ticket)
                        @php
                            $prioVariant = $ticket->priority === 'urgent' ? 'danger' : 'warning';
                            $prioLabels = ['urgent' => 'Urgent', 'high' => 'Haute'];
                        @endphp
                        <div class="flex items-center gap-3 px-5 py-3.5">
                            <div class="w-10 h-10 rounded-lg {{ $ticket->priority === 'urgent' ? 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400' : 'bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400' }} flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $ticket->subject }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {{ $ticket->reference }} · {{ $ticket->user->name ?? '?' }} · {{ $ticket->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <x-badge variant="{{ $prioVariant }}">{{ $prioLabels[$ticket->priority] ?? $ticket->priority }}</x-badge>
                                <button onclick="claimTicket({{ $ticket->id }})" class="inline-flex items-center gap-1.5 h-8 px-3 rounded-md text-xs font-medium bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition-colors">
                                    <i class="fas fa-hand-paper"></i> Prendre
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>
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
        if (data.success) location.reload();
        else alert(data.message);
    })
    .catch(() => alert('Erreur réseau.'));
}
</script>
@endpush