@extends('layouts.admin')

@section('title', 'Gestion des pré-inscriptions')
@section('page-title', 'Gestion des pré-inscriptions')

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.waiting-users.export') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-file-export"></i>Exporter CSV
    </a>
</div>
@endsection

@section('content')
<!-- Statistiques -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Total</p>
                <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['total'] }}</div>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 dark:bg-indigo-900/30">
                <i class="fas fa-users text-2xl text-indigo-600 dark:text-indigo-300"></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">En attente</p>
                <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['pending'] }}</div>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/30">
                <i class="fas fa-hourglass-half text-2xl text-amber-600 dark:text-amber-300"></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Approuvés</p>
                <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['approved'] }}</div>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900/30">
                <i class="fas fa-check-circle text-2xl text-emerald-600 dark:text-emerald-300"></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Convertis</p>
                <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['converted'] }}</div>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-100 dark:bg-violet-900/30">
                <i class="fas fa-user-check text-2xl text-violet-600 dark:text-violet-300"></i>
            </div>
        </div>
    </div>
</div>

<!-- Stats supplémentaires -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-6">
    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5 text-center">
        <i class="fas fa-calendar-day text-indigo-500 mb-2 text-2xl"></i>
        <div class="text-xl font-bold text-slate-900 dark:text-white">{{ $stats['today'] }}</div>
        <p class="text-xs text-slate-500 dark:text-slate-400">Aujourd'hui</p>
    </div>
    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5 text-center">
        <i class="fas fa-calendar-week text-sky-500 mb-2 text-2xl"></i>
        <div class="text-xl font-bold text-slate-900 dark:text-white">{{ $stats['this_week'] }}</div>
        <p class="text-xs text-slate-500 dark:text-slate-400">Cette semaine</p>
    </div>
    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5 text-center">
        <i class="fas fa-calendar-alt text-emerald-500 mb-2 text-2xl"></i>
        <div class="text-xl font-bold text-slate-900 dark:text-white">{{ $stats['this_month'] }}</div>
        <p class="text-xs text-slate-500 dark:text-slate-400">Ce mois</p>
    </div>
</div>

<!-- Filtres et recherche -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm mb-6">
    <div class="p-5 sm:p-6">
        <form method="GET" action="{{ route('admin.waiting-users.index') }}" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                    <i class="fas fa-search mr-1"></i>Recherche
                </label>
                <input type="text" name="search" placeholder="Nom, email, téléphone..."
                       value="{{ request('search') }}"
                       class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                    <i class="fas fa-filter mr-1"></i>Statut
                </label>
                <select name="status" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">
                    <option value="">Tous</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvé</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                    <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converti</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                    <i class="fas fa-calendar mr-1"></i>Date début
                </label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                    <i class="fas fa-calendar mr-1"></i>Date fin
                </label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                    <i class="fas fa-search"></i>Filtrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Liste des pré-inscriptions -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3">
            <h5 class="font-semibold text-slate-900 dark:text-white">
                <i class="fas fa-list mr-2"></i>Liste des pré-inscriptions ({{ $waitingUsers->total() }})
            </h5>
            <div class="flex flex-wrap gap-2">
                <button type="button" onclick="bulkAction('approve')" id="bulkApproveBtn"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 px-3 py-1.5 text-xs font-medium text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <i class="fas fa-check"></i>Approuver
                </button>
                <button type="button" onclick="bulkAction('reject')" id="bulkRejectBtn"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 hover:bg-red-700 px-3 py-1.5 text-xs font-medium text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <i class="fas fa-times"></i>Rejeter
                </button>
            </div>
        </div>
    </div>

    @if($waitingUsers->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()"
                                   class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500 dark:border-slate-600 dark:bg-slate-800">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Nom</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Téléphone</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pays</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Date inscription</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Attente</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($waitingUsers as $user)
                        <tr class="border-t border-slate-100 dark:border-slate-700/50 transition-colors hover:bg-slate-50 dark:hover:bg-slate-700/30">
                            <td class="px-4 py-3 align-middle">
                                <input type="checkbox" class="user-checkbox h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500 dark:border-slate-600 dark:bg-slate-800" value="{{ $user->id }}">
                            </td>
                            <td class="px-4 py-3 align-middle font-semibold text-slate-900 dark:text-white">{{ $user->name }}</td>
                            <td class="px-4 py-3 align-middle">
                                <span class="text-slate-700 dark:text-slate-200">{{ $user->email }}</span>
                                @if($user->email_confirmed_at)
                                    <i class="fas fa-check-circle text-emerald-500 ml-1" title="Email confirmé"></i>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-middle text-slate-700 dark:text-slate-200">{{ $user->phone ?? '-' }}</td>
                            <td class="px-4 py-3 align-middle text-slate-700 dark:text-slate-200">{{ $user->country }}</td>
                            <td class="px-4 py-3 align-middle">{!! $user->status_badge !!}</td>
                            <td class="px-4 py-3 align-middle text-slate-700 dark:text-slate-200">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 align-middle">
                                @php
                                    $waitingClass = $user->waiting_days > 7
                                        ? 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-300'
                                        : ($user->waiting_days > 3
                                            ? 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300'
                                            : 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300');
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $waitingClass }}">
                                    {{ $user->waiting_days }}
                                </span>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('admin.waiting-users.show', $user) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sky-600 hover:bg-sky-50 dark:hover:bg-sky-900/20 transition-colors"
                                       title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($user->status === 'confirmed' || $user->status === 'pending')
                                        <form action="{{ route('admin.waiting-users.approve', $user) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors"
                                                    title="Approuver">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>

                                        <button type="button" onclick="showRejectModal({{ $user->id }})"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors"
                                                title="Rejeter">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif

                                    <form action="{{ route('admin.waiting-users.destroy', $user) }}" method="POST"
                                          onsubmit="return confirm('Supprimer cette pré-inscription ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-slate-400">
                                <i class="fas fa-inbox text-4xl text-slate-200 dark:text-slate-600 mb-3 block"></i>
                                Aucune pré-inscription trouvée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($waitingUsers->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-700">
                {{ $waitingUsers->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <i class="fas fa-inbox text-4xl text-slate-200 dark:text-slate-600 mb-3 block"></i>
            <p class="text-slate-400">Aucune pré-inscription trouvée</p>
        </div>
    @endif
</div>

<!-- Modal de rejet -->
<div id="rejectModal" class="modal-wrapper hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-800 shadow-2xl ring-1 ring-slate-200 dark:ring-slate-700 animate-pop">
        <div class="flex items-center justify-between bg-amber-50 dark:bg-amber-900/20 px-5 py-4 border-b border-amber-100 dark:border-amber-800">
            <h5 class="text-base font-semibold text-amber-800 dark:text-amber-200 flex items-center gap-2">
                <i class="fas fa-times-circle"></i>Rejeter la pré-inscription
            </h5>
            <button type="button" onclick="closeRejectModal()" class="text-amber-400 hover:text-amber-600 transition-colors">
                <i class="fas fa-xmark text-xl"></i>
            </button>
        </div>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="p-5 sm:p-6">
                <label for="reason" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Raison du rejet</label>
                <textarea id="reason" name="reason" rows="3"
                          placeholder="Expliquez pourquoi cette demande est rejetée..."
                          class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500/40 focus:border-primary-500 transition-colors"></textarea>
            </div>
            <div class="bg-slate-50 dark:bg-slate-900 rounded-b-2xl px-5 py-4 flex flex-col-reverse sm:flex-row justify-end gap-3">
                <button type="button" onclick="closeRejectModal()"
                        class="inline-flex justify-center items-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors w-full sm:w-auto">
                    Annuler
                </button>
                <button type="submit"
                        class="inline-flex justify-center items-center gap-2 w-full sm:w-auto rounded-xl bg-red-600 hover:bg-red-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
                    <i class="fas fa-times-circle"></i>Rejeter
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.user-checkbox').forEach(cb => {
        cb.addEventListener('change', updateBulkButtons);
    });

    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateBulkButtons();
    }

    function updateBulkButtons() {
        const checked = document.querySelectorAll('.user-checkbox:checked').length;
        document.getElementById('bulkApproveBtn').disabled = checked === 0;
        document.getElementById('bulkRejectBtn').disabled = checked === 0;
    }

    function bulkAction(action) {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        const userIds = Array.from(checkedBoxes).map(cb => cb.value);
        if (userIds.length === 0) return;
        if (!confirm(`Confirmer cette action pour ${userIds.length} utilisateur(s) ?`)) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.waiting-users.bulk-action") }}';
        form.innerHTML = `
            @csrf
            <input type="hidden" name="action" value="${action}">
            ${userIds.map(id => `<input type="hidden" name="user_ids[]" value="${id}">`).join('')}
        `;
        document.body.appendChild(form);
        form.submit();
    }

    function showRejectModal(userId) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        form.action = `/admin/waiting-users/${userId}/reject`;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') closeRejectModal();
    });
</script>
@endpush
