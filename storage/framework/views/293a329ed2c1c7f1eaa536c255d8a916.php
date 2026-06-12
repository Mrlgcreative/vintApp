<?php $__env->startSection('title', 'Articles en attente de vérification'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
    <!-- En-tête avec gradient -->
    <div class="bg-gradient-to-r from-indigo-500 to-primary-600 rounded-xl p-8 text-white mb-8 shadow-lg">
        <h1 class="text-3xl font-bold mb-2">
            <i class="fas fa-list-check mr-3"></i>
            Articles en attente de vérification
        </h1>
        <p class="text-indigo-100">
            Vérifiez les articles qui correspondent à votre domaine d'expertise
        </p>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-full">
                    <i class="fas fa-hourglass-half text-orange-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">En attente</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($items->total()); ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-graduation-cap text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Votre niveau</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white capitalize"><?php echo e(auth()->user()->expertProfile?->certification_level ?? 'N/A'); ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
            <div class="flex items-center">
                <div class="p-3 bg-indigo-100 rounded-full">
                    <i class="fas fa-tag text-indigo-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Spécialités</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white"><?php echo e(count(auth()->user()->expertProfile?->specialties ?? [])); ?> domaines</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Recherche -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Recherche</label>
                <input type="text" 
                       name="search" 
                       value="<?php echo e(request('search')); ?>"
                       placeholder="Nom du produit..."
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
            </div>

            <!-- Catégorie -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catégorie</label>
                <select name="category" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                    <option value="">Toutes les catégories</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($category->id); ?>" <?php echo e(request('category') == $category->id ? 'selected' : ''); ?>>
                            <?php echo e($category->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <!-- Tri -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tri</label>
                <select name="sort" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                    <option value="-created_at" <?php echo e(request('sort') === '-created_at' ? 'selected' : ''); ?>>Plus récents</option>
                    <option value="created_at" <?php echo e(request('sort') === 'created_at' ? 'selected' : ''); ?>>Plus anciens</option>
                    <option value="price" <?php echo e(request('sort') === 'price' ? 'selected' : ''); ?>>Prix croissant</option>
                    <option value="-price" <?php echo e(request('sort') === '-price' ? 'selected' : ''); ?>>Prix décroissant</option>
                </select>
            </div>

            <!-- Bouton de recherche -->
            <div class="flex items-end">
                <button type="submit" class="w-full px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition flex items-center justify-center">
                    <i class="fas fa-search mr-2"></i>
                    Rechercher
                </button>
            </div>
        </form>
    </div>

    <!-- Liste des articles -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
        <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a href="<?php echo e(route('expert.items.show-for-verification', $item)); ?>" 
               class="block border-b border-gray-200 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition p-6">
                <div class="flex gap-6">
                    <!-- Image -->
                    <div class="flex-shrink-0">
                        <?php if($item->getFirstImageUrl()): ?>
                            <img src="<?php echo e($item->getFirstImageUrl()); ?>" 
                                 class="w-24 h-24 object-cover rounded-lg border border-gray-200 dark:border-gray-600"
                                 alt="<?php echo e($item->name); ?>">
                        <?php else: ?>
                            <div class="w-24 h-24 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-xl"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Infos -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                                    <?php echo e($item->name); ?>

                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    <span class="font-medium"><?php echo e($item->category?->name); ?></span> 
                                    <?php if($item->brand): ?>
                                        • <span class="font-medium"><?php echo e($item->brand->name); ?></span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xl font-bold text-gray-900 dark:text-white">
                                    <?php echo e(number_format($item->price, 0, ',', ' ')); ?> 
                                    <span class="text-sm text-gray-500 dark:text-gray-400">FCFA</span>
                                </p>
                                <span class="inline-block mt-2 px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400 text-xs font-semibold rounded-full">
                                    <i class="fas fa-clock mr-1"></i>
                                    En attente
                                </span>
                            </div>
                        </div>

                        <!-- Description -->
                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">
                            <?php echo e(Str::limit($item->description, 150)); ?>

                        </p>

                        <!-- Vendeur et date -->
                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center">
                                    <i class="fas fa-user mr-2"></i>
                                    <?php echo e($item->user->name); ?>

                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-calendar mr-2"></i>
                                    <?php echo e($item->created_at->format('d/m/Y')); ?>

                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-images mr-2"></i>
                                    <?php echo e(count($item->images ?? [])); ?> image(s)
                                </div>
                            </div>
                            <div class="text-primary-600 dark:text-primary-400 font-semibold">
                                Vérifier <i class="fas fa-arrow-right ml-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="p-12 text-center">
                <div class="mb-4">
                    <i class="fas fa-inbox text-4xl text-gray-400 dark:text-gray-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    Aucun article à vérifier
                </h3>
                <p class="text-gray-500 dark:text-gray-400">
                    Tous les articles correspondant à vos spécialités ont déjà été vérifiés. 
                    <br>Revenez plus tard pour de nouveaux articles.
                </p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if($items->hasPages()): ?>
        <div class="mt-8">
            <?php echo e($items->links('pagination::tailwind')); ?>

        </div>
    <?php endif; ?>
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/aizen/Bureau/sky/vintApp/resources/views/expert/items/pending.blade.php ENDPATH**/ ?>