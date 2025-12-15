@extends('layouts.admin')

@section('title', 'Détails de la marque')
@section('page-title', $brand->name)

@section('page-actions')
<div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.brands.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>Retour à la liste
    </a>
    <a href="{{ route('admin.brands.edit', $brand) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
        <i class="fas fa-edit mr-2"></i>Modifier
    </a>
    <div class="relative">
        <button class="inline-flex items-center px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" 
                type="button" 
                onclick="toggleActionsDropdown()">
            <i class="fas fa-ellipsis-v"></i>
        </button>
        <div id="actions-dropdown" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-20">
            <div class="py-1">
                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="toggleStatus()">
                    <i class="fas fa-{{ $brand->is_active ? 'pause' : 'play' }} mr-2"></i>
                    {{ $brand->is_active ? 'Désactiver' : 'Activer' }}
                </button>
                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" onclick="toggleFeatured()">
                    <i class="fas fa-{{ $brand->is_featured ? 'star-half-alt' : 'star' }} mr-2 text-yellow-500"></i>
                    {{ $brand->is_featured ? 'Retirer de la une' : 'Mettre en une' }}
                </button>
                <hr class="my-1 border-gray-200 dark:border-gray-600">
                <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" onclick="confirmDelete()">
                    <i class="fas fa-trash mr-2"></i>Supprimer
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Informations principales -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6">
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $brand->name }}</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">{{ $brand->slug }}</p>
                        
                        @if($brand->description)
                            <div class="mb-6">
                                <h6 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Description</h6>
                                <p class="text-gray-600 dark:text-gray-400">{{ $brand->description }}</p>
                            </div>
                        @endif
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($brand->website)
                                <div>
                                    <h6 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Site web</h6>
                                    <a href="{{ $brand->website }}" target="_blank" class="text-primary-600 hover:text-primary-700 hover:underline inline-flex items-center">
                                        <i class="fas fa-external-link-alt mr-2"></i>
                                        {{ Str::limit($brand->website, 30) }}
                                    </a>
                                </div>
                            @endif
                            
                            @if($brand->country)
                                <div>
                                    <h6 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Pays d'origine</h6>
                                    <p class="text-gray-600 dark:text-gray-400 flex items-center">
                                        <span class="fi fi-{{ strtolower($brand->country) }} mr-2"></span>
                                        {{ $brand->country }}
                                    </p>
                                </div>
                            @endif
                            
                            @if($brand->founded_year)
                                <div>
                                    <h6 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Année de création</h6>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $brand->founded_year }}</p>
                                </div>
                            @endif
                            
                            @if($brand->category)
                                <div>
                                    <h6 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Catégorie</h6>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                        {{ ucfirst($brand->category) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="mt-4 flex flex-wrap gap-2">
                            @if($brand->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                    Inactive
                                </span>
                            @endif
                            
                            @if($brand->is_featured)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                    <i class="fas fa-star text-yellow-500 mr-1"></i>
                                    En vedette
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex-shrink-0 text-center">
                        @if($brand->logo)
                            <div class="mb-3">
                                <img src="{{ $brand->logo_url }}" 
                                     class="w-40 h-40 object-contain rounded-lg border border-gray-200 dark:border-gray-700 bg-white p-2" 
                                     alt="Logo {{ $brand->name }}">
                            </div>
                        @else
                            <div class="w-40 h-40 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex flex-col items-center justify-center text-white mx-auto">
                                <span class="text-4xl font-bold">{{ strtoupper(substr($brand->name, 0, 2)) }}</span>
                                <span class="text-xs mt-1 opacity-75">Aucun logo</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Articles de la marque -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h5 class="text-lg font-semibold text-gray-900 dark:text-white">Articles de la marque</h5>
                <a href="{{ route('admin.items.index', ['brand' => $brand->id]) }}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors">
                    Voir tous les articles
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            <div class="p-0">
                @if($brand->items && $brand->items->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Article</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Prix</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Créé le</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($brand->items->take(10) as $item)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                @if($item->first_image_url)
                                                    <img src="{{ $item->first_image_url }}" 
                                                         class="w-10 h-10 rounded-lg object-cover border border-gray-200 dark:border-gray-700" 
                                                         alt="{{ $item->title }}">
                                                @else
                                                    <div class="w-10 h-10 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                                        <i class="fas fa-image text-gray-400"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="font-medium text-gray-900 dark:text-white">{{ Str::limit($item->title, 30) }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->category->name ?? 'Sans catégorie' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($item->price, 0, ',', ' ') }} FCFA</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $item->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('admin.items.show', $item) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                            <i class="fas fa-box text-3xl text-gray-400"></i>
                        </div>
                        <h6 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucun article</h6>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Cette marque n'a pas encore d'articles</p>
                        <a href="{{ route('admin.items.create', ['brand' => $brand->id]) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Ajouter un article
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Sidebar avec statistiques -->
    <div class="space-y-6">
        <!-- Statistiques -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h5 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center mr-3">
                        <i class="fas fa-chart-bar text-primary-600"></i>
                    </div>
                    Statistiques
                </h5>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-2xl font-bold text-primary-600">{{ $stats['total_items'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Articles</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ $stats['active_items'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Actifs</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ $stats['sold_items'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Vendus</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-600">{{ number_format($stats['total_views'] ?? 0) }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Vues</div>
                    </div>
                </div>
                
                @if(isset($stats['average_price']) && $stats['average_price'] > 0)
                    <div class="mt-4 p-4 bg-gradient-to-r from-primary-50 to-blue-50 dark:from-primary-900/20 dark:to-blue-900/20 rounded-lg">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Prix moyen</div>
                        <div class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['average_price'], 0, ',', ' ') }} FCFA</div>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Informations système -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h5 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center mr-3">
                        <i class="fas fa-info-circle text-gray-600 dark:text-gray-400"></i>
                    </div>
                    Informations système
                </h5>
            </div>
            <div class="p-6 space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">ID</span>
                    <span class="text-sm text-gray-900 dark:text-white font-mono">#{{ $brand->id }}</span>
                </div>
                
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Créée le</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ $brand->created_at->format('d/m/Y H:i') }}</span>
                </div>
                
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Modifiée le</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ $brand->updated_at->format('d/m/Y H:i') }}</span>
                </div>
                
                @if($brand->created_by)
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Créée par</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $brand->creator->name ?? 'Inconnu' }}</span>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Actions rapides -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h5 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center mr-3">
                        <i class="fas fa-bolt text-green-600"></i>
                    </div>
                    Actions rapides
                </h5>
            </div>
            <div class="p-6 space-y-3">
                <a href="{{ route('admin.items.create', ['brand' => $brand->id]) }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Ajouter un article
                </a>
                
                <a href="{{ route('admin.brands.edit', $brand) }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Modifier la marque
                </a>
                
                <button onclick="exportBrandData()" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-download mr-2"></i>Exporter les données
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDeleteModal()"></div>
    
    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <!-- Header -->
            <div class="bg-red-50 dark:bg-red-900/20 px-6 py-4 border-b border-red-100 dark:border-red-800">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-red-800 dark:text-red-200 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Confirmer la suppression
                    </h3>
                    <button type="button" onclick="closeDeleteModal()" class="text-red-400 hover:text-red-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Body -->
            <div class="px-6 py-4">
                <p class="text-gray-700 dark:text-gray-200 mb-4">
                    Êtes-vous sûr de vouloir supprimer la marque 
                    <strong class="text-gray-900 dark:text-white">{{ $brand->name }}</strong> ?
                </p>
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 rounded">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700 dark:text-yellow-200">
                                Cette action est <strong>irréversible</strong> et supprimera tous les articles associés.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 flex flex-col-reverse sm:flex-row justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors w-full sm:w-auto">
                    <i class="fas fa-times mr-2"></i>Annuler
                </button>
                <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex justify-center items-center w-full px-4 py-2 bg-red-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleActionsDropdown() {
    const dropdown = document.getElementById('actions-dropdown');
    dropdown.classList.toggle('hidden');
}

// Fermer le dropdown en cliquant ailleurs
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick*="toggleActionsDropdown"]') && !event.target.closest('#actions-dropdown')) {
        document.getElementById('actions-dropdown').classList.add('hidden');
    }
});

function confirmDelete() {
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Fermer le modal avec Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDeleteModal();
    }
});

function toggleStatus() {
    fetch(`/admin/brands/{{ $brand->id }}/status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ is_active: {{ $brand->is_active ? 'false' : 'true' }} })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de la modification du statut');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la modification du statut');
    });
}

function toggleFeatured() {
    fetch(`/admin/brands/{{ $brand->id }}/status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ is_featured: {{ $brand->is_featured ? 'false' : 'true' }} })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de la modification du statut vedette');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la modification du statut vedette');
    });
}

function exportBrandData() {
    window.location.href = `/admin/brands/{{ $brand->id }}/export`;
}
</script>
@endpush
