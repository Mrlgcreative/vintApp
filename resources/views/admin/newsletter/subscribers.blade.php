@extends('layouts.admin')

@section('title', 'Newsletter')
@section('page-title', 'Newsletter')

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.settings.newsletter.send') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-paper-plane"></i>Envoyer une Newsletter
    </a>
    <a href="{{ route('admin.settings.newsletter.export') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-download"></i>Exporter CSV
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-900/20 px-4 py-3 animate-fade-in">
            <i class="fas fa-check-circle text-emerald-600 dark:text-emerald-400"></i>
            <span class="flex-1 text-sm font-medium text-emerald-800 dark:text-emerald-200">{{ session('success') }}</span>
            <button type="button" class="text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-200" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20 px-4 py-3 animate-fade-in">
            <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400"></i>
            <span class="flex-1 text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</span>
            <button type="button" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Statistiques -->
    <div class="grid grid-cols-2 gap-4 sm:gap-6 md:grid-cols-3 xl:grid-cols-6">
        <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Total</p>
                    <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['total'] }}</div>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/30">
                    <i class="fas fa-users text-blue-600 dark:text-blue-300"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Actifs</p>
                    <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['active'] }}</div>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900/30">
                    <i class="fas fa-user-check text-emerald-600 dark:text-emerald-300"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Vérifiés</p>
                    <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['verified'] }}</div>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-cyan-100 dark:bg-cyan-900/30">
                    <i class="fas fa-envelope-circle-check text-cyan-600 dark:text-cyan-300"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Envoyés</p>
                    <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($stats['total_emails_sent']) }}</div>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-100 dark:bg-orange-900/30">
                    <i class="fas fa-paper-plane text-orange-600 dark:text-orange-300"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Ouverts</p>
                    <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($stats['total_emails_opened']) }}</div>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary-100 dark:bg-primary-900/30">
                    <i class="fas fa-envelope-open text-primary-600 dark:text-primary-300"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Clics</p>
                    <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($stats['total_clicks']) }}</div>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-100 dark:bg-red-900/30">
                    <i class="fas fa-mouse-pointer text-red-600 dark:text-red-300"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des abonnés -->
    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 px-5 py-4">
            <h3 class="font-semibold text-slate-900 dark:text-white">
                <i class="fas fa-list mr-2 text-primary-600"></i>
                Liste des abonnés ({{ $subscribers->total() }})
            </h3>
        </div>

        @if($subscribers->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Nom</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Statut</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Vérifié</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Préférences</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Statistiques</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Inscription</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                        @forelse($subscribers as $subscriber)
                            <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-700/30">
                                <td class="whitespace-nowrap px-4 py-3.5 text-slate-900 dark:text-white">{{ $subscriber->id }}</td>
                                <td class="whitespace-nowrap px-4 py-3.5 text-slate-900 dark:text-white">
                                    <i class="fas fa-envelope mr-2 text-slate-400"></i>
                                    {{ $subscriber->email }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3.5 text-slate-700 dark:text-slate-200">{{ $subscriber->name ?? '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-3.5">
                                    @if($subscriber->is_active)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">Actif</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-300">Inactif</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3.5">
                                    @if($subscriber->email_verified)
                                        <i class="fas fa-check-circle text-lg text-emerald-600" title="Vérifié"></i>
                                    @else
                                        <i class="fas fa-times-circle text-lg text-red-600" title="Non vérifié"></i>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3.5">
                                    <div class="flex flex-wrap gap-1">
                                        @if($subscriber->receive_new_items)
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset bg-cyan-50 text-cyan-700 ring-cyan-600/20 dark:bg-cyan-900/30 dark:text-cyan-300">Articles</span>
                                        @endif
                                        @if($subscriber->receive_promotions)
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset bg-orange-50 text-orange-700 ring-orange-600/20 dark:bg-orange-900/30 dark:text-orange-300">Promos</span>
                                        @endif
                                        @if($subscriber->receive_newsletters)
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-900/30 dark:text-blue-300">News</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3.5 text-xs text-slate-600 dark:text-slate-300">
                                    <div class="space-y-0.5">
                                        <div><span class="font-medium">Envoyés:</span> {{ $subscriber->emails_sent }}</div>
                                        <div><span class="font-medium">Ouverts:</span> {{ $subscriber->emails_opened }}</div>
                                        <div><span class="font-medium">Clics:</span> {{ $subscriber->emails_clicked }}</div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3.5 text-slate-600 dark:text-slate-300">
                                    {{ $subscriber->created_at->format('d/m/Y') }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3.5">
                                    <div class="flex justify-end gap-1">
                                        <button class="toggle-subscriber inline-flex items-center justify-center w-8 h-8 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors"
                                                data-id="{{ $subscriber->id }}"
                                                title="{{ $subscriber->is_active ? 'Désactiver' : 'Activer' }}">
                                            <i class="fas fa-{{ $subscriber->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
                                        </button>
                                        <button class="delete-subscriber inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                                data-id="{{ $subscriber->id }}"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-12 text-center text-slate-400">
                                    <i class="fas fa-inbox text-4xl text-slate-200 dark:text-slate-600 mb-3 block"></i>
                                    Aucun abonné pour le moment
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($subscribers->hasPages())
                <div class="border-t border-slate-100 dark:border-slate-700 px-5 py-4">
                    {{ $subscribers->links() }}
                </div>
            @endif
        @else
            <div class="py-12 text-center">
                <i class="fas fa-inbox text-4xl text-slate-200 dark:text-slate-600 mb-3 block"></i>
                <p class="text-slate-400">Aucun abonné pour le moment</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toggle-subscriber').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;

            fetch(`/admin/settings/newsletter/${id}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Erreur lors de la modification', 'error');
            });
        });
    });

    document.querySelectorAll('.delete-subscriber').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet abonné ?')) return;

            const id = this.dataset.id;

            fetch(`/admin/settings/newsletter/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Erreur lors de la suppression', 'error');
            });
        });
    });
});

function showNotification(message, type) {
    const bgColors = {
        success: 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-200 dark:border-emerald-800',
        error: 'bg-red-50 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-200 dark:border-red-800',
        warning: 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-900/30 dark:text-amber-200 dark:border-amber-800'
    };

    const icons = {
        success: 'fa-check-circle text-emerald-600',
        error: 'fa-exclamation-circle text-red-600',
        warning: 'fa-exclamation-triangle text-amber-600'
    };

    const alert = document.createElement('div');
    alert.className = `fixed top-20 right-4 z-50 flex items-center gap-3 rounded-2xl border px-5 py-4 shadow-lg animate-fade-in ${bgColors[type]}`;
    alert.style.minWidth = '300px';
    alert.innerHTML = `
        <i class="fas ${icons[type]}"></i>
        <span class="flex-1 text-sm font-medium">${message}</span>
        <button onclick="this.parentElement.remove()" class="text-current opacity-70 hover:opacity-100">
            <i class="fas fa-times"></i>
        </button>
    `;
    document.body.appendChild(alert);
    setTimeout(() => alert.remove(), 5000);
}
</script>
@endpush
