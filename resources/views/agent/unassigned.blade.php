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
@endphp
<div>
    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Tickets non assignés</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Prenez en charge les tickets en attente</p>
        </div>
    </div>

    {{-- Filtres rapides --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('agent.unassigned') }}" class="flex flex-wrap gap-3">
            <select name="priority" class="h-10 px-3 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                <option value="">Toutes priorités</option>
                <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Haute</option>
                <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normale</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Basse</option>
            </select>
            <select name="category" class="h-10 px-3 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                <option value="">Toutes catégories</option>
                <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>Technique</option>
                <option value="account" {{ request('category') === 'account' ? 'selected' : '' }}>Compte</option>
                <option value="payment" {{ request('category') === 'payment' ? 'selected' : '' }}>Paiement</option>
                <option value="order" {{ request('category') === 'order' ? 'selected' : '' }}>Commande</option>
                <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>Général</option>
            </select>
            <button type="submit" class="inline-flex items-center justify-center h-10 px-4 text-sm rounded-md bg-emerald-600 hover:bg-emerald-700 text-white font-medium transition-colors">
                <i class="fas fa-filter mr-1"></i>Filtrer
            </button>
            <a href="{{ route('agent.unassigned') }}" class="inline-flex items-center justify-center h-10 px-4 text-sm rounded-md border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                <i class="fas fa-times mr-1"></i>Reset
            </a>
        </form>
    </div>

    @if($chats->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-sm p-8 text-center">
            <div class="w-16 h-16 mx-auto border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-check text-2xl text-emerald-500"></i>
            </div>
            <h3 class="text-lg font-semibold tracking-tight text-gray-900 dark:text-white mb-2">Tout est pris en charge !</h3>
            <p class="text-gray-500 dark:text-gray-400">Il n'y a aucun ticket non assigné pour le moment.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($chats as $chat)
                @php
                    $prioConfig = [
                        'urgent' => ['border' => 'border-l-red-500 bg-red-50/50 dark:bg-red-500/5', 'icon' => 'fa-exclamation-triangle text-red-500'],
                        'high' => ['border' => 'border-l-orange-500 bg-orange-50/50 dark:bg-orange-500/5', 'icon' => 'fa-arrow-up text-orange-500'],
                        'normal' => ['border' => 'border-l-blue-500', 'icon' => 'fa-minus text-blue-500'],
                        'low' => ['border' => 'border-l-gray-300 dark:border-l-gray-600', 'icon' => 'fa-arrow-down text-gray-400'],
                    ];
                    $cfg = $prioConfig[$chat->priority] ?? $prioConfig['normal'];
                @endphp
                <div class="rounded-xl border border-l-4 {{ $cfg['border'] }} border-gray-200 dark:border-gray-700/60 bg-white dark:bg-gray-800 shadow-sm p-4 sm:p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <i class="fas {{ $cfg['icon'] }} text-xs"></i>
                                <span class="text-xs font-mono text-gray-500 dark:text-gray-400">{{ $chat->reference }}</span>
                                <x-badge variant="{{ $prioVariantOf($chat->priority) }}">{{ ucfirst($chat->priority) }}</x-badge>
                                <x-badge variant="secondary">{{ $catLabels[$chat->category] ?? $chat->category }}</x-badge>
                            </div>
                            <h3 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white">{{ $chat->subject }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                <i class="fas fa-user mr-1"></i>{{ $chat->user->name ?? '?' }} ·
                                <i class="fas fa-clock mr-1"></i>{{ $chat->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="{{ route('agent.show', $chat) }}" class="inline-flex items-center h-9 px-3.5 rounded-md text-xs font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <i class="fas fa-eye mr-1.5"></i>Voir
                            </a>
                            <button onclick="claimTicket({{ $chat->id }})" class="inline-flex items-center h-9 px-3.5 rounded-md text-xs font-medium bg-emerald-600 hover:bg-emerald-700 text-white transition-colors">
                                <i class="fas fa-hand-paper mr-1.5"></i>Prendre en charge
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
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
