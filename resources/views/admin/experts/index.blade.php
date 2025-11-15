@extends('layouts.admin')

@section('title', 'Gestion des Experts')

@section('content')
<div class="space-y-6">
    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Experts -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">
                        Total Experts
                    </p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_experts'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Experts Actifs -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-green-600 uppercase tracking-wide">
                        Experts Actifs
                    </p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active_experts'] }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-user-check text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Vérifications Totales -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-primary-600 uppercase tracking-wide">
                        Vérifications Totales
                    </p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_verifications'] }}</p>
                </div>
                <div class="p-3 bg-primary-100 rounded-full">
                    <i class="fas fa-certificate text-primary-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- En Attente -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-orange-600 uppercase tracking-wide">
                        En Attente
                    </p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['pending_verifications'] }}</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full">
                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- En-tête avec actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <i class="fas fa-user-graduate text-blue-600"></i>
                Gestion des Experts
            </h1>
            <p class="text-gray-600 dark:text-gray-300 mt-1">Gérer les experts en vérification d'authenticité</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.experts.candidates') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                <i class="fas fa-user-plus"></i>
                Désigner un Expert
            </a>
            <button onclick="toggleStats()" 
                    class="bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                <i class="fas fa-chart-bar"></i>
                Statistiques
            </button>
        </div>
    </div>

    <!-- Messages d'alerte -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 relative">
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

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 relative">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                <span class="text-red-800">{{ session('error') }}</span>
            </div>
            <button type="button" class="absolute top-3 right-3 text-red-500 hover:text-red-700" 
                    onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('warning'))
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 relative">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-yellow-500 mr-3"></i>
                <span class="text-yellow-800">{{ session('warning') }}</span>
            </div>
            <button type="button" class="absolute top-3 right-3 text-yellow-500 hover:text-yellow-700" 
                    onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Filtres et recherche -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Filtres de recherche</h3>
            <button onclick="toggleFilters()" 
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-200 transition-colors">
                <i class="fas fa-filter"></i>
            </button>
        </div>
        <div id="filtersPanel" class="hidden p-6">
            <form method="GET" action="{{ route('admin.experts.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Rechercher</label>
                        <input type="text" id="search" name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Nom ou email..."
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Statut</label>
                        <select id="status" name="status" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tous</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspendu</option>
                        </select>
                    </div>
                    <div>
                        <label for="specialization" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Spécialisation</label>
                        <select id="specialization" name="specialization" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Toutes</option>
                            <option value="luxury" {{ request('specialization') === 'luxury' ? 'selected' : '' }}>Luxe</option>
                            <option value="sneakers" {{ request('specialization') === 'sneakers' ? 'selected' : '' }}>Sneakers</option>
                            <option value="watches" {{ request('specialization') === 'watches' ? 'selected' : '' }}>Montres</option>
                            <option value="handbags" {{ request('specialization') === 'handbags' ? 'selected' : '' }}>Sacs</option>
                        </select>
                    </div>
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Trier par</label>
                        <select id="sort" name="sort" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Nom</option>
                            <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Date d'ajout</option>
                            <option value="verifications_count" {{ request('sort') === 'verifications_count' ? 'selected' : '' }}>Nb vérifications</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">&nbsp;</label>
                        <div class="flex gap-2">
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                                <i class="fas fa-search"></i> Filtrer
                            </button>
                            <a href="{{ route('admin.experts.index') }}" 
                               class="bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des experts -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-list text-blue-600"></i>
                Liste des Experts ({{ $experts->total() }})
            </h3>
        </div>
        <div class="p-6">
            @if($experts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-200">Expert</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-200">Spécialisations</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-200">Niveau</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700 dark:text-gray-200">Vérifications</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700 dark:text-gray-200">Taux d'approbation</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700 dark:text-gray-200">Statut</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700 dark:text-gray-200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($experts as $expert)
                                <tr class="border-b border-gray-100 hover:bg-gray-50 dark:bg-gray-900">
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 flex-shrink-0">
                                                @if($expert->user->avatar)
                                                    @php
                                                        $avatarUrl = filter_var($expert->user->avatar, FILTER_VALIDATE_URL) 
                                                            ? $expert->user->avatar 
                                                            : Storage::url($expert->user->avatar);
                                                    @endphp
                                                    <img src="{{ $avatarUrl }}" 
                                                         class="w-10 h-10 rounded-full object-cover" 
                                                         alt="{{ $expert->user->name }}"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="w-10 h-10 bg-blue-600 rounded-full hidden items-center justify-center">
                                                        <span class="text-white font-bold text-sm">
                                                            {{ strtoupper(substr($expert->user->name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                                                        <span class="text-white font-bold text-sm">
                                                            {{ strtoupper(substr($expert->user->name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900 dark:text-white">{{ $expert->user->name }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $expert->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        @if($expert->specialties && count($expert->specialties) > 0)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($expert->specialties as $specialty)
                                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                                        {{ ucfirst(str_replace('_', ' ', $specialty)) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-sm">Aucune spécialisation</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            @if($expert->certification_level === 'master') bg-green-100 text-green-800
                                            @elseif($expert->certification_level === 'senior') bg-blue-100 text-blue-800
                                            @else bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100
                                            @endif">
                                            {{ ucfirst($expert->certification_level) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $expert->verification_count }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">vérifications</div>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @if($expert->approval_rate > 0)
                                            <div class="font-bold text-green-600">{{ number_format($expert->approval_rate, 1) }}%</div>
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1 mt-1">
                                                <div class="bg-green-600 h-1 rounded-full" 
                                                     style="width: {{ $expert->approval_rate }}%"></div>
                                            </div>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" 
                                                   class="sr-only peer"
                                                   data-expert-id="{{ $expert->id }}"
                                                   {{ $expert->is_active ? 'checked' : '' }}
                                                   onchange="toggleExpertStatus({{ $expert->id }})">
                                            <div class="relative w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white dark:bg-gray-800 after:border-gray-300 dark:border-gray-600 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">
                                                {{ $expert->is_active ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </label>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('admin.experts.show', $expert) }}" 
                                               class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" 
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.experts.edit', $expert) }}" 
                                               class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-colors" 
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors" 
                                                    title="Révoquer le statut d'expert"
                                                    onclick="revokeExpert({{ $expert->id }}, '{{ $expert->user->name }}')">
                                                <i class="fas fa-user-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4">
                    <div class="text-sm text-gray-600 dark:text-gray-300">
                        Affichage de {{ $experts->firstItem() ?? 0 }} à {{ $experts->lastItem() ?? 0 }} 
                        sur {{ $experts->total() }} experts
                    </div>
                    <div>
                        {{ $experts->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-user-graduate text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucun expert désigné</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">Commencez par désigner des utilisateurs comme experts.</p>
                    <a href="{{ route('admin.experts.candidates') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-user-plus"></i>
                        Désigner un Expert
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- JavaScript pour les interactions -->
<script>
function toggleFilters() {
    const panel = document.getElementById('filtersPanel');
    panel.classList.toggle('hidden');
}

function toggleStats() {
    // Logique pour afficher/masquer les statistiques
    alert('Statistiques détaillées - À implémenter');
}

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

function revokeExpert(expertId, expertName) {
    if (confirm(`Êtes-vous sûr de vouloir révoquer le statut d'expert de ${expertName} ?`)) {
        // Afficher un indicateur de chargement
        const button = event.target.closest('button');
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;

        fetch(`/admin/experts/${expertId}/revoke`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('La réponse n\'est pas au format JSON');
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data.success) {
                // Afficher message de succès
                if (data.message) {
                    alert(data.message);
                }
                // Recharger la page ou rediriger
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    location.reload();
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
            console.error('Erreur détaillée:', error);
            alert(`Erreur lors de la révocation: ${error.message}`);
            // Restaurer le bouton
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }
}
</script>
@endsection