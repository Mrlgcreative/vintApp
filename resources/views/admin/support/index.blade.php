@extends('layouts.support')

@section('title', 'Tickets Support')

@section('content')
<div>
    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white">Support Client</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Gérez les demandes d'assistance des utilisateurs</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="refreshStats()" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                <i class="fas fa-sync-alt"></i>Actualiser
            </button>
            <a href="{{ route('admin.support.stats') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                <i class="fas fa-chart-bar"></i>Statistiques
            </a>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3">
        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Total</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ $stats['total'] }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300">
                    <i class="fas fa-comments text-[10px] text-sky-500"></i>
                    Total
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-comments text-xs text-sky-500"></i>
                    Conversations
                </div>
                <div class="text-xs text-slate-400">Tous statuts confondus</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Ouvert</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-red-600">{{ $stats['open'] }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-400">
                    <i class="fas fa-exclamation-circle text-[10px]"></i>
                    Ouvert
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-exclamation-circle text-xs text-red-500"></i>
                    En attente de réponse
                </div>
                <div class="text-xs text-slate-400">À traiter en priorité</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">En cours</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-amber-600">{{ $stats['in_progress'] }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-400">
                    <i class="fas fa-clock text-[10px]"></i>
                    En cours
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-clock text-xs text-amber-500"></i>
                    Traitement en cours
                </div>
                <div class="text-xs text-slate-400">Assignées et suivies</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">En attente</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-violet-600">{{ $stats['waiting_user'] }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-violet-200 bg-violet-50 px-2 py-0.5 text-xs font-medium text-violet-700 dark:border-violet-500/30 dark:bg-violet-500/10 dark:text-violet-400">
                    <i class="fas fa-hourglass-half text-[10px]"></i>
                    En attente
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-hourglass-half text-xs text-violet-500"></i>
                    En attente utilisateur
                </div>
                <div class="text-xs text-slate-400">Réponse client attendue</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Fermés aujourd'hui</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-emerald-600">{{ $stats['closed_today'] }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <i class="fas fa-check-circle text-[10px]"></i>
                    Fermés
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-check-circle text-xs text-emerald-500"></i>
                    Résolus
                </div>
                <div class="text-xs text-slate-400">Clôturés sur la journée</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Non assignés</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-orange-600">{{ $stats['unassigned'] }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-orange-200 bg-orange-50 px-2 py-0.5 text-xs font-medium text-orange-700 dark:border-orange-500/30 dark:bg-orange-500/10 dark:text-orange-400">
                    <i class="fas fa-user-times text-[10px]"></i>
                    Non assignés
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-user-times text-xs text-orange-500"></i>
                    Sans agent
                </div>
                <div class="text-xs text-slate-400">À assigner</div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="mb-6 rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="p-5 sm:p-6">
            <form method="GET" action="{{ route('admin.support.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-6">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Recherche</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Référence, sujet..."
                           class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Statut</label>
                    <select name="status" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <option value="">Tous les statuts</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Ouvert</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>En cours</option>
                        <option value="waiting_user" {{ request('status') === 'waiting_user' ? 'selected' : '' }}>En attente utilisateur</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Fermé</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Priorité</label>
                    <select name="priority" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <option value="">Toutes les priorités</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Faible</option>
                        <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normale</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Élevée</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Catégorie</label>
                    <select name="category" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <option value="">Toutes les catégories</option>
                        <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>Technique</option>
                        <option value="account" {{ request('category') === 'account' ? 'selected' : '' }}>Compte</option>
                        <option value="payment" {{ request('category') === 'payment' ? 'selected' : '' }}>Paiement</option>
                        <option value="order" {{ request('category') === 'order' ? 'selected' : '' }}>Commande</option>
                        <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>Général</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Assigné à</label>
                    <select name="assigned_to" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <option value="">Tous</option>
                        <option value="unassigned" {{ request('assigned_to') === 'unassigned' ? 'selected' : '' }}>Non assigné</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" {{ request('assigned_to') == $admin->id ? 'selected' : '' }}>
                                {{ $admin->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-primary-700">
                        <i class="fas fa-search"></i>Filtrer
                    </button>
                    <a href="{{ route('admin.support.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des conversations -->
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-5 py-4 dark:border-slate-700">
            <h3 class="flex items-center gap-2 text-sm sm:text-base font-semibold text-slate-900 dark:text-white">
                <i class="fas fa-inbox text-primary-600"></i>
                Liste des conversations
                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    {{ $chats->total() }} total
                </span>
            </h3>
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-500 dark:text-slate-400">
                    Page {{ $chats->currentPage() }}/{{ $chats->lastPage() }}
                </span>
            </div>
        </div>

        @forelse($chats as $chat)
            <!-- Version Desktop - Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Référence</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Utilisateur</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Sujet</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Statut</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Priorité</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Assigné à</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Dernier message</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        <tr class="border-t border-slate-100 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-700/30">
                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-slate-900 dark:text-white">{{ $chat->reference }}</span>
                                    @if($chat->unread_count_for_admin > 0)
                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                            {{ $chat->unread_count_for_admin }} nouveau(x)
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center gap-3">
                                    @if($chat->user?->avatar)
                                        <img class="h-8 w-8 rounded-full object-cover" src="{{ $chat->user->avatar_url }}" alt="">
                                    @else
                                        <div class="flex h-8 w-8 rounded-full bg-slate-200 dark:bg-slate-700 items-center justify-center">
                                            <i class="fas fa-user text-slate-500 dark:text-slate-400 text-xs"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-medium text-slate-900 dark:text-white text-sm">{{ $chat->user?->name ?? 'Utilisateur supprimé' }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $chat->user?->email ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 align-middle">
                                <div class="font-medium text-slate-900 dark:text-white text-sm">
                                    {{ $chat->subject ?: 'Demande d\'assistance' }}
                                </div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ $chat->formatted_category }}</div>
                            </td>

                            <td class="px-4 py-3 align-middle">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset
                                    {{ $chat->status === 'open' ? 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400' : '' }}
                                    {{ $chat->status === 'in_progress' ? 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400' : '' }}
                                    {{ $chat->status === 'waiting_user' ? 'bg-violet-50 text-violet-700 ring-violet-600/20 dark:bg-violet-500/10 dark:text-violet-400' : '' }}
                                    {{ $chat->status === 'closed' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400' : '' }}">
                                    <span class="h-1.5 w-1.5 rounded-full
                                        {{ $chat->status === 'open' ? 'bg-red-500' : '' }}
                                        {{ $chat->status === 'in_progress' ? 'bg-amber-500' : '' }}
                                        {{ $chat->status === 'waiting_user' ? 'bg-violet-500' : '' }}
                                        {{ $chat->status === 'closed' ? 'bg-emerald-500' : '' }}"></span>
                                    {{ $chat->formatted_status }}
                                </span>
                            </td>

                            <td class="px-4 py-3 align-middle">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset
                                    {{ $chat->priority === 'low' ? 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300' : '' }}
                                    {{ $chat->priority === 'normal' ? 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-500/10 dark:text-sky-400' : '' }}
                                    {{ $chat->priority === 'high' ? 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400' : '' }}
                                    {{ $chat->priority === 'urgent' ? 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400' : '' }}">
                                    {{ $chat->formatted_priority }}
                                </span>
                            </td>

                            <td class="px-4 py-3 align-middle">
                                @if($chat->admin)
                                    <div class="flex items-center gap-2">
                                        @if($chat->admin->avatar)
                                            <img class="h-6 w-6 rounded-full object-cover" src="{{ $chat->admin->avatar_url }}" alt="">
                                        @else
                                            <div class="flex h-6 w-6 rounded-full bg-sky-100 dark:bg-sky-900/30 items-center justify-center">
                                                <i class="fas fa-user text-sky-600 dark:text-sky-400" style="font-size: 0.6rem;"></i>
                                            </div>
                                        @endif
                                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $chat->admin->name }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-slate-400 dark:text-slate-500 italic">Non assigné</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm text-slate-500 dark:text-slate-400">
                                @if($chat->last_message_at)
                                    {{ $chat->last_message_at->diffForHumans() }}
                                @else
                                    -
                                @endif
                            </td>

                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.support.show', $chat) }}"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-sky-600 transition-colors hover:bg-sky-50 dark:hover:bg-sky-900/20"
                                       data-bs-toggle="tooltip"
                                       title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($chat->status !== 'closed')
                                        <button onclick="assignChat({{ $chat->id }})"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-emerald-600 transition-colors hover:bg-emerald-50 dark:hover:bg-emerald-900/20"
                                                data-bs-toggle="tooltip"
                                                title="Assigner">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                        <button onclick="closeChat({{ $chat->id }})"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-600 transition-colors hover:bg-red-50 dark:hover:bg-red-900/20"
                                                data-bs-toggle="tooltip"
                                                title="Fermer">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    @else
                                        <button onclick="reopenChat({{ $chat->id }})"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-emerald-600 transition-colors hover:bg-emerald-50 dark:hover:bg-emerald-900/20"
                                                data-bs-toggle="tooltip"
                                                title="Rouvrir">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Version Mobile/Tablet - Cartes -->
            <div class="lg:hidden">
                <div class="border-b border-slate-100 p-4 transition-colors hover:bg-slate-50 dark:border-slate-700/50 dark:hover:bg-slate-700/30">
                    <div class="mb-2 flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            @if($chat->user?->avatar)
                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $chat->user->avatar_url }}" alt="">
                            @else
                                <div class="flex h-10 w-10 rounded-full bg-slate-200 dark:bg-slate-700 items-center justify-center">
                                    <i class="fas fa-user text-slate-500 dark:text-slate-400 text-xs"></i>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-slate-900 dark:text-white">{{ $chat->reference }}</span>
                                    @if($chat->unread_count_for_admin > 0)
                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                            {{ $chat->unread_count_for_admin }}
                                        </span>
                                    @endif
                                </div>
                                <div class="truncate text-sm text-slate-900 dark:text-white">{{ $chat->user?->name ?? 'Utilisateur supprimé' }}</div>
                                <div class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $chat->user?->email ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.support.show', $chat) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-sky-600 transition-colors hover:bg-sky-50 dark:hover:bg-sky-900/20" data-bs-toggle="tooltip" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($chat->status !== 'closed')
                                <button onclick="assignChat({{ $chat->id }})" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-emerald-600 transition-colors hover:bg-emerald-50 dark:hover:bg-emerald-900/20" data-bs-toggle="tooltip" title="Assigner">
                                    <i class="fas fa-user-plus"></i>
                                </button>
                                <button onclick="closeChat({{ $chat->id }})" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-600 transition-colors hover:bg-red-50 dark:hover:bg-red-900/20" data-bs-toggle="tooltip" title="Fermer">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            @else
                                <button onclick="reopenChat({{ $chat->id }})" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-emerald-600 transition-colors hover:bg-emerald-50 dark:hover:bg-emerald-900/20" data-bs-toggle="tooltip" title="Rouvrir">
                                    <i class="fas fa-undo"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <p class="mb-2 font-medium text-slate-900 dark:text-white text-sm">{{ $chat->subject ?: 'Demande d\'assistance' }}</p>

                    <div class="mb-2 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset
                            {{ $chat->status === 'open' ? 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400' : '' }}
                            {{ $chat->status === 'in_progress' ? 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400' : '' }}
                            {{ $chat->status === 'waiting_user' ? 'bg-violet-50 text-violet-700 ring-violet-600/20 dark:bg-violet-500/10 dark:text-violet-400' : '' }}
                            {{ $chat->status === 'closed' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400' : '' }}">
                            {{ $chat->formatted_status }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset
                            {{ $chat->priority === 'low' ? 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300' : '' }}
                            {{ $chat->priority === 'normal' ? 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-500/10 dark:text-sky-400' : '' }}
                            {{ $chat->priority === 'high' ? 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400' : '' }}
                            {{ $chat->priority === 'urgent' ? 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400' : '' }}">
                            {{ $chat->formatted_priority }}
                        </span>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300">
                            {{ $chat->formatted_category }}
                        </span>
                    </div>

                    <div class="flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400">
                        <span>
                            <i class="fas fa-user-tag text-slate-400"></i>
                            {{ $chat->admin ? $chat->admin->name : 'Non assigné' }}
                        </span>
                        @if($chat->last_message_at)
                            <span>
                                <i class="fas fa-clock text-slate-400"></i>
                                {{ $chat->last_message_at->diffForHumans() }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="py-12 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800">
                    <i class="fas fa-inbox text-3xl text-slate-400"></i>
                </div>
                <h5 class="mb-2 text-lg font-semibold text-slate-900 dark:text-white">
                    @if(request()->has('search') || request()->has('status') || request()->has('priority') || request()->has('category') || request()->has('assigned_to'))
                        Aucune conversation trouvée
                    @else
                        Aucune conversation de support
                    @endif
                </h5>
                <p class="mb-4 text-slate-500 dark:text-slate-400">
                    @if(request()->has('search') || request()->has('status') || request()->has('priority') || request()->has('category') || request()->has('assigned_to'))
                        Aucune conversation ne correspond à vos critères de recherche.
                    @else
                        Les demandes d'assistance apparaîtront ici.
                    @endif
                </p>
                @if(request()->has('search') || request()->has('status') || request()->has('priority') || request()->has('category') || request()->has('assigned_to'))
                    <a href="{{ route('admin.support.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        <i class="fas fa-times"></i>Effacer les filtres
                    </a>
                @endif
            </div>
        @endforelse

        <!-- Pagination -->
        @if($chats->hasPages())
            <div class="border-t border-slate-100 p-4 bg-white dark:border-slate-700 dark:bg-slate-800">
                <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                    <div class="text-center text-xs sm:text-left sm:text-sm text-slate-500 dark:text-slate-300">
                        Affichage de <span class="font-medium text-slate-900 dark:text-white">{{ $chats->firstItem() }}</span> à <span class="font-medium text-slate-900 dark:text-white">{{ $chats->lastItem() }}</span>
                        sur <span class="font-medium text-slate-900 dark:text-white">{{ $chats->total() }}</span> résultats
                    </div>
                    <div class="w-full overflow-x-auto sm:w-auto">
                        {{ $chats->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal d'assignation -->
<div id="assignModal" class="modal-wrapper hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md rounded-xl bg-white shadow-2xl ring-1 ring-slate-200 animate-pop dark:bg-slate-800 dark:ring-slate-700">
        <form id="assignForm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Assigner la conversation</h3>
                <button type="button" onclick="closeAssignModal()" class="rounded-lg text-slate-400 transition-colors hover:text-slate-600 dark:text-slate-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="space-y-3 p-5 sm:p-6">
                @if(isset($agents) && $agents->where('is_active', true)->count() > 0)
                    <button type="button" onclick="autoAssignChat()" class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700 transition-colors hover:bg-emerald-100 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400 dark:hover:bg-emerald-900/40">
                        <i class="fas fa-magic"></i>
                        Auto-assignation (agent le moins chargé)
                    </button>
                    <div class="relative flex items-center">
                        <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                        <span class="flex-shrink mx-3 text-xs text-slate-400">ou manuellement</span>
                        <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                    </div>
                @endif

                <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Sélectionner un agent</label>
                <select id="adminSelect" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                    <option value="">Choisir un agent...</option>
                    @foreach($admins as $admin)
                        <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col-reverse justify-end gap-3 rounded-b-xl bg-slate-50 px-5 py-4 sm:flex-row dark:bg-slate-900">
                <button type="button" onclick="closeAssignModal()" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors sm:w-auto">
                    Annuler
                </button>
                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors sm:w-auto">
                    <i class="fas fa-user-plus"></i>Assigner
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentChatId = null;

// Gestion des tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function assignChat(chatId) {
    currentChatId = chatId;
    document.getElementById('assignModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeAssignModal() {
    document.getElementById('assignModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function autoAssignChat() {
    if (!currentChatId) return;
    fetch(`/admin/support/${currentChatId}/auto-assign`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeAssignModal();
            location.reload();
        } else {
            alert(data.message || 'Aucun agent disponible.');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'auto-assignation.');
    });
}

function closeChat(chatId) {
    if (confirm('Êtes-vous sûr de vouloir fermer cette conversation ?')) {
        fetch(`/admin/support/${chatId}/close`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la fermeture de la conversation.');
        });
    }
}

function reopenChat(chatId) {
    if (confirm('Êtes-vous sûr de vouloir rouvrir cette conversation ?')) {
        fetch(`/admin/support/${chatId}/reopen`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la réouverture de la conversation.');
        });
    }
}

function refreshStats() {
    location.reload();
}

// Gestion du formulaire d'assignation
document.getElementById('assignForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const adminId = document.getElementById('adminSelect').value;

    if (!adminId) {
        alert('Veuillez sélectionner un admin.');
        return;
    }

    fetch(`/admin/support/${currentChatId}/assign`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ admin_id: adminId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeAssignModal();
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors de l\'assignation.');
    });
});

// Fermer le modal avec Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('assignModal').classList.contains('hidden')) {
        closeAssignModal();
    }
});
</script>
@endpush