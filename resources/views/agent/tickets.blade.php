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
@endphp
<div>
    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Mes Tickets</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Tickets qui vous sont assignés</p>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('agent.tickets') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            {{-- Recherche --}}
            <div class="sm:col-span-2">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..."
                           class="w-full h-10 pl-9 pr-3 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                </div>
            </div>
            {{-- Statut --}}
            <div>
                <select name="status" class="w-full h-10 px-3 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                    <option value="">Tous les statuts</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Ouvert</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>En cours</option>
                    <option value="waiting_user" {{ request('status') === 'waiting_user' ? 'selected' : '' }}>Attente utilisateur</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Fermé</option>
                </select>
            </div>
            {{-- Priorité --}}
            <div>
                <select name="priority" class="w-full h-10 px-3 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                    <option value="">Toutes priorités</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Haute</option>
                    <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normale</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Basse</option>
                </select>
            </div>
            {{-- Catégorie + Bouton --}}
            <div class="flex gap-2">
                <div class="flex-1">
                    <select name="category" class="w-full h-10 px-3 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                        <option value="">Catégorie</option>
                        <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>Technique</option>
                        <option value="account" {{ request('category') === 'account' ? 'selected' : '' }}>Compte</option>
                        <option value="payment" {{ request('category') === 'payment' ? 'selected' : '' }}>Paiement</option>
                        <option value="order" {{ request('category') === 'order' ? 'selected' : '' }}>Commande</option>
                        <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>Général</option>
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center justify-center h-10 px-4 text-sm rounded-md bg-emerald-600 hover:bg-emerald-700 text-white font-medium transition-colors">
                    <i class="fas fa-search mr-1"></i>Filtrer
                </button>
                <a href="{{ route('agent.tickets') }}" class="inline-flex items-center justify-center h-10 px-3 text-sm rounded-md border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors" title="Réinitialiser">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Liste tickets --}}
    @if($chats->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-sm p-8 text-center">
            <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-inbox text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold tracking-tight text-gray-900 dark:text-white mb-2">Aucun ticket</h3>
            <p class="text-gray-500 dark:text-gray-400">Pas de tickets correspondant aux filtres sélectionnés.</p>
        </div>
    @else
        {{-- Mobile cards --}}
        <div class="sm:hidden space-y-3">
            @foreach($chats as $chat)
                <a href="{{ route('agent.show', $chat) }}" class="block bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-sm p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $chat->subject }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $chat->reference }}</p>
                        </div>
                        <x-badge variant="{{ $statusVariantOf($chat->status) }}" class="ml-2 flex-shrink-0">{{ $statusLabels[$chat->status] ?? $chat->status }}</x-badge>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span><i class="fas fa-user mr-1"></i>{{ $chat->user->name ?? '?' }}</span>
                        <x-badge variant="{{ $prioVariantOf($chat->priority) }}">{{ ucfirst($chat->priority) }}</x-badge>
                        <span>{{ $chat->last_message_at?->diffForHumans() }}</span>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Desktop table --}}
        <div class="hidden sm:block bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700/60 shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Référence</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Sujet</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Utilisateur</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Statut</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Priorité</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Dernier msg</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($chats as $chat)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 cursor-pointer" onclick="window.location='{{ route('agent.show', $chat) }}'">
                            <td class="px-4 py-3">
                                <span class="text-sm font-mono text-gray-600 dark:text-gray-400">{{ $chat->reference }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[200px] lg:max-w-xs">{{ $chat->subject }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($chat->category) }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300">
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
                            <td class="px-4 py-3 text-right text-xs text-gray-500 dark:text-gray-400">
                                {{ $chat->last_message_at?->diffForHumans() }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $chats->links() }}
        </div>
    @endif
</div>
@endsection
