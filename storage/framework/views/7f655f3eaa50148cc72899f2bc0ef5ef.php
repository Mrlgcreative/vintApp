<?php $__env->startSection('title', 'Mes ventes'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="flex">
        <?php echo $__env->make('seller.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <main class="flex-1 p-6 lg:p-8 pb-20 lg:pb-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Mes ventes</h1>
                        <p class="text-gray-500 dark:text-gray-400 mt-1"><?php echo e($sales->total()); ?> vente(s)</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <?php if($sales->count() > 0): ?>
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-receipt text-gray-400"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h6 class="font-semibold text-gray-900 dark:text-white">Commande #<?php echo e($sale->id); ?></h6>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate"><?php echo e($sale->item->name ?? 'N/A'); ?> · <?php echo e($sale->buyer->name ?? 'N/A'); ?></p>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <p class="font-bold text-gray-900 dark:text-white"><?php echo e(number_format($sale->total_amount, 2)); ?> $</p>
                                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                            <?php if($sale->status === 'completed'): ?> bg-emerald-100 text-emerald-700
                                            <?php elseif($sale->status === 'pending'): ?> bg-yellow-100 text-yellow-700
                                            <?php elseif($sale->status === 'shipped'): ?> bg-blue-100 text-blue-700
                                            <?php elseif($sale->status === 'cancelled'): ?> bg-red-100 text-red-700
                                            <?php else: ?> bg-gray-100 text-gray-500 <?php endif; ?>">
                                            <?php echo e(ucfirst($sale->status)); ?>

                                        </span>
                                    </div>
                                    <a href="<?php echo e(route('orders.show', $sale)); ?>" class="px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                            <?php echo e($sales->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-16">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-shopping-cart text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucune vente</h3>
                            <p class="text-gray-500 dark:text-gray-400">Les ventes apparaîtront ici quand des acheteurs commanderont vos articles</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/aizen/Bureau/sky/vintApp/resources/views/seller/sales.blade.php ENDPATH**/ ?>