@extends('layouts.agent')

@section('title', 'Tickets non assignés')

@section('content')
@php
    $prioVariantOf = fn($p) => match ($p) {
        'urgent' => 'danger',
        'high' => 'warning',
        'normal' => 'info',
        'low' => 'secondary',
        default => 'secondary',
    };
    $catLabels = ['technical' => 'Technique', 'account' => 'Compte', 'payment' => 'Paiement', 'order' => 'Commande', 'general' => 'Général'];
    $prioIcon = ['urgent' => 'fa-fire', 'high' => 'fa-arrow-up', 'normal' => 'fa-minus', 'low' => 'fa-arrow-down'];
    $prioTone = ['urgent' => 'red', 'high' => 'amber', 'normal' => 'blue', 'low' => 'gray'];
    $hasFilters = request()->filled('priority') || request()->filled('category');
@endphp
<div class="space-y-6">
    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Tickets non assignés</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Prenez en charge les tickets en attente</p>
        </div>
        @if($chats->isNotEmpty())
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <x-badge variant="soft-danger">{{ $chats->total() ?? $chats->count() }}</x-badge>
                <span>en attente</span>
            </div>
        @endif
    </div>

    {{-- Filtres --}}
    <x-card class="p-4">
        <form method="GET" action="{{ route('agent.unassigned') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[160px] max-w-xs">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher…"
                       class="w-full h-10 pl-9 pr-3 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-gray-400 focus:border-transparent">
            </div>
            <select name="priority" class="h-10 px-3 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-400">
                <option value="">Toutes priorités</option>
                <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Haute</option>
                <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normale</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Basse</option>
            </select>
            <select name="category" class="h-10 px-3 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-400">
                <option value="">Toutes catégories</option>
                <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>Technique</option>
                <option value="account" {{ request('category') === 'account' ? 'selected' : '' }}>Compte</option>
                <option value="payment" {{ request('category') === 'payment' ? 'selected' : '' }}>Paiement</option>
                <option value="order" {{ request('category') === 'order' ? 'selected' : '' }}>Commande</option>
                <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>Général</option>
            </select>
            <button type="submit" class="inline-flex items-center gap-1.5 h-10 px-4 text-sm rounded-md bg-gray-900 hover:bg-gray-700 text-white font-medium shadow-sm transition-colors">
                <i class="fas fa-filter text-xs"></i> Filtrer
            </button>
            @if($hasFilters)
                <a href="{{ route('agent.unassigned') }}" class="inline-flex items-center gap-1.5 h-10 px-4 text-sm rounded-md border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times text-xs"></i> Reset
                </a>
            @endif
        </form>
    </x-card>

    @if($chats->isEmpty())
        <x-card class="p-10">
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                    <i class="fas fa-check text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold tracking-tight text-gray-900 dark:text-white mb-1.5">
                    {{ $hasFilters ? 'Aucun résultat' : 'Tout est pris en charge !' }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                    {{ $hasFilters ? 'Aucun ticket ne correspond aux filtres sélectionnés.' : "Il n'y a aucun ticket non assigné pour le moment." }}
                </p>
                @if($hasFilters)
                    <x-button-outline :href="route('agent.unassigned')">Réinitialiser les filtres</x-button-outline>
                @endif
            </div>
        </x-card>
    @else
        <div class="space-y-3">
            @foreach($chats as $chat)
                @php
                    $icon = $prioIcon[$chat->priority] ?? 'fa-minus';
                    $tone = $prioTone[$chat->priority] ?? 'blue';
                    $priorityFriendly = ['urgent' => 'Urgent', 'high' => 'Haute', 'normal' => 'Normale', 'low' => 'Basse'];
                @endphp
                <x-card class="p-4 sm:p-5 hover:shadow-md hover:border-gray-300 dark:hover:border-gray-600 transition-all">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        {{-- Icône priorité --}}
                        <x-icon :icon="'fas ' . $icon" :tone="$tone" />

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 mb-1 flex-wrap">
                                <span class="text-xs font-mono text-gray-500 dark:text-gray-400">{{ $chat->reference }}</span>
                                <x-badge variant="{{ $prioVariantOf($chat->priority) }}">{{ $priorityFriendly[$chat->priority] ?? ucfirst($chat->priority) }}</x-badge>
                                <x-badge variant="soft-secondary">{{ $catLabels[$chat->category] ?? $chat->category }}</x-badge>
                            </div>
                            <h3 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white">{{ $chat->subject }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex items-center gap-3">
                                <span class="inline-flex items-center gap-1"><i class="fas fa-user"></i>{{ $chat->user->name ?? '?' }}</span>
                                <span class="inline-flex items-center gap-1"><i class="fas fa-clock"></i>{{ $chat->created_at->diffForHumans() }}</span>
                            </p>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0 sm:pl-4 sm:border-l sm:border-gray-100 dark:sm:border-gray-700">
                            <x-button-outline :href="route('agent.show', $chat)" size="sm" class="gap-1.5">
                                <i class="fas fa-eye text-xs"></i> Voir
                            </x-button-outline>
                            <button onclick="claimTicket({{ $chat->id }})" class="inline-flex items-center gap-1.5 h-9 px-3.5 rounded-md text-xs font-medium bg-gray-900 hover:bg-gray-700 text-white shadow-sm transition-colors">
                                <i class="fas fa-hand-paper"></i> Prendre en charge
                            </button>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="flex justify-end mt-2">
            {{ $chats->links() }}
        </div>
    @endif
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