@extends('layouts.support')

@section('title', 'Gestion des Agents Support')

@section('content')
<div>
    {{-- En-tête --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white">Agents Support</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Gérez l'équipe de support et les assignations</p>
        </div>
        <button onclick="openAddModal()" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
            <i class="fas fa-user-plus"></i>Ajouter un agent
        </button>
    </div>

    {{-- Stats résumé --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 xl:grid-cols-4">
        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Total agents</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white">{{ $agents->count() }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-0.5 text-xs font-medium text-slate-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-300">
                    <i class="fas fa-users text-[10px] text-sky-500"></i>
                    Agents
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-users text-xs text-sky-500"></i>
                    Équipe support
                </div>
                <div class="text-xs text-slate-400">Agents configurés</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Actifs</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-emerald-600">{{ $agents->where('is_active', true)->count() }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <i class="fas fa-user-check text-[10px]"></i>
                    Actifs
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-user-check text-xs text-emerald-500"></i>
                    Disponibles
                </div>
                <div class="text-xs text-slate-400">Reçoivent des tickets</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Tickets en cours</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-amber-600">{{ $agents->sum('active_chats') }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-400">
                    <i class="fas fa-ticket text-[10px]"></i>
                    En cours
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-ticket text-xs text-amber-500"></i>
                    Assignés
                </div>
                <div class="text-xs text-slate-400">Conversations actives</div>
            </div>
        </div>

        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">Total résolus</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-violet-600">{{ $agents->sum('total_resolved') }}</p>
            <div class="absolute right-4 top-4">
                <span class="inline-flex items-center gap-1 rounded-lg border border-violet-200 bg-violet-50 px-2 py-0.5 text-xs font-medium text-violet-700 dark:border-violet-500/30 dark:bg-violet-500/10 dark:text-violet-400">
                    <i class="fas fa-circle-check text-[10px]"></i>
                    Résolus
                </span>
            </div>
            <div class="mt-2.5 flex flex-col gap-0.5 text-sm">
                <div class="flex items-center gap-1.5 font-medium text-slate-700 dark:text-slate-200">
                    <i class="fas fa-circle-check text-xs text-violet-500"></i>
                    Clôturés
                </div>
                <div class="text-xs text-slate-400">Depuis la mise en place</div>
            </div>
        </div>
    </div>

    {{-- Liste des agents --}}
    @if($agents->isEmpty())
        <div class="rounded-xl border border-slate-200 bg-white p-8 text-center shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800">
                <i class="fas fa-users text-2xl text-slate-400"></i>
            </div>
            <h3 class="mb-2 text-lg font-semibold text-slate-900 dark:text-white">Aucun agent configuré</h3>
            <p class="mb-4 text-slate-500 dark:text-slate-400">Ajoutez des agents pour gérer les tickets de support.</p>
            <button onclick="openAddModal()" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                <i class="fas fa-user-plus"></i>Ajouter le premier agent
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4 sm:gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach($agents as $agent)
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 agent-card" data-agent-id="{{ $agent->id }}">
                    {{-- Header agent --}}
                    <div class="p-4 sm:p-5">
                        <div class="mb-3 flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-primary-500 to-primary-600 text-sm font-bold text-white shadow-sm">
                                        {{ strtoupper(substr($agent->user->name, 0, 2)) }}
                                    </div>
                                    <span class="absolute -bottom-0.5 -right-0.5 h-3.5 w-3.5 rounded-full border-2 border-white dark:border-slate-800 {{ $agent->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-slate-900 dark:text-white">{{ $agent->user->name }}</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $agent->user->email }}</p>
                                </div>
                            </div>
                            {{-- Menu actions --}}
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="rounded-lg p-1.5 text-slate-400 transition-colors hover:text-slate-600 dark:hover:text-slate-300">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition
                                     class="absolute right-0 z-10 mt-1 w-48 origin-top-right rounded-xl border border-slate-200 bg-white py-1.5 shadow-lg dark:border-slate-700 dark:bg-slate-800">
                                    <button onclick="editAgent({{ $agent->id }}, {{ $agent->max_chats }}, {{ json_encode($agent->specialties ?? []) }})" @click="open = false"
                                            class="w-full px-4 py-2 text-left text-sm text-slate-700 transition-colors hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700">
                                        <i class="fas fa-cog mr-2 text-slate-400"></i>Paramètres
                                    </button>
                                    <button onclick="toggleAgent({{ $agent->id }})" @click="open = false"
                                            class="w-full px-4 py-2 text-left text-sm {{ $agent->is_active ? 'text-amber-600' : 'text-emerald-600' }} transition-colors hover:bg-slate-100 dark:hover:bg-slate-700">
                                        <i class="fas fa-{{ $agent->is_active ? 'pause' : 'play' }} mr-2"></i>{{ $agent->is_active ? 'Désactiver' : 'Activer' }}
                                    </button>
                                    <hr class="my-1 border-slate-200 dark:border-slate-700">
                                    <button onclick="removeAgent({{ $agent->id }}, '{{ addslashes($agent->user->name) }}')" @click="open = false"
                                            class="w-full px-4 py-2 text-left text-sm text-red-600 transition-colors hover:bg-red-50 dark:hover:bg-red-900/20">
                                        <i class="fas fa-trash mr-2"></i>Retirer
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Charge de travail --}}
                        <div class="mb-3">
                            <div class="mb-1 flex items-center justify-between text-xs">
                                <span class="text-slate-500 dark:text-slate-400">Charge</span>
                                <span class="font-medium {{ $agent->active_chats >= $agent->max_chats ? 'text-red-600' : ($agent->active_chats >= $agent->max_chats * 0.7 ? 'text-amber-600' : 'text-emerald-600') }}">
                                    {{ $agent->active_chats }}/{{ $agent->max_chats }}
                                </span>
                            </div>
                            @php
                                $pct = $agent->max_chats > 0 ? min(100, ($agent->active_chats / $agent->max_chats) * 100) : 0;
                                $color = $pct >= 100 ? 'bg-red-500' : ($pct >= 70 ? 'bg-amber-500' : 'bg-emerald-500');
                            @endphp
                            <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700">
                                <div class="{{ $color }} h-full rounded-full transition-all" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>

                        {{-- Stats rapides --}}
                        <div class="mb-3 grid grid-cols-2 gap-2">
                            <div class="rounded-lg bg-slate-50 px-3 py-2 text-center dark:bg-slate-700/50">
                                <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $agent->active_chats }}</p>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400">En cours</p>
                            </div>
                            <div class="rounded-lg bg-slate-50 px-3 py-2 text-center dark:bg-slate-700/50">
                                <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $agent->total_resolved }}</p>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400">Résolus</p>
                            </div>
                        </div>

                        {{-- Spécialités --}}
                        @if(!empty($agent->specialties))
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($agent->specialties as $spec)
                                    @php
                                        $specColors = [
                                            'technical' => 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-500/10 dark:text-sky-400',
                                            'account' => 'bg-violet-50 text-violet-700 ring-violet-600/20 dark:bg-violet-500/10 dark:text-violet-400',
                                            'payment' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400',
                                            'order' => 'bg-orange-50 text-orange-700 ring-orange-600/20 dark:bg-orange-500/10 dark:text-orange-400',
                                            'general' => 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-400',
                                        ];
                                        $specLabels = [
                                            'technical' => 'Technique',
                                            'account' => 'Compte',
                                            'payment' => 'Paiement',
                                            'order' => 'Commande',
                                            'general' => 'Général',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $specColors[$spec] ?? 'bg-slate-100 text-slate-700 ring-slate-500/20' }}">
                                        {{ $specLabels[$spec] ?? ucfirst($spec) }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs italic text-slate-400">Aucune spécialité</p>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-700/30 sm:px-5">
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $agent->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-400' }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $agent->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                            {{ $agent->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                        @if($agent->last_assigned_at)
                            <span class="text-[10px] text-slate-400" title="{{ $agent->last_assigned_at->format('d/m/Y H:i') }}">
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
<div id="addAgentModal" class="modal-wrapper hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="w-full max-w-lg rounded-xl bg-white shadow-2xl ring-1 ring-slate-200 animate-pop dark:bg-slate-800 dark:ring-slate-700">
        <form id="addAgentForm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">
                    <i class="fas fa-user-plus mr-2 text-primary-600"></i>Ajouter un agent
                </h3>
                <button type="button" onclick="closeAddModal()" class="rounded-lg text-slate-400 transition-colors hover:text-slate-600 dark:text-slate-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="space-y-4 p-5 sm:p-6">
                {{-- Utilisateur --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Utilisateur</label>
                    <select id="agentUserId" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <option value="">Sélectionner un utilisateur...</option>
                        @foreach($availableUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Max tickets --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Tickets max simultanés</label>
                    <input type="number" id="agentMaxChats" value="10" min="1" max="50"
                           class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">L'agent ne recevra plus de tickets au-delà de cette limite</p>
                </div>

                {{-- Spécialités --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Spécialités</label>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                        @php
                            $specLabels = [
                                'technical' => ['Technique', 'fa-cog', 'sky'],
                                'account' => ['Compte', 'fa-user', 'violet'],
                                'payment' => ['Paiement', 'fa-credit-card', 'emerald'],
                                'order' => ['Commande', 'fa-shopping-bag', 'orange'],
                                'general' => ['Général', 'fa-globe', 'slate'],
                            ];
                        @endphp
                        @foreach($specLabels as $key => [$label, $icon, $color])
                            <label class="flex items-center gap-2 rounded-lg border border-slate-200 p-2 transition-colors cursor-pointer hover:bg-slate-50 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50 dark:border-slate-600 dark:hover:bg-slate-700 dark:has-[:checked]:bg-primary-900/20">
                                <input type="checkbox" name="specialties[]" value="{{ $key }}" class="agent-specialty rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                                <i class="fas {{ $icon }} text-xs text-{{ $color }}-500"></i>
                                <span class="text-sm text-slate-700 dark:text-slate-300">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse justify-end gap-3 rounded-b-xl bg-slate-50 px-5 py-4 sm:flex-row dark:bg-slate-900">
                <button type="button" onclick="closeAddModal()" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors sm:w-auto">
                    Annuler
                </button>
                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors sm:w-auto">
                    <i class="fas fa-plus"></i>Ajouter
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Paramètres Agent --}}
<div id="editAgentModal" class="modal-wrapper hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="w-full max-w-lg rounded-xl bg-white shadow-2xl ring-1 ring-slate-200 animate-pop dark:bg-slate-800 dark:ring-slate-700">
        <form id="editAgentForm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">
                    <i class="fas fa-cog mr-2 text-slate-500"></i>Paramètres de l'agent
                </h3>
                <button type="button" onclick="closeEditModal()" class="rounded-lg text-slate-400 transition-colors hover:text-slate-600 dark:text-slate-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="space-y-4 p-5 sm:p-6">
                <input type="hidden" id="editAgentId">

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Tickets max simultanés</label>
                    <input type="number" id="editMaxChats" min="1" max="50"
                           class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Spécialités</label>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                        @foreach($specLabels as $key => [$label, $icon, $color])
                            <label class="flex items-center gap-2 rounded-lg border border-slate-200 p-2 transition-colors cursor-pointer hover:bg-slate-50 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50 dark:border-slate-600 dark:hover:bg-slate-700 dark:has-[:checked]:bg-primary-900/20">
                                <input type="checkbox" name="edit_specialties[]" value="{{ $key }}" class="edit-specialty rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                                <i class="fas {{ $icon }} text-xs text-{{ $color }}-500"></i>
                                <span class="text-sm text-slate-700 dark:text-slate-300">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse justify-end gap-3 rounded-b-xl bg-slate-50 px-5 py-4 sm:flex-row dark:bg-slate-900">
                <button type="button" onclick="closeEditModal()" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors sm:w-auto">
                    Annuler
                </button>
                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors sm:w-auto">
                    <i class="fas fa-save"></i>Enregistrer
                </button>
            </div>
        </form>
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