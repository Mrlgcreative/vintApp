@extends('layouts.support')

@section('title', 'Gestion des Agents Support')

@section('content')
<div>
    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Agents Support</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Gérez l'équipe de support et les assignations</p>
        </div>
        <button onclick="openAddModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
            <i class="fas fa-user-plus mr-2"></i>Ajouter un agent
        </button>
    </div>

    {{-- Stats résumé --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-blue-500 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total agents</p>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $agents->count() }}</h3>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-green-500 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Actifs</p>
            <h3 class="text-2xl font-bold text-green-600">{{ $agents->where('is_active', true)->count() }}</h3>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-yellow-500 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Tickets en cours</p>
            <h3 class="text-2xl font-bold text-yellow-600">{{ $agents->sum('active_chats') }}</h3>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-l-4 border-purple-500 p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total résolus</p>
            <h3 class="text-2xl font-bold text-purple-600">{{ $agents->sum('total_resolved') }}</h3>
        </div>
    </div>

    {{-- Liste des agents --}}
    @if($agents->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-8 text-center">
            <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-users text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucun agent configuré</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Ajoutez des agents pour gérer les tickets de support.</p>
            <button onclick="openAddModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                <i class="fas fa-user-plus mr-2"></i>Ajouter le premier agent
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($agents as $agent)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden agent-card" data-agent-id="{{ $agent->id }}">
                    {{-- Header agent --}}
                    <div class="p-4 sm:p-5">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <div class="w-11 h-11 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                                        {{ strtoupper(substr($agent->user->name, 0, 2)) }}
                                    </div>
                                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white dark:border-gray-800 {{ $agent->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $agent->user->name }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $agent->user->email }}</p>
                                </div>
                            </div>
                            {{-- Menu actions --}}
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg transition-colors">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition
                                     class="absolute right-0 mt-1 w-44 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 py-1 z-10">
                                    <button onclick="editAgent({{ $agent->id }}, {{ $agent->max_chats }}, {{ json_encode($agent->specialties ?? []) }})" @click="open = false"
                                            class="w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        <i class="fas fa-cog mr-2 text-gray-400"></i>Paramètres
                                    </button>
                                    <button onclick="toggleAgent({{ $agent->id }})" @click="open = false"
                                            class="w-full text-left px-3 py-2 text-sm {{ $agent->is_active ? 'text-yellow-600' : 'text-green-600' }} hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        <i class="fas fa-{{ $agent->is_active ? 'pause' : 'play' }} mr-2"></i>{{ $agent->is_active ? 'Désactiver' : 'Activer' }}
                                    </button>
                                    <hr class="my-1 border-gray-200 dark:border-gray-600">
                                    <button onclick="removeAgent({{ $agent->id }}, '{{ addslashes($agent->user->name) }}')" @click="open = false"
                                            class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                        <i class="fas fa-trash mr-2"></i>Retirer
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Charge de travail --}}
                        <div class="mb-3">
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="text-gray-500 dark:text-gray-400">Charge</span>
                                <span class="font-medium {{ $agent->active_chats >= $agent->max_chats ? 'text-red-600' : ($agent->active_chats >= $agent->max_chats * 0.7 ? 'text-yellow-600' : 'text-green-600') }}">
                                    {{ $agent->active_chats }}/{{ $agent->max_chats }}
                                </span>
                            </div>
                            @php
                                $pct = $agent->max_chats > 0 ? min(100, ($agent->active_chats / $agent->max_chats) * 100) : 0;
                                $color = $pct >= 100 ? 'bg-red-500' : ($pct >= 70 ? 'bg-yellow-500' : 'bg-green-500');
                            @endphp
                            <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="{{ $color }} h-full rounded-full transition-all" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>

                        {{-- Stats rapides --}}
                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2 text-center">
                                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $agent->active_chats }}</p>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400">En cours</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2 text-center">
                                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $agent->total_resolved }}</p>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400">Résolus</p>
                            </div>
                        </div>

                        {{-- Spécialités --}}
                        @if(!empty($agent->specialties))
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($agent->specialties as $spec)
                                    @php
                                        $specColors = [
                                            'technical' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                            'account' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                            'payment' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                            'order' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                                            'general' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400',
                                        ];
                                        $specLabels = [
                                            'technical' => 'Technique',
                                            'account' => 'Compte',
                                            'payment' => 'Paiement',
                                            'order' => 'Commande',
                                            'general' => 'Général',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-medium rounded-full {{ $specColors[$spec] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $specLabels[$spec] ?? ucfirst($spec) }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-400 italic">Aucune spécialité</p>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="px-4 sm:px-5 py-2.5 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full {{ $agent->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $agent->is_active ? 'bg-green-500' : 'bg-gray-400' }} mr-1.5"></span>
                            {{ $agent->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                        @if($agent->last_assigned_at)
                            <span class="text-[10px] text-gray-400" title="{{ $agent->last_assigned_at->format('d/m/Y H:i') }}">
                                Dernier: {{ $agent->last_assigned_at->diffForHumans() }}
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Modal Ajouter Agent --}}
<div id="addAgentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="closeAddModal()"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg mx-4">
            <form id="addAgentForm">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-user-plus mr-2 text-blue-600"></i>Ajouter un agent
                    </h3>
                    <button type="button" onclick="closeAddModal()" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="px-6 py-4 space-y-4">
                    {{-- Utilisateur --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Utilisateur</label>
                        <select id="agentUserId" required class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Sélectionner un utilisateur...</option>
                            @foreach($availableUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Max tickets --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tickets max simultanés</label>
                        <input type="number" id="agentMaxChats" value="10" min="1" max="50"
                               class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">L'agent ne recevra plus de tickets au-delà de cette limite</p>
                    </div>

                    {{-- Spécialités --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Spécialités</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @php
                                $specLabels = [
                                    'technical' => ['Technique', 'fa-cog', 'blue'],
                                    'account' => ['Compte', 'fa-user', 'purple'],
                                    'payment' => ['Paiement', 'fa-credit-card', 'green'],
                                    'order' => ['Commande', 'fa-shopping-bag', 'orange'],
                                    'general' => ['Général', 'fa-globe', 'gray'],
                                ];
                            @endphp
                            @foreach($specLabels as $key => [$label, $icon, $color])
                                <label class="flex items-center gap-2 p-2 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-900/20">
                                    <input type="checkbox" name="specialties[]" value="{{ $key }}" class="agent-specialty rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <i class="fas {{ $icon }} text-xs text-{{ $color }}-500"></i>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus mr-1.5"></i>Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Paramètres Agent --}}
<div id="editAgentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="closeEditModal()"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg mx-4">
            <form id="editAgentForm">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-cog mr-2 text-gray-500"></i>Paramètres de l'agent
                    </h3>
                    <button type="button" onclick="closeEditModal()" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="px-6 py-4 space-y-4">
                    <input type="hidden" id="editAgentId">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tickets max simultanés</label>
                        <input type="number" id="editMaxChats" min="1" max="50"
                               class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Spécialités</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($specLabels as $key => [$label, $icon, $color])
                                <label class="flex items-center gap-2 p-2 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-900/20">
                                    <input type="checkbox" name="edit_specialties[]" value="{{ $key }}" class="edit-specialty rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <i class="fas {{ $icon }} text-xs text-{{ $color }}-500"></i>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-save mr-1.5"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// === Modal Ajouter ===
function openAddModal() {
    document.getElementById('addAgentModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeAddModal() {
    document.getElementById('addAgentModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.getElementById('addAgentForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const userId = document.getElementById('agentUserId').value;
    if (!userId) { alert('Sélectionnez un utilisateur.'); return; }

    const specialties = [...document.querySelectorAll('.agent-specialty:checked')].map(el => el.value);

    fetch('{{ route("admin.support.agents.add") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({
            user_id: userId,
            max_chats: parseInt(document.getElementById('agentMaxChats').value) || 10,
            specialties: specialties
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeAddModal();
            location.reload();
        } else {
            alert(data.message || 'Erreur');
        }
    })
    .catch(() => alert('Erreur réseau.'));
});

// === Modal Paramètres ===
function editAgent(id, maxChats, specialties) {
    document.getElementById('editAgentId').value = id;
    document.getElementById('editMaxChats').value = maxChats;

    document.querySelectorAll('.edit-specialty').forEach(el => {
        el.checked = specialties.includes(el.value);
    });

    document.getElementById('editAgentModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    document.getElementById('editAgentModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.getElementById('editAgentForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const agentId = document.getElementById('editAgentId').value;
    const specialties = [...document.querySelectorAll('.edit-specialty:checked')].map(el => el.value);

    fetch(`/admin/support/agents/${agentId}`, {
        method: 'PUT',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({
            max_chats: parseInt(document.getElementById('editMaxChats').value) || 10,
            specialties: specialties
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeEditModal();
            location.reload();
        } else {
            alert(data.message || 'Erreur');
        }
    })
    .catch(() => alert('Erreur réseau.'));
});

// === Toggle / Remove ===
function toggleAgent(id) {
    fetch(`/admin/support/agents/${id}/toggle`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
        else alert(data.message || 'Erreur');
    })
    .catch(() => alert('Erreur réseau.'));
}

function removeAgent(id, name) {
    if (!confirm(`Retirer ${name} de l'équipe support ?`)) return;

    fetch(`/admin/support/agents/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
        else alert(data.message || 'Erreur');
    })
    .catch(() => alert('Erreur réseau.'));
}

// Escape pour fermer les modals
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddModal();
        closeEditModal();
    }
});
</script>
@endpush
