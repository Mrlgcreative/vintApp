@extends('layouts.agent')

@section('title', 'Mes Tickets')

@section('content')
@php
    $statusVariantOf = fn($s) => match ($s) {
        'open' => 'danger',
        'in_progress' => 'info',
        'waiting_user' => 'warning',
        'closed' => 'secondary',
        default => 'secondary',
    };
    $statusLabels = ['open' => 'Ouvert', 'in_progress' => 'En cours', 'waiting_user' => 'Attente', 'closed' => 'Fermé'];
    $prioVariantOf = fn($p) => match ($p) {
        'urgent' => 'danger',
        'high' => 'warning',
        'normal' => 'info',
        'low' => 'secondary',
        default => 'secondary',
    };
    $hasFilters = request()->filled('search') || request()->filled('status') || request()->filled('priority') || request()->filled('category');
@endphp
<div class="space-y-6">
    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Mes Tickets</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Tickets qui vous sont assignés</p>
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
            <x-badge variant="soft-success">{{ $chats->total() ?? $chats->count() }}</x-badge>
            <span>ticket(s) au total</span>
        </div>
    </div>

    {{-- Filtres --}}
    <x-card class="p-4">
        <form method="GET" action="{{ route('agent.tickets') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            {{-- Recherche --}}
            <div class="sm:col-span-2">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par sujet ou référence…"
                           class="w-full h-10 pl-9 pr-3 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-gray-400 focus:border-transparent">
                </div>
            </div>
            {{-- Statut --}}
            <div>
                <select name="status" class="w-full h-10 px-3 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-400">
                    <option value="">Tous les statuts</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Ouvert</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>En cours</option>
                    <option value="waiting_user" {{ request('status') === 'waiting_user' ? 'selected' : '' }}>Attente utilisateur</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Fermé</option>
                </select>
            </div>
            {{-- Priorité --}}
            <div>
                <select name="priority" class="w-full h-10 px-3 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-400">
                    <option value="">Toutes priorités</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Haute</option>
                    <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normale</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Basse</option>
                </select>
            </div>
            {{-- Catégorie + actions --}}
            <div class="flex gap-2">
                <select name="category" class="flex-1 h-10 px-3 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-400">
                    <option value="">Catégorie</option>
                    <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>Technique</option>
                    <option value="account" {{ request('category') === 'account' ? 'selected' : '' }}>Compte</option>
                    <option value="payment" {{ request('category') === 'payment' ? 'selected' : '' }}>Paiement</option>
                    <option value="order" {{ request('category') === 'order' ? 'selected' : '' }}>Commande</option>
                    <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>Général</option>
                </select>
                <button type="submit" class="inline-flex items-center gap-1.5 h-10 px-4 text-sm rounded-md bg-gray-900 hover:bg-gray-700 text-white font-medium shadow-sm transition-colors">
                    <i class="fas fa-search text-xs"></i>
                </button>
                @if($hasFilters)
                    <a href="{{ route('agent.tickets') }}" class="inline-flex items-center justify-center h-10 w-10 text-sm rounded-md border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors" title="Réinitialiser">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </x-card>

    {{-- Liste tickets --}}
    @if($chats->isEmpty())
        <x-card class="p-10">
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                    <i class="fas fa-inbox text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold tracking-tight text-gray-900 dark:text-white mb-1.5">
                    {{ $hasFilters ? 'Aucun résultat' : 'Aucun ticket assigné' }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                    {{ $hasFilters ? 'Aucun ticket ne correspond aux filtres sélectionnés.' : 'Les tickets que vous prenez en charge apparaîtront ici.' }}
                </p>
                @if($hasFilters)
                    <x-button-outline :href="route('agent.tickets')">Réinitialiser les filtres</x-button-outline>
                @else
                    <x-button-primary :href="route('agent.unassigned')" variant="secondary" class="gap-2">
                        <i class="fas fa-inbox text-xs"></i> Voir les non assignés
                    </x-button-primary>
                @endif
            </div>
        </x-card>
    @else
        {{-- Mobile cards --}}
        <div class="sm:hidden space-y-3">
            @foreach($chats as $chat)
                <a href="{{ route('agent.show', $chat) }}"
                   class="block bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-sm hover:shadow-md hover:border-gray-300 dark:hover:border-gray-600 transition-all p-4">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $chat->subject }}</p>
                            <p class="text-[11px] font-mono text-gray-500 dark:text-gray-400">{{ $chat->reference }}</p>
                        </div>
                        <x-badge variant="{{ $statusVariantOf($chat->status) }}" class="flex-shrink-0">{{ $statusLabels[$chat->status] ?? $chat->status }}</x-badge>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <span class="inline-flex items-center gap-1 min-w-0"><i class="fas fa-user"></i><span class="truncate">{{ $chat->user->name ?? '?' }}</span></span>
                        <span class="text-gray-300 dark:text-gray-600">·</span>
                        <x-badge variant="{{ $prioVariantOf($chat->priority) }}">{{ ucfirst($chat->priority) }}</x-badge>
                        <span class="ml-auto whitespace-nowrap">{{ $chat->last_message_at?->diffForHumans() }}</span>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Desktop table --}}
        <x-card class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/40">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Référence</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sujet</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Utilisateur</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Statut</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Priorité</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dernier message</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($chats as $chat)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 cursor-pointer transition-colors" onclick="window.location='{{ route('agent.show', $chat) }}'">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="text-sm font-mono text-gray-600 dark:text-gray-400">{{ $chat->reference }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $chat->subject }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($chat->category) }}</p>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300">
                                            {{ strtoupper(substr($chat->user->name ?? '?', 0, 2)) }}
                                        </div>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $chat->user->name ?? '?' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <x-badge variant="{{ $statusVariantOf($chat->status) }}">{{ $statusLabels[$chat->status] ?? $chat->status }}</x-badge>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <x-badge variant="{{ $prioVariantOf($chat->priority) }}">{{ ucfirst($chat->priority) }}</x-badge>
                                </td>
                                <td class="px-4 py-3 text-right text-xs whitespace-nowrap text-gray-500 dark:text-gray-400">{{ $chat->last_message_at?->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>

        {{-- Pagination --}}
        <div class="flex justify-end mt-2">
            {{ $chats->links() }}
        </div>
    @endif
</div>
@endsection