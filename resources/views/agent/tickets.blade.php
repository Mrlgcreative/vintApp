@extends('layouts.agent')

@section('title', 'Mes Tickets')

@section('content')
<div>
    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Mes Tickets</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Tickets qui vous sont assignés</p>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('agent.tickets') }}" class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-3">
            {{-- Recherche --}}
            <div class="col-span-2 sm:col-span-4 lg:col-span-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..."
                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
            </div>
            {{-- Statut --}}
            <div>
                <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                    <option value="">Tous les statuts</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Ouvert</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>En cours</option>
                    <option value="waiting_user" {{ request('status') === 'waiting_user' ? 'selected' : '' }}>Attente utilisateur</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Fermé</option>
                </select>
            </div>
            {{-- Priorité --}}
            <div>
                <select name="priority" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                    <option value="">Toutes priorités</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Haute</option>
                    <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normale</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Basse</option>
                </select>
            </div>
            {{-- Catégorie --}}
            <div>
                <select name="category" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                    <option value="">Toutes catégories</option>
                    <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>Technique</option>
                    <option value="account" {{ request('category') === 'account' ? 'selected' : '' }}>Compte</option>
                    <option value="payment" {{ request('category') === 'payment' ? 'selected' : '' }}>Paiement</option>
                    <option value="order" {{ request('category') === 'order' ? 'selected' : '' }}>Commande</option>
                    <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>Général</option>
                </select>
            </div>
            {{-- Bouton --}}
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-3 py-2 text-sm bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-search mr-1"></i>Filtrer
                </button>
                <a href="{{ route('agent.tickets') }}" class="px-3 py-2 text-sm text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Liste tickets --}}
    @if($chats->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-8 text-center">
            <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-inbox text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucun ticket</h3>
            <p class="text-gray-500 dark:text-gray-400">Pas de tickets correspondant aux filtres sélectionnés.</p>
        </div>
    @else
        {{-- Mobile cards --}}
        <div class="sm:hidden space-y-3">
            @foreach($chats as $chat)
                <a href="{{ route('agent.show', $chat) }}" class="block bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $chat->subject }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $chat->reference }}</p>
                        </div>
                        @php
                            $statusColors = [
                                'open' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                'in_progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                'waiting_user' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                'closed' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400',
                            ];
                            $statusLabels = ['open' => 'Ouvert', 'in_progress' => 'En cours', 'waiting_user' => 'Attente', 'closed' => 'Fermé'];
                            $prioColors = [
                                'urgent' => 'text-red-600', 'high' => 'text-orange-600',
                                'normal' => 'text-blue-600', 'low' => 'text-gray-500',
                            ];
                        @endphp
                        <span class="ml-2 inline-flex px-2 py-0.5 text-[10px] font-medium rounded-full {{ $statusColors[$chat->status] ?? '' }}">
                            {{ $statusLabels[$chat->status] ?? $chat->status }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span><i class="fas fa-user mr-1"></i>{{ $chat->user->name ?? '?' }}</span>
                        <span class="{{ $prioColors[$chat->priority] ?? '' }}">{{ ucfirst($chat->priority) }}</span>
                        <span>{{ $chat->last_message_at?->diffForHumans() }}</span>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Desktop table --}}
        <div class="hidden sm:block bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
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
                                <span class="inline-flex px-2 py-0.5 text-[10px] font-medium rounded-full {{ $statusColors[$chat->status] ?? '' }}">
                                    {{ $statusLabels[$chat->status] ?? $chat->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $prioBadge = [
                                        'urgent' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                        'high' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                                        'normal' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                        'low' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                    ];
                                @endphp
                                <span class="inline-flex px-2 py-0.5 text-[10px] font-medium rounded-full {{ $prioBadge[$chat->priority] ?? '' }}">
                                    {{ ucfirst($chat->priority) }}
                                </span>
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
