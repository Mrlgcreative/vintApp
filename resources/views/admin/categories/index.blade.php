@extends('layouts.admin')

@section('title', 'Gestion des catégories')
@section('page-title', 'Gestion des catégories')

@section('page-actions')
<div class="flex flex-wrap gap-3">
    <div class="relative">
        <button class="inline-flex items-center px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 dark:bg-slate-900 transition-colors duration-200" 
                type="button" onclick="toggleDropdown('filter-dropdown')">
            <i class="fas fa-filter mr-2"></i>Filtrer
            <i class="fas fa-chevron-down ml-1"></i>
        </button>
        <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white dark:bg-slate-800 ring-1 ring-black ring-opacity-5 hidden z-10" 
             id="filter-dropdown">
            <div class="py-1">
                <a href="{{ route('admin.categories.index') }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:bg-slate-800">Toutes</a>
                <a href="{{ route('admin.categories.index', ['parent' => 'null']) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:bg-slate-800">Principales</a>
                <a href="{{ route('admin.categories.index', ['has_children' => '1']) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:bg-slate-800">Avec sous-catégories</a>
                <a href="{{ route('admin.categories.index', ['status' => 'active']) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:bg-slate-800">Actives</a>
                <a href="{{ route('admin.categories.index', ['featured' => '1']) }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:bg-slate-800">En vedette</a>
            </div>
        </div>
    </div>
    <a href="{{ route('admin.categories.create') }}" 
       class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors duration-200">
        <i class="fas fa-plus mr-2"></i>Nouvelle Catégorie
    </a>
</div>
@endsection

@section('content')
<!-- Liste des catégories -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <div class="p-4 md:p-6 border-b border-slate-200 dark:border-slate-700">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h5 class="text-lg md:text-xl font-bold text-slate-900 dark:text-white mb-1">Liste des catégories</h5>
                @if(isset($categories))
                    <p class="text-xs md:text-sm text-slate-500 dark:text-slate-400">{{ $categories->count() }} catégorie(s) trouvée(s)</p>
                @endif
            </div>
        </div>
    </div>
    <div class="p-0">
        @if(isset($categories) && $categories->count() > 0)
            <!-- Version Desktop - Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Catégorie</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Parent</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Produits</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($categories as $category)
                        <tr class="hover:bg-slate-50 dark:bg-slate-900 transition-colors {{ !$category->is_active ? 'opacity-60' : '' }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($category->image)
                                        <img src="{{ $category->image_url }}" 
                                             class="w-12 h-12 rounded-lg object-cover border border-slate-200 dark:border-slate-700 shadow-sm"
                                             alt="{{ $category->name }}">
                                    @else
                                        <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 text-white rounded-lg flex items-center justify-center border border-primary-300 shadow-sm">
                                            <i class="fas fa-folder text-xl"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-semibold text-slate-900 dark:text-white">{{ $category->name }}</div>
                                        @if($category->slug)
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $category->slug }}</div>
                                        @endif
                                        @if($category->description)
                                            <div class="text-xs text-slate-400 mt-0.5">{{ Str::limit($category->description, 40) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($category->parent)
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-level-up-alt text-slate-400 text-xs"></i>
                                        <span class="text-sm text-slate-700 dark:text-slate-200">{{ $category->parent->name }}</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-folder-open mr-1"></i>Principale
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col items-center">
                                    <div class="flex items-center">
                                        <strong class="text-primary-600 text-lg">{{ $category->items_count ?? 0 }}</strong>
                                        <span class="text-xs text-slate-500 dark:text-slate-400 ml-1">articles</span>
                                    </div>
                                    @if(($category->children_count ?? 0) > 0)
                                        <div class="text-xs text-slate-400 mt-1">
                                            <i class="fas fa-sitemap mr-1"></i>{{ $category->children_count }} sous-catégorie(s)
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category->is_active ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-100 border border-slate-200 dark:border-slate-700' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $category->is_active ? 'bg-green-500' : 'bg-slate-500' }} mr-1.5"></span>
                                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    @if($category->is_featured)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            <i class="fas fa-star text-yellow-500 mr-1"></i>Vedette
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-1 justify-end items-center">
                                    <button onclick="editCategory({{ $category->id }})" 
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-primary-600 hover:bg-primary-50 transition-colors"
                                            title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <div class="relative">
                                        <button class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-600 hover:bg-slate-50 dark:bg-slate-900 transition-colors" 
                                                type="button" 
                                                onclick="toggleDropdown('actions-{{ $category->id }}')">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white dark:bg-slate-800 ring-1 ring-black ring-opacity-5 hidden z-10" 
                                             id="actions-{{ $category->id }}">
                                            <div class="py-1">
                                                <button class="w-full text-left px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:bg-slate-800" onclick="toggleCategoryStatus('{{ $category->id }}', {{ $category->is_active ? 'false' : 'true' }})">
                                                    <i class="fas fa-{{ $category->is_active ? 'pause' : 'play' }} mr-2 text-{{ $category->is_active ? 'warning' : 'success' }}"></i>
                                                    {{ $category->is_active ? 'Désactiver' : 'Activer' }}
                                                </button>
                                                <button class="w-full text-left px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:bg-slate-800" onclick="toggleCategoryFeatured('{{ $category->id }}', {{ $category->is_featured ? 'false' : 'true' }})">
                                                    <i class="fas fa-star mr-2 text-yellow-500"></i>
                                                    {{ $category->is_featured ? 'Retirer vedette' : 'Mettre en vedette' }}
                                                </button>
                                                <hr class="my-1">
                                                <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50" onclick="deleteCategory({{ $category->id }}, '{{ addslashes($category->name) }}')">
                                                    <i class="fas fa-trash mr-2"></i>Supprimer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Version Mobile/Tablet - Cartes -->
            <div class="lg:hidden">
                @foreach($categories as $category)
                <div class="border-b border-slate-200 p-4 hover:bg-slate-50 dark:bg-slate-900 transition-colors {{ !$category->is_active ? 'opacity-60' : '' }}">
                    <div class="flex gap-3">
                        <!-- Logo -->
                        <div class="flex-shrink-0">
                            @if($category->image)
                                <img src="{{ $category->image_url }}" 
                                     class="w-16 h-16 rounded-lg object-cover border border-slate-200 dark:border-slate-700 shadow-sm"
                                     alt="{{ $category->name }}">
                            @else
                                <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 text-white rounded-lg flex items-center justify-center border border-primary-300 shadow-sm">
                                    <i class="fas fa-folder text-2xl"></i>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Contenu principal -->
                        <div class="flex-1 min-w-0">
                            <!-- En-tête avec nom et actions -->
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1 min-w-0">
                                    <h6 class="font-semibold text-slate-900 dark:text-white truncate">{{ $category->name }}</h6>
                                    @if($category->parent)
                                        <p class="text-xs text-slate-500 dark:text-slate-400"><i class="fas fa-level-up-alt mr-1"></i>{{ $category->parent->name }}</p>
                                    @endif
                                </div>
                                
                                <!-- Menu dropdown actions mobile -->
                                <div class="relative">
                                    <button class="text-slate-400 hover:text-slate-600 dark:text-slate-300 p-1" onclick="toggleMobileDropdown({{ $category->id }})">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white dark:bg-slate-800 ring-1 ring-black ring-opacity-5 hidden z-10" 
                                         id="mobile-dropdown-{{ $category->id }}">
                                        <div class="py-1">
                                            <button class="w-full text-left px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:bg-slate-800" onclick="editCategory({{ $category->id }})">
                                                <i class="fas fa-edit w-4"></i> Modifier
                                            </button>
                                            <button class="w-full text-left px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:bg-slate-800" onclick="toggleCategoryStatus('{{ $category->id }}', {{ $category->is_active ? 'false' : 'true' }})">
                                                <i class="fas {{ $category->is_active ? 'fa-pause' : 'fa-play' }} w-4"></i>
                                                {{ $category->is_active ? 'Désactiver' : 'Activer' }}
                                            </button>
                                            <button class="w-full text-left px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:bg-slate-800" onclick="toggleCategoryFeatured('{{ $category->id }}', {{ $category->is_featured ? 'false' : 'true' }})">
                                                <i class="fas fa-star w-4"></i>
                                                {{ $category->is_featured ? 'Retirer vedette' : 'Mettre en vedette' }}
                                            </button>
                                            <hr class="my-1">
                                            <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50" onclick="deleteCategory({{ $category->id }}, '{{ addslashes($category->name) }}')">
                                                <i class="fas fa-trash w-4"></i> Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Description -->
                            @if($category->description)
                                <p class="text-xs text-slate-400 mb-2 line-clamp-2">{{ $category->description }}</p>
                            @endif
                            
                            <!-- Badges et infos -->
                            <div class="flex flex-wrap gap-2 mb-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-100' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $category->is_active ? 'bg-green-500' : 'bg-slate-500' }} mr-1"></span>
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                
                                @if($category->is_featured)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-star text-yellow-500 mr-1"></i>Vedette
                                    </span>
                                @endif
                                
                                @if(!$category->parent)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-folder-open mr-1"></i>Principale
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Statistiques -->
                            <div class="flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400">
                                <span>
                                    <i class="fas fa-box text-slate-400"></i>
                                    {{ $category->items_count ?? 0 }} articles
                                </span>
                                @if(($category->children_count ?? 0) > 0)
                                    <span>
                                        <i class="fas fa-sitemap text-slate-400"></i>
                                        {{ $category->children_count }} sous-cat.
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-100 mb-4">
                    <i class="fas fa-folder text-3xl text-primary-600"></i>
                </div>
                <h5 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Aucune catégorie enregistrée</h5>
                <p class="text-slate-500 dark:text-slate-400 mb-4">Commencez par ajouter des catégories pour organiser vos articles.</p>
                <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Ajouter une catégorie
                </a>
            </div>
        @endif
    </div>
</div>

<div id="categoryModal" class="fixed inset-0 bg-slate-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl p-6 w-full max-w-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Nouvelle Catégorie</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 dark:text-slate-300 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="categoryForm">
                @csrf
                <input type="hidden" id="categoryId">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" required 
                               class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Description</label>
                        <textarea id="description" name="description" rows="3" 
                                  class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Catégorie parente</label>
                        <select id="parent_id" name="parent_id" 
                                class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Aucune (catégorie principale)</option>
                            @if(isset($categories))
                                @foreach($categories->whereNull('parent_id') as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Image</label>
                        <input type="file" id="image" name="image" accept="image/*"
                               class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Format: JPG, PNG. Taille max: 2MB</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="is_active" name="is_active" checked 
                                   class="w-4 h-4 text-primary-600 border-slate-300 dark:border-slate-600 rounded focus:ring-primary-500">
                            <span class="ml-2 text-sm text-slate-700 dark:text-slate-200">Active</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="is_featured" name="is_featured" 
                                   class="w-4 h-4 text-primary-600 border-slate-300 dark:border-slate-600 rounded focus:ring-primary-500">
                            <span class="ml-2 text-sm text-slate-700 dark:text-slate-200">En vedette</span>
                        </label>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="closeModal()" 
                            class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 dark:bg-slate-900 transition-colors">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Enregistrer
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

function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const allDropdowns = document.querySelectorAll('[id$="-dropdown"], [id^="actions-"], [id^="mobile-dropdown-"]');
    
    allDropdowns.forEach(d => {
        if (d.id !== dropdownId) {
            d.classList.add('hidden');
        }
    });
    
    dropdown.classList.toggle('hidden');
}

document.addEventListener('click', function(event) {
    if (!event.target.closest('button[onclick*="toggleDropdown"]') && 
        !event.target.closest('button[onclick*="toggleMobileDropdown"]')) {
        const allDropdowns = document.querySelectorAll('[id$="-dropdown"], [id^="actions-"], [id^="mobile-dropdown-"]');
        allDropdowns.forEach(d => d.classList.add('hidden'));
    }
});

function toggleMobileDropdown(categoryId) {
    toggleDropdown('mobile-dropdown-' + categoryId);
}

function editCategory(id) {
    window.location.href = '{{ route("admin.categories.index") }}/' + id + '/edit';
}

function toggleCategoryStatus(id, newStatus) {
    if (confirm('Voulez-vous changer le statut de cette catégorie ?')) {
        fetch('{{ url("admin/categories") }}/' + id + '/status', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ is_active: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 800);
            } else {
                showToast(data.message || 'Une erreur est survenue', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Erreur lors de la mise à jour du statut', 'error');
        });
    }
}

function toggleCategoryFeatured(id, newStatus) {
    if (confirm('Voulez-vous modifier le statut vedette de cette catégorie ?')) {
        fetch('{{ url("admin/categories") }}/' + id + '/featured', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ is_featured: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 800);
            } else {
                showToast(data.message || 'Une erreur est survenue', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Erreur lors de la mise à jour du statut vedette', 'error');
        });
    }
}

function deleteCategory(id, name) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer la catégorie "${name}" ?\n\nCette action est irréversible.`)) {
        fetch('{{ url("admin/categories") }}/' + id, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 800);
            } else {
                showToast(data.message || 'Une erreur est survenue', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Erreur lors de la suppression', 'error');
        });
    }
}
</script>
@endpush