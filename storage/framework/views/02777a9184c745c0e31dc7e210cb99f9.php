

<?php $__env->startSection('title', 'Rapports'); ?>
<?php $__env->startSection('page-title', 'Rapports et Statistiques'); ?>

<?php $__env->startSection('content'); ?>
<!-- Filtres de période -->
<div class="mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="<?php echo e(route('admin.reports')); ?>" class="flex flex-wrap items-center gap-4">
            <label class="flex items-center gap-2">
                <span class="text-sm font-medium text-gray-700">Période :</span>
                <select name="period" onchange="this.form.submit()" class="rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                    <option value="7" <?php echo e($period == 7 ? 'selected' : ''); ?>>7 derniers jours</option>
                    <option value="30" <?php echo e($period == 30 ? 'selected' : ''); ?>>30 derniers jours</option>
                    <option value="90" <?php echo e($period == 90 ? 'selected' : ''); ?>>90 derniers jours</option>
                    <option value="365" <?php echo e($period == 365 ? 'selected' : ''); ?>>1 an</option>
                </select>
            </label>
        </form>
    </div>
</div>

<!-- Rapport Revenus -->
<div class="mb-8">
    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
        <i class="fas fa-dollar-sign text-green-600"></i>
        Revenus
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total des revenus -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg overflow-hidden text-white">
            <div class="p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-sm font-medium opacity-90">Total des revenus</div>
                    <i class="fas fa-chart-line text-2xl opacity-80"></i>
                </div>
                <div class="text-3xl font-bold">$<?php echo e(number_format($reports['revenue']['total'], 2)); ?></div>
                <div class="text-xs opacity-80 mt-1">Sur <?php echo e($period); ?> jours</div>
            </div>
            <div class="h-1 bg-white opacity-20"></div>
        </div>

        <!-- Nombre de transactions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Transactions</div>
                        <div class="text-2xl font-bold text-gray-900"><?php echo e(number_format($reports['revenue']['count'])); ?></div>
                        <div class="text-xs text-gray-500 mt-1">Transactions complétées</div>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exchange-alt text-xl text-green-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenu moyen -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Revenu moyen</div>
                        <div class="text-2xl font-bold text-gray-900">$<?php echo e(number_format($reports['revenue']['average'], 2)); ?></div>
                        <div class="text-xs text-gray-500 mt-1">Par transaction</div>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calculator text-xl text-blue-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rapport Utilisateurs -->
<div class="mb-8">
    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
        <i class="fas fa-users text-primary-600"></i>
        Utilisateurs
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Nouveaux utilisateurs -->
        <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl shadow-lg overflow-hidden text-white">
            <div class="p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-sm font-medium opacity-90">Nouveaux utilisateurs</div>
                    <i class="fas fa-user-plus text-2xl opacity-80"></i>
                </div>
                <div class="text-3xl font-bold"><?php echo e(number_format($reports['users']['new_users'])); ?></div>
                <div class="text-xs opacity-80 mt-1">Sur <?php echo e($period); ?> jours</div>
            </div>
            <div class="h-1 bg-white opacity-20"></div>
        </div>

        <!-- Utilisateurs actifs -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Utilisateurs actifs</div>
                        <div class="text-2xl font-bold text-gray-900"><?php echo e(number_format($reports['users']['active_users'])); ?></div>
                        <div class="text-xs text-gray-500 mt-1">Actifs récemment</div>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-check text-xl text-green-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total utilisateurs -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Total</div>
                        <div class="text-2xl font-bold text-gray-900"><?php echo e(number_format($reports['users']['total_users'])); ?></div>
                        <div class="text-xs text-gray-500 mt-1">Tous les utilisateurs</div>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-xl text-primary-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rapport Transactions -->
<div class="mb-8">
    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
        <i class="fas fa-receipt text-blue-600"></i>
        Transactions
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <!-- Total transactions -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg overflow-hidden text-white">
            <div class="p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-sm font-medium opacity-90">Total</div>
                    <i class="fas fa-list text-2xl opacity-80"></i>
                </div>
                <div class="text-3xl font-bold"><?php echo e(number_format($reports['transactions']['total'])); ?></div>
                <div class="text-xs opacity-80 mt-1">Toutes les transactions</div>
            </div>
            <div class="h-1 bg-white opacity-20"></div>
        </div>

        <!-- Transactions complétées -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Complétées</div>
                        <div class="text-2xl font-bold text-green-600"><?php echo e(number_format($reports['transactions']['completed'])); ?></div>
                        <div class="text-xs text-gray-500 mt-1">
                            <?php if($reports['transactions']['total'] > 0): ?>
                                <?php echo e(number_format(($reports['transactions']['completed'] / $reports['transactions']['total']) * 100, 1)); ?>% du total
                            <?php else: ?>
                                0% du total
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-xl text-green-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions en attente -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">En attente</div>
                        <div class="text-2xl font-bold text-yellow-600"><?php echo e(number_format($reports['transactions']['pending'])); ?></div>
                        <div class="text-xs text-gray-500 mt-1">
                            <?php if($reports['transactions']['total'] > 0): ?>
                                <?php echo e(number_format(($reports['transactions']['pending'] / $reports['transactions']['total']) * 100, 1)); ?>% du total
                            <?php else: ?>
                                0% du total
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-xl text-yellow-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions échouées -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Échouées</div>
                        <div class="text-2xl font-bold text-red-600"><?php echo e(number_format($reports['transactions']['failed'])); ?></div>
                        <div class="text-xs text-gray-500 mt-1">
                            <?php if($reports['transactions']['total'] > 0): ?>
                                <?php echo e(number_format(($reports['transactions']['failed'] / $reports['transactions']['total']) * 100, 1)); ?>% du total
                            <?php else: ?>
                                0% du total
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-times-circle text-xl text-red-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Articles populaires -->
<div class="mb-8">
    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
        <i class="fas fa-fire text-orange-600"></i>
        Articles les plus populaires
    </h3>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <?php if($reports['popular_items']->count() > 0): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Article</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Favoris</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Commandes</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Prix</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $reports['popular_items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full <?php echo e($index === 0 ? 'bg-yellow-100 text-yellow-600' : ($index === 1 ? 'bg-gray-100 text-gray-600' : ($index === 2 ? 'bg-orange-100 text-orange-600' : 'bg-gray-50 text-gray-500'))); ?> font-bold text-sm">
                                <?php echo e($index + 1); ?>

                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <?php if(!empty($item->images) && is_array($item->images)): ?>
                                <img src="<?php echo e(Storage::url($item->images[0])); ?>" alt="<?php echo e($item->name); ?>" class="w-12 h-12 rounded-lg object-cover">
                                <?php else: ?>
                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                                <?php endif; ?>
                                <div>
                                    <div class="font-medium text-gray-900"><?php echo e($item->name); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo e($item->category->name ?? 'N/A'); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-pink-100 text-pink-700">
                                <i class="fas fa-heart text-xs"></i>
                                <?php echo e(number_format($item->favorites_count)); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-700">
                                <i class="fas fa-shopping-cart text-xs"></i>
                                <?php echo e(number_format($item->orders_count)); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="font-semibold text-gray-900">$<?php echo e(number_format($item->price, 2)); ?></span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="p-12 text-center">
            <i class="fas fa-box-open text-5xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 font-medium">Aucun article populaire pour cette période</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\gloir\Desktop\projet\vintapp\resources\views/admin/reports/index.blade.php ENDPATH**/ ?>