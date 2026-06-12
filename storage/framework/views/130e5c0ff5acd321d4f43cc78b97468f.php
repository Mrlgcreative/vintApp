<?php $__env->startSection('title', 'Gestion des articles'); ?>
<?php $__env->startSection('page-title', 'Articles'); ?>

<?php $__env->startSection('page-actions'); ?>
<div class="flex flex-wrap gap-3">
    <a href="<?php echo e(route('admin.items.create')); ?>"
       class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
        <i class="fas fa-plus mr-2"></i>Nouvel article
    </a>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php if(session('success')): ?>
    <div class="flex items-center rounded-xl bg-green-50 p-4 text-green-800 animate-fade-in mb-6">
        <i class="fas fa-check-circle mr-3 text-green-500"></i>
        <span class="flex-1"><?php echo e(session('success')); ?></span>
        <button type="button" class="ml-4 text-green-500 hover:text-green-700" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="flex items-center rounded-xl bg-red-50 p-4 text-red-800 animate-fade-in mb-6">
        <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
        <span class="flex-1"><?php echo e(session('error')); ?></span>
        <button type="button" class="ml-4 text-red-500 hover:text-red-700" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
<?php endif; ?>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
    <div class="p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div class="md:col-span-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                           class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500"
                           placeholder="Rechercher un article...">
                </div>
            </div>
            <div>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="">Tous les statuts</option>
                    <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Actif</option>
                    <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>En attente</option>
                    <option value="sold" <?php echo e(request('status') === 'sold' ? 'selected' : ''); ?>>Vendu</option>
                    <option value="inactive" <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>>Inactif</option>
                </select>
            </div>
            <div>
                <select name="moderation" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="">Tous</option>
                    <option value="blocked" <?php echo e(request('moderation') === 'blocked' ? 'selected' : ''); ?>>Bloqués</option>
                    <option value="suspended" <?php echo e(request('moderation') === 'suspended' ? 'selected' : ''); ?>>Suspendus</option>
                    <option value="normal" <?php echo e(request('moderation') === 'normal' ? 'selected' : ''); ?>>Normaux</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700">
                    <i class="fas fa-search"></i>
                </button>
                <a href="<?php echo e(route('admin.items.index')); ?>" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <p class="text-xs font-semibold text-primary-600 uppercase tracking-wider mb-2">Total</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($stats['total'] ?? $items->total()); ?></p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider mb-2">Actifs</p>
        <p class="text-2xl font-bold text-green-600"><?php echo e($stats['active'] ?? \App\Models\Item::where('status', 'active')->count()); ?></p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wider mb-2">En attente</p>
        <p class="text-2xl font-bold text-yellow-600"><?php echo e($stats['pending'] ?? \App\Models\Item::where('status', 'pending')->count()); ?></p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <p class="text-xs font-semibold text-red-600 uppercase tracking-wider mb-2">Bloqués</p>
        <p class="text-2xl font-bold text-red-600"><?php echo e($stats['blocked'] ?? \App\Models\Item::where('is_blocked', true)->count()); ?></p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <p class="text-xs font-semibold text-orange-600 uppercase tracking-wider mb-2">Suspendus</p>
        <p class="text-2xl font-bold text-orange-600"><?php echo e($stats['suspended'] ?? \App\Models\Item::where('is_suspended', true)->count()); ?></p>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="p-4 md:p-6 border-b border-gray-200 dark:border-gray-700">
        <h5 class="text-lg font-bold text-gray-900 dark:text-white">Liste des articles</h5>
    </div>
    <div class="p-0">
        <?php if($items->count() > 0): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Article</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Vendeur</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Prix</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Statut</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Modération</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Vues</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50 dark:bg-gray-900 transition-colors <?php echo e($item->is_blocked ? 'bg-red-50 dark:bg-red-900/10' : ($item->is_suspended ? 'bg-orange-50 dark:bg-orange-900/10' : '')); ?>">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                                    <?php if($item->images && count($item->images) > 0): ?>
                                        <img src="<?php echo e(asset('storage/' . $item->images[0])); ?>" alt="" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <i class="fas fa-image text-xl text-gray-400"></i>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <a href="<?php echo e(route('admin.items.show', $item)); ?>" class="font-medium text-gray-900 dark:text-white hover:text-primary-600">
                                        <?php echo e(Str::limit($item->name, 40)); ?>

                                    </a>
                                    <div class="text-xs text-gray-500"><?php echo e($item->category?->name ?? 'N/A'); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <a href="<?php echo e(route('admin.users.show', $item->user)); ?>" class="text-sm text-primary-600 hover:text-primary-700">
                                <?php echo e($item->user?->name ?? 'N/A'); ?>

                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($item->formatted_price ?? ($item->currency === 'USD' ? '$' : '') . number_format($item->price, 2) . ($item->currency !== 'USD' ? ' FC' : '')); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <?php
                                $statusClass = match($item->status) {
                                    'active' => 'bg-green-100 text-green-800 border-green-200',
                                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'sold' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'inactive' => 'bg-gray-100 text-gray-800 border-gray-200',
                                    default => 'bg-gray-100 text-gray-800 border-gray-200',
                                };
                            ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?php echo e($statusClass); ?>">
                                <?php echo e(ucfirst($item->status)); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($item->is_blocked): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                    <i class="fas fa-ban mr-1"></i>Bloqué
                                </span>
                            <?php elseif($item->is_suspended): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">
                                    <i class="fas fa-pause-circle mr-1"></i>Suspendu
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                    <i class="fas fa-check mr-1"></i>Normal
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500"><?php echo e(number_format($item->views ?? 0)); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-500"><?php echo e($item->created_at->format('d/m/Y')); ?></td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex gap-1 justify-end">
                                <a href="<?php echo e(route('admin.items.show', $item)); ?>"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:bg-blue-50 transition-colors" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('admin.items.edit', $item)); ?>"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-primary-600 hover:bg-primary-50 transition-colors" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <?php if($items->hasPages()): ?>
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-xs sm:text-sm text-gray-600">
                    Affichage de <?php echo e($items->firstItem()); ?> à <?php echo e($items->lastItem()); ?> sur <?php echo e($items->total()); ?>

                </div>
                <?php echo e($items->appends(request()->query())->links()); ?>

            </div>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                <i class="fas fa-box text-3xl text-gray-400"></i>
            </div>
            <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucun article</h5>
            <p class="text-gray-500">Aucun article trouvé.</p>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/aizen/Bureau/sky/vintApp/resources/views/admin/items/index.blade.php ENDPATH**/ ?>