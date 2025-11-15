@extends('layouts.admin')

@section('title', 'Détails Expert - ' . $expert->user->name)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                    <i class="fas fa-user-graduate text-blue-600"></i>
                    Profil Expert
                </h1>
                <p class="text-gray-600 dark:text-gray-300 mt-1">Détails et statistiques de {{ $expert->user->name }}</p>
                <!-- Breadcrumb -->
                <nav class="flex mt-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('admin.experts.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-blue-600 text-sm">
                                Experts
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                                <span class="text-sm text-gray-700 dark:text-gray-200">{{ $expert->user->name }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="flex gap-3 mt-4 lg:mt-0">
                <a href="{{ route('admin.experts.edit', $expert) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <i class="fas fa-edit"></i>
                    Modifier
                </a>
                <a href="{{ route('admin.experts.index') }}" 
                   class="bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Retour à la liste
                </a>
            </div>
        </div>

        <!-- Messages de session -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 relative mb-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <span class="text-green-800">{{ session('success') }}</span>
                </div>
                <button type="button" class="absolute top-3 right-3 text-green-500 hover:text-green-700" 
                        onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profil de l'expert -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-id-card text-blue-600"></i>
                            Informations Personnelles
                        </h3>
                    </div>
                    <div class="p-6 text-center">
                        @if($expert->user->avatar)
                            @if(str_starts_with($expert->user->avatar, 'http://') || str_starts_with($expert->user->avatar, 'https://'))
                                <img src="{{ $expert->user->avatar }}" 
                                     class="w-24 h-24 rounded-full mx-auto object-cover"
                                     alt="{{ $expert->user->name }}"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div class="w-24 h-24 bg-blue-600 rounded-full mx-auto flex items-center justify-center" style="display: none;">
                                    <span class="text-white font-bold text-3xl">
                                        {{ strtoupper(substr($expert->user->name, 0, 1)) }}
                                    </span>
                                </div>
                            @else
                                <img src="{{ Storage::url($expert->user->avatar) }}" 
                                     class="w-24 h-24 rounded-full mx-auto object-cover"
                                     alt="{{ $expert->user->name }}"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div class="w-24 h-24 bg-blue-600 rounded-full mx-auto flex items-center justify-center" style="display: none;">
                                    <span class="text-white font-bold text-3xl">
                                        {{ strtoupper(substr($expert->user->name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                        @else
                            <div class="w-24 h-24 bg-blue-600 rounded-full mx-auto flex items-center justify-center">
                                <span class="text-white font-bold text-3xl">
                                    {{ strtoupper(substr($expert->user->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif

                        <h4 class="text-xl font-semibold text-gray-900 dark:text-white mt-4">{{ $expert->user->name }}</h4>
                        <p class="text-gray-600 dark:text-gray-300">{{ $expert->user->email }}</p>
                        
                        @if($expert->user->phone)
                            <p class="text-gray-600 dark:text-gray-300 text-sm">{{ $expert->user->phone }}</p>
                        @endif

                        <div class="mt-4">
                            <span class="px-3 py-1 text-sm font-medium rounded-full
                                @if($expert->certification_level === 'master') bg-green-100 text-green-800
                                @elseif($expert->certification_level === 'senior') bg-blue-100 text-blue-800
                                @else bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100
                                @endif">
                                Expert {{ ucfirst($expert->certification_level) }}
                            </span>
                        </div>

                        <div class="mt-4">
                            @if($expert->is_active)
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                                    <i class="fas fa-check-circle mr-1"></i>Actif
                                </span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">
                                    <i class="fas fa-times-circle mr-1"></i>Inactif
                                </span>
                            @endif
                        </div>

                        <!-- Dates importantes -->
                        <div class="mt-6 space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-300">Membre depuis:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $expert->user->created_at->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-300">Expert depuis:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $expert->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        <!-- Spécialisations -->
                        @if($expert->specialties && count($expert->specialties) > 0)
                            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Spécialisations</h5>
                                <div class="flex flex-wrap gap-2 justify-center">
                                    @foreach($expert->specialties as $specialty)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                            {{ ucfirst(str_replace(['_', '-'], ' ', $specialty)) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Bio -->
                        @if($expert->bio)
                            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Biographie</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-300 text-left">{{ $expert->bio }}</p>
                            </div>
                        @endif

                        <!-- Statistiques principales -->
                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600">{{ $stats['total_verifications'] ?? 0 }}</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-300">Vérifications</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600">{{ number_format($expert->approval_rate ?? 0, 1) }}%</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-300">Approbation</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques et performances -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Statistiques principales -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-chart-line text-blue-600"></i>
                            Statistiques de Performance
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="bg-blue-50 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $stats['pending_verifications'] ?? 0 }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">En Attente</div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $stats['completed_verifications'] ?? 0 }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">Complétées</div>
                            </div>
                            <div class="bg-primary-50 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-primary-600">{{ $stats['this_month_verifications'] ?? 0 }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">Ce mois</div>
                            </div>
                            <div class="bg-orange-50 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-orange-600">{{ $stats['avg_review_time'] ?? 0 }}min</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">Temps moyen</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activité récente -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-clock text-blue-600"></i>
                            Activité Récente
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $expert->verifications()->whereMonth('created_at', now()->month)->count() ?? 0 }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">Ce mois</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $expert->verifications()->where('created_at', '>=', now()->startOfWeek())->count() ?? 0 }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">Cette semaine</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $expert->verifications()->whereDate('created_at', today())->count() ?? 0 }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">Aujourd'hui</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dernières vérifications -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-list text-blue-600"></i>
                            Dernières Vérifications
                        </h3>
                    </div>
                    <div class="p-6">
                        @if(isset($recentVerifications) && $recentVerifications->count() > 0)
                            <div class="space-y-4">
                                @foreach($recentVerifications as $verification)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                        <div class="flex items-center gap-3">
                                            @if($verification->item && $verification->item->images && count($verification->item->images) > 0)
                                                <img src="{{ Storage::url($verification->item->images[0]) }}" 
                                                     class="w-12 h-12 rounded-lg object-cover"
                                                     alt="Product">
                                            @else
                                                <div class="w-12 h-12 bg-gray-300 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-box text-gray-500 dark:text-gray-400"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white">
                                                    {{ $verification->item ? $verification->item->name : 'Produit supprimé' }}
                                                </div>
                                                <div class="text-sm text-gray-600 dark:text-gray-300">
                                                    {{ $verification->created_at->format('d/m/Y H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                @if($verification->status === 'expert_approved') bg-green-100 text-green-800
                                                @elseif($verification->status === 'expert_rejected') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800
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
                                <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Voir toutes les vérifications →
                                </a>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-clipboard-list text-gray-400 text-4xl mb-3"></i>
                                <p class="text-gray-500 dark:text-gray-400">Aucune vérification effectuée</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-cogs text-blue-600"></i>
                            Actions Rapides
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <button onclick="toggleExpertStatus({{ $expert->id }})"
                                    class="p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors text-center">
                                <i class="fas fa-power-off text-gray-600 dark:text-gray-300 text-xl mb-2"></i>
                                <div class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                    {{ $expert->is_active ? 'Désactiver' : 'Activer' }}
                                </div>
                            </button>
                            
                            <a href="{{ route('admin.experts.edit', $expert) }}"
                               class="p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg hover:border-green-500 hover:bg-green-50 transition-colors text-center">
                                <i class="fas fa-edit text-gray-600 dark:text-gray-300 text-xl mb-2"></i>
                                <div class="text-sm font-medium text-gray-700 dark:text-gray-200">Modifier</div>
                            </a>
                            
                            <button onclick="sendMessage({{ $expert->user->id }})"
                                    class="p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg hover:border-primary-500 hover:bg-primary-50 transition-colors text-center">
                                <i class="fas fa-envelope text-gray-600 dark:text-gray-300 text-xl mb-2"></i>
                                <div class="text-sm font-medium text-gray-700 dark:text-gray-200">Message</div>
                            </button>
                            
                            <button onclick="revokeExpert({{ $expert->id }}, '{{ $expert->user->name }}')"
                                    class="p-4 border-2 border-dashed border-red-300 rounded-lg hover:border-red-500 hover:bg-red-50 transition-colors text-center">
                                <i class="fas fa-user-times text-red-600 text-xl mb-2"></i>
                                <div class="text-sm font-medium text-red-700">Révoquer</div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript pour les actions -->
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
    // Logique pour envoyer un message à l'expert
    alert('Fonctionnalité de messagerie - À implémenter');
}

function revokeExpert(expertId, expertName) {
    if (confirm(`Êtes-vous sûr de vouloir révoquer le statut d'expert de ${expertName} ?`)) {
        // Afficher un indicateur de chargement
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
                // Afficher message de succès
                alert(data.message);
                // Rediriger vers la liste des experts
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    window.location.href = '{{ route("admin.experts.index") }}';
                }
            } else {
                // Afficher le message d'erreur spécifique
                alert(data.message || 'Erreur lors de la révocation du statut');
                // Restaurer le bouton
                button.innerHTML = originalContent;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la communication avec le serveur');
            // Restaurer le bouton
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }
}
</script>
@endsection
