

<?php $__env->startSection('content'); ?>
<?php
use Illuminate\Support\Facades\Storage;
?>
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header avec titre et bouton d'ajout -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-layer-group text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Catégories</h1>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mt-1">Organisez vos produits par catégories</p>
                </div>
            </div>
            <a href="<?php echo e(route('categories.create')); ?>" 
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/25 hover:from-primary-600 hover:to-primary-700 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <i class="fas fa-plus mr-2"></i>
                Ajouter une catégorie
            </a>
        </div>

        <!-- Messages de succès et d'erreur -->
        <?php if(session('success')): ?>
            <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-emerald-400"></i>
                    </div>
                    <div class="ml-3">
                        <div class="text-sm text-emerald-700"><?php echo e(session('success')); ?></div>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" 
                                class="inline-flex text-emerald-400 hover:text-emerald-600 transition-colors duration-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <div class="text-sm text-red-700"><?php echo e(session('error')); ?></div>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" 
                                class="inline-flex text-red-400 hover:text-red-600 transition-colors duration-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tableau des catégories -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-primary-600/10 border border-gray-100/50 overflow-hidden">
            <!-- Header du tableau -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-list text-primary-500 mr-2"></i>
                    Liste des catégories
                </h3>
            </div>

            <!-- Version Desktop : Tableau -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Image/Icône</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Nom</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Articles</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-primary-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center overflow-hidden">
                                        <?php if($category->image && Storage::disk('public')->exists($category->image)): ?>
                                            <img src="<?php echo e(Storage::url($category->image)); ?>" 
                                                 alt="<?php echo e($category->name); ?>" 
                                                 class="w-full h-full object-cover">
                                        <?php elseif($category->icon): ?>
                                            <i class="<?php echo e($category->icon); ?> text-primary-500 text-xl"></i>
                                        <?php else: ?>
                                            <i class="fas fa-folder text-gray-400 text-xl"></i>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-gray-900 dark:text-white"><?php echo e($category->name); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-600 dark:text-gray-300 text-sm max-w-xs">
                                        <?php echo e(Str::limit($category->description, 50) ?: '-'); ?>

                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        <?php echo e($category->items_count ?? 0); ?> article<?php echo e(($category->items_count ?? 0) > 1 ? 's' : ''); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($category->is_active): ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2"></span>
                                            Active
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100">
                                            <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                            Inactive
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="<?php echo e(route('categories.show', $category)); ?>" 
                                           class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200"
                                           title="Voir la catégorie">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <a href="<?php echo e(route('categories.edit', $category)); ?>" 
                                           class="inline-flex items-center px-3 py-2 text-sm font-medium text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 hover:text-primary-700 transition-colors duration-200"
                                           title="Modifier la catégorie">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <form action="<?php echo e(route('categories.destroy', $category)); ?>" 
                                              method="POST" 
                                              class="inline-block" 
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ? Tous les articles associés seront également supprimés.');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" 
                                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 hover:text-red-700 transition-colors duration-200"
                                                    title="Supprimer la catégorie">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-layer-group text-gray-400 text-2xl"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucune catégorie trouvée</h3>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-6">Commencez par créer votre première catégorie</p>
                                        <a href="<?php echo e(route('categories.create')); ?>" 
                                           class="inline-flex items-center px-4 py-2 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors duration-200">
                                            <i class="fas fa-plus mr-2"></i>
                                            Créer la première catégorie
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Version Mobile : Cartes -->
            <div class="lg:hidden">
                <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="border-b border-gray-200 dark:border-gray-700 last:border-b-0 p-6 hover:bg-primary-50 transition-colors duration-200">
                        <div class="flex items-start space-x-4">
                            <!-- Image/Icône -->
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center overflow-hidden">
                                    <?php if($category->image && Storage::disk('public')->exists($category->image)): ?>
                                        <img src="<?php echo e(Storage::url($category->image)); ?>" 
                                             alt="<?php echo e($category->name); ?>" 
                                             class="w-full h-full object-cover">
                                    <?php elseif($category->icon): ?>
                                        <i class="<?php echo e($category->icon); ?> text-primary-500 text-2xl"></i>
                                    <?php else: ?>
                                        <i class="fas fa-folder text-gray-400 text-2xl"></i>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Contenu -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1"><?php echo e($category->name); ?></h3>
                                        <?php if($category->description): ?>
                                            <p class="text-gray-600 dark:text-gray-300 text-sm mb-2"><?php echo e(Str::limit($category->description, 80)); ?></p>
                                        <?php endif; ?>
                                        <div class="flex items-center space-x-3 mb-3">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                <?php echo e($category->items_count ?? 0); ?> article<?php echo e(($category->items_count ?? 0) > 1 ? 's' : ''); ?>

                                            </span>
                                            <?php if($category->is_active): ?>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                    <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2"></span>
                                                    Active
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100">
                                                    <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                                    Inactive
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center space-x-2">
                                    <a href="<?php echo e(route('categories.show', $category)); ?>" 
                                       class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                                        <i class="fas fa-eye mr-2"></i>
                                        Voir
                                    </a>
                                    <a href="<?php echo e(route('categories.edit', $category)); ?>" 
                                       class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors duration-200">
                                        <i class="fas fa-edit mr-2"></i>
                                        Modifier
                                    </a>
                                    <form action="<?php echo e(route('categories.destroy', $category)); ?>" 
                                          method="POST" 
                                          class="flex-1" 
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ? Tous les articles associés seront également supprimés.');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" 
                                                class="w-full inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors duration-200">
                                            <i class="fas fa-trash mr-2"></i>
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="p-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-6">
                                <i class="fas fa-layer-group text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Aucune catégorie trouvée</h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-8 max-w-sm">Commencez par créer votre première catégorie pour organiser vos produits</p>
                            <a href="<?php echo e(route('categories.create')); ?>" 
                               class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-semibold rounded-xl shadow-lg hover:bg-primary-700 hover:shadow-xl transition-all duration-300">
                                <i class="fas fa-plus mr-2"></i>
                                Créer la première catégorie
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pagination -->
        <?php if(isset($categories) && method_exists($categories, 'links')): ?>
            <div class="flex justify-center mt-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <?php echo e($categories->links()); ?>

                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/categories/index.blade.php ENDPATH**/ ?>