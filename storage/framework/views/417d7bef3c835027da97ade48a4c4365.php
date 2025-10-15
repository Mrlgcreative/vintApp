

<?php $__env->startSection('title', 'Gestion des catégories'); ?>
<?php $__env->startSection('page-title', 'Gestion des catégories'); ?>

<?php $__env->startSection('page-actions'); ?>
<div class="flex flex-col sm:flex-row flex-wrap gap-2 sm:gap-3">
    <!-- Filtre Dropdown -->
    <div class="relative">
        <button onclick="toggleDropdown('filterDropdown')" 
                class="inline-flex items-center justify-center w-full sm                                                <div class="border-t border-gray-100"></div>
                                                <button class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50" 
                                                        onclick="deleteCategory(<?php echo e($category->id); ?>, '<?php echo e($category->name); ?>')">
                                                    <i class="fas fa-trash mr-2"></i>Supprimer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Vue Mobile/Tablet (Cartes) - Cachée sur desktop -->
            <div class="lg:hidden">
                <div class="divide-y divide-gray-200">
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div data-category-id="<?php echo e($category->id); ?>" 
                         data-parent-id="<?php echo e($category->parent_id); ?>"
                         class="p-4 <?php echo e(!$category->is_active ? 'bg-gray-50' : 'bg-white'); ?> hover:bg-gray-50 transition-colors">
                        <!-- Header de la carte -->
                        <div class="flex items-start gap-3 mb-3">
                            <input class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 category-checkbox mt-1" 
                                   type="checkbox" 
                                   value="<?php echo e($category->id); ?>">
                            
                            <?php if($category->image): ?>
                                <img src="<?php echo e($category->image_url); ?>" 
                                     class="rounded border border-gray-200 flex-shrink-0" 
                                     width="48" 
                                     height="48" 
                                     style="object-fit: cover;"
                                     alt="Image <?php echo e($category->name); ?>">
                            <?php elseif($category->icon): ?>
                                <div class="text-center w-12 h-12 flex items-center justify-center flex-shrink-0">
                                    <i class="<?php echo e($category->icon); ?> text-2xl" style="color: <?php echo e($category->color ?? '#6b7280'); ?>"></i>
                                </div>
                            <?php else: ?>
                                <div class="w-12 h-12 rounded flex items-center justify-center flex-shrink-0 text-white" 
                                     style="background-color: <?php echo e($category->color ?? '#6b7280'); ?>;">
                                    <i class="fas fa-tag"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-900 truncate"><?php echo e($category->name); ?></h4>
                                        <?php if($category->slug): ?>
                                            <p class="text-sm text-gray-500 truncate"><?php echo e($category->slug); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="relative flex-shrink-0">
                                        <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors" 
                                                onclick="toggleMobileDropdown('mobile-actions-<?php echo e($category->id); ?>')">
                                            <i class="fas fa-ellipsis-v text-gray-600"></i>
                                        </button>
                                        <div id="mobile-actions-<?php echo e($category->id); ?>" 
                                             class="hidden absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-30">
                                            <div class="py-1">
                                                <a href="<?php echo e(route('admin.categories.show', $category)); ?>" 
                                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <i class="fas fa-eye mr-2"></i>Voir les détails
                                                </a>
                                                <a href="<?php echo e(route('admin.categories.edit', $category)); ?>" 
                                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <i class="fas fa-edit mr-2"></i>Modifier
                                                </a>
                                                <?php if(!$category->parent_id): ?>
                                                    <div class="border-t border-gray-100"></div>
                                                    <a href="<?php echo e(route('admin.categories.create', ['parent' => $category->id])); ?>" 
                                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                        <i class="fas fa-plus mr-2"></i>Ajouter sous-catégorie
                                                    </a>
                                                <?php endif; ?>
                                                <div class="border-t border-gray-100"></div>
                                                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                                        onclick="toggleCategoryStatus(<?php echo e($category->id); ?>)">
                                                    <i class="fas fa-<?php echo e($category->is_active ? 'pause' : 'play'); ?> mr-2"></i>
                                                    <?php echo e($category->is_active ? 'Désactiver' : 'Activer'); ?>

                                                </button>
                                                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                                        onclick="toggleCategoryFeatured(<?php echo e($category->id); ?>)">
                                                    <i class="fas fa-star mr-2"></i>
                                                    <?php echo e($category->is_featured ? 'Retirer de la vedette' : 'Mettre en vedette'); ?>

                                                </button>
                                                <div class="border-t border-gray-100"></div>
                                                <button class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50" 
                                                        onclick="deleteCategory(<?php echo e($category->id); ?>, '<?php echo e($category->name); ?>')">
                                                    <i class="fas fa-trash mr-2"></i>Supprimer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if($category->description): ?>
                                    <p class="text-sm text-gray-500 mt-1 line-clamp-2"><?php echo e(Str::limit($category->description, 60)); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Badges et infos -->
                        <div class="flex flex-wrap gap-2 mb-3">
                            <?php if($category->parent_id): ?>
                                <div class="flex items-center text-xs">
                                    <i class="fas fa-level-up-alt text-gray-400 transform rotate-90 mr-1"></i>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full bg-gray-100 text-gray-700 border border-gray-300">
                                        <?php echo e($category->parent->name); ?>

                                    </span>
                                </div>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary-50 text-primary-700 border border-primary-200">
                                    Principale
                                </span>
                                <?php if($category->children_count > 0): ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                        <?php echo e($category->children_count); ?> enfant(s)
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?php echo e($category->is_active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-50 text-gray-700 border border-gray-300'); ?>">
                                <?php echo e($category->is_active ? 'Active' : 'Inactive'); ?>

                            </span>
                            
                            <?php if($category->is_featured): ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">
                                    <i class="fas fa-star mr-1"></i>Vedette
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Statistiques et contrôles -->
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500">Articles:</span>
                                <strong class="text-primary-600 ml-1"><?php echo e($category->items_count ?? 0); ?></strong>
                                <?php if($category->children_count > 0): ?>
                                    <div class="text-xs text-gray-500 mt-1">
                                        +<?php echo e($category->total_items_count ?? 0); ?> sous-cat.
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <span class="text-gray-500">Ordre:</span>
                                <strong class="text-gray-900 ml-1"><?php echo e($category->sort_order ?? 0); ?></strong>
                                <div class="flex gap-1 mt-1">
                                    <button class="p-1 border border-gray-300 rounded hover:bg-gray-50 transition-colors" 
                                            onclick="moveCategory(<?php echo e($category->id); ?>, 'up')" 
                                            title="Monter">
                                        <i class="fas fa-chevron-up text-xs text-gray-600"></i>
                                    </button>
                                    <button class="p-1 border border-gray-300 rounded hover:bg-gray-50 transition-colors" 
                                            onclick="moveCategory(<?php echo e($category->id); ?>, 'down')" 
                                            title="Descendre">
                                        <i class="fas fa-chevron-down text-xs text-gray-600"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Footer -->
                        <div class="mt-3 pt-3 border-t border-gray-200 flex items-center justify-between text-xs text-gray-500">
                            <span>Créée le <?php echo e($category->created_at->format('d/m/Y à H:i')); ?></span>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>:px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
            <i class="fas fa-filter mr-2"></i>
            <span class="hidden sm:inline">Filtrer</span>
            <span class="sm:hidden">Filtres</span>
            <i class="fas fa-chevron-down ml-2"></i>
        </button>
        <div id="filterDropdown" class="hidden absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20">
            <div class="py-1">
                <a href="<?php echo e(route('admin.categories.index')); ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-list mr-3 w-4"></i>Toutes
                </a>
                <a href="<?php echo e(route('admin.categories.index', ['parent' => 'null'])); ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-folder mr-3 w-4"></i>Principales
                </a>
                <a href="<?php echo e(route('admin.categories.index', ['has_children' => '1'])); ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-sitemap mr-3 w-4"></i>Avec sous-catégories
                </a>
                <a href="<?php echo e(route('admin.categories.index', ['status' => 'active'])); ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-check-circle mr-3 w-4"></i>Actives
                </a>
                <a href="<?php echo e(route('admin.categories.index', ['featured' => '1'])); ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-star mr-3 w-4"></i>En vedette
                </a>
            </div>
        </div>
    </div>
    
    <!-- Tri Dropdown -->
    <div class="relative">
        <button onclick="toggleDropdown('sortDropdown')" 
                class="inline-flex items-center justify-center w-full sm:w-auto px-3 sm:px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
            <i class="fas fa-sort mr-2"></i>
            <span class="hidden sm:inline">Trier</span>
            <i class="fas fa-chevron-down ml-2"></i>
        </button>
        <div id="sortDropdown" class="hidden absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20">
            <div class="py-1">
                <a href="<?php echo e(route('admin.categories.index', ['sort' => 'name'])); ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-sort-alpha-down mr-3 w-4"></i>Nom A-Z
                </a>
                <a href="<?php echo e(route('admin.categories.index', ['sort' => '-name'])); ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-sort-alpha-up mr-3 w-4"></i>Nom Z-A
                </a>
                <a href="<?php echo e(route('admin.categories.index', ['sort' => 'sort_order'])); ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-sort-numeric-down mr-3 w-4"></i>Ordre
                </a>
                <a href="<?php echo e(route('admin.categories.index', ['sort' => '-created_at'])); ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-clock mr-3 w-4"></i>Plus récentes
                </a>
            </div>
        </div>
    </div>
    
    <a href="<?php echo e(route('admin.categories.create')); ?>" 
       class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors duration-200">
        <i class="fas fa-plus mr-2"></i>
        <span class="hidden sm:inline">Ajouter une catégorie</span>
        <span class="sm:hidden">Ajouter</span>
    </a>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Messages de succès/erreur -->
<?php if(session('success')): ?>
    <div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-4">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-600 mr-3"></i>
            <span class="text-green-800 text-sm font-medium"><?php echo e(session('success')); ?></span>
            <button type="button" onclick="this.parentElement.parentElement.remove()" class="ml-auto text-green-600 hover:text-green-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
            <span class="text-red-800 text-sm font-medium"><?php echo e(session('error')); ?></span>
            <button type="button" onclick="this.parentElement.parentElement.remove()" class="ml-auto text-red-600 hover:text-red-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
<?php endif; ?>

<!-- Barre de recherche et filtres -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4 sm:mb-6">
    <div class="p-4 sm:p-6">
        <form method="GET" action="<?php echo e(route('admin.categories.index')); ?>" class="space-y-4">
            <!-- Ligne 1: Recherche -->
            <div class="grid grid-cols-1 gap-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm sm:text-base" 
                           name="search" 
                           placeholder="Rechercher une catégorie..." 
                           value="<?php echo e(request('search')); ?>">
                </div>
            </div>
            
            <!-- Ligne 2: Filtres -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <select name="parent" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm sm:text-base">
                        <option value="">Toutes les catégories</option>
                        <option value="null" <?php echo e(request('parent') === 'null' ? 'selected' : ''); ?>>Principales seulement</option>
                        <option value="has_parent" <?php echo e(request('parent') === 'has_parent' ? 'selected' : ''); ?>>Sous-catégories</option>
                    </select>
                </div>
                <div>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm sm:text-base">
                        <option value="">Tous les statuts</option>
                        <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Active</option>
                        <option value="inactive" <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                    </select>
                </div>
                <div>
                    <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm sm:text-base">
                        <option value="sort_order" <?php echo e(request('sort', 'sort_order') === 'sort_order' ? 'selected' : ''); ?>>Ordre</option>
                        <option value="name" <?php echo e(request('sort') === 'name' ? 'selected' : ''); ?>>Nom A-Z</option>
                        <option value="-name" <?php echo e(request('sort') === '-name' ? 'selected' : ''); ?>>Nom Z-A</option>
                        <option value="-created_at" <?php echo e(request('sort') === '-created_at' ? 'selected' : ''); ?>>Plus récente</option>
                    </select>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors duration-200">
                        <i class="fas fa-search mr-2"></i>Rechercher
                    </button>
                    <a href="<?php echo e(route('admin.categories.index')); ?>" 
                       class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statistiques -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm text-gray-500 mb-1">Total</p>
                <h3 class="text-xl sm:text-2xl font-bold text-primary-600"><?php echo e($categories->total() ?? 0); ?></h3>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-primary-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-layer-group text-primary-600 text-lg sm:text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm text-gray-500 mb-1">Principales</p>
                <h3 class="text-xl sm:text-2xl font-bold text-green-600"><?php echo e($categories->where('parent_id', null)->count() ?? 0); ?></h3>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-sitemap text-green-600 text-lg sm:text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm text-gray-500 mb-1">En vedette</p>
                <h3 class="text-xl sm:text-2xl font-bold text-yellow-600"><?php echo e($categories->where('is_featured', 1)->count() ?? 0); ?></h3>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-star text-yellow-600 text-lg sm:text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm text-gray-500 mb-1">Articles</p>
                <h3 class="text-xl sm:text-2xl font-bold text-blue-600"><?php echo e($categories->sum('items_count') ?? 0); ?></h3>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-box text-blue-600 text-lg sm:text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Liste des catégories -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <div>
                <h3 class="text-base sm:text-lg font-semibold text-gray-900">Structure hiérarchique</h3>
                <?php if(isset($categories)): ?>
                    <p class="text-sm text-gray-500 mt-1"><?php echo e($categories->total()); ?> catégorie(s) trouvée(s)</p>
                <?php endif; ?>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <label class="flex items-center gap-2">
                    <input type="checkbox" 
                           class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500" 
                           id="expandAll" 
                           onchange="toggleExpandAll()">
                    <span class="text-sm text-gray-700">Développer tout</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" 
                           class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500" 
                           id="showInactive" 
                           onchange="toggleInactive()">
                    <span class="text-sm text-gray-700">Voir inactives</span>
                </label>
            </div>
        </div>
    </div>
    
    <div>
        <?php if(isset($categories) && $categories->count() > 0): ?>
            <!-- Vue Desktop (Table) - Cachée sur mobile et tablet -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" 
                                       class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500" 
                                       id="selectAll" 
                                       onchange="toggleSelectAll()">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hiérarchie</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Articles</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ordre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Créée le</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="categoriesTable">
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr data-category-id="<?php echo e($category->id); ?>" 
                            data-parent-id="<?php echo e($category->parent_id); ?>"
                            class="<?php echo e(!$category->is_active ? 'bg-gray-50' : ''); ?> hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 category-checkbox" 
                                       type="checkbox" 
                                       value="<?php echo e($category->id); ?>">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <?php if($category->parent_id): ?>
                                        <div class="mr-3 w-5">
                                            <i class="fas fa-level-up-alt text-gray-400 transform rotate-90"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if($category->image): ?>
                                        <img src="<?php echo e($category->image_url); ?>" 
                                             class="rounded mr-3 border border-gray-200" 
                                             width="40" 
                                             height="40" 
                                             style="object-fit: cover;"
                                             alt="Image <?php echo e($category->name); ?>">
                                    <?php elseif($category->icon): ?>
                                        <div class="mr-3 text-center w-10">
                                            <i class="<?php echo e($category->icon); ?> text-xl" style="color: <?php echo e($category->color ?? '#6b7280'); ?>"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded flex items-center justify-center mr-3 text-white" 
                                             style="background-color: <?php echo e($category->color ?? '#6b7280'); ?>;">
                                            <i class="fas fa-tag"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div>
                                        <div class="font-semibold text-gray-900"><?php echo e($category->name); ?></div>
                                        <?php if($category->slug): ?>
                                            <div class="text-sm text-gray-500"><?php echo e($category->slug); ?></div>
                                        <?php endif; ?>
                                        <?php if($category->description): ?>
                                            <div class="text-sm text-gray-500"><?php echo e(Str::limit($category->description, 40)); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if($category->parent): ?>
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700 border border-gray-300">
                                            <?php echo e($category->parent->name); ?>

                                        </span>
                                        <i class="fas fa-arrow-right mx-2 text-gray-400 text-xs"></i>
                                        <span class="text-sm text-gray-600"><?php echo e($category->name); ?></span>
                                    </div>
                                <?php else: ?>
                                    <div class="flex items-center gap-1">
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-primary-50 text-primary-700 border border-primary-200">
                                            Principale
                                        </span>
                                        <?php if($category->children_count > 0): ?>
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                                <?php echo e($category->children_count); ?> enfant(s)
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium <?php echo e($category->is_active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-50 text-gray-700 border border-gray-300'); ?>">
                                        <?php echo e($category->is_active ? 'Active' : 'Inactive'); ?>

                                    </span>
                                    <?php if($category->is_featured): ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">
                                            <i class="fas fa-star mr-1"></i>Vedette
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <strong class="text-primary-600"><?php echo e($category->items_count ?? 0); ?></strong>
                                    <span class="text-gray-500 ml-1 text-sm">articles</span>
                                </div>
                                <?php if($category->children_count > 0): ?>
                                    <div class="text-sm text-gray-500">
                                        + <?php echo e($category->total_items_count ?? 0); ?> dans sous-cat.
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                        <?php echo e($category->sort_order ?? 0); ?>

                                    </span>
                                    <div class="ml-2 flex gap-1">
                                        <button class="p-1 border border-gray-300 rounded hover:bg-gray-50 transition-colors" 
                                                onclick="moveCategory(<?php echo e($category->id); ?>, 'up')" 
                                                title="Monter">
                                            <i class="fas fa-chevron-up text-xs text-gray-600"></i>
                                        </button>
                                        <button class="p-1 border border-gray-300 rounded hover:bg-gray-50 transition-colors" 
                                                onclick="moveCategory(<?php echo e($category->id); ?>, 'down')" 
                                                title="Descendre">
                                            <i class="fas fa-chevron-down text-xs text-gray-600"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo e($category->created_at->format('d/m/Y')); ?></div>
                                <div class="text-sm text-gray-500"><?php echo e($category->created_at->format('H:i')); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex gap-1 justify-end">
                                    <a href="<?php echo e(route('admin.categories.show', $category)); ?>" 
                                       class="inline-flex items-center justify-center w-8 h-8 border border-blue-300 rounded-lg text-blue-700 hover:bg-blue-50 transition-colors" 
                                       title="Voir les détails">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="<?php echo e(route('admin.categories.edit', $category)); ?>" 
                                       class="inline-flex items-center justify-center w-8 h-8 border border-primary-300 rounded-lg text-primary-700 hover:bg-primary-50 transition-colors"
                                       title="Modifier">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <div class="relative">
                                        <button class="inline-flex items-center justify-center w-8 h-8 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors" 
                                                type="button" 
                                                onclick="toggleDropdown('actions-<?php echo e($category->id); ?>')">
                                            <i class="fas fa-ellipsis-v text-sm"></i>
                                        </button>
                                        <div id="actions-<?php echo e($category->id); ?>" 
                                             class="hidden absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20">
                                            <div class="py-1">
                                                <?php if(!$category->parent_id): ?>
                                                    <a href="<?php echo e(route('admin.categories.create', ['parent' => $category->id])); ?>" 
                                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                        <i class="fas fa-plus mr-2"></i>Ajouter sous-catégorie
                                                    </a>
                                                    <div class="border-t border-gray-100"></div>
                                                <?php endif; ?>
                                                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                                        onclick="toggleCategoryStatus(<?php echo e($category->id); ?>)">
                                                    <i class="fas fa-<?php echo e($category->is_active ? 'pause' : 'play'); ?> mr-2"></i>
                                                    <?php echo e($category->is_active ? 'Désactiver' : 'Activer'); ?>

                                                </button>
                                                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                                        onclick="toggleCategoryFeatured(<?php echo e($category->id); ?>)">
                                                    <i class="fas fa-star mr-2"></i>
                                                    <?php echo e($category->is_featured ? 'Retirer de la vedette' : 'Mettre en vedette'); ?>

                                                </button>
                                                <div class="border-t border-gray-100"></div>
                                            <li>
                                                <button class="dropdown-item text-danger" onclick="deleteCategory(<?php echo e($category->id); ?>, '<?php echo e($category->name); ?>')">
                                                    <i class="fas fa-trash me-2"></i>Supprimer
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Actions en lot -->
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4" id="bulkActions" style="display: none;">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <span class="font-semibold text-gray-900" id="selectedCount">0</span>
                        <span class="text-gray-600 ml-1">catégorie(s) sélectionnée(s)</span>
                    </div>
                    <div class="flex gap-2">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 transition-colors" onclick="bulkAction('activate')">
                            <i class="fas fa-check mr-2"></i>Activer
                        </button>
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-yellow-600 hover:bg-yellow-700 transition-colors" onclick="bulkAction('deactivate')">
                            <i class="fas fa-pause mr-2"></i>Désactiver
                        </button>
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-colors" onclick="bulkAction('reorder')">
                            <i class="fas fa-sort mr-2"></i>Réorganiser
                        </button>
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 transition-colors" onclick="bulkAction('delete')">
                            <i class="fas fa-trash mr-2"></i>Supprimer
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Pagination -->
            <?php if($categories->hasPages()): ?>
                <div class="bg-white border-t border-gray-200 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-700">
                            Affichage de <span class="font-medium"><?php echo e($categories->firstItem()); ?></span> à 
                            <span class="font-medium"><?php echo e($categories->lastItem()); ?></span> sur 
                            <span class="font-medium"><?php echo e($categories->total()); ?></span> résultats
                        </div>
                        <div>
                            <?php echo e($categories->appends(request()->query())->links()); ?>

                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-12">
                <?php if(request()->has('search') || request()->has('status') || request()->has('parent')): ?>
                    <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-search text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucune catégorie trouvée</h3>
                    <p class="text-gray-500 mb-6">Aucune catégorie ne correspond à vos critères de recherche.</p>
                    <a href="<?php echo e(route('admin.categories.index')); ?>" 
                       class="inline-flex items-center px-4 py-2 border border-primary-300 text-sm font-medium rounded-lg text-primary-700 bg-primary-50 hover:bg-primary-100 transition-colors">
                        <i class="fas fa-times mr-2"></i>Effacer les filtres
                    </a>
                <?php else: ?>
                    <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-layer-group text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucune catégorie enregistrée</h3>
                    <p class="text-gray-500 mb-6">Commencez par créer des catégories pour organiser vos articles.</p>
                    <a href="<?php echo e(route('admin.categories.create')); ?>" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Ajouter une catégorie
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Confirmer la suppression
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Êtes-vous sûr de vouloir supprimer la catégorie <strong id="categoryNameToDelete"></strong> ?
                        </p>
                        <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        Cette action supprimera également toutes les sous-catégories associées.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <form id="deleteForm" method="POST" class="inline">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" 
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Supprimer définitivement
                    </button>
                </form>
                <button type="button" onclick="closeDeleteModal()" 
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Fonction pour les dropdowns desktop
function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const allDropdowns = document.querySelectorAll('[id$="Dropdown"], [id^="actions-"]');
    
    allDropdowns.forEach(d => {
        if (d.id !== dropdownId) {
            d.classList.add('hidden');
        }
    });
    
    dropdown.classList.toggle('hidden');
}

// Fonction pour les dropdowns mobile
function toggleMobileDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const allDropdowns = document.querySelectorAll('[id^="mobile-actions-"]');
    
    allDropdowns.forEach(d => {
        if (d.id !== dropdownId) {
            d.classList.add('hidden');
        }
    });
    
    dropdown.classList.toggle('hidden');
}

// Fermer les dropdowns en cliquant ailleurs
document.addEventListener('click', function(event) {
    if (!event.target.closest('button')) {
        document.querySelectorAll('[id$="Dropdown"], [id^="actions-"], [id^="mobile-actions-"]').forEach(d => {
            d.classList.add('hidden');
        });
    }
});

// Gestion de la sélection multiple
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.category-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateBulkActions();
}

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.category-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    
    if (checkboxes.length > 0) {
        bulkActions.style.display = 'block';
        selectedCount.textContent = checkboxes.length;
    } else {
        bulkActions.style.display = 'none';
    }
}

// Écouteurs pour les checkboxes
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('category-checkbox')) {
        updateBulkActions();
    }
});

// Développer/réduire tout
function toggleExpandAll() {
    const expandAll = document.getElementById('expandAll').checked;
    const subCategories = document.querySelectorAll('[data-parent-id]:not([data-parent-id=""])');
    
    subCategories.forEach(row => {
        row.style.display = expandAll ? '' : 'none';
    });
}

// Afficher/masquer les catégories inactives
function toggleInactive() {
    const showInactive = document.getElementById('showInactive').checked;
    const inactiveRows = document.querySelectorAll('.bg-gray-50[data-category-id]');
    
    inactiveRows.forEach(row => {
        row.style.display = showInactive ? '' : 'none';
    });
}

// Changer le statut d'une catégorie
function toggleCategoryStatus(categoryId) {
    fetch(`/admin/categories/${categoryId}/status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ is_active: true })
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

// Mettre en vedette / retirer de la vedette
function toggleCategoryFeatured(categoryId) {
    fetch(`/admin/categories/${categoryId}/featured`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de la modification');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la modification');
    });
}

// Déplacer une catégorie (ordre)
function moveCategory(categoryId, direction) {
    fetch(`/admin/categories/${categoryId}/move`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ direction: direction })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors du déplacement');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors du déplacement');
    });
}

// Supprimer une catégorie
function deleteCategory(categoryId, categoryName) {
    document.getElementById('categoryNameToDelete').textContent = categoryName;
    document.getElementById('deleteForm').action = `/admin/categories/${categoryId}`;
    document.getElementById('deleteModal').classList.remove('hidden');
}

// Fermer le modal de suppression
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Actions en lot
function bulkAction(action) {
    const selectedCategories = Array.from(document.querySelectorAll('.category-checkbox:checked')).map(cb => cb.value);
    
    if (selectedCategories.length === 0) {
        alert('Veuillez sélectionner au moins une catégorie.');
        return;
    }
    
    let confirmMessage = '';
    switch(action) {
        case 'activate':
            confirmMessage = `Activer ${selectedCategories.length} catégorie(s) ?`;
            break;
        case 'deactivate':
            confirmMessage = `Désactiver ${selectedCategories.length} catégorie(s) ?`;
            break;
        case 'reorder':
            confirmMessage = `Réorganiser ${selectedCategories.length} catégorie(s) ?`;
            break;
        case 'delete':
            confirmMessage = `Supprimer définitivement ${selectedCategories.length} catégorie(s) et leurs sous-catégories ?`;
            break;
    }
    
    if (confirm(confirmMessage)) {
        console.log(`${action} categories:`, selectedCategories);
        alert(`Fonctionnalité à implémenter : ${action}`);
    }
}

// Organiser les catégories par hiérarchie
function organizeHierarchy() {
    const table = document.getElementById('categoriesTable');
    const rows = Array.from(table.querySelectorAll('tr'));
    
    // Séparer les catégories principales et les sous-catégories
    const mainCategories = rows.filter(row => row.dataset.parentId === '' || !row.dataset.parentId);
    const subCategories = rows.filter(row => row.dataset.parentId && row.dataset.parentId !== '');
    
    // Réorganiser
    mainCategories.forEach(mainRow => {
        table.appendChild(mainRow);
        
        // Ajouter les sous-catégories de cette catégorie principale
        const categoryId = mainRow.dataset.categoryId;
        const children = subCategories.filter(row => row.dataset.parentId === categoryId);
        
        children.forEach(childRow => {
            table.appendChild(childRow);
        });
    });
}

// Exécuter au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    organizeHierarchy();
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/admin/categories/index.blade.php ENDPATH**/ ?>