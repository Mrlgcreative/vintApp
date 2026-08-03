@extends('layouts.admin')

@section('title', 'Détails Expert - ' . $expert->user->name)
@section('page-title', 'Profil Expert')
@section('page-subtitle', 'Détails et statistiques de ' . $expert->user->name)

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.experts.index') }}"
       class="inline-flex items-center gap-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
        <i class="fas fa-arrow-left"></i>Retour à la liste
    </a>
    <a href="{{ route('admin.experts.edit', $expert) }}"
       class="inline-flex items-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-colors">
        <i class="fas fa-pen"></i>Modifier
    </a>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Profil de l'expert --}}
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2.5">
                <i class="fas fa-id-card text-primary-600"></i>
                <h3 class="font-semibold text-slate-900 dark:text-white">Informations Personnelles</h3>
            </div>
            <div class="p-6 text-center">
                @if($expert->user->avatar)
                    @if(str_starts_with($expert->user->avatar, 'http://') || str_starts_with($expert->user->avatar, 'https://'))
                        <img src="{{ $expert->user->avatar }}"
                             class="w-24 h-24 rounded-full mx-auto object-cover ring-2 ring-slate-200 dark:ring-slate-700"
                             alt="{{ $expert->user->name }}"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="w-24 h-24 bg-primary-600 rounded-full mx-auto items-center justify-center hidden" style="display: none;">
                            <span class="text-white font-bold text-3xl">
                                {{ strtoupper(substr($expert->user->name, 0, 1)) }}
                            </span>
                        </div>
                    @else
                        <img src="{{ $expert->user->avatar_url }}"
                             class="w-24 h-24 rounded-full mx-auto object-cover ring-2 ring-slate-200 dark:ring-slate-700"
                             alt="{{ $expert->user->name }}"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="w-24 h-24 bg-primary-600 rounded-full mx-auto items-center justify-center hidden" style="display: none;">
                            <span class="text-white font-bold text-3xl">
                                {{ strtoupper(substr($expert->user->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                @else
                    <div class="w-24 h-24 bg-primary-600 rounded-full mx-auto flex items-center justify-center">
                        <span class="text-white font-bold text-3xl">
                            {{ strtoupper(substr($expert->user->name, 0, 1)) }}
                        </span>
                    </div>
                @endif

                <h4 class="text-xl font-semibold text-slate-900 dark:text-white mt-4">{{ $expert->user->name }}</h4>
                <p class="text-slate-500 dark:text-slate-400">{{ $expert->user->email }}</p>

                @if($expert->user->phone)
                    <p class="text-slate-500 dark:text-slate-400 text-sm">{{ $expert->user->phone }}</p>
                @endif

                <div class="mt-4">
                    <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full
                        @if($expert->certification_level === 'master') bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300
                        @elseif($expert->certification_level === 'senior') bg-sky-50 text-sky-700 ring-1 ring-inset ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300
                        @else bg-slate-100 text-slate-700 ring-1 ring-inset ring-slate-600/20 dark:bg-slate-700 dark:text-slate-200
                        @endif">
                        Expert {{ ucfirst($expert->certification_level) }}
                    </span>
                </div>

                <div class="mt-4">
                    @if($expert->is_active)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 text-sm font-medium rounded-full ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300">
                            <i class="fas fa-circle-check"></i>Actif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-700 text-sm font-medium rounded-full ring-1 ring-inset ring-red-600/20 dark:bg-red-900/30 dark:text-red-300">
                            <i class="fas fa-circle-xmark"></i>Inactif
                        </span>
                    @endif
                </div>

                {{-- Dates importantes --}}
                <div class="mt-6 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500 dark:text-slate-400">Membre depuis:</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $expert->user->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500 dark:text-slate-400">Expert depuis:</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $expert->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>

                {{-- Spécialisations --}}
                @if($expert->specialties && count($expert->specialties) > 0)
                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700">
                        <h5 class="text-sm font-medium text-slate-900 dark:text-white mb-3">Spécialisations</h5>
                        <div class="flex flex-wrap gap-2 justify-center">
                            @foreach($expert->specialties as $specialty)
                                <span class="inline-flex px-2.5 py-1 bg-sky-50 text-sky-700 text-xs font-medium rounded-full ring-1 ring-inset ring-sky-600/20 dark:bg-sky-900/30 dark:text-sky-300">
                                    {{ ucfirst(str_replace(['_', '-'], ' ', $specialty)) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Bio --}}
                @if($expert->bio)
                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700">
                        <h5 class="text-sm font-medium text-slate-900 dark:text-white mb-2">Biographie</h5>
                        <p class="text-sm text-slate-500 dark:text-slate-400 text-left">{{ $expert->bio }}</p>
                    </div>
                @endif

                {{-- Statistiques principales --}}
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-primary-600">{{ $stats['total_verifications'] ?? 0 }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">Vérifications</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-emerald-600">{{ number_format($expert->approval_rate ?? 0, 1) }}%</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">Approbation</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiques et performances --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Statistiques principales --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2.5">
                <i class="fas fa-chart-line text-primary-600"></i>
                <h3 class="font-semibold text-slate-900 dark:text-white">Statistiques de Performance</h3>
            </div>
            <div class="p-5 sm:p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-sky-50 dark:bg-sky-900/30 rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-sky-600 dark:text-sky-400">{{ $stats['pending_verifications'] ?? 0 }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">En Attente</div>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-900/30 rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['completed_verifications'] ?? 0 }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Complétées</div>
                    </div>
                    <div class="bg-primary-50 dark:bg-primary-900/30 rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $stats['this_month_verifications'] ?? 0 }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Ce mois</div>
                    </div>
                    <div class="bg-orange-50 dark:bg-orange-900/30 rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['avg_review_time'] ?? 0 }}min</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Temps moyen</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Activité récente --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2.5">
                <i class="fas fa-clock text-primary-600"></i>
                <h3 class="font-semibold text-slate-900 dark:text-white">Activité Récente</h3>
            </div>
            <div class="p-5 sm:p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-slate-50 dark:bg-slate-900/60 rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-slate-900 dark:text-white">
                            {{ $expert->verifications()->whereMonth('created_at', now()->month)->count() ?? 0 }}
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Ce mois</div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-900/60 rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-slate-900 dark:text-white">
                            {{ $expert->verifications()->where('created_at', '>=', now()->startOfWeek())->count() ?? 0 }}
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Cette semaine</div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-900/60 rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-slate-900 dark:text-white">
                            {{ $expert->verifications()->whereDate('created_at', today())->count() ?? 0 }}
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Aujourd'hui</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dernières vérifications --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2.5">
                <i class="fas fa-list text-primary-600"></i>
                <h3 class="font-semibold text-slate-900 dark:text-white">Dernières Vérifications</h3>
            </div>
            <div class="p-5 sm:p-6">
                @if(isset($recentVerifications) && $recentVerifications->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentVerifications as $verification)
                            <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-900/60 rounded-xl">
                                <div class="flex items-center gap-3">
                                    @if($verification->item && $verification->item->images && count($verification->item->images) > 0)
                                        <img src="{{ Storage::url($verification->item->images[0]) }}"
                                             class="w-12 h-12 rounded-lg object-cover"
                                             alt="Product">
                                    @else
                                        <div class="w-12 h-12 bg-slate-200 dark:bg-slate-700 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-box text-slate-500 dark:text-slate-400"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-medium text-slate-900 dark:text-white">
                                            {{ $verification->item ? $verification->item->name : 'Produit supprimé' }}
                                        </div>
                                        <div class="text-sm text-slate-500 dark:text-slate-400">
                                            {{ $verification->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full
                                        @if($verification->status === 'expert_approved') bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300
                                        @elseif($verification->status === 'expert_rejected') bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-900/30 dark:text-red-300
                                        @else bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300
                                        @endif">
                                        @if($verification->status === 'expert_approved') Approuvé
                                        @elseif($verification->status === 'expert_rejected') Rejeté
                                        @elseif($verification->status === 'expert_review') En cours
                                        @else {{ ucfirst($verification->status) }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 text-center">
                        <a href="#" class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                            Voir toutes les vérifications →
                        </a>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-clipboard-list text-slate-300 dark:text-slate-600 text-4xl mb-3"></i>
                        <p class="text-slate-500 dark:text-slate-400">Aucune vérification effectuée</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Actions rapides --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2.5">
                <i class="fas fa-bolt text-primary-600"></i>
                <h3 class="font-semibold text-slate-900 dark:text-white">Actions Rapides</h3>
            </div>
            <div class="p-5 sm:p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <button onclick="toggleExpertStatus({{ $expert->id }})"
                            class="p-4 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl hover:border-sky-500 hover:bg-sky-50 dark:hover:bg-sky-900/20 transition-colors text-center">
                        <i class="fas fa-power-off text-slate-600 dark:text-slate-300 text-xl mb-2"></i>
                        <div class="text-sm font-medium text-slate-700 dark:text-slate-300">
                            {{ $expert->is_active ? 'Désactiver' : 'Activer' }}
                        </div>
                    </button>

                    <a href="{{ route('admin.experts.edit', $expert) }}"
                       class="p-4 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors text-center">
                        <i class="fas fa-pen text-slate-600 dark:text-slate-300 text-xl mb-2"></i>
                        <div class="text-sm font-medium text-slate-700 dark:text-slate-300">Modifier</div>
                    </a>

                    <button onclick="sendMessage({{ $expert->user->id }})"
                            class="p-4 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors text-center">
                        <i class="fas fa-envelope text-slate-600 dark:text-slate-300 text-xl mb-2"></i>
                        <div class="text-sm font-medium text-slate-700 dark:text-slate-300">Message</div>
                    </button>

                    <button onclick="revokeExpert({{ $expert->id }}, '{{ $expert->user->name }}')"
                            class="p-4 border-2 border-dashed border-red-300 rounded-xl hover:border-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-center">
                        <i class="fas fa-user-times text-red-600 text-xl mb-2"></i>
                        <div class="text-sm font-medium text-red-700">Révoquer</div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleExpertStatus(expertId) {
    fetch(`/admin/experts/${expertId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors du changement de statut');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors du changement de statut');
    });
}

function sendMessage(userId) {
    alert('Fonctionnalité de messagerie - À implémenter');
}

function revokeExpert(expertId, expertName) {
    if (confirm(`Êtes-vous sûr de vouloir révoquer le statut d'expert de ${expertName} ?`)) {
        const button = event.target.closest('button');
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Révocation...';
        button.disabled = true;

        fetch(`/admin/experts/${expertId}/revoke`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    window.location.href = '{{ route("admin.experts.index") }}';
                }
            } else {
                alert(data.message || 'Erreur lors de la révocation du statut');
                button.innerHTML = originalContent;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la communication avec le serveur');
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }
}
</script>
@endpush
