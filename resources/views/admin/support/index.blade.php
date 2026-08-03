@extends('layouts.support')

@section('title', 'Tickets Support')

@section('content')
<div>
    <!-- En-tête avec statistiques -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white">Support Client</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1">Gérez les demandes d'assistance des utilisateurs</p>
            </div>
            <div class="flex gap-2">
                <button onclick="refreshStats()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>Actualiser
                </button>
                <a href="{{ route('admin.support.stats') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-chart-bar mr-2"></i>Statistiques
                </a>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
            <!-- Total -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border-l-4 border-blue-500 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Total</p>
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['total'] }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <i class="fas fa-comments text-blue-600 dark:text-blue-400"></i>
                    </div>
                </div>
            </div>

            <!-- Ouvert -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border-l-4 border-red-500 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Ouvert</p>
                        <h3 class="text-2xl font-bold text-red-600">{{ $stats['open'] }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400"></i>
                    </div>
                </div>
            </div>

            <!-- En cours -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border-l-4 border-yellow-500 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">En cours</p>
                        <h3 class="text-2xl font-bold text-yellow-600">{{ $stats['in_progress'] }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                </div>
            </div>

            <!-- En attente -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border-l-4 border-violet-500 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">En attente</p>
                        <h3 class="text-2xl font-bold text-violet-600">{{ $stats['waiting_user'] }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center">
                        <i class="fas fa-hourglass-half text-violet-600 dark:text-violet-400"></i>
                    </div>
                </div>
            </div>

            <!-- Fermés aujourd'hui -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border-l-4 border-green-500 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Fermés aujourd'hui</p>
                        <h3 class="text-2xl font-bold text-green-600">{{ $stats['closed_today'] }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                    </div>
                </div>
            </div>

            <!-- Non assignés -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border-l-4 border-amber-500 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Non assignés</p>
                        <h3 class="text-2xl font-bold text-amber-600">{{ $stats['unassigned'] }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                        <i class="fas fa-user-times text-amber-600 dark:text-amber-400"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm mb-6">
        <div class="p-4 sm:p-6">
            <form method="GET" action="{{ route('admin.support.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Recherche</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Référence, sujet..." 
                           class="w-full px-3 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Statut</label>
                    <select name="status" class="w-full px-3 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous les statuts</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Ouvert</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>En cours</option>
                        <option value="waiting_user" {{ request('status') === 'waiting_user' ? 'selected' : '' }}>En attente utilisateur</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Fermé</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Priorité</label>
                    <select name="priority" class="w-full px-3 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Toutes les priorités</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Faible</option>
                        <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normale</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Élevée</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Catégorie</label>
                    <select name="category" class="w-full px-3 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Toutes les catégories</option>
                        <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>Technique</option>
                        <option value="account" {{ request('category') === 'account' ? 'selected' : '' }}>Compte</option>
                        <option value="payment" {{ request('category') === 'payment' ? 'selected' : '' }}>Paiement</option>
                        <option value="order" {{ request('category') === 'order' ? 'selected' : '' }}>Commande</option>
                        <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>Général</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Assigné à</label>
                    <select name="assigned_to" class="w-full px-3 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-search mr-1"></i>Filtrer
                    </button>
                    <a href="{{ route('admin.support.index') }}" class="inline-flex items-center justify-center px-3 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-lg transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des conversations -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Référence</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Utilisateur</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Sujet</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Priorité</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Assigné à</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Dernier message</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($chats as $chat)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-slate-900 dark:text-white">{{ $chat->reference }}</span>
                                    @if($chat->unread_count_for_admin > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded-full">
                                            {{ $chat->unread_count_for_admin }} nouveau(x)
                                        </span>
                                    @endif
                                </div>
                            </td>
                            
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    @if($chat->user?->avatar)
                                        <img class="w-8 h-8 rounded-full object-cover" src="{{ $chat->user->avatar_url }}" alt="">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-600 flex items-center justify-center">
                                            <i class="fas fa-user text-slate-500 dark:text-slate-400 text-xs"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-medium text-slate-900 dark:text-white text-sm">{{ $chat->user?->name ?? 'Utilisateur supprimé' }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $chat->user?->email ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-4 py-4">
                                <div class="font-medium text-slate-900 dark:text-white text-sm">
                                    {{ $chat->subject ?: 'Demande d\'assistance' }}
                                </div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ $chat->formatted_category }}</div>
                            </td>
                            
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full
                                    {{ $chat->status === 'open' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                    {{ $chat->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                    {{ $chat->status === 'waiting_user' ? 'bg-violet-100 text-violet-800 dark:bg-violet-900/30 dark:text-violet-400' : '' }}
                                    {{ $chat->status === 'closed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}">
                                    {{ $chat->formatted_status }}
                                </span>
                            </td>
                            
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full
                                    {{ $chat->priority === 'low' ? 'bg-slate-100 text-slate-800 dark:bg-slate-600 dark:text-slate-300' : '' }}
                                    {{ $chat->priority === 'normal' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                    {{ $chat->priority === 'high' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400' : '' }}
                                    {{ $chat->priority === 'urgent' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}">
                                    {{ $chat->formatted_priority }}
                                </span>
                            </td>
                            
                            <td class="px-4 py-4">
                                @if($chat->admin)
                                    <div class="flex items-center gap-2">
                                        @if($chat->admin->avatar)
                                            <img class="w-6 h-6 rounded-full object-cover" src="{{ $chat->admin->avatar_url }}" alt="">
                                        @else
                                            <div class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                                <i class="fas fa-user text-blue-600 dark:text-blue-400" style="font-size: 0.6rem;"></i>
                                            </div>
                                        @endif
                                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $chat->admin->name }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-slate-400 dark:text-slate-500 italic">Non assigné</span>
                                @endif
                            </td>
                            
                            <td class="px-4 py-4 text-sm text-slate-500 dark:text-slate-400">
                                @if($chat->last_message_at)
                                    {{ $chat->last_message_at->diffForHumans() }}
                                @else
                                    -
                                @endif
                            </td>
                            
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.support.show', $chat) }}" 
                                       class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($chat->status !== 'closed')
                                        <button onclick="assignChat({{ $chat->id }})" 
                                                class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors" 
                                                title="Assigner">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                        <button onclick="closeChat({{ $chat->id }})" 
                                                class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" 
                                                title="Fermer">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    @else
                                        <button onclick="reopenChat({{ $chat->id }})" 
                                                class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors" 
                                                title="Rouvrir">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <div class="text-slate-400 dark:text-slate-500">
                                    <i class="fas fa-comments text-5xl mb-4"></i>
                                    <p class="text-lg font-medium mb-1">Aucune conversation de support</p>
                                    <p class="text-sm">Les demandes d'assistance apparaîtront ici.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($chats->hasPages())
            <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
                {{ $chats->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal d'assignation -->
<div id="assignModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black/50 transition-opacity modal-overlay" onclick="closeAssignModal()"></div>
        
        <!-- Modal content -->
        <div class="relative bg-white dark:bg-slate-800 rounded-xl shadow-xl transform transition-all sm:my-8 sm:max-w-md w-full mx-4">
            <form id="assignForm">
                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Assigner la conversation</h3>
                    <button type="button" onclick="closeAssignModal()" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 rounded-lg transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Body -->
                <div class="px-6 py-4 space-y-3">
                    {{-- Auto-assign --}}
                    @if(isset($agents) && $agents->where('is_active', true)->count() > 0)
                        <button type="button" onclick="autoAssignChat()" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors font-medium text-sm">
                            <i class="fas fa-magic"></i>
                            Auto-assignation (agent le moins chargé)
                        </button>
                        <div class="relative flex items-center">
                            <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                            <span class="flex-shrink mx-3 text-xs text-slate-400">ou manuellement</span>
                            <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                        </div>
                    @endif

                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Sélectionner un agent</label>
                    <select id="adminSelect" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Choisir un agent...</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Footer -->
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" onclick="closeAssignModal()" class="px-4 py-2 text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg font-medium transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        Assigner
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentChatId = null;

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
@endsection